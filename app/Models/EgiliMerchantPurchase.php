<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @Oracode Model: Egili Merchant Purchase
 * 🎯 Purpose: Represent a single Egili purchase transaction for merchant reporting
 * 🛡️ Privacy: Contains payment and user data - GDPR compliant
 * 🧱 Core Logic: Track complete purchase lifecycle from initiation to completion
 * 
 * Relationships:
 * - belongsTo User (buyer)
 * 
 * Key Features:
 * - Supports FIAT and Crypto payments
 * - Complete audit trail for merchant reconciliation
 * - Invoice generation support (FASE 2)
 * - Payment status tracking
 * 
 * @package App\Models
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Egili Purchase System)
 * @date 2025-11-02
 * @purpose Merchant purchase tracking for Egili sales
 * 
 * @property int $id
 * @property int $user_id
 * @property string $order_reference
 * @property int $egili_amount
 * @property float $egili_unit_price_eur
 * @property float $total_price_eur
 * @property string $payment_method
 * @property string $payment_provider
 * @property string|null $payment_external_id
 * @property string $payment_status
 * @property string|null $crypto_currency
 * @property float|null $crypto_amount
 * @property string|null $crypto_tx_hash
 * @property string|null $invoice_number
 * @property \Carbon\Carbon|null $invoice_issued_at
 * @property string|null $invoice_pdf_path
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string|null $notes
 * @property \Carbon\Carbon $purchased_at
 * @property \Carbon\Carbon|null $completed_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read User $user
 */
class EgiliMerchantPurchase extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'egili_merchant_purchases';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'order_reference',
        'egili_amount',
        'egili_unit_price_eur',
        'total_price_eur',
        'payment_method',
        'payment_provider',
        'payment_external_id',
        'payment_status',
        'crypto_currency',
        'crypto_amount',
        'crypto_tx_hash',
        'invoice_number',
        'invoice_issued_at',
        'invoice_pdf_path',
        'ip_address',
        'user_agent',
        'return_url',
        'notes',
        'purchased_at',
        'completed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'egili_amount' => 'integer',
        'egili_unit_price_eur' => 'decimal:4',
        'total_price_eur' => 'decimal:2',
        'crypto_amount' => 'decimal:8',
        'purchased_at' => 'datetime',
        'completed_at' => 'datetime',
        'invoice_issued_at' => 'datetime',
    ];

    /**
     * Get the user who made the purchase
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if purchase is completed
     *
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->payment_status === 'completed';
    }

    /**
     * Check if purchase is pending
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->payment_status === 'pending';
    }

    /**
     * Check if purchase failed
     *
     * @return bool
     */
    public function isFailed(): bool
    {
        return $this->payment_status === 'failed';
    }

    /**
     * Check if purchase was refunded
     *
     * @return bool
     */
    public function isRefunded(): bool
    {
        return $this->payment_status === 'refunded';
    }

    /**
     * Check if payment was made with FIAT
     *
     * @return bool
     */
    public function isFiatPayment(): bool
    {
        return $this->payment_method === 'fiat';
    }

    /**
     * Check if payment was made with Crypto
     *
     * @return bool
     */
    public function isCryptoPayment(): bool
    {
        return $this->payment_method === 'crypto';
    }

    /**
     * Check if invoice has been issued
     *
     * @return bool
     */
    public function hasInvoice(): bool
    {
        return !is_null($this->invoice_number);
    }

    /**
     * Get formatted total price
     *
     * @return string
     */
    public function getFormattedTotalAttribute(): string
    {
        return '€' . number_format($this->total_price_eur, 2, ',', '.');
    }

    /**
     * Get formatted unit price
     *
     * @return string
     */
    public function getFormattedUnitPriceAttribute(): string
    {
        return '€' . number_format($this->egili_unit_price_eur, 4, ',', '.');
    }

    /**
     * Generate next order reference
     *
     * @return string Format: EGIL-YYYY-NNNNNN
     */
    public static function generateOrderReference(): string
    {
        $year = now()->year;
        $prefix = "EGIL-{$year}-";
        
        // Get last order number for this year
        $lastOrder = self::where('order_reference', 'like', $prefix . '%')
            ->orderBy('order_reference', 'desc')
            ->first();
        
        if ($lastOrder) {
            // Extract number and increment
            $lastNumber = (int) substr($lastOrder->order_reference, -6);
            $nextNumber = $lastNumber + 1;
        } else {
            // First order of the year
            $nextNumber = 1;
        }
        
        return $prefix . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Scope: Only completed purchases
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompleted($query)
    {
        return $query->where('payment_status', 'completed');
    }

    /**
     * Scope: Only pending purchases
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    /**
     * Scope: Purchases within date range
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Carbon\Carbon $startDate
     * @param \Carbon\Carbon $endDate
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('purchased_at', [$startDate, $endDate]);
    }

    /**
     * Scope: Purchases by user
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}

