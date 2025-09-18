<?php

namespace Database\Factories;

use App\Models\CoaAnnex;
use App\Models\Coa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CoaAnnex>
 */
class CoaAnnexFactory extends Factory
{
    protected $model = CoaAnnex::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(['A_PROVENANCE', 'B_CONDITION', 'C_EXHIBITIONS', 'D_PHOTOS']);
        
        return [
            'coa_id' => Coa::factory(),
            'type' => $type,
            'version' => 1,
            'status' => 'active',
            'data' => $this->generateDataForType($type),
            'hash' => null, // Will be calculated after creation
            'issued_by' => $this->faker->name(),
            'supersedes_version' => null,
            'issued_at' => $this->faker->dateTimeThisYear(),
        ];
    }

    /**
     * Generate type-specific data.
     */
    private function generateDataForType(string $type): array
    {
        return match($type) {
            'A_PROVENANCE' => [
                'ownership_history' => [
                    [
                        'owner' => $this->faker->name(),
                        'from_date' => $this->faker->date(),
                        'to_date' => $this->faker->optional()->date(),
                        'acquisition_method' => $this->faker->randomElement(['purchase', 'inheritance', 'gift', 'commission']),
                        'documentation' => $this->faker->optional()->sentence(),
                    ]
                ],
                'creation_details' => [
                    'artist_studio' => $this->faker->optional()->company(),
                    'commission_details' => $this->faker->optional()->sentence(),
                    'original_purpose' => $this->faker->optional()->sentence(),
                ],
                'authenticity_markers' => [
                    'signature_location' => $this->faker->optional()->words(3, true),
                    'hidden_marks' => $this->faker->optional()->sentence(),
                    'material_analysis' => $this->faker->optional()->sentence(),
                ]
            ],
            
            'B_CONDITION' => [
                'condition_reports' => [
                    [
                        'date' => $this->faker->date(),
                        'inspector' => $this->faker->name(),
                        'overall_condition' => $this->faker->randomElement(['Excellent', 'Good', 'Fair', 'Poor']),
                        'detailed_notes' => $this->faker->paragraph(),
                        'damages' => $this->faker->optional()->sentences(2),
                        'restorations' => $this->faker->optional()->sentences(1),
                    ]
                ],
                'conservation_history' => $this->faker->optional()->sentences(2),
                'storage_conditions' => [
                    'environment' => $this->faker->randomElement(['climate_controlled', 'standard', 'archival']),
                    'light_exposure' => $this->faker->randomElement(['minimal', 'moderate', 'high']),
                    'handling_frequency' => $this->faker->randomElement(['rare', 'occasional', 'frequent']),
                ],
            ],
            
            'C_EXHIBITIONS' => [
                'exhibition_history' => [
                    [
                        'title' => $this->faker->sentence(4),
                        'venue' => $this->faker->company(),
                        'location' => $this->faker->city() . ', ' . $this->faker->country(),
                        'start_date' => $this->faker->date(),
                        'end_date' => $this->faker->date(),
                        'curator' => $this->faker->name(),
                        'catalog_number' => $this->faker->optional()->numerify('###'),
                    ]
                ],
                'publications' => [
                    [
                        'title' => $this->faker->sentence(),
                        'author' => $this->faker->name(),
                        'publication_date' => $this->faker->date(),
                        'pages' => $this->faker->optional()->numerify('##-##'),
                        'isbn' => $this->faker->optional()->isbn13(),
                    ]
                ],
                'critical_reception' => $this->faker->optional()->paragraphs(2),
            ],
            
            'D_PHOTOS' => [
                'documentation_photos' => [
                    'overall_views' => [
                        [
                            'filename' => $this->faker->uuid() . '.jpg',
                            'taken_date' => $this->faker->date(),
                            'photographer' => $this->faker->name(),
                            'lighting_conditions' => $this->faker->randomElement(['natural', 'studio', 'raking_light']),
                            'resolution' => $this->faker->randomElement(['300dpi', '600dpi', '1200dpi']),
                        ]
                    ],
                    'detail_shots' => [
                        [
                            'filename' => $this->faker->uuid() . '.jpg',
                            'area_documented' => $this->faker->words(3, true),
                            'magnification' => $this->faker->randomElement(['1x', '2x', '5x', '10x']),
                            'purpose' => $this->faker->randomElement(['signature', 'damage', 'technique', 'restoration']),
                        ]
                    ],
                ],
                'technical_photography' => [
                    'uv_photography' => $this->faker->optional()->boolean(),
                    'infrared_photography' => $this->faker->optional()->boolean(),
                    'xray_photography' => $this->faker->optional()->boolean(),
                    'raking_light' => $this->faker->optional()->boolean(),
                ],
                'photo_standards' => [
                    'color_calibration' => $this->faker->boolean(),
                    'metadata_embedded' => $this->faker->boolean(),
                    'raw_files_preserved' => $this->faker->boolean(),
                ],
            ],
            
            default => ['data' => $this->faker->words(5, true)]
        };
    }

    /**
     * Create annex of specific type.
     */
    public function ofType(string $type): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => $type,
            'data' => $this->generateDataForType($type),
        ]);
    }

    /**
     * Create annex with specific version.
     */
    public function version(int $version, ?int $supersedes = null): static
    {
        return $this->state(fn (array $attributes) => [
            'version' => $version,
            'supersedes_version' => $supersedes,
        ]);
    }

    /**
     * Create superseded annex.
     */
    public function superseded(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'superseded',
        ]);
    }

    /**
     * Create revoked annex.
     */
    public function revoked(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'revoked',
        ]);
    }
}
