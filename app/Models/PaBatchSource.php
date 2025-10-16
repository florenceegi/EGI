<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @Oracode Model: PA Batch Source
 * 🎯 Purpose: Represents a file source directory monitored by NATAN agent
 * 🛡️ Privacy: User-scoped, no PII in path fields
 * 🧱 Core Logic: Tracks directory sources with status, priority, and cached statistics
 *
 * @package App\Models
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - NATAN Batch Agent System)
 * @date 2025-10-16
 * @purpose File source tracking for batch processing system
 *
 * @property int $id
 * @property int $user_id PA entity user ID
 * @property string $name Source name (e.g., "Archivio Storico 2023")
 * @property string|null $description Source description
 * @property string $path Absolute path on PA server
 * @property string $file_pattern Glob pattern (default: *.pdf.p7m)
 * @property string $status Enum: active, paused, error
 * @property bool $auto_process Auto-processing enabled flag
 * @property int $priority Priority (1-10, higher first)
 * @property int $stats_total Total files count (cached)
 * @property int $stats_processed Processed files count (cached)
 * @property int $stats_failed Failed files count (cached)
 * @property int $stats_pending Pending files count (cached)
 * @property \Illuminate\Support\Carbon|null $last_scan_at Last scan timestamp
 * @property \Illuminate\Support\Carbon|null $last_processed_at Last processing timestamp
 * @property int|null $created_by_user_id User who created this source
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read User $user PA entity user who owns this source
 * @property-read User|null $creator User who created this source
 * @property-read \Illuminate\Database\Eloquent\Collection<\App\Models\PaBatchJob> $jobs
 *
 * @method \Illuminate\Database\Eloquent\Collection pendingJobs() Get pending batch jobs
 * @method \Illuminate\Database\Eloquent\Collection failedJobs() Get failed batch jobs
 */
class PaBatchSource extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pa_batch_sources';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'path',
        'file_pattern',
        'status',
        'auto_process',
        'priority',
        'stats_total',
        'stats_processed',
        'stats_failed',
        'stats_pending',
        'last_scan_at',
        'last_processed_at',
        'created_by_user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'auto_process' => 'boolean',
        'priority' => 'integer',
        'stats_total' => 'integer',
        'stats_processed' => 'integer',
        'stats_failed' => 'integer',
        'stats_pending' => 'integer',
        'last_scan_at' => 'datetime',
        'last_processed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the PA entity user who owns this source.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user who created this source.
     *
     * @return BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    /**
     * Get all batch jobs for this source.
     *
     * @return HasMany
     */
    public function jobs(): HasMany
    {
        return $this->hasMany(PaBatchJob::class, 'source_id');
    }

    /**
     * Get pending batch jobs for this source.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function pendingJobs()
    {
        return $this->jobs()->where('status', 'pending')->orderBy('created_at')->get();
    }

    /**
     * Get failed batch jobs for this source.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function failedJobs()
    {
        return $this->jobs()->where('status', 'failed')->orderBy('updated_at', 'desc')->get();
    }

    /**
     * Scope to get only active sources.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get sources ordered by priority.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByPriority($query)
    {
        return $query->orderBy('priority', 'desc');
    }

    /**
     * Check if source is active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if source has pending jobs.
     *
     * @return bool
     */
    public function hasPendingJobs(): bool
    {
        return $this->stats_pending > 0;
    }

    /**
     * Get success rate percentage.
     *
     * @return float
     */
    public function getSuccessRate(): float
    {
        if ($this->stats_total === 0) {
            return 0.0;
        }

        return round(($this->stats_processed / $this->stats_total) * 100, 2);
    }

    /**
     * Get failure rate percentage.
     *
     * @return float
     */
    public function getFailureRate(): float
    {
        if ($this->stats_total === 0) {
            return 0.0;
        }

        return round(($this->stats_failed / $this->stats_total) * 100, 2);
    }
}

