<?php

namespace App\DataTransferObjects\Payment;

/**
 * @Oracode DTO: Payment Status Data
 * 🎯 Purpose: Current status information for existing payments
 * 🧱 Core Logic: Track payment lifecycle from creation to completion
 * 🛡️ Monitoring: Enable payment status tracking without exposing sensitive data
 *
 * @package App\DataTransferObjects\Payment
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Blockchain Integration)
 * @date 2025-10-07
 * @purpose Payment status tracking with lifecycle information
 */
readonly class PaymentStatus {
    public function __construct(
        public string $paymentId,
        public string $status,
        public float $amount,
        public string $currency,
        public bool $isPaid,
        public ?string $failureReason = null,
        public ?string $failureCode = null,
        public array $metadata = [],
        public ?\DateTimeImmutable $createdAt = null,
        public ?\DateTimeImmutable $paidAt = null,
        public ?\DateTimeImmutable $failedAt = null,
        public ?string $receiptUrl = null,
        public ?array $refunds = []
    ) {
    }

    /**
     * Create from PSP response data
     *
     * @param array $data PSP response data
     * @return static
     */
    public static function fromArray(array $data): static {
        return new static(
            paymentId: $data['payment_id'] ?? '',
            status: $data['status'] ?? 'unknown',
            amount: (float) ($data['amount'] ?? 0),
            currency: strtoupper($data['currency'] ?? 'EUR'),
            isPaid: (bool) ($data['is_paid'] ?? false),
            failureReason: $data['failure_reason'] ?? null,
            failureCode: $data['failure_code'] ?? null,
            metadata: $data['metadata'] ?? [],
            createdAt: isset($data['created_at']) ? new \DateTimeImmutable($data['created_at']) : null,
            paidAt: isset($data['paid_at']) ? new \DateTimeImmutable($data['paid_at']) : null,
            failedAt: isset($data['failed_at']) ? new \DateTimeImmutable($data['failed_at']) : null,
            receiptUrl: $data['receipt_url'] ?? null,
            refunds: $data['refunds'] ?? []
        );
    }

    /**
     * Convert to array
     *
     * @return array
     */
    public function toArray(): array {
        return [
            'payment_id' => $this->paymentId,
            'status' => $this->status,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'is_paid' => $this->isPaid,
            'failure_reason' => $this->failureReason,
            'failure_code' => $this->failureCode,
            'metadata' => $this->metadata,
            'created_at' => $this->createdAt?->format('c'),
            'paid_at' => $this->paidAt?->format('c'),
            'failed_at' => $this->failedAt?->format('c'),
            'receipt_url' => $this->receiptUrl,
            'refunds' => $this->refunds,
        ];
    }

    /**
     * Check if payment is completed successfully
     *
     * @return bool
     */
    public function isCompleted(): bool {
        return $this->isPaid && $this->status === 'succeeded';
    }

    /**
     * Check if payment is still processing
     *
     * @return bool
     */
    public function isPending(): bool {
        return in_array($this->status, ['pending', 'processing', 'requires_action']);
    }

    /**
     * Check if payment failed permanently
     *
     * @return bool
     */
    public function isFailed(): bool {
        return in_array($this->status, ['failed', 'cancelled', 'declined']);
    }

    /**
     * Check if payment was refunded
     *
     * @return bool
     */
    public function isRefunded(): bool {
        return !empty($this->refunds) && $this->getTotalRefunded() > 0;
    }

    /**
     * Check if payment was fully refunded
     *
     * @return bool
     */
    public function isFullyRefunded(): bool {
        return $this->getTotalRefunded() >= $this->amount;
    }

    /**
     * Get total refunded amount
     *
     * @return float
     */
    public function getTotalRefunded(): float {
        if (empty($this->refunds)) {
            return 0.0;
        }

        return array_sum(array_column($this->refunds, 'amount'));
    }

    /**
     * Get user-friendly status message
     *
     * @return string
     */
    public function getStatusMessage(): string {
        if ($this->isFullyRefunded()) {
            return 'Payment fully refunded';
        }

        if ($this->isRefunded()) {
            $refunded = $this->getTotalRefunded();
            return "Payment completed (€{$refunded} refunded)";
        }

        return match ($this->status) {
            'succeeded', 'paid' => 'Payment completed successfully',
            'pending' => 'Payment is being processed',
            'processing' => 'Payment is being processed',
            'requires_action' => 'Payment requires your action',
            'failed' => $this->failureReason ?? 'Payment failed',
            'cancelled' => 'Payment cancelled',
            'declined' => 'Payment declined',
            default => 'Unknown payment status'
        };
    }

    /**
     * Get status color for UI display
     *
     * @return string CSS color class
     */
    public function getStatusColor(): string {
        if ($this->isCompleted()) {
            return 'green';
        }

        if ($this->isPending()) {
            return 'yellow';
        }

        if ($this->isFailed()) {
            return 'red';
        }

        return 'gray';
    }
}
