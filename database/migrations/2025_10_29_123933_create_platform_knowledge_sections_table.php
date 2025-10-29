<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Platform Knowledge Sections
 *
 * Knowledge base for AI Platform Assistant to answer questions
 * about FlorenceEGI features, workflows, and functionalities.
 *
 * @package Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - AI Art Advisor)
 * @date 2025-10-29
 * @purpose Knowledge base for platform guidance and help
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('platform_knowledge_sections', function (Blueprint $table) {
            $table->id();

            // Section identification
            $table->string('section_key')->unique()->comment('Unique key (es: egis.create, wallet.connect)');
            $table->string('category', 50)->index()->comment('Category: egis, collections, wallet, marketplace, general');
            
            // Content
            $table->string('title')->comment('Section title (es: "Come creare un EGI")');
            $table->text('content')->comment('Detailed help content for AI context');
            $table->text('keywords')->nullable()->comment('Searchable keywords (JSON array)');
            
            // Metadata
            $table->integer('priority')->default(100)->comment('Display priority (lower = higher priority)');
            $table->boolean('is_active')->default(true)->index()->comment('Active sections only');
            $table->string('locale', 5)->default('it')->comment('Language: it, en, de, es, fr, pt');
            
            // Timestamps
            $table->timestamps();

            // Indexes for performance
            $table->index(['category', 'is_active']);
            $table->index(['locale', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('platform_knowledge_sections');
    }
};
