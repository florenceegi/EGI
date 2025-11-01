<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * EgiliTransaction Model
 *
 * Rappresenta una transazione Egili (token utility piattaforma):
 * - Earn/Spend operations
 * - Complete audit trail
 * - Balance snapshots before/after
 * - Polymorphic source tracking
 * - GDPR-compliant logging
 *
 * @property int $id
 * @property int $wallet_id
 * @property int $user_id
 * @property string $transaction_type (earned|spent|admin_grant|admin_deduct|purchase|refund|expiration|initial_bonus)
 * @property string $operation (add|subtract)
 * @property int $amount
 * @property int $balance_before
 * @property int $balance_after
 * @property string|null $source_type
 * @property int|null $source_id
 * @property string $reason
 * @property string|null $category
 * @property array|null $metadata
 * @property int|null $admin_user_id
 * @property string|null $admin_notes
 * @property string $status (completed|pending|failed|reversed)
 * @property string|null $error_message
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @property-read Wallet $wallet
 * @property-read User $user
 * @property-read Model|null $source
 * @property-read User|null $admin
 *
 * @package FlorenceEGI\Models
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Egili Token System)
 * @date 2025-11-01
 * @purpose Egili transaction tracking with GDPR compliance
 */
class EgiliTransaction extends Model
{
    /**
     * The table associated with the model
     */
    protected $table = 'egili_transactions';
    
    /**
     * The attributes that are mass assignable
     */
    protected $fillable = [
        'wallet_id',
        'user_id',
        'transaction_type',
        'operation',
        'amount',
        'balance_before',
        'balance_after',
        'source_type',
        'source_id',
        'reason',
        'category',
        'metadata',
        'admin_user_id',
        'admin_notes',
        'status',
        'error_message',
        'ip_address',
        'user_agent',
    ];
    
    /**
     * The attributes that should be cast
     */
    protected $casts = [
        'amount' => 'integer',
        'balance_before' => 'integer',
        'balance_after' => 'integer',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    // === RELATIONSHIPS ===
    
    /**
     * Get the wallet that owns this transaction
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }
    
    /**
     * Get the user that owns this transaction
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the source entity (polymorphic)
     * Can be: Egi, Reservation, EgiLivingSubscription, etc.
     */
    public function source(): MorphTo
    {
        return $this->morphTo();
    }
    
    /**
     * Get the admin user (if manual operation)
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }
    
    // === QUERY SCOPES ===
    
    /**
     * Scope: Earned transactions
     */
    public function scopeEarned($query)
    {
        return $query->where('operation', 'add');
    }
    
    /**
     * Scope: Spent transactions
     */
    public function scopeSpent($query)
    {
        return $query->where('operation', 'subtract');
    }
    
    /**
     * Scope: Completed transactions
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
    
    /**
     * Scope: By category
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }
    
    /**
     * Scope: Recent transactions
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
    
    // === ACCESSORS ===
    
    /**
     * Get formatted amount with sign
     */
    public function getSignedAmountAttribute(): string
    {
        $sign = $this->operation === 'add' ? '+' : '-';
        return "{$sign}{$this->amount}";
    }
    
    /**
     * Get human-readable transaction type
     */
    public function getTypeDescriptionAttribute(): string
    {
        return match($this->transaction_type) {
            'earned' => __('egili.transaction_types.earned'),
            'spent' => __('egili.transaction_types.spent'),
            'admin_grant' => __('egili.transaction_types.admin_grant'),
            'admin_deduct' => __('egili.transaction_types.admin_deduct'),
            'purchase' => __('egili.transaction_types.purchase'),
            'refund' => __('egili.transaction_types.refund'),
            'expiration' => __('egili.transaction_types.expiration'),
            'initial_bonus' => __('egili.transaction_types.initial_bonus'),
            default => $this->transaction_type,
        };
    }
    
    /**
     * Check if transaction is reversible
     */
    public function isReversible(): bool
    {
        return $this->status === 'completed' 
            && in_array($this->transaction_type, ['earned', 'spent', 'purchase']);
    }
}

