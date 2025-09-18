<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * CoaSnapshot Model
 *
 * Immutable snapshot of EGI traits and metadata at CoA issue time.
 * This frozen data "makes faith" for the certificate.
 *
 * @package FlorenceEGI\Models
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI CoA Pro System)
 * @date 2025-09-18
 *
 * @property int $id
 * @property string $coa_id ULID foreign key to CoA
 * @property array $snapshot_json Frozen snapshot data
 * @property \Carbon\Carbon $created_at
 */
class CoaSnapshot extends Model {
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'coa_snapshot';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'coa_id',
        'snapshot_json',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'snapshot_json' => 'array',
        'created_at' => 'datetime',
    ];

    //--------------------------------------------------------------------------
    // Relationships
    //--------------------------------------------------------------------------

    /**
     * Get the CoA this snapshot belongs to
     */
    public function coa(): BelongsTo {
        return $this->belongsTo(Coa::class);
    }

    //--------------------------------------------------------------------------
    // Accessors & Methods
    //--------------------------------------------------------------------------

    /**
     * Get work information from snapshot
     */
    public function getWork(): array {
        return $this->snapshot_json['work'] ?? [];
    }

    /**
     * Get traits information from snapshot
     */
    public function getTraits(): array {
        return $this->snapshot_json['traits'] ?? [];
    }

    /**
     * Get images information from snapshot
     */
    public function getImages(): array {
        return $this->snapshot_json['images'] ?? [];
    }

    /**
     * Get issuer information from snapshot
     */
    public function getIssuer(): array {
        return $this->snapshot_json['issuer'] ?? [];
    }

    /**
     * Get declaration from snapshot
     */
    public function getDeclaration(): ?string {
        return $this->snapshot_json['declaration'] ?? null;
    }

    /**
     * Get serial from snapshot
     */
    public function getSerial(): ?string {
        return $this->snapshot_json['serial'] ?? null;
    }
}
