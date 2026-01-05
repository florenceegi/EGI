<?php

namespace App\Services\Payment;

use App\Enums\Payment\OrderStatusEnum;
use App\Models\Order;
use App\Models\PaymentDistribution;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * DisputeHandler Service
 *
 * Handles Stripe dispute (chargeback) events:
 * - charge.dispute.created: Opens dispute, reverses transfers
 * - charge.dispute.closed: Resolves dispute based on outcome
 *
 * P1 FIX: Implements critical dispute handling for payment system integrity.
 *
 * @see /docs/architecture/PAYMENT_SYSTEM_ARCHITECTURE_v2_4_2.md Section 3.4 (P1 Fix)
 */
class DisputeHandler
{
    /**
     * Handle dispute created event
     *
     * When a chargeback is initiated:
     * 1. Mark Order as disputed
     * 2. Reverse all transfers for this payment
     * 3. Log for manual review if reversal fails
     *
     * @param array $dispute Stripe Dispute object
     * @return bool
     */
    public function handleDisputeCreated(array $dispute): bool
    {
        $paymentIntentId = $this->extractPaymentIntentId($dispute);
        $disputeId = $dispute['id'] ?? null;

        if (!$paymentIntentId || !$disputeId) {
            Log::error('DisputeHandler: Missing payment_intent_id or dispute_id', [
                'dispute' => $dispute,
            ]);
            return false;
        }

        Log::info('DisputeHandler: Processing dispute.created', [
            'dispute_id' => $disputeId,
            'payment_intent_id' => $paymentIntentId,
            'amount' => $dispute['amount'] ?? null,
            'reason' => $dispute['reason'] ?? null,
        ]);

        return DB::transaction(function () use ($paymentIntentId, $disputeId, $dispute) {
            // 1. Find and update Order
            $order = Order::where('payment_intent_id', $paymentIntentId)->first();

            if ($order) {
                $order->markAsDisputed($disputeId);
            }

            // 2. Reverse all completed transfers
            $this->reverseTransfersForDispute($paymentIntentId, $disputeId);

            // 3. Log audit trail
            Log::warning('DisputeHandler: Dispute opened - transfers reversed', [
                'dispute_id' => $disputeId,
                'payment_intent_id' => $paymentIntentId,
                'order_id' => $order?->id,
                'reason' => $dispute['reason'] ?? 'unknown',
                'amount' => $dispute['amount'] ?? 0,
            ]);

            return true;
        });
    }

    /**
     * Handle dispute closed event
     *
     * When a dispute is resolved:
     * - won: Restore transfers if possible, mark order as resolved
     * - lost: Keep transfers reversed, mark order as refunded
     * - needs_response: Requires manual action
     *
     * @param array $dispute Stripe Dispute object
     * @return bool
     */
    public function handleDisputeClosed(array $dispute): bool
    {
        $paymentIntentId = $this->extractPaymentIntentId($dispute);
        $disputeId = $dispute['id'] ?? null;
        $status = $dispute['status'] ?? 'unknown';

        if (!$paymentIntentId || !$disputeId) {
            Log::error('DisputeHandler: Missing payment_intent_id or dispute_id in closed event', [
                'dispute' => $dispute,
            ]);
            return false;
        }

        Log::info('DisputeHandler: Processing dispute.closed', [
            'dispute_id' => $disputeId,
            'payment_intent_id' => $paymentIntentId,
            'status' => $status,
        ]);

        $order = Order::where('payment_intent_id', $paymentIntentId)->first();

        switch ($status) {
            case 'won':
                return $this->handleDisputeWon($order, $disputeId, $paymentIntentId);

            case 'lost':
                return $this->handleDisputeLost($order, $disputeId, $paymentIntentId);

            case 'needs_response':
            case 'under_review':
            case 'warning_needs_response':
                Log::warning('DisputeHandler: Dispute requires manual action', [
                    'dispute_id' => $disputeId,
                    'status' => $status,
                    'order_id' => $order?->id,
                ]);
                return true;

            default:
                Log::warning('DisputeHandler: Unknown dispute status', [
                    'dispute_id' => $disputeId,
                    'status' => $status,
                ]);
                return true;
        }
    }

    /**
     * Handle winning a dispute
     *
     * @param Order|null $order
     * @param string $disputeId
     * @param string $paymentIntentId
     * @return bool
     */
    protected function handleDisputeWon(?Order $order, string $disputeId, string $paymentIntentId): bool
    {
        Log::info('DisputeHandler: Dispute won', [
            'dispute_id' => $disputeId,
            'order_id' => $order?->id,
        ]);

        // Update distribution records
        PaymentDistribution::where('payment_intent_id', $paymentIntentId)
            ->where('distribution_status', 'reversed')
            ->update([
                'distribution_status' => 'dispute_won',
                'dispute_resolution' => 'won',
                'dispute_resolved_at' => now(),
            ]);

        // Restore order status if applicable
        if ($order && $order->status === OrderStatusEnum::DISPUTED->value) {
            // Transition back to completed if it was completed before dispute
            $order->update([
                'status' => OrderStatusEnum::COMPLETED->value,
                'disputed' => false,
                'metadata' => array_merge($order->metadata ?? [], [
                    'dispute_resolution' => 'won',
                    'dispute_resolved_at' => now()->toISOString(),
                ]),
            ]);
        }

        return true;
    }

