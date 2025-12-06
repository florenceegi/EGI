<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Helpers\DatabaseHelper;

/**
 * @package App\Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Egili System Foundation)
 * @date 2025-11-02
 * @purpose Extend user_feature_purchases with scheduling/approval fields for Featured/Hyper system
 */
return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        if (!Schema::hasTable('user_feature_purchases')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        // Add new columns (with safe checks for already-existing columns)
        Schema::table('user_feature_purchases', function (Blueprint $table) use ($driver) {
            // Feature Type (lifetime vs consumable)
            if (!Schema::hasColumn('user_feature_purchases', 'is_lifetime')) {
                $column = $table->boolean('is_lifetime')->default(false);

                if (method_exists($column, 'after')) {
                    $column->after('is_active');
                }

                $column->comment('True if feature is lifetime (EGI Living), false if consumable');
            }

            // Egili Reserved (for approval flow)
            if (!Schema::hasColumn('user_feature_purchases', 'egili_reserved')) {
                $column = $table->integer('egili_reserved')->nullable();

                if (method_exists($column, 'after')) {
                    $column->after('amount_paid_egili');
                }

                $column->comment('Egili reserved during pending approval (not yet spent)');
            }

            // Scheduling Fields (for Featured/Hyper with calendar slots)
            if (!Schema::hasColumn('user_feature_purchases', 'scheduled_slot_start')) {
                $column = $table->timestamp('scheduled_slot_start')->nullable();

                if (method_exists($column, 'after')) {
                    $column->after('is_lifetime');
                }

                $column->comment('Start date/time for scheduled feature (Featured/Hyper)');
            }

            if (!Schema::hasColumn('user_feature_purchases', 'scheduled_slot_end')) {
                $column = $table->timestamp('scheduled_slot_end')->nullable();

                if (method_exists($column, 'after')) {
                    $column->after('scheduled_slot_start');
                }

                $column->comment('End date/time for scheduled feature (Featured/Hyper)');
            }

            // Admin Approval Fields
            if (!Schema::hasColumn('user_feature_purchases', 'approved_by_admin_id')) {
                $column = $table->unsignedBigInteger('approved_by_admin_id')->nullable();

                if (method_exists($column, 'after')) {
                    $column->after('scheduled_slot_end');
                }

                $column->comment('Admin who approved/scheduled the feature');
            }

            if (!Schema::hasColumn('user_feature_purchases', 'approved_at')) {
                $column = $table->timestamp('approved_at')->nullable();

                if (method_exists($column, 'after')) {
                    $column->after('approved_by_admin_id');
                }

                $column->comment('When admin approved/scheduled');
            }

            if (!Schema::hasColumn('user_feature_purchases', 'rejection_reason')) {
                $column = $table->text('rejection_reason')->nullable();

                if (method_exists($column, 'after')) {
                    $column->after('approved_at');
                }

                $column->comment('Reason for rejection (if status=rejected)');
            }

            // Usage Metadata (detailed tracking)
            if (!Schema::hasColumn('user_feature_purchases', 'usage_metadata')) {
                $column = $table->json('usage_metadata')->nullable();

                if (method_exists($column, 'after')) {
                    $column->after('rejection_reason');
                }

                $column->comment('Detailed usage tracking (e.g., AI tokens consumed, feature usage stats)');
            }
        });

        // Add foreign key only for drivers che lo supportano
        if ($driver !== 'sqlite' && Schema::hasTable('users')) {
            Schema::table('user_feature_purchases', function (Blueprint $table) {
                $table->foreign('approved_by_admin_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('set null');
            });
        }

        // Add indexes if they don't exist
        Schema::table('user_feature_purchases', function (Blueprint $table) use ($driver) {
            if (DatabaseHelper::isMysql()) {
                $hasScheduledIndex = collect(DB::select("SHOW INDEX FROM user_feature_purchases WHERE Key_name = 'idx_scheduled_slots'"))->count() > 0;
                if (!$hasScheduledIndex) {
                    $table->index(['scheduled_slot_start', 'scheduled_slot_end'], 'idx_scheduled_slots');
                }

                $hasLifetimeIndex = collect(DB::select("SHOW INDEX FROM user_feature_purchases WHERE Key_name = 'idx_is_lifetime'"))->count() > 0;
                if (!$hasLifetimeIndex) {
                    $table->index('is_lifetime', 'idx_is_lifetime');
                }
            } elseif (DatabaseHelper::isPostgres()) {
                // PostgreSQL: check indexes via pg_indexes
                $hasScheduledIndex = collect(DB::select("SELECT indexname FROM pg_indexes WHERE tablename = 'user_feature_purchases' AND indexname = 'idx_scheduled_slots'"))->count() > 0;
                if (!$hasScheduledIndex) {
                    $table->index(['scheduled_slot_start', 'scheduled_slot_end'], 'idx_scheduled_slots');
                }

                $hasLifetimeIndex = collect(DB::select("SELECT indexname FROM pg_indexes WHERE tablename = 'user_feature_purchases' AND indexname = 'idx_is_lifetime'"))->count() > 0;
                if (!$hasLifetimeIndex) {
                    $table->index('is_lifetime', 'idx_is_lifetime');
                }
            } else {
                // SQLite: gli indici non esistono in ambienti test, possiamo crearli direttamente
                $table->index(['scheduled_slot_start', 'scheduled_slot_end'], 'idx_scheduled_slots');
                $table->index('is_lifetime', 'idx_is_lifetime');
            }
        });

        // MODIFY existing status ENUM to add new approval/scheduling values
        // Note: Laravel Schema builder doesn't support MODIFY ENUM, so we use raw SQL
        // Skip on PostgreSQL - it uses VARCHAR which accepts any string
        if (DatabaseHelper::isMysql()) {
            DB::statement("
                ALTER TABLE user_feature_purchases 
                MODIFY COLUMN status ENUM(
                    'active',
                    'expired',
                    'cancelled',
                    'refunded',
                    'pending',
                    'failed',
                    'pending_approval',
                    'scheduled',
                    'rejected'
                ) NOT NULL DEFAULT 'active' 
                COMMENT 'Purchase status (includes approval/scheduling states)'
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        // REVERT status ENUM to original values
        if (!Schema::hasTable('user_feature_purchases')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        // Only modify ENUM on MySQL/MariaDB
        if (DatabaseHelper::isMysql()) {
            DB::statement("
                ALTER TABLE user_feature_purchases 
                MODIFY COLUMN status ENUM(
                    'active',
                    'expired',
                    'cancelled',
                    'refunded',
                    'pending',
                    'failed'
                ) NOT NULL DEFAULT 'active' 
                COMMENT 'Purchase status'
            ");
        }

        Schema::table('user_feature_purchases', function (Blueprint $table) use ($driver) {
            // Drop indexes (silenzioso se non esistono)
            foreach (['idx_scheduled_slots', 'idx_is_lifetime'] as $indexName) {
                try {
                    $table->dropIndex($indexName);
                } catch (\Throwable $e) {
                    // Ignora in ambienti dove l'indice non esiste
                }
            }

            if ($driver !== 'sqlite') {
                try {
                    $table->dropForeign(['approved_by_admin_id']);
                } catch (\Throwable $e) {
                    // Ignora se il vincolo non esiste
                }
            }

            $columns = [
                'is_lifetime',
                'egili_reserved',
                'scheduled_slot_start',
                'scheduled_slot_end',
                'approved_by_admin_id',
                'approved_at',
                'rejection_reason',
                'usage_metadata',
            ];

            $existing = array_filter($columns, fn($column) => Schema::hasColumn('user_feature_purchases', $column));

            if (!empty($existing)) {
                $table->dropColumn($existing);
            }
        });
    }
};
