<?php

namespace App\Enums;

/**
 * Business Type Enum
 * 
 * Sottotipo specifico per user_type 'company' e 'pa_entity'
 * Definisce la tipologia di business/organizzazione
 * 
 * @package App\Enums
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Business Type Classification)
 * @date 2025-10-09
 */
enum BusinessType: string
{
    case INDIVIDUAL = 'individual';
    case SOLE_PROPRIETORSHIP = 'sole_proprietorship';
    case PARTNERSHIP = 'partnership';
    case CORPORATION = 'corporation';
    case NON_PROFIT = 'non_profit';
    case PA_ENTITY = 'pa_entity';

    /**
     * Get display name for business type
     * 
     * @return string
     */
    public function getDisplayName(): string
    {
        return match ($this) {
            self::INDIVIDUAL => 'Persona Fisica',
            self::SOLE_PROPRIETORSHIP => 'Ditta Individuale',
            self::PARTNERSHIP => 'Società di Persone',
            self::CORPORATION => 'Società di Capitali',
            self::NON_PROFIT => 'Ente Non Profit',
            self::PA_ENTITY => 'Pubblica Amministrazione',
        };
    }

    /**
     * Convert database value to enum instance
     * 
     * @param string $value Database value
     * @return self
     * @throws \ValueError If value is invalid
     */
    public static function fromDatabase(string $value): self
    {
        return match ($value) {
            'individual' => self::INDIVIDUAL,
            'sole_proprietorship' => self::SOLE_PROPRIETORSHIP,
            'partnership' => self::PARTNERSHIP,
            'corporation' => self::CORPORATION,
            'non_profit' => self::NON_PROFIT,
            'pa_entity' => self::PA_ENTITY,
            default => throw new \ValueError("Business type '$value' non valido"),
        };
    }

    /**
     * Get all business types as options array
     * 
     * @return array<string, string>
     */
    public static function getOptions(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->getDisplayName();
        }
        return $options;
    }

    /**
     * Check if this is a public administration type
     * 
     * @return bool
     */
    public function isPublicAdministration(): bool
    {
        return $this === self::PA_ENTITY;
    }

    /**
     * Check if this is a profit-oriented business
     * 
     * @return bool
     */
    public function isProfitOriented(): bool
    {
        return in_array($this, [
            self::INDIVIDUAL,
            self::SOLE_PROPRIETORSHIP,
            self::PARTNERSHIP,
            self::CORPORATION,
        ]);
    }
}
