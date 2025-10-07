<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{Schema, DB};

/**
 * Add PA/Enterprise fields to collections table
 *
 * @package Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - PA/Enterprise System MVP)
 * @date 2025-10-02
 * @purpose Enable PA/Enterprise functionality with metadata JSON and expanded type field
 *
 * Changes:
 * 1. ADD metadata JSON field for PA/Enterprise-specific data storage
 * 2. EXPAND type VARCHAR(25) → VARCHAR(50) to accommodate longer type names
 * 3. ADD index on [type, owner_id] for faster PA queries
 */
return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('collections', function (Blueprint $table) {
            // 1. ADD metadata JSON field after featured_position
            // Stores PA/Enterprise-specific data: entity codes, institution info, heritage classification
            $table->json('metadata')->nullable()->after('featured_position')
                ->comment('JSON field for PA/Enterprise data: entity_code, institution_name, heritage_type, etc.');

            // 2. ADD composite index for PA queries optimization
            // Speeds up queries like: WHERE type = 'pa_heritage' AND owner_id = X
            $table->index(['type', 'owner_id'], 'idx_collections_type_owner');
        });

        // 3. EXPAND type field VARCHAR(25) → VARCHAR(50)
        // Allows longer type names like 'pa_heritage', 'company_products', 'pa_documents'
        // Solo per MySQL - SQLite non supporta MODIFY COLUMN
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE collections MODIFY type VARCHAR(50)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('collections', function (Blueprint $table) {
            // Drop metadata field
            $table->dropColumn('metadata');

            // Drop composite index
            $table->dropIndex('idx_collections_type_owner');
        });

        // Revert type field to VARCHAR(25)
        // WARNING: This will TRUNCATE values longer than 25 chars if any exist
        // Solo per MySQL - SQLite non supporta MODIFY COLUMN
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE collections MODIFY type VARCHAR(25)');
        }
    }
};
