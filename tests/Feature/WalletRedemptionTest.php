<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Wallet;
use App\Services\Wallet\WalletProvisioningService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Mockery;
use Tests\TestCase;

/**
 * WalletRedemptionTest
 *
 * Test del flusso completo di riscatto wallet (redemption)
 * che permette all'utente di ottenere la propria seed phrase
 * e cancellare la copia dal server.
 *
 * @Oracode v3.0
 */
class WalletRedemptionTest extends TestCase {
    use DatabaseTransactions;

    protected User $user;
    protected Wallet $wallet;

    protected function setUp(): void {
        parent::setUp();

        // Crea un utente di test
        $this->user = User::factory()->create([
            'email' => 'test-redemption-' . uniqid() . '@example.com',
        ]);

        // Crea un wallet con seed phrase cifrata
        $this->wallet = Wallet::create([
            'user_id' => $this->user->id,
            'wallet' => str_repeat('A', 58), // Indirizzo Algorand valido (58 caratteri)
            'secret_ciphertext' => 'encrypted_mnemonic_data',
            'secret_nonce' => 'random_nonce_value',
            'secret_tag' => 'auth_tag_value',
            'dek_encrypted' => json_encode(['key' => 'encrypted_dek']),
        ]);
    }

    /**
     * Test: La pagina di redemption è accessibile solo agli utenti autenticati
     */
    public function test_redemption_page_requires_authentication(): void {
        $response = $this->get(route('wallet.redemption'));

        $response->assertRedirect(route('login'));
    }

    /**
     * Test: La pagina di redemption viene mostrata correttamente
     */
    public function test_redemption_page_loads_correctly(): void {
        $response = $this->actingAs($this->user)
            ->get(route('wallet.redemption'));

        $response->assertStatus(200);
        $response->assertViewIs('wallet.redemption');
        $response->assertViewHas('walletAddress', $this->wallet->wallet);
        $response->assertViewHas('isRedeemed', false);
    }

    /**
     * Test: Utente senza wallet viene reindirizzato
     */
    public function test_user_without_wallet_is_redirected(): void {
        $userWithoutWallet = User::factory()->create();

        $response = $this->actingAs($userWithoutWallet)
            ->get(route('wallet.redemption'));

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error');
    }

