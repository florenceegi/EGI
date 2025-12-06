<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Helpers\DatabaseHelper;

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
        // Check if egi_id column already exists
        if (!Schema::hasColumn('payment_distributions', 'egi_id')) {
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
        }

        // Populate egi_id for existing records only if column was just created
        if (Schema::hasColumn('payment_distributions', 'egi_id')) {
            // Use Eloquent for SQLite and PostgreSQL compatibility
            $connection = config('database.default');

            if ($connection === 'sqlite' || DatabaseHelper::isPostgres()) {
                // SQLite/PostgreSQL-compatible approach using Eloquent
                $distributions = DB::table('payment_distributions')
                    ->whereNull('egi_id')
                    ->get();

                foreach ($distributions as $dist) {
                    $egiId = null;

                    // Try get from reservation
                    if ($dist->reservation_id) {
                        $egiId = DB::table('reservations')
                            ->where('id', $dist->reservation_id)
                            ->value('egi_id');
                    }

                    // Fallback to egi_blockchain
                    if (!$egiId && $dist->egi_blockchain_id) {
                        $egiId = DB::table('egi_blockchain')
                            ->where('id', $dist->egi_blockchain_id)
                            ->value('egi_id');
                    }

                    if ($egiId) {
                        DB::table('payment_distributions')
                            ->where('id', $dist->id)
                            ->update(['egi_id' => $egiId]);
                    }
                }
            } else {
                // MySQL-optimized query
                DB::statement('
                    UPDATE payment_distributions pd
                    LEFT JOIN reservations r ON pd.reservation_id = r.id
                    LEFT JOIN egi_blockchain eb ON pd.egi_blockchain_id = eb.id
                    SET pd.egi_id = COALESCE(r.egi_id, eb.egi_id)
                    WHERE pd.egi_id IS NULL
                ');
            }

            // To change NOT NULL with foreign key, we need to drop and recreate
            if (DatabaseHelper::isMysql()) {
                // Check if foreign key exists before dropping
                $foreignKeys = DB::select("
                    SELECT CONSTRAINT_NAME
                    FROM information_schema.KEY_COLUMN_USAGE
                    WHERE TABLE_SCHEMA = DATABASE()
                    AND TABLE_NAME = 'payment_distributions'
                    AND COLUMN_NAME = 'egi_id'
                    AND REFERENCED_TABLE_NAME IS NOT NULL
                ");

                if (!empty($foreignKeys)) {
                    $constraintName = $foreignKeys[0]->CONSTRAINT_NAME;
                    DB::statement("ALTER TABLE payment_distributions DROP FOREIGN KEY `{$constraintName}`");
                }

                // Drop index if exists
                $indexes = DB::select("
                    SHOW INDEX FROM payment_distributions
                    WHERE Key_name = 'idx_payment_dist_egi_source'
                ");

                if (!empty($indexes)) {
                    DB::statement("ALTER TABLE payment_distributions DROP INDEX idx_payment_dist_egi_source");
                }
            } elseif (DatabaseHelper::isPostgres()) {
                // PostgreSQL: drop constraints using pg_constraint
                $foreignKeys = DB::select("
                    SELECT conname
                    FROM pg_constraint
                    WHERE conrelid = 'payment_distributions'::regclass
                    AND contype = 'f'
                    AND conname LIKE '%egi_id%'
                ");

                foreach ($foreignKeys as $fk) {
                    DB::statement("ALTER TABLE payment_distributions DROP CONSTRAINT IF EXISTS \"{$fk->conname}\"");
                }

                // Drop index if exists
                DB::statement("DROP INDEX IF EXISTS idx_payment_dist_egi_source");
            }

            // Change column to NOT NULL
            Schema::table('payment_distributions', function (Blueprint $table) {
                $table->unsignedBigInteger('egi_id')->nullable(false)->change();
            });

            // Recreate foreign key and index (except SQLite)
            $connection = config('database.default');
            if ($connection !== 'sqlite') {
                Schema::table('payment_distributions', function (Blueprint $table) {
                    $table->foreign('egi_id')->references('id')->on('egis')->onDelete('cascade');
                    $table->index(['egi_id', 'source_type'], 'idx_payment_dist_egi_source');
                });
            }
        }
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
