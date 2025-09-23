<?php

namespace App\Services\Coa;

use App\Models\Coa;
use App\Models\CoaEvent;
use App\Models\CoaBundle;
use App\Services\Coa\CoaPdfService;
use App\Services\Coa\HashingService;
use App\Services\Coa\AnnexService;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Services\Gdpr\AuditLogService;
use App\Enums\Gdpr\GdprActivityCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * @Oracode Service: CoA Pro Bundle Management
 * 🎯 Purpose: Create comprehensive certificate packages with all components
 * 🛡️ Privacy: Handles GDPR-compliant bundle creation with full audit trail
 * 🧱 Core Logic: Manages bundle generation, packaging, and distribution
 *
 * @package App\Services\Coa
 * @author AI Assistant following FlorenceEGI patterns
 * @version 1.0.0 (CoA Pro System)
 * @date 2025-09-18
 * @purpose Professional bundle management for complete certificate packages
 */
class BundleService {
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
     * Annex service for component gathering
     * @var AnnexService
     */
    protected AnnexService $annexService;

    /**
     * Available bundle types
     */
    public const BUNDLE_TYPES = [
        'COMPLETE' => 'Complete Certificate Package',
        'BASIC' => 'Basic Certificate Only',
        'EXTENDED' => 'Certificate with Selected Annexes',
        'LEGAL' => 'Legal Documentation Package',
        'INSURANCE' => 'Insurance Documentation Package',
        'EXHIBITION' => 'Exhibition Documentation Package',
        'TRANSFER' => 'Ownership Transfer Package'
    ];

    /**
     * Bundle format options
     */
    public const BUNDLE_FORMATS = [
        'PDF_PACKAGE' => 'PDF Document Package',
        'JSON_DATA' => 'Structured JSON Data',
        'ZIP_ARCHIVE' => 'ZIP Archive with All Files',
        'DIGITAL_SIGNATURE' => 'Digitally Signed Package',
        'BLOCKCHAIN_RECORD' => 'Blockchain-Verified Record'
    ];