    /**
     * Test: Wallet già riscattato mostra stato "già riscattato"
     */
    public function test_already_redeemed_wallet_shows_correct_state(): void {
        // Svuota i campi della seed phrase (simula riscatto avvenuto)
        $this->wallet->update([
            'secret_ciphertext' => null,
            'secret_nonce' => null,
            'secret_tag' => null,
            'dek_encrypted' => null,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('wallet.redemption'));

        $response->assertStatus(200);
        $response->assertViewHas('isRedeemed', true);
    }

    /**
     * Test: Step 1 - Conferma con testo corretto genera token
     */
    public function test_confirm_redemption_with_correct_text_generates_token(): void {
        $response = $this->actingAs($this->user)
            ->postJson(route('wallet.redemption.confirm'), [
                'confirmation_text' => 'CONFERMO RISCATTO',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $response->assertJsonStructure([
            'success',
            'token',
            'message',
        ]);

        // Verifica che il token sia stato salvato in sessione
        $this->assertNotNull(session('wallet_redemption_token'));
        $this->assertNotNull(session('wallet_redemption_expires'));
        $this->assertEquals($this->user->id, session('wallet_redemption_user_id'));
    }

    /**
     * Test: Step 1 - Conferma con testo errato viene rifiutata
     */
    public function test_confirm_redemption_with_wrong_text_fails(): void {
        $response = $this->actingAs($this->user)
            ->postJson(route('wallet.redemption.confirm'), [
                'confirmation_text' => 'testo sbagliato',
            ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
    }

    /**
     * Test: Step 1 - Conferma senza testo viene rifiutata (validation)
     */
    public function test_confirm_redemption_without_text_fails_validation(): void {
        $response = $this->actingAs($this->user)
            ->postJson(route('wallet.redemption.confirm'), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['confirmation_text']);
    }

    /**
     * Test: Step 2 - Download senza token valido viene rifiutato
     */
    public function test_download_without_valid_token_is_forbidden(): void {
        $response = $this->actingAs($this->user)
            ->get(route('wallet.redemption.download', ['token' => 'invalid_token']));

        $response->assertStatus(403);
    }

    /**
     * Test: Step 2 - Download con token scaduto viene rifiutato
     */
    public function test_download_with_expired_token_is_forbidden(): void {
        $token = bin2hex(random_bytes(32));

        // Imposta token scaduto (1 ora fa)
        session([
            'wallet_redemption_token' => $token,
            'wallet_redemption_expires' => now()->subHour(),
            'wallet_redemption_user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('wallet.redemption.download', ['token' => $token]));

        $response->assertStatus(403);
    }

    /**
     * Test: Step 2 - Download con token valido restituisce file
     */
    public function test_download_with_valid_token_returns_seed_phrase_file(): void {
        $token = bin2hex(random_bytes(32));

        // Imposta sessione valida
        session([
            'wallet_redemption_token' => $token,
            'wallet_redemption_expires' => now()->addMinutes(15),
            'wallet_redemption_user_id' => $this->user->id,
        ]);

        // Mock del WalletProvisioningService per la decriptazione
        $mockWalletService = Mockery::mock(WalletProvisioningService::class);
        $mockWalletService->shouldReceive('retrieveMnemonic')
            ->once()
            ->andReturn('abandon abandon abandon abandon abandon abandon abandon abandon abandon abandon abandon about');

        $this->app->instance(WalletProvisioningService::class, $mockWalletService);

        $response = $this->actingAs($this->user)
            ->get(route('wallet.redemption.download', ['token' => $token]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
        $response->assertHeader('Content-Disposition');

        // Verifica che il contenuto includa parti del documento
        $content = $response->getContent();
        $this->assertStringContainsString('FLORENCE EGI', $content);
        $this->assertStringContainsString('SEED PHRASE', $content);
        $this->assertStringContainsString('abandon', $content);
    }

    /**
     * Test: Step 3 - Finalizzazione senza checkbox viene rifiutata
     */
    public function test_finalize_without_confirmation_fails_validation(): void {
        $response = $this->actingAs($this->user)
            ->postJson(route('wallet.redemption.finalize'), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['confirm_deletion']);
    }

    /**
     * Test: Step 3 - Finalizzazione con checkbox false viene rifiutata
     */
    public function test_finalize_with_false_confirmation_fails(): void {
        $response = $this->actingAs($this->user)
            ->postJson(route('wallet.redemption.finalize'), [
                'confirm_deletion' => false,
            ]);

        $response->assertStatus(422);
    }

    /**
     * Test: Step 3 - Finalizzazione cancella la seed phrase dal DB
     */
    public function test_finalize_deletes_seed_phrase_from_database(): void {
        // Verifica che la seed phrase esista prima
        $this->assertNotNull($this->wallet->secret_ciphertext);

        $response = $this->actingAs($this->user)
            ->postJson(route('wallet.redemption.finalize'), [
                'confirm_deletion' => true,
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        // Verifica che la seed phrase sia stata cancellata
        $this->wallet->refresh();
        $this->assertNull($this->wallet->secret_ciphertext);
        $this->assertNull($this->wallet->secret_nonce);
        $this->assertNull($this->wallet->secret_tag);
        $this->assertNull($this->wallet->dek_encrypted);
    }

    /**
     * Test: Step 3 - Finalizzazione su wallet già riscattato restituisce errore
     */
    public function test_finalize_on_already_redeemed_wallet_fails(): void {
        // Simula wallet già riscattato
        $this->wallet->update([
            'secret_ciphertext' => null,
            'secret_nonce' => null,
            'secret_tag' => null,
            'dek_encrypted' => null,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson(route('wallet.redemption.finalize'), [
                'confirm_deletion' => true,
            ]);

        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
        ]);
    }

    /**
     * Test: Flusso completo di redemption (integration test)
     */
    public function test_complete_redemption_flow(): void {
        // Mock del WalletProvisioningService
        $mockWalletService = Mockery::mock(WalletProvisioningService::class);
        $mockWalletService->shouldReceive('retrieveMnemonic')
            ->once()
            ->andReturn('test mnemonic phrase for integration test');

        $this->app->instance(WalletProvisioningService::class, $mockWalletService);

        // Step 1: Conferma
        $confirmResponse = $this->actingAs($this->user)
            ->postJson(route('wallet.redemption.confirm'), [
                'confirmation_text' => 'CONFERMO RISCATTO',
            ]);

        $confirmResponse->assertStatus(200);
        $token = $confirmResponse->json('token');

        // Step 2: Download
        $downloadResponse = $this->actingAs($this->user)
            ->get(route('wallet.redemption.download', ['token' => $token]));

        $downloadResponse->assertStatus(200);

        // Step 3: Finalizzazione
        $finalizeResponse = $this->actingAs($this->user)
            ->postJson(route('wallet.redemption.finalize'), [
                'confirm_deletion' => true,
            ]);

        $finalizeResponse->assertStatus(200);

        // Verifica stato finale
        $this->wallet->refresh();
        $this->assertNull($this->wallet->secret_ciphertext);
    }

    /**
     * Test: Conferma case-insensitive (lowercase)
     */
    public function test_confirm_redemption_is_case_insensitive(): void {
        $response = $this->actingAs($this->user)
            ->postJson(route('wallet.redemption.confirm'), [
                'confirmation_text' => 'confermo riscatto', // lowercase
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    /**
     * Test: Conferma con spazi extra viene accettata
     */
    public function test_confirm_redemption_trims_whitespace(): void {
        $response = $this->actingAs($this->user)
            ->postJson(route('wallet.redemption.confirm'), [
                'confirmation_text' => '  CONFERMO RISCATTO  ', // con spazi
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    protected function tearDown(): void {
        Mockery::close();
        parent::tearDown();
    }
}
