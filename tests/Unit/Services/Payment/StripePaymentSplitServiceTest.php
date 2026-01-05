<?php

namespace Tests\Unit\Services\Payment;

use Tests\TestCase;
use App\Services\Payment\StripePaymentSplitService;
use App\Models\{Collection, Wallet, PaymentDistribution};
use App\Enums\Wallet\WalletRoleEnum;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Services\Gdpr\{AuditLogService, ConsentService};
use Stripe\StripeClient;
use Stripe\Transfer;
use Mockery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection as LaravelCollection;
use Illuminate\Database\QueryException;

class StripePaymentSplitServiceTest extends TestCase {
    use RefreshDatabase;

    private StripePaymentSplitService $service;
    private StripeClient $mockStripeClient;
    private UltraLogManager $mockLogger;
    private ErrorManagerInterface $mockErrorManager;

    protected function setUp(): void {
        parent::setUp();

        $this->mockLogger = Mockery::mock(UltraLogManager::class);
        $this->mockErrorManager = Mockery::mock(ErrorManagerInterface::class);
        $mockAuditService = Mockery::mock(AuditLogService::class);
        $mockConsentService = Mockery::mock(ConsentService::class);
        $mockDistributionService = Mockery::mock(\App\Services\PaymentDistributionService::class);

        $this->mockStripeClient = Mockery::mock(StripeClient::class);

        $this->service = new StripePaymentSplitService(
            $this->mockLogger,
            $this->mockErrorManager,
            $mockAuditService,
            $mockConsentService,
            $mockDistributionService
        );

        // Inject mocked Stripe client
        $reflection = new \ReflectionClass($this->service);
        $clientProperty = $reflection->getProperty('stripeClient');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($this->service, $this->mockStripeClient);
    }

    /** @test */
    public function it_handles_platform_retention_correctly() {
        // Arrange
        $paymentIntentId = 'pi_test123';
        $distributions = [
            [
                'wallet_id' => 1,
                'platform_role' => WalletRoleEnum::NATAN->value,
                'amount_cents' => 500, // €5.00 platform fee
                'amount_eur' => 5.00,
                'percentage' => 5.0,
                'user_type' => 'PLATFORM'
            ]
        ];

        $this->mockLogger->shouldReceive('info')->atLeast(1);

        // Act
        $result = $this->invokePrivateMethod('executeAtomicTransfers', [
            $paymentIntentId,
            $distributions,
            ['test' => 'metadata']
        ]);

        // Assert
        $this->assertTrue($result['success']);
        $this->assertCount(1, $result['transfers']);
        $this->assertEquals('platform_retained', $result['transfers'][0]['destination']);
        $this->assertNull($result['transfers'][0]['transfer_id']);
        $this->assertEquals('succeeded', $result['transfers'][0]['status']);
    }

    /** @test */
    public function it_creates_pending_distribution_before_stripe_call() {
        // Arrange
        $paymentIntentId = 'pi_test123';
        $distribution = [
            'wallet_id' => 1,
            'platform_role' => 'CREATOR',
            'amount_cents' => 7000,
            'amount_eur' => 70.00,
            'percentage' => 70.0,
            'stripe_account_id' => 'acct_test123',
            'user_type' => 'CREATOR'
        ];

        // Mock successful transfer creation
        $mockTransfer = Mockery::mock(Transfer::class);
        $mockTransfer->id = 'tr_test123';
        $mockTransfer->destination = 'acct_test123';
        $mockTransfer->amount = 7000;

        $this->mockStripeClient->transfers = Mockery::mock();
        $this->mockStripeClient->transfers
            ->shouldReceive('create')
            ->once()
            ->andReturn($mockTransfer);

        $this->mockLogger->shouldReceive('info')->atLeast(1);

        // Act
        $result = $this->invokePrivateMethod('executeAtomicTransfers', [
            $paymentIntentId,
            [$distribution],
            []
        ]);

        // Assert
        $this->assertTrue($result['success']);

        // Verify distribution was created with proper states
        $distributionRecord = PaymentDistribution::where('payment_intent_id', $paymentIntentId)->first();
        $this->assertNotNull($distributionRecord);
        $this->assertEquals('completed', $distributionRecord->status);
        $this->assertEquals('tr_test123', $distributionRecord->transfer_id);
        $this->assertNotNull($distributionRecord->completed_at);
    }

