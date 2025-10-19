<?php

namespace App\Http\Controllers;

use App\Models\Egi;
use App\Models\EgiTrait;
use App\Models\Collection;
use App\Helpers\FegiAuth;
use App\Services\Gdpr\AuditLogService;
use App\Services\Egi\EgiService;
use App\Services\View\ViewService;
use App\Enums\Gdpr\GdprActivityCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * @Oracode Controller: EGI CRUD Management with Service Layer
 * 🎯 Purpose: Handle EGI (Ecological Goods Invent) CRUD operations with universal architecture
 * 🛡️ Privacy: Full GDPR compliance with audit logging and Spatie-based authorization
 * 🧱 Core Logic: Service Layer + FegiAuth + Spatie permissions + UEM error handling
 *
 * @package App\Http\Controllers
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 3.0.0 (FlorenceEGI Enterprise Architecture - Service Layer)
 * @date 2025-10-04
 * @solution Service Layer Pattern + Spatie authorization + ViewService routing + Universal CRUD
 *
 * Architecture:
 * - EgiService: Business logic layer (CRUD, filtering, authorization checks)
 * - ViewService: View routing layer (role-based view resolution)
 * - Spatie: Permission system (NOT Laravel Policy)
 * - FegiAuth: Unified authentication helper
 * - ULM/UEM/GDPR: Full Ultra ecosystem integration
 */
class EgiController extends Controller {
    /**
     * Ultra Error Manager instance for standardized error handling
     *
     * @var ErrorManagerInterface
     */
    protected ErrorManagerInterface $errorManager;

    /**
     * Ultra Log Manager instance for operational logging
     *
     * @var UltraLogManager
     */
    protected UltraLogManager $logger;

    /**
     * GDPR Audit Log Service instance for compliance tracking
     *
     * @var AuditLogService
     */
    protected AuditLogService $auditLogService;

    /**
     * EGI Service for business logic
     *
     * @var EgiService
     */
    protected EgiService $egiService;

    /**
     * View Service for role-based view routing
     *
     * @var ViewService
     */
    protected ViewService $viewService;

    /**
     * @Oracode Constructor: Dependency Injection Setup (Service Layer)
     * 🎯 Purpose: Initialize Ultra ecosystem + Service Layer for EGI operations
     * 🧱 Core Logic: DI-based service injection following Enterprise patterns
     *
     * @param ErrorManagerInterface $errorManager Ultra Error Manager
     * @param UltraLogManager $logger Ultra Log Manager
     * @param AuditLogService $auditLogService GDPR audit service
     * @param EgiService $egiService Business logic service
     * @param ViewService $viewService View routing service
     */
    public function __construct(
        ErrorManagerInterface $errorManager,
        UltraLogManager $logger,
        AuditLogService $auditLogService,
        EgiService $egiService,
        ViewService $viewService
    ) {
        $this->errorManager = $errorManager;
        $this->logger = $logger;
        $this->auditLogService = $auditLogService;
        $this->egiService = $egiService;
        $this->viewService = $viewService;
    }

    /**
     * @Oracode Method: EGI List (Service Layer)
     * 🎯 Purpose: Display EGI list with role-based filtering
     * 📤 Output: Role-based view with paginated EGI list
     * 🧱 Core Logic: EgiService filters by role + ViewService routes to correct view
     *
     * @param Request $request
     * @return View|RedirectResponse
     */
    public function index(Request $request): View | RedirectResponse {
        try {
            // Authentication required for EGI list
            if (!FegiAuth::check()) {
                return redirect()->route('login')
                    ->with('error', __('errors.authentication_required'));
            }

            $user = FegiAuth::user();

            // Service handles role-based filtering + pagination
            $egis = $this->egiService->index($user, $request);

            // ViewService determines correct view based on user role
            $view = $this->viewService->getViewForRole($user, 'index');

            // ULM logging
            $this->logger->info('EGI_INDEX_ACCESSED', [
                'user_id' => $user->id,
                'user_role' => $user->roles->pluck('name')->first(),
                'view' => $view,
                'results_count' => $egis->count(),
                'total' => $egis->total(),
                'per_page' => $egis->perPage(),
                'current_page' => $egis->currentPage(),
                'filters' => $request->only(['search', 'coa_status']),
            ]);

            // dd($view, $egis);

            return view($view, compact('egis'));
        } catch (\Exception $e) {
            return $this->errorManager->handle('EGI_INDEX_ERROR', [
                'user_id' => FegiAuth::id(),
            ], $e);
        }
    }

