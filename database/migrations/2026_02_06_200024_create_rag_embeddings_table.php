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
     * Create rag_embeddings table - Vector embeddings (1:1 with chunks).
     * Uses pgvector extension for vector similarity search.
     * IVFFlat index optimized for large datasets (10k-1M vectors).
     *
     * @see docs/AI_ANALYSIS/EGI_POSTGRESQL_RAG_SCHEMA_DESIGN.md
     */
    public function up(): void
    {
        Schema::create('rag_embeddings', function (Blueprint $table) {
            $table->id();

            // Foreign Key (CASCADE delete when parent chunk is deleted)
            // UNIQUE constraint ensures 1:1 relationship with chunks
            $table->foreignId('chunk_id')->unique()->constrained('rag_chunks')->onDelete('cascade');

            // Vector Data (added via raw SQL below - vector(1536) type)

            // Model Tracking
            $table->string('model', 100)->default('text-embedding-3-small');
            $table->string('model_version', 50)->nullable();  // e.g., 'v3', for future model upgrades

            // Embedding Metadata
            $table->timestamp('created_at')->default(DB::raw('NOW()'));
            $table->timestamp('updated_at')->default(DB::raw('NOW()'));
        });

        // Add pgvector column (vector(1536) for OpenAI text-embedding-3-small)
        DB::statement('ALTER TABLE rag_embeddings ADD COLUMN embedding vector(1536) NOT NULL');

        // Vector Search Index (IVFFlat for large datasets > 10k vectors)
        // lists parameter should be ~sqrt(row_count), 100 is good starting point
        // Requires VACUUM ANALYZE after bulk inserts for optimal performance
        DB::statement('
            CREATE INDEX idx_rag_embeddings_vector_ivfflat
                ON rag_embeddings
                USING ivfflat (embedding vector_cosine_ops)
                WITH (lists = 100)
        ');

        // Alternative HNSW index (better for < 100k vectors, faster queries but slower builds)
        // Uncomment if dataset is small or query speed is critical:
        // DB::statement('
        //     CREATE INDEX idx_rag_embeddings_vector_hnsw
        //         ON rag_embeddings
        //         USING hnsw (embedding vector_cosine_ops)
        //         WITH (m = 16, ef_construction = 64)
        // ');

        // Other Indexes for Performance
        DB::statement('CREATE INDEX idx_rag_embeddings_chunk ON rag_embeddings(chunk_id)');
        DB::statement('CREATE INDEX idx_rag_embeddings_model ON rag_embeddings(model)');
        DB::statement('CREATE INDEX idx_rag_embeddings_created ON rag_embeddings(created_at DESC)');

        // Trigger to auto-update updated_at (reuses function from rag_documents)
        DB::statement("
            CREATE TRIGGER rag_embeddings_updated_at
                BEFORE UPDATE ON rag_embeddings
                FOR EACH ROW
                EXECUTE FUNCTION update_updated_at_column();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop trigger first
        DB::statement('DROP TRIGGER IF EXISTS rag_embeddings_updated_at ON rag_embeddings');

        // Drop table (cascade will drop all indexes and constraints)
        Schema::dropIfExists('rag_embeddings');
    }
};
