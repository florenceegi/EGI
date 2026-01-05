<?php

namespace Tests\Unit\Console\Commands;

use Tests\TestCase;
use App\Console\Commands\{CleanupWebhookEvents, RetryWebhookEvents};
use App\Models\PspWebhookEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\{Artisan, Log};

class WebhookManagementCommandsTest extends TestCase {
    use RefreshDatabase;

    /** @test */
    public function cleanup_command_removes_old_processed_webhooks() {
        // Arrange - Create webhook events of different ages and statuses
        $oldProcessed = PspWebhookEvent::factory()->create([
            'status' => 'processed',
            'created_at' => now()->subDays(8), // Older than 7 days
            'processed_at' => now()->subDays(8)
        ]);

        $recentProcessed = PspWebhookEvent::factory()->create([
            'status' => 'processed',
            'created_at' => now()->subDays(5), // Within 7 days
            'processed_at' => now()->subDays(5)
        ]);

        $oldFailed = PspWebhookEvent::factory()->create([
            'status' => 'failed',
            'created_at' => now()->subDays(10), // Old but failed - should keep
            'retry_count' => 3
        ]);

        $oldPending = PspWebhookEvent::factory()->create([
            'status' => 'pending',
            'created_at' => now()->subDays(9) // Old pending - should keep
        ]);

        // Act
        $exitCode = Artisan::call('webhook:cleanup');

        // Assert
        $this->assertEquals(0, $exitCode);

        // Old processed should be deleted
        $this->assertDatabaseMissing('psp_webhook_events', ['id' => $oldProcessed->id]);

        // Recent processed should remain
        $this->assertDatabaseHas('psp_webhook_events', ['id' => $recentProcessed->id]);

        // Failed and pending should remain regardless of age
        $this->assertDatabaseHas('psp_webhook_events', ['id' => $oldFailed->id]);
        $this->assertDatabaseHas('psp_webhook_events', ['id' => $oldPending->id]);

        // Verify command output
        $output = Artisan::output();
        $this->assertStringContains('Cleaned up 1 old webhook events', $output);
    }

    /** @test */
    public function cleanup_command_respects_custom_retention_days() {
        // Arrange
        $webhook = PspWebhookEvent::factory()->create([
            'status' => 'processed',
            'created_at' => now()->subDays(4),
            'processed_at' => now()->subDays(4)
        ]);

        // Act - Use custom retention of 3 days
        $exitCode = Artisan::call('webhook:cleanup', ['--days' => 3]);

        // Assert
        $this->assertEquals(0, $exitCode);

        // Should be deleted because it's older than 3 days
        $this->assertDatabaseMissing('psp_webhook_events', ['id' => $webhook->id]);
    }

    /** @test */
    public function cleanup_command_handles_dry_run_mode() {
        // Arrange
        $oldWebhook = PspWebhookEvent::factory()->create([
            'status' => 'processed',
            'created_at' => now()->subDays(8),
            'processed_at' => now()->subDays(8)
        ]);

        // Act
        $exitCode = Artisan::call('webhook:cleanup', ['--dry-run' => true]);

        // Assert
        $this->assertEquals(0, $exitCode);

        // Webhook should still exist in dry-run mode
        $this->assertDatabaseHas('psp_webhook_events', ['id' => $oldWebhook->id]);

        // Verify dry-run output
        $output = Artisan::output();
        $this->assertStringContains('[DRY RUN]', $output);
        $this->assertStringContains('Would clean up 1 webhook events', $output);
    }