    /**
     * @Oracode Method: Show EGI Creation Form (Service Layer)
     * 🎯 Purpose: Display EGI upload/creation form
     * 📤 Output: Role-based view with user collections
     * 🧱 Core Logic: ViewService routes to correct form + Spatie authorization
     *
     * @return View|RedirectResponse
     */
    public function create(): View | RedirectResponse {
        try {
            // Authentication required
            if (!FegiAuth::check()) {
                return redirect()->route('login')
                    ->with('error', __('errors.authentication_required'));
            }

            $user = FegiAuth::user();

            // Check Spatie permission
            if (!$user->can('create_EGI')) {
                return redirect()->back()
                    ->with('error', __('errors.unauthorized_action'));
            }

            // Get user collections (only artwork type for MVP)
            $collections = $user->collections()
                ->where('type', 'artwork')
                ->orderBy('name')
                ->get();

            // ViewService determines correct view based on user role
            $view = $this->viewService->getViewForRole($user, 'create');

            // ULM logging
            $this->logger->info('EGI_CREATE_FORM_ACCESSED', [
                'user_id' => $user->id,
                'user_role' => $user->roles->pluck('name')->first(),
                'view' => $view,
                'collections_count' => $collections->count(),
            ]);

            return view($view, compact('collections'));
        } catch (\Exception $e) {
            return $this->errorManager->handle('EGI_CREATE_FORM_ERROR', [
                'user_id' => FegiAuth::id(),
            ], $e);
        }
    }

    /**
     * @Oracode Method: Show EGI Edit Form (Service Layer)
     * 🎯 Purpose: Display EGI edit form with pre-populated data
     * 📤 Output: Role-based edit view with EGI data and user collections
     * 🧱 Core Logic: ViewService routes to correct form + Spatie authorization
     *
     * @param Egi $egi EGI model instance (route model binding)
     * @return View|RedirectResponse
     */
    public function edit(Egi $egi): View | RedirectResponse {
        try {
            // Authentication required
            if (!FegiAuth::check()) {
                return redirect()->route('login')
                    ->with('error', __('errors.authentication_required'));
            }

            $user = FegiAuth::user();

            // Check Spatie permission
            if (!$user->can('update_EGI')) {
                return redirect()->back()
                    ->with('error', __('errors.unauthorized_action'));
            }

            // Check ownership via EgiService
            if (!$this->egiService->canManageEgi($user, $egi)) {
                return redirect()->back()
                    ->with('error', __('errors.unauthorized_egi_edit'));
            }

            // Get user collections (only artwork type for MVP)
            $collections = $user->collections()
                ->where('type', 'artwork')
                ->orderBy('name')
                ->get();

            // ViewService determines correct view based on user role
            $view = $this->viewService->getViewForRole($user, 'edit');

            // ULM logging
            $this->logger->info('EGI_EDIT_FORM_ACCESSED', [
                'egi_id' => $egi->id,
                'user_id' => $user->id,
                'user_role' => $user->roles->pluck('name')->first(),
                'view' => $view,
                'collections_count' => $collections->count(),
            ]);

            return view($view, compact('egi', 'collections'));
        } catch (\Exception $e) {
            return $this->errorManager->handle('EGI_EDIT_FORM_ERROR', [
                'egi_id' => $egi->id,
                'user_id' => FegiAuth::id(),
            ], $e);
        }
    }

