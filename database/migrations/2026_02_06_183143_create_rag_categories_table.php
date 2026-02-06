<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Create rag_categories table for hierarchical document categorization.
     * Supports i18n via translation keys, visual organization (icon, color),
     * and category-based filtering in RAG queries.
     *
     * @see docs/AI_ANALYSIS/EGI_POSTGRESQL_RAG_SCHEMA_DESIGN.md
     */
    public function up(): void
    {
        Schema::create('rag_categories', function (Blueprint $table) {
            $table->id();

            // Core Fields
            $table->string('slug', 100)->unique();              // URL-friendly: 'platform-features'
            $table->string('name_key', 255);                     // Translation key: 'rag.categories.platform_features'
            $table->string('description_key', 255)->nullable();  // Translation key for description

            // Visual Organization
            $table->string('icon', 50)->nullable();              // Icon name: 'book', 'shield', 'chart'
            $table->string('color', 7)->nullable();              // Hex color: '#D4A574' (Oro Fiorentino)

            // Hierarchical Structure
            $table->foreignId('parent_id')->nullable()->constrained('rag_categories')->onDelete('set null');
            $table->integer('sort_order')->default(0);          // Display order

            // Status
            $table->boolean('is_active')->default(true);

            // Flexible Metadata (JSONB)
            $table->jsonb('metadata')->default('{}');           // Custom fields without schema changes

            // Audit Trail
            $table->timestamps();

            // Indexes
            $table->index('slug');
            $table->index('parent_id');
            $table->index('is_active');
        });

        // GIN index for JSONB metadata (full-text search on metadata)
        DB::statement('CREATE INDEX idx_rag_categories_metadata ON rag_categories USING gin(metadata)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rag_categories');
    }
};
