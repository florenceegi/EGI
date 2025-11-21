<?php

/**
 * @package App\Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Collection Subscription)
 * @date 2025-11-21
 * @purpose Add 'collection_subscription' to source_type enum in ai_credits_transactions
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * Aggiunge 'collection_subscription' all'enum source_type
     * per tracciare pagamenti abbonamenti collection in Egili
     */
    public function up(): void {
        // MariaDB: ALTER ENUM
        DB::statement("
            ALTER TABLE ai_credits_transactions 
            MODIFY COLUMN source_type ENUM(
                'ai_trait_generation',
                'ai_egi_analysis',
                'ai_pricing',
                'ai_marketing',
                'ai_description',
                'ai_translation',
                'ai_pa_analysis_chunked',
                'payment',
                'subscription_plan',
                'promotion',
                'refund',
                'manual',
                'collection_subscription'
            ) NULL COMMENT 'Tipo sorgente/destinazione'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        // Rimuove 'collection_subscription' dall'enum
        DB::statement("
            ALTER TABLE ai_credits_transactions 
            MODIFY COLUMN source_type ENUM(
                'ai_trait_generation',
                'ai_egi_analysis',
                'ai_pricing',
                'ai_marketing',
                'ai_description',
                'ai_translation',
                'ai_pa_analysis_chunked',
                'payment',
                'subscription_plan',
                'promotion',
                'refund',
                'manual'
            ) NULL COMMENT 'Tipo sorgente/destinazione'
        ");
    }
};
