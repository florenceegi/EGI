<?php

namespace App\Services\Coa;

use App\Models\Egi;
use App\Models\EgiTraitsVersion;
use App\Models\CoaSnapshot;
use App\Models\Coa;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Services\Gdpr\AuditLogService;
use App\Enums\Gdpr\GdprActivityCategory;
use Illuminate\Support\Facades\Auth;

/**
 * @Oracode Service: Traits Snapshot Management
 * 🎯 Purpose: Create immutable snapshots of EGI traits for CoA certificates
 * 🛡️ Privacy: Handles GDPR-compliant snapshot creation with full audit trail
 * 🧱 Core Logic: Manages traits versioning, hash generation, and snapshot integrity
 *
 * @package App\Services\Coa
 * @author AI Assistant following FlorenceEGI patterns
 * @version 1.0.0 (CoA Pro System)
 * @date 2025-09-18
 * @purpose Immutable traits preservation for certificate authenticity
 */
class TraitsSnapshotService {
    /**
     * Logger instance for audit trail
     * @var UltraLogManager
     */
    protected UltraLogManager $logger;

    /**
     * Error manager for robust error handling
     * @var ErrorManagerInterface
     */
    protected ErrorManagerInterface $errorManager;

    /**
     * Audit logging service
     * @var AuditLogService
     */
    protected AuditLogService $auditService;

