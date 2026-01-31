<?php

namespace App\Enums\Commerce;

enum CommercialStatusEnum: string
{
    case DRAFT = 'draft';
    case CONFIGURED = 'configured';
    case COMMERCIAL_ENABLED = 'commercial_enabled';

    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Draft',
            self::CONFIGURED => 'Configured',
            self::COMMERCIAL_ENABLED => 'Commercial Enabled',
        };
    }
}
