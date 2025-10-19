<?php

/**
 * @package App\Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Dual Architecture)
 * @date 2025-10-19
 * @purpose Add dual architecture support (ASA/SmartContract/PreMint) to egis table
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * Adds fields to support EGI Dual Architecture:
     * - egi_type: ASA (classic) | SmartContract (living) | PreMint (AI-managed virtual)
     * - pre_mint_mode: For PreMint EGIs, tracks if it's in promotion/test phase
     * - egi_living_enabled: Flag for premium "EGI Vivente" features
     * - egi_living_activated_at: Timestamp when living features were enabled
     */
    public function up(): void
    {
        Schema::table('egis', function (Blueprint $table) {
            // === DUAL ARCHITECTURE TYPE ===
            $table->enum('egi_type', ['ASA', 'SmartContract', 'PreMint'])
                ->default('ASA')
                ->after('status')
                ->comment('EGI architecture type: ASA=classic, SmartContract=living, PreMint=AI virtual');

            // === PRE-MINT TRACKING ===
            $table->boolean('pre_mint_mode')->default(false)
                ->after('egi_type')
                ->comment('True if EGI is in pre-mint phase (not yet on blockchain)');

            $table->timestamp('pre_mint_created_at')->nullable()
                ->after('pre_mint_mode')
                ->comment('When EGI was created in pre-mint mode');

            // === LIVING EGI PREMIUM FEATURES ===
            $table->boolean('egi_living_enabled')->default(false)
                ->after('pre_mint_created_at')
                ->comment('True if EGI Vivente features are active (premium)');

            $table->timestamp('egi_living_activated_at')->nullable()
                ->after('egi_living_enabled')
                ->comment('When EGI Vivente was activated');

            $table->foreignId('egi_living_subscription_id')->nullable()
                ->after('egi_living_activated_at')
                ->constrained('egi_living_subscriptions')
                ->onDelete('set null')
                ->comment('Link to active living subscription (premium)');

            // === SMART CONTRACT REFERENCE ===
            // Note: detailed SC data in egi_smart_contracts table
            $table->string('smart_contract_app_id')->nullable()
                ->after('egi_living_subscription_id')
                ->comment('Algorand SmartContract Application ID (if egi_type=SmartContract)');

            // === INDEXES FOR PERFORMANCE ===
            $table->index('egi_type', 'idx_egis_egi_type');
            $table->index('pre_mint_mode', 'idx_egis_pre_mint_mode');
            $table->index('egi_living_enabled', 'idx_egis_egi_living_enabled');
            $table->index(['egi_type', 'status'], 'idx_egis_type_status');
            $table->index('smart_contract_app_id', 'idx_egis_smart_contract_app_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('egis', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex('idx_egis_egi_type');
            $table->dropIndex('idx_egis_pre_mint_mode');
            $table->dropIndex('idx_egis_egi_living_enabled');
            $table->dropIndex('idx_egis_type_status');
            $table->dropIndex('idx_egis_smart_contract_app_id');

            // Drop foreign key constraint
            $table->dropForeign(['egi_living_subscription_id']);

            // Drop columns
            $table->dropColumn([
                'egi_type',
                'pre_mint_mode',
                'pre_mint_created_at',
                'egi_living_enabled',
                'egi_living_activated_at',
                'egi_living_subscription_id',
                'smart_contract_app_id',
            ]);
        });
    }
};
