<?php

namespace App\Http\Controllers;

use App\Models\Coa;
use App\Models\CoaAnnex;
use App\Services\Coa\AnnexService;
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
 * @Oracode Controller: CoA Annex Management
 * 🎯 Purpose: Handle CoA Pro annex operations (A_PROVENANCE, B_CONDITION, C_EXHIBITIONS, D_PHOTOS)
 * 🛡️ Privacy: GDPR-compliant annex management with full audit trail
 * 🧱 Core Logic: Coordinates AnnexService for professional documentation
 *
 * @package App\Http\Controllers
 * @author AI Assistant following FlorenceEGI patterns
 * @version 1.0.0 (CoA Pro System)
 * @date 2025-09-18
 * @purpose Professional annex management following FlorenceEGI architecture
 */
class AnnexController extends Controller {
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
     * Annex management service
     * @var AnnexService
     */
    protected AnnexService $annexService;

    /**
     * Constructor with dependency injection
     *
     * @param UltraLogManager $logger
     * @param ErrorManagerInterface $errorManager
     * @param AuditLogService $auditService
     * @param AnnexService $annexService
     * @privacy-safe All injected services handle GDPR compliance
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService,
        AnnexService $annexService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditService = $auditService;
        $this->annexService = $annexService;

        // Apply auth middleware
        $this->middleware('auth');
    }

    /**
     * Get all annexes for a CoA certificate
     *
     * @param Request $request
     * @param Coa $coa
     * @return JsonResponse
     * @privacy-safe Returns annexes only for authenticated user's CoA
     *
     * @oracode-dimension governance
     * @value-flow Provides annex overview for certificate management
     * @community-impact Enables professional documentation access
     * @transparency-level High - complete annex listing
     * @narrative-coherence Links annexes to certificate documentation
     */
    public function index(Request $request, Coa $coa): JsonResponse {
        try {
            $user = Auth::user();

            // Security check - user must own the CoA's EGI
            if ($coa->egi->user_id !== $user->id) {
                $this->logger->warning('[Annex Controller] Unauthorized annexes access attempt', [
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

            $this->logger->info('[Annex Controller] Retrieving CoA annexes', [
                'user_id' => $user->id,
                'coa_id' => $coa->id,
                'serial' => $coa->serial,
                'active_only' => $request->boolean('active_only', true)
            ]);

            // Get annexes using service
            $annexes = $this->annexService->getCoaAnnexes($coa, $request->boolean('active_only', true));

            $response = [
                'success' => true,
                'message' => 'Annexes retrieved successfully',
                'data' => $annexes
            ];

            // Log audit trail
            $this->auditService->logUserAction($user, 'coa_annexes_viewed', [
                'coa_id' => $coa->id,
                'serial' => $coa->serial,
                'total_annexes' => $annexes['total_annexes'] ?? 0,
                'active_only' => $request->boolean('active_only', true)
            ], GdprActivityCategory::GDPR_ACTIONS);

            return response()->json($response, 200);
        } catch (\Exception $e) {
            $this->errorManager->handle('ANNEX_INDEX_ERROR', [
                'user_id' => Auth::id(),
                'coa_id' => $coa->id,
                'error' => $e->getMessage(),
                'ip_address' => $request->ip(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve annexes',
                'error' => 'An error occurred while fetching annexes'
            ], 500);
        }
    }

    /**
     * Get specific annex by type and version
     *
     * @param Request $request
     * @param Coa $coa
     * @param string $type
     * @return JsonResponse
     * @privacy-safe Returns annex only for authenticated user's CoA
     */
    public function show(Request $request, Coa $coa, string $type): JsonResponse {
        try {
            $user = Auth::user();

            // Security check
            if ($coa->egi->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Certificate not found or access denied'
                ], 404);
            }

            // Validate annex type
            if (!array_key_exists($type, AnnexService::ANNEX_TYPES)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid annex type'
                ], 400);
            }

            $version = $request->get('version'); // null for latest

            $this->logger->info('[Annex Controller] Retrieving specific annex', [
                'user_id' => $user->id,
                'coa_id' => $coa->id,
                'type' => $type,
                'version' => $version ?? 'latest'
            ]);

            // Get annex using service
            $annex = $this->annexService->getAnnex($coa, $type, $version);

            if (!$annex) {
                return response()->json([
                    'success' => false,
                    'message' => 'Annex not found',
                    'type' => $type,
                    'version' => $version
                ], 404);
            }

            $response = [
                'success' => true,
                'message' => 'Annex retrieved successfully',
                'data' => [
                    'annex' => $annex,
                    'type_description' => AnnexService::ANNEX_TYPES[$type],
                    'integrity_check' => $this->annexService->verifyAnnexIntegrity($annex)
                ]
            ];

            return response()->json($response, 200);
        } catch (\Exception $e) {
            $this->errorManager->handle('ANNEX_SHOW_ERROR', [
                'user_id' => Auth::id(),
                'coa_id' => $coa->id,
                'type' => $type,
                'version' => $request->get('version'),
                'error' => $e->getMessage(),
                'ip_address' => $request->ip(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve annex',
                'error' => 'An error occurred while fetching the annex'
            ], 500);
        }
    }

    /**
     * Create a new annex for a CoA
     *
     * @param Request $request
     * @param Coa $coa
     * @return JsonResponse
     * @privacy-safe Creates annex only for authenticated user's CoA
     */
    public function store(Request $request, Coa $coa): JsonResponse {
        try {
            $user = Auth::user();

            // Security check
            if ($coa->egi->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Certificate not found or access denied'
                ], 404);
            }

            // Validate request
            $validator = Validator::make($request->all(), [
                'type' => 'required|string|in:A_PROVENANCE,B_CONDITION,C_EXHIBITIONS,D_PHOTOS',
                'data' => 'required|array',
                'issued_by' => 'nullable|string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Additional validation based on annex type
            $typeValidation = $this->validateAnnexTypeData($request->type, $request->data);
            if (!$typeValidation['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid data for annex type',
                    'errors' => $typeValidation['errors']
                ], 422);
            }

            $this->logger->info('[Annex Controller] Creating annex', [
                'user_id' => $user->id,
                'coa_id' => $coa->id,
                'type' => $request->type,
                'issued_by' => $request->issued_by,
                'data_size' => count($request->data)
            ]);

            // Create annex using service
            $annex = $this->annexService->createAnnex(
                $coa,
                $request->type,
                $request->data,
                $request->issued_by
            );

            $response = [
                'success' => true,
                'message' => 'Annex created successfully',
                'data' => [
                    'annex' => $annex,
                    'type_description' => AnnexService::ANNEX_TYPES[$request->type],
                    'created_at' => now()->toIso8601String()
                ]
            ];

            $this->logger->info('[Annex Controller] Annex created successfully', [
                'user_id' => $user->id,
                'coa_id' => $coa->id,
                'annex_id' => $annex->id,
                'type' => $request->type,
                'version' => $annex->version
            ]);

            return response()->json($response, 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            $this->errorManager->handle('ANNEX_CREATE_ERROR', [
                'user_id' => Auth::id(),
                'coa_id' => $coa->id,
                'type' => $request->type,
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
                'ip_address' => $request->ip(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create annex',
                'error' => 'An error occurred while creating the annex'
            ], 500);
        }
    }

    /**
     * Update an existing annex (creates new version)
     *
     * @param Request $request
     * @param Coa $coa
     * @param CoaAnnex $annex
     * @return JsonResponse
     * @privacy-safe Updates annex only for authenticated user's CoA
     */
    public function update(Request $request, Coa $coa, CoaAnnex $annex): JsonResponse {
        try {
            $user = Auth::user();

            // Security checks
            if ($coa->egi->user_id !== $user->id || $annex->coa_id !== $coa->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Annex not found or access denied'
                ], 404);
            }

            // Validate request
            $validator = Validator::make($request->all(), [
                'data' => 'required|array',
                'issued_by' => 'nullable|string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Additional validation based on annex type
            $typeValidation = $this->validateAnnexTypeData($annex->type, $request->data);
            if (!$typeValidation['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid data for annex type',
                    'errors' => $typeValidation['errors']
                ], 422);
            }

            $this->logger->info('[Annex Controller] Updating annex', [
                'user_id' => $user->id,
                'coa_id' => $coa->id,
                'annex_id' => $annex->id,
                'type' => $annex->type,
                'current_version' => $annex->version
            ]);

            // Update annex using service (creates new version)
            $newAnnex = $this->annexService->updateAnnex(
                $annex,
                $request->data,
                $request->issued_by
            );

            $response = [
                'success' => true,
                'message' => 'Annex updated successfully',
                'data' => [
                    'new_annex' => $newAnnex,
                    'previous_annex' => $annex,
                    'type_description' => AnnexService::ANNEX_TYPES[$annex->type],
                    'updated_at' => now()->toIso8601String()
                ]
            ];

            $this->logger->info('[Annex Controller] Annex updated successfully', [
                'user_id' => $user->id,
                'coa_id' => $coa->id,
                'old_annex_id' => $annex->id,
                'new_annex_id' => $newAnnex->id,
                'old_version' => $annex->version,
                'new_version' => $newAnnex->version
            ]);

            return response()->json($response, 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            $this->errorManager->handle('ANNEX_UPDATE_ERROR', [
                'user_id' => Auth::id(),
                'coa_id' => $coa->id,
                'annex_id' => $annex->id,
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
                'ip_address' => $request->ip(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update annex',
                'error' => 'An error occurred while updating the annex'
            ], 500);
        }
    }

    /**
     * Get version history for an annex type
     *
     * @param Request $request
     * @param Coa $coa
     * @param string $type
     * @return JsonResponse
     * @privacy-safe Returns history only for authenticated user's CoA
     */
    public function history(Request $request, Coa $coa, string $type): JsonResponse {
        try {
            $user = Auth::user();

            // Security check
            if ($coa->egi->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Certificate not found or access denied'
                ], 404);
            }

            // Validate annex type
            if (!array_key_exists($type, AnnexService::ANNEX_TYPES)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid annex type'
                ], 400);
            }

            $this->logger->info('[Annex Controller] Retrieving annex history', [
                'user_id' => $user->id,
                'coa_id' => $coa->id,
                'type' => $type
            ]);

            // Get history using service
            $history = $this->annexService->getAnnexVersionHistory($coa, $type);

            $response = [
                'success' => true,
                'message' => 'Annex history retrieved successfully',
                'data' => $history
            ];

            return response()->json($response, 200);
        } catch (\Exception $e) {
            $this->errorManager->handle('ANNEX_HISTORY_ERROR', [
                'user_id' => Auth::id(),
                'coa_id' => $coa->id,
                'type' => $type,
                'error' => $e->getMessage(),
                'ip_address' => $request->ip(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve history',
                'error' => 'An error occurred while fetching history'
            ], 500);
        }
    }