    /** @test */
    public function retry_command_processes_failed_webhooks_within_retry_limit() {
        // Arrange - Create failed webhook within retry limit
        $retryableWebhook = PspWebhookEvent::factory()->create([
            'webhook_id' => 'wh_retryable_test',
            'psp' => 'stripe',
            'status' => 'failed',
            'retry_count' => 2, // Below max retries (5)
            'error_message' => 'Temporary failure',
            'event_type' => 'payment_intent',
            'payload' => json_encode(['id' => 'pi_test', 'object' => 'payment_intent'])
        ]);

        $maxRetriesWebhook = PspWebhookEvent::factory()->create([
            'webhook_id' => 'wh_max_retries_test',
            'psp' => 'stripe',
            'status' => 'failed',
            'retry_count' => 5, // At max retries
            'error_message' => 'Max retries reached'
        ]);

        // Mock the payment service for successful retry
        $this->mock(\App\Services\PaymentServiceFactory::class, function ($mock) {
            $paymentService = \Mockery::mock(\App\Contracts\PaymentServiceInterface::class);
            $paymentService->shouldReceive('processPaymentWebhook')
                ->once()
                ->andReturn(['success' => true, 'payment_id' => 'pi_test']);

            $mock->shouldReceive('createPaymentService')
                ->with('stripe')
                ->once()
                ->andReturn($paymentService);
        });

        // Act
        $exitCode = Artisan::call('webhook:retry');

        // Assert
        $this->assertEquals(0, $exitCode);

        // Retryable webhook should be processed successfully
        $retryableWebhook->refresh();
        $this->assertEquals('processed', $retryableWebhook->status);
        $this->assertEquals(0, $retryableWebhook->retry_count); // Reset on success
        $this->assertNotNull($retryableWebhook->processed_at);

        // Max retries webhook should remain unchanged
        $maxRetriesWebhook->refresh();
        $this->assertEquals('failed', $maxRetriesWebhook->status);
        $this->assertEquals(5, $maxRetriesWebhook->retry_count);

        // Verify command output
        $output = Artisan::output();
        $this->assertStringContains('Successfully retried 1 webhook events', $output);
        $this->assertStringContains('Skipped 1 webhook events (max retries exceeded)', $output);
    }

    /** @test */
    public function retry_command_handles_specific_psp_filtering() {
        // Arrange - Create webhooks for different PSPs
        $stripeWebhook = PspWebhookEvent::factory()->create([
            'psp' => 'stripe',
            'status' => 'failed',
            'retry_count' => 1
        ]);

        $paypalWebhook = PspWebhookEvent::factory()->create([
            'psp' => 'paypal',
            'status' => 'failed',
            'retry_count' => 1
        ]);

        // Mock only Stripe service
        $this->mock(\App\Services\PaymentServiceFactory::class, function ($mock) {
            $paymentService = \Mockery::mock(\App\Contracts\PaymentServiceInterface::class);
            $paymentService->shouldReceive('processPaymentWebhook')->once();

            $mock->shouldReceive('createPaymentService')
                ->with('stripe')
                ->once()
                ->andReturn($paymentService);
        });

        // Act - Retry only Stripe webhooks
        $exitCode = Artisan::call('webhook:retry', ['--psp' => 'stripe']);

        // Assert
        $this->assertEquals(0, $exitCode);

        // Only Stripe webhook should be processed
        $stripeWebhook->refresh();
        $this->assertEquals('processed', $stripeWebhook->status);

        // PayPal webhook should remain failed
        $paypalWebhook->refresh();
        $this->assertEquals('failed', $paypalWebhook->status);
    }

    /** @test */
    public function retry_command_respects_max_retries_limit() {
        // Arrange
        $webhook = PspWebhookEvent::factory()->create([
            'psp' => 'stripe',
            'status' => 'failed',
            'retry_count' => 3,
            'payload' => json_encode(['id' => 'pi_test'])
        ]);

        // Mock service to fail again
        $this->mock(\App\Services\PaymentServiceFactory::class, function ($mock) {
            $paymentService = \Mockery::mock(\App\Contracts\PaymentServiceInterface::class);
            $paymentService->shouldReceive('processPaymentWebhook')
                ->once()
                ->andThrow(new \Exception('Still failing'));

            $mock->shouldReceive('createPaymentService')
                ->with('stripe')
                ->once()
                ->andReturn($paymentService);
        });

        // Act - Set max retries to 4
        $exitCode = Artisan::call('webhook:retry', ['--max-retries' => 4]);

        // Assert
        $this->assertEquals(0, $exitCode);

        $webhook->refresh();
        $this->assertEquals('failed', $webhook->status);
        $this->assertEquals(4, $webhook->retry_count); // Incremented
        $this->assertStringContains('Still failing', $webhook->error_message);
    }

