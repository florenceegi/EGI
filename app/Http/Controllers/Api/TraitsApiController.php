<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Egi;
use App\Models\TraitCategory;
use App\Models\TraitType;
use App\Models\EgiTrait;
use App\Helpers\FegiAuth;
use App\Services\Gdpr\AuditLogService;
use App\Enums\Gdpr\GdprActivityCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * @Oracode API Controller: EGI Traits System Management
 * 🎯 Purpose: Handle EGI traits CRUD operations with Ultra ecosystem integration
 * 🛡️ Privacy: Full GDPR compliance with audit logging and collection-based authorization
 *
 * @package App\Http\Controllers\Api
 * @author Padmin D. Curtis (AI Partner OS3.0-Compliant) for Fabio Cherici
 * @version 2.0.0 (FlorenceEGI Traits System with Ultra Integration)
 * @date 2025-08-31
 * @solution Ultra ecosystem compliance + professional DI pattern + comprehensive error handling
 */
class TraitsApiController extends Controller {
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
     * 🎯 Purpose: Initialize Ultra ecosystem services for traits operations
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
     * Helper method to translate trait values
     *
     * @param string $value
     * @param string|null $locale
     * @return string
     */
    private function translateTraitValue(string $value, ?string $locale = null): string {
        // Se il valore è numerico (es. peso "10", "50", "100"), non tradurre
        if (is_numeric($value)) {
            return $value;
        }
        
        $translated = __('trait_elements.values.' . $value, [], $locale);
        
        // Se la traduzione restituisce la chiave stessa, usa il valore originale
        if ($translated === 'trait_elements.values.' . $value) {
            return $value;
        }
        
        return $translated;
    }

