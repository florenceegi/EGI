<?php

namespace App\Models;

use App\Enums\Payment\EgiKindEnum;
use App\Enums\Payment\OrderStatusEnum;
use App\Enums\Payment\PaymentTypeEnum;
use App\Enums\Payment\TxKindEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * Order Model
 *
 * Central entity for tracking the complete purchase lifecycle:
 * payment → split → mint → delivery.
 *
 * @property int $id
 * @property string $uuid
 * @property int $buyer_id
 * @property int|null $egi_id
 * @property int $collection_id
 * @property string $tx_kind
 * @property string $status
 * @property string $payment_type
 * @property string|null $payment_intent_id
 * @property string $currency
 * @property int $amount_cents
 * @property float $amount_eur
 * @property bool $split_executed
 * @property \Carbon\Carbon|null $split_executed_at
 * @property bool $minted
 * @property \Carbon\Carbon|null $minted_at
 * @property string|null $mint_tx_id
 * @property bool $claimed
 * @property \Carbon\Carbon|null $claimed_at
 * @property string|null $claim_tx_id
 * @property string|null $failure_reason
 * @property int $retry_count
 * @property bool $refunded
 * @property \Carbon\Carbon|null $refunded_at
 * @property string|null $refund_id
 * @property bool $disputed
 * @property \Carbon\Carbon|null $disputed_at
 * @property string|null $dispute_id
 * @property array|null $metadata
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @property-read User $buyer
 * @property-read Egi|null $egi
 * @property-read Collection $collection
 * @property-read \Illuminate\Database\Eloquent\Collection<PaymentDistribution> $distributions
 */
