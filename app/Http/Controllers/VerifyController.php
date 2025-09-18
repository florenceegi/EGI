<?php

namespace App\Http\Controllers;

use App\Models\Coa;
use App\Services\Coa\VerifyPageService;
use App\Services\Coa\AnnexService;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;

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
     * Constructor with dependency injection
     *
     * @param UltraLogManager $logger
     * @param ErrorManagerInterface $errorManager
     * @param VerifyPageService $verifyService
     * @param AnnexService $annexService
     * @privacy-safe All injected services handle public verification safely
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        VerifyPageService $verifyService,
        AnnexService $annexService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->verifyService = $verifyService;
        $this->annexService = $annexService;

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

            // Generate verification data using service
            $verificationData = $this->verifyService->generateVerificationData($coa);

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
     * @param string $serial
     * @return JsonResponse
     * @privacy-safe Returns public verification page data
     */
    public function verificationPage(Request $request, string $serial): JsonResponse {
        try {
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
}
