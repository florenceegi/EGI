<?php

namespace App\Services\Coa;

use App\Models\Coa;
use App\Models\CoaAnnex;
use App\Models\CoaEvent;
use App\Services\Coa\HashingService;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Services\Gdpr\AuditLogService;
use App\Enums\Gdpr\GdprActivityCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * @Oracode Service: CoA Pro Annex Management
 * 🎯 Purpose: Manage versioned annexes for CoA Pro certificates
 * 🛡️ Privacy: Handles GDPR-compliant annex creation with full audit trail
 * 🧱 Core Logic: Manages annex versioning, data integrit            return match($eventType) {
            'annex_added' => sprintf(
                'Annex %s v%d added: %s',
                $eventData['annex_type'] ?? 'unknown',
                $eventData['version'] ?? 0,
                self::ANNEX_TYPES[$eventData['annex_type']] ?? 'Unknown Type'
            ),
            'annex_updated' => sprintf(
                'Annex %s updated: v%d → v%d',
                $eventData['annex_type'] ?? 'unknown',
                $eventData['old_version'] ?? 0,
                $eventData['new_version'] ?? 0
            ),
            default => "CoA event: {$eventType}"
        };e
 *
 * @package App\Services\Coa
 * @author AI Assistant following FlorenceEGI patterns
 * @version 1.0.0 (CoA Pro System)
 * @date 2025-09-18
 * @purpose Professional annex management for enhanced certificate documentation
 */
class AnnexService {
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
     * Hashing service for data integrity
     * @var HashingService
     */
    protected HashingService $hashingService;

    /**
     * Available annex types
     */
    public const ANNEX_TYPES = [
        'A_PROVENANCE' => 'Provenance Documentation',
        'B_CONDITION' => 'Condition Assessment',
        'C_EXHIBITIONS' => 'Exhibition History',
        'D_PHOTOS' => 'Documentation Photography'
    ];

