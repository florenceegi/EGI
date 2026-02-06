<?php

namespace App\Models\RagNatan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * RAG Category Model
 *
 * Hierarchical document categorization for RAG system.
 * Supports i18n via translation keys and visual organization.
 *
 * @property int $id
 * @property string $slug
 * @property string $name_key
 * @property string|null $description_key
 * @property string|null $icon
 * @property string|null $color
 * @property int|null $parent_id
 * @property int $sort_order
 * @property bool $is_active
 * @property array $metadata
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Category extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'rag_natan.categories';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'slug',
        'name_key',
        'description_key',
        'icon',
        'color',
        'parent_id',
        'sort_order',
        'is_active',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'metadata' => 'array',
        'sort_order' => 'integer',
        'parent_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the parent category.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get the child categories.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort_order');
    }

    /**
     * Get all documents in this category.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'category_id');
    }

    /**
     * Scope: Only active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Root categories (no parent).
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }
}
