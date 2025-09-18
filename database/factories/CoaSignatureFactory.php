<?php

namespace Database\Factories;

use App\Models\CoaSignature;
use App\Models\Coa;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CoaSignature>
 */
class CoaSignatureFactory extends Factory
{
    protected $model = CoaSignature::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(['digital', 'physical']);

        return [
            'coa_id' => Coa::factory(),
            'user_id' => User::factory(),
            'type' => $type,
            'signature_data' => $this->generateSignatureData($type),
            'algorithm' => $type === 'digital' ? 'RSA-SHA256' : null,
            'certificate_data' => $type === 'digital' ? $this->generateCertificateData() : null,
            'signed_at' => $this->faker->dateTimeThisYear(),
            'expires_at' => $type === 'digital' ? $this->faker->dateTimeBetween('now', '+2 years') : null,
            'metadata' => $this->generateMetadata($type),
        ];
    }

    /**
     * Generate signature data based on type.
     */
    private function generateSignatureData(string $type): string
    {
        if ($type === 'digital') {
            // Simulate a base64 encoded digital signature
            return base64_encode($this->faker->sha256() . $this->faker->sha256());
        } else {
            // For physical signatures, this might be a reference or hash
            return hash('sha256', $this->faker->name() . $this->faker->dateTime()->format('Y-m-d H:i:s'));
        }
    }

    /**
     * Generate certificate data for digital signatures.
     */
    private function generateCertificateData(): array
    {
        return [
            'issuer' => [
                'cn' => $this->faker->company(),
                'o' => $this->faker->company(),
                'c' => $this->faker->countryCode(),
            ],
            'subject' => [
                'cn' => $this->faker->name(),
                'email' => $this->faker->email(),
                'o' => $this->faker->optional()->company(),
            ],
            'serial_number' => $this->faker->numerify('################'),
            'not_before' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s'),
            'not_after' => $this->faker->dateTimeBetween('now', '+2 years')->format('Y-m-d H:i:s'),
            'fingerprint' => strtoupper($this->faker->sha1()),
            'key_usage' => ['digital_signature', 'non_repudiation'],
        ];
    }

    /**
     * Generate metadata based on signature type.
     */
    private function generateMetadata(string $type): array
    {
        $baseMetadata = [
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
            'timestamp' => $this->faker->iso8601(),
        ];

        if ($type === 'digital') {
            return array_merge($baseMetadata, [
                'signing_device' => $this->faker->randomElement(['desktop', 'tablet', 'mobile']),
                'browser' => $this->faker->randomElement(['Chrome', 'Firefox', 'Safari', 'Edge']),
                'tsa_timestamp' => $this->faker->optional()->iso8601(),
                'validation_method' => $this->faker->randomElement(['pkcs7', 'pdf_signature', 'xml_dsig']),
            ]);
        } else {
            return array_merge($baseMetadata, [
                'witness_name' => $this->faker->optional()->name(),
                'location' => $this->faker->city() . ', ' . $this->faker->country(),
                'signature_method' => $this->faker->randomElement(['ink', 'stamp', 'embossed_seal']),
                'document_state' => $this->faker->randomElement(['original', 'certified_copy']),
            ]);
        }
    }

    /**
     * Create digital signature.
     */
    public function digital(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'digital',
            'algorithm' => 'RSA-SHA256',
            'certificate_data' => $this->generateCertificateData(),
            'expires_at' => $this->faker->dateTimeBetween('now', '+2 years'),
            'signature_data' => $this->generateSignatureData('digital'),
            'metadata' => $this->generateMetadata('digital'),
        ]);
    }

    /**
     * Create physical signature.
     */
    public function physical(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'physical',
            'algorithm' => null,
            'certificate_data' => null,
            'expires_at' => null,
            'signature_data' => $this->generateSignatureData('physical'),
            'metadata' => $this->generateMetadata('physical'),
        ]);
    }

    /**
     * Create expired signature.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => $this->faker->dateTimeBetween('-1 year', '-1 day'),
        ]);
    }

    /**
     * Create signature with specific algorithm.
     */
    public function withAlgorithm(string $algorithm): static
    {
        return $this->state(fn (array $attributes) => [
            'algorithm' => $algorithm,
        ]);
    }
}