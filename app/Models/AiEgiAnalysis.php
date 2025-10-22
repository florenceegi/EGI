<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * AiEgiAnalysis Model
 *
 * Rappresenta un'analisi AI completa di un EGI:
 * - Market positioning
 * - Competitive analysis
 * - Pricing strategies
 * - Marketing recommendations
 *
 * @package FlorenceEGI\Models
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0
 * @date 2025-10-22
 */
class AiEgiAnalysis extends Model
{
    use SoftDeletes;

    protected $table = 'ai_egi_analyses';

    protected $fillable = [
        'egi_id',
        'user_id',
        'analysis_type',
        'analysis_scope',
        'ai_raw_response',
        'market_positioning',
        'target_audience',
        'competitive_analysis',
        'unique_selling_points',
        'growth_opportunities',
        'risk_factors',
        'overall_confidence',
        'pricing_confidence',
        'marketing_confidence',
        'pricing_suggestions_count',
        'marketing_actions_count',
        'status',
        'completed_at',
        'reviewed_at',
        'implemented_at',
        'credits_used',
        'ai_model_used',
        'processing_time_ms',
        'tokens_used',
        'error_message',
        'error_code',
        'ip_address',
        'user_agent',
        'version',
        'previous_analysis_id',
    ];

    protected $casts = [
        'analysis_scope' => 'array',
        'ai_raw_response' => 'array',
        'unique_selling_points' => 'array',
        'overall_confidence' => 'integer',
        'pricing_confidence' => 'integer',
        'marketing_confidence' => 'integer',
        'pricing_suggestions_count' => 'integer',
        'marketing_actions_count' => 'integer',
        'credits_used' => 'integer',
        'processing_time_ms' => 'integer',
        'tokens_used' => 'integer',
        'version' => 'integer',
        'completed_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'implemented_at' => 'datetime',
    ];

    // === RELATIONSHIPS ===

    public function egi(): BelongsTo
    {
        return $this->belongsTo(Egi::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pricingSuggestions(): HasMany
    {
        return $this->hasMany(AiPricingSuggestion::class, 'analysis_id');
    }

    public function marketingActions(): HasMany
    {
        return $this->hasMany(AiMarketingAction::class, 'analysis_id');
    }

    public function previousAnalysis(): BelongsTo
    {
        return $this->belongsTo(AiEgiAnalysis::class, 'previous_analysis_id');
    }

    public function nextVersions(): HasMany
    {
        return $this->hasMany(AiEgiAnalysis::class, 'previous_analysis_id');
    }

    // === STATUS CHECKERS ===

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isInReview(): bool
    {
        return $this->status === 'review';
    }

    public function isImplemented(): bool
    {
        return $this->status === 'implemented';
    }

    public function isArchived(): bool
    {
        return $this->status === 'archived';
    }

    public function hasFailed(): bool
    {
        return $this->status === 'failed';
    }

    // === TYPE CHECKERS ===

    public function isFullAnalysis(): bool
    {
        return $this->analysis_type === 'full';
    }

    public function isPricingOnly(): bool
    {
        return $this->analysis_type === 'pricing_only';
    }

    public function isMarketingOnly(): bool
    {
        return $this->analysis_type === 'marketing_only';
    }

    public function isQuickScan(): bool
    {
        return $this->analysis_type === 'quick_scan';
    }

    // === STATE MODIFIERS ===

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    public function markAsReviewed(): void
    {
        $this->update([
            'status' => 'review',
            'reviewed_at' => now(),
        ]);
    }

    public function markAsImplemented(): void
    {
        $this->update([
            'status' => 'implemented',
            'implemented_at' => now(),
        ]);
    }

    public function markAsArchived(): void
    {
        $this->update([
            'status' => 'archived',
        ]);
    }

    public function markAsFailed(string $errorCode, string $errorMessage): void
    {
        $this->update([
            'status' => 'failed',
            'error_code' => $errorCode,
            'error_message' => $errorMessage,
        ]);
    }

    // === SCOPES ===

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForEgi($query, int $egiId)
    {
        return $query->where('egi_id', $egiId);
    }

    public function scopeLatestVersion($query)
    {
        return $query->whereNull('deleted_at')
            ->orderBy('version', 'desc');
    }

    // === HELPERS ===

    public function getConfidenceLevel(): string
    {
        if ($this->overall_confidence >= 80) {
            return 'high';
        } elseif ($this->overall_confidence >= 60) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    public function hasHighConfidence(): bool
    {
        return $this->overall_confidence >= 80;
    }

    public function getCostInCredits(): int
    {
        return $this->credits_used ?? 0;
    }

    public function getProcessingTimeSeconds(): ?float
    {
        return $this->processing_time_ms ? $this->processing_time_ms / 1000 : null;
    }
}
