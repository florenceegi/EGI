<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @Oracode Migration: Add Batch Fields to EGIs Table
 * 🎯 Purpose: Add PA batch agent fields for file tracking and signature validation
 * 🛡️ Privacy: No sensitive data, only metadata
 * 🧱 Core Logic: Extends PA acts with batch processing metadata
 *
 * @package Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - NATAN Batch Agent System)
 * @date 2025-10-16
 * @purpose Add batch agent metadata fields to egis table
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
        Schema::table('egis', function (Blueprint $table) {
            // PA batch agent fields
            $table->text('pa_file_path')->nullable()->after('pa_public_code')
                ->comment('Original file path on PA server (reference only)');

            $table->boolean('pa_signature_valid')->nullable()->after('pa_file_path')
                ->comment('Digital signature validation result');

            $table->date('pa_signature_date')->nullable()->after('pa_signature_valid')
                ->comment('Digital signature timestamp');

            $table->unsignedBigInteger('file_size')->nullable()->after('file_hash')
                ->comment('File size in bytes');

            $table->longText('extracted_text')->nullable()->after('jsonMetadata')
                ->comment('Full extracted text from PA act (for AI processing)');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('egis', function (Blueprint $table) {
            $table->dropColumn(['pa_file_path', 'pa_signature_valid', 'pa_signature_date', 'file_size', 'extracted_text']);
        });
    }
};
