<?php

namespace App\Services;

use App\Models\User;
use App\Models\EgiliMerchantPurchase;
use App\Services\Gdpr\AuditLogService;
use App\Services\Gdpr\ConsentService;
use App\Services\Payment\PaymentServiceFactory;
use App\Services\Payment\CryptoPaymentGateway;
use App\DataTransferObjects\Payment\PaymentRequest;
use App\DataTransferObjects\Payment\CryptoPaymentRequest;
use App\Enums\Gdpr\GdprActivityCategory;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Notifications\EgiliPurchaseConfirmation;

/**
 * @Oracode Service: Egili Purchase Workflow Orchestrator
 * 🎯 Purpose: Unified orchestrator for Egili purchases (FIAT + Crypto)
 * 🧱 Core Logic: Atomic workflow → Payment → Mint Egili → Merchant Record → GDPR
 * 🛡️ GDPR Compliance: Full audit trail + merchant reconciliation records
 * 🛡️ MiCA-SAFE: Only merchant-to-customer sales, no custody
 * 
 * Workflow Steps:
 * 1. GDPR consent validation
 * 2. Price calculation + validation
 * 3. Create merchant purchase record (pending)
 * 4. Process payment via provider
 * 5. Mint Egili to user wallet (via EgiliService)
 * 6. Update merchant record (completed)
 * 7. GDPR audit trail
 * 8. Send confirmation email
 * 
 * @package App\Services
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Egili Purchase System)
 * @date 2025-11-02
 * @purpose Unified Egili purchase orchestrator with OS3 + GDPR compliance
 */
class EgiliPurchaseWorkflowService
{
    private UltraLogManager $logger;
    private ErrorManagerInterface $errorManager;
    private AuditLogService $auditService;
    private ConsentService $consentService;
    private EgiliService $egiliService;
    private PaymentServiceFactory $paymentServiceFactory;
    private CryptoPaymentGateway $cryptoGateway;

    /**
     * Constructor with full dependency injection
     *
     * @param UltraLogManager $logger
     * @param ErrorManagerInterface $errorManager
     * @param AuditLogService $auditService
     * @param ConsentService $consentService
     * @param EgiliService $egiliService
     * @param PaymentServiceFactory $paymentServiceFactory
     * @param CryptoPaymentGateway $cryptoGateway
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService,
        ConsentService $consentService,
        EgiliService $egiliService,
        PaymentServiceFactory $paymentServiceFactory,
        CryptoPaymentGateway $cryptoGateway
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditService = $auditService;
        $this->consentService = $consentService;
        $this->egiliService = $egiliService;
        $this->paymentServiceFactory = $paymentServiceFactory;
        $this->cryptoGateway = $cryptoGateway;
    }

    /**
     * Purchase Egili with FIAT payment (Stripe/PayPal)
     *
     * @param User $user Buyer
     * @param int $egiliAmount Quantity of Egili to purchase
     * @param string $provider Payment provider (stripe, paypal)
     * @param array $paymentData Additional payment data from frontend
     * @return array Purchase result with order details
     * @throws \Exception If purchase fails
     */
    public function purchaseWithFiat(
        User $user,
        int $egiliAmount,
        string $provider,
        array $paymentData = []
    ): array {
        try {
            // 1. ULM: Log workflow start
            $this->logger->info('Egili FIAT purchase workflow initiated', [
                'user_id' => $user->id,
                'egili_amount' => $egiliAmount,
                'payment_provider' => $provider,
                'log_category' => 'EGILI_PURCHASE_FIAT_START'
            ]);

            // 2. GDPR: Validate user consents
            $this->validateUserConsents($user);

            // 3. Validate purchase amount
            $this->validatePurchaseAmount($egiliAmount);

            // 4. Calculate pricing
            $pricing = $this->calculatePricing($egiliAmount);

            // 5. Create merchant purchase record (pending)
            $merchantPurchase = $this->createMerchantPurchaseRecord(
                $user,
                $egiliAmount,
                $pricing,
                'fiat',
                $provider
            );

            // 6. Process FIAT payment
            $paymentService = $this->paymentServiceFactory->create($provider);
            
            $paymentRequest = new PaymentRequest(
                amount: $pricing['total_eur'],
                currency: 'EUR',
                description: "Egili Purchase - {$egiliAmount} Egili",
                metadata: [
                    'user_id' => $user->id,
                    'order_reference' => $merchantPurchase->order_reference,
                    'egili_amount' => $egiliAmount,
                    'type' => 'egili_purchase'
                ]
            );

            $paymentResult = $paymentService->processPayment($paymentRequest);

            if (!$paymentResult->success) {
                // Payment failed - update merchant record
                $merchantPurchase->update([
                    'payment_status' => 'failed',
                    'notes' => "Payment failed: {$paymentResult->errorMessage}"
                ]);

                throw new \Exception(
                    "Payment failed: {$paymentResult->errorMessage} (Code: {$paymentResult->errorCode})"
                );
            }

            // 7. Update merchant record with payment ID
            $merchantPurchase->update([
                'payment_external_id' => $paymentResult->paymentId
            ]);

            // 8. Complete purchase (mint Egili + finalize)
            return $this->completePurchase($user, $merchantPurchase, $paymentResult);

        } catch (\Exception $e) {
            // ULM: Log failure
            $this->logger->error('Egili FIAT purchase workflow failed', [
                'user_id' => $user->id,
                'egili_amount' => $egiliAmount,
                'provider' => $provider,
                'error' => $e->getMessage(),
                'log_category' => 'EGILI_PURCHASE_FIAT_ERROR'
            ]);

            throw $e;
        }
    }

