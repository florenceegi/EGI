<?php

namespace App\Services\Coa;

use App\Models\Egi;
use App\Models\Coa;
use App\Models\CoaSnapshot;
use App\Models\EgiTraitsVersion;
use App\Models\CoaEvent;
use App\Services\Coa\TraitsSnapshotService;
use App\Services\Coa\SerialGenerator;
use App\Traits\EgiTraitsExtraction;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Services\Gdpr\AuditLogService;
use App\Enums\Gdpr\GdprActivityCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * @Oracode Service: CoA Certificate Issuance
 * 🎯 Purpose: Issue new CoA certificates for FlorenceEGI artworks
 * 🛡️ Privacy: Handles GDPR-compliant certificate creation with full audit trail
 * 🧱 Core Logic: Manages complete CoA issuance workflow with immutable snapshots
 *
 * @package App\Services\Coa
 * @author AI Assistant following FlorenceEGI patterns
 * @version 1.0.0 (CoA Pro System)
 * @date 2025-09-18
 * @purpose Professional certificate issuance for artwork authenticity
 */
class CoaIssueService {
    use EgiTraitsExtraction;
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
     * Traits snapshot service
     * @var TraitsSnapshotService
     */
    protected TraitsSnapshotService $snapshotService;

    /**
     * Serial number generator
     * @var SerialGenerator
     */
    protected SerialGenerator $serialGenerator;

