<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Egi;
use App\Models\EgiCoaTrait;
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
 * @Oracode Controller: CoA EGI Traits Management
 * 🎯 Purpose: Manage CoA-specific traits for EGI artworks
 * 🛡️ Privacy: GDPR-compliant traits management with full audit trail
 * 🧱 Core Logic: Coordinates vocabulary selections with EGI metadata
 *
 * @package App\Http\Controllers
 * @author AI Assistant following FlorenceEGI patterns
 * @version 1.0.0 (CoA Traits System)
 * @date 2025-09-19
 * @purpose Professional traits management for CoA certificates
 */
class CoaEgiTraitsController extends Controller {
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

        // Apply authentication middleware
        $this->middleware('auth');
    }

    /**
     * Display the CoA traits for an EGI
     *
     * @param Request $request
     * @param Egi $egi
     * @return JsonResponse
     * @privacy-safe Returns user's own EGI traits only
     *
     * @oracode-dimension governance
     * @value-flow Provides current CoA traits configuration
     * @community-impact Enables traits viewing for artwork description
     * @transparency-level High - complete traits transparency for owner
     * @narrative-coherence Links traits to CoA certification system
     */
    public function show(Request $request, Egi $egi): JsonResponse {
        try {
            // Check ownership
            if (!$this->userOwnsEgi($egi)) {
                $this->auditService->log(
                    GdprActivityCategory::SECURITY_EVENT,
                    'unauthorized_coa_traits_access_attempt',
                    [
                        'egi_id' => $egi->id,
                        'user_id' => Auth::id(),
                        'ip_address' => $request->ip()
                    ]
                );

                return response()->json([
                    'success' => false,
                    'message' => 'Non autorizzato ad accedere a questi traits'
                ], 403);
            }

            $this->logger->info('[CoA EGI Traits] Retrieving traits', [
                'egi_id' => $egi->id,
                'user_id' => Auth::id(),
                'ip_address' => $request->ip()
            ]);

            $coaTraits = $egi->coaTraits;

            // Log audit trail
            $this->auditService->logUserAction(
                Auth::user(),
                'coa_traits_viewed',
                [
                    'egi_id' => $egi->id,
                    'has_traits' => !is_null($coaTraits),
                    'traits_summary' => $coaTraits ? [
                        'technique_count' => count($coaTraits->technique_slugs ?? []),
                        'materials_count' => count($coaTraits->materials_slugs ?? []),
                        'support_count' => count($coaTraits->support_slugs ?? []),
                        'custom_terms_count' => count($coaTraits->technique_free_text ?? []) +
                                              count($coaTraits->materials_free_text ?? []) +
                                              count($coaTraits->support_free_text ?? [])
                    ] : null
                ],
                GdprActivityCategory::DATA_ACCESS
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'egi_id' => $egi->id,
                    'traits' => $coaTraits ? [
                        'technique_slugs' => $coaTraits->technique_slugs ?? [],
                        'materials_slugs' => $coaTraits->materials_slugs ?? [],
                        'support_slugs' => $coaTraits->support_slugs ?? [],
                        'technique_free_text' => $coaTraits->technique_free_text ?? [],
                        'materials_free_text' => $coaTraits->materials_free_text ?? [],
                        'support_free_text' => $coaTraits->support_free_text ?? [],
                        'last_updated_at' => $coaTraits->last_updated_at?->toISOString(),
                        'updated_by_user_id' => $coaTraits->updated_by_user_id
                    ] : null
                ]
            ]);

        } catch (\Exception $e) {
            $this->errorManager->handle('COA_EGI_TRAITS_SHOW_ERROR', [
                'egi_id' => $egi->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'ip_address' => $request->ip(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            return response()->json([
                'success' => false,
                'message' => 'Errore nel recupero dei traits'
            ], 500);
        }
    }

    /**
     * Update CoA traits for an EGI
     *
     * @param Request $request
     * @param Egi $egi
     * @return JsonResponse
     * @privacy-safe Updates user's own EGI traits only
     *
     * @oracode-dimension governance
     * @value-flow Updates CoA traits configuration for artwork
     * @community-impact Enables comprehensive artwork description
     * @transparency-level High - complete update transparency for owner
     * @narrative-coherence Links traits updates to CoA system
     */
    public function update(Request $request, Egi $egi): JsonResponse {
        try {
            // Check ownership
            if (!$this->userOwnsEgi($egi)) {
                $this->auditService->log(
                    GdprActivityCategory::SECURITY_EVENT,
                    'unauthorized_coa_traits_update_attempt',
                    [
                        'egi_id' => $egi->id,
                        'user_id' => Auth::id(),
                        'ip_address' => $request->ip()
                    ]
                );

                return response()->json([
                    'success' => false,
                    'message' => 'Non autorizzato a modificare questi traits'
                ], 403);
            }

            // Validate input
            $validator = Validator::make($request->all(), [
                'technique_slugs' => 'sometimes|json',
                'materials_slugs' => 'sometimes|json',
                'support_slugs' => 'sometimes|json',
                'technique_free_text' => 'sometimes|json',
                'materials_free_text' => 'sometimes|json',
                'support_free_text' => 'sometimes|json'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dati non validi',
                    'errors' => $validator->errors()
                ], 422);
            }

            $this->logger->info('[CoA EGI Traits] Updating traits', [
                'egi_id' => $egi->id,
                'user_id' => Auth::id(),
                'ip_address' => $request->ip()
            ]);

            // Parse JSON inputs
            $traitsData = [];
            $categories = ['technique', 'materials', 'support'];
            $fieldTypes = ['slugs', 'free_text'];

            foreach ($categories as $category) {
                foreach ($fieldTypes as $fieldType) {
                    $fieldName = "{$category}_{$fieldType}";

                    if ($request->has($fieldName)) {
                        $jsonValue = $request->input($fieldName);
                        if (is_string($jsonValue)) {
                            $traitsData[$fieldName] = json_decode($jsonValue, true) ?? [];
                        } else {
                            $traitsData[$fieldName] = $jsonValue ?? [];
                        }
                    }
                }
            }

            // Validate custom terms length
            foreach (['technique_free_text', 'materials_free_text', 'support_free_text'] as $field) {
                if (isset($traitsData[$field])) {
                    foreach ($traitsData[$field] as $customTerm) {
                        if (strlen($customTerm) > 60) {
                            return response()->json([
                                'success' => false,
                                'message' => 'I termini personalizzati non possono superare i 60 caratteri'
                            ], 422);
                        }
                    }
                }
            }

            // Update or create CoA traits
            $coaTraits = EgiCoaTrait::updateOrCreate(
                ['egi_id' => $egi->id],
                array_merge($traitsData, [
                    'last_updated_at' => now(),
                    'updated_by_user_id' => Auth::id()
                ])
            );

            // Log audit trail
            $this->auditService->logUserAction(
                Auth::user(),
                'coa_traits_updated',
                [
                    'egi_id' => $egi->id,
                    'traits_data' => [
                        'technique_count' => count($coaTraits->technique_slugs ?? []),
                        'materials_count' => count($coaTraits->materials_slugs ?? []),
                        'support_count' => count($coaTraits->support_slugs ?? []),
                        'custom_terms_count' => count($coaTraits->technique_free_text ?? []) +
                                              count($coaTraits->materials_free_text ?? []) +
                                              count($coaTraits->support_free_text ?? [])
                    ],
                    'previous_traits_existed' => $coaTraits->wasRecentlyCreated ? false : true
                ],
                GdprActivityCategory::DATA_UPDATE
            );

            $this->logger->info('[CoA EGI Traits] Traits updated successfully', [
                'egi_id' => $egi->id,
                'coa_traits_id' => $coaTraits->id,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Traits aggiornati con successo',
                'data' => [
                    'egi_id' => $egi->id,
                    'traits' => [
                        'technique_slugs' => $coaTraits->technique_slugs ?? [],
                        'materials_slugs' => $coaTraits->materials_slugs ?? [],
                        'support_slugs' => $coaTraits->support_slugs ?? [],
                        'technique_free_text' => $coaTraits->technique_free_text ?? [],
                        'materials_free_text' => $coaTraits->materials_free_text ?? [],
                        'support_free_text' => $coaTraits->support_free_text ?? [],
                        'last_updated_at' => $coaTraits->last_updated_at->toISOString(),
                        'updated_by_user_id' => $coaTraits->updated_by_user_id
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            $this->errorManager->handle('COA_EGI_TRAITS_UPDATE_ERROR', [
                'egi_id' => $egi->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
                'ip_address' => $request->ip(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            return response()->json([
                'success' => false,
                'message' => 'Errore nell\'aggiornamento dei traits'
            ], 500);
        }
    }

    /**
     * Delete CoA traits for an EGI
     *
     * @param Request $request
     * @param Egi $egi
     * @return JsonResponse
     * @privacy-safe Deletes user's own EGI traits only
     */
    public function destroy(Request $request, Egi $egi): JsonResponse {
        try {
            // Check ownership
            if (!$this->userOwnsEgi($egi)) {
                $this->auditService->log(
                    GdprActivityCategory::SECURITY_EVENT,
                    'unauthorized_coa_traits_delete_attempt',
                    [
                        'egi_id' => $egi->id,
                        'user_id' => Auth::id(),
                        'ip_address' => $request->ip()
                    ]
                );

                return response()->json([
                    'success' => false,
                    'message' => 'Non autorizzato a cancellare questi traits'
                ], 403);
            }

            $this->logger->info('[CoA EGI Traits] Deleting traits', [
                'egi_id' => $egi->id,
                'user_id' => Auth::id(),
                'ip_address' => $request->ip()
            ]);

            $coaTraits = $egi->coaTraits;

            if ($coaTraits) {
                $coaTraits->delete();

                // Log audit trail
                $this->auditService->logUserAction(
                    Auth::user(),
                    'coa_traits_deleted',
                    [
                        'egi_id' => $egi->id,
                        'deleted_traits_id' => $coaTraits->id
                    ],
                    GdprActivityCategory::DATA_DELETION
                );

                $this->logger->info('[CoA EGI Traits] Traits deleted successfully', [
                    'egi_id' => $egi->id,
                    'user_id' => Auth::id()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Traits cancellati con successo'
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'message' => 'Nessun traits da cancellare'
                ]);
            }

        } catch (\Exception $e) {
            $this->errorManager->handle('COA_EGI_TRAITS_DELETE_ERROR', [
                'egi_id' => $egi->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'ip_address' => $request->ip(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            return response()->json([
                'success' => false,
                'message' => 'Errore nella cancellazione dei traits'
            ], 500);
        }
    }

    /**
     * Check if current user owns the EGI
     *
     * @param Egi $egi
     * @return bool
     * @privacy-safe Validates ownership for data protection
     */
    private function userOwnsEgi(Egi $egi): bool {
        return $egi->user_id === Auth::id();
    }
}
