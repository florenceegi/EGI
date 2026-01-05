<?php

namespace App\Enums\Payment;

/**
 * @package App\Enums\Payment
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Unified Wallet Architecture)
 * @date 2026-01-01
 * @purpose Define available payment types for the platform
 *
 * @rationale Centralized enum for payment type management.
 *            Used by PaymentAvailabilityResolver and WalletDestination model.
 *            Supports both fiat (Stripe, PayPal, IBAN) and crypto (Algorand) payments.
 *
 * @context From Contratto Operativo - PaymentTypes:
 *          - STRIPE: Card payments via Stripe Connect
 *          - PAYPAL: PayPal merchant payments
 *          - BANK_TRANSFER: SEPA/Wire transfers via IBAN
 *          - ALGORAND: Native Algorand blockchain payments
 */
enum PaymentTypeEnum: string
{
    case STRIPE = 'stripe';
    case PAYPAL = 'paypal';
    case BANK_TRANSFER = 'bank_transfer';
    case ALGORAND = 'algorand';

    /**
     * Get human-readable label for the payment type
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::STRIPE => 'Credit/Debit Card (Stripe)',
            self::PAYPAL => 'PayPal',
            self::BANK_TRANSFER => 'Bank Transfer (SEPA/Wire)',
            self::ALGORAND => 'Algorand (Crypto)',
        };
    }

    /**
     * Get the icon identifier for UI display
     *
     * @return string
     */
    public function icon(): string
    {
        return match ($this) {
            self::STRIPE => 'credit-card',
            self::PAYPAL => 'paypal',
            self::BANK_TRANSFER => 'bank',
            self::ALGORAND => 'algorand',
        };
    }

    /**
     * Check if this payment type is fiat-based
     *
     * @return bool
     */
    public function isFiat(): bool
    {
        return in_array($this, [self::STRIPE, self::PAYPAL, self::BANK_TRANSFER], true);
    }

    /**
     * Check if this payment type is crypto-based
     *
     * @return bool
     */
    public function isCrypto(): bool
    {
        return $this === self::ALGORAND;
    }

    /**
     * Check if this payment type supports automatic splits via PSP
     *
     * @return bool
     */
    public function supportsAutomaticSplit(): bool
    {
        return match ($this) {
            self::STRIPE => true,  // Via Stripe Connect transfers
            self::PAYPAL => true,  // Via PayPal Commerce Platform
            self::BANK_TRANSFER => false, // Manual reconciliation required
            self::ALGORAND => true,  // Via atomic group transactions
        };
    }

    /**
     * Get the required destination field for this payment type
     *
     * @return string Field name in WalletDestination
     */
    public function destinationField(): string
    {
        return match ($this) {
            self::STRIPE => 'stripe_account_id',
            self::PAYPAL => 'paypal_merchant_id',
            self::BANK_TRANSFER => 'iban_encrypted',
            self::ALGORAND => 'algorand_address',
        };
    }

    /**
     * Get all fiat payment types
     *
     * @return array<PaymentTypeEnum>
     */
    public static function fiatTypes(): array
    {
        return array_filter(
            self::cases(),
            fn($type) => $type->isFiat()
        );
    }

    /**
     * Get all crypto payment types
     *
     * @return array<PaymentTypeEnum>
     */
    public static function cryptoTypes(): array
    {
        return array_filter(
            self::cases(),
            fn($type) => $type->isCrypto()
        );
    }

    /**
     * Validate a destination value for this payment type
     *
     * @param string|null $value
     * @return bool
     */
    public function validateDestination(?string $value): bool
    {
        if (empty($value)) {
            return false;
        }

        return match ($this) {
            self::STRIPE => str_starts_with($value, 'acct_'),
            self::PAYPAL => filter_var($value, FILTER_VALIDATE_EMAIL) !== false || preg_match('/^[A-Z0-9]{13}$/', $value),
            self::BANK_TRANSFER => preg_match('/^[A-Z]{2}[0-9]{2}[A-Z0-9]{4,30}$/', strtoupper(str_replace(' ', '', $value))) === 1,
            self::ALGORAND => strlen($value) === 58 && preg_match('/^[A-Z2-7]{58}$/', $value) === 1,
        };
    }
}
