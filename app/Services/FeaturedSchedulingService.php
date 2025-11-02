<?php

namespace App\Services;

use App\Models\User;
use App\Models\Egi;
use App\Models\UserFeaturePurchase;
use App\Enums\Gdpr\GdprActivityCategory;
use App\Services\Gdpr\AuditLogService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * @package App\Services
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Featured/Hyper Scheduling System)
 * @date 2025-11-02
 * @purpose Featured EGI and Hyper Mode scheduling with admin approval
 * 
 * System Overview:
 * 1. Creator requests Featured/Hyper for specific dates
 * 2. System checks slot availability + reserves Egili
 * 3. Admin reviews request in calendar view
 * 4. Admin approves → schedules for future activation
 * 5. Cron job activates when slot_start arrives
 * 6. Cron job deactivates when slot_end arrives
 * 
 * Features:
 * - Slot availability management (max concurrent slots)
 * - Egili reservation (not spent until activation)
 * - Admin approval workflow
 * - Automatic activation/deactivation via cron
 * - GDPR-compliant audit trail
 */
class FeaturedSchedulingService
{
    private UltraLogManager $logger;
    private ErrorManagerInterface $errorManager;
    private AuditLogService $auditService;
    private EgiliService $egiliService;
    private FeaturePricingService $pricingService;
    
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService,
        EgiliService $egiliService,
        FeaturePricingService $pricingService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditService = $auditService;
        $this->egiliService = $egiliService;
        $this->pricingService = $pricingService;
    }
    
    /**
     * Request featured/hyper slot
     * 
     * Creator requests promotion for their EGI.
     * Egili are RESERVED (not spent) until admin approval.
     * 
     * @param User $creator Creator requesting promotion
     * @param Egi $egi EGI to promote
     * @param string $featureType 'featured' or 'hyper'
     * @param Carbon $slotStart Desired start date
     * @param Carbon $slotEnd Desired end date
     * @param string|null $notes Optional notes for admin
     * @return UserFeaturePurchase Request record
     * @throws \Exception If insufficient Egili or invalid dates
     */
    public function requestSlot(
        User $creator,
        Egi $egi,
        string $featureType,
        Carbon $slotStart,
        Carbon $slotEnd,
        ?string $notes = null
    ): UserFeaturePurchase {
        // Validate feature type
        if (!in_array($featureType, ['featured', 'hyper'])) {
            throw new \InvalidArgumentException("Feature type must be 'featured' or 'hyper', got: {$featureType}");
        }
        
        // Validate dates
        if ($slotEnd <= $slotStart) {
            throw new \Exception("End date must be after start date");
        }
        
        if ($slotStart < now()) {
            throw new \Exception("Start date cannot be in the past");
        }
        
        $featureCode = $featureType === 'featured' ? 'featured_home_7days' : 'hyper_mode_7days';
        
        // ULM: Log request start
        $this->logger->info('Featured/Hyper slot request initiated', [
            'creator_id' => $creator->id,
            'egi_id' => $egi->id,
            'feature_type' => $featureType,
            'slot_start' => $slotStart->toDateTimeString(),
            'slot_end' => $slotEnd->toDateTimeString(),
            'log_category' => 'SLOT_REQUEST_START'
        ]);
        
        // Calculate pricing
        $durationDays = $slotStart->diffInDays($slotEnd);
        $pricingBreakdown = $this->pricingService->calculatePrice($featureCode, $creator, 1);
        $costEgili = $pricingBreakdown['final_cost'];
        
        // Check if user can afford (RESERVED, not spent yet)
        if (!$this->egiliService->canSpend($creator, $costEgili)) {
            $currentBalance = $this->egiliService->getBalance($creator);
            throw new \Exception(
                "Saldo Egili insufficiente per richiedere {$featureType}. " .
                "Costo: {$costEgili} Egili, Disponibili: {$currentBalance} Egili"
            );
        }
        
        return DB::transaction(function () use ($creator, $egi, $featureType, $featureCode, $slotStart, $slotEnd, $costEgili, $pricingBreakdown, $notes) {
            // Create request (Egili NOT spent yet, just reserved)
            $request = UserFeaturePurchase::create([
                'user_id' => $creator->id,
                'feature_code' => $featureCode,
                'payment_method' => 'egili',
                'egili_reserved' => $costEgili, // RESERVED, not spent
                'amount_paid_egili' => 0, // Will be set on approval
                'quantity_purchased' => 1,
                'quantity_used' => 0,
                'purchased_at' => now(),
                'status' => 'pending_approval', // Waiting for admin
                'is_lifetime' => false,
                'scheduled_slot_start' => $slotStart,
                'scheduled_slot_end' => $slotEnd,
                'admin_notes' => $notes,
                'metadata' => [
                    'egi_id' => $egi->id,
                    'feature_type' => $featureType,
                    'duration_days' => $slotStart->diffInDays($slotEnd),
                    'pricing_breakdown' => $pricingBreakdown,
                ],
                'source_type' => Egi::class,
                'source_id' => $egi->id,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            // GDPR: Audit trail (TODO: Add FEATURED_EGI_REQUESTED to GdprActivityCategory)
            $this->auditService->logUserAction(
                $creator,
                'featured_slot_requested',
                [
                    'request_id' => $request->id,
                    'egi_id' => $egi->id,
                    'feature_type' => $featureType,
                    'slot_start' => $slotStart->toDateTimeString(),
                    'slot_end' => $slotEnd->toDateTimeString(),
                    'egili_reserved' => $costEgili,
                ],
                GdprActivityCategory::BLOCKCHAIN_ACTIVITY // TODO: Use dedicated category
            );
            
            // ULM: Log success
            $this->logger->info('Slot request created successfully', [
                'creator_id' => $creator->id,
                'request_id' => $request->id,
                'egi_id' => $egi->id,
                'feature_type' => $featureType,
                'egili_reserved' => $costEgili,
                'log_category' => 'SLOT_REQUEST_SUCCESS'
            ]);
            
            // TODO: Send notification to admin (email/toast)
            
            return $request;
        });
    }
    
    /**
     * Schedule (approve) slot request
     * 
     * Admin approves request and schedules activation.
     * Egili are still RESERVED (spent only when cron activates).
     * 
     * @param UserFeaturePurchase $request Request to approve
     * @param User $admin Admin approving
     * @param Carbon $slotStart Confirmed start date (can differ from requested)
     * @param Carbon $slotEnd Confirmed end date
     * @param string|null $notes Admin notes
     * @return UserFeaturePurchase Updated request
     * @throws \Exception If slot unavailable or invalid
     */
    public function scheduleSlot(
        UserFeaturePurchase $request,
        User $admin,
        Carbon $slotStart,
        Carbon $slotEnd,
        ?string $notes = null
    ): UserFeaturePurchase {
        if ($request->status !== 'pending_approval') {
            throw new \Exception("Request must be pending_approval to schedule");
        }
        
        // Check slot availability
        $featureType = $request->metadata['feature_type'];
        $available = $this->checkSlotAvailability($featureType, $slotStart, $slotEnd);
        
        if (!$available['is_available']) {
            throw new \Exception(
                "Slot not available for {$featureType} from {$slotStart->toDateString()} to {$slotEnd->toDateString()}. " .
                "Current: {$available['current_count']}, Max: {$available['max_slots']}"
            );
        }
        
        // ULM: Log scheduling
        $this->logger->info('Admin scheduling slot', [
            'admin_id' => $admin->id,
            'request_id' => $request->id,
            'feature_type' => $featureType,
            'slot_start' => $slotStart->toDateTimeString(),
            'slot_end' => $slotEnd->toDateTimeString(),
            'log_category' => 'SLOT_SCHEDULE_START'
        ]);
        
        return DB::transaction(function () use ($request, $admin, $slotStart, $slotEnd, $notes) {
            // Update request
            $request->update([
                'status' => 'scheduled',
                'scheduled_slot_start' => $slotStart,
                'scheduled_slot_end' => $slotEnd,
                'approved_by_admin_id' => $admin->id,
                'approved_at' => now(),
                'rejection_reason' => null, // Clear any previous rejection
                'admin_notes' => $notes ?? $request->admin_notes,
            ]);
            
            // GDPR: Audit trail
            $this->auditService->logUserAction(
                $admin,
                'featured_slot_scheduled',
                [
                    'request_id' => $request->id,
                    'creator_id' => $request->user_id,
                    'egi_id' => $request->metadata['egi_id'],
                    'feature_type' => $request->metadata['feature_type'],
                    'slot_start' => $slotStart->toDateTimeString(),
                    'slot_end' => $slotEnd->toDateTimeString(),
                ],
                GdprActivityCategory::PERSONAL_DATA_UPDATE
            );
            
            // ULM: Log success
            $this->logger->info('Slot scheduled successfully', [
                'admin_id' => $admin->id,
                'request_id' => $request->id,
                'log_category' => 'SLOT_SCHEDULE_SUCCESS'
            ]);
            
            // TODO: Send notification to creator (email/toast)
            
            return $request->fresh();
        });
    }
    
    /**
     * Reject slot request
     * 
     * Admin rejects request.
     * Reserved Egili are RELEASED (no charge to creator).
     * 
     * @param UserFeaturePurchase $request Request to reject
     * @param User $admin Admin rejecting
     * @param string $reason Reason for rejection
     * @return UserFeaturePurchase Updated request
     */
    public function rejectSlot(
        UserFeaturePurchase $request,
        User $admin,
        string $reason
    ): UserFeaturePurchase {
        if ($request->status !== 'pending_approval') {
            throw new \Exception("Request must be pending_approval to reject");
        }
        
        // ULM: Log rejection
        $this->logger->info('Admin rejecting slot request', [
            'admin_id' => $admin->id,
            'request_id' => $request->id,
            'reason' => $reason,
            'log_category' => 'SLOT_REJECT_START'
        ]);
        
        return DB::transaction(function () use ($request, $admin, $reason) {
            // Update request
            $request->update([
                'status' => 'rejected',
                'rejection_reason' => $reason,
                'approved_by_admin_id' => $admin->id,
                'approved_at' => now(),
                'egili_reserved' => 0, // Release Egili (no charge)
            ]);
            
            // GDPR: Audit trail
            $this->auditService->logUserAction(
                $admin,
                'featured_slot_rejected',
                [
                    'request_id' => $request->id,
                    'creator_id' => $request->user_id,
                    'egi_id' => $request->metadata['egi_id'] ?? null,
                    'reason' => $reason,
                ],
                GdprActivityCategory::PERSONAL_DATA_UPDATE
            );
            
            // ULM: Log success
            $this->logger->info('Slot rejected successfully', [
                'admin_id' => $admin->id,
                'request_id' => $request->id,
                'log_category' => 'SLOT_REJECT_SUCCESS'
            ]);
            
            // TODO: Send notification to creator (email/toast)
            
            return $request->fresh();
        });
    }
    
    /**
     * Activate scheduled slots (CRON JOB)
     * 
     * Runs daily at 00:01.
     * Finds scheduled requests with slot_start <= now() and activates them:
     * - Spends reserved Egili
     * - Updates EGI featured_until/hyper_until
     * - Updates request status to active
     * 
     * @return int Count of slots activated
     */
    public function activateScheduledSlots(): int
    {
        $this->logger->info('Scheduled slot activation job started', [
            'log_category' => 'SLOT_ACTIVATION_START'
        ]);
        
        $requests = UserFeaturePurchase::where('status', 'scheduled')
            ->where('scheduled_slot_start', '<=', now())
            ->get();
        
        if ($requests->isEmpty()) {
            $this->logger->info('No slots to activate', [
                'log_category' => 'SLOT_ACTIVATION_NONE'
            ]);
            return 0;
        }
        
        $activated = 0;
        
        foreach ($requests as $request) {
            try {
                DB::transaction(function () use ($request) {
                    $creator = $request->user;
                    $egiId = $request->metadata['egi_id'] ?? null;
                    $featureType = $request->metadata['feature_type'];
                    
                    if (!$egiId) {
                        throw new \Exception("Missing egi_id in request metadata");
                    }
                    
                    $egi = Egi::find($egiId);
                    if (!$egi) {
                        throw new \Exception("EGI not found: {$egiId}");
                    }
                    
                    // Spend reserved Egili
                    $egiliTransaction = $this->egiliService->spend(
                        $creator,
                        $request->egili_reserved,
                        "featured_slot_activation_{$featureType}",
                        'featured_activation',
                        [
                            'request_id' => $request->id,
                            'egi_id' => $egiId,
                            'feature_type' => $featureType,
                        ]
                    );
                    
                    // Update EGI
                    if ($featureType === 'featured') {
                        $egi->update([
                            'featured_until' => $request->scheduled_slot_end,
                            'featured_by_admin_id' => $request->approved_by_admin_id,
                        ]);
                    } else {
                        $egi->update([
                            'hyper_until' => $request->scheduled_slot_end,
                            'hyper_activated_at' => now(),
                            'hyper_by_admin_id' => $request->approved_by_admin_id,
                        ]);
                    }
                    
                    // Update request
                    $request->update([
                        'status' => 'active',
                        'activated_at' => now(),
                        'amount_paid_egili' => $request->egili_reserved,
                        'egili_reserved' => 0, // No longer reserved
                        'metadata' => array_merge($request->metadata ?? [], [
                            'egili_transaction_id' => $egiliTransaction->id,
                            'activated_at' => now()->toDateTimeString(),
                        ]),
                    ]);
                    
                    // GDPR: Audit trail
                    $this->auditService->logUserAction(
                        $creator,
                        'featured_slot_activated',
                        [
                            'request_id' => $request->id,
                            'egi_id' => $egiId,
                            'feature_type' => $featureType,
                            'egili_spent' => $request->amount_paid_egili,
                        ],
                        GdprActivityCategory::BLOCKCHAIN_ACTIVITY
                    );
                    
                    $this->logger->info('Slot activated', [
                        'request_id' => $request->id,
                        'egi_id' => $egiId,
                        'feature_type' => $featureType,
                        'log_category' => 'SLOT_ACTIVATED'
                    ]);
                });
                
                $activated++;
            } catch (\Exception $e) {
                $this->logger->error('Failed to activate slot', [
                    'request_id' => $request->id,
                    'error' => $e->getMessage(),
                    'log_category' => 'SLOT_ACTIVATION_ERROR'
                ]);
            }
        }
        
        $this->logger->info('Scheduled slot activation job completed', [
            'activated_count' => $activated,
            'log_category' => 'SLOT_ACTIVATION_COMPLETE'
        ]);
        
        return $activated;
    }
    
    /**
     * Deactivate expired slots (CRON JOB)
     * 
     * Runs daily at 23:59.
     * Finds EGIs with featured_until/hyper_until <= now() and deactivates them.
     * 
     * @return int Count of slots deactivated
     */
    public function deactivateExpiredSlots(): int
    {
        $this->logger->info('Expired slot deactivation job started', [
            'log_category' => 'SLOT_DEACTIVATION_START'
        ]);
        
        $deactivated = 0;
        
        // Deactivate expired featured
        $expiredFeatured = Egi::whereNotNull('featured_until')
            ->where('featured_until', '<=', now())
            ->get();
        
        foreach ($expiredFeatured as $egi) {
            $egi->update([
                'featured_until' => null,
                'featured_by_admin_id' => null,
            ]);
            
            $this->logger->info('Featured slot deactivated', [
                'egi_id' => $egi->id,
                'log_category' => 'FEATURED_DEACTIVATED'
            ]);
            
            $deactivated++;
        }
        
        // Deactivate expired hyper
        $expiredHyper = Egi::whereNotNull('hyper_until')
            ->where('hyper_until', '<=', now())
            ->get();
        
        foreach ($expiredHyper as $egi) {
            $egi->update([
                'hyper_until' => null,
                'hyper_activated_at' => null,
                'hyper_by_admin_id' => null,
            ]);
            
            $this->logger->info('Hyper slot deactivated', [
                'egi_id' => $egi->id,
                'log_category' => 'HYPER_DEACTIVATED'
            ]);
            
            $deactivated++;
        }
        
        $this->logger->info('Expired slot deactivation job completed', [
            'deactivated_count' => $deactivated,
            'log_category' => 'SLOT_DEACTIVATION_COMPLETE'
        ]);
        
        return $deactivated;
    }
    
    /**
     * Check slot availability
     * 
     * @param string $featureType 'featured' or 'hyper'
     * @param Carbon $slotStart
     * @param Carbon $slotEnd
     * @return array Availability info
     */
    public function checkSlotAvailability(
        string $featureType,
        Carbon $slotStart,
        Carbon $slotEnd
    ): array {
        // Get max concurrent slots from pricing config
        $featureCode = $featureType === 'featured' ? 'featured_home_7days' : 'hyper_mode_7days';
        $pricing = DB::table('ai_feature_pricing')
            ->where('feature_code', $featureCode)
            ->first();
        
        $maxSlots = $pricing->max_concurrent_slots ?? 3; // Default 3
        
        // Count overlapping active/scheduled slots
        $currentCount = UserFeaturePurchase::where('feature_code', $featureCode)
            ->whereIn('status', ['active', 'scheduled'])
            ->where(function($query) use ($slotStart, $slotEnd) {
                $query->whereBetween('scheduled_slot_start', [$slotStart, $slotEnd])
                      ->orWhereBetween('scheduled_slot_end', [$slotStart, $slotEnd])
                      ->orWhere(function($q) use ($slotStart, $slotEnd) {
                          $q->where('scheduled_slot_start', '<=', $slotStart)
                            ->where('scheduled_slot_end', '>=', $slotEnd);
                      });
            })
            ->count();
        
        return [
            'is_available' => $currentCount < $maxSlots,
            'current_count' => $currentCount,
            'max_slots' => $maxSlots,
            'available_slots' => max(0, $maxSlots - $currentCount),
        ];
    }
    
    /**
     * Get pending requests (for admin queue)
     * 
     * @return Collection<UserFeaturePurchase>
     */
    public function getPendingRequests(): Collection
    {
        return UserFeaturePurchase::where('status', 'pending_approval')
            ->whereIn('feature_code', ['featured_home_7days', 'hyper_mode_7days'])
            ->with(['user', 'source'])
            ->orderBy('created_at', 'asc')
            ->get();
    }
}

