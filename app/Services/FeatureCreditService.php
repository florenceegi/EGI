<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserFeaturePurchase;
use App\Enums\Gdpr\GdprActivityCategory;
use App\Services\Gdpr\AuditLogService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * @package App\Services
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.1.0 (FlorenceEGI - Egili System with Dynamic Pricing)
 * @date 2025-11-02
 * @purpose Manage feature credits (purchase, consumption, conversion)
 * 
 * Feature Credits Manager:
 * - Purchase credits for consumable features (AI descriptions, etc)
 * - Consume credits when using features
 * - Convert unused credits back to Egili (100% value)
 * - Track usage metadata for analytics
 * 
 * Integration with EgiliService:
 * - Uses EgiliService->spend() to pay for credits
 * - Uses EgiliService->earn() to refund on conversion
 * 
 * NEW IN v1.1.0: Dynamic Pricing Integration
 * - Uses FeaturePricingService for tier discounts and promotions
 * - Uses FeaturePromotionService to track promo usage
 * - Stores pricing breakdown in purchase metadata
 */
class FeatureCreditService
{
    private UltraLogManager $logger;
    private ErrorManagerInterface $errorManager;
    private AuditLogService $auditService;
    private EgiliService $egiliService;
    private FeaturePricingService $pricingService;
    private FeaturePromotionService $promotionService;
    
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService,
        EgiliService $egiliService,
        FeaturePricingService $pricingService,
        FeaturePromotionService $promotionService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditService = $auditService;
        $this->egiliService = $egiliService;
        $this->pricingService = $pricingService;
        $this->promotionService = $promotionService;
    }
    
    /**
     * Purchase feature credits
     * 
     * Spends Egili and creates UserFeaturePurchase record.
     * For consumable features (AI descriptions, etc).
     * 
     * @param User $user User purchasing credits
     * @param string $featureCode Feature code from ai_feature_pricing
     * @param int $quantity How many uses to buy
     * @return UserFeaturePurchase Created purchase record
     * @throws \Exception If insufficient Egili or feature not found
     */
    public function purchaseCredits(
        User $user,
        string $featureCode,
        int $quantity = 1
    ): UserFeaturePurchase {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException("Quantity must be positive, got: {$quantity}");
        }
        
        // ULM: Log purchase start
        $this->logger->info('Feature credit purchase initiated', [
            'user_id' => $user->id,
            'feature_code' => $featureCode,
            'quantity' => $quantity,
            'log_category' => 'FEATURE_CREDIT_PURCHASE_START'
        ]);
        
        // Calculate dynamic pricing (tier discounts + promotions)
        $pricingBreakdown = $this->pricingService->calculatePrice($featureCode, $user, $quantity);
        $finalCost = $pricingBreakdown['final_cost'];
        
        // Check if user can afford (final price after all discounts)
        if (!$this->egiliService->canSpend($user, $finalCost)) {
            $currentBalance = $this->egiliService->getBalance($user);
            throw new \Exception(
                "Saldo Egili insufficiente per acquistare {$quantity}x {$featureCode}. " .
                "Costo finale: {$finalCost} Egili, Disponibili: {$currentBalance} Egili"
            );
        }
        
        return DB::transaction(function () use ($user, $featureCode, $quantity, $finalCost, $pricingBreakdown) {
            // Spend Egili (final cost after all discounts)
            $egiliTransaction = $this->egiliService->spend(
                $user,
                $finalCost,
                "purchase_feature_credits_{$featureCode}",
                'feature_purchase',
                [
                    'feature_code' => $featureCode,
                    'quantity' => $quantity,
                    'pricing_breakdown' => $pricingBreakdown,
                ]
            );
            
            // Create purchase record
            $purchase = UserFeaturePurchase::create([
                'user_id' => $user->id,
                'feature_code' => $featureCode,
                'payment_method' => 'egili',
                'amount_paid_egili' => $finalCost,
                'quantity_purchased' => $quantity,
                'quantity_used' => 0,
                'purchased_at' => now(),
                'activated_at' => now(),
                'is_active' => true,
                'status' => 'active',
                'is_lifetime' => false, // Consumable, not lifetime
                'promo_code' => $pricingBreakdown['promo_applied'] ?? null,
                'metadata' => [
                    'egili_transaction_id' => $egiliTransaction->id,
                    'pricing_breakdown' => $pricingBreakdown,
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            // Record promotion usage (if promo was applied)
            if ($pricingBreakdown['promo_applied']) {
                $promo = \App\Models\FeaturePromotion::where('promo_code', $pricingBreakdown['promo_applied'])->first();
                if ($promo) {
                    $this->promotionService->recordPromoUsage(
                        $promo,
                        $user,
                        $pricingBreakdown['total_savings']
                    );
                }
            }
            
            // GDPR: Audit trail (TODO: Add FEATURE_PURCHASED to GdprActivityCategory)
            $this->auditService->logUserAction(
                $user,
                'feature_credits_purchased',
                [
                    'feature_code' => $featureCode,
                    'quantity' => $quantity,
                    'final_cost_egili' => $finalCost,
                    'base_cost' => $pricingBreakdown['base_cost_total'],
                    'total_savings' => $pricingBreakdown['total_savings'],
                    'promo_applied' => $pricingBreakdown['promo_applied'],
                    'purchase_id' => $purchase->id,
                    'egili_transaction_id' => $egiliTransaction->id,
                ],
                GdprActivityCategory::BLOCKCHAIN_ACTIVITY // TODO: Use FEATURE_PURCHASED when added
            );
            
            // ULM: Log success
            $this->logger->info('Feature credits purchased successfully', [
                'user_id' => $user->id,
                'feature_code' => $featureCode,
                'purchase_id' => $purchase->id,
                'quantity' => $quantity,
                'final_cost_egili' => $finalCost,
                'total_savings' => $pricingBreakdown['total_savings'],
                'promo_applied' => $pricingBreakdown['promo_applied'],
                'log_category' => 'FEATURE_CREDIT_PURCHASE_SUCCESS'
            ]);
            
            return $purchase;
        });
    }
    
    /**
     * Consume one credit
     * 
     * Finds first available purchase with credits remaining and increments quantity_used.
     * FIFO consumption (oldest purchase first).
     * 
     * @param User $user User consuming credit
     * @param string $featureCode Feature being used
     * @param array $usageMetadata Additional usage tracking (e.g., AI tokens consumed)
     * @return bool Success
     * @throws \Exception If no credits available
     */
    public function consumeCredit(
        User $user,
        string $featureCode,
        array $usageMetadata = []
    ): bool {
        // Find first purchase with available credits (FIFO)
        $purchase = UserFeaturePurchase::where('user_id', $user->id)
            ->where('feature_code', $featureCode)
            ->where('is_active', true)
            ->where('status', 'active')
            ->whereRaw('quantity_used < quantity_purchased')
            ->orderBy('purchased_at', 'asc') // FIFO
            ->first();
        
        if (!$purchase) {
            $this->logger->warning('Feature credit consumption failed - no credits available', [
                'user_id' => $user->id,
                'feature_code' => $featureCode,
                'log_category' => 'FEATURE_CREDIT_CONSUME_NO_CREDITS'
            ]);
            
            throw new \Exception(
                "Nessun credito disponibile per la feature '{$featureCode}'. Acquista nuovi crediti."
            );
        }
        
        return DB::transaction(function () use ($user, $featureCode, $purchase, $usageMetadata) {
            // Increment usage
            $purchase->increment('quantity_used');
            
            // Append usage metadata
            $currentMetadata = $purchase->usage_metadata ?? [];
            $currentMetadata['usage_history'] = $currentMetadata['usage_history'] ?? [];
            $currentMetadata['usage_history'][] = [
                'used_at' => now()->toDateTimeString(),
                'metadata' => $usageMetadata,
            ];
            $purchase->update(['usage_metadata' => $currentMetadata]);
            
            // GDPR: Audit trail (TODO: Add FEATURE_CONSUMED to GdprActivityCategory)
            $this->auditService->logUserAction(
                $user,
                'feature_credit_consumed',
                [
                    'feature_code' => $featureCode,
                    'purchase_id' => $purchase->id,
                    'quantity_remaining' => $purchase->quantity_purchased - $purchase->quantity_used,
                    'usage_metadata' => $usageMetadata,
                ],
                GdprActivityCategory::BLOCKCHAIN_ACTIVITY // TODO: Use FEATURE_CONSUMED when added
            );
            
            // ULM: Log consumption
            $this->logger->info('Feature credit consumed', [
                'user_id' => $user->id,
                'feature_code' => $featureCode,
                'purchase_id' => $purchase->id,
                'quantity_remaining' => $purchase->quantity_purchased - $purchase->quantity_used,
                'log_category' => 'FEATURE_CREDIT_CONSUMED'
            ]);
            
            return true;
        });
    }
    
    /**
     * Get available credits for feature
     * 
     * Sums all active purchases and subtracts used credits.
     * 
     * @param User $user
     * @param string $featureCode
     * @return int Available credits
     */
    public function getAvailableCredits(User $user, string $featureCode): int
    {
        $available = UserFeaturePurchase::where('user_id', $user->id)
            ->where('feature_code', $featureCode)
            ->where('is_active', true)
            ->where('status', 'active')
            ->selectRaw('SUM(quantity_purchased - quantity_used) as available')
            ->value('available');
        
        return (int) ($available ?? 0);
    }
    
    /**
     * Check if user has feature (lifetime or available credits)
     * 
     * @param User $user
     * @param string $featureCode
     * @return bool True if user can use feature
     */
    public function hasFeature(User $user, string $featureCode): bool
    {
        // Check for lifetime purchase
        $hasLifetime = UserFeaturePurchase::where('user_id', $user->id)
            ->where('feature_code', $featureCode)
            ->where('is_lifetime', true)
            ->where('is_active', true)
            ->where('status', 'active')
            ->exists();
        
        if ($hasLifetime) {
            return true;
        }
        
        // Check for available credits
        return $this->getAvailableCredits($user, $featureCode) > 0;
    }
    
    /**
     * Convert unused credits to Egili (100% value)
     * 
     * Refunds unused credits as Egili.
     * No conversion limits (per user request: "NESSUN LIMITE, CONVERSIONE PIENA").
     * 
     * @param User $user User converting credits
     * @param string $featureCode Feature to convert from
     * @param int $quantity How many credits to convert
     * @return array ['egili_refunded' => int, 'credits_converted' => int, 'purchases_updated' => int]
     * @throws \Exception If insufficient credits available
     */
    public function convertToEgili(
        User $user,
        string $featureCode,
        int $quantity
    ): array {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException("Quantity must be positive, got: {$quantity}");
        }
        
        $availableCredits = $this->getAvailableCredits($user, $featureCode);
        
        if ($availableCredits < $quantity) {
            throw new \Exception(
                "Crediti insufficienti per conversione. Disponibili: {$availableCredits}, Richiesti: {$quantity}"
            );
        }
        
        // ULM: Log conversion start
        $this->logger->info('Feature credit conversion initiated', [
            'user_id' => $user->id,
            'feature_code' => $featureCode,
            'quantity' => $quantity,
            'log_category' => 'FEATURE_CREDIT_CONVERT_START'
        ]);
        
        return DB::transaction(function () use ($user, $featureCode, $quantity) {
            $remaining = $quantity;
            $totalEgiliRefunded = 0;
            $purchasesUpdated = 0;
            
            // Get purchases with available credits (FIFO)
            $purchases = UserFeaturePurchase::where('user_id', $user->id)
                ->where('feature_code', $featureCode)
                ->where('is_active', true)
                ->where('status', 'active')
                ->whereRaw('quantity_used < quantity_purchased')
                ->orderBy('purchased_at', 'asc')
                ->get();
            
            foreach ($purchases as $purchase) {
                if ($remaining <= 0) {
                    break;
                }
                
                $available = $purchase->quantity_purchased - $purchase->quantity_used;
                $toConvert = min($available, $remaining);
                
                // Calculate Egili refund (100% value)
                $costPerCredit = $purchase->metadata['cost_per_use'] ?? ($purchase->amount_paid_egili / $purchase->quantity_purchased);
                $egiliRefund = (int) round($toConvert * $costPerCredit);
                
                // Mark credits as "consumed" (converted)
                $purchase->increment('quantity_used', $toConvert);
                
                // Update metadata
                $metadata = $purchase->usage_metadata ?? [];
                $metadata['converted_to_egili'] = ($metadata['converted_to_egili'] ?? 0) + $toConvert;
                $metadata['conversion_history'] = $metadata['conversion_history'] ?? [];
                $metadata['conversion_history'][] = [
                    'converted_at' => now()->toDateTimeString(),
                    'quantity' => $toConvert,
                    'egili_refunded' => $egiliRefund,
                ];
                $purchase->update(['usage_metadata' => $metadata]);
                
                // Refund Egili (100% value)
                $this->egiliService->earn(
                    $user,
                    $egiliRefund,
                    "feature_credit_conversion_{$featureCode}",
                    'feature_conversion',
                    [
                        'feature_code' => $featureCode,
                        'credits_converted' => $toConvert,
                        'purchase_id' => $purchase->id,
                    ]
                );
                
                $totalEgiliRefunded += $egiliRefund;
                $remaining -= $toConvert;
                $purchasesUpdated++;
                
                $this->logger->debug('Feature credits converted from purchase', [
                    'user_id' => $user->id,
                    'purchase_id' => $purchase->id,
                    'credits_converted' => $toConvert,
                    'egili_refunded' => $egiliRefund,
                    'remaining_to_convert' => $remaining,
                    'log_category' => 'FEATURE_CREDIT_CONVERT_PARTIAL'
                ]);
            }
            
            // GDPR: Audit trail (TODO: Add FEATURE_CREDITS_CONVERTED to GdprActivityCategory)
            $this->auditService->logUserAction(
                $user,
                'feature_credits_converted',
                [
                    'feature_code' => $featureCode,
                    'credits_converted' => $quantity,
                    'egili_refunded' => $totalEgiliRefunded,
                    'purchases_updated' => $purchasesUpdated,
                ],
                GdprActivityCategory::BLOCKCHAIN_ACTIVITY // TODO: Use FEATURE_CREDITS_CONVERTED when added
            );
            
            // ULM: Log success
            $this->logger->info('Feature credits converted successfully', [
                'user_id' => $user->id,
                'feature_code' => $featureCode,
                'credits_converted' => $quantity,
                'egili_refunded' => $totalEgiliRefunded,
                'purchases_updated' => $purchasesUpdated,
                'log_category' => 'FEATURE_CREDIT_CONVERT_SUCCESS'
            ]);
            
            return [
                'egili_refunded' => $totalEgiliRefunded,
                'credits_converted' => $quantity,
                'purchases_updated' => $purchasesUpdated,
            ];
        });
    }
    
    /**
     * Get user's feature purchases history
     * 
     * @param User $user
     * @param string|null $featureCode Optional: filter by feature
     * @return Collection<UserFeaturePurchase>
     */
    public function getUserFeatures(User $user, ?string $featureCode = null): Collection
    {
        $query = UserFeaturePurchase::where('user_id', $user->id)
            ->orderBy('purchased_at', 'desc');
        
        if ($featureCode) {
            $query->where('feature_code', $featureCode);
        }
        
        return $query->get();
    }
    
    /**
     * Get user's active features summary
     * 
     * Returns summary of all active features with available credits.
     * 
     * @param User $user
     * @return array Feature summary by code
     */
    public function getActiveFeaturesummary(User $user): array
    {
        $features = UserFeaturePurchase::where('user_id', $user->id)
            ->where('is_active', true)
            ->where('status', 'active')
            ->get()
            ->groupBy('feature_code');
        
        $summary = [];
        
        foreach ($features as $featureCode => $purchases) {
            $isLifetime = $purchases->contains('is_lifetime', true);
            $totalPurchased = $purchases->sum('quantity_purchased');
            $totalUsed = $purchases->sum('quantity_used');
            $available = $totalPurchased - $totalUsed;
            
            $summary[$featureCode] = [
                'feature_code' => $featureCode,
                'is_lifetime' => $isLifetime,
                'total_purchased' => $totalPurchased,
                'total_used' => $totalUsed,
                'available' => $available,
                'purchases_count' => $purchases->count(),
                'first_purchased_at' => $purchases->min('purchased_at'),
                'last_purchased_at' => $purchases->max('purchased_at'),
            ];
        }
        
        return $summary;
    }
}

