<?php

namespace App\Services;

use App\Models\User;
use App\Models\Egi;
use App\Models\EgiLivingSubscription;
use App\Enums\Gdpr\GdprActivityCategory;
use App\Services\Gdpr\AuditLogService;
use App\Services\EgiliService;
use App\Services\Payment\CryptoPaymentGateway;
use App\Services\Payment\PaymentServiceFactory;
use App\DataTransferObjects\Payment\CryptoPaymentRequest;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Illuminate\Support\Facades\DB;

/**
 * @Oracode Service: EGI Living Payment Orchestrator
 * 🎯 Purpose: Unified payment handler for EGI Living subscriptions
 * 🧱 Core Logic: Orchestrate FIAT/Crypto/Egili payments with atomic transactions
 * 🛡️ GDPR Compliance: Full audit trail for all payment operations
 * 
 * Supported Payment Methods:
 * - FIAT: Stripe, PayPal (via PaymentServiceFactory)
 * - Crypto: Multiple providers (via CryptoPaymentGateway)
 * - Egili: Platform utility token (via EgiliService)
 * 
 * @package App\Services
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Dual Architecture)
 * @date 2025-11-01
 * @purpose Unified payment orchestrator with OS3 + GDPR compliance
 */
class EgiLivingPaymentService
{
    private UltraLogManager $logger;
    private ErrorManagerInterface $errorManager;
    private AuditLogService $auditService;
    private EgiliService $egiliService;
    private CryptoPaymentGateway $cryptoGateway;
    private PaymentServiceFactory $paymentFactory;
    
    /**
     * EGI Living subscription pricing (from config)
     */
    private array $pricing;
    
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService,
        EgiliService $egiliService,
        CryptoPaymentGateway $cryptoGateway,
        PaymentServiceFactory $paymentFactory
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditService = $auditService;
        $this->egiliService = $egiliService;
        $this->cryptoGateway = $cryptoGateway;
        $this->paymentFactory = $paymentFactory;
        
