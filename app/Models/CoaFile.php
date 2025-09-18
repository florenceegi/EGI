<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * CoaFile Model
 *
 * Files associated with CoA including PDFs, images, and annex packs.
 * Each file has SHA-256 hash for integrity verification.
 *
 * @package FlorenceEGI\Models
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI CoA Pro System)
 * @date 2025-09-18
 *
 * @property int $id
 * @property string $coa_id ULID foreign key to CoA
 * @property string $kind File type (pdf, core_pdf, bundle_pdf, etc.)
 * @property string $path File path in storage
 * @property string $sha256 SHA-256 hash
 * @property int|null $bytes File size in bytes
 * @property \Carbon\Carbon $created_at
 */
class CoaFile extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'coa_files';

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
        'path',
        'sha256',
        'bytes',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'bytes' => 'integer',
        'created_at' => 'datetime',
    ];

    //--------------------------------------------------------------------------
    // Constants
    //--------------------------------------------------------------------------

    public const KIND_PDF = 'pdf';
    public const KIND_CORE_PDF = 'core_pdf';
    public const KIND_BUNDLE_PDF = 'bundle_pdf';
    public const KIND_ANNEX_PACK = 'annex_pack';
    public const KIND_SCAN_SIGNED = 'scan_signed';
    public const KIND_IMAGE_FRONT = 'image_front';
    public const KIND_IMAGE_BACK = 'image_back';
    public const KIND_SIGNATURE_DETAIL = 'signature_detail';

    //--------------------------------------------------------------------------
    // Relationships
    //--------------------------------------------------------------------------

    /**
     * Get the CoA this file belongs to
     */
    public function coa(): BelongsTo
    {
        return $this->belongsTo(Coa::class);
    }

    //--------------------------------------------------------------------------
    // Scopes & Methods
    //--------------------------------------------------------------------------

    /**
     * Scope for PDF files
     */
    public function scopePdfs($query)
    {
        return $query->whereIn('kind', [
            self::KIND_PDF,
            self::KIND_CORE_PDF,
            self::KIND_BUNDLE_PDF
        ]);
    }

    /**
     * Scope for image files
     */
    public function scopeImages($query)
    {
        return $query->whereIn('kind', [
            self::KIND_IMAGE_FRONT,
            self::KIND_IMAGE_BACK,
            self::KIND_SIGNATURE_DETAIL
        ]);
    }

    /**
     * Check if file exists in storage
     */
    public function exists(): bool
    {
        return Storage::exists($this->path);
    }

    /**
     * Get file URL for download
     */
    public function getUrl(): string
    {
        return Storage::url($this->path);
    }

    /**
     * Get human readable file size
     */
    public function getHumanSizeAttribute(): string
    {
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
     * Verify file integrity against stored hash
     */
    public function verifyIntegrity(): bool
    {
        if (!$this->exists()) {
            return false;
        }

        $actualHash = hash_file('sha256', Storage::path($this->path));
        return hash_equals($this->sha256, $actualHash);
    }
}
