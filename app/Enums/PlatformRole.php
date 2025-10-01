<?php

namespace App\Enums;

/**
 *
 *
 * @package App\Enums
 */
enum PlatformRole: string
{

    case EPP = 'epp';
    case NATAN = 'natan ';
    case CREATOR = 'creator';
    case COLLECTOR = 'collector';
    case COMMISSIONER = 'commissioner';
    case COMPANY = 'company';
    case TRADER_PRO = 'trader_pro';
    case VIP = 'vip';
    case WEAK = 'weak';
    case PA_ENTITY = 'pa_entity';
    case INSPECTOR = 'inspector';

    /**
     * Converte un valore stringa del database in un'istanza dell'enum.
     *
     * @param string $value Il valore dello stato proveniente dal database.
     * @return self L'istanza dell'enum corrispondente al valore.
     * @throws \ValueError Se il valore non è valido o non mappato.
     */
    public static function fromDatabase(string $value): self
    {
        // Usa il costrutto match per mappare i valori stringa ai casi dell'enum.
        return match($value) {
            'epp' => self::EPP,
            'natan' => self::NATAN,
            'creator' => self::CREATOR,
            'collector' => self::COLLECTOR,
            'commissioner' => self::COMMISSIONER,
            'company' => self::COMPANY,
            'trader_pro' => self::TRADER_PRO,
            'vip' => self::VIP,
            'weak' => self::WEAK,
            'pa_entity' => self::PA_ENTITY,
            'inspector' => self::INSPECTOR,

            default => throw new \ValueError("Platform role '$value' non valido") // Lancia un'eccezione per valori non riconosciuti.
        };
    }
}