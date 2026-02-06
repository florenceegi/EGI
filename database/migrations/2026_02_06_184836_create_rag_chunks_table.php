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
     * Create chunks table - Document chunks (paragraphs/sections) for embeddings.
     * Each document is split into chunks for granular vector search.
     * Supports full-text search, position tracking, and flexible metadata.
     *
     * Schema: rag_natan.chunks
     *
     * @see docs/AI_ANALYSIS/EGI_POSTGRESQL_RAG_SCHEMA_DESIGN.md
     */
    public function up(): void
    {
        // Set search_path to rag_natan schema
        DB::statement('SET search_path TO rag_natan, public');

        Schema::create('chunks', function (Blueprint $table) {
            $table->id();

            // Foreign Key (CASCADE delete when parent document is deleted)
            $table->foreignId('document_id')->constrained('rag_natan.documents')->onDelete('cascade');

            // Chunk Content
            $table->text('text');                               // Chunk content (paragraph/section)
            $table->string('section_title', 500)->nullable();   // Section heading (if applicable)
            $table->integer('chunk_order');                     // 0-based position in document
            $table->integer('char_start')->nullable();          // Start position in original content
            $table->integer('char_end')->nullable();            // End position in original content
            $table->integer('token_count')->nullable();         // Precomputed token count (OpenAI tiktoken)

            // Chunk Metadata (inherited from parent + chunk-specific)
            $table->string('chunk_type', 50)->default('paragraph');  // 'paragraph', 'heading', 'list', 'code', 'table'
            $table->string('language', 2);                      // Inherited from document
            $table->jsonb('metadata')->default('{}');           // Custom chunk metadata

            // Audit
            $table->timestamp('created_at')->default(DB::raw('NOW()'));
            $table->timestamp('updated_at')->default(DB::raw('NOW()'));
        });

        // Add UUID column with default gen_random_uuid() and unique constraint
        DB::statement('ALTER TABLE rag_natan.chunks ADD COLUMN uuid UUID DEFAULT gen_random_uuid() UNIQUE NOT NULL');

        // Add tsvector column for full-text search
        DB::statement('ALTER TABLE rag_natan.chunks ADD COLUMN search_vector tsvector');

        // Add CHECK constraint for language
        DB::statement("ALTER TABLE rag_natan.chunks ADD CONSTRAINT chunks_language_check CHECK (language IN ('it', 'en', 'de', 'es', 'fr', 'pt'))");

        // Add UNIQUE constraint (document_id, chunk_order) to preserve document structure
        DB::statement('ALTER TABLE rag_natan.chunks ADD CONSTRAINT chunks_order_unique UNIQUE (document_id, chunk_order)');

        // Indexes for Performance
        DB::statement('CREATE INDEX idx_chunks_document ON rag_natan.chunks(document_id)');
        DB::statement('CREATE INDEX idx_chunks_language ON rag_natan.chunks(language)');
        DB::statement('CREATE INDEX idx_chunks_type ON rag_natan.chunks(chunk_type)');
        DB::statement('CREATE INDEX idx_chunks_order ON rag_natan.chunks(document_id, chunk_order)');
        DB::statement('CREATE INDEX idx_chunks_metadata ON rag_natan.chunks USING gin(metadata)');

        // Full-Text Search Index (GIN for tsvector)
        DB::statement('CREATE INDEX idx_chunks_search_vector ON rag_natan.chunks USING gin(search_vector)');

        // Trigger to auto-update search_vector
        DB::statement("
            CREATE OR REPLACE FUNCTION rag_natan.chunks_search_vector_trigger() RETURNS trigger AS \$\$
            BEGIN
                NEW.search_vector :=
                    setweight(to_tsvector('italian', coalesce(NEW.section_title, '')), 'A') ||
                    setweight(to_tsvector('italian', coalesce(NEW.text, '')), 'B');
                RETURN NEW;
            END;
            \$\$ LANGUAGE plpgsql;
        ");

        DB::statement("
            CREATE TRIGGER chunks_search_vector_update
                BEFORE INSERT OR UPDATE OF text, section_title
                ON rag_natan.chunks
                FOR EACH ROW
                EXECUTE FUNCTION rag_natan.chunks_search_vector_trigger();
        ");

        // Trigger to auto-update updated_at (reuses function from documents migration)
        DB::statement("
            CREATE TRIGGER chunks_updated_at
                BEFORE UPDATE ON rag_natan.chunks
                FOR EACH ROW
                EXECUTE FUNCTION rag_natan.update_updated_at_column();
        ");

        // Reset search_path to default
        DB::statement('SET search_path TO core, public');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('SET search_path TO rag_natan, public');

        // Drop triggers first
        DB::statement('DROP TRIGGER IF EXISTS chunks_updated_at ON rag_natan.chunks');
        DB::statement('DROP TRIGGER IF EXISTS chunks_search_vector_update ON rag_natan.chunks');

        // Drop chunk-specific trigger function
        DB::statement('DROP FUNCTION IF EXISTS rag_natan.chunks_search_vector_trigger()');

        // Drop table (cascade will drop all indexes and constraints)
        Schema::dropIfExists('chunks');

        DB::statement('SET search_path TO core, public');
    }
};