    /**
     * @Oracode Method: Show EGI Detail (Service Layer)
     * 🎯 Purpose: Display EGI detail page with conditional CRUD interface
     * 📤 Output: Role-based view with EGI data and CRUD permissions
     * 🧱 Core Logic: EgiService for data + ViewService for routing + authorization checks
     *
     * @param Egi $egi EGI model instance (route model binding)
     * @return View|RedirectResponse
     */
    public function show(Egi $egi): View | RedirectResponse {
        try {
            // Check authentication (public access allowed for published EGIs)
            if (!FegiAuth::check()) {
                // For public access: only published EGIs visible
                if (!$egi->is_published) {
                    return redirect()->route('collections.index')
                        ->with('error', __('errors.authentication_required'));
                }

                // Public view: no CRUD box, minimal data
                $canManage = false;
                $egi->is_liked = false;
                $egi->likes_count = $egi->likes()->count();

                // ✅ CRITICAL: Load blockchain relationship for mint button visibility
                $egi->load('blockchain');

                $collectionEgis = $egi->collection->egis()
                    ->whereNotNull('key_file')
                    ->whereNotNull('extension')
                    ->where('is_published', true)
                    ->orderBy('position')
                    ->orderBy('id')
                    ->get();

                // Get collection for view (needed by blade template)
                $collection = $egi->collection;

                // Use default view for public users
                return view('egis.show', compact('egi', 'collection', 'canManage', 'collectionEgis'));
            }

            // Authenticated user: delegate to service
            $user = FegiAuth::user();

            // Service handles eager loading and authorization check
            $egi = $this->egiService->show($user, $egi);

            // Check management permissions (for CRUD box display)
            $canManage = $this->canManageEgi($user, $egi);

            // Check likes for authenticated users
            $userId = FegiAuth::id();
            $egi->is_liked = $egi->likes()
                ->where('user_id', $userId)
                ->exists();

            $egi->likes_count = $egi->likes()->count();

            // Get all EGIs from the same collection for the navigation carousel
            $collectionEgis = $egi->collection->egis()
                ->whereNotNull('key_file')
                ->whereNotNull('extension')
                ->orderBy('position')
                ->orderBy('id')
                ->get();

            // Get collection for view (needed by blade template)
            $collection = $egi->collection;

            // ViewService determines correct view based on user role
            $view = $this->viewService->getViewForRole($user, 'show');

            // ULM logging
            $this->logger->info('EGI_DETAIL_ACCESSED', [
                'egi_id' => $egi->id,
                'collection_id' => $egi->collection_id,
                'user_id' => $user->id,
                'user_role' => $user->roles->pluck('name')->first(),
                'view' => $view,
                'can_manage' => $canManage,
                'auth_type' => FegiAuth::getAuthType(),
            ]);

            return view($view, compact('egi', 'collection', 'canManage', 'collectionEgis'));
        } catch (\Exception $e) {
            return $this->errorManager->handle('EGI_PAGE_RENDERING_ERROR', [
                'egi_id' => $egi->id,
                'user_id' => FegiAuth::id(),
            ], $e);
        }
    }

    /**
     * @Oracode Method: Update EGI Data (Service Layer)
     * 🎯 Purpose: Update EGI fields with validation and audit logging
     * 📤 Output: JSON response for AJAX or redirect for form submission
     * 🧱 Core Logic: EgiService handles update + Spatie authorization + GDPR audit
     *
     * @param Request $request
     * @param Egi $egi
     * @return JsonResponse|RedirectResponse
     */
    public function update(Request $request, Egi $egi) {
        try {
            // Check authentication
            if (!FegiAuth::check()) {
                return $this->errorManager->handle('EGI_UNAUTHORIZED_ACCESS', [
                    'egi_id' => $egi->id,
                    'action' => 'update'
                ]);
            }

            $user = FegiAuth::user();

            // Check basic permission (Spatie)
            if (!FegiAuth::can('update_EGI')) {
                return $this->errorManager->handle('EGI_UNAUTHORIZED_ACCESS', [
                    'user_id' => $user->id,
                    'egi_id' => $egi->id,
                    'action' => 'update',
                    'reason' => 'missing_update_egi_permission'
                ]);
            }

            // Check collection membership and role
            if (!$this->canManageEgi($user, $egi)) {
                return $this->errorManager->handle('EGI_UNAUTHORIZED_ACCESS', [
                    'user_id' => $user->id,
                    'egi_id' => $egi->id,
                    'collection_id' => $egi->collection_id,
                    'action' => 'update',
                    'reason' => 'insufficient_collection_permissions'
                ]);
            }

            // Validate input
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:60',
                'description' => 'nullable|string|max:5000',
                'price' => 'nullable|numeric|min:0',
                'creation_date' => 'nullable|date',
                'is_published' => 'boolean'
            ], [
                'title.required' => __('egi.validation.title_required'),
                'title.max' => __('egi.validation.title_max'),
                'description.max' => __('egi.validation.description_max'),
                'price.numeric' => __('egi.validation.price_numeric'),
                'price.min' => __('egi.validation.price_min'),
                'creation_date.date' => __('egi.validation.creation_date_invalid')
            ]);

