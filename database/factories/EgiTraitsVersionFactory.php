<?php

namespace Database\Factories;

use App\Models\EgiTraitsVersion;
use App\Models\Egi;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EgiTraitsVersion>
 */
class EgiTraitsVersionFactory extends Factory
{
    protected $model = EgiTraitsVersion::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $traitsData = $this->generateTraitsData();
        
        return [
            'egi_id' => Egi::factory(),
            'version' => $this->faker->numberBetween(1, 10),
            'traits_data' => $traitsData,
            'traits_hash' => hash('sha256', json_encode($traitsData)),
            'change_reason' => $this->faker->randomElement([
                'Initial creation',
                'Updated description',
                'Added exhibition history',
                'Condition assessment update',
                'Provenance verification',
                'Technical analysis results',
                'Restoration documentation',
                'Market value update'
            ]),
            'changed_fields' => $this->faker->randomElements([
                'title', 'description', 'author', 'year', 'technique', 'dimensions',
                'style', 'subject', 'colors', 'materials', 'provenance', 'exhibitions',
                'condition', 'rarity_score', 'market_value', 'insurance_value'
            ], $this->faker->numberBetween(1, 4)),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }

    /**
     * Generate comprehensive traits data for an EGI.
     */
    private function generateTraitsData(): array
    {
        return [
            // Basic Information
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(3),
            'author' => $this->faker->name(),
            'year' => $this->faker->year(),
            
            // Technical Details
            'technique' => $this->faker->randomElement([
                'Oil on canvas', 'Acrylic on canvas', 'Watercolor on paper',
                'Digital art', 'Mixed media', 'Photography', 'Sculpture',
                'Ink on paper', 'Pastel on paper', 'Charcoal on paper'
            ]),
            'dimensions' => [
                'width' => $this->faker->numberBetween(10, 200),
                'height' => $this->faker->numberBetween(10, 200),
                'depth' => $this->faker->optional()->numberBetween(1, 50),
                'unit' => 'cm',
                'weight' => $this->faker->optional()->numberBetween(100, 5000), // grams
            ],
            
            // Artistic Properties
            'style' => $this->faker->randomElement([
                'Abstract', 'Realistic', 'Impressionist', 'Contemporary',
                'Classical', 'Surreal', 'Minimalist', 'Expressionist'
            ]),
            'subject' => $this->faker->randomElement([
                'Portrait', 'Landscape', 'Still Life', 'Abstract',
                'Figure', 'Architecture', 'Nature', 'Urban Scene'
            ]),
            'colors' => $this->faker->randomElements([
                'red', 'blue', 'green', 'yellow', 'purple', 'orange',
                'black', 'white', 'brown', 'pink', 'gray', 'turquoise'
            ], $this->faker->numberBetween(2, 6)),
            'materials' => $this->faker->randomElements([
                'canvas', 'paper', 'wood', 'metal', 'ceramic', 'glass',
                'stone', 'fabric', 'plastic', 'leather'
            ], $this->faker->numberBetween(1, 3)),
            
            // Historical Information
            'provenance' => [
                'origin' => $this->faker->city() . ', ' . $this->faker->country(),
                'creation_story' => $this->faker->sentence(),
                'first_owner' => $this->faker->name(),
                'acquisition_method' => $this->faker->randomElement(['commission', 'purchase', 'gift', 'inheritance']),
            ],
            'exhibitions' => $this->faker->optional()->sentences(2),
            'publications' => $this->faker->optional()->sentences(1),
            
            // Condition and Conservation
            'condition' => $this->faker->randomElement(['Excellent', 'Very Good', 'Good', 'Fair', 'Poor']),
            'condition_notes' => $this->faker->optional()->sentence(),
            'conservation_history' => $this->faker->optional()->sentences(1),
            'restoration_notes' => $this->faker->optional()->sentence(),
            
            // Market and Rarity
            'rarity_score' => $this->faker->numberBetween(1, 100),
            'market_value' => [
                'currency' => 'EUR',
                'amount' => $this->faker->numberBetween(100, 50000),
                'assessment_date' => $this->faker->date(),
                'assessor' => $this->faker->name(),
            ],
            'insurance_value' => [
                'currency' => 'EUR',
                'amount' => $this->faker->numberBetween(150, 75000),
                'valid_until' => $this->faker->dateTimeBetween('now', '+2 years')->format('Y-m-d'),
            ],
            
            // Additional Metadata
            'cultural_significance' => $this->faker->optional()->randomElement(['high', 'medium', 'low']),
            'artistic_movement' => $this->faker->optional()->randomElement([
                'Renaissance', 'Baroque', 'Romanticism', 'Impressionism',
                'Modernism', 'Contemporary', 'Post-Modern'
            ]),
            'inspiration_sources' => $this->faker->optional()->words(3, true),
            'technical_notes' => $this->faker->optional()->sentence(),
            'awards' => $this->faker->optional()->sentences(1),
        ];
    }

    /**
     * Create version with specific version number.
     */
    public function version(int $version): static
    {
        return $this->state(fn (array $attributes) => [
            'version' => $version,
        ]);
    }

    /**
     * Create initial version.
     */
    public function initial(): static
    {
        return $this->state(fn (array $attributes) => [
            'version' => 1,
            'change_reason' => 'Initial creation',
            'changed_fields' => ['all'],
        ]);
    }

    /**
     * Create version with specific change reason.
     */
    public function withChangeReason(string $reason, array $fields = []): static
    {
        return $this->state(fn (array $attributes) => [
            'change_reason' => $reason,
            'changed_fields' => empty($fields) ? $attributes['changed_fields'] : $fields,
        ]);
    }

    /**
     * Create version with minimal changes.
     */
    public function minorUpdate(): static
    {
        return $this->state(fn (array $attributes) => [
            'change_reason' => 'Minor update',
            'changed_fields' => $this->faker->randomElements(['description', 'condition'], 1),
        ]);
    }

    /**
     * Create version with major changes.
     */
    public function majorUpdate(): static
    {
        return $this->state(fn (array $attributes) => [
            'change_reason' => 'Major update - comprehensive review',
            'changed_fields' => $this->faker->randomElements([
                'title', 'description', 'author', 'year', 'technique', 'dimensions',
                'provenance', 'condition', 'market_value'
            ], $this->faker->numberBetween(3, 6)),
        ]);
    }
}
