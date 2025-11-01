<?php

namespace App\DataTransferObjects\Payment;

/**
 * Crypto Payment Result DTO
 * 
 * Immutable data transfer object for crypto payment results.
 * 
 * @package App\DataTransferObjects\Payment
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Crypto Payment Gateway)
 * @date 2025-11-01
 */
class CryptoPaymentResult
{
    public function __construct(
        public readonly bool $success,
        public readonly string $provider,             // coinbase_commerce, bitpay, etc.
        public readonly string $payment_id,           // Provider payment ID
        public readonly string $status,               // pending, completed, failed, expired
        public readonly ?string $redirect_url = null, // URL to redirect user for payment
        public readonly ?float $amount_eur = null,
        public readonly ?float $amount_crypto = null,
        public readonly ?string $crypto_symbol = null, // BTC, ETH, USDC, ALGO
        public readonly ?string $wallet_address = null,
        public readonly ?string $payment_url = null,   // Direct payment URL (QR code)
        public readonly ?string $tx_id = null,         // Blockchain transaction ID
        public readonly ?string $error_message = null,
        public readonly ?array $metadata = null,
    ) {}
    
    /**
     * Create successful result
     */
    public static function success(
        string $provider,
        string $payment_id,
        string $redirect_url,
        array $metadata = []
    ): self {
        return new self(
            success: true,
            provider: $provider,
            payment_id: $payment_id,
            status: 'pending',
            redirect_url: $redirect_url,
            metadata: $metadata,
        );
    }
    
    /**
     * Create failed result
     */
    public static function failed(
        string $provider,
        string $error_message,
        array $metadata = []
    ): self {
        return new self(
            success: false,
            provider: $provider,
            payment_id: '',
            status: 'failed',
            error_message: $error_message,
            metadata: $metadata,
        );
    }
    
    /**
     * Check if payment is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
    
    /**
     * Check if payment is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
    
    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'provider' => $this->provider,
            'payment_id' => $this->payment_id,
            'status' => $this->status,
            'redirect_url' => $this->redirect_url,
            'amount_eur' => $this->amount_eur,
            'amount_crypto' => $this->amount_crypto,
            'crypto_symbol' => $this->crypto_symbol,
            'wallet_address' => $this->wallet_address,
            'payment_url' => $this->payment_url,
            'tx_id' => $this->tx_id,
            'error_message' => $this->error_message,
            'metadata' => $this->metadata,
        ];
    }
}

