<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * @Oracode Migration: Security Events Table
 * 🎯 Purpose: Track security-related events and incidents
 * 🛡️ Privacy: Security event logging with privacy-safe storage
 * 🧱 Core Logic: Comprehensive security monitoring and audit
 *
 * @author Padmin D. Curtis (for Fabio Cherici)
 * @version 1.0.0
 * @date 2025-05-22
 */
return new class extends Migration {
    /**
     * Run the migrations
     *
     * @return void
     * @privacy-safe Creates security event tracking table
     */
    public function up(): void {
        Schema::create('security_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');

            // Event classification
            $table->string('event_type', 100); // failed_login, suspicious_activity, breach_attempt
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('status', ['detected', 'investigating', 'resolved', 'false_positive'])->default('detected');

            // Event details
            $table->json('details')->nullable(); // Event-specific details
            $table->text('description')->nullable(); // Human-readable description
            $table->json('metadata')->nullable(); // Additional metadata

            // Context
            $table->ipAddress('ip_address')->nullable(); // Masked for privacy
            $table->text('user_agent')->nullable();
            $table->string('source', 50)->nullable(); // web, api, system, external

            // Resolution
            $table->text('resolution_notes')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('set null');

            // Data retention
            $table->timestamp('expires_at')->nullable(); // Long retention for security events

            // Timestamps
            $table->timestamps();

            // Indexes for security monitoring
            $table->index(['user_id', 'event_type']);
            $table->index(['event_type', 'severity']);
            $table->index(['severity', 'status']);
            $table->index(['created_at', 'severity']);
            $table->index('status');
            $table->index('expires_at');

            // Full-text search on description for security analysis (MySQL only)
            if (DB::getDriverName() === 'mysql') {
                $table->fullText('description');
            }
        });
    }

    /**
     * Reverse the migrations
     *
     * @return void
     */
    public function down(): void {
        Schema::dropIfExists('security_events');
    }
};
