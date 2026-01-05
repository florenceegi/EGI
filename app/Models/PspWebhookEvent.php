<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * P0.5 GAP FIX: DB-based webhook idempotency model
 *
 * Replaces cache-based approach with persistent storage
 * Prevents duplicate webhook processing across multiple servers
 */
class PspWebhookEvent extends Model {
    use HasFactory;

    protected $fillable = [
        'provider',
        'event_id',
        'event_type',
        'status',
        'payload',
        'error_message',
        'retry_count',
        'received_at',
        'processed_at'
    ];

    protected $casts = [
        'payload' => 'array',
        'received_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    /**
     * Scopes for common queries
     */
    public function scopeProcessing($query) {
        return $query->where('status', 'processing');
    }

    public function scopeStuckProcessing($query, int $timeoutMinutes = 5) {
        return $query->where('status', 'processing')
            ->where('received_at', '<', now()->subMinutes($timeoutMinutes));
    }

    public function scopeFailed($query) {
        return $query->where('status', 'failed');
    }

    public function scopeForRetry($query, int $maxRetries = 3) {
        return $query->where('status', 'failed')
            ->where('retry_count', '<', $maxRetries);
    }

    /**
     * Mark event as processed
     */
    public function markProcessed(): void {
        $this->update([
            'status' => 'processed',
            'processed_at' => now()
        ]);
    }

    /**
     * Mark event as failed
     */
    public function markFailed(string $errorMessage): void {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
            'retry_count' => $this->retry_count + 1
        ]);
    }

    /**
     * Reset for retry
     */
    public function resetForRetry(): void {
        $this->update([
            'status' => 'processing',
            'error_message' => null,
            'processed_at' => null,
            'retry_count' => $this->retry_count + 1
        ]);
    }
}
