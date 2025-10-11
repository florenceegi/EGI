<?php

/**
 * @package App\Http\Controllers
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Blockchain Integration)
 * @date 2025-10-07
 * @purpose Controller for EGI minting operations (primo acquisto after reservation)
 */

namespace App\Http\Controllers;

use App\Models\Egi;
use App\Models\Reservation;
use App\Models\EgiBlockchain;
use App\Jobs\MintEgiJob;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Services\Gdpr\AuditLogService;
use App\Enums\Gdpr\GdprActivityCategory;

class MintController extends Controller {

    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;
    protected AuditLogService $auditService;

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService
    ) {
        $this->middleware('auth');
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditService = $auditService;
    }

    /**
     * Show mint checkout form for winning reservation
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showCheckout(Request $request) {
        try {
            $egiId = $request->query('egi_id');
            $reservationId = $request->query('reservation_id');

            if (!$egiId || !$reservationId) {
                return redirect()->back()->withErrors(['error' => 'Parametri mancanti']);
            }

            $egi = Egi::findOrFail($egiId);
            $reservation = Reservation::findOrFail($reservationId);

            // Verify user is the winner of the reservation
            if ($reservation->user_id !== Auth::id()) {
                return redirect()->back()->withErrors(['error' => 'Non sei il vincitore di questa prenotazione']);
            }

            // Verify reservation is still highest and active
            if (!$reservation->is_current || $reservation->status !== 'active' || $reservation->superseded_by_id) {
                return redirect()->back()->withErrors(['error' => 'La prenotazione non è più valida']);
            }

            // Verify EGI is not already minted
            if ($egi->blockchain && $egi->blockchain->isMinted()) {
                return redirect()->back()->withErrors(['error' => 'Questo EGI è già stato mintato']);
            }

            return view('mint.checkout', compact('egi', 'reservation'));
        } catch (\Exception $e) {
            $this->errorManager->handle('MINT_CHECKOUT_ERROR', [
                'user_id' => Auth::id(),
                'egi_id' => $request->query('egi_id'),
                'reservation_id' => $request->query('reservation_id'),
                'error' => $e->getMessage()
            ], $e);

            return redirect()->back()->withErrors(['error' => 'Errore nel caricamento checkout']);
        }
    }

    /**
     * Process mint payment and initiate blockchain minting
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function processMint(Request $request): JsonResponse {
        try {
            $validated = $request->validate([
                'egi_id' => 'required|integer|exists:egis,id',
                'reservation_id' => 'required|integer|exists:reservations,id',
                'payment_method' => 'required|string|in:stripe,paypal',
                'buyer_wallet' => 'nullable|string|max:255', // Optional - user wallet for direct transfer
                'co_creator_display_name' => 'nullable|string|min:2|max:100|regex:/^[a-zA-Z0-9\s.\'\-]+$/', // AREA 5.5.1
            ]);           

            $egi = Egi::findOrFail($validated['egi_id']);
            $reservation = Reservation::findOrFail($validated['reservation_id']);

            // Security checks
            if ($reservation->user_id !== Auth::id()) {
                return response()->json(['error' => 'Non autorizzato'], 403);
            }

            if (!$reservation->is_current || $reservation->status !== 'active') {
                return response()->json(['error' => 'Prenotazione non più valida'], 400);
            }

            if ($egi->blockchain && $egi->blockchain->isMinted()) {
                return response()->json(['error' => 'EGI già mintato'], 400);
            }

            // MOCK Payment processing (V1 - FIAT only)
            $paymentAmount = $reservation->offer_amount_fiat ?? $reservation->amount_eur;
            $paymentResult = $this->mockPaymentProcessing($validated['payment_method'], $paymentAmount);

            if (!$paymentResult['success']) {
                throw new \Exception('Pagamento fallito: ' . $paymentResult['error']);
            }

            // Create blockchain record
            $blockchainRecord = EgiBlockchain::create([
                'egi_id' => $egi->id,
                'reservation_id' => $reservation->id,
                'payment_method' => $validated['payment_method'], // stripe, paypal, bank_transfer
                'psp_provider' => $paymentResult['provider'],
                'payment_reference' => $paymentResult['reference'],
                'paid_amount' => $paymentAmount,
                'paid_currency' => 'EUR',
                'buyer_user_id' => Auth::id(),
                'buyer_wallet' => $validated['buyer_wallet'] ?? null,
                'ownership_type' => $validated['buyer_wallet'] ? 'wallet' : 'treasury',
                'platform_wallet' => config('algorand.algorand.treasury_address', 'TREASURY_PENDING'),
                'mint_status' => 'minting_queued',
                // AREA 5.5.1: Store proposed co-creator name (will be frozen during mint)
                'co_creator_display_name' => $validated['co_creator_display_name'] ?? null,
            ]);

            // Queue REAL blockchain mint job
            dispatch(new MintEgiJob($blockchainRecord->id));

            // TEMP FIX: Force sync owner_id immediately after dispatch
            // This ensures owner_id is synced even if Job executes async
            \App\Models\Egi::where('id', $egi->id)->update([
                'owner_id' => Auth::id()
            ]);

            $this->logger->info('REAL blockchain mint job dispatched + owner_id synced', [
                'blockchain_record_id' => $blockchainRecord->id,
                'egi_id' => $egi->id,
                'user_id' => Auth::id(),
                'owner_id_set_to' => Auth::id()
            ]);

            // GDPR audit log - CORRETTO: metodo E costante verificati
            $this->auditService->logUserAction(
                Auth::user(),
                'EGI mint initiated',
                [
                    'egi_id' => $egi->id,
                    'reservation_id' => $reservation->id,
                    'amount' => $paymentAmount,
                    'payment_method' => $validated['payment_method']
                ],
                GdprActivityCategory::BLOCKCHAIN_ACTIVITY
            );

            $this->logger->info('[MintController] Mint initiated successfully', [
                'user_id' => Auth::id(),
                'egi_id' => $egi->id,
                'blockchain_record_id' => $blockchainRecord->id,
                'amount' => $paymentAmount
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Mint avviato con successo! Riceverai una notifica quando completato.',
                'data' => [
                    'blockchain_record_id' => $blockchainRecord->id,
                    'mint_status' => 'minting_queued',
                    'estimated_completion' => '5-10 minuti'
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Dati non validi', 'details' => $e->errors()], 422);
        } catch (\Exception $e) {
            $this->errorManager->handle('MINT_PROCESS_ERROR', [
                'user_id' => Auth::id(),
                'egi_id' => $request->input('egi_id'),
                'reservation_id' => $request->input('reservation_id'),
                'payment_method' => $request->input('payment_method'),
                'error' => $e->getMessage()
            ], $e);

            return response()->json(['error' => 'Errore durante il mint'], 500);
        }
    }

    /**
     * Mock payment processing for V1 (FIAT only)
     * In V2 this will be replaced with real PSP integration
     *
     * @param string $method
     * @param float $amount
     * @return array
     */
    private function mockPaymentProcessing(string $method, float $amount): array {
        // MOCK: Always successful for development
        return [
            'success' => true,
            'provider' => $method === 'stripe' ? 'Stripe' : 'PayPal',
            'reference' => strtoupper($method) . '-MOCK-' . time() . '-1234',
            'amount' => $amount,
            'currency' => 'EUR',
            'status' => 'completed'
        ];
    }

    /**
     * Show direct mint checkout form (Phase 2: dual path)
     *
     * Allows users to mint EGI directly without reservation (if available)
     *
     * @param int $id EGI ID
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showDirectMint(int $id) {
        try {
            $egi = Egi::findOrFail($id);

            // Check availability using service
            $availabilityService = app(\App\Services\EgiAvailabilityService::class);
            $availability = $availabilityService->checkAvailability($egi, Auth::user());

            // Verify user can mint this EGI
            if (!$availability['can_mint']) {
                $reason = $availability['mint_reason'] ?? 'not_available';

                return redirect()->route('egis.show', $egi->id)
                    ->withErrors(['error' => __("mint.errors.{$reason}", [], $reason)]);
            }

            // GDPR audit log
            $this->auditService->logUserAction(
                Auth::user(),
                'Direct mint checkout viewed',
                [
                    'egi_id' => $egi->id,
                    'egi_title' => $egi->title,
                    'price' => $egi->price
                ],
                GdprActivityCategory::BLOCKCHAIN_ACTIVITY
            );

            $this->logger->info('DIRECT_MINT_CHECKOUT_VIEWED', [
                'user_id' => Auth::id(),
                'egi_id' => $egi->id,
                'availability' => $availability
            ]);

            // Direct mint = no reservation
            $reservation = null;

            return view('mint.checkout', compact('egi', 'availability', 'reservation'));
        } catch (\Exception $e) {
            $this->errorManager->handle('DIRECT_MINT_CHECKOUT_ERROR', [
                'user_id' => Auth::id(),
                'egi_id' => $id,
                'error' => $e->getMessage()
            ], $e);

            return redirect()->back()->withErrors(['error' => 'Errore nel caricamento checkout diretto']);
        }
    }

    /**
     * Process direct mint payment and initiate blockchain minting (Phase 2)
     *
     * Direct mint without reservation - immediate purchase flow
     *
     * @param int $id EGI ID
     * @param \App\Http\Requests\MintDirectRequest $request
     * @return JsonResponse
     */
    public function processDirectMint(int $id, \App\Http\Requests\MintDirectRequest $request): JsonResponse {
        try {
            $validated = $request->validated();

            $egi = Egi::findOrFail($id);

            // dd($validated);

            // Double-check availability (prevent race conditions)
            $availabilityService = app(\App\Services\EgiAvailabilityService::class);
            $availability = $availabilityService->checkAvailability($egi, Auth::user());

            if (!$availability['can_mint']) {
                return response()->json([
                    'error' => 'EGI non più disponibile per mint diretto',
                    'reason' => $availability['mint_reason']
                ], 400);
            }

            // Check if EGI already minted (race condition protection)
            if ($egi->blockchain && $egi->blockchain->isMinted()) {
                return response()->json(['error' => 'EGI già mintato'], 400);
            }

            // MOCK Payment processing (V1 - FIAT only)
            $paymentAmount = $egi->price ?? 0;
            $paymentResult = $this->mockPaymentProcessing($validated['payment_method'], $paymentAmount);

            if (!$paymentResult['success']) {
                throw new \Exception('Pagamento fallito: ' . $paymentResult['error']);
            }

            // Create blockchain record (NO reservation_id for direct mint)
            $blockchainRecord = EgiBlockchain::create([
                'egi_id' => $egi->id,
                'reservation_id' => null, // NULL = direct mint without reservation
                'payment_method' => $validated['payment_method'],
                'psp_provider' => $paymentResult['provider'],
                'payment_reference' => $paymentResult['reference'],
                'paid_amount' => $paymentAmount,
                'paid_currency' => 'EUR',
                'buyer_user_id' => Auth::id(),
                'buyer_wallet' => $validated['wallet_address'] ?? null,
                'ownership_type' => isset($validated['wallet_address']) ? 'wallet' : 'treasury',
                'platform_wallet' => config('algorand.algorand.treasury_address', 'TREASURY_PENDING'),
                'mint_status' => 'minting_queued',
                // AREA 5.5.1: Store proposed co-creator name (will be frozen during mint)
                'co_creator_display_name' => $validated['co_creator_display_name'] ?? null,
            ]);

            // Queue REAL blockchain mint job
            dispatch(new MintEgiJob($blockchainRecord->id));

            // TEMP FIX: Force sync owner_id immediately after dispatch
            // This ensures owner_id is synced even if Job executes async
            \App\Models\Egi::where('id', $egi->id)->update([
                'owner_id' => Auth::id()
            ]);

            $this->logger->info('DIRECT MINT job dispatched + owner_id synced', [
                'blockchain_record_id' => $blockchainRecord->id,
                'egi_id' => $egi->id,
                'user_id' => Auth::id(),
                'owner_id_set_to' => Auth::id(),
                'direct_mint' => true
            ]);

            // GDPR audit log
            $this->auditService->logUserAction(
                Auth::user(),
                'Direct mint initiated',
                [
                    'egi_id' => $egi->id,
                    'amount' => $paymentAmount,
                    'payment_method' => $validated['payment_method'],
                    'has_wallet' => isset($validated['wallet_address'])
                ],
                GdprActivityCategory::BLOCKCHAIN_ACTIVITY
            );

            $this->logger->info('[MintController] Direct mint initiated successfully', [
                'user_id' => Auth::id(),
                'egi_id' => $egi->id,
                'blockchain_record_id' => $blockchainRecord->id,
                'amount' => $paymentAmount,
                'flow' => 'direct_mint'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Mint diretto avviato con successo! Riceverai una notifica quando completato.',
                'data' => [
                    'blockchain_record_id' => $blockchainRecord->id,
                    'mint_status' => 'minting_queued',
                    'estimated_completion' => '5-10 minuti',
                    'flow_type' => 'direct_mint'
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Dati non validi', 'details' => $e->errors()], 422);
        } catch (\Exception $e) {
            $this->errorManager->handle('DIRECT_MINT_PROCESS_ERROR', [
                'user_id' => Auth::id(),
                'egi_id' => $id,
                'payment_method' => $request->input('payment_method'),
                'error' => $e->getMessage()
            ], $e);

            return response()->json(['error' => 'Errore durante il mint diretto'], 500);
        }
    }
}