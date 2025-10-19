<?php

namespace App\Http\Controllers;

use App\Models\EgiReservationCertificate;
use App\Models\Reservation;
use App\Services\CertificateGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Ultra\ErrorManager\Facades\UltraError;
use Ultra\UltraLogManager\UltraLogManager;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @Oracode Controller: EgiReservationCertificateController
 * 🎯 Purpose: Handles certificate display, download, and verification
 * 🧱 Core Logic: Manages certificate lifecycle and verification
 * 🛡️ GDPR: Handles certificate data with privacy in mind
 *
 * @package App\Http\Controllers
 * @author Padmin D. Curtis (for Fabio Cherici)
 * @version 1.0.0
 * @date 2025-05-16
 * @seo-purpose Provides public certificate verification pages
 */
class EgiReservationCertificateController extends Controller {
    /**
     * @var UltraLogManager
     */
    protected UltraLogManager $logger;

    /**
     * @var CertificateGeneratorService
     */
    protected CertificateGeneratorService $certificateGenerator;

    /**
     * Constructor with dependency injection
     *
     * @param UltraLogManager $logger
     * @param CertificateGeneratorService $certificateGenerator
     */
    public function __construct(
        UltraLogManager $logger,
        CertificateGeneratorService $certificateGenerator
    ) {
        $this->logger = $logger;
        $this->certificateGenerator = $certificateGenerator;
    }

