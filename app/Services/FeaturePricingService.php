<?php

namespace App\Services;

use App\Models\User;
use App\Models\FeaturePromotion;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * @package App\Services
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Egili System Pricing)
 * @date 2025-11-02
 * @purpose Dynamic feature pricing calculator with tier discounts and promotions
 * 
 * Pricing Calculation Logic:
 * 1. Get base price from ai_feature_pricing
 * 2. Apply tier discount (if user has tier)
 * 3. Apply best active promotion
 * 4. Return pricing breakdown
 * 
 * Features:
 * - Multi-tier pricing support
 * - Promotion stacking (tier + promo)
 * - Quantity discounts
 * - Detailed price breakdown for UI
 */
class FeaturePricingService
{
    private UltraLogManager $logger;
    
    public function __construct(UltraLogManager $logger)
    {
        $this->logger = $logger;
    }
    
    /**
     * Calculate final price for feature purchase
     * 
     * Applies:
     * - Tier discount (based on user membership tier)
     * - Best active promotion (highest discount)
     * 
     * @param string $featureCode Feature code from ai_feature_pricing
     * @param User $user User purchasing (for tier pricing)
     * @param int $quantity Quantity to purchase
     * @return array Detailed pricing breakdown
     * @throws \Exception If feature not found
     */
    public function calculatePrice(
        string $featureCode,
        User $user,
        int $quantity = 1
    ): array {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException("Quantity must be positive, got: {$quantity}");
        }
        
        // Get feature pricing
        $pricing = DB::table('ai_feature_pricing')
            ->where('feature_code', $featureCode)
            ->first();
        
        if (!$pricing) {
            throw new \Exception("Feature '{$featureCode}' not found in pricing catalog");
        }
        
        if (!$pricing->is_active) {
            throw new \Exception("Feature '{$featureCode}' is not currently available for purchase");
        }
        
        // Determine base cost
        $baseCostPerUnit = $this->getBaseCost($pricing);
        $baseCostTotal = $baseCostPerUnit * $quantity;
        
        // ULM: Log pricing calculation start
        $this->logger->debug('Pricing calculation initiated', [
            'feature_code' => $featureCode,
            'user_id' => $user->id,
            'quantity' => $quantity,
            'base_cost_per_unit' => $baseCostPerUnit,
            'base_cost_total' => $baseCostTotal,
            'log_category' => 'PRICING_CALC_START'
        ]);
        
        // Apply tier discount
        $tierDiscount = $this->calculateTierDiscount($pricing, $user, $baseCostTotal);
        $priceAfterTier = $baseCostTotal - $tierDiscount;
        
        // Get active promotions
        $promotions = $this->getActivePromotions($featureCode);
        
        // Apply best promotion
        $promoData = $this->applyBestPromotion($priceAfterTier, $promotions, $user);
        $promoDiscount = $promoData['discount'];
        $promoApplied = $promoData['promo'];
        
        // Calculate final price
        $finalCost = max(0, $priceAfterTier - $promoDiscount);
        $totalSavings = $baseCostTotal - $finalCost;
        
        $breakdown = [
            'feature_code' => $featureCode,
            'feature_name' => $pricing->name_key ?? $featureCode,
            'quantity' => $quantity,
            
            // Base pricing
            'base_cost_per_unit' => $baseCostPerUnit,
            'base_cost_total' => $baseCostTotal,
            
            // Tier discount
            'tier_discount' => $tierDiscount,
            'tier_discount_percent' => $baseCostTotal > 0 ? round(($tierDiscount / $baseCostTotal) * 100, 2) : 0,
            'price_after_tier' => $priceAfterTier,
            
            // Promotion
            'promo_discount' => $promoDiscount,
            'promo_discount_percent' => $priceAfterTier > 0 ? round(($promoDiscount / $priceAfterTier) * 100, 2) : 0,
            'promo_applied' => $promoApplied?->promo_code,
            'promo_name' => $promoApplied?->promo_name,
            'promo_badge' => $promoApplied?->badge_text,
            
            // Final
            'final_cost' => $finalCost,
            'total_savings' => $totalSavings,
            'savings_percent' => $baseCostTotal > 0 ? round(($totalSavings / $baseCostTotal) * 100, 2) : 0,
            
            // Metadata
            'has_tier_discount' => $tierDiscount > 0,
            'has_promo' => $promoDiscount > 0,
            'is_free' => $finalCost === 0,
        ];
        
        // ULM: Log pricing calculation result
        $this->logger->info('Pricing calculated', [
            'feature_code' => $featureCode,
            'user_id' => $user->id,
            'final_cost' => $finalCost,
            'total_savings' => $totalSavings,
            'promo_applied' => $promoApplied?->promo_code,
            'log_category' => 'PRICING_CALC_SUCCESS'
        ]);
        
