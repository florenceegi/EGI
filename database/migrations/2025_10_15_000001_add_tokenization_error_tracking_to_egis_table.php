<?php

/**
 * PA Acts Tokenization Error Tracking Migration
 *
 * Adds error tracking columns to `egis` table for PA Acts tokenization system.
 * Enables persistent error storage and retry attempt tracking for blockchain anchoring.
 *
 * CONTEXT:
 * - PA acts use document anchoring (not NFT minting)
 * - Tokenization happens asynchronously via TokenizePaActJob
 * - Errors must be persisted in DB for UI visibility and retry logic
 * - No need for separate egi_blockchain table (PA workflow is simpler)
 *
 * COLUMNS ADDED:
 * - pa_tokenization_error: TEXT - Stores last error message if tokenization fails
 * - pa_tokenization_attempts: INT - Counts retry attempts for monitoring
 * - pa_tokenization_status: ENUM - Granular status tracking (optional, can use pa_anchored)
 *
 * WORKFLOW STATES:
 * - pending: Upload completed, waiting for tokenization
 * - processing: TokenizePaActJob is running
 * - completed: Successfully anchored on blockchain (pa_anchored=true)
 * - failed: Tokenization failed after retries (see pa_tokenization_error)
 *
 * @package Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - N.A.T.A.N. Error Tracking)
 * @date 2025-10-15
 * @purpose Add error tracking for PA acts tokenization workflow
 *
 * @architecture Database Layer (Migration)
 * @dependencies egis table (2024_12_10_171308_create_egis_table.php)
 * @gdpr-safe Error messages sanitized, no PII stored
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds tokenization error tracking columns to egis table for PA acts.
     * Enables persistent error storage and retry monitoring.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('egis', function (Blueprint $table) {
            // === TOKENIZATION ERROR TRACKING ===

            // Error message from last failed tokenization attempt
            // Stores sanitized error for UI display and debugging
            // NULL = no error, TEXT = last error message
            $table->text('pa_tokenization_error')
                ->nullable()
                ->after('pa_anchored_at')
                ->comment('Last tokenization error message (NULL if no error)');

            // Retry attempt counter for monitoring and circuit breaker
            // Incremented on each TokenizePaActJob execution
            // Reset to 0 on successful anchoring
            $table->integer('pa_tokenization_attempts')
                ->default(0)
                ->after('pa_tokenization_error')
                ->comment('Number of tokenization attempts (reset on success)');

            // === OPTIONAL: GRANULAR STATUS TRACKING ===

            // Granular status for better UI feedback
            // Alternative to boolean pa_anchored for more detailed states
            // Can be used for progress indicators and error handling
            $table->enum('pa_tokenization_status', [
                'pending',      // Upload completed, waiting for tokenization
                'processing',   // TokenizePaActJob is currently running
                'completed',    // Successfully anchored on blockchain
                'failed'        // Tokenization failed after retries
            ])
                ->default('pending')
                ->after('pa_tokenization_attempts')
                ->index()
                ->comment('Granular tokenization status for UI feedback');

            // === PERFORMANCE INDEXES ===

            // Index for filtering failed tokenizations
            $table->index(
                ['pa_tokenization_status', 'pa_tokenization_attempts'],
                'idx_pa_tokenization_status_attempts'
            );

            // Note: Cannot index TEXT column (pa_tokenization_error) in MariaDB
            // Use pa_tokenization_status='failed' to find acts with errors
        });
    }

    /**
     * Reverse the migrations.
     *
     * Removes tokenization error tracking columns from egis table.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('egis', function (Blueprint $table) {
            // Drop indexes first (required for MySQL)
            $table->dropIndex('idx_pa_tokenization_status_attempts');
            
            // Drop columns
            $table->dropColumn([
                'pa_tokenization_error',
                'pa_tokenization_attempts',
                'pa_tokenization_status'
            ]);
        });
    }
};
