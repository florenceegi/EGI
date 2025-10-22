<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * AiFeaturePricing Model
 *
 * Gestisce prezzi dinamici di tutte le features AI e premium della piattaforma.
 * Supporta pricing FIAT, Egili, tier differenziati, bundle, e limiti mensili.
 *
 * @package FlorenceEGI\Models
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0
 * @date 2025-10-22
 * @source docs/ai/TOKENOMICS_EGILI_EQUILIBRIUM.md lines 454-531
 */
class AiFeaturePricing extends Model
{
    use SoftDeletes;

    protected $table = 'ai_feature_pricing';

    protected $fillable = [
        'feature_code',
        'feature_name',
        'feature_description',
        'feature_category',
        'cost_fiat_eur',
        'cost_egili',
        'is_free',
        'free_monthly_limit',
        'tier_pricing',
        'min_tier_required',
        'is_bundle',
        'bundle_features',
        'discount_percentage',
        'bundle_type',
        'is_recurring',
        'recurrence_period',
        'duration_hours',
        'expires',
        'max_uses_per_purchase',
        'monthly_quota',
        'stackable',
        'feature_parameters',
        'benefits',
        'expected_roi_multiplier',
        'is_active',
        'available_from',
        'available_until',
        'is_beta',
        'requires_approval',
        'display_order',
        'is_featured',
        'icon_name',
        'badge_color',
        'total_purchases',
        'total_egili_spent',
        'total_fiat_revenue',
        'last_purchased_at',
        'metadata',
        'admin_notes',
    ];

    protected $casts = [
        'cost_fiat_eur' => 'decimal:2',
        'cost_egili' => 'integer',
        'is_free' => 'boolean',
        'free_monthly_limit' => 'integer',
        'tier_pricing' => 'array',
        'is_bundle' => 'boolean',
        'bundle_features' => 'array',
        'discount_percentage' => 'integer',
        'is_recurring' => 'boolean',
        'duration_hours' => 'integer',
        'expires' => 'boolean',
        'max_uses_per_purchase' => 'integer',
        'monthly_quota' => 'integer',
        'stackable' => 'boolean',
        'feature_parameters' => 'array',
        'benefits' => 'array',
        'expected_roi_multiplier' => 'decimal:2',
        'is_active' => 'boolean',
        'available_from' => 'datetime',
        'available_until' => 'datetime',
        'is_beta' => 'boolean',
        'requires_approval' => 'boolean',
        'display_order' => 'integer',
        'is_featured' => 'boolean',
        'total_purchases' => 'integer',
        'total_egili_spent' => 'integer',
        'total_fiat_revenue' => 'decimal:2',
        'last_purchased_at' => 'datetime',
        'metadata' => 'array',
    ];

    // === SCOPES ===

    /**
     * Scope: solo features attive
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: features per categoria
     */
    public function scopeCategory($query, string $category)
    {
        return $query->where('feature_category', $category);
    }

    /**
     * Scope: features accessibili per tier
     */
    public function scopeForTier($query, string $tier)
    {
        $tierOrder = ['free' => 0, 'starter' => 1, 'pro' => 2, 'business' => 3, 'enterprise' => 4];
        $userTierLevel = $tierOrder[$tier] ?? 0;

        return $query->where(function ($q) use ($tierOrder, $userTierLevel) {
            foreach ($tierOrder as $tierName => $level) {
                if ($level <= $userTierLevel) {
                    $q->orWhere('min_tier_required', $tierName);
                }
            }
        });
    }

    /**
     * Scope: solo bundles
     */
    public function scopeBundles($query)
    {
        return $query->where('is_bundle', true);
    }

    /**
     * Scope: features in evidenza
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    // === METHODS ===

    /**
     * Get prezzo per tier specifico
     */
    public function getPriceForTier(string $tier): ?int
    {
        if ($this->is_free) {
            return 0;
        }

        if ($this->tier_pricing && isset($this->tier_pricing[$tier])) {
            return $this->tier_pricing[$tier];
        }

        return $this->cost_egili;
    }

    /**
     * Check se feature è disponibile ora
     */
    public function isAvailable(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();

        if ($this->available_from && $now->isBefore($this->available_from)) {
            return false;
        }

        if ($this->available_until && $now->isAfter($this->available_until)) {
            return false;
        }

        return true;
    }

    /**
     * Incrementa contatori dopo acquisto
     */
    public function recordPurchase(int $egiliSpent = 0, float $fiatSpent = 0): void
    {
        $this->increment('total_purchases');

        if ($egiliSpent > 0) {
            $this->increment('total_egili_spent', $egiliSpent);
        }

        if ($fiatSpent > 0) {
            $this->increment('total_fiat_revenue', $fiatSpent);
        }

        $this->update(['last_purchased_at' => now()]);
    }

    /**
     * Get descrizione completa con benefici
     */
    public function getFullDescription(): string
    {
        $description = $this->feature_description ?? '';

        if ($this->benefits && count($this->benefits) > 0) {
            $description .= "\n\nInclude:\n";
            foreach ($this->benefits as $benefit) {
                $description .= "- {$benefit}\n";
            }
        }

        return trim($description);
    }
}
