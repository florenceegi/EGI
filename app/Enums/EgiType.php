<?php

namespace App\Enums;

/**
 * @package App\Enums
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Dual Architecture)
 * @date 2025-10-19
 * @purpose Enum for EGI architecture types (ASA/SmartContract/PreMint)
 */
enum EgiType: string
{
/**
     * Classic EGI - Algorand Standard Asset (ASA)
     * Static blockchain token, no AI interaction, basic functionality
     */
    case ASA = 'ASA';

/**
     * Living EGI - Algorand SmartContract
     * Autonomous asset with AI curator, promoter, and memory
     * Premium feature with subscription
     */
    case SMART_CONTRACT = 'SmartContract';

/**
     * Pre-Mint EGI - Virtual AI-managed asset
     * Not yet minted on blockchain, managed by N.A.T.A.N
     * Used for testing, promotion, and AI training before mint
     */
    case PRE_MINT = 'PreMint';

    /**
     * Get human-readable label for the enum value
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::ASA => 'EGI Classico (ASA)',
            self::SMART_CONTRACT => 'EGI Vivente (SmartContract)',
            self::PRE_MINT => 'EGI Pre-Mint (Virtuale)',
        };
    }

    /**
     * Get description for the enum value
     *
     * @return string
     */
    public function description(): string
    {
        return match ($this) {
            self::ASA => 'Asset statico su blockchain Algorand. Certificato di autenticità digitale permanente.',
            self::SMART_CONTRACT => 'Asset intelligente con AI integrata. Analisi automatica, promozione, memoria evolutiva.',
            self::PRE_MINT => 'Asset virtuale gestito da AI prima del mint. Test, promozione e training.',
        };
    }

    /**
     * Check if this type requires blockchain minting
     *
     * @return bool
     */
    public function requiresBlockchain(): bool
    {
        return match ($this) {
            self::ASA, self::SMART_CONTRACT => true,
            self::PRE_MINT => false,
        };
    }

    /**
     * Check if this type supports AI features
     *
     * @return bool
     */
    public function supportsAI(): bool
    {
        return match ($this) {
            self::SMART_CONTRACT, self::PRE_MINT => true,
            self::ASA => false,
        };
    }

    /**
     * Check if this type is premium (requires payment)
     *
     * @return bool
     */
    public function isPremium(): bool
    {
        return match ($this) {
            self::SMART_CONTRACT => true,
            self::ASA, self::PRE_MINT => false,
        };
    }

    /**
     * Get badge color class for UI
     *
     * @return string Tailwind CSS classes
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::ASA => 'bg-blue-600 text-white',
            self::SMART_CONTRACT => 'bg-purple-600 text-white',
            self::PRE_MINT => 'bg-orange-500 text-white',
        };
    }

    /**
     * Get all types as array for select/dropdown
     *
     * @return array
     */
    public static function options(): array
    {
        return array_map(
            fn($case) => [
                'value' => $case->value,
                'label' => $case->label(),
                'description' => $case->description(),
                'isPremium' => $case->isPremium(),
            ],
            self::cases()
        );
    }
}