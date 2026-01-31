<?php

namespace App\Enums\Commerce;

enum ImpactModeEnum: string
{
    case EPP = 'EPP';
    case SUBSCRIPTION = 'SUBSCRIPTION';

    public function label(): string
    {
        return match($this) {
            self::EPP => 'EPP Donation',
            self::SUBSCRIPTION => 'Subscription Plan',
        };
    }
}
