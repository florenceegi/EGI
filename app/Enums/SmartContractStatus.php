<?php

namespace App\Enums;

/**
 * @package App\Enums
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Dual Architecture)
 * @date 2025-10-19
 * @purpose Enum for SmartContract lifecycle status
 */
enum SmartContractStatus: string
{
/**
     * Deploying - SmartContract is being deployed to blockchain
     */
    case DEPLOYING = 'deploying';

/**
     * Active - SmartContract is deployed and operational
     */
    case ACTIVE = 'active';

/**
     * Paused - SmartContract is temporarily paused (admin action)
     */
    case PAUSED = 'paused';

/**
     * Terminated - SmartContract has been terminated (permanent)
     */
    case TERMINATED = 'terminated';

    /**
     * Get human-readable label for the enum value
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::DEPLOYING => 'In Deploy',
            self::ACTIVE => 'Attivo',
            self::PAUSED => 'In Pausa',
            self::TERMINATED => 'Terminato',
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
            self::DEPLOYING => 'SmartContract in fase di deploy su blockchain Algorand.',
            self::ACTIVE => 'SmartContract operativo. Le funzioni AI sono attive.',
            self::PAUSED => 'SmartContract temporaneamente in pausa. Le funzioni AI sono sospese.',
            self::TERMINATED => 'SmartContract terminato in modo permanente.',
        };
    }

    /**
     * Check if AI triggers should execute for this status
     *
     * @return bool
     */
    public function allowsAITriggers(): bool
    {
        return match ($this) {
            self::ACTIVE => true,
            self::DEPLOYING, self::PAUSED, self::TERMINATED => false,
        };
    }

    /**
     * Check if SmartContract can be paused
     *
     * @return bool
     */
    public function canPause(): bool
    {
        return match ($this) {
            self::ACTIVE => true,
            self::DEPLOYING, self::PAUSED, self::TERMINATED => false,
        };
    }

    /**
     * Check if SmartContract can be resumed
     *
     * @return bool
     */
    public function canResume(): bool
    {
        return match ($this) {
            self::PAUSED => true,
            self::DEPLOYING, self::ACTIVE, self::TERMINATED => false,
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
            self::DEPLOYING => 'bg-blue-500 text-white animate-pulse',
            self::ACTIVE => 'bg-green-600 text-white',
            self::PAUSED => 'bg-yellow-600 text-white',
            self::TERMINATED => 'bg-red-700 text-white',
        };
    }
}
