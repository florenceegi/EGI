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
     * Create responses table - Generated RAG responses.
     * Stores synthesized answers, quality metrics, and performance data.
     *
     * Schema: rag_natan.responses
     *
     * @see docs/AI_ANALYSIS/EGI_POSTGRESQL_RAG_SCHEMA_DESIGN.md
     */
    public function up(): void
    {
        // Set search_path to rag_natan schema
        DB::statement('SET search_path TO rag_natan, core, public');

        Schema::create('responses', function (Blueprint $table) {
            $table->id();

            // Foreign Key to queries (CASCADE delete)
            $table->foreignId('query_id')->constrained('rag_natan.queries')->onDelete('cascade');

            // Response Content
            $table->text('answer');                     // Synthesized answer (Markdown)
            $table->text('answer_html')->nullable();    // Rendered HTML (optional)

            // Quality Metrics
            $table->decimal('urs_score', 5, 2);         // Unified Reliability Score (0-100)
            $table->text('urs_explanation')->nullable();

            // RAG Pipeline Metadata
            $table->jsonb('claims_used')->default('[]');
            $table->jsonb('gaps_detected')->default('[]');
            $table->jsonb('hallucinations')->default('[]');
            $table->jsonb('sources_used')->default('[]');

            // Performance Metrics
            $table->integer('processing_time_ms');
            $table->integer('tokens_input')->nullable();
            $table->integer('tokens_output')->nullable();
            $table->decimal('cost_usd', 10, 6)->nullable();
            $table->string('model_used', 100)->nullable();

            // Pipeline Stages Timing
            $table->jsonb('stage_timings')->default('{}');

            // Cache Strategy
            $table->boolean('is_cached')->default(false);
            $table->string('cache_key', 255)->nullable();
            $table->timestamp('cache_expires_at')->nullable();

            // Audit
            $table->timestamp('created_at')->default(DB::raw('NOW()'));
            $table->timestamp('served_from_cache_at')->nullable();
        });

        // Add UUID column
        DB::statement('ALTER TABLE rag_natan.responses ADD COLUMN uuid UUID DEFAULT gen_random_uuid() UNIQUE NOT NULL');

        // Add CHECK constraint for urs_score
        DB::statement('ALTER TABLE rag_natan.responses ADD CONSTRAINT responses_urs_check CHECK (urs_score BETWEEN 0 AND 100)');

        // Indexes
        DB::statement('CREATE INDEX idx_responses_query ON rag_natan.responses(query_id)');
        DB::statement('CREATE INDEX idx_responses_urs ON rag_natan.responses(urs_score)');
        DB::statement('CREATE INDEX idx_responses_cache_key ON rag_natan.responses(cache_key) WHERE is_cached = true');
        DB::statement('CREATE INDEX idx_responses_created ON rag_natan.responses(created_at DESC)');
        DB::statement('CREATE INDEX idx_responses_sources ON rag_natan.responses USING gin(sources_used)');

        // Add circular FK: queries.response_id → responses.id
        DB::statement('ALTER TABLE rag_natan.queries ADD COLUMN response_id BIGINT REFERENCES rag_natan.responses(id) ON DELETE SET NULL');
        DB::statement('CREATE INDEX idx_queries_response ON rag_natan.queries(response_id) WHERE response_id IS NOT NULL');

        // Reset search_path to default
        DB::statement('SET search_path TO core, public');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('SET search_path TO rag_natan, core, public');

        // Drop circular FK from queries first
        DB::statement('DROP INDEX IF EXISTS rag_natan.idx_queries_response');
        DB::statement('ALTER TABLE rag_natan.queries DROP COLUMN IF EXISTS response_id');

        // Drop table
        Schema::dropIfExists('responses');

        DB::statement('SET search_path TO core, public');
    }
};
