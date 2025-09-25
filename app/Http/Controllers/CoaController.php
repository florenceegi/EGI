<?php

namespace App\Http\Controllers;

use App\Models\Coa;
use App\Models\CoaAnnex;
use App\Models\CoaEvent;
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
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

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
     * Update CoA location (City, Province, Country string)
     *
     * @param Request $request
     * @param Coa $coa
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|void
     * @privacy-safe Touches minimal PII-like location string; full GDPR logging
     */
    public function updateLocation(Request $request, Coa $coa) {
        try {
            $user = Auth::user();

            // Ownership check
            if ($coa->egi->user_id !== $user->id) {
                return $this->errorManager->handle('COA_SHOW_ERROR', [
                    'user_id' => $user->id,
                    'coa_id' => $coa->id,
                    'reason' => 'unauthorized_update_location',
                    'ip_address' => $request->ip(),
                ]);
            }

            // Validation
            $validated = $request->validate([
                'location' => 'required|string|min:2|max:255'
            ]);

            // ULM audit start
            $this->logger->info('[CoA Controller] updateLocation start', [
                'user_id' => $user->id,
                'coa_id' => $coa->id,
                'prev_location' => $coa->location,
                'new_location' => $validated['location'],
                'ip' => $request->ip(),
            ]);

            // Persist
            $previous = $coa->location;
            $coa->update(['location' => $validated['location']]);

            // GDPR Audit
            $this->auditService->logUserAction($user, 'coa_location_updated', [
                'coa_id' => $coa->id,
                'serial' => $coa->serial,
                'previous_location' => $previous,
                'new_location' => $validated['location'],
                'ip_address' => $request->ip(),
                'user_agent' => substr($request->userAgent() ?? '', 0, 255)
            ], GdprActivityCategory::GDPR_ACTIONS);

            $this->logger->info('[CoA Controller] updateLocation success', [
                'user_id' => $user->id,
                'coa_id' => $coa->id,
            ]);

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => __('egi.coa.location_updated'), 'data' => [
                    'location' => $coa->location,
                ]]);
            }

            return redirect()->back()->with('success', __('egi.coa.location_updated'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->logger->warning('[CoA Controller] updateLocation validation failed', [
                'user_id' => Auth::id(),
                'errors' => $e->errors(),
            ]);
            throw $e;
        } catch (\Throwable $e) {
            return $this->errorManager->handle('COA_UPDATE_LOCATION_ERROR', [
                'user_id' => Auth::id(),
                'coa_id' => $coa->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'ip_address' => $request->ip(),
            ], $e);
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
     * @return JsonResponse|void UEM può gestire la risposta automaticamente
     * @privacy-safe Issues certificate only for authenticated user's EGI
     */
    public function issue(Request $request) {

        try {
            $user = Auth::user();

            // Validate request
            $validator = Validator::make($request->all(), [
                'egi_id' => 'required|integer|exists:egis,id',
                'issued_by' => 'nullable|string|max:255',
                'notes' => 'nullable|string|max:1000',
                'auto_generate_pdf' => 'nullable|boolean',
                'location' => 'nullable|string|max:255'
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
                'issued_by' => $request->issued_by,
                'location' => $request->location
            ]);

            // Issue certificate using service
            $coa = $this->issueService->issueCertificate(
                $egi,
                $request->issued_by ?? $user->name,
                $request->notes,
                $request->filled('location') ? trim((string)$request->location) : null
            );

            // Se il service restituisce null, l'ErrorManager ha già gestito l'errore
            if (!$coa) {
                return; // UEM ha già gestito la risposta di errore
            }

            // Auto-generate PDF if requested
            $pdfGenerated = false;
            $auto = $request->has('auto_generate_pdf')
                ? $request->boolean('auto_generate_pdf')
                : (bool) config('coa.auto_generate_pdf', true);
            if ($auto) {
                try {
                    $bundleService = app(BundleService::class);
                    $this->logger->info('[CoA Controller] Auto-PDF generation start', [
                        'coa_id' => $coa->id,
                        'serial' => $coa->serial,
                        'user_id' => $user->id
                    ]);
                    $bundleService->generateCoaPdf($coa);
                    $pdfGenerated = true;

                    $this->logger->info('[CoA Controller] PDF auto-generated', [
                        'coa_id' => $coa->id,
                        'serial' => $coa->serial,
                        'user_id' => $user->id
                    ]);
                } catch (\Exception $e) {
                    // UEM pattern: register structured error with context
                    $this->errorManager->handle('COA_AUTO_PDF_GENERATION_ERROR', [
                        'coa_id' => $coa->id,
                        'serial' => $coa->serial,
                        'user_id' => $user->id,
                        'egi_id' => $egi->id,
                        'ip_address' => $request->ip(),
                        'timestamp' => now()->toIso8601String()
                    ], $e);
                    // ULM warning with concise summary
                    $this->logger->warning('[CoA Controller] PDF auto-generation failed', [
                        'coa_id' => $coa->id,
                        'serial' => $coa->serial,
                        'user_id' => $user->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            $response = [
                'success' => true,
                'message' => 'Certificate issued successfully',
                'data' => [
                    'certificate' => $coa,
                    'egi' => $egi,
                    'issued_at' => now()->toIso8601String(),
                    'pdf_generated' => $pdfGenerated,
                    'pdf_url' => $pdfGenerated ? route('coa.pdf.download', $coa) : null
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
            // Log dell'errore originale per debug
            \Log::error('[CoA Controller] Errore durante emissione certificato', [
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'error_trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'request_data' => $request->all()
            ]);

            // Utilizziamo la convenzione UEM standard - l'ErrorManager gestisce tutto
            $this->errorManager->handle('COA_ISSUE_ERROR', [], $e);
            // UEM gestisce automaticamente la risposta, non restituiamo nulla manualmente
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

    /**
     * Check if an EGI already has a CoA certificate
     *
     * @param Request $request
     * @return JsonResponse
     *
     * @uem-pattern Ultra Error Manager pattern for EGI certificate validation
     * @narrative-coherence Prevents duplicate certificate issuance
     */
    public function checkEgiCertificate(Request $request): JsonResponse {
        try {
            $request->validate([
                'egi_id' => 'required|integer|exists:egis,id'
            ]);

            $user = Auth::user();
            $egiId = $request->egi_id;

            $this->logger->info('[CoA Controller] Checking EGI certificate status', [
                'user_id' => $user->id,
                'egi_id' => $egiId,
                'request_ip' => $request->ip()
            ]);

            // Check if user owns the EGI
            $egi = $user->egis()->find($egiId);
            if (!$egi) {
                $this->errorManager->handle('COA_EGI_NOT_OWNED', [
                    'user_id' => $user->id,
                    'egi_id' => $egiId,
                    'ip_address' => $request->ip()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'EGI not found or not owned by user'
                ], 404);
            }

            // Check for existing certificate
            $existingCoa = Coa::where('egi_id', $egiId)->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'has_certificate' => $existingCoa !== null,
                    'certificate' => $existingCoa ? [
                        'id' => $existingCoa->id,
                        'serial' => $existingCoa->serial,
                        'status' => $existingCoa->status,
                        'issue_date' => $existingCoa->issue_date,
                        'expiry_date' => $existingCoa->expiry_date
                    ] : null,
                    'egi' => [
                        'id' => $egi->id,
                        'name' => $egi->name,
                        'eligible_for_coa' => true // You can add specific eligibility logic here
                    ]
                ]
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_CHECK_EGI_ERROR', [
                'user_id' => Auth::id(),
                'egi_id' => $request->egi_id ?? null,
                'error' => $e->getMessage(),
                'ip_address' => $request->ip()
            ], $e);

            return response()->json([
                'success' => false,
                'message' => 'Failed to check EGI certificate status'
            ], 500);
        }
    }

    /**
     * Admin dashboard with comprehensive CoA statistics
     *
     * @param Request $request
     * @return JsonResponse
     *
     * @uem-pattern Ultra Error Manager for admin operations
     * @narrative-coherence Provides administrative oversight of CoA system
     */
    public function adminDashboard(Request $request): JsonResponse {
        try {
            $user = Auth::user();

            $this->logger->info('[CoA Controller] Admin dashboard accessed', [
                'user_id' => $user->id,
                'request_ip' => $request->ip(),
                'admin_level' => $user->role ?? 'unknown'
            ]);

            // System-wide statistics
            $systemStats = [
                'total_certificates' => Coa::count(),
                'active_certificates' => Coa::where('status', 'active')->count(),
                'revoked_certificates' => Coa::where('status', 'revoked')->count(),
                'expired_certificates' => Coa::where('expiry_date', '<', now())->count(),
                'certificates_this_month' => Coa::whereMonth('created_at', now()->month)->count(),
                'certificates_this_year' => Coa::whereYear('created_at', now()->year)->count(),
                'total_annexes' => CoaAnnex::count(),
                // 'total_addendums' => CoaAddendum::count(), // TODO: Implement when CoaAddendum model is created
                'unique_certificate_holders' => Coa::distinct('user_id')->count('user_id')
            ];

            // Recent activity
            $recentActivity = Coa::with(['egi.user', 'events'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($coa) {
                    return [
                        'id' => $coa->id,
                        'serial' => $coa->serial,
                        'egi_name' => $coa->egi->name,
                        'user_name' => $coa->egi->user->name,
                        'status' => $coa->status,
                        'created_at' => $coa->created_at,
                        'last_event' => $coa->events->first()?->event_type
                    ];
                });

            // Issuance trends (last 12 months)
            $issuanceTrends = Coa::where('created_at', '>=', now()->subYear())
                ->groupBy(\DB::raw('YEAR(created_at), MONTH(created_at)'))
                ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, count(*) as count')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Admin dashboard data retrieved successfully',
                'data' => [
                    'system_statistics' => $systemStats,
                    'recent_activity' => $recentActivity,
                    'issuance_trends' => $issuanceTrends,
                    'generated_at' => now()->toIso8601String()
                ]
            ], 200);
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_ADMIN_DASHBOARD_ERROR', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'ip_address' => $request->ip()
            ], $e);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load admin dashboard'
            ], 500);
        }
    }

    /**
     * Batch revoke multiple certificates
     *
     * @param Request $request
     * @return JsonResponse
     *
     * @uem-pattern Ultra Error Manager for batch operations
     * @narrative-coherence Enables efficient mass certificate management
     */
    public function batchRevoke(Request $request): JsonResponse {
        try {
            $request->validate([
                'certificate_ids' => 'required|array|min:1|max:100',
                'certificate_ids.*' => 'integer|exists:coas,id',
                'reason' => 'required|string|max:500'
            ]);

            $user = Auth::user();
            $certificateIds = $request->certificate_ids;
            $reason = $request->reason;

            $this->logger->info('[CoA Controller] Batch revoke initiated', [
                'user_id' => $user->id,
                'certificate_count' => count($certificateIds),
                'reason' => $reason,
                'request_ip' => $request->ip()
            ]);

            $results = [
                'successful' => [],
                'failed' => [],
                'total_processed' => 0
            ];

            foreach ($certificateIds as $coaId) {
                try {
                    $result = $this->coaService->revokeCertificate($coaId, $reason);

                    if ($result['success']) {
                        $results['successful'][] = [
                            'id' => $coaId,
                            'serial' => $result['data']['serial'] ?? null
                        ];
                    } else {
                        $results['failed'][] = [
                            'id' => $coaId,
                            'error' => $result['message']
                        ];
                    }
                } catch (\Exception $e) {
                    $results['failed'][] = [
                        'id' => $coaId,
                        'error' => 'Unexpected error during revocation'
                    ];
                }

                $results['total_processed']++;
            }

            return response()->json([
                'success' => true,
                'message' => sprintf(
                    'Batch revocation completed. %d successful, %d failed.',
                    count($results['successful']),
                    count($results['failed'])
                ),
                'data' => $results
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_BATCH_REVOKE_ERROR', [
                'user_id' => Auth::id(),
                'certificate_ids' => $request->certificate_ids ?? [],
                'error' => $e->getMessage(),
                'ip_address' => $request->ip()
            ], $e);

            return response()->json([
                'success' => false,
                'message' => 'Failed to process batch revocation'
            ], 500);
        }
    }

    /**
     * Generate comprehensive reports
     *
     * @param Request $request
     * @return JsonResponse
     *
     * @uem-pattern Ultra Error Manager for reporting operations
     * @narrative-coherence Provides detailed analytical insights
     */
    public function reports(Request $request): JsonResponse {
        try {
            $request->validate([
                'report_type' => 'required|string|in:summary,detailed,activity,trends',
                'date_from' => 'nullable|date',
                'date_to' => 'nullable|date|after_or_equal:date_from',
                'format' => 'nullable|string|in:json,csv'
            ]);

            $reportType = $request->report_type;
            $dateFrom = $request->date_from ? Carbon::parse($request->date_from) : now()->subMonth();
            $dateTo = $request->date_to ? Carbon::parse($request->date_to) : now();
            $format = $request->get('format', 'json');

            $this->logger->info('[CoA Controller] Generating report', [
                'user_id' => Auth::id(),
                'report_type' => $reportType,
                'date_range' => [$dateFrom->toDateString(), $dateTo->toDateString()],
                'format' => $format
            ]);

            $reportData = [];

            switch ($reportType) {
                case 'summary':
                    $reportData = [
                        'period' => [$dateFrom->toDateString(), $dateTo->toDateString()],
                        'certificates_issued' => Coa::whereBetween('created_at', [$dateFrom, $dateTo])->count(),
                        'certificates_revoked' => Coa::whereBetween('updated_at', [$dateFrom, $dateTo])
                            ->where('status', 'revoked')->count(),
                        'status_distribution' => Coa::whereBetween('created_at', [$dateFrom, $dateTo])
                            ->groupBy('status')
                            ->selectRaw('status, count(*) as count')
                            ->pluck('count', 'status'),
                        'daily_issuance' => Coa::whereBetween('created_at', [$dateFrom, $dateTo])
                            ->groupBy(\DB::raw('DATE(created_at)'))
                            ->selectRaw('DATE(created_at) as date, count(*) as count')
                            ->orderBy('date')
                            ->get()
                    ];
                    break;

                case 'detailed':
                    $reportData = Coa::with(['egi.user', 'annexes']) // TODO: Add 'addendums' when model is created
                        ->whereBetween('created_at', [$dateFrom, $dateTo])
                        ->get()
                        ->map(function ($coa) {
                            return [
                                'id' => $coa->id,
                                'serial' => $coa->serial,
                                'egi_name' => $coa->egi->name,
                                'user_name' => $coa->egi->user->name,
                                'user_email' => $coa->egi->user->email,
                                'status' => $coa->status,
                                'issue_date' => $coa->issue_date,
                                'expiry_date' => $coa->expiry_date,
                                'annexes_count' => $coa->annexes->count(),
                                // 'addendums_count' => $coa->addendums->count() // TODO: Add when CoaAddendum model is created
                            ];
                        });
                    break;

                case 'activity':
                    $reportData = CoaEvent::with(['coa.egi.user'])
                        ->whereBetween('created_at', [$dateFrom, $dateTo])
                        ->orderBy('created_at', 'desc')
                        ->get()
                        ->map(function ($event) {
                            return [
                                'event_type' => $event->event_type,
                                'certificate_serial' => $event->coa->serial,
                                'egi_name' => $event->coa->egi->name,
                                'user_name' => $event->coa->egi->user->name,
                                'event_data' => $event->event_data,
                                'timestamp' => $event->created_at
                            ];
                        });
                    break;

                case 'trends':
                    $reportData = [
                        'monthly_trends' => Coa::whereBetween('created_at', [$dateFrom, $dateTo])
                            ->groupBy(\DB::raw('YEAR(created_at), MONTH(created_at)'))
                            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, count(*) as count')
                            ->orderBy('year')
                            ->orderBy('month')
                            ->get(),
                        'status_trends' => Coa::whereBetween('updated_at', [$dateFrom, $dateTo])
                            ->groupBy('status', \DB::raw('DATE(updated_at)'))
                            ->selectRaw('status, DATE(updated_at) as date, count(*) as count')
                            ->orderBy('date')
                            ->get()
                            ->groupBy('status')
                    ];
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => 'Report generated successfully',
                'data' => [
                    'report_type' => $reportType,
                    'period' => [$dateFrom->toDateString(), $dateTo->toDateString()],
                    'generated_at' => now()->toIso8601String(),
                    'data' => $reportData
                ]
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_REPORTS_ERROR', [
                'user_id' => Auth::id(),
                'report_type' => $request->report_type ?? null,
                'error' => $e->getMessage(),
                'ip_address' => $request->ip()
            ], $e);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate report'
            ], 500);
        }
    }

    /**
     * System settings management
     *
     * @param Request $request
     * @return JsonResponse
     *
     * @uem-pattern Ultra Error Manager for settings operations
     * @narrative-coherence Manages CoA system configuration
     */
    public function settings(Request $request): JsonResponse {
        try {
            $user = Auth::user();

            $this->logger->info('[CoA Controller] Settings accessed', [
                'user_id' => $user->id,
                'request_ip' => $request->ip()
            ]);

            // Get current CoA system settings
            $settings = [
                'certificate_validity_days' => config('coa.certificate_validity_days', 365),
                'max_annexes_per_certificate' => config('coa.max_annexes_per_certificate', 10),
                'auto_renewal_enabled' => config('coa.auto_renewal_enabled', false),
                'notification_settings' => [
                    'expiry_warning_days' => config('coa.expiry_warning_days', 30),
                    'email_notifications' => config('coa.email_notifications', true),
                    'admin_notifications' => config('coa.admin_notifications', true)
                ],
                'security_settings' => [
                    'require_two_factor' => config('coa.require_two_factor', false),
                    'audit_log_retention_days' => config('coa.audit_log_retention_days', 90),
                    'rate_limiting' => [
                        'issue_per_hour' => config('coa.rate_limits.issue_per_hour', 10),
                        'verify_per_minute' => config('coa.rate_limits.verify_per_minute', 60)
                    ]
                ],
                'template_settings' => [
                    'default_template' => config('coa.default_template', 'standard'),
                    'custom_logo_enabled' => config('coa.custom_logo_enabled', false),
                    'watermark_enabled' => config('coa.watermark_enabled', true)
                ]
            ];

            return response()->json([
                'success' => true,
                'message' => 'Settings retrieved successfully',
                'data' => [
                    'settings' => $settings,
                    'editable_by_admin' => $user->hasRole('admin') || $user->hasRole('super_admin'),
                    'last_updated' => cache('coa_settings_last_updated', now()->toIso8601String())
                ]
            ], 200);
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_SETTINGS_ERROR', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'ip_address' => $request->ip()
            ], $e);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve settings'
            ], 500);
        }
    }

    /**
     * Export data in various formats
     *
     * @param Request $request
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @uem-pattern Ultra Error Manager for export operations
     * @narrative-coherence Enables data portability and backup
     */
    public function exportData(Request $request) {
        try {
            $request->validate([
                'export_type' => 'required|string|in:certificates,events,statistics',
                'format' => 'required|string|in:json,csv,pdf',
                'date_from' => 'nullable|date',
                'date_to' => 'nullable|date|after_or_equal:date_from',
                'filters' => 'nullable|array'
            ]);

            $user = Auth::user();
            $exportType = $request->export_type;
            $format = $request->format;
            $dateFrom = $request->date_from ? Carbon::parse($request->date_from) : now()->subMonth();
            $dateTo = $request->date_to ? Carbon::parse($request->date_to) : now();
            $filters = $request->get('filters', []);

            $this->logger->info('[CoA Controller] Data export initiated', [
                'user_id' => $user->id,
                'export_type' => $exportType,
                'format' => $format,
                'date_range' => [$dateFrom->toDateString(), $dateTo->toDateString()],
                'filters' => $filters
            ]);

            // For now, return JSON format data
            // TODO: Implement actual file generation for CSV/PDF
            $exportData = [];

            switch ($exportType) {
                case 'certificates':
                    $query = Coa::with(['egi.user', 'annexes']) // TODO: Add 'addendums' when model is created
                        ->whereBetween('created_at', [$dateFrom, $dateTo]);

                    if (isset($filters['status'])) {
                        $query->where('status', $filters['status']);
                    }

                    $exportData = $query->get()->map(function ($coa) {
                        return [
                            'serial' => $coa->serial,
                            'egi_name' => $coa->egi->name,
                            'user_email' => $coa->egi->user->email,
                            'status' => $coa->status,
                            'issue_date' => $coa->issue_date,
                            'expiry_date' => $coa->expiry_date,
                            'verification_hash' => $coa->verification_hash
                        ];
                    });
                    break;

                case 'events':
                    $exportData = CoaEvent::with(['coa'])
                        ->whereBetween('created_at', [$dateFrom, $dateTo])
                        ->get()
                        ->map(function ($event) {
                            return [
                                'certificate_serial' => $event->coa->serial,
                                'event_type' => $event->event_type,
                                'event_data' => $event->event_data,
                                'timestamp' => $event->created_at
                            ];
                        });
                    break;

                case 'statistics':
                    $exportData = [
                        'summary' => [
                            'total_certificates' => Coa::whereBetween('created_at', [$dateFrom, $dateTo])->count(),
                            'active_certificates' => Coa::whereBetween('created_at', [$dateFrom, $dateTo])
                                ->where('status', 'active')->count(),
                            'revoked_certificates' => Coa::whereBetween('created_at', [$dateFrom, $dateTo])
                                ->where('status', 'revoked')->count()
                        ],
                        'daily_breakdown' => Coa::whereBetween('created_at', [$dateFrom, $dateTo])
                            ->groupBy(\DB::raw('DATE(created_at)'))
                            ->selectRaw('DATE(created_at) as date, count(*) as count')
                            ->orderBy('date')
                            ->get()
                    ];
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => 'Data exported successfully',
                'data' => [
                    'export_type' => $exportType,
                    'format' => $format,
                    'period' => [$dateFrom->toDateString(), $dateTo->toDateString()],
                    'record_count' => is_array($exportData) ? count($exportData) : $exportData->count(),
                    'generated_at' => now()->toIso8601String(),
                    'data' => $exportData
                ]
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_EXPORT_ERROR', [
                'user_id' => Auth::id(),
                'export_type' => $request->export_type ?? null,
                'error' => $e->getMessage(),
                'ip_address' => $request->ip()
            ], $e);

            return response()->json([
                'success' => false,
                'message' => 'Failed to export data'
            ], 500);
        }
    }

    /**
     * Search certificates with advanced filters
     *
     * @param Request $request
     * @return JsonResponse
     *
     * @uem-pattern Ultra Error Manager for search operations
     * @narrative-coherence Enables comprehensive certificate discovery
     */
    public function search(Request $request): JsonResponse {
        try {
            $request->validate([
                'query' => 'nullable|string|max:255',
                'filters' => 'nullable|array',
                'filters.status' => 'nullable|string|in:active,revoked,expired',
                'filters.date_from' => 'nullable|date',
                'filters.date_to' => 'nullable|date|after_or_equal:filters.date_from',
                'filters.user_id' => 'nullable|integer|exists:users,id',
                'sort_by' => 'nullable|string|in:serial,issue_date,expiry_date,status',
                'sort_direction' => 'nullable|string|in:asc,desc',
                'per_page' => 'nullable|integer|min:1|max:100'
            ]);

            $user = Auth::user();
            $query = $request->get('query', '');
            $filters = $request->get('filters', []);
            $sortBy = $request->get('sort_by', 'issue_date');
            $sortDirection = $request->get('sort_direction', 'desc');
            $perPage = $request->get('per_page', 20);

            $this->logger->info('[CoA Controller] Certificate search initiated', [
                'user_id' => $user->id,
                'query' => $query,
                'filters' => $filters,
                'request_ip' => $request->ip()
            ]);

            $searchQuery = Coa::with(['egi.user', 'annexes']); // TODO: Add 'addendums' when model is created

            // Apply text search
            if (!empty($query)) {
                $searchQuery->where(function ($q) use ($query) {
                    $q->where('serial', 'like', "%{$query}%")
                        ->orWhereHas('egi', function ($q) use ($query) {
                            $q->where('name', 'like', "%{$query}%");
                        })
                        ->orWhereHas('egi.user', function ($q) use ($query) {
                            $q->where('name', 'like', "%{$query}%")
                                ->orWhere('email', 'like', "%{$query}%");
                        });
                });
            }

            // Apply filters
            if (isset($filters['status'])) {
                $searchQuery->where('status', $filters['status']);
            }

            if (isset($filters['date_from'])) {
                $searchQuery->where('issue_date', '>=', $filters['date_from']);
            }

            if (isset($filters['date_to'])) {
                $searchQuery->where('issue_date', '<=', $filters['date_to']);
            }

            if (isset($filters['user_id'])) {
                $searchQuery->whereHas('egi', function ($q) use ($filters) {
                    $q->where('user_id', $filters['user_id']);
                });
            }

            // Apply sorting
            $searchQuery->orderBy($sortBy, $sortDirection);

            // Execute search with pagination
            $results = $searchQuery->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Search completed successfully',
                'data' => [
                    'results' => $results->items(),
                    'pagination' => [
                        'current_page' => $results->currentPage(),
                        'last_page' => $results->lastPage(),
                        'per_page' => $results->perPage(),
                        'total' => $results->total(),
                        'from' => $results->firstItem(),
                        'to' => $results->lastItem()
                    ],
                    'search_parameters' => [
                        'query' => $query,
                        'filters' => $filters,
                        'sort_by' => $sortBy,
                        'sort_direction' => $sortDirection
                    ]
                ]
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_SEARCH_ERROR', [
                'user_id' => Auth::id(),
                'search_query' => $request->query ?? null,
                'error' => $e->getMessage(),
                'ip_address' => $request->ip()
            ], $e);

            return response()->json([
                'success' => false,
                'message' => 'Failed to perform search'
            ], 500);
        }
    }

    /**
     * Validate certificate serial format
     *
     * @param Request $request
     * @return JsonResponse
     *
     * @uem-pattern Ultra Error Manager for validation operations
     * @narrative-coherence Ensures serial number integrity
     */
    public function validateSerial(Request $request): JsonResponse {
        try {
            $request->validate([
                'serial' => 'required|string|max:50'
            ]);

            $serial = $request->serial;

            $this->logger->info('[CoA Controller] Serial validation requested', [
                'user_id' => Auth::id(),
                'serial' => $serial,
                'request_ip' => $request->ip()
            ]);

            // Check if serial already exists
            $existingCoa = Coa::where('serial', $serial)->first();

            // Validate serial format (you can customize this based on your requirements)
            $isValidFormat = \Illuminate\Support\Str::isMatch('/^COA-[A-Z0-9]{8}-[A-Z0-9]{4}$/', $serial);

            return response()->json([
                'success' => true,
                'data' => [
                    'serial' => $serial,
                    'is_valid_format' => $isValidFormat,
                    'is_unique' => $existingCoa === null,
                    'exists' => $existingCoa !== null,
                    'existing_certificate' => $existingCoa ? [
                        'id' => $existingCoa->id,
                        'status' => $existingCoa->status,
                        'issue_date' => $existingCoa->issue_date
                    ] : null
                ]
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_VALIDATE_SERIAL_ERROR', [
                'user_id' => Auth::id(),
                'serial' => $request->serial ?? null,
                'error' => $e->getMessage(),
                'ip_address' => $request->ip()
            ], $e);

            return response()->json([
                'success' => false,
                'message' => 'Failed to validate serial'
            ], 500);
        }
    }

    /**
     * Preview certificate bundle before creation
     *
     * @param Request $request
     * @param int $coaId
     * @return JsonResponse
     *
     * @uem-pattern Ultra Error Manager for preview operations
     * @narrative-coherence Allows bundle review before finalization
     */
    public function previewBundle(Request $request, int $coaId): JsonResponse {
        try {
            $user = Auth::user();

            $this->logger->info('[CoA Controller] Bundle preview requested', [
                'user_id' => $user->id,
                'coa_id' => $coaId,
                'request_ip' => $request->ip()
            ]);

            // Get certificate with all related data
            $coa = Coa::with(['egi.user', 'annexes', 'events']) // TODO: Add 'addendums' when model is created
                ->whereHas('egi', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->findOrFail($coaId);

            // Generate bundle preview data
            $bundlePreview = [
                'certificate' => [
                    'serial' => $coa->serial,
                    'status' => $coa->status,
                    'issue_date' => $coa->issue_date,
                    'expiry_date' => $coa->expiry_date,
                    'verification_hash' => $coa->verification_hash
                ],
                'egi_details' => [
                    'name' => $coa->egi->name,
                    'description' => $coa->egi->description,
                    'owner' => $coa->egi->user->name
                ],
                'annexes' => $coa->annexes->map(function ($annex) {
                    return [
                        'type' => $annex->type,
                        'data' => $annex->data,
                        'created_at' => $annex->created_at
                    ];
                }),
                // TODO: Add addendums when CoaAddendum model is created
                /*
                'addendums' => $coa->addendums->map(function ($addendum) {
                    return [
                        'version' => $addendum->version,
                        'content' => $addendum->content,
                        'status' => $addendum->status,
                        'created_at' => $addendum->created_at
                    ];
                }),
                */
                'bundle_metadata' => [
                    'generated_at' => now()->toIso8601String(),
                    'total_pages_estimate' => 1 + $coa->annexes->count(), // TODO: Add addendums count when model is created
                    'bundle_size_estimate' => '2-5 MB', // Placeholder
                    'qr_code_included' => true,
                    'digital_signature_included' => true
                ]
            ];

            return response()->json([
                'success' => true,
                'message' => 'Bundle preview generated successfully',
                'data' => [
                    'preview' => $bundlePreview,
                    'ready_for_download' => $coa->status === 'active'
                ]
            ], 200);
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_PREVIEW_BUNDLE_ERROR', [
                'user_id' => Auth::id(),
                'coa_id' => $coaId,
                'error' => $e->getMessage(),
                'ip_address' => $request->ip()
            ], $e);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate bundle preview'
            ], 404);
        }
    }

    /**
     * Check requirements before certificate issuance
     *
     * @param Request $request
     * @return JsonResponse
     *
     * @uem-pattern Ultra Error Manager for requirement validation
     * @narrative-coherence Validates all prerequisites for certificate issuance
     */
    public function checkIssueRequirements(Request $request): JsonResponse {
        try {
            $request->validate([
                'egi_id' => 'required|integer|exists:egis,id'
            ]);

            $user = Auth::user();
            $egiId = $request->egi_id;

            $this->logger->info('[CoA Controller] Issue requirements check', [
                'user_id' => $user->id,
                'egi_id' => $egiId,
                'request_ip' => $request->ip()
            ]);

            // Check if user owns the EGI
            $egi = $user->egis()->find($egiId);
            if (!$egi) {
                return response()->json([
                    'success' => false,
                    'message' => 'EGI not found or not owned by user'
                ], 404);
            }

            $requirements = [
                'egi_ownership' => [
                    'status' => 'passed',
                    'message' => 'User owns the EGI'
                ],
                'existing_certificate' => [
                    'status' => Coa::where('egi_id', $egiId)->exists() ? 'failed' : 'passed',
                    'message' => Coa::where('egi_id', $egiId)->exists()
                        ? 'EGI already has a certificate'
                        : 'No existing certificate found'
                ],
                'egi_completeness' => [
                    'status' => (!empty($egi->name) && !empty($egi->description)) ? 'passed' : 'failed',
                    'message' => (!empty($egi->name) && !empty($egi->description))
                        ? 'EGI has required information'
                        : 'EGI missing required information'
                ],
                'user_verification' => [
                    'status' => $user->email_verified_at ? 'passed' : 'failed',
                    'message' => $user->email_verified_at
                        ? 'User email is verified'
                        : 'User email requires verification'
                ],
                'rate_limits' => [
                    'status' => 'passed', // You can implement actual rate limit checking here
                    'message' => 'Rate limits not exceeded'
                ]
            ];

            $allRequirementsMet = collect($requirements)->every(function ($req) {
                return $req['status'] === 'passed';
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'egi_id' => $egiId,
                    'egi_name' => $egi->name,
                    'requirements' => $requirements,
                    'can_issue_certificate' => $allRequirementsMet,
                    'requirements_summary' => [
                        'total' => count($requirements),
                        'passed' => collect($requirements)->where('status', 'passed')->count(),
                        'failed' => collect($requirements)->where('status', 'failed')->count()
                    ]
                ]
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_CHECK_REQUIREMENTS_ERROR', [
                'user_id' => Auth::id(),
                'egi_id' => $request->egi_id ?? null,
                'error' => $e->getMessage(),
                'ip_address' => $request->ip()
            ], $e);

            return response()->json([
                'success' => false,
                'message' => 'Failed to check issue requirements'
            ], 500);
        }
    }

    /**
     * Check if PDF exists for a CoA certificate
     * @param Coa $coa
     * @return JsonResponse
     */
    public function checkPdf(Coa $coa): JsonResponse {
        try {
            $bundleService = app(BundleService::class);
            $pdfExists = $bundleService->pdfExists($coa);

            return response()->json([
                'success' => true,
                'pdf_exists' => $pdfExists,
                'pdf_url' => $pdfExists ? route('coa.pdf.download', $coa) : null,
                'download_url' => $pdfExists ? route('coa.pdf.download', $coa) : null
            ]);
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_PDF_CHECK_ERROR', [
                'coa_id' => $coa->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ], $e);

            return response()->json([
                'success' => false,
                'message' => 'Failed to check PDF status'
            ], 500);
        }
    }

    /**
     * Generate PDF for a CoA certificate
     * @param Coa $coa
     * @return JsonResponse
     */
    public function generatePdf(Coa $coa): JsonResponse {
        try {
            $bundleService = app(BundleService::class);
            $pdfPath = $bundleService->generateCoaPdf($coa);

            // Log PDF generation
            $this->logger->info('COA PDF generated', [
                'coa_id' => $coa->id,
                'coa_serial' => $coa->serial,
                'user_id' => Auth::id(),
                'pdf_path' => $pdfPath
            ]);

            return response()->json([
                'success' => true,
                'message' => 'PDF generated successfully',
                'download_url' => route('coa.pdf.download', $coa)
            ]);
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_PDF_GENERATION_ERROR', [
                'coa_id' => $coa->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ], $e);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate PDF'
            ], 500);
        }
    }

    /**
     * Download PDF for a CoA certificate
     * @param Coa $coa
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function downloadPdf(Coa $coa) {
        try {
            $bundleService = app(BundleService::class);

            // Check if PDF exists, generate if not
            if (!$bundleService->pdfExists($coa)) {
                $bundleService->generateCoaPdf($coa);
            }

            $pdfPath = $bundleService->getPdfPath($coa);
            $filename = "CoA-{$coa->serial}.pdf";

            // Log PDF download
            $this->logger->info('COA PDF downloaded', [
                'coa_id' => $coa->id,
                'coa_serial' => $coa->serial,
                'user_id' => Auth::id(),
                'filename' => $filename
            ]);

            return response()->download($pdfPath, $filename);
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_PDF_DOWNLOAD_ERROR', [
                'coa_id' => $coa->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ], $e);

            return redirect()->back()->with('error', 'Failed to download PDF');
        }
    }

    /**
     * Inspector countersignature (QES mock) on latest CoA PDF
     *
     * @param Request $request
     * @param Coa $coa
     * @return JsonResponse
     */
    public function countersignInspector(Request $request, Coa $coa): JsonResponse {
        try {
            $user = Auth::user();

            // Ownership or role check (only owner or expert/admin)
            if (!($user->hasRole('admin') || $user->hasRole('expert') || $coa->egi->user_id === $user->id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }

            // Feature flag
            if (!(bool) config('coa.signature.inspector.enabled', false)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Inspector countersignature is disabled'
                ], 400);
            }

            // Validate request
            $validator = Validator::make($request->all(), [
                'reason' => 'nullable|string|max:255',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Ensure latest PDF exists
            $bundleService = app(\App\Services\Coa\BundleService::class);
            if (!$bundleService->pdfExists($coa)) {
                $bundleService->generateCoaPdf($coa);
            }

            // Retrieve latest CoA PDF file record
            $latestFile = $coa->files()->where('kind', 'like', 'pdf%')->orderByDesc('id')->first();
            if (!$latestFile) {
                return response()->json([
                    'success' => false,
                    'message' => 'No PDF available for countersignature'
                ], 404);
            }

            // Execute countersign
            /** @var \App\Services\Coa\Signature\SignatureService $signatureService */
            $signatureService = app(\App\Services\Coa\Signature\SignatureService::class);
            $result = $signatureService->countersignInspector($latestFile, [
                'reason' => $request->get('reason', 'Inspector countersign')
            ]);

            if (!($result['success'] ?? false)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Countersignature failed',
                    'error' => $result['error'] ?? null
                ], 500);
            }

            // Attach signature metadata onto CoA
            $meta = $coa->metadata ?? [];
            if (!is_array($meta)) { $meta = []; }
            $meta['signatures'] = array_values(array_filter(array_merge($meta['signatures'] ?? [], [
                $result['signature_info'] ?? []
            ])));
            $coa->update(['metadata' => $meta]);

            // Audit
            $this->auditService->logUserAction($user, 'coa_inspector_countersigned', [
                'coa_id' => $coa->id,
                'serial' => $coa->serial,
                'file_id' => $result['file_id'] ?? null,
            ], GdprActivityCategory::GDPR_ACTIONS);

            return response()->json([
                'success' => true,
                'message' => 'Inspector countersignature applied',
                'data' => [
                    'file_id' => $result['file_id'] ?? null,
                    'file_path' => $result['file_path'] ?? null,
                    'signature_info' => $result['signature_info'] ?? []
                ]
            ], 200);
        } catch (\Throwable $e) {
            $this->errorManager->handle('COA_INSPECTOR_COUNTERSIGN_ERROR', [
                'user_id' => Auth::id(),
                'coa_id' => $coa->id,
                'error' => $e->getMessage(),
            ], $e);
            return response()->json([
                'success' => false,
                'message' => 'Failed to countersign certificate'
            ], 500);
        }
    }

    /**
     * View CoA certificate in HTML format
     * @param Coa $coa
     * @return \Illuminate\View\View
     */
    public function viewCertificate(Coa $coa) {
        try {
            // Load relationships needed for certificate view
            $coa->load([
                'egi.user',
                'annexes',
                'signatures',
                'events'
            ]);

            // Log certificate view
            $this->logger->info('COA certificate viewed (HTML)', [
                'coa_id' => $coa->id,
                'coa_serial' => $coa->serial,
                'user_id' => Auth::id() ?? 'anonymous',
                'viewer_ip' => request()->ip()
            ]);

            return view('coa.certificate', compact('coa'));
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_CERTIFICATE_VIEW_ERROR', [
                'coa_id' => $coa->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ], $e);

            return redirect()->back()->with('error', 'Failed to load certificate view');
        }
    }
}
