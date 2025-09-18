<?php

namespace App\Http\Controllers;

use App\Models\Coa;
use App\Models\Egi;
use App\Services\Coa\CoaIssueService;
use App\Services\Coa\CoaRevocationService;
use App\Services\Coa\AnnexService;
use App\Services\Coa\BundleService;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Services\Gdpr\AuditLogService;
use App\Enums\Gdpr\GdprActivityCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * @Oracode Controller: CoA Certificate Management
 * 🎯 Purpose: Handle Certificate of Authenticity operations
 * 🛡️ Privacy: GDPR-compliant CoA management with full audit trail
 * 🧱 Core Logic: Coordinates Services for certificate lifecycle management
 *
 * @package App\Http\Controllers
 * @author AI Assistant following FlorenceEGI patterns
 * @version 1.0.0 (CoA Pro System)
 * @date 2025-09-18
 * @purpose Professional certificate management following FlorenceEGI architecture
 */
class CoaController extends Controller {
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
     * CoA issuance service
     * @var CoaIssueService
     */
    protected CoaIssueService $issueService;

    /**
     * CoA revocation service
     * @var CoaRevocationService
     */
    protected CoaRevocationService $revocationService;

    /**
     * Annex management service
     * @var AnnexService
     */
    protected AnnexService $annexService;

    /**
     * Bundle creation service
     * @var BundleService
     */
    protected BundleService $bundleService;

