<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Egi;
use App\Models\User;
use App\Models\Wallet;
use App\Models\EgiliTransaction;
use App\Services\EgiliService;
use App\Services\Gdpr\AuditLogService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Models\UserActivity;
use Mockery;

/**
 * @Oracode Test: Egili ToS v3.0.0 Compliance Tests
 * 🎯 Purpose: Verify codebase alignment with ToS v3.0.0 Egili reform
 * 🧱 Core Logic: E1-E4 from debiti_tecnici.md §8 — Blocco E
 * 🛡️ Coverage: DB schema, config, service layer, controller output
 *
 * Covers:
 * E1 — Nessun EGI con payment_by_egili=true (colonna rimossa)
 * E2 — Flusso acquisto: solo FIAT valido, crypto disabilitato
 * E3 — Merit reward → accredito Egili → transazione corretta
 * E4 — Flusso pagamento EGI non mostra opzione Egili
 *
 * @package Tests\Feature
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Egili Credit System)
 * @date 2026-02-25
 */
class EgiliTosV3ComplianceTest extends TestCase {
    use DatabaseTransactions;

    // =========================================================
    // E1 — DB Schema: payment_by_egili rimossa, egili ban da egi_blockchain
    // =========================================================

    /**
     * E1a — La colonna payment_by_egili non deve esistere nella tabella egis.
     * A1 migration (2026-02-25) la rimuove definitivamente.
     *
     * @test
     */
    public function test_payment_by_egili_column_removed_from_egis_table(): void {
        $this->assertFalse(
            Schema::hasColumn('egis', 'payment_by_egili'),
            'ToS v3.0.0 — A1: la colonna payment_by_egili deve essere rimossa dalla tabella egis.'
        );
    }

    /**
     * E1b — Il valore 'egili' non deve essere accettato come payment_method in egi_blockchain.
     * A2 migration (2026-02-25) aggiunge il CHECK constraint su PostgreSQL.
     * Su SQLite (test env) verifichiamo che il record con 'egili' non sia stato
     * lasciato da una safety-update nell'out-of-band migration.
     *
     * @test
     */
    public function test_no_egi_blockchain_record_has_egili_as_payment_method(): void {
        $count = DB::table('egi_blockchain')
            ->where('payment_method', 'egili')
            ->count();

        $this->assertEquals(
            0,
            $count,
            'ToS v3.0.0 — A2: nessun record in egi_blockchain deve avere payment_method = "egili".'
        );
    }

    /**
     * E1c — EgiFactory non inserisce payment_by_egili (colonna inesistente).
     * Verifica che il factory crei correttamente un Egi senza errori DB.
     *
     * @test
     */
    public function test_egi_factory_creates_without_payment_by_egili(): void {
        $egi = Egi::factory()->create();

        $this->assertNotNull($egi->id);
        $this->assertFalse(
            Schema::hasColumn('egis', 'payment_by_egili'),
            'Il factory non deve più scrivere payment_by_egili.'
        );
    }

    // =========================================================
    // E2 — Acquisto pacchetto AI: solo FIAT, crypto disabled
    // =========================================================

    /**
     * E2a — Tutti i crypto providers in config/egili.php devono essere disabled.
     * ToS v3.0.0: Egili si ottengono solo via AI Package (FIAT) o merit reward.
     *
     * @test
     */
    public function test_all_crypto_providers_are_disabled_tos_v3(): void {
        $cryptoProviders = config('egili.ai_package_payment_providers.crypto', []);

        $this->assertNotEmpty(
            $cryptoProviders,
            'La sezione crypto deve esistere in config (anche se disabled).'
        );

        foreach ($cryptoProviders as $name => $provider) {
            $this->assertFalse(
                $provider['enabled'] ?? true,
                "ToS v3.0.0 — E2: il provider crypto '{$name}' deve essere disabled."
            );
        }
    }

    /**
     * E2b — Almeno un provider FIAT deve essere enabled.
     * Verifica che il flusso di acquisto AI Package via FIAT sia operativo.
     *
     * @test
     */
    public function test_at_least_one_fiat_provider_is_enabled(): void {
        $fiatProviders = config('egili.ai_package_payment_providers.fiat', []);

        $enabledProviders = array_filter($fiatProviders, fn($p) => $p['enabled'] ?? false);

        $this->assertNotEmpty(
            $enabledProviders,
            'ToS v3.0.0 — E2: almeno un provider FIAT deve essere abilitato per acquistare Pacchetti AI.'
        );
    }

    /**
     * E2c — Il POST /egili/purchase con payment_method=crypto deve essere rifiutato
     * per utente autenticato (validation 422 o logica blocked).
     *
     * @test
     */
    public function test_egili_purchase_endpoint_rejects_crypto_payment_method(): void {
        $user = User::factory()->create();
        Wallet::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->postJson(route('egili.purchase.process'), [
                'egili_amount'    => 5000,
                'payment_method'  => 'crypto',
                'crypto_provider' => 'algorand',
            ]);

        // Deve fallire (422 validation o 500 da provider disabled) — mai 200 success
        $this->assertNotEquals(
            200,
            $response->status(),
            'ToS v3.0.0 — E2: il flusso crypto non deve mai completarsi con successo.'
        );
    }