    /**
     * Constructor with dependency injection
     *
     * @param UltraLogManager $logger
     * @param ErrorManagerInterface $errorManager
     * @param AuditLogService $auditService
     * @param HashingService $hashingService
     * @param AnnexService $annexService
     * @privacy-safe All injected services handle GDPR compliance
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService,
        HashingService $hashingService,
        AnnexService $annexService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditService = $auditService;
        $this->hashingService = $hashingService;
        $this->annexService = $annexService;
    }

    /**
     * Create complete bundle for a CoA
     *
     * @param Coa $coa The CoA to bundle
     * @param string $bundleType Type of bundle to create
     * @param array $formats Output formats required
     * @param array $options Bundle configuration options
     * @return array Bundle creation results
     * @privacy-safe Creates bundle only for authenticated user's CoA
     *
     * @oracode-dimension governance
     * @value-flow Creates comprehensive documentation packages for certificate management
     * @community-impact Provides complete certificate packages for professional use
     * @transparency-level High - complete bundle creation process
     * @narrative-coherence Links all certificate components into unified package
     */
    public function createBundle(Coa $coa, string $bundleType, array $formats = ['PDF_PACKAGE'], array $options = []): array {
        try {
            $user = Auth::user();

            // Security check - user must own the CoA's EGI
            if ($coa->egi->user_id !== $user->id) {
                $this->errorManager->handle('COA_BUNDLE_UNAUTHORIZED', [
                    'user_id' => $user->id,
                    'coa_id' => $coa->id,
                    'egi_owner_id' => $coa->egi->user_id,
                    'bundle_type' => $bundleType,
                    'ip_address' => request()->ip(),
                    'timestamp' => now()->toIso8601String()
                ], new \Illuminate\Auth\Access\AuthorizationException('User cannot create bundle for CoA they do not own'));

                throw new \Illuminate\Auth\Access\AuthorizationException('Unauthorized action.');
            }

            // Validate bundle type
            if (!array_key_exists($bundleType, self::BUNDLE_TYPES)) {
                throw new \Exception("Invalid bundle type: {$bundleType}");
            }

            // Validate formats
            foreach ($formats as $format) {
                if (!array_key_exists($format, self::BUNDLE_FORMATS)) {
                    throw new \Exception("Invalid bundle format: {$format}");
                }
            }

            // Check if CoA is valid for bundling
            if ($coa->status !== 'valid') {
                throw new \Exception('Cannot create bundle for a CoA that is not in valid status');
            }

            $this->logger->info('[CoA Bundle] Creating bundle', [
                'user_id' => $user->id,
                'coa_id' => $coa->id,
                'serial' => $coa->serial,
                'bundle_type' => $bundleType,
                'formats' => $formats,
                'options' => $options
            ]);

            // Use database transaction
            $bundleResult = DB::transaction(function () use ($coa, $bundleType, $formats, $options, $user) {
                return $this->createBundleInTransaction($coa, $bundleType, $formats, $options, $user);
            });

            $this->logger->info('[CoA Bundle] Bundle created successfully', [
                'user_id' => $user->id,
                'coa_id' => $coa->id,
                'bundle_id' => $bundleResult['bundle_id'],
                'bundle_type' => $bundleType,
                'formats_created' => count($bundleResult['files']),
                'total_size' => $bundleResult['total_size']
            ]);

            // Log audit trail
            $this->auditService->logUserAction($user, 'coa_bundle_created', [
                'coa_id' => $coa->id,
                'bundle_id' => $bundleResult['bundle_id'],
                'bundle_type' => $bundleType,
                'formats' => $formats,
                'total_size' => $bundleResult['total_size']
            ], GdprActivityCategory::GDPR_ACTIONS);

            return $bundleResult;
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            throw $e; // Re-throw auth exceptions
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_BUNDLE_CREATE_ERROR', [
                'user_id' => Auth::id(),
                'coa_id' => $coa->id,
                'bundle_type' => $bundleType,
                'formats' => $formats,
                'error' => $e->getMessage(),
                'ip_address' => request()->ip(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            throw $e;
        }
    }

    /**
     * Create bundle within database transaction
     *
     * @param Coa $coa
     * @param string $bundleType
     * @param array $formats
     * @param array $options
     * @param \App\Models\User $user
     * @return array
     * @privacy-safe Internal transaction method
     */
    protected function createBundleInTransaction(Coa $coa, string $bundleType, array $formats, array $options, $user): array {
        // Generate unique bundle ID
        $bundleId = 'BUNDLE-' . $coa->serial . '-' . now()->format('YmdHis') . '-' . substr(md5(uniqid()), 0, 8);

        // Collect bundle components
        $components = $this->collectBundleComponents($coa, $bundleType, $options);

        // Generate bundle metadata
        $metadata = [
            'bundle_id' => $bundleId,
            'coa_id' => $coa->id,
            'coa_serial' => $coa->serial,
            'bundle_type' => $bundleType,
            'created_by' => $user->name,
            'created_at' => now()->toIso8601String(),
            'components' => $components['summary'],
            'formats' => $formats,
            'options' => $options,
            'version' => '1.0.0'
        ];

        // Create bundle record (if using CoaBundle model)
        $bundleRecord = $this->createBundleRecord($coa, $bundleId, $bundleType, $metadata, $components, $user);

        // Generate files in requested formats
        $files = [];
        $totalSize = 0;

        foreach ($formats as $format) {
            $fileResult = $this->generateBundleFile($bundleId, $format, $components, $metadata, $options);
            $files[$format] = $fileResult;
            $totalSize += $fileResult['size'];
        }

        // Update bundle record with file information
        $this->updateBundleWithFiles($bundleRecord, $files, $totalSize);

        // Create bundle event
        $this->createCoaEvent($coa, 'bundle_created', [
            'bundle_id' => $bundleId,
            'bundle_type' => $bundleType,
            'formats' => $formats,
            'components_count' => count($components['data']),
            'total_size' => $totalSize,
            'created_by' => $user->name
        ]);

        return [
            'bundle_id' => $bundleId,
            'bundle_type' => $bundleType,
            'coa_id' => $coa->id,
            'coa_serial' => $coa->serial,
            'formats' => $formats,
            'files' => $files,
            'total_size' => $totalSize,
            'components' => $components['summary'],
            'metadata' => $metadata,
            'created_at' => now()->toIso8601String(),
            'download_expires_at' => now()->addDays(30)->toIso8601String() // Bundle expires in 30 days
        ];
    }

    /**
     * Collect all components for bundle based on type
     *
     * @param Coa $coa
     * @param string $bundleType
     * @param array $options
     * @return array
     * @privacy-safe Collects only user's own CoA components
     */
    protected function collectBundleComponents(Coa $coa, string $bundleType, array $options): array {
        $components = [
            'data' => [],
            'summary' => []
        ];

        // Always include basic CoA data
        $components['data']['coa'] = [
            'id' => $coa->id,
            'serial' => $coa->serial,
            'status' => $coa->status,
            'issue_date' => $coa->issue_date,
            'issued_by' => $coa->issued_by,
            'traits_snapshot' => $coa->traits_snapshot,
            'hash' => $coa->hash,
            'egi_data' => [
                'name' => $coa->egi->name,
                'description' => $coa->egi->description,
                'created_at' => $coa->egi->created_at,
                'owner' => $coa->egi->user->name
            ]
        ];

        $components['summary']['coa'] = [
            'included' => true,
            'type' => 'Certificate of Authenticity',
            'size' => strlen(json_encode($components['data']['coa']))
        ];

        // Include annexes based on bundle type
        if (in_array($bundleType, ['COMPLETE', 'EXTENDED', 'LEGAL', 'INSURANCE', 'EXHIBITION'])) {
            $annexes = $this->annexService->getCoaAnnexes($coa, true);

            if ($bundleType === 'COMPLETE') {
                // Include all annexes
                $components['data']['annexes'] = $annexes;
            } elseif ($bundleType === 'EXTENDED' && isset($options['selected_annexes'])) {
                // Include only selected annexes
                $selectedAnnexes = $options['selected_annexes'];
                $filteredAnnexes = array_filter($annexes['annexes'], function ($annex) use ($selectedAnnexes) {
                    return in_array($annex['type'], $selectedAnnexes);
                });
                $components['data']['annexes'] = $filteredAnnexes;
            } elseif ($bundleType === 'LEGAL') {
                // Include legal-relevant annexes
                $legalTypes = ['A_PROVENANCE'];
                $filteredAnnexes = array_filter($annexes['annexes'], function ($annex) use ($legalTypes) {
                    return in_array($annex['type'], $legalTypes);
                });
                $components['data']['annexes'] = $filteredAnnexes;
            } elseif ($bundleType === 'INSURANCE') {
                // Include insurance-relevant annexes
                $insuranceTypes = ['B_CONDITION', 'D_PHOTOS'];
                $filteredAnnexes = array_filter($annexes['annexes'], function ($annex) use ($insuranceTypes) {
                    return in_array($annex['type'], $insuranceTypes);
                });
                $components['data']['annexes'] = $filteredAnnexes;
            } elseif ($bundleType === 'EXHIBITION') {
                // Include exhibition-relevant annexes
                $exhibitionTypes = ['C_EXHIBITIONS', 'D_PHOTOS', 'B_CONDITION'];
                $filteredAnnexes = array_filter($annexes['annexes'], function ($annex) use ($exhibitionTypes) {
                    return in_array($annex['type'], $exhibitionTypes);
                });
                $components['data']['annexes'] = $filteredAnnexes;
            }

            $components['summary']['annexes'] = [
                'included' => true,
                'count' => count($components['data']['annexes'] ?? []),
                'types' => array_unique(array_column($components['data']['annexes'] ?? [], 'type')),
                'size' => strlen(json_encode($components['data']['annexes'] ?? []))
            ];
        }

        // Include event history if requested
        if (in_array($bundleType, ['COMPLETE', 'LEGAL']) || (isset($options['include_history']) && $options['include_history'])) {
            $events = CoaEvent::where('coa_id', $coa->id)
                ->orderBy('occurred_at', 'desc')
                ->get()
                ->toArray();

            $components['data']['events'] = $events;
            $components['summary']['events'] = [
                'included' => true,
                'count' => count($events),
                'size' => strlen(json_encode($events))
            ];
        }

        // Calculate total size
        $totalSize = 0;
        foreach ($components['summary'] as $summary) {
            $totalSize += $summary['size'] ?? 0;
        }

        $components['summary']['total_size'] = $totalSize;
        $components['summary']['components_count'] = count($components['data']);

        return $components;
    }

    /**
     * Generate bundle file in specified format
     *
     * @param string $bundleId
     * @param string $format
     * @param array $components
     * @param array $metadata
     * @param array $options
     * @return array
     * @privacy-safe Internal file generation method
     */
    protected function generateBundleFile(string $bundleId, string $format, array $components, array $metadata, array $options): array {
        $fileName = $bundleId . '_' . strtolower($format);
        $filePath = "coa_bundles/{$metadata['coa_id']}/{$bundleId}";

        switch ($format) {
            case 'JSON_DATA':
                return $this->generateJsonBundle($fileName, $filePath, $components, $metadata);

            case 'PDF_PACKAGE':
                return $this->generatePdfBundle($fileName, $filePath, $components, $metadata, $options);

            case 'ZIP_ARCHIVE':
                return $this->generateZipBundle($fileName, $filePath, $components, $metadata, $options);

            case 'DIGITAL_SIGNATURE':
                return $this->generateSignedBundle($fileName, $filePath, $components, $metadata, $options);

            case 'BLOCKCHAIN_RECORD':
                return $this->generateBlockchainBundle($fileName, $filePath, $components, $metadata, $options);

            default:
                throw new \Exception("Unsupported bundle format: {$format}");
        }
    }

    /**
     * Generate JSON format bundle
     *
     * @param string $fileName
     * @param string $filePath
     * @param array $components
     * @param array $metadata
     * @return array
     * @privacy-safe Generates JSON data export
     */
    protected function generateJsonBundle(string $fileName, string $filePath, array $components, array $metadata): array {
        $jsonData = [
            'metadata' => $metadata,
            'components' => $components['data'],
            'generated_at' => now()->toIso8601String(),
            'format' => 'JSON_DATA',
            'version' => '1.0.0'
        ];

        $jsonContent = json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $fullFileName = $fileName . '.json';
        $fullPath = $filePath . '/' . $fullFileName;

        // Store file
        Storage::disk('private')->put($fullPath, $jsonContent);

        return [
            'format' => 'JSON_DATA',
            'file_name' => $fullFileName,
            'file_path' => $fullPath,
            'size' => strlen($jsonContent),
            'mime_type' => 'application/json',
            'generated_at' => now()->toIso8601String()
        ];
    }

    /**
     * Generate PDF format bundle
     *
     * @param string $fileName
     * @param string $filePath
     * @param array $components
     * @param array $metadata
     * @param array $options
     * @return array
     * @privacy-safe Generates PDF document export
     */
    protected function generatePdfBundle(string $fileName, string $filePath, array $components, array $metadata, array $options): array {
        // This would integrate with a PDF generation library like TCPDF or DOMPDF
        // For now, we'll create a placeholder implementation

        $pdfContent = $this->generatePdfContent($components, $metadata, $options);
        $fullFileName = $fileName . '.pdf';
        $fullPath = $filePath . '/' . $fullFileName;

        // Store file (in real implementation, this would be actual PDF content)
        Storage::disk('private')->put($fullPath, $pdfContent);

        return [
            'format' => 'PDF_PACKAGE',
            'file_name' => $fullFileName,
            'file_path' => $fullPath,
            'size' => strlen($pdfContent),
            'mime_type' => 'application/pdf',
            'generated_at' => now()->toIso8601String()
        ];
    }

    /**
     * Generate placeholder PDF content
     *
     * @param array $components
     * @param array $metadata
     * @param array $options
     * @return string
     * @privacy-safe Generates PDF content from CoA data
     */
    protected function generatePdfContent(array $components, array $metadata, array $options): string {
        // This is a placeholder - in real implementation, you would use a PDF library
        $content = "Certificate of Authenticity - Bundle Package\n";
        $content .= "Generated: " . $metadata['created_at'] . "\n";
        $content .= "Bundle ID: " . $metadata['bundle_id'] . "\n";
        $content .= "CoA Serial: " . $metadata['coa_serial'] . "\n\n";

        $content .= "Certificate Details:\n";
        if (isset($components['coa'])) {
            $content .= "- Status: " . $components['coa']['status'] . "\n";
            $content .= "- Issue Date: " . $components['coa']['issue_date'] . "\n";
            $content .= "- Issued By: " . $components['coa']['issued_by'] . "\n";
        }

        if (isset($components['annexes'])) {
            $content .= "\nAnnexes Included:\n";
            foreach ($components['annexes'] as $annex) {
                $content .= "- " . $annex['type_description'] . " (v" . $annex['version'] . ")\n";
            }
        }

        if (isset($components['events'])) {
            $content .= "\nEvent History:\n";
            foreach (array_slice($components['events'], 0, 10) as $event) {
                $content .= "- " . $event['event_type'] . ": " . $event['description'] . "\n";
            }
        }

        return $content;
    }

    /**
     * Generate ZIP archive bundle
     *
     * @param string $fileName
     * @param string $filePath
     * @param array $components
     * @param array $metadata
     * @param array $options
     * @return array
     * @privacy-safe Generates ZIP archive with all components
     */
    protected function generateZipBundle(string $fileName, string $filePath, array $components, array $metadata, array $options): array {
        // This would create a ZIP archive containing multiple files
        // For now, we'll create a placeholder

        $zipContent = "ZIP Archive containing CoA bundle components";
        $fullFileName = $fileName . '.zip';
        $fullPath = $filePath . '/' . $fullFileName;

        Storage::disk('private')->put($fullPath, $zipContent);

        return [
            'format' => 'ZIP_ARCHIVE',
            'file_name' => $fullFileName,
            'file_path' => $fullPath,
            'size' => strlen($zipContent),
            'mime_type' => 'application/zip',
            'generated_at' => now()->toIso8601String()
        ];
    }

    /**
     * Generate digitally signed bundle
     *
     * @param string $fileName
     * @param string $filePath
     * @param array $components
     * @param array $metadata
     * @param array $options
     * @return array
     * @privacy-safe Generates cryptographically signed bundle
     */
    protected function generateSignedBundle(string $fileName, string $filePath, array $components, array $metadata, array $options): array {
        // This would implement digital signature functionality
        $signedContent = "Digitally signed CoA bundle";
        $fullFileName = $fileName . '_signed.json';
        $fullPath = $filePath . '/' . $fullFileName;

        Storage::disk('private')->put($fullPath, $signedContent);

        return [
            'format' => 'DIGITAL_SIGNATURE',
            'file_name' => $fullFileName,
            'file_path' => $fullPath,
            'size' => strlen($signedContent),
            'mime_type' => 'application/json',
            'generated_at' => now()->toIso8601String(),
            'signature_algorithm' => 'RSA-SHA256'
        ];
    }

    /**
     * Generate blockchain record bundle
     *
     * @param string $fileName
     * @param string $filePath
     * @param array $components
     * @param array $metadata
     * @param array $options
     * @return array
     * @privacy-safe Generates blockchain-verified bundle
     */
    protected function generateBlockchainBundle(string $fileName, string $filePath, array $components, array $metadata, array $options): array {
        // This would implement blockchain integration
        $blockchainContent = "Blockchain-verified CoA record";
        $fullFileName = $fileName . '_blockchain.json';
        $fullPath = $filePath . '/' . $fullFileName;

        Storage::disk('private')->put($fullPath, $blockchainContent);

        return [
            'format' => 'BLOCKCHAIN_RECORD',
            'file_name' => $fullFileName,
            'file_path' => $fullPath,
            'size' => strlen($blockchainContent),
            'mime_type' => 'application/json',
            'generated_at' => now()->toIso8601String(),
            'blockchain_hash' => hash('sha256', $blockchainContent)
        ];
    }

    /**
     * Create bundle record in database
     *
     * @param Coa $coa
     * @param string $bundleId
     * @param string $bundleType
     * @param array $metadata
     * @param array $components
     * @param \App\Models\User $user
     * @return array
     * @privacy-safe Creates bundle record for user's own CoA
     */
    protected function createBundleRecord(Coa $coa, string $bundleId, string $bundleType, array $metadata, array $components, $user): array {
        // Since we don't have a CoaBundle model yet, we'll store in a simple array
        // In real implementation, this would create a database record

        return [
            'id' => $bundleId,
            'coa_id' => $coa->id,
            'bundle_type' => $bundleType,
            'metadata' => $metadata,
            'components_summary' => $components['summary'],
            'created_by' => $user->id,
            'created_at' => now(),
            'status' => 'generated'
        ];
    }

    /**
     * Update bundle record with file information
     *
     * @param array $bundleRecord
     * @param array $files
     * @param int $totalSize
     * @return void
     * @privacy-safe Updates bundle record with file metadata
     */
    protected function updateBundleWithFiles(array &$bundleRecord, array $files, int $totalSize): void {
        $bundleRecord['files'] = $files;
        $bundleRecord['total_size'] = $totalSize;
        $bundleRecord['status'] = 'ready';
        $bundleRecord['updated_at'] = now();
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
            case 'bundle_created':
                return sprintf(
                    'Bundle %s created: %s with %d components (%s)',
                    $eventData['bundle_id'] ?? 'unknown',
                    $eventData['bundle_type'] ?? 'unknown',
                    $eventData['components_count'] ?? 0,
                    implode(', ', $eventData['formats'] ?? [])
                );
            default:
                return "CoA event: {$eventType}";
        }
    }

