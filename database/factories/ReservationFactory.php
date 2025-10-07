<?php

namespace Database\Factories;

use App\Models\Egi;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory {
    protected $model = Reservation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array {
        $offerEur = $this->faker->randomFloat(2, 10, 5000);

        return [
            'user_id' => User::factory(),
            'egi_id' => Egi::factory(),
            'type' => $this->faker->randomElement(['weak', 'strong']),
            'status' => 'active',
            'offer_amount_fiat' => $offerEur,
            'amount_eur' => $offerEur, // Canonical amount in EUR
            'input_amount' => $offerEur, // Input amount
            'input_currency' => 'EUR',
            'input_timestamp' => now(),
            'offer_amount_algo' => $offerEur * 0.5, // Conversione approssimativa
            'is_current' => true,
            'expires_at' => now()->addDays(30),
        ];
    }

    /**
     * Stato per prenotazione attiva e corrente
     */
    public function active() {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'active',
                'is_current' => true,
            ];
        });
    }

    /**
     * Stato per prenotazione strong
     */
    public function strong() {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'strong',
            ];
        });
    }

    /**
     * Stato per prenotazione weak
     */
    public function weak() {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'weak',
            ];
        });
    }
}