    /**
     * Constructor with dependency injection
     *
     * @param UltraLogManager $logger
     * @param ErrorManagerInterface $errorManager
     * @param AuditLogService $auditService
     * @privacy-safe All injected services handle GDPR compliance
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditService = $auditService;
    }

    /**
     * Create a new traits version and snapshot for an EGI
     *
     * @param Egi $egi The EGI to create snapshot for
     * @param string $changeReason Reason for creating new version
     * @param array $changedFields List of fields that changed
     * @return EgiTraitsVersion
     * @privacy-safe Creates snapshot only for authenticated user's artwork
     *
     * @oracode-dimension governance
     * @value-flow Creates immutable record of artwork state
     * @community-impact Ensures authenticity verification for collectors
     * @transparency-level High - complete traits preservation
     * @narrative-coherence Links current state to historical record
     */
    public function createTraitsVersion(Egi $egi, string $changeReason = 'CoA issuance', array $changedFields = []): EgiTraitsVersion {
        try {
            $user = Auth::user();

            // Security check - user must own the EGI
            if ($egi->user_id !== $user->id) {
                $this->errorManager->handle('COA_TRAITS_SNAPSHOT_UNAUTHORIZED', [
                    'user_id' => $user->id,
                    'egi_id' => $egi->id,
                    'egi_owner_id' => $egi->user_id,
                    'ip_address' => request()->ip(),
                    'timestamp' => now()->toIso8601String()
                ], new \Illuminate\Auth\Access\AuthorizationException('User cannot create snapshot for EGI they do not own'));

                throw new \Illuminate\Auth\Access\AuthorizationException('Unauthorized action.');
            }

            $this->logger->info('[CoA Snapshot] Creating traits version', [
                'user_id' => $user->id,
                'egi_id' => $egi->id,
                'change_reason' => $changeReason,
                'changed_fields_count' => count($changedFields)
            ]);

            // Get current traits data
            $currentTraits = $this->extractTraitsData($egi);
            $traitsHash = $this->generateTraitsHash($currentTraits);

            // Get next version number
            $nextVersion = $this->getNextVersionNumber($egi);

            // Create traits version record
            $traitsVersion = EgiTraitsVersion::create([
                'egi_id' => $egi->id,
                'version' => $nextVersion,
                'traits_data' => $currentTraits,
                'traits_hash' => $traitsHash,
                'change_reason' => $changeReason,
                'changed_fields' => $changedFields,
            ]);

            $this->logger->info('[CoA Snapshot] Traits version created', [
                'user_id' => $user->id,
                'egi_id' => $egi->id,
                'traits_version_id' => $traitsVersion->id,
                'version' => $nextVersion,
                'traits_hash' => $traitsHash
            ]);

            // Log audit trail
            $this->auditService->logUserAction($user, 'traits_version_created', [
                'egi_id' => $egi->id,
                'traits_version_id' => $traitsVersion->id,
                'version' => $nextVersion,
                'change_reason' => $changeReason,
                'traits_hash' => $traitsHash
            ], GdprActivityCategory::GDPR_ACTIONS);

            return $traitsVersion;
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            throw $e; // Re-throw auth exceptions
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_TRAITS_VERSION_CREATE_ERROR', [
                'user_id' => Auth::id(),
                'egi_id' => $egi->id,
                'change_reason' => $changeReason,
                'error' => $e->getMessage(),
                'ip_address' => request()->ip(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            throw $e;
        }
    }

    /**
     * Create a CoA snapshot linked to a traits version
     *
     * @param Coa $coa The CoA to create snapshot for
     * @param EgiTraitsVersion $traitsVersion The traits version to snapshot
     * @return CoaSnapshot
     * @privacy-safe Creates snapshot only for authenticated user's CoA
     */
    public function createCoaSnapshot(Coa $coa, EgiTraitsVersion $traitsVersion): CoaSnapshot {
        try {
            $user = Auth::user();

            // Security check - user must own the CoA's EGI
            if ($coa->egi->user_id !== $user->id) {
                throw new \Illuminate\Auth\Access\AuthorizationException('Unauthorized action.');
            }

            $this->logger->info('[CoA Snapshot] Creating CoA snapshot', [
                'user_id' => $user->id,
                'coa_id' => $coa->id,
                'traits_version_id' => $traitsVersion->id,
                'traits_hash' => $traitsVersion->traits_hash
            ]);

            // Create snapshot record
            $snapshot = CoaSnapshot::create([
                'coa_id' => $coa->id,
                'traits_version_id' => $traitsVersion->id,
                'traits_hash' => $traitsVersion->traits_hash,
                'traits_data' => $traitsVersion->traits_data,
                'snapshot_at' => now(),
            ]);

            $this->logger->info('[CoA Snapshot] CoA snapshot created', [
                'user_id' => $user->id,
                'coa_id' => $coa->id,
                'snapshot_id' => $snapshot->id,
                'traits_version_id' => $traitsVersion->id
            ]);

            // Log audit trail
            $this->auditService->logUserAction($user, 'coa_snapshot_created', [
                'coa_id' => $coa->id,
                'snapshot_id' => $snapshot->id,
                'traits_version_id' => $traitsVersion->id,
                'traits_hash' => $traitsVersion->traits_hash
            ], GdprActivityCategory::GDPR_ACTIONS);

            return $snapshot;
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_SNAPSHOT_CREATE_ERROR', [
                'user_id' => Auth::id(),
                'coa_id' => $coa->id,
                'traits_version_id' => $traitsVersion->id,
                'error' => $e->getMessage(),
                'ip_address' => request()->ip(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            throw $e;
        }
    }

    /**
     * Verify integrity of a snapshot against its hash
     *
     * @param CoaSnapshot $snapshot The snapshot to verify
     * @return bool True if integrity check passes
     * @privacy-safe Verification only, no data exposure
     */
    public function verifySnapshotIntegrity(CoaSnapshot $snapshot): bool {
        try {
            $this->logger->info('[CoA Snapshot] Verifying snapshot integrity', [
                'snapshot_id' => $snapshot->id,
                'coa_id' => $snapshot->coa_id,
                'stored_hash' => $snapshot->traits_hash
            ]);

            // Recalculate hash from stored traits data
            $calculatedHash = $this->generateTraitsHash($snapshot->traits_data);

            // Compare hashes
            $isValid = hash_equals($snapshot->traits_hash, $calculatedHash);

            $this->logger->info('[CoA Snapshot] Integrity verification result', [
                'snapshot_id' => $snapshot->id,
                'stored_hash' => $snapshot->traits_hash,
                'calculated_hash' => $calculatedHash,
                'is_valid' => $isValid
            ]);

            if (!$isValid) {
                $this->logger->error('[CoA Snapshot] Integrity verification failed', [
                    'snapshot_id' => $snapshot->id,
                    'coa_id' => $snapshot->coa_id,
                    'stored_hash' => $snapshot->traits_hash,
                    'calculated_hash' => $calculatedHash
                ]);
            }

            return $isValid;
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_SNAPSHOT_INTEGRITY_CHECK_ERROR', [
                'snapshot_id' => $snapshot->id,
                'coa_id' => $snapshot->coa_id,
                'error' => $e->getMessage(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            return false;
        }
    }

    /**
     * Extract traits data from an EGI model
     *
     * @param Egi $egi The EGI to extract traits from
     * @return array The traits data
     * @privacy-safe Extracts only artwork metadata, no personal data
     */
    protected function extractTraitsData(Egi $egi): array {
        // Extract all relevant traits from the EGI
        // This should match the structure expected by the CoA system
        return [
            'id' => $egi->id,
            'title' => $egi->title,
            'description' => $egi->description,
            'author' => $egi->author,
            'year' => $egi->year,
            'technique' => $egi->technique,
            'dimensions' => $egi->dimensions,
            'style' => $egi->style,
            'subject' => $egi->subject,
            'colors' => $egi->colors,
            'materials' => $egi->materials,
            'provenance' => $egi->provenance,
            'exhibitions' => $egi->exhibitions,
            'condition' => $egi->condition,
            'rarity_score' => $egi->rarity_score,
            'market_value' => $egi->market_value,
            'insurance_value' => $egi->insurance_value,
            'cultural_significance' => $egi->cultural_significance,
            'artistic_movement' => $egi->artistic_movement,
            'inspiration_sources' => $egi->inspiration_sources,
            'technical_notes' => $egi->technical_notes,
            'awards' => $egi->awards,
            'created_at' => $egi->created_at?->toIso8601String(),
            'updated_at' => $egi->updated_at?->toIso8601String(),
        ];
    }

    /**
     * Generate a cryptographic hash of traits data
     *
     * @param array $traitsData The traits data to hash
     * @return string The SHA-256 hash
     * @privacy-safe Hash generation, no data storage
     */
    protected function generateTraitsHash(array $traitsData): string {
        // Sort array keys to ensure consistent hashing
        ksort($traitsData);

        // Convert to JSON and hash
        $jsonData = json_encode($traitsData, JSON_SORT_KEYS | JSON_UNESCAPED_UNICODE);

        return hash('sha256', $jsonData);
    }

    /**
     * Get the next version number for an EGI
     *
     * @param Egi $egi The EGI to get next version for
     * @return int The next version number
     * @privacy-safe Version calculation only
     */
    protected function getNextVersionNumber(Egi $egi): int {
        $lastVersion = EgiTraitsVersion::where('egi_id', $egi->id)
            ->max('version');

        return ($lastVersion ?? 0) + 1;
    }

    /**
     * Get the latest traits version for an EGI
     *
     * @param Egi $egi The EGI to get latest version for
     * @return EgiTraitsVersion|null The latest version or null
     * @privacy-safe Read-only access to user's own data
     */
    public function getLatestTraitsVersion(Egi $egi): ?EgiTraitsVersion {
        return EgiTraitsVersion::where('egi_id', $egi->id)
            ->orderBy('version', 'desc')
            ->first();
    }

    /**
     * Compare two traits versions and return differences
     *
     * @param EgiTraitsVersion $oldVersion
     * @param EgiTraitsVersion $newVersion
     * @return array Array of differences
     * @privacy-safe Comparison only, no data modification
     */
    public function compareTraitsVersions(EgiTraitsVersion $oldVersion, EgiTraitsVersion $newVersion): array {
        $oldData = $oldVersion->traits_data;
        $newData = $newVersion->traits_data;

        $differences = [];

        // Find added or changed fields
        foreach ($newData as $key => $newValue) {
            if (!isset($oldData[$key]) || $oldData[$key] !== $newValue) {
                $differences[$key] = [
                    'old' => $oldData[$key] ?? null,
                    'new' => $newValue
                ];
            }
        }

        // Find removed fields
        foreach ($oldData as $key => $oldValue) {
            if (!isset($newData[$key])) {
                $differences[$key] = [
                    'old' => $oldValue,
                    'new' => null
                ];
            }
        }

        return $differences;
    }
}
