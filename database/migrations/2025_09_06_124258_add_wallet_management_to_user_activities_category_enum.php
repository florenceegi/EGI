<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * @package Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Add wallet_management to category enum)
 * @date 2025-09-06
 * @purpose Add wallet_management to user_activities category enum for GDPR compliance
 */
return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        // Per MySQL/MariaDB, modifichiamo l'enum aggiungendo wallet_management
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
                'wallet_management'
            )");
        } else {
            // SQLite: non supporta ENUM, i test useranno string validation nel model
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        // Rimuovi wallet_management dall'enum (solo se non ci sono record con questo valore)
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
                'personal_data_update'
            )");
        }
    }
};