    /** @test */
    public function it_handles_race_condition_on_unique_constraint() {
        // Arrange
        $paymentIntentId = 'pi_test123';
        $walletId = 1;

        // Create existing record to simulate race condition
        $existingDistribution = PaymentDistribution::factory()->create([
            'payment_intent_id' => $paymentIntentId,
            'wallet_id' => $walletId,
            'status' => 'completed',
            'transfer_id' => 'tr_existing'
        ]);

        $distribution = [
            'wallet_id' => $walletId,
            'platform_role' => 'CREATOR',
            'amount_cents' => 7000,
            'amount_eur' => 70.00,
            'percentage' => 70.0,
            'stripe_account_id' => 'acct_test123',
            'user_type' => 'CREATOR'
        ];

        $this->mockLogger->shouldReceive('info')->atLeast(1);

        // Act
        $result = $this->invokePrivateMethod('executeAtomicTransfers', [
            $paymentIntentId,
            [$distribution],
            []
        ]);

        // Assert - should return existing record without new Stripe call
        $this->assertTrue($result['success']);
        $this->assertEquals('tr_existing', $result['transfers'][0]['transfer_id']);

        // Verify no additional Stripe calls were made
        $this->mockStripeClient->shouldNotHaveReceived('transfers');
    }

    /** @test */
    public function it_protects_terminal_states_from_degradation() {
        // Arrange
        $paymentIntentId = 'pi_test123';
        $walletId = 1;

        // Create distribution in terminal state
        $terminalDistribution = PaymentDistribution::factory()->create([
            'payment_intent_id' => $paymentIntentId,
            'wallet_id' => $walletId,
            'status' => 'reversed', // Terminal state
            'transfer_id' => 'tr_original',
            'reversal_id' => 'trr_original'
        ]);

        $distribution = [
            'wallet_id' => $walletId,
            'platform_role' => 'CREATOR',
            'amount_cents' => 7000,
            'amount_eur' => 70.00,
            'percentage' => 70.0,
            'stripe_account_id' => 'acct_test123'
        ];

        // Act
        $result = $this->invokePrivateMethod('createPendingDistribution', [$paymentIntentId, $distribution]);

        // Assert - should return unchanged terminal record
        $this->assertEquals('reversed', $result->status);
        $this->assertEquals('tr_original', $result->transfer_id);
        $this->assertEquals('trr_original', $result->reversal_id);
    }

    /** @test */
    public function it_handles_stripe_transfer_failures_gracefully() {
        // Arrange
        $paymentIntentId = 'pi_test123';
        $distribution = [
            'wallet_id' => 1,
            'platform_role' => 'CREATOR',
            'amount_cents' => 7000,
            'amount_eur' => 70.00,
            'percentage' => 70.0,
            'stripe_account_id' => 'acct_test123',
            'user_type' => 'CREATOR'
        ];

        // Mock Stripe failure
        $this->mockStripeClient->transfers = Mockery::mock();
        $this->mockStripeClient->transfers
            ->shouldReceive('create')
            ->once()
            ->andThrow(new \Stripe\Exception\InvalidRequestException(
                'No such destination: acct_invalid',
                'destination'
            ));

        $this->mockLogger->shouldReceive('error')->once();

        // Act
        $result = $this->invokePrivateMethod('executeAtomicTransfers', [
            $paymentIntentId,
            [$distribution],
            []
        ]);

        // Assert
        $this->assertTrue($result['success']); // Continues despite individual failure
        $this->assertEquals('failed', $result['transfers'][0]['status']);

        // Verify distribution marked as failed
        $distributionRecord = PaymentDistribution::where('payment_intent_id', $paymentIntentId)->first();
        $this->assertEquals('failed', $distributionRecord->status);
        $this->assertStringContains('No such destination', $distributionRecord->failure_reason);
        $this->assertEquals(1, $distributionRecord->retry_count);
    }

