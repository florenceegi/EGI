<?php

namespace App\Enums\Wallet;

/**
 * @package App\Enums\Wallet
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Tokenomics)
 * @date 2025-10-10
 * @purpose Define wallet roles with immutable royalty percentages
 * 
 * @rationale Centralized, type-safe, immutable source of truth for royalty distribution.
 *            Replaces fragile config files with enum-based tokenomics management.
 *            Ensures compile-time validation and prevents runtime configuration corruption.
 * 
 * @context FlorenceEGI tokenomics distribution:
 *          MINT (Primary Market): Creator 68%, EPP 20%, Natan 10%, Frangette 2% = 100%
 *          REBIND (Secondary Market): Creator 4.5%, EPP 0.8%, Natan 0.7%, Frangette 0.1% = 6.1%
 * 
 * @legislative Complies with Legge 633/1941 Art. 144 (droit de suite 0.25%-4%)
 *              FlorenceEGI applies 4.5% rebind to creator (disclosed in T&C)
 */
enum WalletRoleEnum: string {
    case CREATOR = 'Creator';
    case EPP = 'EPP';
    case NATAN = 'Natan';
    case FRANGETTE = 'Frangette';

    /**
     * Get mint royalty percentage for this role (Primary Market)
     * 
     * @return float Percentage (0-100)
     */
    public function getMintRoyalty(): float {
        return match ($this) {
            self::CREATOR => 68.0,
            self::EPP => 20.0,
            self::NATAN => 10.0,
            self::FRANGETTE => 2.0,
        };
    }

    /**
     * Get rebind (secondary market) royalty percentage for this role
     * 
     * @return float Percentage (0-6.1)
     */
    public function getRebindRoyalty(): float {
        return match ($this) {
            self::CREATOR => 4.5,
            self::EPP => 0.8,
            self::NATAN => 0.7,
            self::FRANGETTE => 0.1,
        };
    }

    /**
     * Get wallet address from config for platform roles
     * 
     * @return string|null Algorand wallet address (null for CREATOR - dynamic)
     */
    public function getWalletAddress(): ?string {
        return match ($this) {
            self::NATAN => config('app.natan_wallet_address'),
            self::EPP => config('app.epp_wallet_address'),
            self::FRANGETTE => config('app.frangette_wallet_address'),
            self::CREATOR => null, // User's wallet - dynamic
        };
    }

    /**
     * Get user ID from config for platform roles
     * 
     * @return int|null Platform user ID (null for CREATOR - dynamic)
     */
    public function getUserId(): ?int {
        return match ($this) {
            self::NATAN => config('app.natan_id', 1),
            self::EPP => config('app.epp_id', 2),
            self::FRANGETTE => config('app.frangette_id', 3),
            self::CREATOR => null, // Dynamic user
        };
    }

    /**
     * Check if this role is a platform role (not user-specific)
     * 
     * @return bool
     */
    public function isPlatformRole(): bool {
        return $this !== self::CREATOR;
    }

    /**
     * Validate total mint percentages sum to 100%
     * 
     * @return bool
     */
    public static function validateMintTotal(): bool {
        $total = array_sum(array_map(
            fn($role) => $role->getMintRoyalty(),
            self::cases()
        ));

        return abs($total - 100.0) < 0.01; // Float precision tolerance
    }

    /**
     * Get total rebind percentage across all roles
     * 
     * @return float Total secondary market royalty (should be 6.1%)
     */
    public static function getTotalRebindPercentage(): float {
        return array_sum(array_map(
            fn($role) => $role->getRebindRoyalty(),
            self::cases()
        ));
    }

    /**
     * Get all platform roles (exclude Creator)
     * 
     * @return array<WalletRoleEnum>
     */
    public static function platformRoles(): array {
        return array_filter(
            self::cases(),
            fn($role) => $role->isPlatformRole()
        );
    }

    /**
     * Get formatted description for this role
     * 
     * @return string Human-readable description
     */
    public function getDescription(): string {
        return match ($this) {
            self::CREATOR => 'Creator/Artist - Intellectual property owner',
            self::EPP => 'EPP Association - Environmental impact partner',
            self::NATAN => 'Natan Platform - Technology & infrastructure',
            self::FRANGETTE => 'Frangette Association - Ecosystem development',
        };
    }

    /**
     * Get formatted tokenomics summary
     * 
     * @return string Formatted string with mint and rebind percentages
     */
    public function getTokenomicsSummary(): string {
        return sprintf(
            '%s: Mint %.1f%%, Rebind %.1f%%',
            $this->value,
            $this->getMintRoyalty(),
            $this->getRebindRoyalty()
        );
    }
}
