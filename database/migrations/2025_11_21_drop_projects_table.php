<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Drop projects table (replaced by collections with context='pa_project')
 * 
 * SAFE: projects table is empty, no data loss
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Skip if projects table doesn't exist
        if (!Schema::hasTable('projects')) {
            return;
        }
        
        // Verifica che sia veramente vuota prima di droppare
        $count = DB::table('projects')->count();
        
        if ($count > 0) {
            throw new \Exception("ATTENZIONE: projects table contiene $count record! Migration bloccata per sicurezza.");
        }
        
        // Drop FK constraints referencing projects table first
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME, TABLE_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE REFERENCED_TABLE_NAME = 'projects'
            AND TABLE_SCHEMA = DATABASE()
        ");
        
        foreach ($foreignKeys as $fk) {
            DB::statement("ALTER TABLE `{$fk->TABLE_NAME}` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
        }
        
        Schema::dropIfExists('projects');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ricrea tabella projects (backup safety)
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->string('icon', 50)->default('folder_open');
            $table->string('color', 20)->default('#1B365D');
            $table->json('settings')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};

