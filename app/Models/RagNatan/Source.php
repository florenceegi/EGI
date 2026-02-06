<?php

namespace App\Models\RagNatan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * RAG Source Model
 *
 * Response source citations (N:M pivot between responses and chunks).
 *
 * @property int $id
 * @property int $response_id
 * @property int $chunk_id
 * @property string|null $claim_id
 * @property string|null $exact_quote
 * @property float|null $relevance_score
 * @property int|null $citation_order
 * @property \Carbon\Carbon $created_at
 */
class Source extends Model
{
    protected $table = 'rag_natan.sources';

    const UPDATED_AT = null; // Only has created_at

    protected $fillable = [
        'response_id',
        'chunk_id',
        'claim_id',
        'exact_quote',
        'relevance_score',
        'citation_order',
    ];

    protected $casts = [
        'response_id' => 'integer',
        'chunk_id' => 'integer',
        'relevance_score' => 'decimal:2',
        'citation_order' => 'integer',
        'created_at' => 'datetime',
    ];

    public function response(): BelongsTo
    {
        return $this->belongsTo(Response::class, 'response_id');
    }

    public function chunk(): BelongsTo
    {
        return $this->belongsTo(Chunk::class, 'chunk_id');
    }

    public function scopeHighRelevance($query, float $minScore = 7.0)
    {
        return $query->where('relevance_score', '>=', $minScore);
    }
}
