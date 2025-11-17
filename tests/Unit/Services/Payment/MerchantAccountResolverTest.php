<?php

namespace Tests\Unit\Services\Payment;

use App\Models\Collection;
use App\Models\Egi;
use App\Models\User;
use App\Models\Wallet;
use App\Services\Payment\MerchantAccountResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Mockery;
use Tests\TestCase;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * @Oracode Test: MerchantAccountResolver Unit Tests
 * 🎯 Purpose: Verificare validazione PSP multi-wallet per split payment
 * 🛡️ Coverage: Tutti gli scenari possibili (success, failures, edge cases)
 * 
 * @package Tests\Unit\Services\Payment
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0
 * @date 2025-11-17
 */
class MerchantAccountResolverTest extends TestCase
{
    use RefreshDatabase;

    protected MerchantAccountResolver $resolver;
    protected $mockLogger;
    protected $mockErrorManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockLogger = Mockery::mock(UltraLogManager::class);
        $this->mockLogger->shouldReceive('info')->andReturnNull();
        $this->mockLogger->shouldReceive('warning')->andReturnNull();
        $this->mockLogger->shouldReceive('debug')->andReturnNull();
        $this->mockLogger->shouldReceive('error')->andReturnNull();

        $this->mockErrorManager = Mockery::mock(ErrorManagerInterface::class);