    /** @test */
    public function it_uses_deterministic_idempotency_keys() {
        // Arrange
        $paymentIntentId = 'pi_test123';
        $walletId = 1;
        $distribution = [
            'wallet_id' => $walletId,
            'platform_role' => 'CREATOR',
            'amount_cents' => 7000,
            'amount_eur' => 70.00
        ];

        // Act
        $idempotencyKey = $this->invokePrivateMethod('generateIdempotencyKey', [$paymentIntentId, $walletId]);

        // Assert
        $this->assertEquals("pi_{$paymentIntentId}_w_{$walletId}_v2", $idempotencyKey);

        // Test deterministic - same inputs = same key
        $idempotencyKey2 = $this->invokePrivateMethod('generateIdempotencyKey', [$paymentIntentId, $walletId]);
        $this->assertEquals($idempotencyKey, $idempotencyKey2);
    }

    /** @test */
    public function it_validates_stripe_accounts_before_transfer() {
        // Arrange
        $distributions = [
            [
                'wallet_id' => 1,
                'platform_role' => 'CREATOR',
                'stripe_account_id' => null // Missing account
            ],
            [
                'wallet_id' => 2,
                'platform_role' => 'MERCHANT',
                'stripe_account_id' => 'acct_valid123'
            ]
        ];

        // Mock Stripe account validation
        $mockAccount = Mockery::mock();
        $mockAccount->payouts_enabled = true;
        $mockAccount->charges_enabled = true;

        $this->mockStripeClient->accounts = Mockery::mock();
        $this->mockStripeClient->accounts
            ->shouldReceive('retrieve')
            ->with('acct_valid123')
            ->once()
            ->andReturn($mockAccount);

        // Act & Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Missing Stripe account for wallet 1');

        $this->invokePrivateMethod('validateStripeAccounts', [$distributions]);
    }

    /** @test */
    public function it_calculates_distributions_accurately() {
        // Arrange
        $collection = Collection::factory()->create();

        $creator = Wallet::factory()->create(['stripe_account_id' => 'acct_creator']);
        $merchant = Wallet::factory()->create(['stripe_account_id' => 'acct_merchant']);
        $platform = Wallet::factory()->create(['stripe_account_id' => null]);

        $collection->wallets()->attach([
            $creator->id => ['platform_role' => 'CREATOR', 'percentage' => 70.0],
            $merchant->id => ['platform_role' => 'MERCHANT', 'percentage' => 25.0],
            $platform->id => ['platform_role' => 'NATAN', 'percentage' => 5.0]
        ]);

        $wallets = $collection->wallets;
        $totalAmount = 100.0;

        // Act
        $distributions = $this->invokePrivateMethod('calculateDistributions', [
            $wallets,
            $totalAmount,
            123, // egi_id
            ['test' => 'metadata']
        ]);

        // Assert
        $this->assertCount(3, $distributions);

        $creatorDist = collect($distributions)->firstWhere('platform_role', 'CREATOR');
        $this->assertEquals(70.0, $creatorDist['amount_eur']);
        $this->assertEquals(7000, $creatorDist['amount_cents']);

        $merchantDist = collect($distributions)->firstWhere('platform_role', 'MERCHANT');
        $this->assertEquals(25.0, $merchantDist['amount_eur']);

        $platformDist = collect($distributions)->firstWhere('platform_role', 'NATAN');
        $this->assertEquals(5.0, $platformDist['amount_eur']);
    }

    /**
     * Helper method to invoke private methods for testing
     */
    private function invokePrivateMethod(string $methodName, array $parameters = []) {
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($this->service, $parameters);
    }

    protected function tearDown(): void {
        Mockery::close();
        parent::tearDown();
    }
}
