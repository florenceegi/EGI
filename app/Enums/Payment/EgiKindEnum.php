<?php

namespace App\Enums\Payment;

/**
 * @package App\Enums\Payment
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Unified Wallet Architecture)
 * @date 2026-01-01
 * @purpose Define EGI transaction kinds for royalty differentiation
 *
 * @rationale Distinguishes between primary market (mint) and secondary market (rebind)
 *            transactions for proper royalty calculation.
 *
 * @context From Contratto Operativo - Wallet Split:
 *          - MINT_PRIMARY: First sale, uses royalty_mint percentages
 *          - REBIND_SECONDARY: Resale, uses royalty_rebind percentages
 *
 * @legislative Complies with Legge 633/1941 Art. 144 (droit de suite)
 */
enum EgiKindEnum: string
{
    case MINT_PRIMARY = 'mint_primary';
    case REBIND_SECONDARY = 'rebind_secondary';

    /**
     * Get human-readable label
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::MINT_PRIMARY => 'Primary Sale (Mint)',
            self::REBIND_SECONDARY => 'Secondary Sale (Rebind)',
        };
    }

    /**
     * Get the royalty field name to use on Wallet model
     *
     * @return string
     */
    public function royaltyField(): string
    {
        return match ($this) {
            self::MINT_PRIMARY => 'royalty_mint',
            self::REBIND_SECONDARY => 'royalty_rebind',
        };
    }

    /**
     * Check if this is a primary market transaction
     *
     * @return bool
     */
    public function isPrimary(): bool
    {
        return $this === self::MINT_PRIMARY;
    }

    /**
     * Check if this is a secondary market transaction
     *
     * @return bool
     */
    public function isSecondary(): bool
    {
        return $this === self::REBIND_SECONDARY;
    }

    /**
     * Get the expected total royalty percentage for this kind
     *
     * @return float Total percentage expected across all wallets
     */
    public function expectedTotalPercentage(): float
    {
        return match ($this) {
            self::MINT_PRIMARY => 100.0,  // Full distribution
            self::REBIND_SECONDARY => 6.1,  // Secondary market royalty
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
            self::MINT_PRIMARY => 'Primary market sale - full distribution to stakeholders',
            self::REBIND_SECONDARY => 'Secondary market resale - droit de suite royalty distribution',
        };
    }
}
