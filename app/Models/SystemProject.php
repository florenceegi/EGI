<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * SystemProject Model
 * 
 * Represents an application/codebase in the EGI ecosystem.
 * Examples: FlorenceEGI (monotenant), NATAN_LOC (multitenant)
 * 
 * Distinct from User "projects" (PA document folders):
 * - SystemProject = Application (FlorenceEGI, NATAN_LOC)
 * - Project = PA user document folder
 * 
 * @package App\Models
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0
 * @date 2026-01-09
 */
class SystemProject extends Model
{
    use HasFactory;

    protected $table = 'system_projects';

    protected $fillable = [
        'code',
        'name',
        'is_multitenant',
        'settings',
        'is_active',
    ];

    protected $casts = [
        'is_multitenant' => 'boolean',
        'is_active' => 'boolean',
        'settings' => 'array',
    ];

    /**
     * Get all tenants belonging to this system project
     * Only applicable for multitenant projects
     */
    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class, 'system_project_id');
    }

    /**
     * Get all users belonging to this system project
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'system_project_id');
    }

    /**
     * Check if this is a multitenant project
     */
    public function isMultitenant(): bool
    {
        return $this->is_multitenant;
    }

    /**
     * Scope: only active projects
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: multitenant projects only
     */
    public function scopeMultitenant($query)
    {
        return $query->where('is_multitenant', true);
    }
}
