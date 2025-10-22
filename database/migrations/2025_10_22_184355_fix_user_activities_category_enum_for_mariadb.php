<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * @purpose FIX: Add ALL missing category values to user_activities.category enum (MariaDB compatible)
 * @package Database\Migrations
 * @author Padmin D. Curtis
 * @date 2025-10-22
 * 
 * Previous migrations used `if (DB::getDriverName() === 'mysql')` which failed on MariaDB.
 * This migration fixes the enum once and for all with both mysql and mariadb support.
 */
return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        $driver = DB::getDriverName();
        
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
                'egi_trait_management'
            ) NOT NULL");
        }
    }
};
