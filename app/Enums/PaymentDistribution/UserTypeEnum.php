<?php

namespace App\Enums\PaymentDistribution;

/**
 * @Oracode Enum: Payment Distribution User Types
 * 🎯 Purpose: Type-safe user types for payment distributions
 * 🛡️ Privacy: Categorization for GDPR compliance and analytics
 * 🧱 Core Logic: Granular user segmentation for business intelligence
 *
 * @package App\Enums\PaymentDistribution
 * @author GitHub Copilot for Fabio Cherici
 * @version 1.0.0
 * @date 2025-08-20
 */
enum UserTypeEnum: string {
    case WEAK = 'weak';
    case CREATOR = 'creator';
    case COLLECTOR = 'collector';
    case COMMISSIONER = 'commissioner';
    case COMPANY = 'company';
    case EPP = 'epp';
    case TRADER_PRO = 'trader-pro';
    case VIP = 'vip';
    case NATAN = 'natan';

    // Aggiunti da UserRoleForInvite.php
    case PATRON = 'patron';
    case ADMIN = 'admin';
    case EDITOR = 'editor';
    case GUEST = 'guest';

    /**
     * Get the display name for the user type
     * @return string
     */
    public function getDisplayName(): string {
        return match ($this) {
            self::WEAK => __('payment_distribution.user_types.weak'),
            self::CREATOR => __('payment_distribution.user_types.creator'),
            self::COLLECTOR => __('payment_distribution.user_types.collector'),
            self::COMMISSIONER => __('payment_distribution.user_types.commissioner'),
            self::COMPANY => __('payment_distribution.user_types.company'),
            self::EPP => __('payment_distribution.user_types.epp'),
            self::TRADER_PRO => __('payment_distribution.user_types.trader_pro'),
            self::VIP => __('payment_distribution.user_types.vip'),
            self::NATAN => __('payment_distribution.user_types.natan'),
            self::PATRON => __('payment_distribution.user_types.patron'),
            self::ADMIN => __('payment_distribution.user_types.admin'),
            self::EDITOR => __('payment_distribution.user_types.editor'),
            self::GUEST => __('payment_distribution.user_types.guest'),
        };
    }

    /**
     * Get the description for the user type
     * @return string
     */
    public function getDescription(): string {
        return match ($this) {
            self::WEAK => __('payment_distribution.user_types_desc.weak'),
            self::CREATOR => __('payment_distribution.user_types_desc.creator'),
            self::COLLECTOR => __('payment_distribution.user_types_desc.collector'),
            self::COMMISSIONER => __('payment_distribution.user_types_desc.commissioner'),
            self::COMPANY => __('payment_distribution.user_types_desc.company'),
            self::EPP => __('payment_distribution.user_types_desc.epp'),
            self::TRADER_PRO => __('payment_distribution.user_types_desc.trader_pro'),
            self::VIP => __('payment_distribution.user_types_desc.vip'),
            self::NATAN => __('payment_distribution.user_types_desc.natan'),
            self::PATRON => __('payment_distribution.user_types_desc.patron'),
            self::ADMIN => __('payment_distribution.user_types_desc.admin'),
            self::EDITOR => __('payment_distribution.user_types_desc.editor'),
            self::GUEST => __('payment_distribution.user_types_desc.guest'),
        };
    }

    /**
     * Get all user types as array for selects
     * @return array<string, string>
     */
    public static function getOptions(): array {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->getDisplayName();
        }
        return $options;
    }

    /**
     * Get user types that are EPP-related
     * @return array<UserTypeEnum>
     */
    public static function getEppTypes(): array {
        return [self::EPP];
    }

    /**
     * Check if this user type is EPP-related
     * @return bool
     */
    public function isEpp(): bool {
        return $this === self::EPP;
    }

    /**
     * Get user types that are business-oriented
     * @return array<UserTypeEnum>
     */
    public static function getBusinessTypes(): array {
        return [self::COMPANY, self::TRADER_PRO];
    }

    /**
     * Check if this user type is business-oriented
     * @return bool
     */
    public function isBusiness(): bool {
        return in_array($this, self::getBusinessTypes());
    }

    /**
     * Get user types that are individual users
     * @return array<UserTypeEnum>
     */
    public static function getIndividualTypes(): array {
        return [self::WEAK, self::CREATOR, self::COLLECTOR, self::COMMISSIONER, self::VIP, self::PATRON, self::ADMIN, self::EDITOR, self::GUEST];
    }

    /**
     * Check if this user type is an individual user
     * @return bool
     */
    public function isIndividual(): bool {
        return in_array($this, self::getIndividualTypes());
    }
}