    /**
     * Verify annex integrity
     *
     * @param Request $request
     * @param Coa $coa
     * @param CoaAnnex $annex
     * @return JsonResponse
     * @privacy-safe Verifies integrity for authenticated user's annex
     */
    public function verifyIntegrity(Request $request, Coa $coa, CoaAnnex $annex): JsonResponse {
        try {
            $user = Auth::user();

            // Security checks
            if ($coa->egi->user_id !== $user->id || $annex->coa_id !== $coa->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Annex not found or access denied'
                ], 404);
            }

            $this->logger->info('[Annex Controller] Verifying annex integrity', [
                'user_id' => $user->id,
                'coa_id' => $coa->id,
                'annex_id' => $annex->id,
                'type' => $annex->type,
                'version' => $annex->version
            ]);

            // Verify integrity using service
            $verification = $this->annexService->verifyAnnexIntegrity($annex);

            $response = [
                'success' => true,
                'message' => 'Integrity verification completed',
                'data' => $verification
            ];

            $this->logger->info('[Annex Controller] Integrity verification completed', [
                'user_id' => $user->id,
                'annex_id' => $annex->id,
                'is_valid' => $verification['is_valid']
            ]);

            return response()->json($response, 200);
        } catch (\Exception $e) {
            $this->errorManager->handle('ANNEX_INTEGRITY_ERROR', [
                'user_id' => Auth::id(),
                'coa_id' => $coa->id,
                'annex_id' => $annex->id,
                'error' => $e->getMessage(),
                'ip_address' => $request->ip(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            return response()->json([
                'success' => false,
                'message' => 'Integrity verification failed',
                'error' => 'An error occurred during verification'
            ], 500);
        }
    }

    /**
     * Get available annex types
     *
     * @param Request $request
     * @return JsonResponse
     * @privacy-safe Returns static configuration data
     */
    public function types(Request $request): JsonResponse {
        try {
            $types = $this->annexService->getAvailableTypes();

            $response = [
                'success' => true,
                'message' => 'Annex types retrieved successfully',
                'data' => [
                    'types' => $types,
                    'total_types' => count($types)
                ]
            ];

            return response()->json($response, 200);
        } catch (\Exception $e) {
            $this->errorManager->handle('ANNEX_TYPES_ERROR', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve types',
                'error' => 'An error occurred while fetching types'
            ], 500);
        }
    }

    /**
     * Validate annex type-specific data
     *
     * @param string $type
     * @param array $data
     * @return array Validation result
     * @privacy-safe Internal validation method
     */
    protected function validateAnnexTypeData(string $type, array $data): array {
        $errors = [];

        switch ($type) {
            case 'A_PROVENANCE':
                if (empty($data['provenance_history'])) {
                    $errors[] = 'Provenance history is required';
                }
                if (empty($data['ownership_chain'])) {
                    $errors[] = 'Ownership chain is required';
                }
                break;

            case 'B_CONDITION':
                if (empty($data['condition_assessment'])) {
                    $errors[] = 'Condition assessment is required';
                }
                if (empty($data['assessment_date'])) {
                    $errors[] = 'Assessment date is required';
                }
                break;

            case 'C_EXHIBITIONS':
                if (empty($data['exhibition_history'])) {
                    $errors[] = 'Exhibition history is required';
                }
                break;

            case 'D_PHOTOS':
                if (empty($data['photo_documentation'])) {
                    $errors[] = 'Photo documentation is required';
                }
                if (empty($data['photo_metadata'])) {
                    $errors[] = 'Photo metadata is required';
                }
                break;
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
}
