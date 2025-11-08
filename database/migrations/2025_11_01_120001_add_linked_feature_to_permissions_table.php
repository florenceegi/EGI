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
        if (!Schema::hasTable('permissions')) {
            return; // Spatie tables non presenti (contesto test)
        }

        if (Schema::hasColumn('permissions', 'linked_feature_code')) {
            return; // Migrazione già applicata
        }

        $driver = Schema::getConnection()->getDriverName();

        Schema::table('permissions', function (Blueprint $table) use ($driver) {
            // Link to feature pricing (if permission is purchasable)
            $column = $table->string('linked_feature_code', 100)->nullable();

            // "after" non supportato da SQLite: protezione con metodo dinamico
            if (method_exists($column, 'after')) {
                $column->after('guard_name');
            }

            $column->comment('Feature code in ai_feature_pricing (if purchasable)');

            $supportsForeign = Schema::hasTable('ai_feature_pricing') && $driver !== 'sqlite';

            if ($supportsForeign) {
                $table->foreign('linked_feature_code')
                    ->references('feature_code')
                    ->on('ai_feature_pricing')
                    ->onDelete('set null');
            }

            $table->index('linked_feature_code', 'idx_permissions_linked_feature');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('permissions') || !Schema::hasColumn('permissions', 'linked_feature_code')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        Schema::table('permissions', function (Blueprint $table) use ($driver) {
            $supportsForeign = Schema::hasTable('ai_feature_pricing') && $driver !== 'sqlite';

            if ($supportsForeign) {
                $table->dropForeign(['linked_feature_code']);
            }

            try {
                $table->dropIndex('idx_permissions_linked_feature');
            } catch (\Throwable $e) {
                // Ignora se indice non esiste (SQLite/ambienti test)
            }

            $table->dropColumn('linked_feature_code');
        });
    }
};





