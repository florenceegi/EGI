<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * AiMarketingAction Model
 *
 * Rappresenta un'azione marketing suggerita da AI:
 * - Social media strategies
 * - Content recommendations
 * - Advertising campaigns
 * - Engagement tactics
 *
 * @package FlorenceEGI\Models
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0
 * @date 2025-10-22
 */
class AiMarketingAction extends Model
{
    use SoftDeletes;

    protected $table = 'ai_marketing_actions';

    protected $fillable = [
        'analysis_id',
        'egi_id',
        'user_id',
        'action_type',
        'priority',
        'effort_level',
        'title',
        'description',
        'rationale',
        'expected_outcome',
        'step_by_step_guide',
        'required_resources',
        'target_audience',
        'target_platforms',
        'recommended_hashtags',
        'recommended_keywords',
        'suggested_start_date',
        'suggested_end_date',
        'estimated_duration_days',
        'frequency',
        'estimated_cost_min',
        'estimated_cost_max',
        'cost_breakdown',
        'expected_impact',
        'kpi_targets',
        'estimated_reach',
        'estimated_engagement',
        'estimated_roi_percentage',
        'confidence_score',
        'success_probability_factors',
        'suggested_content_title',
        'suggested_content_body',
        'suggested_visuals',
        'call_to_action',
        'user_decision',
        'user_notes',
        'decided_at',
        'started_at',
        'completed_at',
        'actual_results',
        'actual_reach',
        'actual_engagement',
        'actual_cost',
        'actual_roi_percentage',
        'was_successful',
        'lessons_learned',
        'parent_action_id',
        'related_actions_ids',
        'ai_metadata',
    ];

    protected $casts = [
        'step_by_step_guide' => 'array',
        'required_resources' => 'array',
        'target_audience' => 'array',
        'target_platforms' => 'array',
        'recommended_hashtags' => 'array',
        'recommended_keywords' => 'array',
        'suggested_start_date' => 'date',
        'suggested_end_date' => 'date',
        'estimated_duration_days' => 'integer',
        'estimated_cost_min' => 'decimal:2',
        'estimated_cost_max' => 'decimal:2',
        'cost_breakdown' => 'array',
        'kpi_targets' => 'array',
        'estimated_reach' => 'integer',
        'estimated_engagement' => 'integer',
        'estimated_roi_percentage' => 'decimal:2',
        'confidence_score' => 'integer',
        'success_probability_factors' => 'array',
        'suggested_visuals' => 'array',
        'call_to_action' => 'array',
        'decided_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'actual_results' => 'array',
        'actual_reach' => 'integer',
        'actual_engagement' => 'integer',
        'actual_cost' => 'decimal:2',
        'actual_roi_percentage' => 'decimal:2',
        'was_successful' => 'boolean',
        'related_actions_ids' => 'array',
        'ai_metadata' => 'array',
    ];

    // === RELATIONSHIPS ===

    public function analysis(): BelongsTo
    {
        return $this->belongsTo(AiEgiAnalysis::class, 'analysis_id');
    }

