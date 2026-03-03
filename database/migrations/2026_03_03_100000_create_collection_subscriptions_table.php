<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @package Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Collection Subscription FIAT)
 * @date 2026-03-03
 * @purpose Tabella abbonamenti FIAT per Collection Company (Profilo NORMAL).
 *          Sostituisce il flusso errato basato su Egili come "pagamento".
 *          Gli Egili restano strumento di sconto opzionale, mai di pagamento.
 *
 * Relazione: Collection (1) → CollectionSubscription (N) — storico abbonamenti
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collection_subscriptions', function (Blueprint $table) {
            $table->id();

            // === RELAZIONI ===
            $table->foreignId('collection_id')
                ->constrained('collections')
                ->onDelete('cascade')
                ->comment('FK collections.id');

            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('FK users.id — utente che ha sottoscritto');

            // === PIANO ===
            $table->string('feature_code', 100)
                ->comment('feature_code da ai_feature_pricing (es: collection_subscription_starter)');

            $table->string('plan_tier', 50)
                ->comment('Tier leggibile: starter|basic|professional|unlimited');

            $table->unsignedInteger('max_egis')->nullable()
                ->comment('Max EGI consentiti da questo piano. NULL = illimitato');

            // === PAGAMENTO FIAT ===
            $table->string('payment_provider', 50)->default('stripe')
                ->comment('Provider pagamento: stripe|paypal|...');

            $table->string('provider_session_id', 255)->nullable()
                ->comment('Stripe Checkout Session ID o equivalente');

            $table->string('provider_subscription_id', 255)->nullable()
                ->comment('Stripe Subscription ID per rinnovi automatici');

            $table->string('provider_payment_intent_id', 255)->nullable()
                ->comment('Stripe PaymentIntent ID — conferma pagamento');

            $table->decimal('amount_eur', 10, 2)
                ->comment('Importo pagato in EUR');

            // === SCONTO EGILI (opzionale, MiCA-safe) ===
            $table->unsignedInteger('egili_discount_applied')->default(0)
                ->comment('Egili usati per sconto (reward points, non pagamento)');

            $table->decimal('discount_amount_eur', 10, 2)->default(0)
                ->comment('EUR di sconto applicato grazie agli Egili');

            // === STATO ===
            $table->enum('status', [
                'pending',    // In attesa di conferma pagamento
                'active',     // Abbonamento attivo
                'cancelled',  // Cancellato dall'utente
                'expired',    // Scaduto senza rinnovo
                'refunded',   // Rimborsato
                'failed',     // Pagamento fallito
            ])->default('pending')->index()
                ->comment('Stato abbonamento');

            // === DURATA ===
            $table->timestamp('starts_at')->nullable()
                ->comment('Inizio validità abbonamento');

            $table->timestamp('expires_at')->nullable()->index()
                ->comment('Scadenza abbonamento');

            $table->boolean('auto_renew')->default(false)
                ->comment('Se il rinnovo automatico è attivo');

            $table->timestamp('cancelled_at')->nullable()
                ->comment('Data cancellazione (se status=cancelled)');

            // === METADATA ===
            $table->json('metadata')->nullable()
                ->comment('Dati aggiuntivi (webhook payload, note, etc.)');

            $table->timestamps();
            $table->softDeletes();

            // === INDICI ===
            $table->index(['collection_id', 'status', 'expires_at'],
                'col_subs_collection_status_expires');
            $table->index(['user_id', 'status'],
                'col_subs_user_status');
            $table->index('feature_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collection_subscriptions');
    }
};
