<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * @package App\Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Egili System Foundation)
 * @date 2025-11-02
 * @purpose Extend user_feature_purchases with scheduling/approval fields for Featured/Hyper system
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add new columns (with safe checks for already-existing columns)
        Schema::table('user_feature_purchases', function (Blueprint $table) {
            // Feature Type (lifetime vs consumable)
            if (!Schema::hasColumn('user_feature_purchases', 'is_lifetime')) {
                $table->boolean('is_lifetime')
                      ->default(false)
                      ->after('is_active')
                      ->comment('True if feature is lifetime (EGI Living), false if consumable');
            }
            
            // Egili Reserved (for approval flow)
            if (!Schema::hasColumn('user_feature_purchases', 'egili_reserved')) {
                $table->integer('egili_reserved')
                      ->nullable()
                      ->after('amount_paid_egili')
                      ->comment('Egili reserved during pending approval (not yet spent)');
            }
            
            // Scheduling Fields (for Featured/Hyper with calendar slots)
            if (!Schema::hasColumn('user_feature_purchases', 'scheduled_slot_start')) {
                $table->timestamp('scheduled_slot_start')
                      ->nullable()
                      ->after('is_lifetime')
                      ->comment('Start date/time for scheduled feature (Featured/Hyper)');
            }
            
            if (!Schema::hasColumn('user_feature_purchases', 'scheduled_slot_end')) {
                $table->timestamp('scheduled_slot_end')
                      ->nullable()
                      ->after('scheduled_slot_start')
                      ->comment('End date/time for scheduled feature (Featured/Hyper)');
            }
            
            // Admin Approval Fields
            if (!Schema::hasColumn('user_feature_purchases', 'approved_by_admin_id')) {
                $table->unsignedBigInteger('approved_by_admin_id')
                      ->nullable()
                      ->after('scheduled_slot_end')
                      ->comment('Admin who approved/scheduled the feature');
            }
            
            if (!Schema::hasColumn('user_feature_purchases', 'approved_at')) {
                $table->timestamp('approved_at')
                      ->nullable()
                      ->after('approved_by_admin_id')
                      ->comment('When admin approved/scheduled');
            }
            
            if (!Schema::hasColumn('user_feature_purchases', 'rejection_reason')) {
                $table->text('rejection_reason')
                      ->nullable()
                      ->after('approved_at')
                      ->comment('Reason for rejection (if status=rejected)');
            }
            
            // Usage Metadata (detailed tracking)
            if (!Schema::hasColumn('user_feature_purchases', 'usage_metadata')) {
                $table->json('usage_metadata')
                      ->nullable()
                      ->after('rejection_reason')
                      ->comment('Detailed usage tracking (e.g., AI tokens consumed, feature usage stats)');
            }
        });
        
        // Add foreign key only if it doesn't exist
        $foreignKeys = collect(DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'user_feature_purchases' 
            AND CONSTRAINT_NAME LIKE '%approved_by_admin_id%'
        "))->pluck('CONSTRAINT_NAME');
        
        if ($foreignKeys->isEmpty()) {
            Schema::table('user_feature_purchases', function (Blueprint $table) {
                $table->foreign('approved_by_admin_id')
                      ->references('id')
                      ->on('users')
                      ->onDelete('set null');
            });
        }
        
        // Add indexes if they don't exist
        Schema::table('user_feature_purchases', function (Blueprint $table) {
            if (!collect(DB::select("SHOW INDEX FROM user_feature_purchases WHERE Key_name = 'idx_scheduled_slots'"))->count()) {
                $table->index(['scheduled_slot_start', 'scheduled_slot_end'], 'idx_scheduled_slots');
            }
            
            if (!collect(DB::select("SHOW INDEX FROM user_feature_purchases WHERE Key_name = 'idx_is_lifetime'"))->count()) {
                $table->index('is_lifetime', 'idx_is_lifetime');
            }
        });
        
        // MODIFY existing status ENUM to add new approval/scheduling values
        // Note: Laravel Schema builder doesn't support MODIFY ENUM, so we use raw SQL
        DB::statement("
            ALTER TABLE user_feature_purchases 
            MODIFY COLUMN status ENUM(
                'active',           -- Feature is active and usable
                'expired',          -- Expired (time-based)
                'cancelled',        -- Cancelled by user
                'refunded',         -- Refunded
                'pending',          -- Payment pending confirmation
                'failed',           -- Payment failed
                'pending_approval', -- NEW: Waiting for admin approval (Featured/Hyper)
                'scheduled',        -- NEW: Approved and scheduled for future activation
                'rejected'          -- NEW: Admin rejected request
            ) NOT NULL DEFAULT 'active' 
            COMMENT 'Purchase status (includes approval/scheduling states)'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // REVERT status ENUM to original values
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
        
        Schema::table('user_feature_purchases', function (Blueprint $table) {
            // Drop indexes
            $table->dropIndex('idx_scheduled_slots');
            $table->dropIndex('idx_is_lifetime');
            
            // Drop foreign key
            $table->dropForeign(['approved_by_admin_id']);
            
            // Drop columns
            $table->dropColumn([
                'is_lifetime',
                'egili_reserved',
                'scheduled_slot_start',
                'scheduled_slot_end',
                'approved_by_admin_id',
                'approved_at',
                'rejection_reason',
                'usage_metadata',
            ]);
        });
    }
};
