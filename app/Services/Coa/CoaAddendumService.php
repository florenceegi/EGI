<?php

namespace App\Services\Coa;

use App\Models\Coa;
use App\Models\CoaAnnex;
use App\Models\CoaAddendum;
use App\Models\CoaEvent;
use App\Models\CoaPolicy;
use App\Services\Coa\HashingService;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Services\Gdpr\AuditLogService;
use App\Enums\Gdpr\GdprActivityCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * @Oracode Service: CoA Pro Addendum Management
 * 🎯 Purpose: Manage addendums and policies for CoA Pro certificates
 * 🛡️ Privacy: Handles GDPR-compliant addendum creation with full audit trail
 * 🧱 Core Logic: Manages versioned addendums, policy updates, and compliance
 *
 * @package App\Services\Coa
 * @author AI Assistant following FlorenceEGI patterns
 * @version 1.0.0 (CoA Pro System)
 * @date 2025-09-18
 * @purpose Professional addendum management for enhanced certificate governance
 */
class CoaAddendumService {
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
     * Available addendum types
     */
    public const ADDENDUM_TYPES = [
        'POLICY_UPDATE' => 'Policy Update',
        'CONDITION_CHANGE' => 'Condition Change',
        'EXHIBITION_ADDITION' => 'Exhibition Addition',
        'PROVENANCE_UPDATE' => 'Provenance Update',
        'OWNERSHIP_TRANSFER' => 'Ownership Transfer',
        'TECHNICAL_CORRECTION' => 'Technical Correction',
        'CONSERVATION_REPORT' => 'Conservation Report',
        'VALUATION_UPDATE' => 'Valuation Update'
    ];

