<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 * N.A.T.A.N. EgiAct Model
 *
 * Represents an administrative act with AI-extracted metadata
 *
 * @package App\Models
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - N.A.T.A.N.)
 * @date 2025-10-09
 *
 * @property int $id
 * @property string $document_id
 * @property string $tipo_atto
 * @property string|null $numero_atto
 * @property string $data_atto
 * @property string $oggetto
 * @property string|null $ente
 * @property string|null $direzione
 * @property string|null $responsabile
 * @property float|null $importo
 * @property array $metadata_json
 * @property string|null $hash_firma
 * @property string|null $blockchain_tx
 * @property string|null $qr_code
 * @property string $processing_status
 * @property int|null $ai_tokens_used
 * @property float|null $ai_cost
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 */
class EgiAct extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'egi_acts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'document_id',
        'tipo_atto',
        'numero_atto',
        'data_atto',
        'oggetto',
        'ente',
        'direzione',
        'responsabile',
        'importo',
        'metadata_json',
        'hash_firma',
        'blockchain_tx',
        'qr_code',
        'processing_status',
        'ai_tokens_used',
        'ai_cost',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data_atto' => 'date',
        'importo' => 'decimal:2',
        'metadata_json' => 'array',
        'ai_tokens_used' => 'integer',
        'ai_cost' => 'decimal:4',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<string>
     */
    protected $hidden = [
        // Hash firma potrebbe contenere dati sensibili
    ];

    /**
     * Spatie Activity Log configuration
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'tipo_atto',
                'numero_atto',
                'oggetto',
                'importo',
                'processing_status'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Boot method for model events
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate document_id if not provided
        static::creating(function ($model) {
            if (empty($model->document_id)) {
                $model->document_id = bin2hex(random_bytes(16));
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope: Filter by tipo_atto
     */
    public function scopeOfType($query, string $tipo)
    {
        return $query->where('tipo_atto', $tipo);
    }

    /**
     * Scope: Filter by ente
     */
    public function scopeByEnte($query, string $ente)
    {
        return $query->where('ente', $ente);
    }

    /**
     * Scope: Filter by direzione
     */
    public function scopeByDirezione($query, string $direzione)
    {
        return $query->where('direzione', $direzione);
    }

    /**
     * Scope: Filter by date range
     */
    public function scopeDateRange($query, $from = null, $to = null)
    {
        if ($from) {
            $query->where('data_atto', '>=', $from);
        }
        if ($to) {
            $query->where('data_atto', '<=', $to);
        }
        return $query;
    }

    /**
     * Scope: Filter by importo range
     */
    public function scopeImportoRange($query, $min = null, $max = null)
    {
        if ($min !== null) {
            $query->where('importo', '>=', $min);
        }
        if ($max !== null) {
            $query->where('importo', '<=', $max);
        }
        return $query;
    }

    /**
     * Scope: Only completed acts
     */
    public function scopeCompleted($query)
    {
        return $query->where('processing_status', 'completed');
    }

    /**
     * Scope: Only pending acts
     */
    public function scopePending($query)
    {
        return $query->where('processing_status', 'pending');
    }

    /**
     * Scope: Only failed acts
     */
    public function scopeFailed($query)
    {
        return $query->where('processing_status', 'failed');
    }

    /**
     * Scope: Full-text search on oggetto
     */
    public function scopeSearch($query, string $searchTerm)
    {
        if (config('database.default') === 'mysql') {
            // MySQL fulltext search
            return $query->whereRaw(
                'MATCH(oggetto) AGAINST(? IN BOOLEAN MODE)',
                [$searchTerm]
            );
        } else {
            // Fallback to LIKE for other databases
            return $query->where('oggetto', 'LIKE', "%{$searchTerm}%");
        }
    }

    /**
     * Scope: Recent acts (last 30 days)
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators
    |--------------------------------------------------------------------------
    */

    /**
     * Get categories from metadata_json
     */
    public function getCategoriaAttribute(): array
    {
        return $this->metadata_json['categoria'] ?? [];
    }

    /**
     * Get firmatari from metadata_json
     */
    public function getFirmatariAttribute(): array
    {
        return $this->metadata_json['firmatari'] ?? [];
    }

    /**
     * Get urgenza from metadata_json
     */
    public function getUrgenzaAttribute(): ?string
    {
        return $this->metadata_json['urgenza'] ?? null;
    }

    /**
     * Get scadenza from metadata_json
     */
    public function getScadenzaAttribute(): ?string
    {
        return $this->metadata_json['scadenza'] ?? null;
    }

    /**
     * Check if act is blockchain certified
     */
    public function getIsCertifiedAttribute(): bool
    {
        return !empty($this->blockchain_tx);
    }

    /**
     * Get verification URL
     */
    public function getVerificationUrlAttribute(): ?string
    {
        if (!$this->qr_code) {
            return null;
        }

        return route('natan.verify', ['documentId' => $this->document_id]);
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Mark act as completed
     */
    public function markAsCompleted(): self
    {
        $this->update(['processing_status' => 'completed']);
        return $this;
    }

    /**
     * Mark act as failed
     */
    public function markAsFailed(): self
    {
        $this->update(['processing_status' => 'failed']);
        return $this;
    }

    /**
     * Get formatted importo
     */
    public function getFormattedImporto(): string
    {
        if (!$this->importo) {
            return '-';
        }

        return '€ ' . number_format($this->importo, 2, ',', '.');
    }

    /**
     * Get formatted data_atto
     */
    public function getFormattedData(): string
    {
        return $this->data_atto->format('d/m/Y');
    }

    /**
     * Get short oggetto (truncated)
     */
    public function getShortOggetto(int $length = 100): string
    {
        return strlen($this->oggetto) > $length
            ? substr($this->oggetto, 0, $length) . '...'
            : $this->oggetto;
    }

    /**
     * Get AI cost formatted
     */
    public function getFormattedAiCost(): string
    {
        if (!$this->ai_cost) {
            return '€ 0.00';
        }

        return '€ ' . number_format($this->ai_cost, 4, ',', '.');
    }

    /**
     * Check if act has QR code
     */
    public function hasQrCode(): bool
    {
        return !empty($this->qr_code);
    }

    /**
     * Check if processing is complete
     */
    public function isCompleted(): bool
    {
        return $this->processing_status === 'completed';
    }

    /**
     * Check if processing is pending
     */
    public function isPending(): bool
    {
        return $this->processing_status === 'pending';
    }

    /**
     * Check if processing has failed
     */
    public function isFailed(): bool
    {
        return $this->processing_status === 'failed';
    }
}
