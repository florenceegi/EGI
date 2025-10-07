<?php

namespace App\Contracts;

use App\DataTransferObjects\Payment\PaymentRequest;
use App\DataTransferObjects\Payment\PaymentResult;
use App\DataTransferObjects\Payment\RefundResult;
use App\DataTransferObjects\Payment\PaymentStatus;

/**
 * @Oracode Interface: Payment Service Interface
 * 🎯 Purpose: Contract for all payment service implementations (mock & real)
 * 🧱 Core Logic: Define standard interface for FIAT payment processing
 * 🛡️ MiCA-SAFE: Only FIAT payments, no crypto-asset custody
 *
 * @package App\Contracts
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Blockchain Integration)
 * @date 2025-10-07
 * @purpose Standard interface for payment service providers (Stripe, PayPal, etc.)
 */
interface PaymentServiceInterface {
    /**
     * Process payment for EGI purchase
     *
     * @param PaymentRequest $request Payment request data
     * @return PaymentResult Payment processing result
     * @throws \Exception Payment processing failed
     * @privacy-safe Process only payment data, no sensitive PII storage
     */
    public function processPayment(PaymentRequest $request): PaymentResult;

    /**
     * Verify webhook payload authenticity
     *
     * @param array $payload Webhook payload data
     * @return bool True if webhook is authentic
     * @throws \Exception Webhook verification failed
     * @security Critical for payment security - verify signature/timestamp
     */
    public function verifyWebhook(array $payload): bool;

    /**
     * Refund payment by payment ID
     *
     * @param string $paymentId Payment provider's payment ID
     * @return RefundResult Refund processing result
     * @throws \Exception Refund processing failed
     * @gdpr-compliant Audit trail for financial operations
     */
    public function refundPayment(string $paymentId): RefundResult;

    /**
     * Get payment status by payment ID
     *
     * @param string $paymentId Payment provider's payment ID
     * @return PaymentStatus Current payment status
     * @throws \Exception Status retrieval failed
     * @useful For monitoring payment completion, dispute status
     */
    public function getPaymentStatus(string $paymentId): PaymentStatus;

    /**
     * Get provider name for identification
     *
     * @return string Provider name (stripe, paypal, etc.)
     */
    public function getProviderName(): string;

    /**
     * Check if provider supports currency
     *
     * @param string $currency Currency code (EUR, USD, etc.)
     * @return bool True if currency is supported
     */
    public function supportsCurrency(string $currency): bool;

    /**
     * Get supported currencies list
     *
     * @return array Array of supported currency codes
     */
    public function getSupportedCurrencies(): array;
}
