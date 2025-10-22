<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * AiTraitGeneration Model
 *
 * Rappresenta una sessione di generazione AI traits per un EGI
 *
 * @package FlorenceEGI\Models
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0
 * @date 2025-10-21
 */
class AiTraitGeneration extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'ai_trait_generations';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'egi_id',
        'user_id',
        'requested_count',
        'image_url',
        'ai_raw_response',
        'total_confidence',
        'analysis_notes',
        'exact_matches_count',
        'fuzzy_matches_count',
        'new_proposals_count',
        'status',
        'analyzed_at',
        'reviewed_at',
        'applied_at',
        'error_message',
        'error_code',
        'ip_address',
        'user_agent',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'requested_count' => 'integer',
        'total_confidence' => 'integer',
        'exact_matches_count' => 'integer',
        'fuzzy_matches_count' => 'integer',
        'new_proposals_count' => 'integer',
        'ai_raw_response' => 'array',
        'analyzed_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'applied_at' => 'datetime',
    ];

    /**
     * Get the EGI this generation belongs to
     */
    public function egi(): BelongsTo
    {
        return $this->belongsTo(Egi::class);
    }

    /**
     * Get the user who requested this generation
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all trait proposals for this generation
     */
    public function proposals(): HasMany
    {
        return $this->hasMany(AiTraitProposal::class, 'generation_id');
    }

    /**
     * Get only approved proposals
     */
    public function approvedProposals(): HasMany
    {
        return $this->proposals()->where('user_decision', 'approved');
    }

    /**
     * Get only pending proposals
     */
    public function pendingProposals(): HasMany
    {
        return $this->proposals()->where('user_decision', 'pending');
    }

    /**
     * Get only rejected proposals
     */
    public function rejectedProposals(): HasMany
    {
        return $this->proposals()->where('user_decision', 'rejected');
    }

    /**
     * Check if generation is pending AI analysis
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if generation is analyzed and awaiting user review
     */
    public function isAwaitingReview(): bool
    {
        return $this->status === 'analyzed';
    }

    /**
     * Check if generation has been fully approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if generation has been applied to EGI
     */
    public function isApplied(): bool
    {
        return $this->status === 'applied';
    }

    /**
     * Check if generation has failed
     */
    public function hasFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Mark generation as analyzed
     */
    public function markAsAnalyzed(): void
    {
        $this->update([
            'status' => 'analyzed',
            'analyzed_at' => now(),
        ]);
    }

    /**
     * Mark generation as approved
     */
    public function markAsApproved(): void
    {
        $this->update([
            'status' => 'approved',
            'reviewed_at' => now(),
        ]);
    }

    /**
     * Mark generation as applied
     */
    public function markAsApplied(): void
    {
        $this->update([
            'status' => 'applied',
            'applied_at' => now(),
        ]);
    }

    /**
     * Mark generation as failed
     */
    public function markAsFailed(string $errorCode, string $errorMessage): void
    {
        $this->update([
            'status' => 'failed',
            'error_code' => $errorCode,
            'error_message' => $errorMessage,
        ]);
    }

    /**
     * Get success rate (approved proposals / total proposals)
     */
    public function getSuccessRateAttribute(): ?float
    {
        $total = $this->proposals()->count();

        if ($total === 0) {
            return null;
        }

        $approved = $this->approvedProposals()->count();

        return round(($approved / $total) * 100, 2);
    }
}



