<?php
// database/migrations/2024_01_15_000011_create_consent_histories_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * @Oracode Migration: Consent Histories Table
 * 🎯 Purpose: Detailed historical tracking of all consent changes
 * 🛡️ Privacy: GDPR Article 7 consent documentation and audit trail
 * 🧱 Core Logic: Immutable consent change log for legal compliance
 *
 * @author Padmin D. Curtis (for Fabio Cherici)
 * @version 1.0.0
 * @date 2024-01-15
 */
return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     * @privacy-safe Creates immutable consent history tracking
     */
    public function up(): void {
        Schema::create('consent_histories', function (Blueprint $table) {
            $table->id();

            // User and consent relationship
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_consent_id')->nullable()->constrained()->onDelete('set null');
            $table->string('consent_type_slug', 100);

            // Historical action tracking
            $table->enum('action', [
                'granted',              // Initial consent given
                'renewed',              // Consent renewed/refreshed
                'withdrawn',            // Consent withdrawn
                'expired',              // Consent expired automatically
                'updated',              // Consent terms updated
                'migrated',             // Consent migrated to new version
                'restored',             // Withdrawn consent restored
                'invalidated'           // Consent invalidated (e.g., age verification failed)
            ]);

            $table->timestamp('action_timestamp');
            $table->string('action_source', 100); // web, mobile_app, api, admin, system

            // Previous and new state
            $table->json('previous_state')->nullable(); // State before action
            $table->json('new_state'); // State after action
            $table->json('state_diff')->nullable(); // What specifically changed

            // Consent details at time of action
            $table->string('consent_version', 20)->nullable();
            $table->text('consent_text_shown')->nullable(); // Exact text user saw
            $table->json('consent_options_available')->nullable(); // Options presented
            $table->json('consent_selections')->nullable(); // What user selected

            // User interaction evidence
            $table->string('interaction_method', 50); // checkbox, toggle, form, etc.
            $table->boolean('explicit_action')->default(true); // Was it explicit user action
            $table->integer('time_to_decision')->nullable(); // Seconds to make decision
            $table->json('interaction_metadata')->nullable(); // Mouse movements, scrolling, etc.

            // Technical context
            $table->ipAddress('ip_address');
            $table->text('user_agent');
            $table->string('session_id', 100)->nullable();
            $table->string('device_fingerprint', 255)->nullable();
            $table->json('browser_info')->nullable();
            $table->string('referrer_url', 500)->nullable();

            // Legal and business context
            $table->string('legal_basis', 100)->default('consent');
            $table->text('reason_for_action')->nullable(); // Why action was taken
            $table->string('triggered_by', 100)->nullable(); // What triggered this action
            $table->json('business_context')->nullable(); // Additional business information

            // Notification and communication
            $table->boolean('user_notified')->default(false);
            $table->timestamp('notification_sent_at')->nullable();
            $table->string('notification_channel', 50)->nullable();
            $table->boolean('acknowledgment_required')->default(false);
            $table->timestamp('acknowledged_at')->nullable();

            // Verification and validation
            $table->boolean('age_verified')->nullable();
            $table->boolean('identity_verified')->nullable();
            $table->json('verification_methods')->nullable();
            $table->text('verification_notes')->nullable();

            // Administrative tracking
            $table->foreignId('admin_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('admin_notes')->nullable();
            $table->boolean('requires_review')->default(false);
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');

            // Data integrity
            $table->string('record_hash', 64); // Immutability verification
            $table->json('integrity_metadata')->nullable();
            $table->boolean('is_verified')->default(true);

            // Related records
            $table->string('related_request_id', 100)->nullable(); // Related GDPR request
            $table->string('related_incident_id', 100)->nullable(); // Related security incident
            $table->json('related_records')->nullable(); // Other related records

            // Timestamps
            $table->timestamps();

            // Indexes for performance and reporting
            $table->index(['user_id', 'action_timestamp'], 'ch_user_action_time');
            $table->index(['user_id', 'consent_type_slug', 'action_timestamp'], 'ch_user_consent_time');
            $table->index(['consent_type_slug', 'action'], 'ch_consent_action');
            $table->index(['action', 'action_timestamp'], 'ch_action_time');
            $table->index(['action_source', 'action_timestamp'], 'ch_source_time');
            $table->index('action_timestamp', 'ch_action_timestamp');
            $table->index(['requires_review', 'action_timestamp'], 'ch_review_time');

            // Full-text search on reason and notes (MySQL only)
            if (DB::getDriverName() === 'mysql') {
                $table->fullText(['reason_for_action', 'admin_notes']);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void {
        Schema::dropIfExists('consent_histories');
    }
};
