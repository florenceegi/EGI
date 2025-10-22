<?php

/**
 * @package App\Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - AI Credits User Balance)
 * @date 2025-10-22
 * @purpose Add AI credits balance and subscription tier to users table
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * Aggiungiamo campi AI credits alla tabella users:
     * - Balance crediti
     * - Subscription tier
     * - Limiti tier
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // === AI CREDITS BALANCE ===
            $table->unsignedInteger('ai_credits_balance')
                ->default(0)
                ->after('remember_token')
                ->comment('Saldo crediti AI disponibili');

            $table->unsignedInteger('ai_credits_lifetime_earned')
                ->default(0)
                ->after('ai_credits_balance')
                ->comment('Totale crediti guadagnati lifetime');

            $table->unsignedInteger('ai_credits_lifetime_used')
                ->default(0)
                ->after('ai_credits_lifetime_earned')
                ->comment('Totale crediti utilizzati lifetime');

            // === SUBSCRIPTION TIER ===
            $table->enum('ai_subscription_tier', [
                'free',       // Tier gratuito (limiti base)
                'starter',    // Starter (per hobbisti)
                'pro',        // Pro (per professionisti)
                'business',   // Business (per gallerie/aziende)
                'enterprise'  // Enterprise (custom limits)
            ])->default('free')
                ->after('ai_credits_lifetime_used')
                ->comment('Tier subscription AI');

            $table->date('ai_subscription_expires_at')
                ->nullable()
                ->after('ai_subscription_tier')
                ->comment('Data scadenza subscription (NULL = free tier)');

            $table->boolean('ai_subscription_active')
                ->default(false)
                ->after('ai_subscription_expires_at')
                ->comment('Se subscription è attiva');

            // === MONTHLY LIMITS (reset ogni mese) ===
            $table->unsignedSmallInteger('ai_monthly_analyses_used')
                ->default(0)
                ->after('ai_subscription_active')
                ->comment('Analisi AI usate questo mese');

            $table->unsignedSmallInteger('ai_monthly_trait_generations_used')
                ->default(0)
                ->after('ai_monthly_analyses_used')
                ->comment('Generazioni traits usate questo mese');

            $table->date('ai_monthly_limit_reset_at')
                ->nullable()
                ->after('ai_monthly_trait_generations_used')
                ->comment('Prossimo reset limiti mensili');

            // === FEATURE ACCESS FLAGS ===
            $table->boolean('ai_access_full_analysis')
                ->default(false)
                ->after('ai_monthly_limit_reset_at')
                ->comment('Accesso analisi complete');

            $table->boolean('ai_access_pricing_suggestions')
                ->default(false)
                ->after('ai_access_full_analysis')
                ->comment('Accesso suggerimenti pricing');

            $table->boolean('ai_access_marketing_actions')
                ->default(false)
                ->after('ai_access_pricing_suggestions')
                ->comment('Accesso azioni marketing');

            $table->boolean('ai_access_trait_generation')
                ->default(true)
                ->after('ai_access_marketing_actions')
                ->comment('Accesso generazione traits (default true)');

            $table->boolean('ai_access_priority_support')
                ->default(false)
                ->after('ai_access_trait_generation')
                ->comment('Accesso support prioritario');

            // === INDEXES ===
            $table->index('ai_subscription_tier', 'idx_user_ai_tier');
            $table->index('ai_subscription_active', 'idx_user_ai_active');
            $table->index('ai_credits_balance', 'idx_user_ai_balance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_user_ai_tier');
            $table->dropIndex('idx_user_ai_active');
            $table->dropIndex('idx_user_ai_balance');

            $table->dropColumn([
                'ai_credits_balance',
                'ai_credits_lifetime_earned',
                'ai_credits_lifetime_used',
                'ai_subscription_tier',
                'ai_subscription_expires_at',
                'ai_subscription_active',
                'ai_monthly_analyses_used',
                'ai_monthly_trait_generations_used',
                'ai_monthly_limit_reset_at',
                'ai_access_full_analysis',
                'ai_access_pricing_suggestions',
                'ai_access_marketing_actions',
                'ai_access_trait_generation',
                'ai_access_priority_support',
            ]);
        });
    }
};