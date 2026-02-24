<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @Oracode Migration: DSA Complaints Table
 * Purpose: Notice-and-Action mechanism and internal complaint handling
 * Compliance: Digital Services Act (Reg. UE 2022/2065) Art. 16, 17, 20, 21
 *
 * @author Padmin D. Curtis (for Fabio Cherici)
 * @version 1.0.0
 * @date 2026-02-24
 */
return new class extends Migration
{
    /**
     * Run the migrations
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();

            // DSA complaint type classification (Art. 16, 20)
            $table->enum('type', [
                'content_report',       // Art. 16 - Illegal content notice
                'ip_violation',         // Intellectual property infringement
                'fraud',                // Fraudulent activity
                'moderation_appeal',    // Art. 20 - Appeal against moderation decision
                'general',              // General complaint
            ]);

            // Status workflow
            $table->enum('status', [
                'received',             // Initial receipt (auto)
                'under_review',         // Being reviewed by moderator
                'action_taken',         // Decision made, action taken
                'dismissed',            // Dismissed with motivation (Art. 17)
                'appealed',             // User appealed the decision (Art. 20)
                'resolved',             // Final resolution
            ])->default('received');

            // Reporter
            $table->foreignId('reporter_user_id')->nullable()->constrained('users')->onDelete('set null');

            // Reported content (polymorphic-style)
            $table->enum('reported_content_type', ['egi', 'collection', 'user_profile', 'comment'])->nullable();
            $table->unsignedBigInteger('reported_content_id')->nullable();

            // Reported user
            $table->foreignId('reported_user_id')->nullable()->constrained('users')->onDelete('set null');

            // Complaint body
            $table->text('description');
            $table->json('evidence_urls')->nullable();

            // Moderator decision (Art. 17 - motivation required)
            $table->text('decision')->nullable();
            $table->foreignId('decision_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('decided_at')->nullable();

            // Appeal (Art. 20 DSA)
            $table->text('appeal_text')->nullable();
            $table->timestamp('appeal_decided_at')->nullable();

            $table->timestamps();

            // Indexes for complaint management
            $table->index(['reporter_user_id', 'status']);
            $table->index(['type', 'status']);
            $table->index(['reported_content_type', 'reported_content_id']);
            $table->index(['status', 'created_at']);
            $table->index('reported_user_id');
        });
    }

    /**
     * Reverse the migrations
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
