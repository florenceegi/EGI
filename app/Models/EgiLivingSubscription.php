<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\EgiLivingStatus;

/**
 * @package App\Models
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Dual Architecture)
 * @date 2025-10-19
 * @purpose Model for EGI Vivente (Living) subscriptions
 *
 * @property int $id
 * @property int $egi_id
 * @property int $user_id
 * @property string $status Subscription status (pending_payment|active|suspended|cancelled|expired)
 * @property string $plan_type Plan type (one_time|monthly|yearly|lifetime)
 * @property float $paid_amount
 * @property string $paid_currency
 * @property string $payment_method
 * @property string|null $payment_reference
 * @property \Carbon\Carbon|null $paid_at
 * @property \Carbon\Carbon|null $activated_at
 * @property \Carbon\Carbon|null $expires_at
 * @property \Carbon\Carbon|null $cancelled_at
 * @property array|null $enabled_features
 * @property int $ai_analysis_interval
 * @property int $ai_executions_count
 * @property \Carbon\Carbon|null $last_ai_execution_at
 * @property string|null $cancellation_reason
 * @property array|null $subscription_metadata
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @property-read Egi $egi The EGI this subscription applies to
 * @property-read User $user The user who owns this subscription
 */
class EgiLivingSubscription extends Model
{
    use HasFactory;

    protected $table = 'egi_living_subscriptions';

    protected $fillable = [
        'egi_id',
        'user_id',
        'status',
        'plan_type',
        'paid_amount',
        'paid_currency',
        'payment_method',
        'payment_reference',
        'paid_at',
        'activated_at',
        'expires_at',
        'cancelled_at',
        'enabled_features',
        'ai_analysis_interval',
        'ai_executions_count',
        'last_ai_execution_at',
        'cancellation_reason',
        'subscription_metadata',
    ];

    protected $casts = [
        'paid_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'activated_at' => 'datetime',
        'expires_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'last_ai_execution_at' => 'datetime',
        'ai_analysis_interval' => 'integer',
        'ai_executions_count' => 'integer',
        'enabled_features' => 'array',
        'subscription_metadata' => 'array',
    ];

    /**
     * Get the EGI this subscription applies to
     */
    public function egi(): BelongsTo
    {
        return $this->belongsTo(Egi::class);
    }

    /**
     * Get the user who owns this subscription
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if subscription is active
     */
    public function isActive(): bool
    {
        return $this->status === EgiLivingStatus::ACTIVE->value;
    }

    /**
     * Check if subscription is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if subscription is lifetime
     */
    public function isLifetime(): bool
    {
        return $this->plan_type === 'lifetime' || $this->expires_at === null;
    }

    /**
     * Check if specific AI feature is enabled
     */
    public function hasFeature(string $feature): bool
    {
        return in_array($feature, $this->enabled_features ?? []);
    }

    /**
     * Get days remaining until expiration
     */
    public function getDaysRemaining(): ?int
    {
        if (!$this->expires_at) {
            return null; // Lifetime
        }

        return max(0, now()->diffInDays($this->expires_at, false));
    }

    /**
     * Activate subscription
     */
    public function activate(): void
    {
        $this->update([
            'status' => EgiLivingStatus::ACTIVE->value,
            'activated_at' => now(),
        ]);
    }

    /**
     * Cancel subscription
     */
    public function cancel(string $reason = null): void
    {
        $this->update([
            'status' => EgiLivingStatus::CANCELLED->value,
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
        ]);
    }

    /**
     * Suspend subscription
     */
    public function suspend(): void
    {
        $this->update([
            'status' => EgiLivingStatus::SUSPENDED->value,
        ]);
    }

    /**
     * Scope to get active subscriptions
     */
    public function scopeActive($query)
    {
        return $query->where('status', EgiLivingStatus::ACTIVE->value);
    }

    /**
     * Scope to get expired subscriptions
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now())
            ->whereNotNull('expires_at');
    }

    /**
     * Scope to get subscriptions expiring soon
     */
    public function scopeExpiringSoon($query, int $days = 7)
    {
        return $query->where('expires_at', '<=', now()->addDays($days))
            ->where('expires_at', '>', now())
            ->where('status', EgiLivingStatus::ACTIVE->value);
    }
}

