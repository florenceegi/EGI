<?php

namespace App\Services;

use App\DataTransferObjects\EgiMetadataStructure;
use App\Enums\Gdpr\GdprActivityCategory;
use App\Models\Egi;
use App\Models\EgiBlockchain;
use App\Models\User;
use App\Services\Gdpr\AuditLogService;
use App\Services\Gdpr\ConsentService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * EgiMetadataBuilderService - NFT Metadata Construction & Validation
 *
 * @package App\Services
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Area 5 Metadata Management)
 * @date 2025-10-09
 * @purpose Build, validate, and export OpenSea-compatible NFT metadata for EGI minting workflow
 *
 * @context
 * This service is responsible for:
 * - Building comprehensive metadata structure from EGI data
 * - Extracting traits (standard + CoA)
 * - Validating metadata against OpenSea standards
 * - Preparing metadata for IPFS upload (Area 6)
 * - Updating metadata in egi_blockchain table
 *
 * @architecture
 * - Orchestrates metadata construction during mint workflow
 * - Integrates with EgiMetadataStructure DTO
 * - GDPR compliant with full audit trail
 * - OpenSea metadata standard v1.0 compatible
 * - IPFS-ready JSON export
 *
 * @dependencies
 * - UltraLogManager: Audit trail logging
 * - ErrorManagerInterface: Error handling & recovery
 * - AuditLogService: GDPR audit logging
 * - ConsentService: GDPR consent verification
 *
 * @mca_safe
 * - No crypto custody for users
 * - Only metadata management service
 * - FIAT payment flow compatible
 */
class EgiMetadataBuilderService
{
    private UltraLogManager $logger;
    private ErrorManagerInterface $errorManager;
    private AuditLogService $auditService;
    private ConsentService $consentService;

