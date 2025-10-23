<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * PA Act Embeddings Table
 *
 * Stores vector embeddings for semantic search on PA acts.
 *
 * ARCHITECTURE:
 * - Uses OpenAI text-embedding-ada-002 (1536 dimensions)
 * - Embeddings stored as JSON in MariaDB
 * - Cosine similarity calculated in PHP
 * - Scales to 100k+ acts with acceptable performance
 *
 * EMBEDDING CONTENT:
 * - Protocol number + title + description
 * - Excludes PII, signatures, internal paths (GDPR)
 *
 * FUTURE OPTIMIZATION:
 * - Can migrate to PostgreSQL + pgvector for better performance
 * - Current implementation sufficient for 24k acts
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pa_act_embeddings', function (Blueprint $table) {
            $table->id();

            // Foreign key to egis table
            $table->foreignId('egi_id')
                ->constrained('egis')
                ->cascadeOnDelete()
                ->index();

            // Vector embedding (1536 floats as JSON array)
            // Example: [0.023, -0.045, 0.112, ...]
            $table->json('embedding');

            // Embedding model used (for versioning)
            $table->string('model', 100)->default('text-embedding-ada-002');

            // Hash of content embedded (to detect changes)
            $table->string('content_hash', 64)->index();

            // Metadata for debugging
            $table->unsignedInteger('vector_dimension')->default(1536);

            $table->timestamps();

            // Ensure one embedding per atto (can regenerate if content changes)
            $table->unique('egi_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pa_act_embeddings');
    }
};
