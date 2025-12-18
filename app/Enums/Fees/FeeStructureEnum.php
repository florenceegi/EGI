<?php

namespace App\Enums\Fees;

enum FeeStructureEnum: string
{
    case CONTRIBUTOR_MINT = 'contributor_mint';
    case CONTRIBUTOR_REBIND = 'contributor_rebind';
    case NORMAL_MINT = 'normal_mint';
    case NORMAL_REBIND = 'normal_rebind';

    public function getDistribution(): array
    {
        return match($this) {
            self::CONTRIBUTOR_MINT => [
                'Natan' => 10.0,
                'Frangette' => 2.0,
                'EPP' => 20.0,
                'Creator' => 68.0, // Base remainder
            ],
            self::CONTRIBUTOR_REBIND => [
                'Natan' => 0.8,
                'Frangette' => 0.2,
                'EPP' => 1.5,
                'Creator' => 4.5, // Fixed fee
            ],
            self::NORMAL_MINT => [
                'Natan' => 10.0,
                'Frangette' => 0.0,
                'EPP' => 0.0,
                'Creator' => 90.0, // Base remainder
            ],
            self::NORMAL_REBIND => [
                'Natan' => 1.0,
                'Frangette' => 0.0,
                'EPP' => 0.0,
                'Creator' => 6.0, // Fixed fee
            ],
        };
    }

    /**
     * Get structure for specific profile and operation
     */
    public static function fromProfile(string $profileType, string $operation): self
    {
        $profile = strtolower($profileType);
        $op = strtolower($operation);

        return match(true) {
            $profile === 'contributor' && $op === 'mint' => self::CONTRIBUTOR_MINT,
            $profile === 'contributor' && $op === 'rebind' => self::CONTRIBUTOR_REBIND,
            $profile === 'normal' && $op === 'mint' => self::NORMAL_MINT,
            $profile === 'normal' && $op === 'rebind' => self::NORMAL_REBIND,
            default => self::CONTRIBUTOR_MINT, // Safe fallback
        };
    }
}
