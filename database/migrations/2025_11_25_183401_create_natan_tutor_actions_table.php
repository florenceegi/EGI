<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Natan Tutor Actions
 *
 * Tracking delle azioni eseguite da Natan Tutor per gli utenti.
 * Ogni azione consuma Egili e viene tracciata per analytics e supporto.
 *
 * @see docs/FlorenceEGI/Implementation/NatanTutor/NATAN_TUTOR_DESIGN.md
 */
return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('natan_tutor_actions', function (Blueprint $table) {
            $table->id();

            // User reference
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Action details
            $table->string('action_code', 100)->comment('e.g., natan_action_mint, natan_navigate');
            $table->json('action_params')->nullable()->comment('Parameters passed to action');
            $table->string('mode', 20)->default('tutoring')->comment('tutoring or expert');

            // Egili cost
            $table->integer('egili_cost')->default(0)->comment('Egili charged for this action');

            // Status tracking
            $table->enum('status', [
                'pending',      // Waiting for confirmation
                'confirmed',    // User confirmed, ready to execute
                'executing',    // Currently executing
                'completed',    // Successfully completed
                'failed',       // Execution failed
                'cancelled',    // User cancelled
                'refunded'      // Egili refunded due to failure
            ])->default('pending');

            // Result tracking
            $table->json('result')->nullable()->comment('Action result data');
            $table->text('error_message')->nullable()->comment('Error details if failed');

            // Timestamps
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('executed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'created_at'], 'idx_user_actions');
            $table->index('action_code', 'idx_action_code');
            $table->index('status', 'idx_status');
            $table->index(['user_id', 'status'], 'idx_user_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('natan_tutor_actions');
    }
};
