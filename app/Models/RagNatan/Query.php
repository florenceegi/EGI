<?php

namespace App\Models\RagNatan;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * RAG Query Model
 *
 * User query log & analytics.
 *
 * @property int $id
 * @property string $uuid
 * @property int|null $user_id
 * @property int|null $response_id
 * @property string $question
 * @property string $question_hash
 * @property string $language
 * @property array $context
 * @property float|null $urs_score
 * @property int|null $answer_length
 * @property int|null $chunks_used
 * @property int|null $response_time_ms
 * @property bool|null $was_helpful
 * @property string|null $feedback_text
 * @property int $view_count
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string|null $session_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon|null $responded_at
 */
class Query extends Model
{
    protected $table = 'rag_natan.queries';

    const UPDATED_AT = null; // Only has created_at and responded_at

    protected $fillable = [
        'uuid',
        'user_id',
        'response_id',
        'question',
        'question_hash',
        'language',
        'context',
        'urs_score',
        'answer_length',
        'chunks_used',
        'response_time_ms',
        'was_helpful',
        'feedback_text',
        'view_count',
        'ip_address',
        'user_agent',
        'session_id',
        'responded_at',
    ];

    protected $casts = [
        'context' => 'array',
        'urs_score' => 'decimal:2',
        'answer_length' => 'integer',
        'chunks_used' => 'integer',
        'response_time_ms' => 'integer',
        'view_count' => 'integer',
        'was_helpful' => 'boolean',
        'created_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function response(): BelongsTo
    {
        return $this->belongsTo(Response::class, 'response_id');
    }

    public function scopeLanguage($query, string $lang)
    {
        return $query->where('language', $lang);
    }

    public function scopeHelpful($query)
    {
        return $query->where('was_helpful', true);
    }

    public function scopeUnhelpful($query)
    {
        return $query->where('was_helpful', false);
    }
}
