<?php

declare(strict_types=1);

namespace App\Models;

use FlorenceEgi\Hub\Traits\HasAggregations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @package App\Models
 * @author Fabio Cherici
 * @version 1.0.0 (FlorenceEGI)
 * @date 2025-11-28
 * @purpose Model per tenant con supporto Aggregazioni P2P
 *
 * Rappresenta un tenant nel sistema FlorenceEGI.
 * Condivide la stessa tabella 'tenants' con NATAN_LOC.
 * 
 * AGGREGAZIONI P2P:
 * I tenant possono formare aggregazioni consensuali per condividere dati.
 * Usa HasAggregations trait per le funzionalità di aggregazione.
 */
class Tenant extends Model
{
    use HasFactory, SoftDeletes, HasAggregations;

    protected $table = 'tenants';

    protected $fillable = [
        'name',
        'slug',
        'code',
        'entity_type',
        'email',
        'phone',
        'address',
        'vat_number',
        'settings',
        'is_active',
        'trial_ends_at',
        'subscription_ends_at',
        'notes',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
    ];

    /**
     * Get users belonging to this tenant
     */
    public function users()
    {
        return $this->hasMany(User::class, 'tenant_id');
    }
}