    /** @test */
    public function retry_command_handles_batch_processing() {
        // Arrange - Create many failed webhooks
        $webhooks = collect();
        for ($i = 0; $i < 15; $i++) {
            $webhooks->push(PspWebhookEvent::factory()->create([
                'psp' => 'stripe',
                'status' => 'failed',
                'retry_count' => 1,
                'payload' => json_encode(['id' => "pi_test_{$i}"])
            ]));
        }

        // Mock service for batch processing
        $this->mock(\App\Services\PaymentServiceFactory::class, function ($mock) {
            $paymentService = \Mockery::mock(\App\Contracts\PaymentServiceInterface::class);
            $paymentService->shouldReceive('processPaymentWebhook')
                ->times(15) // Called for each webhook
                ->andReturn(['success' => true]);

            $mock->shouldReceive('createPaymentService')
                ->with('stripe')
                ->times(15)
                ->andReturn($paymentService);
        });

        // Act - Process with batch size of 5
        $exitCode = Artisan::call('webhook:retry', ['--batch-size' => 5]);

        // Assert
        $this->assertEquals(0, $exitCode);

        // All webhooks should be processed successfully
        foreach ($webhooks as $webhook) {
            $webhook->refresh();
            $this->assertEquals('processed', $webhook->status);
        }

        // Verify batching in output
        $output = Artisan::output();
        $this->assertStringContains('Processing batch', $output);
    }

    /** @test */
    public function retry_command_logs_processing_errors() {
        // Arrange
        $webhook = PspWebhookEvent::factory()->create([
            'webhook_id' => 'wh_error_test',
            'psp' => 'stripe',
            'status' => 'failed',
            'retry_count' => 1,
            'payload' => json_encode(['id' => 'pi_error_test'])
        ]);

        // Mock service to throw exception
        $this->mock(\App\Services\PaymentServiceFactory::class, function ($mock) {
            $paymentService = \Mockery::mock(\App\Contracts\PaymentServiceInterface::class);
            $paymentService->shouldReceive('processPaymentWebhook')
                ->once()
                ->andThrow(new \Exception('Service error'));

            $mock->shouldReceive('createPaymentService')
                ->once()
                ->andReturn($paymentService);
        });

        // Expect error to be logged
        Log::shouldReceive('error')
            ->once()
            ->withArgs(function ($message, $context) {
                return str_contains($message, 'Failed to retry webhook') &&
                    $context['webhook_id'] === 'wh_error_test';
            });

        // Act
        $exitCode = Artisan::call('webhook:retry');

        // Assert
        $this->assertEquals(0, $exitCode); // Command continues despite errors

        $webhook->refresh();
        $this->assertEquals('failed', $webhook->status);
        $this->assertEquals(2, $webhook->retry_count); // Incremented
        $this->assertStringContains('Service error', $webhook->error_message);
    }

    /** @test */
    public function commands_provide_helpful_output_and_statistics() {
        // Arrange for cleanup command
        PspWebhookEvent::factory()->count(3)->create([
            'status' => 'processed',
            'created_at' => now()->subDays(10)
        ]);

        PspWebhookEvent::factory()->count(2)->create([
            'status' => 'failed',
            'retry_count' => 1
        ]);

        // Act - Test cleanup command output
        $exitCode = Artisan::call('webhook:cleanup', ['--verbose' => true]);

        // Assert cleanup output
        $this->assertEquals(0, $exitCode);
        $output = Artisan::output();
        $this->assertStringContains('Scanning for webhook events older than 7 days', $output);
        $this->assertStringContains('Cleaned up 3 old webhook events', $output);

        // Act - Test retry command output
        $exitCode = Artisan::call('webhook:retry', ['--verbose' => true]);

        // Assert retry output
        $this->assertEquals(0, $exitCode);
        $output = Artisan::output();
        $this->assertStringContains('Found 2 webhook events to retry', $output);
    }
}