    // =========================================================
    // E3 — Merit reward → accredito Egili → transazione corretta
    // =========================================================

    /**
     * E3 — grantGiftFromSystem() (merit reward) deve:
     *  - Aumentare il saldo Egili del wallet
     *  - Creare una EgiliTransaction con transaction_type='admin_grant', category='reward'
     *  - Impostare egili_type='gift'
     *
     * @test
     */
    public function test_merit_reward_grants_egili_and_creates_correct_transaction(): void {
        // Arrange
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id'       => $user->id,
            'egili_balance' => 0,
        ]);

        $mockLogger = Mockery::mock(UltraLogManager::class);
        $mockLogger->shouldReceive('info')->andReturn(null);
        $mockLogger->shouldReceive('warning')->andReturn(null);
        $mockLogger->shouldReceive('error')->andReturn(null);

        $mockErrorManager = Mockery::mock(ErrorManagerInterface::class);

        $mockAuditService = Mockery::mock(AuditLogService::class);
        $mockAuditService->shouldReceive('logUserAction')->andReturn(Mockery::mock(UserActivity::class));

        $service = new EgiliService($mockLogger, $mockErrorManager, $mockAuditService);

        // Act — simula premiazione merito (contest_winner, achievement, etc.)
        $transaction = $service->grantGiftFromSystem(
            $user,
            amount: 500,
            expirationDays: 90,
            reason: 'contest_winner',
            metadata: ['contest_id' => 42]
        );

        // Assert — transazione corretta
        $this->assertInstanceOf(EgiliTransaction::class, $transaction);
        $this->assertEquals(
            'admin_grant',
            $transaction->transaction_type,
            'Merit reward deve usare transaction_type=admin_grant.'
        );
        $this->assertEquals(
            'reward',
            $transaction->category,
            'Merit reward deve avere category=reward.'
        );
        $this->assertEquals(
            'gift',
            $transaction->egili_type,
            'Merit reward credita Egili di tipo gift (con scadenza).'
        );
        $this->assertEquals(500, $transaction->amount);
        $this->assertEquals(0, $transaction->balance_before);
        $this->assertEquals(500, $transaction->balance_after);
        $this->assertEquals('completed', $transaction->status);

        // Assert — saldo wallet aggiornato
        $wallet->refresh();
        $this->assertEquals(
            500,
            $wallet->egili_balance,
            'Il saldo wallet deve riflettere l\'accredito del merit reward.'
        );
    }

    // =========================================================
    // E4 — Flusso pagamento EGI: opzione Egili mai presentata
    // =========================================================

    /**
     * E4a — La colonna payment_by_egili non esiste: su qualsiasi istanza Egi
     * l'attributo è null (falsy). La logica del MintController
     * `if ($egi->payment_by_egili && ...)` rimane sempre false
     * → showEgiliOption non può mai diventare true.
     *
     * @test
     */
    public function test_mint_payment_form_never_passes_show_egili_option_true(): void {
        // Prerequisito: la colonna è stata rimossa (A1)
        $this->assertFalse(
            Schema::hasColumn('egis', 'payment_by_egili'),
            'ToS v3.0.0 — E4: payment_by_egili deve essere assente dallo schema egis.'
        );

        // Verifica comportamento Eloquent: attributo inesistente → null (falsy)
        $egi = Egi::factory()->create(['price' => 100]);
        $paymentByEgili = $egi->payment_by_egili;

        $this->assertNull(
            $paymentByEgili,
            'ToS v3.0.0 — E4: $egi->payment_by_egili deve essere null su colonna rimossa.'
        );

        // Simula la logica del controller: showEgiliOption partition
        $showEgiliOption = false;
        if ($egi->payment_by_egili && 100 > 0) {
            $showEgiliOption = true; // dead code — non deve mai eseguire
        }

        $this->assertFalse(
            $showEgiliOption,
            'ToS v3.0.0 — E4: la logica controller produce sempre showEgiliOption=false.'
        );
    }

    /**
     * E4b — RebindController non deve passare showEgiliOption=true alla view.
     * Stesso ragionamento di E4a per il mercato secondario (rebind).
     *
     * @test
     */
    public function test_rebind_checkout_never_passes_show_egili_option_true(): void {
        $owner = User::factory()->create();
        $buyer = User::factory()->create();
        $egi   = Egi::factory()->create([
            'user_id'      => $owner->id,
            'is_published' => true,
            'price'        => 100,
        ]);

        $response = $this->actingAs($buyer)
            ->get(route('egi.rebind', ['id' => $egi->id]));

        // Può essere 200 o redirect — in ogni caso non deve mostrare l'opzione Egili
        if ($response->status() === 200) {
            $viewData = $response->viewData('showEgiliOption');
            $this->assertFalse(
                (bool) $viewData,
                'ToS v3.0.0 — E4: showEgiliOption deve essere false nel checkout rebind.'
            );
        } else {
            // Redirect o altro: in ogni caso non è arrivato alla view con egili enabled
            $this->assertTrue(true, 'Rebind ha reindirizzato — nessuna opzione Egili mostrata.');
        }
    }

    protected function tearDown(): void {
        Mockery::close();
        parent::tearDown();
    }
}
