<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * PA Act Embedding Model
 *
 * Stores vector embeddings for semantic search on PA acts.
 *
 * @property int $id
 * @property int $egi_id
 * @property array $embedding Vector of 1536 floats
 * @property string $model Embedding model name
 * @property string $content_hash SHA256 of embedded content
 * @property int $vector_dimension Dimension count (1536)
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 *
 * @property-read Egi $egi
 */
class PaActEmbedding extends Model
{
    protected $table = 'pa_act_embeddings';

    protected $fillable = [
        'egi_id',
        'embedding',
        'model',
        'content_hash',
        'vector_dimension',
    ];

    protected $casts = [
        'embedding' => 'array',
        'vector_dimension' => 'integer',
    ];

    /**
     * Get the EGI (PA Act) this embedding belongs to
     */
    public function egi(): BelongsTo
    {
        return $this->belongsTo(Egi::class, 'egi_id');
    }
}