    /**
     * Purchase Egili with Crypto payment
     *
     * @param User $user Buyer
     * @param int $egiliAmount Quantity of Egili to purchase
     * @param string $cryptoProvider Crypto gateway provider
     * @param array $cryptoData Crypto payment data
     * @return array Purchase result with order details
     * @throws \Exception If purchase fails
     */
    public function purchaseWithCrypto(
        User $user,
        int $egiliAmount,
        string $cryptoProvider,
        array $cryptoData = []
    ): array {
        try {
            // 1. ULM: Log workflow start
            $this->logger->info('Egili Crypto purchase workflow initiated', [
                'user_id' => $user->id,
                'egili_amount' => $egiliAmount,
                'crypto_provider' => $cryptoProvider,
                'log_category' => 'EGILI_PURCHASE_CRYPTO_START'
            ]);

            // 2. GDPR: Validate user consents
            $this->validateUserConsents($user);

            // 3. Validate purchase amount
            $this->validatePurchaseAmount($egiliAmount);

            // 4. Calculate pricing
            $pricing = $this->calculatePricing($egiliAmount);

            // 5. Create merchant purchase record (pending)
            $merchantPurchase = $this->createMerchantPurchaseRecord(
                $user,
                $egiliAmount,
                $pricing,
                'crypto',
                $cryptoProvider
            );

            // 6. Create crypto payment request
            $cryptoRequest = new CryptoPaymentRequest(
                amountEur: $pricing['total_eur'],
                description: "Egili Purchase - {$egiliAmount} Egili",
                metadata: [
                    'user_id' => $user->id,
                    'order_reference' => $merchantPurchase->order_reference,
                    'egili_amount' => $egiliAmount,
                    'type' => 'egili_purchase'
                ]
            );

            // 7. Initiate crypto payment (returns payment URL for redirect)
            $cryptoResult = $this->cryptoGateway->initiatePayment($cryptoProvider, $cryptoRequest);

            if (!$cryptoResult['success']) {
                $merchantPurchase->update([
                    'payment_status' => 'failed',
                    'notes' => "Crypto payment initiation failed: {$cryptoResult['error']}"
                ]);

                throw new \Exception("Crypto payment initiation failed: {$cryptoResult['error']}");
            }

            // 8. Update merchant record with crypto payment ID
            $merchantPurchase->update([
                'payment_external_id' => $cryptoResult['payment_id']
            ]);

            // 9. Return payment URL for frontend redirect
            return [
                'success' => true,
                'payment_url' => $cryptoResult['payment_url'],
                'order_reference' => $merchantPurchase->order_reference,
                'payment_id' => $cryptoResult['payment_id'],
                'message' => 'Crypto payment initiated - redirect to gateway'
            ];

        } catch (\Exception $e) {
            // ULM: Log failure
            $this->logger->error('Egili Crypto purchase workflow failed', [
                'user_id' => $user->id,
                'egili_amount' => $egiliAmount,
                'crypto_provider' => $cryptoProvider,
                'error' => $e->getMessage(),
                'log_category' => 'EGILI_PURCHASE_CRYPTO_ERROR'
            ]);

            throw $e;
        }
    }

