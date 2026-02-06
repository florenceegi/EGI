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
     * Create documents table - Master documents table for RAG system.
     * Supports multi-language content, full-text search, hierarchical categorization,
     * and flexible metadata via JSONB.
     *
     * Schema: rag_natan.documents
     *
     * @see docs/AI_ANALYSIS/EGI_POSTGRESQL_RAG_SCHEMA_DESIGN.md
     */
    public function up(): void
    {
        // Set search_path to rag_natan schema
        DB::statement('SET search_path TO rag_natan, public');

        Schema::create('documents', function (Blueprint $table) {
            $table->id();

            // Public identifier (UUID) - added via raw SQL below
            $table->foreignId('category_id')->nullable()->constrained('rag_natan.categories')->onDelete('set null');

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
        DB::statement('ALTER TABLE rag_natan.documents ADD COLUMN uuid UUID DEFAULT gen_random_uuid() UNIQUE NOT NULL');

        // Add PostgreSQL array columns (TEXT[])
        DB::statement('ALTER TABLE rag_natan.documents ADD COLUMN tags TEXT[]');
        DB::statement('ALTER TABLE rag_natan.documents ADD COLUMN keywords TEXT[]');

        // Add tsvector column for full-text search
        DB::statement('ALTER TABLE rag_natan.documents ADD COLUMN search_vector tsvector');

        // Add CHECK constraints
        DB::statement("ALTER TABLE rag_natan.documents ADD CONSTRAINT documents_language_check CHECK (language IN ('it', 'en', 'de', 'es', 'fr', 'pt'))");
        DB::statement("ALTER TABLE rag_natan.documents ADD CONSTRAINT documents_status_check CHECK (status IN ('draft', 'published', 'archived'))");

        // Add UNIQUE constraint (slug, language)
        DB::statement('ALTER TABLE rag_natan.documents ADD CONSTRAINT documents_slug_language_unique UNIQUE (slug, language)');

        // Indexes for Performance
        DB::statement('CREATE INDEX idx_documents_category ON rag_natan.documents(category_id) WHERE category_id IS NOT NULL');
        DB::statement('CREATE INDEX idx_documents_language ON rag_natan.documents(language)');
        DB::statement('CREATE INDEX idx_documents_status ON rag_natan.documents(status) WHERE status = \'published\'');
        DB::statement('CREATE INDEX idx_documents_type ON rag_natan.documents(document_type) WHERE document_type IS NOT NULL');
        DB::statement('CREATE INDEX idx_documents_tags ON rag_natan.documents USING gin(tags)');
        DB::statement('CREATE INDEX idx_documents_keywords ON rag_natan.documents USING gin(keywords)');
        DB::statement('CREATE INDEX idx_documents_metadata ON rag_natan.documents USING gin(metadata)');
        DB::statement('CREATE INDEX idx_documents_created ON rag_natan.documents(created_at DESC)');

        // Full-Text Search Index (GIN for tsvector)
        DB::statement('CREATE INDEX idx_documents_search_vector ON rag_natan.documents USING gin(search_vector)');

        // Trigger Function to auto-update search_vector
        DB::statement("
            CREATE OR REPLACE FUNCTION rag_natan.documents_search_vector_trigger() RETURNS trigger AS \$\$
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
            CREATE TRIGGER documents_search_vector_update
                BEFORE INSERT OR UPDATE OF title, excerpt, content, tags, keywords
                ON rag_natan.documents
                FOR EACH ROW
                EXECUTE FUNCTION rag_natan.documents_search_vector_trigger();
        ");

        // Trigger Function to auto-update updated_at
        DB::statement("
            CREATE OR REPLACE FUNCTION rag_natan.update_updated_at_column() RETURNS trigger AS \$\$
            BEGIN
                NEW.updated_at = NOW();
                RETURN NEW;
            END;
            \$\$ LANGUAGE plpgsql;
        ");

        DB::statement("
            CREATE TRIGGER documents_updated_at
                BEFORE UPDATE ON rag_natan.documents
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
        DB::statement('DROP TRIGGER IF EXISTS documents_updated_at ON rag_natan.documents');
        DB::statement('DROP TRIGGER IF EXISTS documents_search_vector_update ON rag_natan.documents');

        // Drop trigger functions
        DB::statement('DROP FUNCTION IF EXISTS rag_natan.update_updated_at_column()');
        DB::statement('DROP FUNCTION IF EXISTS rag_natan.documents_search_vector_trigger()');

        // Drop table (cascade will drop all indexes and constraints)
        Schema::dropIfExists('documents');

        DB::statement('SET search_path TO core, public');
    }
};
