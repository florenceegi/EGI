<?php

namespace App\Services;

use App\Models\User;
use App\Models\Collection;
use App\Models\AiCreditsTransaction;
use App\Helpers\FegiAuth;
use App\Services\EgiliService;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

/**
 * Collection Subscription Service
 *
 * Gestisce abbonamenti collection pagati in Egili:
 * - Check active subscription
 * - Process subscription payment
 * - Validate collection rights
 * - Integration con sistema Egili esistente
 *
 * @package App\Services
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Collection Subscription)
 * @date 2025-11-21
 * @purpose Implement collection subscription system with Egili payment
 */
class CollectionSubscriptionService {
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;
    protected EgiliService $egiliService;

    /**
     * Subscription pricing configuration
     * TODO: Move to config/collection-subscription.php or database
     */
    private const SUBSCRIPTION_COST_EGILI = 5000; // 5000 Egili per mese
    private const SUBSCRIPTION_DURATION_DAYS = 30; // 30 giorni

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
     * Check if collection has active subscription
     *
     * @param Collection $collection Collection to check
     * @return bool True if has active subscription
     */
    public function hasActiveSubscription(Collection $collection): bool {
        try {
            // Check last subscription transaction
            $lastSubscription = AiCreditsTransaction::where('source_model', 'App\\Models\\Collection')
                ->where('source_id', $collection->id)
                ->where('source_type', 'collection_subscription')
                ->where('transaction_type', 'subscription')
                ->where('status', 'completed')
                ->where('expires_at', '>', now())
                ->where('is_expired', false)
                ->orderBy('created_at', 'desc')
                ->first();

            return $lastSubscription !== null;
        } catch (\Exception $e) {
            $this->logger->error('[CollectionSubscription] Check subscription failed', [
                'collection_id' => $collection->id,
                'error' => $e->getMessage(),
                'log_category' => 'COLLECTION_SUBSCRIPTION_CHECK_FAILED'
            ]);

            return false;
        }
    }

    /**
     * Check if collection has rights (EPP support OR active subscription)
     *
     * @param Collection $collection Collection to check
     * @return bool True if has rights
     */
    public function collectionHasRights(Collection $collection): bool {
        // Check EPP support first (highest priority)
        if ($collection->epp_project_id !== null) {
            return true;
        }

        // Check active subscription
        return $this->hasActiveSubscription($collection);
    }

