<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserFeaturePurchase;
use App\Enums\Gdpr\GdprActivityCategory;
use App\Services\Gdpr\AuditLogService;
use App\Services\EgiliService;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * @Oracode Service: Feature Purchase Orchestrator
 * 🎯 Purpose: Manage feature purchases with automatic permission granting
 * 🧱 Core Logic: Purchase → Payment → Grant Permission → Activate Feature
 * 🛡️ GDPR Compliance: Full audit trail for purchases
 * 
 * HYBRID APPROACH:
 * - Pricing from ai_feature_pricing (catalog)
 * - Auto-grant Spatie permissions after payment
 * - Track purchase history in user_feature_purchases
 * - Support FIAT/Crypto/Egili payment methods
 * 
 * @package App\Services
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Feature Purchase System)
 * @date 2025-11-01
 */
class FeaturePurchaseService
{
    private UltraLogManager $logger;
    private ErrorManagerInterface $errorManager;
    private AuditLogService $auditService;
    private EgiliService $egiliService;
    
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService,
        EgiliService $egiliService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditService = $auditService;
        $this->egiliService = $egiliService;
    }
    
    /**
     * Get feature pricing from catalog
     * 
     * @param string $featureCode
     * @return object|null Feature pricing record
     */
    public function getFeaturePricing(string $featureCode): ?object
    {
        return DB::table('ai_feature_pricing')
            ->where('feature_code', $featureCode)
            ->where('is_active', true)
            ->first();
    }
    
    /**
     * Check if user already has feature
     * 
     * @param User $user
     * @param string $featureCode
     * @return bool
     */
    public function userHasFeature(User $user, string $featureCode): bool
    {
        return UserFeaturePurchase::where('user_id', $user->id)
            ->where('feature_code', $featureCode)
            ->active()
            ->exists();
    }
    
    /**
     * Purchase feature with Egili
     * 
     * @param User $user
     * @param string $featureCode
     * @param array $metadata Additional context
     * @return UserFeaturePurchase
     * @throws \Exception
     */
    public function purchaseWithEgili(
        User $user,
        string $featureCode,
        array $metadata = []
    ): UserFeaturePurchase {
        // Get pricing
        $pricing = $this->getFeaturePricing($featureCode);
        
        if (!$pricing) {
            throw new \Exception("Feature not found or inactive: {$featureCode}");
        }
        
        if (!$pricing->cost_egili) {
            throw new \Exception("Feature cannot be purchased with Egili: {$featureCode}");
        }
        
        // ULM: Log purchase initiation
        $this->logger->info('Feature purchase with Egili initiated', [
            'user_id' => $user->id,
            'feature_code' => $featureCode,
            'cost_egili' => $pricing->cost_egili,
            'log_category' => 'FEATURE_PURCHASE_EGILI_START'
        ]);
        
        return DB::transaction(function () use ($user, $featureCode, $pricing, $metadata) {
            // 1. Spend Egili (atomic, handled by EgiliService)
            $egiliTransaction = $this->egiliService->spend(
                $user,
                $pricing->cost_egili,
                "feature_purchase_{$featureCode}",
                'service',
                array_merge($metadata, ['feature_code' => $featureCode])
            );
            
            // 2. Create purchase record
            $purchase = UserFeaturePurchase::create([
                'user_id' => $user->id,
                'feature_code' => $featureCode,
                'granted_permission' => $this->getLinkedPermission($featureCode),
                'payment_method' => 'egili',
                'amount_paid_egili' => $pricing->cost_egili,
                'purchased_at' => now(),
                'activated_at' => now(),
                'expires_at' => $this->calculateExpiration($pricing),
                'is_active' => true,
                'quantity_purchased' => $pricing->max_uses_per_purchase,
                'source_type' => $metadata['source_type'] ?? null,
                'source_id' => $metadata['source_id'] ?? null,
                'status' => 'active',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'metadata' => $metadata,
            ]);
            
            // 3. Auto-grant permission (Spatie)
            if ($purchase->granted_permission) {
                $this->grantPermissionToUser($user, $purchase->granted_permission);
            }
            
            // 4. GDPR: Audit trail
            $this->auditService->logUserAction(
                $user,
                'feature_purchased_egili',
                [
                    'feature_code' => $featureCode,
                    'feature_name' => $pricing->feature_name,
                    'cost_egili' => $pricing->cost_egili,
                    'purchase_id' => $purchase->id,
                    'egili_transaction_id' => $egiliTransaction->id,
                ],
                GdprActivityCategory::BLOCKCHAIN_ACTIVITY
            );
            
            // 5. ULM: Log success
            $this->logger->info('Feature purchased with Egili successfully', [
                'user_id' => $user->id,
                'feature_code' => $featureCode,
                'purchase_id' => $purchase->id,
                'log_category' => 'FEATURE_PURCHASE_EGILI_SUCCESS'
            ]);
            
            return $purchase;
        });
    }
    
    /**
     * Purchase feature with FIAT
     */
    public function purchaseWithFiat(
        User $user,
        string $featureCode,
        string $provider,
        array $metadata = []
    ): array {
        $pricing = $this->getFeaturePricing($featureCode);
        
        if (!$pricing || !$pricing->cost_fiat_eur) {
            throw new \Exception("Feature cannot be purchased with FIAT: {$featureCode}");
        }
        
        // Return payment session for redirect
        // Actual purchase record created in webhook/callback
        return [
            'payment_method' => 'fiat',
            'provider' => $provider,
            'amount_eur' => $pricing->cost_fiat_eur,
            'redirect_url' => route('features.payment.fiat', [
                'code' => $featureCode,
                'provider' => $provider,
            ]),
        ];
    }
    
    /**
     * Activate feature after payment confirmation
     * 
     * @param User $user
     * @param string $featureCode
     * @param string $paymentMethod
     * @param string $paymentId
     * @param array $metadata
     * @return UserFeaturePurchase
     */
    public function activateAfterPayment(
        User $user,
        string $featureCode,
        string $paymentMethod,
        string $paymentId,
        array $metadata = []
    ): UserFeaturePurchase {
        $pricing = $this->getFeaturePricing($featureCode);
        
        return DB::transaction(function () use ($user, $featureCode, $pricing, $paymentMethod, $paymentId, $metadata) {
            // Create purchase record
            $purchase = UserFeaturePurchase::create([
                'user_id' => $user->id,
                'feature_code' => $featureCode,
                'granted_permission' => $this->getLinkedPermission($featureCode),
                'payment_method' => $paymentMethod,
                'payment_provider' => $metadata['provider'] ?? null,
                'payment_transaction_id' => $paymentId,
                'amount_paid_eur' => $pricing->cost_fiat_eur,
                'purchased_at' => now(),
                'activated_at' => now(),
                'expires_at' => $this->calculateExpiration($pricing),
                'is_active' => true,
                'status' => 'active',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'metadata' => $metadata,
            ]);
            
            // Auto-grant permission
            if ($purchase->granted_permission) {
                $this->grantPermissionToUser($user, $purchase->granted_permission);
            }
            
            // GDPR: Audit trail
            $this->auditService->logUserAction(
                $user,
                'feature_purchased_' . $paymentMethod,
                [
                    'feature_code' => $featureCode,
                    'purchase_id' => $purchase->id,
                    'payment_id' => $paymentId,
                ],
                GdprActivityCategory::BLOCKCHAIN_ACTIVITY
            );
            
            return $purchase;
        });
    }
    
    /**
     * Grant Spatie permission to user
     */
    private function grantPermissionToUser(User $user, string $permissionName): void
    {
        // Check if permission exists
        $permission = Permission::where('name', $permissionName)->first();
        
        if (!$permission) {
            $this->logger->warning('Permission not found, skipping auto-grant', [
                'permission_name' => $permissionName,
                'user_id' => $user->id,
                'log_category' => 'FEATURE_PERMISSION_NOT_FOUND'
            ]);
            return;
        }
        
        // Grant permission if not already assigned
        if (!$user->hasPermissionTo($permissionName)) {
            $user->givePermissionTo($permissionName);
            
            $this->logger->info('Permission auto-granted after purchase', [
                'user_id' => $user->id,
                'permission_name' => $permissionName,
                'log_category' => 'FEATURE_PERMISSION_GRANTED'
            ]);
        }
    }
    
    /**
     * Get linked permission from ai_feature_pricing
     */
    private function getLinkedPermission(string $featureCode): ?string
    {
        // Check if permission table has linked_feature_code
        $permission = DB::table('permissions')
            ->where('linked_feature_code', $featureCode)
            ->first();
        
        return $permission?->name;
    }
    
    /**
     * Calculate expiration date
     */
    private function calculateExpiration($pricing): ?\Carbon\Carbon
    {
        if (!$pricing->expires || !$pricing->duration_hours) {
            return null; // Lifetime
        }
        
        return now()->addHours($pricing->duration_hours);
    }
}

