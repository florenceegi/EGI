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
        // Check if we need to rename blockchain_algorand_id to egi_blockchain_id
        if (Schema::hasColumn('egi_reservation_certificates', 'blockchain_algorand_id')) {
            // Rename column using raw SQL
            DB::statement('ALTER TABLE egi_reservation_certificates
                CHANGE COLUMN blockchain_algorand_id egi_blockchain_id BIGINT UNSIGNED NULL
                COMMENT "Reference to blockchain record for mint certificates"');
        } elseif (!Schema::hasColumn('egi_reservation_certificates', 'egi_blockchain_id')) {
            // Add egi_blockchain_id column if it doesn't exist
            Schema::table('egi_reservation_certificates', function (Blueprint $table) {
                $table->unsignedBigInteger('egi_blockchain_id')
                    ->nullable()
                    ->after('egi_id')
                    ->comment('Reference to blockchain record for mint certificates');
            });
        }

        // Add certificate_type column if not exists
        if (!Schema::hasColumn('egi_reservation_certificates', 'certificate_type')) {
            Schema::table('egi_reservation_certificates', function (Blueprint $table) {
                $table->enum('certificate_type', ['reservation', 'mint'])
                    ->default('reservation')
                    ->after('egi_id')
                    ->comment('Type of certificate: reservation or mint');
            });
        }

        // Add foreign key and indexes (Laravel handles "if not exists" automatically)
        try {
            Schema::table('egi_reservation_certificates', function (Blueprint $table) {
                // Laravel will skip if foreign key already exists
                if (!Schema::hasIndex('egi_reservation_certificates', 'egi_reservation_certificates_egi_blockchain_id_foreign')) {
                    $table->foreign('egi_blockchain_id')
                        ->references('id')
                        ->on('egi_blockchain')
                        ->onDelete('cascade');
                }

                // Add indexes if they don't exist
                if (!Schema::hasIndex('egi_reservation_certificates', 'egi_reservation_certificates_certificate_type_index')) {
                    $table->index('certificate_type');
                }

                if (!Schema::hasIndex('egi_reservation_certificates', 'egi_reservation_certificates_egi_id_certificate_type_index')) {
                    $table->index(['egi_id', 'certificate_type']);
                }
            });
        } catch (\Exception $e) {
            // Indexes/foreign keys might already exist - safe to ignore
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        try {
            Schema::table('egi_reservation_certificates', function (Blueprint $table) {
                // Drop indexes
                $table->dropIndex(['egi_id', 'certificate_type']);
                $table->dropIndex(['certificate_type']);

                // Drop foreign key
                $table->dropForeign(['egi_blockchain_id']);

                // Drop certificate_type column
                $table->dropColumn('certificate_type');
            });
        } catch (\Exception $e) {
            // Safe to ignore if already dropped
        }

        // Rename column back if needed
        if (Schema::hasColumn('egi_reservation_certificates', 'egi_blockchain_id')) {
            try {
                DB::statement('ALTER TABLE egi_reservation_certificates
                    CHANGE COLUMN egi_blockchain_id blockchain_algorand_id BIGINT UNSIGNED NULL');
            } catch (\Exception $e) {
                // Ignore if fails
            }
        }
    }
};
