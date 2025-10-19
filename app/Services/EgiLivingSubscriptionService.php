<?php

namespace App\Services;

use App\Models\Egi;
use App\Models\EgiLivingSubscription;
use App\Models\User;
use App\Enums\EgiLivingStatus;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Services\Gdpr\AuditLogService;
use App\Enums\Gdpr\GdprActivityCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * @package App\Services
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Dual Architecture)
 * @date 2025-10-19
 * @purpose Service for managing EGI Vivente (Living) subscriptions and payments
 */
class EgiLivingSubscriptionService
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
     * Create a Living subscription for an EGI
     *
     * @param Egi $egi EGI to enable Living features for
     * @param User $user User purchasing the subscription
     * @param string $planType Plan type (one_time|monthly|yearly|lifetime)
     * @param string $paymentMethod Payment method (stripe|paypal|bank_transfer)
     * @return EgiLivingSubscription Created subscription (pending payment)
     * @throws \Exception
     */
    public function createSubscription(
        Egi $egi,
        User $user,
        string $planType = 'one_time',
        string $paymentMethod = 'stripe'
    ): EgiLivingSubscription {
        DB::beginTransaction();

        try {
            $this->logger->info('Creating Living subscription', [
                'egi_id' => $egi->id,
                'user_id' => $user->id,
                'plan_type' => $planType,
                'log_category' => 'LIVING_SUBSCRIPTION_CREATE'
            ]);

            // Validate plan type
            $plan = $this->getPlanConfig($planType);

            // Check if EGI already has an active subscription
            if ($egi->egi_living_enabled && $egi->livingSubscription) {
                throw new \Exception('EGI already has an active Living subscription');
            }

            // Create subscription record (pending payment)
            $subscription = EgiLivingSubscription::create([
                'egi_id' => $egi->id,
                'user_id' => $user->id,
                'status' => EgiLivingStatus::PENDING_PAYMENT->value,
                'plan_type' => $planType,
                'paid_amount' => $plan['price_eur'],
                'paid_currency' => 'EUR',
                'payment_method' => $paymentMethod,
                'enabled_features' => $plan['features'],
                'ai_analysis_interval' => config("egi_living.ai_trigger_intervals.{$plan['trigger_interval']}"),
                'expires_at' => $this->calculateExpirationDate($plan['duration_days']),
            ]);

            // GDPR: Audit trail
            $this->auditService->logUserAction(
                $user,
                'living_subscription_created',
                [
                    'egi_id' => $egi->id,
                    'subscription_id' => $subscription->id,
                    'plan_type' => $planType,
                ],
                GdprActivityCategory::BLOCKCHAIN_ACTIVITY
            );

            DB::commit();

            $this->logger->info('Living subscription created (pending payment)', [
                'subscription_id' => $subscription->id,
                'log_category' => 'LIVING_SUBSCRIPTION_CREATED'
            ]);

            return $subscription;
        } catch (\Exception $e) {
            DB::rollBack();

            $this->errorManager->handle('LIVING_SUBSCRIPTION_CREATE_FAILED', [
                'egi_id' => $egi->id,
                'user_id' => $user->id,
                'error_message' => $e->getMessage()
            ], $e);

            throw $e;
        }
    }

    /**
     * Complete payment for a subscription (called by PSP webhook)
     *
     * @param EgiLivingSubscription $subscription
     * @param string $paymentReference PSP transaction reference
     * @return EgiLivingSubscription Activated subscription
     * @throws \Exception
     */
    public function completePayment(
        EgiLivingSubscription $subscription,
        string $paymentReference
    ): EgiLivingSubscription {
        DB::beginTransaction();

        try {
            $this->logger->info('Completing Living subscription payment', [
                'subscription_id' => $subscription->id,
                'payment_reference' => $paymentReference,
                'log_category' => 'LIVING_SUBSCRIPTION_PAYMENT'
            ]);

            // Validate subscription status
            if ($subscription->status !== EgiLivingStatus::PENDING_PAYMENT->value) {
                throw new \Exception('Subscription is not pending payment');
            }

            // Update subscription
            $subscription->update([
                'status' => EgiLivingStatus::ACTIVE->value,
                'payment_reference' => $paymentReference,
                'paid_at' => now(),
                'activated_at' => now(),
            ]);

            // Enable Living features on EGI
            $subscription->egi->update([
                'egi_living_enabled' => true,
                'egi_living_activated_at' => now(),
                'egi_living_subscription_id' => $subscription->id,
            ]);

            // GDPR: Audit trail
            $this->auditService->logUserAction(
                $subscription->user,
                'living_subscription_activated',
                [
                    'subscription_id' => $subscription->id,
                    'egi_id' => $subscription->egi_id,
                    'payment_reference' => $paymentReference,
                ],
                GdprActivityCategory::BLOCKCHAIN_ACTIVITY
            );

            DB::commit();

            $this->logger->info('Living subscription activated', [
                'subscription_id' => $subscription->id,
                'log_category' => 'LIVING_SUBSCRIPTION_ACTIVATED'
            ]);

            return $subscription->fresh();
        } catch (\Exception $e) {
            DB::rollBack();

            $this->errorManager->handle('LIVING_SUBSCRIPTION_PAYMENT_FAILED', [
                'subscription_id' => $subscription->id,
                'error_message' => $e->getMessage()
            ], $e);

            throw $e;
        }
    }

    /**
     * Cancel a Living subscription
     *
     * @param EgiLivingSubscription $subscription
     * @param string|null $reason Cancellation reason
     * @param User $user User performing cancellation
     * @return EgiLivingSubscription Cancelled subscription
     * @throws \Exception
     */
    public function cancelSubscription(
        EgiLivingSubscription $subscription,
        ?string $reason,
        User $user
    ): EgiLivingSubscription {
        DB::beginTransaction();

        try {
            $this->logger->info('Cancelling Living subscription', [
                'subscription_id' => $subscription->id,
                'user_id' => $user->id,
                'log_category' => 'LIVING_SUBSCRIPTION_CANCEL'
            ]);

            // Cancel subscription
            $subscription->cancel($reason);

            // Disable Living features on EGI
            $subscription->egi->update([
                'egi_living_enabled' => false,
                'egi_living_subscription_id' => null,
            ]);

            // GDPR: Audit trail
            $this->auditService->logUserAction(
                $user,
                'living_subscription_cancelled',
                [
                    'subscription_id' => $subscription->id,
                    'egi_id' => $subscription->egi_id,
                    'reason' => $reason,
                ],
                GdprActivityCategory::BLOCKCHAIN_ACTIVITY
            );

            DB::commit();

            $this->logger->info('Living subscription cancelled', [
                'subscription_id' => $subscription->id,
                'log_category' => 'LIVING_SUBSCRIPTION_CANCELLED'
            ]);

            return $subscription->fresh();
        } catch (\Exception $e) {
            DB::rollBack();

            $this->errorManager->handle('LIVING_SUBSCRIPTION_CANCEL_FAILED', [
                'subscription_id' => $subscription->id,
                'error_message' => $e->getMessage()
            ], $e);

            throw $e;
        }
    }

    /**
     * Check and expire subscriptions that have passed their expiration date
     *
     * @return int Number of expired subscriptions
     */
    public function expireSubscriptions(): int
    {
        try {
            $this->logger->info('Checking for expired subscriptions', [
                'log_category' => 'LIVING_SUBSCRIPTION_EXPIRE_CHECK'
            ]);

            $expiredSubscriptions = EgiLivingSubscription::active()
                ->where('expires_at', '<=', now())
                ->whereNotNull('expires_at')
                ->get();

            $count = 0;

            foreach ($expiredSubscriptions as $subscription) {
                DB::beginTransaction();

                try {
                    $subscription->update([
                        'status' => EgiLivingStatus::EXPIRED->value,
                    ]);

                    // Disable Living features on EGI
                    $subscription->egi->update([
                        'egi_living_enabled' => false,
                        'egi_living_subscription_id' => null,
                    ]);

                    DB::commit();
                    $count++;
                } catch (\Exception $e) {
                    DB::rollBack();
                    $this->logger->error('Failed to expire subscription', [
                        'subscription_id' => $subscription->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $this->logger->info('Expired subscriptions processed', [
                'count' => $count,
                'log_category' => 'LIVING_SUBSCRIPTION_EXPIRED'
            ]);

            return $count;
        } catch (\Exception $e) {
            $this->logger->error('Subscription expiration check failed', [
                'error' => $e->getMessage(),
            ]);

            return 0;
        }
    }

    /**
     * Get plan configuration
     *
     * @param string $planType
     * @return array Plan config
     * @throws \Exception
     */
    private function getPlanConfig(string $planType): array
    {
        $plan = config("egi_living.subscription_plans.{$planType}");

        if (!$plan) {
            throw new \Exception("Unknown plan type: {$planType}");
        }

        return $plan;
    }

    /**
     * Calculate expiration date based on duration
     *
     * @param int|null $durationDays
     * @return \Carbon\Carbon|null
     */
    private function calculateExpirationDate(?int $durationDays): ?\Carbon\Carbon
    {
        if ($durationDays === null) {
            return null; // Lifetime
        }

        return now()->addDays($durationDays);
    }

    /**
     * Get available plans for display
     *
     * @return array Plans with pricing and features
     */
    public function getAvailablePlans(): array
    {
        return config('egi_living.subscription_plans');
    }
}