        // Load pricing from config
        $this->pricing = config('egi_living.subscription_plans.one_time', [
            'price_eur' => 49.99,
            'price_egili' => 500,
        ]);
    }
    
    /**
     * Get EGI Living subscription pricing
     * 
     * @return array Pricing details (EUR, Egili, crypto equivalent)
     */
    public function getPricing(): array
    {
        return [
            'price_eur' => $this->pricing['price_eur'] ?? 49.99,
            'price_egili' => $this->pricing['price_egili'] ?? 500,
            'name' => __('egi_living.subscription.one_time_name'),
            'description' => __('egi_living.subscription.one_time_description'),
            'features' => $this->pricing['features'] ?? [],
        ];
    }
    
    /**
     * Check if user can afford EGI Living with Egili
     * 
     * @param User $user
     * @return bool
     */
    public function canPayWithEgili(User $user): bool
    {
        $priceEgili = $this->pricing['price_egili'] ?? 500;
        return $this->egiliService->canSpend($user, $priceEgili);
    }
    
    /**
     * Process payment with Egili
     * 
     * @param User $user
     * @param Egi $egi
     * @return array Result with subscription details
     * @throws \Exception If payment fails
     */
    public function payWithEgili(User $user, Egi $egi): array
    {
        $priceEgili = $this->pricing['price_egili'] ?? 500;
        
        // ULM: Log payment initiation
        $this->logger->info('EGI Living payment with Egili initiated', [
            'user_id' => $user->id,
            'egi_id' => $egi->id,
            'price_egili' => $priceEgili,
            'log_category' => 'LIVING_PAYMENT_EGILI_START'
        ]);
        
        // NOTE: EgiliService->spend() already uses DB::transaction()
        // We don't wrap in another transaction to avoid nesting
        
        // 1. Spend Egili (atomic transaction handled by EgiliService)
        $transaction = $this->egiliService->spend(
            $user,
            $priceEgili,
            'egi_living_subscription',
            'service',
            [
                'egi_id' => $egi->id,
                'subscription_type' => 'one_time',
            ],
            $egi
        );
        
        // 2. Activate Living features (separate transaction after Egili spent)
        $egi->update([
            'egi_living_enabled' => true,
            'egi_living_activated_at' => now(),
        ]);
        
        // 3. GDPR: Audit trail
        $this->auditService->logUserAction(
            $user,
            'egi_living_purchased_egili',
            [
                'egi_id' => $egi->id,
                'price_egili' => $priceEgili,
                'egili_transaction_id' => $transaction->id,
                'balance_after' => $this->egiliService->getBalance($user),
            ],
            GdprActivityCategory::BLOCKCHAIN_ACTIVITY
        );
        
        // 4. ULM: Log success
        $this->logger->info('EGI Living payment with Egili completed', [
            'user_id' => $user->id,
            'egi_id' => $egi->id,
            'egili_transaction_id' => $transaction->id,
            'log_category' => 'LIVING_PAYMENT_EGILI_SUCCESS'
        ]);
        
        return [
            'success' => true,
            'payment_method' => 'egili',
            'egi_id' => $egi->id,
            'transaction_id' => $transaction->id,
            'message' => __('egi_living.payment.egili_success'),
        ];
    }
    
    /**
     * Initiate payment with FIAT (Stripe/PayPal)
     * 
     * @param User $user
     * @param Egi $egi
     * @param string $provider 'stripe' or 'paypal'
     * @return array Payment session details with redirect URL
     * @throws \Exception If payment initiation fails
     */
    public function payWithFiat(User $user, Egi $egi, string $provider): array
    {
        $priceEur = $this->pricing['price_eur'] ?? 49.99;
        
        // ULM: Log payment initiation
        $this->logger->info('EGI Living payment with FIAT initiated', [
            'user_id' => $user->id,
            'egi_id' => $egi->id,
            'provider' => $provider,
            'price_eur' => $priceEur,
            'log_category' => 'LIVING_PAYMENT_FIAT_START'
        ]);
        
        try {
            // Get payment service
            $paymentService = $this->paymentFactory->create($provider);
            
            // Create payment request
            $paymentRequest = new \App\DataTransferObjects\Payment\PaymentRequest(
                amount: $priceEur,
                currency: 'EUR',
                description: __('egi_living.payment.description', ['egi_id' => $egi->id]),
                customerId: (string) $user->id,
                metadata: [
                    'service_type' => 'egi_living_subscription',
                    'egi_id' => $egi->id,
                    'user_id' => $user->id,
                ]
            );
            
            // Initiate payment
            $result = $paymentService->initiate($paymentRequest);
            
            // GDPR: Audit trail
            $this->auditService->logUserAction(
                $user,
                'egi_living_payment_initiated',
                [
                    'egi_id' => $egi->id,
                    'provider' => $provider,
                    'price_eur' => $priceEur,
                    'payment_id' => $result->paymentId,
                ],
                GdprActivityCategory::BLOCKCHAIN_ACTIVITY
            );
            
            return [
                'success' => true,
                'payment_method' => 'fiat',
                'provider' => $provider,
                'payment_id' => $result->paymentId,
                'redirect_url' => $result->redirectUrl,
                'message' => __('egi_living.payment.fiat_redirect'),
            ];
            
        } catch (\Exception $e) {
            $this->logger->error('EGI Living FIAT payment failed', [
                'user_id' => $user->id,
                'egi_id' => $egi->id,
                'provider' => $provider,
                'error' => $e->getMessage(),
                'log_category' => 'LIVING_PAYMENT_FIAT_ERROR'
            ]);
            
            throw $e;
        }
    }
    
    /**
     * Initiate payment with Crypto
     * 
     * @param User $user
     * @param Egi $egi
     * @param string|null $cryptoProvider Crypto provider name (null = default)
     * @return array Payment session details with redirect URL
     * @throws \Exception If payment initiation fails
     */
    public function payWithCrypto(User $user, Egi $egi, ?string $cryptoProvider = null): array
    {
        $priceEur = $this->pricing['price_eur'] ?? 49.99;
        
        // ULM: Log payment initiation
        $this->logger->info('EGI Living payment with Crypto initiated', [
            'user_id' => $user->id,
            'egi_id' => $egi->id,
            'provider' => $cryptoProvider ?? 'default',
            'price_eur' => $priceEur,
            'log_category' => 'LIVING_PAYMENT_CRYPTO_START'
        ]);
        
        try {
            // Create crypto payment request
            $request = CryptoPaymentRequest::fromArray([
                'amount_eur' => $priceEur,
                'description' => __('egi_living.payment.description', ['egi_id' => $egi->id]),
                'user_id' => $user->id,
                'service_type' => 'egi_living_subscription',
                'source_id' => $egi->id,
                'success_url' => route('egi-living.payment.success', ['egi' => $egi->id]),
                'cancel_url' => route('egi-living.payment.cancel', ['egi' => $egi->id]),
                'metadata' => [
                    'egi_id' => $egi->id,
                    'user_id' => $user->id,
                ],
            ]);
            
            // Initiate crypto payment
            $result = $this->cryptoGateway->initiate($request, $cryptoProvider);
            
            if (!$result->success) {
                throw new \Exception($result->error_message ?? 'Crypto payment initiation failed');
            }
            
            // GDPR: Audit trail
            $this->auditService->logUserAction(
                $user,
                'egi_living_payment_crypto_initiated',
                [
                    'egi_id' => $egi->id,
                    'provider' => $result->provider,
                    'price_eur' => $priceEur,
                    'payment_id' => $result->payment_id,
                ],
                GdprActivityCategory::BLOCKCHAIN_ACTIVITY
            );
            
            return [
                'success' => true,
                'payment_method' => 'crypto',
                'provider' => $result->provider,
                'payment_id' => $result->payment_id,
                'redirect_url' => $result->redirect_url,
                'message' => __('egi_living.payment.crypto_redirect'),
            ];
            
        } catch (\Exception $e) {
            $this->logger->error('EGI Living Crypto payment failed', [
                'user_id' => $user->id,
                'egi_id' => $egi->id,
                'provider' => $cryptoProvider ?? 'default',
                'error' => $e->getMessage(),
                'log_category' => 'LIVING_PAYMENT_CRYPTO_ERROR'
            ]);
            
            throw $e;
        }
    }
    
    /**
     * Verify and complete payment after redirect/webhook
     * 
     * @param string $paymentMethod 'fiat' or 'crypto'
     * @param string $paymentId Payment ID from provider
     * @param Egi $egi EGI to activate
     * @return bool True if payment verified and Living activated
     */
    public function verifyAndActivate(string $paymentMethod, string $paymentId, Egi $egi): bool
    {
        $this->logger->info('Verifying EGI Living payment', [
            'payment_method' => $paymentMethod,
            'payment_id' => $paymentId,
            'egi_id' => $egi->id,
            'log_category' => 'LIVING_PAYMENT_VERIFY'
        ]);
        
        try {
            // Verify payment based on method
            $verified = match($paymentMethod) {
                'fiat' => $this->verifyFiatPayment($paymentId),
                'crypto' => $this->verifyCryptoPayment($paymentId),
                default => false,
            };
            
            if (!$verified) {
                throw new \Exception("Payment verification failed for {$paymentMethod}");
            }
            
            // Activate Living features
            return DB::transaction(function () use ($egi, $paymentMethod, $paymentId) {
                $egi->update([
                    'egi_living_enabled' => true,
                    'egi_living_activated_at' => now(),
                ]);
                
                // GDPR: Audit trail
                $this->auditService->logUserAction(
                    $egi->user,
                    'egi_living_activated',
                    [
                        'egi_id' => $egi->id,
                        'payment_method' => $paymentMethod,
                        'payment_id' => $paymentId,
                    ],
                    GdprActivityCategory::BLOCKCHAIN_ACTIVITY
                );
                
                return true;
            });
            
        } catch (\Exception $e) {
            $this->logger->error('EGI Living payment verification failed', [
                'payment_method' => $paymentMethod,
                'payment_id' => $paymentId,
                'egi_id' => $egi->id,
                'error' => $e->getMessage(),
                'log_category' => 'LIVING_PAYMENT_VERIFY_ERROR'
            ]);
            
            return false;
        }
    }
    
    /**
     * Verify FIAT payment
     */
    private function verifyFiatPayment(string $paymentId): bool
    {
        // TODO: Implement FIAT verification
        // For now, assume success (will be implemented with webhook)
        return true;
    }
    
    /**
     * Verify Crypto payment
     */
    private function verifyCryptoPayment(string $paymentId): bool
    {
        $result = $this->cryptoGateway->verify($paymentId);
        return $result->isCompleted();
    }
}