    /**
     * Show certificate details
     *
     * @param Request $request The HTTP request
     * @param string $uuid The certificate UUID
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     *
     * @seo-purpose Display certificate details with proper metadata
     * @schema-type Certificate
     */
    public function show(Request $request, string $uuid) {
        try {
            // Carica il certificato con EGI e le sue prenotazioni ordinate
            $certificate = EgiReservationCertificate::where('certificate_uuid', $uuid)
                ->with([
                    'egi' => function ($query) {
                        // Carica l'EGI con le prenotazioni ordinate usando la relazione modificata
                        $query->with('reservationCertificates');
                    },
                    'egi.collection',
                    'reservation',
                    'egiBlockchain' // Load blockchain data for MINT certificates
                ])
                ->firstOrFail();

            // Log access to certificate
            $this->logger->info('Certificate view accessed', [
                'certificate_uuid' => $uuid,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            // Check if this is a newly created certificate (coming from reservation flow)
            $showSuccess = $request->session()->has('success');

            return view('certificates.show', [
                'certificate' => $certificate,
                'showSuccess' => $showSuccess,
                'title' => __('certificate.page_title', ['uuid' => $certificate->certificate_uuid]),
                'metaDescription' => __('certificate.meta_description', [
                    'type' => ucfirst($certificate->reservation_type),
                    'title' => $certificate->egi->title ?? __('certificate.unknown_egi')
                ])
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to display certificate', [
                'certificate_uuid' => $uuid,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('home')->with('error', __('certificate.not_found'));
        }
    }

    /**
     * Download certificate PDF
     *
     * @param Request $request The HTTP request
     * @param string $uuid The certificate UUID
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
     */
    public function download(Request $request, string $uuid) {
        try {
            $certificate = EgiReservationCertificate::where('certificate_uuid', $uuid)->firstOrFail();

            // Check if PDF exists
            if (!$certificate->hasPdf()) {
                // Try to generate it if missing
                $result = $this->certificateGenerator->generatePdf($certificate);

                if (!$result || !$certificate->hasPdf()) {
                    throw new \Exception('Failed to generate PDF for certificate');
                }
            }

            // Log download
            $this->logger->info('Certificate PDF downloaded', [
                'certificate_uuid' => $uuid,
                'pdf_path' => $certificate->pdf_path,
                'disk' => config('filesystems.default'),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            // Generate safe filename
            $filename = 'Certificato_Blockchain_' . $certificate->certificate_uuid . '.pdf';

            // Return the file for INLINE viewing (no attachment header - browser will display)
            return Storage::response(
                $certificate->pdf_path,
                $filename,
                [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . $filename . '"',
                ]
            );
        } catch (\Exception $e) {
            $this->logger->error('Certificate PDF download failed', [
                'certificate_uuid' => $uuid,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('egi-certificates.show', $uuid)
                ->with('error', __('certificate.download_failed'));
        }
    }

    /**
     * Verify certificate authenticity
     *
     * @param Request $request The HTTP request
     * @param string $uuid The certificate UUID
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     *
     * @seo-purpose Public verification page for certificates
     * @schema-type VerificationService
     */
    public function verify(Request $request, string $uuid) {
        try {
            $certificate = EgiReservationCertificate::where('certificate_uuid', $uuid)
                ->with(['egi', 'egi.collection', 'reservation', 'egiBlockchain'])
                ->firstOrFail();

            // Generate verification data
            $verificationData = $certificate->generateVerificationData();

            // Verify signature
            $isValid = $certificate->verifySignature($verificationData);

            // Determine certificate type
            $isBlockchainCertificate = $certificate->certificate_type === 'mint';

            // Variables for reservation certificates
            $isHighestPriority = false;
            $isEgiAvailable = false;

            // Only check reservation-specific validations for reservation certificates
            if (!$isBlockchainCertificate) {
                $isHighestPriority = $certificate->is_current_highest && !$certificate->is_superseded;
                $isEgiAvailable = $certificate->egi && !$certificate->egi->mint;
            }

            // Log verification
            $this->logger->info('Certificate verification accessed', [
                'certificate_uuid' => $uuid,
                'certificate_type' => $certificate->certificate_type,
                'is_valid' => $isValid,
                'is_blockchain_cert' => $isBlockchainCertificate,
                'is_highest_priority' => $isHighestPriority,
                'is_egi_available' => $isEgiAvailable,
                'ip' => $request->ip()
            ]);

            return view('certificates.verify', [
                'certificate' => $certificate,
                'isValid' => $isValid,
                'isBlockchainCertificate' => $isBlockchainCertificate,
                'isHighestPriority' => $isHighestPriority,
                'isEgiAvailable' => $isEgiAvailable,
                'title' => __('certificate.verify_page_title', ['uuid' => $certificate->certificate_uuid]),
                'metaDescription' => __('certificate.verify_meta_description', [
                    'uuid' => $certificate->certificate_uuid
                ])
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Certificate verification failed', [
                'certificate_uuid' => $uuid,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('home')
                ->with('error', __('certificate.verification_failed'));
        }
    }

    /**
     * List certificates for an EGI
     *
     * @param Request $request The HTTP request
     * @param int $egiId The EGI ID
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function listByEgi(Request $request, int $egiId) {
        try {
            // Only show certificates for active reservations
            $certificates = EgiReservationCertificate::where('egi_id', $egiId)
                ->whereHas('reservation', function ($query) {
                    $query->where('status', 'active');
                })
                ->with(['reservation'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return view('certificates.list-by-egi', [
                'certificates' => $certificates,
                'egiId' => $egiId,
                'title' => __('certificate.list_by_egi_title', ['egi_id' => $egiId]),
                'metaDescription' => __('certificate.list_by_egi_meta_description', ['egi_id' => $egiId])
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to list certificates by EGI', [
                'egi_id' => $egiId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('home.collections.index')
                ->with('error', __('certificate.list_failed'));
        }
    }

    /**
     * List certificates for a user
     *
     * @param Request $request The HTTP request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     *
     * @privacy-safe Only shows user's own certificates
     */
    public function listByUser(Request $request) {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login')
                ->with('error', __('certificate.auth_required'));
        }

        try {
            // Get all certificates for user's reservations
            $certificates = EgiReservationCertificate::where('user_id', $user->id)
                ->with(['egi', 'egi.collection', 'reservation'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return view('certificates.list-by-user', [
                'certificates' => $certificates,
                'title' => __('certificate.user_certificates_title'),
                'metaDescription' => __('certificate.user_certificates_meta_description')
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to list user certificates', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('dashboard')
                ->with('error', __('certificate.list_failed'));
        }
    }

    /**
     * Generate post-mint blockchain certificate and payment breakdown
     *
     * @param Request $request The HTTP request
     * @param int $egiId The EGI ID that was just minted
     * @return \Illuminate\Http\JsonResponse
     *
     * @purpose Called after successful mint to generate certificate + payment breakdown
     * @returns JSON with certificate_url, payment_breakdown[], blockchain_data
     */
    public function generatePostMintCertificate(Request $request, int $egiId) {
        try {
            $user = $request->user();

            // Load EGI with blockchain record
            $egi = \App\Models\Egi::with('blockchain')->findOrFail($egiId);

            // Verify authorization - user must be the buyer
            if (!$egi->blockchain || $egi->blockchain->buyer_user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => __('certificate.unauthorized_access')
                ], 403);
            }

            // Verify EGI is minted
            if ($egi->blockchain->mint_status !== 'minted') {
                return response()->json([
                    'success' => false,
                    'message' => __('certificate.egi_not_minted')
                ], 400);
            }

            $this->logger->info('Generating post-mint certificate', [
                'egi_id' => $egiId,
                'user_id' => $user->id,
                'asa_id' => $egi->blockchain->asa_id
            ]);

            // ✅ Check if certificate already exists (prevent duplicate generation on page reload)
            $certificate = \App\Models\EgiReservationCertificate::where('egi_id', $egi->id)
                ->where('certificate_type', 'mint')
                ->where('egi_blockchain_id', $egi->blockchain->id)
                ->first();

            if (!$certificate) {
                // Certificate doesn't exist - generate new one
                $this->logger->info('No existing certificate found, generating new one', [
                    'egi_id' => $egiId
                ]);

                $certificate = $this->certificateGenerator->generateBlockchainCertificate(
                    $egi,
                    $egi->blockchain
                );
            } else {
                // Certificate already exists - use existing one
                $this->logger->info('Using existing certificate (prevented duplicate)', [
                    'egi_id' => $egiId,
                    'certificate_uuid' => $certificate->certificate_uuid
                ]);
            }

            // Query payment breakdown (amounts > 0 only)
            $distributions = \App\Models\PaymentDistribution::where('egi_id', $egi->id)
                ->where('source_type', 'mint')
                ->where('distribution_status', 'CONFIRMED')
                ->where('amount_eur', '>', 0)
                ->with('user:id,name,nick_name,last_name,wallet') // Load all fields needed for User->name accessor
                ->get();

            // Format payment breakdown for frontend
            $paymentBreakdown = $distributions->map(function ($dist) {
                // Get user name from relationship or metadata fallback
                $recipientName = $dist->user?->name ?? $dist->metadata['recipient_name'] ?? __('certificate.unknown_recipient');

                // 🎯 platform_role is a BRAND NAME (Frangette, Natan, EPP, Creator)
                // NOT translated - same in all languages (proper noun)
                $role = $dist->platform_role ?? ($dist->user_type?->value ?? 'Unknown');

                return [
                    'recipient' => $recipientName,
                    'role' => $role, // Raw value - no translation (Frangette stays Frangette worldwide)
                    'amount_eur' => number_format($dist->amount_eur, 2, ',', '.'),
                    'percentage' => $dist->percentage,
                ];
            })->toArray();

            // Blockchain data for display
            $blockchainData = [
                'asa_id' => $egi->blockchain->asa_id,
                'tx_id' => $egi->blockchain->blockchain_tx_id,
                'buyer_wallet' => $egi->blockchain->buyer_wallet,
                'minted_at' => $egi->blockchain->minted_at->format('d/m/Y H:i:s'),
                'pera_explorer_url' => 'https://explorer.perawallet.app/asset/' . $egi->blockchain->asa_id,
            ];

            $this->logger->info('Post-mint certificate generated successfully', [
                'egi_id' => $egiId,
                'certificate_uuid' => $certificate->certificate_uuid,
                'payment_distributions_count' => count($paymentBreakdown)
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'certificate_url' => $certificate->getPdfUrl(),
                    'certificate_uuid' => $certificate->certificate_uuid,
                    'public_url' => route('egi-certificates.show', $certificate->certificate_uuid),
                    'payment_breakdown' => $paymentBreakdown,
                    'blockchain_data' => $blockchainData,
                ]
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to generate post-mint certificate', [
                'egi_id' => $egiId,
                'user_id' => $request->user()?->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('certificate.generation_failed')
            ], 500);
        }
    }

    /**
     * Check if mint certificate PDF exists and return download URL
     * PUBLIC ENDPOINT - Certificates are blockchain-verified public documents
     *
     * @package App\Http\Controllers
     * @author Padmin D. Curtis (AI Partner OS3.0)
     * @version 2.0.0 (FlorenceEGI - PUBLIC Certificate Access)
     * @date 2025-10-18
     * @purpose Check mint certificate PDF availability for thumbnail rendering
     * @access PUBLIC - No authorization required (blockchain transparency principle)
     *
     * @param Request $request
     * @param int $egiId - EGI ID
     * @return JsonResponse
     */
    public function checkMintCertificatePdf(Request $request, int $egiId) {
        try {
            // Load EGI with blockchain (NO AUTH CHECK - certificates are PUBLIC)
            $egi = \App\Models\Egi::with('blockchain')->findOrFail($egiId);

            $this->logger->info('Certificate PDF check started', [
                'egi_id' => $egiId,
                'has_blockchain' => $egi->blockchain ? true : false,
                'blockchain_id' => $egi->blockchain?->id,
                'mint_status' => $egi->blockchain?->mint_status
            ]);

            // Verify EGI has been minted
            if (!$egi->blockchain) {
                $this->logger->info('EGI not minted yet for PDF check', [
                    'egi_id' => $egiId
                ]);

                return response()->json([
                    'success' => false,
                    'pdf_exists' => false,
                    'message' => __('certificate.egi_not_minted')
                ], 404);
            }

            // Find mint certificate
            $certificate = \App\Models\EgiReservationCertificate::where('egi_id', $egiId)
                ->where('certificate_type', 'mint')
                ->where('egi_blockchain_id', $egi->blockchain->id)
                ->first();

            $this->logger->info('Certificate search result', [
                'egi_id' => $egiId,
                'blockchain_id' => $egi->blockchain->id,
                'certificate_found' => $certificate ? true : false,
                'certificate_uuid' => $certificate?->certificate_uuid
            ]);

            if (!$certificate) {
                $this->logger->info('No mint certificate found for PDF check', [
                    'egi_id' => $egiId,
                    'blockchain_id' => $egi->blockchain->id
                ]);

                return response()->json([
                    'success' => false,
                    'pdf_exists' => false,
                    'message' => __('certificate.not_found')
                ], 404);
            }

            // Check if PDF file exists on disk (use default disk, not 'local')
            $pdfPath = $certificate->pdf_path;
            $pdfExists = $pdfPath && \Storage::exists($pdfPath); // Uses default disk (public)

            if (!$pdfExists) {
                $this->logger->warning('Mint certificate exists but PDF file missing', [
                    'egi_id' => $egiId,
                    'certificate_uuid' => $certificate->certificate_uuid,
                    'pdf_path' => $pdfPath,
                    'default_disk' => config('filesystems.default')
                ]);

                return response()->json([
                    'success' => false,
                    'pdf_exists' => false,
                    'message' => __('certificate.pdf_not_found')
                ], 404);
            }

            // PDF exists - return download URL
            $this->logger->info('Mint certificate PDF check successful', [
                'egi_id' => $egiId,
                'certificate_uuid' => $certificate->certificate_uuid,
                'pdf_exists' => true
            ]);

            return response()->json([
                'success' => true,
                'pdf_exists' => true,
                'download_url' => $certificate->getPdfUrl(),
                'certificate_uuid' => $certificate->certificate_uuid,
                'public_url' => route('egi-certificates.show', $certificate->certificate_uuid),
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to check mint certificate PDF', [
                'egi_id' => $egiId,
                'user_id' => $request->user()?->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('certificate.check_failed')
            ], 500);
        }
    }
}
