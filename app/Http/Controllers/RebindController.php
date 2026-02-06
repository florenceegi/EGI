<?php

/**
 * @package App\Http\Controllers
 * @author FlorenceEGI Team
 * @version 1.1.0
 * @date 2025-01-XX
 * @purpose Controller for EGI Rebind (Secondary Market)
 *
 * Rebind allows the owner of a minted EGI to sell it on the secondary market.
 * This is different from the primary mint process - the EGI is already on-chain
 * and ownership is transferred from the current owner to the new buyer.
 *
 * Payment Flow:
 * - Supports Stripe, PayPal, and Egili payment methods
 * - Payment goes to seller (current owner) minus platform fees
 * - Creator royalty (wallets.royalty_rebind) may apply
 */

namespace App\Http\Controllers;

use App\Models\Egi;
use App\Models\PaymentDistribution;
use App\Enums\PaymentDistribution\UserTypeEnum;
use App\Enums\PaymentDistribution\DistributionStatusEnum;
use App\DataTransferObjects\Payment\PaymentRequest;
use App\Exceptions\Payment\MerchantAccountNotConfiguredException;
use App\Services\Payment\PaymentServiceFactory;
use App\Services\Payment\StripeRealPaymentService;
use App\Services\Payment\PayPalRealPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Services\Gdpr\AuditLogService;
use App\Enums\Gdpr\GdprActivityCategory;
use App\Services\EgiAvailabilityService;
use App\Services\EgiliService;
use App\Services\Payment\MerchantAccountResolver;
use App\Helpers\FegiAuth;
use App\Models\UserShippingAddress; // Added
use App\Notifications\Commerce\EgiSoldNotification; // Added
use App\Services\Commerce\EgiListingService; // Added
use Illuminate\Support\Facades\Notification; // Added

