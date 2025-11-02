<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @package App\Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Featured/Hyper System)
 * @date 2025-11-02
 * @purpose Extend egis table with Featured EGI and Hyper Mode fields
 * 
 * Featured EGI: Promoted visibility in homepage (limited slots, admin-approved)
 * Hyper Mode: Enhanced visibility + analytics (temporal, admin-approved)
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('egis', function (Blueprint $table) {
            // === FEATURED EGI ===
            $table->timestamp('featured_until')->nullable()
                  ->after('status')
                  ->comment('Featured in homepage until this date (NULL = not featured)');
            
            $table->unsignedBigInteger('featured_by_admin_id')->nullable()
                  ->after('featured_until')
                  ->comment('Admin who approved featured status');
            
            $table->foreign('featured_by_admin_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
            
            // === HYPER MODE ===
            $table->timestamp('hyper_until')->nullable()
                  ->after('featured_by_admin_id')
                  ->comment('Hyper mode active until this date (NULL = not hyper)');
            
            $table->timestamp('hyper_activated_at')->nullable()
                  ->after('hyper_until')
                  ->comment('When hyper mode was activated');
            
            $table->unsignedBigInteger('hyper_by_admin_id')->nullable()
                  ->after('hyper_activated_at')
                  ->comment('Admin who approved hyper mode');
            
            $table->foreign('hyper_by_admin_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
            
            // === INDEXES ===
            $table->index('featured_until', 'idx_featured_until');
            $table->index('hyper_until', 'idx_hyper_until');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('egis', function (Blueprint $table) {
            // Drop indexes
            $table->dropIndex('idx_featured_until');
            $table->dropIndex('idx_hyper_until');
            
            // Drop foreign keys
            $table->dropForeign(['featured_by_admin_id']);
            $table->dropForeign(['hyper_by_admin_id']);
            
            // Drop columns
            $table->dropColumn([
                'featured_until',
                'featured_by_admin_id',
                'hyper_until',
                'hyper_activated_at',
                'hyper_by_admin_id',
            ]);
        });
    }
};
