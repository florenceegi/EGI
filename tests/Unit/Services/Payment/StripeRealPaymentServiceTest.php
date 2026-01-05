<?php

namespace Tests\Unit\Services\Payment;

use Tests\TestCase;
use App\Services\Payment\StripeRealPaymentService;
use App\Services\Payment\StripePaymentSplitService;
use App\DataTransferObjects\Payment\PaymentRequest;
use App\Services\Gdpr\{AuditLogService, ConsentService};
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Stripe\StripeClient;
use Stripe\PaymentIntent;
use Stripe\Transfer;
use Mockery;
use App\Models\PaymentDistribution;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StripeRealPaymentServiceTest extends TestCase {
    use RefreshDatabase;

    private StripeRealPaymentService $service;
    private StripeClient $mockStripeClient;
    private UltraLogManager $mockLogger;
    private ErrorManagerInterface $mockErrorManager;
    private AuditLogService $mockAuditService;
    private ConsentService $mockConsentService;
    private StripePaymentSplitService $mockSplitService;

    protected function setUp(): void {
        parent::setUp();

        $this->mockLogger = Mockery::mock(UltraLogManager::class);
        $this->mockErrorManager = Mockery::mock(ErrorManagerInterface::class);
        $this->mockAuditService = Mockery::mock(AuditLogService::class);
        $this->mockConsentService = Mockery::mock(ConsentService::class);
        $this->mockSplitService = Mockery::mock(StripePaymentSplitService::class);
        $this->mockStripeClient = Mockery::mock(StripeClient::class);

        $this->service = new StripeRealPaymentService(
            $this->mockLogger,
            $this->mockErrorManager,
            $this->mockAuditService,
            $this->mockConsentService,
            $this->mockSplitService
        );

        // Inject mocked Stripe client via reflection
        $reflection = new \ReflectionClass($this->service);
        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($this->service, $this->mockStripeClient);
    }

    /** @test */
    public function it_creates_single_payment_intent_successfully() {
        // Arrange
        $request = new PaymentRequest(
            amount: 100.00,
            currency: 'EUR',
            egiId: 123,
            userId: 1,
            customerEmail: 'test@example.com'
        );

        $mockPaymentIntent = Mockery::mock(PaymentIntent::class);
        $mockPaymentIntent->id = 'pi_test123';
        $mockPaymentIntent->status = 'succeeded';
        $mockPaymentIntent->client_secret = 'pi_test123_secret';

        $mockCharge = Mockery::mock();
        $mockCharge->receipt_url = 'https://pay.stripe.com/receipts/test';
        $mockPaymentIntent->charges = (object)['data' => [$mockCharge]];

        $this->mockStripeClient->paymentIntents = Mockery::mock();
        $this->mockStripeClient->paymentIntents
            ->shouldReceive('create')
            ->once()
            ->andReturn($mockPaymentIntent);

        $this->mockLogger->shouldReceive('info')->atLeast(1);

        // Act
        $result = $this->service->processPayment($request, []);

        // Assert
        $this->assertTrue($result->success);
        $this->assertEquals('pi_test123', $result->paymentId);
        $this->assertEquals(100.00, $result->amount);
        $this->assertEquals('EUR', $result->currency);
    }

    /** @test */
    public function it_handles_payment_failure_gracefully() {
        // Arrange
        $request = new PaymentRequest(
            amount: 100.00,
            currency: 'EUR',
            egiId: 123,
            userId: 1,
            customerEmail: 'test@example.com'
        );

        $this->mockStripeClient->paymentIntents = Mockery::mock();
        $this->mockStripeClient->paymentIntents
            ->shouldReceive('create')
            ->once()
            ->andThrow(new \Stripe\Exception\CardException('Card declined', 'card_declined', 'card_declined'));

        $this->mockLogger->shouldReceive('error')->once();
        $this->mockErrorManager->shouldReceive('handle')->once();

        // Act
        $result = $this->service->processPayment($request, []);

        // Assert
        $this->assertFalse($result->success);
        $this->assertStringContains('Card declined', $result->errorMessage);
    }

    /** @test */
    public function it_reverses_transfers_on_refund_with_proper_state_tracking() {
        // Arrange
        $paymentId = 'pi_test123';

        // Create test distributions
        $distribution1 = PaymentDistribution::factory()->create([
            'payment_intent_id' => $paymentId,
            'transfer_id' => 'tr_test1',
            'status' => 'completed',
            'amount_eur' => 70.00,
            'wallet_id' => 1
        ]);

        $distribution2 = PaymentDistribution::factory()->create([
            'payment_intent_id' => $paymentId,
            'transfer_id' => 'tr_test2',
            'status' => 'completed',
            'amount_eur' => 25.00,
            'wallet_id' => 2
        ]);

        // Mock Stripe refund
        $mockRefund = Mockery::mock();
        $mockRefund->id = 're_test123';
        $mockRefund->amount = 10000; // €100.00 in cents
        $mockRefund->currency = 'eur';

        $this->mockStripeClient->refunds = Mockery::mock();
        $this->mockStripeClient->refunds
            ->shouldReceive('create')
            ->once()
            ->with(['payment_intent' => $paymentId])
            ->andReturn($mockRefund);

        // Mock transfer reversals
        $mockReversal1 = Mockery::mock();
        $mockReversal1->id = 'trr_test1';

        $mockReversal2 = Mockery::mock();
        $mockReversal2->id = 'trr_test2';

        $this->mockStripeClient->transfers = Mockery::mock();
        $this->mockStripeClient->transfers
            ->shouldReceive('createReversal')
            ->twice()
            ->andReturn($mockReversal1, $mockReversal2);

        $this->mockLogger->shouldReceive('info')->atLeast(1);

        // Act
        $result = $this->service->refundPayment($paymentId);

        // Assert
        $this->assertTrue($result->success);
        $this->assertEquals('re_test123', $result->refundId);

        // Verify distributions were updated
        $distribution1->refresh();
        $distribution2->refresh();

        $this->assertEquals('reversed', $distribution1->status);
        $this->assertEquals('reversed', $distribution2->status);
        $this->assertEquals('trr_test1', $distribution1->reversal_id);
        $this->assertNotNull($distribution1->reversed_at);
    }

    /** @test */
    public function it_handles_reversal_failures_without_blocking_refund() {
        // Arrange
        $paymentId = 'pi_test123';

        $distribution = PaymentDistribution::factory()->create([
            'payment_intent_id' => $paymentId,
            'transfer_id' => 'tr_test1',
            'status' => 'completed',
            'amount_eur' => 70.00
        ]);

        // Mock Stripe refund success
        $mockRefund = Mockery::mock();
        $mockRefund->id = 're_test123';
        $mockRefund->amount = 10000;
        $mockRefund->currency = 'eur';

        $this->mockStripeClient->refunds = Mockery::mock();
        $this->mockStripeClient->refunds
            ->shouldReceive('create')
            ->once()
            ->andReturn($mockRefund);

        // Mock transfer reversal failure
        $this->mockStripeClient->transfers = Mockery::mock();
        $this->mockStripeClient->transfers
            ->shouldReceive('createReversal')
            ->once()
            ->andThrow(new \Exception('Insufficient funds in connected account'));

        $this->mockLogger->shouldReceive('info')->atLeast(1);
        $this->mockLogger->shouldReceive('error')->once();

        // Act
        $result = $this->service->refundPayment($paymentId);

        // Assert
        $this->assertTrue($result->success); // Refund succeeds even if reversal fails

        // Verify distribution marked as reversal_failed
        $distribution->refresh();
        $this->assertEquals('reversal_failed', $distribution->status);
        $this->assertStringContains('Insufficient funds', $distribution->failure_reason);
        $this->assertEquals(1, $distribution->retry_count);
    }

    /** @test */
    public function it_adds_idempotency_key_to_reversals() {
        // Arrange
        $paymentId = 'pi_test123';
        $distribution = PaymentDistribution::factory()->create([
            'payment_intent_id' => $paymentId,
            'transfer_id' => 'tr_test1',
            'status' => 'completed'
        ]);

        $mockRefund = Mockery::mock();
        $mockRefund->id = 're_test123';
        $mockRefund->amount = 10000;
        $mockRefund->currency = 'eur';

        $this->mockStripeClient->refunds = Mockery::mock();
        $this->mockStripeClient->refunds->shouldReceive('create')->andReturn($mockRefund);

        $mockReversal = Mockery::mock();
        $mockReversal->id = 'trr_test1';

        // Assert idempotency key is passed
        $this->mockStripeClient->transfers = Mockery::mock();
        $this->mockStripeClient->transfers
            ->shouldReceive('createReversal')
            ->once()
            ->with(
                'tr_test1',
                Mockery::any(),
                ['idempotency_key' => $paymentId . '_reversal_tr_test1']
            )
            ->andReturn($mockReversal);

        $this->mockLogger->shouldReceive('info')->atLeast(1);

        // Act
        $this->service->refundPayment($paymentId);
    }

    protected function tearDown(): void {
        Mockery::close();
        parent::tearDown();
    }
}
