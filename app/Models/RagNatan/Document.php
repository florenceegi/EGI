<?php

namespace App\Models\RagNatan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * RAG Document Model
 *
 * Master documents table with full-text search, multi-language support,
 * and hierarchical categorization.
 *
 * @property int $id
 * @property string $uuid
 * @property int|null $category_id
 * @property string $title
 * @property string $slug
 * @property string $content
 * @property string|null $excerpt
 * @property string $language
 * @property string|null $document_type
 * @property string $version
 * @property string $status
 * @property array|null $tags
 * @property array|null $keywords
 * @property array $metadata
 * @property int $view_count
 * @property int $helpful_count
 * @property \Carbon\Carbon|null $last_indexed_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $published_at
 * @property \Carbon\Carbon|null $archived_at
 */
class Document extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'rag_natan.documents';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'uuid',
        'category_id',
        'title',
        'slug',
        'content',
        'excerpt',
        'language',
        'document_type',
        'version',
        'status',
        'tags',
        'keywords',
        'metadata',
        'view_count',
        'helpful_count',
        'last_indexed_at',
        'published_at',
        'archived_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'tags' => \App\Casts\PostgreSqlArray::class,          // PostgreSQL TEXT[]
        'keywords' => \App\Casts\PostgreSqlArray::class,      // PostgreSQL TEXT[]
        'metadata' => 'array',      // PostgreSQL JSONB
        'view_count' => 'integer',
        'helpful_count' => 'integer',
        'last_indexed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'published_at' => 'datetime',
        'archived_at' => 'datetime',
    ];

    /**
     * Get the category that owns the document.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Get all chunks for this document.
     */
    public function chunks(): HasMany
    {
        return $this->hasMany(Chunk::class, 'document_id')->orderBy('chunk_order');
    }

    /**
     * Scope: Only published documents.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope: Filter by language.
     */
    public function scopeLanguage($query, string $lang)
    {
        return $query->where('language', $lang);
    }

    /**
     * Scope: Filter by document type.
     */
    public function scopeType($query, string $type)
    {
        return $query->where('document_type', $type);
    }

    /**
     * Scope: Full-text search (uses PostgreSQL tsvector).
     */
    public function scopeSearch($query, string $searchTerm)
    {
        return $query->whereRaw(
            "search_vector @@ plainto_tsquery('italian', ?)",
            [$searchTerm]
        );
    }
}