    /**
     * Constructor with dependency injection
     *
     * @param UltraLogManager $logger
     * @param ErrorManagerInterface $errorManager
     * @param AuditLogService $auditService
     * @param CoaIssueService $issueService
     * @param CoaRevocationService $revocationService
     * @param AnnexService $annexService
     * @param BundleService $bundleService
     * @privacy-safe All injected services handle GDPR compliance
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService,
        CoaIssueService $issueService,
        CoaRevocationService $revocationService,
        AnnexService $annexService,
        BundleService $bundleService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditService = $auditService;
        $this->issueService = $issueService;
        $this->revocationService = $revocationService;
        $this->annexService = $annexService;
        $this->bundleService = $bundleService;

        // Apply auth middleware
        $this->middleware('auth');
    }

    /**
     * Display a listing of user's CoA certificates
     *
     * @param Request $request
     * @return JsonResponse
     * @privacy-safe Returns only authenticated user's certificates
     *
     * @oracode-dimension governance
     * @value-flow Provides certificate overview for portfolio management
     * @community-impact Enables certificate discovery and management
     * @transparency-level Medium - certificate listing with metadata
     * @narrative-coherence Links certificates to user's collection
     */
    public function index(Request $request): JsonResponse {
        try {
            $user = Auth::user();

            $this->logger->info('[CoA Controller] Listing user certificates', [
                'user_id' => $user->id,
                'request_ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            // Get user's EGIs with CoA certificates
            $query = Coa::whereHas('egi', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->with(['egi', 'annexes', 'events']);

            // Apply filters
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($query) use ($search) {
                    $query->where('serial', 'like', "%{$search}%")
                        ->orWhereHas('egi', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%");
                        });
                });
            }

            // Apply sorting
            $sortBy = $request->get('sort_by', 'issue_date');
            $sortDirection = $request->get('sort_direction', 'desc');
            $query->orderBy($sortBy, $sortDirection);

            // Paginate results
            $perPage = min($request->get('per_page', 15), 50); // Max 50 per page
            $certificates = $query->paginate($perPage);

            $response = [
                'success' => true,
                'message' => 'Certificates retrieved successfully',
                'data' => [
                    'certificates' => $certificates->items(),
                    'pagination' => [
                        'current_page' => $certificates->currentPage(),
                        'last_page' => $certificates->lastPage(),
                        'per_page' => $certificates->perPage(),
                        'total' => $certificates->total(),
                        'from' => $certificates->firstItem(),
                        'to' => $certificates->lastItem()
                    ],
                    'filters_applied' => [
                        'status' => $request->status,
                        'search' => $request->search,
                        'sort_by' => $sortBy,
                        'sort_direction' => $sortDirection
                    ]
                ]
            ];

            $this->logger->info('[CoA Controller] Certificates retrieved successfully', [
                'user_id' => $user->id,
                'total_certificates' => $certificates->total(),
                'current_page' => $certificates->currentPage()
            ]);

            return response()->json($response, 200);
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_INDEX_ERROR', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
                'ip_address' => $request->ip(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve certificates',
                'error' => 'An error occurred while fetching your certificates'
            ], 500);
        }
    }

    /**
     * Display the specified CoA certificate
     *
     * @param Request $request
     * @param Coa $coa
     * @return JsonResponse
     * @privacy-safe Returns certificate only if user owns the associated EGI
     */
    public function show(Request $request, Coa $coa): JsonResponse {
        try {
            $user = Auth::user();

            // Security check - user must own the EGI
            if ($coa->egi->user_id !== $user->id) {
                $this->logger->warning('[CoA Controller] Unauthorized certificate access attempt', [
                    'user_id' => $user->id,
                    'coa_id' => $coa->id,
                    'egi_owner_id' => $coa->egi->user_id,
                    'ip_address' => $request->ip()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Certificate not found or access denied'
                ], 404);
            }

            $this->logger->info('[CoA Controller] Retrieving certificate details', [
                'user_id' => $user->id,
                'coa_id' => $coa->id,
                'serial' => $coa->serial
            ]);

            // Load relationships
            $coa->load(['egi', 'annexes' => function ($query) {
                $query->where('status', 'active')->orderBy('type')->orderBy('version', 'desc');
            }, 'events' => function ($query) {
                $query->orderBy('occurred_at', 'desc')->limit(20);
            }, 'signatures', 'files']);

            // Get annexes summary if requested
            $includeAnnexes = $request->boolean('include_annexes', true);
            $annexesData = null;
            if ($includeAnnexes) {
                $annexesData = $this->annexService->getCoaAnnexes($coa, true);
            }

            $response = [
                'success' => true,
                'message' => 'Certificate retrieved successfully',
                'data' => [
                    'certificate' => $coa,
                    'annexes' => $annexesData,
                    'stats' => [
                        'total_annexes' => $coa->annexes->count(),
                        'total_events' => $coa->events->count(),
                        'total_signatures' => $coa->signatures->count(),
                        'total_files' => $coa->files->count()
                    ]
                ]
            ];

            // Log audit trail
            $this->auditService->logUserAction($user, 'coa_viewed', [
                'coa_id' => $coa->id,
                'serial' => $coa->serial,
                'egi_id' => $coa->egi->id,
                'include_annexes' => $includeAnnexes
            ], GdprActivityCategory::GDPR_ACTIONS);

            return response()->json($response, 200);
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_SHOW_ERROR', [
                'user_id' => Auth::id(),
                'coa_id' => $coa->id,
                'error' => $e->getMessage(),
                'ip_address' => $request->ip(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve certificate',
                'error' => 'An error occurred while fetching the certificate'
            ], 500);
        }
    }

    /**
     * Issue a new CoA certificate for an EGI
     *
     * @param Request $request
     * @return JsonResponse
     * @privacy-safe Issues certificate only for authenticated user's EGI
     */
    public function issue(Request $request): JsonResponse {
        try {
            $user = Auth::user();

            // Validate request
            $validator = Validator::make($request->all(), [
                'egi_id' => 'required|integer|exists:egis,id',
                'issued_by' => 'nullable|string|max:255',
                'notes' => 'nullable|string|max:1000'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $egi = Egi::findOrFail($request->egi_id);

            // Security check - user must own the EGI
            if ($egi->user_id !== $user->id) {
                $this->logger->warning('[CoA Controller] Unauthorized certificate issuance attempt', [
                    'user_id' => $user->id,
                    'egi_id' => $egi->id,
                    'egi_owner_id' => $egi->user_id,
                    'ip_address' => $request->ip()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'EGI not found or access denied'
                ], 404);
            }

            $this->logger->info('[CoA Controller] Issuing certificate', [
                'user_id' => $user->id,
                'egi_id' => $egi->id,
                'egi_name' => $egi->name,
                'issued_by' => $request->issued_by
            ]);

            // Issue certificate using service
            $coa = $this->issueService->issueCertificate(
                $egi,
                $request->issued_by ?? $user->name,
                $request->notes
            );

            $response = [
                'success' => true,
                'message' => 'Certificate issued successfully',
                'data' => [
                    'certificate' => $coa,
                    'egi' => $egi,
                    'issued_at' => now()->toIso8601String()
                ]
            ];

            $this->logger->info('[CoA Controller] Certificate issued successfully', [
                'user_id' => $user->id,
                'coa_id' => $coa->id,
                'serial' => $coa->serial,
                'egi_id' => $egi->id
            ]);

            return response()->json($response, 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_ISSUE_ERROR', [
                'user_id' => Auth::id(),
                'egi_id' => $request->egi_id,
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
                'ip_address' => $request->ip(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            return response()->json([
                'success' => false,
                'message' => 'Failed to issue certificate',
                'error' => 'An error occurred while issuing the certificate'
            ], 500);
        }
    }

    /**
     * Re-issue an existing CoA certificate
     *
     * @param Request $request
     * @param Coa $coa
     * @return JsonResponse
     * @privacy-safe Re-issues certificate only for authenticated user's CoA
     */
    public function reissue(Request $request, Coa $coa): JsonResponse {
        try {
            $user = Auth::user();

            // Security check - user must own the EGI
            if ($coa->egi->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Certificate not found or access denied'
                ], 404);
            }

            // Validate request
            $validator = Validator::make($request->all(), [
                'reason' => 'required|string|max:500',
                'issued_by' => 'nullable|string|max:255',
                'notes' => 'nullable|string|max:1000'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $this->logger->info('[CoA Controller] Re-issuing certificate', [
                'user_id' => $user->id,
                'coa_id' => $coa->id,
                'serial' => $coa->serial,
                'reason' => $request->reason
            ]);

            // Re-issue certificate using service
            $newCoa = $this->issueService->reIssueCertificate(
                $coa,
                $request->reason,
                $request->issued_by ?? $user->name,
                $request->notes
            );

            $response = [
                'success' => true,
                'message' => 'Certificate re-issued successfully',
                'data' => [
                    'new_certificate' => $newCoa,
                    'original_certificate' => $coa,
                    'reason' => $request->reason,
                    'issued_at' => now()->toIso8601String()
                ]
            ];

            $this->logger->info('[CoA Controller] Certificate re-issued successfully', [
                'user_id' => $user->id,
                'original_coa_id' => $coa->id,
                'new_coa_id' => $newCoa->id,
                'new_serial' => $newCoa->serial
            ]);

            return response()->json($response, 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_REISSUE_ERROR', [
                'user_id' => Auth::id(),
                'coa_id' => $coa->id,
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
                'ip_address' => $request->ip(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            return response()->json([
                'success' => false,
                'message' => 'Failed to re-issue certificate',
                'error' => 'An error occurred while re-issuing the certificate'
            ], 500);
        }
    }

    /**
     * Revoke a CoA certificate
     *
     * @param Request $request
     * @param Coa $coa
     * @return JsonResponse
     * @privacy-safe Revokes certificate only for authenticated user's CoA
     */
    public function revoke(Request $request, Coa $coa): JsonResponse {
        try {
            $user = Auth::user();

            // Security check - user must own the EGI
            if ($coa->egi->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Certificate not found or access denied'
                ], 404);
            }

            // Validate request
            $validator = Validator::make($request->all(), [
                'reason' => 'required|string|in:duplicate,error,fraud,owner_request,legal_dispute,technical_issue',
                'notes' => 'nullable|string|max:1000',
                'revoked_by' => 'nullable|string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $this->logger->info('[CoA Controller] Revoking certificate', [
                'user_id' => $user->id,
                'coa_id' => $coa->id,
                'serial' => $coa->serial,
                'reason' => $request->reason
            ]);

            // Revoke certificate using service
            $revocationResult = $this->revocationService->revokeCertificate(
                $coa,
                $request->reason,
                $request->notes,
                $request->revoked_by ?? $user->name
            );

            $response = [
                'success' => true,
                'message' => 'Certificate revoked successfully',
                'data' => [
                    'certificate' => $coa,
                    'revocation' => $revocationResult,
                    'revoked_at' => now()->toIso8601String()
                ]
            ];

            $this->logger->info('[CoA Controller] Certificate revoked successfully', [
                'user_id' => $user->id,
                'coa_id' => $coa->id,
                'serial' => $coa->serial,
                'reason' => $request->reason
            ]);

            return response()->json($response, 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_REVOKE_ERROR', [
                'user_id' => Auth::id(),
                'coa_id' => $coa->id,
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
                'ip_address' => $request->ip(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            return response()->json([
                'success' => false,
                'message' => 'Failed to revoke certificate',
                'error' => 'An error occurred while revoking the certificate'
            ], 500);
        }
    }

    /**
     * Create a certificate bundle
     *
     * @param Request $request
     * @param Coa $coa
     * @return JsonResponse
     * @privacy-safe Creates bundle only for authenticated user's CoA
     */
    public function createBundle(Request $request, Coa $coa): JsonResponse {
        try {
            $user = Auth::user();

            // Security check - user must own the EGI
            if ($coa->egi->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Certificate not found or access denied'
                ], 404);
            }

            // Validate request
            $validator = Validator::make($request->all(), [
                'bundle_type' => 'required|string|in:COMPLETE,BASIC,EXTENDED,LEGAL,INSURANCE,EXHIBITION,TRANSFER',
                'formats' => 'required|array|min:1',
                'formats.*' => 'string|in:PDF_PACKAGE,JSON_DATA,ZIP_ARCHIVE,DIGITAL_SIGNATURE,BLOCKCHAIN_RECORD',
                'options' => 'nullable|array',
                'options.selected_annexes' => 'nullable|array',
                'options.include_history' => 'nullable|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $this->logger->info('[CoA Controller] Creating certificate bundle', [
                'user_id' => $user->id,
                'coa_id' => $coa->id,
                'bundle_type' => $request->bundle_type,
                'formats' => $request->formats
            ]);

            // Create bundle using service
            $bundleResult = $this->bundleService->createBundle(
                $coa,
                $request->bundle_type,
                $request->formats,
                $request->options ?? []
            );

            $response = [
                'success' => true,
                'message' => 'Certificate bundle created successfully',
                'data' => $bundleResult
            ];

            $this->logger->info('[CoA Controller] Certificate bundle created successfully', [
                'user_id' => $user->id,
                'coa_id' => $coa->id,
                'bundle_id' => $bundleResult['bundle_id'],
                'total_size' => $bundleResult['total_size']
            ]);

            return response()->json($response, 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_BUNDLE_ERROR', [
                'user_id' => Auth::id(),
                'coa_id' => $coa->id,
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
                'ip_address' => $request->ip(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create certificate bundle',
                'error' => 'An error occurred while creating the bundle'
            ], 500);
        }
    }

    /**
     * Get certificate statistics
     *
     * @param Request $request
     * @return JsonResponse
     * @privacy-safe Returns statistics only for authenticated user's certificates
     */
    public function statistics(Request $request): JsonResponse {
        try {
            $user = Auth::user();

            $this->logger->info('[CoA Controller] Retrieving certificate statistics', [
                'user_id' => $user->id
            ]);

            // Get user's CoA statistics
            $stats = [
                'total_certificates' => Coa::whereHas('egi', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })->count(),

                'by_status' => Coa::whereHas('egi', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })->groupBy('status')->selectRaw('status, count(*) as count')->pluck('count', 'status'),

                'recent_activity' => Coa::whereHas('egi', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })->with(['egi' => function ($query) {
                    $query->select('id', 'name');
                }])->orderBy('created_at', 'desc')->limit(5)->get(),

                'monthly_issuance' => Coa::whereHas('egi', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })->where('created_at', '>=', now()->subMonths(12))
                    ->groupBy(\DB::raw('YEAR(created_at), MONTH(created_at)'))
                    ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, count(*) as count')
                    ->orderBy('year', 'desc')
                    ->orderBy('month', 'desc')
                    ->get()
            ];

            $response = [
                'success' => true,
                'message' => 'Certificate statistics retrieved successfully',
                'data' => [
                    'statistics' => $stats,
                    'generated_at' => now()->toIso8601String()
                ]
            ];

            return response()->json($response, 200);
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_STATISTICS_ERROR', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'ip_address' => $request->ip(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve statistics',
                'error' => 'An error occurred while fetching statistics'
            ], 500);
        }
    }
}
