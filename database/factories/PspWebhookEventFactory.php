<?php

namespace Database\Factories;

use App\Models\PspWebhookEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory for PspWebhookEvent model.
 * Generates test data for webhook event testing.
 */
class PspWebhookEventFactory extends Factory {
    protected $model = PspWebhookEvent::class;

    public function definition(): array {
        return [
            'event_id' => 'evt_' . $this->faker->uuid(),
            'provider' => $this->faker->randomElement(['stripe', 'paypal']),
            'event_type' => $this->faker->randomElement(['payment_intent.succeeded', 'transfer.created', 'payment_intent.failed', 'charge.succeeded']),
            'status' => $this->faker->randomElement(['processing', 'processed', 'failed']),
            'payload' => function (array $attributes) {
                return [
                    'id' => 'pi_' . $this->faker->uuid(),
                    'object' => $attributes['event_type'],
                    'status' => $this->faker->randomElement(['succeeded', 'failed', 'requires_action']),
                    'amount' => $this->faker->numberBetween(100, 10000),
                    'currency' => 'eur',
                    'metadata' => [
                        'egi_id' => $this->faker->numberBetween(1, 1000),
                        'collection_id' => $this->faker->numberBetween(1, 100)
                    ]
                ];
            },
            'retry_count' => function (array $attributes) {
                return $attributes['status'] === 'failed'
                    ? $this->faker->numberBetween(0, 5)
                    : 0;
            },
            'error_message' => function (array $attributes) {
                return $attributes['status'] === 'failed'
                    ? $this->faker->sentence()
                    : null;
            },
            'processed_at' => function (array $attributes) {
                return $attributes['status'] === 'processed'
                    ? $this->faker->dateTimeThisMonth()
                    : null;
            },
            'received_at' => $this->faker->dateTimeThisMonth()
        ];
    }

    /**
     * Create a processing webhook event
     */
    public function processing(): static {
        return $this->state(fn() => [
            'status' => 'processing',
            'processed_at' => null,
            'error_message' => null,
            'retry_count' => 0
        ]);
    }

    /**
     * Create a processed webhook event
     */
    public function processed(): static {
        return $this->state(fn() => [
            'status' => 'processed',
            'processed_at' => $this->faker->dateTimeThisMonth(),
            'error_message' => null,
            'retry_count' => 0
        ]);
    }

    /**
     * Create a failed webhook event
     */
    public function failed(): static {
        return $this->state(fn() => [
            'status' => 'failed',
            'processed_at' => null,
            'error_message' => $this->faker->sentence(),
            'retry_count' => $this->faker->numberBetween(1, 5)
        ]);
    }

    /**
     * Create webhook for specific PSP
     */
    public function forPsp(string $psp): static {
        return $this->state(fn() => ['psp' => $psp]);
    }

    /**
     * Create webhook with specific event type
     */
    public function withEventType(string $eventType): static {
        return $this->state(fn() => [
            'event_type' => $eventType,
            'payload' => [
                'id' => $eventType === 'payment_intent' ? 'pi_' . $this->faker->uuid() : 'tr_' . $this->faker->uuid(),
                'object' => $eventType,
                'status' => 'succeeded',
                'amount' => $this->faker->numberBetween(100, 10000)
            ]
        ]);
    }

    /**
     * Create webhook with specific payload
     */
    public function withPayload(array $payload): static {
        return $this->state(fn() => [
            'payload' => $payload,
            'payload_hash' => hash('sha256', json_encode($payload))
        ]);
    }

    /**
     * Create webhook with specific retry count
     */
    public function withRetryCount(int $retryCount): static {
        return $this->state(fn() => [
            'retry_count' => $retryCount,
            'status' => $retryCount > 0 ? 'failed' : 'processed'
        ]);
    }

    /**
     * Create old webhook (for cleanup testing)
     */
    public function old(int $daysOld = 10): static {
        return $this->state(fn() => [
            'created_at' => now()->subDays($daysOld),
            'updated_at' => now()->subDays($daysOld)
        ]);
    }

    /**
     * Create Stripe webhook
     */
    public function stripe(): static {
        return $this->state(fn() => [
            'psp' => 'stripe',
            'webhook_id' => 'wh_' . $this->faker->uuid()
        ]);
    }

    /**
     * Create PayPal webhook
     */
    public function paypal(): static {
        return $this->state(fn() => [
            'psp' => 'paypal',
            'webhook_id' => 'WH-' . strtoupper($this->faker->uuid())
        ]);
    }
}
