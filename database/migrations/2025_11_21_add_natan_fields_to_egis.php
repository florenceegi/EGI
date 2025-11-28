<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add NATAN document fields to egis table
 * 
 * EGIs diventa la tabella unificata per:
 * - FlorenceEGI NFTs (marketplace)
 * - NATAN PA Documents (administrative acts)
 * - Hybrid use cases (contratti, CoA, etc.)
 * 
 * Differenziazione via campo 'context'
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('egis', function (Blueprint $table) {
            // Context: già esiste ✅
            // original_filename: già esiste ✅
            // mime_type: già esiste ✅
            // size_bytes: già esiste ✅
            // pa_file_path: già esiste (usare quello invece di file_path) ✅
            
            // Aggiungi SOLO campi mancanti:
            
            // Document processing status (complementare a pa_tokenization_status)
            if (!Schema::hasColumn('egis', 'document_status')) {
                $table->enum('document_status', ['pending', 'processing', 'ready', 'failed'])
                    ->default('pending')
                    ->index()
                    ->after('pa_tokenization_status')
                    ->comment('Document processing status for embeddings/RAG (complementary to pa_tokenization_status)');
            }
            
            // Error message for document processing
            if (!Schema::hasColumn('egis', 'document_error_message')) {
                $table->text('document_error_message')
                    ->nullable()
                    ->after('document_status')
                    ->comment('Error message if document processing failed');
            }
            
            // Processing metadata
            if (!Schema::hasColumn('egis', 'processing_metadata')) {
                $table->json('processing_metadata')
                    ->nullable()
                    ->after('document_error_message')
                    ->comment('Processing stats: pages, words, tokens_count, chunks_count, embedding_model, processed_at');
            }
            
            // Processed timestamp
            if (!Schema::hasColumn('egis', 'document_processed_at')) {
                $table->timestamp('document_processed_at')
                    ->nullable()
                    ->after('processing_metadata')
                    ->comment('Timestamp when document processing completed');
            }
            
            // Tenant ID for multi-tenancy
            if (!Schema::hasColumn('egis', 'tenant_id')) {
                $table->unsignedBigInteger('tenant_id')
                    ->nullable()
                    ->index()
                    ->after('user_id')
                    ->comment('Tenant ID for multi-tenant PA documents');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('egis', function (Blueprint $table) {
            // Drop solo i campi che abbiamo aggiunto (non quelli pre-esistenti)
            $table->dropColumn([
                'document_status',
                'document_error_message',
                'processing_metadata',
                'document_processed_at',
                'tenant_id'
            ]);
        });
    }
};

