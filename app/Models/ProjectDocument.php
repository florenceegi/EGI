<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * ProjectDocument Model
 *
 * Documents uploaded to PA projects for RAG processing
 *
 * @package App\Models
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Projects System)
 * @date 2025-10-27
 * @purpose Store uploaded documents with processing status and metadata
 *
 * @property int $id
 * @property int $project_id
 * @property string $filename
 * @property string $original_name
 * @property string $mime_type
 * @property int $size_bytes
 * @property string $file_path
 * @property string $status
 * @property string|null $error_message
 * @property array|null $metadata
 * @property \Illuminate\Support\Carbon|null $processed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read Project $project
 * @property-read \Illuminate\Database\Eloquent\Collection|ProjectDocumentChunk[] $chunks
 */
class ProjectDocument extends Model {
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_READY = 'ready';
    const STATUS_FAILED = 'failed';

    protected $fillable = [
        'project_id',
        'filename',
        'original_name',
        'mime_type',
        'size_bytes',
        'file_path',
        'status',
        'error_message',
        'metadata',
        'processed_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'size_bytes' => 'integer',
        'processed_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => self::STATUS_PENDING,
    ];

    /**
     * Get parent project
     */
    public function project(): BelongsTo {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get document chunks
     */
    public function chunks(): HasMany {
        return $this->hasMany(ProjectDocumentChunk::class);
    }

    /**
     * Scope: ready documents only
     */
    public function scopeReady($query) {
        return $query->where('status', self::STATUS_READY);
    }

    /**
     * Scope: failed documents
     */
    public function scopeFailed($query) {
        return $query->where('status', self::STATUS_FAILED);
    }

    /**
     * Scope: processing or pending
     */
    public function scopeProcessing($query) {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_PROCESSING]);
    }

    /**
     * Check if document is ready
     */
    public function isReady(): bool {
        return $this->status === self::STATUS_READY;
    }

    /**
     * Check if document failed
     */
    public function isFailed(): bool {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Check if document is processing
     */
    public function isProcessing(): bool {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_PROCESSING]);
    }

    /**
     * Mark document as processing
     */
    public function markAsProcessing(): void {
        $this->update(['status' => self::STATUS_PROCESSING]);
    }

    /**
     * Mark document as ready
     */
    public function markAsReady(array $metadata = []): void {
        $this->update([
            'status' => self::STATUS_READY,
            'processed_at' => now(),
            'metadata' => array_merge($this->metadata ?? [], $metadata),
        ]);
    }

    /**
     * Mark document as failed
     */
    public function markAsFailed(string $errorMessage): void {
        $this->update([
            'status' => self::STATUS_FAILED,
            'error_message' => $errorMessage,
        ]);
    }

    /**
     * Get human-readable file size
     */
    public function getHumanFileSizeAttribute(): string {
        $bytes = $this->size_bytes;

        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }

        return $bytes . ' bytes';
    }

    /**
     * Get file extension
     */
    public function getFileExtensionAttribute(): string {
        return pathinfo($this->original_name, PATHINFO_EXTENSION);
    }

    /**
     * Get chunks count
     */
    public function getChunksCountAttribute(): int {
        return $this->chunks()->count();
    }
}
