<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Services\Gdpr\AuditLogService;
use App\Enums\Gdpr\GdprActivityCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * @Oracode Controller: Collection CRUD Operations
 * 🎯 Purpose: Handle collection metadata updates and deletions
 * 🛡️ Security: Authentication and authorization checks
 * 🧱 Core Logic: Validation, business rules, data persistence
 *
 * @package App\Http\Controllers
 * @author Padmin D. Curtis (for Fabio Cherici)
 * @version 2.0.0
 * @date 2025-08-28
 */
class CollectionCrudController extends Controller {
    /**
     * Logger instance for operation tracking
     * @var UltraLogManager
     */
    protected UltraLogManager $logger;

    /**
     * Error manager for robust error handling
     * @var ErrorManagerInterface
     */
    protected ErrorManagerInterface $errorManager;

    /**
     * Audit service for GDPR compliance
     * @var AuditLogService
     */
    protected AuditLogService $auditService;

    /**
     * Constructor with dependency injection
     *
     * @param UltraLogManager $logger
     * @param ErrorManagerInterface $errorManager
     * @param AuditLogService $auditService
     * @privacy-safe All dependencies handle GDPR compliance
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditService = $auditService;
    }
    /**
     * Update collection metadata
     *
     * @param Request $request
     * @param Collection $collection
     * @return JsonResponse
     * @privacy-safe Updates collection for authenticated user only
     */
    public function update(Request $request, Collection $collection): JsonResponse {
        try {
            $this->logger->info('Collection CRUD: Starting update operation', [
                'collection_id' => $collection->id,
                'log_category' => 'COLLECTION_CRUD_UPDATE'
            ]);

            // PILLAR0: No assumptions - validate authentication
            $user = $request->user();
            if (!$user) {
                $this->logger->warning('Collection CRUD: Unauthenticated update attempt', [
                    'collection_id' => $collection->id,
                    'log_category' => 'COLLECTION_CRUD_AUTH_FAIL'
                ]);

                return response()->json(['message' => 'Unauthenticated'], 401);
            }

            // Authorization check with ownership validation
            $owns = (int)$collection->creator_id === (int)$user->id;
            $canUpdate = $user->can('update_collection') || ($user->can('edit_own_collection') && $owns);

            if (!$canUpdate) {
                $this->auditService->logUserAction(
                    $user,
                    'collection_update_unauthorized',
                    ['collection_id' => $collection->id, 'user_owns' => $owns],
                    GdprActivityCategory::SECURITY_EVENTS
                );

                $this->logger->warning('Collection CRUD: Unauthorized update attempt', [
                    'user_id' => $user->id,
                    'collection_id' => $collection->id,
                    'user_owns_collection' => $owns,
                    'log_category' => 'COLLECTION_CRUD_AUTH_FAIL'
                ]);

                return response()->json(['message' => 'Forbidden'], 403);
            }

            // Validation rules - strict typing
            $rules = [
                'collection_name'     => ['required', 'string', 'max:150'],
                'description'         => ['nullable', 'string', 'max:10000'],
                'url_collection_site' => ['nullable', 'url', 'max:255'],
                'type'                => ['nullable', 'string', 'max:50'],
                'floor_price'         => ['nullable', 'numeric', 'min:0'],
                'is_published'        => ['nullable', 'boolean'],
            ];

            $data = $request->all();
            $validator = Validator::make($data, $rules);

            if ($validator->fails()) {
                $this->logger->debug('Collection CRUD: Validation failed', [
                    'collection_id' => $collection->id,
                    'user_id' => $user->id,
                    'validation_errors' => $validator->errors()->toArray(),
                    'log_category' => 'COLLECTION_CRUD_VALIDATION'
                ]);

                return response()->json([
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $payload = $validator->validated();

            // Business rule: publishing guard with method existence check
            if (array_key_exists('is_published', $payload) && $payload['is_published']) {
                if (method_exists($collection, 'canBePublished') && !$collection->canBePublished()) {
                    $this->logger->warning('Collection CRUD: Publishing conditions not met', [
                        'collection_id' => $collection->id,
                        'user_id' => $user->id,
                        'log_category' => 'COLLECTION_CRUD_BUSINESS_RULE'
                    ]);

                    return response()->json([
                        'message' => 'Cannot publish due to pending approvals or invalid state',
                        'errors' => ['is_published' => ['Publishing conditions not met']],
                    ], 422);
                }
            }

            // Store original state for audit logging
            $originalState = $collection->only([
                'collection_name',
                'description',
                'url_collection_site',
                'type',
                'floor_price',
                'is_published'
            ]);

            // Update collection with validated data
            $collection->fill([
                'collection_name'     => $payload['collection_name'] ?? $collection->collection_name,
                'description'         => $payload['description'] ?? $collection->description,
                'url_collection_site' => $payload['url_collection_site'] ?? $collection->url_collection_site,
                'type'                => $payload['type'] ?? $collection->type,
                'floor_price'         => $payload['floor_price'] ?? $collection->floor_price,
                'is_published'        => array_key_exists('is_published', $payload)
                    ? (bool)$payload['is_published']
                    : $collection->is_published,
            ]);

            // Default EPP assignment - business rule
            // Skip for company users (EPP is voluntary for them)
            $isCompanyUser = $collection->is_epp_voluntary || $collection->creator?->usertype === 'company';
            if (!$isCompanyUser && is_null($collection->epp_id)) {
                $collection->epp_id = 2;
                $this->logger->debug('Collection CRUD: Applied default EPP', [
                    'collection_id' => $collection->id,
                    'epp_id' => 2,
                    'log_category' => 'COLLECTION_CRUD_DEFAULT_EPP'
                ]);
            }

            $collection->save();

            // GDPR audit logging
            $this->auditService->logUserAction(
                $user,
                'collection_metadata_updated',
                [
                    'collection_id' => $collection->id,
                    'original_state' => $originalState,
                    'updated_fields' => array_keys($payload),
                    'epp_id_applied' => $collection->epp_id,
                    'is_company_user' => $isCompanyUser
                ],
                GdprActivityCategory::PLATFORM_USAGE
            );

            // Load relationships for response
            $collection->load(['epp']);

            // Calculate schema image with method existence check
            $schemaImage = null;
            if (method_exists($collection, 'getFirstMediaUrl')) {
                $schemaImage = $collection->getFirstMediaUrl('head', 'banner') ?: null;
            }

            $this->logger->info('Collection CRUD: Update completed successfully', [
                'collection_id' => $collection->id,
                'user_id' => $user->id,
                'updated_fields' => array_keys($payload),
                'log_category' => 'COLLECTION_CRUD_SUCCESS'
            ]);

            return response()->json([
                'success' => true,
                'collection' => [
                    'id' => $collection->id,
                    'collection_name' => $collection->collection_name,
                    'description' => $collection->description,
                    'url_collection_site' => $collection->url_collection_site,
                    'type' => $collection->type,
                    'floor_price' => $collection->floor_price,
                    'is_published' => (bool)$collection->is_published,
                    'epp_id' => $collection->epp_id,
                    'epp' => $collection->epp ? [
                        'id' => $collection->epp->id,
                        'name' => $collection->epp->name,
                        'description' => $collection->epp->description,
                    ] : null,
                ],
                'schema_image' => $schemaImage,
            ]);
        } catch (\Exception $e) {
            return $this->errorManager->handle('COLLECTION_UPDATE_FAILED', [
                'collection_id' => $collection->id,
                'user_id' => $request->user()?->id,
                'error' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Delete collection
     *
     * @param Request $request
     * @param Collection $collection
     * @return JsonResponse
     * @privacy-safe Deletes collection for authorized user only
     */
    public function destroy(Request $request, Collection $collection): JsonResponse {
        try {
            $this->logger->info('Collection CRUD: Starting delete operation', [
                'collection_id' => $collection->id,
                'log_category' => 'COLLECTION_CRUD_DELETE'
            ]);

            // PILLAR0: No assumptions - validate authentication
            $user = $request->user();
            if (!$user) {
                $this->logger->warning('Collection CRUD: Unauthenticated delete attempt', [
                    'collection_id' => $collection->id,
                    'log_category' => 'COLLECTION_CRUD_AUTH_FAIL'
                ]);

                return response()->json(['message' => 'Unauthenticated'], 401);
            }

            // Authorization check with ownership validation
            $owns = (int)$collection->creator_id === (int)$user->id;
            $canDelete = $user->can('delete_collection') || ($user->can('edit_own_collection') && $owns);

            if (!$canDelete) {
                $this->auditService->logUserAction(
                    $user,
                    'collection_delete_unauthorized',
                    ['collection_id' => $collection->id, 'user_owns' => $owns],
                    GdprActivityCategory::SECURITY_EVENTS
                );

                $this->logger->warning('Collection CRUD: Unauthorized delete attempt', [
                    'user_id' => $user->id,
                    'collection_id' => $collection->id,
                    'user_owns_collection' => $owns,
                    'log_category' => 'COLLECTION_CRUD_AUTH_FAIL'
                ]);

                return response()->json(['message' => 'Forbidden'], 403);
            }

            // Store collection data for audit before deletion
            $collectionSnapshot = [
                'id' => $collection->id,
                'collection_name' => $collection->collection_name,
                'creator_id' => $collection->creator_id,
                'is_published' => $collection->is_published,
                'created_at' => $collection->created_at?->toISOString(),
            ];

            // Perform deletion
            $collection->delete();

            // GDPR audit logging
            $this->auditService->logUserAction(
                $user,
                'collection_deleted',
                [
                    'deleted_collection' => $collectionSnapshot,
                    'deletion_method' => 'soft_delete'
                ],
                GdprActivityCategory::PLATFORM_USAGE
            );

            $this->logger->info('Collection CRUD: Delete completed successfully', [
                'collection_id' => $collectionSnapshot['id'],
                'user_id' => $user->id,
                'log_category' => 'COLLECTION_CRUD_SUCCESS'
            ]);

            return response()->json([
                'success' => true,
                'redirect_url' => route('home.collections.index'),
            ]);
        } catch (\Exception $e) {
            return $this->errorManager->handle('COLLECTION_DELETE_FAILED', [
                'collection_id' => $collection->id,
                'user_id' => $request->user()?->id,
                'error' => $e->getMessage()
            ], $e);
        }
    }
}
