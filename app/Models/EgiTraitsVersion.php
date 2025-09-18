<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * EgiTraitsVersion Model
 *
 * Audit trail for EGI traits changes with versioning.
 * Tracks when and who modified traits for transparency.
 *
 * @package FlorenceEGI\Models
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI CoA Pro System)
 * @date 2025-09-18
 *
 * @property int $id
 * @property int $egi_id Foreign key to EGI
 * @property int $version Version number
 * @property array $traits_json Snapshot of traits for versioning
 * @property int|null $created_by Who created this version
 * @property \Carbon\Carbon $created_at
 */
class EgiTraitsVersion extends Model {
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'egi_traits_version';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'egi_id',
        'version',
        'traits_json',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'traits_json' => 'array',
        'version' => 'integer',
        'created_at' => 'datetime',
    ];

    //--------------------------------------------------------------------------
    // Relationships
    //--------------------------------------------------------------------------

    /**
     * Get the EGI this version belongs to
     */
    public function egi(): BelongsTo {
        return $this->belongsTo(Egi::class);
    }

    /**
     * Get the user who created this version
     */
    public function creator(): BelongsTo {
        return $this->belongsTo(User::class, 'created_by');
    }

    //--------------------------------------------------------------------------
    // Scopes & Methods
    //--------------------------------------------------------------------------

    /**
     * Scope for getting latest version
     */
    public function scopeLatest($query) {
        return $query->orderBy('version', 'desc');
    }

    /**
     * Get next version number for an EGI
     */
    public static function getNextVersion(int $egiId): int {
        $latest = static::where('egi_id', $egiId)
            ->orderBy('version', 'desc')
            ->first();

        return $latest ? $latest->version + 1 : 1;
    }

    /**
     * Create new version for EGI traits
     */
    public static function createVersion(int $egiId, array $traitsData, ?int $createdBy = null): self {
        return static::create([
            'egi_id' => $egiId,
            'version' => static::getNextVersion($egiId),
            'traits_json' => $traitsData,
            'created_by' => $createdBy,
            'created_at' => now(),
        ]);
    }

    /**
     * Compare traits with another version
     */
    public function compareWith(EgiTraitsVersion $otherVersion): array {
        $currentTraits = $this->traits_json;
        $otherTraits = $otherVersion->traits_json;

        return [
            'added' => array_diff_key($currentTraits, $otherTraits),
            'removed' => array_diff_key($otherTraits, $currentTraits),
            'modified' => array_filter($currentTraits, function ($value, $key) use ($otherTraits) {
                return isset($otherTraits[$key]) && $otherTraits[$key] !== $value;
            }, ARRAY_FILTER_USE_BOTH),
        ];
    }

    /**
     * Get trait changes summary
     */
    public function getChangesSummary(): string {
        if ($this->version === 1) {
            return 'Initial version';
        }

        $previousVersion = static::where('egi_id', $this->egi_id)
            ->where('version', $this->version - 1)
            ->first();

        if (!$previousVersion) {
            return 'Version ' . $this->version;
        }

        $changes = $this->compareWith($previousVersion);
        $summary = [];

        if (!empty($changes['added'])) {
            $summary[] = count($changes['added']) . ' traits added';
        }

        if (!empty($changes['removed'])) {
            $summary[] = count($changes['removed']) . ' traits removed';
        }

        if (!empty($changes['modified'])) {
            $summary[] = count($changes['modified']) . ' traits modified';
        }

        return implode(', ', $summary) ?: 'No changes';
    }
}
