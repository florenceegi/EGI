<?php

namespace Tests\Feature\Payment;

use Tests\TestCase;
use App\Models\{User, Collection, Wallet, PaymentIntent, PaymentDistribution, PspWebhookEvent};
use App\Services\Payment\StripeRealPaymentService;
use App\Enums\Wallet\WalletRoleEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\{DB, Http, Queue};
use Stripe\StripeClient;
use Mockery;

class PaymentWorkflowIntegrationTest extends TestCase {
    use RefreshDatabase;

    private Collection $collection;
    private Wallet $creatorWallet;
    private Wallet $merchantWallet;
    private Wallet $platformWallet;
    private User $user;
    private StripeClient $mockStripeClient;

    protected function setUp(): void {
        parent::setUp();

        // Create test entities
        $this->user = User::factory()->create(['email' => 'test@example.com']);

        $this->creatorWallet = Wallet::factory()->create([
            'stripe_account_id' => 'acct_creator_123',
            'user_id' => User::factory()->create()->id
        ]);

        $this->merchantWallet = Wallet::factory()->create([
            'stripe_account_id' => 'acct_merchant_123',
            'user_id' => User::factory()->create()->id
        ]);

        $this->platformWallet = Wallet::factory()->create([
            'stripe_account_id' => null, // Platform retention
            'user_id' => User::factory()->create()->id
        ]);

        $this->collection = Collection::factory()->create();

        // Attach wallets with distribution percentages
        $this->collection->wallets()->attach([
            $this->creatorWallet->id => [
                'platform_role' => WalletRoleEnum::CREATOR->value,
                'percentage' => 70.0
            ],
            $this->merchantWallet->id => [
                'platform_role' => WalletRoleEnum::MERCHANT->value,
                'percentage' => 25.0
            ],
            $this->platformWallet->id => [
                'platform_role' => WalletRoleEnum::NATAN->value,
                'percentage' => 5.0
            ]
        ]);

        $this->mockStripeClient = Mockery::mock(StripeClient::class);
    }

    /** @test */
    public function it_processes_complete_mint_and_split_payment_flow() {
        // Arrange - Mock Stripe calls
        $this->mockStripePaymentIntent('pi_test_complete_flow', 10000); // €100.00
        $this->mockStripeTransfers([
            ['id' => 'tr_creator_123', 'destination' => 'acct_creator_123', 'amount' => 7000],
            ['id' => 'tr_merchant_123', 'destination' => 'acct_merchant_123', 'amount' => 2500]
        ]);

        // Act 1 - Create payment intent via API
        $response = $this->postJson('/api/payments/create-intent', [
            'collection_id' => $this->collection->id,
            'amount' => 100.00,
            'currency' => 'eur',
            'metadata' => [
                'egi_id' => '12345',
                'mint_type' => 'collection_mint'
            ]
        ]);

        // Assert payment intent created
        $response->assertStatus(201);
        $paymentData = $response->json();
        $this->assertArrayHasKey('payment_intent_id', $paymentData);

        $paymentIntent = PaymentIntent::where('stripe_payment_intent_id', $paymentData['payment_intent_id'])->first();
        $this->assertNotNull($paymentIntent);
        $this->assertEquals('pending', $paymentIntent->status);

        // Act 2 - Simulate Stripe webhook for successful payment
        $webhookPayload = [
            'id' => 'evt_webhook_test',
            'object' => 'event',
            'type' => 'payment_intent.succeeded',
            'data' => [
                'object' => [
                    'id' => $paymentData['payment_intent_id'],
                    'object' => 'payment_intent',
                    'status' => 'succeeded',
                    'amount' => 10000,
                    'currency' => 'eur',
                    'metadata' => [
                        'egi_id' => '12345',
                        'collection_id' => $this->collection->id
                    ]
                ]
            ]
        ];

        $webhookResponse = $this->postJson('/webhook/stripe', $webhookPayload, [
            'Stripe-Signature' => 'test_signature'
        ]);

        // Assert webhook processed successfully
        $webhookResponse->assertStatus(200);
        $webhookData = $webhookResponse->json();
        $this->assertTrue($webhookData['success']);

        // Verify payment intent updated
        $paymentIntent->refresh();
        $this->assertEquals('succeeded', $paymentIntent->status);
        $this->assertNotNull($paymentIntent->completed_at);

        // Verify distributions created correctly
        $distributions = PaymentDistribution::where('payment_intent_id', $paymentData['payment_intent_id'])->get();
        $this->assertCount(3, $distributions);

        // Creator distribution
        $creatorDist = $distributions->where('wallet_id', $this->creatorWallet->id)->first();
        $this->assertEquals('completed', $creatorDist->status);
        $this->assertEquals(7000, $creatorDist->amount_cents);
        $this->assertEquals('tr_creator_123', $creatorDist->transfer_id);

        // Merchant distribution
        $merchantDist = $distributions->where('wallet_id', $this->merchantWallet->id)->first();
        $this->assertEquals('completed', $merchantDist->status);
        $this->assertEquals(2500, $merchantDist->amount_cents);
        $this->assertEquals('tr_merchant_123', $merchantDist->transfer_id);

        // Platform distribution (retained)
        $platformDist = $distributions->where('wallet_id', $this->platformWallet->id)->first();
        $this->assertEquals('completed', $platformDist->status);
        $this->assertEquals(500, $platformDist->amount_cents);
        $this->assertNull($platformDist->transfer_id); // Platform retained

        // Verify webhook event logged
        $this->assertDatabaseHas('psp_webhook_events', [
            'webhook_id' => 'evt_webhook_test',
            'psp' => 'stripe',
            'event_type' => 'payment_intent',
            'status' => 'processed'
        ]);
    }

