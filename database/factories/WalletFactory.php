<?php

namespace Database\Factories;

use App\Models\Wallet;
use App\Models\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;

class WalletFactory extends Factory
{
    protected $model = Wallet::class;

    public function definition()
    {
        return [
            'collection_id' => Collection::factory(),
            'platform_role' => $this->faker->randomElement(['Frangette', 'Mediator', 'Creator']),
            'wallet' => $this->faker->unique()->regexify('[a-zA-Z0-9]{42}'),
            'royalty_mint' => $this->faker->randomFloat(2, 0, 50),
            'royalty_rebind' => $this->faker->randomFloat(2, 0, 10),
            'egili_balance' => $this->faker->numberBetween(0, 10000),
            'egili_lifetime_earned' => $this->faker->numberBetween(0, 50000),
            'egili_lifetime_spent' => $this->faker->numberBetween(0, 40000),
        ];
    }
}