    /**
     * Complete purchase after successful payment
     * (Called after FIAT payment OR crypto webhook confirmation)
     *
     * @param User $user
     * @param EgiliMerchantPurchase $merchantPurchase
     * @param mixed $paymentResult Payment result object
     * @return array Completion result
     * @throws \Exception If completion fails
     */
    private function completePurchase(
        User $user,
        EgiliMerchantPurchase $merchantPurchase,
        $paymentResult
    ): array {
        return DB::transaction(function () use ($user, $merchantPurchase, $paymentResult) {
            // 1. Mint Egili to user wallet
            $egiliTransaction = $this->egiliService->earn(
                $user,
                $merchantPurchase->egili_amount,
                'egili_purchase',
                'purchase',
                [
                    'order_reference' => $merchantPurchase->order_reference,
                    'payment_method' => $merchantPurchase->payment_method,
                    'payment_provider' => $merchantPurchase->payment_provider,
                    'total_paid_eur' => $merchantPurchase->total_price_eur,
                ]
            );

            // 2. Update merchant purchase record to completed
            $merchantPurchase->update([
                'payment_status' => 'completed',
                'completed_at' => now()
            ]);

            // 3. GDPR: Audit trail
            $this->auditService->logUserAction(
                $user,
                'egili_purchase_completed',
                [
                    'order_reference' => $merchantPurchase->order_reference,
                    'egili_amount' => $merchantPurchase->egili_amount,
                    'total_eur' => $merchantPurchase->total_price_eur,
                    'payment_method' => $merchantPurchase->payment_method,
                    'payment_provider' => $merchantPurchase->payment_provider,
                    'egili_transaction_id' => $egiliTransaction->id,
                    'new_balance' => $this->egiliService->getBalance($user),
                ],
                GdprActivityCategory::WALLET_MANAGEMENT
            );

            // 4. Send confirmation email
            if (Config::get('egili.notifications.send_purchase_confirmation', true)) {
                try {
                    $user->notify(new EgiliPurchaseConfirmation($merchantPurchase));
                    
                    $this->logger->info('Egili purchase confirmation email queued', [
                        'user_id' => $user->id,
                        'order_reference' => $merchantPurchase->order_reference,
                        'log_category' => 'EGILI_PURCHASE_EMAIL_SENT'
                    ]);
                } catch (\Exception $e) {
                    // Log email failure but don't fail the purchase
                    $this->logger->warning('Egili purchase confirmation email failed', [
                        'user_id' => $user->id,
                        'order_reference' => $merchantPurchase->order_reference,
                        'error' => $e->getMessage(),
                        'log_category' => 'EGILI_PURCHASE_EMAIL_FAILED'
                    ]);
                }
            }

            // 5. ULM: Log success
            $this->logger->info('Egili purchase completed successfully', [
                'user_id' => $user->id,
                'order_reference' => $merchantPurchase->order_reference,
                'egili_amount' => $merchantPurchase->egili_amount,
                'new_balance' => $this->egiliService->getBalance($user),
                'log_category' => 'EGILI_PURCHASE_COMPLETED'
            ]);

            return [
                'success' => true,
                'order_reference' => $merchantPurchase->order_reference,
                'egili_amount' => $merchantPurchase->egili_amount,
                'total_eur' => $merchantPurchase->total_price_eur,
                'new_balance' => $this->egiliService->getBalance($user),
                'merchant_purchase_id' => $merchantPurchase->id,
                'egili_transaction_id' => $egiliTransaction->id,
            ];
        });
    }

