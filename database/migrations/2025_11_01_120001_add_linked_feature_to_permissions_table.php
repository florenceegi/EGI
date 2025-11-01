<?php

/**
 * @package App\Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Feature Purchase System)
 * @date 2025-11-01
 * @purpose Link permissions to ai_feature_pricing for hybrid approach
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            // Link to feature pricing (if permission is purchasable)
            $table->string('linked_feature_code', 100)->nullable()
                ->after('guard_name')
                ->comment('Feature code in ai_feature_pricing (if purchasable)');
            
            $table->foreign('linked_feature_code')
                ->references('feature_code')
                ->on('ai_feature_pricing')
                ->onDelete('set null');
            
            $table->index('linked_feature_code', 'idx_permissions_linked_feature');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropForeign(['linked_feature_code']);
            $table->dropIndex('idx_permissions_linked_feature');
            $table->dropColumn('linked_feature_code');
        });
    }
};

