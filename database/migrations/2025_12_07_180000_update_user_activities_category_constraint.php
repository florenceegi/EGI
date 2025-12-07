<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * @package Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - PostgreSQL Migration)
 * @date 2025-12-07
 * @purpose Update category CHECK constraint to include all GdprActivityCategory values
 *          Adds: wallet_created, wallet_secret_accessed, wallet_redeemed, wallet_mnemonic_deleted,
 *                admin_access, admin_action, ai_processing, ai_credits_usage, egi_trait_management
 */
return new class extends Migration {
    /**
     * Run the migrations
     *
     * @return void
     */
    public function up(): void {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'pgsql') {
            // PostgreSQL: Drop old constraint and create new one with all categories
            DB::statement('ALTER TABLE user_activities DROP CONSTRAINT IF EXISTS user_activities_category_check');

            DB::statement("
                ALTER TABLE user_activities
                ADD CONSTRAINT user_activities_category_check
                CHECK (category IN (
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
                    'admin_access',
                    'admin_action',
                    'blockchain_activity',
                    'media_management',
                    'privacy_management',
                    'personal_data_update',
                    'wallet_management',
                    'wallet_created',
                    'wallet_secret_accessed',
                    'wallet_redeemed',
                    'wallet_mnemonic_deleted',
                    'notification_management',
                    'ai_processing',
                    'ai_credits_usage',
                    'egi_trait_management'
                ))
            ");
        }
        // MySQL/MariaDB: ENUM is already defined in the column, no constraint needed
    }

    /**
     * Reverse the migrations
     *
     * @return void
     */
    public function down(): void {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'pgsql') {
            // Revert to original constraint (though this might fail if new categories are in use)
            DB::statement('ALTER TABLE user_activities DROP CONSTRAINT IF EXISTS user_activities_category_check');

            DB::statement("
                ALTER TABLE user_activities
                ADD CONSTRAINT user_activities_category_check
                CHECK (category IN (
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
                    'notification_management'
                ))
            ");
        }
    }
};