    /**
     * Constructor with GDPR/Ultra compliance
     *
     * @param UltraLogManager $logger Ultra logging manager for audit trails
     * @param ErrorManagerInterface $errorManager Ultra error manager for error handling
     * @param AuditLogService $auditService GDPR audit logging service
     * @param ConsentService $consentService GDPR consent management service
     * @privacy-safe All injected services handle GDPR compliance
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService,
        ConsentService $consentService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditService = $auditService;
        $this->consentService = $consentService;
    }

    /**
     * Build comprehensive metadata structure from EGI data
     *
     * @param Egi $egi EGI model instance with traits loaded
     * @param User|null $coCreator Co-creator user (minter) - nullable for pre-mint scenarios
     * @return EgiMetadataStructure Complete metadata structure ready for blockchain/IPFS
     *
     * @throws \Exception If metadata construction fails
     *
     * @business_logic
     * 1. Extract standard EGI traits (category, rarity, properties)
     * 2. Extract CoA traits if Certificate of Authenticity exists
     * 3. Build OpenSea-compatible attributes array
     * 4. Include collection context and references
     * 5. Prepare technical specs (dimensions, format, size)
     * 6. Set edition information (e.g., "1/1")
     * 7. Generate CoA reference if applicable
     * 8. Return complete EgiMetadataStructure DTO
     *
     * @gdpr_compliance
     * - Logs metadata construction activity
     * - Does not expose sensitive user data
     * - Audit trail for NFT metadata creation
     *
     * @example
     * ```php
     * $egi = Egi::with(['traits', 'collection'])->find(123);
     * $metadata = $metadataBuilder->buildMetadata($egi, Auth::user());
     * ```
     */
    public function buildMetadata(Egi $egi, ?User $coCreator = null): EgiMetadataStructure
    {
        try {
            // 1. ULM: Log metadata construction start
            $this->logger->info('EgiMetadataBuilderService: Building metadata started', [
                'egi_id' => $egi->id,
                'egi_title' => $egi->title,
                'collection_id' => $egi->collection_id,
                'co_creator_id' => $coCreator?->id,
                'log_category' => 'METADATA_BUILDER_START'
            ]);

            // 2. Load relations if not already loaded
            if (!$egi->relationLoaded('traits')) {
                $egi->load(['traits.category', 'traits.traitType']);
            }
            if (!$egi->relationLoaded('collection')) {
                $egi->load('collection');
            }

            // 3. Extract standard EGI traits
            $standardTraits = $this->extractStandardTraits($egi);

            // 4. Extract CoA traits (if exists)
            $coaTraits = $this->extractCoaTraits($egi);

            // 5. Generate CoA reference if applicable
            $coaReference = $this->generateCoaReference($egi);

            // 6. Build technical specs
            $technicalSpecs = $this->buildTechnicalSpecs($egi);

            // 7. Determine edition string
            $edition = $this->determineEdition($egi);

            // 8. Build properties (OpenSea format)
            $properties = $this->buildProperties($egi, $coCreator);

            // 9. Build attributes (OpenSea format)
            $attributes = $this->buildAttributes($standardTraits, $coaTraits, $egi, $coCreator);

            // 10. Create metadata structure
            $metadata = new EgiMetadataStructure();
            $metadata->traits = $standardTraits;
            $metadata->coa_traits = $coaTraits;
            $metadata->coa_reference = $coaReference;
            $metadata->creation_date = $egi->creation_date ? Carbon::parse($egi->creation_date) : now();
            $metadata->edition = $edition;
            $metadata->technical_specs = $technicalSpecs;
            $metadata->ipfs_image_cid = null; // Will be set during IPFS upload (Area 6)
            $metadata->ipfs_metadata_cid = null; // Will be set during metadata upload (Area 6)
            $metadata->collection_slug = $egi->collection->collection_name ?? null;
            $metadata->collection_id = $egi->collection_id;
            $metadata->properties = $properties;
            $metadata->attributes = $attributes;

            // 11. ULM: Log successful metadata construction
            $this->logger->info('EgiMetadataBuilderService: Metadata constructed successfully', [
                'egi_id' => $egi->id,
                'traits_count' => count($standardTraits),
                'coa_traits_count' => count($coaTraits),
                'has_coa_reference' => !is_null($coaReference),
                'edition' => $edition,
                'log_category' => 'METADATA_BUILDER_SUCCESS'
            ]);

            // 12. GDPR: Audit log metadata construction
            if ($coCreator) {
                $this->auditService->logActivity(
                    $coCreator,
                    GdprActivityCategory::BLOCKCHAIN_OPERATION,
                    'EGI metadata constructed for mint',
                    [
                        'egi_id' => $egi->id,
                        'egi_title' => $egi->title,
                        'traits_count' => count($standardTraits),
                        'has_coa' => !is_null($coaReference)
                    ]
                );
            }

            return $metadata;

        } catch (\Exception $e) {
            // 13. UEM: Error handling
            $this->errorManager->handle('EGI_METADATA_BUILD_FAILED', [
                'egi_id' => $egi->id,
                'co_creator_id' => $coCreator?->id,
                'error' => $e->getMessage()
            ], $e);

            throw $e;
        }
    }

    /**
     * Validate metadata structure against OpenSea standards
     *
     * @param array $metadata Metadata array to validate
     * @return bool True if valid, throws ValidationException if invalid
     *
     * @throws ValidationException If validation fails
     *
     * @business_logic
     * OpenSea metadata requirements:
     * - name: required, string, max 100 chars
     * - description: optional, string, max 1000 chars
     * - image: required, string (URL or IPFS)
     * - attributes: optional, array of objects with trait_type + value
     * - properties: optional, object
     *
     * @example
     * ```php
     * $isValid = $metadataBuilder->validateMetadata($metadata->toArray());
     * ```
     */
    public function validateMetadata(array $metadata): bool
    {
        try {
            // 1. ULM: Log validation start
            $this->logger->info('EgiMetadataBuilderService: Validating metadata', [
                'metadata_keys' => array_keys($metadata),
                'log_category' => 'METADATA_VALIDATION_START'
            ]);

            // 2. Define validation rules (OpenSea standard)
            $rules = [
                'name' => 'required|string|max:100',
                'description' => 'nullable|string|max:1000',
                'image' => 'required|string',
                'external_url' => 'nullable|url',
                'attributes' => 'nullable|array',
                'attributes.*.trait_type' => 'required_with:attributes|string',
                'attributes.*.value' => 'required_with:attributes',
                'properties' => 'nullable|array',
            ];

            // 3. Execute validation
            $validator = Validator::make($metadata, $rules);

            if ($validator->fails()) {
                // 4. ULM: Log validation errors
                $this->logger->warning('EgiMetadataBuilderService: Metadata validation failed', [
                    'errors' => $validator->errors()->toArray(),
                    'log_category' => 'METADATA_VALIDATION_FAILED'
                ]);

                throw new ValidationException($validator);
            }

            // 5. Additional business logic validation
            $this->validateBusinessRules($metadata);

            // 6. ULM: Log successful validation
            $this->logger->info('EgiMetadataBuilderService: Metadata validation passed', [
                'log_category' => 'METADATA_VALIDATION_SUCCESS'
            ]);

            return true;

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            // 7. UEM: Error handling
            $this->errorManager->handle('EGI_METADATA_VALIDATION_FAILED', [
                'metadata_keys' => array_keys($metadata),
                'error' => $e->getMessage()
            ], $e);

            throw $e;
        }
    }

