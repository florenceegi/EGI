<?php

namespace App\Enums\Payment;

/**
 * @package App\Enums\Payment
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Unified Wallet Architecture)
 * @date 2026-01-01
 * @purpose Define Order lifecycle states
 *
 * @rationale Implements the Order state machine from the architecture documents.
 *            Each status represents a checkpoint in the payment/minting flow.
 *
 * @context Order lifecycle:
 *          PENDING → PAID → SPLIT_DONE → MINTED → COMPLETED
 *                  ↓         ↓            ↓
 *               FAILED   SPLIT_FAILED  MINT_FAILED
 *                          ↓
 *               REFUNDED ← DISPUTED
 */
enum OrderStatusEnum: string
{
    // Happy path states
    case PENDING = 'pending';           // Order created, awaiting payment
    case PAID = 'paid';                 // Payment confirmed via webhook
    case SPLIT_DONE = 'split_done';     // All transfers executed successfully
    case MINTED = 'minted';             // EGI minted on blockchain
    case COMPLETED = 'completed';       // Order fully completed
    
    // Failure states
    case FAILED = 'failed';             // Payment failed
    case SPLIT_FAILED = 'split_failed'; // One or more transfers failed
    case MINT_FAILED = 'mint_failed';   // Blockchain minting failed
    
    // Reversal states
    case REFUNDED = 'refunded';         // Full refund processed
    case DISPUTED = 'disputed';         // Chargeback/dispute opened
    case CANCELLED = 'cancelled';       // Order cancelled before payment

    /**
     * Get human-readable label
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending Payment',
            self::PAID => 'Payment Received',
            self::SPLIT_DONE => 'Funds Distributed',
            self::MINTED => 'EGI Minted',
            self::COMPLETED => 'Completed',
            self::FAILED => 'Payment Failed',
            self::SPLIT_FAILED => 'Distribution Failed',
            self::MINT_FAILED => 'Minting Failed',
            self::REFUNDED => 'Refunded',
            self::DISPUTED => 'Disputed',
            self::CANCELLED => 'Cancelled',
        };
    }

    /**
     * Get CSS color class for UI display
     *
     * @return string
     */
    public function colorClass(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::PAID, self::SPLIT_DONE, self::MINTED => 'info',
            self::COMPLETED => 'success',
            self::FAILED, self::SPLIT_FAILED, self::MINT_FAILED => 'danger',
            self::REFUNDED, self::DISPUTED, self::CANCELLED => 'secondary',
        };
    }

    /**
     * Check if this status is a terminal (final) state
     *
     * @return bool
     */
    public function isTerminal(): bool
    {
        return in_array($this, [
            self::COMPLETED,
            self::FAILED,
            self::REFUNDED,
            self::CANCELLED,
        ], true);
    }

    /**
     * Check if this status represents a failure
     *
     * @return bool
     */
    public function isFailure(): bool
    {
        return in_array($this, [
            self::FAILED,
            self::SPLIT_FAILED,
            self::MINT_FAILED,
        ], true);
    }

    /**
     * Check if this status allows refund
     *
     * @return bool
     */
    public function allowsRefund(): bool
    {
        return in_array($this, [
            self::PAID,
            self::SPLIT_DONE,
            self::MINTED,
            self::COMPLETED,
            self::SPLIT_FAILED,
            self::MINT_FAILED,
        ], true);
    }

    /**
     * Get valid next statuses from current status
     *
     * @return array<OrderStatusEnum>
     */
    public function validTransitions(): array
    {
        return match ($this) {
            self::PENDING => [self::PAID, self::FAILED, self::CANCELLED],
            self::PAID => [self::SPLIT_DONE, self::SPLIT_FAILED, self::REFUNDED, self::DISPUTED],
            self::SPLIT_DONE => [self::MINTED, self::MINT_FAILED, self::REFUNDED, self::DISPUTED],
            self::MINTED => [self::COMPLETED, self::REFUNDED, self::DISPUTED],
            self::COMPLETED => [self::REFUNDED, self::DISPUTED],
            self::SPLIT_FAILED => [self::SPLIT_DONE, self::REFUNDED],  // Retry allowed
            self::MINT_FAILED => [self::MINTED, self::REFUNDED],      // Retry allowed
            self::DISPUTED => [self::REFUNDED, self::COMPLETED],       // Dispute resolved
            // Terminal states
            self::FAILED, self::REFUNDED, self::CANCELLED => [],
        };
    }

    /**
     * Check if transition to target status is valid
     *
     * @param OrderStatusEnum $target
     * @return bool
     */
    public function canTransitionTo(OrderStatusEnum $target): bool
    {
        return in_array($target, $this->validTransitions(), true);
    }

    /**
     * Get description for audit/logging purposes
     *
     * @return string
     */
    public function auditDescription(): string
    {
        return match ($this) {
            self::PENDING => 'Order created, waiting for payment confirmation',
            self::PAID => 'Payment confirmed via PSP webhook',
            self::SPLIT_DONE => 'All stakeholder transfers completed successfully',
            self::MINTED => 'EGI token minted on Algorand blockchain',
            self::COMPLETED => 'Order fully completed, EGI delivered to buyer',
            self::FAILED => 'Payment was declined or failed',
            self::SPLIT_FAILED => 'One or more stakeholder transfers failed',
            self::MINT_FAILED => 'Blockchain minting transaction failed',
            self::REFUNDED => 'Full refund processed, transfers reversed',
            self::DISPUTED => 'Chargeback or dispute opened by buyer',
            self::CANCELLED => 'Order cancelled before payment',
        };
    }
}
