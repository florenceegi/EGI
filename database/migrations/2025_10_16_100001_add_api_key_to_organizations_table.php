<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @Oracode Migration: Add API Key to Users
 * 🎯 Purpose: Enable PA entities to authenticate NATAN agent via API
 * 🛡️ Privacy: API keys are encrypted at rest for security
 * 🧱 Core Logic: Adds encrypted api_key field to users table (for PA entities)
 *
 * @package Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - NATAN Batch Agent System)
 * @date 2025-10-16
 * @purpose Add API authentication capability for PA entity users
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
        Schema::table('users', function (Blueprint $table) {
            // API key for NATAN agent authentication (encrypted, only for PA entities)
            $table->text('natan_api_key')->nullable()->after('remember_token');

            // API key metadata
            $table->timestamp('natan_api_key_generated_at')->nullable()->after('natan_api_key');
            $table->timestamp('natan_api_key_last_used_at')->nullable()->after('natan_api_key_generated_at');

            // Index for performance
            $table->index('natan_api_key_last_used_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['natan_api_key_last_used_at']);
            $table->dropColumn(['natan_api_key', 'natan_api_key_generated_at', 'natan_api_key_last_used_at']);
        });
    }
};
