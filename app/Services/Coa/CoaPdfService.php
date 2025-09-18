<?php

namespace App\Services\Coa;

use App\Models\Coa;
use App\Models\CoaAnnex;
// use App\Models\CoaAddendum; // TODO: Create this model later
use App\Models\CoaFile;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Services\Gdpr\AuditLogService;
use App\Enums\Gdpr\GdprActivityCategory;
use Barryvdh\DomPDF\Facade\Pdf;
// use SimpleSoftwareIO\QrCode\Facades\QrCode; // TODO: Install when needed
use Carbon\Carbon;

/**
 * CoA PDF Generation Service
 *
 * 🎯 Purpose: Professional PDF generation for CoA certificates and Pro bundles
 * 🛡️ Security: UEM/ULM pattern compliance with GDPR audit trails
 * 📄 Features: Core PDF, Pro bundles with annexes, QR codes, professional layouts
 *
 * Pattern: FlorenceEGI Service Layer
 * UEM: COA_PDF_* error codes for comprehensive error handling
 * ULM: Detailed logging for all PDF generation operations
 * GDPR: Full audit trail compliance for certificate generation
 */
class CoaPdfService {
    /**
     * PDF Format configurations
     */
    const PDF_FORMATS = [
        'core' => [
            'template' => 'coa.pdf.simple', // Temporary simplified template
            'filename' => 'coa-core-{serial}-{timestamp}.pdf',
            'size' => 'A4',
            'orientation' => 'portrait'
        ],
        'bundle' => [
            'name' => 'Professional Bundle',
            'template' => 'coa.pdf.bundle',
            'filename' => 'COA-Bundle-{serial}.pdf',
            'size' => 'A4',
            'orientation' => 'portrait'
        ],
        'addendum' => [
            'name' => 'Policy Addendum',
            'template' => 'coa.pdf.addendum',
            'filename' => 'COA-Addendum-{serial}-v{version}.pdf',
            'size' => 'A4',
            'orientation' => 'portrait'
        ]
    ];

    /**
     * PDF Quality settings
     */
    const PDF_OPTIONS = [
        'compression' => true,
        'quality' => 95,
        'dpi' => 300,
        'font_size' => 10,
        'margin_top' => 15,
        'margin_bottom' => 15,
        'margin_left' => 15,
        'margin_right' => 15
    ];

    private ErrorManagerInterface $errorManager;
    private UltraLogManager $logger;
    private AuditLogService $auditService;
    private HashingService $hashingService;

    public function __construct(
        ErrorManagerInterface $errorManager,
        UltraLogManager $logger,
        AuditLogService $auditService,
        HashingService $hashingService
    ) {
        $this->errorManager = $errorManager;
        $this->logger = $logger;
        $this->auditService = $auditService;
        $this->hashingService = $hashingService;
    }

