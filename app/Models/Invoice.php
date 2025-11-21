<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @package App\Models
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Invoicing System)
 * @date 2025-11-21
 * @purpose Represents an invoice in the FlorenceEGI platform
 * 
 * @property int $id
 * @property int $invoice_number
 * @property string $invoice_code
 * @property string $invoice_type
 * @property string $invoice_status
 * @property int $seller_user_id
 * @property int|null $buyer_user_id
 * @property string $issue_date
 * @property string|null $due_date
 * @property string|null $payment_date
 * @property float $subtotal_eur
 * @property float $tax_amount_eur
 * @property float $total_eur
 * @property string $currency
 * @property string|null $payment_method
 * @property string|null $notes
 * @property string|null $pdf_path
 * @property string|null $xml_path
 * @property string|null $sdi_id
 * @property string $sdi_status
 * @property string|null $sdi_sent_at
 * @property string|null $sdi_delivered_at
 * @property string|null $sdi_rejection_reason
 * @property string|null $external_system_id
 * @property string|null $external_system_name
 * @property string $managed_by
 * @property array|null $metadata
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 */
class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'invoices';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'invoice_number',
        'invoice_code',
        'invoice_type',
        'invoice_status',
        'seller_user_id',
        'buyer_user_id',
        'issue_date',
        'due_date',
        'payment_date',
        'subtotal_eur',
        'tax_amount_eur',
        'total_eur',
        'currency',
        'payment_method',
        'notes',
        'pdf_path',
        'xml_path',
        'sdi_id',
        'sdi_status',
        'sdi_sent_at',
        'sdi_delivered_at',
        'sdi_rejection_reason',
        'external_system_id',
        'external_system_name',
        'managed_by',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'invoice_number' => 'integer',
        'seller_user_id' => 'integer',
        'buyer_user_id' => 'integer',
        'issue_date' => 'date',
        'due_date' => 'date',
        'payment_date' => 'date',
        'subtotal_eur' => 'decimal:2',
        'tax_amount_eur' => 'decimal:2',
        'total_eur' => 'decimal:2',
        'sdi_sent_at' => 'datetime',
        'sdi_delivered_at' => 'datetime',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the seller (user who issues the invoice)
     *
     * @return BelongsTo
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_user_id');
    }

    /**
     * Get the buyer (user who receives the invoice)
     *
     * @return BelongsTo
     */
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_user_id');
    }

    /**
     * Get the invoice items
     *
     * @return HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * Get the invoice aggregation (if this invoice was generated from aggregation)
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function aggregation()
    {
        return $this->hasOne(InvoiceAggregation::class, 'invoice_id');
    }

    /**
     * Check if invoice is managed by platform
     *
     * @return bool
     */
    public function isManagedByPlatform(): bool
    {
        return $this->managed_by === 'platform';
    }

    /**
     * Check if invoice is managed by user external system
     *
     * @return bool
     */
    public function isManagedByUserExternal(): bool
    {
        return $this->managed_by === 'user_external';
    }

    /**
     * Check if invoice is draft
     *
     * @return bool
     */
    public function isDraft(): bool
    {
        return $this->invoice_status === 'draft';
    }

    /**
     * Check if invoice is paid
     *
     * @return bool
     */
    public function isPaid(): bool
    {
        return $this->invoice_status === 'paid';
    }

    /**
     * Check if invoice is cancelled
     *
     * @return bool
     */
    public function isCancelled(): bool
    {
        return $this->invoice_status === 'cancelled';
    }

    /**
     * Check if invoice is sent to SDI
     *
     * @return bool
     */
    public function isSentToSDI(): bool
    {
        return in_array($this->sdi_status, ['sent', 'delivered']);
    }

    /**
     * Check if invoice is delivered via SDI
     *
     * @return bool
     */
    public function isDeliveredViaSDI(): bool
    {
        return $this->sdi_status === 'delivered';
    }

    /**
     * Check if invoice is rejected by SDI
     *
     * @return bool
     */
    public function isRejectedBySDI(): bool
    {
        return $this->sdi_status === 'rejected';
    }

    /**
     * Scope to filter by seller
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForSeller($query, int $userId)
    {
        return $query->where('seller_user_id', $userId);
    }

    /**
     * Scope to filter by buyer
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForBuyer($query, int $userId)
    {
        return $query->where('buyer_user_id', $userId);
    }

    /**
     * Scope to filter by status
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('invoice_status', $status);
    }

    /**
     * Scope to filter by type
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('invoice_type', $type);
    }

    /**
     * Scope to filter by managed mode
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $managedBy
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeManagedBy($query, string $managedBy)
    {
        return $query->where('managed_by', $managedBy);
    }

    /**
     * Scope to filter by date range
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $startDate
     * @param string $endDate
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInDateRange($query, string $startDate, string $endDate)
    {
        return $query->whereBetween('issue_date', [$startDate, $endDate]);
    }
}
