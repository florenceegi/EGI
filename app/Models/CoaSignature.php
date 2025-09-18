<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * CoaSignature Model
 *
 * Digital and physical signatures associated with CoA.
 * Supports QES (Qualified Electronic Signature), wallet signatures, and autograph scans.
 *
 * @package FlorenceEGI\Models
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI CoA Pro System)
 * @date 2025-09-18
 *
 * @property int $id
 * @property string $coa_id ULID foreign key to CoA
 * @property string $kind Signature type (qes, wallet, autograph_scan)
 * @property string|null $provider QES provider (Namirial, InfoCert, etc.)
 * @property array|null $payload QES manifest or metadata
 * @property string|null $pubkey Public key for wallet signatures
 * @property string|null $signature_base64 Base64 encoded signature
 * @property \Carbon\Carbon $created_at
 */
class CoaSignature extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'coa_signatures';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'coa_id',
        'kind',
        'provider',
        'payload',
        'pubkey',
        'signature_base64',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'payload' => 'array',
        'created_at' => 'datetime',
    ];

    //--------------------------------------------------------------------------
    // Constants
    //--------------------------------------------------------------------------

    public const KIND_QES = 'qes';
    public const KIND_WALLET = 'wallet';
    public const KIND_AUTOGRAPH_SCAN = 'autograph_scan';

    // QES Providers
    public const PROVIDER_NAMIRIAL = 'namirial';
    public const PROVIDER_INFOCERT = 'infocert';
    public const PROVIDER_ARUBA = 'aruba';

    //--------------------------------------------------------------------------
    // Relationships
    //--------------------------------------------------------------------------

    /**
     * Get the CoA this signature belongs to
     */
    public function coa(): BelongsTo
    {
        return $this->belongsTo(Coa::class);
    }

    //--------------------------------------------------------------------------
    // Scopes & Methods
    //--------------------------------------------------------------------------

    /**
     * Scope for QES signatures
     */
    public function scopeQes($query)
    {
        return $query->where('kind', self::KIND_QES);
    }

    /**
     * Scope for wallet signatures
     */
    public function scopeWallet($query)
    {
        return $query->where('kind', self::KIND_WALLET);
    }

    /**
     * Scope for autograph scan signatures
     */
    public function scopeAutographScan($query)
    {
        return $query->where('kind', self::KIND_AUTOGRAPH_SCAN);
    }

    /**
     * Check if this is a QES signature
     */
    public function isQes(): bool
    {
        return $this->kind === self::KIND_QES;
    }

    /**
     * Check if this is a wallet signature
     */
    public function isWallet(): bool
    {
        return $this->kind === self::KIND_WALLET;
    }

    /**
     * Check if this is an autograph scan
     */
    public function isAutographScan(): bool
    {
        return $this->kind === self::KIND_AUTOGRAPH_SCAN;
    }

    /**
     * Get signature algorithm from payload
     */
    public function getAlgorithm(): ?string
    {
        if ($this->isWallet() && isset($this->payload['algo'])) {
            return $this->payload['algo'];
        }

        if ($this->isQes() && isset($this->payload['algorithm'])) {
            return $this->payload['algorithm'];
        }

        return null;
    }

    /**
     * Get signature timestamp from payload
     */
    public function getSignatureTimestamp(): ?\Carbon\Carbon
    {
        if (isset($this->payload['timestamp'])) {
            return \Carbon\Carbon::parse($this->payload['timestamp']);
        }

        return $this->created_at;
    }

    /**
     * Get human readable signature info
     */
    public function getDisplayInfo(): string
    {
        switch ($this->kind) {
            case self::KIND_QES:
                return "QES ({$this->provider})";
            case self::KIND_WALLET:
                $algo = $this->getAlgorithm() ?: 'ed25519';
                return "Wallet ({$algo})";
            case self::KIND_AUTOGRAPH_SCAN:
                return "Autograph Scan";
            default:
                return ucfirst($this->kind);
        }
    }
}
