<?php

namespace App\Enums;

/**
 * @package App\Enums
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Dual Architecture)
 * @date 2025-10-19
 * @purpose Enum for EGI Vivente (Living) subscription status
 */
enum EgiLivingStatus: string
{
/**
     * Payment pending - Subscription created but not paid yet
     */
    case PENDING_PAYMENT = 'pending_payment';

/**
     * Active - Subscription is active and AI features are enabled
     */
    case ACTIVE = 'active';

/**
     * Suspended - Subscription temporarily suspended (payment failed, etc.)
     */
    case SUSPENDED = 'suspended';

/**
     * Cancelled - User cancelled the subscription
     */
    case CANCELLED = 'cancelled';

/**
     * Expired - Subscription period ended (for non-lifetime plans)
     */
    case EXPIRED = 'expired';

    /**
     * Get human-readable label for the enum value
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING_PAYMENT => 'In attesa di pagamento',
            self::ACTIVE => 'Attivo',
            self::SUSPENDED => 'Sospeso',
            self::CANCELLED => 'Annullato',
            self::EXPIRED => 'Scaduto',
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
            self::PENDING_PAYMENT => 'Abbonamento creato, in attesa del completamento del pagamento.',
            self::ACTIVE => 'Abbonamento attivo. Le funzionalità AI sono abilitate.',
            self::SUSPENDED => 'Abbonamento temporaneamente sospeso. Verificare il metodo di pagamento.',
            self::CANCELLED => 'Abbonamento annullato dall\'utente. Le funzionalità AI sono disabilitate.',
            self::EXPIRED => 'Abbonamento scaduto. Rinnova per riattivare le funzionalità AI.',
        };
    }

    /**
     * Check if AI features should be enabled for this status
     *
     * @return bool
     */
    public function aiEnabled(): bool
    {
        return match ($this) {
            self::ACTIVE => true,
            self::PENDING_PAYMENT, self::SUSPENDED, self::CANCELLED, self::EXPIRED => false,
        };
    }

    /**
     * Check if subscription can be renewed
     *
     * @return bool
     */
    public function canRenew(): bool
    {
        return match ($this) {
            self::EXPIRED, self::CANCELLED => true,
            self::PENDING_PAYMENT, self::ACTIVE, self::SUSPENDED => false,
        };
    }

    /**
     * Check if subscription can be cancelled
     *
     * @return bool
     */
    public function canCancel(): bool
    {
        return match ($this) {
            self::ACTIVE, self::SUSPENDED => true,
            self::PENDING_PAYMENT, self::CANCELLED, self::EXPIRED => false,
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
            self::PENDING_PAYMENT => 'bg-yellow-500 text-white',
            self::ACTIVE => 'bg-green-600 text-white',
            self::SUSPENDED => 'bg-orange-600 text-white',
            self::CANCELLED => 'bg-gray-600 text-white',
            self::EXPIRED => 'bg-red-600 text-white',
        };
    }

    /**
     * Get icon class for UI
     *
     * @return string Font Awesome icon class
     */
    public function iconClass(): string
    {
        return match ($this) {
            self::PENDING_PAYMENT => 'fa-clock',
            self::ACTIVE => 'fa-check-circle',
            self::SUSPENDED => 'fa-pause-circle',
            self::CANCELLED => 'fa-times-circle',
            self::EXPIRED => 'fa-exclamation-triangle',
        };
    }
}

