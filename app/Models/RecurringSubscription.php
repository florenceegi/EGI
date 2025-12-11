<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class RecurringSubscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'subscribable_type',
        'subscribable_id',
        'service_type',
        'status',
        'next_renewal_at',
        'last_renewal_at',
        'renewal_count',
        'failed_attempts',
        'last_failed_at',
        'metadata',
    ];

    protected $casts = [
        'next_renewal_at' => 'datetime',
        'last_renewal_at' => 'datetime',
        'last_failed_at' => 'datetime',
        'metadata' => 'array',
        'renewal_count' => 'integer',
        'failed_attempts' => 'integer',
    ];

    /**
     * User owning the subscription
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The subscribed entity (Collection, etc.)
     */
    public function subscribable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope for active subscriptions due for renewal
     */
    public function scopeDueForRenewal($query)
    {
        return $query->where('status', 'active')
                     ->where('next_renewal_at', '<=', now());
    }

    /**
     * Scope for finding an active subscription for a specific entity
     */
    public function scopeForEntity($query, Model $entity, string $serviceType)
    {
        return $query->where('subscribable_type', $entity->getMorphClass())
                     ->where('subscribable_id', $entity->id)
                     ->where('service_type', $serviceType)
                     ->where('status', 'active');
    }
}
