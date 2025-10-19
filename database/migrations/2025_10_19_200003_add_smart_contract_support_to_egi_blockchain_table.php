<?php

/**
 * @package App\Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Dual Architecture)
 * @date 2025-10-19
 * @purpose Add SmartContract support fields to egi_blockchain table
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * Adds fields to support SmartContract EGI type in egi_blockchain table.
     * Maintains backward compatibility with ASA-only EGIs.
     */
    public function up(): void
    {
        Schema::table('egi_blockchain', function (Blueprint $table) {
            // === BLOCKCHAIN TYPE ===
            $table->enum('blockchain_type', ['ASA', 'SmartContract'])
                ->default('ASA')
                ->after('egi_id')
                ->comment('Type of blockchain asset: ASA=classic token, SmartContract=living EGI');

            // === SMART CONTRACT REFERENCE ===
            $table->foreignId('smart_contract_id')->nullable()
                ->after('blockchain_type')
                ->constrained('egi_smart_contracts')
                ->onDelete('set null')
                ->comment('Link to SmartContract details (if blockchain_type=SmartContract)');

            // === INDEXES FOR PERFORMANCE ===
            $table->index('blockchain_type', 'idx_egi_blockchain_type');
            $table->index('smart_contract_id', 'idx_egi_blockchain_sc_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('egi_blockchain', function (Blueprint $table) {
            // Drop indexes
            $table->dropIndex('idx_egi_blockchain_type');
            $table->dropIndex('idx_egi_blockchain_sc_id');

            // Drop foreign key
            $table->dropForeign(['smart_contract_id']);

            // Drop columns
            $table->dropColumn([
                'blockchain_type',
                'smart_contract_id',
            ]);
        });
    }
};