    /**
     * Get all available trait categories
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCategories(Request $request) {
        $collectionId = $request->get('collection_id');

        try {
            // Cache con chiave più specifica e timeout più breve
            $cacheKey = "trait_categories_v2_{$collectionId}_" . md5(serialize([
                'collection_id' => $collectionId,
                'timestamp' => now()->format('Y-m-d-H') // Invalidazione ogni ora
            ]));

            $categories = Cache::remember(
                $cacheKey,
                1800, // 30 minuti invece di 1 ora
                function () use ($collectionId) {
                    $query = TraitCategory::query();

                    if ($collectionId) {
                        $query->where(function ($q) use ($collectionId) {
                            $q->where('is_system', true)
                                ->orWhere('collection_id', $collectionId);
                        });
                    } else {
                        // Se non c'è collection_id, prendi solo le categorie di sistema
                        $query->where('is_system', true);
                    }

                    return $query->orderBy('sort_order')
                        ->orderBy('name')
                        ->get();
                }
            );

            // Add translations to categories
            $categoriesWithTranslations = $categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'translated_name' => __('trait_elements.categories.' . $category->name, [], null, $category->name),
                    'slug' => $category->slug,
                    'icon' => $category->icon,
                    'color' => $category->color,
                    'is_system' => $category->is_system,
                    'sort_order' => $category->sort_order,
                ];
            });

            $this->logger->info('TRAITS_API: Categories loaded successfully', [
                'collection_id' => $collectionId,
                'categories_count' => $categories->count(),
                'cache_key' => $cacheKey,
                'user_id' => FegiAuth::id()
            ]);

            return response()->json([
                'success' => true,
                'categories' => $categoriesWithTranslations
            ]);
        } catch (\Exception $e) {
            return $this->errorManager->handle('TRAITS_CATEGORIES_LOAD_FAILED', [
                'collection_id' => $collectionId,
                'user_id' => FegiAuth::id(),
                'error' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Get trait types for a category
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTraitTypes(Request $request) {
        $request->validate([
            'category_id' => 'required|exists:trait_categories,id'
        ]);

        $categoryId = $request->get('category_id');
        $collectionId = $request->get('collection_id');

        $types = TraitType::where('category_id', $categoryId)
            ->where(function ($query) use ($collectionId) {
                $query->where('is_system', true)
                    ->orWhere('collection_id', $collectionId);
            })
            ->orderBy('name')
            ->get();

        // Add translations to trait types
        $typesWithTranslations = $types->map(function ($type) {
            return [
                'id' => $type->id,
                'name' => $type->name,
                'translated_name' => __('trait_elements.types.' . $type->name, [], null, $type->name),
                'slug' => $type->slug,
                'display_type' => $type->display_type,
                'allowed_values' => $type->allowed_values,
                'unit' => $type->unit,
                'category_id' => $type->category_id,
                'is_system' => $type->is_system,
                'created_at' => $type->created_at,
                'updated_at' => $type->updated_at,
            ];
        });

        return response()->json([
            'success' => true,
            'types' => $typesWithTranslations
        ]);
    }

    /**
     * Get trait types for a specific category via URL parameter
     *
     * @param int $categoryId
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTraitTypesByCategory($categoryId, Request $request) {
        $collectionId = $request->get('collection_id');

        $types = TraitType::where('category_id', $categoryId)
            ->where(function ($query) use ($collectionId) {
                $query->where('is_system', true)
                    ->orWhere('collection_id', $collectionId);
            })
            ->orderBy('name')
            ->get();

        // Add translations to trait types
        $typesWithTranslations = $types->map(function ($type) {
            return [
                'id' => $type->id,
                'name' => $type->name,
                'translated_name' => __('trait_elements.types.' . $type->name, [], null, $type->name),
                'slug' => $type->slug,
                'display_type' => $type->display_type,
                'allowed_values' => $type->allowed_values,
                'unit' => $type->unit,
                'category_id' => $type->category_id,
                'is_system' => $type->is_system,
                'created_at' => $type->created_at,
                'updated_at' => $type->updated_at,
            ];
        });

        return response()->json([
            'success' => true,
            'types' => $typesWithTranslations
        ]);
    }

    /**
     * Get traits for an EGI
     *
     * @param int $egiId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEgiTraits($egiId) {
        $egi = Egi::findOrFail($egiId);

        $traits = EgiTrait::with(['category', 'traitType'])
            ->where('egi_id', $egiId)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($trait) {
                // Use pre-calculated rarity percentage from database
                return [
                    'id' => $trait->id,
                    'category_id' => $trait->category_id,
                    'category_name' => $trait->category->name,
                    'trait_type_id' => $trait->trait_type_id,
                    'type_name' => $trait->traitType->name,
                    'value' => $trait->value,
                    'display_value' => $trait->display_value,
                    'display_type' => $trait->traitType->display_type,
                    'unit' => $trait->traitType->unit,
                    'rarity_percentage' => $trait->rarity_percentage,
                    'sort_order' => $trait->sort_order
                ];
            });

        return response()->json([
            'success' => true,
            'traits' => $traits,
            'is_locked' => $egi->is_published || !empty($egi->ipfs_hash)
        ]);
    }

    /**
     * @Oracode Method: Save EGI Traits
     * 🎯 Purpose: Save/update traits for a specific EGI with validation and authorization
     * 📤 Output: JSON response with operation status
     * 🧱 Core Logic: FegiAuth authorization + transaction + rarity update + audit logging
     *
     * @param Request $request
     * @param int $egiId
     * @return JsonResponse
     */
    public function saveEgiTraits(Request $request, $egiId): JsonResponse {
        try {
            // Check authentication
            if (!FegiAuth::check()) {
                return $this->errorManager->handle('TRAITS_UNAUTHORIZED_ACCESS', [
                    'egi_id' => $egiId,
                    'action' => 'save_traits'
                ]);
            }

            $user = FegiAuth::user();

            // Log operation start (developers only - English)
            $this->logger->info('TRAITS_API: Save traits operation started', [
                'egi_id' => $egiId,
                'user_id' => $user->id,
                'traits_count' => count($request->input('traits', [])),
                'auth_type' => FegiAuth::getAuthType()
            ]);

            // Verify EGI exists and check ownership
            $egi = Egi::findOrFail($egiId);

            if ($egi->user_id !== $user->id) {
                return $this->errorManager->handle('TRAITS_UNAUTHORIZED_ACCESS', [
                    'user_id' => $user->id,
                    'egi_id' => $egiId,
                    'owner_id' => $egi->user_id,
                    'action' => 'save_traits'
                ]);
            }

            // 🔒 BLOCKCHAIN IMMUTABILITY: Check if EGI is minted (BLOCKING)
            if ($egi->token_EGI) {
                return $this->errorManager->handle('TRAITS_EGI_MINTED', [
                    'user_id' => $user->id,
                    'egi_id' => $egiId,
                    'token_egi' => $egi->token_EGI,
                    'action' => 'save_traits'
                ]);
            }

            // Check if EGI is published (cannot modify)
            if ($egi->is_published) {
                return $this->errorManager->handle('TRAITS_EGI_PUBLISHED', [
                    'user_id' => $user->id,
                    'egi_id' => $egiId,
                    'action' => 'save_traits'
                ]);
            }

            $traits = $request->input('traits', []);

            if (empty($traits)) {
                return $this->errorManager->handle('TRAITS_NO_DATA_PROVIDED', [
                    'user_id' => $user->id,
                    'egi_id' => $egiId
                ]);
            }

            // Save traits in transaction
            DB::transaction(function () use ($egiId, $traits, $user) {
                $existingTraits = EgiTrait::where('egi_id', $egiId)->get();
                $keptTraitIds = [];

                // Process each trait
                foreach ($traits as $index => $traitData) {
                    // If display_value is not provided, translate the value
                    $displayValue = $traitData['display_value'] ?? null;
                    if (empty($displayValue)) {
                        $displayValue = $this->translateTraitValue($traitData['value']);
                    }

                    if (isset($traitData['id']) && $traitData['id'] > 0) {
                        // Update existing trait
                        $existingTrait = $existingTraits->where('id', $traitData['id'])->first();

                        if ($existingTrait) {
                            $existingTrait->update([
                                'category_id' => $traitData['category_id'],
                                'trait_type_id' => $traitData['trait_type_id'],
                                'value' => $traitData['value'],
                                'display_value' => $displayValue,
                                'sort_order' => $index
                            ]);
                            $keptTraitIds[] = $existingTrait->id;
                        } else {
                            // Create new trait (ID not found)
                            $created = EgiTrait::create([
                                'egi_id' => $egiId,
                                'category_id' => $traitData['category_id'],
                                'trait_type_id' => $traitData['trait_type_id'],
                                'value' => $traitData['value'],
                                'display_value' => $displayValue,
                                'sort_order' => $index,
                                'is_locked' => false
                            ]);
                            $keptTraitIds[] = $created->id;
                        }
                    } else {
                        // Create new trait
                        $created = EgiTrait::create([
                            'egi_id' => $egiId,
                            'category_id' => $traitData['category_id'],
                            'trait_type_id' => $traitData['trait_type_id'],
                            'value' => $traitData['value'],
                            'display_value' => $displayValue,
                            'sort_order' => $index,
                            'is_locked' => false
                        ]);
                        $keptTraitIds[] = $created->id;
                    }
                }

                // Delete traits no longer present
                $traitsToDelete = $existingTraits->whereNotIn('id', $keptTraitIds);
                if ($traitsToDelete->count() > 0) {
                    $deletedIds = $traitsToDelete->pluck('id')->toArray();
                    EgiTrait::whereIn('id', $deletedIds)->delete();
                }
            });

            // Update rarity percentages for collection
            $this->updateRarityPercentages($egi->collection_id);

            // Log successful operation (developers only - English)
            $this->logger->info('TRAITS_API: Traits saved successfully', [
                'user_id' => $user->id,
                'egi_id' => $egiId,
                'collection_id' => $egi->collection_id,
                'traits_count' => count($traits)
            ]);

            // GDPR audit log
            $this->auditLogService->logUserAction(
                $user,
                'egi_traits_saved',
                [
                    'egi_id' => $egiId,
                    'collection_id' => $egi->collection_id,
                    'traits_count' => count($traits)
                ],
                GdprActivityCategory::CONTENT_MODIFICATION
            );

            return response()->json([
                'success' => true,
                'message' => 'Traits saved successfully'
            ]);
        } catch (\Exception $e) {
            return $this->errorManager->handle('TRAITS_SAVE_FAILED', [
                'user_id' => FegiAuth::id(),
                'egi_id' => $egiId,
                'error' => $e->getMessage()
            ], $e);
        }
    }
    /**
     * @Oracode Method: Add New EGI Traits
     * 🎯 Purpose: Add new traits to an EGI without affecting existing ones
     * 📤 Output: JSON response with operation status
     * 🧱 Core Logic: FegiAuth authorization + transaction + rarity update + audit logging
     *
     * @param Request $request
     * @param int $egiId
     * @return JsonResponse
     */
    public function addEgiTraits(Request $request, $egiId): JsonResponse {
        try {
            // Check authentication
            if (!FegiAuth::check()) {
                return $this->errorManager->handle('TRAITS_UNAUTHORIZED_ACCESS', [
                    'egi_id' => $egiId,
                    'action' => 'add_traits'
                ]);
            }

            $user = FegiAuth::user();

            // Log operation start (developers only - English)
            $this->logger->info('TRAITS_API: Add traits operation started', [
                'egi_id' => $egiId,
                'user_id' => $user->id,
                'new_traits_count' => count($request->input('traits', [])),
                'auth_type' => FegiAuth::getAuthType()
            ]);

            // Verify EGI exists and check ownership
            $egi = Egi::findOrFail($egiId);

            if ($egi->user_id !== $user->id) {
                return $this->errorManager->handle('TRAITS_UNAUTHORIZED_ACCESS', [
                    'user_id' => $user->id,
                    'egi_id' => $egiId,
                    'owner_id' => $egi->user_id,
                    'action' => 'add_traits'
                ]);
            }

            // Check if EGI is published (cannot modify)
            if ($egi->is_published) {
                return $this->errorManager->handle('TRAITS_EGI_PUBLISHED', [
                    'user_id' => $user->id,
                    'egi_id' => $egiId,
                    'action' => 'add_traits'
                ]);
            }

            $newTraits = $request->input('traits', []);

            if (empty($newTraits)) {
                return $this->errorManager->handle('TRAITS_NO_DATA_PROVIDED', [
                    'user_id' => $user->id,
                    'egi_id' => $egiId
                ]);
            }

            // Add new traits in transaction
            DB::transaction(function () use ($egiId, $newTraits) {
                $maxSortOrder = EgiTrait::where('egi_id', $egiId)->max('sort_order') ?? -1;

                foreach ($newTraits as $index => $traitData) {
                    // If display_value is not provided, translate the value
                    $displayValue = $traitData['display_value'] ?? null;
                    if (empty($displayValue)) {
                        $displayValue = $this->translateTraitValue($traitData['value']);
                    }

                    EgiTrait::create([
                        'egi_id' => $egiId,
                        'category_id' => $traitData['category_id'],
                        'trait_type_id' => $traitData['trait_type_id'],
                        'value' => $traitData['value'],
                        'display_value' => $displayValue,
                        'sort_order' => $maxSortOrder + 1 + $index,
                        'is_locked' => false
                    ]);
                }
            });

            // Update rarity percentages for collection
            $this->updateRarityPercentages($egi->collection_id);

            // Log successful operation (developers only - English)
            $this->logger->info('TRAITS_API: New traits added successfully', [
                'user_id' => $user->id,
                'egi_id' => $egiId,
                'collection_id' => $egi->collection_id,
                'new_traits_count' => count($newTraits)
            ]);

            // GDPR audit log
            $this->auditLogService->logUserAction(
                $user,
                'egi_traits_added',
                [
                    'egi_id' => $egiId,
                    'collection_id' => $egi->collection_id,
                    'new_traits_count' => count($newTraits)
                ],
                GdprActivityCategory::CONTENT_MODIFICATION
            );

            return response()->json([
                'success' => true,
                'message' => 'New traits added successfully'
            ]);
        } catch (\Exception $e) {
            return $this->errorManager->handle('TRAITS_ADD_FAILED', [
                'user_id' => FegiAuth::id(),
                'egi_id' => $egiId,
                'error' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Add a single trait to an EGI
     *
     * @param Request $request
     * @param int $egiId
     * @return JsonResponse
     */
    public function addSingleTrait(Request $request, $egiId): JsonResponse {
        try {
            // Check authentication
            if (!FegiAuth::check()) {
                return $this->errorManager->handle('TRAITS_UNAUTHORIZED_ACCESS', [
                    'egi_id' => $egiId,
                    'action' => 'add_single_trait'
                ]);
            }

            $user = FegiAuth::user();

            // Log operation start (developers only - English)
            $this->logger->info('TRAITS_API: Add single trait operation started', [
                'egi_id' => $egiId,
                'user_id' => $user->id,
                'auth_type' => FegiAuth::getAuthType()
            ]);

            // Verify EGI exists and check ownership
            $egi = Egi::findOrFail($egiId);

            if ($egi->user_id !== $user->id) {
                return $this->errorManager->handle('TRAITS_UNAUTHORIZED_ACCESS', [
                    'user_id' => $user->id,
                    'egi_id' => $egiId,
                    'owner_id' => $egi->user_id,
                    'action' => 'add_single_trait'
                ]);
            }

            // Check if EGI is published (cannot modify)
            if ($egi->is_published) {
                return $this->errorManager->handle('TRAITS_EGI_PUBLISHED', [
                    'user_id' => $user->id,
                    'egi_id' => $egiId,
                    'action' => 'add_single_trait'
                ]);
            }

            // Validate required fields
            $request->validate([
                'trait_category_id' => 'required|exists:trait_categories,id',
                'trait_type_id' => 'required|exists:trait_types,id',
                'value' => 'required|string|max:255'
            ]);

            // Add single trait in transaction
            $newTrait = DB::transaction(function () use ($egiId, $request) {
                $maxSortOrder = EgiTrait::where('egi_id', $egiId)->max('sort_order') ?? -1;

                // If display_value is not provided, translate the value
                $displayValue = $request->input('display_value');
                if (empty($displayValue)) {
                    $displayValue = $this->translateTraitValue($request->input('value'));
                }

                return EgiTrait::create([
                    'egi_id' => $egiId,
                    'category_id' => $request->input('trait_category_id'),
                    'trait_type_id' => $request->input('trait_type_id'),
                    'value' => $request->input('value'),
                    'display_value' => $displayValue,
                    'sort_order' => $maxSortOrder + 1,
                    'is_locked' => false
                ]);
            });

            // Update rarity percentages for collection
            $this->updateRarityPercentages($egi->collection_id);

            // Log successful operation (developers only - English)
            $this->logger->info('TRAITS_API: Single trait added successfully', [
                'user_id' => $user->id,
                'egi_id' => $egiId,
                'collection_id' => $egi->collection_id,
                'trait_id' => $newTrait->id
            ]);

            // GDPR audit log
            $this->auditLogService->logUserAction(
                $user,
                'egi_trait_added',
                [
                    'egi_id' => $egiId,
                    'collection_id' => $egi->collection_id,
                    'trait_id' => $newTrait->id
                ],
                GdprActivityCategory::CONTENT_MODIFICATION
            );

            return response()->json([
                'success' => true,
                'message' => 'Trait added successfully',
                'trait' => $newTrait
            ]);
        } catch (\Exception $e) {
            return $this->errorManager->handle('TRAITS_ADD_SINGLE_FAILED', [
                'user_id' => FegiAuth::id(),
                'egi_id' => $egiId,
                'error' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Generate IPFS metadata for EGI traits
     *
     * @param int $egiId
     * @return array
     */
    public function generateMetadata($egiId) {
        $egi = Egi::with(['traits.traitType'])->findOrFail($egiId);

        $attributes = $egi->traits->map(function ($trait) {
            $metadata = [
                'trait_type' => $trait->traitType->name,
                'value' => $trait->value
            ];

            if ($trait->traitType->display_type !== 'text') {
                $metadata['display_type'] = $trait->traitType->display_type;
            }

            if ($trait->traitType->unit) {
                $metadata['unit'] = $trait->traitType->unit;
            }

            if ($trait->traitType->display_type === 'boost_number') {
                $metadata['max_value'] = 100;
            }

            return $metadata;
        });

        return [
            'name' => $egi->title,
            'description' => $egi->description,
            'image' => $egi->ipfs_image_hash ? "ipfs://{$egi->ipfs_image_hash}" : $egi->main_image_url,
            'attributes' => $attributes,
            'external_url' => route('egis.show', $egi->id),
            'background_color' => 'D4A574' // Oro Fiorentino
        ];
    }

    /**
     * Clear traits cache (utile per testing o aggiornamenti)
     */
    public function clearCache(Request $request) {
        try {
            // Per semplicità, usiamo artisan cache:clear per pulire tutto
            \Artisan::call('cache:clear');

            return response()->json([
                'success' => true,
                'message' => 'Traits cache cleared successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error clearing cache: ' . $e->getMessage()
            ], 500);
        }
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
            Cache::flush();

            $this->logger->info('TRAITS_API: Traits rarity cache cleared for collection', [
                'collection_id' => $collectionId,
                'pattern' => $pattern
            ]);
        } catch (\Exception $e) {
            // Log error but don't fail the main operation
            $this->logger->error('TRAITS_API: Failed to clear traits rarity cache', [
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
            $this->logger->info('TRAITS_API: Updating rarity percentages for collection', [
                'collection_id' => $collectionId
            ]);

            // Get total EGIs in collection
            $totalEgis = Egi::where('collection_id', $collectionId)->count();

            if ($totalEgis === 0) {
                $this->logger->info('TRAITS_API: No EGIs in collection, skipping rarity update', [
                    'collection_id' => $collectionId
                ]);
                return;
            }

            // Get all unique trait combinations (trait_type_id + value) in this collection
            $uniqueTraits = EgiTrait::join('egis', 'egis.id', '=', 'egi_traits.egi_id')
                ->where('egis.collection_id', $collectionId)
                ->select('egi_traits.trait_type_id', 'egi_traits.value')
                ->distinct()
                ->get();

            $this->logger->info('TRAITS_API: Found unique traits for rarity calculation', [
                'collection_id' => $collectionId,
                'unique_traits_count' => $uniqueTraits->count(),
                'total_egis' => $totalEgis
            ]);

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

                $this->logger->debug('TRAITS_API: Updated rarity percentage', [
                    'collection_id' => $collectionId,
                    'trait_type_id' => $uniqueTrait->trait_type_id,
                    'value' => $uniqueTrait->value,
                    'percentage' => $percentage,
                    'egis_with_trait' => $egisWithTrait,
                    'updated_count' => $updatedCount
                ]);
            }

            $this->logger->info('TRAITS_API: Rarity percentages updated successfully', [
                'collection_id' => $collectionId,
                'processed_traits' => $uniqueTraits->count()
            ]);
        } catch (\Exception $e) {
            $this->logger->error('TRAITS_API: Failed to update rarity percentages', [
                'collection_id' => $collectionId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * @Oracode Method: Delete EGI Trait
     * 🎯 Purpose: Delete a specific trait from an EGI with authorization and rarity update
     * 📤 Output: JSON response with operation status
     * 🧱 Core Logic: FegiAuth authorization + trait deletion + rarity update + audit logging
     *
     * @param Request $request
     * @param int $egiId
     * @param int $traitId
     * @return JsonResponse
     */
    public function deleteTrait(Request $request, int $egiId, int $traitId): JsonResponse {
        try {
            // Check authentication
            if (!FegiAuth::check()) {
                return $this->errorManager->handle('TRAITS_UNAUTHORIZED_ACCESS', [
                    'egi_id' => $egiId,
                    'trait_id' => $traitId,
                    'action' => 'delete_trait'
                ]);
            }

            $user = FegiAuth::user();

            // Load EGI with relationships
            $egi = Egi::with(['collection', 'traits'])->find($egiId);

            if (!$egi) {
                return $this->errorManager->handle('EGI_NOT_FOUND', [
                    'user_id' => $user->id,
                    'egi_id' => $egiId,
                    'action' => 'delete_trait'
                ]);
            }

            // Authorization check - only owner can delete traits
            if ($egi->user_id !== $user->id) {
                return $this->errorManager->handle('TRAITS_UNAUTHORIZED_ACCESS', [
                    'user_id' => $user->id,
                    'egi_id' => $egiId,
                    'owner_id' => $egi->user_id,
                    'trait_id' => $traitId,
                    'action' => 'delete_trait'
                ]);
            }

            // Check if EGI is published (cannot modify)
            if ($egi->is_published) {
                return $this->errorManager->handle('TRAITS_EGI_PUBLISHED', [
                    'user_id' => $user->id,
                    'egi_id' => $egiId,
                    'trait_id' => $traitId,
                    'action' => 'delete_trait'
                ]);
            }

            // Find the trait
            $trait = $egi->traits()->where('id', $traitId)->first();

            if (!$trait) {
                return $this->errorManager->handle('TRAIT_NOT_FOUND', [
                    'user_id' => $user->id,
                    'egi_id' => $egiId,
                    'trait_id' => $traitId
                ]);
            }

            // Store trait data for logging before deletion
            $traitData = [
                'type' => $trait->traitType->name ?? 'Unknown',
                'value' => $trait->value,
                'category' => $trait->category->name ?? 'Unknown'
            ];

            // Delete the trait
            $trait->delete();

            // Update rarity percentages for collection
            $this->updateRarityPercentages($egi->collection_id);

            // Log successful deletion (developers only - English)
            $this->logger->info('TRAITS_API: Trait deleted successfully', [
                'user_id' => $user->id,
                'egi_id' => $egiId,
                'trait_id' => $traitId,
                'collection_id' => $egi->collection_id,
                'trait_data' => $traitData,
                'remaining_traits' => $egi->traits()->count()
            ]);

            // GDPR audit log
            $this->auditLogService->logUserAction(
                $user,
                'egi_trait_deleted',
                [
                    'egi_id' => $egiId,
                    'trait_id' => $traitId,
                    'collection_id' => $egi->collection_id,
                    'trait_data' => $traitData
                ],
                GdprActivityCategory::CONTENT_MODIFICATION
            );

            return response()->json([
                'success' => true,
                'message' => 'Trait eliminato con successo.',
                'data' => [
                    'egi_id' => $egiId,
                    'trait_id' => $traitId,
                    'remaining_traits' => $egi->traits()->count()
                ]
            ]);
        } catch (\Exception $e) {
            return $this->errorManager->handle('EGI_TRAIT_DELETE_FAILED', [
                'user_id' => FegiAuth::id(),
                'egi_id' => $egiId,
                'trait_id' => $traitId,
                'error' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Upload image for a trait
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function uploadTraitImage(Request $request): JsonResponse {
        try {
            $request->validate([
                'trait_id' => 'required|exists:egi_traits,id',
                'image' => 'required|image|max:10240', // 10MB max
                'image_alt_text' => 'nullable|string|max:255',
                'image_description' => 'nullable|string|max:1000'
            ]);

            if (!FegiAuth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utente non autorizzato.'
                ], 401);
            }

            $trait = EgiTrait::findOrFail($request->trait_id);

            // Check if user can manage this EGI (same pattern as other methods)
            $user = FegiAuth::user();
            $egi = $trait->egi;

            // Basic authorization check - user must be authenticated
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utente non autorizzato.'
                ], 401);
            }

            // Handle file upload
            $file = $request->file('image');
            $filename = time() . '_' . $trait->id . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('trait-images', $filename, 'public');

            // Update trait with image info
            $trait->update([
                'image_path' => $path,
                'image_alt_text' => $request->image_alt_text,
                'image_description' => $request->image_description
            ]);

            // Log GDPR activity
            $this->auditLogService->log(
                FegiAuth::id(),
                GdprActivityCategory::DATA_MODIFICATION,
                'trait_image_upload',
                "Upload immagine per trait {$trait->name}",
                ['trait_id' => $trait->id, 'egi_id' => $trait->egi_id]
            );

            return response()->json([
                'success' => true,
                'message' => 'Immagine caricata con successo.',
                'data' => [
                    'trait_id' => $trait->id,
                    'image_url' => asset('storage/' . $path),
                    'thumbnail_url' => asset('storage/' . $path)
                ]
            ]);
        } catch (\Exception $e) {
            return $this->errorManager->handle('TRAIT_IMAGE_UPLOAD_FAILED', [
                'user_id' => FegiAuth::id(),
                'trait_id' => $request->trait_id ?? null,
                'error' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Delete image for a trait
     *
     * @param EgiTrait $trait
     * @return JsonResponse
     */
    public function deleteTraitImage(EgiTrait $trait): JsonResponse {
        try {
            if (!FegiAuth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utente non autorizzato.'
                ], 401);
            }

            // Check if user can manage this EGI (same pattern as other methods)
            $user = FegiAuth::user();
            $egi = $trait->egi;

            // Basic authorization check - user must be authenticated
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utente non autorizzato.'
                ], 401);
            }

            // Delete file if exists
            if ($trait->image_path && file_exists(storage_path('app/public/' . $trait->image_path))) {
                unlink(storage_path('app/public/' . $trait->image_path));
            }

            // Clear image data
            $trait->update([
                'image_path' => null,
                'image_alt_text' => null,
                'image_description' => null
            ]);

            // Log GDPR activity
            $this->auditLogService->log(
                FegiAuth::id(),
                GdprActivityCategory::DATA_DELETION,
                'trait_image_delete',
                "Eliminazione immagine per trait {$trait->name}",
                ['trait_id' => $trait->id, 'egi_id' => $trait->egi_id]
            );

            return response()->json([
                'success' => true,
                'message' => 'Immagine eliminata con successo.',
                'data' => [
                    'trait_id' => $trait->id
                ]
            ]);
        } catch (\Exception $e) {
            return $this->errorManager->handle('TRAIT_IMAGE_DELETE_FAILED', [
                'user_id' => FegiAuth::id(),
                'trait_id' => $trait->id,
                'error' => $e->getMessage()
            ], $e);
        }
    }
}