    /**
     * Available policy types
     */
    public const POLICY_TYPES = [
        'AUTHENTICATION' => 'Authentication Policy',
        'CONSERVATION' => 'Conservation Guidelines',
        'EXHIBITION' => 'Exhibition Terms',
        'TRANSFER' => 'Transfer Conditions',
        'INSURANCE' => 'Insurance Requirements',
        'PRIVACY' => 'Privacy Policy',
        'DISPUTE' => 'Dispute Resolution'
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
     * Create new addendum for a CoA
     *
     * @param Coa $coa The CoA to add addendum to
     * @param string $type Addendum type
     * @param array $content Addendum content
     * @param string|null $reason Reason for addendum
     * @param array $metadata Additional metadata
     * @return CoaAddendum The created addendum
     * @privacy-safe Creates addendum only for authenticated user's CoA
     *
     * @oracode-dimension governance
     * @value-flow Creates versioned documentation amendments for certificate updates
     * @community-impact Provides transparent certificate evolution tracking
     * @transparency-level High - complete addendum creation process
     * @narrative-coherence Links addendums to certificate governance
     */
    public function createAddendum(Coa $coa, string $type, array $content, ?string $reason = null, array $metadata = []): CoaAddendum {
        try {
            $user = Auth::user();

            // Security check - user must own the CoA's EGI
            if ($coa->egi->user_id !== $user->id) {
                $this->errorManager->handle('COA_ADDENDUM_UNAUTHORIZED', [
                    'user_id' => $user->id,
                    'coa_id' => $coa->id,
                    'egi_owner_id' => $coa->egi->user_id,
                    'addendum_type' => $type,
                    'ip_address' => request()->ip(),
                    'timestamp' => now()->toIso8601String()
                ], new \Illuminate\Auth\Access\AuthorizationException('User cannot create addendum for CoA they do not own'));

                throw new \Illuminate\Auth\Access\AuthorizationException('Unauthorized action.');
            }

            // Validate addendum type
            if (!array_key_exists($type, self::ADDENDUM_TYPES)) {
                throw new \Exception("Invalid addendum type: {$type}");
            }

            // Check if CoA is valid for addendums
            if ($coa->status !== 'valid') {
                throw new \Exception('Cannot add addendum to a CoA that is not in valid status');
            }

            $this->logger->info('[CoA Addendum] Creating new addendum', [
                'user_id' => $user->id,
                'coa_id' => $coa->id,
                'serial' => $coa->serial,
                'addendum_type' => $type,
                'reason' => $reason,
                'content_size' => count($content)
            ]);

            // Use database transaction
            $addendum = DB::transaction(function () use ($coa, $type, $content, $reason, $metadata, $user) {
                return $this->createAddendumInTransaction($coa, $type, $content, $reason, $metadata, $user);
            });

            $this->logger->info('[CoA Addendum] Addendum created successfully', [
                'user_id' => $user->id,
                'coa_id' => $coa->id,
                'addendum_id' => $addendum->id,
                'addendum_type' => $type,
                'version' => $addendum->version
            ]);

            // Log audit trail
            $this->auditService->logUserAction($user, 'coa_addendum_created', [
                'coa_id' => $coa->id,
                'addendum_id' => $addendum->id,
                'addendum_type' => $type,
                'version' => $addendum->version,
                'reason' => $reason
            ], GdprActivityCategory::GDPR_ACTIONS);

            return $addendum;
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            throw $e; // Re-throw auth exceptions
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_ADDENDUM_CREATE_ERROR', [
                'user_id' => Auth::id(),
                'coa_id' => $coa->id,
                'addendum_type' => $type,
                'error' => $e->getMessage(),
                'ip_address' => request()->ip(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            throw $e;
        }
    }

    /**
     * Create policy for a CoA
     *
     * @param Coa $coa The CoA to add policy to
     * @param string $type Policy type
     * @param array $terms Policy terms
     * @param string|null $description Policy description
     * @param \DateTime|null $effectiveDate When policy becomes effective
     * @param \DateTime|null $expiryDate When policy expires
     * @return CoaPolicy The created policy
     * @privacy-safe Creates policy only for authenticated user's CoA
     */
    public function createPolicy(Coa $coa, string $type, array $terms, ?string $description = null, ?\DateTime $effectiveDate = null, ?\DateTime $expiryDate = null): CoaPolicy {
        try {
            $user = Auth::user();

            // Security check
            if ($coa->egi->user_id !== $user->id) {
                throw new \Illuminate\Auth\Access\AuthorizationException('Unauthorized action.');
            }

            // Validate policy type
            if (!array_key_exists($type, self::POLICY_TYPES)) {
                throw new \Exception("Invalid policy type: {$type}");
            }

            $this->logger->info('[CoA Policy] Creating new policy', [
                'user_id' => $user->id,
                'coa_id' => $coa->id,
                'policy_type' => $type,
                'effective_date' => $effectiveDate?->format('Y-m-d'),
                'expiry_date' => $expiryDate?->format('Y-m-d')
            ]);

            // Use database transaction
            $policy = DB::transaction(function () use ($coa, $type, $terms, $description, $effectiveDate, $expiryDate, $user) {
                return $this->createPolicyInTransaction($coa, $type, $terms, $description, $effectiveDate, $expiryDate, $user);
            });

            $this->logger->info('[CoA Policy] Policy created successfully', [
                'user_id' => $user->id,
                'coa_id' => $coa->id,
                'policy_id' => $policy->id,
                'policy_type' => $type,
                'version' => $policy->version
            ]);

            // Log audit trail
            $this->auditService->logUserAction($user, 'coa_policy_created', [
                'coa_id' => $coa->id,
                'policy_id' => $policy->id,
                'policy_type' => $type,
                'version' => $policy->version,
                'effective_date' => $effectiveDate?->format('Y-m-d'),
                'expiry_date' => $expiryDate?->format('Y-m-d')
            ], GdprActivityCategory::GDPR_ACTIONS);

            return $policy;
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_POLICY_CREATE_ERROR', [
                'user_id' => Auth::id(),
                'coa_id' => $coa->id,
                'policy_type' => $type,
                'error' => $e->getMessage(),
                'ip_address' => request()->ip(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            throw $e;
        }
    }

    /**
     * Create addendum within database transaction
     *
     * @param Coa $coa
     * @param string $type
     * @param array $content
     * @param string|null $reason
     * @param array $metadata
     * @param \App\Models\User $user
     * @return CoaAddendum
     * @privacy-safe Internal transaction method
     */
    protected function createAddendumInTransaction(Coa $coa, string $type, array $content, ?string $reason, array $metadata, $user): CoaAddendum {
        // Get next version number
        $nextVersion = $this->getNextAddendumVersion($coa);

        // Generate hash for content integrity
        $contentHash = $this->hashingService->generateHash([
            'content' => $content,
            'metadata' => $metadata,
            'reason' => $reason,
            'type' => $type
        ]);

        // Create addendum record
        $addendum = CoaAddendum::create([
            'coa_id' => $coa->id,
            'type' => $type,
            'version' => $nextVersion,
            'status' => 'active',
            'content' => $content,
            'metadata' => $metadata,
            'reason' => $reason,
            'hash' => $contentHash,
            'created_by' => $user->name,
            'created_at' => now(),
            'effective_date' => now(),
        ]);

        // Create addendum event
        $this->createCoaEvent($coa, 'addendum_created', [
            'addendum_id' => $addendum->id,
            'addendum_type' => $type,
            'version' => $nextVersion,
            'reason' => $reason,
            'created_by' => $user->name,
            'content_size' => count($content),
            'hash' => $contentHash
        ]);

        return $addendum;
    }

    /**
     * Create policy within database transaction
     *
     * @param Coa $coa
     * @param string $type
     * @param array $terms
     * @param string|null $description
     * @param \DateTime|null $effectiveDate
     * @param \DateTime|null $expiryDate
     * @param \App\Models\User $user
     * @return CoaPolicy
     * @privacy-safe Internal transaction method
     */
    protected function createPolicyInTransaction(Coa $coa, string $type, array $terms, ?string $description, ?\DateTime $effectiveDate, ?\DateTime $expiryDate, $user): CoaPolicy {
        // Supersede existing active policy of same type
        CoaPolicy::where('coa_id', $coa->id)
            ->where('type', $type)
            ->where('status', 'active')
            ->update(['status' => 'superseded']);

        // Get next version number for this policy type
        $nextVersion = $this->getNextPolicyVersion($coa, $type);

        // Generate hash for policy integrity
        $policyHash = $this->hashingService->generateHash([
            'terms' => $terms,
            'description' => $description,
            'type' => $type,
            'effective_date' => $effectiveDate?->format('Y-m-d H:i:s'),
            'expiry_date' => $expiryDate?->format('Y-m-d H:i:s')
        ]);

        // Create policy record
        $policy = CoaPolicy::create([
            'coa_id' => $coa->id,
            'type' => $type,
            'version' => $nextVersion,
            'status' => 'active',
            'terms' => $terms,
            'description' => $description,
            'hash' => $policyHash,
            'created_by' => $user->name,
            'effective_date' => $effectiveDate ?? now(),
            'expiry_date' => $expiryDate,
            'created_at' => now(),
        ]);

        // Create policy event
        $this->createCoaEvent($coa, 'policy_created', [
            'policy_id' => $policy->id,
            'policy_type' => $type,
            'version' => $nextVersion,
            'created_by' => $user->name,
            'effective_date' => $effectiveDate?->format('Y-m-d H:i:s'),
            'expiry_date' => $expiryDate?->format('Y-m-d H:i:s'),
            'hash' => $policyHash
        ]);

        return $policy;
    }

    /**
     * Get all addendums for a CoA
     *
     * @param Coa $coa The CoA to get addendums for
     * @param bool $activeOnly Whether to return only active addendums
     * @return array Addendums data
     * @privacy-safe Returns addendums only for user's own CoA
     */
    public function getCoaAddendums(Coa $coa, bool $activeOnly = true): array {
        try {
            $user = Auth::user();

            // Security check
            if ($coa->egi->user_id !== $user->id) {
                throw new \Illuminate\Auth\Access\AuthorizationException('Unauthorized action.');
            }

            $query = CoaAddendum::where('coa_id', $coa->id);

            if ($activeOnly) {
                $query->where('status', 'active');
            }

            $addendums = $query->orderBy('version', 'desc')->get();

            $results = [
                'coa_id' => $coa->id,
                'serial' => $coa->serial,
                'total_addendums' => $addendums->count(),
                'addendums_by_type' => [],
                'addendums' => []
            ];

            // Group by type
            foreach (self::ADDENDUM_TYPES as $type => $description) {
                $typeAddendums = $addendums->where('type', $type);
                $results['addendums_by_type'][$type] = [
                    'type' => $type,
                    'description' => $description,
                    'count' => $typeAddendums->count(),
                    'latest_version' => $typeAddendums->first()?->version,
                    'addendums' => $typeAddendums->values()->toArray()
                ];
            }

            // All addendums list
            foreach ($addendums as $addendum) {
                $results['addendums'][] = [
                    'id' => $addendum->id,
                    'type' => $addendum->type,
                    'type_description' => self::ADDENDUM_TYPES[$addendum->type] ?? $addendum->type,
                    'version' => $addendum->version,
                    'status' => $addendum->status,
                    'reason' => $addendum->reason,
                    'created_by' => $addendum->created_by,
                    'created_at' => $addendum->created_at,
                    'effective_date' => $addendum->effective_date,
                    'content_size' => count($addendum->content ?? []),
                    'metadata_size' => count($addendum->metadata ?? []),
                    'hash' => $addendum->hash
                ];
            }

            return $results;
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_GET_ADDENDUMS_ERROR', [
                'user_id' => Auth::id(),
                'coa_id' => $coa->id,
                'active_only' => $activeOnly,
                'error' => $e->getMessage(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            return [
                'coa_id' => $coa->id,
                'error' => true,
                'message' => 'Failed to retrieve addendums'
            ];
        }
    }

    /**
     * Get all policies for a CoA
     *
     * @param Coa $coa The CoA to get policies for
     * @param bool $activeOnly Whether to return only active policies
     * @return array Policies data
     * @privacy-safe Returns policies only for user's own CoA
     */
    public function getCoaPolicies(Coa $coa, bool $activeOnly = true): array {
        try {
            $user = Auth::user();

            // Security check
            if ($coa->egi->user_id !== $user->id) {
                throw new \Illuminate\Auth\Access\AuthorizationException('Unauthorized action.');
            }

            $query = CoaPolicy::where('coa_id', $coa->id);

            if ($activeOnly) {
                $query->where('status', 'active')
                    ->where(function ($q) {
                        $q->whereNull('expiry_date')
                            ->orWhere('expiry_date', '>', now());
                    });
            }

            $policies = $query->orderBy('type')
                ->orderBy('version', 'desc')
                ->get();

            $results = [
                'coa_id' => $coa->id,
                'serial' => $coa->serial,
                'total_policies' => $policies->count(),
                'policies_by_type' => [],
                'policies' => []
            ];

            // Group by type
            foreach (self::POLICY_TYPES as $type => $description) {
                $typePolicies = $policies->where('type', $type);
                $currentPolicy = $typePolicies->where('status', 'active')->first();

                $results['policies_by_type'][$type] = [
                    'type' => $type,
                    'description' => $description,
                    'count' => $typePolicies->count(),
                    'current_version' => $currentPolicy?->version,
                    'current_policy_id' => $currentPolicy?->id,
                    'effective_date' => $currentPolicy?->effective_date,
                    'expiry_date' => $currentPolicy?->expiry_date,
                    'policies' => $typePolicies->values()->toArray()
                ];
            }

            // All policies list
            foreach ($policies as $policy) {
                $results['policies'][] = [
                    'id' => $policy->id,
                    'type' => $policy->type,
                    'type_description' => self::POLICY_TYPES[$policy->type] ?? $policy->type,
                    'version' => $policy->version,
                    'status' => $policy->status,
                    'description' => $policy->description,
                    'created_by' => $policy->created_by,
                    'created_at' => $policy->created_at,
                    'effective_date' => $policy->effective_date,
                    'expiry_date' => $policy->expiry_date,
                    'terms_count' => count($policy->terms ?? []),
                    'hash' => $policy->hash
                ];
            }

            return $results;
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_GET_POLICIES_ERROR', [
                'user_id' => Auth::id(),
                'coa_id' => $coa->id,
                'active_only' => $activeOnly,
                'error' => $e->getMessage(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            return [
                'coa_id' => $coa->id,
                'error' => true,
                'message' => 'Failed to retrieve policies'
            ];
        }
    }

    /**
     * Get next addendum version number
     *
     * @param Coa $coa
     * @return int
     * @privacy-safe Version calculation only
     */
    protected function getNextAddendumVersion(Coa $coa): int {
        $lastVersion = CoaAddendum::where('coa_id', $coa->id)
            ->max('version');

        return ($lastVersion ?? 0) + 1;
    }

    /**
     * Get next policy version number for specific type
     *
     * @param Coa $coa
     * @param string $type
     * @return int
     * @privacy-safe Version calculation only
     */
    protected function getNextPolicyVersion(Coa $coa, string $type): int {
        $lastVersion = CoaPolicy::where('coa_id', $coa->id)
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
            case 'addendum_created':
                return sprintf(
                    'Addendum %s v%d created: %s',
                    $eventData['addendum_type'] ?? 'unknown',
                    $eventData['version'] ?? 0,
                    $eventData['reason'] ?? 'No reason specified'
                );
            case 'policy_created':
                return sprintf(
                    'Policy %s v%d created: %s',
                    $eventData['policy_type'] ?? 'unknown',
                    $eventData['version'] ?? 0,
                    self::POLICY_TYPES[$eventData['policy_type']] ?? 'Unknown Policy'
                );
            default:
                return "CoA event: {$eventType}";
        }
    }

    /**
     * Get available addendum types
     *
     * @return array Available types with descriptions
     * @privacy-safe Returns static configuration data
     */
    public function getAvailableAddendumTypes(): array {
        return self::ADDENDUM_TYPES;
    }

    /**
     * Get available policy types
     *
     * @return array Available types with descriptions
     * @privacy-safe Returns static configuration data
     */
    public function getAvailablePolicyTypes(): array {
        return self::POLICY_TYPES;
    }

    /**
     * Verify addendum data integrity
     *
     * @param CoaAddendum $addendum The addendum to verify
     * @return array Verification results
     * @privacy-safe Verification only, no data modification
     */
    public function verifyAddendumIntegrity(CoaAddendum $addendum): array {
        try {
            $this->logger->info('[CoA Addendum] Verifying addendum integrity', [
                'addendum_id' => $addendum->id,
                'coa_id' => $addendum->coa_id,
                'type' => $addendum->type,
                'version' => $addendum->version
            ]);

            $results = [
                'addendum_id' => $addendum->id,
                'is_valid' => false,
                'checks' => []
            ];

            // Check hash integrity
            $verificationData = [
                'content' => $addendum->content,
                'metadata' => $addendum->metadata,
                'reason' => $addendum->reason,
                'type' => $addendum->type
            ];

            $currentHash = $this->hashingService->generateHash($verificationData);
            $hashValid = hash_equals($addendum->hash, $currentHash);

            $results['checks']['hash_integrity'] = [
                'valid' => $hashValid,
                'stored_hash' => $addendum->hash,
                'calculated_hash' => $currentHash
            ];

            // Check content structure
            $contentValid = !empty($addendum->content) && is_array($addendum->content);
            $results['checks']['content_structure'] = [
                'valid' => $contentValid,
                'has_content' => !empty($addendum->content),
                'is_array' => is_array($addendum->content)
            ];

            // Check type validity
            $typeValid = array_key_exists($addendum->type, self::ADDENDUM_TYPES);
            $results['checks']['type_validity'] = [
                'valid' => $typeValid,
                'type' => $addendum->type
            ];

            // Check version
            $versionValid = $addendum->version > 0;
            $results['checks']['version_validity'] = [
                'valid' => $versionValid,
                'version' => $addendum->version
            ];

            // Overall validity
            $results['is_valid'] = $hashValid && $contentValid && $typeValid && $versionValid;

            $this->logger->info('[CoA Addendum] Integrity verification completed', [
                'addendum_id' => $addendum->id,
                'is_valid' => $results['is_valid'],
                'checks_passed' => count(array_filter($results['checks'], fn($c) => $c['valid']))
            ]);

            return $results;
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_ADDENDUM_INTEGRITY_ERROR', [
                'addendum_id' => $addendum->id,
                'coa_id' => $addendum->coa_id,
                'error' => $e->getMessage(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            return [
                'addendum_id' => $addendum->id,
                'is_valid' => false,
                'error' => 'Verification failed',
                'checks' => []
            ];
        }
    }

    /**
     * Verify policy data integrity
     *
     * @param CoaPolicy $policy The policy to verify
     * @return array Verification results
     * @privacy-safe Verification only, no data modification
     */
    public function verifyPolicyIntegrity(CoaPolicy $policy): array {
        try {
            $this->logger->info('[CoA Policy] Verifying policy integrity', [
                'policy_id' => $policy->id,
                'coa_id' => $policy->coa_id,
                'type' => $policy->type,
                'version' => $policy->version
            ]);

            $results = [
                'policy_id' => $policy->id,
                'is_valid' => false,
                'checks' => []
            ];

            // Check hash integrity
            $verificationData = [
                'terms' => $policy->terms,
                'description' => $policy->description,
                'type' => $policy->type,
                'effective_date' => $policy->effective_date?->format('Y-m-d H:i:s'),
                'expiry_date' => $policy->expiry_date?->format('Y-m-d H:i:s')
            ];

            $currentHash = $this->hashingService->generateHash($verificationData);
            $hashValid = hash_equals($policy->hash, $currentHash);

            $results['checks']['hash_integrity'] = [
                'valid' => $hashValid,
                'stored_hash' => $policy->hash,
                'calculated_hash' => $currentHash
            ];

            // Check terms structure
            $termsValid = !empty($policy->terms) && is_array($policy->terms);
            $results['checks']['terms_structure'] = [
                'valid' => $termsValid,
                'has_terms' => !empty($policy->terms),
                'is_array' => is_array($policy->terms)
            ];

            // Check type validity
            $typeValid = array_key_exists($policy->type, self::POLICY_TYPES);
            $results['checks']['type_validity'] = [
                'valid' => $typeValid,
                'type' => $policy->type
            ];

            // Check dates validity
            $datesValid = true;
            if ($policy->expiry_date && $policy->effective_date) {
                $datesValid = $policy->expiry_date > $policy->effective_date;
            }

            $results['checks']['dates_validity'] = [
                'valid' => $datesValid,
                'effective_date' => $policy->effective_date?->format('Y-m-d H:i:s'),
                'expiry_date' => $policy->expiry_date?->format('Y-m-d H:i:s')
            ];

            // Overall validity
            $results['is_valid'] = $hashValid && $termsValid && $typeValid && $datesValid;

            $this->logger->info('[CoA Policy] Integrity verification completed', [
                'policy_id' => $policy->id,
                'is_valid' => $results['is_valid'],
                'checks_passed' => count(array_filter($results['checks'], fn($c) => $c['valid']))
            ]);

            return $results;
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_POLICY_INTEGRITY_ERROR', [
                'policy_id' => $policy->id,
                'coa_id' => $policy->coa_id,
                'error' => $e->getMessage(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            return [
                'policy_id' => $policy->id,
                'is_valid' => false,
                'error' => 'Verification failed',
                'checks' => []
            ];
        }
    }
}