    public function egi(): BelongsTo
    {
        return $this->belongsTo(Egi::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parentAction(): BelongsTo
    {
        return $this->belongsTo(AiMarketingAction::class, 'parent_action_id');
    }

    public function childActions(): HasMany
    {
        return $this->hasMany(AiMarketingAction::class, 'parent_action_id');
    }

    // === STATUS CHECKERS ===

    public function isPending(): bool
    {
        return $this->user_decision === 'pending';
    }

    public function isAccepted(): bool
    {
        return $this->user_decision === 'accepted';
    }

    public function isInProgress(): bool
    {
        return $this->user_decision === 'in_progress';
    }

    public function isCompleted(): bool
    {
        return $this->user_decision === 'completed';
    }

    public function isRejected(): bool
    {
        return $this->user_decision === 'rejected';
    }

    public function isPostponed(): bool
    {
        return $this->user_decision === 'postponed';
    }

    public function isModified(): bool
    {
        return $this->user_decision === 'modified';
    }

    // === PRIORITY CHECKERS ===

    public function isCritical(): bool
    {
        return $this->priority === 'critical';
    }

    public function isHighPriority(): bool
    {
        return $this->priority === 'high';
    }

    public function isMediumPriority(): bool
    {
        return $this->priority === 'medium';
    }

    public function isLowPriority(): bool
    {
        return $this->priority === 'low';
    }

    // === EFFORT CHECKERS ===

    public function isQuickWin(): bool
    {
        return $this->effort_level === 'quick_win';
    }

    public function isLowEffort(): bool
    {
        return $this->effort_level === 'low';
    }

    public function isMediumEffort(): bool
    {
        return $this->effort_level === 'medium';
    }

    public function isHighEffort(): bool
    {
        return $this->effort_level === 'high';
    }

    public function isOngoing(): bool
    {
        return $this->effort_level === 'ongoing';
    }

    // === STATE MODIFIERS ===

    public function accept(?string $notes = null): void
    {
        $this->update([
            'user_decision' => 'accepted',
            'user_notes' => $notes,
            'decided_at' => now(),
        ]);
    }

    public function reject(?string $notes = null): void
    {
        $this->update([
            'user_decision' => 'rejected',
            'user_notes' => $notes,
            'decided_at' => now(),
        ]);
    }

    public function start(?string $notes = null): void
    {
        $this->update([
            'user_decision' => 'in_progress',
            'user_notes' => $notes,
            'started_at' => now(),
        ]);
    }

    public function complete(array $actualResults, ?string $lessonsLearned = null): void
    {
        $this->update([
            'user_decision' => 'completed',
            'completed_at' => now(),
            'actual_results' => $actualResults,
            'lessons_learned' => $lessonsLearned,
        ]);
    }

    public function postpone(?string $notes = null): void
    {
        $this->update([
            'user_decision' => 'postponed',
            'user_notes' => $notes,
            'decided_at' => now(),
        ]);
    }

    public function recordResults(
        int $actualReach,
        int $actualEngagement,
        float $actualCost,
        float $actualRoi,
        bool $wasSuccessful
    ): void {
        $this->update([
            'actual_reach' => $actualReach,
            'actual_engagement' => $actualEngagement,
            'actual_cost' => $actualCost,
            'actual_roi_percentage' => $actualRoi,
            'was_successful' => $wasSuccessful,
        ]);
    }

    // === SCOPES ===

    public function scopePending($query)
    {
        return $query->where('user_decision', 'pending');
    }

    public function scopeAccepted($query)
    {
        return $query->where('user_decision', 'accepted');
    }

    public function scopeInProgress($query)
    {
        return $query->where('user_decision', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('user_decision', 'completed');
    }

    public function scopeCritical($query)
    {
        return $query->where('priority', 'critical');
    }

    public function scopeQuickWins($query)
    {
        return $query->where('effort_level', 'quick_win');
    }

    public function scopeHighConfidence($query)
    {
        return $query->where('confidence_score', '>=', 80);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForEgi($query, int $egiId)
    {
        return $query->where('egi_id', $egiId);
    }

    public function scopeByType($query, string $actionType)
    {
        return $query->where('action_type', $actionType);
    }

    public function scopeDueThisWeek($query)
    {
        return $query->whereBetween('suggested_start_date', [now(), now()->addWeek()]);
    }

    // === HELPERS ===

    public function getConfidenceLevel(): string
    {
        if ($this->confidence_score >= 80) {
            return 'high';
        } elseif ($this->confidence_score >= 60) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    public function hasHighConfidence(): bool
    {
        return $this->confidence_score >= 80;
    }

    public function getEstimatedCostRange(): array
    {
        return [
            'min' => $this->estimated_cost_min,
            'max' => $this->estimated_cost_max,
        ];
    }

    public function getPriorityScore(): int
    {
        // Calcola score priorità basato su priority + impact + confidence
        $priorityScores = [
            'critical' => 40,
            'high' => 30,
            'medium' => 20,
            'low' => 10,
        ];

        $impactScores = [
            'high' => 30,
            'medium' => 20,
            'low' => 10,
        ];

        $confidenceScore = ($this->confidence_score / 100) * 30;

        return $priorityScores[$this->priority] +
            $impactScores[$this->expected_impact] +
            $confidenceScore;
    }

    public function isOverdue(): bool
    {
        return $this->suggested_start_date &&
            $this->suggested_start_date->isPast() &&
            $this->isPending();
    }

    public function getDaysUntilStart(): ?int
    {
        return $this->suggested_start_date ?
            now()->diffInDays($this->suggested_start_date, false) :
            null;
    }

    public function getDuration(): ?int
    {
        if ($this->started_at && $this->completed_at) {
            return $this->started_at->diffInDays($this->completed_at);
        }
        return null;
    }

    public function getROIComparison(): ?array
    {
        if (!$this->estimated_roi_percentage || !$this->actual_roi_percentage) {
            return null;
        }

        return [
            'estimated' => $this->estimated_roi_percentage,
            'actual' => $this->actual_roi_percentage,
            'difference' => $this->actual_roi_percentage - $this->estimated_roi_percentage,
            'accuracy_percentage' => (1 - abs($this->actual_roi_percentage - $this->estimated_roi_percentage) / $this->estimated_roi_percentage) * 100,
        ];
    }
}
