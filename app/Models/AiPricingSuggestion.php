<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * AiPricingSuggestion Model
 *
 * Rappresenta un suggerimento pricing AI per un EGI:
 * - Price recommendations (min/optimal/max)
 * - Market analysis
 * - Revenue projections
 * - Strategy rationale
 *
 * @package FlorenceEGI\Models
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0
 * @date 2025-10-22
 */
class AiPricingSuggestion extends Model
{
    use SoftDeletes;

    protected $table = 'ai_pricing_suggestions';

    protected $fillable = [
        'analysis_id',
        'egi_id',
        'user_id',
        'strategy_type',
        'strategy_rationale',
        'suggested_price_min',
        'suggested_price_optimal',
        'suggested_price_max',
        'current_price',
        'price_adjustment_percentage',
        'market_average_price',
        'competitor_price_low',
        'competitor_price_high',
        'market_position',
        'estimated_revenue_low',
        'estimated_revenue_optimal',
        'estimated_revenue_high',
        'estimated_sales_volume',
        'estimated_time_to_sell_days',
        'confidence_score',
        'confidence_factors',
        'validation_notes',
        'seasonal_factors',
        'optimal_listing_date',
        'timing_recommendations',
        'user_decision',
        'user_final_price',
        'user_notes',
        'decided_at',
        'was_implemented',
        'implemented_at',
        'actual_sale_price',
        'actual_days_to_sell',
        'was_accurate',
        'ai_metadata',
    ];

    protected $casts = [
        'suggested_price_min' => 'decimal:2',
        'suggested_price_optimal' => 'decimal:2',
        'suggested_price_max' => 'decimal:2',
        'current_price' => 'decimal:2',
        'price_adjustment_percentage' => 'decimal:2',
        'market_average_price' => 'decimal:2',
        'competitor_price_low' => 'decimal:2',
        'competitor_price_high' => 'decimal:2',
        'estimated_revenue_low' => 'decimal:2',
        'estimated_revenue_optimal' => 'decimal:2',
        'estimated_revenue_high' => 'decimal:2',
        'estimated_sales_volume' => 'integer',
        'estimated_time_to_sell_days' => 'integer',
        'confidence_score' => 'integer',
        'confidence_factors' => 'array',
        'seasonal_factors' => 'array',
        'optimal_listing_date' => 'date',
        'market_position' => 'array',
        'decided_at' => 'datetime',
        'was_implemented' => 'boolean',
        'implemented_at' => 'datetime',
        'actual_sale_price' => 'decimal:2',
        'actual_days_to_sell' => 'integer',
        'was_accurate' => 'boolean',
        'ai_metadata' => 'array',
    ];

    // === RELATIONSHIPS ===

    public function analysis(): BelongsTo
    {
        return $this->belongsTo(AiEgiAnalysis::class, 'analysis_id');
    }

