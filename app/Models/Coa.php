<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * CoA (Certificate of Authenticity) Model
 *
 * Represents the core immutable certificate with snapshot, serial, and signatures.
 * Supports professional workflow with Core + Annexes architecture.
 *
 * @package FlorenceEGI\Models
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI CoA Pro System)
 * @date 2025-09-18
 *
 * @property string $id ULID primary key
 * @property int $egi_id Foreign key to EGI
 * @property string $serial Unique serial (COA-EGI-YYYY-000###)
 * @property string $status valid|revoked
 * @property string $issuer_type author|archive|platform
 * @property string $issuer_name Who issues the CoA
 * @property string|null $issuer_location Location of issuer
 * @property \Carbon\Carbon $issued_at Issue timestamp
 * @property \Carbon\Carbon|null $revoked_at Revocation timestamp
 * @property string|null $revoke_reason Reason for revocation
 */
class Coa extends Model {
    use HasFactory, HasUlids;

    /**
     * The table associated with the model.
     */
    protected $table = 'coa';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'egi_id',
        'serial',
        'status',
        'issuer_type',
        'issuer_name',
        'issuer_location',
        'location',
        'issued_at',
        'revoked_at',
        'revoke_reason',
        'verification_hash',
        'integrity_hash',
        'signature_data',
        'notes',
        'expires_at',
        'metadata',
        'creator_info',
        'qr_code_data',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'issued_at' => 'datetime',
        'revoked_at' => 'datetime',
        'expires_at' => 'datetime',
        'signature_data' => 'array',
        'metadata' => 'array',
        'creator_info' => 'array',
    ];

    //--------------------------------------------------------------------------
    // Relationships
    //--------------------------------------------------------------------------

    /**
     * Get the EGI this CoA belongs to
     */
    public function egi(): BelongsTo {
        return $this->belongsTo(Egi::class);
    }

    /**
     * Get the immutable snapshot for this CoA
     */
    public function snapshot(): HasOne {
        return $this->hasOne(CoaSnapshot::class);
    }

    /**
     * Get all files associated with this CoA
     */
    public function files(): HasMany {
        return $this->hasMany(CoaFile::class);
    }

    /**
     * Get all signatures for this CoA
     */
    public function signatures(): HasMany {
        return $this->hasMany(CoaSignature::class);
    }

    /**
     * Get all annexes for this CoA (Pro feature)
     */
    public function annexes(): HasMany {
        return $this->hasMany(CoaAnnex::class);
    }

    /**
     * Get all events for this CoA (Pro feature)
     */
    public function events(): HasMany {
        return $this->hasMany(CoaEvent::class)->orderBy('created_at', 'desc');
    }

    //--------------------------------------------------------------------------
    // Scopes & Methods
    //--------------------------------------------------------------------------

    /**
     * Scope for valid CoAs
     */
    public function scopeValid($query) {
        return $query->where('status', 'valid');
    }

    /**
     * Scope for revoked CoAs
     */
    public function scopeRevoked($query) {
        return $query->where('status', 'revoked');
    }

    /**
     * Check if CoA is valid
     */
    public function isValid(): bool {
        return $this->status === 'valid';
    }

    /**
     * Check if CoA is revoked
     */
    public function isRevoked(): bool {
        return $this->status === 'revoked';
    }

    /**
     * Get the main PDF file (core_pdf or pdf)
     */
    public function getMainPdf(): ?CoaFile {
        return $this->files()
            ->whereIn('kind', ['core_pdf', 'pdf'])
            ->orderByRaw("CASE WHEN kind = 'core_pdf' THEN 1 ELSE 2 END")
            ->first();
    }

    /**
     * Get the bundle PDF file (if exists)
     */
    public function getBundlePdf(): ?CoaFile {
        return $this->files()->where('kind', 'bundle_pdf')->first();
    }
}
