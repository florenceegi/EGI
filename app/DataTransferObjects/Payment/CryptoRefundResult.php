<?php

namespace App\DataTransferObjects\Payment;

/**
 * Crypto Refund Result DTO
 * 
 * @package App\DataTransferObjects\Payment
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Crypto Payment Gateway)
 * @date 2025-11-01
 */
class CryptoRefundResult
{
    public function __construct(
        public readonly bool $success,
        public readonly string $provider,
        public readonly string $payment_id,
        public readonly ?string $refund_id = null,
        public readonly ?float $amount_refunded = null,
        public readonly ?string $error_message = null,
        public readonly ?array $metadata = null,
    ) {}
    
    /**
     * Create successful refund
     */
    public static function success(
        string $provider,
        string $payment_id,
        string $refund_id,
        float $amount_refunded
    ): self {
        return new self(
            success: true,
            provider: $provider,
            payment_id: $payment_id,
            refund_id: $refund_id,
            amount_refunded: $amount_refunded,
        );
    }
    
    /**
     * Create failed refund
     */
    public static function failed(
        string $provider,
        string $payment_id,
        string $error_message
    ): self {
        return new self(
            success: false,
            provider: $provider,
            payment_id: $payment_id,
            error_message: $error_message,
        );
    }
}