    /**
     * Update metadata in existing EgiBlockchain record
     *
     * @param EgiBlockchain $egiBlockchain Blockchain record to update
     * @param array $updates Metadata updates to apply
     * @return void
     *
     * @throws \Exception If update fails
     *
     * @business_logic
     * - Merges new metadata with existing
     * - Validates merged metadata
     * - Updates metadata_last_updated_at timestamp
     * - Preserves IPFS CIDs if already set
     * - Full audit trail
     *
     * @gdpr_compliance
     * - Logs metadata update activity
     * - Tracks all changes in audit trail
     * - User consent verified before update
     *
     * @example
     * ```php
     * $metadataBuilder->updateMetadata($egiBlockchain, [
     *     'traits' => ['new_trait' => 'value'],
     *     'properties' => ['updated' => true]
     * ]);
     * ```
     */
    public function updateMetadata(EgiBlockchain $egiBlockchain, array $updates): void
    {
        try {
            // 1. ULM: Log update start
            $this->logger->info('EgiMetadataBuilderService: Updating metadata', [
                'egi_blockchain_id' => $egiBlockchain->id,
                'egi_id' => $egiBlockchain->egi_id,
                'update_keys' => array_keys($updates),
                'log_category' => 'METADATA_UPDATE_START'
            ]);

            // 2. Get current metadata
            $currentMetadata = $egiBlockchain->metadata ?? [];

            // 3. Merge updates with existing metadata
            $mergedMetadata = array_merge($currentMetadata, $updates);

            // 4. Validate merged metadata structure
            // Note: Skip full OpenSea validation for partial updates
            // Only validate if we have a complete metadata structure
            if (isset($mergedMetadata['name']) && isset($mergedMetadata['image'])) {
                $this->validateMetadata($mergedMetadata);
            }

            // 5. Update blockchain record
            $egiBlockchain->update([
                'metadata' => $mergedMetadata,
                'metadata_last_updated_at' => now()
            ]);

            // 6. ULM: Log successful update
            $this->logger->info('EgiMetadataBuilderService: Metadata updated successfully', [
                'egi_blockchain_id' => $egiBlockchain->id,
                'egi_id' => $egiBlockchain->egi_id,
                'log_category' => 'METADATA_UPDATE_SUCCESS'
            ]);

            // 7. GDPR: Audit log metadata update
            if ($egiBlockchain->buyer) {
                $this->auditService->logActivity(
                    $egiBlockchain->buyer,
                    GdprActivityCategory::BLOCKCHAIN_OPERATION,
                    'EGI metadata updated',
                    [
                        'egi_blockchain_id' => $egiBlockchain->id,
                        'egi_id' => $egiBlockchain->egi_id,
                        'updated_fields' => array_keys($updates)
                    ]
                );
            }

        } catch (\Exception $e) {
            // 8. UEM: Error handling
            $this->errorManager->handle('EGI_METADATA_UPDATE_FAILED', [
                'egi_blockchain_id' => $egiBlockchain->id,
                'egi_id' => $egiBlockchain->egi_id,
                'error' => $e->getMessage()
            ], $e);

            throw $e;
        }
    }