        $this->resolver = new MerchantAccountResolver(
            $this->mockLogger,
            $this->mockErrorManager
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * TEST 1: Provider disabilitato in .env → can_accept_payments = false
     */
    public function test_returns_false_when_stripe_disabled_in_config()
    {
        // Setup: Stripe disabilitato
        Config::set('algorand.payments.stripe_enabled', false);

        $collection = Collection::factory()->create();
        $egi = Egi::factory()->create(['collection_id' => $collection->id]);

        // Act
        $result = $this->resolver->validateAllCollectionWallets($egi, 'stripe');

        // Assert
        $this->assertFalse($result['provider_enabled']);
        $this->assertFalse($result['can_accept_payments']);
        $this->assertContains('provider_disabled', $result['errors']);
    }

    /**
     * TEST 2: Stripe abilitato + 1 wallet valido → can_accept_payments = true
     * 
     * ⚡ INTEGRATION TEST: Usa account Stripe REALE esistente
     */
    public function test_returns_true_when_stripe_enabled_and_one_valid_wallet()
    {
        // Setup: Stripe abilitato
        Config::set('algorand.payments.stripe_enabled', true);
        // Secret key già configurato in .env

        $user = User::factory()->create();
        $collection = Collection::factory()->create(['creator_id' => $user->id]);
        $egi = Egi::factory()->create(['collection_id' => $collection->id]);

        // ✅ USA ACCOUNT STRIPE REALE ESISTENTE (User ID 16)
        Wallet::factory()->create([
            'collection_id' => $collection->id,
            'user_id' => $user->id,
            'stripe_account_id' => 'acct_1STURZ16pYPa4Mwq', // Account REALE User 16
            'royalty_mint' => 100,
            'platform_role' => 'creator'
        ]);

        // Act: Chiama Stripe API REALE
        $result = $this->resolver->validateAllCollectionWallets($egi, 'stripe');

        // Assert
        $this->assertTrue($result['provider_enabled']);
        $this->assertEquals(1, $result['total_wallets']);
        $this->assertEquals(1, $result['valid_wallets']);
        $this->assertTrue($result['all_valid']);
        $this->assertTrue($result['can_accept_payments']);
        $this->assertEmpty($result['invalid_wallets']);
    }

    /**
     * TEST 3: Stripe abilitato + 3 wallet tutti validi → can_accept_payments = true
     * 
     * ⚡ INTEGRATION TEST: Usa stesso account REALE 3 volte (simula split payment)
     * In produzione saranno account diversi, ma qui testiamo la LOGICA
     */
    public function test_returns_true_when_all_three_wallets_valid()
    {
        // Setup
        Config::set('algorand.payments.stripe_enabled', true);
        // Secret key già configurato in .env

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();
        
        $collection = Collection::factory()->create(['creator_id' => $user1->id]);
        $egi = Egi::factory()->create(['collection_id' => $collection->id]);

        // ✅ 3 wallet che usano STESSO account Stripe REALE
        // (In produzione saranno account diversi, ma logica è la stessa)
        Wallet::factory()->create([
            'collection_id' => $collection->id,
            'user_id' => $user1->id,
            'stripe_account_id' => 'acct_1STURZ16pYPa4Mwq', // Account REALE User 16
            'royalty_mint' => 70,
            'platform_role' => 'creator'
        ]);

        Wallet::factory()->create([
            'collection_id' => $collection->id,
            'user_id' => $user2->id,
            'stripe_account_id' => 'acct_1STURZ16pYPa4Mwq', // Stesso account (simula multi-wallet)
            'royalty_mint' => 20,
            'platform_role' => 'platform'
        ]);

        Wallet::factory()->create([
            'collection_id' => $collection->id,
            'user_id' => $user3->id,
            'stripe_account_id' => 'acct_1STURZ16pYPa4Mwq', // Stesso account
            'royalty_mint' => 10,
            'platform_role' => 'partner'
        ]);

        // Act: Chiama Stripe API REALE per TUTTI e 3 i wallet
        $result = $this->resolver->validateAllCollectionWallets($egi, 'stripe');

        // Assert: TUTTI e 3 devono essere validi
        $this->assertTrue($result['provider_enabled']);
        $this->assertEquals(3, $result['total_wallets']);
        $this->assertEquals(3, $result['valid_wallets']);
        $this->assertTrue($result['all_valid']);
        $this->assertTrue($result['can_accept_payments']);
        $this->assertEmpty($result['invalid_wallets']);
    }

    /**
     * TEST 4: 3 wallet ma UNO charges_disabled → can_accept_payments = false
     * 
     * ⚡ INTEGRATION TEST: 2 account REALI + 1 inesistente (simula disabled)
     * Account inesistente = Stripe ritorna error = marcato come invalid
     */
    public function test_returns_false_when_one_of_three_wallets_charges_disabled()
    {
        // Setup
        Config::set('algorand.payments.stripe_enabled', true);
        // Secret key già configurato in .env

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();
        
        $collection = Collection::factory()->create(['creator_id' => $user1->id]);
        $egi = Egi::factory()->create(['collection_id' => $collection->id]);

        // ✅ 2 wallet con account REALE valido
        $wallet1 = Wallet::factory()->create([
            'collection_id' => $collection->id,
            'user_id' => $user1->id,
            'stripe_account_id' => 'acct_1STURZ16pYPa4Mwq', // REALE User 16
            'royalty_mint' => 70,
        ]);

        $wallet2 = Wallet::factory()->create([
            'collection_id' => $collection->id,
            'user_id' => $user2->id,
            'stripe_account_id' => 'acct_1STURZ16pYPa4Mwq', // REALE User 16
            'royalty_mint' => 20,
        ]);

        // ❌ 1 wallet con account INESISTENTE (Stripe ritorna error = invalid)
        $wallet3 = Wallet::factory()->create([
            'collection_id' => $collection->id,
            'user_id' => $user3->id,
            'stripe_account_id' => 'acct_fake_disabled_999', // INESISTENTE
            'royalty_mint' => 10,
        ]);

        // Act: Stripe API REALE fallirà su acct_fake_disabled_999
        $result = $this->resolver->validateAllCollectionWallets($egi, 'stripe');

        // Assert: 1 wallet invalido BLOCCA tutto
        $this->assertTrue($result['provider_enabled']);
        $this->assertEquals(3, $result['total_wallets']);
        $this->assertEquals(2, $result['valid_wallets']); // Solo 2 validi
        $this->assertFalse($result['all_valid']); // NON tutti validi
        $this->assertFalse($result['can_accept_payments']); // ❌ BLOCCATO
        $this->assertCount(1, $result['invalid_wallets']); // 1 wallet invalido
        $this->assertEquals($wallet3->id, $result['invalid_wallets'][0]['wallet_id']);
        // Error sarà "verification_failed" o "account_not_found" (dipende da Stripe response)
        $this->assertNotEmpty($result['invalid_wallets'][0]['error']);
    }

    /**
     * TEST 5: Nessun wallet nella collection → can_accept_payments = false
     */
    public function test_returns_false_when_no_wallets_in_collection()
    {
        // Setup
        Config::set('algorand.payments.stripe_enabled', true);

        $collection = Collection::factory()->create();
        $egi = Egi::factory()->create(['collection_id' => $collection->id]);
        
        // Nessun wallet creato

        // Act
        $result = $this->resolver->validateAllCollectionWallets($egi, 'stripe');

        // Assert
        $this->assertTrue($result['provider_enabled']);
        $this->assertEquals(0, $result['total_wallets']);
        $this->assertFalse($result['can_accept_payments']);
        $this->assertContains('no_wallets_configured', $result['errors']);
    }

    /**
     * TEST 6: Wallet senza stripe_account_id → can_accept_payments = false
     */
    public function test_returns_false_when_wallet_has_no_stripe_account_id()
    {
        // Setup
        Config::set('algorand.payments.stripe_enabled', true);

        $user = User::factory()->create();
        $collection = Collection::factory()->create(['creator_id' => $user->id]);
        $egi = Egi::factory()->create(['collection_id' => $collection->id]);

        // Wallet SENZA stripe_account_id
        Wallet::factory()->create([
            'collection_id' => $collection->id,
            'user_id' => $user->id,
            'stripe_account_id' => null, // ❌ MANCANTE
            'royalty_mint' => 100,
        ]);

        // Act
        $result = $this->resolver->validateAllCollectionWallets($egi, 'stripe');

        // Assert
        $this->assertEquals(0, $result['total_wallets']); // Non conta wallet senza stripe_account_id
        $this->assertFalse($result['can_accept_payments']);
    }

    /**
     * TEST 7: Collection non trovata → can_accept_payments = false
     */
    public function test_returns_false_when_collection_not_found()
    {
        // Setup: EGI senza collection
        $egi = new Egi(['id' => 999, 'collection_id' => null]);

        // Act
        $result = $this->resolver->validateAllCollectionWallets($egi, 'stripe');

        // Assert
        $this->assertFalse($result['can_accept_payments']);
        $this->assertContains('collection_not_found', $result['errors']);
    }

    /**
     * TEST 8: Stripe API call fallisce → wallet marcato come invalid
     * 
     * ⚡ INTEGRATION TEST: Account inesistente simula API error
     */
    public function test_marks_wallet_invalid_when_stripe_api_fails()
    {
        // Setup
        Config::set('algorand.payments.stripe_enabled', true);
        // Secret key già configurato in .env

        $user = User::factory()->create();
        $collection = Collection::factory()->create(['creator_id' => $user->id]);
        $egi = Egi::factory()->create(['collection_id' => $collection->id]);

        // ❌ Account INESISTENTE (Stripe API ritorna error)
        $wallet = Wallet::factory()->create([
            'collection_id' => $collection->id,
            'user_id' => $user->id,
            'stripe_account_id' => 'acct_nonexistent_error_999', // INESISTENTE
            'royalty_mint' => 100,
        ]);

        // Act: Stripe API REALE fallisce su account inesistente
        $result = $this->resolver->validateAllCollectionWallets($egi, 'stripe');

        // Assert: Wallet marcato come invalid
        $this->assertEquals(1, $result['total_wallets']);
        $this->assertEquals(0, $result['valid_wallets']);
        $this->assertFalse($result['can_accept_payments']);
        $this->assertCount(1, $result['invalid_wallets']);
        // Error sarà "verification_failed" o simile (gestito da MerchantAccountResolver)
        $this->assertNotEmpty($result['invalid_wallets'][0]['error']);
    }

    /**
     * TEST 9: PayPal provider → ritorna paypal_not_implemented
     */
    public function test_paypal_returns_not_implemented()
    {
        // Setup
        Config::set('payment.paypal.enabled', true);

        $user = User::factory()->create();
        $collection = Collection::factory()->create(['creator_id' => $user->id]);
        $egi = Egi::factory()->create(['collection_id' => $collection->id]);

        Wallet::factory()->create([
            'collection_id' => $collection->id,
            'user_id' => $user->id,
            'paypal_merchant_id' => 'paypal_merchant_123',
            'royalty_mint' => 100,
        ]);

        // Act
        $result = $this->resolver->validateAllCollectionWallets($egi, 'paypal');

        // Assert
        $this->assertTrue($result['provider_enabled']);
        $this->assertEquals(1, $result['total_wallets']);
        $this->assertEquals(0, $result['valid_wallets']); // PayPal non implementato
        $this->assertFalse($result['can_accept_payments']);
        $this->assertEquals('paypal_not_implemented', $result['invalid_wallets'][0]['error']);
    }

    // =====================================================
    // NOTA: Questi test usano Stripe API REALE in Test Mode
    // Nessun mock necessario - hai già tutto configurato!
    // =====================================================
}

