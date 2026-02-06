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
     * Install pgvector extension for vector similarity search.
     * Required for RAG embeddings storage and retrieval.
     */
    public function up(): void
    {
        // Install pgvector extension (0.8.0 on AWS RDS)
        DB::statement('CREATE EXTENSION IF NOT EXISTS vector');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop pgvector extension
        DB::statement('DROP EXTENSION IF EXISTS vector');
    }
};