        return $breakdown;
    }
    
    /**
     * Get base cost for feature (per-use or lifetime)
     * 
     * @param object $pricing Pricing record from ai_feature_pricing
     * @return int Base cost in Egili
     */
    private function getBaseCost(object $pricing): int
    {
        // Check feature type (from Task 1.5)
        $featureType = $pricing->feature_type ?? 'consumable';
        
        if ($featureType === 'lifetime' && $pricing->lifetime_cost) {
            return (int) $pricing->lifetime_cost;
        }
        
        if ($featureType === 'consumable' && $pricing->cost_per_use) {
            return (int) $pricing->cost_per_use;
        }
        
        // Fallback to cost_egili
        return (int) $pricing->cost_egili;
    }
    
    /**
     * Calculate tier discount
     * 
     * Checks ai_feature_pricing.tier_pricing JSON for user's tier.
     * Format: {"free": 100, "pro": 80, "enterprise": 60}
     * 
     * @param object $pricing Pricing record
     * @param User $user User purchasing
     * @param int $basePrice Base price before discounts
     * @return int Discount amount in Egili
     */
    private function calculateTierDiscount(object $pricing, User $user, int $basePrice): int
    {
        // Get tier pricing (JSON field)
        $tierPricing = $pricing->tier_pricing ? json_decode($pricing->tier_pricing, true) : null;
        
        if (!$tierPricing || !is_array($tierPricing)) {
            return 0;
        }
        
        // Determine user tier (simplified - assume method exists)
        // TODO: Implement proper tier detection (via Spatie roles or separate tier system)
        $userTier = $this->getUserTier($user);
        
        if (!$userTier || !isset($tierPricing[$userTier])) {
            return 0;
        }
        
        // Tier pricing can be:
        // 1. Absolute price: {"pro": 400} -> user pays 400 instead of base
        // 2. Percentage discount: {"pro": -20} -> user gets 20% off
        
        $tierValue = $tierPricing[$userTier];
        
        if ($tierValue < 0) {
            // Percentage discount (e.g., -20 = 20% off)
            $discountPercent = abs($tierValue);
            return (int) round($basePrice * ($discountPercent / 100));
        } else {
            // Absolute price (e.g., 400 = user pays 400)
            return max(0, $basePrice - $tierValue);
        }
    }
    
    /**
     * Get user tier
     * 
     * TODO: Implement proper tier system
     * For now, returns 'free' for all users
     * 
     * @param User $user
     * @return string Tier name (free, pro, enterprise)
     */
    private function getUserTier(User $user): string
    {
        // PLACEHOLDER: Implement tier detection
        // Options:
        // 1. Check Spatie role: $user->hasRole('pro') → 'pro'
        // 2. Check separate tier field: $user->tier
        // 3. Check subscription status
        
        return 'free';
    }
    
    /**
     * Get active promotions for feature
     * 
     * Returns all valid promotions that apply to the feature:
     * - Global promotions
     * - Feature-specific promotions
     * - Category-specific promotions
     * 
     * @param string $featureCode
     * @return Collection<FeaturePromotion>
     */
    public function getActivePromotions(string $featureCode): Collection
    {
        // Get feature category (if exists)
        $featureCategory = DB::table('ai_feature_pricing')
            ->where('feature_code', $featureCode)
            ->value('category');
        
        $promotions = FeaturePromotion::valid()
            ->notExhausted()
            ->where(function($query) use ($featureCode, $featureCategory) {
                $query->where('is_global', true)
                      ->orWhere('feature_code', $featureCode);
                
                if ($featureCategory) {
                    $query->orWhere('feature_category', $featureCategory);
                }
            })
            ->orderByRaw("
                CASE discount_type
                    WHEN 'percentage' THEN discount_value
                    ELSE (discount_value / 10) 
                END DESC
            ") // Order by effective discount (approximate)
            ->get();
        
        return $promotions;
    }
    
    /**
     * Apply best promotion (highest discount)
     * 
     * @param int $basePrice Price before promo
     * @param Collection $promotions Available promotions
     * @param User $user User purchasing
     * @return array ['discount' => int, 'promo' => FeaturePromotion|null]
     */
    private function applyBestPromotion(int $basePrice, Collection $promotions, User $user): array
    {
        if ($promotions->isEmpty()) {
            return ['discount' => 0, 'promo' => null];
        }
        
        $bestDiscount = 0;
        $bestPromo = null;
        
        foreach ($promotions as $promo) {
            // Check if user can use this promo
            if (!$promo->canBeUsedBy($user)) {
                continue;
            }
            
            $discount = $promo->calculateDiscount($basePrice);
            
            if ($discount > $bestDiscount) {
                $bestDiscount = $discount;
                $bestPromo = $promo;
            }
        }
        
        if ($bestPromo) {
            $this->logger->debug('Best promotion selected', [
                'promo_code' => $bestPromo->promo_code,
                'discount' => $bestDiscount,
                'base_price' => $basePrice,
                'log_category' => 'PRICING_PROMO_SELECTED'
            ]);
        }
        
        return ['discount' => $bestDiscount, 'promo' => $bestPromo];
    }
    
    /**
     * Get pricing preview for multiple features (for bundles)
     * 
     * @param array $featureCodes Array of feature codes
     * @param User $user
     * @return array Pricing breakdown per feature + total
     */
    public function calculateBundlePrice(array $featureCodes, User $user): array
    {
        $features = [];
        $totalBase = 0;
        $totalFinal = 0;
        $totalSavings = 0;
        
        foreach ($featureCodes as $featureCode) {
            try {
                $pricing = $this->calculatePrice($featureCode, $user, 1);
                $features[] = $pricing;
                $totalBase += $pricing['base_cost_total'];
                $totalFinal += $pricing['final_cost'];
                $totalSavings += $pricing['total_savings'];
            } catch (\Exception $e) {
                $this->logger->warning('Bundle pricing failed for feature', [
                    'feature_code' => $featureCode,
                    'error' => $e->getMessage(),
                    'log_category' => 'PRICING_BUNDLE_ERROR'
                ]);
            }
        }
        
        return [
            'features' => $features,
            'bundle_total_base' => $totalBase,
            'bundle_total_final' => $totalFinal,
            'bundle_total_savings' => $totalSavings,
            'bundle_savings_percent' => $totalBase > 0 ? round(($totalSavings / $totalBase) * 100, 2) : 0,
        ];
    }
}



