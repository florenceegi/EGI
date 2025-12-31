<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * UserPaymentMethod - Polymorphic payment method configuration
 * 
 * Stores payment method preferences for Users and Collections.
 * 
 * Available methods:
 * - 'stripe': Stripe Connect account
 * - 'egili': Internal Egili credits
 * - 'bank_transfer': Bank transfer with IBAN
 * 
 * Config examples:
 * - bank_transfer: { "iban": "IT60X0542...", "bic": "BPPIITRRXXX", "holder": "Mario Rossi" }
 * - stripe: { "account_status": "active" }
 * - egili: {}
 */
class UserPaymentMethod extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'user_payment_methods';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'payable_type',
        'payable_id',
        'method',
        'is_enabled',
        'is_default',
        'config',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_enabled' => 'boolean',
        'is_default' => 'boolean',
        'config' => 'encrypted:array',
    ];

    /**
     * Available payment methods
     */
    public const METHODS = [
        'stripe' => 'Stripe',
        'egili' => 'Egili',
        'bank_transfer' => 'Bank Transfer',
    ];

    /**
     * Get the parent payable model (User or Collection).
     */
    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope to get only enabled payment methods.
     */
    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }

    /**
     * Scope to get the default payment method.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Check if this method requires IBAN configuration.
     */
    public function requiresIban(): bool
    {
        return $this->method === 'bank_transfer';
    }

    /**
     * Get IBAN from config (for bank_transfer method).
     */
    public function getIban(): ?string
    {
        return $this->config['iban'] ?? null;
    }

    /**
     * Get BIC from config (for bank_transfer method).
     */
    public function getBic(): ?string
    {
        return $this->config['bic'] ?? null;
    }

    /**
     * Get account holder name from config (for bank_transfer method).
     */
    public function getHolder(): ?string
    {
        return $this->config['holder'] ?? null;
    }

    /**
     * Check if bank_transfer configuration is complete.
     */
    public function hasBankConfig(): bool
    {
        return $this->method === 'bank_transfer' 
            && !empty($this->config['iban']);
    }

    /**
     * Get human-readable method name.
     */
    public function getMethodNameAttribute(): string
    {
        return self::METHODS[$this->method] ?? $this->method;
    }
}
