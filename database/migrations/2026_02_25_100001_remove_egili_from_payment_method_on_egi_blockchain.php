<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * @package App\Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Egili Credit System)
 * @date 2026-02-25
 * @purpose Remove 'egili' from payment_method on egi_blockchain — ToS v3.0.0 compliance
 *
 * RATIONALE (ToS v3.0.0 § Egili):
 * Gli Egili NON sono un mezzo di pagamento per l'acquisto/trasferimento di EGI.
 * Sorgente: debiti_tecnici.md §8 — A2
 *
 * DB STRATEGY per driver:
 * - PostgreSQL: payment_method è stringa — aggiunge CHECK constraint che esclude 'egili'
 * - MySQL:       MODIFY COLUMN ENUM senza 'egili'
 * - SQLite:      nessuna azione (test environment)
 */
return new class extends Migration {
    /** Valori ammessi dopo la rimozione di 'egili' */
    private const VALID_METHODS = ['stripe', 'paypal', 'bank_transfer', 'mock'];

    /**
     * Run the migrations.
     */
    public function up(): void {
        // Safety: aggiorna eventuali record anomali (non dovrebbero esistere)
        DB::table('egi_blockchain')
            ->where('payment_method', 'egili')
            ->update(['payment_method' => 'mock']);

        $driver = DB::connection()->getDriverName();

        if ($driver === 'pgsql') {
            // Rimuove constraint precedente se esiste, poi aggiunge senza 'egili'
            DB::statement('ALTER TABLE egi_blockchain DROP CONSTRAINT IF EXISTS egi_blockchain_payment_method_check');
            $values = implode("','", self::VALID_METHODS);
            DB::statement("ALTER TABLE egi_blockchain ADD CONSTRAINT egi_blockchain_payment_method_check CHECK (payment_method IN ('{$values}'))");
        }

        if ($driver === 'mysql') {
            $values = implode("','", self::VALID_METHODS);
            DB::statement("ALTER TABLE egi_blockchain MODIFY COLUMN payment_method ENUM('{$values}') DEFAULT 'mock'");
        }
        // SQLite: nessuna azione
    }

    /**
     * Reverse the migrations.
     * Ripristina l'accettazione di 'egili' (solo per rollback tecnico — NON per produzione).
     */
    public function down(): void {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE egi_blockchain DROP CONSTRAINT IF EXISTS egi_blockchain_payment_method_check');
            $methods = array_merge(self::VALID_METHODS, ['egili']);
            $values = implode("','", $methods);
            DB::statement("ALTER TABLE egi_blockchain ADD CONSTRAINT egi_blockchain_payment_method_check CHECK (payment_method IN ('{$values}'))");
        }

        if ($driver === 'mysql') {
            $methods = implode("','", array_merge(self::VALID_METHODS, ['egili']));
            DB::statement("ALTER TABLE egi_blockchain MODIFY COLUMN payment_method ENUM('{$methods}') DEFAULT 'mock'");
        }
    }
};
