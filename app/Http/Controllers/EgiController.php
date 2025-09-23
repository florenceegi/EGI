<?php

namespace App\Http\Controllers;

use App\Models\Egi;
use App\Models\EgiTrait;
use App\Models\Collection;
use App\Helpers\FegiAuth;
use App\Services\Gdpr\AuditLogService;
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
 * @Oracode Controller: EGI CRUD Management with FegiAuth Integration
 * 🎯 Purpose: Handle EGI (Ecological Goods Invent) CRUD operations with unified authentication
 * 🛡️ Privacy: Full GDPR compliance with audit logging and collection-based authorization
 * 🧱 Core Logic: FegiAuth-based authorization + collection membership validation + UEM error handling
 *
 * @package App\Http\Controllers
 * @author Padmin D. Curtis (AI Partner OS2.0-Compliant) for Fabio Cherici
 * @version 2.0.0 (FlorenceEGI MVP EGI_CRUD)
 * @date 2025-06-28
 * @solution FegiAuth integration + collection permission validation + Ultra ecosystem compliance
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
     * @Oracode Constructor: Dependency Injection Setup
     * 🎯 Purpose: Initialize Ultra ecosystem services for EGI CRUD operations
     * 🧱 Core Logic: DI-based service injection following Ultra patterns
     *
     * @param ErrorManagerInterface $errorManager Ultra Error Manager for standardized error handling
     * @param UltraLogManager $logger Ultra Log Manager for operational logging
     * @param AuditLogService $auditLogService GDPR audit service for compliance tracking
     */
    public function __construct(
        ErrorManagerInterface $errorManager,
        UltraLogManager $logger,
        AuditLogService $auditLogService
    ) {
        $this->errorManager = $errorManager;
        $this->logger = $logger;
        $this->auditLogService = $auditLogService;
    }

    /**
     * @Oracode Method: Show EGI Detail with CRUD Box
     * 🎯 Purpose: Display EGI detail page with conditional CRUD interface
     * 📤 Output: View with EGI data and CRUD permissions
     * 🧱 Core Logic: Load EGI relationships + determine CRUD visibility based on FegiAuth + collection membership
     *
     * @param int $id EGI ID
     * @return View
     */
    public function show($id): View | RedirectResponse {
        try {
            $egi = Egi::with([
                'collection.creator',
                'collection.users',
                'collection.epp',
                'user',
                'owner',
                'likes',
                'reservationCertificates',
                'traits.category',
                'traits.traitType'
            ])->find($id);

            // Check if EGI exists
            if (!$egi) {
                return redirect()->route('collections.index')
                    ->with('error', __('errors.egi_not_found', ['id' => $id]));
            }

            // Log page access (developers only - English)
            $this->logger->info('EGI_PAGE_ACCESS: EGI detail page accessed successfully', [
                'traits_count' => $egi->traits->count(),
                'egi_id' => $egi->id,
                'collection_id' => $egi->collection_id,
                'user_id' => FegiAuth::id(),
                'auth_type' => FegiAuth::getAuthType()
            ]);

            // Rarity percentages are pre-calculated and stored in database
            // No need for dynamic calculation anymore

            // Check if user can see CRUD box
            $canManage = false;
            if (FegiAuth::check()) {
                $user = FegiAuth::user();
                $canManage = $this->canManageEgi($user, $egi);
            }

            // Check likes for authenticated users
            if (FegiAuth::check()) {
                $userId = FegiAuth::id();
                $egi->is_liked = $egi->likes()
                    ->where('user_id', $userId)
                    ->exists();
            } else {
                $egi->is_liked = false;
            }

            $egi->likes_count = $egi->likes()->count();
            $collection = $egi->collection;

            // Get all EGIs from the same collection for the navigation carousel
            $collectionEgis = $collection->egis()
                ->whereNotNull('key_file')
                ->whereNotNull('extension')
                ->orderBy('position')
                ->orderBy('id')
                ->get();

            return view('egis.show', compact('egi', 'collection', 'canManage', 'collectionEgis'));
        } catch (\Exception $e) {
            return $this->errorManager->handle('EGI_PAGE_RENDERING_ERROR', [
                'egi_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => FegiAuth::id()
            ], $e);
        }
    }

    /**
     * @Oracode Method: Update EGI Data
     * 🎯 Purpose: Update EGI fields with validation and audit logging
     * 📤 Output: JSON response for AJAX or redirect for form submission
     * 🧱 Core Logic: FegiAuth authorization + collection membership + validation + transaction + audit
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

            // Check basic permission
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
                    'validation_errors' => $validator->errors()->toArray()
                ]);
            }

            $validated = $validator->validated();

            // Store original data for audit
            $originalData = [
                'title' => $egi->title,
                'description' => $egi->description,
                'price' => $egi->price,
                'creation_date' => $egi->creation_date?->toDateString(),
                'is_published' => $egi->is_published
            ];

            // Update EGI in transaction
            DB::transaction(function () use ($egi, $validated, $user) {
                $egi->fill($validated);
                $egi->updated_by = $user->id;
                $egi->save();
            });

            // Log operational action (developers only - English)
            $this->logger->info('EGI_UPDATE: EGI updated successfully', [
                'user_id' => $user->id,
                'egi_id' => $egi->id,
                'collection_id' => $egi->collection_id,
                'updated_fields' => array_keys($validated),
                'auth_type' => FegiAuth::getAuthType()
            ]);

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
                'error' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * @Oracode Method: Delete EGI
     * 🎯 Purpose: Soft delete EGI with proper authorization and audit logging
     * 📤 Output: JSON response for AJAX or redirect for form submission
     * 🧱 Core Logic: FegiAuth authorization + collection membership + soft delete + audit
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

            // Check basic permission
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

            // Soft delete EGI in transaction
            DB::transaction(function () use ($egi, $user) {
                $egi->updated_by = $user->id;
                $egi->save();
                $egi->delete(); // Soft delete
            });

            // Clear traits rarity cache after EGI deletion
            $this->clearTraitsRarityCache($egi->collection_id);

            // Update rarity percentages for remaining traits in collection
            $this->updateRarityPercentages($egi->collection_id);

            // Log operational action (developers only - English)
            $this->logger->warning('EGI_DELETE: EGI deleted successfully', [
                'user_id' => $user->id,
                'egi_id' => $egi->id,
                'collection_id' => $egi->collection_id,
                'auth_type' => FegiAuth::getAuthType()
            ]);

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
                        'egi_id' => $egi->id,
                        'collection_id' => $egi->collection_id
                    ]
                ]);
            }

            return redirect()->route('home.collections.show', $egi->collection)
                ->with('success', __('egi.crud.delete_success'));
        } catch (\Exception $e) {
            return $this->errorManager->handle('EGI_DELETE_FAILED', [
                'user_id' => FegiAuth::id(),
                'egi_id' => $egi->id,
                'error' => $e->getMessage()
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
