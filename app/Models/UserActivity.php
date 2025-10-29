<?php

namespace App\Models;

use App\Enums\Gdpr\GdprActivityCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @Oracode Model: User Activity
 * 🎯 Purpose: Comprehensive user activity audit trail
 * 🛡️ Privacy: Privacy-conscious activity logging with retention
 * 🧱 Core Logic: Categorized activity tracking with automated cleanup
 *
 * @package App\Models
 * @author Padmin D. Curtis (for Fabio Cherici)
 * @version 1.0.0
 * @date 2025-05-22
 */
class UserActivity extends Model {
    use HasFactory;

    /**
     * The attributes that are mass assignable
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'action',
        'category',
        'context',
        'metadata',
        'privacy_level',
        'ip_address',
        'user_agent',
        'session_id',
        'expires_at'
    ];

    /**
     * The attributes that should be cast
     * @var array<string, string>
     */
    protected $casts = [
        'context' => 'array',
        'metadata' => 'array',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'category' => GdprActivityCategory::class,
    ];

    /**
     * Activity categories with retention periods
     * @var array<string, array>
     */
    public static array $categories = [
        'authentication' => ['name' => 'Authentication', 'retention_days' => 365],
        'gdpr_actions' => ['name' => 'GDPR Actions', 'retention_days' => 2555],
        'data_access' => ['name' => 'Data Access', 'retention_days' => 1095],
        'platform_usage' => ['name' => 'Platform Usage', 'retention_days' => 730],
        'security_events' => ['name' => 'Security Events', 'retention_days' => 2555],
        'blockchain_activity' => ['name' => 'Blockchain Activity', 'retention_days' => 2555]
    ];

    /**
     * Privacy levels
     * @var array<string, string>
     */
    public static array $privacyLevels = [
        'standard' => 'Standard Privacy',
        'high' => 'High Privacy',
        'critical' => 'Critical Privacy',
        'immutable' => 'Immutable Record'
    ];

    /**
     * Get the user that owns the activity
     * @return BelongsTo
     * @privacy-safe Returns owning user relationship
     */
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for specific category
     * @param $query
     * @param string $category
     * @return mixed
     * @privacy-safe Filters by activity category
     */
    public function scopeCategory($query, string $category) {
        return $query->where('category', $category);
    }

    /**
     * Scope for specific privacy level
     * @param $query
     * @param string $level
     * @return mixed
     * @privacy-safe Filters by privacy level
     */
    public function scopePrivacyLevel($query, string $level) {
        return $query->where('privacy_level', $level);
    }

    /**
     * Scope for expired activities
     * @param $query
     * @return mixed
     * @privacy-safe Filters for expired activities
     */
    public function scopeExpired($query) {
        return $query->where('expires_at', '<', now());
    }

    /**
     * Scope for recent activities
     * @param $query
     * @param int $days
     * @return mixed
     * @privacy-safe Filters for recent activities
     */
    public function scopeRecent($query, int $days = 30) {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Get category display name
     * @return string
     * @privacy-safe Returns friendly category name
     */
    public function getCategoryNameAttribute(): string {
        return self::$categories[$this->category]['name'] ?? 'Unknown Category';
    }

    /**
     * Get privacy level display name
     * @return string
     * @privacy-safe Returns friendly privacy level name
     */
    public function getPrivacyLevelNameAttribute(): string {
        return self::$privacyLevels[$this->privacy_level] ?? 'Unknown Privacy Level';
    }

    /**
     * Check if activity is expired
     * @return bool
     * @privacy-safe Checks expiration status
     */
    public function isExpired(): bool {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Get CSS class based on privacy/risk level
     * @return string
     */
    public function getRiskLevelClass(): string {
        return match ($this->privacy_level) {
            'critical' => 'risk-critical',
            'high' => 'risk-high',
            'immutable' => 'risk-immutable',
            default => 'risk-standard'
        };
    }

    /**
     * Get icon CSS class based on category
     * @return string
     */
    public function getIconClass(): string {
        $categoryValue = $this->category instanceof \BackedEnum
            ? $this->category->value
            : $this->category;

        return match ($categoryValue) {
            'authentication', 'authentication_login' => 'icon-auth',
            'gdpr_actions', 'data_access' => 'icon-gdpr',
            'security_events' => 'icon-security',
            'blockchain_activity' => 'icon-blockchain',
            'admin_access', 'admin_action' => 'icon-admin',
            default => 'icon-default'
        };
    }

    /**
     * Get SVG icon based on category
     * @return string
     */
    public function getIconSvg(): string {
        $categoryValue = $this->category instanceof \BackedEnum
            ? $this->category->value
            : $this->category;

        return match ($categoryValue) {
            'authentication', 'authentication_login' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>',
            'gdpr_actions' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>',
            'security_events' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>',
            'blockchain_activity' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>',
            'admin_access', 'admin_action' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>',
            default => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
        };
    }

    /**
     * Get formatted device info
     * @return string
     */
    public function getFormattedDeviceInfo(): string {
        if (empty($this->user_agent)) {
            return __('gdpr.activity_log.unknown_device');
        }

        // Basic user agent parsing
        $userAgent = $this->user_agent;

        if (str_contains($userAgent, 'Mobile')) {
            return '📱 ' . __('gdpr.activity_log.mobile_device');
        } elseif (str_contains($userAgent, 'Tablet')) {
            return '📱 ' . __('gdpr.activity_log.tablet_device');
        } else {
            return '💻 ' . __('gdpr.activity_log.desktop_device');
        }
    }

    /**
     * Get action_type attribute (alias for action)
     * @return string
     */
    public function getActionTypeAttribute(): string {
        return $this->action;
    }

    /**
     * Get description attribute (from metadata or context)
     * @return string|null
     */
    public function getDescriptionAttribute(): ?string {
        return $this->metadata['description'] ?? $this->context['description'] ?? null;
    }

    /**
     * Get location attribute (from context or metadata)
     * @return string|null
     */
    public function getLocationAttribute(): ?string {
        return $this->context['location'] ?? $this->metadata['location'] ?? null;
    }

    /**
     * Get device_info attribute (alias for user_agent)
     * @return string|null
     */
    public function getDeviceInfoAttribute(): ?string {
        return $this->user_agent;
    }

    /**
     * Get risk_level attribute (derived from privacy_level)
     * @return string
     */
    public function getRiskLevelAttribute(): string {
        return match ($this->privacy_level) {
            'critical' => 'critical',
            'high' => 'high',
            'immutable' => 'immutable',
            default => 'standard'
        };
    }

    /**
     * Check if activity is sensitive
     * @return bool
     */
    public function getIsSensitiveAttribute(): bool {
        return in_array($this->privacy_level, ['high', 'critical', 'immutable']);
    }

    /**
     * Check if activity requires attention
     * @return bool
     */
    public function getRequiresAttentionAttribute(): bool {
        return $this->privacy_level === 'critical' ||
            ($this->context['requires_attention'] ?? false);
    }

    /**
     * Get context_data attribute (alias for context)
     * @return array|null
     */
    public function getContextDataAttribute(): ?array {
        return $this->context;
    }
}
