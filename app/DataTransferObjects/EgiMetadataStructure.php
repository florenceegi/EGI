<?php

/**
 * @package App\DataTransferObjects
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Phase 2 Area 5)
 * @date 2025-10-09
 * @purpose DTO for EGI NFT metadata - OpenSea compatible structure
 */

namespace App\DataTransferObjects;

use Carbon\Carbon;

/**
 * EgiMetadataStructure - Data Transfer Object for NFT Metadata
 * 
 * OpenSea/NFT.Storage compatible metadata structure for EGI tokens.
 * Supports standard NFT traits, CoA (Certificate of Authenticity), 
 * IPFS references, and collection metadata.
 * 
 * @see https://docs.opensea.io/docs/metadata-standards
 */
class EgiMetadataStructure {
    /**
     * Standard NFT traits (key-value pairs)
     * Example: ['Color' => 'Blue', 'Rarity' => 'Legendary']
     */
    public array $traits = [];

    /**
     * Certificate of Authenticity specific traits
     * Example: ['Authenticity Level' => 'Verified', 'Inspector' => 'John Doe']
     */
    public array $coa_traits = [];

    /**
     * CoA reference code (if EGI has CoA)
     * Format: "COA-{collection_id}-{egi_id}-{hash}"
     */
    public ?string $coa_reference = null;

    /**
     * Creation date of the EGI
     */
    public Carbon $creation_date;

    /**
     * Edition information (e.g., "1/1" for unique, "5/100" for limited)
     */
    public string $edition = '1/1';

    /**
     * Technical specifications of the EGI asset
     * Example: ['format' => 'image/png', 'width' => 1920, 'height' => 1080]
     */
    public array $technical_specs = [];

    /**
     * IPFS CID for the image file
     */
    public ?string $ipfs_image_cid = null;

    /**
     * IPFS CID for the metadata JSON itself (self-reference after upload)
     */
    public ?string $ipfs_metadata_cid = null;

    /**
     * Collection slug identifier
     */
    public ?string $collection_slug = null;

    /**
     * Collection database ID
     */
    public ?int $collection_id = null;

    /**
     * OpenSea-compatible properties object
     * Custom properties for enhanced marketplace display
     */
    public array $properties = [];

    /**
     * OpenSea-compatible attributes array
     * Standard format: [{'trait_type': 'X', 'value': 'Y'}]
     */
    public array $attributes = [];

    /**
     * Constructor
     */
    public function __construct() {
        $this->creation_date = Carbon::now();
    }

    /**
     * Convert to array for database storage or JSON export
     * 
     * @return array
     */
    public function toArray(): array {
        return [
            'traits' => $this->traits,
            'coa_traits' => $this->coa_traits,
            'coa_reference' => $this->coa_reference,
            'creation_date' => $this->creation_date->toIso8601String(),
            'edition' => $this->edition,
            'technical_specs' => $this->technical_specs,
            'ipfs_image_cid' => $this->ipfs_image_cid,
            'ipfs_metadata_cid' => $this->ipfs_metadata_cid,
            'collection_slug' => $this->collection_slug,
            'collection_id' => $this->collection_id,
            'properties' => $this->properties,
            'attributes' => $this->attributes,
        ];
    }

    /**
     * Convert to OpenSea-compatible JSON structure
     * 
     * @param string $name NFT name (e.g., "EGI #123")
     * @param string $description NFT description
     * @param string $externalUrl External URL (e.g., "https://florenceegi.it/egi/123")
     * @return array
     */
    public function toOpenSeaFormat(string $name, string $description, string $externalUrl): array {
        return [
            'name' => $name,
            'description' => $description,
            'image' => $this->ipfs_image_cid ? "ipfs://{$this->ipfs_image_cid}" : null,
            'external_url' => $externalUrl,
            'attributes' => $this->attributes,
            'properties' => array_merge($this->properties, [
                'coa_reference' => $this->coa_reference,
                'creation_date' => $this->creation_date->toDateString(),
                'blockchain' => 'algorand',
                'edition' => $this->edition,
            ]),
        ];
    }

    /**
     * Create from array (for database retrieval)
     * 
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self {
        $instance = new self();
        
        $instance->traits = $data['traits'] ?? [];
        $instance->coa_traits = $data['coa_traits'] ?? [];
        $instance->coa_reference = $data['coa_reference'] ?? null;
        $instance->creation_date = isset($data['creation_date']) 
            ? Carbon::parse($data['creation_date']) 
            : Carbon::now();
        $instance->edition = $data['edition'] ?? '1/1';
        $instance->technical_specs = $data['technical_specs'] ?? [];
        $instance->ipfs_image_cid = $data['ipfs_image_cid'] ?? null;
        $instance->ipfs_metadata_cid = $data['ipfs_metadata_cid'] ?? null;
        $instance->collection_slug = $data['collection_slug'] ?? null;
        $instance->collection_id = $data['collection_id'] ?? null;
        $instance->properties = $data['properties'] ?? [];
        $instance->attributes = $data['attributes'] ?? [];
        
        return $instance;
    }

    /**
     * Add a standard trait
     * 
     * @param string $name Trait name
     * @param mixed $value Trait value
     * @return self
     */
    public function addTrait(string $name, $value): self {
        $this->traits[$name] = $value;
        
        // Also add to OpenSea attributes format
        $this->attributes[] = [
            'trait_type' => $name,
            'value' => $value,
        ];
        
        return $this;
    }

    /**
     * Add a CoA-specific trait
     * 
     * @param string $name Trait name
     * @param mixed $value Trait value
     * @return self
     */
    public function addCoaTrait(string $name, $value): self {
        $this->coa_traits[$name] = $value;
        
        // Also add to OpenSea attributes format with CoA prefix
        $this->attributes[] = [
            'trait_type' => "CoA: {$name}",
            'value' => $value,
        ];
        
        return $this;
    }

    /**
     * Set technical specifications
     * 
     * @param array $specs Technical specs array
     * @return self
     */
    public function setTechnicalSpecs(array $specs): self {
        $this->technical_specs = $specs;
        return $this;
    }

    /**
     * Check if metadata has CoA
     * 
     * @return bool
     */
    public function hasCoA(): bool {
        return !empty($this->coa_reference);
    }

    /**
     * Get all traits (standard + CoA)
     * 
     * @return array
     */
    public function getAllTraits(): array {
        return array_merge($this->traits, $this->coa_traits);
    }
}