    /**
     * Validate user has required consents
     *
     * @param User $user
     * @return void
     * @throws \Exception If consent missing
     */
    private function validateUserConsents(User $user): void
    {
        if (!$this->consentService->hasConsent($user, 'allow-personal-data-processing')) {
            throw new \Exception('Personal data processing consent required to purchase Egili');
        }

        // Log consent check
        $this->logger->info('GDPR consent validated for Egili purchase', [
            'user_id' => $user->id,
            'consent_type' => 'allow-personal-data-processing',
            'log_category' => 'EGILI_PURCHASE_CONSENT'
        ]);
    }

    /**
     * Validate purchase amount meets minimum requirements
     *
     * @param int $egiliAmount
     * @return void
     * @throws \Exception If amount invalid
     */
    private function validatePurchaseAmount(int $egiliAmount): void
    {
        $minPurchase = Config::get('egili.purchase.min_amount', 5000);
        $maxPurchase = Config::get('egili.purchase.max_amount', 1000000);

        if ($egiliAmount < $minPurchase) {
            throw new \Exception(
                "Minimum purchase is {$minPurchase} Egili (€" . 
                number_format($minPurchase * 0.01, 2) . ")"
            );
        }

        if ($egiliAmount > $maxPurchase) {
            throw new \Exception(
                "Maximum purchase is {$maxPurchase} Egili (€" . 
                number_format($maxPurchase * 0.01, 2) . ")"
            );
        }
    }

    /**
     * Calculate pricing for purchase
     *
     * @param int $egiliAmount
     * @return array Pricing breakdown
     */
    private function calculatePricing(int $egiliAmount): array
    {
        $unitPrice = Config::get('egili.purchase.unit_price_eur', 0.01);
        $totalEur = $egiliAmount * $unitPrice;

        return [
            'egili_amount' => $egiliAmount,
            'unit_price_eur' => $unitPrice,
            'total_eur' => round($totalEur, 2),
        ];
    }

    /**
     * Create merchant purchase record
     *
     * @param User $user
     * @param int $egiliAmount
     * @param array $pricing
     * @param string $paymentMethod
     * @param string $provider
     * @return EgiliMerchantPurchase
     */
    private function createMerchantPurchaseRecord(
        User $user,
        int $egiliAmount,
        array $pricing,
        string $paymentMethod,
        string $provider
    ): EgiliMerchantPurchase {
        return EgiliMerchantPurchase::create([
            'user_id' => $user->id,
            'order_reference' => EgiliMerchantPurchase::generateOrderReference(),
            'egili_amount' => $egiliAmount,
            'egili_unit_price_eur' => $pricing['unit_price_eur'],
            'total_price_eur' => $pricing['total_eur'],
            'payment_method' => $paymentMethod,
            'payment_provider' => $provider,
            'payment_status' => 'pending',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'purchased_at' => now(),
        ]);
    }

    /**
     * Get purchase by order reference
     *
     * @param string $orderReference
     * @return EgiliMerchantPurchase|null
     */
    public function getPurchaseByOrderReference(string $orderReference): ?EgiliMerchantPurchase
    {
        return EgiliMerchantPurchase::where('order_reference', $orderReference)->first();
    }

    /**
     * Get user's purchase history
     *
     * @param User $user
     * @param int|null $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserPurchaseHistory(User $user, ?int $limit = null)
    {
        $query = EgiliMerchantPurchase::forUser($user->id)
            ->orderBy('purchased_at', 'desc');

        if ($limit !== null) {
            $query->limit($limit);
        }

        return $query->get();
    }
}

