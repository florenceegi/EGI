<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Add 'ai_credits_usage' to user_activities category enum
 *
 * @package Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - AI Credits GDPR Compliance)
 * @date 2025-10-28
 * @purpose Add ai_credits_usage category for GDPR audit trail of AI credits operations
 */
return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        $driver = DB::getDriverName();

        // Skip for SQLite (testing)
        if ($driver === 'sqlite') {
            return;
        }

        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement("ALTER TABLE user_activities MODIFY COLUMN category ENUM(
                'authentication',
                'authentication_login',
                'authentication_logout',
                'registration',
                'gdpr_actions',
                'data_access',
                'data_deletion',
                'content_creation',
                'content_modification',
                'platform_usage',
                'system_interaction',
                'security_events',
                'blockchain_activity',
                'media_management',
                'privacy_management',
                'personal_data_update',
                'wallet_management',
                'notification_management',
                'ai_processing',
                'ai_credits_usage',
                'egi_trait_management',
                'admin_access',
                'admin_action',
                'wallet_created',
                'wallet_secret_accessed'
            ) NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            return;
        }

        if ($driver === 'mysql' || $driver === 'mariadb') {
            // Set to NULL before removing from enum
            DB::statement("
                UPDATE user_activities
                SET category = 'ai_processing'
                WHERE category = 'ai_credits_usage'
            ");

            DB::statement("ALTER TABLE user_activities MODIFY COLUMN category ENUM(
                'authentication',
                'authentication_login',
                'authentication_logout',
                'registration',
                'gdpr_actions',
                'data_access',
                'data_deletion',
                'content_creation',
                'content_modification',
                'platform_usage',
                'system_interaction',
                'security_events',
                'blockchain_activity',
                'media_management',
                'privacy_management',
                'personal_data_update',
                'wallet_management',
                'notification_management',
                'ai_processing',
                'egi_trait_management',
                'admin_access',
                'admin_action',
                'wallet_created',
                'wallet_secret_accessed'
            ) NOT NULL");
        }
    }
};