    /**
     * Constructor with dependency injection
     *
     * @param UltraLogManager $logger
     * @param ErrorManagerInterface $errorManager
     * @param AuditLogService $auditService
     * @param TraitsSnapshotService $snapshotService
     * @param SerialGenerator $serialGenerator
     * @privacy-safe All injected services handle GDPR compliance
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService,
        TraitsSnapshotService $snapshotService,
        SerialGenerator $serialGenerator
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditService = $auditService;
        $this->snapshotService = $snapshotService;
        $this->serialGenerator = $serialGenerator;
    }

    /**
     * Issue a new CoA certificate for an EGI artwork
     *
     * @param Egi $egi The artwork to issue CoA for
     * @param array $issuerData Issuer information
     * @return Coa The newly issued CoA
     * @privacy-safe Creates certificate only for authenticated user's artwork
     *
     * @oracode-dimension governance
     * @value-flow Creates immutable certificate of authenticity
     * @community-impact Provides collectors with verified artwork provenance
     * @transparency-level High - complete certificate issuance process
     * @narrative-coherence Links artwork to permanent authenticity record
     */
    public function issueCertificate(Egi $egi, ?string $issuedBy = null, ?string $notes = null, ?string $location = null): ?Coa {
        try {
            $user = Auth::user();

            // Use provided issuer name or default to authenticated user
            $issuerName = $issuedBy ?? $user->name ?? 'System';

            // Security check - user must own the EGI
            if ($egi->user_id !== $user->id) {
                $this->errorManager->handle('COA_ISSUE_UNAUTHORIZED', [
                    'user_id' => $user->id,
                    'egi_id' => $egi->id,
                    'egi_owner_id' => $egi->user_id,
                    'ip_address' => request()->ip(),
                    'timestamp' => now()->toIso8601String()
                ], new \Illuminate\Auth\Access\AuthorizationException('User cannot issue CoA for EGI they do not own'));

                throw new \Illuminate\Auth\Access\AuthorizationException('Unauthorized action.');
            }

            // Check if EGI already has an active CoA
            $existingCoa = $egi->activeCoa()->first();
            if ($existingCoa) {
                $this->logger->warning('[CoA Issue] EGI already has active CoA', [
                    'user_id' => $user->id,
                    'egi_id' => $egi->id,
                    'existing_coa_id' => $existingCoa->id,
                    'existing_serial' => $existingCoa->serial
                ]);

                $this->errorManager->handle('COA_ISSUE_ALREADY_EXISTS', [
                    'user_id' => $user->id,
                    'egi_id' => $egi->id,
                    'existing_coa_id' => $existingCoa->id,
                    'existing_serial' => $existingCoa->serial,
                    'timestamp' => now()->toIso8601String()
                ], new \Exception('EGI already has an active CoA'));

                throw new \Exception('This artwork already has an active Certificate of Authenticity.');
            }

            $this->logger->info('[CoA Issue] Starting certificate issuance', [
                'user_id' => $user->id,
                'egi_id' => $egi->id,
                'egi_title' => $egi->title,
                'issuer_name' => $issuerName,
                'notes' => $notes
            ]);

            // Determine issue location: prefer provided location, otherwise derive from user's personal data
            $candidateLocation = $location ? trim($location) : null;
            if (empty($candidateLocation)) {
                try {
                    $candidateLocation = $egi->user ? $this->extractIssueLocation($egi->user) : null;
                } catch (\Throwable $t) {
                    // no-op, handled below if empty
                }
            }

            // Enforce location requirement for issuance
            if (empty($candidateLocation) || !is_string($candidateLocation) || mb_strlen(trim($candidateLocation)) < 3) {
                // Reuse existing UEM code mapped to SweetAlert for user feedback
                $this->errorManager->handle('COA_PDF_VALIDITY_MISSING_ISSUE_PLACE', [
                    'user_id' => $user->id,
                    'egi_id' => $egi->id,
                    'reason' => 'issuance_requires_location',
                ]);
                return null; // Stop issuance
            }

            // Use database transaction for atomic operation
            $coa = DB::transaction(function () use ($egi, $issuerName, $notes, $user, $candidateLocation) {
                return $this->issueCoaInTransaction($egi, $issuerName, $notes, $user, $candidateLocation);
            });

            $this->logger->info('[CoA Issue] Certificate issued successfully', [
                'user_id' => $user->id,
                'egi_id' => $egi->id,
                'coa_id' => $coa->id,
                'serial' => $coa->serial,
                'timestamp' => now()->toIso8601String()
            ]);

            // Log audit trail
            $this->auditService->logUserAction($user, 'coa_issued', [
                'egi_id' => $egi->id,
                'coa_id' => $coa->id,
                'serial' => $coa->serial,
                'issuer_type' => $coa->issuer_type,
                'issuer_name' => $coa->issuer_name
            ], GdprActivityCategory::GDPR_ACTIONS);

            // Centralized, robust auto-PDF trigger (post-commit)
            $auto = request()->has('auto_generate_pdf')
                ? request()->boolean('auto_generate_pdf')
                : (bool) config('coa.auto_generate_pdf', true);
            if ($auto) {
                DB::afterCommit(function () use ($coa, $user) {
                    try {
                        /** @var \Ultra\UltraLogManager\UltraLogManager $logger */
                        $logger = app(\Ultra\UltraLogManager\UltraLogManager::class);
                        /** @var \Ultra\ErrorManager\Interfaces\ErrorManagerInterface $errorManager */
                        $errorManager = app(\Ultra\ErrorManager\Interfaces\ErrorManagerInterface::class);

                        $logger->info('[CoA Issue] Post-commit Auto-PDF generation start', [
                            'coa_id' => $coa->id,
                            'serial' => $coa->serial,
                            'user_id' => $user->id
                        ]);

                        /** @var \App\Services\Coa\BundleService $bundleService */
                        $bundleService = app(\App\Services\Coa\BundleService::class);

                        // Generate only if not already available (avoids duplicates if controller already generated it)
                        if (method_exists($bundleService, 'pdfExists') && !$bundleService->pdfExists($coa)) {
                            $bundleService->generateCoaPdf($coa);
                        }

                        $logger->info('[CoA Issue] Post-commit Auto-PDF generation end', [
                            'coa_id' => $coa->id,
                            'serial' => $coa->serial,
                            'user_id' => $user->id
                        ]);
                    } catch (\Throwable $e) {
                        // Non-critical: auto-PDF failure MUST NOT break CoA issuance.
                        // UEM is intentionally skipped here — calling handle() with an
                        // undefined code throws UNEXPECTED and would propagate out of the
                        // afterCommit closure, killing the entire response.
                        app(\Ultra\UltraLogManager\UltraLogManager::class)->warning('[CoA Issue] Post-commit Auto-PDF failed (non-critical)', [
                            'coa_id' => $coa->id,
                            'serial' => $coa->serial,
                            'user_id' => $user->id,
                            'error' => $e->getMessage(),
                            'error_class' => get_class($e),
                        ]);
                    }
                });
            }

            return $coa;
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            throw $e; // Re-throw auth exceptions
        } catch (\Exception $e) {
            // Log dell'errore originale per debug
            \Log::error('[CoA Service] Errore durante issueCertificate', [
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'error_trace' => $e->getTraceAsString(),
                'egi_id' => $egi->id,
                'user_id' => Auth::id(),
                'issued_by' => $issuedBy,
                'notes' => $notes
            ]);

            // Utilizziamo la convenzione UEM standard - l'ErrorManager gestisce tutto
            $this->errorManager->handle('COA_ISSUE_CERTIFICATE_ERROR', [], $e);
            // UEM ha gestito l'errore, non ri-lanciamo l'eccezione
            return null; // Indica che l'operazione è fallita
        }
    }

