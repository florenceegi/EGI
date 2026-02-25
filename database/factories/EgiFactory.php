<?php

namespace Database\Factories;

use App\Models\Collection;
use App\Models\Egi;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Egi>
 */
class EgiFactory extends Factory {
    protected $model = Egi::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array {
        return [
            'collection_id' => Collection::factory(),
            'user_id' => User::factory(),
            'title' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            // 'payment_by_egili' rimosso — ToS v3.0.0: colonna droppa con migration A1 (2026-02-25)
            'is_published' => false,
            'mint' => false,
            'extension' => 'jpg',
            'media' => false,
        ];
    }

    /**
     * Stato per EGI pubblicato
     */
    public function published() {
        return $this->state(function (array $attributes) {
            return [
                'is_published' => true,
            ];
        });
    }
}
