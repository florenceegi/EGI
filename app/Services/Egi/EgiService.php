<?php

namespace App\Services\Egi;

use App\Models\Egi;
use App\Models\User;
use App\Helpers\FegiAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * EGI Service - Business Logic Layer
 *
 * @package App\Services\Egi
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Enterprise Architecture Refactor)
 * @date 2025-10-04
 * @purpose Service Layer per gestione EGI con filtri role-based
 *
 * Features:
 * - Query EGI con filtri per ruolo (Creator, PA, Inspector, Company)
 * - CRUD operations con authorization delegation
 * - Cache management
 * - Statistics aggregation
 * - Universal business logic (no controller logic)
 *
 * Architecture:
 * - Service Layer Pattern (SOLID principles)
 * - Role-based filtering (PA, Creator, Inspector, Company)
 * - Spatie permission-based authorization (via $user->can())
 * - Collection membership validation
 * - ULM logging integration
 * - ErrorManager exception handling
 *
 * GDPR: ULM logging per operazioni dati, audit trail via AuditLogService
 */
class EgiService {
    /**
     * Ultra Log Manager instance
     */
    protected UltraLogManager $logger;

    /**
     * Ultra Error Manager instance
     */
    protected ErrorManagerInterface $errorManager;

    /**
     * Constructor - Dependency Injection
     *
     * @param UltraLogManager $logger
     * @param ErrorManagerInterface $errorManager
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
    }

    /**
     * Get EGIs list with role-based filtering
     *
     * @param User $user Authenticated user
     * @param Request $request Request con filtri (search, status, etc.)
     * @param int $perPage Items per page (default 15)
     * @return LengthAwarePaginator
     *
     * Features:
     * - Role-based filtering (Creator, PA, Inspector, Company)
     * - Search filters (title, artist, description)
     * - Status filters (published, CoA status)
     * - Pagination
     * - Eager loading relationships
     *
     * IMPORTANT: NO hidden ->take() limit (REGOLA STATISTICS)
     */
    public function index(User $user, Request $request, int $perPage = 15): LengthAwarePaginator {
        try {
            // Base query with relationships
            $query = Egi::with([
                'collection',
                'coa',
                'user',
                'owner',
                'blockchain.buyer', // 🤝 Co-Creator data
                'reservations' => function ($query) {
                    $query->where('sub_status', 'highest')
                        ->where('status', 'active')
                        ->with('user');
                }
            ]);

            // Apply role-based filters
            $this->applyRoleFilters($query, $user);

            // Apply search filters if present
            if ($search = $request->input('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('artist', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Apply CoA status filter if present
            if ($coaStatus = $request->input('coa_status')) {
                if ($coaStatus === 'no_coa') {
                    $query->doesntHave('coa');
                } else {
                    $query->whereHas('coa', function ($q) use ($coaStatus) {
                        $q->where('status', $coaStatus);
                    });
                }
            }

            // Apply published filter if present
            if ($request->has('is_published')) {
                $query->where('is_published', $request->boolean('is_published'));
            }

            // Order by creation date (newest first)
            $query->orderBy('created_at', 'desc');

            // Paginate (NO hidden ->take() limit!)
            /** @var LengthAwarePaginator $egis */
            $egis = $query->paginate($perPage);

            // ULM: Log query
            $this->logger->info('EGI_SERVICE_INDEX: EGI list queried', [
                'user_id' => $user->id,
                'role' => $user->roles->pluck('name')->first(),
                'filters' => $request->only(['search', 'coa_status', 'is_published']),
                'results_count' => $egis->count(),
                'total' => $egis->total(),
            ]);

            return $egis;
        } catch (\Exception $e) {
            // ULM: Log error context
            $this->logger->error('EGI_SERVICE_INDEX_ERROR', [
                'user_id' => $user->id,
                'filters' => $request->only(['search', 'coa_status', 'is_published']),
                'exception' => $e->getMessage(),
            ]);

            // Re-throw: Controller will handle via ErrorManager
            throw $e;
        }
    }

    /**
     * Get single EGI with relationships
     *
     * @param User $user Authenticated user
     * @param Egi $egi EGI model instance
     * @return Egi
     *
     * Features:
     * - Eager load relationships (collection, CoA, traits, etc.)
     * - Authorization check via Policy
     * - ULM logging
     */
    public function show(User $user, Egi $egi): Egi {
        try {
            // Load relationships for detail view
            $egi->load([
                'collection.creator',
                'collection.users',
                'collection.epp',
                'collection.owner',
                'user',
                'owner',
                'likes',
                'coa.files',
                'coa.signatures.signer',
                'coa.events',
                'coaTraits',
                'traits.category',
                'traits.traitType',
                'reservationCertificates', // ✅ Reservation history
                'mintCertificates', // ✅ Mint/Rebind blockchain purchases history
            ]);

            // Check likes for authenticated user
            if ($user) {
                $egi->is_liked = $egi->likes()->where('user_id', $user->id)->exists();
            } else {
                $egi->is_liked = false;
            }

            $egi->likes_count = $egi->likes()->count();

            // ULM: Log EGI access
            $this->logger->info('EGI_SERVICE_SHOW: EGI detail accessed', [
                'user_id' => $user->id,
                'egi_id' => $egi->id,
                'egi_title' => $egi->title,
                'role' => $user->roles->pluck('name')->first(),
            ]);

            return $egi;
        } catch (\Exception $e) {
            // ULM: Log error context
            $this->logger->error('EGI_SERVICE_SHOW_ERROR', [
                'user_id' => $user->id,
                'egi_id' => $egi->id,
                'exception' => $e->getMessage(),
            ]);

            // Re-throw: Controller will handle via ErrorManager
            throw $e;
        }
    }

    /**
     * Update EGI data
     *
     * @param User $user Authenticated user
     * @param Egi $egi EGI model instance
     * @param array $data Validated data
     * @return Egi Updated EGI
     *
     * Features:
     * - DB transaction
     * - Authorization via Policy
     * - ULM logging
     * - Cache invalidation
     */
    public function update(User $user, Egi $egi, array $data): Egi {
        try {
            // Store original data for audit
            $originalData = [
                'title' => $egi->title,
                'description' => $egi->description,
                'price' => $egi->price,
                'creation_date' => $egi->creation_date?->toDateString(),
                'is_published' => $egi->is_published,
            ];

            // Update in transaction
            DB::transaction(function () use ($egi, $data, $user) {
                $egi->fill($data);
                $egi->updated_by = $user->id;
                $egi->save();
            });

            // ULM: Log update
            $this->logger->info('EGI_SERVICE_UPDATE: EGI updated', [
                'user_id' => $user->id,
                'egi_id' => $egi->id,
                'updated_fields' => array_keys($data),
                'original_data' => $originalData,
            ]);

            // Invalidate cache if needed
            $this->invalidateEgiCache($egi);

            return $egi->fresh();
        } catch (\Exception $e) {
            // ULM: Log error context
            $this->logger->error('EGI_SERVICE_UPDATE_ERROR', [
                'user_id' => $user->id,
                'egi_id' => $egi->id,
                'updated_fields' => array_keys($data),
                'exception' => $e->getMessage(),
            ]);

            // Re-throw: Controller will handle via ErrorManager
            throw $e;
        }
    }

    /**
     * Soft delete EGI
     *
     * @param User $user Authenticated user
     * @param Egi $egi EGI model instance
     * @return bool Success status
     *
     * Features:
     * - Soft delete (SoftDeletes trait)
     * - Authorization via Policy
     * - ULM logging
     * - Cache invalidation
     */
    public function destroy(User $user, Egi $egi): bool {
        try {
            // Soft delete in transaction
            DB::transaction(function () use ($egi) {
                $egi->delete();
            });

            // ULM: Log deletion
            $this->logger->info('EGI_SERVICE_DESTROY: EGI soft deleted', [
                'user_id' => $user->id,
                'egi_id' => $egi->id,
                'egi_title' => $egi->title,
            ]);

            // Invalidate cache
            $this->invalidateEgiCache($egi);

            return true;
        } catch (\Exception $e) {
            // ULM: Log error context
            $this->logger->error('EGI_SERVICE_DESTROY_ERROR', [
                'user_id' => $user->id,
                'egi_id' => $egi->id,
                'exception' => $e->getMessage(),
            ]);

            // Re-throw: Controller will handle via ErrorManager
            throw $e;
        }
    }

    /**
     * Check if user can access EGI
     *
     * @param User $user
     * @param Egi $egi
     * @return bool
     *
     * Uses Spatie permissions + collection ownership
     */
    public function canUserAccessEgi(User $user, Egi $egi): bool {
        // Check Spatie permission first
        if (!$user->can('view_EGI')) {
            return false;
        }

        // Public EGIs are accessible
        if ($egi->is_published) {
            return true;
        }

        // Creator owns EGI
        if ($egi->user_id === $user->id) {
            return true;
        }

        // PA owns collection
        if ($user->hasRole('pa_entity') && $egi->collection?->owner_id === $user->id) {
            return true;
        }

        // Inspector assigned to collection
        if ($user->hasRole('inspector') && $egi->collection) {
            return $egi->collection->inspectors()->where('user_id', $user->id)->exists();
        }

        // Company owns collection
        if ($user->hasRole('company') && $egi->collection?->owner_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Apply role-based filters to query
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param User $user
     * @return void
     *
     * Filters:
     * - Creator: owns EGI (user_id)
     * - PA: owns collection (collection.owner_id)
     * - Inspector: assigned to collection (collection.inspectors pivot)
     * - Company: owns collection (future)
     */
    protected function applyRoleFilters($query, User $user): void {
        if ($user->hasRole('pa_entity')) {
            $this->applyPAFilters($query, $user);
        } elseif ($user->hasRole('inspector')) {
            $this->applyInspectorFilters($query, $user);
        } elseif ($user->hasRole('company')) {
            $this->applyCompanyFilters($query, $user);
        } else {
            // Default: Creator filter
            $this->applyCreatorFilters($query, $user);
        }
    }

    /**
     * Apply Creator filters (owns EGI)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param User $user
     * @return void
     */
    protected function applyCreatorFilters($query, User $user): void {
        $query->where('user_id', $user->id);
    }

    /**
     * Apply PA filters (owns collection)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param User $user
     * @return void
     */
    protected function applyPAFilters($query, User $user): void {
        $query->whereHas('collection', function ($q) use ($user) {
            $q->where('owner_id', $user->id)
                ->where('type', 'artwork'); // MVP uses artwork type
        });
    }

    /**
     * Apply Inspector filters (assigned to collection)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param User $user
     * @return void
     */
    protected function applyInspectorFilters($query, User $user): void {
        $query->whereHas('collection.inspectors', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        });
    }

    /**
     * Apply Company filters (owns collection)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param User $user
     * @return void
     *
     * Future implementation for Company role
     */
    protected function applyCompanyFilters($query, User $user): void {
        $query->whereHas('collection', function ($q) use ($user) {
            $q->where('owner_id', $user->id)
                ->where('type', 'company_product'); // Future: company products
        });
    }

    /**
     * Check if user can manage EGI (edit/delete)
     *
     * @param User $user
     * @param Egi $egi
     * @return bool
     *
     * Authorization rules:
     * - Creator: can manage own EGIs (user_id match)
     * - PA Entity: can manage EGIs in owned collections
     * - Inspector: cannot directly manage (read-only)
     * - Company: can manage EGIs in owned collections (future)
     *
     * Uses collection ownership check via pivot table
     */
    public function canManageEgi(User $user, Egi $egi): bool {
        try {
            // Check direct ownership (Creator)
            if ($egi->user_id === $user->id) {
                return true;
            }

            // Check collection ownership (PA/Company)
            if ($egi->collection_id) {
                $isCollectionOwner = $user->collections()
                    ->where('collections.id', $egi->collection_id)
                    ->exists();

                if ($isCollectionOwner) {
                    return true;
                }
            }

            // Default: no permission
            return false;
        } catch (\Exception $e) {
            // Log error and deny access on exception
            \Log::error('EgiService::canManageEgi error', [
                'user_id' => $user->id,
                'egi_id' => $egi->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Invalidate EGI-related caches
     *
     * @param Egi $egi
     * @return void
     */
    protected function invalidateEgiCache(Egi $egi): void {
        // Invalidate collection cache
        if ($egi->collection_id) {
            \Cache::forget("collection_{$egi->collection_id}_egis");
        }

        // Invalidate user cache
        if ($egi->user_id) {
            \Cache::forget("user_{$egi->user_id}_egis");
        }
    }
}
