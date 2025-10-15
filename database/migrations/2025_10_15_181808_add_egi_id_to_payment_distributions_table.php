<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @Oracode Migration: Add egi_id to payment_distributions
 * 🎯 Purpose: Direct EGI reference for better query performance
 * 🛡️ Privacy: Maintains referential integrity
 * 🧱 Core Logic: Simplifies queries and prevents data loss
 *
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Architecture Fix)
 * @date 2025-10-15
 * @purpose Fix architectural issue: add direct egi_id to avoid complex JOINs
 */
return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('payment_distributions', function (Blueprint $table) {
            // Add egi_id as direct foreign key (always set, regardless of source_type)
            $table->foreignId('egi_id')
                ->nullable() // Temporarily nullable for existing records
                ->after('reservation_id')
                ->constrained('egis')
                ->onDelete('cascade')
                ->comment('Direct EGI reference (always set for both reservation and mint)');

            // Add index for better query performance
            $table->index(['egi_id', 'source_type'], 'idx_payment_dist_egi_source');
        });

        // Populate egi_id for existing records
        DB::statement('
            UPDATE payment_distributions pd
            LEFT JOIN reservations r ON pd.reservation_id = r.id
            LEFT JOIN egi_blockchain eb ON pd.egi_blockchain_id = eb.id
            SET pd.egi_id = COALESCE(r.egi_id, eb.egi_id)
            WHERE pd.egi_id IS NULL
        ');

        // Make egi_id NOT NULL after population
        Schema::table('payment_distributions', function (Blueprint $table) {
            $table->unsignedBigInteger('egi_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('payment_distributions', function (Blueprint $table) {
            $table->dropIndex('idx_payment_dist_egi_source');
            $table->dropForeign(['egi_id']);
            $table->dropColumn('egi_id');
        });
    }
};
