<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * 📜 Oracode Eloquent Model: Utility
 * Represents a utility associated with an EGI, providing additional value
 * beyond the digital asset (physical goods, services, digital content, or hybrid).
 *
 * @package     App\Models
 * @version     1.0.0 (FlorenceEGI - Utility System)
 * @author      Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @copyright   2025 Fabio Cherici
 * @license     Proprietary
 *
 * @purpose     Manages utility data for EGIs including shipping information,
 *              service details, escrow configuration, and media gallery.
 *              Enables creators to add real-world value to their NFTs.
 *
 * @context     Used by UtilityManager component, controllers, and services
 *              to handle utility creation, modification, and display.
 *
 * @state       Represents the state of a single row in the 'utilities' table.
 *
 * @property int $id Primary key
 * @property int $egi_id Foreign key to EGI (unique relationship)
 * @property string $type Utility type: physical, service, hybrid, digital
 * @property string $title Title of the utility
 * @property string|null $description Detailed description
 * @property string $status Status of the utility (active, inactive, etc.)
 * @property bool $requires_shipping Whether shipping is required
 * @property string|null $shipping_type Type of shipping
 * @property int|null $estimated_shipping_days Estimated shipping time
 * @property float|null $weight Weight in kg for physical items
 * @property array|null $dimensions Length, width, height in cm
 * @property bool $fragile Whether item is fragile
 * @property bool $insurance_recommended Whether insurance is recommended
 * @property string|null $shipping_notes Special shipping instructions
 * @property string $escrow_tier Escrow tier: immediate, standard, premium
 * @property date|null $valid_from Service validity start date
 * @property date|null $valid_until Service validity end date
 * @property int|null $max_uses Maximum number of service uses
 * @property int $current_uses Current number of service uses
 * @property string|null $activation_instructions Service activation instructions
 * @property array|null $metadata Additional metadata in JSON format
 */
class Utility extends Model implements HasMedia {
    use InteractsWithMedia;

    protected $fillable = [
        'egi_id',
        'type',
        'title',
        'description',
        'status',
        'requires_shipping',
        'shipping_type',
        'estimated_shipping_days',
        'weight',
        'dimensions',
        'fragile',
        'insurance_recommended',
        'shipping_notes',
        'escrow_tier',
        'valid_from',
        'valid_until',
        'max_uses',
        'current_uses',
        'activation_instructions',
        'activation_instructions',
        'metadata',
        'requires_fulfillment', // P0 Commerce: Fulfillment flag (deprecates requires_shipping)
    ];

    protected $casts = [
        'dimensions' => 'array',
        'metadata' => 'array',
        'fragile' => 'boolean',
        'insurance_recommended' => 'boolean',
        'requires_shipping' => 'boolean',
        'requires_fulfillment' => 'boolean', // P0 Commerce
        'valid_from' => 'date',
        'valid_until' => 'date'
    ];

    /**
     * Boot method - auto-set values
     */
    protected static function boot() {
        parent::boot();

        static::creating(function ($utility) {
            // Auto-set requires_shipping based on type
            if (in_array($utility->type, ['physical', 'hybrid'])) {
                $utility->requires_shipping = true;
            }

            // Calculate escrow tier based on EGI price
            $utility->escrow_tier = $utility->calculateEscrowTier();
        });
    }

    /**
     * Relationship to EGI
     */
    public function egi() {
        return $this->belongsTo(Egi::class);
    }

    /**
     * Register media collections
     */
    public function registerMediaCollections(): void {
        $this->addMediaCollection('utility_gallery')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->useDisk('public')
            ->useFallbackUrl('/images/no-image.jpg');

        $this->addMediaCollection('utility_documents')
            ->acceptsMimeTypes(['application/pdf'])
            ->useDisk('public');
    }

    /**
     * Register media conversions for image optimization
     */
    public function registerMediaConversions(\Spatie\MediaLibrary\MediaCollections\Models\Media $media = null): void {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->sharpen(10)
            ->optimize()
            ->performOnCollections('utility_gallery');

        $this->addMediaConversion('medium')
            ->width(600)
            ->height(600)
            ->sharpen(10)
            ->optimize()
            ->performOnCollections('utility_gallery');

        $this->addMediaConversion('large')
            ->width(1200)
            ->height(1200)
            ->sharpen(10)
            ->optimize()
            ->performOnCollections('utility_gallery');
    }

    /**
     * Calculate escrow tier based on EGI price
     */
    public function calculateEscrowTier(): string {
        $price = $this->egi->price ?? 0;

        if ($price < 100) {
            return 'immediate';
        } elseif ($price <= 2000) {
            return 'standard';
        } else {
            return 'premium';
        }
    }
}