class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'buyer_id',
        'egi_id',
        'collection_id',
        'tx_kind',
        'status',
        'payment_type',
        'payment_intent_id',
        'currency',
        'amount_cents',
        'amount_eur',
        'split_executed',
        'split_executed_at',
        'minted',
        'minted_at',
        'mint_tx_id',
        'claimed',
        'claimed_at',
        'claim_tx_id',
        'failure_reason',
        'retry_count',
        'refunded',
        'refunded_at',
        'refund_id',
        'disputed',
        'disputed_at',
        'dispute_id',
        'metadata',
    ];

    protected $casts = [
        'amount_cents' => 'integer',
        'amount_eur' => 'decimal:2',
        'split_executed' => 'boolean',
        'split_executed_at' => 'datetime',
        'minted' => 'boolean',
        'minted_at' => 'datetime',
        'claimed' => 'boolean',
        'claimed_at' => 'datetime',
        'retry_count' => 'integer',
        'refunded' => 'boolean',
        'refunded_at' => 'datetime',
        'disputed' => 'boolean',
        'disputed_at' => 'datetime',
        'metadata' => 'array',
    ];

    /* ========================================
     * BOOT METHODS
     * ======================================== */

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->uuid)) {
                $order->uuid = (string) Str::uuid();
            }
        });
    }

    /* ========================================
     * RELATIONSHIPS
     * ======================================== */

    /**
     * Get the buyer (user) for this order
     */
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /**
     * Get the EGI for this order
     */
    public function egi(): BelongsTo
    {
        return $this->belongsTo(Egi::class);
    }

    /**
     * Get the collection for this order
     */
    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class);
    }

    /**
     * Get the payment distributions for this order
     */
    public function distributions(): HasMany
    {
        return $this->hasMany(PaymentDistribution::class, 'payment_intent_id', 'payment_intent_id');
    }

    /* ========================================
     * ACCESSORS
     * ======================================== */

    /**
     * Get the TxKindEnum for this order
     */
    public function getTxKindEnumAttribute(): TxKindEnum
    {
        return TxKindEnum::from($this->tx_kind);
    }

    /**
     * Get the OrderStatusEnum for this order
     */
    public function getStatusEnumAttribute(): OrderStatusEnum
    {
        return OrderStatusEnum::from($this->status);
    }

    /**
     * Get the PaymentTypeEnum for this order
     */
    public function getPaymentTypeEnumAttribute(): PaymentTypeEnum
    {
        return PaymentTypeEnum::from($this->payment_type);
    }

    /**
     * Get the EgiKindEnum based on tx_kind
     */
    public function getEgiKindAttribute(): ?EgiKindEnum
    {
        return $this->txKindEnum->toEgiKind();
    }

    /* ========================================
     * STATE MACHINE METHODS
     * ======================================== */

    /**
     * Check if a status transition is valid
     */
    public function canTransitionTo(OrderStatusEnum $target): bool
    {
        return $this->statusEnum->canTransitionTo($target);
    }

    /**
     * Transition to a new status with validation
     */
    public function transitionTo(OrderStatusEnum $target): bool
    {
        if (!$this->canTransitionTo($target)) {
            throw new \InvalidArgumentException(
                "Invalid transition from {$this->status} to {$target->value}"
            );
        }

        return $this->update(['status' => $target->value]);
    }

    /**
     * Mark order as paid
     */
    public function markAsPaid(): bool
    {
        return $this->transitionTo(OrderStatusEnum::PAID);
    }

    /**
     * Mark split as executed
     */
    public function markSplitDone(): bool
    {
        $this->update([
            'split_executed' => true,
            'split_executed_at' => now(),
        ]);
        return $this->transitionTo(OrderStatusEnum::SPLIT_DONE);
    }

    /**
     * Mark as minted
     */
    public function markAsMinted(string $txId): bool
    {
        $this->update([
            'minted' => true,
            'minted_at' => now(),
            'mint_tx_id' => $txId,
        ]);
        return $this->transitionTo(OrderStatusEnum::MINTED);
    }

    /**
     * Mark as completed
     */
    public function markAsCompleted(): bool
    {
        return $this->transitionTo(OrderStatusEnum::COMPLETED);
    }

    /**
     * Mark as failed
     */
    public function markAsFailed(string $reason): bool
    {
        $this->update([
            'failure_reason' => $reason,
            'retry_count' => $this->retry_count + 1,
        ]);
        return $this->transitionTo(OrderStatusEnum::FAILED);
    }

    /**
     * Mark as refunded
     */
    public function markAsRefunded(string $refundId): bool
    {
        $this->update([
            'refunded' => true,
            'refunded_at' => now(),
            'refund_id' => $refundId,
        ]);
        return $this->transitionTo(OrderStatusEnum::REFUNDED);
    }

    /**
     * Mark as disputed
     */
    public function markAsDisputed(string $disputeId): bool
    {
        $this->update([
            'disputed' => true,
            'disputed_at' => now(),
            'dispute_id' => $disputeId,
        ]);
        return $this->transitionTo(OrderStatusEnum::DISPUTED);
    }

    /* ========================================
     * QUERY SCOPES
     * ======================================== */

    /**
     * Scope to filter by status
     */
    public function scopeWithStatus($query, OrderStatusEnum $status)
    {
        return $query->where('status', $status->value);
    }

    /**
     * Scope to filter by transaction kind
     */
    public function scopeOfKind($query, TxKindEnum $kind)
    {
        return $query->where('tx_kind', $kind->value);
    }

    /**
     * Scope to get pending orders
     */
    public function scopePending($query)
    {
        return $query->where('status', OrderStatusEnum::PENDING->value);
    }

    /**
     * Scope to get orders needing retry
     */
    public function scopeNeedsRetry($query)
    {
        return $query->whereIn('status', [
            OrderStatusEnum::SPLIT_FAILED->value,
            OrderStatusEnum::MINT_FAILED->value,
        ])->where('retry_count', '<', 3);
    }

    /**
     * Scope for buyer's orders
     */
    public function scopeForBuyer($query, int $userId)
    {
        return $query->where('buyer_id', $userId);
    }

    /* ========================================
     * HELPER METHODS
     * ======================================== */

    /**
     * Check if order is in a terminal state
     */
    public function isTerminal(): bool
    {
        return $this->statusEnum->isTerminal();
    }

    /**
     * Check if order allows refund
     */
    public function allowsRefund(): bool
    {
        return $this->statusEnum->allowsRefund();
    }

    /**
     * Check if order is completed successfully
     */
    public function isSuccessful(): bool
    {
        return $this->status === OrderStatusEnum::COMPLETED->value;
    }

    /**
     * Get the route key name (use UUID for URLs)
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
