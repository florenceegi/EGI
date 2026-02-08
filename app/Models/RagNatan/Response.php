<?php

namespace App\Models\RagNatan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * RAG Response Model
 *
 * Generated RAG responses with quality metrics.
 *
 * @property int $id
 * @property string $uuid
 * @property int $query_id
 * @property string $answer
 * @property string|null $answer_html
 * @property float $urs_score
 * @property string|null $urs_explanation
 * @property array $claims_used
 * @property array $gaps_detected
 * @property array $hallucinations
 * @property array $sources_used
 * @property int $processing_time_ms
 * @property int|null $tokens_input
 * @property int|null $tokens_output
 * @property float|null $cost_usd
 * @property string|null $model_used
 * @property array $stage_timings
 * @property bool $is_cached
 * @property string|null $cache_key
 * @property \Carbon\Carbon|null $cache_expires_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon|null $served_from_cache_at
 */
class Response extends Model
{
    protected $table = 'rag_natan.responses';

    const UPDATED_AT = null; // Only has created_at and served_from_cache_at

    protected $fillable = [
        'uuid',
        'query_id',
        'answer',
        'answer_html',
        'urs_score',
        'urs_explanation',
        'claims_used',
        'gaps_detected',
        'hallucinations',
        'sources_used',
        'processing_time_ms',
        'tokens_input',
        'tokens_output',
        'cost_usd',
        'model_used',
        'stage_timings',
        'is_cached',
        'cache_key',
        'cache_expires_at',
        'served_from_cache_at',
    ];

    protected $casts = [
        'urs_score' => 'decimal:2',
        'claims_used' => 'array',
        'gaps_detected' => 'array',
        'hallucinations' => 'array',
        'sources_used' => 'array',
        'stage_timings' => 'array',
        'processing_time_ms' => 'integer',
        'tokens_input' => 'integer',
        'tokens_output' => 'integer',
        'cost_usd' => 'decimal:6',
        'is_cached' => 'boolean',
        'created_at' => 'datetime',
        'cache_expires_at' => 'datetime',
        'served_from_cache_at' => 'datetime',
    ];

    public function queryRecord(): BelongsTo
    {
        return $this->belongsTo(Query::class, 'query_id');
    }

    public function sources(): HasMany
    {
        return $this->hasMany(Source::class, 'response_id');
    }

    public function scopeHighQuality($query, float $minScore = 70.0)
    {
        return $query->where('urs_score', '>=', $minScore);
    }

    public function scopeCached($query)
    {
        return $query->where('is_cached', true);
    }
}
