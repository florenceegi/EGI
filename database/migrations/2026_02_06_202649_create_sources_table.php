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
     * Create sources table - Response source citations (N:M pivot).
     * Links responses to the chunks used as evidence.
     *
     * Schema: rag_natan.sources
     *
     * @see docs/AI_ANALYSIS/EGI_POSTGRESQL_RAG_SCHEMA_DESIGN.md
     */
    public function up(): void
    {
        // Set search_path to rag_natan schema
        DB::statement('SET search_path TO rag_natan, core, public');

        Schema::create('sources', function (Blueprint $table) {
            $table->id();

            // Foreign Keys (CASCADE delete)
            $table->foreignId('response_id')->constrained('rag_natan.responses')->onDelete('cascade');
            $table->foreignId('chunk_id')->constrained('rag_natan.chunks')->onDelete('cascade');

            // Citation Metadata
            $table->string('claim_id', 50)->nullable();         // e.g., "CLAIM_001"
            $table->text('exact_quote')->nullable();            // Quoted text from chunk
            $table->decimal('relevance_score', 5, 2)->nullable(); // 0-10 score
            $table->integer('citation_order')->nullable();      // Display order in answer

            // Audit
            $table->timestamp('created_at')->default(DB::raw('NOW()'));

            // UNIQUE constraint (response_id, chunk_id, claim_id)
            $table->unique(['response_id', 'chunk_id', 'claim_id'], 'sources_unique');
        });

        // Indexes
        DB::statement('CREATE INDEX idx_sources_response ON rag_natan.sources(response_id)');
        DB::statement('CREATE INDEX idx_sources_chunk ON rag_natan.sources(chunk_id)');
        DB::statement('CREATE INDEX idx_sources_relevance ON rag_natan.sources(relevance_score DESC)');

        // Reset search_path to default
        DB::statement('SET search_path TO core, public');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('SET search_path TO rag_natan, core, public');
        Schema::dropIfExists('sources');
        DB::statement('SET search_path TO core, public');
    }
};
