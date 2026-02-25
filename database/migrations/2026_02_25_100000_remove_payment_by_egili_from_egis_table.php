<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * @package App\Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Egili Credit System)
 * @date 2026-02-25
 * @purpose Remove payment_by_egili from egis table — ToS v3.0.0 compliance
 *
 * RATIONALE (ToS v3.0.0 § Egili):
 * Gli Egili NON sono un mezzo di pagamento per l'acquisto di EGI.
 * Sorgente: debiti_tecnici.md §8 — A1
 *
 * PRE-CONDITION:
 * - F10/F11/F12 (debiti_tecnici.md §8) già bloccano UI (showEgiliOption = false)
 * - EgiliPurchaseController non offre mai questa opzione
 * - payment_by_egili è sempre false su tutti i record esistenti
 */
return new class extends Migration {
    /**
     * Run the migrations.
     * Rimuove la colonna payment_by_egili dalla tabella egis.
     * Blocca preventivamente eventuali record true (non dovrebbero esistere).
     */
    public function up(): void {
        // Safety: forza a false qualsiasi record anomalo prima di rimuovere
        DB::table('egis')->where('payment_by_egili', true)->update(['payment_by_egili' => false]);

        Schema::table('egis', function (Blueprint $table) {
            $table->dropColumn('payment_by_egili');
        });
    }

    /**
     * Reverse the migrations.
     * Ripristina la colonna (sempre false — non ripristina il vecchio comportamento).
     */
    public function down(): void {
        Schema::table('egis', function (Blueprint $table) {
            $table->boolean('payment_by_egili')
                ->default(false)
                ->after('price')
                ->comment('DEPRECATA — ToS v3.0.0. Egili NON usabili come pagamento EGI. Sempre false.');
        });
    }
};
