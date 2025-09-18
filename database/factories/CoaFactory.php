<?php

namespace Database\Factories;

use App\Models\Coa;
use App\Models\Egi;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Coa>
 */
class CoaFactory extends Factory
{
    protected $model = Coa::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $year = $this->faker->year();
        $counter = $this->faker->numberBetween(1, 999);
        
        return [
            'egi_id' => Egi::factory(),
            'serial' => sprintf('COA-EGI-%s-%06d', $year, $counter),
            'status' => 'valid',
            'issuer_type' => $this->faker->randomElement(['author', 'archive', 'platform']),
            'issuer_name' => $this->faker->name(),
            'issuer_location' => $this->faker->city() . ', ' . $this->faker->country(),
            'issued_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'revoked_at' => null,
            'revoke_reason' => null,
        ];
    }

    /**
     * Indicate that the CoA is revoked.
     */
    public function revoked(string $reason = 'Test revocation'): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'revoked',
            'revoked_at' => $this->faker->dateTimeBetween($attributes['issued_at'], 'now'),
            'revoke_reason' => $reason,
        ]);
    }

    /**
     * Set specific issuer type.
     */
    public function issuerType(string $type): static
    {
        return $this->state(fn (array $attributes) => [
            'issuer_type' => $type,
        ]);
    }

    /**
     * Set specific serial format.
     */
    public function withSerial(string $serial): static
    {
        return $this->state(fn (array $attributes) => [
            'serial' => $serial,
        ]);
    }
}
