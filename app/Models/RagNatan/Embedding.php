<?php

namespace App\Models\RagNatan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * RAG Embedding Model
 *
 * Vector embeddings (1:1 with chunks) for similarity search.
 *
 * @property int $id
 * @property int $chunk_id
 * @property string $embedding  // vector(1536) - stored as pgvector type
 * @property string $model
 * @property string|null $model_version
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Embedding extends Model
{
    protected $table = 'rag_natan.embeddings';

    protected $fillable = [
        'chunk_id',
        'embedding',
        'model',
        'model_version',
    ];

    protected $casts = [
        'chunk_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        // Note: 'embedding' is pgvector type, handled as string in Laravel
    ];

    public function chunk(): BelongsTo
    {
        return $this->belongsTo(Chunk::class, 'chunk_id');
    }

    /**
     * Scope: Find similar embeddings by vector distance.
     *
     * @param array $queryVector Array of floats (1536 dimensions)
     * @param int $limit Number of results to return
     */
    public function scopeSimilar($query, array $queryVector, int $limit = 20)
    {
        $vectorString = '[' . implode(',', $queryVector) . ']';

        return $query
            ->selectRaw('*, (embedding <-> ?) as distance', [$vectorString])
            ->orderBy('distance')
            ->limit($limit);
    }
}
