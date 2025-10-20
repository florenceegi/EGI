<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\SmartContractStatus;

/**
 * @package App\Models
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Dual Architecture)
 * @date 2025-10-19
 * @purpose Model for EGI SmartContract metadata and state
 *
 * @property int $id
 * @property int $egi_id
 * @property string $app_id Algorand Application ID
 * @property string $creator_address
 * @property string $authorized_agent_address Oracle wallet address
 * @property string $deployment_tx_id
 * @property \Carbon\Carbon $deployed_at
 * @property string $sc_status SmartContract status (deploying|active|paused|terminated)
 * @property int $trigger_interval Seconds between AI triggers
 * @property \Carbon\Carbon|null $next_trigger_at
 * @property \Carbon\Carbon|null $last_trigger_at
 * @property int $total_triggers_count
 * @property string|null $metadata_hash IPFS hash of current metadata
 * @property string|null $license_id
 * @property string|null $terms_hash
 * @property string|null $anchoring_root Merkle root
 * @property array|null $global_state_snapshot Full SC state as JSON
 * @property \Carbon\Carbon|null $state_last_synced_at
 * @property int $ai_executions_success
 * @property int $ai_executions_failed
 * @property array|null $last_ai_result
 * @property \Carbon\Carbon|null $last_ai_result_at
 * @property string|null $last_error
 * @property \Carbon\Carbon|null $last_error_at
 * @property array|null $sc_metadata Additional metadata
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @property-read Egi $egi The EGI this SmartContract belongs to
 */
class EgiSmartContract extends Model
{
    use HasFactory;

    protected $table = 'egi_smart_contracts';

    protected $fillable = [
        'egi_id',
        'app_id',
        'creator_address',
        'authorized_agent_address',
        'deployment_tx_id',
        'deployed_at',
        'sc_status',
        'trigger_interval',
        'next_trigger_at',
        'last_trigger_at',
        'total_triggers_count',
        'metadata_hash',
        'license_id',
        'terms_hash',
        'anchoring_root',
        'global_state_snapshot',
        'state_last_synced_at',
        'ai_executions_success',
        'ai_executions_failed',
        'last_ai_result',
        'last_ai_result_at',
        'last_error',
        'last_error_at',
        'sc_metadata',
    ];

    protected $casts = [
        'deployed_at' => 'datetime',
        'next_trigger_at' => 'datetime',
        'last_trigger_at' => 'datetime',
        'state_last_synced_at' => 'datetime',
        'last_ai_result_at' => 'datetime',
        'last_error_at' => 'datetime',
        'trigger_interval' => 'integer',
        'total_triggers_count' => 'integer',
        'ai_executions_success' => 'integer',
        'ai_executions_failed' => 'integer',
        'global_state_snapshot' => 'array',
        'last_ai_result' => 'array',
        'sc_metadata' => 'array',
    ];

    /**
     * Get the EGI this SmartContract belongs to
     */
    public function egi(): BelongsTo
    {
        return $this->belongsTo(Egi::class);
    }

    /**
     * Check if SmartContract is active
     */
    public function isActive(): bool
    {
        return $this->sc_status === SmartContractStatus::ACTIVE->value;
    }

    /**
     * Check if AI trigger is ready
     */
    public function isTriggerReady(): bool
    {
        return $this->isActive() &&
            $this->next_trigger_at &&
            $this->next_trigger_at->isPast();
    }

    /**
     * Get Algorand explorer URL for this SmartContract
     */
    public function getExplorerUrl(): string
    {
        $network = config('algorand.network', 'testnet');
        $baseUrl = $network === 'mainnet'
            ? 'https://algoexplorer.io'
            : 'https://testnet.algoexplorer.io';

        return "{$baseUrl}/application/{$this->app_id}";
    }

    /**
     * Get success rate for AI executions
     */
    public function getAISuccessRate(): float
    {
        $total = $this->ai_executions_success + $this->ai_executions_failed;

        if ($total === 0) {
            return 0.0;
        }

        return ($this->ai_executions_success / $total) * 100;
    }

    /**
     * Scope to get SmartContracts ready for AI trigger
     */
    public function scopeReadyForTrigger($query)
    {
        return $query->where('sc_status', SmartContractStatus::ACTIVE->value)
            ->where('next_trigger_at', '<=', now());
    }

    /**
     * Scope to get active SmartContracts
     */
    public function scopeActive($query)
    {
        return $query->where('sc_status', SmartContractStatus::ACTIVE->value);
    }
}

