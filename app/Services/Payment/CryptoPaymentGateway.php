<?php

namespace App\Services\Payment;

use App\Contracts\CryptoPaymentProviderInterface;
use App\DataTransferObjects\Payment\CryptoPaymentRequest;
use App\DataTransferObjects\Payment\CryptoPaymentResult;
use App\DataTransferObjects\Payment\CryptoRefundResult;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * @Oracode Service: Crypto Payment Gateway (Multi-Provider)
 * 🎯 Purpose: Unified gateway for multiple crypto payment providers
 * 🧱 Core Logic: Strategy pattern - route to appropriate provider
 * 🛡️ MiCA-SAFE: All providers handle crypto, platform receives EUR settlement
 * 
 * Supported Providers:
 * - Coinbase Commerce (BTC, ETH, USDC, ALGO, DAI, etc.)
 * - BitPay (BTC, BCH, ETH, etc.)
 * - NOWPayments (100+ cryptos including ALGO, USDC)
 * 
 * @package App\Services\Payment
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Crypto Payment Gateway)
 * @date 2025-11-01
 * @purpose Multi-provider crypto payment gateway with MiCA compliance
 */
class CryptoPaymentGateway
{
    private UltraLogManager $logger;
    private ErrorManagerInterface $errorManager;
    
    /**
     * Registered providers
     * 
     * @var array<string, CryptoPaymentProviderInterface>
     */
    private array $providers = [];
    
