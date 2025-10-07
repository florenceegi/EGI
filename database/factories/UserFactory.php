<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'), // Default password
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'remember_token' => Str::random(10),
            'profile_photo_path' => null,
            'wallet' => Str::random(42), // Genera un indirizzo wallet casuale
            'wallet_balance' => $this->faker->randomFloat(2, 0, 1000), // Saldo casuale
        ];
    }

    /**
     * Assegna il ruolo di creator all'utente dopo la creazione.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (User $user) {
            // Recupera il ruolo di creator o crealo se non esiste (senza ID fisso)
            $role = Role::firstOrCreate(
                ['name' => 'creator', 'guard_name' => 'web'],
                ['name' => 'creator', 'guard_name' => 'web']
            );

            // Assegna il ruolo all'utente
            $user->assignRole($role);
        });
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
