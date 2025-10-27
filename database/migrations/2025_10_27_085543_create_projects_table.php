<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Projects Table Migration
 *
 * Tabella per Projects System - Upload documenti PA + priority RAG
 *
 * @package FlorenceEGI
 * @subpackage Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Projects System)
 * @date 2025-10-27
 * @purpose Store PA user projects for document management and RAG
 */
return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();

            // Owner
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Project info
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->string('icon', 50)->default('folder_open'); // Material icon name
            $table->string('color', 7)->default('#1B365D'); // Hex color

            // Settings JSON
            $table->json('settings')->nullable()->comment('
                {
                    "max_documents": 50,
                    "max_size_mb": 10,
                    "auto_embed": true,
                    "priority_rag": true,
                    "allowed_types": ["pdf", "docx", "txt", "csv", "xlsx", "md"]
                }
            ');

            // Status
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('is_active');
            $table->index(['user_id', 'is_active'], 'idx_projects_user_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('projects');
    }
};