    /**
     * Issue CoA within database transaction
     *
     * @param Egi $egi
     * @param string $issuerName
     * @param string|null $notes
     * @param \App\Models\User $user
     * @return Coa
     * @privacy-safe Internal transaction method
     */
    protected function issueCoaInTransaction(Egi $egi, string $issuerName, ?string $notes, $user, ?string $location = null): Coa {
        // 1. Generate unique serial number
        try {
            $serial = $this->serialGenerator->generateSerial();

            // Ensure we have a valid string (handle staging environment quirks)
            if (is_array($serial)) {
                $serial = reset($serial);
                $this->logger->warning('[CoA Issue] Generated serial was array, converted', [
                    'converted_serial' => $serial,
                    'egi_id' => $egi->id
                ]);
            }

            $serial = (string)$serial;

            if (empty($serial) || !str_contains($serial, 'COA-EGI')) {
                throw new \Exception('Invalid serial generated: ' . $serial);
            }
        } catch (\Exception $e) {
            $this->logger->error('[CoA Issue] Error generating serial', [
                'error' => $e->getMessage(),
                'error_type' => get_class($e),
                'egi_id' => $egi->id,
                'user_id' => $user->id
            ]);
            throw $e;
        }

        // 2. Create traits version snapshot
        $traitsVersion = $this->snapshotService->createTraitsVersion(
            $egi,
            'CoA issuance',
            ['all'] // All fields are included in initial CoA
        );

        // Se la creazione dello snapshot fallisce, usciamo dall'operazione
        if (!$traitsVersion) {
            throw new \Exception('Failed to create traits version snapshot');
        }

        // 3. Prepare issuer information
        $issuerInfo = $this->prepareIssuerData($issuerName, $user);

        // 4. Prepare creator info for role distinction
        $creatorInfo = $this->prepareCreatorInfo($egi, $user);

        // 5. Create CoA record
        $coa = Coa::create([
            'egi_id' => $egi->id,
            'serial' => $serial,
            'status' => 'valid',
            'issuer_type' => $issuerInfo['type'],
            'issuer_name' => $issuerInfo['name'],
            'issuer_location' => $issuerInfo['location'],
            'issued_at' => now(),
            'notes' => $notes, // Add notes field
            'creator_info' => $creatorInfo, // Store creator info when Creator ≠ Author
            'location' => $location,
        ]);

        // 5. Create CoA snapshot
        $snapshot = $this->snapshotService->createCoaSnapshot($coa, $traitsVersion);

        // Se la creazione dello snapshot fallisce, usciamo dall'operazione
        if (!$snapshot) {
            throw new \Exception('Failed to create CoA snapshot');
        }

        // 6. Calculate and update verification hash based on core CoA data
        $verificationHash = $this->calculateVerificationHash($coa, $snapshot, $issuerInfo);
        $coa->update(['verification_hash' => $verificationHash]);

        // 6b. Calculate integrity_hash (canonical minimal set) and default expires_at if policy applies
        try {
            $integrityPayload = [
                'serial' => $coa->serial,
                'issued_at' => $coa->issued_at->toISOString(),
                'snapshot' => $snapshot->snapshot_json,
            ];
            $flags = (\defined('JSON_SORT_KEYS') ? \JSON_SORT_KEYS : 0) | (\defined('JSON_UNESCAPED_UNICODE') ? \JSON_UNESCAPED_UNICODE : 0);
            $integrityJson = json_encode($integrityPayload, $flags);
            $integrityHash = hash('sha256', $integrityJson ?: '');
            $updates = ['integrity_hash' => $integrityHash];
            // Optional: set default expiration policy e.g., 10 years from issue
            if (empty($coa->expires_at)) {
                $updates['expires_at'] = now()->addYears(10);
            }
            // Persist QR verification URL
            $updates['qr_code_data'] = route('coa.verify.view', $verificationHash);
            $coa->update($updates);
        } catch (\Throwable $t) {
            $this->logger->warning('[CoA Issue] Integrity hash calculation failed', [
                'coa_id' => $coa->id,
                'error' => $t->getMessage()
            ]);
        }

        $this->logger->info('[CoA Issue] Verification hash calculated', [
            'coa_id' => $coa->id,
            'verification_hash' => $verificationHash,
            'hash_length' => strlen($verificationHash)
        ]);

        // 7. Create issuance event
        $this->createCoaEvent($coa, 'coa_issued', [
            'issuer_name' => $issuerInfo['name'],
            'issuer_organization' => $issuerInfo['organization'] ?? null,
            'issue_reason' => 'new_artwork',
            'initial_traits_hash' => $traitsVersion->traits_hash,
            'snapshot_id' => $snapshot->id
        ]);

        $this->logger->info('[CoA Issue] CoA created in transaction', [
            'coa_id' => $coa->id,
            'serial' => $serial,
            'traits_version_id' => $traitsVersion->id,
            'snapshot_id' => $snapshot->id,
            'issuer_type' => $issuerInfo['type']
        ]);

        return $coa;
    }

