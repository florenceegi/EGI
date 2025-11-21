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
 * @purpose Represents an invoice item (line item) in the FlorenceEGI platform
 * 
 * @property int $id
 * @property int $invoice_id
 * @property string|null $code
 * @property string $description
 * @property float $quantity
 * @property float $unit_price_eur
 * @property float $tax_rate
 * @property float $tax_amount_eur
 * @property float $subtotal_eur
 * @property float $total_eur
 * @property int|null $egi_id
 * @property int|null $payment_distribution_id
 * @property array|null $metadata
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class InvoiceItem extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'invoice_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'invoice_id',
        'code',
        'description',
        'quantity',
        'unit_price_eur',
        'tax_rate',
        'tax_amount_eur',
        'subtotal_eur',
        'total_eur',
        'egi_id',
        'payment_distribution_id',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'invoice_id' => 'integer',
        'quantity' => 'decimal:2',
        'unit_price_eur' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount_eur' => 'decimal:2',
        'subtotal_eur' => 'decimal:2',
        'total_eur' => 'decimal:2',
        'egi_id' => 'integer',
        'payment_distribution_id' => 'integer',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the invoice that owns the item
     *
     * @return BelongsTo
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Get the EGI associated with this item (if any)
     *
     * @return BelongsTo
     */
    public function egi(): BelongsTo
    {
        return $this->belongsTo(Egi::class);
    }

    /**
     * Get the payment distribution associated with this item (if any)
     *
     * @return BelongsTo
     */
    public function paymentDistribution(): BelongsTo
    {
        return $this->belongsTo(PaymentDistribution::class);
    }

    /**
     * Calculate subtotal (quantity * unit_price)
     *
     * @return float
     */
    public function calculateSubtotal(): float
    {
        return round($this->quantity * $this->unit_price_eur, 2);
    }

    /**
     * Calculate tax amount (subtotal * tax_rate / 100)
     *
     * @return float
     */
    public function calculateTaxAmount(): float
    {
        $subtotal = $this->calculateSubtotal();
        return round($subtotal * $this->tax_rate / 100, 2);
    }

    /**
     * Calculate total (subtotal + tax)
     *
     * @return float
     */
    public function calculateTotal(): float
    {
        return round($this->calculateSubtotal() + $this->calculateTaxAmount(), 2);
    }

    /**
     * Recalculate and save all amounts
     *
     * @return void
     */
    public function recalculateAmounts(): void
    {
        $this->subtotal_eur = $this->calculateSubtotal();
        $this->tax_amount_eur = $this->calculateTaxAmount();
        $this->total_eur = $this->calculateTotal();
    }
}