class RebindController extends Controller {
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;
    protected AuditLogService $auditService;
    protected EgiAvailabilityService $availabilityService;
    protected MerchantAccountResolver $merchantAccountResolver;
    protected PaymentServiceFactory $paymentFactory;
    protected EgiListingService $listingService; // Added

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService,
        EgiAvailabilityService $availabilityService,
        MerchantAccountResolver $merchantAccountResolver,
        PaymentServiceFactory $paymentFactory,
        EgiListingService $listingService // Added
    ) {
        $this->middleware('auth');
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditService = $auditService;
        $this->availabilityService = $availabilityService;
        $this->merchantAccountResolver = $merchantAccountResolver;
        $this->paymentFactory = $paymentFactory;
        $this->listingService = $listingService; // Added
    }

    /**
     * Show the Rebind checkout page (Secondary Market Purchase)
     *
     * @param int $id EGI ID
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show(int $id, Request $request) {
        try {
            $egi = Egi::with(['utility.media', 'user', 'owner', 'collection', 'blockchain'])
                ->findOrFail($id);

            // Check availability using service
            $availability = $this->availabilityService->checkAvailability($egi, Auth::user());

            // Verify user can rebind this EGI
            if (!$availability['can_rebind']) {
                $reason = $availability['rebind_reason'] ?? 'not_available';

                $this->logger->warning('REBIND_NOT_AVAILABLE', [
                    'user_id' => Auth::id(),
                    'egi_id' => $egi->id,
                    'reason' => $reason,
                    'is_owner' => $availability['is_owner'] ?? false,
                ]);

                return redirect()
                    ->route('egis.show', $egi->id)
                    ->withErrors(['error' => __('rebind.errors.not_available')]);
            }

            // GDPR audit log
            $this->auditService->logUserAction(
                Auth::user(),
                'Rebind checkout viewed',
                [
                    'egi_id' => $egi->id,
                    'egi_title' => $egi->title,
                    'price' => $egi->price,
                    'current_owner_id' => $egi->owner_id,
                ],
                GdprActivityCategory::BLOCKCHAIN_ACTIVITY
            );

            $this->logger->info('REBIND_CHECKOUT_VIEWED', [
                'user_id' => Auth::id(),
                'egi_id' => $egi->id,
                'price' => $egi->price,
                'owner_id' => $egi->owner_id,
            ]);

            // Payment amount = EGI price (set by owner for secondary sale)
            $paymentAmountEur = $egi->price ?? 0;

            // Check Egili payment option
            $showEgiliOption = false;
            $canPayWithEgili = false;
            $egiliBalance = 0;
            $requiredEgili = 0;

            if ($egi->payment_by_egili && $paymentAmountEur > 0) {
                /** @var EgiliService $egiliService */
                $egiliService = app(EgiliService::class);
                $egiliBalance = $egiliService->getBalance(Auth::user());
                $requiredEgili = max($egiliService->fromEur($paymentAmountEur), 1);
                $showEgiliOption = true;
                $canPayWithEgili = $egiliBalance >= $requiredEgili;
            }

            // Validate payment methods
            $seller = $egi->owner;
            if (!$seller) {
                return redirect()
                    ->route('egis.show', $egi->id)
                    ->withErrors(['error' => __('rebind.errors.checkout_error')]);
            }

            $stripeValidation = $this->merchantAccountResolver->validateUserWallets($seller, 'stripe');
            $paypalValidation = $this->merchantAccountResolver->validateUserWallets($seller, 'paypal');

            $stripeMerchantAvailable = $stripeValidation['can_accept_payments'];
            $paypalAvailable = $paypalValidation['can_accept_payments'];

            // Determine user-friendly error messages
            $stripeMerchantError = null;
            if (!$stripeMerchantAvailable) {
                $stripeMerchantError = __('payment.errors.merchant_account_incomplete');
            }

            // ... (previous validation code) ...

            // 1. Check if shipping is required
            $shippingRequired = $this->listingService->shippingRequiredForEgi($egi);
            $shippingAddresses = [];

            if ($shippingRequired) {
                // Fetch user's shipping addresses
                $shippingAddresses = UserShippingAddress::where('user_id', Auth::id())
                    ->orderBy('is_default', 'desc')
                    ->get();
            }

            return view('rebind.checkout', [
                'egi' => $egi,
                'owner' => $egi->owner,
                'paymentAmountEur' => $paymentAmountEur,
                'showEgiliOption' => $showEgiliOption,
                'canPayWithEgili' => $canPayWithEgili,
                'egiliBalance' => $egiliBalance,
                'requiredEgili' => $requiredEgili,
                'stripeMerchantAvailable' => $stripeMerchantAvailable,
                'stripeMerchantError' => $stripeMerchantError,
                'paypalAvailable' => $paypalAvailable,
                'stripePublicKey' => config('services.stripe.key'),
                'shippingRequired' => $shippingRequired, // Added
                'shippingAddresses' => $shippingAddresses, // Added
            ]);
        } catch (\Exception $e) {
            $this->errorManager->handle('REBIND_CHECKOUT_ERROR', [
                'user_id' => Auth::id(),
                'egi_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->route('egis.show', $id)
                ->withErrors(['error' => __('rebind.errors.checkout_error')]);
        }
    }

    /**
     * Process the Rebind purchase (transfer ownership on secondary market)
     *
     * @param int $id EGI ID
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function process(int $id, Request $request) {
        try {
            $egi = Egi::with(['owner', 'collection'])->findOrFail($id);

            // 1. Check shipping requirements FIRST
            $shippingRequired = $this->listingService->shippingRequiredForEgi($egi);
            $shippingAddressSnapshot = null;

            $validationRules = [
                'payment_method' => 'required|string|in:stripe,paypal,egili',
            ];

            if ($shippingRequired) {
                $validationRules['shipping_address_id'] = [
                    'required',
                    'exists:user_shipping_addresses,id,user_id,' . Auth::id()
                ];
            }

            $validated = $request->validate($validationRules);

            if ($shippingRequired) {
                // Fetch the selected address
                $address = UserShippingAddress::find($validated['shipping_address_id']);
                // Create snapshot (array)
                $shippingAddressSnapshot = $address->toArray();
            }

            // ... availability check ...
            // Re-verify availability (prevent race conditions)
            $availability = $this->availabilityService->checkAvailability($egi, Auth::user());

            if (!$availability['can_rebind']) {
               // ... (existing warning code) ...
               // (This block is unchanged, just context for placement)
                 $this->logger->warning('REBIND_PROCESS_NOT_AVAILABLE', [
                    'user_id' => Auth::id(),
                    'egi_id' => $egi->id,
                    'reason' => $availability['rebind_reason'] ?? 'unknown',
                ]);

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'error' => __('rebind.errors.not_available'),
                    ], 403);
                }

                return redirect()
                    ->route('egis.show', $egi->id)
                    ->withErrors(['error' => __('rebind.errors.not_available')]);
            }
            
            // ... (price check code unchanged) ...
             $paymentAmountEur = $egi->price ?? 0;

            if ($paymentAmountEur <= 0) {
                 // ... (existing price error code) ...
                  $this->logger->error('REBIND_INVALID_PRICE', [
                    'user_id' => Auth::id(),
                    'egi_id' => $egi->id,
                    'price' => $paymentAmountEur,
                ]);

                return redirect()->back()->withErrors(['error' => __('rebind.errors.invalid_price')]);
            }

            $paymentMethod = $validated['payment_method']; // Already defined
            $paymentProvider = null;
            $paymentReference = null;
            $paidCurrency = 'EUR';
            $paidAmountRecorded = $paymentAmountEur;
            $egiliMetadata = [];
            $paymentMetadata = [];

            $this->logger->info('REBIND_PROCESS_STARTED', [
                'user_id' => Auth::id(),
                'egi_id' => $egi->id,
                'price' => $egi->price,
                'current_owner_id' => $egi->owner_id,
                'payment_method' => $paymentMethod,
                'shipping_required' => $shippingRequired, // Added log context
            ]);

            // === PAYMENT PROCESSING ===
            if ($paymentMethod === 'egili') {
                try {
                    $egiliPayment = $this->handleEgiliRebindPayment($egi, $paymentAmountEur);

                    $paymentProvider = 'egili_internal';
                    $paymentReference = $egiliPayment['reference'];
                    $paidCurrency = 'EGL';
                    $paidAmountRecorded = $egiliPayment['amount_eur'];
                    $egiliMetadata = [
                        'egili_transaction_id' => $egiliPayment['transaction_id'] ?? null,
                        'amount_egili' => $egiliPayment['amount_egili'] // Store actual tokens paid in metadata
                    ];
                } catch (RuntimeException $egiliException) {
                    $reason = $egiliException->getMessage();

                    $this->logger->warning('REBIND_EGILI_PAYMENT_FAILED', [
                        'user_id' => Auth::id(),
                        'egi_id' => $egi->id,
                        'reason' => $reason,
                    ]);

                    $errorMessageKey = match ($reason) {
                        'insufficient_egili' => 'rebind.errors.insufficient_egili',
                        'egili_disabled' => 'rebind.errors.egili_disabled',
                        'unauthenticated' => 'rebind.errors.unauthorized',
                        default => 'rebind.errors.payment_failed',
                    };

                    return redirect()->back()->withErrors(['error' => __($errorMessageKey)]);
                }
            } else {
                // Stripe or PayPal payment
                $seller = $egi->owner;
                if (!$seller) {
                    return redirect()->back()->withErrors(['error' => __('rebind.errors.merchant_not_configured')]);
                }

                $merchantValidation = $this->merchantAccountResolver->validateUserWallets($seller, $paymentMethod);
                if (!$merchantValidation['can_accept_payments']) {
                    return redirect()->back()->withErrors(['error' => __('rebind.errors.merchant_not_configured')]);
                }

                try {
                    $paymentService = $this->paymentFactory->create($paymentMethod);
                } catch (\Throwable $factoryException) {
                    $this->errorManager->handle('REBIND_PAYMENT_PROVIDER_UNAVAILABLE', [
                        'provider' => $paymentMethod,
                        'error' => $factoryException->getMessage(),
                    ], $factoryException);

                    return redirect()->back()->withErrors(['error' => __('rebind.errors.payment_failed')]);
                }

                $merchantContext = [];

                try {
                    if ($paymentService instanceof StripeRealPaymentService || $paymentService instanceof PayPalRealPaymentService) {
                        $seller = $egi->owner;
                        if (!$seller) {
                            throw new RuntimeException('seller_not_found');
                        }

                        $merchantContext = $this->merchantAccountResolver
                            ->resolveForUserAndProvider($seller, $paymentMethod);
                    }
                } catch (MerchantAccountNotConfiguredException $merchantException) {
                    return redirect()->back()->withErrors(['error' => __('rebind.errors.merchant_not_configured')]);
                } catch (\Throwable $resolverException) {
                    $this->errorManager->handle('REBIND_PAYMENT_PROVIDER_UNAVAILABLE', [
                        'provider' => $paymentMethod,
                        'error' => $resolverException->getMessage(),
                        'context' => 'merchant_account_resolver',
                    ], $resolverException);

                    return redirect()->back()->withErrors(['error' => __('rebind.errors.payment_failed')]);
                }

                $paymentRequest = (new PaymentRequest(
                    amount: $paymentAmountEur,
                    currency: 'EUR',
                    customerEmail: Auth::user()->email,
                    egiId: $egi->id,
                    reservationId: null, // No reservation for rebind
                    userId: Auth::id(),
                    metadata: [
                        'flow' => 'rebind',
                        'seller_id' => $egi->owner_id,
                        'initiated_at' => now()->toIso8601String(),
                    ],
                    successUrl: route('egi.rebind', ['id' => $egi->id]) . '?payment_success=1',
                    cancelUrl: route('egi.rebind', ['id' => $egi->id])
                ))->withMerchantContext($merchantContext);

                $paymentResultObject = $paymentService->processPayment($paymentRequest);

                // Handle redirect (PayPal, 3D Secure)
                if ($paymentResultObject->requiresAction() && $paymentResultObject->redirectUrl) {
                    $this->logger->info('REBIND_PAYMENT_REQUIRES_ACTION', [
                        'provider' => $paymentService->getProviderName(),
                        'payment_id' => $paymentResultObject->paymentId,
                        'redirect_url' => $paymentResultObject->redirectUrl,
                    ]);

                    return redirect()->away($paymentResultObject->redirectUrl);
                }

                if (!$paymentResultObject->success) {
                    $this->errorManager->handle('REBIND_PAYMENT_FAILED', [
                        'user_id' => Auth::id(),
                        'egi_id' => $egi->id,
                        'payment_method' => $paymentMethod,
                        'amount' => $paymentAmountEur,
                        'error' => $paymentResultObject->errorMessage,
                        'error_code' => $paymentResultObject->errorCode,
                    ]);

                    return redirect()->back()->withErrors([
                        'error' => $paymentResultObject->errorMessage ?? __('rebind.errors.payment_failed')
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

            // === OWNERSHIP TRANSFER ===
            $previousOwnerId = $egi->owner_id;
            $newOwnerId = Auth::id();

            // Create rebind distribution record (single source of truth for ownership history)
            $rebindDistribution = PaymentDistribution::create([
                'source_type' => 'rebind',
                'egi_id' => $egi->id,
                'collection_id' => $egi->collection_id,
                'seller_user_id' => $previousOwnerId,
                'buyer_user_id' => $newOwnerId,
                'user_id' => $previousOwnerId, // Seller receives payment
                'user_type' => UserTypeEnum::COLLECTOR,
                'sale_price' => $paymentAmountEur,
                'amount_eur' => $paidAmountRecorded,
                'percentage' => 100.00, // Full amount to seller (royalties will be separate distributions)
                'exchange_rate' => 1.0, // Default to 1.0 as base is EUR
                'distribution_status' => DistributionStatusEnum::CONFIRMED,
                'metadata' => [
                    'payment_method' => $paymentMethod,
                    'psp_provider' => $paymentProvider,
                    'payment_reference' => $paymentReference,
                    'paid_currency' => $paidCurrency,
                    'merchant_psp_config' => $paymentMetadata['merchant_psp'] ?? null,
                    'egili_metadata' => $egiliMetadata ?: null,
                ],
            ]);

            // Transfer ownership in database
            // Transfer ownership in database
            $egi->owner_id = $newOwnerId;
            $egi->save();

            // Update Blockchain Record (Transaction Context & Shipping)
            // We update this to reflect the "Current State" for the notification system
            if ($egi->blockchain) {
                $blockchainUpdate = [
                    'buyer_user_id' => $newOwnerId, // IMPORTANT: Notification uses this to identify buyer
                    'payment_method' => $paymentMethod,
                    // Reset shipping fields for new owner
                    'tracking_code' => null,
                    'carrier' => null,
                    'shipped_at' => null,
                ];

                if ($shippingRequired && $shippingAddressSnapshot) {
                    $blockchainUpdate['shipping_address_snapshot'] = $shippingAddressSnapshot;
                }

                $egi->blockchain->update($blockchainUpdate);
            }

            // Notification Trigger (Sold Notification to Seller)
            // We need to fetch the previous owner User object
            $previousOwner = \App\Models\User::find($previousOwnerId);
            if ($previousOwner && $egi->blockchain) {
                 // Refresh relation to ensuring buyer is loaded correctly
                 $egi->blockchain->load('buyer');
                 
                 // Create Payload Shipping Record (Persistent Notification Data)
                 $payload = \App\Models\NotificationPayloadShipping::create([
                     'egi_blockchain_id' => $egi->blockchain->id,
                     'seller_id' => $previousOwnerId,
                     'buyer_id' => $newOwnerId,
                     'shipping_address_snapshot' => $egi->blockchain->shipping_address_snapshot,
                     'status' => 'pending'
                 ]);
                 
                 $previousOwner->notify(new EgiSoldNotification($payload));
            }

            // GDPR audit log
            $this->auditService->logUserAction(
                Auth::user(),
                'Rebind purchase completed',
                [
                    'egi_id' => $egi->id,
                    'egi_title' => $egi->title,
                    'distribution_id' => $rebindDistribution->id,
                    'price' => $paymentAmountEur,
                    'paid_amount' => $paidAmountRecorded,
                    'paid_currency' => $paidCurrency,
                    'payment_method' => $paymentMethod,
                    'payment_provider' => $paymentProvider,
                    'payment_reference' => $paymentReference,
                    'seller_id' => $previousOwnerId,
                    'buyer_id' => $newOwnerId,
                    'shipping_included' => $shippingRequired,
                ],
                GdprActivityCategory::BLOCKCHAIN_ACTIVITY
            );

            $this->logger->info('REBIND_COMPLETED', [
                'user_id' => $newOwnerId,
                'egi_id' => $egi->id,
                'distribution_id' => $rebindDistribution->id,
                'previous_owner_id' => $previousOwnerId,
                'price' => $paymentAmountEur,
                'payment_method' => $paymentMethod,
                'payment_provider' => $paymentProvider,
                'notification_sent' => true,
            ]);

            // TODO: Future enhancements:
            // 1. Blockchain ownership transfer via Algorand atomic transfer
            // 2. (Done) Notification to seller and buyer
            // 3. Calculate and Distribute Royalties (Creator, EPP, Natan, Frangette)
            // Logic:
            // - Creator: Wallet->royalty_rebind OR config('egi.default_wallets.Creator.rebind_royalty')
            // - EPP: config('egi.default_wallets.EPP.rebind_royalty')
            // - Natan: config('egi.default_wallets.Natan.rebind_royalty')
            // - Frangette: config('egi.default_wallets.Ass_Frangette.rebind_royalty')

            $totalRoyaltyPercentage = 0.0;
            $royaltyDistributions = [];
            
            // Database-Driven Royalties (Source of Truth: Wallets Table)
            // This supports both Contributor and Normal profiles automatically
            $collectionWallets = $egi->collection->wallets;

            foreach ($collectionWallets as $wallet) {
                // Skip if no rebind royalty is configured for this wallet
                if ((float)$wallet->royalty_rebind <= 0) {
                    continue;
                }

                $percentage = (float)$wallet->royalty_rebind;
                $amount = round($paymentAmountEur * ($percentage / 100), 2);
                
                if ($amount <= 0) continue;

                $userId = $wallet->user_id;

                // Fallback for Reader/Legacy wallets if user_id missing (shouldn't happen on new logic)
                if (!$userId) {
                    if ($wallet->platform_role === 'Creator') {
                         $userId = $egi->collection->creator_id;
                    } elseif ($wallet->platform_role === 'Natan' || $wallet->platform_role === 'Ass_Frangette') {
                         $userId = config('egi.default_ids.natan_user_id');
                    } elseif ($wallet->platform_role === 'EPP') {
                         $userId = config('egi.default_ids.epp_user_id');
                    }
                }

                if (!$userId) continue;

                $royaltyDistributions[] = [
                    'role' => $wallet->platform_role,
                    'user_id' => $userId,
                    'wallet_id' => $wallet->id,
                    'amount' => $amount,
                    'percentage' => $percentage
                ];
                
                $totalRoyaltyPercentage += $percentage;
            }

            // Deduct total royalties from seller share
            // Ensure seller doesn't go negative (edge case protection)
            $totalRoyaltyAmount = collect($royaltyDistributions)->sum('amount');
            
            if ($totalRoyaltyAmount > 0 && $totalRoyaltyAmount < $paidAmountRecorded) {
                // Update Seller Distribution
                $rebindDistribution->amount_eur -= $totalRoyaltyAmount;
                $rebindDistribution->percentage = 100.0 - $totalRoyaltyPercentage;
                $rebindDistribution->save();
                
                // Create Royalty Distributions
                foreach ($royaltyDistributions as $dist) {
                    PaymentDistribution::create([
                        'source_type' => 'rebind',
                        'egi_id' => $egi->id,
                        'collection_id' => $egi->collection_id,
                        'seller_user_id' => $previousOwnerId,
                        'buyer_user_id' => $newOwnerId,
                        'user_id' => $dist['user_id'],
                        'wallet_id' => $dist['wallet_id'],
                        'user_type' => match($dist['role']) { // Map roles to UserTypeEnum
                            'Creator' => UserTypeEnum::CREATOR,
                            'EPP' => UserTypeEnum::EPP,
                            'Natan' => UserTypeEnum::NATAN,
                            'Frangette', 'Ass_Frangette' => UserTypeEnum::FRANGETTE,
                            default => UserTypeEnum::FRANGETTE // Fallback
                        },
                        'sale_price' => $paymentAmountEur,
                        'amount_eur' => $dist['amount'],
                        'percentage' => $dist['percentage'],
                        'exchange_rate' => 1.0,
                        'distribution_status' => DistributionStatusEnum::CONFIRMED,
                        'metadata' => [
                            'royalty_type' => strtolower($dist['role']) . '_rebind',
                            'origin_distribution_id' => $rebindDistribution->id,
                            'payment_method' => $paymentMethod,
                            'paid_currency' => $paidCurrency,
                        ],
                    ]);
                }
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('rebind.success.purchase_completed'),
                    'redirect' => route('egis.show', $egi->id),
                ]);
            }

            return redirect()
                ->route('collector.portfolio', Auth::id())
                ->with('success', __('rebind.success.purchase_completed'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->errorManager->handle('REBIND_VALIDATION_ERROR', [
                'user_id' => Auth::id(),
                'errors' => json_encode($e->errors()),
            ], $e);
            return redirect()->back()->withErrors(['error' => __('rebind.errors.validation_failed')]);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            $this->errorManager->handle('REBIND_STRIPE_ERROR', [
                'user_id' => Auth::id(),
                'egi_id' => $id,
                'error' => $e->getMessage(),
            ], $e);
            return redirect()->back()->withErrors(['error' => __('rebind.errors.payment_failed')]);
        } catch (\Exception $e) {
            $this->errorManager->handle('REBIND_PROCESS_ERROR', [
                'user_id' => Auth::id(),
                'egi_id' => $id,
                'error' => $e->getMessage(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => __('rebind.errors.process_error'),
                ], 500);
            }

            return redirect()
                ->route('egis.show', $id)
                ->withErrors(['error' => __('rebind.errors.process_error')]);
        }
    }

    /**
     * Handle Egili payment for rebind
     *
     * @param Egi $egi
     * @param float $amountEur
     * @return array
    * @throws RuntimeException
     */
    private function handleEgiliRebindPayment(Egi $egi, float $amountEur): array {
        if (!$egi->payment_by_egili) {
            throw new RuntimeException('egili_disabled');
        }

        $user = Auth::user();

        if (!$user) {
            throw new RuntimeException('unauthenticated');
        }

        /** @var EgiliService $egiliService */
        $egiliService = app(EgiliService::class);
        $requiredEgili = max($egiliService->fromEur($amountEur), 1);

        if (!$egiliService->canSpend($user, $requiredEgili)) {
            throw new RuntimeException('insufficient_egili');
        }

        $reference = 'EGL-REBIND-' . strtoupper(Str::random(12));

        $transaction = $egiliService->spend(
            $user,
            $requiredEgili,
            'egi_rebind',
            'rebind',
            [
                'egi_id' => $egi->id,
                'seller_id' => $egi->owner_id,
                'payment_reference' => $reference,
                'amount_eur' => $amountEur,
            ],
            $egi
        );

        $this->logger->info('REBIND_EGILI_PAYMENT_PROCESSED', [
            'user_id' => $user->id,
            'egi_id' => $egi->id,
            'seller_id' => $egi->owner_id,
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
}
