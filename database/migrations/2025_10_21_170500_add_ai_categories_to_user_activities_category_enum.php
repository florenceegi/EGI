<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * @purpose Add 'ai_processing' and 'egi_trait_management' values to user_activities.category enum
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @date 2025-10-21
 */
return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        // Add the new enum values to the existing category column (MySQL only)
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

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        // Remove the new values from enum (MySQL only)
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
                'notification_management'
            )");
        }
    }
};






