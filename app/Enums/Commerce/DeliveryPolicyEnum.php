<?php

namespace App\Enums\Commerce;

enum DeliveryPolicyEnum: string
{
    case DIGITAL_ONLY = 'DIGITAL_ONLY';
    case PHYSICAL_ALLOWED = 'PHYSICAL_ALLOWED';
    case PHYSICAL_REQUIRED = 'PHYSICAL_REQUIRED';

    public function label(): string
    {
        return match($this) {
            self::DIGITAL_ONLY => 'Digital Only',
            self::PHYSICAL_ALLOWED => 'Physical Allowed',
            self::PHYSICAL_REQUIRED => 'Physical Required',
        };
    }
}
