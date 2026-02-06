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
     * Create queries table - User query log & analytics.
     * Tracks all user questions, response summaries, and feedback.
     *
     * Schema: rag_natan.queries
     *
     * @see docs/AI_ANALYSIS/EGI_POSTGRESQL_RAG_SCHEMA_DESIGN.md
     */
    public function up(): void
    {
        // Set search_path to rag_natan schema
        DB::statement('SET search_path TO rag_natan, core, public');

        Schema::create('queries', function (Blueprint $table) {
            $table->id();

            // Foreign Key to users (nullable for anonymous queries)
            $table->foreignId('user_id')->nullable()->constrained('core.users')->onDelete('set null');

            // Query Data
            $table->text('question');
            $table->string('question_hash', 64);        // SHA256 for deduplication
            $table->string('language', 2);
            $table->jsonb('context')->default('{}');    // { user_type, mode, filters, etc. }

            // Response Summary (response_id added later after responses table exists)
            $table->decimal('urs_score', 5, 2)->nullable();
            $table->integer('answer_length')->nullable();
            $table->integer('chunks_used')->nullable();
            $table->integer('response_time_ms')->nullable();

            // Analytics
            $table->boolean('was_helpful')->nullable();
            $table->text('feedback_text')->nullable();
            $table->integer('view_count')->default(0);

            // Metadata
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('session_id', 255)->nullable();

            // Audit
            $table->timestamp('created_at')->default(DB::raw('NOW()'));
            $table->timestamp('responded_at')->nullable();
        });

        // Add UUID column
        DB::statement('ALTER TABLE rag_natan.queries ADD COLUMN uuid UUID DEFAULT gen_random_uuid() UNIQUE NOT NULL');

        // Add CHECK constraint for language
        DB::statement("ALTER TABLE rag_natan.queries ADD CONSTRAINT queries_language_check CHECK (language IN ('it', 'en', 'de', 'es', 'fr', 'pt'))");

        // Indexes
        DB::statement('CREATE INDEX idx_queries_user ON rag_natan.queries(user_id) WHERE user_id IS NOT NULL');
        DB::statement('CREATE INDEX idx_queries_hash ON rag_natan.queries(question_hash)');
        DB::statement('CREATE INDEX idx_queries_language ON rag_natan.queries(language)');
        DB::statement('CREATE INDEX idx_queries_created ON rag_natan.queries(created_at DESC)');
        DB::statement('CREATE INDEX idx_queries_context ON rag_natan.queries USING gin(context)');
        DB::statement('CREATE INDEX idx_queries_helpful ON rag_natan.queries(was_helpful) WHERE was_helpful IS NOT NULL');
        DB::statement('CREATE INDEX idx_queries_urs ON rag_natan.queries(urs_score) WHERE urs_score IS NOT NULL');

        // Helper function: Generate question hash
        DB::statement("
            CREATE OR REPLACE FUNCTION rag_natan.generate_question_hash(question TEXT) RETURNS VARCHAR(64) AS \$\$
            BEGIN
                RETURN encode(digest(lower(trim(question)), 'sha256'), 'hex');
            END;
            \$\$ LANGUAGE plpgsql IMMUTABLE;
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

        // Drop helper function
        DB::statement('DROP FUNCTION IF EXISTS rag_natan.generate_question_hash(TEXT)');

        // Drop table
        Schema::dropIfExists('queries');

        DB::statement('SET search_path TO core, public');
    }
};
