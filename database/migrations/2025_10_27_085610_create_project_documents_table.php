<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Project Documents Table Migration
 *
 * Tabella per documenti caricati nei progetti PA
 *
 * @package FlorenceEGI
 * @subpackage Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Projects System)
 * @date 2025-10-27
 * @purpose Store uploaded PA documents for RAG processing
 */
return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('project_documents', function (Blueprint $table) {
            $table->id();

            // Parent project
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');

            // File info
            $table->string('filename', 255); // Hashed filename
            $table->string('original_name', 255); // User-visible name
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('size_bytes');
            $table->string('file_path', 500); // storage/app/projects/{project_id}/{filename}

            // Processing status
            $table->enum('status', ['pending', 'processing', 'ready', 'failed'])->default('pending');
            $table->text('error_message')->nullable(); // If status = failed

            // Metadata JSON
            $table->json('metadata')->nullable()->comment('
                {
                    "pages": 10,
                    "words": 5000,
                    "extraction_method": "pdftotext",
                    "chunks_count": 15,
                    "processed_at": "2025-10-27T10:00:00Z"
                }
            ');

            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('project_id');
            $table->index('status');
            $table->index(['project_id', 'status'], 'idx_project_documents_project_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('project_documents');
    }
};
