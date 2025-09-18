<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * CoaAnnex Model
 *
 * Versioned annexes for professional CoA workflow.
 * Supports different annex types with independent versioning.
 *
 * @package FlorenceEGI\Models
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI CoA Pro System)
 * @date 2025-09-18
 *
 * @property string $id ULID primary key
 * @property string $coa_id ULID foreign key to CoA
 * @property string $code Annex type (A_PROVENANCE, B_CONDITION, C_EXHIBITIONS, D_PHOTOS)
 * @property int $version Version number for this annex type
 * @property string $path File path in storage
 * @property string $mime MIME type
 * @property int|null $bytes File size in bytes
 * @property string $sha256 SHA-256 hash
 * @property int|null $created_by Who created this version
 * @property \Carbon\Carbon $created_at
 */
class CoaAnnex extends Model {
    use HasFactory, HasUlids;

    /**
     * The table associated with the model.
     */
    protected $table = 'coa_annexes';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'coa_id',
        'code',
        'version',
        'path',
        'mime',
        'bytes',
        'sha256',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'version' => 'integer',
        'bytes' => 'integer',
        'created_at' => 'datetime',
    ];

    //--------------------------------------------------------------------------
    // Constants
    //--------------------------------------------------------------------------

    public const CODE_PROVENANCE = 'A_PROVENANCE';
    public const CODE_CONDITION = 'B_CONDITION';
    public const CODE_EXHIBITIONS = 'C_EXHIBITIONS';
    public const CODE_PHOTOS = 'D_PHOTOS';

    // Policy: re-issue required for these codes
    public const CRITICAL_CODES = [
        self::CODE_PROVENANCE,
        self::CODE_CONDITION,
    ];

    // Policy: addendum allowed for these codes
    public const ADDENDUM_CODES = [
        self::CODE_EXHIBITIONS,
        self::CODE_PHOTOS,
    ];

    //--------------------------------------------------------------------------
    // Relationships
    //--------------------------------------------------------------------------

    /**
     * Get the CoA this annex belongs to
     */
    public function coa(): BelongsTo {
        return $this->belongsTo(Coa::class);
    }

    /**
     * Get the user who created this annex version
     */
    public function creator(): BelongsTo {
        return $this->belongsTo(User::class, 'created_by');
    }

    //--------------------------------------------------------------------------
    // Scopes & Methods
    //--------------------------------------------------------------------------

    /**
     * Scope for latest version of each annex type
     */
    public function scopeLatestVersions($query) {
        return $query->whereIn('id', function ($subQuery) {
            $subQuery->select('id')
                ->from('coa_annexes')
                ->whereColumn('coa_id', 'coa_annexes.coa_id')
                ->whereColumn('code', 'coa_annexes.code')
                ->orderBy('version', 'desc')
                ->limit(1);
        });
    }

    /**
     * Scope for specific annex code
     */
    public function scopeOfCode($query, string $code) {
        return $query->where('code', $code);
    }

    /**
     * Scope for critical annexes (require re-issue)
     */
    public function scopeCritical($query) {
        return $query->whereIn('code', self::CRITICAL_CODES);
    }

    /**
     * Scope for addendum-allowed annexes
     */
    public function scopeAddendumAllowed($query) {
        return $query->whereIn('code', self::ADDENDUM_CODES);
    }

    /**
     * Get next version number for this annex code
     */
    public static function getNextVersion(string $coaId, string $code): int {
        $latest = static::where('coa_id', $coaId)
            ->where('code', $code)
            ->orderBy('version', 'desc')
            ->first();

        return $latest ? $latest->version + 1 : 1;
    }

    /**
     * Check if this annex is critical (requires re-issue)
     */
    public function isCritical(): bool {
        return in_array($this->code, self::CRITICAL_CODES);
    }

    /**
     * Check if this annex allows addendum updates
     */
    public function allowsAddendum(): bool {
        return in_array($this->code, self::ADDENDUM_CODES);
    }

    /**
     * Get human readable annex name
     */
    public function getDisplayName(): string {
        return match ($this->code) {
            self::CODE_PROVENANCE => 'Provenance',
            self::CODE_CONDITION => 'Condition Report',
            self::CODE_EXHIBITIONS => 'Exhibitions & Publications',
            self::CODE_PHOTOS => 'Photo Documentation',
            default => $this->code,
        };
    }

    /**
     * Get human readable file size
     */
    public function getHumanSizeAttribute(): string {
        if (!$this->bytes) {
            return 'Unknown size';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = $this->bytes;
        $unitIndex = 0;

        while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
            $bytes /= 1024;
            $unitIndex++;
        }

        return round($bytes, 2) . ' ' . $units[$unitIndex];
    }

    /**
     * Check if file exists in storage
     */
    public function exists(): bool {
        return Storage::exists($this->path);
    }

    /**
     * Get file URL for download
     */
    public function getUrl(): string {
        return Storage::url($this->path);
    }

    /**
     * Verify file integrity against stored hash
     */
    public function verifyIntegrity(): bool {
        if (!$this->exists()) {
            return false;
        }

        $actualHash = hash_file('sha256', Storage::path($this->path));
        return hash_equals($this->sha256, $actualHash);
    }
}