    /**
     * Generate Core CoA PDF certificate
     *
     * @param Coa $coa The certificate to generate PDF for
     * @param User|null $user The user requesting the PDF (null for system generation)
     * @param array $options Additional PDF options
     * @return array ['path' => string, 'filename' => string, 'size' => int, 'hash' => string]
     * @throws AuthorizationException
     */
    public function generateCorePdf(Coa $coa, ?User $user = null, array $options = []): array {
        try {
            $user = $user ?? Auth::user();

            // Authorization check
            if ($user && !$this->canGeneratePdf($coa, $user, 'core')) {
                throw new AuthorizationException('User is not authorized to generate core PDF for this CoA');
            }

            $this->logger->info('[CoA PDF] Generating core PDF', [
                'user_id' => $user?->id,
                'coa_id' => $coa->id,
                'serial' => $coa->serial,
                'format' => 'core',
                'options' => $options
            ]);

            // Validate CoA status
            if ($coa->status !== 'valid') {
                throw new \Exception('Cannot generate PDF for a CoA that is not in valid status');
            }

            // Prepare PDF data
            $pdfData = $this->prepareCorePdfData($coa, $options);

            // Generate QR code for verification
            $qrCode = $this->generateVerificationQr($coa);
            $pdfData['qr_code'] = $qrCode;

            // Generate PDF using template
            $pdf = $this->createPdfFromTemplate('core', $pdfData, $options);

            // Save PDF file
            $filename = $this->generateFilename('core', $coa);
            $path = $this->savePdfFile($pdf, $filename);

            // Calculate file hash
            $fileHash = $this->hashingService->hashFile($path);
            $fileSize = Storage::size($path);

            // Store PDF file record
            $coaFile = $this->storePdfFileRecord($coa, $path, $filename, $fileHash, $fileSize, 'pdf');

            $this->logger->info('[CoA PDF] Core PDF generated successfully', [
                'user_id' => $user?->id,
                'coa_id' => $coa->id,
                'file_id' => $coaFile->id,
                'filename' => $filename,
                'size' => $fileSize,
                'hash' => $fileHash
            ]);

            // Log audit trail
            if ($user) {
                $this->auditService->logUserAction($user, 'coa_pdf_generated', [
                    'coa_id' => $coa->id,
                    'file_id' => $coaFile->id,
                    'format' => 'core',
                    'filename' => $filename,
                    'size' => $fileSize
                ], GdprActivityCategory::GDPR_ACTIONS);
            }

            return [
                'path' => $path,
                'filename' => $filename,
                'size' => $fileSize,
                'hash' => $fileHash,
                'file_id' => $coaFile->id
            ];
        } catch (AuthorizationException $e) {
            throw $e; // Re-throw auth exceptions
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_PDF_CORE_GENERATION_ERROR', [
                'user_id' => $user?->id,
                'coa_id' => $coa->id,
                'error' => $e->getMessage(),
                'options' => $options
            ]);
            throw $e;
        }
    }

    /**
     * Generate Professional Bundle PDF with annexes
     *
     * @param Coa $coa The certificate to generate bundle for
     * @param User|null $user The user requesting the bundle
     * @param array $options Bundle options (include_annexes, include_addendums, etc.)
     * @return array ['path' => string, 'filename' => string, 'size' => int, 'hash' => string, 'annexes' => array]
     * @throws AuthorizationException
     */
    public function generateBundlePdf(Coa $coa, ?User $user = null, array $options = []): array {
        try {
            $user = $user ?? Auth::user();

            // Authorization check
            if ($user && !$this->canGeneratePdf($coa, $user, 'bundle')) {
                throw new AuthorizationException('User is not authorized to generate bundle PDF for this CoA');
            }

            $this->logger->info('[CoA PDF] Generating bundle PDF', [
                'user_id' => $user?->id,
                'coa_id' => $coa->id,
                'serial' => $coa->serial,
                'format' => 'bundle',
                'options' => $options
            ]);

            // Validate CoA status
            if ($coa->status !== 'valid') {
                throw new \Exception('Cannot generate bundle PDF for a CoA that is not in valid status');
            }

            // Load annexes if requested
            $annexes = [];
            if ($options['include_annexes'] ?? true) {
                $annexes = $this->loadCoaAnnexes($coa);
            }

            // Load addendums if requested
            $addendums = [];
            if ($options['include_addendums'] ?? true) {
                $addendums = $this->loadCoaAddendums($coa);
            }

            // Prepare comprehensive PDF data
            $pdfData = $this->prepareBundlePdfData($coa, $annexes, $addendums, $options);

            // Generate QR code for verification
            $qrCode = $this->generateVerificationQr($coa);
            $pdfData['qr_code'] = $qrCode;

            // Generate PDF using bundle template
            $pdf = $this->createPdfFromTemplate('bundle', $pdfData, $options);

            // Save PDF file
            $filename = $this->generateFilename('bundle', $coa);
            $path = $this->savePdfFile($pdf, $filename);

            // Calculate file hash
            $fileHash = $this->hashingService->hashFile($path);
            $fileSize = Storage::size($path);

            // Store PDF file record
            $coaFile = $this->storePdfFileRecord($coa, $path, $filename, $fileHash, $fileSize, 'pdf_bundle');

            $this->logger->info('[CoA PDF] Bundle PDF generated successfully', [
                'user_id' => $user?->id,
                'coa_id' => $coa->id,
                'file_id' => $coaFile->id,
                'filename' => $filename,
                'size' => $fileSize,
                'hash' => $fileHash,
                'annexes_count' => count($annexes),
                'addendums_count' => count($addendums)
            ]);

            // Log audit trail
            if ($user) {
                $this->auditService->logUserAction($user, 'coa_bundle_pdf_generated', [
                    'coa_id' => $coa->id,
                    'file_id' => $coaFile->id,
                    'format' => 'bundle',
                    'filename' => $filename,
                    'size' => $fileSize,
                    'annexes_count' => count($annexes),
                    'addendums_count' => count($addendums)
                ], GdprActivityCategory::GDPR_ACTIONS);
            }

            return [
                'path' => $path,
                'filename' => $filename,
                'size' => $fileSize,
                'hash' => $fileHash,
                'file_id' => $coaFile->id,
                'annexes' => $annexes,
                'addendums' => $addendums
            ];
        } catch (AuthorizationException $e) {
            throw $e; // Re-throw auth exceptions
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_PDF_BUNDLE_GENERATION_ERROR', [
                'user_id' => $user?->id,
                'coa_id' => $coa->id,
                'error' => $e->getMessage(),
                'options' => $options
            ]);
            throw $e;
        }
    }

    /**
     * Generate addendum PDF - TODO: Implement when CoaAddendum model is created
     *
     * @param mixed $addendum The addendum (placeholder)
     * @param User|null $user Requesting user
     * @param array $options PDF generation options
     * @return array Result with error for now
     */
    public function generateAddendumPdf($addendum, ?User $user = null, array $options = []): array {
        return [
            'success' => false,
            'error' => 'COA_PDF_ADDENDUM_NOT_IMPLEMENTED',
            'message' => 'Addendum PDF generation not yet implemented - CoaAddendum model needed'
        ];
    }

    /**
     * Get PDF download URL for a CoA file
     *
     * @param CoaFile $coaFile The CoA file record
     * @param bool $temporary Whether to generate a temporary signed URL
     * @param int $expirationMinutes Expiration time for temporary URLs (default: 60)
     * @return string The download URL
     */
    public function getPdfDownloadUrl(CoaFile $coaFile, bool $temporary = false, int $expirationMinutes = 60): string {
        try {
            if ($temporary) {
                // Generate temporary signed URL
                return Storage::temporaryUrl(
                    $coaFile->file_path,
                    Carbon::now()->addMinutes($expirationMinutes)
                );
            }

            // Return direct storage URL
            return Storage::url($coaFile->file_path);
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_PDF_URL_GENERATION_ERROR', [
                'file_id' => $coaFile->id,
                'temporary' => $temporary,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Validate PDF file integrity using stored hash
     *
     * @param CoaFile $coaFile The CoA file to validate
     * @return bool True if file integrity is valid
     */
    public function validatePdfIntegrity(CoaFile $coaFile): bool {
        try {
            if (!Storage::exists($coaFile->file_path)) {
                $this->logger->warning('[CoA PDF] File not found for integrity check', [
                    'file_id' => $coaFile->id,
                    'path' => $coaFile->file_path
                ]);
                return false;
            }

            $currentHash = $this->hashingService->hashFile($coaFile->file_path);
            $isValid = $currentHash === $coaFile->file_hash;

            $this->logger->info('[CoA PDF] File integrity check completed', [
                'file_id' => $coaFile->id,
                'is_valid' => $isValid,
                'stored_hash' => $coaFile->file_hash,
                'current_hash' => $currentHash
            ]);

            if (!$isValid) {
                $this->errorManager->handle('COA_PDF_INTEGRITY_FAILURE', [
                    'file_id' => $coaFile->id,
                    'stored_hash' => $coaFile->file_hash,
                    'current_hash' => $currentHash
                ]);
            }

            return $isValid;
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_PDF_INTEGRITY_CHECK_ERROR', [
                'file_id' => $coaFile->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Generate QR code for CoA verification
     *
     * @param Coa $coa The certificate to generate QR code for
     * @return string Base64 encoded QR code image
     */
    private function generateVerificationQr(Coa $coa): string {
        try {
            $verifyUrl = route('verify.certificate', ['serial' => $coa->serial]);

            // TODO: Install SimpleSoftwareIO/laravel-qrcode package
            // $qrCode = QrCode::format('svg')->size(200)->margin(0)->generate($verifyUrl);
            // return 'data:image/svg+xml;base64,' . base64_encode($qrCode);

            // Placeholder for now
            $placeholder = '<svg width="200" height="200" xmlns="http://www.w3.org/2000/svg"><rect width="200" height="200" fill="#f0f0f0" stroke="#ccc" stroke-width="2"/><text x="100" y="90" text-anchor="middle" font-size="14">QR Code</text><text x="100" y="110" text-anchor="middle" font-size="12">Package Needed</text><text x="100" y="130" text-anchor="middle" font-size="10">' . $coa->serial . '</text></svg>';

            return 'data:image/svg+xml;base64,' . base64_encode($placeholder);
        } catch (\Exception $e) {
            $this->logger->error('[CoA PDF] QR code generation failed', [
                'coa_id' => $coa->id,
                'serial' => $coa->serial,
                'error' => $e->getMessage()
            ]);

            // Return placeholder QR code
            return 'data:image/svg+xml;base64,' . base64_encode('<svg></svg>');
        }
    }

    /**
     * Check if user can generate PDF for CoA
     *
     * @param Coa $coa The certificate
     * @param User $user The user
     * @param string $format The PDF format ('core', 'bundle', 'addendum')
     * @return bool
     */
    private function canGeneratePdf(Coa $coa, User $user, string $format): bool {
        // System/admin users can generate any PDF
        if ($user->hasRole(['admin', 'system'])) {
            return true;
        }

        // Owner or creator can generate core PDFs
        if ($format === 'core') {
            return $coa->egi->user_id === $user->id || $coa->egi->creator_id === $user->id;
        }

        // Only owner/creator can generate Pro features (bundle, addendum)
        if ($format === 'bundle' || $format === 'addendum') {
            return $coa->egi->user_id === $user->id || $coa->egi->creator_id === $user->id;
        }

        return false;
    }

    /**
     * Continue with helper methods...
     */

    /**
     * Prepare PDF data for core certificate
     *
     * @param Coa $coa The certificate
     * @param array $options PDF options
     * @return array Prepared data for PDF template
     */
    private function prepareCorePdfData(Coa $coa, array $options = []): array {
        $egi = $coa->egi()->with(['traits', 'user'])->first();

        return [
            'coa' => $coa,
            'egi' => $egi,
            'serial' => $coa->serial,
            'issued_at' => $coa->issued_at,
            'verification_hash' => $coa->verification_hash ?? hash('sha256', $coa->serial),
            'traits_snapshot' => $egi->traits->toArray(), // Convert traits relation to array
            'creator' => $egi->user, // The user who created the EGI
            'owner' => $egi->user,   // Current owner (same as creator for now)
            'title' => $egi->title,
            'description' => $egi->description,
            'creation_date' => $egi->creation_date,
            'image_url' => $egi->getFirstMediaUrl('images'),
            'platform_info' => [
                'name' => 'FlorenceEGI',
                'url' => config('app.url'),
                'issued_at' => Carbon::now()
            ],
            'options' => $options
        ];
    }

    /**
     * Prepare PDF data for professional bundle
     *
     * @param Coa $coa The certificate
     * @param array $annexes CoA annexes
     * @param array $addendums CoA addendums
     * @param array $options PDF options
     * @return array Prepared data for PDF template
     */
    private function prepareBundlePdfData(Coa $coa, array $annexes = [], array $addendums = [], array $options = []): array {
        $coreData = $this->prepareCorePdfData($coa, $options);

        return array_merge($coreData, [
            'annexes' => $this->formatAnnexesForPdf($annexes),
            'addendums' => $this->formatAddendumsForPdf($addendums),
            'bundle_info' => [
                'generated_at' => Carbon::now(),
                'includes_annexes' => !empty($annexes),
                'includes_addendums' => !empty($addendums),
                'annexes_count' => count($annexes),
                'addendums_count' => count($addendums)
            ]
        ]);
    }

    /**
     * Prepare PDF data for addendum - TODO: Implement when CoaAddendum exists
     *
     * @param mixed $addendum The addendum (placeholder)
     * @param array $options PDF options
     * @return array Empty array for now
     */
    private function prepareAddendumPdfData($addendum, array $options = []): array {
        // TODO: Implement when CoaAddendum model is created
        return [];
    }

    /**
     * Load CoA annexes for bundle
     *
     * @param Coa $coa The certificate
     * @return array Formatted annexes data
     */
    private function loadCoaAnnexes(Coa $coa): array {
        return CoaAnnex::where('coa_id', $coa->id)
            ->orderBy('type')
            ->orderBy('created_at')
            ->get()
            ->toArray();
    }

    /**
     * Load CoA addendums for bundle - TODO: Implement when CoaAddendum exists
     *
     * @param Coa $coa The certificate
     * @return array Empty array for now
     */
    private function loadCoaAddendums(Coa $coa): array {
        // TODO: Implement when CoaAddendum model is created
        return [];
    }

    /**
     * Format annexes for PDF display
     *
     * @param array $annexes Raw annexes data
     * @return array Formatted annexes
     */
    private function formatAnnexesForPdf(array $annexes): array {
        $formatted = [];

        foreach ($annexes as $annex) {
            $formatted[] = [
                'type' => $annex['type'],
                'type_label' => $this->getAnnexTypeLabel($annex['type']),
                'data' => $annex['data'],
                'created_at' => Carbon::parse($annex['created_at']),
                'updated_at' => Carbon::parse($annex['updated_at'])
            ];
        }

        return $formatted;
    }

    /**
     * Format addendums for PDF display
     *
     * @param array $addendums Raw addendums data
     * @return array Formatted addendums
     */
    private function formatAddendumsForPdf(array $addendums): array {
        $formatted = [];

        foreach ($addendums as $addendum) {
            $formatted[] = [
                'version' => $addendum['version'],
                'title' => $addendum['title'],
                'policy_type' => $addendum['policy_type'],
                'published_at' => Carbon::parse($addendum['published_at']),
                'content' => $addendum['content']
            ];
        }

        return $formatted;
    }

    /**
     * Get human-readable label for annex type
     *
     * @param string $type Annex type
     * @return string Human-readable label
     */
    private function getAnnexTypeLabel(string $type): string {
        $labels = [
            'A_PROVENANCE' => __('egi.coa.provenance_title'),
            'B_CONDITION' => __('egi.coa.condition_title'),
            'C_EXHIBITIONS' => __('egi.coa.exhibitions_title'),
            'D_PHOTOS' => __('egi.coa.photos_title')
        ];

        return $labels[$type] ?? $type;
    }

    /**
     * Create PDF from template
     *
     * @param string $format PDF format ('core', 'bundle', 'addendum')
     * @param array $data Template data
     * @param array $options PDF options
     * @return \Barryvdh\DomPDF\PDF PDF instance
     */
    private function createPdfFromTemplate(string $format, array $data, array $options = []): \Barryvdh\DomPDF\PDF {
        $formatConfig = self::PDF_FORMATS[$format];
        $pdfOptions = array_merge(self::PDF_OPTIONS, $options);

        // Render Blade template
        $html = View::make($formatConfig['template'], $data)->render();

        // Create PDF
        $pdf = Pdf::loadHTML($html);

        // Configure PDF options
        $pdf->setPaper($formatConfig['size'], $formatConfig['orientation']);

        // Set DomPDF options
        $pdf->getDomPDF()->getOptions()->set([
            'isRemoteEnabled' => true,
            'isPhpEnabled' => false,
            'isJavascriptEnabled' => false,
            'isHtml5ParserEnabled' => true,
            'isFontSubsettingEnabled' => true,
            'defaultFont' => 'DejaVu Sans'
        ]);

        return $pdf;
    }

    /**
     * Generate filename for PDF
     *
     * @param string $format PDF format
     * @param Coa $coa The certificate
     * @param array $params Additional parameters (version, etc.)
     * @return string Generated filename
     */
    private function generateFilename(string $format, Coa $coa, array $params = []): string {
        $template = self::PDF_FORMATS[$format]['filename'];

        $replacements = [
            '{serial}' => $coa->serial,
            '{timestamp}' => Carbon::now()->format('Y-m-d-H-i-s'),
            '{version}' => $params['version'] ?? '1'
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }

    /**
     * Save PDF file to storage
     *
     * @param \Barryvdh\DomPDF\PDF $pdf PDF instance
     * @param string $filename Filename to save as
     * @return string Storage path
     */
    private function savePdfFile(\Barryvdh\DomPDF\PDF $pdf, string $filename): string {
        $path = 'coa/pdf/' . date('Y/m') . '/' . $filename;

        // Ensure directory exists
        $directory = dirname($path);
        if (!Storage::exists($directory)) {
            Storage::makeDirectory($directory);
        }

        // Save PDF file
        Storage::put($path, $pdf->output());

        return $path;
    }

    /**
     * Store PDF file record in database
     *
     * @param Coa $coa The certificate
     * @param string $path Storage path
     * @param string $filename Original filename
     * @param string $hash File hash
     * @param int $size File size in bytes
     * @param string $kind File kind ('pdf', 'pdf_bundle', 'pdf_addendum')
     * @param int|null $annexId Related annex ID (for addendum files)
     * @return CoaFile Created file record
     */
    private function storePdfFileRecord(
        Coa $coa,
        string $path,
        string $filename,
        string $hash,
        int $size,
        string $kind,
        ?int $annexId = null
    ): CoaFile {
        return CoaFile::create([
            'coa_id' => $coa->id,
            'file_path' => $path,
            'filename' => $filename,
            'file_hash' => $hash,
            'file_size' => $size,
            'kind' => $kind,
            'annex_id' => $annexId,
            'mime_type' => 'application/pdf',
            'metadata' => [
                'generated_at' => Carbon::now()->toISOString(),
                'generator' => 'CoaPdfService',
                'version' => '1.0'
            ]
        ]);
    }
}
