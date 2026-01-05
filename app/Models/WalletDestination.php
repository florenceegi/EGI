<?php

namespace App\Models;

use App\Enums\Payment\PaymentTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

/**
 * WalletDestination Model
 *
 * Represents a payment destination for a wallet. Each wallet can have
 * multiple destinations (one per payment type), enabling scalable
 * payment method management.
 *
 * @property int $id
 * @property int $wallet_id
 * @property string $payment_type
 * @property string $destination_value
 * @property bool $is_verified
 * @property \Carbon\Carbon|null $verified_at
 * @property bool $is_primary
 * @property array|null $metadata
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @property-read Wallet $wallet
 * @property-read PaymentTypeEnum $paymentTypeEnum
 */
class WalletDestination extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'payment_type',
        'destination_value',
        'is_verified',
        'verified_at',
        'is_primary',
        'metadata',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'is_primary' => 'boolean',
        'metadata' => 'array',
    ];

    /**
     * Hide sensitive data from serialization
     */
    protected $hidden = [
        'destination_value',  // May contain encrypted IBAN or account IDs
    ];

    /* ========================================
     * RELATIONSHIPS
     * ======================================== */

    /**
     * Get the wallet this destination belongs to
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    /* ========================================
     * ACCESSORS & MUTATORS
     * ======================================== */

    /**
     * Get the PaymentTypeEnum for this destination
     */
    public function getPaymentTypeEnumAttribute(): PaymentTypeEnum
    {
        return PaymentTypeEnum::from($this->payment_type);
    }

    /**
     * Get decrypted destination value (for IBAN type)
     */
    public function getDecryptedValueAttribute(): ?string
    {
        if ($this->payment_type === PaymentTypeEnum::BANK_TRANSFER->value) {
            try {
                return Crypt::decryptString($this->destination_value);
            } catch (\Exception $e) {
                return null;
            }
        }
        return $this->destination_value;
    }

    /**
     * Set destination value (encrypt if IBAN)
     */
    public function setDestinationValueAttribute(string $value): void
    {
        if ($this->payment_type === PaymentTypeEnum::BANK_TRANSFER->value) {
            $this->attributes['destination_value'] = Crypt::encryptString($value);
        } else {
            $this->attributes['destination_value'] = $value;
        }
    }

    /* ========================================
     * VALIDATION METHODS
     * ======================================== */

    /**
     * Check if this destination is valid for payments
     */
    public function isValidForPayment(): bool
    {
        $enum = $this->paymentTypeEnum;
        return $enum->validateDestination($this->decrypted_value);
    }

    /**
     * Check if this destination supports automatic splits
     */
    public function supportsAutomaticSplit(): bool
    {
        return $this->paymentTypeEnum->supportsAutomaticSplit();
    }

    /* ========================================
     * QUERY SCOPES
     * ======================================== */

    /**
     * Scope to filter by payment type
     */
    public function scopeOfType($query, PaymentTypeEnum $type)
    {
        return $query->where('payment_type', $type->value);
    }

    /**
     * Scope to get verified destinations only
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope to get primary destinations only
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    /* ========================================
     * HELPER METHODS
     * ======================================== */

    /**
     * Mark this destination as verified
     */
    public function markAsVerified(): bool
    {
        return $this->update([
            'is_verified' => true,
            'verified_at' => now(),
        ]);
    }

    /**
     * Set as primary destination for this payment type
     */
    public function setAsPrimary(): bool
    {
        // Unset other primary destinations for the same wallet/type
        self::where('wallet_id', $this->wallet_id)
            ->where('payment_type', $this->payment_type)
            ->where('id', '!=', $this->id)
            ->update(['is_primary' => false]);

        return $this->update(['is_primary' => true]);
    }

    /**
     * Get a masked version of the destination for display
     */
    public function getMaskedDestinationAttribute(): string
    {
        $value = $this->decrypted_value;
        if (!$value) {
            return '***';
        }

        return match ($this->payment_type) {
            PaymentTypeEnum::STRIPE->value => 'acct_***' . substr($value, -4),
            PaymentTypeEnum::PAYPAL->value => substr($value, 0, 3) . '***' . substr($value, -3),
            PaymentTypeEnum::BANK_TRANSFER->value => substr($value, 0, 4) . '****' . substr($value, -4),
            PaymentTypeEnum::ALGORAND->value => substr($value, 0, 6) . '...' . substr($value, -6),
            default => '***',
        };
    }
}