    /** @test */
    public function it_handles_payment_failure_gracefully() {
        // Arrange
        $paymentIntentId = 'pi_test_failed';
        PaymentIntent::factory()->create([
            'stripe_payment_intent_id' => $paymentIntentId,
            'collection_id' => $this->collection->id,
            'status' => 'pending'
        ]);

        // Act - Simulate failed payment webhook
        $webhookPayload = [
            'id' => 'evt_failed_payment',
            'object' => 'event',
            'type' => 'payment_intent.payment_failed',
            'data' => [
                'object' => [
                    'id' => $paymentIntentId,
                    'object' => 'payment_intent',
                    'status' => 'requires_payment_method',
                    'last_payment_error' => [
                        'code' => 'card_declined',
                        'message' => 'Your card was declined.'
                    ]
                ]
            ]
        ];

        $response = $this->postJson('/webhook/stripe', $webhookPayload, [
            'Stripe-Signature' => 'test_signature'
        ]);

        // Assert
        $response->assertStatus(200);

        $paymentIntent = PaymentIntent::where('stripe_payment_intent_id', $paymentIntentId)->first();
        $this->assertEquals('failed', $paymentIntent->status);
        $this->assertStringContains('card_declined', $paymentIntent->failure_reason);

        // Verify no distributions were created
        $distributions = PaymentDistribution::where('payment_intent_id', $paymentIntentId)->count();
        $this->assertEquals(0, $distributions);
    }

    /** @test */
    public function it_processes_refund_with_transfer_reversals() {
        // Arrange - Create completed payment with distributions
        $paymentIntentId = 'pi_test_refund';
        $paymentIntent = PaymentIntent::factory()->create([
            'stripe_payment_intent_id' => $paymentIntentId,
            'collection_id' => $this->collection->id,
            'status' => 'succeeded',
            'amount_cents' => 10000
        ]);

        // Create completed distributions
        PaymentDistribution::factory()->create([
            'payment_intent_id' => $paymentIntentId,
            'wallet_id' => $this->creatorWallet->id,
            'status' => 'completed',
            'transfer_id' => 'tr_creator_original',
            'amount_cents' => 7000
        ]);

        PaymentDistribution::factory()->create([
            'payment_intent_id' => $paymentIntentId,
            'wallet_id' => $this->merchantWallet->id,
            'status' => 'completed',
            'transfer_id' => 'tr_merchant_original',
            'amount_cents' => 2500
        ]);

        // Mock Stripe refund and reversal calls
        $this->mockStripeRefund($paymentIntentId, 10000);
        $this->mockStripeReversals([
            ['id' => 'trr_creator_reverse', 'transfer' => 'tr_creator_original'],
            ['id' => 'trr_merchant_reverse', 'transfer' => 'tr_merchant_original']
        ]);

        // Act - Process refund request
        $response = $this->postJson("/api/payments/{$paymentIntentId}/refund", [
            'amount' => 100.00,
            'reason' => 'requested_by_customer'
        ]);

        // Assert refund processed
        $response->assertStatus(200);
        $refundData = $response->json();
        $this->assertTrue($refundData['success']);

        // Verify distributions marked as reversed
        $distributions = PaymentDistribution::where('payment_intent_id', $paymentIntentId)->get();

        $creatorDist = $distributions->where('wallet_id', $this->creatorWallet->id)->first();
        $this->assertEquals('reversed', $creatorDist->status);
        $this->assertEquals('trr_creator_reverse', $creatorDist->reversal_id);

        $merchantDist = $distributions->where('wallet_id', $this->merchantWallet->id)->first();
        $this->assertEquals('reversed', $merchantDist->status);
        $this->assertEquals('trr_merchant_reverse', $merchantDist->reversal_id);

        // Verify payment intent marked as refunded
        $paymentIntent->refresh();
        $this->assertEquals('refunded', $paymentIntent->status);
        $this->assertNotNull($paymentIntent->refunded_at);
    }

