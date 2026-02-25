<?php

namespace Database\Factories;

use App\Models\EgiliTransaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @Oracode Factory: EgiliTransaction Factory
 * 🎯 Purpose: Generate test data for EgiliTransaction model
 * 🧱 Core Logic: Realistic Egili transaction scenarios
 * 🛡️ Testing: Support unit and integration tests
 * 
 * @package Database\Factories
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.1.0 (FlorenceEGI - Egili Credit System)
 * @date 2025-11-01
 * @extends Factory<EgiliTransaction>
 */
class EgiliTransactionFactory extends Factory {
    protected $model = EgiliTransaction::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array {
        $operation = $this->faker->randomElement(['add', 'subtract']);
        $transactionType = $operation === 'add'
            ? $this->faker->randomElement(['earned', 'admin_grant', 'purchase', 'initial_bonus'])
            : $this->faker->randomElement(['spent', 'admin_deduct']);

        $balanceBefore = $this->faker->numberBetween(0, 10000);
        $amount = $this->faker->numberBetween(10, 500);
        $balanceAfter = $operation === 'add'
            ? $balanceBefore + $amount
            : max(0, $balanceBefore - $amount);

        return [
            'wallet_id' => Wallet::factory(),
            'user_id' => User::factory(),
            'transaction_type' => $transactionType,
            'operation' => $operation,
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'source_type' => null,
            'source_id' => null,
            'reason' => $this->faker->randomElement([
                'egi_sale_cashback',
                'living_subscription_payment',
                'fee_discount',
                'milestone_bonus',
                'daily_login',
            ]),
            'category' => $this->faker->randomElement([
                'trading',
                'service',
                'milestone',
                'gamification',
                'admin',
            ]),
            'metadata' => null,
            'admin_user_id' => null,
            'admin_notes' => null,
            'status' => 'completed',
            'error_message' => null,
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
        ];
    }

    /**
     * State: Earned transaction
     */
    public function earned(): static {
        return $this->state(fn(array $attributes) => [
            'transaction_type' => 'earned',
            'operation' => 'add',
        ]);
    }

    /**
     * State: Spent transaction
     */
    public function spent(): static {
        return $this->state(fn(array $attributes) => [
            'transaction_type' => 'spent',
            'operation' => 'subtract',
        ]);
    }

    /**
     * State: Failed transaction
     */
    public function failed(): static {
        return $this->state(fn(array $attributes) => [
            'status' => 'failed',
            'error_message' => $this->faker->sentence(),
        ]);
    }

    /**
     * State: Admin granted bonus
     */
    public function adminGrant(): static {
        return $this->state(fn(array $attributes) => [
            'transaction_type' => 'admin_grant',
            'operation' => 'add',
            'category' => 'admin',
            'admin_user_id' => User::factory(),
            'admin_notes' => $this->faker->sentence(),
        ]);
    }
}
