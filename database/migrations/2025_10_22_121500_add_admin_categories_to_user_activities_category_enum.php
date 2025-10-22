<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * @purpose Add 'admin_access' and 'admin_action' values to user_activities.category enum
 * @package Database\Migrations
 * @author Padmin D. Curtis
 * @date 2025-10-22
 */
return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        if (DB::getDriverName() === 'mysql') {
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
                'admin_action'
            )");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        if (DB::getDriverName() === 'mysql') {
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
                'egi_trait_management'
            )");
        }
    }
};