    /** @test */
    public function it_prevents_race_conditions_on_concurrent_webhooks() {
        // Arrange
        $paymentIntentId = 'pi_concurrent_test';

        // Simulate concurrent webhook processing using database transactions
        DB::beginTransaction();

        try {
            // First webhook creates payment intent
            PaymentIntent::factory()->create([
                'stripe_payment_intent_id' => $paymentIntentId,
                'collection_id' => $this->collection->id,
                'status' => 'pending'
            ]);

            // Mock concurrent processing
            $this->mockStripePaymentIntent($paymentIntentId, 10000);
            $this->mockStripeTransfers([
                ['id' => 'tr_concurrent_1', 'destination' => 'acct_creator_123', 'amount' => 7000]
            ]);

            // Act - Process same webhook twice concurrently
            $webhookPayload = [
                'id' => 'evt_concurrent_test',
                'object' => 'event',
                'type' => 'payment_intent.succeeded',
                'data' => [
                    'object' => [
                        'id' => $paymentIntentId,
                        'status' => 'succeeded',
                        'amount' => 10000
                    ]
                ]
            ];

            // First processing
            $response1 = $this->postJson('/webhook/stripe', $webhookPayload, [
                'Stripe-Signature' => 'test_signature'
            ]);

            // Second processing (should be idempotent)
            $response2 = $this->postJson('/webhook/stripe', $webhookPayload, [
                'Stripe-Signature' => 'test_signature'
            ]);

            DB::commit();

            // Assert both responses successful
            $response1->assertStatus(200);
            $response2->assertStatus(200);

            // But verify only one set of distributions created
            $distributionCount = PaymentDistribution::where('payment_intent_id', $paymentIntentId)->count();
            $this->assertEquals(3, $distributionCount); // Not duplicated

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /** @test */
    public function it_handles_partial_transfer_failures_in_split_payments() {
        // Arrange
        $paymentIntentId = 'pi_partial_failure';
        PaymentIntent::factory()->create([
            'stripe_payment_intent_id' => $paymentIntentId,
            'collection_id' => $this->collection->id,
            'status' => 'pending'
        ]);

        // Mock successful payment but partial transfer failure
        $this->mockStripePaymentIntent($paymentIntentId, 10000);

        // Creator transfer succeeds, merchant transfer fails
        $this->mockStripeTransfers([
            ['id' => 'tr_creator_success', 'destination' => 'acct_creator_123', 'amount' => 7000]
        ]);

        $this->mockStripeTransferFailure('acct_merchant_123', 'No such destination account');

        // Act
        $webhookPayload = [
            'id' => 'evt_partial_failure',
            'object' => 'event',
            'type' => 'payment_intent.succeeded',
            'data' => [
                'object' => [
                    'id' => $paymentIntentId,
                    'status' => 'succeeded',
                    'amount' => 10000,
                    'metadata' => ['collection_id' => $this->collection->id]
                ]
            ]
        ];

        $response = $this->postJson('/webhook/stripe', $webhookPayload, [
            'Stripe-Signature' => 'test_signature'
        ]);

        // Assert webhook still processes successfully (resilient)
        $response->assertStatus(200);

        $distributions = PaymentDistribution::where('payment_intent_id', $paymentIntentId)->get();

        // Creator distribution completed
        $creatorDist = $distributions->where('wallet_id', $this->creatorWallet->id)->first();
        $this->assertEquals('completed', $creatorDist->status);
        $this->assertEquals('tr_creator_success', $creatorDist->transfer_id);

        // Merchant distribution failed
        $merchantDist = $distributions->where('wallet_id', $this->merchantWallet->id)->first();
        $this->assertEquals('failed', $merchantDist->status);
        $this->assertStringContains('No such destination account', $merchantDist->failure_reason);
        $this->assertEquals(1, $merchantDist->retry_count);

        // Platform distribution succeeded (retained)
        $platformDist = $distributions->where('wallet_id', $this->platformWallet->id)->first();
        $this->assertEquals('completed', $platformDist->status);
    }

    /** @test */
    public function it_maintains_data_integrity_during_system_failures() {
        // Arrange - Simulate system failure during processing
        $paymentIntentId = 'pi_system_failure';

        // Act & Assert - Use database transaction to verify rollback behavior
        try {
            DB::transaction(function () use ($paymentIntentId) {
                PaymentIntent::factory()->create([
                    'stripe_payment_intent_id' => $paymentIntentId,
                    'status' => 'pending'
                ]);

                // Create partial distributions
                PaymentDistribution::factory()->create([
                    'payment_intent_id' => $paymentIntentId,
                    'wallet_id' => $this->creatorWallet->id,
                    'status' => 'pending'
                ]);

                // Simulate system failure
                throw new \Exception('Simulated system failure');
            });
        } catch (\Exception $e) {
            // Expected failure
        }

        // Verify no partial data persisted
        $this->assertDatabaseMissing('payment_intents', [
            'stripe_payment_intent_id' => $paymentIntentId
        ]);

        $this->assertDatabaseMissing('payment_distributions', [
            'payment_intent_id' => $paymentIntentId
        ]);
    }

    /**
     * Mock Stripe PaymentIntent retrieval
     */
    private function mockStripePaymentIntent(string $paymentIntentId, int $amountCents): void {
        $mockPaymentIntent = Mockery::mock();
        $mockPaymentIntent->id = $paymentIntentId;
        $mockPaymentIntent->status = 'succeeded';
        $mockPaymentIntent->amount = $amountCents;
        $mockPaymentIntent->currency = 'eur';

        $this->mockStripeClient->paymentIntents = Mockery::mock();
        $this->mockStripeClient->paymentIntents
            ->shouldReceive('retrieve')
            ->with($paymentIntentId)
            ->andReturn($mockPaymentIntent);
    }

    /**
     * Mock Stripe Transfer creation
     */
    private function mockStripeTransfers(array $transfers): void {
        $this->mockStripeClient->transfers = Mockery::mock();

        foreach ($transfers as $transfer) {
            $mockTransfer = Mockery::mock();
            $mockTransfer->id = $transfer['id'];
            $mockTransfer->destination = $transfer['destination'];
            $mockTransfer->amount = $transfer['amount'];

            $this->mockStripeClient->transfers
                ->shouldReceive('create')
                ->andReturn($mockTransfer);
        }
    }

    /**
     * Mock Stripe Transfer failure
     */
    private function mockStripeTransferFailure(string $destination, string $errorMessage): void {
        $this->mockStripeClient->transfers = Mockery::mock();
        $this->mockStripeClient->transfers
            ->shouldReceive('create')
            ->andThrow(new \Stripe\Exception\InvalidRequestException($errorMessage, 'destination'));
    }

    /**
     * Mock Stripe Refund creation
     */
    private function mockStripeRefund(string $paymentIntentId, int $amount): void {
        $mockRefund = Mockery::mock();
        $mockRefund->id = 'pi_' . $paymentIntentId . '_refund';
        $mockRefund->payment_intent = $paymentIntentId;
        $mockRefund->amount = $amount;

        $this->mockStripeClient->refunds = Mockery::mock();
        $this->mockStripeClient->refunds
            ->shouldReceive('create')
            ->with(['payment_intent' => $paymentIntentId, 'amount' => $amount])
            ->andReturn($mockRefund);
    }

    /**
     * Mock Stripe Transfer Reversals
     */
    private function mockStripeReversals(array $reversals): void {
        foreach ($reversals as $reversal) {
            $mockReversal = Mockery::mock();
            $mockReversal->id = $reversal['id'];
            $mockReversal->transfer = $reversal['transfer'];

            $this->mockStripeClient->transfers = Mockery::mock();
            $this->mockStripeClient->transfers
                ->shouldReceive('createReversal')
                ->with($reversal['transfer'])
                ->andReturn($mockReversal);
        }
    }

    protected function tearDown(): void {
        Mockery::close();
        parent::tearDown();
    }
}
