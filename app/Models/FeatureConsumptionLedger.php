<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @package App\Models
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Feature Consumption Ledger)
 * @date 2025-11-03
 * @purpose Track granular feature consumption (token-based, unit-based, time-based) with fractional costs
 * 
 * Relationships:
 * - belongsTo: User (consumer)
 * - belongsTo: EgiliTransaction (batch charge transaction)
 * 
 * Scopes:
 * - pending(): Only pending charges (not yet batched)
 * - batched(): Batched but not charged
 * - charged(): Already charged to user
 * - forUser(User): Filter by user
 * - forFeature(string): Filter by feature_code
 * - tokenBased(): Only token-based consumption
 */
class FeatureConsumptionLedger extends Model
{
    protected $table = 'feature_consumption_ledger';
    
    protected $fillable = [
        'user_id',
        'feature_code',
        'consumption_type',
        'units_consumed',
        'unit_type',
        'cost_per_unit',
        'total_cost_egili',
        'billing_status',
        'batched_in_transaction_id',
        'charged_at',
        'request_metadata',
        'ip_address',
        'user_agent',
        'consumed_at',
    ];
    
    protected $casts = [
        'units_consumed' => 'decimal:4',
        'cost_per_unit' => 'decimal:6',
        'total_cost_egili' => 'decimal:4',
        'request_metadata' => 'array',
        'consumed_at' => 'datetime',
        'charged_at' => 'datetime',
    ];
    
    /**
     * User who consumed the feature
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Egili transaction when batch charged (nullable)
     */
    public function batchTransaction(): BelongsTo
    {
        return $this->belongsTo(EgiliTransaction::class, 'batched_in_transaction_id');
    }
    
    /**
     * Scope: Only pending charges
     */
    public function scopePending($query)
    {
        return $query->where('billing_status', 'pending');
    }
    
    /**
     * Scope: Only batched (included in transaction but not charged)
     */
    public function scopeBatched($query)
    {
        return $query->where('billing_status', 'batched');
    }
    
    /**
     * Scope: Already charged
     */
    public function scopeCharged($query)
    {
        return $query->where('billing_status', 'charged');
    }
    
    /**
     * Scope: Filter by user
     */
    public function scopeForUser($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }
    
    /**
     * Scope: Filter by feature code
     */
    public function scopeForFeature($query, string $featureCode)
    {
        return $query->where('feature_code', $featureCode);
    }
    
    /**
     * Scope: Only token-based consumption
     */
    public function scopeTokenBased($query)
    {
        return $query->where('consumption_type', 'token_based');
    }
    
    /**
     * Get pending debt total for user
     * 
     * @param User $user
     * @return float Total pending Egili debt (fractional)
     */
    public static function getPendingDebt(User $user): float
    {
        return (float) self::where('user_id', $user->id)
            ->where('billing_status', 'pending')
            ->sum('total_cost_egili');
    }
    
    /**
     * Get pending debt breakdown by feature for user
     * 
     * @param User $user
     * @return array Feature code => pending debt
     */
    public static function getPendingDebtByFeature(User $user): array
    {
        return self::where('user_id', $user->id)
            ->where('billing_status', 'pending')
            ->selectRaw('feature_code, SUM(total_cost_egili) as debt')
            ->groupBy('feature_code')
            ->pluck('debt', 'feature_code')
            ->toArray();
    }
}

















