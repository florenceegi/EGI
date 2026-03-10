<?php

namespace App\Http\Controllers;

use App\Models\UserPaymentMethod;
use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;
use App\Services\Gdpr\AuditLogService;
use App\Enums\Gdpr\GdprActivityCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use App\Services\Payment\StripeConnectService;
use App\Services\OnboardingChecklistService;

/**
 * PaymentSettingsController
 *
 * Manages user and collection payment method configurations.
 * Adheres to Ultra Standards for logging, error handling, and GDPR compliance.
 *
 * @package App\Http\Controllers
 */
class PaymentSettingsController extends Controller {
    /**
     * @var array Available payment methods on the platform
     */
    /**
     * ToS v3.0.0: 'egili' RIMOSSO — gli Egili NON sono un metodo di pagamento
     * per l'acquisto di EGI. Sono crediti AI ottenibili solo via Pacchetti AI o merit reward.
     */
    public const AVAILABLE_METHODS = [
        'stripe' => [
            'name' => 'Stripe',
            'description' => 'Accept card payments via Stripe Connect',
            'icon' => 'credit-card',
            'requires_connect' => true,
        ],
        'bank_transfer' => [
            'name' => 'Bank Transfer',
            'description' => 'Direct bank transfer payments',
            'icon' => 'building',
            'requires_iban' => true,
        ],
    ];

    public function __construct(
        protected ErrorManagerInterface $errorManager,
        protected UltraLogManager $logger,
        protected AuditLogService $auditService,
        protected StripeConnectService $stripeConnect,
        protected OnboardingChecklistService $checklistService
    ) {
    }

