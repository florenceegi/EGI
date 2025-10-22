<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * AiCreditsTransaction Model
 *
 * Rappresenta una transazione crediti AI:
 * - Purchase/refund
 * - Usage tracking
 * - Balance management
 * - Subscription tracking
 *
 * @package FlorenceEGI\Models
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0
 * @date 2025-10-22
 */
class AiCreditsTransaction extends Model
{
    protected $table = 'ai_credits_transactions';

    protected $fillable = [
        'user_id',
        'transaction_type',
        'operation',
        'amount',
        'balance_before',
        'balance_after',
        'source_type',
        'source_id',
        'source_model',
        'feature_used',
        'feature_parameters',
        'tokens_consumed',
        'ai_model',
        'subscription_tier',
        'discount_applied_percentage',
        'was_free_tier',
        'payment_method',
        'payment_transaction_id',
        'payment_amount',
        'currency',
        'credits_per_euro',
        'expires_at',
        'is_expired',
        'promo_code',
        'bonus_reason',
        'is_bonus',
        'admin_user_id',
        'admin_notes',
        'status',
        'error_message',
        'ip_address',
        'user_agent',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'integer',
        'balance_before' => 'integer',
        'balance_after' => 'integer',
        'source_id' => 'integer',
        'tokens_consumed' => 'integer',
        'feature_parameters' => 'array',
        'discount_applied_percentage' => 'decimal:2',
        'was_free_tier' => 'boolean',
        'payment_amount' => 'decimal:2',
        'credits_per_euro' => 'decimal:2',
        'expires_at' => 'date',
        'is_expired' => 'boolean',
        'is_bonus' => 'boolean',
        'metadata' => 'array',
    ];

