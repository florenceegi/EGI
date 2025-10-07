<?php

namespace App\DataTransferObjects\Payment;

/**
 * @Oracode DTO: Refund Result Data
 * 🎯 Purpose: Structured response from payment refund processing
 * 🧱 Core Logic: Standard format for refund success/failure responses
 * 🛡️ GDPR: Audit trail for financial operations with minimal data retention
 *
 * @package App\DataTransferObjects\Payment
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Blockchain Integration)
 * @date 2025-10-07
 * @purpose Refund processing response data with status and details
 */
readonly class RefundResult
{
    public function __construct(
        public bool $success,
        public string $refundId,
        public string $originalPaymentId,
        public float $refundAmount,
        public string $currency,
        public string $status,
        public ?string $errorMessage = null,
        public ?string $errorCode = null,
        public ?string $reason = null,
        public array $metadata = [],
        public ?\DateTimeImmutable $processedAt = null
    ) {
    }

    /**
     * Create successful refund result
     * 
     * @param string $refundId PSP refund ID
     * @param string $originalPaymentId Original payment ID
     * @param float $refundAmount Refund amount
     * @param string $currency Refund currency
     * @param string $reason Refund reason
     * @param array $metadata Additional metadata
     * @return static
     */
    public static function success(
        string $refundId,
        string $originalPaymentId,
        float $refundAmount,
        string $currency,
        string $reason = 'Customer request',
        array $metadata = []
    ): static {
        return new static(
            success: true,
            refundId: $refundId,
            originalPaymentId: $originalPaymentId,
            refundAmount: $refundAmount,
            currency: strtoupper($currency),
            status: 'completed',
            reason: $reason,
            metadata: $metadata,
            processedAt: new \DateTimeImmutable()
        );
    }

    /**
     * Create failed refund result
     * 
     * @param string $refundId PSP refund ID (may be empty for early failures)
     * @param string $originalPaymentId Original payment ID
     * @param string $errorMessage Human-readable error message
     * @param string $errorCode Error code for debugging
     * @param array $metadata Additional metadata
     * @return static
     */
    public static function failure(
        string $refundId,
        string $originalPaymentId,
        string $errorMessage,
        string $errorCode = 'REFUND_FAILED',
        array $metadata = []
    ): static {
        return new static(
            success: false,
            refundId: $refundId,
            originalPaymentId: $originalPaymentId,
            refundAmount: 0.0,
            currency: '',
            status: 'failed',
            errorMessage: $errorMessage,
            errorCode: $errorCode,
            metadata: $metadata,
            processedAt: new \DateTimeImmutable()
        );
    }

    /**
     * Create pending refund result
     * 
     * @param string $refundId PSP refund ID
     * @param string $originalPaymentId Original payment ID
     * @param float $refundAmount Refund amount
     * @param string $currency Refund currency
     * @param string $reason Refund reason
     * @param array $metadata Additional metadata
     * @return static
     */
    public static function pending(
        string $refundId,
        string $originalPaymentId,
        float $refundAmount,
        string $currency,
        string $reason = 'Processing',
        array $metadata = []
    ): static {
        return new static(
            success: false, // Not completed yet
            refundId: $refundId,
            originalPaymentId: $originalPaymentId,
            refundAmount: $refundAmount,
            currency: strtoupper($currency),
            status: 'pending',
            reason: $reason,
            metadata: $metadata,
            processedAt: new \DateTimeImmutable()
        );
    }

    /**
     * Convert to array
     * 
     * @return array
     */
    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'refund_id' => $this->refundId,
            'original_payment_id' => $this->originalPaymentId,
            'refund_amount' => $this->refundAmount,
            'currency' => $this->currency,
            'status' => $this->status,
            'error_message' => $this->errorMessage,
            'error_code' => $this->errorCode,
            'reason' => $this->reason,
            'metadata' => $this->metadata,
            'processed_at' => $this->processedAt?->format('c'),
        ];
    }

    /**
     * Check if refund is completed and successful
     * 
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->success && $this->status === 'completed';
    }

    /**
     * Check if refund is still processing
     * 
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if refund failed permanently
     * 
     * @return bool
     */
    public function isFailed(): bool
    {
        return !$this->success && $this->status === 'failed';
    }

    /**
     * Get user-friendly status message
     * 
     * @return string
     */
    public function getStatusMessage(): string
    {
        return match ($this->status) {
            'completed' => "Refund of {$this->refundAmount} {$this->currency} completed successfully",
            'pending' => 'Refund is being processed',
            'failed' => $this->errorMessage ?? 'Refund failed',
            'cancelled' => 'Refund cancelled',
            default => 'Unknown refund status'
        };
    }
}