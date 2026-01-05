<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\QueryException;
use App\Models\PaymentDistribution;
use App\Models\PspWebhookEvent;
use App\Models\Collection;
use App\Models\Wallet;
use Illuminate\Support\Str;

/**
 * Unit tests for payment-related models
 */
class PaymentModelsTest extends TestCase {
    use RefreshDatabase;

    protected $collection;

    protected function setUp(): void {
        parent::setUp();

        // Create a collection for use in tests
        $this->collection = Collection::factory()->create();
    }

    /** @test */
    public function payment_distribution_enforces_unique_payment_wallet_constraint() {
        // Arrange
        $paymentIntentId = 'pi_test_unique';
        $walletId = 1;

        PaymentDistribution::factory()->create([
            'payment_intent_id' => $paymentIntentId,
            'wallet_id' => $walletId,
            'collection_id' => $this->collection->id,
            'distribution_status' => 'completed'
        ]);

        // Act & Assert - Should throw unique constraint violation
        $this->expectException(QueryException::class);

        PaymentDistribution::factory()->create([
            'payment_intent_id' => $paymentIntentId,
            'wallet_id' => $walletId, // Same combination
            'collection_id' => $this->collection->id,
            'distribution_status' => 'pending'
        ]);
    }

    /** @test */
    public function payment_distribution_allows_different_wallet_combinations() {
        // Arrange & Act
        $paymentIntentId = 'pi_test_different';
        $wallet1 = \App\Models\Wallet::factory()->create();
        $wallet2 = \App\Models\Wallet::factory()->create();

        $distribution1 = PaymentDistribution::factory()->create([
            'payment_intent_id' => $paymentIntentId,
            'wallet_id' => $wallet1->id,
            'user_type' => 'creator'
        ]);

        $distribution2 = PaymentDistribution::factory()->create([
            'payment_intent_id' => $paymentIntentId,
            'wallet_id' => $wallet2->id, // Different wallet
            'user_type' => 'epp'
        ]);

        // Assert
        $this->assertEquals($paymentIntentId, $distribution1->payment_intent_id);
        $this->assertEquals($paymentIntentId, $distribution2->payment_intent_id);
        $this->assertNotEquals($distribution1->wallet_id, $distribution2->wallet_id);
    }

    /** @test */
    public function payment_distribution_tracks_state_transitions_correctly() {
        // Arrange & Act
        $distribution = PaymentDistribution::factory()->create([
            'distribution_status' => 'pending',
            'user_type' => 'creator'
        ]);

        // Assert initial state
        $this->assertEquals('pending', $distribution->distribution_status->value);
        $this->assertNull($distribution->transfer_id);

        // Act - Complete distribution
        $distribution->update([
            'distribution_status' => 'completed',
            'transfer_id' => 'tr_12345'
        ]);

        // Assert completed state
        $this->assertEquals('completed', $distribution->fresh()->distribution_status->value);
        $this->assertEquals('tr_12345', $distribution->fresh()->transfer_id);
    }

    /** @test */
    public function payment_distribution_calculates_amounts_accurately() {
        // Arrange & Act
        $distribution = PaymentDistribution::factory()->create([
            'amount_cents' => 5000, // €50.00
            'percentage' => 25.5,
            'user_type' => 'creator'
        ]);

        // Assert
        $this->assertEquals(5000, $distribution->amount_cents);
        $this->assertEquals(25.5, $distribution->percentage);
        $this->assertEquals(50.00, $distribution->amount_cents / 100); // Convert to euros
    }

    /** @test */
    public function payment_distribution_validates_status_transitions() {
        // Test valid status values
        $statuses = ['pending', 'completed', 'failed', 'reversed'];

        foreach ($statuses as $status) {
            $distribution = PaymentDistribution::factory()->create([
                'distribution_status' => $status,
                'user_type' => 'epp'
            ]);

            $this->assertEquals($status, $distribution->distribution_status->value);
        }
    }

    /** @test */
    public function payment_distribution_has_correct_relationships() {
        // Arrange
        $wallet = Wallet::factory()->create();
        $paymentIntentId = 'pi_relationship_test';

        $distribution = PaymentDistribution::factory()->create([
            'wallet_id' => $wallet->id,
            'payment_intent_id' => $paymentIntentId
        ]);

        // Act & Assert
        $this->assertInstanceOf(Wallet::class, $distribution->wallet);
        $this->assertEquals($wallet->id, $distribution->wallet->id);
        $this->assertEquals($paymentIntentId, $distribution->payment_intent_id);
    }

    /** @test */
    public function psp_webhook_event_enforces_unique_provider_event_constraint() {
        // Arrange
        $eventId = 'evt_unique_test';
        $provider = 'stripe';

        PspWebhookEvent::factory()->create([
            'event_id' => $eventId,
            'provider' => $provider,
            'status' => 'processed'
        ]);

        // Act & Assert - Should throw unique constraint violation
        $this->expectException(QueryException::class);

        PspWebhookEvent::factory()->create([
            'event_id' => $eventId,
            'provider' => $provider, // Same combination
            'status' => 'processing'
        ]);
    }

    /** @test */
    public function psp_webhook_event_allows_different_event_ids() {
        // Arrange & Act
        $event1 = PspWebhookEvent::factory()->create([
            'event_id' => 'evt_test_1',
            'provider' => 'paypal'
        ]);

        $event2 = PspWebhookEvent::factory()->create([
            'event_id' => 'evt_test_2',
            'provider' => 'paypal'
        ]);

        // Assert
        $this->assertNotEquals($event1->event_id, $event2->event_id);
        $this->assertEquals('paypal', $event1->provider);
        $this->assertEquals('paypal', $event2->provider);
    }

    /** @test */
    public function psp_webhook_event_tracks_retry_attempts() {
        // Arrange & Act
        $event = PspWebhookEvent::factory()->create([
            'event_id' => 'evt_' . Str::uuid(),
            'provider' => 'paypal',
            'status' => 'failed',
            'retry_count' => 3,
            'error_message' => 'Test error message'
        ]);

        // Assert
        $this->assertEquals('failed', $event->status);
        $this->assertEquals(3, $event->retry_count);
        $this->assertEquals('Test error message', $event->error_message);
    }

    /** @test */
    public function models_handle_json_attributes_correctly() {
        // Arrange & Act
        $payload = [
            'id' => 'pi_test_json',
            'amount' => 1000,
            'metadata' => ['egi_id' => 123, 'collection_id' => 456]
        ];

        $event = PspWebhookEvent::factory()->create([
            'payload' => $payload
        ]);

        // Assert
        $this->assertIsArray($event->payload);
        $this->assertEquals($payload['id'], $event->payload['id']);
        $this->assertEquals($payload['metadata']['egi_id'], $event->payload['metadata']['egi_id']);
    }

    /** @test */
    public function models_use_appropriate_date_casting() {
        // Arrange & Act
        $distribution = PaymentDistribution::factory()->create([
            'user_type' => 'epp',
            'distribution_status' => 'completed'
        ]);

        $event = PspWebhookEvent::factory()->create();

        // Assert
        $this->assertInstanceOf(\Carbon\Carbon::class, $distribution->created_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $event->received_at);

        if ($event->processed_at) {
            $this->assertInstanceOf(\Carbon\Carbon::class, $event->processed_at);
        }
    }
}
