<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @package App\Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Feature Consumption Tracking)
 * @date 2025-11-03
 * @purpose Create feature_consumption_ledger for granular consumption tracking (token-based, unit-based, time-based)
 * 
 * Architecture:
 * - Supports fractional costs (DECIMAL) before batch charging (INT)
 * - Scalable for ALL consumption-based features (AI chat, traits, analysis, API calls)
 * - Complete audit trail with metadata
 * - Batch charging when pending debt reaches threshold
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('feature_consumption_ledger', function (Blueprint $table) {
            $table->id();
            
            // === USER & FEATURE ===
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('User who consumed the feature');
            
            $table->string('feature_code', 100)
                ->comment('Feature code from ai_feature_pricing (e.g., ai_chat_assistant)');
            
            // === CONSUMPTION DETAILS ===
            $table->enum('consumption_type', ['token_based', 'unit_based', 'time_based'])
                ->default('unit_based')
                ->comment('How consumption is measured');
            
            $table->decimal('units_consumed', 10, 4)
                ->comment('Amount consumed (e.g., 650.5 tokens, 1.0 use, 2.5 hours)');
            
            $table->string('unit_type', 50)
                ->comment('Unit of measure (tokens, api_calls, hours, uses, messages)');
            
            // === COST CALCULATION (Fractional) ===
            $table->decimal('cost_per_unit', 10, 6)
                ->comment('Egili cost per single unit (e.g., 0.000001 Egili/token)');
            
            $table->decimal('total_cost_egili', 10, 4)
                ->comment('Total fractional cost in Egili (e.g., 0.6505 Egili)');
            
            // === BILLING STATUS ===
            $table->enum('billing_status', ['pending', 'batched', 'charged'])
                ->default('pending')
                ->comment('pending = accumulating, batched = included in charge, charged = paid');
            
            $table->foreignId('batched_in_transaction_id')
                ->nullable()
                ->constrained('egili_transactions')
                ->onDelete('set null')
                ->comment('Link to egili_transactions when batch charged');
            
            $table->timestamp('charged_at')
                ->nullable()
                ->comment('When this consumption was charged to user');
            
            // === REQUEST METADATA (GDPR Audit) ===
            $table->json('request_metadata')
                ->nullable()
                ->comment('Service-specific metadata (model, prompt_length, conversation_id, etc)');
            
            $table->string('ip_address', 45)
                ->nullable()
                ->comment('User IP at consumption time');
            
            $table->text('user_agent')
                ->nullable()
                ->comment('User agent string');
            
            // === TIMESTAMPS ===
            $table->timestamp('consumed_at')
                ->useCurrent()
                ->comment('When feature was consumed');
            
            $table->timestamps();
            
            // === INDEXES FOR PERFORMANCE ===
            $table->index(['user_id', 'billing_status'], 'idx_user_billing');
            $table->index(['feature_code', 'billing_status'], 'idx_feature_billing');
            $table->index('consumed_at', 'idx_consumed_at');
            $table->index('billing_status', 'idx_billing_status');
            $table->index('batched_in_transaction_id', 'idx_batched_transaction');
            
            // Foreign key for feature_code validation (soft - no constraint)
            // We don't add FK constraint because ai_feature_pricing may have soft deletes
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feature_consumption_ledger');
    }
};


















