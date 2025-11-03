<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @package App\Models
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI)
 * @date 2025-11-03
 * @purpose Modello per configurazione backup automatici
 * 
 * CONTESTO: Sistema backup automatico con cron job
 * PERCORSO FILE: app/Models/AutomaticBackupConfig.php
 */
class AutomaticBackupConfig extends Model
{
    protected $table = 'automatic_backups_config';
    
    protected $fillable = [
        'is_enabled',
        'interval_type',
        'interval_value',
        'start_time',
        'retention_days',
        'max_backups',
        'last_backup_at',
        'next_backup_at',
        'last_error',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'interval_value' => 'integer',
        'retention_days' => 'integer',
        'max_backups' => 'integer',
        'last_backup_at' => 'datetime',
        'next_backup_at' => 'datetime',
    ];

    /**
     * Get singleton instance
     */
    public static function getConfig(): self
    {
        return static::firstOrCreate([], [
            'is_enabled' => false,
            'interval_type' => 'hours',
            'interval_value' => 24,
            'start_time' => '02:00',
            'retention_days' => 30,
            'max_backups' => 10,
        ]);
    }
}
