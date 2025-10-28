<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Add 'ai_pa_analysis_chunked' to source_type enum
 *
 * CONTEXT: Task 5 - AI Credits Cost Tracking for chunked PA analysis
 *
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - AI Credits)
 * @date 2025-10-27
 */
return new class extends Migration {
    public function up(): void {
        // Skip for SQLite (enum handled differently)
        if (config('database.default') === 'sqlite') {
            return;
        }

        DB::statement("
            ALTER TABLE `ai_credits_transactions`
            MODIFY COLUMN `source_type` ENUM(
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
                'manual'
            ) NULL
        ");
    }

    public function down(): void {
        if (config('database.default') === 'sqlite') {
            return;
        }

        // Set to NULL before removing from enum
        DB::statement("
            UPDATE `ai_credits_transactions`
            SET `source_type` = NULL
            WHERE `source_type` = 'ai_pa_analysis_chunked'
        ");

        DB::statement("
            ALTER TABLE `ai_credits_transactions`
            MODIFY COLUMN `source_type` ENUM(
                'ai_trait_generation',
                'ai_egi_analysis',
                'ai_pricing',
                'ai_marketing',
                'ai_description',
                'ai_translation',
                'payment',
                'subscription_plan',
                'promotion',
                'manual'
            ) NULL
        ");
    }
};
