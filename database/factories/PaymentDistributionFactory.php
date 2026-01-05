<?php

namespace Database\Factories;

use App\Models\PaymentDistribution;
use App\Models\User;
use App\Models\Egi;
use App\Models\Wallet;
use App\Enums\PaymentDistribution\DistributionStatusEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory for PaymentDistribution model.
 * Generates test data for payment distribution testing.
 */
class PaymentDistributionFactory extends Factory {
    protected $model = PaymentDistribution::class;

    public function definition(): array {
        $egi = Egi::factory()->create();
        $wallet = Wallet::factory()->create();

        return [
            'payment_intent_id' => 'pi_' . $this->faker->uuid(),
            'wallet_id' => $wallet->id,
            'collection_id' => $egi->collection_id,
            'user_id' => $egi->user_id,
            'egi_id' => $egi->id,
            'platform_role' => $this->faker->randomElement(['creator', 'epp', 'natan', 'frangette']),
            'amount_cents' => $this->faker->numberBetween(100, 10000), // €1.00 to €100.00
            'amount_eur' => function (array $attributes) {
                return $attributes['amount_cents'] / 100; // Convert cents to euros
            },
            'exchange_rate' => $this->faker->randomFloat(4, 0.8, 1.2), // EUR to other currency rate
            'percentage' => $this->faker->randomFloat(2, 1.0, 100.0),
            'user_type' => $this->faker->randomElement(['creator', 'epp', 'company']),
            'distribution_status' => $this->faker->randomElement([
                DistributionStatusEnum::PENDING->value,
                DistributionStatusEnum::COMPLETED->value,
                DistributionStatusEnum::FAILED->value,
                DistributionStatusEnum::REVERSED->value,
                DistributionStatusEnum::REVERSAL_FAILED->value
            ]),
            'transfer_id' => function (array $attributes) {
                return in_array($attributes['distribution_status'], ['completed', 'failed'])
                    ? 'tr_' . $this->faker->uuid()
                    : null;
            },
            'reversal_id' => function (array $attributes) {
                return $attributes['distribution_status'] === 'failed'
                    ? 'trr_' . $this->faker->uuid()
                    : null;
            },
            'failure_reason' => function (array $attributes) {
                return $attributes['distribution_status'] === 'failed'
                    ? $this->faker->sentence()
                    : null;
            },
            'retry_count' => function (array $attributes) {
                return $attributes['distribution_status'] === 'failed'
                    ? $this->faker->numberBetween(0, 5)
                    : 0;
            },
            'completed_at' => function (array $attributes) {
                return $attributes['distribution_status'] === 'completed'
                    ? $this->faker->dateTimeThisMonth()
                    : null;
            },
            'reversed_at' => function (array $attributes) {
                return $attributes['distribution_status'] === 'failed'
                    ? $this->faker->dateTimeThisMonth()
                    : null;
            },
            'metadata' => [
                'egi_id' => $this->faker->numberBetween(1, 1000),
                'collection_id' => $this->faker->numberBetween(1, 100),
                'mint_type' => $this->faker->randomElement(['collection_mint', 'single_mint'])
            ]
        ];
    }

    /**
     * Create a pending distribution
     */
    public function pending(): static {
        return $this->state(fn() => [
            'distribution_status' => DistributionStatusEnum::PENDING->value,
            'transfer_id' => null,
            'reversal_id' => null,
            'completed_at' => null,
            'reversed_at' => null,
            'failure_reason' => null,
            'retry_count' => 0
        ]);
    }

    /**
     * Create a completed distribution
     */
    public function completed(): static {
        return $this->state(fn() => [
            'distribution_status' => DistributionStatusEnum::COMPLETED->value,
            'transfer_id' => 'tr_' . $this->faker->uuid(),
            'completed_at' => $this->faker->dateTimeThisMonth(),
            'reversal_id' => null,
            'reversed_at' => null,
            'failure_reason' => null,
            'retry_count' => 0
        ]);
    }

    /**
     * Create a failed distribution
     */
    public function failed(): static {
        return $this->state(fn() => [
            'distribution_status' => DistributionStatusEnum::FAILED->value,
            'transfer_id' => null,
            'completed_at' => null,
            'reversal_id' => null,
            'reversed_at' => null,
            'failure_reason' => $this->faker->sentence(),
            'retry_count' => $this->faker->numberBetween(1, 5)
        ]);
    }

    /**
     * Create a reversed distribution
     */
    public function reversed(): static {
        return $this->state(fn() => [
            'distribution_status' => DistributionStatusEnum::FAILED->value, // Use failed status as reversed might not exist
            'transfer_id' => 'tr_' . $this->faker->uuid(),
            'reversal_id' => 'trr_' . $this->faker->uuid(),
            'completed_at' => $this->faker->dateTimeThisMonth(),
            'reversed_at' => $this->faker->dateTimeThisMonth(),
            'failure_reason' => null,
            'retry_count' => 0
        ]);
    }

    /**
     * Create distribution for specific wallet
     */
    public function forWallet(int $walletId): static {
        return $this->state(fn() => ['wallet_id' => $walletId]);
    }

    /**
     * Create distribution for specific payment
     */
    public function forPayment(string $paymentIntentId): static {
        return $this->state(fn() => ['payment_intent_id' => $paymentIntentId]);
    }

    /**
     * Create distribution with specific role
     */
    public function withRole(string $role): static {
        return $this->state(fn() => ['platform_role' => $role]);
    }

    /**
     * Create distribution with specific amount
     */
    public function withAmount(int $amountCents, float $percentage): static {
        return $this->state(fn() => [
            'amount_cents' => $amountCents,
            'percentage' => $percentage
        ]);
    }
}
