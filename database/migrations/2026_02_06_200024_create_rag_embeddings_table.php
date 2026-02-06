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
     * Create embeddings table - Vector embeddings (1:1 with chunks).
     * Uses pgvector extension for vector similarity search.
     * IVFFlat index optimized for large datasets (10k-1M vectors).
     *
     * Schema: rag_natan.embeddings
     *
     * @see docs/AI_ANALYSIS/EGI_POSTGRESQL_RAG_SCHEMA_DESIGN.md
     */
    public function up(): void
    {
        // Set search_path to rag_natan schema (include core for pgvector type)
        DB::statement('SET search_path TO rag_natan, core, public');

        Schema::create('embeddings', function (Blueprint $table) {
            $table->id();

            // Foreign Key (CASCADE delete when parent chunk is deleted)
            // UNIQUE constraint ensures 1:1 relationship with chunks
            $table->foreignId('chunk_id')->unique()->constrained('rag_natan.chunks')->onDelete('cascade');

            // Vector Data (added via raw SQL below - vector(1536) type)

            // Model Tracking
            $table->string('model', 100)->default('text-embedding-3-small');
            $table->string('model_version', 50)->nullable();  // e.g., 'v3', for future model upgrades

            // Embedding Metadata
            $table->timestamp('created_at')->default(DB::raw('NOW()'));
            $table->timestamp('updated_at')->default(DB::raw('NOW()'));
        });

        // Add pgvector column (vector(1536) for OpenAI text-embedding-3-small)
        DB::statement('ALTER TABLE rag_natan.embeddings ADD COLUMN embedding vector(1536) NOT NULL');

        // Vector Search Index (IVFFlat for large datasets > 10k vectors)
        // lists parameter should be ~sqrt(row_count), 100 is good starting point
        // Requires VACUUM ANALYZE after bulk inserts for optimal performance
        DB::statement('
            CREATE INDEX idx_embeddings_vector_ivfflat
                ON rag_natan.embeddings
                USING ivfflat (embedding vector_cosine_ops)
                WITH (lists = 100)
        ');

        // Alternative HNSW index (better for < 100k vectors, faster queries but slower builds)
        // Uncomment if dataset is small or query speed is critical:
        // DB::statement('
        //     CREATE INDEX idx_embeddings_vector_hnsw
        //         ON rag_natan.embeddings
        //         USING hnsw (embedding vector_cosine_ops)
        //         WITH (m = 16, ef_construction = 64)
        // ');

        // Other Indexes for Performance
        DB::statement('CREATE INDEX idx_embeddings_chunk ON rag_natan.embeddings(chunk_id)');
        DB::statement('CREATE INDEX idx_embeddings_model ON rag_natan.embeddings(model)');
        DB::statement('CREATE INDEX idx_embeddings_created ON rag_natan.embeddings(created_at DESC)');

        // Trigger to auto-update updated_at (reuses function from documents migration)
        DB::statement("
            CREATE TRIGGER embeddings_updated_at
                BEFORE UPDATE ON rag_natan.embeddings
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

        // Drop trigger first
        DB::statement('DROP TRIGGER IF EXISTS embeddings_updated_at ON rag_natan.embeddings');

        // Drop table (cascade will drop all indexes and constraints)
        Schema::dropIfExists('embeddings');

        DB::statement('SET search_path TO core, public');
    }
};
