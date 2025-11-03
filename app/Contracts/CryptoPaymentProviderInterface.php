<?php

namespace App\Contracts;

use App\DataTransferObjects\Payment\CryptoPaymentRequest;
use App\DataTransferObjects\Payment\CryptoPaymentResult;
use App\DataTransferObjects\Payment\CryptoRefundResult;

/**
 * Crypto Payment Provider Interface
 * 
 * Contract for crypto payment providers (Coinbase Commerce, BitPay, NOWPayments, etc.)
 * All providers must implement this interface for Gateway pattern.
 * 
 * MiCA-SAFE: Providers handle crypto, platform receives EUR settlement.
 * 
 * @package App\Contracts
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Crypto Payment Gateway)
 * @date 2025-11-01
 * @purpose Unified interface for crypto payment providers
 */
interface CryptoPaymentProviderInterface
{
    /**
     * Initialize crypto payment session
     * 
     * @param CryptoPaymentRequest $request Payment request details
     * @return CryptoPaymentResult Payment session (redirect URL, payment ID, etc.)
     */
    public function initiate(CryptoPaymentRequest $request): CryptoPaymentResult;
    
    /**
     * Verify payment status via webhook or API
     * 
     * @param string $paymentId Provider payment ID
     * @return CryptoPaymentResult Updated payment status
     */
    public function verify(string $paymentId): CryptoPaymentResult;
    
    /**
     * Process webhook notification from provider
     * 
     * @param array $payload Webhook payload
     * @return CryptoPaymentResult Payment result from webhook
     */
    public function processWebhook(array $payload): CryptoPaymentResult;
    
    /**
     * Refund payment (if supported by provider)
     * 
     * @param string $paymentId Provider payment ID
     * @param float|null $amount Amount to refund (null = full refund)
     * @return CryptoRefundResult Refund result
     */
    public function refund(string $paymentId, ?float $amount = null): CryptoRefundResult;
    
    /**
     * Get provider name
     * 
     * @return string Provider name (coinbase_commerce, bitpay, nowpayments)
     */
    public function getProviderName(): string;
    
    /**
     * Get supported cryptocurrencies
     * 
     * @return array List of supported crypto symbols (BTC, ETH, USDC, ALGO, etc.)
     */
    public function getSupportedCryptos(): array;
    
    /**
     * Check if provider is configured and ready
     * 
     * @return bool True if API keys are set and provider is active
     */
    public function isConfigured(): bool;
}





