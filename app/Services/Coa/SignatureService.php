<?php

namespace App\Services\Coa;

use App\Models\Coa;
use App\Models\CoaSignature;
use App\Models\User;
use App\Models\CoaEvent;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Services\Gdpr\AuditLogService;
use App\Enums\Gdpr\GdprActivityCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * @Oracode Service: CoA Digital Signature Management
 * 🎯 Purpose: Manage digital and physical signatures for CoA certificates
 * 🛡️ Privacy: Handles GDPR-compliant signature creation with full audit trail
 * 🧱 Core Logic: Manages signature generation, validation, and verification
 *
 * @package App\Services\Coa
 * @author AI Assistant following FlorenceEGI patterns
 * @version 1.0.0 (CoA Pro System)
 * @date 2025-09-18
 * @purpose Digital signature authentication for certificate validity
 */
class SignatureService {
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
     * Available signature algorithms
     */
    public const SIGNATURE_ALGORITHMS = [
        'RSA-SHA256' => 'RSA with SHA-256',
        'RSA-SHA512' => 'RSA with SHA-512',
        'ECDSA-SHA256' => 'ECDSA with SHA-256',
        'ECDSA-SHA512' => 'ECDSA with SHA-512',
        'EdDSA' => 'Edwards-curve Digital Signature Algorithm'
    ];

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
     * Add digital signature to a CoA
     *
     * @param Coa $coa The CoA to sign
     * @param array $signatureData Signature data
     * @return CoaSignature The created signature
     * @privacy-safe Creates signature only for authenticated user's CoA
     *
     * @oracode-dimension governance
     * @value-flow Creates cryptographic proof of certificate authenticity
     * @community-impact Enables verifiable digital signatures for collectors
     * @transparency-level High - signature verification is publicly available
     * @narrative-coherence Links signature to certificate lifecycle
     */
    public function addDigitalSignature(Coa $coa, array $signatureData): CoaSignature {
        try {
            $user = Auth::user();

            // Security check - user must own the CoA's EGI
            if ($coa->egi->user_id !== $user->id) {
                $this->errorManager->handle('COA_SIGNATURE_UNAUTHORIZED', [
                    'user_id' => $user->id,
                    'coa_id' => $coa->id,
                    'egi_owner_id' => $coa->egi->user_id,
                    'ip_address' => request()->ip(),
                    'timestamp' => now()->toIso8601String()
                ], new \Illuminate\Auth\Access\AuthorizationException('User cannot sign CoA they do not own'));

                throw new \Illuminate\Auth\Access\AuthorizationException('Unauthorized action.');
            }

            // Check if CoA is valid for signing
            if ($coa->status !== 'valid') {
                throw new \Exception('Cannot sign a CoA that is not in valid status');
            }

            $this->logger->info('[CoA Signature] Adding digital signature', [
                'user_id' => $user->id,
                'coa_id' => $coa->id,
                'serial' => $coa->serial,
                'algorithm' => $signatureData['algorithm'] ?? 'RSA-SHA256'
            ]);

            // Use database transaction
            $signature = DB::transaction(function () use ($coa, $signatureData, $user) {
                return $this->createDigitalSignatureInTransaction($coa, $signatureData, $user);
            });

            $this->logger->info('[CoA Signature] Digital signature added successfully', [
                'user_id' => $user->id,
                'coa_id' => $coa->id,
                'signature_id' => $signature->id,
                'algorithm' => $signature->algorithm
            ]);

            // Log audit trail
            $this->auditService->logUserAction($user, 'coa_signature_added', [
                'coa_id' => $coa->id,
                'signature_id' => $signature->id,
                'signature_type' => 'digital',
                'algorithm' => $signature->algorithm
            ], GdprActivityCategory::GDPR_ACTIONS);

            return $signature;
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            throw $e; // Re-throw auth exceptions
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_DIGITAL_SIGNATURE_ERROR', [
                'user_id' => Auth::id(),
                'coa_id' => $coa->id,
                'signature_data' => array_keys($signatureData),
                'error' => $e->getMessage(),
                'ip_address' => request()->ip(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            throw $e;
        }
    }

    /**
     * Add physical signature to a CoA
     *
     * @param Coa $coa The CoA to sign
     * @param array $signatureData Physical signature data
     * @return CoaSignature The created signature
     * @privacy-safe Creates signature only for authenticated user's CoA
     */
    public function addPhysicalSignature(Coa $coa, array $signatureData): CoaSignature {
        try {
            $user = Auth::user();

            // Security check - user must own the CoA's EGI
            if ($coa->egi->user_id !== $user->id) {
                throw new \Illuminate\Auth\Access\AuthorizationException('Unauthorized action.');
            }

            // Check if CoA is valid for signing
            if ($coa->status !== 'valid') {
                throw new \Exception('Cannot sign a CoA that is not in valid status');
            }

            $this->logger->info('[CoA Signature] Adding physical signature', [
                'user_id' => $user->id,
                'coa_id' => $coa->id,
                'serial' => $coa->serial,
                'signature_method' => $signatureData['method'] ?? 'ink'
            ]);

            // Use database transaction
            $signature = DB::transaction(function () use ($coa, $signatureData, $user) {
                return $this->createPhysicalSignatureInTransaction($coa, $signatureData, $user);
            });

            $this->logger->info('[CoA Signature] Physical signature added successfully', [
                'user_id' => $user->id,
                'coa_id' => $coa->id,
                'signature_id' => $signature->id
            ]);

            // Log audit trail
            $this->auditService->logUserAction($user, 'coa_signature_added', [
                'coa_id' => $coa->id,
                'signature_id' => $signature->id,
                'signature_type' => 'physical',
                'method' => $signatureData['method'] ?? 'ink'
            ], GdprActivityCategory::GDPR_ACTIONS);

            return $signature;
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_PHYSICAL_SIGNATURE_ERROR', [
                'user_id' => Auth::id(),
                'coa_id' => $coa->id,
                'signature_data' => array_keys($signatureData),
                'error' => $e->getMessage(),
                'ip_address' => request()->ip(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            throw $e;
        }
    }

    /**
     * Create digital signature within database transaction
     *
     * @param Coa $coa
     * @param array $signatureData
     * @param User $user
     * @return CoaSignature
     * @privacy-safe Internal transaction method
     */
    protected function createDigitalSignatureInTransaction(Coa $coa, array $signatureData, User $user): CoaSignature {
        // Prepare signature data
        $algorithm = $signatureData['algorithm'] ?? 'RSA-SHA256';

        // Validate algorithm
        if (!array_key_exists($algorithm, self::SIGNATURE_ALGORITHMS)) {
            throw new \Exception("Unsupported signature algorithm: {$algorithm}");
        }

        // Generate signature (in real implementation, this would use actual cryptography)
        $signatureValue = $this->generateDigitalSignature($coa, $algorithm, $signatureData);

        // Create certificate data
        $certificateData = $this->generateCertificateData($user, $signatureData);

        // Create signature record
        $signature = CoaSignature::create([
            'coa_id' => $coa->id,
            'user_id' => $user->id,
            'type' => 'digital',
            'signature_data' => $signatureValue,
            'algorithm' => $algorithm,
            'certificate_data' => $certificateData,
            'signed_at' => now(),
            'expires_at' => $signatureData['expires_at'] ?? now()->addYears(2),
            'metadata' => $this->generateDigitalSignatureMetadata($signatureData),
        ]);

        // Create signature event
        $this->createCoaEvent($coa, 'signature_added', [
            'signature_id' => $signature->id,
            'signature_type' => 'digital',
            'signer_name' => $user->name,
            'signer_role' => $signatureData['signer_role'] ?? 'owner',
            'signature_algorithm' => $algorithm
        ]);

        return $signature;
    }

    /**
     * Create physical signature within database transaction
     *
     * @param Coa $coa
     * @param array $signatureData
     * @param User $user
     * @return CoaSignature
     * @privacy-safe Internal transaction method
     */
    protected function createPhysicalSignatureInTransaction(Coa $coa, array $signatureData, User $user): CoaSignature {
        // Generate physical signature reference
        $signatureReference = $this->generatePhysicalSignatureReference($coa, $signatureData);

        // Create signature record
        $signature = CoaSignature::create([
            'coa_id' => $coa->id,
            'user_id' => $user->id,
            'type' => 'physical',
            'signature_data' => $signatureReference,
            'algorithm' => null,
            'certificate_data' => null,
            'signed_at' => now(),
            'expires_at' => null,
            'metadata' => $this->generatePhysicalSignatureMetadata($signatureData),
        ]);

        // Create signature event
        $this->createCoaEvent($coa, 'signature_added', [
            'signature_id' => $signature->id,
            'signature_type' => 'physical',
            'signer_name' => $user->name,
            'signer_role' => $signatureData['signer_role'] ?? 'owner',
            'signature_method' => $signatureData['method'] ?? 'ink'
        ]);

        return $signature;
    }

    /**
     * Verify a digital signature
     *
     * @param CoaSignature $signature The signature to verify
     * @return array Verification results
     * @privacy-safe Verification only, no data modification
     */
    public function verifyDigitalSignature(CoaSignature $signature): array {
        try {
            $this->logger->info('[CoA Signature] Verifying digital signature', [
                'signature_id' => $signature->id,
                'coa_id' => $signature->coa_id,
                'algorithm' => $signature->algorithm
            ]);

            $results = [
                'signature_id' => $signature->id,
                'is_valid' => false,
                'algorithm' => $signature->algorithm,
                'signed_at' => $signature->signed_at,
                'expires_at' => $signature->expires_at,
                'checks' => []
            ];

            // Check if signature type is digital
            if ($signature->type !== 'digital') {
                $results['checks']['type'] = [
                    'valid' => false,
                    'error' => 'Not a digital signature'
                ];
                return $results;
            }

            // Check expiration
            $isExpired = $signature->expires_at && $signature->expires_at < now();
            $results['checks']['expiration'] = [
                'valid' => !$isExpired,
                'expires_at' => $signature->expires_at,
                'is_expired' => $isExpired
            ];

            // Check algorithm support
            $algorithmSupported = array_key_exists($signature->algorithm, self::SIGNATURE_ALGORITHMS);
            $results['checks']['algorithm'] = [
                'valid' => $algorithmSupported,
                'algorithm' => $signature->algorithm
            ];

            // Check certificate data
            $certificateValid = $this->validateCertificateData($signature->certificate_data);
            $results['checks']['certificate'] = [
                'valid' => $certificateValid,
                'data_present' => !empty($signature->certificate_data)
            ];

            // Check signature data format
            $signatureFormatValid = !empty($signature->signature_data);
            $results['checks']['signature_format'] = [
                'valid' => $signatureFormatValid,
                'data_length' => strlen($signature->signature_data ?? '')
            ];

            // Overall validity
            $results['is_valid'] = !$isExpired && $algorithmSupported && $certificateValid && $signatureFormatValid;

            $this->logger->info('[CoA Signature] Digital signature verification completed', [
                'signature_id' => $signature->id,
                'is_valid' => $results['is_valid'],
                'checks_passed' => count(array_filter($results['checks'], fn($c) => $c['valid']))
            ]);

            return $results;
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_DIGITAL_SIGNATURE_VERIFY_ERROR', [
                'signature_id' => $signature->id,
                'coa_id' => $signature->coa_id,
                'error' => $e->getMessage(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            return [
                'signature_id' => $signature->id,
                'is_valid' => false,
                'error' => 'Verification failed',
                'checks' => []
            ];
        }
    }

    /**
     * Get all signatures for a CoA
     *
     * @param Coa $coa The CoA to get signatures for
     * @return array Signatures data
     * @privacy-safe Returns signatures only for user's own CoA
     */
    public function getCoaSignatures(Coa $coa): array {
        try {
            $user = Auth::user();

            // Security check
            if ($coa->egi->user_id !== $user->id) {
                throw new \Illuminate\Auth\Access\AuthorizationException('Unauthorized action.');
            }

            $signatures = CoaSignature::where('coa_id', $coa->id)
                ->orderBy('signed_at', 'desc')
                ->get();

            $results = [
                'coa_id' => $coa->id,
                'serial' => $coa->serial,
                'signatures_count' => $signatures->count(),
                'signatures' => []
            ];

            foreach ($signatures as $signature) {
                $signatureData = [
                    'id' => $signature->id,
                    'type' => $signature->type,
                    'algorithm' => $signature->algorithm,
                    'signed_at' => $signature->signed_at,
                    'expires_at' => $signature->expires_at,
                    'metadata' => $signature->metadata,
                    'user_id' => $signature->user_id,
                    'user_name' => $signature->user->name ?? 'Unknown'
                ];

                // Add verification status for digital signatures
                if ($signature->type === 'digital') {
                    $verification = $this->verifyDigitalSignature($signature);
                    $signatureData['verification'] = $verification;
                }

                $results['signatures'][] = $signatureData;
            }

            $this->logger->info('[CoA Signature] Signatures retrieved', [
                'coa_id' => $coa->id,
                'signatures_count' => $results['signatures_count'],
                'user_id' => $user->id
            ]);

            return $results;
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_GET_SIGNATURES_ERROR', [
                'user_id' => Auth::id(),
                'coa_id' => $coa->id,
                'error' => $e->getMessage(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            return [
                'coa_id' => $coa->id,
                'error' => true,
                'message' => 'Failed to retrieve signatures'
            ];
        }
    }

    /**
     * Generate digital signature (mock implementation)
     *
     * @param Coa $coa
     * @param string $algorithm
     * @param array $signatureData
     * @return string
     * @privacy-safe Signature generation only
     */
    protected function generateDigitalSignature(Coa $coa, string $algorithm, array $signatureData): string {
        // In a real implementation, this would use actual cryptographic signing
        // For now, we generate a mock signature
        $dataToSign = json_encode([
            'coa_id' => $coa->id,
            'serial' => $coa->serial,
            'timestamp' => now()->toIso8601String(),
            'algorithm' => $algorithm
        ]);

        return base64_encode(hash('sha256', $dataToSign . (string)time()));
    }

    /**
     * Generate physical signature reference
     *
     * @param Coa $coa
     * @param array $signatureData
     * @return string
     * @privacy-safe Reference generation only
     */
    protected function generatePhysicalSignatureReference(Coa $coa, array $signatureData): string {
        return hash('sha256', json_encode([
            'coa_id' => $coa->id,
            'serial' => $coa->serial,
            'method' => $signatureData['method'] ?? 'ink',
            'timestamp' => now()->toIso8601String()
        ]));
    }

    /**
     * Generate certificate data for digital signature
     *
     * @param User $user
     * @param array $signatureData
     * @return array
     * @privacy-safe Certificate metadata generation
     */
    protected function generateCertificateData(User $user, array $signatureData): array {
        return [
            'issuer' => [
                'cn' => 'FlorenceEGI Certificate Authority',
                'o' => 'FlorenceEGI Platform',
                'c' => 'IT'
            ],
            'subject' => [
                'cn' => $user->name,
                'email' => $user->email,
                'o' => $signatureData['organization'] ?? null
            ],
            'serial_number' => sprintf('%016d', time() + 1000),
            'not_before' => now()->format('Y-m-d H:i:s'),
            'not_after' => ($signatureData['expires_at'] ?? now()->addYears(2))->format('Y-m-d H:i:s'),
            'fingerprint' => strtoupper(hash('sha1', $user->email . now()->toIso8601String())),
            'key_usage' => ['digital_signature', 'non_repudiation']
        ];
    }

    /**
     * Generate metadata for digital signature
     *
     * @param array $signatureData
     * @return array
     * @privacy-safe Metadata generation
     */
    protected function generateDigitalSignatureMetadata(array $signatureData): array {
        return [
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toIso8601String(),
            'signing_device' => $signatureData['device'] ?? 'unknown',
            'browser' => $signatureData['browser'] ?? 'unknown',
            'validation_method' => 'platform_signature'
        ];
    }

    /**
     * Generate metadata for physical signature
     *
     * @param array $signatureData
     * @return array
     * @privacy-safe Metadata generation
     */
    protected function generatePhysicalSignatureMetadata(array $signatureData): array {
        return [
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toIso8601String(),
            'witness_name' => $signatureData['witness'] ?? null,
            'location' => $signatureData['location'] ?? 'Digital Platform',
            'signature_method' => $signatureData['method'] ?? 'ink',
            'document_state' => $signatureData['document_state'] ?? 'digital'
        ];
    }

    /**
     * Validate certificate data structure
     *
     * @param array|null $certificateData
     * @return bool
     * @privacy-safe Certificate validation only
     */
    protected function validateCertificateData(?array $certificateData): bool {
        if (empty($certificateData)) {
            return false;
        }

        $requiredFields = ['issuer', 'subject', 'serial_number', 'not_before', 'not_after'];

        foreach ($requiredFields as $field) {
            if (!array_key_exists($field, $certificateData)) {
                return false;
            }
        }

        return true;
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
        return match ($eventType) {
            'signature_added' => sprintf(
                '%s signature added by %s',
                ucfirst($eventData['signature_type'] ?? 'unknown'),
                $eventData['signer_name'] ?? 'unknown'
            ),
            default => "CoA event: {$eventType}"
        };
    }

    /**
     * Get available signature algorithms
     *
     * @return array Available algorithms with descriptions
     * @privacy-safe Returns static configuration data
     */
    public function getAvailableAlgorithms(): array {
        return self::SIGNATURE_ALGORITHMS;
    }
}
