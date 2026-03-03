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
 * Collection Subscription Service (LEGACY)
 *
 * ⚠️  DEPRECATO — mantiene retrocompatibilità con abbonamenti antecedenti 2026-03-03.
 * Il nuovo sistema usa CollectionSubscriptionFiatService (pagamento FIAT + Stripe).
 *
 * Gestisce abbonamenti collection pagati in Egili (vecchio sistema ERRATO):
 * - Check active subscription (usa ai_credits_transactions)
 * - Process subscription payment con Egili (viola MiCA — NON usare per nuovi abbonamenti)
 * - Validate collection rights
 *
 * @deprecated Usare CollectionSubscriptionFiatService per nuovi abbonamenti.
 *
 * @package App\Services
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.1 (FlorenceEGI - Collection Subscription LEGACY)
 * @date 2025-11-21 (deprecato 2026-03-03)
 * @purpose LEGACY — retrocompatibilità vecchi abbonamenti Egili
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
     *
     * @deprecated Controlla solo le vecchie transazioni Egili in ai_credits_transactions.
     *             Usare CollectionSubscriptionFiatService::hasActiveSubscription() per
     *             il nuovo sistema FIAT (collection_subscriptions table).
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
     * Company users: ONLY subscription required (EPP is voluntary)
     * Other users: EPP support OR active subscription
     *
     * @param Collection $collection Collection to check
     * @return bool True if has rights
     *
     * @changelog 2025-12-05: Added company usertype handling - subscription only required
     */
    public function collectionHasRights(Collection $collection): bool {
        // Load creator relationship if not loaded
        if (!$collection->relationLoaded('creator')) {
            $collection->load('creator');
        }

        // Company users: ONLY subscription required (EPP is voluntary/optional)
        if ($collection->is_epp_voluntary || $collection->creator?->usertype === \App\Enums\User\MerchantUserTypeEnum::COMPANY->value) {
            $this->logger->debug('[CollectionSubscription] Company user - checking subscription only', [
                'collection_id' => $collection->id,
                'creator_usertype' => $collection->creator?->usertype,
                'is_epp_voluntary' => $collection->is_epp_voluntary,
                'log_category' => 'COLLECTION_RIGHTS_CHECK_COMPANY'
            ]);
            return $this->hasActiveSubscription($collection);
        }

        // Other users: Check EPP support first (highest priority)
        if ($collection->epp_project_id !== null) {
            return true;
        }

        // Fallback: Check active subscription
        return $this->hasActiveSubscription($collection);
    }

    /**
     * Check if collection requires subscription (company) vs EPP (others)
     *
     * @param Collection $collection Collection to check
     * @return array Rights requirements for this collection type
     */
    public function getRightsRequirements(Collection $collection): array {
        // Load creator relationship if not loaded
        if (!$collection->relationLoaded('creator')) {
            $collection->load('creator');
        }

        $isCompany = $collection->is_epp_voluntary || $collection->creator?->usertype === \App\Enums\User\MerchantUserTypeEnum::COMPANY->value;

        if ($isCompany) {
            return [
                'type' => 'company',
                'requires_subscription' => true,
                'requires_epp' => false,
                'epp_voluntary' => true,
                'has_epp_donation' => $collection->epp_project_id !== null && $collection->epp_donation_percentage > 0,
                'epp_donation_percentage' => $collection->epp_donation_percentage,
                'has_rights' => $this->hasActiveSubscription($collection),
            ];
        }

        return [
            'type' => 'standard',
            'requires_subscription' => false,
            'requires_epp' => true,
            'epp_voluntary' => false,
            'has_epp' => $collection->epp_project_id !== null,
            'has_rights' => $this->collectionHasRights($collection),
        ];
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
                'transaction' => $lastSubscription,
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
     *
     * @deprecated Implementazione ERRATA — usa Egili come mezzo di pagamento (viola MiCA).
     *             Sostituito da CollectionSubscriptionFiatService::initiatePayment().
     *             NON rimuovere: mantiene retrocompatibilità per vecchi abbonamenti.
     */
    public function processSubscription(User $user, Collection $collection, bool $autoRenew = true): array {
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

            // Register Recurring Subscription if requested
            if ($autoRenew) {
                try {
                    $recurringService = app(RecurringPaymentService::class);
                    $recurringService->registerSubscription(
                        $user, 
                        $collection, 
                        'collection_subscription', 
                        $expiresAt,
                        [
                            'cost_egili' => self::SUBSCRIPTION_COST_EGILI,
                            'duration_days' => self::SUBSCRIPTION_DURATION_DAYS
                        ]
                    );
                } catch (\Exception $e) {
                    // Log error but don't fail the main subscription
                    $this->logger->error('[CollectionSubscription] Failed to register auto-renew', [
                        'user_id' => $user->id,
                        'collection_id' => $collection->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

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
     *
     * @deprecated Prezzi hardcoded non aggiornati.
     *             Usare CollectionSubscriptionFiatService::getActivePlans() per dati live da ai_feature_pricing.
     */
    public function getSubscriptionPricing(): array {
        return [
            'cost_egili' => self::SUBSCRIPTION_COST_EGILI,
            'duration_days' => self::SUBSCRIPTION_DURATION_DAYS,
            'cost_eur_equivalent' => self::SUBSCRIPTION_COST_EGILI * 0.01, // 1 Egili = €0.01
        ];
    }
        /**
     * Record a renewal transaction (called by RecurringPaymentService)
     */
    public function recordRenewalTransaction(User $user, Collection $collection, int $amount, int $durationDays)
    {
        $expiresAt = now()->addDays($durationDays);
        
        // Retrieve the EgiliTransaction that was just created (hacky but effective for MVP)
        // Or better: Pass the EgiliTransaction object if refactored.
        // For MVP, we'll just create the AiCredits record.
        
        $egiliTransaction = \App\Models\EgiliTransaction::where('user_id', $user->id)
            ->where('amount', $amount)
            ->orderBy('created_at', 'desc')
            ->first();

        AiCreditsTransaction::create([
            'user_id' => $user->id,
            'transaction_type' => 'subscription',
            'operation' => 'subtract',
            'amount' => $amount,
            'balance_before' => $egiliTransaction?->balance_before ?? 0,
            'balance_after' => $egiliTransaction?->balance_after ?? 0,
            'source_type' => 'collection_subscription',
            'source_id' => $collection->id,
            'source_model' => 'App\\Models\\Collection',
            'feature_used' => 'collection_subscription_renewal',
            'feature_parameters' => json_encode([
                'collection_id' => $collection->id,
                'collection_name' => $collection->collection_name ?? $collection->name,
                'duration_days' => $durationDays,
                'auto_renewal' => true,
            ]),
            'subscription_tier' => 'collection_basic',
            'currency' => 'EGILI',
            'expires_at' => $expiresAt,
            'is_expired' => false,
            'status' => 'completed',
            'ip_address' => '127.0.0.1', // System (Null IP)
            'user_agent' => 'System/AutoRenewal',
            'metadata' => json_encode([
                'renewal' => true,
                'egili_transaction_id' => $egiliTransaction?->id,
            ]),
        ]);
        
        // Clear cache
        Cache::forget("collection_has_rights_{$collection->id}");
        
        $this->logger->info('[CollectionSubscription] Renewal recorded', [
            'collection_id' => $collection->id,
            'user_id' => $user->id,
            'expires_at' => $expiresAt
        ]);
    }
    /**
     * Check if collection can sell EGIs (Policy Rule #1)
     * If subscription expired or invalid -> NO SALES
     */
    public function canSellEgis(Collection $collection): bool
    {
        return $this->collectionHasRights($collection);
    }

    /**
     * Check if a plan is eligible for the collection (Downgrade Protection Policy)
     * Cannot subscribe to plan < existing EGI count
     */
    public function isPlanEligible(Collection $collection, int $planSize): bool
    {
        return $collection->egis()->count() <= $planSize;
    }
    /**
     * Cancel subscription and process refund
     *
     * @param User $user User requesting cancellation
     * @param Collection $collection Collection to cancel subscription for
     * @return array Result data
     *
     * @deprecated Cancellazione basata sulle vecchie transazioni Egili.
     *             Per i nuovi abbonamenti FIAT gestire cancellazione tramite Stripe/PayPal.
     */
    public function cancelSubscription(User $user, Collection $collection): array
    {
        // 1. Get active subscription transaction
        $lastSubscription = AiCreditsTransaction::where('source_model', 'App\\Models\\Collection')
            ->where('source_id', $collection->id)
            ->where('source_type', 'collection_subscription')
            ->where('transaction_type', 'subscription')
            ->where('status', 'completed')
            ->where('expires_at', '>', now())
            ->where('is_expired', false)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$lastSubscription) {
            return [
                'success' => false,
                'message' => 'No active subscription found to cancel.',
            ];
        }

        // 2. Determine Refund Logic
        // Rule A: Full Refund if < 24h OR No published EGIs ever
        $hoursSinceStart = $lastSubscription->created_at->diffInHours(now());
        $publishedEgisCount = $collection->egis()->where('status', 'published')->count();
        
        $isFullRefund = ($hoursSinceStart < 24) || ($publishedEgisCount === 0);
        
        $refundAmount = 0;
        
        if ($isFullRefund) {
            $refundAmount = $lastSubscription->amount;
        } else {
            // Rule B: Prorated Refund (Days Remaining)
            // Refund = Cost - (DailyCost * DaysUsed)
            // Effective Days Used = TotalDays - DaysRemaining
            $totalDays = self::SUBSCRIPTION_DURATION_DAYS;
            $daysRemaining = now()->diffInDays($lastSubscription->expires_at, false);
            
            if ($daysRemaining > 0) {
                $dailyCost = $lastSubscription->amount / $totalDays;
                $refundAmount = (int) round($daysRemaining * $dailyCost);
            }
        }

        DB::beginTransaction();
        try {
            // 3. Mark subscription as expired/cancelled
            $lastSubscription->is_expired = true;
            $lastSubscription->save(); // Or update status to 'cancelled' if field existed, but schema uses is_expired logic
            
            // Also cancel Recurring Subscription if exists
            $recurringService = app(RecurringPaymentService::class);
            $recurringService->cancelSubscription($user, $collection, 'collection_subscription');

            // 4. Process Refund if applicable
            if ($refundAmount > 0) {
                 $this->egiliService->earn(
                    $user,
                    $refundAmount,
                    'subscription_refund',
                    'refund',
                    [
                        'collection_id' => $collection->id,
                        'original_transaction_id' => $lastSubscription->id,
                        'refund_type' => $isFullRefund ? 'full' : 'prorated',
                        'egis_published_count' => $publishedEgisCount,
                        'description' => 'Refund for Collection Subscription Cancellation'
                    ],
                    $lastSubscription
                );
            }

            // 5. Clear Cache & Commit
            Cache::forget("collection_has_rights_{$collection->id}");
            DB::commit();

            $this->logger->info('[CollectionSubscription] Subscription cancelled', [
                'collection_id' => $collection->id,
                'user_id' => $user->id,
                'refund_amount' => $refundAmount,
                'refund_type' => $isFullRefund ? 'full' : 'prorated'
            ]);

            return [
                'success' => true,
                'message' => 'Subscription cancelled successfully.',
                'refund_amount' => $refundAmount,
                'refund_type' => $isFullRefund ? 'full' : 'prorated'
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            $this->logger->error('[CollectionSubscription] Cancellation failed', [
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => 'Error processing cancellation: ' . $e->getMessage()
            ];
        }
    }
}
