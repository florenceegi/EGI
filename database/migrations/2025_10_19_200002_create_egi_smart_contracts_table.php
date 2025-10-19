<?php

/**
 * @package App\Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Dual Architecture)
 * @date 2025-10-19
 * @purpose Create egi_smart_contracts table for SmartContract EGI metadata
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * Creates table to store detailed SmartContract metadata and state.
     * Only populated for egi_type = 'SmartContract'.
     * Tracks AI interactions, triggers, and on-chain state.
     */
    public function up(): void
    {
        Schema::create('egi_smart_contracts', function (Blueprint $table) {
            $table->id();

            // === CORE RELATIONSHIP ===
            $table->foreignId('egi_id')
                ->unique()
                ->constrained('egis')
                ->onDelete('cascade')
                ->comment('EGI this SmartContract belongs to (1:1 relationship)');

            // === ALGORAND SMART CONTRACT DATA ===
            $table->string('app_id')->unique()
                ->comment('Algorand Application ID (SmartContract address)');

            $table->string('creator_address')
                ->comment('Algorand address that deployed the SC');

            $table->string('authorized_agent_address')
                ->comment('Oracolo wallet authorized to call update_state()');

            $table->string('deployment_tx_id')
                ->comment('Algorand transaction ID for SC deployment');

            $table->timestamp('deployed_at')
                ->comment('When SC was deployed on-chain');

            // === SMART CONTRACT STATUS ===
            $table->enum('sc_status', [
                'deploying',
                'active',
                'paused',
                'terminated'
            ])->default('deploying')
                ->comment('Current SmartContract lifecycle status');

            // === AI TRIGGER CONFIGURATION ===
            $table->integer('trigger_interval')->default(86400)
                ->comment('Seconds between AI analysis triggers (default 24h)');

            $table->timestamp('next_trigger_at')->nullable()
                ->comment('Timestamp for next scheduled AI analysis');

            $table->timestamp('last_trigger_at')->nullable()
                ->comment('Last time AI analysis was triggered');

            $table->integer('total_triggers_count')->default(0)
                ->comment('Total number of triggers executed');

            // === ON-CHAIN STATE SNAPSHOT ===
            $table->string('metadata_hash')->nullable()
                ->comment('Current IPFS hash of EGI metadata (synced from SC)');

            $table->string('license_id')->nullable()
                ->comment('Current license ID stored in SC');

            $table->string('terms_hash')->nullable()
                ->comment('Hash of current terms and conditions');

            $table->string('anchoring_root')->nullable()
                ->comment('Merkle root for daily anchoring');

            $table->json('global_state_snapshot')->nullable()
                ->comment('Full snapshot of SC global state (JSON)');

            $table->timestamp('state_last_synced_at')->nullable()
                ->comment('Last time state was synced from blockchain');

            // === AI EXECUTION TRACKING ===
            $table->integer('ai_executions_success')->default(0)
                ->comment('Number of successful AI executions');

            $table->integer('ai_executions_failed')->default(0)
                ->comment('Number of failed AI executions');

            $table->json('last_ai_result')->nullable()
                ->comment('Result of last AI analysis (JSON)');

            $table->timestamp('last_ai_result_at')->nullable()
                ->comment('Timestamp of last AI analysis result');

            // === ERROR TRACKING ===
            $table->text('last_error')->nullable()
                ->comment('Last error message from SC or AI execution');

            $table->timestamp('last_error_at')->nullable()
                ->comment('Timestamp of last error');

            // === METADATA ===
            $table->json('sc_metadata')->nullable()
                ->comment('Additional SmartContract metadata (features, config, etc.)');

            $table->timestamps();

            // === INDEXES FOR PERFORMANCE ===
            $table->index('egi_id', 'idx_egi_sc_egi_id');
            $table->index('app_id', 'idx_egi_sc_app_id');
            $table->index('sc_status', 'idx_egi_sc_status');
            $table->index('next_trigger_at', 'idx_egi_sc_next_trigger');
            $table->index(['sc_status', 'next_trigger_at'], 'idx_egi_sc_status_trigger');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('egi_smart_contracts');
    }
};
