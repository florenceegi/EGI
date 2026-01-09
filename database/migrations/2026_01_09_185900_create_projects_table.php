<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create system_projects table
 * 
 * SystemProjects represent applications/codebases (FlorenceEGI, NATAN_LOC, etc.)
 * This is distinct from Tenants which represent client organizations.
 * Named "system_projects" to avoid conflict with existing "projects" (PA document projects).
 * 
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0
 * @date 2026-01-09
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_projects', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique()->comment('Short code: FEGI, NATAN_LOC');
            $table->string('name')->comment('Display name: FlorenceEGI');
            $table->boolean('is_multitenant')->default(true)->comment('true=has tenants, false=monotenant');
            $table->json('settings')->nullable()->comment('Project-specific configuration');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_projects');
    }
};