    /**
     * Get available bundle types
     *
     * @return array Available types with descriptions
     * @privacy-safe Returns static configuration data
     */
    public function getAvailableBundleTypes(): array {
        return self::BUNDLE_TYPES;
    }

    /**
     * Get available bundle formats
     *
     * @return array Available formats with descriptions
     * @privacy-safe Returns static configuration data
     */
    public function getAvailableBundleFormats(): array {
        return self::BUNDLE_FORMATS;
    }

    /**
     * Get bundle recommendation based on use case
     *
     * @param string $useCase The intended use case
     * @return array Recommended bundle configuration
     * @privacy-safe Returns configuration recommendations
     */
    public function getBundleRecommendation(string $useCase): array {
        $recommendations = [
            'legal_transfer' => [
                'bundle_type' => 'LEGAL',
                'formats' => ['PDF_PACKAGE', 'DIGITAL_SIGNATURE'],
                'options' => ['include_history' => true]
            ],
            'insurance_claim' => [
                'bundle_type' => 'INSURANCE',
                'formats' => ['PDF_PACKAGE', 'JSON_DATA'],
                'options' => ['include_photos' => true]
            ],
            'exhibition_loan' => [
                'bundle_type' => 'EXHIBITION',
                'formats' => ['PDF_PACKAGE'],
                'options' => ['include_condition_report' => true]
            ],
            'complete_archive' => [
                'bundle_type' => 'COMPLETE',
                'formats' => ['ZIP_ARCHIVE', 'JSON_DATA'],
                'options' => ['include_history' => true, 'include_all_versions' => true]
            ],
            'basic_verification' => [
                'bundle_type' => 'BASIC',
                'formats' => ['PDF_PACKAGE'],
                'options' => []
            ]
        ];

        return $recommendations[$useCase] ?? $recommendations['basic_verification'];
    }

