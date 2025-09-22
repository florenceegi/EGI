<?php

namespace App\Http\Controllers;

use App\Models\Coa;
use App\Services\Coa\VerifyPageService;
use App\Services\Coa\AnnexService;
use App\Services\Coa\CoaPdfService;
use App\Traits\EgiTraitsExtraction;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * @Oracode Controller: CoA Public Verification
 * 🎯 Purpose: Handle public certificate verification and validation
 * 🛡️ Privacy: Public verification with rate limiting and security measures
 * 🧱 Core Logic: Coordinates public certificate verification without authentication
 *
 * @package App\Http\Controllers
 * @author AI Assistant following FlorenceEGI patterns
 * @version 1.0.0 (CoA Pro System)
 * @date 2025-09-18
 * @purpose Public certificate verification following FlorenceEGI architecture
 */
class VerifyController extends Controller {
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
     * Verification service
     * @var VerifyPageService
     */
    protected VerifyPageService $verifyService;

    /**
     * Annex service for public annex data
     * @var AnnexService
     */
    protected AnnexService $annexService;

    /**
     * CoA PDF service for certificate PDF generation
     * @var CoaPdfService
     */
    protected CoaPdfService $coaPdfService;

    /**
     * Constructor with dependency injection
     *
     * @param UltraLogManager $logger
     * @param ErrorManagerInterface $errorManager
     * @param VerifyPageService $verifyService
     * @param AnnexService $annexService
     * @param CoaPdfService $coaPdfService
     * @privacy-safe All injected services handle public verification safely
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        VerifyPageService $verifyService,
        AnnexService $annexService,
        CoaPdfService $coaPdfService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->verifyService = $verifyService;
        $this->annexService = $annexService;
        $this->coaPdfService = $coaPdfService;

        // Apply rate limiting to all methods
        $this->middleware('throttle:60,1'); // 60 requests per minute
    }

    /**
     * Verify certificate by serial number
     *
     * @param Request $request
     * @param string $serial
     * @return JsonResponse
     * @privacy-safe Public verification with limited data exposure
     *
     * @oracode-dimension governance
     * @value-flow Enables public verification of certificate authenticity
     * @community-impact Builds trust through transparent verification
     * @transparency-level High - public verification process
     * @narrative-coherence Links certificates to public verification system
     */
    public function verify(Request $request, string $serial): JsonResponse {
        try {
            // Additional rate limiting for verification
            $key = 'verify_' . $request->ip();
            if (RateLimiter::tooManyAttempts($key, 10)) {
                $this->logger->warning('[Verify Controller] Rate limit exceeded for verification', [
                    'ip_address' => $request->ip(),
                    'serial' => $serial,
                    'user_agent' => $request->userAgent()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Too many verification attempts. Please try again later.',
                    'retry_after' => RateLimiter::availableIn($key)
                ], 429);
            }

            RateLimiter::hit($key, 300); // 5 minute decay

            $this->logger->info('[Verify Controller] Certificate verification requested', [
                'serial' => $serial,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'referrer' => $request->header('referer')
            ]);

            // Find certificate
            $coa = Coa::where('serial', $serial)->first();

            if (!$coa) {
                $this->logger->info('[Verify Controller] Certificate not found', [
                    'serial' => $serial,
                    'ip_address' => $request->ip()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Certificate not found',
                    'serial' => $serial,
                    'verified' => false,
                    'status' => 'not_found'
                ], 404);
            }

            // Generate verification data directly
            $verificationData = [
                'certificate' => [
                    'serial' => $coa->serial,
                    'status' => $coa->status,
                    'issued_at' => $coa->issued_at,
                    'issuer_name' => $coa->issuer_name,
                    'verification_hash' => $coa->verification_hash
                ],
                'artwork' => [
                    'title' => $coa->egi->title ?? $coa->egi->name,
                    'creator' => $coa->egi->user->name
                ],
                'verification' => [
                    'verified_at' => now(),
                    'is_valid' => $coa->status === 'valid'
                ]
            ];

            $response = [
                'success' => true,
                'message' => 'Certificate verification completed',
                'verified' => $coa->status === 'valid',
                'data' => $verificationData
            ];

            $this->logger->info('[Verify Controller] Certificate verification completed', [
                'serial' => $serial,
                'coa_id' => $coa->id,
                'status' => $coa->status,
                'verified' => $coa->status === 'valid',
                'ip_address' => $request->ip()
            ]);

            return response()->json($response, 200);
        } catch (\Exception $e) {
            $this->errorManager->handle('VERIFY_CERTIFICATE_ERROR', [
                'serial' => $serial,
                'error' => $e->getMessage(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            return response()->json([
                'success' => false,
                'message' => 'Verification service temporarily unavailable',
                'error' => 'An error occurred during verification'
            ], 500);
        }
    }

    /**
     * Get verification page data
     *
     * @param Request $request
     * @param string|null $serial
     * @return JsonResponse
     * @privacy-safe Returns public verification page data
     */
    public function verificationPage(Request $request, string $serial = null): JsonResponse {
        try {
            // If no serial provided, return general verification page
            if (!$serial) {
                $this->logger->info('[Verify Controller] Serving general verification page', [
                    'ip_address' => $request->ip()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Certificate verification page',
                    'data' => [
                        'page_type' => 'verification_form',
                        'title' => __('egi.coa.public_verification'),
                        'description' => __('egi.coa.verification_description'),
                        'instructions' => __('egi.coa.verification_instructions'),
                        'form_fields' => [
                            'serial' => [
                                'label' => __('egi.coa.serial_number'),
                                'placeholder' => __('egi.coa.enter_serial'),
                                'required' => true,
                                'pattern' => '[A-Z0-9\-]+',
                                'help' => __('egi.coa.serial_help')
                            ]
                        ]
                    ]
                ], 200);
            }

            // Use cached data if available
            $cacheKey = "verify_page_{$serial}";
            $cachedData = Cache::get($cacheKey);

            if ($cachedData) {
                $this->logger->info('[Verify Controller] Serving cached verification page', [
                    'serial' => $serial,
                    'ip_address' => $request->ip()
                ]);

                return response()->json($cachedData, 200);
            }

            $this->logger->info('[Verify Controller] Generating verification page', [
                'serial' => $serial,
                'ip_address' => $request->ip()
            ]);

            // Find certificate
            $coa = Coa::where('serial', $serial)->with(['egi'])->first();

            if (!$coa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Certificate not found',
                    'serial' => $serial
                ], 404);
            }

            // Generate verification page using service
            $pageData = $this->verifyService->generateVerificationPage($coa);

            // Cache for 1 hour
            Cache::put($cacheKey, $pageData, 3600);

            $this->logger->info('[Verify Controller] Verification page generated', [
                'serial' => $serial,
                'coa_id' => $coa->id,
                'cached' => true
            ]);

            return response()->json($pageData, 200);
        } catch (\Exception $e) {
            $this->errorManager->handle('VERIFY_PAGE_ERROR', [
                'serial' => $serial,
                'error' => $e->getMessage(),
                'ip_address' => $request->ip(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            return response()->json([
                'success' => false,
                'message' => 'Unable to generate verification page',
                'error' => 'An error occurred while generating the page'
            ], 500);
        }
    }

    /**
     * Verify certificate hash integrity
     *
     * @param Request $request
     * @param string $serial
     * @return JsonResponse
     * @privacy-safe Public hash verification for integrity checking
     */
    public function verifyHash(Request $request, string $serial): JsonResponse {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'hash' => 'required|string|size:64', // SHA-256 hash
                'component' => 'nullable|string|in:certificate,traits,snapshot'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid hash format',
                    'errors' => $validator->errors()
                ], 422);
            }

            $this->logger->info('[Verify Controller] Hash verification requested', [
                'serial' => $serial,
                'hash' => $request->hash,
                'component' => $request->component,
                'ip_address' => $request->ip()
            ]);

            // Find certificate
            $coa = Coa::where('serial', $serial)->first();

            if (!$coa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Certificate not found',
                    'serial' => $serial
                ], 404);
            }

