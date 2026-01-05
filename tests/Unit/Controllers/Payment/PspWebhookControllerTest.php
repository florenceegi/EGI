<?php

namespace Tests\Unit\Controllers\Payment;

use Tests\TestCase;
use App\Http\Controllers\Payment\PspWebhookController;
use App\Models\PspWebhookEvent;
use App\Services\PaymentServiceFactory;
use App\Contracts\PaymentServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Mockery;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PspWebhookControllerTest extends TestCase {
    use RefreshDatabase;

    private PspWebhookController $controller;
    private PaymentServiceFactory $mockFactory;
    private PaymentServiceInterface $mockPaymentService;
    private $mockLogger;
    private $mockErrorManager;
    private $mockAuditService;
    private $mockConsentService;

    protected function setUp(): void {
        parent::setUp();

        $this->mockPaymentService = Mockery::mock(PaymentServiceInterface::class);
        $this->mockFactory = Mockery::mock(PaymentServiceFactory::class);
        $this->mockLogger = Mockery::mock(\Ultra\UltraLogManager\UltraLogManager::class);
        $this->mockErrorManager = Mockery::mock(\Ultra\ErrorManager\Interfaces\ErrorManagerInterface::class);
        $this->mockAuditService = Mockery::mock(\App\Services\AuditLogService::class);
        $this->mockConsentService = Mockery::mock(\App\Services\ConsentService::class);

        $this->controller = new PspWebhookController(
            $this->mockLogger,
            $this->mockErrorManager,
            $this->mockAuditService,
            $this->mockConsentService,
            $this->mockFactory
        );
    }

    /** @test */
    public function it_processes_stripe_webhook_successfully() {
        // Arrange
        $webhookId = 'wh_test_123456789';
        $payloadData = [
            'id' => 'pi_test_payment_intent',
            'object' => 'payment_intent',
            'status' => 'succeeded',
            'amount' => 5000,
            'metadata' => ['egi_id' => '123']
        ];

        $request = $this->createMockRequest('stripe', $webhookId, $payloadData);

        $this->mockFactory
            ->shouldReceive('create')
            ->with('stripe')
            ->once()
            ->andReturn($this->mockPaymentService);

        $this->mockPaymentService
            ->shouldReceive('verifyWebhook')
            ->once()
            ->andReturn(true);

        $this->mockPaymentService
            ->shouldReceive('processPaymentWebhook')
            ->with($payloadData, [])
            ->once()
            ->andReturn(['success' => true, 'payment_id' => 'pi_test_payment_intent']);

        // Act
        $response = $this->controller->processWebhook($request, 'stripe');

        // Assert
        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertTrue($responseData['success']);

        // Verify webhook event was logged
        $this->assertDatabaseHas('psp_webhook_events', [
            'webhook_id' => $webhookId,
            'psp' => 'stripe',
            'event_type' => 'payment_intent',
            'status' => 'processed',
            'payload_hash' => hash('sha256', json_encode($payloadData))
        ]);
    }

    /** @test */
    public function it_prevents_duplicate_webhook_processing() {
        // Arrange - Create existing webhook event
        $webhookId = 'wh_duplicate_test';
        $payloadData = ['id' => 'pi_test', 'object' => 'payment_intent'];

        PspWebhookEvent::create([
            'webhook_id' => $webhookId,
            'psp' => 'stripe',
            'event_type' => 'payment_intent',
            'status' => 'processed',
            'payload_hash' => hash('sha256', json_encode($payloadData)),
            'processed_at' => now()
        ]);

        $request = $this->createMockRequest('stripe', $webhookId, $payloadData);

        // Act
        $response = $this->controller->processWebhook($request, 'stripe');

        // Assert
        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertTrue($responseData['success']);
        $this->assertEquals('already_processed', $responseData['status']);

        // Verify no payment service was called
        $this->mockFactory->shouldNotHaveReceived('createPaymentService');
    }

    /** @test */
    public function it_handles_webhook_processing_failures() {
        // Arrange
        $webhookId = 'wh_failure_test';
        $payloadData = ['id' => 'pi_fail', 'object' => 'payment_intent'];

        $request = $this->createMockRequest('stripe', $webhookId, $payloadData);

        $this->mockFactory
            ->shouldReceive('createPaymentService')
            ->with('stripe')
            ->once()
            ->andReturn($this->mockPaymentService);

        $this->mockPaymentService
            ->shouldReceive('processPaymentWebhook')
            ->once()
            ->andThrow(new \Exception('Processing failed'));

        // Act
        $response = $this->controller->processWebhook($request, 'stripe');

        // Assert
        $this->assertEquals(500, $response->getStatusCode());

        // Verify webhook event marked as failed
        $this->assertDatabaseHas('psp_webhook_events', [
            'webhook_id' => $webhookId,
            'status' => 'failed',
            'error_message' => 'Processing failed',
            'retry_count' => 1
        ]);
    }

    /** @test */
    public function it_handles_invalid_psp_gracefully() {
        // Arrange
        $request = Request::create('/webhook/invalid_psp', 'POST', [], [], [], [], '{}');
        $request->headers->set('stripe-signature', 'invalid_sig');

        $this->mockFactory
            ->shouldReceive('createPaymentService')
            ->with('invalid_psp')
            ->once()
            ->andThrow(new \InvalidArgumentException('Unsupported PSP: invalid_psp'));

        // Act
        $response = $this->controller->processWebhook($request, 'invalid_psp');

        // Assert
        $this->assertEquals(400, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['success']);
        $this->assertStringContains('Unsupported PSP', $responseData['error']);
    }

    /** @test */
    public function it_validates_webhook_signatures() {
        // Arrange
        $request = Request::create('/webhook/stripe', 'POST', [], [], [], [], '{}');
        // Missing stripe-signature header

        // Act
        $response = $this->controller->processWebhook($request, 'stripe');

        // Assert
        $this->assertEquals(400, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['success']);
        $this->assertStringContains('Missing webhook signature', $responseData['error']);
    }

    /** @test */
    public function it_extracts_webhook_id_from_different_headers() {
        // Test Stripe webhook ID extraction
        $stripeRequest = Request::create('/webhook/stripe', 'POST', [], [], [], [], '{}');
        $stripeRequest->headers->set('stripe-signature', 't=123,v1=signature,wh_id=wh_stripe_123');

        $webhookId = $this->invokePrivateMethod('extractWebhookId', [$stripeRequest, 'stripe']);
        $this->assertEquals('wh_stripe_123', $webhookId);

        // Test fallback to generated ID
        $genericRequest = Request::create('/webhook/generic', 'POST', [], [], [], [], '{"id": "evt_123"}');
        $genericRequest->headers->set('x-webhook-signature', 'signature');

        $webhookId = $this->invokePrivateMethod('extractWebhookId', [$genericRequest, 'generic']);
        $this->assertStringContains('generic_', $webhookId);
    }

    /** @test */
    public function it_determines_event_types_correctly() {
        // Test payment_intent event
        $paymentPayload = ['object' => 'payment_intent', 'status' => 'succeeded'];
        $eventType = $this->invokePrivateMethod('determineEventType', [$paymentPayload]);
        $this->assertEquals('payment_intent', $eventType);

        // Test transfer event
        $transferPayload = ['object' => 'transfer', 'destination' => 'acct_123'];
        $eventType = $this->invokePrivateMethod('determineEventType', [$transferPayload]);
        $this->assertEquals('transfer', $eventType);

        // Test unknown event
        $unknownPayload = ['object' => 'unknown_object'];
        $eventType = $this->invokePrivateMethod('determineEventType', [$unknownPayload]);
        $this->assertEquals('unknown', $eventType);
    }

    /** @test */
    public function it_calculates_payload_hash_consistently() {
        // Arrange
        $payload1 = ['id' => 'test', 'amount' => 1000, 'metadata' => ['key' => 'value']];
        $payload2 = ['metadata' => ['key' => 'value'], 'id' => 'test', 'amount' => 1000]; // Different order

        // Act
        $hash1 = $this->invokePrivateMethod('calculatePayloadHash', [$payload1]);
        $hash2 = $this->invokePrivateMethod('calculatePayloadHash', [$payload2]);

        // Assert - Should be same despite different key order
        $this->assertEquals($hash1, $hash2);
        $this->assertEquals(64, strlen($hash1)); // SHA256 length
    }

    /** @test */
    public function it_handles_retry_logic_correctly() {
        // Arrange - Create webhook with failed attempts
        $webhookId = 'wh_retry_test';
        $payloadData = ['id' => 'pi_retry', 'object' => 'payment_intent'];

        $existingEvent = PspWebhookEvent::create([
            'webhook_id' => $webhookId,
            'psp' => 'stripe',
            'event_type' => 'payment_intent',
            'status' => 'failed',
            'payload_hash' => hash('sha256', json_encode($payloadData)),
            'retry_count' => 2, // Already failed twice
            'error_message' => 'Previous failure'
        ]);

        $request = $this->createMockRequest('stripe', $webhookId, $payloadData);

        $this->mockFactory
            ->shouldReceive('createPaymentService')
            ->with('stripe')
            ->once()
            ->andReturn($this->mockPaymentService);

        $this->mockPaymentService
            ->shouldReceive('processPaymentWebhook')
            ->once()
            ->andReturn(['success' => true, 'payment_id' => 'pi_retry']);

        // Act
        $response = $this->controller->processWebhook($request, 'stripe');

        // Assert - Should process successfully and reset retry count
        $this->assertEquals(200, $response->getStatusCode());

        $updatedEvent = $existingEvent->fresh();
        $this->assertEquals('processed', $updatedEvent->status);
        $this->assertEquals(0, $updatedEvent->retry_count);
        $this->assertNotNull($updatedEvent->processed_at);
    }

    /** @test */
    public function it_limits_maximum_retries() {
        // Arrange - Create webhook with max retries reached
        $webhookId = 'wh_max_retry_test';
        $payloadData = ['id' => 'pi_max_retry', 'object' => 'payment_intent'];

        PspWebhookEvent::create([
            'webhook_id' => $webhookId,
            'psp' => 'stripe',
            'event_type' => 'payment_intent',
            'status' => 'failed',
            'payload_hash' => hash('sha256', json_encode($payloadData)),
            'retry_count' => 5, // Max retries reached
            'error_message' => 'Max retries reached'
        ]);

        $request = $this->createMockRequest('stripe', $webhookId, $payloadData);

        // Act
        $response = $this->controller->processWebhook($request, 'stripe');

        // Assert - Should not process again
        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('max_retries_exceeded', $responseData['status']);

        // Verify no payment service was called
        $this->mockFactory->shouldNotHaveReceived('createPaymentService');
    }

    /**
     * Helper to create mock request with proper headers
     */
    private function createMockRequest(string $psp, string $webhookId, array $payload): Request {
        $jsonPayload = json_encode($payload);
        $request = Request::create("/webhook/{$psp}", 'POST', [], [], [], [], $jsonPayload);

        if ($psp === 'stripe') {
            $request->headers->set('stripe-signature', "t=123,v1=signature,wh_id={$webhookId}");
        } else {
            $request->headers->set('x-webhook-id', $webhookId);
        }

        return $request;
    }

    /**
     * Helper method to invoke private methods for testing
     */
    private function invokePrivateMethod(string $methodName, array $parameters = []) {
        $reflection = new \ReflectionClass($this->controller);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($this->controller, $parameters);
    }

    protected function tearDown(): void {
        Mockery::close();
        parent::tearDown();
    }
}