    public function egi(): BelongsTo
    {
        return $this->belongsTo(Egi::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // === STATUS CHECKERS ===

    public function isPending(): bool
    {
        return $this->user_decision === 'pending';
    }

    public function isAccepted(): bool
    {
        return $this->user_decision === 'accepted';
    }

    public function isRejected(): bool
    {
        return $this->user_decision === 'rejected';
    }

    public function isModified(): bool
    {
        return $this->user_decision === 'modified';
    }

    public function isIgnored(): bool
    {
        return $this->user_decision === 'ignored';
    }

    public function isImplemented(): bool
    {
        return $this->was_implemented;
    }

    // === STRATEGY CHECKERS ===

    public function isCompetitiveStrategy(): bool
    {
        return $this->strategy_type === 'competitive';
    }

    public function isValueBasedStrategy(): bool
    {
        return $this->strategy_type === 'value_based';
    }

    public function isPremiumStrategy(): bool
    {
        return $this->strategy_type === 'premium';
    }

    public function isPenetrationStrategy(): bool
    {
        return $this->strategy_type === 'penetration';
    }

    public function isDynamicStrategy(): bool
    {
        return $this->strategy_type === 'dynamic';
    }

    public function isPsychologicalStrategy(): bool
    {
        return $this->strategy_type === 'psychological';
    }

    // === STATE MODIFIERS ===

    public function accept(float $finalPrice, ?string $notes = null): void
    {
        $this->update([
            'user_decision' => 'accepted',
            'user_final_price' => $finalPrice,
            'user_notes' => $notes,
            'decided_at' => now(),
        ]);
    }

    public function reject(?string $notes = null): void
    {
        $this->update([
            'user_decision' => 'rejected',
            'user_notes' => $notes,
            'decided_at' => now(),
        ]);
    }

    public function modify(float $finalPrice, ?string $notes = null): void
    {
        $this->update([
            'user_decision' => 'modified',
            'user_final_price' => $finalPrice,
            'user_notes' => $notes,
            'decided_at' => now(),
        ]);
    }

    public function markAsImplemented(): void
    {
        $this->update([
            'was_implemented' => true,
            'implemented_at' => now(),
        ]);
    }

    public function recordActualSale(float $salePrice, int $daysToSell): void
    {
        $this->update([
            'actual_sale_price' => $salePrice,
            'actual_days_to_sell' => $daysToSell,
            'was_accurate' => $this->evaluateAccuracy($salePrice, $daysToSell),
        ]);
    }

    // === SCOPES ===

    public function scopePending($query)
    {
        return $query->where('user_decision', 'pending');
    }

    public function scopeAccepted($query)
    {
        return $query->where('user_decision', 'accepted');
    }

    public function scopeImplemented($query)
    {
        return $query->where('was_implemented', true);
    }

    public function scopeHighConfidence($query)
    {
        return $query->where('confidence_score', '>=', 80);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForEgi($query, int $egiId)
    {
        return $query->where('egi_id', $egiId);
    }

    // === HELPERS ===

    public function getPriceRange(): array
    {
        return [
            'min' => $this->suggested_price_min,
            'optimal' => $this->suggested_price_optimal,
            'max' => $this->suggested_price_max,
        ];
    }

    public function getRevenueProjection(): array
    {
        return [
            'low' => $this->estimated_revenue_low,
            'optimal' => $this->estimated_revenue_optimal,
            'high' => $this->estimated_revenue_high,
        ];
    }

    public function getConfidenceLevel(): string
    {
        if ($this->confidence_score >= 80) {
            return 'high';
        } elseif ($this->confidence_score >= 60) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    public function hasHighConfidence(): bool
    {
        return $this->confidence_score >= 80;
    }

    public function getPriceAdjustmentDirection(): string
    {
        if ($this->price_adjustment_percentage > 0) {
            return 'increase';
        } elseif ($this->price_adjustment_percentage < 0) {
            return 'decrease';
        } else {
            return 'maintain';
        }
    }

    public function isAboveMarket(): bool
    {
        return $this->suggested_price_optimal > $this->market_average_price;
    }

    public function isBelowMarket(): bool
    {
        return $this->suggested_price_optimal < $this->market_average_price;
    }

    public function isAtMarket(): bool
    {
        $tolerance = 0.05; // 5% tolerance
        $diff = abs($this->suggested_price_optimal - $this->market_average_price) / $this->market_average_price;
        return $diff <= $tolerance;
    }

    // === PRIVATE HELPERS ===

    private function evaluateAccuracy(float $salePrice, int $daysToSell): bool
    {
        // Consider accurate if:
        // 1. Sale price within ±10% of suggested optimal
        // 2. Days to sell within ±30% of estimate
        $priceTolerance = 0.10;
        $timeTolerance = 0.30;

        $priceAccurate = abs($salePrice - $this->suggested_price_optimal) / $this->suggested_price_optimal <= $priceTolerance;
        $timeAccurate = $this->estimated_time_to_sell_days ? 
            abs($daysToSell - $this->estimated_time_to_sell_days) / $this->estimated_time_to_sell_days <= $timeTolerance : 
            true;

        return $priceAccurate && $timeAccurate;
    }
}