            if ($validator->fails()) {
                return $this->errorManager->handle('EGI_VALIDATION_FAILED', [
                    'user_id' => $user->id,
                    'egi_id' => $egi->id,
                    'validation_errors' => json_encode($validator->errors()->toArray())
                ]);
            }

            $validated = $validator->validated();

            // 🔒 BLOCKCHAIN IMMUTABILITY: Check if EGI is minted (BLOCKING)
            if ($egi->token_EGI) {
                // Se EGI è mintato, SOLO price può essere modificato
                $allowedFields = ['price'];
                $attemptedFields = array_keys($validated);
                $blockedFields = array_diff($attemptedFields, $allowedFields);

                if (!empty($blockedFields)) {
                    return $this->errorManager->handle('EGI_METADATA_IMMUTABLE', [
                        'user_id' => $user->id,
                        'egi_id' => $egi->id,
                        'token_egi' => $egi->token_EGI,
                        'attempted_fields' => $attemptedFields,
                        'blocked_fields' => $blockedFields,
                        'allowed_fields' => $allowedFields
                    ]);
                }

                // Se arriva qui, sta modificando SOLO price → permesso
                // Rimuovi comunque altri campi per sicurezza
                $validated = array_intersect_key($validated, array_flip($allowedFields));
            }

            // Store original data for audit
            $originalData = [
                'title' => $egi->title,
                'description' => $egi->description,
                'price' => $egi->price,
                'creation_date' => $egi->creation_date?->toDateString(),
                'is_published' => $egi->is_published
            ];

            // Service handles update (transaction, updated_by, cache invalidation)
            $egi = $this->egiService->update($user, $egi, $validated);

            // Log GDPR audit trail
            $this->auditLogService->logUserAction(
                $user,
                'egi_updated',
                [
                    'egi_id' => $egi->id,
                    'collection_id' => $egi->collection_id,
                    'original_data' => $originalData,
                    'updated_data' => $validated,
                    'updated_fields' => array_keys($validated)
                ],
                GdprActivityCategory::PLATFORM_USAGE
            );

