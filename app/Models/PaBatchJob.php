<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @Oracode Model: PA Batch Job
 * 🎯 Purpose: Represents a single file processing job from NATAN agent
 * 🛡️ Privacy: Stores file hash (not content), GDPR-compliant error messages
 * 🧱 Core Logic: Job queue with status tracking, retry logic, and audit trail
 *
 * @package App\Models
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - NATAN Batch Agent System)
 * @date 2025-10-16
 * @purpose Individual file processing job tracking
 *
 * @property int $id
 * @property int $user_id PA entity user ID
 * @property int $source_id Source ID
 * @property int|null $egi_id Created EGI ID (nullable)
 * @property string $file_name File name only
 * @property string|null $file_path PA-side full path (reference)
 * @property string $file_hash SHA256 hash
 * @property int $file_size File size in bytes
 * @property string $status Enum: pending, processing, completed, failed, duplicate
 * @property int $attempts Processing attempts count
 * @property int $max_attempts Maximum retry attempts
 * @property string|null $last_error GDPR-sanitized error message
 * @property string|null $error_code Coded error (no PII)
 * @property \Illuminate\Support\Carbon|null $processing_started_at
 * @property \Illuminate\Support\Carbon|null $processing_completed_at
 * @property int|null $processing_duration_seconds Processing duration (cached)
 * @property array|null $agent_metadata JSON metadata from agent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read User $user PA entity user who owns this job
 * @property-read PaBatchSource $source Source directory
 * @property-read Egi|null $egi Created EGI record
 */
class PaBatchJob extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pa_batch_jobs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'source_id',
        'egi_id',
        'file_name',
        'file_path',
        'file_hash',
        'file_size',
        'status',
        'attempts',
        'max_attempts',
        'last_error',
        'error_code',
        'processing_started_at',
        'processing_completed_at',
        'processing_duration_seconds',
        'agent_metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'file_size' => 'integer',
        'attempts' => 'integer',
        'max_attempts' => 'integer',
        'processing_duration_seconds' => 'integer',
        'agent_metadata' => 'array',
        'processing_started_at' => 'datetime',
        'processing_completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the PA entity user who owns this job.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the source directory for this job.
     *
     * @return BelongsTo
     */
    public function source(): BelongsTo
    {
        return $this->belongsTo(PaBatchSource::class, 'source_id');
    }

    /**
     * Get the created EGI record.
     *
     * @return BelongsTo
     */
    public function egi(): BelongsTo
    {
        return $this->belongsTo(Egi::class, 'egi_id');
    }

    /**
     * Scope to get pending jobs.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get processing jobs.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    /**
     * Scope to get completed jobs.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope to get failed jobs.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope to get retryable jobs (failed but under max attempts).
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRetryable($query)
    {
        return $query->where('status', 'failed')
            ->whereColumn('attempts', '<', 'max_attempts');
    }

    /**
     * Check if job can be retried.
     *
     * @return bool
     */
    public function canRetry(): bool
    {
        return $this->status === 'failed' && $this->attempts < $this->max_attempts;
    }

    /**
     * Check if job is permanently failed.
     *
     * @return bool
     */
    public function isPermanentlyFailed(): bool
    {
        return $this->status === 'failed' && $this->attempts >= $this->max_attempts;
    }

    /**
     * Check if job is completed.
     *
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if job is duplicate.
     *
     * @return bool
     */
    public function isDuplicate(): bool
    {
        return $this->status === 'duplicate';
    }

    /**
     * Mark job as processing.
     *
     * @return bool
     */
    public function markAsProcessing(): bool
    {
        return $this->update([
            'status' => 'processing',
            'processing_started_at' => now(),
            'attempts' => $this->attempts + 1,
        ]);
    }

    /**
     * Mark job as completed.
     *
     * @param int $egiId Created EGI ID
     * @return bool
     */
    public function markAsCompleted(int $egiId): bool
    {
        $now = now();
        $duration = $this->processing_started_at
            ? $now->diffInSeconds($this->processing_started_at)
            : null;

        return $this->update([
            'status' => 'completed',
            'egi_id' => $egiId,
            'processing_completed_at' => $now,
            'processing_duration_seconds' => $duration,
            'last_error' => null,
            'error_code' => null,
        ]);
    }

    /**
     * Mark job as failed with error.
     *
     * @param string $errorMessage GDPR-sanitized error message
     * @param string|null $errorCode Coded error (no PII)
     * @return bool
     */
    public function markAsFailed(string $errorMessage, ?string $errorCode = null): bool
    {
        $status = $this->attempts >= $this->max_attempts ? 'failed' : 'pending';

        return $this->update([
            'status' => $status,
            'last_error' => $errorMessage,
            'error_code' => $errorCode,
        ]);
    }

    /**
     * Mark job as duplicate.
     *
     * @param int $existingEgiId Existing EGI ID
     * @return bool
     */
    public function markAsDuplicate(int $existingEgiId): bool
    {
        return $this->update([
            'status' => 'duplicate',
            'egi_id' => $existingEgiId,
            'processing_completed_at' => now(),
        ]);
    }

    /**
     * Get formatted file size.
     *
     * @return string
     */
    public function getFormattedFileSize(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}

