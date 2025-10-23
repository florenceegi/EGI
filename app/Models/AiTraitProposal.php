<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * AiTraitProposal Model
 *
 * Rappresenta una singola proposta di trait dall'AI
 *
 * @package FlorenceEGI\Models
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0
 * @date 2025-10-21
 */
class AiTraitProposal extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'ai_trait_proposals';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'generation_id',
        'category_suggestion',
        'type_suggestion',
        'value_suggestion',
        'display_value_suggestion',
        'confidence',
        'reasoning',
        'match_type',
        'matched_category_id',
        'matched_type_id',
        'matched_value',
        'category_match_score',
        'type_match_score',
        'value_match_score',
        'user_decision',
        'user_modifications',
        'reviewed_at',
        'created_category_id',
        'created_type_id',
        'created_trait_id',
        'applied_at',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'confidence' => 'integer',
        'category_match_score' => 'integer',
        'type_match_score' => 'integer',
        'value_match_score' => 'integer',
        'user_modifications' => 'array',
        'reviewed_at' => 'datetime',
        'applied_at' => 'datetime',
        'sort_order' => 'integer',
    ];

    /**
     * Get the generation session this proposal belongs to
     */
    public function generation(): BelongsTo
    {
        return $this->belongsTo(AiTraitGeneration::class, 'generation_id');
    }

    /**
     * Get the matched category (if any)
     */
    public function matchedCategory(): BelongsTo
    {
        return $this->belongsTo(TraitCategory::class, 'matched_category_id');
    }

    /**
     * Get the matched type (if any)
     */
    public function matchedType(): BelongsTo
    {
        return $this->belongsTo(TraitType::class, 'matched_type_id');
    }

    /**
     * Get the created category (if was new)
     */
    public function createdCategory(): BelongsTo
    {
        return $this->belongsTo(TraitCategory::class, 'created_category_id');
    }

    /**
     * Get the created type (if was new)
     */
    public function createdType(): BelongsTo
    {
        return $this->belongsTo(TraitType::class, 'created_type_id');
    }

    /**
     * Get the created EGI trait (after approval)
     */
    public function createdTrait(): BelongsTo
    {
        return $this->belongsTo(EgiTrait::class, 'created_trait_id');
    }

    /**
     * Check if proposal is pending review
     */
    public function isPending(): bool
    {
        return $this->user_decision === 'pending';
    }

    /**
     * Check if proposal is approved
     */
    public function isApproved(): bool
    {
        return $this->user_decision === 'approved';
    }

    /**
     * Check if proposal is rejected
     */
    public function isRejected(): bool
    {
        return $this->user_decision === 'rejected';
    }

    /**
     * Check if proposal was modified by user
     */
    public function isModified(): bool
    {
        return $this->user_decision === 'modified';
    }

    /**
     * Check if proposal has been applied to EGI
     */
    public function isApplied(): bool
    {
        return !is_null($this->applied_at);
    }

    /**
     * Check if this is an exact match
     */
    public function isExactMatch(): bool
    {
        return $this->match_type === 'exact';
    }

    /**
     * Check if this is a fuzzy match
     */
    public function isFuzzyMatch(): bool
    {
        return $this->match_type === 'fuzzy';
    }

    /**
     * Check if this requires new category creation
     */
    public function requiresNewCategory(): bool
    {
        return $this->match_type === 'new_category';
    }

    /**
     * Check if this requires new type creation
     */
    public function requiresNewType(): bool
    {
        return in_array($this->match_type, ['new_type', 'new_category']);
    }

    /**
     * Check if this requires new value addition
     */
    public function requiresNewValue(): bool
    {
        return in_array($this->match_type, ['new_value', 'new_type', 'new_category']);
    }

    /**
     * Mark as approved
     */
    public function markAsApproved(): void
    {
        $this->update([
            'user_decision' => 'approved',
            'reviewed_at' => now(),
        ]);
    }

    /**
     * Mark as rejected
     */
    public function markAsRejected(): void
    {
        $this->update([
            'user_decision' => 'rejected',
            'reviewed_at' => now(),
        ]);
    }

    /**
     * Mark as modified with user changes
     */
    public function markAsModified(array $modifications): void
    {
        $this->update([
            'user_decision' => 'modified',
            'user_modifications' => $modifications,
            'reviewed_at' => now(),
        ]);
    }

    /**
     * Mark as applied with created references
     */
    public function markAsApplied(
        ?int $createdCategoryId = null,
        ?int $createdTypeId = null,
        ?int $createdTraitId = null
    ): void {
        $this->update([
            'created_category_id' => $createdCategoryId,
            'created_type_id' => $createdTypeId,
            'created_trait_id' => $createdTraitId,
            'applied_at' => now(),
        ]);
    }

    /**
     * Get average match score across category/type/value
     */
    public function getAverageMatchScoreAttribute(): ?float
    {
        $scores = array_filter([
            $this->category_match_score,
            $this->type_match_score,
            $this->value_match_score,
        ]);

        if (empty($scores)) {
            return null;
        }

        return round(array_sum($scores) / count($scores), 2);
    }

    /**
     * Get display label for UI
     */
    public function getDisplayLabelAttribute(): string
    {
        return sprintf(
            '%s → %s: %s',
            $this->category_suggestion,
            $this->type_suggestion,
            $this->display_value_suggestion ?? $this->value_suggestion
        );
    }
}