    /**
     * Prepare issuer data with defaults
     *
     * @param string $issuerName Issuer name
     * @param \App\Models\User $user Current user
     * @return array Prepared issuer information
     * @privacy-safe Prepares issuer metadata only
     */
    protected function prepareIssuerData(string $issuerName, $user): array {
        return [
            'type' => 'platform',
            'name' => $issuerName ?: ($user->name ?? 'FlorenceEGI Platform'),
            'location' => 'Digital Platform',
            'organization' => 'FlorenceEGI'
        ];
    }

    /**
     * Prepare creator information for role distinction (Author vs Creator vs Issuer)
     *
     * @param Egi $egi
     * @param \App\Models\User $user
     * @return array|null
     * @privacy-safe Extracts role distinction data from artwork using traits
     */
    protected function prepareCreatorInfo(Egi $egi, $user): ?array {
        // Extract author from traits using the trait helper
        $author = $this->extractAuthorFromTraits($egi);
        $creator = $egi->user->name ?? null;

        // Only store creator info when Creator ≠ Author
        if ($creator && $creator !== $author && $egi->user) {
            return [
                'name' => $creator,
                'user_id' => $egi->user->id,
                'role' => 'creator',
                'platform_role' => 'uploader',
                'relationship_to_author' => $this->determineCreatorRelationshipFromTraits($egi),
                'authorization_provided' => $this->hasAuthorizationInTraits($egi),
                'verification_method' => $this->extractVerificationMethod($egi),
                'created_at' => now()->toIso8601String()
            ];
        }

        return null;
    }

