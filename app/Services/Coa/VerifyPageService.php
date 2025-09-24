<?php

namespace App\Services\Coa;

use App\Models\Coa;
use App\Models\CoaEvent;
use App\Services\Coa\HashingService;
use App\Services\Coa\AnnexService;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;

/**
 * @Oracode Service: CoA Pro Public Verification
 * 🎯 Purpose: Generate public verification pages and data for CoA certificates
 * 🛡️ Privacy: Public verification with controlled data exposure
 * 🧱 Core Logic: Manages public verification, QR codes, and integrity checking
 *
 * @package App\Services\Coa
 * @author AI Assistant following FlorenceEGI patterns
 * @version 1.0.0 (CoA Pro System)
 * @date 2025-09-18
 * @purpose Public verification management for certificate transparency
 */
class VerifyPageService {
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
     * Hashing service for integrity verification
     * @var HashingService
     */
    protected HashingService $hashingService;

    /**
     * Annex service for public annex data
     * @var AnnexService
     */
    protected AnnexService $annexService;

    /**
     * Constructor with dependency injection
     *
     * @param UltraLogManager $logger
     * @param ErrorManagerInterface $errorManager
     * @param HashingService $hashingService
     * @param AnnexService $annexService
     * @privacy-safe All injected services handle public data safely
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        HashingService $hashingService,
        AnnexService $annexService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->hashingService = $hashingService;
        $this->annexService = $annexService;
    }

    /**
     * Generate verification data for a certificate
     *
     * @param Coa $coa The certificate to verify
     * @return array Verification data
     * @privacy-safe Returns only public verification information
     *
     * @oracode-dimension governance
     * @value-flow Provides public verification for certificate authenticity
     * @community-impact Builds trust through transparent verification
     * @transparency-level High - public verification data
     * @narrative-coherence Links certificates to public verification system
     */
    public function generateVerificationData(Coa $coa): array {
        try {
            $this->logger->info('[Verify Service] Generating verification data', [
                'coa_id' => $coa->id,
                'serial' => $coa->serial,
                'status' => $coa->status
            ]);

            // Basic certificate information (public)
            $verificationData = [
                'certificate' => [
                    'serial' => $coa->serial,
                    'status' => $coa->status,
                    'issued_at' => optional($coa->issued_at)->toIso8601String(),
                    'issued_by' => $coa->issuer_name,
                    'is_valid' => $coa->status === 'valid',
                    'verification_hash' => $coa->verification_hash,
                    'verification_url' => $coa->verification_hash
                        ? URL::route('coa.verify.view', $coa->verification_hash)
                        : URL::route('coa.verify.certificate.view', $coa->serial)
                ],
                'artwork' => [
                    'name' => $coa->egi->name,
                    'description' => $coa->egi->description,
                    'created_date' => $coa->egi->created_at->format('Y-m-d')
                ],
                'integrity' => [
                    'hash_verified' => $this->verifyIntegrity($coa),
                    'last_verified' => now()->toIso8601String()
                ],
                'verification_info' => [
                    'verified_at' => now()->toIso8601String(),
                    'verification_method' => 'digital_signature',
                    'system_version' => '1.0.0'
                ]
            ];

            // Add public annexes summary if available
            if ($coa->annexes()->where('status', 'active')->exists()) {
                $verificationData['annexes'] = $this->getPublicAnnexesSummary($coa);
            }

            // Add recent events (limited public information)
            $verificationData['recent_activity'] = $this->getPublicEvents($coa);

            $this->logger->info('[Verify Service] Verification data generated', [
                'coa_id' => $coa->id,
                'serial' => $coa->serial,
                'is_valid' => $verificationData['certificate']['is_valid'],
                'has_annexes' => isset($verificationData['annexes'])
            ]);

            return $verificationData;
        } catch (\Exception $e) {
            $this->errorManager->handle('VERIFY_DATA_GENERATION_ERROR', [
                'coa_id' => $coa->id,
                'serial' => $coa->serial,
                'error' => $e->getMessage(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            // Return minimal data on error
            return [
                'certificate' => [
                    'serial' => $coa->serial,
                    'status' => 'verification_error',
                    'is_valid' => false,
                    'error' => 'Verification data unavailable'
                ],
                'verification_info' => [
                    'verified_at' => now()->toIso8601String(),
                    'error' => true
                ]
            ];
        }
    }

    /**
     * Generate verification page data
     *
     * @param Coa $coa The certificate
     * @return array Page data
     * @privacy-safe Returns public page data only
     */
    public function generateVerificationPage(Coa $coa): array {
        try {
            $this->logger->info('[Verify Service] Generating verification page', [
                'coa_id' => $coa->id,
                'serial' => $coa->serial
            ]);

            $pageData = [
                'success' => true,
                'page_type' => 'certificate_verification',
                'title' => "Certificate Verification - {$coa->serial}",
                'meta' => [
                    'description' => "Public verification page for Certificate of Authenticity {$coa->serial}",
                    'keywords' => 'certificate, authenticity, verification, artwork, blockchain',
                    'robots' => 'index,follow'
                ],
                'certificate' => $this->generateVerificationData($coa),
                'ui_components' => [
                    'qr_code' => $this->generateQrCode($coa),
                    'verification_badge' => $this->generateVerificationBadge($coa),
                    'share_links' => $this->generateShareLinks($coa)
                ],
                'page_metadata' => [
                    'generated_at' => now()->toIso8601String(),
                    'cache_expires' => now()->addHour()->toIso8601String(),
                    'page_version' => '1.0.0'
                ]
            ];

            return $pageData;
        } catch (\Exception $e) {
            $this->errorManager->handle('VERIFY_PAGE_GENERATION_ERROR', [
                'coa_id' => $coa->id,
                'serial' => $coa->serial,
                'error' => $e->getMessage(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            return [
                'success' => false,
                'error' => 'Unable to generate verification page',
                'page_type' => 'error',
                'certificate' => [
                    'serial' => $coa->serial,
                    'status' => 'page_error'
                ]
            ];
        }
    }

    /**
     * Verify certificate hash integrity
     *
     * @param Coa $coa The certificate
     * @param string $providedHash Hash to verify
     * @param string|null $component Component to verify (certificate, traits, snapshot)
     * @return array Verification results
     * @privacy-safe Hash verification without exposing sensitive data
     */
    public function verifyHash(Coa $coa, string $providedHash, ?string $component = null): array {
        try {
            $this->logger->info('[Verify Service] Verifying hash', [
                'coa_id' => $coa->id,
                'serial' => $coa->serial,
                'component' => $component,
                'provided_hash' => substr($providedHash, 0, 8) . '...' // Log only first 8 chars
            ]);

            $results = [
                'certificate_serial' => $coa->serial,
                'hash_provided' => $providedHash,
                'component' => $component ?? 'certificate',
                'is_valid' => false,
                'verification_details' => []
            ];

            switch ($component) {
                case 'traits':
                    // Verify hash of traits section from snapshot against provided
                    $traits = $coa->snapshot?->snapshot_json['traits'] ?? null;
                    if ($traits) {
                        $calc = $this->hashingService->generateHash($traits);
                        $results['is_valid'] = hash_equals($calc, $providedHash);
                        $results['verification_details']['calculated_hash'] = $calc;
                    }
                    break;

                case 'snapshot':
                    // Verify full snapshot hash against provided
                    $snap = $coa->snapshot?->snapshot_json ?? null;
                    if ($snap) {
                        $snapshotHash = $this->hashingService->generateHash($snap);
                        $results['is_valid'] = hash_equals($snapshotHash, $providedHash);
                        $results['verification_details']['calculated_hash'] = $snapshotHash;
                    }
                    break;

                case 'certificate':
                default:
                    // Verify stored verification/integrity hash
                    $stored = $coa->integrity_hash ?: $coa->verification_hash;
                    if ($stored) {
                        $results['is_valid'] = hash_equals($stored, $providedHash);
                        $results['verification_details']['stored_hash'] = $stored;
                    }
                    break;
            }

            $results['verified_at'] = now()->toIso8601String();
            $results['verification_method'] = 'hash_comparison';

            $this->logger->info('[Verify Service] Hash verification completed', [
                'coa_id' => $coa->id,
                'component' => $component,
                'is_valid' => $results['is_valid']
            ]);

            return $results;
        } catch (\Exception $e) {
            $this->errorManager->handle('VERIFY_HASH_ERROR', [
                'coa_id' => $coa->id,
                'provided_hash' => substr($providedHash, 0, 8) . '...',
                'component' => $component,
                'error' => $e->getMessage(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            return [
                'certificate_serial' => $coa->serial,
                'hash_provided' => $providedHash,
                'component' => $component ?? 'certificate',
                'is_valid' => false,
                'error' => 'Hash verification failed',
                'verified_at' => now()->toIso8601String()
            ];
        }
    }

    /**
     * Get public annexes data
     *
     * @param Coa $coa The certificate
     * @return array Public annexes data
     * @privacy-safe Returns only public portions of annexes
     */
    public function getPublicAnnexes(Coa $coa): array {
        try {
            $this->logger->info('[Verify Service] Getting public annexes', [
                'coa_id' => $coa->id,
                'serial' => $coa->serial
            ]);

            $publicAnnexes = [
                'certificate_serial' => $coa->serial,
                'annexes_available' => true,
                'total_annexes' => 0,
                'annexes' => []
            ];

            // Get active annexes
            $annexes = $coa->annexes()->where('status', 'active')->get();
            $publicAnnexes['total_annexes'] = $annexes->count();

            foreach ($annexes as $annex) {
                $publicAnnexes['annexes'][] = [
                    'type' => $annex->type,
                    'type_description' => AnnexService::ANNEX_TYPES[$annex->type] ?? $annex->type,
                    'version' => $annex->version,
                    'issued_at' => $annex->issued_at,
                    'issued_by' => $annex->issued_by,
                    'hash' => $annex->hash,
                    'has_public_data' => !empty($annex->data['public'] ?? []),
                    'public_summary' => $annex->data['public_summary'] ?? null
                ];
            }

            $this->logger->info('[Verify Service] Public annexes retrieved', [
                'coa_id' => $coa->id,
                'total_annexes' => $publicAnnexes['total_annexes']
            ]);

            return $publicAnnexes;
        } catch (\Exception $e) {
            $this->errorManager->handle('VERIFY_PUBLIC_ANNEXES_ERROR', [
                'coa_id' => $coa->id,
                'error' => $e->getMessage(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            return [
                'certificate_serial' => $coa->serial,
                'annexes_available' => false,
                'error' => 'Unable to retrieve annexes',
                'total_annexes' => 0,
                'annexes' => []
            ];
        }
    }

    /**
     * Generate QR code for certificate
     *
     * @param Coa $coa The certificate
     * @return array QR code data
     * @privacy-safe Generates public QR code
     */
    public function generateQrCode(Coa $coa): array {
        try {
            $verificationUrl = $coa->verification_hash
                ? URL::route('coa.verify.view', $coa->verification_hash)
                : URL::route('coa.verify.certificate.view', $coa->serial);

            $qrData = [
                'url' => $verificationUrl,
                'format' => 'svg',
                'size' => 200,
                'error_correction' => 'M',
                'content' => [
                    'type' => 'certificate_verification',
                    'serial' => $coa->serial,
                    'verification_url' => $verificationUrl,
                    'generated_at' => now()->toIso8601String()
                ]
            ];

            // In a real implementation, you would generate actual QR code here
            $qrData['svg_content'] = $this->generateQrSvg($verificationUrl);

            // Persist QR code data if not stored yet
            if (!$coa->qr_code_data) {
                $coa->update(['qr_code_data' => $verificationUrl]);
            }
            return $qrData;
        } catch (\Exception $e) {
            $this->errorManager->handle('VERIFY_QR_GENERATION_ERROR', [
                'coa_id' => $coa->id,
                'error' => $e->getMessage(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            return [
                'error' => 'QR code generation failed',
                'url' => ($coa->verification_hash
                    ? URL::route('coa.verify.view', $coa->verification_hash)
                    : URL::route('coa.verify.certificate.view', $coa->serial))
            ];
        }
    }

    /**
     * Batch verify multiple certificates
     *
     * @param array $serials Array of certificate serials
     * @return array Batch verification results
     * @privacy-safe Batch verification with public data only
     */
    public function batchVerify(array $serials): array {
        try {
            $this->logger->info('[Verify Service] Batch verification started', [
                'serials_count' => count($serials),
                'serials' => $serials
            ]);

            $results = [];

            foreach ($serials as $serial) {
                $coa = Coa::where('serial', $serial)->first();

                if ($coa) {
                    $results[$serial] = [
                        'found' => true,
                        'verified' => $coa->status === 'valid',
                        'status' => $coa->status,
                        'issue_date' => $coa->issue_date,
                        'artwork_name' => $coa->egi->name
                    ];
                } else {
                    $results[$serial] = [
                        'found' => false,
                        'verified' => false,
                        'status' => 'not_found'
                    ];
                }
            }

            $this->logger->info('[Verify Service] Batch verification completed', [
                'serials_count' => count($serials),
                'found_count' => count(array_filter($results, fn($r) => $r['found'])),
                'verified_count' => count(array_filter($results, fn($r) => $r['verified']))
            ]);

            return [
                'batch_id' => uniqid('batch_'),
                'processed_count' => count($serials),
                'found_count' => count(array_filter($results, fn($r) => $r['found'])),
                'verified_count' => count(array_filter($results, fn($r) => $r['verified'])),
                'results' => $results,
                'processed_at' => now()->toIso8601String()
            ];
        } catch (\Exception $e) {
            $this->errorManager->handle('VERIFY_BATCH_ERROR', [
                'serials' => $serials,
                'error' => $e->getMessage(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            return [
                'error' => 'Batch verification failed',
                'processed_count' => 0,
                'results' => []
            ];
        }
    }

    /**
     * Verify certificate integrity
     *
     * @param Coa $coa The certificate
     * @return bool True if integrity check passes
     * @privacy-safe Internal integrity verification
     */
    protected function verifyIntegrity(Coa $coa): bool {
        try {
            $snapshot = $coa->snapshot?->snapshot_json;
            if (!$snapshot) {
                return false;
            }
            $calculated = $this->hashingService->generateHash([
                'serial' => $coa->serial,
                'snapshot' => $snapshot,
                'issued_at' => optional($coa->issued_at)->toIso8601String(),
                'egi_id' => $coa->egi_id
            ]);
            $stored = $coa->integrity_hash ?: $coa->verification_hash;
            return $stored ? hash_equals($stored, $calculated) : false;
        } catch (\Exception $e) {
            $this->logger->warning('[Verify Service] Integrity check failed', [
                'coa_id' => $coa->id,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Get public annexes summary
     *
     * @param Coa $coa The certificate
     * @return array Annexes summary
     * @privacy-safe Returns public summary only
     */
    protected function getPublicAnnexesSummary(Coa $coa): array {
        $annexes = $coa->annexes()->where('status', 'active')->get();

        $summary = [
            'total_count' => $annexes->count(),
            'types_available' => [],
            'latest_update' => null
        ];

        foreach ($annexes as $annex) {
            if (!in_array($annex->type, $summary['types_available'])) {
                $summary['types_available'][] = $annex->type;
            }

            if (!$summary['latest_update'] || $annex->issued_at > $summary['latest_update']) {
                $summary['latest_update'] = $annex->issued_at;
            }
        }

        return $summary;
    }

    /**
     * Get public events for certificate
     *
     * @param Coa $coa The certificate
     * @return array Public events
     * @privacy-safe Returns limited public event information
     */
    protected function getPublicEvents(Coa $coa): array {
        $events = CoaEvent::where('coa_id', $coa->id)
            ->whereIn('type', ['ISSUED', 'ANNEX_ADDED'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $publicEvents = [];

        foreach ($events as $event) {
            $publicEvents[] = [
                'event_type' => $event->type,
                'occurred_at' => $event->created_at,
                'description' => $this->getPublicEventDescription($event->type)
            ];
        }

        return $publicEvents;
    }

    /**
     * Get public event description
     *
     * @param string $eventType
     * @return string Public description
     * @privacy-safe Returns safe public descriptions
     */
    protected function getPublicEventDescription(string $eventType): string {
        return match ($eventType) {
            'certificate_issued' => 'Certificate issued',
            'certificate_verified' => 'Certificate verified',
            'annex_added' => 'Documentation updated',
            default => 'Certificate activity'
        };
    }

    /**
     * Generate verification badge data
     *
     * @param Coa $coa The certificate
     * @return array Badge data
     * @privacy-safe Generates public verification badge
     */
    protected function generateVerificationBadge(Coa $coa): array {
        return [
            'type' => $coa->status === 'valid' ? 'verified' : 'invalid',
            'status' => $coa->status,
            'text' => $coa->status === 'valid' ? 'Verified Authentic' : 'Not Verified',
            'color' => $coa->status === 'valid' ? 'green' : 'red',
            'icon' => $coa->status === 'valid' ? 'check-circle' : 'x-circle'
        ];
    }

    /**
     * Generate share links for certificate
     *
     * @param Coa $coa The certificate
     * @return array Share links
     * @privacy-safe Generates public share links
     */
    protected function generateShareLinks(Coa $coa): array {
        $verificationUrl = $coa->verification_hash
            ? URL::route('coa.verify.view', $coa->verification_hash)
            : URL::route('coa.verify.certificate.view', $coa->serial);
        $title = "Certificate of Authenticity - {$coa->egi->name}";

        return [
            'verification_url' => $verificationUrl,
            'social_links' => [
                'twitter' => "https://twitter.com/intent/tweet?text=" . urlencode($title) . "&url=" . urlencode($verificationUrl),
                'facebook' => "https://www.facebook.com/sharer/sharer.php?u=" . urlencode($verificationUrl),
                'linkedin' => "https://www.linkedin.com/sharing/share-offsite/?url=" . urlencode($verificationUrl)
            ]
        ];
    }

    /**
     * Generate placeholder SVG for QR code
     *
     * @param string $url The URL to encode
     * @return string SVG content
     * @privacy-safe Generates placeholder SVG
     */
    protected function generateQrSvg(string $url): string {
        // This is a placeholder - in real implementation, use a QR code library
        return '<svg width="200" height="200" xmlns="http://www.w3.org/2000/svg">
            <rect width="200" height="200" fill="white"/>
            <rect x="10" y="10" width="180" height="180" fill="black"/>
            <rect x="20" y="20" width="160" height="160" fill="white"/>
            <text x="100" y="100" text-anchor="middle" fill="black" font-family="Arial" font-size="12">QR Code</text>
            <text x="100" y="120" text-anchor="middle" fill="black" font-family="Arial" font-size="8">Verification</text>
        </svg>';
    }
}
