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
        Schema::table('egi_reservation_certificates', function (Blueprint $table) {
            // Add certificate_type enum (reservation = existing, mint = blockchain certified)
            $table->enum('certificate_type', ['reservation', 'mint'])
                ->default('reservation')
                ->after('id')
                ->comment('Type of certificate: reservation (provisional) or mint (blockchain certified)');

            // Add egi_blockchain_id foreign key for mint certificates
            $table->unsignedBigInteger('egi_blockchain_id')
                ->nullable()
                ->after('reservation_id')
                ->comment('Reference to blockchain record for mint certificates');

            $table->foreign('egi_blockchain_id')
                ->references('id')
                ->on('egi_blockchain')
                ->onDelete('cascade');

            // Add indexes for performance
            $table->index('certificate_type');
            $table->index(['egi_id', 'certificate_type']);
        });

        // Make reservation_id nullable using raw SQL (avoid doctrine/dbal dependency)
        DB::statement('ALTER TABLE egi_reservation_certificates MODIFY reservation_id BIGINT UNSIGNED NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        // Restore reservation_id as NOT NULL using raw SQL
        DB::statement('ALTER TABLE egi_reservation_certificates MODIFY reservation_id BIGINT UNSIGNED NOT NULL');

        Schema::table('egi_reservation_certificates', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['egi_blockchain_id']);

            // Drop indexes
            $table->dropIndex(['egi_id', 'certificate_type']);
            $table->dropIndex(['certificate_type']);

            // Drop columns
            $table->dropColumn('egi_blockchain_id');
            $table->dropColumn('certificate_type');
        });
    }
};