    /**
     * Constructor with dependency injection
     *
     * @param UltraLogManager $logger
     * @param ErrorManagerInterface $errorManager
     * @param AuditLogService $auditService
     * @param HashingService $hashingService
     * @privacy-safe All injected services handle GDPR compliance
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService,
        HashingService $hashingService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditService = $auditService;
        $this->hashingService = $hashingService;
    }

    /**
     * Create new annex for a CoA
     *
     * @param Coa $coa The CoA to add annex to
     * @param string $type Annex type
     * @param array $data Annex data
     * @param string|null $issuedBy Who issued the annex
     * @return CoaAnnex The created annex
     * @privacy-safe Creates annex only for authenticated user's CoA
     *
     * @oracode-dimension governance
     * @value-flow Creates versioned documentation for certificate enhancement
     * @community-impact Provides professional documentation for collectors
     * @transparency-level High - complete annex creation process
     * @narrative-coherence Links annexes to certificate evolution
     */
    public function createAnnex(Coa $coa, string $type, array $data, ?string $issuedBy = null): CoaAnnex {
        try {
            $user = Auth::user();

            // Security check - user must own the CoA's EGI
            if ($coa->egi->user_id !== $user->id) {
                $this->errorManager->handle('COA_ANNEX_UNAUTHORIZED', [
                    'user_id' => $user->id,
                    'coa_id' => $coa->id,
                    'egi_owner_id' => $coa->egi->user_id,
                    'annex_type' => $type,
                    'ip_address' => request()->ip(),
                    'timestamp' => now()->toIso8601String()
                ], new \Illuminate\Auth\Access\AuthorizationException('User cannot create annex for CoA they do not own'));

                throw new \Illuminate\Auth\Access\AuthorizationException('Unauthorized action.');
            }

            // Validate annex type
            if (!array_key_exists($type, self::ANNEX_TYPES)) {
                throw new \Exception("Invalid annex type: {$type}");
            }

            // Check if CoA is valid for annexes
            if ($coa->status !== 'valid') {
                throw new \Exception('Cannot add annex to a CoA that is not in valid status');
            }

            $this->logger->info('[CoA Annex] Creating new annex', [
                'user_id' => $user->id,
                'coa_id' => $coa->id,
                'serial' => $coa->serial,
                'annex_type' => $type,
                'data_size' => count($data)
            ]);

            // Use database transaction
            $annex = DB::transaction(function () use ($coa, $type, $data, $issuedBy, $user) {
                return $this->createAnnexInTransaction($coa, $type, $data, $issuedBy, $user);
            });

            $this->logger->info('[CoA Annex] Annex created successfully', [
                'user_id' => $user->id,
                'coa_id' => $coa->id,
                'annex_id' => $annex->id,
                'annex_type' => $type,
                'version' => $annex->version
            ]);

            // Log audit trail
            $this->auditService->logUserAction($user, 'coa_annex_created', [
                'coa_id' => $coa->id,
                'annex_id' => $annex->id,
                'annex_type' => $type,
                'version' => $annex->version,
                'issued_by' => $issuedBy ?? $user->name
            ], GdprActivityCategory::GDPR_ACTIONS);

            return $annex;
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            throw $e; // Re-throw auth exceptions
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_ANNEX_CREATE_ERROR', [
                'user_id' => Auth::id(),
                'coa_id' => $coa->id,
                'annex_type' => $type,
                'error' => $e->getMessage(),
                'ip_address' => request()->ip(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            throw $e;
        }
    }

    /**
     * Update existing annex (creates new version)
     *
     * @param CoaAnnex $existingAnnex The annex to update
     * @param array $newData New annex data
     * @param string|null $issuedBy Who issued the update
     * @return CoaAnnex The new version of the annex
     * @privacy-safe Updates only user's own annexes
     */
    public function updateAnnex(CoaAnnex $existingAnnex, array $newData, ?string $issuedBy = null): CoaAnnex {
        try {
            $user = Auth::user();

            // Security check - user must own the CoA's EGI
            if ($existingAnnex->coa->egi->user_id !== $user->id) {
                throw new \Illuminate\Auth\Access\AuthorizationException('Unauthorized action.');
            }

            // Check if annex is active
            if ($existingAnnex->status !== 'active') {
                throw new \Exception('Cannot update a non-active annex');
            }

            $this->logger->info('[CoA Annex] Updating annex', [
                'user_id' => $user->id,
                'coa_id' => $existingAnnex->coa_id,
                'annex_id' => $existingAnnex->id,
                'annex_type' => $existingAnnex->type,
                'current_version' => $existingAnnex->version
            ]);

            // Use database transaction
            $newAnnex = DB::transaction(function () use ($existingAnnex, $newData, $issuedBy, $user) {
                return $this->updateAnnexInTransaction($existingAnnex, $newData, $issuedBy, $user);
            });

            $this->logger->info('[CoA Annex] Annex updated successfully', [
                'user_id' => $user->id,
                'coa_id' => $existingAnnex->coa_id,
                'old_annex_id' => $existingAnnex->id,
                'new_annex_id' => $newAnnex->id,
                'old_version' => $existingAnnex->version,
                'new_version' => $newAnnex->version
            ]);

            // Log audit trail
            $this->auditService->logUserAction($user, 'coa_annex_updated', [
                'coa_id' => $existingAnnex->coa_id,
                'old_annex_id' => $existingAnnex->id,
                'new_annex_id' => $newAnnex->id,
                'annex_type' => $existingAnnex->type,
                'old_version' => $existingAnnex->version,
                'new_version' => $newAnnex->version,
                'issued_by' => $issuedBy ?? $user->name
            ], GdprActivityCategory::GDPR_ACTIONS);

            return $newAnnex;
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_ANNEX_UPDATE_ERROR', [
                'user_id' => Auth::id(),
                'annex_id' => $existingAnnex->id,
                'coa_id' => $existingAnnex->coa_id,
                'error' => $e->getMessage(),
                'ip_address' => request()->ip(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            throw $e;
        }
    }

    /**
     * Create annex within database transaction
     *
     * @param Coa $coa
     * @param string $type
     * @param array $data
     * @param string|null $issuedBy
     * @param \App\Models\User $user
     * @return CoaAnnex
     * @privacy-safe Internal transaction method
     */
    protected function createAnnexInTransaction(Coa $coa, string $type, array $data, ?string $issuedBy, $user): CoaAnnex {
        // Get next version number for this type
        $nextVersion = $this->getNextVersionForType($coa, $type);

        // Generate hash for data integrity
        $dataHash = $this->hashingService->generateHash($data);

        // Create annex record
        $annex = CoaAnnex::create([
            'coa_id' => $coa->id,
            'type' => $type,
            'version' => $nextVersion,
            'status' => 'active',
            'data' => $data,
            'hash' => $dataHash,
            'issued_by' => $issuedBy ?? $user->name,
            'supersedes_version' => null,
            'issued_at' => now(),
        ]);

        // Create annex event
        $this->createCoaEvent($coa, 'annex_added', [
            'annex_id' => $annex->id,
            'annex_type' => $type,
            'version' => $nextVersion,
            'added_by' => $issuedBy ?? $user->name,
            'data_size' => count($data),
            'hash' => $dataHash
        ]);

        return $annex;
    }

    /**
     * Update annex within database transaction
     *
     * @param CoaAnnex $existingAnnex
     * @param array $newData
     * @param string|null $issuedBy
     * @param \App\Models\User $user
     * @return CoaAnnex
     * @privacy-safe Internal transaction method
     */
    protected function updateAnnexInTransaction(CoaAnnex $existingAnnex, array $newData, ?string $issuedBy, $user): CoaAnnex {
        // Supersede existing annex
        $existingAnnex->update([
            'status' => 'superseded'
        ]);

        // Get next version number
        $nextVersion = $existingAnnex->version + 1;

        // Generate hash for new data
        $dataHash = $this->hashingService->generateHash($newData);

        // Create new annex version
        $newAnnex = CoaAnnex::create([
            'coa_id' => $existingAnnex->coa_id,
            'type' => $existingAnnex->type,
            'version' => $nextVersion,
            'status' => 'active',
            'data' => $newData,
            'hash' => $dataHash,
            'issued_by' => $issuedBy ?? $user->name,
            'supersedes_version' => $existingAnnex->version,
            'issued_at' => now(),
        ]);

        // Create annex update event
        $this->createCoaEvent($existingAnnex->coa, 'annex_updated', [
            'annex_id' => $newAnnex->id,
            'annex_type' => $existingAnnex->type,
            'old_version' => $existingAnnex->version,
            'new_version' => $nextVersion,
            'superseded_annex_id' => $existingAnnex->id,
            'updated_by' => $issuedBy ?? $user->name,
            'hash' => $dataHash
        ]);

        return $newAnnex;
    }

    /**
     * Get all annexes for a CoA
     *
     * @param Coa $coa The CoA to get annexes for
     * @param bool $activeOnly Whether to return only active annexes
     * @return array Annexes data organized by type
     * @privacy-safe Returns annexes only for user's own CoA
     */
    public function getCoaAnnexes(Coa $coa, bool $activeOnly = true): array {
        try {
            $user = Auth::user();

            // Security check
            if ($coa->egi->user_id !== $user->id) {
                throw new \Illuminate\Auth\Access\AuthorizationException('Unauthorized action.');
            }

            $query = CoaAnnex::where('coa_id', $coa->id);

            if ($activeOnly) {
                $query->where('status', 'active');
            }

            $annexes = $query->orderBy('type')
                ->orderBy('version', 'desc')
                ->get();

            $results = [
                'coa_id' => $coa->id,
                'serial' => $coa->serial,
                'total_annexes' => $annexes->count(),
                'annexes_by_type' => [],
                'annexes' => []
            ];

            // Group by type
            foreach (self::ANNEX_TYPES as $type => $description) {
                $typeAnnexes = $annexes->where('type', $type);
                $results['annexes_by_type'][$type] = [
                    'type' => $type,
                    'description' => $description,
                    'count' => $typeAnnexes->count(),
                    'latest_version' => $typeAnnexes->first()?->version,
                    'annexes' => $typeAnnexes->values()->toArray()
                ];
            }

            // All annexes list
            foreach ($annexes as $annex) {
                $results['annexes'][] = [
                    'id' => $annex->id,
                    'type' => $annex->type,
                    'type_description' => self::ANNEX_TYPES[$annex->type] ?? $annex->type,
                    'version' => $annex->version,
                    'status' => $annex->status,
                    'issued_by' => $annex->issued_by,
                    'issued_at' => $annex->issued_at,
                    'supersedes_version' => $annex->supersedes_version,
                    'data_size' => count($annex->data ?? []),
                    'hash' => $annex->hash
                ];
            }

            $this->logger->info('[CoA Annex] Annexes retrieved', [
                'coa_id' => $coa->id,
                'total_annexes' => $results['total_annexes'],
                'active_only' => $activeOnly,
                'user_id' => $user->id
            ]);

            return $results;
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_GET_ANNEXES_ERROR', [
                'user_id' => Auth::id(),
                'coa_id' => $coa->id,
                'active_only' => $activeOnly,
                'error' => $e->getMessage(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            return [
                'coa_id' => $coa->id,
                'error' => true,
                'message' => 'Failed to retrieve annexes'
            ];
        }
    }

    /**
     * Get specific annex by type and version
     *
     * @param Coa $coa The CoA
     * @param string $type Annex type
     * @param int|null $version Specific version (null for latest)
     * @return CoaAnnex|null The annex or null if not found
     * @privacy-safe Returns annex only for user's own CoA
     */
    public function getAnnex(Coa $coa, string $type, ?int $version = null): ?CoaAnnex {
        try {
            $user = Auth::user();

            // Security check
            if ($coa->egi->user_id !== $user->id) {
                throw new \Illuminate\Auth\Access\AuthorizationException('Unauthorized action.');
            }

            $query = CoaAnnex::where('coa_id', $coa->id)
                ->where('type', $type);

            if ($version !== null) {
                $query->where('version', $version);
            } else {
                $query->where('status', 'active')
                    ->orderBy('version', 'desc');
            }

            $annex = $query->first();

            $this->logger->info('[CoA Annex] Annex retrieved', [
                'coa_id' => $coa->id,
                'type' => $type,
                'version' => $version ?? 'latest',
                'found' => $annex !== null,
                'annex_id' => $annex?->id,
                'user_id' => $user->id
            ]);

            return $annex;
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_GET_ANNEX_ERROR', [
                'user_id' => Auth::id(),
                'coa_id' => $coa->id,
                'type' => $type,
                'version' => $version,
                'error' => $e->getMessage(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            return null;
        }
    }

    /**
     * Verify annex data integrity
     *
     * @param CoaAnnex $annex The annex to verify
     * @return array Verification results
     * @privacy-safe Verification only, no data modification
     */
    public function verifyAnnexIntegrity(CoaAnnex $annex): array {
        try {
            $this->logger->info('[CoA Annex] Verifying annex integrity', [
                'annex_id' => $annex->id,
                'coa_id' => $annex->coa_id,
                'type' => $annex->type,
                'version' => $annex->version
            ]);

            $results = [
                'annex_id' => $annex->id,
                'is_valid' => false,
                'checks' => []
            ];

            // Check hash integrity
            $currentHash = $this->hashingService->generateHash($annex->data);
            $hashValid = hash_equals($annex->hash, $currentHash);

            $results['checks']['hash_integrity'] = [
                'valid' => $hashValid,
                'stored_hash' => $annex->hash,
                'calculated_hash' => $currentHash
            ];

            // Check data structure
            $dataValid = !empty($annex->data) && is_array($annex->data);
            $results['checks']['data_structure'] = [
                'valid' => $dataValid,
                'has_data' => !empty($annex->data),
                'is_array' => is_array($annex->data)
            ];

            // Check type validity
            $typeValid = array_key_exists($annex->type, self::ANNEX_TYPES);
            $results['checks']['type_validity'] = [
                'valid' => $typeValid,
                'type' => $annex->type
            ];

            // Check version consistency
            $versionValid = $annex->version > 0;
            if ($annex->supersedes_version !== null) {
                $versionValid = $versionValid && $annex->version > $annex->supersedes_version;
            }

            $results['checks']['version_consistency'] = [
                'valid' => $versionValid,
                'version' => $annex->version,
                'supersedes_version' => $annex->supersedes_version
            ];

            // Overall validity
            $results['is_valid'] = $hashValid && $dataValid && $typeValid && $versionValid;

            $this->logger->info('[CoA Annex] Integrity verification completed', [
                'annex_id' => $annex->id,
                'is_valid' => $results['is_valid'],
                'checks_passed' => count(array_filter($results['checks'], fn($c) => $c['valid']))
            ]);

            return $results;
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_ANNEX_INTEGRITY_ERROR', [
                'annex_id' => $annex->id,
                'coa_id' => $annex->coa_id,
                'error' => $e->getMessage(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            return [
                'annex_id' => $annex->id,
                'is_valid' => false,
                'error' => 'Verification failed',
                'checks' => []
            ];
        }
    }

    /**
     * Get next version number for annex type
     *
     * @param Coa $coa
     * @param string $type
     * @return int
     * @privacy-safe Version calculation only
     */
    protected function getNextVersionForType(Coa $coa, string $type): int {
        $lastVersion = CoaAnnex::where('coa_id', $coa->id)
            ->where('type', $type)
            ->max('version');

        return ($lastVersion ?? 0) + 1;
    }

    /**
     * Create a CoA event record
     *
     * @param Coa $coa
     * @param string $eventType
     * @param array $eventData
     * @return CoaEvent
     * @privacy-safe Creates audit event for user's own CoA
     */
    protected function createCoaEvent(Coa $coa, string $eventType, array $eventData = []): CoaEvent {
        $baseData = [
            'timestamp' => now()->toIso8601String(),
            'session_id' => session()->getId(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ];

        $event = CoaEvent::create([
            'coa_id' => $coa->id,
            'user_id' => Auth::id(),
            'event_type' => $eventType,
            'description' => $this->getEventDescription($eventType, $eventData),
            'event_data' => array_merge($baseData, $eventData),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'occurred_at' => now(),
        ]);

        return $event;
    }

    /**
     * Generate human-readable description for event types
     *
     * @param string $eventType
     * @param array $eventData
     * @return string
     * @privacy-safe Generates description from event metadata
     */
    protected function getEventDescription(string $eventType, array $eventData): string {
        switch ($eventType) {
            case 'annex_added':
                return sprintf(
                    'Annex %s v%d added: %s',
                    $eventData['annex_type'] ?? 'unknown',
                    $eventData['version'] ?? 0,
                    self::ANNEX_TYPES[$eventData['annex_type']] ?? 'Unknown Type'
                );
            case 'annex_updated':
                return sprintf(
                    'Annex %s updated: v%d → v%d',
                    $eventData['annex_type'] ?? 'unknown',
                    $eventData['old_version'] ?? 0,
                    $eventData['new_version'] ?? 0
                );
            default:
                return "CoA event: {$eventType}";
        }
    }

    /**
     * Get available annex types
     *
     * @return array Available types with descriptions
     * @privacy-safe Returns static configuration data
     */
    public function getAvailableTypes(): array {
        return self::ANNEX_TYPES;
    }

    /**
     * Get annex version history for a type
     *
     * @param Coa $coa
     * @param string $type
     * @return array Version history
     * @privacy-safe Returns history only for user's own CoA
     */
    public function getAnnexVersionHistory(Coa $coa, string $type): array {
        try {
            $user = Auth::user();

            // Security check
            if ($coa->egi->user_id !== $user->id) {
                throw new \Illuminate\Auth\Access\AuthorizationException('Unauthorized action.');
            }

            $annexes = CoaAnnex::where('coa_id', $coa->id)
                ->where('type', $type)
                ->orderBy('version', 'desc')
                ->get();

            $history = [
                'coa_id' => $coa->id,
                'type' => $type,
                'type_description' => self::ANNEX_TYPES[$type] ?? $type,
                'total_versions' => $annexes->count(),
                'current_version' => $annexes->where('status', 'active')->first()?->version,
                'versions' => []
            ];

            foreach ($annexes as $annex) {
                $history['versions'][] = [
                    'id' => $annex->id,
                    'version' => $annex->version,
                    'status' => $annex->status,
                    'issued_by' => $annex->issued_by,
                    'issued_at' => $annex->issued_at,
                    'supersedes_version' => $annex->supersedes_version,
                    'hash' => $annex->hash,
                    'data_size' => count($annex->data ?? [])
                ];
            }

            return $history;
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_ANNEX_HISTORY_ERROR', [
                'user_id' => Auth::id(),
                'coa_id' => $coa->id,
                'type' => $type,
                'error' => $e->getMessage(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            return [
                'coa_id' => $coa->id,
                'type' => $type,
                'error' => true,
                'message' => 'Failed to retrieve version history'
            ];
        }
    }
}
