<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @Oracode Wallet Model: Unified wallet management with encryption
 * 🎯 Purpose: Gestione wallet Algorand + IBAN con encryption per collection
 * 🛡️ Security: Envelope encryption per mnemonics, encrypted IBAN
 * 💡 Flexibility: Supporta wallet per utenti E non-utenti (user_id nullable)
 *
 * @package App\Models
 * @version 2.0.0 (FlorenceEGI - Secure Wallet Module)
 */
class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        // ═══ RELATIONSHIPS ═══
        'collection_id',
        'user_id', // NULLABLE - supporta wallet per non-utenti!
        'notification_payload_wallets_id',

        // ═══ BUSINESS LOGIC ═══
        'wallet', // Algorand address pubblico
        'royalty_mint',
        'royalty_rebind',
        'is_anonymous',
        'metadata',
        'platform_role',
        'stripe_account_id',
        'paypal_merchant_id',

        // ═══ ENCRYPTION FIELDS ═══
        'secret_ciphertext', // Mnemonic cifrata
        'secret_nonce',
        'secret_tag',
        'dek_encrypted', // JSON
        'cipher_algo',

        // ═══ IBAN FIELDS ═══
        'iban_encrypted',
        'iban_hash',
        'iban_last4',

        // ═══ EGILI TOKEN BALANCE ═══
        // 'egili_balance', // SECURITY: Managed via EgiliService only
        // 'egili_lifetime_earned',
        // 'egili_lifetime_spent',


        // ═══ VERSIONING ═══
        'version',
        'wallet_type',
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
        'metadata' => 'array',
        'iban_encrypted' => 'encrypted', // Laravel encrypted cast
        'egili_balance' => 'integer',
        'egili_lifetime_earned' => 'integer',
        'egili_lifetime_spent' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Hidden attributes for array/JSON serialization
     * SECURITY: Never expose these fields
     */
    protected $hidden = [
        'secret_ciphertext',
        'secret_nonce',
        'secret_tag',
        'dek_encrypted',
        'iban_encrypted',
        'iban_hash',
        'stripe_account_id',
        'paypal_merchant_id',
    ];

    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // /**
    //  * Relazione uno a molti con NotificationPayloadWallet
    //  *
    //  * @return \Illuminate\Database\Eloquent\Relations\HasMany
    //  */
    // public function notificationPayloadWallets()
    // {
    //     return $this->hasMany(NotificationPayloadWallet::class, 'id', 'notification_payload_wallets_id');
    // }

    public function notificationPayloadWallet()
    {
        return $this->belongsTo(NotificationPayloadWallet::class, 'notification_payload_wallets_id', 'id');
    }

    public function notificationPayload()
    {
        return $this->belongsTo(NotificationPayloadWallet::class, 'notification_payload_wallets_id', 'id');
    }

    /**
     * Get all Egili transactions for this wallet
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function egiliTransactions()
    {
        return $this->hasMany(EgiliTransaction::class, 'wallet_id');
    }

    /**
     * Get masked IBAN for display (only last 4 digits)
     *
     * @return string|null
     */
    public function getMaskedIbanAttribute(): ?string
    {
        if (!$this->iban_last4) {
            return null;
        }

        return '****' . $this->iban_last4;
    }

    /**
     * Check if wallet has encrypted mnemonic
     */
    public function hasMnemonic(): bool
    {
        return !empty($this->secret_ciphertext) && !empty($this->dek_encrypted);
    }

    /**
     * Check if wallet has IBAN
     */
    public function hasIban(): bool
    {
        return !empty($this->iban_encrypted);
    }

    /**
     * Check if this is a platform system wallet (EPP, Natan, Frangette)
     */
    public function isPlatformWallet(): bool
    {
        return in_array($this->platform_role, ['EPP', 'Natan', 'Frangette']);
    }

    /**
     * Check if this is a creator wallet
     */
    public function isCreatorWallet(): bool
    {
        return $this->platform_role === 'Creator';
    }
}