    // === RELATIONSHIPS ===

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function adminUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    /**
     * Get the source (polymorphic relationship)
     */
    public function source(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'source_model', 'source_id');
    }

    // === TRANSACTION TYPE CHECKERS ===

    public function isPurchase(): bool
    {
        return $this->transaction_type === 'purchase';
    }

    public function isSubscription(): bool
    {
        return $this->transaction_type === 'subscription';
    }

    public function isBonus(): bool
    {
        return $this->transaction_type === 'bonus';
    }

    public function isRefund(): bool
    {
        return $this->transaction_type === 'refund';
    }

    public function isUsage(): bool
    {
        return $this->transaction_type === 'usage';
    }

    public function isExpiration(): bool
    {
        return $this->transaction_type === 'expiration';
    }

    public function isTransfer(): bool
    {
        return in_array($this->transaction_type, ['transfer_in', 'transfer_out']);
    }

    public function isAdminAdjustment(): bool
    {
        return $this->transaction_type === 'admin_adjustment';
    }

    // === OPERATION CHECKERS ===

    public function isAddition(): bool
    {
        return $this->operation === 'add';
    }

    public function isSubtraction(): bool
    {
        return $this->operation === 'subtract';
    }

    // === STATUS CHECKERS ===

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isRefunded(): bool
    {
        return $this->status === 'refunded';
    }

    // === FEATURE TYPE CHECKERS ===

    public function isTraitGeneration(): bool
    {
        return $this->source_type === 'ai_trait_generation';
    }

    public function isEgiAnalysis(): bool
    {
        return $this->source_type === 'ai_egi_analysis';
    }

    public function isPricingSuggestion(): bool
    {
        return $this->source_type === 'ai_pricing';
    }

    public function isMarketingAction(): bool
    {
        return $this->source_type === 'ai_marketing';
    }

    public function isDescription(): bool
    {
        return $this->source_type === 'ai_description';
    }

    public function isTranslation(): bool
    {
        return $this->source_type === 'ai_translation';
    }

    // === SCOPES ===

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeAdditions($query)
    {
        return $query->where('operation', 'add');
    }

    public function scopeSubtractions($query)
    {
        return $query->where('operation', 'subtract');
    }

    public function scopePurchases($query)
    {
        return $query->where('transaction_type', 'purchase');
    }

    public function scopeUsage($query)
    {
        return $query->where('transaction_type', 'usage');
    }

    public function scopeBonuses($query)
    {
        return $query->where('is_bonus', true);
    }

    public function scopeNotExpired($query)
    {
        return $query->where('is_expired', false);
    }

    public function scopeExpired($query)
    {
        return $query->where('is_expired', true);
    }

    public function scopeByFeature($query, string $featureUsed)
    {
        return $query->where('feature_used', $featureUsed);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereBetween('created_at', [
            now()->startOfMonth(),
            now()->endOfMonth()
        ]);
    }

    public function scopeLastMonth($query)
    {
        return $query->whereBetween('created_at', [
            now()->subMonth()->startOfMonth(),
            now()->subMonth()->endOfMonth()
        ]);
    }

    // === HELPERS ===

    public function getBalanceChange(): int
    {
        return $this->balance_after - $this->balance_before;
    }

    public function isPositiveTransaction(): bool
    {
        return $this->getBalanceChange() > 0;
    }

    public function isNegativeTransaction(): bool
    {
        return $this->getBalanceChange() < 0;
    }

    public function getCostInEuros(): ?float
    {
        if ($this->payment_amount) {
            return (float) $this->payment_amount;
        }

        if ($this->credits_per_euro && $this->amount) {
            return $this->amount / $this->credits_per_euro;
        }

        return null;
    }

    public function getEffectiveRate(): ?float
    {
        // Calculate effective credits per euro after discounts
        if (!$this->payment_amount || !$this->amount) {
            return null;
        }

        return $this->amount / $this->payment_amount;
    }

    public function getDiscountAmount(): ?float
    {
        if (!$this->discount_applied_percentage || !$this->payment_amount) {
            return null;
        }

        $originalAmount = $this->payment_amount / (1 - ($this->discount_applied_percentage / 100));
        return $originalAmount - $this->payment_amount;
    }

    public function getDaysUntilExpiration(): ?int
    {
        if (!$this->expires_at || $this->is_expired) {
            return null;
        }

        return now()->diffInDays($this->expires_at, false);
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        $daysUntil = $this->getDaysUntilExpiration();
        return $daysUntil !== null && $daysUntil <= $days && $daysUntil > 0;
    }

    public function getFeatureDescription(): string
    {
        $descriptions = [
            'ai_trait_generation' => 'Generazione Traits AI',
            'ai_egi_analysis' => 'Analisi Completa EGI',
            'ai_pricing' => 'Suggerimenti Pricing',
            'ai_marketing' => 'Azioni Marketing',
            'ai_description' => 'Generazione Descrizioni',
            'ai_translation' => 'Traduzioni AI',
            'payment' => 'Acquisto Crediti',
            'subscription_plan' => 'Piano Subscription',
            'promotion' => 'Promozione',
            'manual' => 'Operazione Manuale',
        ];

        return $descriptions[$this->source_type] ?? $this->source_type;
    }

    public function getTransactionTypeLabel(): string
    {
        $labels = [
            'purchase' => 'Acquisto',
            'subscription' => 'Subscription',
            'bonus' => 'Bonus',
            'refund' => 'Rimborso',
            'usage' => 'Utilizzo',
            'expiration' => 'Scadenza',
            'transfer_in' => 'Trasferimento In',
            'transfer_out' => 'Trasferimento Out',
            'admin_adjustment' => 'Correzione Admin',
        ];

        return $labels[$this->transaction_type] ?? $this->transaction_type;
    }

    // === STATIC HELPERS ===

    public static function getUserBalance(int $userId): int
    {
        $lastTransaction = static::forUser($userId)
            ->completed()
            ->orderBy('id', 'desc')
            ->first();

        return $lastTransaction ? $lastTransaction->balance_after : 0;
    }

    public static function getTotalCreditsEarned(int $userId): int
    {
        return static::forUser($userId)
            ->completed()
            ->additions()
            ->sum('amount');
    }

    public static function getTotalCreditsSpent(int $userId): int
    {
        return static::forUser($userId)
            ->completed()
            ->subtractions()
            ->sum('amount');
    }

    public static function getMonthlySpending(int $userId): int
    {
        return static::forUser($userId)
            ->completed()
            ->subtractions()
            ->thisMonth()
            ->sum('amount');
    }
}
