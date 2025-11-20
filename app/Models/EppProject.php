<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * EPP Project model.
 *
 * Represents individual environmental projects managed by EPP Users.
 * One EPP User (usertype='epp') can have multiple projects.
 * Collections select ONE specific EppProject to support.
 *
 * --- Core Logic ---
 * 1. Links to User model where usertype='epp'
 * 2. Tracks specific environmental initiatives (ARF, APR, BPE)
 * 3. Manages financial goals (target_funds, current_funds)
 * 4. Supports multiple projects per EPP organization
 * 5. Collections choose which project to support via epp_project_id
 * --- End Core Logic ---
 *
 * @package App\Models
 * @version 2.0.0
 *
 * @property int $id
 * @property int $epp_user_id Foreign key to users table (usertype='epp')
 * @property string $name Project name
 * @property string $description Project description
 * @property string $project_type ARF, APR, BPE
 * @property string $status completed, in_progress, planned
 * @property float $target_value Environmental impact target
 * @property float $current_value Current environmental impact
 * @property float $target_funds Financial target in EUR
 * @property float $current_funds Current funds raised in EUR
 * @property string|null $evidence_url
 * @property string|null $evidence_type
 * @property array|null $media
 * @property \Carbon\Carbon $target_date
 * @property \Carbon\Carbon $completion_date
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class EppProject extends Model implements HasMedia {
    use HasFactory;
    use InteractsWithMedia;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'epp_projects';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'epp_user_id',
        'name',
        'description',
        'project_type',
        'status',
        'target_value',
        'current_value',
        'target_funds',
        'current_funds',
        'evidence_url',
        'evidence_type',
        'media',
        'target_date',
        'completion_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'target_value' => 'float',
        'current_value' => 'float',
        'target_funds' => 'float',
        'current_funds' => 'float',
        'media' => 'array',
        'target_date' => 'datetime',
        'completion_date' => 'datetime',
    ];

    /**
     * Get the EPP User that owns this project.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function eppUser() {
        return $this->belongsTo(User::class, 'epp_user_id');
    }

    /**
     * Get the collections supporting this project.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function collections() {
        return $this->hasMany(Collection::class, 'epp_project_id');
    }

    /**
     * Get the completion percentage for this project.
     *
     * @return float
     */
    public function getCompletionPercentageAttribute() {
        if ($this->target_value <= 0) {
            return 0;
        }

        $percentage = ($this->current_value / $this->target_value) * 100;
        return min(100, $percentage); // Cap at 100%
    }

    /**
     * Get the funds completion percentage for this project.
     *
     * @return float
     */
    public function getFundsCompletionPercentageAttribute() {
        if (!$this->target_funds || $this->target_funds <= 0) {
            return 0;
        }

        $percentage = ($this->current_funds / $this->target_funds) * 100;
        return min(100, $percentage); // Cap at 100%
    }

    /**
     * Get the project type name (translated).
     *
     * @return string
     */
    public function getProjectTypeNameAttribute() {
        $types = [
            'ARF' => 'Reforestation',
            'APR' => 'Ocean Cleanup',
            'BPE' => 'Bee Protection'
        ];

        return $types[$this->project_type] ?? $this->project_type;
    }

    /**
     * Check if this project is completed.
     *
     * @return bool
     */
    public function isCompleted() {
        return $this->status === 'completed';
    }

    /**
     * Check if this project is overdue.
     *
     * @return bool
     */
    public function isOverdue() {
        return $this->target_date && $this->target_date->isPast() && $this->status !== 'completed';
    }

    /**
     * Scope a query to only include active projects.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query) {
        return $query->whereIn('status', ['in_progress', 'planned']);
    }

    /**
     * Scope a query to only include completed projects.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompleted($query) {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include in-progress projects.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInProgress($query) {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope a query to only include planned projects.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePlanned($query) {
        return $query->where('status', 'planned');
    }

    /**
     * Scope a query to only include projects of a specific type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, $type) {
        return $query->where('project_type', $type);
    }

    /**
     * Scope a query to only include projects by EPP User.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $eppUserId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByEppUser($query, $eppUserId) {
        return $query->where('epp_user_id', $eppUserId);
    }

    /**
     * Update the project's progress.
     *
     * @param float $newValue
     * @param bool $completed
     * @return self
     */
    public function updateProgress($newValue, $completed = false) {
        $this->current_value = $newValue;

        if ($completed || $newValue >= $this->target_value) {
            $this->status = 'completed';
            $this->completion_date = now();
        } else {
            $this->status = 'in_progress';
        }

        $this->save();

        return $this;
    }

    /**
     * Update the project's funding.
     *
     * @param float $amount
     * @return self
     */
    public function addFunds($amount) {
        $this->current_funds += $amount;
        $this->save();

        return $this;
    }
}
