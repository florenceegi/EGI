<?php

namespace App\DataTransferObjects\Payment;

use InvalidArgumentException;

/**
 * @Oracode DTO: Payment Request Data
 * 🎯 Purpose: Structured data for payment processing requests
 * 🧱 Core Logic: Validate and structure payment data for PSP providers
 * 🛡️ MiCA-SAFE: Only FIAT currency payments, no crypto-asset data
 *
 * @package App\DataTransferObjects\Payment
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Blockchain Integration)
 * @date 2025-10-07
 * @purpose Payment request data transfer object with validation
 */
readonly class PaymentRequest {
    public function __construct(
        public float $amount,
        public string $currency,
        public string $customerEmail,
        public ?int $egiId = null,
        public ?int $reservationId = null,
        public ?int $userId = null,
        public array $metadata = [],
        public ?string $successUrl = null,
        public ?string $cancelUrl = null,
        public ?string $webhookUrl = null,
        public array $merchantContext = []
    ) {
        $this->validateAmount($amount);
        $this->validateCurrency($currency);
        $this->validateEmail($customerEmail);
        
        if ($egiId !== null) {
            $this->validateEgiId($egiId);
        }
    }

    /**
     * Create from array data
     *
     * @param array $data Input data array
     * @return static
     * @throws \InvalidArgumentException Invalid data provided
     */
    public static function fromArray(array $data): static {
        return new static(
            amount: (float) ($data['amount'] ?? 0),
            currency: strtoupper($data['currency'] ?? 'EUR'),
            customerEmail: $data['customer_email'] ?? '',
            egiId: isset($data['egi_id']) ? (int) $data['egi_id'] : null,
            reservationId: isset($data['reservation_id']) ? (int) $data['reservation_id'] : null,
            userId: isset($data['user_id']) ? (int) $data['user_id'] : null,
            metadata: $data['metadata'] ?? [],
            successUrl: $data['success_url'] ?? null,
            cancelUrl: $data['cancel_url'] ?? null,
            webhookUrl: $data['webhook_url'] ?? null,
            merchantContext: $data['merchant_context'] ?? []
        );
    }

    /**
     * Convert to array
     *
     * @return array
     */
    public function toArray(): array {
        return [
            'amount' => $this->amount,
            'currency' => $this->currency,
            'customer_email' => $this->customerEmail,
            'egi_id' => $this->egiId,
            'reservation_id' => $this->reservationId,
            'user_id' => $this->userId,
            'metadata' => $this->metadata,
            'success_url' => $this->successUrl,
            'cancel_url' => $this->cancelUrl,
            'webhook_url' => $this->webhookUrl,
            'merchant_context' => $this->merchantContext,
        ];
    }

    /**
     * Get formatted amount in cents/pence for PSP
     *
     * @return int Amount in cents
     */
    public function getAmountInCents(): int {
        return (int) round($this->amount * 100);
    }

    /**
     * Get PSP-compatible metadata
     *
     * @return array Metadata formatted for PSP
     */
    public function getPspMetadata(): array {
        $base = [
            'platform' => 'FlorenceEGI',
            'type' => $this->egiId ? 'egi_purchase' : 'egili_purchase'
        ];

        if ($this->egiId) {
            $base['egi_id'] = (string) $this->egiId;
        }

        if ($this->reservationId) {
            $base['reservation_id'] = (string) $this->reservationId;
        }

        if ($this->userId) {
            $base['user_id'] = (string) $this->userId;
        }

        return array_merge($base, $this->metadata);
    }

    /**
     * Get merchant PSP context (Stripe Connect / PayPal merchant IDs).
     */
    public function getMerchantContext(): array
    {
        return $this->merchantContext;
    }

    /**
     * Clone the payment request with a new merchant context.
     */
    public function withMerchantContext(array $merchantContext): static
    {
        return new static(
            amount: $this->amount,
            currency: $this->currency,
            customerEmail: $this->customerEmail,
            egiId: $this->egiId,
            reservationId: $this->reservationId,
            userId: $this->userId,
            metadata: $this->metadata,
            successUrl: $this->successUrl,
            cancelUrl: $this->cancelUrl,
            webhookUrl: $this->webhookUrl,
            merchantContext: $merchantContext
        );
    }

    /**
     * Validate amount
     *
     * @param float $amount
     * @throws \InvalidArgumentException
     */
    private function validateAmount(float $amount): void {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Payment amount must be greater than 0');
        }

        if ($amount > 100000) {
            throw new InvalidArgumentException('Payment amount too large (max €100,000)');
        }
    }

    /**
     * Validate currency
     *
     * @param string $currency
     * @throws \InvalidArgumentException
     */
    private function validateCurrency(string $currency): void {
        $supportedCurrencies = ['EUR', 'USD', 'GBP'];

        if (!in_array(strtoupper($currency), $supportedCurrencies)) {
            throw new InvalidArgumentException("Currency {$currency} not supported");
        }
    }

    /**
     * Validate email
     *
     * @param string $email
     * @throws \InvalidArgumentException
     */
    private function validateEmail(string $email): void {
        if (!\filter_var($email, \FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid customer email format');
        }
    }

    /**
     * Validate EGI ID
     *
     * @param int $egiId
     * @throws \InvalidArgumentException
     */
    private function validateEgiId(int $egiId): void {
        if ($egiId <= 0) {
            throw new InvalidArgumentException('Invalid EGI ID');
        }
    }
}
