<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Contracts\PaymentServiceInterface;
use App\Services\Payment\{
    StripePaymentService,
    PayPalPaymentService,
    StripeRealPaymentService,
    PayPalRealPaymentService
};
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Services\Gdpr\{AuditLogService, ConsentService};
use Exception;

/**
 * @Oracode Factory: Payment Service Factory
 * 🎯 Purpose: Factory pattern for creating payment service instances
 * 🧱 Core Logic: Dynamic PSP selection based on provider type
 * 🛡️ MiCA-SAFE: All created services handle only FIAT payments
 *
 * @package App\Services\Payment
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Blockchain Integration)
 * @date 2025-10-07
 * @purpose Factory for payment service provider instantiation
 */
class PaymentServiceFactory {
    private UltraLogManager $logger;
    private ErrorManagerInterface $errorManager;
    private AuditLogService $auditService;
    private ConsentService $consentService;
    private array $serviceCache = [];

    /**
     * Constructor with GDPR/Ultra compliance dependencies
     *
     * @param UltraLogManager $logger Ultra logging manager
     * @param ErrorManagerInterface $errorManager Ultra error manager
     * @param AuditLogService $auditService GDPR audit service
     * @param ConsentService $consentService GDPR consent service
     * @privacy-safe All injected services handle GDPR compliance
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService,
        ConsentService $consentService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditService = $auditService;
        $this->consentService = $consentService;
    }

    /**
     * Create payment service instance for specified provider
     *
     * @param string $provider PSP provider identifier (stripe|paypal)
     * @return PaymentServiceInterface Payment service instance
     * @throws Exception Unsupported provider
     * @gdpr-compliant All created services are GDPR-compliant
     */
    public function create(string $provider): PaymentServiceInterface {
        // Use cached instance if available
        if (isset($this->serviceCache[$provider])) {
            return $this->serviceCache[$provider];
        }

        // Create service instance based on provider
        $provider = strtolower($provider);
        $mockMode = (bool) config('algorand.payments.mock_mode', true);

        $service = match ($provider) {
            'stripe' => $this->resolveStripeService($mockMode),
            'paypal' => $this->resolvePayPalService($mockMode),
            default => throw new Exception("Unsupported payment provider: {$provider}")
        };

        // Cache for future use
        $this->serviceCache[$provider] = $service;

        // Log service creation
        $this->logger->info('Payment service created', [
            'provider' => $provider,
            'service_class' => get_class($service),
            'cached' => false
        ]);

        return $service;
    }

    /**
     * Get list of supported providers
     *
     * @return array List of supported provider identifiers
     */
    public function getSupportedProviders(): array {
        return ['stripe', 'paypal'];
    }

    /**
     * Check if provider is supported
     *
     * @param string $provider Provider identifier
     * @return bool True if provider is supported
     */
    public function isProviderSupported(string $provider): bool {
        return in_array(strtolower($provider), $this->getSupportedProviders(), true);
    }

    /**
     * Clear service cache (useful for testing)
     *
     * @return void
     */
    public function clearCache(): void {
        $this->serviceCache = [];

        $this->logger->info('Payment service cache cleared', [
            'services_cleared' => count($this->serviceCache)
        ]);
    }

    /**
     * Resolve Stripe payment service based on configuration.
     */
    private function resolveStripeService(bool $mockMode): PaymentServiceInterface
    {
        $stripeEnabled = (bool) config('algorand.payments.stripe_enabled', false);

        if ($mockMode || !$stripeEnabled) {
            return new StripePaymentService(
                $this->logger,
                $this->errorManager,
                $this->auditService,
                $this->consentService
            );
        }

        // Resolve StripePaymentSplitService dependency
        $splitService = app(StripePaymentSplitService::class);

        return new StripeRealPaymentService(
            $this->logger,
            $this->errorManager,
            $this->auditService,
            $this->consentService,
            $splitService
        );
    }

    /**
     * Resolve PayPal payment service based on configuration.
     */
    private function resolvePayPalService(bool $mockMode): PaymentServiceInterface
    {
        $paypalEnabled = (bool) config('algorand.payments.paypal_enabled', false);

        if ($mockMode || !$paypalEnabled) {
            return new PayPalPaymentService(
                $this->logger,
                $this->errorManager,
                $this->auditService,
                $this->consentService
            );
        }

        return new PayPalRealPaymentService(
            $this->logger,
            $this->errorManager,
            $this->auditService,
            $this->consentService
        );
    }
}
