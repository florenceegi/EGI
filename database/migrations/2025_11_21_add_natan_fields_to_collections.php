<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add NATAN project fields to collections table
 * 
 * Collections diventa la tabella unificata per:
 * - FlorenceEGI Collections (marketplace)
 * - NATAN Projects (PA document management)
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
        Schema::table('collections', function (Blueprint $table) {
            // Context discriminator
            $table->enum('context', ['marketplace', 'pa_project', 'hybrid'])
                ->default('marketplace')
                ->index()
                ->after('type')
                ->comment('Context: marketplace (FEGI) or pa_project (NATAN)');
            
            // NATAN Project-specific fields
            $table->string('icon', 50)
                ->nullable()
                ->after('image_avatar')
                ->comment('Material icon name for NATAN projects (e.g., folder_open, description)');
            
            $table->string('color', 20)
                ->nullable()
                ->after('icon')
                ->comment('Hex color for NATAN project UI (e.g., #1B365D)');
            
            $table->json('settings')
                ->nullable()
                ->after('color')
                ->comment('Project settings: max_documents, max_size_mb, allowed_types, auto_embed, priority_rag');
            
            $table->boolean('is_active')
                ->default(true)
                ->index()
                ->after('is_published')
                ->comment('Active status for NATAN projects');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('collections', function (Blueprint $table) {
            $table->dropColumn(['context', 'icon', 'color', 'settings', 'is_active']);
        });
    }
};