    /**
     * Display the payment settings page
     */
    public function index() {
        try {
            $user = Auth::user();

            // Access restricted to users who can receive payments (including collectors for rebind)
            if (!$user->isSeller()) {
                return redirect()->route('home')
                    ->with('error', __('payment.settings_restricted_to_sellers'));
            }

            $userMethods = $user->paymentMethods()->get()->keyBy('method');

            $this->logger->info('[PaymentSettings][Index] Page loaded', [
                'user_id' => $user->id,
                'methods_count' => $userMethods->count()
            ]);

            return view('settings.payments.index', [
                'user' => $user,
                'availableMethods' => self::AVAILABLE_METHODS,
                'userMethods' => $userMethods,
                'stripeConnected' => !empty($user->stripe_account_id),
            ]);
        } catch (\Exception $e) {
            return $this->errorManager->handle('PAYMENT_SETTINGS_INDEX_ERROR', [
                'user_id' => Auth::id() ?? 'guest',
                'error' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Toggle a payment method on/off
     */
    public function toggle(Request $request, string $method) {
        try {
            $user = Auth::user();

            if (!$user->isSeller()) {
                throw new \Exception(__('payment.settings_restricted_to_sellers'), 403);
            }

            if (!array_key_exists($method, self::AVAILABLE_METHODS)) {
                throw new \Exception(__('payment.invalid_method'), 400);
            }

            $paymentMethod = $user->paymentMethods()->firstOrCreate(
                ['method' => $method],
                ['is_enabled' => false, 'config' => []]
            );

            $oldState = $paymentMethod->is_enabled;
            $paymentMethod->is_enabled = !$oldState;
            $paymentMethod->save();

            // AUDIT & LOG
            $this->logAction($user, 'payment_method_toggled', [
                'method' => $method,
                'old_state' => $oldState,
                'new_state' => $paymentMethod->is_enabled
            ]);

            return response()->json([
                'success' => true,
                'is_enabled' => $paymentMethod->is_enabled,
                'message' => $paymentMethod->is_enabled
                    ? __('payment.method_enabled', ['method' => self::AVAILABLE_METHODS[$method]['name']])
                    : __('payment.method_disabled', ['method' => self::AVAILABLE_METHODS[$method]['name']])
            ]);
        } catch (\Exception $e) {
            return $this->jsonError('PAYMENT_TOGGLE_ERROR', $e);
        }
    }

    /**
     * Set a payment method as default
     */
    public function setDefault(Request $request, string $method) {
        try {
            $user = Auth::user();

            if (!$user->isSeller()) {
                throw new \Exception(__('payment.settings_restricted_to_sellers'), 403);
            }

            $paymentMethod = $user->paymentMethods()
                ->where('method', $method)
                ->where('is_enabled', true)
                ->first();

            if (!$paymentMethod) {
                throw new \Exception(__('payment.must_enable_first'), 400);
            }

            // Remove default from all other methods
            $user->paymentMethods()->where('is_default', true)->update(['is_default' => false]);

            // Set this one as default
            $paymentMethod->is_default = true;
            $paymentMethod->save();

            $this->logAction($user, 'payment_method_set_default', ['method' => $method]);

            return response()->json([
                'success' => true,
                'message' => __('payment.default_set', ['method' => self::AVAILABLE_METHODS[$method]['name']])
            ]);
        } catch (\Exception $e) {
            return $this->jsonError('PAYMENT_SET_DEFAULT_ERROR', $e);
        }
    }

    /**
     * Update bank transfer configuration (IBAN)
     */
    public function updateBankConfig(Request $request) {
        try {
            $user = Auth::user();

            if (!$user->isSeller()) {
                throw new \Exception(__('payment.settings_restricted_to_sellers'), 403);
            }

            $validator = Validator::make($request->all(), [
                'iban' => ['required', 'string', 'min:15', 'max:34', 'regex:/^[A-Z]{2}[0-9]{2}[A-Z0-9]{4,30}$/'],
                'bic' => ['nullable', 'string', 'max:11', 'regex:/^[A-Z]{6}[A-Z0-9]{2}([A-Z0-9]{3})?$/'],
                'holder' => ['required', 'string', 'max:100'],
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            $wallet = $user->primaryWallet;
            if (!$wallet) {
                throw new \Exception("User {$user->id} has no primary wallet available for payment configuration.");
            }

            // Create or update WalletDestination for BANK_TRANSFER
            $destination = \App\Models\WalletDestination::updateOrCreate(
                [
                    'wallet_id' => $wallet->id,
                    'payment_type' => \App\Enums\Payment\PaymentTypeEnum::BANK_TRANSFER->value,
                ],
                [
                    'destination_value' => strtoupper(str_replace(' ', '', $request->iban)), // Automatically encrypted by mutator
                    'is_verified' => false, // Verify manually or via API
                    'is_primary' => true, // Default to primary if setting up
                    'metadata' => [
                        'bic' => $request->bic ? strtoupper($request->bic) : null,
                        'holder' => $request->holder,
                    ]
                ]
            );

            // Legacy: also update UserPaymentMethod for backward compatibility if needed,
            // but for now we focus on the new architecture.
            // Use the UserPaymentMethod only if strictly required by other legacy parts not yet refactored.
            // ... (Skipping legacy update as per instruction to use WalletDestination)

            // GDPR AUDIT - Sensitive Financial Data
            $this->auditService->logUserAction(
                $user,
                'payment_bank_details_updated',
                ['destination_id' => $destination->id],
                GdprActivityCategory::WALLET_MANAGEMENT
            );

            $this->logger->info('[Payment] Bank details updated via WalletDestination', ['user_id' => $user->id, 'wallet_id' => $wallet->id]);

            return response()->json([
                'success' => true,
                'message' => __('payment.bank_details_saved')
            ]);
        } catch (\Exception $e) {
            return $this->jsonError('PAYMENT_BANK_CONFIG_ERROR', $e);
        }
    }

    /**
     * Update Stripe configuration (Manual Account ID)
     */
    public function updateStripeConfig(Request $request) {
        try {
            $user = Auth::user();

            if (!$user->isSeller()) {
                throw new \Exception(__('payment.settings_restricted_to_sellers'), 403);
            }

            $validator = Validator::make($request->all(), [
                'stripe_account_id' => ['required', 'string', 'starts_with:acct_', 'max:255'],
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            $wallet = $user->primaryWallet;
            if (!$wallet) {
                throw new \Exception("User {$user->id} has no primary wallet available for payment configuration.");
            }

            // Update WalletDestination for STRIPE
            $destination = \App\Models\WalletDestination::updateOrCreate(
                [
                    'wallet_id' => $wallet->id,
                    'payment_type' => \App\Enums\Payment\PaymentTypeEnum::STRIPE->value,
                ],
                [
                    'destination_value' => $request->stripe_account_id,
                    'is_verified' => true, // Assuming manual entry is trusted or verified elsewhere
                    'is_primary' => true,
                    'metadata' => ['connected_at' => now()->toIso8601String()]
                ]
            );

            // Deprecated: Update User model directly (legacy) - KEEPING for safety until full deprecation
            $user->stripe_account_id = $request->stripe_account_id;
            $user->save();

            // GDPR AUDIT - Financial Data
            $this->auditService->logUserAction(
                $user,
                'payment_stripe_connected_manually',
                ['stripe_account_id' => $request->stripe_account_id, 'destination_id' => $destination->id],
                GdprActivityCategory::WALLET_MANAGEMENT
            );

            $this->logger->info('[Payment] Stripe connected via WalletDestination', ['user_id' => $user->id, 'wallet_id' => $wallet->id]);

            return response()->json([
                'success' => true,
                'message' => __('payment.stripe_connected')
            ]);
        } catch (\Exception $e) {
            return $this->jsonError('PAYMENT_STRIPE_CONFIG_ERROR', $e);
        }
    }

    /**
     * Get available payment methods for API
     */
    public function getAvailable() {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json(['success' => false, 'message' => __('auth.unauthorized')], 401);
            }

            $methods = [];
            foreach (self::AVAILABLE_METHODS as $key => $info) {
                $userMethod = $user->paymentMethods()->where('method', $key)->first();
                $methods[$key] = [
                    'name' => $info['name'],
                    'description' => $info['description'],
                    'is_enabled' => $userMethod?->is_enabled ?? false,
                    'is_default' => $userMethod?->is_default ?? false,
                    'has_config' => $userMethod && $key === 'bank_transfer' ? $userMethod->hasBankConfig() : true,
                ];
            }

            return response()->json([
                'success' => true,
                'methods' => $methods,
            ]);
        } catch (\Exception $e) {
            return $this->jsonError('PAYMENT_API_GET_ERROR', $e);
        }
    }

    // ==========================================
    // COLLECTION METHODS
    // ==========================================

    // ==========================================
    // MODAL & API
    // ==========================================

    /**
     * Display the payment settings modal content.
     */
    public function modal(): View {
        $user = Auth::user();

        // 1. Get User Payment Methods (from DB) - Legacy fallback
        $userMethods = $user->paymentMethods->keyBy('method');

        // 2. Load Wallet Destinations (New Source of Truth)
        $wallet = $user->primaryWallet;
        $destinations = $wallet
            ? $wallet->destinations()->get()->keyBy('payment_type')
            : collect();

        // 3. Resolve Stripe Configuration
        $stripeDest = $destinations->get(\App\Enums\Payment\PaymentTypeEnum::STRIPE->value);
        $stripeAccountId = $stripeDest?->destination_value;

        // 4. Resolve Bank Configuration
        $bankDest = $destinations->get(\App\Enums\Payment\PaymentTypeEnum::BANK_TRANSFER->value);
        $bankDetails = [
            // Decrypted value via accessor if accessible, otherwise direct decryption if needed
            // The model accessor is getDecryptedValueAttribute
            'iban' => $bankDest?->decrypted_value,
            'bic' => $bankDest?->metadata['bic'] ?? null,
            'holder' => $bankDest?->metadata['holder'] ?? null,
        ];

        // 5. Define Available Methods
        $availableMethods = self::AVAILABLE_METHODS;

        // 6. Prepare View Data
        return view('settings.payments.modal-content', [
            'user' => $user,
            'userMethods' => $userMethods,
            'availableMethods' => $availableMethods,
            'stripeConnected' => !empty($stripeAccountId),
            'stripeAccountId' => $stripeAccountId, // New variable for View
            'bankDetails' => $bankDetails,          // New variable for View
            'pspName' => config('egi.payment.psp_name', 'FlorenceEGI Payment System'),
        ]);
    }

    /**
     * Start Stripe Connect guided onboarding.
     * Creates Express account (idempotent) + returns hosted onboarding URL.
     *
     * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
     */
    public function startStripeOnboarding(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $wallet = $user->primaryWallet;

            if (!$wallet) {
                return response()->json(['success' => false, 'message' => __('payment.wizard.no_wallet')], 422);
            }

            $result = $this->stripeConnect->ensureExpressAccount($wallet, $user);
            $accountId = $result['account']['id'];

            $onboardingUrl = $this->stripeConnect->createAccountLink(
                $accountId,
                route('settings.payments.stripe.return'),
                route('settings.payments.stripe.refresh')
            );

            if (!$onboardingUrl) {
                return response()->json(['success' => false, 'message' => __('payment.wizard.link_failed')], 500);
            }

            $this->logger->info('[PaymentSettings] Stripe onboarding started', [
                'user_id' => $user->id,
                'stripe_account_id' => $accountId,
            ]);

            return response()->json(['success' => true, 'url' => $onboardingUrl]);
        } catch (\Exception $e) {
            $this->logger->error('[PaymentSettings] Stripe onboarding failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);
            return response()->json(['success' => false, 'message' => __('payment.wizard.link_failed')], 500);
        }
    }

    /**
     * Handle Stripe return URL after successful onboarding.
     * Refreshes checklist cache and redirects with success flash.
     */
    public function stripeReturn(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $this->checklistService->refreshChecklist($user, 'creator');

        $this->logger->info('[PaymentSettings] Stripe onboarding completed', ['user_id' => $user->id]);

        return redirect(url('/'))->with('success', __('payment.wizard.success'));
    }

    /**
     * Handle Stripe refresh URL (hosted onboarding link expired).
     * Prompts user to restart the wizard.
     */
    public function stripeRefresh(Request $request): RedirectResponse
    {
        return redirect(url('/'))->with('info', __('payment.wizard.refresh'));
    }

    /**
     * Display the payment settings page for a specific collection
     */
    public function indexCollection(Collection $collection) {
        try {
            $this->authorize('update', $collection);

            $user = Auth::user();
            $collectionMethods = $collection->paymentMethods()->get()->keyBy('method');
            $userMethods = $user->paymentMethods()->get()->keyBy('method');

            $this->logger->info('[PaymentSettings][Collection] Page loaded', [
                'user_id' => $user->id,
                'collection_id' => $collection->id
            ]);

            return view('settings.payments.collection', [
                'user' => $user,
                'collection' => $collection,
                'availableMethods' => self::AVAILABLE_METHODS,
                'collectionMethods' => $collectionMethods,
                'userMethods' => $userMethods,
                'stripeConnected' => !empty($user->stripe_account_id),
            ]);
        } catch (\Exception $e) {
            return $this->errorManager->handle('COLLECTION_PAYMENT_INDEX_ERROR', [
                'collection_id' => $collection->id ?? 'unknown',
                'error' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Toggle a payment method for a collection
     */
    public function toggleCollection(Request $request, Collection $collection, string $method) {
        try {
            $this->authorize('update', $collection);

            if (!array_key_exists($method, self::AVAILABLE_METHODS)) {
                throw new \Exception(__('payment.invalid_method'), 400);
            }

            $paymentMethod = $collection->paymentMethods()->firstOrCreate(
                ['method' => $method],
                ['is_enabled' => false, 'config' => []]
            );

            $oldState = $paymentMethod->is_enabled;
            $paymentMethod->is_enabled = !$oldState;
            $paymentMethod->save();

            // AUDIT & LOG
            $this->logCollectionAction($collection, 'collection_payment_toggled', [
                'method' => $method,
                'new_state' => $paymentMethod->is_enabled
            ]);

            return response()->json([
                'success' => true,
                'is_enabled' => $paymentMethod->is_enabled,
                'message' => $paymentMethod->is_enabled
                    ? __('payment.collection_method_enabled', ['method' => self::AVAILABLE_METHODS[$method]['name']])
                    : __('payment.collection_method_disabled', ['method' => self::AVAILABLE_METHODS[$method]['name']])
            ]);
        } catch (\Exception $e) {
            return $this->jsonError('COLLECTION_PAYMENT_TOGGLE_ERROR', $e);
        }
    }

    /**
     * Set a payment method as default for a collection
     */
    public function setDefaultCollection(Request $request, Collection $collection, string $method) {
        try {
            $this->authorize('update', $collection);

            $paymentMethod = $collection->paymentMethods()
                ->where('method', $method)
                ->where('is_enabled', true)
                ->first();

            if (!$paymentMethod) {
                throw new \Exception(__('payment.collection_must_enable_first'), 400);
            }

            $collection->paymentMethods()->where('is_default', true)->update(['is_default' => false]);
            $paymentMethod->is_default = true;
            $paymentMethod->save();

            $this->logCollectionAction($collection, 'collection_payment_set_default', ['method' => $method]);

            return response()->json([
                'success' => true,
                'message' => __('payment.collection_default_set', ['method' => self::AVAILABLE_METHODS[$method]['name']])
            ]);
        } catch (\Exception $e) {
            return $this->jsonError('COLLECTION_PAYMENT_DEFAULT_ERROR', $e);
        }
    }

    /**
     * Update bank transfer configuration for a collection
     */
    public function updateBankConfigCollection(Request $request, Collection $collection) {
        try {
            $this->authorize('update', $collection);

            $validator = Validator::make($request->all(), [
                'iban' => ['required', 'string', 'min:15', 'max:34', 'regex:/^[A-Z]{2}[0-9]{2}[A-Z0-9]{4,30}$/'],
                'bic' => ['nullable', 'string', 'max:11', 'regex:/^[A-Z]{6}[A-Z0-9]{2}([A-Z0-9]{3})?$/'],
                'holder' => ['required', 'string', 'max:100'],
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            $paymentMethod = $collection->paymentMethods()->updateOrCreate(
                ['method' => 'bank_transfer'],
                [
                    'is_enabled' => true,
                    'config' => [
                        'iban' => strtoupper(str_replace(' ', '', $request->iban)),
                        'bic' => $request->bic ? strtoupper($request->bic) : null,
                        'holder' => $request->holder,
                    ],
                ]
            );

            // GDPR AUDIT for Collection - sensitive financial data
            $this->auditService->logUserAction(
                Auth::user(),
                'collection_bank_details_updated',
                [
                    'collection_id' => $collection->id,
                    'method_id' => $paymentMethod->id
                ],
                GdprActivityCategory::FINANCIAL
            );

            $this->logger->info('[Payment][Collection] Bank details updated', [
                'collection_id' => $collection->id,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => __('payment.collection_bank_details_saved')
            ]);
        } catch (\Exception $e) {
            return $this->jsonError('COLLECTION_PAYMENT_BANK_ERROR', $e);
        }
    }

    // ==========================================
    // PRIVATE HELPERS
    // ==========================================

    /**
     * Helper to log actions efficiently
     */
    private function logAction($user, $action, $data) {
        $this->logger->info("[Payment] {$action}", array_merge(['user_id' => $user->id], $data));

        $this->auditService->logUserAction(
            $user,
            $action,
            $data,
            GdprActivityCategory::WALLET_MANAGEMENT
        );
    }

    /**
     * Helper to log collection actions efficiently
     */
    private function logCollectionAction($collection, $action, $data) {
        $user = Auth::user();
        $this->logger->info("[Payment][Collection] {$action}", array_merge([
            'user_id' => $user->id,
            'collection_id' => $collection->id
        ], $data));

        $this->auditService->logUserAction(
            $user,
            $action,
            array_merge(['collection_id' => $collection->id], $data),
            GdprActivityCategory::WALLET_MANAGEMENT
        );
    }

    /**
     * Helper to handle JSON errors consistently
     */
    private function jsonError(string $code, \Exception $e) {
        // Log the full error safely
        $this->errorManager->handle($code, [
            'user_id' => Auth::id() ?? 'guest',
            'error' => $e->getMessage()
        ], $e);

        // Return user-friendly JSON (masking internal error if production, though check app config if needed)
        // For simple controllers, message is enough. Exception code 403/400 used if thrown explicitly.
        $status = $e->getCode() && is_int($e->getCode()) && $e->getCode() >= 400 ? $e->getCode() : 500;

        return response()->json([
            'success' => false,
            'message' => $e->getMessage() ?: __('payment.generic_error')
        ], $status);
    }
}
