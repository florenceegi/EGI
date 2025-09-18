<?php

namespace Database\Factories;

use App\Models\CoaFile;
use App\Models\Coa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CoaFile>
 */
class CoaFileFactory extends Factory
{
    protected $model = CoaFile::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fileTypes = ['pdf', 'jpg', 'png', 'doc', 'docx'];
        $fileType = $this->faker->randomElement($fileTypes);
        $filename = $this->faker->slug() . '.' . $fileType;
        
        return [
            'coa_id' => Coa::factory(),
            'type' => $this->faker->randomElement(['certificate', 'attachment', 'evidence', 'signature_image']),
            'filename' => $filename,
            'original_name' => $this->faker->words(3, true) . '.' . $fileType,
            'mime_type' => $this->getMimeType($fileType),
            'size' => $this->faker->numberBetween(1024, 10485760), // 1KB to 10MB
            'path' => 'coa/files/' . date('Y/m') . '/' . $filename,
            'hash' => hash('sha256', $this->faker->text(1000)),
            'metadata' => $this->generateMetadata($fileType),
            'uploaded_at' => $this->faker->dateTimeThisYear(),
        ];
    }

    /**
     * Get MIME type for file extension.
     */
    private function getMimeType(string $extension): string
    {
        return match($extension) {
            'pdf' => 'application/pdf',
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            default => 'application/octet-stream'
        };
    }

    /**
     * Generate metadata based on file type.
     */
    private function generateMetadata(string $fileType): array
    {
        $baseMetadata = [
            'uploaded_by' => $this->faker->name(),
            'upload_timestamp' => $this->faker->iso8601(),
        ];

        return match($fileType) {
            'jpg', 'jpeg', 'png' => array_merge($baseMetadata, [
                'width' => $this->faker->numberBetween(800, 4000),
                'height' => $this->faker->numberBetween(600, 3000),
                'dpi' => $this->faker->randomElement([72, 150, 300, 600]),
                'color_space' => $this->faker->randomElement(['RGB', 'CMYK', 'Grayscale']),
                'camera_model' => $this->faker->optional()->randomElement(['Canon EOS R5', 'Nikon D850', 'Sony A7R IV']),
                'lens' => $this->faker->optional()->randomElement(['24-70mm f/2.8', '50mm f/1.4', '85mm f/1.8']),
            ]),
            
            'pdf' => array_merge($baseMetadata, [
                'pages' => $this->faker->numberBetween(1, 50),
                'version' => $this->faker->randomElement(['1.4', '1.5', '1.6', '1.7']),
                'encrypted' => $this->faker->boolean(20),
                'has_signatures' => $this->faker->boolean(30),
            ]),
            
            default => $baseMetadata
        };
    }

    /**
     * Create file of specific type.
     */
    public function ofType(string $type): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => $type,
        ]);
    }

    /**
     * Create image file.
     */
    public function image(): static
    {
        $extension = $this->faker->randomElement(['jpg', 'png']);
        $filename = $this->faker->slug() . '.' . $extension;
        
        return $this->state(fn (array $attributes) => [
            'filename' => $filename,
            'original_name' => $this->faker->words(3, true) . '.' . $extension,
            'mime_type' => $this->getMimeType($extension),
            'path' => 'coa/files/' . date('Y/m') . '/' . $filename,
            'metadata' => $this->generateMetadata($extension),
        ]);
    }

    /**
     * Create PDF file.
     */
    public function pdf(): static
    {
        $filename = $this->faker->slug() . '.pdf';
        
        return $this->state(fn (array $attributes) => [
            'filename' => $filename,
            'original_name' => $this->faker->words(3, true) . '.pdf',
            'mime_type' => 'application/pdf',
            'path' => 'coa/files/' . date('Y/m') . '/' . $filename,
            'metadata' => $this->generateMetadata('pdf'),
        ]);
    }

    /**
     * Create large file.
     */
    public function large(): static
    {
        return $this->state(fn (array $attributes) => [
            'size' => $this->faker->numberBetween(10485760, 104857600), // 10MB to 100MB
        ]);
    }
}
