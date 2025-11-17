<?php

namespace Tests\Feature\Payment;

use App\Models\Collection;
use App\Models\Egi;
use App\Models\User;
use App\Models\Wallet;
use App\Services\Payment\MerchantAccountResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * @Oracode Integration Test: Merchant Account Validation con Stripe Test Mode
 * 🎯 Purpose: Test REALI con Stripe API in Test Mode
 * 🛡️ Coverage: Validazione account Stripe REALI (charges_enabled, details_submitted)
 * 
 * ⚠️ REQUIREMENTS:
 * - Stripe Test Mode account configurato
 * - STRIPE_ENABLED=true in .env.testing
 * - STRIPE_SECRET_KEY con chiave test (sk_test_xxx)
 * - Almeno 1 Connected Account in test mode per test success
 * 
 * @package Tests\Feature\Payment
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0
 * @date 2025-11-17
 */
class MerchantAccountValidationIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected MerchantAccountResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resolver = app(MerchantAccountResolver::class);

        // Skip test se Stripe non configurato
        if (!config('services.stripe.enabled') || empty(config('algorand.payments.stripe.secret_key'))) {
            $this->markTestSkipped('Stripe not configured - skipping integration tests');
        }

        // Verifica che sia in test mode
        $secretKey = config('algorand.payments.stripe.secret_key');
        if (!str_starts_with($secretKey, 'sk_test_')) {
            $this->markTestSkipped('Stripe secret key is not in test mode - skipping for safety');
        }
    }

    /**
     * INTEGRATION TEST 1: Validazione REALE con Stripe Test Account valido
     * 
     * Prerequisiti:
     * - Creare un Connected Account in Stripe Dashboard Test Mode
     * - Completare onboarding (charges_enabled = true)
     * - Usare account_id qui sotto
     */
    public function test_validates_real_stripe_account_successfully()
    {
        $this->markTestIncomplete(
            'Configure a real Stripe Test Connected Account ID in this test to run it'
        );

        // TODO: Inserisci qui un REAL Stripe Test Connected Account ID
        $testStripeAccountId = 'acct_XXXXX'; // ← SOSTITUISCI CON ID REALE

        $user = User::factory()->create([
            'email' => 'test-creator@florenceegi.test',
            'name' => 'Test Creator'
        ]);

        $collection = Collection::factory()->create(['user_id' => $user->id]);
        $egi = Egi::factory()->create(['collection_id' => $collection->id]);

        Wallet::factory()->create([
            'collection_id' => $collection->id,
            'user_id' => $user->id,
            'stripe_account_id' => $testStripeAccountId,
            'royalty_mint' => 100,
            'platform_role' => 'creator'
        ]);

        // Act: Chiamata REALE a Stripe API
        $result = $this->resolver->validateAllCollectionWallets($egi, 'stripe');

        // Assert: Verifica risultato REALE
        $this->assertTrue($result['provider_enabled'], 'Stripe should be enabled');
        $this->assertEquals(1, $result['total_wallets'], 'Should have 1 wallet');
        $this->assertEquals(1, $result['valid_wallets'], 'Wallet should be valid');
        $this->assertTrue($result['all_valid'], 'All wallets should be valid');
        $this->assertTrue($result['can_accept_payments'], 'Should be able to accept payments');
        $this->assertEmpty($result['invalid_wallets'], 'Should have no invalid wallets');
    }

    /**
     * INTEGRATION TEST 2: Account Stripe REALE ma non completato (charges_enabled = false)
     */
    public function test_detects_real_stripe_account_not_ready()
    {
        $this->markTestIncomplete(
            'Configure a real Stripe Test Connected Account ID (NOT completed) to run this test'
        );

        // TODO: Inserisci un account NON completato (charges_enabled = false)
        $incompleteStripeAccountId = 'acct_YYYYY'; // ← Account NON completato

        $user = User::factory()->create();
        $collection = Collection::factory()->create(['user_id' => $user->id]);
        $egi = Egi::factory()->create(['collection_id' => $collection->id]);

        Wallet::factory()->create([
            'collection_id' => $collection->id,
            'user_id' => $user->id,
            'stripe_account_id' => $incompleteStripeAccountId,
            'royalty_mint' => 100,
        ]);

        // Act
        $result = $this->resolver->validateAllCollectionWallets($egi, 'stripe');

        // Assert
        $this->assertEquals(1, $result['total_wallets']);
        $this->assertEquals(0, $result['valid_wallets']); // ← NON valido
        $this->assertFalse($result['can_accept_payments']); // ← BLOCCATO
        $this->assertCount(1, $result['invalid_wallets']);
        $this->assertEquals('charges_disabled', $result['invalid_wallets'][0]['error']);
    }

    /**
     * INTEGRATION TEST 3: Account ID inesistente → Stripe API ritorna errore
     */
    public function test_handles_real_stripe_api_error_for_nonexistent_account()
    {
        $user = User::factory()->create();
        $collection = Collection::factory()->create(['user_id' => $user->id]);
        $egi = Egi::factory()->create(['collection_id' => $collection->id]);

        // Account ID fake che NON esiste in Stripe
        $fakeAccountId = 'acct_nonexistent_fake_12345';

        Wallet::factory()->create([
            'collection_id' => $collection->id,
            'user_id' => $user->id,
            'stripe_account_id' => $fakeAccountId,
            'royalty_mint' => 100,
        ]);

        // Act: Chiamata REALE a Stripe → dovrebbe fallire
        $result = $this->resolver->validateAllCollectionWallets($egi, 'stripe');

        // Assert
        $this->assertEquals(1, $result['total_wallets']);
        $this->assertEquals(0, $result['valid_wallets']);
        $this->assertFalse($result['can_accept_payments']);
        $this->assertCount(1, $result['invalid_wallets']);
        $this->assertEquals('verification_failed', $result['invalid_wallets'][0]['error']);
    }

    /**
     * INTEGRATION TEST 4: Multi-wallet con mix validi/invalidi
     * 
     * Scenario reale: 3 wallet
     * - Wallet 1: Account valido
     * - Wallet 2: Account non completato
     * - Wallet 3: Account inesistente
     * 
     * Expected: can_accept_payments = false (perché non TUTTI sono validi)
     */
    public function test_validates_multi_wallet_scenario_with_mixed_statuses()
    {
        $this->markTestIncomplete(
            'Configure 2 real Stripe Test Connected Accounts (1 valid, 1 incomplete) to run this test'
        );

        $validAccountId = 'acct_XXXXX'; // ← Account VALIDO
        $incompleteAccountId = 'acct_YYYYY'; // ← Account NON completato
        $fakeAccountId = 'acct_fake_nonexistent'; // ← Account fake

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();

        $collection = Collection::factory()->create(['user_id' => $user1->id]);
        $egi = Egi::factory()->create(['collection_id' => $collection->id]);

        // Wallet 1: VALIDO
        Wallet::factory()->create([
            'collection_id' => $collection->id,
            'user_id' => $user1->id,
            'stripe_account_id' => $validAccountId,
            'royalty_mint' => 70,
            'platform_role' => 'creator'
        ]);

        // Wallet 2: NON COMPLETATO
        Wallet::factory()->create([
            'collection_id' => $collection->id,
            'user_id' => $user2->id,
            'stripe_account_id' => $incompleteAccountId,
            'royalty_mint' => 20,
            'platform_role' => 'platform'
        ]);

        // Wallet 3: INESISTENTE
        Wallet::factory()->create([
            'collection_id' => $collection->id,
            'user_id' => $user3->id,
            'stripe_account_id' => $fakeAccountId,
            'royalty_mint' => 10,
            'platform_role' => 'partner'
        ]);

        // Act
        $result = $this->resolver->validateAllCollectionWallets($egi, 'stripe');

        // Assert
        $this->assertEquals(3, $result['total_wallets'], 'Should have 3 wallets');
        $this->assertEquals(1, $result['valid_wallets'], 'Only 1 should be valid');
        $this->assertFalse($result['all_valid'], 'Not all wallets are valid');
        $this->assertFalse($result['can_accept_payments'], 'Should NOT accept payments');
        $this->assertCount(2, $result['invalid_wallets'], 'Should have 2 invalid wallets');
    }
}

