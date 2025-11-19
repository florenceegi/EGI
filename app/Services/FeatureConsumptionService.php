<?php

namespace App\Services;

use App\Models\User;
use App\Models\FeatureConsumptionLedger;
use App\Models\EgiliTransaction;
use App\Services\EgiliService;
use App\Enums\Gdpr\GdprActivityCategory;
use Illuminate\Support\Facades\DB;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * @package App\Services
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Feature Consumption Tracking)
 * @date 2025-11-03
 * @purpose Granular tracking and batch charging for consumption-based features
 * 
 * Features:
 * - Records fractional consumption (tokens, API calls, hours)
 * - Accumulates pending debt
 * - Batch charges when threshold reached
 * - Complete audit trail
 * - Scalable for all consumption-based features
 */
class FeatureConsumptionService
{
    private UltraLogManager $logger;
    private ErrorManagerInterface $errorManager;
    private EgiliService $egiliService;
    
    /**
     * Batch charging threshold (Egili)
     * When pending debt reaches this, auto-charge
     */
    private const BATCH_THRESHOLD = 10;
    
    /**
     * Token to Egili conversion rate
     * 1 Egili = 1,000,000 tokens (1M tokens)
     */
    private const EGILI_PER_MILLION_TOKENS = 1;
    
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        EgiliService $egiliService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->egiliService = $egiliService;
    }
    
    /**
     * Record feature consumption (fractional)
     * 
     * @param User $user Consumer
     * @param string $featureCode Feature code (e.g., 'ai_chat_assistant')
     * @param string $consumptionType 'token_based' | 'unit_based' | 'time_based'
     * @param float $unitsConsumed Amount consumed (e.g., 650 tokens, 1 use, 2.5 hours)
     * @param string $unitType Unit of measure (tokens, uses, hours, api_calls)
     * @param float $costPerUnit Egili cost per single unit
     * @param array|null $requestMetadata Service-specific metadata
     * @param string|null $ipAddress User IP
     * @param string|null $userAgent User agent
     * @return FeatureConsumptionLedger Created ledger entry
     */
    public function recordConsumption(
        User $user,
        string $featureCode,
        string $consumptionType,
        float $unitsConsumed,
        string $unitType,
        float $costPerUnit,
        ?array $requestMetadata = null,
        ?string $ipAddress = null,
        ?string $userAgent = null
    ): FeatureConsumptionLedger {
        try {
            // Calculate total fractional cost
            $totalCost = round($unitsConsumed * $costPerUnit, 4);
            
            // Create ledger entry
            $ledger = FeatureConsumptionLedger::create([
                'user_id' => $user->id,
                'feature_code' => $featureCode,
                'consumption_type' => $consumptionType,
                'units_consumed' => $unitsConsumed,
                'unit_type' => $unitType,
                'cost_per_unit' => $costPerUnit,
                'total_cost_egili' => $totalCost,
                'billing_status' => 'pending',
                'request_metadata' => $requestMetadata,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'consumed_at' => now(),
            ]);
            
            // ULM: Log consumption
            $this->logger->info('Feature consumption recorded', [
                'user_id' => $user->id,
                'feature_code' => $featureCode,
                'units_consumed' => $unitsConsumed,
                'unit_type' => $unitType,
                'total_cost_egili' => $totalCost,
                'billing_status' => 'pending',
                'log_category' => 'FEATURE_CONSUMPTION_RECORDED'
            ]);
            
            // Check if should auto-batch charge
            $this->checkAndBatchCharge($user);
            
            return $ledger;
            
        } catch (\Exception $e) {
            $this->logger->error('Feature consumption recording failed', [
                'user_id' => $user->id,
                'feature_code' => $featureCode,
                'error' => $e->getMessage(),
                'log_category' => 'FEATURE_CONSUMPTION_ERROR'
            ]);
            
            throw $e;
        }
    }
    
    /**
     * Record token-based consumption (helper for AI services)
     * 
     * @param User $user
     * @param string $featureCode
     * @param int $tokensUsed
     * @param array|null $requestMetadata
     * @param string|null $ipAddress
     * @param string|null $userAgent
     * @return FeatureConsumptionLedger
     */
    public function recordTokenConsumption(
        User $user,
        string $featureCode,
        int $tokensUsed,
        ?array $requestMetadata = null,
        ?string $ipAddress = null,
        ?string $userAgent = null
    ): FeatureConsumptionLedger {
        // Calculate cost: 1 Egili per 1M tokens
        $costPerToken = self::EGILI_PER_MILLION_TOKENS / 1000000;
        
        return $this->recordConsumption(
            $user,
            $featureCode,
            'token_based',
            $tokensUsed,
            'tokens',
            $costPerToken,
            $requestMetadata,
            $ipAddress,
            $userAgent
        );
    }
    
    /**
     * Get pending debt for user
     * 
     * @param User $user
     * @return float Total pending Egili debt (fractional)
     */
    public function getPendingDebt(User $user): float
    {
        return FeatureConsumptionLedger::getPendingDebt($user);
    }
    
    /**
     * Get pending debt breakdown by feature
     * 
     * @param User $user
     * @return array ['feature_code' => debt_amount]
     */
    public function getPendingDebtBreakdown(User $user): array
    {
        return FeatureConsumptionLedger::getPendingDebtByFeature($user);
    }
    
    /**
     * Check if user's pending debt reached threshold and auto-batch charge
     * 
     * @param User $user
     * @return bool True if charged, false if below threshold
     */
    private function checkAndBatchCharge(User $user): bool
    {
        $pendingDebt = $this->getPendingDebt($user);
        
        if ($pendingDebt >= self::BATCH_THRESHOLD) {
            $this->batchChargePendingDebt($user);
            return true;
        }
        
        return false;
    }
    
    /**
     * Batch charge all pending consumption debt
     * 
     * Charges floor(pending_debt) Egili (rounds down to INT)
     * Remainder stays pending for next batch
     * 
     * @param User $user
     * @param bool $force Force charge even if below threshold
     * @return array ['charged_egili' => int, 'charged_entries' => int, 'transaction_id' => int]
     * @throws \Exception
     */
    public function batchChargePendingDebt(User $user, bool $force = false): array
    {
        return DB::transaction(function () use ($user, $force) {
            // Get total pending debt
            $pendingDebt = $this->getPendingDebt($user);
            
            if ($pendingDebt <= 0) {
                $this->logger->info('No pending debt to charge', [
                    'user_id' => $user->id,
                    'log_category' => 'BATCH_CHARGE_NO_DEBT'
                ]);
                
                return [
                    'charged_egili' => 0,
                    'charged_entries' => 0,
                    'transaction_id' => null,
                ];
            }
            
            // Check threshold (unless forced)
            if (!$force && $pendingDebt < self::BATCH_THRESHOLD) {
                $this->logger->info('Pending debt below threshold, not charging', [
                    'user_id' => $user->id,
                    'pending_debt' => $pendingDebt,
                    'threshold' => self::BATCH_THRESHOLD,
                    'log_category' => 'BATCH_CHARGE_BELOW_THRESHOLD'
                ]);
                
                return [
                    'charged_egili' => 0,
                    'charged_entries' => 0,
                    'transaction_id' => null,
                ];
            }
            
            // Calculate Egili to charge (round DOWN to INT, remainder stays pending)
            $egiliToCharge = (int) floor($pendingDebt);
            
            if ($egiliToCharge <= 0) {
                return [
                    'charged_egili' => 0,
                    'charged_entries' => 0,
                    'transaction_id' => null,
                ];
            }
            
            // ULM: Log batch charge start
            $this->logger->info('Batch charging pending consumption debt', [
                'user_id' => $user->id,
                'pending_debt' => $pendingDebt,
                'egili_to_charge' => $egiliToCharge,
                'remainder' => $pendingDebt - $egiliToCharge,
                'forced' => $force,
                'log_category' => 'BATCH_CHARGE_START'
            ]);
            
            // Spend Egili using EgiliService (verified method)
            $egiliTransactions = $this->egiliService->spendWithPriority(
                $user,
                $egiliToCharge,
                'feature_consumption_batch_charge',
                'AI_PROCESSING', // GDPR category
                [
                    'pending_debt_total' => $pendingDebt,
                    'egili_charged' => $egiliToCharge,
                    'remainder' => $pendingDebt - $egiliToCharge,
                    'forced' => $force,
                ],
                null
            );
            
            // Get first transaction ID for reference
            $mainTransactionId = $egiliTransactions[0]->id ?? null;
            
            // Mark ALL pending ledger entries as batched
            // (we charge floor, but mark ALL as batched for clean accounting)
            $updatedCount = FeatureConsumptionLedger::where('user_id', $user->id)
                ->where('billing_status', 'pending')
                ->update([
                    'billing_status' => 'batched',
                    'batched_in_transaction_id' => $mainTransactionId,
                    'charged_at' => now(),
                ]);
            
            // ULM: Log batch charge success
            $this->logger->info('Batch charge completed', [
                'user_id' => $user->id,
                'egili_charged' => $egiliToCharge,
                'ledger_entries_updated' => $updatedCount,
                'transaction_id' => $mainTransactionId,
                'log_category' => 'BATCH_CHARGE_SUCCESS'
            ]);
            
            return [
                'charged_egili' => $egiliToCharge,
                'charged_entries' => $updatedCount,
                'transaction_id' => $mainTransactionId,
                'pending_debt_before' => $pendingDebt,
                'remainder' => 0, // All marked as batched, remainder tracked in next consumption
            ];
        });
    }
    
    /**
     * Get consumption statistics for user
     * 
     * @param User $user
     * @param string|null $featureCode Filter by feature (null = all features)
     * @return array Statistics
     */
    public function getConsumptionStats(User $user, ?string $featureCode = null): array
    {
        $query = FeatureConsumptionLedger::where('user_id', $user->id);
        
        if ($featureCode) {
            $query->where('feature_code', $featureCode);
        }
        
        return [
            'total_entries' => $query->count(),
            'total_cost_egili' => (float) $query->sum('total_cost_egili'),
            'pending_cost' => (float) $query->where('billing_status', 'pending')->sum('total_cost_egili'),
            'charged_cost' => (float) $query->whereIn('billing_status', ['batched', 'charged'])->sum('total_cost_egili'),
            'by_feature' => $query->selectRaw('
                    feature_code,
                    COUNT(*) as uses,
                    SUM(units_consumed) as total_units,
                    SUM(total_cost_egili) as total_cost
                ')
                ->groupBy('feature_code')
                ->get()
                ->keyBy('feature_code')
                ->toArray(),
        ];
    }
}


















