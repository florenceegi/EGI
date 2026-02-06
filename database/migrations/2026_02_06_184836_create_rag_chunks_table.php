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
     * Create rag_chunks table - Document chunks (paragraphs/sections) for embeddings.
     * Each document is split into chunks for granular vector search.
     * Supports full-text search, position tracking, and flexible metadata.
     *
     * @see docs/AI_ANALYSIS/EGI_POSTGRESQL_RAG_SCHEMA_DESIGN.md
     */
    public function up(): void
    {
        Schema::create('rag_chunks', function (Blueprint $table) {
            $table->id();

            // Foreign Key (CASCADE delete when parent document is deleted)
            $table->foreignId('document_id')->constrained('rag_documents')->onDelete('cascade');

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
        DB::statement('ALTER TABLE rag_chunks ADD COLUMN uuid UUID DEFAULT gen_random_uuid() UNIQUE NOT NULL');

        // Add tsvector column for full-text search
        DB::statement('ALTER TABLE rag_chunks ADD COLUMN search_vector tsvector');

        // Add CHECK constraint for language
        DB::statement("ALTER TABLE rag_chunks ADD CONSTRAINT rag_chunks_language_check CHECK (language IN ('it', 'en', 'de', 'es', 'fr', 'pt'))");

        // Add UNIQUE constraint (document_id, chunk_order) to preserve document structure
        DB::statement('ALTER TABLE rag_chunks ADD CONSTRAINT rag_chunks_order_unique UNIQUE (document_id, chunk_order)');

        // Indexes for Performance
        DB::statement('CREATE INDEX idx_rag_chunks_document ON rag_chunks(document_id)');
        DB::statement('CREATE INDEX idx_rag_chunks_language ON rag_chunks(language)');
        DB::statement('CREATE INDEX idx_rag_chunks_type ON rag_chunks(chunk_type)');
        DB::statement('CREATE INDEX idx_rag_chunks_order ON rag_chunks(document_id, chunk_order)');
        DB::statement('CREATE INDEX idx_rag_chunks_metadata ON rag_chunks USING gin(metadata)');

        // Full-Text Search Index (GIN for tsvector)
        DB::statement('CREATE INDEX idx_rag_chunks_search_vector ON rag_chunks USING gin(search_vector)');

        // Trigger to auto-update search_vector (reuses function from rag_documents)
        // Note: This trigger uses the same function as rag_documents, but only updates from text + section_title
        DB::statement("
            CREATE OR REPLACE FUNCTION rag_chunks_search_vector_trigger() RETURNS trigger AS \$\$
            BEGIN
                NEW.search_vector :=
                    setweight(to_tsvector('italian', coalesce(NEW.section_title, '')), 'A') ||
                    setweight(to_tsvector('italian', coalesce(NEW.text, '')), 'B');
                RETURN NEW;
            END;
            \$\$ LANGUAGE plpgsql;
        ");

        DB::statement("
            CREATE TRIGGER rag_chunks_search_vector_update
                BEFORE INSERT OR UPDATE OF text, section_title
                ON rag_chunks
                FOR EACH ROW
                EXECUTE FUNCTION rag_chunks_search_vector_trigger();
        ");

        // Trigger to auto-update updated_at (reuses function from rag_documents)
        DB::statement("
            CREATE TRIGGER rag_chunks_updated_at
                BEFORE UPDATE ON rag_chunks
                FOR EACH ROW
                EXECUTE FUNCTION update_updated_at_column();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop triggers first
        DB::statement('DROP TRIGGER IF EXISTS rag_chunks_updated_at ON rag_chunks');
        DB::statement('DROP TRIGGER IF EXISTS rag_chunks_search_vector_update ON rag_chunks');

        // Drop chunk-specific trigger function
        DB::statement('DROP FUNCTION IF EXISTS rag_chunks_search_vector_trigger()');

        // Drop table (cascade will drop all indexes and constraints)
        Schema::dropIfExists('rag_chunks');
    }
};