    /**
     * Determine relationship between creator and author using traits
     *
     * @param Egi $egi
     * @return string
     * @privacy-safe Determines relationship from traits data
     */
    protected function determineCreatorRelationshipFromTraits(Egi $egi): string {
        // Check for relationship information in traits first
        $relationshipFromTraits = $this->extractRelationshipFromTraits($egi);

        if ($relationshipFromTraits) {
            return $relationshipFromTraits;
        }

        // Check for authorization-related traits
        $authTraits = [
            'Autorizzazione',
            'Authorization',
            'Delega',
            'Mandate',
            'Rappresentanza',
            'Representation',
            'Procura',
            'Power of attorney'
        ];

        foreach ($authTraits as $traitName) {
            $authValue = $this->extractTraitValue($egi, [$traitName]);
            if ($authValue) {
                return 'Autorizzato: ' . $authValue;
            }
        }

        $author = $this->extractAuthorFromTraits($egi);
        $creator = $egi->user->name ?? null;

        if ($creator && $author) {
            // Simple heuristics based on names
            if (stripos($creator, 'gallery') !== false || stripos($creator, 'galleria') !== false) {
                return 'Rappresentante della galleria';
            }

            if (stripos($creator, 'estate') !== false || stripos($creator, 'eredi') !== false) {
                return 'Rappresentante degli eredi';
            }

            if (stripos($creator, 'archive') !== false || stripos($creator, 'archivio') !== false) {
                return 'Archivio autorizzato';
            }
        }

        return 'Caricatore della piattaforma';
    }

    /**
     * Extract verification method from traits
     *
     * @param Egi $egi
     * @return string
     */
    protected function extractVerificationMethod(Egi $egi): string {
        $verificationTraits = [
            'Verifica',
            'Verification',
            'Metodo verifica',
            'Verification method',
            'Convalida',
            'Validation',
            'Conferma',
            'Confirmation'
        ];

        $verification = $this->extractTraitValue($egi, $verificationTraits);

        if ($verification) {
            return $verification;
        }

        // Check if there are authorization documents in traits
        if ($this->hasAuthorizationInTraits($egi)) {
            return 'Documentazione in traits';
        }

        return 'Non specificato';
    }

