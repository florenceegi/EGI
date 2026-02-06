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
     * Create rag_documents table - Master documents table for RAG system.
     * Supports multi-language content, full-text search, hierarchical categorization,
     * and flexible metadata via JSONB.
     *
     * @see docs/AI_ANALYSIS/EGI_POSTGRESQL_RAG_SCHEMA_DESIGN.md
     */
    public function up(): void
    {
        Schema::create('rag_documents', function (Blueprint $table) {
            $table->id();

            // Public identifier (UUID) - added via raw SQL below
            $table->foreignId('category_id')->nullable()->constrained('rag_categories')->onDelete('set null');

            // Core Fields
            $table->string('title', 500);
            $table->string('slug', 255);
            $table->text('content');
            $table->text('excerpt')->nullable();
            $table->string('language', 2)->default('it');

            // Structured Metadata (indexed columns)
            $table->string('document_type', 50)->nullable();
            $table->string('version', 20)->default('1.0');
            $table->string('status', 20)->default('published');
            // tags and keywords (TEXT[]) added via raw SQL below

            // Full-Text Search (tsvector) added via raw SQL below

            // Flexible Metadata (JSONB)
            $table->jsonb('metadata')->default('{}');

            // Stats & Analytics
            $table->integer('view_count')->default(0);
            $table->integer('helpful_count')->default(0);
            $table->timestamp('last_indexed_at')->nullable();

            // Audit Trail
            $table->timestamp('created_at')->default(DB::raw('NOW()'));
            $table->timestamp('updated_at')->default(DB::raw('NOW()'));
            $table->timestamp('published_at')->nullable();
            $table->timestamp('archived_at')->nullable();
        });

        // Add UUID column with default gen_random_uuid() and unique constraint
        DB::statement('ALTER TABLE rag_documents ADD COLUMN uuid UUID DEFAULT gen_random_uuid() UNIQUE NOT NULL');

        // Add PostgreSQL array columns (TEXT[])
        DB::statement('ALTER TABLE rag_documents ADD COLUMN tags TEXT[]');
        DB::statement('ALTER TABLE rag_documents ADD COLUMN keywords TEXT[]');

        // Add tsvector column for full-text search
        DB::statement('ALTER TABLE rag_documents ADD COLUMN search_vector tsvector');

        // Add CHECK constraints
        DB::statement("ALTER TABLE rag_documents ADD CONSTRAINT rag_documents_language_check CHECK (language IN ('it', 'en', 'de', 'es', 'fr', 'pt'))");
        DB::statement("ALTER TABLE rag_documents ADD CONSTRAINT rag_documents_status_check CHECK (status IN ('draft', 'published', 'archived'))");

        // Add UNIQUE constraint (slug, language)
        DB::statement('ALTER TABLE rag_documents ADD CONSTRAINT rag_documents_slug_language_unique UNIQUE (slug, language)');

        // Indexes for Performance
        DB::statement('CREATE INDEX idx_rag_documents_category ON rag_documents(category_id) WHERE category_id IS NOT NULL');
        DB::statement('CREATE INDEX idx_rag_documents_language ON rag_documents(language)');
        DB::statement('CREATE INDEX idx_rag_documents_status ON rag_documents(status) WHERE status = \'published\'');
        DB::statement('CREATE INDEX idx_rag_documents_type ON rag_documents(document_type) WHERE document_type IS NOT NULL');
        DB::statement('CREATE INDEX idx_rag_documents_tags ON rag_documents USING gin(tags)');
        DB::statement('CREATE INDEX idx_rag_documents_keywords ON rag_documents USING gin(keywords)');
        DB::statement('CREATE INDEX idx_rag_documents_metadata ON rag_documents USING gin(metadata)');
        DB::statement('CREATE INDEX idx_rag_documents_created ON rag_documents(created_at DESC)');

        // Full-Text Search Index (GIN for tsvector)
        DB::statement('CREATE INDEX idx_rag_documents_search_vector ON rag_documents USING gin(search_vector)');

        // Trigger Function to auto-update search_vector
        DB::statement("
            CREATE OR REPLACE FUNCTION rag_documents_search_vector_trigger() RETURNS trigger AS \$\$
            BEGIN
                NEW.search_vector :=
                    setweight(to_tsvector('italian', coalesce(NEW.title, '')), 'A') ||
                    setweight(to_tsvector('italian', coalesce(NEW.excerpt, '')), 'B') ||
                    setweight(to_tsvector('italian', coalesce(NEW.content, '')), 'C') ||
                    setweight(to_tsvector('italian', coalesce(array_to_string(NEW.tags, ' '), '')), 'B') ||
                    setweight(to_tsvector('italian', coalesce(array_to_string(NEW.keywords, ' '), '')), 'B');
                RETURN NEW;
            END;
            \$\$ LANGUAGE plpgsql;
        ");

        DB::statement("
            CREATE TRIGGER rag_documents_search_vector_update
                BEFORE INSERT OR UPDATE OF title, excerpt, content, tags, keywords
                ON rag_documents
                FOR EACH ROW
                EXECUTE FUNCTION rag_documents_search_vector_trigger();
        ");

        // Trigger Function to auto-update updated_at
        DB::statement("
            CREATE OR REPLACE FUNCTION update_updated_at_column() RETURNS trigger AS \$\$
            BEGIN
                NEW.updated_at = NOW();
                RETURN NEW;
            END;
            \$\$ LANGUAGE plpgsql;
        ");

        DB::statement("
            CREATE TRIGGER rag_documents_updated_at
                BEFORE UPDATE ON rag_documents
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
        DB::statement('DROP TRIGGER IF EXISTS rag_documents_updated_at ON rag_documents');
        DB::statement('DROP TRIGGER IF EXISTS rag_documents_search_vector_update ON rag_documents');

        // Drop trigger functions
        DB::statement('DROP FUNCTION IF EXISTS update_updated_at_column()');
        DB::statement('DROP FUNCTION IF EXISTS rag_documents_search_vector_trigger()');

        // Drop table (cascade will drop all indexes and constraints)
        Schema::dropIfExists('rag_documents');
    }
};
