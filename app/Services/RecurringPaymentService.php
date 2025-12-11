<?php

namespace App\Services;

use App\Models\User;
use App\Models\RecurringSubscription;
use App\Services\EgiliService;
use Ultra\UltraLogManager\UltraLogManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RecurringPaymentService
{
    protected EgiliService $egiliService;
    protected UltraLogManager $logger;

    public function __construct(
        EgiliService $egiliService,
        UltraLogManager $logger
    ) {
        $this->egiliService = $egiliService;
        $this->logger = $logger;
    }

    /**
     * Register or update a recurring subscription
     *
     * @param User $user User subscribing
     * @param Model $subscribable Entity being subscribed to (e.g., Collection)
     * @param string $serviceType Identifier for the service
     * @param \DateTime $nextRenewalAt Date of next renewal
     * @param array $metadata Optional pricing/config snapshot
     * @return RecurringSubscription
     */
    public function registerSubscription(
        User $user,
        Model $subscribable,
        string $serviceType,
        $nextRenewalAt,
        array $metadata = []
    ): RecurringSubscription {
        return RecurringSubscription::updateOrCreate(
            [
                'user_id' => $user->id,
                'subscribable_type' => $subscribable->getMorphClass(),
                'subscribable_id' => $subscribable->id,
                'service_type' => $serviceType,
            ],
            [
                'status' => 'active',
                'next_renewal_at' => $nextRenewalAt,
                'failed_attempts' => 0, // Reset failures on manual re-subscription
                'metadata' => $metadata,
            ]
        );
    }

    /**
     * Cancel a subscription
     */
    public function cancelSubscription(User $user, Model $subscribable, string $serviceType): bool
    {
        $subscription = RecurringSubscription::where('user_id', $user->id)
            ->where('subscribable_type', $subscribable->getMorphClass())
            ->where('subscribable_id', $subscribable->id)
            ->where('service_type', $serviceType)
            ->first();

        if ($subscription) {
            $subscription->status = 'cancelled';
            $subscription->save();
            return true;
        }

        return false;
    }

    /**
     * Check if a subscription is active
     */
    public function hasActiveSubscription(User $user, Model $subscribable, string $serviceType): bool
    {
        return RecurringSubscription::where('user_id', $user->id)
            ->where('subscribable_type', $subscribable->getMorphClass())
            ->where('subscribable_id', $subscribable->id)
            ->where('service_type', $serviceType)
            ->where('status', 'active')
            ->exists();
    }

    /**
     * Toggle subscription status (Active/Cancelled)
     */
    public function toggleSubscriptionStatus(User $user, Model $subscribable, string $serviceType, bool $active): string
    {
        $subscription = RecurringSubscription::where('user_id', $user->id)
            ->where('subscribable_type', $subscribable->getMorphClass())
            ->where('subscribable_id', $subscribable->id)
            ->where('service_type', $serviceType)
            ->first();

        if ($subscription) {
            $subscription->status = $active ? 'active' : 'cancelled';
            $subscription->save();
            return $subscription->status;
        }

        return 'not_found';
    }

    /**
     * Process all due renewals
     * Main entry point for Cron Job
     */
    public function processDueRenewals()
    {
        $due = RecurringSubscription::dueForRenewal()->with(['user', 'subscribable'])->get();

        foreach ($due as $subscription) {
            $this->processRenewal($subscription);
        }
    }

    /**
     * Process a single renewal
     */
    protected function processRenewal(RecurringSubscription $subscription)
    {
        $user = $subscription->user;
        
        // TODO: Dynamically fetch cost based on service_type or metadata
        // For now, assume simple logic or fetch from metadata
        $amount = $subscription->metadata['cost_egili'] ?? 5000; // Default fallback
        $durationDays = $subscription->metadata['duration_days'] ?? 30;

        try {
            DB::beginTransaction();

            if ($this->egiliService->canSpend($user, $amount)) {
                // Perform Spend
                $this->egiliService->spend(
                    $user,
                    $amount,
                    $subscription->service_type . '_renewal',
                    'Auto-Renewal: ' . $subscription->service_type,
                    [
                        'subscription_id' => $subscription->id,
                        'subscribable_id' => $subscription->subscribable_id
                    ]
                );

                // Update Subscription
                $subscription->last_renewal_at = now();
                $subscription->next_renewal_at = now()->addDays($durationDays);
                $subscription->renewal_count++;
                $subscription->failed_attempts = 0;
                $subscription->save();

                // Create Transaction Record (Mirror logic from CollectionSubscriptionService if needed)
                // For now relying on egiliService->spend creating one side of the transaction
                // Ideally we'd call the specific service (CollectionSubscriptionService) to record the logic properly
                // But to keep it generic, we trust the Ledger.
                
                // If it's a Collection Subscription, we might need a specific AiCreditsTransaction
                // This is a trade-off of generic vs specific. 
                // SOLUTION: Use a factory strategy or event dispatcher?
                // For MVP: Check service_type and call specific logic if available, or just log.
                
                if ($subscription->service_type === 'collection_subscription') {
                     // Call CollectionSubscriptionService to finalizing the logical "Subscription" transaction creation
                     // This prevents code duplication and ensures completeness
                     $svc = app(\App\Services\CollectionSubscriptionService::class);
                     $svc->recordRenewalTransaction($user, $subscription->subscribable, $amount, $durationDays); 
                }

                DB::commit();
                
                $this->logger->info('Subscription renewed successfully', [
                    'subscription_id' => $subscription->id,
                    'user_id' => $user->id
                ]);

            } else {
                // Insufficient Funds
                $subscription->failed_attempts++;
                $subscription->last_failed_at = now();
                
                // Retry logic: suspend after 3 fails?
                if ($subscription->failed_attempts >= 3) {
                    $subscription->status = 'payment_failed'; // or suspended
                }
                
                $subscription->save();
                DB::commit(); // Commit the failure state update

                $this->logger->warning('Subscription renewal failed: Insufficient funds', [
                    'subscription_id' => $subscription->id,
                    'user_id' => $user->id
                ]);
            }

        } catch (\Throwable $e) {
            DB::rollBack();
            $this->logger->error('Subscription renewal system error', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