    /**
     * Get subscription status for collection
     *
     * @param Collection $collection Collection to check
     * @return array Status data
     */
    public function getSubscriptionStatus(Collection $collection): array {
        try {
            $lastSubscription = AiCreditsTransaction::where('source_model', 'App\\Models\\Collection')
                ->where('source_id', $collection->id)
                ->where('source_type', 'collection_subscription')
                ->where('transaction_type', 'subscription')
                ->where('status', 'completed')
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$lastSubscription) {
                return [
                    'has_subscription' => false,
                    'is_active' => false,
                    'expires_at' => null,
                    'days_remaining' => 0,
                ];
            }

            $isActive = !$lastSubscription->is_expired &&
                $lastSubscription->expires_at > now();

            $daysRemaining = $isActive ? now()->diffInDays($lastSubscription->expires_at, false) : 0;

            return [
                'has_subscription' => true,
                'is_active' => $isActive,
                'expires_at' => $lastSubscription->expires_at,
                'days_remaining' => (int) $daysRemaining,
                'last_payment_date' => $lastSubscription->created_at,
                'last_payment_amount' => $lastSubscription->amount,
            ];
        } catch (\Exception $e) {
            $this->logger->error('[CollectionSubscription] Get status failed', [
                'collection_id' => $collection->id,
                'error' => $e->getMessage(),
                'log_category' => 'COLLECTION_SUBSCRIPTION_STATUS_FAILED'
            ]);

            return [
                'has_subscription' => false,
                'is_active' => false,
                'expires_at' => null,
                'days_remaining' => 0,
            ];
        }
    }

    /**
     * Process subscription payment
     *
     * @param User $user User purchasing subscription
     * @param Collection $collection Collection to subscribe
     * @return array Result with success status and data
     */
    public function processSubscription(User $user, Collection $collection): array {
        DB::beginTransaction();

        try {
            $this->logger->info('[CollectionSubscription] Processing subscription', [
                'user_id' => $user->id,
                'collection_id' => $collection->id,
                'cost_egili' => self::SUBSCRIPTION_COST_EGILI,
                'log_category' => 'COLLECTION_SUBSCRIPTION_PROCESS_START'
            ]);

            // 1. Check user wallet balance via EgiliService
            if (!$this->egiliService->canSpend($user, self::SUBSCRIPTION_COST_EGILI)) {
                $currentBalance = $this->egiliService->getBalance($user);
                return [
                    'success' => false,
                    'message' => 'Insufficient Egili balance',
                    'required_egili' => self::SUBSCRIPTION_COST_EGILI,
                    'current_balance' => $currentBalance,
                    'missing_egili' => self::SUBSCRIPTION_COST_EGILI - $currentBalance,
                ];
            }

            // 2. Calculate expiration date
            $expiresAt = now()->addDays(self::SUBSCRIPTION_DURATION_DAYS);

            // 3. Deduct Egili via EgiliService (creates EgiliTransaction)
            $egiliTransaction = $this->egiliService->spend(
                $user,
                self::SUBSCRIPTION_COST_EGILI,
                'collection_subscription',
                'Collection Subscription Payment',
                [
                    'collection_id' => $collection->id,
                    'collection_name' => $collection->name,
                    'duration_days' => self::SUBSCRIPTION_DURATION_DAYS,
                ]
            );

            // 4. Create ai_credits_transaction for subscription tracking
            $transaction = AiCreditsTransaction::create([
                'user_id' => $user->id,
                'transaction_type' => 'subscription',
                'operation' => 'subtract',
                'amount' => self::SUBSCRIPTION_COST_EGILI,
                'balance_before' => $egiliTransaction->balance_before,
                'balance_after' => $egiliTransaction->balance_after,
                'source_type' => 'collection_subscription',
                'source_id' => $collection->id,
                'source_model' => 'App\\Models\\Collection',
                'feature_used' => 'collection_subscription',
                'feature_parameters' => json_encode([
                    'collection_id' => $collection->id,
                    'collection_name' => $collection->name,
                    'duration_days' => self::SUBSCRIPTION_DURATION_DAYS,
                    'egili_transaction_id' => $egiliTransaction->id,
                ]),
                'subscription_tier' => 'collection_basic',
                'currency' => 'EGILI',
                'credits_per_euro' => null,
                'expires_at' => $expiresAt,
                'is_expired' => false,
                'status' => 'completed',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'metadata' => json_encode([
                    'collection_id' => $collection->id,
                    'collection_name' => $collection->name,
                    'subscription_type' => 'monthly',
                    'egili_transaction_id' => $egiliTransaction->id,
                ]),
            ]);

            DB::commit();

            $this->logger->info('[CollectionSubscription] Subscription completed', [
                'user_id' => $user->id,
                'collection_id' => $collection->id,
                'transaction_id' => $transaction->id,
                'expires_at' => $expiresAt->toDateTimeString(),
                'log_category' => 'COLLECTION_SUBSCRIPTION_SUCCESS'
            ]);

            // Clear cache for collection rights
            Cache::forget("collection_has_rights_{$collection->id}");

            return [
                'success' => true,
                'message' => 'Subscription activated successfully',
                'transaction_id' => $transaction->id,
                'egili_transaction_id' => $egiliTransaction->id,
                'expires_at' => $expiresAt,
                'days_remaining' => self::SUBSCRIPTION_DURATION_DAYS,
                'new_balance' => $this->egiliService->getBalance($user),
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            $this->logger->error('[CollectionSubscription] Subscription failed', [
                'user_id' => $user->id,
                'collection_id' => $collection->id,
                'error' => $e->getMessage(),
                'log_category' => 'COLLECTION_SUBSCRIPTION_FAILED'
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get subscription pricing configuration
     *
     * @return array Pricing data
     */
    public function getSubscriptionPricing(): array {
        return [
            'cost_egili' => self::SUBSCRIPTION_COST_EGILI,
            'duration_days' => self::SUBSCRIPTION_DURATION_DAYS,
            'cost_eur_equivalent' => self::SUBSCRIPTION_COST_EGILI * 0.01, // 1 Egili = €0.01
        ];
    }
}
