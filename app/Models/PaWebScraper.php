<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaWebScraper extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'source_entity',
        'description',
        'base_url',
        'api_endpoint',
        'method',
        'headers',
        'payload_template',
        'query_params',
        'data_mapping',
        'pagination_type',
        'pagination_config',
        'is_active',
        'schedule_frequency',
        'last_run_at',
        'next_run_at',
        'total_items_scraped',
        'user_id',
        'created_by_user_id',
        'status',
        'last_error',
        'stats',
        // GDPR fields
        'data_source_type',
        'legal_basis',
        'data_retention_policy',
        'gdpr_compliant',
        'pii_fields_to_exclude',
        'last_gdpr_audit_at',
    ];

    protected $casts = [
        'headers' => 'array',
        'payload_template' => 'array',
        'query_params' => 'array',
        'data_mapping' => 'array',
        'pagination_config' => 'array',
        'stats' => 'array',
        'pii_fields_to_exclude' => 'array',
        'is_active' => 'boolean',
        'gdpr_compliant' => 'boolean',
        'last_run_at' => 'datetime',
        'next_run_at' => 'datetime',
        'last_gdpr_audit_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('status', 'active');
    }

    public function scopeDueForExecution($query)
    {
        return $query->active()
            ->where('next_run_at', '<=', now())
            ->orWhereNull('next_run_at');
    }

    // Metodi helper
    public function getFullUrl(): string
    {
        if ($this->api_endpoint) {
            return rtrim($this->base_url, '/') . '/' . ltrim($this->api_endpoint, '/');
        }
        return $this->base_url;
    }

    public function markAsRunning(): void
    {
        $this->update([
            'status' => 'running',
            'last_run_at' => now(),
        ]);
    }

    public function markAsSuccess(int $itemsScraped, array $stats = []): void
    {
        $this->update([
            'status' => 'active',
            'total_items_scraped' => $this->total_items_scraped + $itemsScraped,
            'last_error' => null,
            'stats' => $stats,
            'next_run_at' => $this->calculateNextRun(),
        ]);
    }

    public function markAsError(string $error): void
    {
        $this->update([
            'status' => 'error',
            'last_error' => $error,
            'next_run_at' => $this->calculateNextRun(),
        ]);
    }

    protected function calculateNextRun(): ?\Carbon\Carbon
    {
        if (!$this->schedule_frequency || $this->schedule_frequency === 'manual') {
            return null;
        }

        return match ($this->schedule_frequency) {
            'hourly' => now()->addHour(),
            'daily' => now()->addDay(),
            'weekly' => now()->addWeek(),
            'monthly' => now()->addMonth(),
            default => null,
        };
    }

    // Metodo per testare la connessione
    public function testConnection(): array
    {
        try {
            $client = new \GuzzleHttp\Client();

            $options = [
                'timeout' => 10,
                'headers' => $this->headers ?? [],
            ];

            if ($this->method === 'POST') {
                $options['json'] = $this->payload_template ?? [];
            } else {
                $options['query'] = $this->query_params ?? [];
            }

            $response = $client->request($this->method, $this->getFullUrl(), $options);

            return [
                'success' => true,
                'status_code' => $response->getStatusCode(),
                'message' => 'Connection successful',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
