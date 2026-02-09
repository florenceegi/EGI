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
     * Create query_cache table - Query result cache for performance.
     * Caches responses to avoid re-running expensive RAG pipeline.
     *
     * Schema: rag_natan.query_cache
     *
     * @see docs/AI_ANALYSIS/EGI_POSTGRESQL_RAG_SCHEMA_DESIGN.md
     */
    public function up(): void
    {
        // Set search_path to rag_natan schema
        DB::statement('SET search_path TO rag_natan, core, public');

        // Skip if table already exists (for idempotent migrations)
        if (Schema::hasTable('query_cache')) {
            DB::statement('SET search_path TO core, public');
            return;
        }

        Schema::create('query_cache', function (Blueprint $table) {
            $table->id();

            // Cache Key (unique identifier)
            $table->string('cache_key', 255)->unique();
            $table->string('question_hash', 64);

            // Foreign Key to responses (CASCADE delete)
            $table->foreignId('response_id')->nullable()->constrained('rag_natan.responses')->onDelete('cascade');

            // Cache Metadata
            $table->string('language', 2);
            $table->string('context_hash', 64)->nullable();     // Hash of context JSONB
            $table->integer('hit_count')->default(0);
            $table->timestamp('last_hit_at')->nullable();

            // Cache Control
            $table->timestamp('expires_at');
            $table->boolean('is_stale')->default(false);

            // Audit
            $table->timestamp('created_at')->default(DB::raw('NOW()'));
        });

        // Add CHECK constraint for language
        DB::statement("ALTER TABLE rag_natan.query_cache ADD CONSTRAINT query_cache_language_check CHECK (language IN ('it', 'en', 'de', 'es', 'fr', 'pt'))");

        // Indexes
        DB::statement('CREATE INDEX idx_query_cache_key ON rag_natan.query_cache(cache_key)');
        DB::statement('CREATE INDEX idx_query_cache_hash ON rag_natan.query_cache(question_hash)');
        DB::statement('CREATE INDEX idx_query_cache_expires ON rag_natan.query_cache(expires_at) WHERE is_stale = false');
        DB::statement('CREATE INDEX idx_query_cache_hits ON rag_natan.query_cache(hit_count DESC)');

        // Auto-cleanup function for expired cache
        DB::statement("
            CREATE OR REPLACE FUNCTION rag_natan.cleanup_expired_cache() RETURNS void AS \$\$
            BEGIN
                DELETE FROM rag_natan.query_cache WHERE expires_at < NOW();
            END;
            \$\$ LANGUAGE plpgsql;
        ");

        // Reset search_path to default
        DB::statement('SET search_path TO core, public');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('SET search_path TO rag_natan, core, public');

        // Drop cleanup function
        DB::statement('DROP FUNCTION IF EXISTS rag_natan.cleanup_expired_cache()');

        // Drop table
        Schema::dropIfExists('query_cache');

        DB::statement('SET search_path TO core, public');
    }
};
