<?php

namespace Database\Factories;

use App\Models\CoaSnapshot;
use App\Models\Coa;
use App\Models\EgiTraitsVersion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CoaSnapshot>
 */
class CoaSnapshotFactory extends Factory {
    protected $model = CoaSnapshot::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array {
        return [
            'coa_id' => Coa::factory(),
            'traits_version_id' => EgiTraitsVersion::factory(),
            'traits_hash' => hash('sha256', json_encode($this->generateTraitsData())),
            'traits_data' => $this->generateTraitsData(),
            'snapshot_at' => $this->faker->dateTimeThisYear(),
        ];
    }

    /**
     * Generate realistic traits data for testing.
     */
    private function generateTraitsData(): array {
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'author' => $this->faker->name(),
            'year' => $this->faker->year(),
            'technique' => $this->faker->randomElement(['Oil on canvas', 'Acrylic', 'Watercolor', 'Digital Art', 'Mixed Media']),
            'dimensions' => [
                'width' => $this->faker->numberBetween(10, 200),
                'height' => $this->faker->numberBetween(10, 200),
                'unit' => 'cm'
            ],
            'style' => $this->faker->randomElement(['Abstract', 'Realistic', 'Impressionist', 'Contemporary', 'Classical']),
            'subject' => $this->faker->randomElement(['Portrait', 'Landscape', 'Still Life', 'Abstract', 'Figure']),
            'colors' => $this->faker->randomElements(['red', 'blue', 'green', 'yellow', 'purple', 'orange', 'black', 'white'], $this->faker->numberBetween(2, 5)),
            'materials' => $this->faker->randomElements(['canvas', 'paper', 'wood', 'metal', 'ceramic'], $this->faker->numberBetween(1, 3)),
            'provenance' => $this->faker->sentence(),
            'exhibitions' => $this->faker->optional()->sentences(2),
            'condition' => $this->faker->randomElement(['Excellent', 'Good', 'Fair', 'Poor']),
            'rarity_score' => $this->faker->numberBetween(1, 100),
        ];
    }

    /**
     * Create snapshot with specific traits data.
     */
    public function withTraitsData(array $data): static {
        return $this->state(fn(array $attributes) => [
            'traits_data' => $data,
            'traits_hash' => hash('sha256', json_encode($data)),
        ]);
    }

    /**
     * Create snapshot with custom hash.
     */
    public function withHash(string $hash): static {
        return $this->state(fn(array $attributes) => [
            'traits_hash' => $hash,
        ]);
    }
}
