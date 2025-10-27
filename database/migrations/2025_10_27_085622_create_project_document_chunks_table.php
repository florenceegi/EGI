<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Project Document Chunks Table Migration
 *
 * Tabella per chunks di documenti con embeddings per RAG search
 *
 * @package FlorenceEGI
 * @subpackage Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Projects System)
 * @date 2025-10-27
 * @purpose Store document chunks with embeddings for semantic search
 */
return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('project_document_chunks', function (Blueprint $table) {
            $table->id();

            // Parent document
            $table->foreignId('project_document_id')->constrained('project_documents')->onDelete('cascade');

            // Chunk info
            $table->unsignedInteger('chunk_index'); // 0, 1, 2, ...
            $table->text('chunk_text'); // Max ~1000 tokens

            // Embedding (JSON array of 1536 floats for ada-002)
            $table->json('embedding')->nullable()->comment('Vector embedding 1536 dimensions (OpenAI ada-002)');
            $table->string('embedding_model', 50)->default('text-embedding-ada-002'); // Track model version

            // Metadata
            $table->unsignedInteger('tokens_count')->nullable();
            $table->unsignedInteger('page_number')->nullable(); // If applicable (PDF)
            $table->json('metadata')->nullable()->comment('
                {
                    "start_char": 0,
                    "end_char": 500,
                    "overlap_tokens": 200,
                    "source_line": 42
                }
            ');

            $table->timestamps();

            // Indexes
            $table->index('project_document_id');
            $table->index(['project_document_id', 'chunk_index'], 'idx_chunks_document_index');

            // TODO: SPATIAL index for embedding if MariaDB supports
            // MariaDB 10.10+ supports vector search with SPATIAL INDEX
            // $table->spatialIndex('embedding'); // Requires POINT/GEOMETRY type
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('project_document_chunks');
    }
};
