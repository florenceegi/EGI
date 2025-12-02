<?php

namespace App\Enums\User;

/**
 * @Oracode Enum: Merchant User Types
 * 🎯 Purpose: Define user types that can sell (merchants) and need Stripe onboarding
 * 🧱 Core Logic: Centralized definition of merchant user types
 *
 * @package App\Enums\User
 * @author Padmin D. Curtis for Fabio Cherici
 * @version 1.0.0
 * @date 2025-12-01
 */
enum MerchantUserTypeEnum: string {
    case CREATOR = 'creator';
    case COMPANY = 'company';
    case PATRON = 'patron';

    /**
     * Get all merchant user type values as array
     *
     * @return array<string>
     */
    public static function values(): array {
        return array_column(self::cases(), 'value');
    }

    /**
     * Check if a user type is a merchant
     *
     * @param string|null $userType
     * @return bool
     */
    public static function isMerchant(?string $userType): bool {
        if ($userType === null) {
            return false;
        }

        return in_array($userType, self::values(), true);
    }

    /**
     * Get localized label for the merchant type
     *
     * @return string
     */
    public function label(): string {
        return match ($this) {
            self::CREATOR => __('user_types.creator'),
            self::COMPANY => __('user_types.company'),
            self::PATRON => __('user_types.patron'),
        };
    }
}
