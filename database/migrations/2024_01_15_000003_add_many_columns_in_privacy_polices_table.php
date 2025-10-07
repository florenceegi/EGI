<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * @Oracode Complete Privacy Policy Schema Alignment OS2
 * 🎯 Purpose: Allineamento completo schema migration con model
 * 🧱 Core Logic: Fix 14 discrepanze tra database e model
 * 📡 API: Schema finale per GDPR compliance completa
 * 🛡️ GDPR: Tutti i campi necessari per privacy management
 *
 * @author Padmin D. Curtis (for Fabio Cherici)
 * @version 2.0.0
 * @date 2025-06-20
 */
return new class extends Migration
{
    /**
     * Run the migrations - Fix completo allineamento
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('privacy_policies', function (Blueprint $table) {

            // ===== 1. DOCUMENT TYPE (enum) =====
            $table->enum('document_type', [
                'privacy_policy',
                'terms_of_service',
                'cookie_policy',
                'data_processing_agreement',
                'consent_form',
                'gdpr_notice',
                'retention_policy',
                'security_policy'
            ])->default('privacy_policy')->after('title');

            // ===== 2. EXPIRY DATE =====
            $table->timestamp('expiry_date')->nullable()->after('effective_date');

            // ===== 3. APPROVAL DATE (quello che mancava!) =====
            $table->timestamp('approval_date')->nullable()->after('approved_by');

            // ===== 4. LEGAL REVIEW SYSTEM =====
            $table->enum('legal_review_status', [
                'pending',
                'in_progress',
                'approved',
                'requires_changes',
                'rejected'
            ])->default('pending')->after('approval_date');

            $table->foreignId('legal_reviewer')->nullable()
                  ->constrained('users')->onDelete('set null')->after('legal_review_status');

            $table->text('review_notes')->nullable()->after('legal_reviewer');

            // ===== 5. CHANGE MANAGEMENT =====
            $table->text('change_description')->nullable()->after('review_notes');
            $table->foreignId('previous_version_id')->nullable()
                  ->constrained('privacy_policies')->onDelete('set null')->after('change_description');

            // ===== 6. NOTIFICATION SYSTEM =====
            $table->boolean('notification_sent')->default(false)->after('previous_version_id');
            $table->timestamp('notification_date')->nullable()->after('notification_sent');

            // ===== 7. CONSENT REQUIREMENT =====
            $table->boolean('requires_consent')->default(true)->after('notification_date');

        });

        // ===== 8. STATUS ENUM UPDATE =====
        // Aggiorna enum status per includere tutti i valori del model
        // SQLite compatible approach - drop and recreate column
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE privacy_policies MODIFY COLUMN status ENUM(
                'draft', 'under_review', 'approved', 'active', 'superseded', 'archived', 'rejected'
            ) DEFAULT 'draft'");
        } else {
            // SQLite: semplicemente aggiorna il default, enum non supportato
            // I test useranno string validation nel model
        }

        // ===== 9. INDICI OTTIMIZZATI =====
        Schema::table('privacy_policies', function (Blueprint $table) {
            $table->index(['document_type', 'is_active'], 'pp_type_active');
            $table->index(['status', 'effective_date'], 'pp_status_effective');
            $table->index(['legal_review_status'], 'pp_legal_review');
            $table->index(['requires_consent'], 'pp_requires_consent');
            $table->index(['language', 'document_type'], 'pp_language_type');
            $table->index(['notification_sent'], 'pp_notification_sent');
            $table->index(['expiry_date'], 'pp_expiry_date');
        });
    }

    /**
     * Reverse the migrations
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('privacy_policies', function (Blueprint $table) {

            // Drop indexes
            $table->dropIndex('pp_type_active');
            $table->dropIndex('pp_status_effective');
            $table->dropIndex('pp_legal_review');
            $table->dropIndex('pp_requires_consent');
            $table->dropIndex('pp_language_type');
            $table->dropIndex('pp_notification_sent');
            $table->dropIndex('pp_expiry_date');

            // Drop foreign keys first
            $table->dropForeign(['legal_reviewer']);
            $table->dropForeign(['previous_version_id']);

            // Drop all added columns
            $table->dropColumn([
                'document_type',
                'expiry_date',
                'approval_date',
                'legal_review_status',
                'legal_reviewer',
                'review_notes',
                'change_description',
                'previous_version_id',
                'notification_sent',
                'notification_date',
                'requires_consent'
            ]);
        });

        // Restore original status enum
        DB::statement("ALTER TABLE privacy_policies MODIFY COLUMN status ENUM(
            'draft', 'review', 'approved', 'published', 'archived'
        ) DEFAULT 'draft'");
    }
};
