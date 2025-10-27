<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Project Model
 *
 * PA user projects for document upload and priority RAG search
 *
 * @package App\Models
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Projects System)
 * @date 2025-10-27
 * @purpose Store PA projects with document management and RAG capabilities
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string|null $description
 * @property string $icon
 * @property string $color
 * @property array|null $settings
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read User $owner
 * @property-read \Illuminate\Database\Eloquent\Collection|ProjectDocument[] $documents
 * @property-read \Illuminate\Database\Eloquent\Collection|NatanChatMessage[] $chatMessages
 */
class Project extends Model {
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'icon',
        'color',
        'settings',
        'is_active',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
    ];

    protected $attributes = [
        'icon' => 'folder_open',
        'color' => '#1B365D',
        'is_active' => true,
        'settings' => '{"max_documents":50,"max_size_mb":10,"auto_embed":true,"priority_rag":true,"allowed_types":["pdf","docx","txt","csv","xlsx","md"]}',
    ];

    /**
     * Get project owner (PA user)
     */
    public function owner(): BelongsTo {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get project documents
     */
    public function documents(): HasMany {
        return $this->hasMany(ProjectDocument::class);
    }

    /**
     * Get project chat messages
     */
    public function chatMessages(): HasMany {
        return $this->hasMany(NatanChatMessage::class);
    }

    /**
     * Scope: only active projects
     */
    public function scopeActive($query) {
        return $query->where('is_active', true);
    }

    /**
     * Scope: for specific user
     */
    public function scopeForUser($query, User $user) {
        return $query->where('user_id', $user->id);
    }

    /**
     * Get documents count
     */
    public function getDocumentsCountAttribute(): int {
        return $this->documents()->count();
    }

    /**
     * Get ready documents count
     */
    public function getReadyDocumentsCountAttribute(): int {
        return $this->documents()->where('status', 'ready')->count();
    }

    /**
     * Check if project can accept more documents
     */
    public function canAddDocument(): bool {
        $maxDocuments = $this->settings['max_documents'] ?? 50;
        return $this->documents()->count() < $maxDocuments;
    }

    /**
     * Get max file size in bytes
     */
    public function getMaxFileSizeBytes(): int {
        $maxSizeMb = $this->settings['max_size_mb'] ?? 10;
        return $maxSizeMb * 1024 * 1024;
    }

    /**
     * Get allowed file types
     */
    public function getAllowedFileTypes(): array {
        return $this->settings['allowed_types'] ?? ['pdf', 'docx', 'txt', 'csv', 'xlsx', 'md'];
    }
}
