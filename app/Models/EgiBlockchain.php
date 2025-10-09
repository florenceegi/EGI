<?php

/**
 * @package App\Models
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Blockchain Integration)
 * @date 2025-10-07
 * @purpose EgiBlockchain model for NFT/ASA blockchain data (1:1 with Egi)
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class EgiBlockchain extends Model {
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'egi_blockchain';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        // Core relationship
        'egi_id',

        // Blockchain data
        'asa_id',
        'anchor_hash',
        'blockchain_tx_id',
        'platform_wallet',

        // Payment data (FIAT only - MiCA-SAFE)
        'payment_method',
        'psp_provider',
        'payment_reference',
        'paid_amount',
        'paid_currency',

        // Ownership data
        'ownership_type',
        'buyer_wallet',
        'buyer_user_id',

        // Certificate data
        'certificate_uuid',
        'certificate_path',
        'verification_url',

        // Reservation link
        'reservation_id',

        // Minting status
        'mint_status',
        'minted_at',
        'mint_error',

        // V2 crypto payments (future - CASP/EMI licensed)
        'merchant_psp_config',
        'crypto_payment_reference',
        'supports_crypto_payments',

        // Phase 2 Area 5: Metadata fields
        'metadata',
        'creator_display_name',
        'co_creator_display_name',
        'metadata_ipfs_cid',
        'metadata_last_updated_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'paid_amount' => 'decimal:2',
        'supports_crypto_payments' => 'boolean',
        'minted_at' => 'datetime',
        'merchant_psp_config' => 'array',
        'metadata' => 'array',
        'metadata_last_updated_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'mint_error',
        'merchant_psp_config',
    ];

    /**
     * Boot the model.
     */
    protected static function boot() {
        parent::boot();

        // Auto-generate certificate UUID on creation
        static::creating(function ($model) {
            if (empty($model->certificate_uuid)) {
                $model->certificate_uuid = (string) Str::uuid();
            }
        });
    }

    // ===== RELATIONSHIPS =====

    /**
     * Get the EGI that owns this blockchain record.
     */
    public function egi(): BelongsTo {
        return $this->belongsTo(Egi::class);
    }

    /**
     * Get the user who bought this EGI.
     */
    public function buyer(): BelongsTo {
        return $this->belongsTo(User::class, 'buyer_user_id');
    }

    /**
     * Get the reservation that led to this purchase (if any).
     */
    public function reservation(): BelongsTo {
        return $this->belongsTo(Reservation::class);
    }

    // ===== SCOPES =====

    /**
     * Scope to get only minted blockchain records.
     */
    public function scopeMinted(Builder $query): Builder {
        return $query->where('mint_status', 'minted');
    }

    /**
     * Scope to get pending blockchain records (unminted, queued, minting).
     */
    public function scopePending(Builder $query): Builder {
        return $query->whereIn('mint_status', ['unminted', 'minting_queued', 'minting']);
    }

    /**
     * Scope to get failed blockchain records.
     */
    public function scopeFailed(Builder $query): Builder {
        return $query->where('mint_status', 'failed');
    }

    /**
     * Scope to get blockchain records by ownership type.
     */
    public function scopeByOwnership(Builder $query, string $type): Builder {
        return $query->where('ownership_type', $type);
    }

    /**
     * Scope to get blockchain records by payment method.
     */
    public function scopeByPaymentMethod(Builder $query, string $method): Builder {
        return $query->where('payment_method', $method);
    }

    // ===== ACCESSORS & MUTATORS =====

    /**
     * Get the formatted paid amount with currency.
     */
    public function getFormattedAmountAttribute(): string {
        if ($this->paid_amount === null) {
            return 'N/A';
        }

        return number_format($this->paid_amount, 2) . ' ' . $this->paid_currency;
    }

    /**
     * Get human-readable mint status.
     */
    public function getMintStatusLabelAttribute(): string {
        return match ($this->mint_status) {
            'unminted' => 'Non Mintato',
            'minting_queued' => 'In Coda',
            'minting' => 'Minting in Corso',
            'minted' => 'Mintato',
            'failed' => 'Fallito',
            default => 'Sconosciuto',
        };
    }

    /**
     * Get human-readable ownership type.
     */
    public function getOwnershipTypeLabelAttribute(): string {
        return match ($this->ownership_type) {
            'treasury' => 'Deposito Piattaforma',
            'wallet' => 'Wallet Utente',
            default => 'Sconosciuto',
        };
    }

    /**
     * Check if the blockchain record is successfully minted.
     */
    public function isMinted(): bool {
        return $this->mint_status === 'minted' && !empty($this->asa_id);
    }

    /**
     * Check if the blockchain record is in a pending state.
     */
    public function isPending(): bool {
        return in_array($this->mint_status, ['unminted', 'minting_queued', 'minting']);
    }

    /**
     * Check if the blockchain record has failed.
     */
    public function hasFailed(): bool {
        return $this->mint_status === 'failed';
    }

    /**
     * Check if a certificate has been generated.
     */
    public function hasCertificate(): bool {
        return !empty($this->certificate_path) && !empty($this->verification_url);
    }

    /**
     * Get the public verification URL.
     */
    public function getVerificationUrl(): ?string {
        return $this->verification_url;
    }

    /**
     * Check if the asset is owned by a user wallet.
     */
    public function isOwnedByUser(): bool {
        return $this->ownership_type === 'wallet' && !empty($this->buyer_wallet);
    }

    /**
     * Check if the asset is held in treasury.
     */
    public function isInTreasury(): bool {
        return $this->ownership_type === 'treasury';
    }

    /**
     * Get blockchain explorer URL for the ASA (if minted).
     */
    public function getBlockchainExplorerUrl(): ?string {
        if (!$this->isMinted()) {
            return null;
        }

        // Algorand explorer URL for sandbox/testnet/mainnet
        $network = config('algorand.network', 'sandbox');
        $baseUrl = match ($network) {
            'mainnet' => 'https://explorer.perawallet.app/asset/',
            'testnet' => 'https://testnet.explorer.perawallet.app/asset/',
            default => null, // Sandbox doesn't have public explorer
        };

        return $baseUrl ? $baseUrl . $this->asa_id : null;
    }

    // ===== PHASE 2 AREA 5: METADATA METHODS =====

    /**
     * Get metadata as EgiMetadataStructure object.
     *
     * @return \App\DataTransferObjects\EgiMetadataStructure|null
     */
    public function getMetadataStructure(): ?\App\DataTransferObjects\EgiMetadataStructure {
        if (empty($this->metadata)) {
            return null;
        }

        return \App\DataTransferObjects\EgiMetadataStructure::fromArray($this->metadata);
    }

    /**
     * Check if blockchain record has metadata.
     *
     * @return bool
     */
    public function hasMetadata(): bool {
        return !empty($this->metadata);
    }

    /**
     * Check if metadata includes CoA reference.
     *
     * @return bool
     */
    public function hasCoaReference(): bool {
        return !empty($this->metadata['coa_reference'] ?? null);
    }

    /**
     * Get standard traits from metadata.
     *
     * @return array
     */
    public function getTraits(): array {
        return $this->metadata['traits'] ?? [];
    }

    /**
     * Get CoA traits from metadata.
     *
     * @return array
     */
    public function getCoaTraits(): array {
        return $this->metadata['coa_traits'] ?? [];
    }

    /**
     * Get OpenSea-compatible attributes array.
     *
     * @return array
     */
    public function getOpenSeaAttributes(): array {
        return $this->metadata['attributes'] ?? [];
    }

    /**
     * Get IPFS gateway URL for metadata.
     *
     * @return string|null
     */
    public function getMetadataIpfsUrl(): ?string {
        if (empty($this->metadata_ipfs_cid)) {
            return null;
        }

        $gateway = config('ipfs.gateway_url', 'https://gateway.pinata.cloud');
        return "{$gateway}/ipfs/{$this->metadata_ipfs_cid}";
    }

    /**
     * Check if display names are frozen (immutable after mint).
     * Display names cannot be changed after minting completes.
     *
     * @return bool
     */
    public function areDisplayNamesFrozen(): bool {
        return $this->isMinted();
    }

    /**
     * Get creator display name (frozen at EGI creation).
     *
     * @return string|null
     */
    public function getCreatorDisplayName(): ?string {
        return $this->creator_display_name;
    }

    /**
     * Get co-creator display name (frozen at mint time).
     *
     * @return string|null
     */
    public function getCoCreatorDisplayName(): ?string {
        return $this->co_creator_display_name;
    }
}