    /**
     * Re-issue a CoA certificate (replaces existing with new one)
     *
     * @param Coa $existingCoa The existing CoA to replace
     * @param string $reason Reason for re-issuance
     * @param string|null $issuedBy New issuer name
     * @param string|null $notes Optional notes
     * @return Coa The new CoA certificate
     * @privacy-safe Re-issues only user's own certificates
     */
    public function reIssueCertificate(Coa $existingCoa, string $reason, ?string $issuedBy = null, ?string $notes = null): Coa {
        try {
            $user = Auth::user();

            // Security check - user must own the CoA's EGI
            if ($existingCoa->egi->user_id !== $user->id) {
                throw new \Illuminate\Auth\Access\AuthorizationException('Unauthorized action.');
            }

            $this->logger->info('[CoA Re-issue] Starting certificate re-issuance', [
                'user_id' => $user->id,
                'existing_coa_id' => $existingCoa->id,
                'existing_serial' => $existingCoa->serial,
                'reason' => $reason
            ]);

            // Use database transaction
            $newCoa = DB::transaction(function () use ($existingCoa, $reason, $issuedBy, $notes, $user) {
                return $this->reIssueCoaInTransaction($existingCoa, $reason, $issuedBy, $notes, $user);
            });

            $this->logger->info('[CoA Re-issue] Certificate re-issued successfully', [
                'user_id' => $user->id,
                'old_coa_id' => $existingCoa->id,
                'old_serial' => $existingCoa->serial,
                'new_coa_id' => $newCoa->id,
                'new_serial' => $newCoa->serial,
                'reason' => $reason
            ]);

            // Log audit trail
            $this->auditService->logUserAction($user, 'coa_reissued', [
                'old_coa_id' => $existingCoa->id,
                'old_serial' => $existingCoa->serial,
                'new_coa_id' => $newCoa->id,
                'new_serial' => $newCoa->serial,
                'reason' => $reason
            ], GdprActivityCategory::GDPR_ACTIONS);

            return $newCoa;
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_REISSUE_CERTIFICATE_ERROR', [
                'user_id' => Auth::id(),
                'existing_coa_id' => $existingCoa->id,
                'reason' => $reason,
                'error' => $e->getMessage(),
                'ip_address' => request()->ip(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            throw $e;
        }
    }

    /**
     * Re-issue CoA within database transaction
     *
     * @param Coa $existingCoa
     * @param string $reason
     * @param array $issuerData
     * @param \App\Models\User $user
     * @return Coa
     * @privacy-safe Internal transaction method
     */
    protected function reIssueCoaInTransaction(Coa $existingCoa, string $reason, ?string $issuedBy, ?string $notes, $user): Coa {
        // 1. Revoke existing CoA
        $existingCoa->update([
            'status' => 'revoked',
            'revoked_at' => now(),
            'revoke_reason' => "Re-issued: {$reason}"
        ]);

        // 2. Create revocation event
        $this->createCoaEvent($existingCoa, 'coa_revoked', [
            'revocation_reason' => "Re-issued: {$reason}",
            'revoked_by' => $user->name,
            'replacement_planned' => true
        ]);

        // 3. Issue new CoA
        $derivedLocation = null;
        try {
            $derivedLocation = $existingCoa->location ?: ($existingCoa->egi && $existingCoa->egi->user ? $this->extractIssueLocation($existingCoa->egi->user) : null);
        } catch (\Throwable $t) {
            $derivedLocation = $existingCoa->location;
        }

        $newCoa = $this->issueCoaInTransaction($existingCoa->egi, $issuedBy, $notes, $user, $derivedLocation);

        // 4. Create re-issuance event for new CoA
        $this->createCoaEvent($newCoa, 'coa_reissued', [
            'replaced_coa_id' => $existingCoa->id,
            'replaced_serial' => $existingCoa->serial,
            'reissue_reason' => $reason
        ]);

        return $newCoa;
    }

    /**
     * Check if an EGI can have a CoA issued
     *
     * @param Egi $egi The EGI to check
     * @return array Validation result with status and messages
     * @privacy-safe Validation only for user's own artwork
     */
    public function canIssueCoaForEgi(Egi $egi): array {
        try {
            $user = Auth::user();

            // Security check
            if ($egi->user_id !== $user->id) {
                return [
                    'can_issue' => false,
                    'reason' => 'unauthorized',
                    'message' => 'You can only issue CoA for your own artworks.'
                ];
            }

            // Check for existing active CoA
            $existingCoa = $egi->activeCoa();
            if ($existingCoa) {
                return [
                    'can_issue' => false,
                    'reason' => 'exists',
                    'message' => 'This artwork already has an active Certificate of Authenticity.',
                    'existing_coa' => [
                        'id' => $existingCoa->id,
                        'serial' => $existingCoa->serial,
                        'issued_at' => $existingCoa->issued_at
                    ]
                ];
            }

            // Check if EGI has required data
            $requiredFields = ['title', 'author', 'year'];
            $missingFields = [];

            foreach ($requiredFields as $field) {
                if (empty($egi->$field)) {
                    $missingFields[] = $field;
                }
            }

            if (!empty($missingFields)) {
                return [
                    'can_issue' => false,
                    'reason' => 'incomplete_data',
                    'message' => 'Artwork is missing required information for CoA issuance.',
                    'missing_fields' => $missingFields
                ];
            }

            return [
                'can_issue' => true,
                'reason' => 'valid',
                'message' => 'Artwork is ready for CoA issuance.'
            ];
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_ISSUE_VALIDATION_ERROR', [
                'user_id' => Auth::id(),
                'egi_id' => $egi->id,
                'error' => $e->getMessage(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            return [
                'can_issue' => false,
                'reason' => 'error',
                'message' => 'An error occurred while validating the artwork.',
                'error' => true
            ];
        }
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

        // Map event types to enum values
        $enumType = match ($eventType) {
            'coa_issued' => 'ISSUED',
            'coa_revoked' => 'REVOKED',
            'coa_reissued' => 'ISSUED', // Re-issuance is still an issuance
            default => 'ISSUED'
        };

        $event = CoaEvent::create([
            'coa_id' => $coa->id,
            'type' => $enumType,
            'payload' => array_merge($baseData, $eventData),
            'actor_id' => Auth::id(),
            'created_at' => now(),
        ]);

        $this->logger->info('[CoA Event] Event created', [
            'coa_id' => $coa->id,
            'event_id' => $event->id,
            'event_type' => $eventType,
            'user_id' => Auth::id()
        ]);

        return $event;
    }

    /**
     * Calculate verification hash based on core CoA data
     *
     * @param Coa $coa Certificate of Authenticity
     * @param CoaSnapshot $snapshot Immutable snapshot data
     * @param array $issuerInfo Issuer information
     * @return string SHA-256 verification hash
     * @privacy-safe Cryptographic hash generation only
     *
     * @oracode-dimension governance
     * @value-flow Creates cryptographic verification fingerprint
     * @community-impact Enables certificate integrity verification
     * @transparency-level High - hash components are auditable
     * @narrative-coherence Links certificate to its verification identity
     */
    protected function calculateVerificationHash(Coa $coa, CoaSnapshot $snapshot, array $issuerInfo): string {
        try {
            // Core data components for verification hash
            $coreData = [
                'serial' => $coa->serial,
                'issued_at' => $coa->issued_at->toISOString(),
                'issuer' => [
                    'type' => $coa->issuer_type,
                    'name' => $coa->issuer_name,
                    'location' => $coa->issuer_location,
                ],
                'snapshot_data' => $snapshot->snapshot_json,
                'metadata' => [
                    'egi_id' => $coa->egi_id,
                    'status' => $coa->status,
                    'creator_info' => $coa->creator_info,
                ]
            ];

            // Convert to normalized JSON for consistent hashing
            $flags = (\defined('JSON_SORT_KEYS') ? \JSON_SORT_KEYS : 0) | (\defined('JSON_UNESCAPED_UNICODE') ? \JSON_UNESCAPED_UNICODE : 0);
            $normalizedJson = json_encode($coreData, $flags);

            if ($normalizedJson === false) {
                throw new \Exception('Failed to encode core data for hashing');
            }

            // Generate SHA-256 hash
            $verificationHash = hash('sha256', $normalizedJson);

            $this->logger->info('[CoA Hash] Verification hash calculated', [
                'coa_id' => $coa->id,
                'data_components' => array_keys($coreData),
                'json_length' => strlen($normalizedJson),
                'hash_prefix' => substr($verificationHash, 0, 16) . '...',
                'log_category' => 'COA_VERIFICATION_HASH'
            ]);

            return $verificationHash;
        } catch (\Exception $e) {
            $this->logger->error('[CoA Hash] Verification hash calculation failed', [
                'coa_id' => $coa->id,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'log_category' => 'COA_VERIFICATION_HASH_ERROR'
            ]);

            // Re-throw for proper error handling
            throw new \Exception("Verification hash calculation failed: " . $e->getMessage(), 0, $e);
        }
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
        return match ($eventType) {
            'coa_issued' => 'Certificate of Authenticity issued',
            'coa_reissued' => 'Certificate of Authenticity re-issued',
            'coa_revoked' => 'Certificate of Authenticity revoked',
            default => "CoA event: {$eventType}"
        };
    }
}
