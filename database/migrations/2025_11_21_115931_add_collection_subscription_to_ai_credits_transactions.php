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
use App\Helpers\DatabaseHelper;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * Aggiunge 'collection_subscription' all'enum source_type
     * per tracciare pagamenti abbonamenti collection in Egili
     */
    public function up(): void {
        $driver = DB::getDriverName();

        if ($driver === 'mysql' || $driver === 'mariadb') {
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
        } elseif ($driver === 'pgsql') {
            // Drop old constraint
            DB::statement("ALTER TABLE ai_credits_transactions DROP CONSTRAINT IF EXISTS ai_credits_transactions_source_type_check");
            // Add new constraint with new value
            DB::statement("ALTER TABLE ai_credits_transactions ADD CONSTRAINT ai_credits_transactions_source_type_check CHECK (source_type::text = ANY (ARRAY[
                'ai_trait_generation'::character varying,
                'ai_egi_analysis'::character varying,
                'ai_pricing'::character varying,
                'ai_marketing'::character varying,
                'ai_description'::character varying,
                'ai_translation'::character varying,
                'ai_pa_analysis_chunked'::character varying,
                'payment'::character varying,
                'subscription_plan'::character varying,
                'promotion'::character varying,
                'refund'::character varying,
                'manual'::character varying,
                'collection_subscription'::character varying
            ]::text[]))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        $driver = DB::getDriverName();

        if ($driver === 'mysql' || $driver === 'mariadb') {
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
        } elseif ($driver === 'pgsql') {
            // Drop new constraint
            DB::statement("ALTER TABLE ai_credits_transactions DROP CONSTRAINT IF EXISTS ai_credits_transactions_source_type_check");
            // Restore old constraint (without collection_subscription)
            DB::statement("ALTER TABLE ai_credits_transactions ADD CONSTRAINT ai_credits_transactions_source_type_check CHECK (source_type::text = ANY (ARRAY[
                'ai_trait_generation'::character varying,
                'ai_egi_analysis'::character varying,
                'ai_pricing'::character varying,
                'ai_marketing'::character varying,
                'ai_description'::character varying,
                'ai_translation'::character varying,
                'ai_pa_analysis_chunked'::character varying,
                'payment'::character varying,
                'subscription_plan'::character varying,
                'promotion'::character varying,
                'refund'::character varying,
                'manual'::character varying
            ]::text[]))");
        }
    }
};
