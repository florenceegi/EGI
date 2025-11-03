<?php

namespace App\Services;

use App\Models\User;
use App\Models\FeaturePromotion;
use App\Enums\Gdpr\GdprActivityCategory;
use App\Services\Gdpr\AuditLogService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * @package App\Services
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Egili System Pricing)
 * @date 2025-11-02
 * @purpose Promotion Manager - CRUD and lifecycle management
 * 
 * Manages:
 * - CRUD operations for promotions
 * - Activation/deactivation
 * - Usage tracking and limits
 * - Promo eligibility checking
 */
class FeaturePromotionService
{
    private UltraLogManager $logger;
    private ErrorManagerInterface $errorManager;
    private AuditLogService $auditService;
    
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditService = $auditService;
    }
    
    /**
     * Create new promotion
     * 
     * @param array $data Promotion data
     * @param User $admin Admin creating promo
     * @return FeaturePromotion Created promotion
     * @throws \Exception If validation fails
     */
    public function createPromotion(array $data, User $admin): FeaturePromotion
    {
        // Validate required fields
        $this->validatePromotionData($data);
        
        // ULM: Log creation start
        $this->logger->info('Promotion creation initiated', [
            'admin_id' => $admin->id,
            'promo_code' => $data['promo_code'],
            'log_category' => 'PROMO_CREATE_START'
        ]);
        
        return DB::transaction(function () use ($data, $admin) {
            // Create promotion
            $promo = FeaturePromotion::create([
                'promo_code' => strtoupper($data['promo_code']),
                'promo_name' => $data['promo_name'],
                'promo_description' => $data['promo_description'] ?? null,
                'is_global' => $data['is_global'] ?? false,
                'feature_code' => $data['feature_code'] ?? null,
                'feature_category' => $data['feature_category'] ?? null,
                'discount_type' => $data['discount_type'],
                'discount_value' => $data['discount_value'],
                'start_at' => $data['start_at'],
                'end_at' => $data['end_at'],
                'max_uses' => $data['max_uses'] ?? null,
                'max_uses_per_user' => $data['max_uses_per_user'] ?? null,
                'current_uses' => 0,
                'is_active' => $data['is_active'] ?? true,
                'is_featured' => $data['is_featured'] ?? false,
                'badge_text' => $data['badge_text'] ?? null,
                'created_by_admin_id' => $admin->id,
                'admin_notes' => $data['admin_notes'] ?? null,
                'total_egili_saved' => 0,
                'total_purchases_with_promo' => 0,
            ]);
            
            // GDPR: Audit trail (TODO: Add PROMO_CREATED to GdprActivityCategory)
            $this->auditService->logUserAction(
                $admin,
                'feature_promo_created',
                [
                    'promo_id' => $promo->id,
                    'promo_code' => $promo->promo_code,
                    'discount_type' => $promo->discount_type,
                    'discount_value' => $promo->discount_value,
                    'is_global' => $promo->is_global,
                    'feature_code' => $promo->feature_code,
                ],
                GdprActivityCategory::PERSONAL_DATA_UPDATE // TODO: Use dedicated category
            );
            
            // ULM: Log success
            $this->logger->info('Promotion created successfully', [
                'admin_id' => $admin->id,
                'promo_id' => $promo->id,
                'promo_code' => $promo->promo_code,
                'log_category' => 'PROMO_CREATE_SUCCESS'
            ]);
            
            return $promo;
        });
    }
    
    /**
     * Update existing promotion
     * 
     * @param int $id Promotion ID
     * @param array $data Updated data
     * @param User $admin Admin updating promo
     * @return FeaturePromotion Updated promotion
     * @throws \Exception If promo not found
     */
    public function updatePromotion(int $id, array $data, User $admin): FeaturePromotion
    {
        $promo = FeaturePromotion::findOrFail($id);
        
        // ULM: Log update start
        $this->logger->info('Promotion update initiated', [
            'admin_id' => $admin->id,
            'promo_id' => $promo->id,
            'promo_code' => $promo->promo_code,
            'log_category' => 'PROMO_UPDATE_START'
        ]);
        
        return DB::transaction(function () use ($promo, $data, $admin) {
            $oldData = $promo->toArray();
            
            // Update fields
            $promo->update(array_filter($data, fn($value) => $value !== null));
            
            // GDPR: Audit trail
            $this->auditService->logUserAction(
                $admin,
                'feature_promo_updated',
                [
                    'promo_id' => $promo->id,
                    'promo_code' => $promo->promo_code,
                    'changes' => array_diff_assoc($promo->toArray(), $oldData),
                ],
                GdprActivityCategory::PERSONAL_DATA_UPDATE
            );
            
            // ULM: Log success
            $this->logger->info('Promotion updated successfully', [
                'admin_id' => $admin->id,
                'promo_id' => $promo->id,
                'log_category' => 'PROMO_UPDATE_SUCCESS'
            ]);
            
            return $promo->fresh();
        });
    }
    
    /**
     * Delete promotion
     * 
     * @param int $id Promotion ID
     * @param User $admin Admin deleting promo
     * @return bool Success
     * @throws \Exception If promo not found or has active uses
     */
    public function deletePromotion(int $id, User $admin): bool
    {
        $promo = FeaturePromotion::findOrFail($id);
        
        // Prevent deletion if promo has been used
        if ($promo->current_uses > 0) {
            throw new \Exception(
                "Cannot delete promotion '{$promo->promo_code}' - it has been used {$promo->current_uses} times. Deactivate instead."
            );
        }
        
        // ULM: Log deletion
        $this->logger->warning('Promotion deleted', [
            'admin_id' => $admin->id,
            'promo_id' => $promo->id,
            'promo_code' => $promo->promo_code,
            'log_category' => 'PROMO_DELETE'
        ]);
        
        // GDPR: Audit trail
        $this->auditService->logUserAction(
            $admin,
            'feature_promo_deleted',
            [
                'promo_id' => $promo->id,
                'promo_code' => $promo->promo_code,
            ],
            GdprActivityCategory::PERSONAL_DATA_UPDATE
        );
        
        return $promo->delete();
    }
    
    /**
     * Activate promotion
     * 
     * @param int $id Promotion ID
     * @param User $admin Admin activating promo
     * @return FeaturePromotion Activated promotion
     */
    public function activatePromotion(int $id, User $admin): FeaturePromotion
    {
        $promo = FeaturePromotion::findOrFail($id);
        
        $promo->update(['is_active' => true]);
        
        // ULM: Log activation
        $this->logger->info('Promotion activated', [
            'admin_id' => $admin->id,
            'promo_id' => $promo->id,
            'promo_code' => $promo->promo_code,
            'log_category' => 'PROMO_ACTIVATE'
        ]);
        
        // GDPR: Audit trail
        $this->auditService->logUserAction(
            $admin,
            'feature_promo_activated',
            [
                'promo_id' => $promo->id,
                'promo_code' => $promo->promo_code,
            ],
            GdprActivityCategory::PERSONAL_DATA_UPDATE
        );
        
        return $promo;
    }
    
    /**
     * Deactivate promotion
     * 
     * @param int $id Promotion ID
     * @param User $admin Admin deactivating promo
     * @return FeaturePromotion Deactivated promotion
     */
    public function deactivatePromotion(int $id, User $admin): FeaturePromotion
    {
        $promo = FeaturePromotion::findOrFail($id);
        
        $promo->update(['is_active' => false]);
        
        // ULM: Log deactivation
        $this->logger->info('Promotion deactivated', [
            'admin_id' => $admin->id,
            'promo_id' => $promo->id,
            'promo_code' => $promo->promo_code,
            'log_category' => 'PROMO_DEACTIVATE'
        ]);
        
        // GDPR: Audit trail
        $this->auditService->logUserAction(
            $admin,
            'feature_promo_deactivated',
            [
                'promo_id' => $promo->id,
                'promo_code' => $promo->promo_code,
            ],
            GdprActivityCategory::PERSONAL_DATA_UPDATE
        );
        
        return $promo;
    }
    
    /**
     * Get all active promotions (for pricing calculation)
     * 
     * @param string|null $featureCode Optional: filter by feature
     * @return Collection<FeaturePromotion>
     */
    public function getActivePromotions(?string $featureCode = null): Collection
    {
        $query = FeaturePromotion::valid()->notExhausted();
        
        if ($featureCode) {
            $query->forFeature($featureCode);
        }
        
        return $query->get();
    }
    
    /**
     * Record promotion usage
     * 
     * Called after a successful purchase using the promo.
     * Increments usage counters and tracks savings.
     * 
     * @param FeaturePromotion $promo Promotion used
     * @param User $user User who used promo
     * @param int $egiliSaved Egili saved by user
     * @return void
     */
    public function recordPromoUsage(
        FeaturePromotion $promo,
        User $user,
        int $egiliSaved
    ): void {
        DB::transaction(function () use ($promo, $user, $egiliSaved) {
            // Update promo stats
            $promo->recordUse($egiliSaved);
            
            // GDPR: Audit trail (TODO: Add FEATURE_PROMO_APPLIED to GdprActivityCategory)
            $this->auditService->logUserAction(
                $user,
                'feature_promo_used',
                [
                    'promo_id' => $promo->id,
                    'promo_code' => $promo->promo_code,
                    'egili_saved' => $egiliSaved,
                ],
                GdprActivityCategory::BLOCKCHAIN_ACTIVITY // TODO: Use dedicated category
            );
            
            // ULM: Log usage
            $this->logger->info('Promotion used', [
                'user_id' => $user->id,
                'promo_id' => $promo->id,
                'promo_code' => $promo->promo_code,
                'egili_saved' => $egiliSaved,
                'remaining_uses' => $promo->remaining_uses,
                'log_category' => 'PROMO_USED'
            ]);
        });
    }
    
    /**
     * Check if user can use promo
     * 
     * @param FeaturePromotion $promo Promotion to check
     * @param User $user User attempting to use promo
     * @return bool True if user can use promo
     */
    public function canUsePromo(FeaturePromotion $promo, User $user): bool
    {
        return $promo->canBeUsedBy($user);
    }
    
    /**
     * Get promotion stats
     * 
     * @param int $id Promotion ID
     * @return array Stats breakdown
     */
    public function getPromoStats(int $id): array
    {
        $promo = FeaturePromotion::findOrFail($id);
        
        return [
            'promo_code' => $promo->promo_code,
            'promo_name' => $promo->promo_name,
            'is_active' => $promo->is_active,
            'is_valid' => $promo->isValid(),
            
            // Usage stats
            'current_uses' => $promo->current_uses,
            'max_uses' => $promo->max_uses,
            'remaining_uses' => $promo->remaining_uses,
            'usage_percent' => $promo->max_uses ? round(($promo->current_uses / $promo->max_uses) * 100, 2) : null,
            
            // Financial stats
            'total_purchases' => $promo->total_purchases_with_promo,
            'total_egili_saved' => $promo->total_egili_saved,
            'avg_savings_per_purchase' => $promo->total_purchases_with_promo > 0 
                ? round($promo->total_egili_saved / $promo->total_purchases_with_promo, 2)
                : 0,
            
            // Temporal stats
            'days_remaining' => $promo->days_remaining,
            'is_expiring_soon' => $promo->isExpiringSoon(),
            'start_at' => $promo->start_at,
            'end_at' => $promo->end_at,
        ];
    }
    
    /**
     * Validate promotion data
     * 
     * @param array $data
     * @throws \Exception If validation fails
     */
    private function validatePromotionData(array $data): void
    {
        $required = ['promo_code', 'promo_name', 'discount_type', 'discount_value', 'start_at', 'end_at'];
        
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                throw new \Exception("Field '{$field}' is required for promotion creation");
            }
        }
        
        // Validate discount type
        if (!in_array($data['discount_type'], ['percentage', 'fixed_amount'])) {
            throw new \Exception("Invalid discount_type. Must be 'percentage' or 'fixed_amount'");
        }
        
        // Validate discount value
        if ($data['discount_value'] <= 0) {
            throw new \Exception("Discount value must be positive");
        }
        
        if ($data['discount_type'] === 'percentage' && $data['discount_value'] > 100) {
            throw new \Exception("Percentage discount cannot exceed 100%");
        }
        
        // Validate dates
        if (strtotime($data['end_at']) <= strtotime($data['start_at'])) {
            throw new \Exception("End date must be after start date");
        }
        
        // Validate scope
        if (!($data['is_global'] ?? false) && !isset($data['feature_code']) && !isset($data['feature_category'])) {
            throw new \Exception("Non-global promotions must have either feature_code or feature_category");
        }
    }
}



