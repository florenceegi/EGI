<?php

/**
 * @package App\Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - AI Credits System)
 * @date 2025-10-22
 * @purpose Create table for AI credits transactions and usage tracking
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * Tabella per gestione crediti AI:
     * - Purchase tracking
     * - Usage tracking
     * - Balance management
     * - Subscription tiers
     */
    public function up(): void {
        Schema::create('ai_credits_transactions', function (Blueprint $table) {
            $table->id();

            // === RELAZIONI ===
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('Utente proprietario crediti');

            // === TRANSACTION TYPE ===
            $table->enum('transaction_type', [
                'purchase',        // Acquisto crediti
                'subscription',    // Ricarica subscription
                'bonus',           // Bonus promozionale
                'refund',          // Rimborso
                'usage',           // Consumo crediti
                'expiration',      // Scadenza crediti
                'transfer_in',     // Trasferimento in entrata
                'transfer_out',    // Trasferimento in uscita
                'admin_adjustment' // Correzione admin
            ])->comment('Tipo transazione');

            $table->enum('operation', [
                'add',      // Aggiunta crediti
                'subtract'  // Sottrazione crediti
            ])->comment('Operazione (add/subtract)');

            // === AMOUNTS ===
            $table->unsignedInteger('amount')
                ->comment('Quantità crediti transazione');

            $table->unsignedInteger('balance_before')
                ->comment('Saldo prima della transazione');

            $table->unsignedInteger('balance_after')
                ->comment('Saldo dopo la transazione');

            // === SOURCE/DESTINATION ===
            $table->enum('source_type', [
                'ai_trait_generation',  // Generazione traits
                'ai_egi_analysis',      // Analisi EGI
                'ai_pricing',           // Suggerimenti pricing
                'ai_marketing',         // Azioni marketing
                'ai_description',       // Generazione descrizioni
                'ai_translation',       // Traduzioni AI
                'ai_pa_analysis_chunked', // Analisi PA chunked (Task 5)
                'payment',              // Pagamento
                'subscription_plan',    // Piano subscription
                'promotion',            // Promozione
                'refund',               // Rimborso
                'manual'                // Operazione manuale
            ])->nullable()
                ->comment('Tipo sorgente/destinazione');

            $table->unsignedBigInteger('source_id')
                ->nullable()
                ->comment('ID record sorgente (polymorphic)');

            $table->string('source_model', 255)
                ->nullable()
                ->comment('Classe Model sorgente (polymorphic)');

            // === FEATURE TRACKING ===
            $table->string('feature_used', 100)
                ->nullable()
                ->comment('Feature AI utilizzata');

            $table->json('feature_parameters')
                ->nullable()
                ->comment('Parametri feature (JSON)');

            $table->unsignedInteger('tokens_consumed')
                ->nullable()
                ->comment('Token AI consumati (se applicabile)');

            $table->string('ai_model', 100)
                ->nullable()
                ->comment('Modello AI utilizzato');

            // === SUBSCRIPTION TIER ===
            $table->string('subscription_tier', 50)
                ->nullable()
                ->comment('Tier subscription al momento transazione');

            $table->decimal('discount_applied_percentage', 5, 2)
                ->nullable()
                ->comment('Sconto applicato (%)');

            $table->boolean('was_free_tier')
                ->default(false)
                ->comment('Se era tier gratuito');

            // === PAYMENT DETAILS (se purchase) ===
            $table->string('payment_method', 50)
                ->nullable()
                ->comment('Metodo pagamento (stripe, paypal, etc.)');

            $table->string('payment_transaction_id', 255)
                ->nullable()
                ->comment('ID transazione payment gateway');

            $table->decimal('payment_amount', 10, 2)
                ->nullable()
                ->comment('Importo pagato (EUR)');

            $table->string('currency', 3)
                ->default('EUR')
                ->comment('Valuta pagamento');

            $table->decimal('credits_per_euro', 10, 2)
                ->nullable()
                ->comment('Ratio crediti/euro al momento acquisto');

            // === EXPIRATION ===
            $table->date('expires_at')
                ->nullable()
                ->comment('Data scadenza crediti (se applicabile)');

            $table->boolean('is_expired')
                ->default(false)
                ->comment('Se crediti sono scaduti');

            // === PROMO & BONUSES ===
            $table->string('promo_code', 50)
                ->nullable()
                ->comment('Codice promo utilizzato');

            $table->string('bonus_reason', 255)
                ->nullable()
                ->comment('Motivo bonus');

            $table->boolean('is_bonus')
                ->default(false)
                ->comment('Se è un bonus');

            // === ADMIN ACTIONS ===
            $table->foreignId('admin_user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->comment('Admin che ha eseguito operazione manuale');

            $table->text('admin_notes')
                ->nullable()
                ->comment('Note admin');

            // === STATUS & VALIDATION ===
            $table->enum('status', [
                'pending',      // In attesa (per pagamenti)
                'completed',    // Completata
                'failed',       // Fallita
                'cancelled',    // Cancellata
                'refunded'      // Rimborsata
            ])->default('completed')
                ->comment('Stato transazione');

            $table->text('error_message')
                ->nullable()
                ->comment('Messaggio errore (se failed)');

            // === AUDIT TRAIL ===
            $table->string('ip_address', 45)
                ->nullable()
                ->comment('IP utente');

            $table->string('user_agent', 500)
                ->nullable()
                ->comment('User agent');

            // === METADATA ===
            $table->json('metadata')
                ->nullable()
                ->comment('Metadata aggiuntivi (JSON)');

            $table->timestamps();

            // === INDEXES ===
            $table->index('user_id', 'idx_credits_user');
            $table->index('transaction_type', 'idx_credits_type');
            $table->index('operation', 'idx_credits_operation');
            $table->index('source_type', 'idx_credits_source_type');
            $table->index(['source_type', 'source_id'], 'idx_credits_source');
            $table->index('status', 'idx_credits_status');
            $table->index('feature_used', 'idx_credits_feature');
            $table->index('expires_at', 'idx_credits_expiration');
            $table->index('is_expired', 'idx_credits_expired');
            $table->index(['user_id', 'created_at'], 'idx_credits_user_date');
            $table->index('created_at', 'idx_credits_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('ai_credits_transactions');
    }
};