            // Return appropriate response
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('egi.crud.update_success'),
                    'data' => [
                        'egi_id' => $egi->id,
                        'updated_fields' => array_keys($validated)
                    ]
                ]);
            }

            return redirect()->route('egis.show', $egi)
                ->with('success', __('egi.crud.update_success'));
        } catch (\Exception $e) {
            return $this->errorManager->handle('EGI_UPDATE_FAILED', [
                'user_id' => FegiAuth::id(),
                'egi_id' => $egi->id,
            ], $e);
        }
    }

    /**
     * @Oracode Method: Delete EGI (Service Layer)
     * 🎯 Purpose: Soft delete EGI with proper authorization and audit logging
     * 📤 Output: JSON response for AJAX or redirect for form submission
     * 🧱 Core Logic: EgiService handles destroy + Spatie authorization + GDPR audit
     *
     * @param Request $request
     * @param Egi $egi
     * @return JsonResponse|RedirectResponse
     */
    public function destroy(Request $request, Egi $egi) {
        try {
            // Check authentication
            if (!FegiAuth::check()) {
                return $this->errorManager->handle('EGI_UNAUTHORIZED_ACCESS', [
                    'egi_id' => $egi->id,
                    'action' => 'delete'
                ]);
            }

            $user = FegiAuth::user();

            // Check basic permission (Spatie)
            if (!FegiAuth::can('delete_EGI')) {
                return $this->errorManager->handle('EGI_UNAUTHORIZED_ACCESS', [
                    'user_id' => $user->id,
                    'egi_id' => $egi->id,
                    'action' => 'delete',
                    'reason' => 'missing_delete_egi_permission'
                ]);
            }

            // Check collection membership and role
            if (!$this->canManageEgi($user, $egi)) {
                return $this->errorManager->handle('EGI_UNAUTHORIZED_ACCESS', [
                    'user_id' => $user->id,
                    'egi_id' => $egi->id,
                    'collection_id' => $egi->collection_id,
                    'action' => 'delete',
                    'reason' => 'insufficient_collection_permissions'
                ]);
            }

            // Store data for audit before deletion
            $egiData = [
                'egi_id' => $egi->id,
                'title' => $egi->title,
                'collection_id' => $egi->collection_id,
                'user_id' => $egi->user_id,
                'owner_id' => $egi->owner_id,
                'is_published' => $egi->is_published,
                'created_at' => $egi->created_at->toISOString()
            ];

            // Service handles soft delete (transaction, updated_by, cache invalidation, rarity updates)
            $this->egiService->destroy($user, $egi);

            // Log GDPR audit trail
            $this->auditLogService->logUserAction(
                $user,
                'egi_deleted',
                [
                    'deleted_egi_data' => $egiData,
                    'deletion_type' => 'soft_delete'
                ],
                GdprActivityCategory::PLATFORM_USAGE
            );

            // Return appropriate response
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('egi.crud.delete_success'),
                    'data' => [
                        'egi_id' => $egiData['egi_id'],
                        'collection_id' => $egiData['collection_id']
                    ]
                ]);
            }

            return redirect()->route('home.collections.show', $egiData['collection_id'])
                ->with('success', __('egi.crud.delete_success'));
        } catch (\Exception $e) {
            return $this->errorManager->handle('EGI_DELETE_FAILED', [
                'user_id' => FegiAuth::id(),
                'egi_id' => $egi->id,
            ], $e);
        }
    }

    /**
     * @Oracode Method: Authorize EGI Management Access
     * 🎯 Purpose: Check if user can manage specific EGI based on collection membership and role
     * 📤 Output: Boolean indicating management permission
     * 🧱 Core Logic: Collection membership + role validation (admin/editor required)
     *
     * @param \App\Models\User $user User attempting the action
     * @param Egi $egi EGI being accessed
     * @return bool True if user can manage this EGI
     */
    protected function canManageEgi($user, Egi $egi): bool {
        try {
            $collection = $egi->collection;

            // Check collection membership via collection_users pivot table
            $membership = $collection->users()
                ->where('user_id', $user->id)
                ->first();

            if (!$membership) {
                return false;
            }

            // Check role - only admin/editor/creator can manage EGIs
            $userRole = $membership->pivot->role ?? null;

            return in_array($userRole, ['admin', 'editor', 'creator']);
        } catch (\Exception $e) {
            // Log error (developers only - English)
            $this->logger->error('EGI_PERMISSION_CHECK_ERROR: Failed to check EGI management permissions', [
                'user_id' => $user->id,
                'egi_id' => $egi->id,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Calculate rarity percentage for a trait value
     *
     * @param int $traitTypeId
     * @param string $value
     * @param int $collectionId
     * @return float
     */
    private function calculateRarity($traitTypeId, $value, $collectionId) {
        $cacheKey = "trait_rarity_{$collectionId}_{$traitTypeId}_{$value}";

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 3600, function () use ($traitTypeId, $value, $collectionId) {
            // Total EGIs in collection
            $totalEgis = \App\Models\Egi::where('collection_id', $collectionId)->count();

            if ($totalEgis === 0) {
                return 0;
            }

            // EGIs with this trait value
            $egisWithTrait = \App\Models\EgiTrait::join('egis', 'egis.id', '=', 'egi_traits.egi_id')
                ->where('egis.collection_id', $collectionId)
                ->where('egi_traits.trait_type_id', $traitTypeId)
                ->where('egi_traits.value', $value)
                ->count();

            return round(($egisWithTrait / $totalEgis) * 100, 2);
        });
    }

    /**
     * Clear traits rarity cache for a specific collection
     *
     * @param int $collectionId
     * @return void
     */
    private function clearTraitsRarityCache(int $collectionId): void {
        try {
            // Get all cache keys that match the pattern for this collection
            $pattern = "trait_rarity_{$collectionId}_*";

            // Use Cache::flush() to clear all cache or implement more specific clearing
            // For now, we'll use a simple approach and clear all cache
            \Illuminate\Support\Facades\Cache::flush();

            $this->logger->info('Traits rarity cache cleared for collection', [
                'collection_id' => $collectionId,
                'pattern' => $pattern
            ]);
        } catch (\Exception $e) {
            // Log error but don't fail the main operation
            $this->logger->error('Failed to clear traits rarity cache', [
                'collection_id' => $collectionId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update rarity percentages for all traits in a collection
     *
     * @param int $collectionId
     * @return void
     */
    private function updateRarityPercentages(int $collectionId): void {
        try {
            $this->logger->info('Updating rarity percentages for collection', ['collection_id' => $collectionId]);

            // Get total EGIs in collection
            $totalEgis = Egi::where('collection_id', $collectionId)->count();

            if ($totalEgis === 0) {
                $this->logger->info('No EGIs in collection, skipping rarity update', ['collection_id' => $collectionId]);
                return;
            }

            // Get all unique trait combinations (trait_type_id + value) in this collection
            $uniqueTraits = EgiTrait::join('egis', 'egis.id', '=', 'egi_traits.egi_id')
                ->where('egis.collection_id', $collectionId)
                ->select('egi_traits.trait_type_id', 'egi_traits.value')
                ->distinct()
                ->get();

            $this->logger->info('Found unique traits', ['count' => $uniqueTraits->count()]);

            // Calculate and update rarity for each unique trait combination
            foreach ($uniqueTraits as $uniqueTrait) {
                // Count how many EGIs have this trait
                $egisWithTrait = EgiTrait::join('egis', 'egis.id', '=', 'egi_traits.egi_id')
                    ->where('egis.collection_id', $collectionId)
                    ->where('egi_traits.trait_type_id', $uniqueTrait->trait_type_id)
                    ->where('egi_traits.value', $uniqueTrait->value)
                    ->count();

                // Calculate percentage
                $percentage = round(($egisWithTrait / $totalEgis) * 100, 2);

                // Update all traits with this combination
                $updatedCount = EgiTrait::join('egis', 'egis.id', '=', 'egi_traits.egi_id')
                    ->where('egis.collection_id', $collectionId)
                    ->where('egi_traits.trait_type_id', $uniqueTrait->trait_type_id)
                    ->where('egi_traits.value', $uniqueTrait->value)
                    ->update(['egi_traits.rarity_percentage' => $percentage]);

                $this->logger->info('Updated rarity percentage', [
                    'trait_type_id' => $uniqueTrait->trait_type_id,
                    'value' => $uniqueTrait->value,
                    'percentage' => $percentage,
                    'egis_with_trait' => $egisWithTrait,
                    'total_egis' => $totalEgis,
                    'updated_count' => $updatedCount
                ]);
            }

            $this->logger->info('Rarity percentages updated successfully for collection', ['collection_id' => $collectionId]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to update rarity percentages', [
                'collection_id' => $collectionId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get dossier data for an EGI including utility gallery images
     *
     * @param Egi $egi
     * @return JsonResponse
     */
    public function dossier(Egi $egi): JsonResponse {
        try {
            $this->logger->info('Loading dossier for EGI', ['egi_id' => $egi->id]);

            // Load utility with media
            $utility = $egi->utility()->with('media')->first();

            if (!$utility) {
                $this->logger->info('No utility found for EGI', ['egi_id' => $egi->id]);
                return response()->json([
                    'status' => 'no_utility',
                    'message' => 'No utility configured for this EGI'
                ]);
            }

            // Get utility gallery images
            $images = $utility->getMedia('utility_gallery');
            $this->logger->info('Found utility with images', ['utility_id' => $utility->id, 'images_count' => $images->count()]);

            if ($images->isEmpty()) {
                $this->logger->info('No images found in utility gallery', ['utility_id' => $utility->id]);
                return response()->json([
                    'status' => 'no_images',
                    'message' => 'Utility exists but no images available'
                ]);
            }

            // Format image data
            $imageData = $images->map(function ($media) {
                return [
                    'id' => $media->id,
                    'name' => $media->name,
                    'file_name' => $media->file_name,
                    'url' => $media->getUrl(),
                    'thumb_url' => $media->getUrl('thumb'),
                    'medium_url' => $media->getUrl('medium'),
                    'size' => $media->size,
                    'mime_type' => $media->mime_type,
                ];
            });

            return response()->json([
                'status' => 'success',
                'data' => [
                    'egi' => [
                        'id' => $egi->id,
                        'title' => $egi->title,
                        'internal_id' => str_pad($egi->id, 7, '0', STR_PAD_LEFT),
                        'author' => $egi->collection->creator->name ?? 'Unknown',
                        'year' => $egi->created_at->format('Y'),
                    ],
                    'utility' => [
                        'id' => $utility->id,
                        'title' => $utility->title,
                        'type' => $utility->type,
                        'images_count' => $images->count(),
                    ],
                    'images' => $imageData
                ]
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to load dossier data', [
                'egi_id' => $egi->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load dossier data'
            ], 500);
        }
    }
}
