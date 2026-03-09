<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

/**
 * @package App\Models
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Collection Subscription FIAT)
 * @date 2026-03-03
 * @purpose Model per abbonamenti FIAT Collection Company.
 *          Traccia ogni abbonamento pagato con Stripe/PayPal.
 *          MiCA-safe: Egili usati SOLO come sconto, mai come pagamento.
 *
 * @property int    $id
 * @property int    $collection_id
 * @property int    $user_id
 * @property string $feature_code
 * @property string $plan_tier
 * @property int|null $max_egis
 * @property string $payment_provider
 * @property string|null $provider_session_id
 * @property string|null $provider_subscription_id
 * @property string|null $provider_payment_intent_id
 * @property float  $amount_eur
 * @property int    $egili_discount_applied
 * @property float  $discount_amount_eur
 * @property string $status  pending|active|cancelled|expired|refunded|failed
 * @property \Carbon\Carbon|null $starts_at
 * @property \Carbon\Carbon|null $expires_at
 * @property bool   $auto_renew
 * @property \Carbon\Carbon|null $cancelled_at
 * @property array|null $metadata
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 */
class CollectionSubscription extends Model {
    use HasFactory, SoftDeletes;

    protected $table = 'collection_subscriptions';

    protected $fillable = [
        'collection_id',
        'user_id',
        'feature_code',
        'plan_tier',
        'max_egis',
        'payment_provider',
        'provider_session_id',
        'provider_subscription_id',
        'provider_payment_intent_id',
        'amount_eur',
        'egili_discount_applied',
        'discount_amount_eur',
        'status',
        'starts_at',
        'expires_at',
        'auto_renew',
        'cancelled_at',
        'metadata',
    ];

    protected $casts = [
        'max_egis'               => 'integer',
        'amount_eur'             => 'decimal:2',
        'egili_discount_applied' => 'integer',
        'discount_amount_eur'    => 'decimal:2',
        'auto_renew'             => 'boolean',
        'starts_at'              => 'datetime',
        'expires_at'             => 'datetime',
        'cancelled_at'           => 'datetime',
        'metadata'               => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relazioni
    |--------------------------------------------------------------------------
    */

    public function collection(): BelongsTo {
        return $this->belongsTo(Collection::class, 'collection_id');
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'user_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scope
    |--------------------------------------------------------------------------
    */

    /**
     * Scope: abbonamenti attivi (status=active e non ancora scaduti).
     */
    public function scopeActive(Builder $query): Builder {
        return $query
            ->where('status', 'active')
            ->where(function (Builder $q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * Scope: abbonamenti per una specifica collection.
     */
    public function scopeForCollection(Builder $query, int $collectionId): Builder {
        return $query->where('collection_id', $collectionId);
    }

    /**
     * Scope: abbonamenti completati (pagamento confermato).
     */
    public function scopeConfirmed(Builder $query): Builder {
        return $query->whereIn('status', ['active', 'expired', 'cancelled']);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Indica se questo abbonamento è correntemente attivo.
     */
    public function isActive(): bool {
        if ($this->status !== 'active') {
            return false;
        }

        if ($this->expires_at === null) {
            return true;
        }

        return $this->expires_at->isFuture();
    }

    /**
     * Indica se questo abbonamento è scaduto.
     */
    public function isExpired(): bool {
        if ($this->expires_at === null) {
            return false;
        }

        return $this->expires_at->isPast();
    }

    /**
     * Restituisce i giorni rimanenti alla scadenza.
     * Null se non ha scadenza, 0 se già scaduto.
     */
    public function daysRemaining(): ?int {
        if ($this->expires_at === null) {
            return null;
        }

        if ($this->expires_at->isPast()) {
            return 0;
        }

        return (int) now()->diffInDays($this->expires_at);
    }

    /**
     * Indica se c'è stato un sconto Egili applicato.
     */
    public function hasEgiliDiscount(): bool {
        return $this->egili_discount_applied > 0;
    }

    /**
     * Importo totale netto effettivamente pagato.
     */
    public function netAmountEur(): float {
        return max(0.0, (float) $this->amount_eur - (float) $this->discount_amount_eur);
    }
}