            // Verify hash using service
            $hashVerification = $this->verifyService->verifyHash($coa, $request->hash, $request->component);

            $response = [
                'success' => true,
                'message' => 'Hash verification completed',
                'data' => $hashVerification
            ];

            $this->logger->info('[Verify Controller] Hash verification completed', [
                'serial' => $serial,
                'hash_valid' => $hashVerification['is_valid'],
                'component' => $request->component
            ]);

            return response()->json($response, 200);
        } catch (\Exception $e) {
            $this->errorManager->handle('VERIFY_HASH_ERROR', [
                'serial' => $serial,
                'hash' => $request->hash ?? 'missing',
                'error' => $e->getMessage(),
                'ip_address' => $request->ip(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            return response()->json([
                'success' => false,
                'message' => 'Hash verification failed',
                'error' => 'An error occurred during hash verification'
            ], 500);
        }
    }

    /**
     * Get public annexes for verification
     *
     * @param Request $request
     * @param string $serial
     * @return JsonResponse
     * @privacy-safe Returns public portions of annexes for verification
     */
    public function annexes(Request $request, string $serial): JsonResponse {
        try {
            // Rate limiting for annex requests
            $key = 'annexes_' . $request->ip();
            if (RateLimiter::tooManyAttempts($key, 20)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Too many annex requests. Please try again later.',
                    'retry_after' => RateLimiter::availableIn($key)
                ], 429);
            }

            RateLimiter::hit($key, 300);

            $this->logger->info('[Verify Controller] Public annexes requested', [
                'serial' => $serial,
                'ip_address' => $request->ip()
            ]);

            // Find certificate
            $coa = Coa::where('serial', $serial)->first();

            if (!$coa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Certificate not found',
                    'serial' => $serial
                ], 404);
            }

            // Get public annex data using service
            $publicAnnexes = $this->verifyService->getPublicAnnexes($coa);

            $response = [
                'success' => true,
                'message' => 'Public annexes retrieved successfully',
                'data' => $publicAnnexes
            ];

            $this->logger->info('[Verify Controller] Public annexes provided', [
                'serial' => $serial,
                'coa_id' => $coa->id,
                'annexes_count' => count($publicAnnexes['annexes'] ?? [])
            ]);

            return response()->json($response, 200);
        } catch (\Exception $e) {
            $this->errorManager->handle('VERIFY_ANNEXES_ERROR', [
                'serial' => $serial,
                'error' => $e->getMessage(),
                'ip_address' => $request->ip(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            return response()->json([
                'success' => false,
                'message' => 'Unable to retrieve annexes',
                'error' => 'An error occurred while fetching annexes'
            ], 500);
        }
    }

    /**
     * Generate QR code for certificate verification
     *
     * @param Request $request
     * @param string $serial
     * @return JsonResponse
     * @privacy-safe Generates public QR code for verification
     */
    public function qrCode(Request $request, string $serial): JsonResponse {
        try {
            $this->logger->info('[Verify Controller] QR code requested', [
                'serial' => $serial,
                'ip_address' => $request->ip()
            ]);

            // Find certificate
            $coa = Coa::where('serial', $serial)->first();

            if (!$coa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Certificate not found',
                    'serial' => $serial
                ], 404);
            }

            // Generate QR code using service
            $qrData = $this->verifyService->generateQrCode($coa);

            $response = [
                'success' => true,
                'message' => 'QR code generated successfully',
                'data' => $qrData
            ];

            return response()->json($response, 200);
        } catch (\Exception $e) {
            $this->errorManager->handle('VERIFY_QR_ERROR', [
                'serial' => $serial,
                'error' => $e->getMessage(),
                'ip_address' => $request->ip(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            return response()->json([
                'success' => false,
                'message' => 'Unable to generate QR code',
                'error' => 'An error occurred while generating QR code'
            ], 500);
        }
    }

    /**
     * Batch verify multiple certificates
     *
     * @param Request $request
     * @return JsonResponse
     * @privacy-safe Batch verification with enhanced rate limiting
     */
    public function batchVerify(Request $request): JsonResponse {
        try {
            // Enhanced rate limiting for batch operations
            $key = 'batch_verify_' . $request->ip();
            if (RateLimiter::tooManyAttempts($key, 5)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Too many batch verification attempts. Please try again later.',
                    'retry_after' => RateLimiter::availableIn($key)
                ], 429);
            }

            RateLimiter::hit($key, 600); // 10 minute decay

            // Validate request
            $validator = Validator::make($request->all(), [
                'serials' => 'required|array|min:1|max:10', // Max 10 certificates at once
                'serials.*' => 'required|string|max:50'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid request format',
                    'errors' => $validator->errors()
                ], 422);
            }

            $serials = $request->serials;

            $this->logger->info('[Verify Controller] Batch verification requested', [
                'serials_count' => count($serials),
                'serials' => $serials,
                'ip_address' => $request->ip()
            ]);

            // Batch verify using service
            $batchResults = $this->verifyService->batchVerify($serials);

            $response = [
                'success' => true,
                'message' => 'Batch verification completed',
                'data' => $batchResults
            ];

            $this->logger->info('[Verify Controller] Batch verification completed', [
                'serials_count' => count($serials),
                'verified_count' => count(array_filter($batchResults, function ($result) {
                    return $result['verified'] ?? false;
                })),
                'ip_address' => $request->ip()
            ]);

            return response()->json($response, 200);
        } catch (\Exception $e) {
            $this->errorManager->handle('VERIFY_BATCH_ERROR', [
                'serials' => $request->serials ?? [],
                'error' => $e->getMessage(),
                'ip_address' => $request->ip(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            return response()->json([
                'success' => false,
                'message' => 'Batch verification failed',
                'error' => 'An error occurred during batch verification'
            ], 500);
        }
    }

    /**
     * Get verification statistics (public)
     *
     * @param Request $request
     * @return JsonResponse
     * @privacy-safe Returns public verification statistics
     */
    public function statistics(Request $request): JsonResponse {
        try {
            // Cache statistics for 1 hour
            $cacheKey = 'verify_stats_public';
            $stats = Cache::remember($cacheKey, 3600, function () {
                return [
                    'total_certificates' => Coa::count(),
                    'valid_certificates' => Coa::where('status', 'valid')->count(),
                    'total_verifications_today' => Cache::get('daily_verifications_' . now()->format('Y-m-d'), 0),
                    'system_status' => 'operational',
                    'last_updated' => now()->toIso8601String()
                ];
            });

            $response = [
                'success' => true,
                'message' => 'Verification statistics retrieved successfully',
                'data' => $stats
            ];

            return response()->json($response, 200);
        } catch (\Exception $e) {
            $this->errorManager->handle('VERIFY_STATS_ERROR', [
                'error' => $e->getMessage(),
                'ip_address' => $request->ip(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            return response()->json([
                'success' => false,
                'message' => 'Unable to retrieve statistics',
                'error' => 'An error occurred while fetching statistics'
            ], 500);
        }
    }

    /**
     * View specific certificate by verification hash
     *
     * @param Request $request
     * @param string $hash The verification hash
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     * @privacy-safe Returns public certificate view
     *
     * @oracode-dimension governance
     * @value-flow Provides public certificate display
     * @community-impact Builds trust through transparent certificate viewing
     * @transparency-level High - public certificate information
     * @narrative-coherence Links verification hash to certificate display
     */
    public function viewCertificate(Request $request, string $hash) {
        try {
            // Rate limiting for certificate viewing
            $key = 'view_cert_' . $request->ip();
            if (RateLimiter::tooManyAttempts($key, 30)) {
                $this->logger->warning('[Verify Controller] Rate limit exceeded for certificate viewing', [
                    'ip_address' => $request->ip(),
                    'hash' => substr($hash, 0, 8) . '...',
                    'user_agent' => $request->userAgent()
                ]);

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Too many requests. Please try again later.'
                    ], 429);
                }

                return back()->withErrors(['message' => 'Too many requests. Please try again later.']);
            }

            RateLimiter::hit($key, 300); // 5 minutes

            $this->logger->info('[Verify Controller] Viewing certificate by hash', [
                'hash' => substr($hash, 0, 8) . '...',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            // Find certificate by verification hash
            $coa = Coa::where('verification_hash', $hash)
                ->with(['egi', 'egi.user'])
                ->first();

            if (!$coa) {
                $this->logger->warning('[Verify Controller] Certificate not found', [
                    'hash' => substr($hash, 0, 8) . '...',
                    'ip_address' => $request->ip()
                ]);

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Certificate not found'
                    ], 404);
                }

                return view('coa.public.not-found', [
                    'hash' => substr($hash, 0, 8) . '...'
                ]);
            }

            // Check if has CoA specific traits
            $hasValidCoaTraits = false;
            $coaTraits = $coa->egi->coaTraits;
            if ($coaTraits) {
                $hasValidCoaTraits = !empty($coaTraits->technique_slugs) ||
                    !empty($coaTraits->materials_slugs) ||
                    !empty($coaTraits->support_slugs) ||
                    !empty($coaTraits->technique_free_text) ||
                    !empty($coaTraits->materials_free_text) ||
                    !empty($coaTraits->support_free_text);
            }

            // Generate certificate view data
            $certificateData = [
                'certificate' => [
                    'serial' => $coa->serial,
                    'status' => $coa->status,
                    'issued_at' => $coa->issued_at,
                    'issuer_name' => $coa->issuer_name,
                    'notes' => $coa->notes,
                    'verification_hash' => $coa->verification_hash ?? hash('sha256', $coa->serial . $coa->issued_at),
                    'has_coa_traits' => $hasValidCoaTraits,
                    'effective_status' => ($coa->status === 'valid' && $hasValidCoaTraits) ? 'valid' : 'incomplete'
                ],
                'artwork' => [
                    'name' => $coa->egi->title ?? $coa->egi->name ?? 'Unknown Artwork',
                    'description' => $coa->egi->description ?? '',
                    'creator' => $coa->egi->user->name ?? 'Unknown Creator',
                    'author' => $this->extractAuthorFromTraits($coa->egi),
                    'year' => $this->extractYearFromTraits($coa->egi),
                    'technique' => $this->extractTechniqueFromTraits($coa->egi),
                    'materials' => $this->extractMaterialsFromTraits($coa->egi), // AGGIUNTO: estrazione materiali
                    'support' => $this->extractSupportFromTraits($coa->egi),
                    'dimensions' => $this->extractDimensionsFromTraits($coa->egi),
                    'edition' => $this->extractEditionFromTraits($coa->egi),
                    'traits' => $this->extractAllArtworkMetadata($coa->egi),
                    'internal_id' => $coa->egi->id,
                    'image_url' => $coa->egi->main_image_url, // AGGIUNTO: URL immagine principale
                    'thumbnail_url' => $coa->egi->thumbnail_image_url // AGGIUNTO: URL thumbnail
                ],
                'verification' => [
                    'is_valid' => $coa->status === 'valid',
                    'verified_at' => now(),
                    'verification_url' => route('coa.verify.certificate', $coa->serial)
                ]
            ];

            $this->logger->info('[Verify Controller] Certificate view generated', [
                'coa_id' => $coa->id,
                'serial' => $coa->serial,
                'status' => $coa->status,
                'ip_address' => $request->ip()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $certificateData
                ]);
            }

            // Return Blade view for web browsers
            return view('coa.public.certificate', [
                'certificate' => $certificateData['certificate'],
                'artwork' => $certificateData['artwork'],
                'verification' => $certificateData['verification']
            ]);
        } catch (\Exception $e) {
            $this->logger->error('[Verify Controller] Certificate view failed', [
                'hash' => substr($hash, 0, 8) . '...',
                'error' => $e->getMessage(),
                'ip_address' => $request->ip()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to load certificate'
                ], 500);
            }

            return view('coa.public.error', [
                'message' => 'Unable to load certificate'
            ]);
        }
    }

    /**
     * View certificate by serial number (HTML page)
     *
     * @param Request $request
     * @param string $serial
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     * @privacy-safe Returns public certificate view by serial
     */
    public function viewCertificateBySerial(Request $request, string $serial) {
        try {
            // Rate limiting for certificate viewing
            $key = 'view_cert_serial_' . $request->ip();
            if (RateLimiter::tooManyAttempts($key, 30)) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Too many requests. Please try again later.'
                    ], 429);
                }
                return back()->withErrors(['message' => 'Too many requests. Please try again later.']);
            }

            RateLimiter::hit($key, 300); // 5 minutes

            // Find certificate by serial
            $coa = Coa::where('serial', $serial)
                ->with(['egi.traits.category', 'egi.traits.traitType', 'egi.user'])
                ->first();

            if (!$coa) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Certificate not found'
                    ], 404);
                }
                return view('coa.public.not-found', ['serial' => $serial]);
            }

            // Check if has CoA specific traits
            $hasValidCoaTraits = false;
            $coaTraits = $coa->egi->coaTraits;
            if ($coaTraits) {
                $hasValidCoaTraits = !empty($coaTraits->technique_slugs) ||
                    !empty($coaTraits->materials_slugs) ||
                    !empty($coaTraits->support_slugs) ||
                    !empty($coaTraits->technique_free_text) ||
                    !empty($coaTraits->materials_free_text) ||
                    !empty($coaTraits->support_free_text);
            }

            // Generate certificate view data
            $certificateData = [
                'certificate' => [
                    'id' => $coa->id,
                    'serial' => $coa->serial,
                    'status' => $coa->status,
                    'issued_at' => $coa->issued_at,
                    'issuer_name' => $coa->issuer_name,
                    'notes' => $coa->notes,
                    'verification_hash' => $coa->verification_hash ?? hash('sha256', $coa->serial . $coa->issued_at),
                    'issue_location' => $coa->issue_location ?? 'Firenze, Italia',
                    'qes_signature' => $coa->qes_signature ?? false,
                    'wallet_signature' => $coa->wallet_signature ?? true,
                    'wallet_public_key' => $coa->wallet_public_key ?? '0x' . Str::random(40),
                    'has_annexes' => $coa->annexes()->exists(),
                    'blockchain_info' => $coa->blockchain_asset_id ? [
                        'network' => 'Ethereum Mainnet',
                        'asset_id' => $coa->blockchain_asset_id,
                        'explorer_url' => 'https://etherscan.io/token/' . $coa->blockchain_asset_id
                    ] : null,
                    'version' => $coa->version ?? 1,
                    'has_coa_traits' => $hasValidCoaTraits,
                    'effective_status' => ($coa->status === 'valid' && $hasValidCoaTraits) ? 'valid' : 'incomplete'
                ],
                'artwork' => [
                    'name' => $coa->egi->title ?? $coa->egi->name ?? 'Unknown Artwork',
                    'internal_id' => str_pad($coa->egi->id, 7, '0', STR_PAD_LEFT),
                    'description' => $coa->egi->description ?? '',
                    'author' => $this->extractAuthorFromTraits($coa->egi), // Using trait method
                    'year' => $this->extractYearFromTraits($coa->egi),
                    'technique' => $this->extractTechniqueFromTraits($coa->egi),
                    'materials' => $this->extractMaterialsFromTraits($coa->egi), // AGGIUNTO: estrazione materiali
                    'support' => $this->extractSupportFromTraits($coa->egi),
                    'dimensions' => $this->extractDimensionsFromTraits($coa->egi),
                    'edition' => $this->extractEditionFromTraits($coa->egi),
                    'traits' => $this->extractAllArtworkMetadata($coa->egi),
                    'thumbnail' => $coa->egi->media ? asset('storage/' . $coa->egi->media) : null,
                    'image_url' => $coa->egi->main_image_url, // AGGIUNTO: URL immagine principale
                    'thumbnail_url' => $coa->egi->thumbnail_image_url, // AGGIUNTO: URL thumbnail
                    'dossier_link' => route('egis.show', $coa->egi->id) . '#gallery'
                ],
                'verification' => [
                    'is_valid' => $coa->status === 'valid',
                    'verified_at' => now(),
                    'verification_url' => route('coa.verify.certificate', $coa->serial)
                ]
            ];

            // Add creator info from database if available
            if ($coa->creator_info) {
                $certificateData['creator'] = $coa->creator_info;
            }

            // Add annexes data if available
            if ($coa->annexes()->exists()) {
                $certificateData['annexes'] = [
                    'provenance' => $this->getAnnexData($coa, 'A_PROVENANCE'),
                    'condition' => $this->getAnnexData($coa, 'B_CONDITION'),
                    'exhibitions' => $this->getAnnexData($coa, 'C_EXHIBITIONS'),
                    'photos' => $this->getAnnexData($coa, 'D_PHOTOS'),
                    'authorization' => $this->getAnnexData($coa, 'E_AUTHORIZATION') // Nuovo Annesso E
                ];
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $certificateData
                ]);
            }

            // Return Blade view for web browsers
            return view('coa.public.certificate', [
                'certificate' => $certificateData['certificate'],
                'artwork' => $certificateData['artwork'],
                'verification' => $certificateData['verification'],
                'creator' => $certificateData['creator'] ?? null,
                'annexes' => $certificateData['annexes'] ?? []
            ]);
        } catch (\Exception $e) {
            $this->logger->error('[Verify Controller] Certificate view by serial failed', [
                'serial' => $serial,
                'error' => $e->getMessage(),
                'ip_address' => $request->ip()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to load certificate'
                ], 500);
            }

            return view('coa.public.error', [
                'message' => 'Unable to load certificate'
            ]);
        }
    }

    /**
     * Get creator info only when Creator ≠ Author (for role distinction)
     */
    private function getCreatorInfo($coa) {
        $author = $this->extractAuthorFromTraits($coa->egi);
        $creator = $coa->egi->user->name ?? null;

        // Only return creator info if creator is different from author
        if ($creator && $creator !== $author) {
            return [
                'name' => $creator,
                'role' => 'Creator/Uploader',
                'platform_id' => $coa->egi->user->id,
                'relationship_to_author' => $this->getCreatorRelationship($coa),
                'upload_date' => $coa->egi->created_at?->format('Y-m-d'),
                'verification_method' => $this->getVerificationMethod($coa)
            ];
        }

        return null;
    }

    /**
     * Determine relationship between creator and author using traits
     */
    private function getCreatorRelationship($coa) {
        // First check for relationship information in traits
        $relationshipFromTraits = $this->extractRelationshipFromTraits($coa->egi);

        if ($relationshipFromTraits) {
            return $relationshipFromTraits;
        }

        // Check for authorization documents in Annex E
        $authAnnex = $this->getAnnexData($coa, 'E_AUTHORIZATION');
        if ($authAnnex && isset($authAnnex['data']['authorization_type'])) {
            return $authAnnex['data']['authorization_type'];
        }

        // Check if traits contain authorization info
        if ($this->hasAuthorizationInTraits($coa->egi)) {
            return 'Autorizzato (vedi traits)';
        }

        // Default relationship types based on common scenarios
        $creator = $coa->egi->user->name ?? '';

        // Simple heuristics based on creator name patterns
        if (stripos($creator, 'gallery') !== false || stripos($creator, 'galleria') !== false) {
            return 'Rappresentante della galleria';
        }

        if (stripos($creator, 'estate') !== false || stripos($creator, 'eredi') !== false) {
            return 'Rappresentante degli eredi';
        }

        if (stripos($creator, 'archive') !== false || stripos($creator, 'archivio') !== false) {
            return 'Archivio autorizzato';
        }

        return 'Caricatore della piattaforma';
    }

    /**
     * Get verification method for creator authorization
     */
    private function getVerificationMethod($coa) {
        // Check for verification method in traits
        $verificationFromTraits = $this->extractTraitValue($coa->egi, [
            'Verifica',
            'Verification',
            'Metodo verifica',
            'Verification method'
        ]);

        if ($verificationFromTraits) {
            return $verificationFromTraits;
        }

        // Check if there's an Annex E with verification documents
        $authAnnex = $this->getAnnexData($coa, 'E_AUTHORIZATION');
        if ($authAnnex) {
            return 'Documentazione allegata (Annesso E)';
        }

        // Check if traits contain authorization references
        if ($this->hasAuthorizationInTraits($coa->egi)) {
            return 'Autorizzazione documentata nei traits';
        }

        return 'Non specificato';
    }
    private function extractTraitValue($egi, array $needles): ?string {
        foreach ($needles as $needle) {
            $trait = $egi->traits->first(function ($trait) use ($needle) {
                return stripos($trait->traitType->name ?? '', $needle) !== false ||
                    stripos($trait->category->name ?? '', $needle) !== false;
            });

            if ($trait) {
                return $trait->value;
            }
        }

        return null;
    }

    /**
     * Get annex data for a specific type
     *
     * @param \App\Models\Coa $coa
     * @param string $type
     * @return array|null
     */
    private function getAnnexData($coa, string $type): ?array {
        $annex = $coa->annexes()->where('type', $type)->first();

        if (!$annex) {
            return null;
        }

        return [
            'version' => $annex->version ?? 1,
            'items_count' => $annex->items_count ?? 0,
            'hash' => hash('sha256', $annex->data ?? ''),
            'download_url' => route('coa.annexes.download', [$coa->id, $type])
        ];
    }

    //--------------------------------------------------------------------------
    // CoA Traits Extraction Methods
    //--------------------------------------------------------------------------

    /**
     * Extract author from CoA traits or EGI data
     *
     * @param \App\Models\Egi $egi
     * @return string
     */
    private function extractAuthorFromTraits($egi): string {
        $coaTraits = $egi->coaTraits;

        if ($coaTraits) {
            // Check for author in CoA traits custom text
            $authorFromCustom = $this->findAuthorInCustomTraits($coaTraits);
            if ($authorFromCustom) {
                return $authorFromCustom;
            }
        }

        // Fallback to EGI author field or generic trait extraction
        if (!empty($egi->author)) {
            return $egi->author;
        }

        $authorFromTraits = $this->extractTraitValue($egi, ['Autore', 'Author', 'Artist', 'Artista']);
        if ($authorFromTraits) {
            return $authorFromTraits;
        }

        return $egi->user->name ?? 'Unknown Author';
    }

    /**
     * Extract year from CoA traits or EGI data
     *
     * @param \App\Models\Egi $egi
     * @return string|null
     */
    private function extractYearFromTraits($egi): ?string {
        // Try CoA traits first (check if any custom text contains year)
        $coaTraits = $egi->coaTraits;
        if ($coaTraits) {
            $yearFromCoaTraits = $this->findYearInCoaTraits($coaTraits);
            if ($yearFromCoaTraits) {
                return $yearFromCoaTraits;
            }
        }

        // Fallback to EGI year field or generic traits
        if (!empty($egi->year)) {
            return (string) $egi->year;
        }

        return $this->extractTraitValue($egi, ['Anno', 'Year', 'Data', 'Date']);
    }

    /**
     * Extract technique from CoA traits
     *
     * @param \App\Models\Egi $egi
     * @return string|null
     */
    private function extractTechniqueFromTraits($egi): ?string {
        $coaTraits = $egi->coaTraits;

        if ($coaTraits && (!empty($coaTraits->technique_slugs) || !empty($coaTraits->technique_free_text))) {
            $vocabularyTranslations = __('coa_vocabulary');
            $techniques = [];

            // Add vocabulary terms from slugs
            if (!empty($coaTraits->technique_slugs)) {
                foreach ($coaTraits->technique_slugs as $slug) {
                    $techniques[] = $vocabularyTranslations[$slug] ?? ucfirst(str_replace(['_', '-'], ' ', $slug));
                }
            }

            // Add custom technique free text if present
            if (!empty($coaTraits->technique_free_text)) {
                foreach ($coaTraits->technique_free_text as $customTechnique) {
                    $techniques[] = $customTechnique;
                }
            }

            return implode(', ', $techniques);
        }

        // Fallback to EGI technique field or generic traits
        if (!empty($egi->technique)) {
            return $egi->technique;
        }

        return $this->extractTraitValue($egi, ['Tecnica', 'Technique', 'Medium']);
    }

    /**
     * Extract materials from CoA traits
     *
     * @param \App\Models\Egi $egi
     * @return string|null
     */
    private function extractMaterialsFromTraits($egi): ?string {
        $coaTraits = $egi->coaTraits;

        if ($coaTraits && (!empty($coaTraits->materials_slugs) || !empty($coaTraits->materials_free_text))) {
            $vocabularyTranslations = __('coa_vocabulary');
            $materials = [];

            // Add vocabulary terms from slugs
            if (!empty($coaTraits->materials_slugs)) {
                foreach ($coaTraits->materials_slugs as $slug) {
                    $materials[] = $vocabularyTranslations[$slug] ?? ucfirst(str_replace(['_', '-'], ' ', $slug));
                }
            }

            // Add custom materials if present
            if (!empty($coaTraits->materials_free_text)) {
                foreach ($coaTraits->materials_free_text as $customMaterial) {
                    $materials[] = $customMaterial;
                }
            }

            return implode(', ', $materials);
        }

        // Fallback to EGI materials field or generic traits
        if (!empty($egi->materials)) {
            return $egi->materials;
        }

        return $this->extractTraitValue($egi, ['Materiale', 'Materials', 'Material']);
    }

    /**
     * Extract support from CoA traits
     *
     * @param \App\Models\Egi $egi
     * @return string|null
     */
    private function extractSupportFromTraits($egi): ?string {
        $coaTraits = $egi->coaTraits;

        if ($coaTraits && (!empty($coaTraits->support_slugs) || !empty($coaTraits->support_free_text))) {
            $vocabularyTranslations = __('coa_vocabulary');
            $supports = [];

            // Add vocabulary terms from slugs
            if (!empty($coaTraits->support_slugs)) {
                foreach ($coaTraits->support_slugs as $slug) {
                    $supports[] = $vocabularyTranslations[$slug] ?? ucfirst(str_replace(['_', '-'], ' ', $slug));
                }
            }

            // Add custom support free text if present
            if (!empty($coaTraits->support_free_text)) {
                foreach ($coaTraits->support_free_text as $customSupport) {
                    $supports[] = $customSupport;
                }
            }

            return implode(', ', $supports);
        }

        // Fallback to generic traits or EGI fields
        return $this->extractTraitValue($egi, ['Supporto', 'Support', 'Material']);
    }

    /**
     * Extract dimensions from CoA traits or EGI data
     *
     * @param \App\Models\Egi $egi
     * @return string|null
     */
    private function extractDimensionsFromTraits($egi): ?string {
        // Check if dimensions info is in CoA custom text
        $coaTraits = $egi->coaTraits;
        if ($coaTraits) {
            $dimensionsFromCoaTraits = $this->findDimensionsInCoaTraits($coaTraits);
            if ($dimensionsFromCoaTraits) {
                return $dimensionsFromCoaTraits;
            }
        }

        // Fallback to EGI dimensions field or generic traits
        if (!empty($egi->dimensions)) {
            return $egi->dimensions;
        }

        return $this->extractTraitValue($egi, ['Dimensioni', 'Dimensions', 'Size']);
    }

    /**
     * Extract edition from CoA traits or EGI data
     *
     * @param \App\Models\Egi $egi
     * @return string|null
     */
    private function extractEditionFromTraits($egi): ?string {
        // Check if edition info is in CoA custom text
        $coaTraits = $egi->coaTraits;
        if ($coaTraits) {
            $editionFromCoaTraits = $this->findEditionInCoaTraits($coaTraits);
            if ($editionFromCoaTraits) {
                return $editionFromCoaTraits;
            }
        }

        return $this->extractTraitValue($egi, ['Edizione', 'Edition', 'Tiratura']);
    }

    /**
     * Extract all artwork metadata in structured format for certificate display
     *
     * @param \App\Models\Egi $egi
     * @return array
     */
    private function extractAllArtworkMetadata($egi): array {
        $traits = [];
        $metadata = [];
        $coaTraits = $egi->coaTraits;
        $hasValidCoaTraits = false;

        if ($coaTraits) {
            // Check if has any valid CoA traits
            $hasValidCoaTraits = !empty($coaTraits->technique_slugs) ||
                !empty($coaTraits->materials_slugs) ||
                !empty($coaTraits->support_slugs) ||
                !empty($coaTraits->technique_free_text) ||
                !empty($coaTraits->materials_free_text) ||
                !empty($coaTraits->support_free_text);
        }

        if ($hasValidCoaTraits) {
            // Use structured CoA traits (ONLY for the main certificate sections)
            $vocabularyTranslations = __('coa_vocabulary');

            // Technique traits
            if (!empty($coaTraits->technique_slugs)) {
                foreach ($coaTraits->technique_slugs as $slug) {
                    $traits[] = [
                        'trait_type' => 'Tecnica',
                        'value' => $vocabularyTranslations[$slug] ?? ucfirst(str_replace(['_', '-'], ' ', $slug)),
                        'category' => 'technique'
                    ];
                }
            }

            if (!empty($coaTraits->technique_free_text)) {
                foreach ($coaTraits->technique_free_text as $text) {
                    $traits[] = [
                        'trait_type' => 'Tecnica (Custom)',
                        'value' => $text,
                        'category' => 'technique'
                    ];
                }
            }

            // Materials traits
            if (!empty($coaTraits->materials_slugs)) {
                foreach ($coaTraits->materials_slugs as $slug) {
                    $traits[] = [
                        'trait_type' => 'Materiale',
                        'value' => $vocabularyTranslations[$slug] ?? ucfirst(str_replace(['_', '-'], ' ', $slug)),
                        'category' => 'materials'
                    ];
                }
            }

            if (!empty($coaTraits->materials_free_text)) {
                foreach ($coaTraits->materials_free_text as $text) {
                    $traits[] = [
                        'trait_type' => 'Materiale (Custom)',
                        'value' => $text,
                        'category' => 'materials'
                    ];
                }
            }

            // Support traits
            if (!empty($coaTraits->support_slugs)) {
                foreach ($coaTraits->support_slugs as $slug) {
                    $traits[] = [
                        'trait_type' => 'Supporto',
                        'value' => $vocabularyTranslations[$slug] ?? ucfirst(str_replace(['_', '-'], ' ', $slug)),
                        'category' => 'support'
                    ];
                }
            }

            if (!empty($coaTraits->support_free_text)) {
                foreach ($coaTraits->support_free_text as $text) {
                    $traits[] = [
                        'trait_type' => 'Supporto (Custom)',
                        'value' => $text,
                        'category' => 'support'
                    ];
                }
            }

            // IMPORTANT: When CoA traits exist, do NOT put them in 'data' field
            // They are already handled by the structured template sections
            // The 'data' field should remain empty to avoid duplication

        } else {
            // Fallback to generic EGI traits as primary traits (when no CoA traits exist)
            if ($egi->traits && $egi->traits->count() > 0) {
                foreach ($egi->traits as $trait) {
                    if ($trait->value && trim($trait->value) !== '') {
                        $traits[] = [
                            'trait_type' => $trait->traitType->name ?? 'Unknown',
                            'value' => $trait->value,
                            'category' => 'generic'
                        ];
                    }
                }
            }
        }

        // Always add EGI description and technical metadata
        $metadata = $this->extractAdditionalMetadata($egi);

        return [
            'data' => $hasValidCoaTraits ? [] : $traits, // When CoA traits exist, don't duplicate in 'data'
            'metadata' => $metadata,
            'source_type' => $hasValidCoaTraits ? 'coa_traits' : 'generic_egi',
            'traits_incomplete' => !$hasValidCoaTraits
        ];
    }

    /**
     * Extract additional metadata from EGI (description, technical info, platform traits)
     *
     * @param \App\Models\Egi $egi
     * @return array
     */
    private function extractAdditionalMetadata($egi): array {
        $metadata = [];

        // PLATFORM TRAITS (from egi_traits table) - These are separate from CoA traits
        if ($egi->traits && $egi->traits->count() > 0) {
            foreach ($egi->traits as $trait) {
                if ($trait->value && trim($trait->value) !== '') {
                    $metadata[] = [
                        'type' => 'platform_trait',
                        'label' => $trait->traitType->name ?? 'Trait Piattaforma',
                        'value' => $trait->value,
                        'category' => 'platform_metadata'
                    ];
                }
            }
        }

        // Description (often missing in certificates)
        if (!empty($egi->description)) {
            $metadata[] = [
                'type' => 'description',
                'label' => 'Descrizione Opera',
                'value' => $egi->description,
                'category' => 'artwork_info'
            ];
        }

        // File technical information
        if (!empty($egi->size)) {
            $metadata[] = [
                'type' => 'file_size',
                'label' => 'Dimensioni File',
                'value' => $egi->size,
                'category' => 'technical'
            ];
        }

        if (!empty($egi->dimension)) {
            $metadata[] = [
                'type' => 'image_dimensions',
                'label' => 'Dimensioni Immagine',
                'value' => $egi->dimension,
                'category' => 'technical'
            ];
        }

        if (!empty($egi->file_mime)) {
            $metadata[] = [
                'type' => 'mime_type',
                'label' => 'Tipo File',
                'value' => $egi->file_mime,
                'category' => 'technical'
            ];
        }

        if (!empty($egi->extension)) {
            $metadata[] = [
                'type' => 'extension',
                'label' => 'Estensione',
                'value' => strtoupper($egi->extension),
                'category' => 'technical'
            ];
        }

        // Additional JSON metadata
        if (!empty($egi->jsonMetadata) && is_array($egi->jsonMetadata)) {
            foreach ($egi->jsonMetadata as $key => $value) {
                if (!empty($value) && !is_array($value) && !is_object($value)) {
                    $metadata[] = [
                        'type' => 'json_metadata',
                        'label' => ucfirst(str_replace(['_', '-'], ' ', $key)),
                        'value' => (string) $value,
                        'category' => 'platform_metadata'
                    ];
                }
            }
        }

        // Creation and publishing dates
        if (!empty($egi->creation_date)) {
            $metadata[] = [
                'type' => 'creation_date',
                'label' => 'Data Creazione Artistica',
                'value' => $egi->creation_date->format('d/m/Y'),
                'category' => 'artwork_info'
            ];
        }

        if (!empty($egi->created_at)) {
            $metadata[] = [
                'type' => 'upload_date',
                'label' => 'Data Caricamento Piattaforma',
                'value' => $egi->created_at->format('d/m/Y H:i'),
                'category' => 'platform_metadata'
            ];
        }

        // Publication status
        if (isset($egi->is_published)) {
            $metadata[] = [
                'type' => 'publication_status',
                'label' => 'Stato Pubblicazione',
                'value' => $egi->is_published ? 'Pubblicato' : 'Non Pubblicato',
                'category' => 'platform_metadata'
            ];
        }

        // Collection information
        if ($egi->collection && !empty($egi->collection->name)) {
            $metadata[] = [
                'type' => 'collection',
                'label' => 'Collezione',
                'value' => $egi->collection->name,
                'category' => 'artwork_info'
            ];
        }

        return $metadata;
    }

    //--------------------------------------------------------------------------
    // CoA Traits Helper Methods
    //--------------------------------------------------------------------------

    /**
     * Find author in CoA custom traits text
     */
    private function findAuthorInCustomTraits($coaTraits): ?string {
        $customTexts = [
            $coaTraits->technique_other,
            $coaTraits->materials_other,
            $coaTraits->support_other
        ];

        foreach ($customTexts as $text) {
            if (empty($text)) continue;

            // Look for patterns like "Author: Name" or "Autore: Nome"
            $patterns = ['autore:', 'author:', 'artist:', 'artista:'];
            foreach ($patterns as $pattern) {
                $pos = stripos($text, $pattern);
                if ($pos !== false) {
                    $afterPattern = substr($text, $pos + strlen($pattern));
                    $name = trim(explode(',', explode(';', $afterPattern)[0])[0]);
                    if (!empty($name)) {
                        return $name;
                    }
                }
            }
        }

        return null;
    }

    /**
     * Find year in CoA traits
     */
    private function findYearInCoaTraits($coaTraits): ?string {
        $customTexts = [
            $coaTraits->technique_other,
            $coaTraits->materials_other,
            $coaTraits->support_other
        ];

        foreach ($customTexts as $text) {
            if (empty($text)) continue;

            // Look for 4-digit years (1900-2099)
            for ($year = 1900; $year <= 2099; $year++) {
                if (strpos($text, (string)$year) !== false) {
                    return (string)$year;
                }
            }
        }

        return null;
    }

    /**
     * Find dimensions in CoA traits
     */
    private function findDimensionsInCoaTraits($coaTraits): ?string {
        $customTexts = [
            $coaTraits->technique_other,
            $coaTraits->materials_other,
            $coaTraits->support_other
        ];

        foreach ($customTexts as $text) {
            if (empty($text)) continue;

            // Look for dimension patterns like "120x80" or "120 x 80"
            $text = str_replace(['X', '×'], 'x', strtolower($text));
            $words = explode(' ', $text);
            foreach ($words as $word) {
                if (strpos($word, 'x') !== false) {
                    $parts = explode('x', $word);
                    if (count($parts) == 2 && is_numeric(trim($parts[0])) && is_numeric(trim($parts[1]))) {
                        return trim($parts[0]) . 'x' . trim($parts[1]);
                    }
                }
            }
        }

        return null;
    }

    /**
     * Find edition in CoA traits
     */
    private function findEditionInCoaTraits($coaTraits): ?string {
        $customTexts = [
            $coaTraits->technique_other,
            $coaTraits->materials_other,
            $coaTraits->support_other
        ];

        foreach ($customTexts as $text) {
            if (empty($text)) continue;

            // Look for edition patterns
            $patterns = ['edizione:', 'edition:', 'tiratura:'];
            foreach ($patterns as $pattern) {
                $pos = stripos($text, $pattern);
                if ($pos !== false) {
                    $afterPattern = substr($text, $pos + strlen($pattern));
                    $edition = trim(explode(',', explode(';', $afterPattern)[0])[0]);
                    if (!empty($edition)) {
                        return $edition;
                    }
                }
            }

            // Look for fraction patterns like "1/10"
            if (strpos($text, '/') !== false) {
                $words = explode(' ', $text);
                foreach ($words as $word) {
                    if (strpos($word, '/') !== false) {
                        $parts = explode('/', $word);
                        if (count($parts) == 2 && is_numeric($parts[0]) && is_numeric($parts[1])) {
                            return $word;
                        }
                    }
                }
            }
        }

        return null;
    }

    /**
     * Download certificate PDF by serial number
     *
     * @param Request $request
     * @param string $serial
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     * @privacy-safe Public PDF download with rate limiting
     * @community-impact Enables certificate download for verification
     * @transparency-level High - public certificate PDF access
     * @narrative-coherence Links to PDF generation system
     */
    public function downloadCertificatePdf(Request $request, string $serial) {
        try {
            // Rate limiting for PDF downloads
            $key = 'pdf_download_' . $request->ip();
            if (RateLimiter::tooManyAttempts($key, 5)) {
                $this->logger->warning('[Verify Controller] Rate limit exceeded for PDF download', [
                    'ip_address' => $request->ip(),
                    'serial' => $serial
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Too many PDF download attempts. Please try again later.'
                ], 429);
            }

            RateLimiter::hit($key, 60); // 1 minute lockout after 5 attempts

            // Find the certificate
            $coa = Coa::where('serial', $serial)
                ->with(['egi', 'egi.coaTraits'])
                ->first();

            if (!$coa) {
                $this->logger->info('[Verify Controller] PDF download attempted for non-existent certificate', [
                    'serial' => $serial,
                    'ip_address' => $request->ip()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Certificate not found'
                ], 404);
            }

            // Check if certificate is publicly accessible
            if (!$coa->egi || !$coa->egi->is_public) {
                $this->logger->info('[Verify Controller] PDF download attempted for non-public certificate', [
                    'serial' => $serial,
                    'ip_address' => $request->ip()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Certificate not publicly accessible'
                ], 403);
            }

            // Generate PDF using CoaPdfService
            $pdfResult = $this->coaPdfService->generateCorePdf($coa);

            if (!$pdfResult['success']) {
                $this->logger->error('[Verify Controller] PDF generation failed', [
                    'serial' => $serial,
                    'error' => $pdfResult['message'] ?? 'Unknown error',
                    'ip_address' => $request->ip()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Unable to generate PDF certificate'
                ], 500);
            }

            $this->logger->info('[Verify Controller] PDF certificate downloaded successfully', [
                'serial' => $serial,
                'ip_address' => $request->ip()
            ]);

            // Return PDF file download response
            $fileName = 'certificate_' . $serial . '.pdf';

            return response($pdfResult['pdf_content'])
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"')
                ->header('Content-Length', strlen($pdfResult['pdf_content']));
        } catch (\Exception $e) {
            // Temporarily show the actual error for debugging
            \Log::error('[Verify Controller] PDF generation error: ' . $e->getMessage());
            \Log::error('[Verify Controller] PDF generation stack: ' . $e->getTraceAsString());

            throw $e; // Re-throw to see the actual error

            $this->errorManager->handle('CERTIFICATE_PDF_DOWNLOAD_ERROR', [
                'serial' => $serial,
                'error' => $e->getMessage(),
                'ip_address' => $request->ip(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            return response()->json([
                'success' => false,
                'message' => 'Unable to download certificate PDF'
            ], 500);
        }
    }
}
