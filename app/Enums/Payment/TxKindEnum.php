<?php

namespace App\Enums\Payment;

/**
 * @package App\Enums\Payment
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Unified Wallet Architecture)
 * @date 2026-01-01
 * @purpose Define transaction types for Order tracking
 *
 * @rationale Provides clear categorization of transaction types
 *            for Order model and payment flow tracking.
 *
 * @context From Contratto Narrativo EGI Claiming:
 *          - PURCHASE: Initial EGI purchase (creates pending ownership)
 *          - REBIND: Secondary market EGI transfer
 *          - CLAIM: User claims EGI from Treasury to personal wallet
 */
enum TxKindEnum: string
{
    case PURCHASE = 'purchase';
    case REBIND = 'rebind';
    case CLAIM = 'claim';

    /**
     * Get human-readable label
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::PURCHASE => 'Purchase',
            self::REBIND => 'Rebind (Transfer)',
            self::CLAIM => 'Claim to Wallet',
        };
    }

    /**
     * Get the corresponding EgiKind for royalty calculation
     *
     * @return EgiKindEnum|null Null for CLAIM (no royalty distribution)
     */
    public function toEgiKind(): ?EgiKindEnum
    {
        return match ($this) {
            self::PURCHASE => EgiKindEnum::MINT_PRIMARY,
            self::REBIND => EgiKindEnum::REBIND_SECONDARY,
            self::CLAIM => null,  // Claim is gas-only, no royalties
        };
    }

    /**
     * Check if this transaction type requires payment
     *
     * @return bool
     */
    public function requiresPayment(): bool
    {
        return match ($this) {
            self::PURCHASE => true,
            self::REBIND => true,
            self::CLAIM => false,  // Claim only requires gas
        };
    }

    /**
     * Check if this transaction type triggers royalty distribution
     *
     * @return bool
     */
    public function triggersRoyaltyDistribution(): bool
    {
        return match ($this) {
            self::PURCHASE => true,
            self::REBIND => true,
            self::CLAIM => false,
        };
    }

    /**
     * Check if this transaction type requires blockchain interaction
     *
     * @return bool
     */
    public function requiresBlockchain(): bool
    {
        return match ($this) {
            self::PURCHASE => true,   // Mint EGI on-chain
            self::REBIND => true,     // Transfer EGI on-chain
            self::CLAIM => true,      // Transfer from Treasury
        };
    }

    /**
     * Get description for audit/logging purposes
     *
     * @return string
     */
    public function auditDescription(): string
    {
        return match ($this) {
            self::PURCHASE => 'Primary market EGI purchase with minting',
            self::REBIND => 'Secondary market EGI transfer with royalty distribution',
            self::CLAIM => 'User claiming EGI from Treasury to personal Algorand wallet',
        };
    }
}