    /**
     * Handle losing a dispute (chargeback confirmed)
     *
     * @param Order|null $order
     * @param string $disputeId
     * @param string $paymentIntentId
     * @return bool
     */
    protected function handleDisputeLost(?Order $order, string $disputeId, string $paymentIntentId): bool
    {
        Log::warning('DisputeHandler: Dispute lost - chargeback confirmed', [
            'dispute_id' => $disputeId,
            'order_id' => $order?->id,
        ]);

        // Update distribution records
        PaymentDistribution::where('payment_intent_id', $paymentIntentId)
            ->whereIn('distribution_status', ['reversed', 'dispute_won'])
            ->update([
                'distribution_status' => 'dispute_lost',
                'dispute_resolution' => 'lost',
                'dispute_resolved_at' => now(),
            ]);

        // Mark order as refunded (chargeback is effectively a forced refund)
        if ($order) {
            $order->update([
                'status' => OrderStatusEnum::REFUNDED->value,
                'refunded' => true,
                'refunded_at' => now(),
                'refund_id' => $disputeId,  // Use dispute ID as refund reference
                'metadata' => array_merge($order->metadata ?? [], [
                    'dispute_resolution' => 'lost',
                    'dispute_resolved_at' => now()->toISOString(),
                    'chargeback_confirmed' => true,
                ]),
            ]);
        }

        return true;
    }

    /**
     * Reverse all transfers for a disputed payment
     *
     * @param string $paymentIntentId
     * @param string $disputeId
     * @return void
     */
    protected function reverseTransfersForDispute(string $paymentIntentId, string $disputeId): void
    {
        $distributions = PaymentDistribution::where('payment_intent_id', $paymentIntentId)
            ->whereNotNull('transfer_id')
            ->where('distribution_status', 'completed')
            ->get();

        if ($distributions->isEmpty()) {
            Log::info('DisputeHandler: No transfers to reverse for dispute', [
                'payment_intent_id' => $paymentIntentId,
                'dispute_id' => $disputeId,
            ]);
            return;
        }

        Log::info('DisputeHandler: Reversing transfers for dispute', [
            'payment_intent_id' => $paymentIntentId,
            'dispute_id' => $disputeId,
            'transfer_count' => $distributions->count(),
        ]);

        /** @var \Stripe\StripeClient $stripeClient */
        $stripeClient = app(\Stripe\StripeClient::class);

        foreach ($distributions as $distribution) {
            try {
                $reversal = $stripeClient->transfers->createReversal(
                    $distribution->transfer_id,
                    [
                        'description' => "Dispute reversal: {$disputeId}",
                        'metadata' => [
                            'dispute_id' => $disputeId,
                            'original_payment_intent' => $paymentIntentId,
                            'reversal_reason' => 'chargeback_dispute',
                        ],
                    ]
                );

                $distribution->update([
                    'distribution_status' => 'reversed',
                    'reversal_id' => $reversal->id,
                    'reversed_at' => now(),
                    'failure_reason' => null,
                ]);

                Log::info('DisputeHandler: Transfer reversed successfully', [
                    'transfer_id' => $distribution->transfer_id,
                    'reversal_id' => $reversal->id,
                    'dispute_id' => $disputeId,
                ]);

            } catch (\Exception $e) {
                // Log failure but don't throw - continue with other transfers
                $distribution->update([
                    'distribution_status' => 'reversal_failed',
                    'failure_reason' => $e->getMessage(),
                    'retry_count' => ($distribution->retry_count ?? 0) + 1,
                ]);

                Log::error('DisputeHandler: Transfer reversal failed', [
                    'transfer_id' => $distribution->transfer_id,
                    'dispute_id' => $disputeId,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Extract payment intent ID from dispute object
     *
     * @param array $dispute
     * @return string|null
     */
    protected function extractPaymentIntentId(array $dispute): ?string
    {
        // Direct payment_intent field
        if (!empty($dispute['payment_intent'])) {
            return $dispute['payment_intent'];
        }

        // Nested in charge object
        if (!empty($dispute['charge'])) {
            $charge = $dispute['charge'];
            if (is_array($charge) && !empty($charge['payment_intent'])) {
                return $charge['payment_intent'];
            }
            // If charge is a string ID, we'd need to fetch it - log and return null
            if (is_string($charge)) {
                Log::warning('DisputeHandler: Charge is string ID, cannot extract payment_intent', [
                    'charge_id' => $charge,
                ]);
            }
        }

        return null;
    }

    /**
     * Process Stripe webhook for dispute events
     *
     * @param array $event Full Stripe event object
     * @return bool
     */
    public function processStripeEvent(array $event): bool
    {
        $eventType = $event['type'] ?? '';
        $dispute = $event['data']['object'] ?? [];

        return match ($eventType) {
            'charge.dispute.created' => $this->handleDisputeCreated($dispute),
            'charge.dispute.closed' => $this->handleDisputeClosed($dispute),
            'charge.dispute.updated' => $this->handleDisputeUpdated($dispute),
            default => false,
        };
    }

    /**
     * Handle dispute updated event (status changes during dispute process)
     *
     * @param array $dispute
     * @return bool
     */
    protected function handleDisputeUpdated(array $dispute): bool
    {
        $disputeId = $dispute['id'] ?? null;
        $status = $dispute['status'] ?? 'unknown';

        Log::info('DisputeHandler: Dispute updated', [
            'dispute_id' => $disputeId,
            'status' => $status,
        ]);

        // Just log the update - actual handling happens on created/closed
        return true;
    }

    /**
     * Check if an event type is a dispute event
     *
     * @param string $eventType
     * @return bool
     */
    public static function isDisputeEvent(string $eventType): bool
    {
        return in_array($eventType, [
            'charge.dispute.created',
            'charge.dispute.closed',
            'charge.dispute.updated',
            'charge.dispute.funds_reinstated',
            'charge.dispute.funds_withdrawn',
        ], true);
    }
}
