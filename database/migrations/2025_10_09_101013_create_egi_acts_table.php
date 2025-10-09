<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * N.A.T.A.N. Database Migration - EGI Acts Table
 *
 * Creates table for storing administrative acts metadata extracted via AI
 *
 * @package Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - N.A.T.A.N.)
 * @date 2025-10-09
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('egi_acts', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Unique document identifier (hash-based)
            $table->string('document_id', 255)->unique()->index();

            // Act classification
            $table->string('tipo_atto', 100)->index(); // Determina, Delibera, Ordinanza, etc.
            $table->string('numero_atto', 100)->nullable();
            $table->date('data_atto')->index();

            // Content
            $table->text('oggetto'); // Fulltext index added separately
            $table->string('ente', 255)->nullable()->index();
            $table->string('direzione', 255)->nullable()->index();
            $table->string('responsabile', 255)->nullable();

            // Financial data
            $table->decimal('importo', 15, 2)->nullable()->index();

            // Full metadata JSON from AI
            $table->json('metadata_json');

            // Blockchain certification
            $table->string('hash_firma', 255)->nullable();
            $table->string('blockchain_tx', 255)->nullable()->index();
            $table->text('qr_code')->nullable();

            // Processing status
            $table->enum('processing_status', ['pending', 'completed', 'failed'])->default('pending')->index();

            // AI usage tracking
            $table->integer('ai_tokens_used')->nullable();
            $table->decimal('ai_cost', 10, 4)->nullable();

            // Audit timestamps
            $table->timestamps();

            // Soft deletes for GDPR compliance
            $table->softDeletes();

            // Performance indexes
            $table->index('created_at');
            $table->index(['tipo_atto', 'data_atto']);
            $table->index(['ente', 'direzione']);
        });

        // Add fulltext index for oggetto (MySQL/MariaDB)
        if (config('database.default') === 'mysql') {
            DB::statement('ALTER TABLE egi_acts ADD FULLTEXT fulltext_oggetto (oggetto)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('egi_acts');
    }
};