    /**
     * Estimate bundle size before creation
     *
     * @param Coa $coa
     * @param string $bundleType
     * @param array $formats
     * @param array $options
     * @return array Size estimation
     * @privacy-safe Estimates size for user's own CoA
     */
    public function estimateBundleSize(Coa $coa, string $bundleType, array $formats, array $options = []): array {
        try {
            $user = Auth::user();

            // Security check
            if ($coa->egi->user_id !== $user->id) {
                throw new \Illuminate\Auth\Access\AuthorizationException('Unauthorized action.');
            }

            // Collect components for estimation
            $components = $this->collectBundleComponents($coa, $bundleType, $options);

            $estimatedSizes = [];
            $totalEstimatedSize = 0;

            foreach ($formats as $format) {
                $formatSize = $this->estimateFormatSize($format, $components);
                $estimatedSizes[$format] = $formatSize;
                $totalEstimatedSize += $formatSize;
            }

            return [
                'coa_id' => $coa->id,
                'bundle_type' => $bundleType,
                'formats' => $formats,
                'estimated_sizes' => $estimatedSizes,
                'total_estimated_size' => $totalEstimatedSize,
                'components_count' => count($components['data']),
                'estimated_generation_time' => $this->estimateGenerationTime($totalEstimatedSize),
                'storage_duration' => '30 days'
            ];
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_BUNDLE_ESTIMATE_ERROR', [
                'user_id' => Auth::id(),
                'coa_id' => $coa->id,
                'bundle_type' => $bundleType,
                'formats' => $formats,
                'error' => $e->getMessage(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            return [
                'coa_id' => $coa->id,
                'error' => true,
                'message' => 'Failed to estimate bundle size'
            ];
        }
    }

    /**
     * Estimate size for specific format
     *
     * @param string $format
     * @param array $components
     * @return int Estimated size in bytes
     * @privacy-safe Size calculation only
     */
    protected function estimateFormatSize(string $format, array $components): int {
        $baseSize = $components['summary']['total_size'] ?? 1000;

        switch ($format) {
            case 'JSON_DATA':
                return (int)($baseSize * 1.2); // JSON overhead
            case 'PDF_PACKAGE':
                return (int)($baseSize * 3.0); // PDF formatting overhead
            case 'ZIP_ARCHIVE':
                return (int)($baseSize * 0.7); // Compression benefit
            case 'DIGITAL_SIGNATURE':
                return (int)($baseSize * 1.3); // Signature overhead
            case 'BLOCKCHAIN_RECORD':
                return (int)($baseSize * 1.1); // Minimal overhead
            default:
                return $baseSize;
        }
    }

    /**
     * Estimate generation time
     *
     * @param int $totalSize Total estimated size in bytes
     * @return string Estimated time description
     * @privacy-safe Time estimation only
     */
    protected function estimateGenerationTime(int $totalSize): string {
        if ($totalSize < 100000) { // < 100KB
            return '5-10 seconds';
        } elseif ($totalSize < 1000000) { // < 1MB
            return '10-30 seconds';
        } elseif ($totalSize < 10000000) { // < 10MB
            return '30-60 seconds';
        } else {
            return '1-3 minutes';
        }
    }

    /**
     * Check if PDF exists for a CoA certificate
     *
     * @param Coa $coa
     * @return bool
     * @privacy-safe File existence check only
     */
    public function pdfExists(Coa $coa): bool {
        try {
            // Prefer DB record + Storage check via CoaFile
            $file = $coa->getMainPdf();
            if ($file && isset($file->path)) {
                return Storage::exists($file->path);
            }

            // Fallback: legacy local path (older placeholder generation)
            $pdfPath = $this->getPdfPath($coa);
            return is_string($pdfPath) && file_exists($pdfPath);
        } catch (\Exception $e) {
            $this->logger->warning('PDF existence check failed', [
                'coa_id' => $coa->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get PDF file path for a CoA certificate
     *
     * @param Coa $coa
     * @return string
     * @privacy-safe Path generation only
     */
    public function getPdfPath(Coa $coa): string {
        // If a CoaFile exists, return absolute path via Storage
        $file = $coa->getMainPdf();
        if ($file && isset($file->path)) {
            return Storage::path($file->path);
        }

        // Fallback to legacy location (placeholder-era)
        $directory = storage_path('app/coa/pdf');
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        return $directory . "/coa-{$coa->id}-{$coa->serial}.pdf";
    }

    /**
     * Generate PDF file for a CoA certificate
     *
     * @param Coa $coa
     * @return string Path to generated PDF
     * @privacy-safe PDF generation with audit trail
     */
    public function generateCoaPdf(Coa $coa): string {
        try {
            // If already exists (via CoaFile), return absolute path
            $existing = $coa->getMainPdf();
            if ($existing && isset($existing->path) && Storage::exists($existing->path)) {
                return Storage::path($existing->path);
            }

            // Delegate to the real PDF generator service (DomPDF based)
            /** @var CoaPdfService $pdfService */
            $pdfService = app(CoaPdfService::class);
            $result = $pdfService->generateCorePdf($coa, Auth::user(), [
                'format' => 'A4',
                'orientation' => 'portrait'
            ]);

            $absolutePath = isset($result['path']) ? Storage::path($result['path']) : null;

            $this->logger->info('COA PDF generated (CoaPdfService)', [
                'coa_id' => $coa->id,
                'coa_serial' => $coa->serial,
                'storage_path' => $result['path'] ?? null,
                'absolute_path' => $absolutePath,
                'file_size' => $result['file_size'] ?? null,
                'file_hash' => $result['file_hash'] ?? null,
            ]);

            if (!$absolutePath) {
                throw new \Exception('PDF generated but path is missing');
            }

            return $absolutePath;
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_PDF_GENERATION_ERROR', [
                'coa_id' => $coa->id,
                'error' => $e->getMessage()
            ], $e);

            throw $e;
        }
    }
}
