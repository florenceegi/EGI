<?php

namespace App\Models\RagNatan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * RAG QueryCache Model
 *
 * Query result cache for performance optimization.
 *
 * @property int $id
 * @property string $cache_key
 * @property string $question_hash
 * @property int|null $response_id
 * @property string $language
 * @property string|null $context_hash
 * @property int $hit_count
 * @property \Carbon\Carbon|null $last_hit_at
 * @property \Carbon\Carbon $expires_at
 * @property bool $is_stale
 * @property \Carbon\Carbon $created_at
 */
class QueryCache extends Model
{
    protected $table = 'rag_natan.query_cache';

    const UPDATED_AT = null; // Only has created_at

    protected $fillable = [
        'cache_key',
        'question_hash',
        'response_id',
        'language',
        'context_hash',
        'hit_count',
        'last_hit_at',
        'expires_at',
        'is_stale',
    ];

    protected $casts = [
        'response_id' => 'integer',
        'hit_count' => 'integer',
        'is_stale' => 'boolean',
        'last_hit_at' => 'datetime',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function response(): BelongsTo
    {
        return $this->belongsTo(Response::class, 'response_id');
    }

    /**
     * Scope: Only valid (not expired, not stale) cache entries.
     */
    public function scopeValid($query)
    {
        return $query
            ->where('is_stale', false)
            ->where('expires_at', '>', now());
    }

    /**
     * Scope: Expired cache entries.
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }

    /**
     * Increment hit counter and update last_hit_at.
     */
    public function recordHit(): void
    {
        $this->increment('hit_count');
        $this->update(['last_hit_at' => now()]);
    }
}
