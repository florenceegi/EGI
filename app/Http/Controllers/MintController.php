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
use Illuminate\Support\Facades\Cache;
use App\DataTransferObjects\Payment\PaymentRequest;
use App\Exceptions\Payment\MerchantAccountNotConfiguredException;
use App\Services\Payment\PaymentServiceFactory;
use App\Services\Payment\MerchantAccountResolver;
use App\Services\Payment\StripeRealPaymentService;
use App\Services\Payment\PayPalRealPaymentService;
use Illuminate\Support\Str;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Services\Gdpr\AuditLogService;
use App\Services\CertificateGeneratorService;
use App\Enums\Gdpr\GdprActivityCategory;
use App\Services\EgiliService;

class MintController extends Controller {

    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;
    protected AuditLogService $auditService;
    protected CertificateGeneratorService $certificateGenerator;
    protected PaymentServiceFactory $paymentFactory;
    protected MerchantAccountResolver $merchantAccountResolver;

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService,
        CertificateGeneratorService $certificateGenerator,
        PaymentServiceFactory $paymentFactory,
        MerchantAccountResolver $merchantAccountResolver
    ) {
        $this->middleware('auth')->except(['showMintResult']);
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditService = $auditService;
        $this->certificateGenerator = $certificateGenerator;
        $this->paymentFactory = $paymentFactory;
        $this->merchantAccountResolver = $merchantAccountResolver;
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
            $paymentSuccess = request()->query('payment_success');
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

            // Check if already minted (Masters are usually not minted, or if they are, we ignore it for cloning)
            if ($egi->blockchain && $egi->blockchain->isMinted() && !$egi->is_template) {
                return redirect()->route('mint.show', $egi->blockchain->id);
            }

            // POLICY CHECK: Check if Collection can sell EGIs (Subscription/EPP valid)
            $subscriptionService = app(\App\Services\CollectionSubscriptionService::class);
            if (!$subscriptionService->canSellEgis($egi->collection)) {
                 $this->errorManager->handle('MINT_BLOCKED_SUBSCRIPTION_EXPIRED', [
                    'user_id' => Auth::id(),
                    'egi_id' => $egiId,
                    'collection_id' => $egi->collection_id
                ]);
                
                // Redirect with specific error message
                return redirect()->back()->withErrors([
                    'error' => 'La vendita di questo EGI è attualmente sospesa perché l\'abbonamento della collezione è scaduto.'
                ]);
            }

            // ✅ Se il pagamento è stato completato con successo (redirect da Stripe)
            if ($paymentSuccess == '1') {
                $sessionId = request()->query('session_id');

                // Cerca il blockchain record più recente per questo EGI e utente
                $blockchainRecord = EgiBlockchain::where('egi_id', $egiId)
                    ->where('buyer_user_id', Auth::id())
                    ->orderBy('created_at', 'desc')
                    ->first();

                if ($blockchainRecord) {
                    // Se il record è in pending_checkout, dobbiamo completare il pagamento
                    if ($blockchainRecord->mint_status === 'pending_checkout' && $sessionId) {
                        $this->logger->info('Completing pending checkout payment', [
                            'user_id' => Auth::id(),
                            'egi_id' => $egiId,
                            'blockchain_record_id' => $blockchainRecord->id,
                            'session_id' => $sessionId,
                        ]);

                        try {
                            // Verify payment with Stripe
                            $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
                            $session = $stripe->checkout->sessions->retrieve($sessionId);

                            if ($session->payment_status === 'paid') {
                                // Update blockchain record to start minting
                                $blockchainRecord->update([
                                    'mint_status' => 'minting_queued',
                                    'payment_completed_at' => now(),
                                ]);

                                $this->logger->info('Payment verified, dispatching mint job', [
                                    'blockchain_record_id' => $blockchainRecord->id,
                                    'stripe_session_id' => $sessionId,
                                ]);

                                // Dispatch mint job
                                \App\Jobs\ProcessMintJob::dispatch($blockchainRecord->id);
                            }
                        } catch (\Exception $e) {
                            $this->logger->error('Failed to verify Stripe payment', [
                                'error' => $e->getMessage(),
                                'session_id' => $sessionId,
                            ]);
                        }
                    }

                    $this->logger->info('Payment success redirect - showing mint result', [
                        'user_id' => Auth::id(),
                        'egi_id' => $egiId,
                        'blockchain_record_id' => $blockchainRecord->id,
                    ]);

                    return redirect()->route('mint.show', $blockchainRecord->id)
                        ->with('success', __('mint.notification.processing_message'));
                }

                // Se non troviamo il record, mostriamo un messaggio di attesa
                $this->logger->warning('Payment success but blockchain record not found yet', [
                    'user_id' => Auth::id(),
                    'egi_id' => $egiId,
                ]);

                return view('mint.payment-form', [
                    'egi' => $egi,
                    'reservation' => $reservation,
                    'paymentAmountEur' => 0,
                    'showEgiliOption' => false,
                    'canPayWithEgili' => false,
                    'egiliBalance' => 0,
                    'requiredEgili' => 0,
                    'paymentProcessing' => true,
                    'stripeMerchantAvailable' => false,
                    'stripeMerchantError' => null,
                    'paypalAvailable' => false,
                    'paypalError' => null,
                ]);
            }

            $paymentAmountEur = $this->resolvePaymentAmount($reservation, $egi);
            $showEgiliOption = false;
            $canPayWithEgili = false;
            $egiliBalance = 0;
            $requiredEgili = 0;

            if ($egi->payment_by_egili && $paymentAmountEur !== null && $paymentAmountEur > 0) {
                /** @var EgiliService $egiliService */
                $egiliService = app(EgiliService::class);
                $egiliBalance = $egiliService->getBalance(Auth::user());
                $requiredEgili = max($egiliService->fromEur($paymentAmountEur), 1);
                $showEgiliOption = true;
                $canPayWithEgili = $egiliBalance >= $requiredEgili;
            }

            // Validate ALL collection wallets (CENTRALIZED METHOD)
            $stripeValidation = $this->merchantAccountResolver->validateAllCollectionWallets($egi, 'stripe');
            $paypalValidation = $this->merchantAccountResolver->validateAllCollectionWallets($egi, 'paypal');

            $stripeMerchantAvailable = $stripeValidation['can_accept_payments'];
            $paypalAvailable = $paypalValidation['can_accept_payments'];

            // Determine user-friendly error messages
            $stripeMerchantError = null;
            if (!$stripeMerchantAvailable) {
                if (!empty($stripeValidation['invalid_wallets'])) {
                    $stripeMerchantError = __('payment.errors.some_wallets_invalid');
                } elseif ($stripeValidation['total_wallets'] === 0) {
                    $stripeMerchantError = __('payment.errors.merchant_account_incomplete');
                } elseif (!$stripeValidation['provider_enabled']) {
                    $stripeMerchantError = __('payment.errors.stripe_disabled');
                } else {
                    $stripeMerchantError = __('payment.errors.merchant_account_disabled');
                }
            }

            $paypalError = null;
            if (!$paypalAvailable) {
                if (!empty($paypalValidation['invalid_wallets'])) {
                    $paypalError = __('payment.errors.some_wallets_invalid');
                } elseif ($paypalValidation['total_wallets'] === 0) {
                    $paypalError = __('payment.errors.paypal_not_configured');
                } elseif (!$paypalValidation['provider_enabled']) {
                    $paypalError = __('payment.errors.paypal_disabled');
                } else {
                    $paypalError = __('payment.errors.paypal_not_implemented');
                }
            }

            $this->logger->info('Mint payment form: PSP validation completed', [
                'egi_id' => $egi->id,
                'stripe_can_accept' => $stripeMerchantAvailable,
                'stripe_wallets' => $stripeValidation['total_wallets'],
                'stripe_valid' => $stripeValidation['valid_wallets'],
                'paypal_can_accept' => $paypalAvailable,
                'paypal_wallets' => $paypalValidation['total_wallets'],
                'paypal_valid' => $paypalValidation['valid_wallets'],
            ]);

            // COMMODITY STRATEGY: Detect & Execute
            $commodityData = null;
            $commodityRefreshedAt = null;
            $commodityValidUntil = null;
            $isCommodity = false;

            // Strategy Resolution Logic
            // First check DB column, then traits.
            $commodityType = $egi->commodity_type ?? ($egi->getTraitByTypeSlug('commodity-type')?->value);

            if ($commodityType) {
                try {
                    // Make Strategy
                    $commodityStrategy = \App\Egi\Commodity\CommodityFactory::make($commodityType);
                    $isCommodity = true;

                    // Refresh Price (Generic)
                    // forceRefresh in Contract returns array with 'success'
                    $refreshResult = $commodityStrategy->forceRefresh('EUR', $egi);

                    if ($refreshResult['success']) {
                        // Calculate Value (Generic)
                        $commodityValue = $commodityStrategy->calculateValue($egi, 'EUR');

                        if ($commodityValue) {
                            $commodityData = $commodityValue;
                            $commodityRefreshedAt = now()->toIso8601String();
                            $commodityValidUntil = now()->addMinutes(10)->toIso8601String();
                            
                            // Update payment amount
                            $paymentAmountEur = (float) ($commodityValue['final_value'] ?? $commodityValue['price'] ?? 0);

                            // Cache logic (Generic Key)
                            // We stick to the existing key pattern for backward compat or genericize it
                            // Key: commodity_mint_{userId}_{egiId}
                            $cacheKey = 'commodity_mint_' . Auth::id() . '_' . $egi->id;
                            
                            Cache::put($cacheKey, [
                                'refreshed_at' => now()->timestamp,
                                'valid_until' => now()->addMinutes(10)->timestamp,
                                'price' => $paymentAmountEur,
                                'data' => $commodityData,
                                'type' => $commodityType
                            ], 600);

                            $this->logger->info('Commodity price refreshed for mint form', [
                                'egi_id' => $egi->id,
                                'type' => $commodityType,
                                'final_value' => $paymentAmountEur,
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    $this->logger->warning('Commodity Strategy Failed in Mint Form', [
                        'egi_id' => $egi->id,
                        'type' => $commodityType,
                        'error' => $e->getMessage()
                    ]);
                    // Fallback or ignore - standard EGI flow continues if strict check not enforced here
                }
            }

            return view('mint.payment-form', compact(
                'egi',
                'reservation',
                'paymentAmountEur',
                'showEgiliOption',
                'canPayWithEgili',
                'egiliBalance',
                'requiredEgili',
                'stripeMerchantAvailable',
                'stripeMerchantError',
                'paypalAvailable',
                'paypalError',
                'isCommodity',
                'commodityData',
                'commodityRefreshedAt',
                'commodityValidUntil'
            ));
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
                'payment_method' => 'required|string|in:stripe,paypal,egili',
                'buyer_wallet' => 'nullable|string|max:255', // Optional - user wallet for direct transfer
                'co_creator_display_name' => 'nullable|string|min:2|max:100|regex:/^[a-zA-Z0-9\s.\'\-]+$/', // AREA 5.5.1
            ]);

            $egi = Egi::findOrFail($validated['egi_id']);
            // ✅ FIX: Capture ID before potential cloning swap because Cache uses the original ID
            $originalEgiId = $egi->id;
            
            // MASTER CLONABLE LOGIC: If buying a Master, clone it first
            if ($egi->is_template) {
                if (!$egi->allow_buyer_clone) {
                     abort(403, 'This Master Template is not available for buyer cloning.');
                }
                
                $this->logger->info('Processing Purchase for Master EGI - Cloning...', [
                    'master_id' => $egi->id,
                    'buyer_id' => Auth::id()
                ]);
                
                $cloneAction = app(\App\Actions\Egi\CloneEgiFromMasterAction::class);
                // Execute cloning WITHOUT immediate minting (controller handles minting queue)
                $clone = $cloneAction->execute($egi, Auth::user(), false);
                
                // Swap EGI for the new Child
                $egi = $clone;
                $validated['egi_id'] = $egi->id; // Update validation array just in case
                
                $this->logger->info('Master cloned successfully. Proceeding with Mint for Child.', [
                    'master_id' => $egi->parent_id,
                    'child_id' => $egi->id,
                    'serial' => $egi->serial_number
                ]);
            }

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

            // OPTIONAL: Treasury funds check (fail-open se non disponibile)
            try {
                $algorandService = app(\App\Services\AlgorandService::class);

                // Verifica se il metodo esiste prima di chiamarlo (defensive programming)
                if (method_exists($algorandService, 'checkTreasuryFunds')) {
                    $fundsCheck = $algorandService->checkTreasuryFunds(Auth::user());

                    if (isset($fundsCheck['has_sufficient_funds']) && !$fundsCheck['has_sufficient_funds']) {
                        $this->logger->error('Mint blocked - insufficient treasury funds', [
                            'user_id' => Auth::id(),
                            'egi_id' => $validated['egi_id'],
                            'balance_algo' => $fundsCheck['balance_algo'] ?? 'unknown',
                            'required_algo' => $fundsCheck['required_algo'] ?? 'unknown',
                            'treasury_address' => $fundsCheck['treasury_address'] ?? 'unknown'
                        ]);

                        // UEM: Messaggio user-friendly per utente finale
                        return $this->errorManager->handle('MINT_BLOCKED_INSUFFICIENT_FUNDS', [
                            'user_id' => Auth::id(),
                            'egi_id' => $validated['egi_id'],
                            'balance_algo' => $fundsCheck['balance_algo'] ?? 'unknown',
                            'required_algo' => $fundsCheck['required_algo'] ?? 'unknown',
                        ]);
                    }

                    $this->logger->info('Treasury funds check passed', [
                        'user_id' => Auth::id(),
                        'egi_id' => $validated['egi_id'],
                        'balance_algo' => $fundsCheck['balance_algo'] ?? 'checked'
                    ]);
                } else {
                    // Metodo non ancora implementato - log warning ma procedi
                    $this->logger->warning('Treasury funds check method not available - proceeding anyway', [
                        'user_id' => Auth::id(),
                        'egi_id' => $validated['egi_id'],
                        'note' => 'checkTreasuryFunds method not found in AlgorandService'
                    ]);
                }
            } catch (\Exception $e) {
                // Errore durante check fondi - UEM per notificare team ma mostra messaggio user-friendly
                $this->logger->error('Treasury funds check failed', [
                    'user_id' => Auth::id(),
                    'egi_id' => $validated['egi_id'],
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                // UEM: Messaggio user-friendly invece di errore tecnico raw
                return $this->errorManager->handle('MINT_TECHNICAL_ERROR_TREASURY', [
                    'user_id' => Auth::id(),
                    'egi_id' => $validated['egi_id'],
                    'error_type' => get_class($e),
                    'error_message' => $e->getMessage(),
                ]);
            }

            // COMMODITY STRATEGY: Validate Price Validity (Generic)
            $commodityMintData = null;
            $commodityType = $egi->commodity_type ?? ($egi->getTraitByTypeSlug('commodity-type')?->value);

            if ($commodityType) {
                 // Try generic key first
                 $cacheKey = 'commodity_mint_' . Auth::id() . '_' . $originalEgiId;
                 $commodityMintData = Cache::get($cacheKey);

                 // Backward compatibility / Fallback for migration period (or if view used old key logic, though we updated it)
                 if (!$commodityMintData && $commodityType === 'goldbar') {
                     $cacheKeyOld = 'gold_bar_mint_' . Auth::id() . '_' . $originalEgiId;
                     $commodityMintData = Cache::get($cacheKeyOld);
                 }

                 if (!$commodityMintData) {
                    $this->logger->warning('Commodity mint attempted without price CACHE - attempting HOT REFRESH', [
                        'user_id' => Auth::id(),
                        'egi_id' => $egi->id,
                        'type' => $commodityType
                    ]);

                    try {
                        $strategy = \App\Egi\Commodity\CommodityFactory::make($commodityType);
                        $refreshResult = $strategy->calculateValue($egi, 'EUR');

                        if ($refreshResult) {
                             $price = (float) ($refreshResult['final_value'] ?? $refreshResult['price'] ?? 0);
                             $commodityMintData = [
                                 'refreshed_at' => now()->timestamp,
                                 'valid_until' => now()->addMinutes(10)->timestamp,
                                 'price' => $price,
                                 'data' => $refreshResult,
                                 'type' => $commodityType
                             ];
                             // Proceed with fresh data
                             $this->logger->info('Commodity HOT REFRESH successful', ['price' => $price]);
                        } else {
                             throw new \Exception("Strategy calculation returned null");
                        }
                    } catch (\Exception $e) {
                         $this->logger->error('Commodity HOT REFRESH failed - aborting mint', [
                             'egi_id' => $egi->id,
                             'error' => $e->getMessage()
                        ]);
                         return redirect()->back()->withErrors([
                            'error' => __('gold_bar.mint_price_expired'), // We can genericize key later
                            'gold_bar_price_expired' => true,
                        ]);
                    }
                 }

                 // Check validity
                 if (now()->timestamp > $commodityMintData['valid_until']) {
                     $this->logger->warning('Commodity mint price expired', [
                        'user_id' => Auth::id(),
                        'egi_id' => $egi->id
                     ]);
                     if (isset($cacheKey)) Cache::forget($cacheKey);
                     
                     return redirect()->back()->withErrors([
                        'error' => __('gold_bar.mint_price_expired'),
                        'gold_bar_price_expired' => true,
                    ]);
                 }
            }

            $paymentAmountEur = $this->resolvePaymentAmount($reservation, $egi);

            if ($paymentAmountEur === null || $paymentAmountEur <= 0) {
                $this->logger->error('Invalid payment amount detected', [
                    'user_id' => Auth::id(),
                    'egi_id' => $egi->id,
                    'reservation_id' => $reservation?->id,
                    'calculated_amount' => $paymentAmountEur,
                ]);

                return redirect()->back()->withErrors(['error' => __('mint.errors.invalid_amount')]);
            }

            $paymentMethod = $validated['payment_method'];
            $paymentProvider = null;
            $paymentReference = null;
            $paidCurrency = 'EUR';
            $paidAmountRecorded = $paymentAmountEur;
            $egiliMetadata = [];
            $paymentMetadata = [];

            if ($paymentMethod === 'egili') {
                try {
                    $egiliPayment = $this->handleEgiliPayment($egi, $reservation, $paymentAmountEur);

                    $paymentProvider = 'egili_internal';
                    $paymentReference = $egiliPayment['reference'];
                    $paidCurrency = 'EGL';
                    $paidAmountRecorded = $egiliPayment['amount_egili'];
                    $egiliMetadata = [
                        'egili_transaction_id' => $egiliPayment['transaction_id'] ?? null,
                    ];
                } catch (RuntimeException $egiliException) {
                    $reason = $egiliException->getMessage();

                    $this->logger->warning('Egili payment failed', [
                        'user_id' => Auth::id(),
                        'egi_id' => $egi->id,
                        'reservation_id' => $reservation?->id,
                        'reason' => $reason,
                    ]);

                    $errorMessageKey = match ($reason) {
                        'insufficient_egili' => 'mint.errors.insufficient_egili',
                        'egili_disabled' => 'mint.errors.egili_disabled',
                        'unauthenticated' => 'mint.errors.unauthorized',
                        default => 'mint.errors.payment_failed',
                    };

                    $this->errorManager->handle('MINT_PAYMENT_FAILED', [
                        'user_id' => Auth::id(),
                        'egi_id' => $validated['egi_id'],
                        'payment_method' => $paymentMethod,
                        'amount' => $paymentAmountEur,
                        'reason' => $reason,
                    ], $egiliException);

                    return redirect()->back()->withErrors(['error' => __($errorMessageKey)]);
                }
            } else {
                try {
                    $paymentService = $this->paymentFactory->create($paymentMethod);
                } catch (\Throwable $factoryException) {
                    $this->errorManager->handle('MINT_PAYMENT_PROVIDER_UNAVAILABLE', [
                        'provider' => $paymentMethod,
                        'error' => $factoryException->getMessage(),
                    ], $factoryException);

                    return redirect()->back()->withErrors(['error' => __('mint.errors.payment_failed')]);
                }

                $merchantContext = [];

                try {
                    if ($paymentService instanceof StripeRealPaymentService || $paymentService instanceof PayPalRealPaymentService) {
                        $merchantContext = $this->merchantAccountResolver
                            ->resolveForEgiAndProvider($egi, $paymentMethod);
                    }
                } catch (MerchantAccountNotConfiguredException $merchantException) {
                    return redirect()->back()->withErrors(['error' => __('mint.errors.merchant_not_configured')]);
                } catch (\Throwable $resolverException) {
                    $this->errorManager->handle('MINT_PAYMENT_PROVIDER_UNAVAILABLE', [
                        'provider' => $paymentMethod,
                        'error' => $resolverException->getMessage(),
                        'context' => 'merchant_account_resolver',
                    ], $resolverException);

                    return redirect()->back()->withErrors(['error' => __('mint.errors.payment_failed')]);
                }

                $paymentRequest = (new PaymentRequest(
                    amount: $paymentAmountEur,
                    currency: 'EUR',
                    customerEmail: Auth::user()->email,
                    egiId: $egi->id,
                    reservationId: $reservation?->id,
                    userId: Auth::id(),
                    metadata: [
                        'flow' => $reservation ? 'reservation_mint' : 'direct_mint',
                        'initiated_at' => now()->toIso8601String(),
                    ],
                    successUrl: route('mint.payment-form', ['egiId' => $egi->id]) . '?payment_success=1',
                    cancelUrl: route('mint.payment-form', ['egiId' => $egi->id])
                ))->withMerchantContext($merchantContext);

                $paymentResultObject = $paymentService->processPayment($paymentRequest);

                if ($paymentResultObject->requiresAction() && $paymentResultObject->redirectUrl) {
                    $this->logger->info('Payment requires additional user action', [
                        'provider' => $paymentService->getProviderName(),
                        'payment_id' => $paymentResultObject->paymentId,
                        'redirect_url' => $paymentResultObject->redirectUrl,
                    ]);

                    return redirect()->away($paymentResultObject->redirectUrl);
                }

                if (!$paymentResultObject->success) {
                    $this->errorManager->handle('MINT_PAYMENT_FAILED', [
                        'user_id' => Auth::id(),
                        'egi_id' => $validated['egi_id'],
                        'payment_method' => $paymentMethod,
                        'amount' => $paymentAmountEur,
                        'error' => $paymentResultObject->errorMessage,
                        'error_code' => $paymentResultObject->errorCode,
                    ]);

                    return redirect()->back()->withErrors([
                        'error' => $paymentResultObject->errorMessage ?? __('mint.errors.payment_failed')
                    ]);
                }

                $paymentProvider = $paymentService->getProviderName();
                $paymentReference = $paymentResultObject->paymentId;
                $paidCurrency = $paymentResultObject->currency ?: $paidCurrency;
                $paidAmountRecorded = $paymentResultObject->amount ?: $paidAmountRecorded;
                $paymentMetadata = $paymentResultObject->metadata;

                if (!isset($paymentMetadata['merchant_psp']) && !empty($merchantContext)) {
                    $paymentMetadata['merchant_psp'] = $merchantContext;
                }
            }

            // Commodity: Freeze the price at mint time by saving it to EGI
            if ($commodityType && $commodityMintData) {
                // Ensure price is saved to the EGI record so it's immutable for invoicing
                $egi->update([
                    'price' => $commodityMintData['price'],
                ]);
                
                $this->logger->info('Commodity price frozen at mint', [
                    'egi_id' => $egi->id,
                    'type' => $commodityType,
                    'frozen_price' => $commodityMintData['price'],
                ]);
                
                // Clear the cache data
                // Clear both new and old keys to be sure
                Cache::forget('commodity_mint_' . Auth::id() . '_' . $originalEgiId);
                Cache::forget('gold_bar_mint_' . Auth::id() . '_' . $originalEgiId);
            }

            // Create blockchain record
            $metadata = [
                'merchant_psp' => $paymentMetadata['merchant_psp'] ?? null,
            ];

            // Commodity: Store base value for distribution logic (Cost Reimbursement)
            // Strategy: We rely on the data structure returned by calculateValue
            if ($commodityType && isset($commodityMintData['data']['base_value'])) {
                $baseValue = (float) $commodityMintData['data']['base_value'];
                $metadata['commodity_base_value'] = $baseValue;
                // Backward compat for Gold Specific reports?
                if ($commodityType === 'goldbar') {
                     $metadata['gold_base_value'] = $baseValue;
                }
                
                $this->logger->info('Commodity base value stored in metadata', [
                    'egi_id' => $egi->id,
                    'type' => $commodityType,
                    'base_value' => $baseValue
                ]);
            }

            $blockchainRecord = EgiBlockchain::create([
                'egi_id' => $egi->id,
                'reservation_id' => $reservation?->id, // ✅ NULLABLE per mint diretto
                'payment_method' => $paymentMethod,
                'psp_provider' => $paymentProvider,
                'payment_reference' => $paymentReference,
                'paid_amount' => $paidAmountRecorded,
                'paid_currency' => $paidCurrency,
                'buyer_user_id' => Auth::id(),
                'buyer_wallet' => $validated['buyer_wallet'] ?? null,
                'ownership_type' => $validated['buyer_wallet'] ? 'wallet' : 'treasury',
                'platform_wallet' => config('algorand.algorand.treasury_address', 'TREASURY_PENDING'),
                'mint_status' => 'minting_queued',
                'metadata' => $metadata, // Store metadata
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
                    'amount_eur' => $paymentAmountEur,
                    'paid_amount_recorded' => $paidAmountRecorded,
                    'paid_currency' => $paidCurrency,
                    'payment_method' => $paymentMethod,
                    'payment_provider' => $paymentProvider,
                    'payment_reference' => $paymentReference,
                    'payment_metadata' => array_filter($paymentMetadata),
                    'egili_transaction_id' => $egiliMetadata['egili_transaction_id'] ?? null,
                ],
                GdprActivityCategory::BLOCKCHAIN_ACTIVITY
            );

            $this->logger->info('[MintController] Mint initiated successfully', [
                'user_id' => Auth::id(),
                'egi_id' => $egi->id,
                'blockchain_record_id' => $blockchainRecord->id,
                'amount_eur' => $paymentAmountEur,
                'paid_currency' => $paidCurrency,
                'paid_amount_recorded' => $paidAmountRecorded,
                'payment_method' => $paymentMethod
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
        } catch (\Stripe\Exception\ApiErrorException $e) {
            // Determine specific Stripe error and use UEM
            $originalMessage = $e->getMessage();
            $errorCode = 'STRIPE_PAYMENT_API_ERROR';

            if (str_contains($originalMessage, 'cannot currently make charges')) {
                $errorCode = 'STRIPE_MERCHANT_ACCOUNT_DISABLED';
            } elseif (str_contains($originalMessage, 'requirements.disabled_reason')) {
                $errorCode = 'STRIPE_MERCHANT_ACCOUNT_INCOMPLETE';
            }

            // UEM will handle user message and redirect
            $response = $this->errorManager->handle($errorCode, [
                'user_id' => Auth::id(),
                'egi_id' => $request->input('egi_id'),
                'payment_method' => $request->input('payment_method'),
                'error_message' => $originalMessage,
                'error_code' => $e->getStripeCode(),
                'error_type' => $e->getError()?->type,
            ], $e);

            // If UEM returns a response, use it; otherwise fallback
            if ($response instanceof \Illuminate\Http\RedirectResponse || $response instanceof \Illuminate\Http\JsonResponse) {
                return $response;
            }

            // Fallback if UEM doesn't return a response
            return redirect()->back()->withErrors(['error' => __('mint.errors.payment_failed')]);
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
     * Calculate the EUR amount to charge based on reservation or EGI price.
     *
     * @param Reservation|null $reservation
     * @param Egi $egi
     * @return float|null
     */
    private function resolvePaymentAmount(?Reservation $reservation, Egi $egi): ?float {
        if ($reservation) {
            if (!is_null($reservation->amount_eur)) {
                return (float) $reservation->amount_eur;
            }

            if (
                !is_null($reservation->offer_amount_fiat) &&
                (($reservation->fiat_currency ?? 'EUR') === 'EUR')
            ) {
                return (float) $reservation->offer_amount_fiat;
            }

            return null;
        }

        // Gold Bar EGI: get calculated gold value
        if ($egi->isGoldBar()) {
            $goldValue = $egi->getGoldBarValue();
            if ($goldValue && isset($goldValue['final_value'])) {
                return (float) $goldValue['final_value'];
            }
            return null;
        }

        return !is_null($egi->price) ? (float) $egi->price : null;
    }

    /**
     * Handle Egili payment flow (balance check, deduction, audit)
     *
     * @param Egi $egi
     * @param Reservation|null $reservation
     * @param float $amountEur
     * @return array{success:bool,reference:string,provider:string,amount_eur:float,amount_egili:int,transaction_id:int|null}
     */
    private function handleEgiliPayment(Egi $egi, ?Reservation $reservation, float $amountEur): array {
        if (!$egi->payment_by_egili) {
            throw new \RuntimeException('egili_disabled');
        }

        $user = Auth::user();

        if (!$user) {
            throw new \RuntimeException('unauthenticated');
        }

        /** @var EgiliService $egiliService */
        $egiliService = app(EgiliService::class);
        $requiredEgili = max($egiliService->fromEur($amountEur), 1);

        if (!$egiliService->canSpend($user, $requiredEgili)) {
            throw new \RuntimeException('insufficient_egili');
        }

        $reference = 'EGL-' . strtoupper(Str::random(12));

        $transaction = $egiliService->spend(
            $user,
            $requiredEgili,
            'egi_direct_mint',
            'mint',
            [
                'egi_id' => $egi->id,
                'reservation_id' => $reservation?->id,
                'payment_reference' => $reference,
                'amount_eur' => $amountEur,
            ],
            $egi
        );

        $this->logger->info('EGILI_PAYMENT_PROCESSED', [
            'user_id' => $user->id,
            'egi_id' => $egi->id,
            'reservation_id' => $reservation?->id,
            'required_egili' => $requiredEgili,
            'reference' => $reference,
            'transaction_id' => $transaction->id ?? null,
        ]);

        return [
            'success' => true,
            'reference' => $reference,
            'provider' => 'egili_internal',
            'amount_eur' => $amountEur,
            'amount_egili' => $requiredEgili,
            'transaction_id' => $transaction->id ?? null,
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

            $paymentAmountEur = $this->resolvePaymentAmount(null, $egi);
            $showEgiliOption = false;
            $canPayWithEgili = false;
            $egiliBalance = 0;
            $requiredEgili = 0;

            if ($egi->payment_by_egili && $paymentAmountEur !== null && $paymentAmountEur > 0) {
                /** @var EgiliService $egiliService */
                $egiliService = app(EgiliService::class);
                $egiliBalance = $egiliService->getBalance(Auth::user());
                $requiredEgili = max($egiliService->fromEur($paymentAmountEur), 1);
                $showEgiliOption = true;
                $canPayWithEgili = $egiliBalance >= $requiredEgili;
            }

            // Validate ALL collection wallets (CENTRALIZED METHOD)
            $stripeValidation = $this->merchantAccountResolver->validateAllCollectionWallets($egi, 'stripe');
            $paypalValidation = $this->merchantAccountResolver->validateAllCollectionWallets($egi, 'paypal');

            $stripeMerchantAvailable = $stripeValidation['can_accept_payments'];
            $paypalAvailable = $paypalValidation['can_accept_payments'];

            // Determine user-friendly error messages
            $stripeMerchantError = null;
            if (!$stripeMerchantAvailable) {
                if (!empty($stripeValidation['invalid_wallets'])) {
                    $stripeMerchantError = __('payment.errors.some_wallets_invalid');
                } elseif ($stripeValidation['total_wallets'] === 0) {
                    $stripeMerchantError = __('payment.errors.merchant_account_incomplete');
                } elseif (!$stripeValidation['provider_enabled']) {
                    $stripeMerchantError = __('payment.errors.stripe_disabled');
                } else {
                    $stripeMerchantError = __('payment.errors.merchant_account_disabled');
                }
            }

            $paypalError = null;
            if (!$paypalAvailable) {
                if (!empty($paypalValidation['invalid_wallets'])) {
                    $paypalError = __('payment.errors.some_wallets_invalid');
                } elseif ($paypalValidation['total_wallets'] === 0) {
                    $paypalError = __('payment.errors.paypal_not_configured');
                } elseif (!$paypalValidation['provider_enabled']) {
                    $paypalError = __('payment.errors.paypal_disabled');
                } else {
                    $paypalError = __('payment.errors.paypal_not_implemented');
                }
            }

            $this->logger->info('Direct mint: PSP validation completed', [
                'egi_id' => $egi->id,
                'stripe_can_accept' => $stripeMerchantAvailable,
                'stripe_wallets' => $stripeValidation['total_wallets'],
                'stripe_valid' => $stripeValidation['valid_wallets'],
                'paypal_can_accept' => $paypalAvailable,
                'paypal_wallets' => $paypalValidation['total_wallets'],
                'paypal_valid' => $paypalValidation['valid_wallets'],
            ]);

            // Gold Bar specific data
            $isGoldBar = $egi->isGoldBar();
            $goldBarData = null;
            $goldPriceRefreshedAt = null;
            $goldPriceValidUntil = null;

            if ($isGoldBar) {
                // Get fresh gold price for Gold Bar EGIs
                $goldPriceService = app(\App\Contracts\GoldPriceServiceInterface::class);
                $refreshResult = $goldPriceService->forceRefreshFree('EUR', $egi);

                if ($refreshResult['success']) {
                    $goldBarValue = $goldPriceService->calculateFromEgi($egi, 'EUR');
                    if ($goldBarValue) {
                        $goldBarData = $goldBarValue;
                        $goldPriceRefreshedAt = now()->toIso8601String();
                        $goldPriceValidUntil = now()->addMinutes(10)->toIso8601String();
                        // Update payment amount with fresh gold price
                        $paymentAmountEur = (float) $goldBarValue['final_value'];

                        // CALCULATE SPLIT FOR UI PREVIEW (Transparency)
                        $margin = (float) ($goldBarValue['margin_applied'] ?? 0);
                        $baseValue = (float) ($goldBarValue['base_value'] ?? 0);
                        
                        $platformFee = $margin * 0.10;
                        $companyShare = $baseValue + ($margin * 0.90);
                        
                        $goldBarValue['platform_fee'] = $platformFee;
                        $goldBarValue['company_share'] = $companyShare;
                        
                        $goldBarData = $goldBarValue;

                        // STAGING FIX: Use Cache instead of Session for robustness
                        // Key MUST match processMint expectation: gold_bar_mint_{userId}_{egiId}
                        $cacheKey = 'gold_bar_mint_' . Auth::id() . '_' . $egi->id;
                        
                        Cache::put($cacheKey, [
                            'refreshed_at' => now()->timestamp,
                            'valid_until' => now()->addMinutes(10)->timestamp,
                            'price' => $paymentAmountEur,
                            'data' => $goldBarValue, // FIX: Standardize key to 'data' to match processDirectMint logic
                        ], 600); // 10 minutes TTL

                        $this->logger->info('Gold Bar price CACHED for direct mint form (Page Load)', [
                            'egi_id' => $egi->id,
                            'cache_key' => $cacheKey,
                            'final_value' => $paymentAmountEur
                        ]);

                        /* $this->logger->info('Gold Bar price refreshed for direct mint form', [
                            'egi_id' => $egi->id,
                            'final_value' => $paymentAmountEur,
                            'gold_price_per_gram' => $goldBarValue['gold_price_per_gram'],
                        ]); */
                    }
                }
            }

            return view('mint.payment-form', compact(
                'egi',
                'availability',
                'reservation',
                'mintStatus',
                'blockchainData',
                'paymentAmountEur',
                'showEgiliOption',
                'canPayWithEgili',
                'egiliBalance',
                'requiredEgili',
                'stripeMerchantAvailable',
                'stripeMerchantError',
                'paypalAvailable',
                'paypalError',
                'isGoldBar',
                'goldBarData',
                'goldPriceRefreshedAt',
                'goldPriceValidUntil'
            ));
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
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function processDirectMint(int $id, \App\Http\Requests\MintDirectRequest $request) {
        try {
            $validated = $request->validated();

            $egi = Egi::findOrFail($id);
            // ✅ FIX: Capture ID before potential cloning logic (though consistent here, good practice)
            $originalEgiId = $egi->id;

            // dd($validated);

            // MASTER CLONABLE LOGIC: If buying a Master, clone it first
            $wasCloned = false;
            if ($egi->is_template) {
                if (!$egi->allow_buyer_clone) {
                     $this->errorManager->handle('DIRECT_MINT_MASTER_NOT_CLONABLE', [
                        'user_id' => Auth::id(),
                        'egi_id' => $egi->id
                     ]);
                     
                     if ($request->wantsJson()) {
                         return response()->json(['error' => 'not_clonable'], 403);
                     }
                     return redirect()->back()->withErrors(['error' => 'This Master Template is not available for buyer cloning.']);
                }
                
                $this->logger->info('Processing Direct Mint for Master EGI - Cloning...', [
                    'master_id' => $egi->id,
                    'buyer_id' => Auth::id()
                ]);
                
                $cloneAction = app(\App\Actions\Egi\CloneEgiFromMasterAction::class);
                // Execute cloning WITHOUT immediate minting (controller handles minting queue)
                $clone = $cloneAction->execute($egi, Auth::user(), false);
                
                // Swap EGI for the new Child
                $egi = $clone;
                $wasCloned = true;
                // Note: We leave $id as original ID for some error logs, but main flows use $egi->id
                
                $this->logger->info('Master cloned successfully (Direct Mint). Proceeding with Mint for Child.', [
                    'master_id' => $egi->parent_id,
                    'child_id' => $egi->id,
                    'serial' => $egi->serial_number
                ]);
            }

            // Double-check availability (prevent race conditions)
            // SKIP for clones: We just created it for this user, so we know it's available.
            // (Standard check fails with 'own_egi_cannot_mint' because user is owner)
            if (!$wasCloned) {
                $availabilityService = app(\App\Services\EgiAvailabilityService::class);
                $availability = $availabilityService->checkAvailability($egi, Auth::user());

                if (!$availability['can_mint']) {
                    $this->errorManager->handle('DIRECT_MINT_NOT_AVAILABLE_RACE', [
                        'user_id' => Auth::id(),
                        'egi_id' => $id,
                        'reason' => $availability['mint_reason'],
                        'availability' => json_encode($availability),
                    ]);
                    
                    if ($request->wantsJson()) {
                        return response()->json([], 400);
                    }
                    return redirect()->back()->withErrors(['error' => __('mint.errors.direct_mint_not_available')]);
                }
            }

            // Check if EGI already minted (race condition protection)
            if ($egi->blockchain && $egi->blockchain->isMinted()) {
                $this->errorManager->handle('DIRECT_MINT_ALREADY_MINTED', [
                    'user_id' => Auth::id(),
                    'egi_id' => $id,
                    'blockchain_id' => $egi->blockchain->id,
                ]);
                
                if ($request->wantsJson()) {
                    return response()->json([], 400);
                }
                return redirect()->route('mint.show', $egi->blockchain->id)->with('info', __('mint.errors.already_minted'));
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

                // UEM già gestisce tutto
                if ($request->wantsJson()) {
                    return response()->json([], 503);
                }
                return redirect()->back()->withErrors(['error' => __('mint.errors.service_unavailable')]);
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

                    if ($request->wantsJson()) {
                        return response()->json([
                            'error' => 'insufficient_funds',
                            'message' => __('mint.errors.insufficient_treasury_funds')
                        ], 503);
                    }
                    return redirect()->back()->withErrors(['error' => __('mint.errors.insufficient_treasury_funds')]);
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

            // FIX: Re-fetch Master to check isGoldBar status safely (Clone might be unhydrated)
            // We use originalEgiId which captures the ID passed to the controller (Master ID)
            $masterCheck = Egi::find($originalEgiId);
            $wasMasterGoldBar = $masterCheck ? $masterCheck->isGoldBar() : false;

            // ...

            // Gold Bar specific: Check 10-minute price validity timeout
            $goldBarMintData = null;
            // FIX: Check if ORIGINAL was a Gold Bar (Clone might not have traits hydrated yet)
            if ($wasMasterGoldBar) { 
                // STAGING FIX: Use Cache instead of Session
                $cacheKey = 'gold_bar_mint_' . Auth::id() . '_' . $originalEgiId;
                $goldBarMintData = Cache::get($cacheKey);

                if (!$goldBarMintData) {
                    $this->logger->error('Gold Bar direct mint attempted without price CACHE data', [
                        'user_id' => Auth::id(),
                        'egi_id' => $egi->id,
                        'original_egi_id' => $originalEgiId,
                        'cache_key' => $cacheKey
                    ]);
                    
                    if ($request->wantsJson()) {
                        return response()->json([
                            'success' => false,
                            'error' => 'gold_bar_price_expired',
                            'message' => __('gold_bar.mint_price_expired'),
                        ], 422);
                    }
                    return redirect()->back()->withErrors(['error' => __('gold_bar.mint_price_expired')]);
                }

                // Check if price is still valid (10-minute timeout)
                if (now()->timestamp > $goldBarMintData['valid_until']) {
                    $this->logger->warning('Gold Bar direct mint price expired', [
                        'user_id' => Auth::id(),
                        'egi_id' => $egi->id,
                        'valid_until' => $goldBarMintData['valid_until'],
                        'now' => now()->timestamp,
                    ]);

                    // Clear expired cache
                    Cache::forget($cacheKey);

                    if ($request->wantsJson()) {
                        return response()->json([
                            'success' => false,
                            'error' => 'gold_bar_price_expired',
                            'message' => __('gold_bar.mint_price_expired'),
                        ], 422);
                    }
                    return redirect()->back()->withErrors(['error' => __('gold_bar.mint_price_expired')]);
                }
            }

            $paymentAmountEur = $this->resolvePaymentAmount(null, $egi);

            if ($paymentAmountEur === null || $paymentAmountEur <= 0) {
                $this->logger->error('Invalid payment amount (direct mint)', [
                    'user_id' => Auth::id(),
                    'egi_id' => $egi->id,
                    'calculated_amount' => $paymentAmountEur,
                ]);

                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'error' => 'invalid_amount',
                        'message' => __('mint.errors.invalid_amount'),
                    ], 422);
                }
                return redirect()->back()->withErrors(['error' => __('mint.errors.invalid_amount')]);
            }

            $paymentMethod = $validated['payment_method'];
            $paymentProvider = null;
            $paymentReference = null;
            $paidCurrency = 'EUR';
            $paidAmountRecorded = $paymentAmountEur;
            $egiliMetadata = [];
            $paidAmountRecorded = $paymentAmountEur;
            $egiliMetadata = [];
            $paymentMetadata = [];
            $blockchainRecord = null; // Initialize variable for scope access

            if ($paymentMethod === 'egili') {
                try {
                    $egiliPayment = $this->handleEgiliPayment($egi, null, $paymentAmountEur);

                    $paymentProvider = 'egili_internal';
                    $paymentReference = $egiliPayment['reference'];
                    $paidCurrency = 'EGL';
                    $paidAmountRecorded = $egiliPayment['amount_egili'];
                    $egiliMetadata = [
                        'egili_transaction_id' => $egiliPayment['transaction_id'] ?? null,
                    ];
                } catch (\RuntimeException $egiliException) {
                    $reason = $egiliException->getMessage();

                    $this->logger->warning('Egili payment failed (direct mint)', [
                        'user_id' => Auth::id(),
                        'egi_id' => $egi->id,
                        'reason' => $reason,
                    ]);

                    $this->errorManager->handle('DIRECT_MINT_PAYMENT_FAILED', [
                        'user_id' => Auth::id(),
                        'egi_id' => $egi->id,
                        'payment_method' => $paymentMethod,
                        'amount' => $paymentAmountEur,
                        'reason' => $reason,
                    ], $egiliException);

                    $messageKey = match ($reason) {
                        'insufficient_egili' => 'mint.errors.insufficient_egili',
                        'egili_disabled' => 'mint.errors.egili_disabled',
                        'unauthenticated' => 'mint.errors.unauthorized',
                        default => 'mint.errors.payment_failed',
                    };

                    if ($request->wantsJson()) {
                        return response()->json([
                            'success' => false,
                            'error' => $reason,
                            'message' => __($messageKey),
                        ], $reason === 'insufficient_egili' ? 422 : 500);
                    }
                    return redirect()->back()->withErrors(['error' => __($messageKey)]);
                }
            } else {
                try {
                    $paymentService = $this->paymentFactory->create($paymentMethod);
                } catch (\Throwable $factoryException) {
                    $this->errorManager->handle('DIRECT_MINT_PAYMENT_FAILED', [
                        'user_id' => Auth::id(),
                        'egi_id' => $egi->id,
                        'payment_method' => $paymentMethod,
                        'amount' => $paymentAmountEur,
                        'error' => $factoryException->getMessage(),
                    ], $factoryException);

                    if ($request->wantsJson()) {
                        return response()->json([
                            'success' => false,
                            'error' => 'provider_unavailable',
                        ], 500);
                    }
                    return redirect()->back()->withErrors(['error' => __('mint.errors.payment_failed')]);
                }

                // Resolve merchant context for EGI minting (Stripe Connect)
                $merchantContext = $this->merchantAccountResolver->resolveForEgiAndProvider(
                    $egi,
                    $paymentMethod
                );

                // FIX: Create Blockchain Record BEFORE Payment to allow linking distributions
                // This prevents "Double Record" creation by MintEgiJob (which checks for existing linked recs)
                $blockchainRecord = EgiBlockchain::create([
                    'egi_id' => $egi->id,
                    'reservation_id' => null, // NULL = direct mint
                    'payment_method' => $paymentMethod,
                    'psp_provider' => $paymentService->getProviderName(),
                    'payment_reference' => 'pending_' . uniqid(),
                    'paid_amount' => $paymentAmountEur,
                    'paid_currency' => 'EUR',
                    'buyer_user_id' => Auth::id(),
                    'buyer_wallet' => $validated['wallet_address'] ?? null,
                    'ownership_type' => isset($validated['wallet_address']) ? 'wallet' : 'treasury',
                    'platform_wallet' => config('algorand.algorand.treasury_address', 'TREASURY_PENDING'),
                    'mint_status' => 'pending_checkout',
                    'co_creator_display_name' => $validated['co_creator_display_name'] ?? null,
                ]);

                $paymentRequest = new PaymentRequest(
                    amount: $paymentAmountEur,
                    currency: 'EUR',
                    customerEmail: Auth::user()->email,
                    egiId: $egi->id,
                    reservationId: null,
                    userId: Auth::id(),
                    metadata: [
                        'flow' => 'direct_mint',
                        'initiated_at' => now()->toIso8601String(),
                        'commodity_base_value' => $goldBarMintData['data']['base_value'] ?? null, // INJECT FROZEN COST
                        'egi_blockchain_id' => $blockchainRecord->id, // LINK STRIPE TO BLOCKCHAIN RECORD
                    ],
                    // Use same pattern as reservation mint - redirect to payment-form with success flag
                    successUrl: route('mint.payment-form', ['egiId' => $egi->id]) . '?payment_success=1',
                    cancelUrl: route('mint.payment-form', ['egiId' => $egi->id]),
                    merchantContext: $merchantContext
                );

                $paymentResultObject = $paymentService->processPayment($paymentRequest);

                if ($paymentResultObject->requiresAction() && $paymentResultObject->redirectUrl) {
                    $this->logger->info('Direct mint payment requires action - creating pending record', [
                        'provider' => $paymentService->getProviderName(),
                        'payment_id' => $paymentResultObject->paymentId,
                    ]);

                    // Update existing pending record instead of creating new one
                    if ($blockchainRecord) {
                        $blockchainRecord->update([
                            'mint_status' => 'pending_checkout',
                            'payment_reference' => $paymentResultObject->paymentId,
                            'ownership_type' => isset($validated['wallet_address']) ? 'wallet' : 'treasury',
                        ]);
                    }

                    $this->logger->info('Created pending blockchain record for Stripe Checkout', [
                        'blockchain_record_id' => $blockchainRecord->id,
                        'egi_id' => $egi->id,
                        'payment_id' => $paymentResultObject->paymentId,
                    ]);

                    if ($request->wantsJson()) {
                        return response()->json([
                            'success' => false,
                            'error' => 'requires_action',
                            'redirect_url' => $paymentResultObject->redirectUrl,
                        ], 202);
                    }
                    return redirect()->away($paymentResultObject->redirectUrl);
                }

                if (!$paymentResultObject->success) {
                    if ($blockchainRecord) {
                        $blockchainRecord->update(['mint_status' => 'payment_failed']);
                    }
                    $this->errorManager->handle('DIRECT_MINT_PAYMENT_FAILED', [
                        'user_id' => Auth::id(),
                        'egi_id' => $egi->id,
                        'payment_method' => $paymentMethod,
                        'amount' => $paymentAmountEur,
                        'error' => $paymentResultObject->errorMessage,
                        'error_code' => $paymentResultObject->errorCode,
                    ]);

                    if ($request->wantsJson()) {
                        return response()->json([
                            'success' => false,
                            'error' => 'payment_failed',
                            'message' => $paymentResultObject->errorMessage ?? __('mint.errors.payment_failed'),
                        ], 500);
                    }
                    return redirect()->back()->withErrors([
                         'error' => $paymentResultObject->errorMessage ?? __('mint.errors.payment_failed')
                    ]);
                }

                $paymentProvider = $paymentService->getProviderName();
                $paymentReference = $paymentResultObject->paymentId;
                $paidCurrency = $paymentResultObject->currency ?: $paidCurrency;
                $paidAmountRecorded = $paymentResultObject->amount ?: $paidAmountRecorded;
                $paymentMetadata = $paymentResultObject->metadata;
            }

            // Gold Bar: Freeze the price at mint time by saving it to EGI
            if ($egi->isGoldBar() && $goldBarMintData) {
                $egi->update([
                    'price' => $goldBarMintData['price'],
                ]);

                $this->logger->info('Gold Bar price frozen at direct mint', [
                    'egi_id' => $egi->id,
                    'frozen_price' => $goldBarMintData['price'],
                    'gold_data' => $goldBarMintData['gold_data'] ?? null,
                ]);

                // Clear the session data
                session()->forget('gold_bar_mint_' . $egi->id);
            }

            // Create or Update blockchain record
            if ($blockchainRecord) {
                 // Stripe Flow: Update existing record
                 $blockchainRecord->update([
                    'mint_status' => 'minting_queued', // Ready for processing
                    'payment_reference' => $paymentReference,
                    'paid_amount' => $paidAmountRecorded,
                    'paid_currency' => $paidCurrency,
                    // Ensure metadata from payment (e.g. fees) is merged
                    'metadata' => array_merge($blockchainRecord->metadata ?? [], $paymentMetadata ?? []),
                 ]);
            } else {
                // Egili Flow (or others): Create new record
                $blockchainRecord = EgiBlockchain::create([
                    'egi_id' => $egi->id,
                    'reservation_id' => null, // NULL = direct mint without reservation
                    'payment_method' => $paymentMethod,
                    'psp_provider' => $paymentProvider,
                    'payment_reference' => $paymentReference,
                    'paid_amount' => $paidAmountRecorded,
                    'paid_currency' => $paidCurrency,
                    'buyer_user_id' => Auth::id(),
                    'buyer_wallet' => $validated['wallet_address'] ?? null,
                    'ownership_type' => isset($validated['wallet_address']) ? 'wallet' : 'treasury',
                    'platform_wallet' => config('algorand.algorand.treasury_address', 'TREASURY_PENDING'),
                    'mint_status' => 'minting_queued',
                    'co_creator_display_name' => $validated['co_creator_display_name'] ?? null,
                    'metadata' => $egiliMetadata, // Egili metadata
                ]);
            }

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
                    'amount_eur' => $paymentAmountEur,
                    'paid_amount_recorded' => $paidAmountRecorded,
                    'paid_currency' => $paidCurrency,
                    'payment_method' => $paymentMethod,
                    'payment_provider' => $paymentProvider,
                    'payment_reference' => $paymentReference,
                    'payment_metadata' => array_filter($paymentMetadata),
                    'has_wallet' => isset($validated['wallet_address']),
                    'egili_transaction_id' => $egiliMetadata['egili_transaction_id'] ?? null,
                ],
                GdprActivityCategory::BLOCKCHAIN_ACTIVITY
            );

            $this->logger->info('[MintController] Direct mint initiated successfully', [
                'user_id' => Auth::id(),
                'egi_id' => $egi->id,
                'blockchain_record_id' => $blockchainRecord->id,
                'amount_eur' => $paymentAmountEur,
                'paid_currency' => $paidCurrency,
                'paid_amount_recorded' => $paidAmountRecorded,
                'payment_method' => $paymentMethod,
                'flow' => 'direct_mint'
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'blockchain_record_id' => $blockchainRecord->id,
                        'mint_status' => 'minting_queued',
                        'flow_type' => 'direct_mint'
                    ]
                ]);
            }

            return redirect()->route('mint.show', $blockchainRecord->id)
                ->with('success', __('mint.notification.processing_message'));

        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->errorManager->handle('DIRECT_MINT_VALIDATION_ERROR', [
                'user_id' => Auth::id(),
                'errors' => $e->errors(),
            ], $e);

            if ($request->wantsJson()) {
                return response()->json([], 422);
            }
            return redirect()->back()->withErrors(['error' => __('mint.errors.validation_failed')]);

        } catch (\Exception $e) {
            $this->errorManager->handle('DIRECT_MINT_PROCESS_ERROR', [
                'user_id' => Auth::id(),
                'egi_id' => $id,
                'payment_method' => $request->input('payment_method'),
                'error' => $e->getMessage()
            ], $e);

            if ($request->wantsJson()) {
                return response()->json([], 500);
            }
            return redirect()->back()->withErrors(['error' => __('mint.errors.mint_failed')]);
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
    public function showMintResult(int|string $egiBlockchainId) {
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

            // Check if current user is the owner (buyer who minted the EGI)
            // FALLBACK: Se buyer_user_id è null (mint vecchio o owner self-mint), usa egi.user_id (creator)
            $buyerId = $blockchain->buyer_user_id ?? $egi->user_id;
            // CAST ESPLICITO: Evita type mismatch int vs string
            $isOwner = Auth::check() && (int)Auth::id() === (int)$buyerId;

            $this->logger->info('Mint result page viewed', [
                'user_id' => Auth::id(),
                'egi_id' => $egi->id,
                'blockchain_id' => $blockchain->id,
                'buyer_user_id_raw' => $blockchain->buyer_user_id,
                'buyer_user_id_fallback' => $buyerId,
                'egi_creator' => $egi->user_id,
                'is_owner' => $isOwner,
                'auth_check' => Auth::check(),
                'user_id_match' => Auth::id() === $buyerId,
                'certificate_exists' => !is_null($certificate)
            ]);

            return view('mint.mint', compact('egi', 'blockchain', 'certificate', 'paymentBreakdown', 'salePrice', 'isOwner', 'buyerId'));
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
            // FALLBACK: Se buyer_user_id è null (owner self-mint), usa egi.user_id (creator)
            $buyerId = $blockchain->buyer_user_id ?? $blockchain->egi->user_id;

            if ((int)$buyerId !== (int)Auth::id()) {
                $this->logger->warning('Unauthorized certificate regeneration attempt', [
                    'user_id' => Auth::id(),
                    'blockchain_id' => $egiBlockchainId,
                    'buyer_user_id_raw' => $blockchain->buyer_user_id,
                    'buyer_user_id_fallback' => $buyerId,
                    'egi_creator' => $blockchain->egi->user_id
                ]);

                return redirect()->back()->withErrors(['error' => __('errors.unauthorized')]);
            }

            $this->logger->info('Authorization check passed for certificate regeneration', [
                'user_id' => Auth::id(),
                'buyer_user_id_used' => $buyerId,
                'buyer_user_id_raw' => $blockchain->buyer_user_id
            ]);

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
