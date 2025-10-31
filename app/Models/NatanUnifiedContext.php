<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class NatanUnifiedContext extends Model {
    protected $table = 'natan_unified_context';

    protected $fillable = [
        'session_id',
        'content',
        'embedding',
        'source_type',
        'source_id',
        'source_url',
        'source_title',
        'metadata',
        'similarity_score',
        'expires_at',
    ];

    protected $casts = [
        'embedding' => 'array', // JSON array di 1536 float (MariaDB compatible)
        'metadata' => 'array',
        'similarity_score' => 'float',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope: filtra solo chunks non scaduti
     */
    public function scopeActive(Builder $query): Builder {
        return $query->where('expires_at', '>', now());
    }

    /**
     * Scope: filtra per session_id
     */
    public function scopeForSession(Builder $query, string $sessionId): Builder {
        return $query->where('session_id', $sessionId);
    }

    /**
     * Scope: filtra per tipo di fonte
     */
    public function scopeOfType(Builder $query, string $type): Builder {
        return $query->where('source_type', $type);
    }

    /**
     * Scope: filtra chunks scaduti (per cleanup)
     */
    public function scopeExpired(Builder $query): Builder {
        return $query->where('expires_at', '<=', now());
    }

    /**
     * Relazione polimorfica alla fonte originale (opzionale)
     */
    public function source() {
        // Non implementato ancora - richiede colonne polymorphic
        // return $this->morphTo();
    }

    /**
     * Formatta per citazione in risposta Claude
     */
    public function formatForCitation(): string {
        $typeLabel = match ($this->source_type) {
            'act' => 'ATTO',
            'web' => 'WEB',
            'memory' => 'MEMORIA',
            'file' => 'FILE',
        };

        $similarity = $this->similarity_score ? sprintf('%.0f%%', $this->similarity_score * 100) : 'N/A';

        $citation = "[$typeLabel] {$this->source_title} (Rilevanza: {$similarity})";

        if ($this->source_url) {
            $citation .= "\nURL: {$this->source_url}";
        }

        if ($this->metadata && isset($this->metadata['date'])) {
            $citation .= "\nData: {$this->metadata['date']}";
        }

        return $citation;
    }
}
