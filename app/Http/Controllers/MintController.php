<?php

/**
 * @package App\Http\Controllers
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 2.0.0 (FlorenceEGI - NEW 2-PAGE ARCHITECTURE)
 * @date 2025-10-18
 * @purpose Controller for EGI minting - NEW CLEAN APPROACH
 */

namespace App\Http\Controllers;

use App\Models\Egi;
use App\Models\Reservation;
use App\Models\EgiBlockchain;
use App\Jobs\MintEgiJob;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Services\Gdpr\AuditLogService;
use App\Services\CertificateGeneratorService;
use App\Enums\Gdpr\GdprActivityCategory;

class MintController extends Controller {

    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;
    protected AuditLogService $auditService;
    protected CertificateGeneratorService $certificateGenerator;

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService,
        CertificateGeneratorService $certificateGenerator
    ) {
        $this->middleware('auth')->except(['showMintResult']);
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditService = $auditService;
        $this->certificateGenerator = $certificateGenerator;
    }

    /**
     * PAGINA 1: Show payment form (NEW 2-PAGE ARCHITECTURE)
     * ✅ DUAL PATH: Supporta mint con reservation E mint diretto
     *
     * @param int $egiId EGI to mint
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showPaymentForm(int $egiId) {
        try {
            $reservationId = request()->query('reservation_id');
            $egi = Egi::with(['utility.media', 'user'])->findOrFail($egiId);
            $reservation = null;

            // ✅ DUAL PATH: Se c'è reservation_id, valida la reservation
            if ($reservationId) {
                $reservation = Reservation::findOrFail($reservationId);

                // Authorization: user must be reservation winner
                if ($reservation->user_id !== Auth::id()) {
                    $this->errorManager->handle('MINT_CHECKOUT_UNAUTHORIZED', [
                        'user_id' => Auth::id(),
                        'reservation_user_id' => $reservation->user_id,
                        'egi_id' => $egiId,
                        'reservation_id' => $reservationId,
                    ]);
                    return redirect()->back()->withErrors(['error' => __('mint.errors.unauthorized')]);
                }

                // Check reservation is still valid
                if (!$reservation->is_current || $reservation->status !== 'active') {
                    $this->errorManager->handle('MINT_CHECKOUT_INVALID_RESERVATION', [
                        'user_id' => Auth::id(),
                        'egi_id' => $egiId,
                        'reservation_id' => $reservationId,
                        'status' => $reservation->status,
                    ]);
                    return redirect()->back()->withErrors(['error' => __('mint.errors.invalid_reservation')]);
                }
            }

            // Check if already minted
            if ($egi->blockchain && $egi->blockchain->isMinted()) {
                return redirect()->route('mint.show', $egi->blockchain->id);
            }

            return view('mint.payment-form', compact('egi', 'reservation'));
        } catch (\Exception $e) {
            $this->errorManager->handle('MINT_CHECKOUT_ERROR', [
                'user_id' => Auth::id(),
                'egi_id' => $egiId,
                'error' => $e->getMessage()
            ], $e);

            return redirect()->back()->withErrors(['error' => __('mint.errors.unexpected')]);
        }
    }

    /**
     * OLD METHOD - Mantained for backward compatibility
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     * @deprecated Use showPaymentForm() instead
     */
    public function showCheckout(Request $request) {
        try {
            $egiId = $request->query('egi_id');
            $reservationId = $request->query('reservation_id');

            if (!$egiId || !$reservationId) {
                $this->errorManager->handle('MINT_CHECKOUT_MISSING_PARAMS', [
                    'user_id' => Auth::id(),
                    'egi_id' => $egiId,
                    'reservation_id' => $reservationId,
                ]);
                return redirect()->back();
            }

            $egi = Egi::with([
                'utility.media',
                'traits.category',
                'traits.traitType',
                'traits.media'
            ])->findOrFail($egiId);
            $reservation = Reservation::findOrFail($reservationId);

            // Verify user is the winner of the reservation
            if ($reservation->user_id !== Auth::id()) {
                $this->errorManager->handle('MINT_CHECKOUT_UNAUTHORIZED', [
                    'user_id' => Auth::id(),
                    'reservation_user_id' => $reservation->user_id,
                    'egi_id' => $egiId,
                    'reservation_id' => $reservationId,
                ]);
                return redirect()->back();
            }

            // Verify reservation is still highest and active
            if (!$reservation->is_current || $reservation->status !== 'active' || $reservation->superseded_by_id) {
                $this->errorManager->handle('MINT_CHECKOUT_INVALID_RESERVATION', [
                    'user_id' => Auth::id(),
                    'egi_id' => $egiId,
                    'reservation_id' => $reservationId,
                    'is_current' => $reservation->is_current,
                    'status' => $reservation->status,
                    'superseded_by_id' => $reservation->superseded_by_id,
                ]);
                return redirect()->back();
            }

            // Check mint status (supports async minting)
            $mintStatus = 'not_started'; // Default: no mint initiated
            $blockchainData = null;

            if ($egi->blockchain) {
                $blockchain = $egi->blockchain;

                if ($blockchain->isMinted()) {
                    // Mint completed - show success badge
                    $mintStatus = 'completed';
                    $blockchainData = [
                        'asa_id' => $blockchain->asa_id,
                        'tx_id' => $blockchain->blockchain_tx_id,
                        'minted_at' => $blockchain->minted_at?->format('d/m/Y H:i:s'),
                    ];
                } elseif (in_array($blockchain->mint_status, ['minting_queued', 'minting'])) {
                    // Mint in progress - show processing badge
                    $mintStatus = 'processing';
                    $blockchainData = [
                        'status' => $blockchain->mint_status,
                        'created_at' => $blockchain->created_at,
                    ];
                } elseif ($blockchain->mint_status === 'failed') {
                    // Mint failed - show error (allow retry)
                    $mintStatus = 'failed';
                    $blockchainData = [
                        'error' => $blockchain->mint_error_message,
                    ];
                }
            }

            return view('mint.checkout', compact('egi', 'reservation', 'mintStatus', 'blockchainData'));
        } catch (\Exception $e) {
            $this->errorManager->handle('MINT_CHECKOUT_ERROR', [
                'user_id' => Auth::id(),
                'egi_id' => $request->query('egi_id'),
                'reservation_id' => $request->query('reservation_id'),
                'error' => $e->getMessage()
            ], $e);

            return redirect()->back();
        }
    }

    /**
     * Process mint payment and initiate blockchain minting
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processMint(Request $request): \Illuminate\Http\RedirectResponse {
        try {
            $validated = $request->validate([
                'egi_id' => 'required|integer|exists:egis,id',
                'reservation_id' => 'nullable|integer|exists:reservations,id', // ✅ NULLABLE per mint diretto
                'payment_method' => 'required|string|in:stripe,paypal',
                'buyer_wallet' => 'nullable|string|max:255', // Optional - user wallet for direct transfer
                'co_creator_display_name' => 'nullable|string|min:2|max:100|regex:/^[a-zA-Z0-9\s.\'\-]+$/', // AREA 5.5.1
            ]);

            $egi = Egi::findOrFail($validated['egi_id']);

            // ✅ DUAL PATH: Mint con reservation VS Mint diretto
            $reservation = null;
            if (!empty($validated['reservation_id'])) {
                $reservation = Reservation::findOrFail($validated['reservation_id']);

                // Security checks SOLO per mint con reservation
                if ($reservation->user_id !== Auth::id()) {
                    $this->errorManager->handle('MINT_UNAUTHORIZED', [
                        'user_id' => Auth::id(),
                        'reservation_user_id' => $reservation->user_id,
                        'egi_id' => $validated['egi_id'],
                        'reservation_id' => $validated['reservation_id'],
                    ]);
                    return redirect()->back()->withErrors(['error' => __('mint.errors.unauthorized')]);
                }

                if (!$reservation->is_current || $reservation->status !== 'active') {
                    $this->errorManager->handle('MINT_INVALID_RESERVATION', [
                        'user_id' => Auth::id(),
                        'egi_id' => $validated['egi_id'],
                        'reservation_id' => $validated['reservation_id'],
                        'is_current' => $reservation->is_current,
                        'status' => $reservation->status,
                    ]);
                    return redirect()->back()->withErrors(['error' => __('mint.errors.invalid_reservation')]);
                }
            }

            if ($egi->blockchain && $egi->blockchain->isMinted()) {
                $this->errorManager->handle('MINT_ALREADY_MINTED', [
                    'user_id' => Auth::id(),
                    'egi_id' => $validated['egi_id'],
                    'blockchain_id' => $egi->blockchain->id,
                ]);
                return redirect()->route('mint.show', $egi->blockchain->id)
                    ->with('info', __('mint.errors.already_minted'));
            }

            // CRITICAL: Check treasury funds BEFORE payment processing
            $algorandService = app(\App\Services\AlgorandService::class);
            try {
                $fundsCheck = $algorandService->checkTreasuryFunds(Auth::user());

                if (!$fundsCheck['has_sufficient_funds']) {
                    $this->logger->error('Mint blocked - insufficient treasury funds', [
                        'user_id' => Auth::id(),
                        'egi_id' => $validated['egi_id'],
                        'balance_algo' => $fundsCheck['balance_algo'],
                        'required_algo' => $fundsCheck['required_algo'],
                        'treasury_address' => $fundsCheck['treasury_address']
                    ]);

                    $this->errorManager->handle('MINT_BLOCKED_INSUFFICIENT_FUNDS', [
                        'user_id' => Auth::id(),
                        'egi_id' => $validated['egi_id'],
                        'balance_algo' => $fundsCheck['balance_algo'],
                        'required_algo' => $fundsCheck['required_algo'],
                        'treasury_address' => $fundsCheck['treasury_address']
                    ]);

                    return redirect()->back()->withErrors([
                        'error' => __('mint.errors.insufficient_treasury_funds')
                    ]);
                }

                $this->logger->info('Treasury funds check passed', [
                    'user_id' => Auth::id(),
                    'egi_id' => $validated['egi_id'],
                    'balance_algo' => $fundsCheck['balance_algo']
                ]);
            } catch (\Exception $e) {
                $this->logger->error('Treasury funds check failed', [
                    'user_id' => Auth::id(),
                    'egi_id' => $validated['egi_id'],
                    'error' => $e->getMessage()
                ]);

                // Se il check fallisce, procediamo comunque (fail-open per non bloccare completamente)
                // ma logghiamo l'errore per monitoring
            }

            // MOCK Payment processing (V1 - FIAT only)
            // ✅ DUAL PATH: usa reservation amount se presente, altrimenti EGI price
            $paymentAmount = $reservation
                ? ($reservation->offer_amount_fiat ?? $reservation->amount_eur)
                : $egi->price;

            $paymentResult = $this->mockPaymentProcessing($validated['payment_method'], $paymentAmount);

            if (!$paymentResult['success']) {
                $this->errorManager->handle('MINT_PAYMENT_FAILED', [
                    'user_id' => Auth::id(),
                    'egi_id' => $validated['egi_id'],
                    'payment_method' => $validated['payment_method'],
                    'amount' => $paymentAmount,
                    'error' => $paymentResult['error'] ?? 'Unknown',
                ]);
                return redirect()->back()->withErrors(['error' => __('mint.errors.payment_failed')]);
            }

            // Create blockchain record
            $blockchainRecord = EgiBlockchain::create([
                'egi_id' => $egi->id,
                'reservation_id' => $reservation?->id, // ✅ NULLABLE per mint diretto
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

            // CRITICAL: Ensure queue worker is running with intelligent retry
            $queueWorkerService = app(\App\Services\QueueWorkerService::class);
            $maxRetries = 3;
            $retryDelay = 2; // seconds
            $workerReady = false;

            for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
                $this->logger->info("Worker availability check (processMint)", [
                    'attempt' => $attempt,
                    'max_retries' => $maxRetries,
                    'egi_id' => $egi->id
                ]);

                if ($queueWorkerService->ensureWorkerRunning()) {
                    $workerReady = true;
                    $this->logger->info("Worker ready (processMint)", [
                        'attempt' => $attempt,
                        'egi_id' => $egi->id
                    ]);
                    break;
                }

                if ($attempt < $maxRetries) {
                    $this->logger->warning("Worker not ready, retrying (processMint)", [
                        'attempt' => $attempt,
                        'retry_in' => $retryDelay,
                        'egi_id' => $egi->id
                    ]);
                    sleep($retryDelay);
                }
            }

            // Final check after all retries
            if (!$workerReady) {
                $this->logger->error("Worker unavailable after all retries (processMint)", [
                    'attempts' => $maxRetries,
                    'egi_id' => $egi->id,
                    'user_id' => Auth::id()
                ]);

                $this->errorManager->handle('MINT_BLOCKED_WORKER_UNAVAILABLE', [
                    'user_id' => Auth::id(),
                    'egi_id' => $egi->id,
                    'attempts' => $maxRetries
                ]);

                return redirect()->back()->withErrors([
                    'error' => __('mint.errors.worker.error_message')
                ]);
            }

            // Queue REAL blockchain mint job (async with worker)
            // 🔧 XDEBUG MODE: dispatch commented, using dispatchSync
            // dispatch(new MintEgiJob($blockchainRecord->id));
            \App\Jobs\MintEgiJob::dispatchSync($blockchainRecord->id);

            // CRITICAL FIX: Sync owner_id immediately in Controller
            // This guarantees owner_id is correct even before Job processes
            // Job will re-sync in EgiMintingService (redundant but safe)
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
                    'reservation_id' => $reservation?->id, // ✅ NULLABLE per mint diretto
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

            // ✅ REDIRECT to mint result page (form submit normale, non AJAX)
            return redirect()->route('mint.show', $blockchainRecord->id)
                ->with('success', __('mint.notification.processing_message'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->errorManager->handle('MINT_VALIDATION_ERROR', [
                'user_id' => Auth::id(),
                'errors' => json_encode($e->errors()), // Convert array to string
            ], $e);
            return redirect()->back()->withErrors(['error' => __('mint.errors.validation_failed')]);
        } catch (\Exception $e) {
            $this->errorManager->handle('MINT_PROCESS_ERROR', [
                'user_id' => Auth::id(),
                'egi_id' => $request->input('egi_id'),
                'reservation_id' => $request->input('reservation_id'),
                'payment_method' => $request->input('payment_method'),
                'error' => $e->getMessage()
            ], $e);

            return redirect()->back()->withErrors(['error' => __('mint.errors.mint_failed')]);
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
    public function showDirectMint(int $id, Request $request) {
        try {
            $egi = Egi::findOrFail($id);

            // Check if user already minted this EGI (allow viewing post-mint)
            $alreadyMintedByUser = $egi->blockchain
                && $egi->blockchain->buyer_user_id === Auth::id()
                && $egi->blockchain->isMinted();

            // Check availability using service
            $availabilityService = app(\App\Services\EgiAvailabilityService::class);
            $availability = $availabilityService->checkAvailability($egi, Auth::user());

            // If success=1 parameter present OR user already minted, skip availability check
            $isPollingCallback = $request->has('success');
            $canViewPostMint = $isPollingCallback || $alreadyMintedByUser;

            // Verify user can mint this EGI (skip if coming from mint success callback OR already minted by user)
            if (!$canViewPostMint && !$availability['can_mint']) {
                $reason = $availability['mint_reason'] ?? 'not_available';

                $this->errorManager->handle('DIRECT_MINT_NOT_AVAILABLE', [
                    'user_id' => Auth::id(),
                    'egi_id' => $egi->id,
                    // 'reason' => $reason,
                    // 'availability' => $availability,
                ]);

                return redirect()->route('egis.show', $egi->id);
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

            // Check mint status - REDIRECT if already minted
            if ($egi->blockchain && $egi->blockchain->isMinted()) {
                // EGI già mintato → redirect a mint.show (pagina risultato)
                return redirect()->route('mint.show', $egi->blockchain->id);
            }

            // Check mint status (supports async minting)
            $mintStatus = 'not_started'; // Default: no mint initiated
            $blockchainData = null;

            if ($egi->blockchain) {
                $blockchain = $egi->blockchain;

                if (in_array($blockchain->mint_status, ['minting_queued', 'minting'])) {
                    // Mint in progress - show processing badge
                    $mintStatus = 'processing';
                    $blockchainData = [
                        'status' => $blockchain->mint_status,
                        'created_at' => $blockchain->created_at,
                    ];
                } elseif ($blockchain->mint_status === 'failed') {
                    // Mint failed - show error (allow retry)
                    $mintStatus = 'failed';
                    $blockchainData = [
                        'error' => $blockchain->mint_error_message,
                    ];
                }
            }

            return view('mint.payment-form', compact('egi', 'availability', 'reservation', 'mintStatus', 'blockchainData'));
        } catch (\Exception $e) {
            $this->errorManager->handle('DIRECT_MINT_VALIDATION_ERROR', [
                'user_id' => Auth::id(),
                'egi_id' => $id,
                'error' => $e->getMessage()
            ], $e);

            return redirect()->back();
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
                $this->errorManager->handle('DIRECT_MINT_NOT_AVAILABLE_RACE', [
                    'user_id' => Auth::id(),
                    'egi_id' => $id,
                    'reason' => $availability['mint_reason'],
                    'availability' => $availability,
                ]);
                return response()->json([], 400);
            }

            // Check if EGI already minted (race condition protection)
            if ($egi->blockchain && $egi->blockchain->isMinted()) {
                $this->errorManager->handle('DIRECT_MINT_ALREADY_MINTED', [
                    'user_id' => Auth::id(),
                    'egi_id' => $id,
                    'blockchain_id' => $egi->blockchain->id,
                ]);
                return response()->json([], 400);
            }

            // CRITICAL: Check microservice availability BEFORE processing payment
            // This prevents charging the user if blockchain is unavailable
            $algorandService = app(\App\Services\AlgorandService::class);
            try {
                $healthStatus = $algorandService->getNetworkStatus();
                $this->logger->info('Microservice health check passed before mint', [
                    'egi_id' => $egi->id,
                    'user_id' => Auth::id(),
                    'microservice_status' => 'healthy'
                ]);
            } catch (\Exception $e) {
                // Microservice unavailable - abort BEFORE payment
                // UEM handles logging, notifications, and UI messages
                $this->errorManager->handle('MINT_BLOCKED_MICROSERVICE_UNAVAILABLE', [
                    'egi_id' => $egi->id,
                    'user_id' => Auth::id(),
                    'error' => $e->getMessage()
                ], $e);

                // UEM già gestisce tutto - ritorno solo status code
                return response()->json([], 503);
            }

            // CRITICAL: Check treasury funds BEFORE payment processing
            try {
                $fundsCheck = $algorandService->checkTreasuryFunds(Auth::user());

                if (!$fundsCheck['has_sufficient_funds']) {
                    $this->logger->error('Direct mint blocked - insufficient treasury funds', [
                        'user_id' => Auth::id(),
                        'egi_id' => $egi->id,
                        'balance_algo' => $fundsCheck['balance_algo'],
                        'required_algo' => $fundsCheck['required_algo'],
                        'treasury_address' => $fundsCheck['treasury_address']
                    ]);

                    $this->errorManager->handle('MINT_BLOCKED_INSUFFICIENT_FUNDS', [
                        'user_id' => Auth::id(),
                        'egi_id' => $egi->id,
                        'balance_algo' => $fundsCheck['balance_algo'],
                        'required_algo' => $fundsCheck['required_algo'],
                        'treasury_address' => $fundsCheck['treasury_address']
                    ]);

                    return response()->json([
                        'error' => 'insufficient_funds',
                        'message' => __('mint.errors.insufficient_treasury_funds')
                    ], 503);
                }

                $this->logger->info('Treasury funds check passed (direct mint)', [
                    'user_id' => Auth::id(),
                    'egi_id' => $egi->id,
                    'balance_algo' => $fundsCheck['balance_algo']
                ]);
            } catch (\Exception $e) {
                $this->logger->error('Treasury funds check failed (direct mint)', [
                    'user_id' => Auth::id(),
                    'egi_id' => $egi->id,
                    'error' => $e->getMessage()
                ]);

                // Se il check fallisce, procediamo comunque (fail-open per non bloccare completamente)
                // ma logghiamo l'errore per monitoring
            }

            // MOCK Payment processing (V1 - FIAT only)
            $paymentAmount = $egi->price ?? 0;
            $paymentResult = $this->mockPaymentProcessing($validated['payment_method'], $paymentAmount);

            if (!$paymentResult['success']) {
                $this->errorManager->handle('DIRECT_MINT_PAYMENT_FAILED', [
                    'user_id' => Auth::id(),
                    'egi_id' => $id,
                    'payment_method' => $validated['payment_method'],
                    'amount' => $paymentAmount,
                    'error' => $paymentResult['error'] ?? 'Unknown',
                ]);
                return response()->json([], 500);
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

            $this->logger->emergency('🚨 DIRECT MINT - BEFORE DISPATCH', [
                'blockchain_record_id' => $blockchainRecord->id,
                'egi_id' => $egi->id,
                'user_id' => Auth::id()
            ]);

            // CRITICAL: Ensure queue worker is running with intelligent retry
            $queueWorkerService = app(\App\Services\QueueWorkerService::class);
            $maxRetries = 3;
            $retryDelay = 2; // seconds
            $workerReady = false;

            for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
                $this->logger->info("Worker availability check (processDirectMint)", [
                    'attempt' => $attempt,
                    'max_retries' => $maxRetries,
                    'egi_id' => $egi->id
                ]);

                if ($queueWorkerService->ensureWorkerRunning()) {
                    $workerReady = true;
                    $this->logger->info("Worker ready (processDirectMint)", [
                        'attempt' => $attempt,
                        'egi_id' => $egi->id
                    ]);
                    break;
                }

                if ($attempt < $maxRetries) {
                    $this->logger->warning("Worker not ready, retrying (processDirectMint)", [
                        'attempt' => $attempt,
                        'retry_in' => $retryDelay,
                        'egi_id' => $egi->id
                    ]);
                    sleep($retryDelay);
                }
            }

            // Final check after all retries
            if (!$workerReady) {
                $this->logger->error("Worker unavailable after all retries (processDirectMint)", [
                    'attempts' => $maxRetries,
                    'egi_id' => $egi->id,
                    'user_id' => Auth::id()
                ]);

                $this->errorManager->handle('MINT_BLOCKED_WORKER_UNAVAILABLE', [
                    'user_id' => Auth::id(),
                    'egi_id' => $egi->id,
                    'attempts' => $maxRetries
                ]);

                return response()->json([
                    'error' => 'worker_unavailable',
                    'message' => 'Il sistema di elaborazione non è disponibile. Riprova tra qualche minuto.'
                ], 503);
            }

            // Queue REAL blockchain mint job (async with worker)
            // 🔧 XDEBUG MODE: dispatch commented, using dispatchSync
            // dispatch(new MintEgiJob($blockchainRecord->id));
            \App\Jobs\MintEgiJob::dispatchSync($blockchainRecord->id);

            $this->logger->emergency('🚨 DIRECT MINT - AFTER DISPATCH', [
                'blockchain_record_id' => $blockchainRecord->id,
                'egi_id' => $egi->id
            ]);

            // CRITICAL FIX: Sync owner_id immediately in Controller
            // This guarantees owner_id is correct even before Job processes
            // Job will re-sync in EgiMintingService (redundant but safe)
            \App\Models\Egi::where('id', $egi->id)->update([
                'owner_id' => Auth::id()
            ]);

            $this->logger->emergency('🚨 DIRECT MINT - AFTER OWNER UPDATE', [
                'egi_id' => $egi->id,
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
                'data' => [
                    'blockchain_record_id' => $blockchainRecord->id,
                    'mint_status' => 'minting_queued',
                    'flow_type' => 'direct_mint'
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->errorManager->handle('DIRECT_MINT_VALIDATION_ERROR', [
                'user_id' => Auth::id(),
                'errors' => $e->errors(),
            ], $e);
            return response()->json([], 422);
        } catch (\Exception $e) {
            $this->errorManager->handle('DIRECT_MINT_PROCESS_ERROR', [
                'user_id' => Auth::id(),
                'egi_id' => $id,
                'payment_method' => $request->input('payment_method'),
                'error' => $e->getMessage()
            ], $e);

            return response()->json([], 500);
        }
    }

    /**
     * Check mint status for polling (AJAX endpoint)
     *
     * @param int $egiId
     * @return JsonResponse
     *
     * @purpose Called by frontend polling to check if mint is complete
     * @returns JSON with status: minting_queued|minting|minted|failed + blockchain data if minted
     */
    public function checkMintStatus(int $egiId): JsonResponse {
        try {
            $user = Auth::user();

            // Load EGI with blockchain record
            $egi = Egi::with('egiBlockchain')->findOrFail($egiId);

            // Verify authorization - user must be the buyer or creator
            if (
                !$egi->egiBlockchain ||
                ($egi->egiBlockchain->buyer_user_id !== $user->id && $egi->creator_id !== $user->id)
            ) {
                return response()->json([
                    'status' => 'unauthorized'
                ], 403);
            }

            $blockchain = $egi->egiBlockchain;
            $status = $blockchain->mint_status;

            $this->logger->debug('Mint status check', [
                'egi_id' => $egiId,
                'user_id' => $user->id,
                'mint_status' => $status,
                'asa_id' => $blockchain->asa_id
            ]);

            // Build response based on status
            $response = [
                'status' => $status,
                'egi_id' => $egiId
            ];

            // If minted, include blockchain data
            if ($status === 'minted') {
                $response['asa_id'] = $blockchain->asa_id;
                $response['tx_id'] = $blockchain->blockchain_tx_id;
                $response['buyer_wallet'] = $blockchain->buyer_wallet;
                $response['minted_at'] = $blockchain->minted_at?->format('d/m/Y H:i:s');
            }

            // If failed, include error
            if ($status === 'failed') {
                $response['error'] = $blockchain->mint_error ?? 'Unknown error';
            }

            return response()->json($response);
        } catch (\Exception $e) {
            $this->logger->error('Failed to check mint status', [
                'egi_id' => $egiId,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to check mint status'
            ], 500);
        }
    }

    /**
     * PAGINA 2: Show mint result (READ-ONLY, riapribile)
     *
     * @param int $egiBlockchainId EgiBlockchain record ID
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showMintResult(int $egiBlockchainId) {
        try {
            // NESSUN CHECK RESTRITTIVO - Solo fetch dati e mostra
            $blockchain = EgiBlockchain::with(['egi.utility.media', 'egi.user', 'egi.reservations', 'buyer'])->findOrFail($egiBlockchainId);
            $egi = $blockchain->egi;

            // 💰 CALCOLO PREZZO VENDITA (base o ultima prenotazione)
            $lastReservation = $egi->reservations()
                ->where('status', 'active')
                ->orderBy('created_at', 'desc')
                ->first();

            $salePrice = $lastReservation
                ? ($lastReservation->offer_amount_fiat ?? $egi->price)
                : $egi->price;

            // Get certificate (può essere null se non ancora generato)
            $certificate = \App\Models\EgiReservationCertificate::where('egi_blockchain_id', $blockchain->id)
                ->first();

            // Get payment breakdown (può essere vuoto)
            $paymentBreakdown = \App\Models\PaymentDistribution::where('source_type', 'mint')
                ->where('egi_blockchain_id', $blockchain->id)
                ->where('amount_eur', '>', 0)
                ->with('user') // CARICA relazione user
                ->get()
                ->map(function ($dist) {
                    return [
                        'recipient_user_id' => $dist->user_id,
                        'recipient_name' => $dist->user->name ?? 'N/A',
                        'recipient_wallet' => $dist->wallet_id ?? 'N/A',
                        'amount_eur' => $dist->amount_eur,
                        'currency' => 'EUR',
                        'role' => $dist->platform_role ?? 'N/A' // PLATFORM_ROLE non role!
                    ];
                })
                ->toArray();

            $this->logger->info('Mint result page viewed', [
                'user_id' => Auth::id(),
                'egi_id' => $egi->id,
                'blockchain_id' => $blockchain->id,
            ]);

            return view('mint.mint', compact('egi', 'blockchain', 'certificate', 'paymentBreakdown', 'salePrice'));
        } catch (\Exception $e) {
            $this->errorManager->handle('MINT_CHECKOUT_ERROR', [
                'user_id' => Auth::id(),
                'blockchain_id' => $egiBlockchainId,
                'error' => $e->getMessage()
            ], $e);

            return redirect()->back()->withErrors(['error' => __('mint.errors.unexpected')]);
        }
    }

    /**
     * Regenerate blockchain certificate without new mint
     *
     * @package App\Http\Controllers
     * @author Padmin D. Curtis (AI Partner OS3.0)
     * @version 1.0.0 (FlorenceEGI - Certificate Regeneration)
     * @date 2025-10-19
     * @purpose Allows users to regenerate certificate after fixes without minting again
     *
     * @param int $egiBlockchainId The blockchain record ID
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function regenerateCertificate(int $egiBlockchainId) {
        try {
            // 1. ULM: Log start
            $this->logger->info('Certificate regeneration initiated', [
                'user_id' => Auth::id(),
                'blockchain_id' => $egiBlockchainId
            ]);

            // 2. Load blockchain record with EGI
            $blockchain = EgiBlockchain::with('egi')->findOrFail($egiBlockchainId);

            // 3. Authorization: user must be the buyer
            if ($blockchain->buyer_user_id !== Auth::id()) {
                $this->logger->warning('Unauthorized certificate regeneration attempt', [
                    'user_id' => Auth::id(),
                    'blockchain_id' => $egiBlockchainId,
                    'actual_buyer_id' => $blockchain->buyer_user_id
                ]);

                return redirect()->back()->withErrors(['error' => __('errors.unauthorized')]);
            }

            // 4. DELETE old certificates for this blockchain to force regeneration
            \App\Models\EgiReservationCertificate::where('egi_blockchain_id', $blockchain->id)
                ->delete();

            $this->logger->info('Old certificates deleted before regeneration', [
                'blockchain_id' => $blockchain->id
            ]);

            // 5. Regenerate certificate (creates NEW record)
            $certificate = $this->certificateGenerator->generateBlockchainCertificate(
                $blockchain->egi,
                $blockchain
            );

            // 6. GDPR: Audit trail
            $this->auditService->logUserAction(
                Auth::user(),
                'certificate_regenerated',
                [
                    'blockchain_id' => $blockchain->id,
                    'egi_id' => $blockchain->egi_id,
                    'certificate_uuid' => $certificate->certificate_uuid,
                ],
                GdprActivityCategory::BLOCKCHAIN_ACTIVITY
            );

            // 7. ULM: Log success
            $this->logger->info('Certificate regenerated successfully', [
                'user_id' => Auth::id(),
                'blockchain_id' => $egiBlockchainId,
                'certificate_uuid' => $certificate->certificate_uuid,
                'new_pdf_path' => $certificate->pdf_path
            ]);

            // 8. Return JSON response to trigger thumbnail reload
            return response()->json([
                'success' => true,
                'message' => __('mint.post_mint.regenerate_success'),
                'certificate_uuid' => $certificate->certificate_uuid,
                'pdf_url' => $certificate->getPdfUrl(),
                'public_url' => route('egi-certificates.show', $certificate->certificate_uuid),
                'egi_id' => $blockchain->egi_id
            ]);
        } catch (\Exception $e) {
            // 9. UEM: Error handling
            $this->errorManager->handle('CERTIFICATE_REGENERATION_FAILED', [
                'user_id' => Auth::id(),
                'blockchain_id' => $egiBlockchainId,
                'error' => $e->getMessage()
            ], $e);

            return redirect()->back()->withErrors(['error' => __('errors.certificate_regeneration_failed')]);
        }
    }
}