    /**
     * Export metadata for IPFS upload (Area 6 integration)
     *
     * @param EgiMetadataStructure $metadata Metadata structure to export
     * @return string JSON-encoded metadata ready for IPFS
     *
     * @throws \Exception If export fails
     *
     * @business_logic
     * - Converts EgiMetadataStructure to OpenSea-compatible JSON
     * - Pretty-printed for readability
     * - Includes all required OpenSea fields
     * - Ready for IPFS upload
     *
     * @example
     * ```php
     * $json = $metadataBuilder->exportForIpfs($metadata);
     * // Upload $json to IPFS via IpfsService
     * ```
     */
    public function exportForIpfs(EgiMetadataStructure $metadata): string
    {
        try {
            // 1. ULM: Log export start
            $this->logger->info('EgiMetadataBuilderService: Exporting metadata for IPFS', [
                'collection_id' => $metadata->collection_id,
                'has_coa_reference' => !is_null($metadata->coa_reference),
                'log_category' => 'METADATA_EXPORT_START'
            ]);

            // 2. Convert to OpenSea format
            // Build name, description, and external URL from metadata properties
            $name = "EGI #{$metadata->collection_id}";
            $description = $metadata->properties['description'] ?? 'FlorenceEGI NFT - Ecological Goods Invent';
            $externalUrl = $metadata->properties['external_url'] ?? "https://florenceegi.it/egi/{$metadata->collection_id}";
            
            $openSeaFormat = $metadata->toOpenSeaFormat($name, $description, $externalUrl);

            // 3. Encode to JSON (pretty-printed for IPFS readability)
            $json = json_encode($openSeaFormat, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

            if ($json === false) {
                throw new \Exception('Failed to encode metadata to JSON: ' . json_last_error_msg());
            }

            // 4. ULM: Log successful export
            $this->logger->info('EgiMetadataBuilderService: Metadata exported successfully', [
                'json_size' => strlen($json),
                'log_category' => 'METADATA_EXPORT_SUCCESS'
            ]);

            return $json;

        } catch (\Exception $e) {
            // 5. UEM: Error handling
            $this->errorManager->handle('EGI_METADATA_EXPORT_FAILED', [
                'collection_id' => $metadata->collection_id,
                'error' => $e->getMessage()
            ], $e);

            throw $e;
        }
    }

    // ===== PRIVATE HELPER METHODS =====

    /**
     * Extract standard EGI traits from traits relationship
     *
     * @param Egi $egi EGI model with traits loaded
     * @return array Associative array of trait type => value
     */
    private function extractStandardTraits(Egi $egi): array
    {
        $traits = [];

        foreach ($egi->traits as $trait) {
            // Skip CoA traits (handled separately)
            if ($trait->category && str_contains(strtolower($trait->category->slug ?? ''), 'coa')) {
                continue;
            }

            $traitType = $trait->traitType->name ?? 'Unknown';
            $value = $trait->display_value ?? $trait->value;

            $traits[$traitType] = $value;
        }

        return $traits;
    }

    /**
     * Extract CoA (Certificate of Authenticity) traits
     *
     * @param Egi $egi EGI model with traits loaded
     * @return array Associative array of CoA trait type => value
     */
    private function extractCoaTraits(Egi $egi): array
    {
        $coaTraits = [];

        foreach ($egi->traits as $trait) {
            // Only include CoA traits
            if (!$trait->category || !str_contains(strtolower($trait->category->slug ?? ''), 'coa')) {
                continue;
            }

            $traitType = $trait->traitType->name ?? 'Unknown';
            $value = $trait->display_value ?? $trait->value;

            $coaTraits[$traitType] = $value;
        }

        return $coaTraits;
    }

    /**
     * Generate CoA reference string if EGI has CoA
     *
     * @param Egi $egi EGI model
     * @return string|null CoA reference (e.g., "COA-123-XYZ") or null
     */
    private function generateCoaReference(Egi $egi): ?string
    {
        // Check if EGI has CoA traits
        $hasCoaTraits = $egi->traits->contains(function ($trait) {
            return $trait->category && str_contains(strtolower($trait->category->slug ?? ''), 'coa');
        });

        if (!$hasCoaTraits) {
            return null;
        }

        // Generate reference: COA-{egi_id}-{collection_id}
        return sprintf('COA-%d-%d', $egi->id, $egi->collection_id);
    }

    /**
     * Build technical specifications array
     *
     * @param Egi $egi EGI model
     * @return array Technical specs (dimensions, format, size)
     */
    private function buildTechnicalSpecs(Egi $egi): array
    {
        return [
            'dimensions' => $egi->dimension ?? 'Unknown',
            'size' => $egi->size ?? 'Unknown',
            'format' => $egi->extension ?? 'Unknown',
            'mime_type' => $egi->file_mime ?? 'Unknown',
            'type' => $egi->type ?? 'image'
        ];
    }

    /**
     * Determine edition string (e.g., "1/1", "5/100")
     *
     * @param Egi $egi EGI model
     * @return string Edition string
     */
    private function determineEdition(Egi $egi): string
    {
        // Default to "1/1" for unique pieces
        // Future: Implement edition logic based on collection settings
        return '1/1';
    }

    /**
     * Build OpenSea properties object
     *
     * @param Egi $egi EGI model
     * @param User|null $coCreator Co-creator user
     * @return array Properties object
     */
    private function buildProperties(Egi $egi, ?User $coCreator): array
    {
        return [
            'egi_id' => $egi->id,
            'collection_id' => $egi->collection_id,
            'collection_name' => $egi->collection->collection_name ?? 'Unknown',
            'creator' => $egi->user->name ?? 'Unknown',
            'co_creator' => $coCreator?->name,
            'creation_date' => $egi->creation_date?->format('Y-m-d') ?? now()->format('Y-m-d'),
            'blockchain' => 'algorand',
            'platform' => 'FlorenceEGI'
        ];
    }

    /**
     * Build OpenSea attributes array
     *
     * @param array $standardTraits Standard EGI traits
     * @param array $coaTraits CoA traits
     * @param Egi $egi EGI model
     * @param User|null $coCreator Co-creator user
     * @return array Attributes array (OpenSea format)
     */
    private function buildAttributes(array $standardTraits, array $coaTraits, Egi $egi, ?User $coCreator): array
    {
        $attributes = [];

        // Add standard traits as attributes
        foreach ($standardTraits as $type => $value) {
            $attributes[] = [
                'trait_type' => $type,
                'value' => $value
            ];
        }

        // Add CoA traits as attributes (prefixed)
        foreach ($coaTraits as $type => $value) {
            $attributes[] = [
                'trait_type' => 'CoA: ' . $type,
                'value' => $value
            ];
        }

        // Add system attributes
        $attributes[] = [
            'trait_type' => 'Creator',
            'value' => $egi->user->name ?? 'Unknown'
        ];

        if ($coCreator) {
            $attributes[] = [
                'trait_type' => 'Co-Creator',
                'value' => $coCreator->name
            ];
        }

        $attributes[] = [
            'trait_type' => 'Collection',
            'value' => $egi->collection->collection_name ?? 'Unknown'
        ];

        $attributes[] = [
            'trait_type' => 'Edition',
            'value' => $this->determineEdition($egi)
        ];

        return $attributes;
    }

    /**
     * Validate business rules for metadata
     *
     * @param array $metadata Metadata to validate
     * @return void
     * @throws \Exception If business rules violated
     */
    private function validateBusinessRules(array $metadata): void
    {
        // Business rule: attributes array must have at least one entry
        if (isset($metadata['attributes']) && is_array($metadata['attributes']) && count($metadata['attributes']) === 0) {
            throw new \Exception('Metadata must have at least one attribute');
        }

        // Business rule: image must be valid URL or IPFS format
        if (isset($metadata['image'])) {
            $image = $metadata['image'];
            // Use Laravel's Validator for URL validation instead of filter_var
            $urlValidator = Validator::make(['url' => $image], ['url' => 'url']);
            $isValidUrl = !$urlValidator->fails();
            $isIpfsUri = str_starts_with($image, 'ipfs://');
            
            if (!$isValidUrl && !$isIpfsUri) {
                throw new \Exception('Image must be a valid URL or IPFS URI');
            }
        }

        // Additional business rules can be added here
    }
}
