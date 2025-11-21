<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @package App\Models
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Invoicing System)
 * @date 2025-11-21
 * @purpose Represents a monthly aggregation of sales for invoicing purposes
 * 
 * @property int $id
 * @property int $user_id
 * @property string $period_start
 * @property string $period_end
 * @property float $total_sales_eur
 * @property int $total_items
 * @property int $total_buyers
 * @property string $status
 * @property int|null $invoice_id
 * @property string|null $invoiced_at
 * @property string|null $export_format
 * @property string|null $export_path
 * @property string|null $exported_at
 * @property array|null $metadata
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class InvoiceAggregation extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'invoice_aggregations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'period_start',
        'period_end',
        'total_sales_eur',
        'total_items',
        'total_buyers',
        'status',
        'invoice_id',
        'invoiced_at',
        'export_format',
        'export_path',
        'exported_at',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'user_id' => 'integer',
        'period_start' => 'date',
        'period_end' => 'date',
        'total_sales_eur' => 'decimal:2',
        'total_items' => 'integer',
        'total_buyers' => 'integer',
        'invoice_id' => 'integer',
        'invoiced_at' => 'datetime',
        'exported_at' => 'datetime',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user (seller) that owns this aggregation
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the invoice generated from this aggregation (if any)
     *
     * @return BelongsTo
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Check if aggregation is pending
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if aggregation has been invoiced
     *
     * @return bool
     */
    public function isInvoiced(): bool
    {
        return $this->status === 'invoiced' && $this->invoice_id !== null;
    }

    /**
     * Check if aggregation has been exported
     *
     * @return bool
     */
    public function isExported(): bool
    {
        return $this->status === 'exported' && $this->export_path !== null;
    }

    /**
     * Check if aggregation is cancelled
     *
     * @return bool
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Mark as invoiced
     *
     * @param int $invoiceId
     * @return void
     */
    public function markAsInvoiced(int $invoiceId): void
    {
        $this->update([
            'status' => 'invoiced',
            'invoice_id' => $invoiceId,
            'invoiced_at' => now(),
        ]);
    }

    /**
     * Mark as exported
     *
     * @param string $format
     * @param string $path
     * @return void
     */
    public function markAsExported(string $format, string $path): void
    {
        $this->update([
            'status' => 'exported',
            'export_format' => $format,
            'export_path' => $path,
            'exported_at' => now(),
        ]);
    }

    /**
     * Mark as cancelled
     *
     * @return void
     */
    public function markAsCancelled(): void
    {
        $this->update([
            'status' => 'cancelled',
        ]);
    }

    /**
     * Scope to filter by user
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
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
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by period
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $startDate
     * @param string $endDate
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInPeriod($query, string $startDate, string $endDate)
    {
        return $query->where('period_start', '>=', $startDate)
                     ->where('period_end', '<=', $endDate);
    }

    /**
     * Scope to get pending aggregations
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
