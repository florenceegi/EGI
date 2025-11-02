<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

/**
 * FeaturePromotion Model
 * 
 * Manages promotional discounts for features:
 * - Temporal promotions (Black Friday, seasonal sales)
 * - Feature-specific or global promotions
 * - Usage limits and tracking
 * - Stats for conversion analysis
 * 
 * @property int $id
 * @property string $promo_code
 * @property string $promo_name
 * @property string|null $promo_description
 * @property bool $is_global
 * @property string|null $feature_code
 * @property string|null $feature_category
 * @property string $discount_type (percentage|fixed_amount)
 * @property float $discount_value
 * @property Carbon $start_at
 * @property Carbon $end_at
 * @property int|null $max_uses
 * @property int|null $max_uses_per_user
 * @property int $current_uses
 * @property bool $is_active
 * @property bool $is_featured
 * @property string|null $badge_text
 * @property int $created_by_admin_id
 * @property string|null $admin_notes
 * @property int $total_egili_saved
 * @property int $total_purchases_with_promo
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property-read User $createdByAdmin
 * 
 * @package App\Models
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Egili System Pricing)
 * @date 2025-11-02
 * @purpose Feature promotion management with usage tracking
 */
class FeaturePromotion extends Model
{
    /**
     * The table associated with the model
     */
    protected $table = 'feature_promotions';
    
    /**
     * The attributes that are mass assignable
     */
    protected $fillable = [
        'promo_code',
        'promo_name',
        'promo_description',
        'is_global',
        'feature_code',
        'feature_category',
        'discount_type',
        'discount_value',
        'start_at',
        'end_at',
        'max_uses',
        'max_uses_per_user',
        'current_uses',
        'is_active',
        'is_featured',
        'badge_text',
        'created_by_admin_id',
        'admin_notes',
        'total_egili_saved',
        'total_purchases_with_promo',
    ];
    
    /**
     * The attributes that should be cast
     */
    protected $casts = [
        'is_global' => 'boolean',
        'discount_value' => 'decimal:2',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'max_uses' => 'integer',
        'max_uses_per_user' => 'integer',
        'current_uses' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'total_egili_saved' => 'integer',
        'total_purchases_with_promo' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    // === RELATIONSHIPS ===
    
    /**
     * Get the admin who created this promotion
     */
    public function createdByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_admin_id');
    }
    
    // === QUERY SCOPES ===
    
    /**
     * Scope: Active promotions
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    /**
     * Scope: Currently valid (active + within date range)
     */
    public function scopeValid($query)
    {
        $now = now();
        return $query->where('is_active', true)
                     ->where('start_at', '<=', $now)
                     ->where('end_at', '>=', $now);
    }
    
    /**
     * Scope: Featured promotions
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
    
    /**
     * Scope: Global promotions
     */
    public function scopeGlobal($query)
    {
        return $query->where('is_global', true);
    }
    
    /**
     * Scope: For specific feature
     */
    public function scopeForFeature($query, string $featureCode)
    {
        return $query->where(function($q) use ($featureCode) {
            $q->where('is_global', true)
              ->orWhere('feature_code', $featureCode);
        });
    }
    
    /**
     * Scope: Not exhausted (still has uses available)
     */
    public function scopeNotExhausted($query)
    {
        return $query->where(function($q) {
            $q->whereNull('max_uses')
              ->orWhereRaw('current_uses < max_uses');
        });
    }
    
    /**
     * Scope: Upcoming (not yet started)
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_at', '>', now());
    }
    
    /**
     * Scope: Expired (past end date)
     */
    public function scopeExpired($query)
    {
        return $query->where('end_at', '<', now());
    }
    
    // === HELPER METHODS ===
    
    /**
     * Check if promotion is currently valid
     * 
     * @return bool
     */
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }
        
        $now = now();
        if ($now < $this->start_at || $now > $this->end_at) {
            return false;
        }
        
        // Check if exhausted
        if ($this->max_uses !== null && $this->current_uses >= $this->max_uses) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Check if user can use this promo
     * 
     * @param User $user
     * @return bool
     */
    public function canBeUsedBy(User $user): bool
    {
        if (!$this->isValid()) {
            return false;
        }
        
        // Check per-user limit
        if ($this->max_uses_per_user !== null) {
            $userUses = UserFeaturePurchase::where('user_id', $user->id)
                ->where('promo_code', $this->promo_code)
                ->count();
            
            if ($userUses >= $this->max_uses_per_user) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Calculate discount amount for a base price
     * 
     * @param int $basePrice Base price in Egili
     * @return int Discount amount in Egili
     */
    public function calculateDiscount(int $basePrice): int
    {
        if ($this->discount_type === 'percentage') {
            return (int) round($basePrice * ($this->discount_value / 100));
        } else {
            // Fixed amount
            return min((int) $this->discount_value, $basePrice); // Don't discount more than price
        }
    }
    
    /**
     * Calculate final price after discount
     * 
     * @param int $basePrice Base price in Egili
     * @return int Final price in Egili
     */
    public function applyDiscount(int $basePrice): int
    {
        $discount = $this->calculateDiscount($basePrice);
        return max(0, $basePrice - $discount);
    }
    
    /**
     * Record a use of this promotion
     * 
     * @param int $egiliSaved Egili saved by user
     * @return void
     */
    public function recordUse(int $egiliSaved): void
    {
        $this->increment('current_uses');
        $this->increment('total_purchases_with_promo');
        $this->increment('total_egili_saved', $egiliSaved);
    }
    
    /**
     * Get discount display string
     * 
     * @return string Human-readable discount (e.g., "-50%", "-1000 Egili")
     */
    public function getDiscountDisplayAttribute(): string
    {
        if ($this->discount_type === 'percentage') {
            return "-{$this->discount_value}%";
        } else {
            return "-{$this->discount_value} Egili";
        }
    }
    
    /**
     * Get remaining uses
     * 
     * @return int|null Remaining uses (NULL if unlimited)
     */
    public function getRemainingUsesAttribute(): ?int
    {
        if ($this->max_uses === null) {
            return null;
        }
        
        return max(0, $this->max_uses - $this->current_uses);
    }
    
    /**
     * Get days remaining
     * 
     * @return int Days until expiration
     */
    public function getDaysRemainingAttribute(): int
    {
        return max(0, now()->diffInDays($this->end_at, false));
    }
    
    /**
     * Check if promo is expiring soon (within 3 days)
     * 
     * @return bool
     */
    public function isExpiringSoon(): bool
    {
        return $this->days_remaining <= 3 && $this->days_remaining > 0;
    }
}
