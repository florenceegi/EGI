<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        // Columns already exist from previous partial migration, just need to:
        // 1. Rename blockchain_algorand_id to egi_blockchain_id
        // 2. Add foreign key
        // 3. Add indexes

        // Step 1: Rename column using raw SQL
        DB::statement('ALTER TABLE egi_reservation_certificates
            CHANGE COLUMN blockchain_algorand_id egi_blockchain_id BIGINT UNSIGNED NULL
            COMMENT "Reference to blockchain record for mint certificates"');

        // Step 2 & 3: Add foreign key and indexes
        Schema::table('egi_reservation_certificates', function (Blueprint $table) {
            $table->foreign('egi_blockchain_id')
                ->references('id')
                ->on('egi_blockchain')
                ->onDelete('cascade');

            // Add indexes for performance
            $table->index('certificate_type');
            $table->index(['egi_id', 'certificate_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('egi_reservation_certificates', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['egi_blockchain_id']);

            // Drop indexes
            $table->dropIndex(['egi_id', 'certificate_type']);
            $table->dropIndex(['certificate_type']);
        });

        // Rename column back using raw SQL
        DB::statement('ALTER TABLE egi_reservation_certificates 
            CHANGE COLUMN egi_blockchain_id blockchain_algorand_id BIGINT UNSIGNED NULL');
    }
};