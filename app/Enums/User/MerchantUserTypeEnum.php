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
    case EPP = 'epp';
    case TRADER_PRO = 'trader_pro';
    case PA_ENTITY = 'pa_entity';

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
            self::EPP => __('user_types.epp'),
            self::TRADER_PRO => __('user_types.trader_pro'),
            self::PA_ENTITY => __('user_types.pa_entity'),
        };
    }

    /**
     * Get user types that can own collections
     * 
     * @return array<string>
     */
    public static function collectionOwnerTypes(): array {
        return [
            self::CREATOR->value,
            self::COMPANY->value,
            self::PATRON->value,  // mecenate
            self::EPP->value,     // EPP owns their specific ecosystem
            self::PA_ENTITY->value,
        ];
    }

    /**
     * Check if a user type can own collections
     *
     * @param string|null $userType
     * @return bool
     */
    public static function canOwnCollections(?string $userType): bool {
        if ($userType === null) {
            return false;
        }

        return in_array($userType, self::collectionOwnerTypes(), true);
    }
}
