<?php

namespace App\DataTransferObjects\Payment;

/**
 * Crypto Payment Request DTO
 * 
 * Immutable data transfer object for crypto payment requests.
 * 
 * @package App\DataTransferObjects\Payment
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Crypto Payment Gateway)
 * @date 2025-11-01
 */
class CryptoPaymentRequest
{
    public function __construct(
        public readonly float $amount_eur,
        public readonly string $description,
        public readonly int $user_id,
        public readonly string $service_type,         // 'egi_living', 'egili_pack', etc.
        public readonly ?int $source_id = null,       // EGI ID, subscription ID, etc.
        public readonly ?string $success_url = null,
        public readonly ?string $cancel_url = null,
        public readonly ?array $metadata = null,
    ) {}
    
    /**
     * Create from array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            amount_eur: (float) $data['amount_eur'],
            description: (string) $data['description'],
            user_id: (int) $data['user_id'],
            service_type: (string) $data['service_type'],
            source_id: $data['source_id'] ?? null,
            success_url: $data['success_url'] ?? null,
            cancel_url: $data['cancel_url'] ?? null,
            metadata: $data['metadata'] ?? null,
        );
    }
    
    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'amount_eur' => $this->amount_eur,
            'description' => $this->description,
            'user_id' => $this->user_id,
            'service_type' => $this->service_type,
            'source_id' => $this->source_id,
            'success_url' => $this->success_url,
            'cancel_url' => $this->cancel_url,
            'metadata' => $this->metadata,
        ];
    }
}





