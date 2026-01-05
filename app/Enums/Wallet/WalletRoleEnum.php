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
    case COMPANY = 'Company';
    case BUYER = 'Buyer';  // EGI purchaser - for claiming flow

    /**
     * Get mint royalty percentage for this role (Primary Market)
     *
     * Note: These are BASE royalties. Actual distribution depends on user type:
     * - CREATOR: 68% (+ EPP mandatory via % or subscription + Frangette 2%)
     * - COMPANY: 90% base (no EPP obligation, no Frangette) - EPP voluntary reduces this
     *
     * @return float Percentage (0-100)
     */
    public function getMintRoyalty(): float {
        return match ($this) {
            self::CREATOR => 68.0,
            self::COMPANY => 90.0, // No EPP, no Frangette obligation
            self::EPP => 20.0,
            self::NATAN => 10.0,
            self::FRANGETTE => 2.0,
            self::BUYER => 0.0,  // Buyer doesn't receive royalties
        };
    }

    /**
     * Get rebind (secondary market) royalty percentage for this role
     *
     * Note: COMPANY does not pay Frangette (0.1%) and EPP is optional
     * - CREATOR total rebind: 4.5% + 0.8% EPP + 0.7% Natan + 0.1% Frangette = 6.1%
     * - COMPANY total rebind: 4.6% + 0.7% Natan = 5.3% (EPP optional)
     *
     * @return float Percentage
     */
    public function getRebindRoyalty(): float {
        return match ($this) {
            self::CREATOR => 4.5,
            self::COMPANY => 4.6, // Gets Frangette share (4.5 + 0.1)
            self::EPP => 0.8,
            self::NATAN => 0.7,
            self::FRANGETTE => 0.1,
            self::BUYER => 0.0,  // Buyer doesn't receive royalties
        };
    }

    /**
     * Get wallet address from config for platform roles
     *
     * @return string|null Algorand wallet address (null for dynamic user roles)
     */
    public function getWalletAddress(): ?string {
        return match ($this) {
            self::NATAN => config('app.natan_wallet_address'),
            self::EPP => config('app.epp_wallet_address'),
            self::FRANGETTE => config('app.frangette_wallet_address'),
            self::CREATOR, self::COMPANY, self::BUYER => null, // User's wallet - dynamic
        };
    }

    /**
     * Get user ID from config for platform roles
     *
     * @return int|null Platform user ID (null for dynamic user roles)
     * 
     * NOTE: EPP returns null because EPP is now dynamically assigned per-collection
     * by the user, not a fixed platform account. EPP wallet creation depends on
     * the collection's epp_project_id relationship.
     */
    public function getUserId(): ?int {
        return match ($this) {
            self::NATAN => config('app.natan_id', 1),
            self::EPP => null, // EPP is dynamic per-collection, not a fixed system account
            self::FRANGETTE => config('app.frangette_id', 3),
            self::CREATOR, self::COMPANY, self::BUYER => null, // Dynamic user
        };
    }

    /**
     * Check if this role is a platform role (not user-specific)
     * Platform roles are system accounts: Natan, Frangette
     * Dynamic roles: Creator, Company, EPP (assigned per-collection)
     *
     * @return bool
     */
    public function isPlatformRole(): bool {
        // EPP is no longer a fixed platform role - it's dynamically assigned per-collection
        return in_array($this, [self::NATAN, self::FRANGETTE], true);
    }

    /**
     * Validate total mint percentages sum to 100%
     * Note: Excludes COMPANY as it's mutually exclusive with CREATOR
     *
     * @return bool
     */
    public static function validateMintTotal(): bool {
        // Exclude COMPANY and BUYER from total - they're not royalty recipients
        $roles = array_filter(
            self::cases(),
            fn($role) => !in_array($role, [self::COMPANY, self::BUYER], true)
        );

        $total = array_sum(array_map(
            fn($role) => $role->getMintRoyalty(),
            $roles
        ));

        return abs($total - 100.0) < 0.01; // Float precision tolerance
    }

    /**
     * Get total rebind percentage across all roles
     * Note: Excludes COMPANY as it's mutually exclusive with CREATOR
     *
     * @return float Total secondary market royalty (should be 6.1%)
     */
    public static function getTotalRebindPercentage(): float {
        // Exclude COMPANY and BUYER from total - they're not standard royalty recipients
        $roles = array_filter(
            self::cases(),
            fn($role) => !in_array($role, [self::COMPANY, self::BUYER], true)
        );

        return array_sum(array_map(
            fn($role) => $role->getRebindRoyalty(),
            $roles
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
            self::COMPANY => 'Company/Business - Corporate entity',
            self::EPP => 'EPP Association - Environmental impact partner',
            self::NATAN => 'Natan Platform - Technology & infrastructure',
            self::FRANGETTE => 'Frangette Association - Ecosystem development',
            self::BUYER => 'Buyer - EGI purchaser and owner',
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
