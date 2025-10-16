<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @Oracode Migration: PA Batch Jobs Table
 * 🎯 Purpose: Track individual file processing jobs from NATAN agent
 * 🛡️ Privacy: Stores file hash (not content), GDPR-compliant error messages
 * 🧱 Core Logic: Job queue for metadata processing with retry logic and audit trail
 *
 * @package Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - NATAN Batch Agent System)
 * @date 2025-10-16
 * @purpose Create jobs table for NATAN batch file processing queue
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('pa_batch_jobs', function (Blueprint $table) {
            $table->id();
            
            // Relationships
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('PA entity user who owns this job');
            
            $table->foreignId('source_id')
                ->constrained('pa_batch_sources')
                ->onDelete('cascade');
            
            $table->foreignId('egi_id')
                ->nullable()
                ->constrained('egis')
                ->onDelete('set null');
            
            // File identification (privacy-safe)
            $table->string('file_name', 255); // Just filename, no path
            $table->text('file_path')->nullable(); // PA-side path (for reference)
            $table->char('file_hash', 64)->unique(); // SHA256
            $table->unsignedBigInteger('file_size'); // Bytes
            
            // Processing status
            $table->enum('status', [
                'pending',
                'processing',
                'completed',
                'failed',
                'duplicate'
            ])->default('pending');
            
            // Retry logic
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->unsignedTinyInteger('max_attempts')->default(3);
            
            // Error tracking (GDPR-sanitized)
            $table->text('last_error')->nullable();
            $table->string('error_code', 50)->nullable(); // Coded error (no PII)
            
            // Timing
            $table->timestamp('processing_started_at')->nullable();
            $table->timestamp('processing_completed_at')->nullable();
            $table->unsignedInteger('processing_duration_seconds')->nullable(); // Cache
            
            // Metadata (JSON from agent)
            $table->json('agent_metadata')->nullable();
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['source_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index('file_hash'); // Duplicate check
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('pa_batch_jobs');
    }
};

