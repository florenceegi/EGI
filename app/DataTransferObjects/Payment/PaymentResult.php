<?php

namespace App\DataTransferObjects\Payment;

/**
 * @Oracode DTO: Payment Result Data
 * 🎯 Purpose: Structured response from payment processing
 * 🧱 Core Logic: Standard format for payment success/failure responses
 * 🛡️ Security: Sanitized payment data, no sensitive PSP details exposed
 *
 * @package App\DataTransferObjects\Payment
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Blockchain Integration)
 * @date 2025-10-07
 * @purpose Payment processing response data with status and details
 */
readonly class PaymentResult
{
    public function __construct(
        public bool $success,
        public string $paymentId,
        public float $amount,
        public string $currency,
        public string $status,
        public ?string $errorMessage = null,
        public ?string $errorCode = null,
        public array $metadata = [],
        public ?string $redirectUrl = null,
        public ?string $receiptUrl = null,
        public ?\DateTimeImmutable $processedAt = null
    ) {
    }

    /**
     * Create successful payment result
     * 
     * @param string $paymentId PSP payment ID
     * @param float $amount Payment amount
     * @param string $currency Payment currency
     * @param array $metadata Additional metadata
     * @return static
     */
    public static function success(
        string $paymentId,
        float $amount,
        string $currency,
        array $metadata = []
    ): static {
        return new static(
            success: true,
            paymentId: $paymentId,
            amount: $amount,
            currency: strtoupper($currency),
            status: 'completed',
            metadata: $metadata,
            processedAt: new \DateTimeImmutable()
        );
    }

    /**
     * Create failed payment result
     * 
     * @param string $paymentId PSP payment ID (may be empty for early failures)
     * @param string $errorMessage Human-readable error message
     * @param string $errorCode Error code for debugging
     * @param array $metadata Additional metadata
     * @return static
     */
    public static function failure(
        string $paymentId,
        string $errorMessage,
        string $errorCode = 'PAYMENT_FAILED',
        array $metadata = []
    ): static {
        return new static(
            success: false,
            paymentId: $paymentId,
            amount: 0.0,
            currency: '',
            status: 'failed',
            errorMessage: $errorMessage,
            errorCode: $errorCode,
            metadata: $metadata,
            processedAt: new \DateTimeImmutable()
        );
    }

    /**
     * Create pending payment result (requires user action)
     * 
     * @param string $paymentId PSP payment ID
     * @param float $amount Payment amount
     * @param string $currency Payment currency
     * @param string $redirectUrl URL for user to complete payment
     * @param array $metadata Additional metadata
     * @return static
     */
    public static function pending(
        string $paymentId,
        float $amount,
        string $currency,
        string $redirectUrl,
        array $metadata = []
    ): static {
        return new static(
            success: false, // Not successful yet
            paymentId: $paymentId,
            amount: $amount,
            currency: strtoupper($currency),
            status: 'pending',
            redirectUrl: $redirectUrl,
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
            'payment_id' => $this->paymentId,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'status' => $this->status,
            'error_message' => $this->errorMessage,
            'error_code' => $this->errorCode,
            'metadata' => $this->metadata,
            'redirect_url' => $this->redirectUrl,
            'receipt_url' => $this->receiptUrl,
            'processed_at' => $this->processedAt?->format('c'),
        ];
    }

    /**
     * Check if payment is completed and successful
     * 
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->success && $this->status === 'completed';
    }

    /**
     * Check if payment requires user action
     * 
     * @return bool
     */
    public function requiresAction(): bool
    {
        return $this->status === 'pending' && !empty($this->redirectUrl);
    }

    /**
     * Check if payment failed permanently
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
            'completed' => 'Payment completed successfully',
            'pending' => 'Payment pending user action',
            'failed' => $this->errorMessage ?? 'Payment failed',
            'cancelled' => 'Payment cancelled by user',
            'refunded' => 'Payment refunded',
            default => 'Unknown payment status'
        };
    }
}