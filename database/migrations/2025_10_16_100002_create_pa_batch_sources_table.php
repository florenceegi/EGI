<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @Oracode Migration: PA Batch Sources Table
 * 🎯 Purpose: Track file sources monitored by NATAN agent for batch processing
 * 🛡️ Privacy: Organization-scoped, no PII in source paths
 * 🧱 Core Logic: Stores directory paths, patterns, status, and cached statistics
 *
 * @package Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - NATAN Batch Agent System)
 * @date 2025-10-16
 * @purpose Create sources table for NATAN batch file monitoring
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
        Schema::create('pa_batch_sources', function (Blueprint $table) {
            $table->id();
            
            // PA Entity (User) relationship
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('PA entity user who owns this source');
            
            // Source identification
            $table->string('name', 100); // Es: "Archivio Storico 2023"
            $table->text('description')->nullable();
            $table->text('path'); // Absolute path on PA server
            
            // Processing configuration
            $table->string('file_pattern', 50)->default('*.pdf.p7m'); // Glob pattern
            $table->enum('status', ['active', 'paused', 'error'])->default('paused');
            $table->boolean('auto_process')->default(true);
            $table->unsignedTinyInteger('priority')->default(5); // 1-10 (higher = first)
            
            // Cached statistics (updated by agent)
            $table->unsignedInteger('stats_total')->default(0);
            $table->unsignedInteger('stats_processed')->default(0);
            $table->unsignedInteger('stats_failed')->default(0);
            $table->unsignedInteger('stats_pending')->default(0);
            
            // Activity tracking
            $table->timestamp('last_scan_at')->nullable();
            $table->timestamp('last_processed_at')->nullable();
            
            // Audit
            $table->foreignId('created_by_user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['user_id', 'status']);
            $table->index('priority');
            $table->index('last_scan_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('pa_batch_sources');
    }
};

