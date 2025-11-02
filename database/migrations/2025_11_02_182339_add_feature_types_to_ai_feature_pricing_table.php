<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @package App\Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Egili System Foundation)
 * @date 2025-11-02
 * @purpose Extend ai_feature_pricing with feature types (lifetime/consumable/temporal) and approval requirements
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ai_feature_pricing', function (Blueprint $table) {
            // Feature Type Classification
            $table->enum('feature_type', ['lifetime', 'consumable', 'temporal'])
                  ->default('consumable')
                  ->after('is_active')
                  ->comment('Feature type: lifetime (one-time purchase), consumable (per-use), temporal (timed duration)');
            
            // Cost per single use (for consumable features)
            $table->integer('cost_per_use')
                  ->nullable()
                  ->after('cost_egili')
                  ->comment('Egili cost for single use (consumable features only)');
            
            // Lifetime cost (for lifetime features)
            $table->integer('lifetime_cost')
                  ->nullable()
                  ->after('cost_per_use')
                  ->comment('Egili cost for lifetime purchase (lifetime features only)');
            
            // Admin Approval Required (for Featured/Hyper with limited slots)
            $table->boolean('requires_admin_approval')
                  ->default(false)
                  ->after('lifetime_cost')
                  ->comment('True if feature requires admin approval before activation (Featured/Hyper)');
            
            // Max Concurrent Slots (for Featured/Hyper with limited availability)
            $table->integer('max_concurrent_slots')
                  ->nullable()
                  ->after('requires_admin_approval')
                  ->comment('Max concurrent active users for this feature (e.g., max 3 Featured EGIs in homepage)');
            
            // Indexes for performance
            $table->index('feature_type', 'idx_feature_type');
            $table->index('requires_admin_approval', 'idx_requires_approval');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_feature_pricing', function (Blueprint $table) {
            // Drop indexes
            $table->dropIndex('idx_feature_type');
            $table->dropIndex('idx_requires_approval');
            
            // Drop columns
            $table->dropColumn([
                'feature_type',
                'cost_per_use',
                'lifetime_cost',
                'requires_admin_approval',
                'max_concurrent_slots',
            ]);
        });
    }
};
