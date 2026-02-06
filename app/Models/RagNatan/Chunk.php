<?php

namespace App\Models\RagNatan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * RAG Chunk Model
 *
 * Document chunks (paragraphs/sections) for granular vector search.
 *
 * @property int $id
 * @property string $uuid
 * @property int $document_id
 * @property string $text
 * @property string|null $section_title
 * @property int $chunk_order
 * @property int|null $char_start
 * @property int|null $char_end
 * @property int|null $token_count
 * @property string $chunk_type
 * @property string $language
 * @property array $metadata
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Chunk extends Model
{
    protected $table = 'rag_natan.chunks';

    protected $fillable = [
        'uuid',
        'document_id',
        'text',
        'section_title',
        'chunk_order',
        'char_start',
        'char_end',
        'token_count',
        'chunk_type',
        'language',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'chunk_order' => 'integer',
        'char_start' => 'integer',
        'char_end' => 'integer',
        'token_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'document_id');
    }

    public function embedding(): HasOne
    {
        return $this->hasOne(Embedding::class, 'chunk_id');
    }

    public function sources(): HasMany
    {
        return $this->hasMany(Source::class, 'chunk_id');
    }

    public function scopeLanguage($query, string $lang)
    {
        return $query->where('language', $lang);
    }

    public function scopeType($query, string $type)
    {
        return $query->where('chunk_type', $type);
    }
}
