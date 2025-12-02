<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Refactor project_document_chunks to use egis table
 * 
 * SAFE: All tables are empty (0 records)
 * 
 * Changes:
 * 1. Drop FK constraint to project_documents
 * 2. Rename column: project_document_id → egi_id
 * 3. Add FK constraint to egis
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Verifica che le tabelle siano vuote (safety check) - solo se esistono
        $projectsCount = Schema::hasTable('projects') ? DB::table('projects')->count() : 0;
        $projectDocumentsCount = Schema::hasTable('project_documents') ? DB::table('project_documents')->count() : 0;
        $chunksCount = Schema::hasTable('project_document_chunks') ? DB::table('project_document_chunks')->count() : 0;
        
        if ($projectsCount > 0 || $projectDocumentsCount > 0 || $chunksCount > 0) {
            throw new \Exception(
                "ATTENZIONE: Tabelle non vuote! projects: $projectsCount, project_documents: $projectDocumentsCount, chunks: $chunksCount. Migration bloccata."
            );
        }
        
        // Skip if project_document_chunks doesn't exist or already migrated
        if (!Schema::hasTable('project_document_chunks')) {
            return;
        }
        
        if (!Schema::hasColumn('project_document_chunks', 'project_document_id')) {
            // Already migrated
            return;
        }
        
        Schema::table('project_document_chunks', function (Blueprint $table) {
            // 1. Drop FK constraint to project_documents
            $table->dropForeign(['project_document_id']);
            $table->dropIndex('project_document_chunks_project_document_id_index');
        });
        
        // 2. Rename column (outside main closure for compatibility)
        Schema::table('project_document_chunks', function (Blueprint $table) {
            $table->renameColumn('project_document_id', 'egi_id');
        });
        
        // 3. Add FK constraint to egis
        Schema::table('project_document_chunks', function (Blueprint $table) {
            $table->foreign('egi_id')
                ->references('id')
                ->on('egis')
                ->onDelete('cascade');
            
            $table->index('egi_id');
        });
        
        // 4. Update compound index
        Schema::table('project_document_chunks', function (Blueprint $table) {
            $table->dropIndex('idx_chunks_document_index');
            $table->index(['egi_id', 'chunk_index'], 'idx_chunks_egi_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_document_chunks', function (Blueprint $table) {
            $table->dropForeign(['egi_id']);
            $table->dropIndex(['egi_id']);
            $table->dropIndex('idx_chunks_egi_index');
        });
        
        Schema::table('project_document_chunks', function (Blueprint $table) {
            $table->renameColumn('egi_id', 'project_document_id');
        });
        
        Schema::table('project_document_chunks', function (Blueprint $table) {
            $table->foreign('project_document_id')
                ->references('id')
                ->on('project_documents')
                ->onDelete('cascade');
            
            $table->index('project_document_id');
            $table->index(['project_document_id', 'chunk_index'], 'idx_chunks_document_index');
        });
    }
};

