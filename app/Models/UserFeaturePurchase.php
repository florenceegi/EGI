<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * UserFeaturePurchase Model
 *
 * Tracks feature purchases by users with payment details and activation status.
 * Links to ai_feature_pricing catalog and auto-grants Spatie permissions.
 *
 * @property int $id
 * @property int $user_id
 * @property string $feature_code
 * @property string|null $granted_permission
 * @property string $payment_method (fiat|crypto|egili|free)
 * @property string|null $payment_provider
 * @property string|null $payment_transaction_id
 * @property float|null $amount_paid_eur
 * @property int|null $amount_paid_egili
 * @property \Carbon\Carbon $purchased_at
 * @property \Carbon\Carbon|null $activated_at
 * @property \Carbon\Carbon|null $expires_at
 * @property bool $is_active
 * @property bool $auto_renew
 * @property int|null $quantity_purchased
 * @property int $quantity_used
 * @property string|null $source_type
 * @property int|null $source_id
 * @property int|null $admin_user_id
 * @property string|null $promo_code
 * @property string|null $admin_notes
 * @property string $status
 * @property string|null $error_message
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property array|null $metadata
 *
 * @package App\Models
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Feature Purchase System)
 * @date 2025-11-01
 */
class UserFeaturePurchase extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $table = 'user_feature_purchases';
    
    protected $fillable = [
        'user_id',
        'feature_code',
        'granted_permission',
        'payment_method',
        'payment_provider',
        'payment_transaction_id',
        'amount_paid_eur',
        'amount_paid_egili',
        'purchased_at',
        'activated_at',
        'expires_at',
        'is_active',
        'auto_renew',
        'quantity_purchased',
        'quantity_used',
        'source_type',
        'source_id',
        'admin_user_id',
        'promo_code',
        'admin_notes',
        'status',
        'error_message',
        'ip_address',
        'user_agent',
        'metadata',
    ];
    
    protected $casts = [
        'purchased_at' => 'datetime',
        'activated_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'auto_renew' => 'boolean',
        'amount_paid_eur' => 'decimal:2',
        'amount_paid_egili' => 'integer',
        'quantity_purchased' => 'integer',
        'quantity_used' => 'integer',
        'metadata' => 'array',
    ];
    
    // === RELATIONSHIPS ===
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }
    
    public function source(): MorphTo
    {
        return $this->morphTo();
    }
    
    // === SCOPES ===
    
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('status', 'active')
            ->where(function($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
    }
    
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }
    
    public function scopeByFeature($query, string $featureCode)
    {
        return $query->where('feature_code', $featureCode);
    }
    
    // === HELPERS ===
    
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
    
    public function hasQuotaRemaining(): bool
    {
        if ($this->quantity_purchased === null) {
            return true; // Unlimited usage
        }
        
        return $this->quantity_used < $this->quantity_purchased;
    }
    
    public function getRemainingQuota(): ?int
    {
        if ($this->quantity_purchased === null) {
            return null; // Unlimited
        }
        
        return max(0, $this->quantity_purchased - $this->quantity_used);
    }
}

