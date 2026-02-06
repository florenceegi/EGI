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
     * Create rag_natan schema - Dedicated PostgreSQL schema for RAG system.
     * Separates RAG tables from core business tables for better organization.
     *
     * Schema: rag_natan.*
     * Tables: categories, documents, chunks, embeddings, queries, responses, sources, query_cache
     *
     * @see docs/AI_ANALYSIS/EGI_POSTGRESQL_RAG_SCHEMA_DESIGN.md
     */
    public function up(): void
    {
        // Create dedicated schema for RAG Natan system
        DB::statement('CREATE SCHEMA IF NOT EXISTS rag_natan');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop schema and all its tables (CASCADE)
        DB::statement('DROP SCHEMA IF EXISTS rag_natan CASCADE');
    }
};
