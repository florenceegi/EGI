<?php

namespace Database\Factories;

use App\Models\CoaEvent;
use App\Models\Coa;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CoaEvent>
 */
class CoaEventFactory extends Factory
{
    protected $model = CoaEvent::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $eventType = $this->faker->randomElement([
            'coa_issued', 'coa_viewed', 'coa_verified', 'coa_revoked',
            'annex_added', 'annex_updated', 'signature_added',
            'file_uploaded', 'ownership_transferred', 'condition_updated'
        ]);
        
        return [
            'coa_id' => Coa::factory(),
            'user_id' => User::factory()->optional(),
            'event_type' => $eventType,
            'description' => $this->generateDescription($eventType),
            'event_data' => $this->generateEventData($eventType),
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
            'occurred_at' => $this->faker->dateTimeThisYear(),
        ];
    }

    /**
     * Generate human-readable description for event type.
     */
    private function generateDescription(string $eventType): string
    {
        return match($eventType) {
            'coa_issued' => 'Certificate of Authenticity issued',
            'coa_viewed' => 'Certificate viewed by ' . $this->faker->randomElement(['owner', 'potential buyer', 'expert', 'public']),
            'coa_verified' => 'Certificate authenticity verified',
            'coa_revoked' => 'Certificate revoked due to ' . $this->faker->randomElement(['error', 'fraud', 'request', 'update']),
            'annex_added' => 'New annex added: ' . $this->faker->randomElement(['Provenance', 'Condition', 'Exhibition', 'Photography']),
            'annex_updated' => 'Annex updated: ' . $this->faker->randomElement(['Provenance', 'Condition', 'Exhibition', 'Photography']),
            'signature_added' => 'Digital signature added by ' . $this->faker->name(),
            'file_uploaded' => 'Supporting document uploaded: ' . $this->faker->words(3, true),
            'ownership_transferred' => 'Ownership transferred to ' . $this->faker->name(),
            'condition_updated' => 'Condition assessment updated',
            default => 'System event: ' . $eventType
        };
    }

    /**
     * Generate event-specific data.
     */
    private function generateEventData(string $eventType): array
    {
        $baseData = [
            'timestamp' => $this->faker->iso8601(),
            'session_id' => $this->faker->uuid(),
        ];

        return match($eventType) {
            'coa_issued' => array_merge($baseData, [
                'issuer_name' => $this->faker->name(),
                'issuer_organization' => $this->faker->company(),
                'issue_reason' => $this->faker->randomElement(['new_artwork', 'ownership_change', 'update_required']),
                'initial_traits_hash' => hash('sha256', $this->faker->text()),
            ]),
            
            'coa_viewed' => array_merge($baseData, [
                'viewer_type' => $this->faker->randomElement(['public', 'authenticated', 'owner', 'expert']),
                'view_duration' => $this->faker->numberBetween(10, 300), // seconds
                'sections_viewed' => $this->faker->randomElements(['basic_info', 'provenance', 'condition', 'files'], 2),
                'referrer' => $this->faker->optional()->url(),
            ]),
            
            'coa_verified' => array_merge($baseData, [
                'verification_method' => $this->faker->randomElement(['qr_scan', 'serial_lookup', 'url_access']),
                'verification_result' => 'valid',
                'signature_check' => $this->faker->boolean(90),
                'hash_verification' => $this->faker->boolean(95),
            ]),
            
            'coa_revoked' => array_merge($baseData, [
                'revocation_reason' => $this->faker->randomElement(['data_error', 'fraud_detected', 'owner_request', 'system_upgrade']),
                'revoked_by' => $this->faker->name(),
                'replacement_coa' => $this->faker->optional()->uuid(),
                'notification_sent' => $this->faker->boolean(80),
            ]),
            
            'annex_added' => array_merge($baseData, [
                'annex_type' => $this->faker->randomElement(['A_PROVENANCE', 'B_CONDITION', 'C_EXHIBITIONS', 'D_PHOTOS']),
                'annex_version' => 1,
                'added_by' => $this->faker->name(),
                'data_size' => $this->faker->numberBetween(1024, 102400), // bytes
            ]),
            
            'annex_updated' => array_merge($baseData, [
                'annex_type' => $this->faker->randomElement(['A_PROVENANCE', 'B_CONDITION', 'C_EXHIBITIONS', 'D_PHOTOS']),
                'old_version' => $this->faker->numberBetween(1, 5),
                'new_version' => $this->faker->numberBetween(2, 6),
                'updated_by' => $this->faker->name(),
                'change_summary' => $this->faker->sentence(),
            ]),
            
            'signature_added' => array_merge($baseData, [
                'signature_type' => $this->faker->randomElement(['digital', 'physical']),
                'signer_name' => $this->faker->name(),
                'signer_role' => $this->faker->randomElement(['owner', 'expert', 'witness', 'authority']),
                'signature_algorithm' => $this->faker->randomElement(['RSA-SHA256', 'ECDSA', 'manual']),
            ]),
            
            'file_uploaded' => array_merge($baseData, [
                'file_type' => $this->faker->randomElement(['certificate', 'attachment', 'evidence', 'signature_image']),
                'file_name' => $this->faker->words(3, true) . '.' . $this->faker->randomElement(['pdf', 'jpg', 'png', 'doc']),
                'file_size' => $this->faker->numberBetween(1024, 10485760),
                'mime_type' => $this->faker->randomElement(['application/pdf', 'image/jpeg', 'image/png', 'application/msword']),
                'uploaded_by' => $this->faker->name(),
            ]),
            
            'ownership_transferred' => array_merge($baseData, [
                'previous_owner' => $this->faker->name(),
                'new_owner' => $this->faker->name(),
                'transfer_method' => $this->faker->randomElement(['sale', 'gift', 'inheritance', 'exchange']),
                'transfer_date' => $this->faker->date(),
                'documentation' => $this->faker->optional()->sentence(),
            ]),
            
            'condition_updated' => array_merge($baseData, [
                'previous_condition' => $this->faker->randomElement(['Excellent', 'Good', 'Fair', 'Poor']),
                'new_condition' => $this->faker->randomElement(['Excellent', 'Good', 'Fair', 'Poor']),
                'assessor' => $this->faker->name(),
                'assessment_date' => $this->faker->date(),
                'notes' => $this->faker->optional()->sentence(),
            ]),
            
            default => $baseData
        };
    }

    /**
     * Create event of specific type.
     */
    public function ofType(string $eventType): static
    {
        return $this->state(fn (array $attributes) => [
            'event_type' => $eventType,
            'description' => $this->generateDescription($eventType),
            'event_data' => $this->generateEventData($eventType),
        ]);
    }

    /**
     * Create CoA issuance event.
     */
    public function coaIssued(): static
    {
        return $this->ofType('coa_issued');
    }

    /**
     * Create CoA view event.
     */
    public function coaViewed(): static
    {
        return $this->ofType('coa_viewed');
    }

    /**
     * Create verification event.
     */
    public function coaVerified(): static
    {
        return $this->ofType('coa_verified');
    }

    /**
     * Create revocation event.
     */
    public function coaRevoked(): static
    {
        return $this->ofType('coa_revoked');
    }

    /**
     * Create annex addition event.
     */
    public function annexAdded(string $annexType = null): static
    {
        $state = ['event_type' => 'annex_added'];
        
        if ($annexType) {
            $eventData = $this->generateEventData('annex_added');
            $eventData['annex_type'] = $annexType;
            $state['event_data'] = $eventData;
            $state['description'] = 'New annex added: ' . str_replace(['A_', 'B_', 'C_', 'D_'], '', $annexType);
        } else {
            $state['description'] = $this->generateDescription('annex_added');
            $state['event_data'] = $this->generateEventData('annex_added');
        }
        
        return $this->state(fn (array $attributes) => $state);
    }

    /**
     * Create signature addition event.
     */
    public function signatureAdded(): static
    {
        return $this->ofType('signature_added');
    }

    /**
     * Create file upload event.
     */
    public function fileUploaded(): static
    {
        return $this->ofType('file_uploaded');
    }

    /**
     * Create event with anonymous user.
     */
    public function anonymous(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => null,
        ]);
    }

    /**
     * Create recent event.
     */
    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'occurred_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
        ]);
    }
}