    /**
     * Default provider (fallback)
     */
    private ?string $defaultProvider = null;
    
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
    }
    
    /**
     * Register a crypto payment provider
     * 
     * @param CryptoPaymentProviderInterface $provider
     * @return void
     */
    public function registerProvider(CryptoPaymentProviderInterface $provider): void
    {
        $providerName = $provider->getProviderName();
        $this->providers[$providerName] = $provider;
        
        $this->logger->info('Crypto payment provider registered', [
            'provider' => $providerName,
            'configured' => $provider->isConfigured(),
            'supported_cryptos' => $provider->getSupportedCryptos(),
            'log_category' => 'CRYPTO_GATEWAY_REGISTER'
        ]);
        
        // Set as default if first configured provider
        if ($this->defaultProvider === null && $provider->isConfigured()) {
            $this->defaultProvider = $providerName;
            $this->logger->info('Set default crypto payment provider', [
                'provider' => $providerName,
                'log_category' => 'CRYPTO_GATEWAY_DEFAULT'
            ]);
        }
    }
    
    /**
     * Set default provider
     * 
     * @param string $providerName
     * @return void
     * @throws \Exception If provider not registered
     */
    public function setDefaultProvider(string $providerName): void
    {
        if (!isset($this->providers[$providerName])) {
            throw new \Exception("Crypto payment provider not registered: {$providerName}");
        }
        
        $this->defaultProvider = $providerName;
        $this->logger->info('Default crypto payment provider changed', [
            'provider' => $providerName,
            'log_category' => 'CRYPTO_GATEWAY_DEFAULT_CHANGE'
        ]);
    }
    
    /**
     * Get provider instance
     * 
     * @param string|null $providerName Provider name (null = default)
     * @return CryptoPaymentProviderInterface
     * @throws \Exception If provider not found or not configured
     */
    public function getProvider(?string $providerName = null): CryptoPaymentProviderInterface
    {
        // Use default if no provider specified
        if ($providerName === null) {
            if ($this->defaultProvider === null) {
                throw new \Exception('No default crypto payment provider configured');
            }
            $providerName = $this->defaultProvider;
        }
        
        // Check provider exists
        if (!isset($this->providers[$providerName])) {
            $this->logger->error('Crypto payment provider not found', [
                'provider_requested' => $providerName,
                'available_providers' => array_keys($this->providers),
                'log_category' => 'CRYPTO_GATEWAY_PROVIDER_NOT_FOUND'
            ]);
            
            throw new \Exception("Crypto payment provider not found: {$providerName}");
        }
        
        $provider = $this->providers[$providerName];
        
        // Check provider is configured
        if (!$provider->isConfigured()) {
            $this->logger->error('Crypto payment provider not configured', [
                'provider' => $providerName,
                'log_category' => 'CRYPTO_GATEWAY_PROVIDER_NOT_CONFIGURED'
            ]);
            
            throw new \Exception("Crypto payment provider not configured: {$providerName}");
        }
        
        return $provider;
    }
    
    /**
     * Initiate crypto payment
     * 
     * @param CryptoPaymentRequest $request
     * @param string|null $providerName Provider to use (null = default)
     * @return CryptoPaymentResult
     */
    public function initiate(
        CryptoPaymentRequest $request,
        ?string $providerName = null
    ): CryptoPaymentResult {
        try {
            $provider = $this->getProvider($providerName);
            
            $this->logger->info('Crypto payment initiated', [
                'provider' => $provider->getProviderName(),
                'user_id' => $request->user_id,
                'amount_eur' => $request->amount_eur,
                'service_type' => $request->service_type,
                'log_category' => 'CRYPTO_PAYMENT_INITIATE'
            ]);
            
            $result = $provider->initiate($request);
            
            if ($result->success) {
                $this->logger->info('Crypto payment session created', [
                    'provider' => $provider->getProviderName(),
                    'payment_id' => $result->payment_id,
                    'status' => $result->status,
                    'log_category' => 'CRYPTO_PAYMENT_SESSION_CREATED'
                ]);
            } else {
                $this->logger->error('Crypto payment session failed', [
                    'provider' => $provider->getProviderName(),
                    'error' => $result->error_message,
                    'log_category' => 'CRYPTO_PAYMENT_SESSION_FAILED'
                ]);
            }
            
            return $result;
            
        } catch (\Exception $e) {
            $this->logger->error('Crypto payment gateway error', [
                'provider' => $providerName ?? 'default',
                'error' => $e->getMessage(),
                'log_category' => 'CRYPTO_GATEWAY_ERROR'
            ]);
            
            return CryptoPaymentResult::failed(
                $providerName ?? 'unknown',
                $e->getMessage()
            );
        }
    }
    
    /**
     * Verify payment status
     * 
     * @param string $paymentId Provider payment ID
     * @param string|null $providerName Provider name (null = auto-detect from payment_id format)
     * @return CryptoPaymentResult
     */
    public function verify(string $paymentId, ?string $providerName = null): CryptoPaymentResult
    {
        try {
            // Auto-detect provider if not specified (based on payment_id format)
            if ($providerName === null) {
                $providerName = $this->detectProviderFromPaymentId($paymentId);
            }
            
            $provider = $this->getProvider($providerName);
            
            return $provider->verify($paymentId);
            
        } catch (\Exception $e) {
            $this->logger->error('Crypto payment verification error', [
                'payment_id' => $paymentId,
                'provider' => $providerName,
                'error' => $e->getMessage(),
                'log_category' => 'CRYPTO_VERIFY_ERROR'
            ]);
            
            return CryptoPaymentResult::failed(
                $providerName ?? 'unknown',
                $e->getMessage()
            );
        }
    }
    
    /**
     * Process webhook from provider
     * 
     * @param string $providerName Provider name
     * @param array $payload Webhook payload
     * @return CryptoPaymentResult
     */
    public function processWebhook(string $providerName, array $payload): CryptoPaymentResult
    {
        try {
            $provider = $this->getProvider($providerName);
            
            $this->logger->info('Crypto webhook received', [
                'provider' => $providerName,
                'payload_keys' => array_keys($payload),
                'log_category' => 'CRYPTO_WEBHOOK_RECEIVED'
            ]);
            
            return $provider->processWebhook($payload);
            
        } catch (\Exception $e) {
            $this->logger->error('Crypto webhook processing error', [
                'provider' => $providerName,
                'error' => $e->getMessage(),
                'log_category' => 'CRYPTO_WEBHOOK_ERROR'
            ]);
            
            return CryptoPaymentResult::failed($providerName, $e->getMessage());
        }
    }
    
    /**
     * Refund crypto payment
     * 
     * @param string $paymentId Provider payment ID
     * @param float|null $amount Amount to refund (null = full)
     * @param string|null $providerName Provider name
     * @return CryptoRefundResult
     */
    public function refund(
        string $paymentId,
        ?float $amount = null,
        ?string $providerName = null
    ): CryptoRefundResult {
        try {
            if ($providerName === null) {
                $providerName = $this->detectProviderFromPaymentId($paymentId);
            }
            
            $provider = $this->getProvider($providerName);
            
            return $provider->refund($paymentId, $amount);
            
        } catch (\Exception $e) {
            $this->logger->error('Crypto refund error', [
                'payment_id' => $paymentId,
                'amount' => $amount,
                'provider' => $providerName,
                'error' => $e->getMessage(),
                'log_category' => 'CRYPTO_REFUND_ERROR'
            ]);
            
            return CryptoRefundResult::failed(
                $providerName ?? 'unknown',
                $paymentId,
                $e->getMessage()
            );
        }
    }
    
    /**
     * Get all available providers
     * 
     * @return array Provider names and configuration status
     */
    public function getAvailableProviders(): array
    {
        $providers = [];
        
        foreach ($this->providers as $name => $provider) {
            $providers[$name] = [
                'name' => $name,
                'configured' => $provider->isConfigured(),
                'supported_cryptos' => $provider->getSupportedCryptos(),
                'is_default' => $name === $this->defaultProvider,
            ];
        }
        
        return $providers;
    }
    
    /**
     * Detect provider from payment ID format
     * 
     * @param string $paymentId
     * @return string Provider name
     * @throws \Exception If provider cannot be detected
     */
    private function detectProviderFromPaymentId(string $paymentId): string
    {
        // Coinbase Commerce: starts with alphanumeric (e.g., "ABC123-XYZ")
        // BitPay: starts with "invoice_" or similar
        // NOWPayments: numeric payment ID
        
        if (preg_match('/^[A-Z0-9\-]{8,}$/i', $paymentId)) {
            return 'coinbase_commerce';
        }
        
        if (str_starts_with($paymentId, 'invoice_')) {
            return 'bitpay';
        }
        
        if (is_numeric($paymentId)) {
            return 'nowpayments';
        }
        
        // Fallback to default
        if ($this->defaultProvider !== null) {
            $this->logger->warning('Could not detect provider from payment_id, using default', [
                'payment_id' => $paymentId,
                'default_provider' => $this->defaultProvider,
                'log_category' => 'CRYPTO_PROVIDER_AUTO_DETECT_FALLBACK'
            ]);
            return $this->defaultProvider;
        }
        
        throw new \Exception("Cannot detect crypto payment provider from payment_id: {$paymentId}");
    }
}





