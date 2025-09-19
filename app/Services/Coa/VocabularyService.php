<?php

namespace App\Services\Coa;

use App\Models\VocabularyTerm;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Services\Gdpr\AuditLogService;
use App\Enums\Gdpr\GdprActivityCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * @Oracode Service: CoA Pro Vocabulary Management
 * 🎯 Purpose: Manage CoA-specific vocabulary terms for artwork traits
 * 🛡️ Privacy: Handles GDPR-compliant vocabulary data access with full audit trail
 * 🧱 Core Logic: Manages controlled vocabulary for CoA certificates with i18n support
 *
 * @package App\Services\Coa
 * @author AI Assistant following FlorenceEGI patterns
 * @version 1.0.0 (CoA Pro System)
 * @date 2025-09-19
 * @purpose Professional vocabulary management for CoA traits system
 */
class VocabularyService {
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
    }

    /**
     * Get vocabulary terms by category with pagination and translations
     *
     * @param string $category The vocabulary category
     * @param int $perPage Number of items per page
     * @param string|null $locale Override locale for translations
     * @return LengthAwarePaginator
     * @privacy-safe Returns public vocabulary data only
     *
     * @oracode-dimension governance
     * @value-flow Provides structured vocabulary for CoA traits
     * @community-impact Enables standardized artwork description
     * @transparency-level High - complete vocabulary transparency
     * @narrative-coherence Links vocabulary to CoA certification
     */
    public function getTermsByCategory(string $category, int $perPage = 20, ?string $locale = null): LengthAwarePaginator {
        try {
            $this->logger->info('[Vocabulary Service] Retrieving terms by category', [
                'category' => $category,
                'per_page' => $perPage,
                'locale' => $locale ?? app()->getLocale(),
                'user_id' => Auth::id(),
                'ip_address' => request()->ip()
            ]);

            // Validate category
            $validCategories = ['technique', 'materials', 'support'];
            if (!in_array($category, $validCategories)) {
                $this->errorManager->handle('VOCABULARY_INVALID_CATEGORY', [
                    'category' => $category,
                    'valid_categories' => $validCategories,
                    'user_id' => Auth::id(),
                    'timestamp' => now()->toIso8601String()
                ], new \InvalidArgumentException('Invalid vocabulary category'));

                throw new \InvalidArgumentException('Invalid vocabulary category provided.');
            }

            // Get paginated terms with translations
            $terms = VocabularyTerm::byCategory($category)
                ->orderBy('slug')
                ->paginate($perPage);

            // Transform with translations
            $terms->getCollection()->transform(function ($term) use ($locale) {
                return $this->transformTermWithTranslation($term, $locale);
            });

            // Log audit trail for GDPR compliance
            $this->auditService->logUserAction(
                Auth::user(),
                'vocabulary_category_accessed',
                [
                    'category' => $category,
                    'terms_count' => $terms->total(),
                    'locale' => $locale ?? app()->getLocale(),
                    'page' => $terms->currentPage()
                ],
                GdprActivityCategory::PLATFORM_USAGE
            );

            $this->logger->info('[Vocabulary Service] Terms retrieved successfully', [
                'category' => $category,
                'total_terms' => $terms->total(),
                'current_page' => $terms->currentPage(),
                'user_id' => Auth::id()
            ]);

            return $terms;
        } catch (\Exception $e) {
            $this->errorManager->handle('VOCABULARY_CATEGORY_RETRIEVAL_ERROR', [
                'category' => $category,
                'per_page' => $perPage,
                'locale' => $locale,
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'ip_address' => request()->ip(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            throw $e;
        }
    }

    /**
     * Get all vocabulary categories with counts
     *
     * @param string|null $locale Override locale for translations
     * @return array
     * @privacy-safe Returns public vocabulary metadata only
     *
     * @oracode-dimension governance
     * @value-flow Provides vocabulary structure overview
     * @community-impact Enables vocabulary navigation
     * @transparency-level High - complete category transparency
     * @narrative-coherence Links categories to CoA traits system
     */
    public function getCategories(?string $locale = null): array {
        try {
            $this->logger->info('[Vocabulary Service] Retrieving categories', [
                'locale' => $locale ?? app()->getLocale(),
                'user_id' => Auth::id(),
                'ip_address' => request()->ip()
            ]);

            $categories = VocabularyTerm::selectRaw('category, COUNT(*) as count')
                ->groupBy('category')
                ->orderBy('category')
                ->get()
                ->map(function ($item) use ($locale) {
                    return [
                        'category' => $item->category,
                        'count' => $item->count,
                        'name' => __("coa_vocabulary.category_{$item->category}", [], $locale ?? app()->getLocale()),
                        'description' => __("coa_vocabulary.category_{$item->category}_description", [], $locale ?? app()->getLocale())
                    ];
                })
                ->toArray();

            // Log audit trail
            $this->auditService->logUserAction(
                Auth::user(),
                'vocabulary_categories_accessed',
                [
                    'categories_count' => count($categories),
                    'locale' => $locale ?? app()->getLocale()
                ],
                GdprActivityCategory::PLATFORM_USAGE
            );

            $this->logger->info('[Vocabulary Service] Categories retrieved successfully', [
                'categories_count' => count($categories),
                'user_id' => Auth::id()
            ]);

            return $categories;
        } catch (\Exception $e) {
            $this->errorManager->handle('VOCABULARY_CATEGORIES_RETRIEVAL_ERROR', [
                'locale' => $locale,
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'ip_address' => request()->ip(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            throw $e;
        }
    }

    /**
     * Search vocabulary terms across all categories
     *
     * @param string $query Search query
     * @param int $perPage Number of items per page
     * @param string|null $category Filter by category
     * @param string|null $locale Override locale for translations
     * @return LengthAwarePaginator
     * @privacy-safe Returns public vocabulary data only
     *
     * @oracode-dimension governance
     * @value-flow Enables vocabulary discovery and search
     * @community-impact Facilitates artwork trait selection
     * @transparency-level High - complete search transparency
     * @narrative-coherence Links search to CoA trait selection
     */
    public function searchTerms(string $query, int $perPage = 20, ?string $category = null, ?string $locale = null): LengthAwarePaginator {
        try {
            $this->logger->info('[Vocabulary Service] Searching terms', [
                'query' => $query,
                'category' => $category,
                'per_page' => $perPage,
                'locale' => $locale ?? app()->getLocale(),
                'user_id' => Auth::id(),
                'ip_address' => request()->ip()
            ]);

            // Build search query
            $searchQuery = VocabularyTerm::search($query);

            // Apply category filter if provided
            if ($category) {
                $searchQuery->where('category', $category);
            }

            // Get paginated results
            $terms = $searchQuery->orderBy('slug')->paginate($perPage);

            // Transform with translations
            $terms->getCollection()->transform(function ($term) use ($locale) {
                return $this->transformTermWithTranslation($term, $locale);
            });

            // Log audit trail
            $this->auditService->logUserAction(
                Auth::user(),
                'vocabulary_search_performed',
                [
                    'query' => $query,
                    'category' => $category,
                    'results_count' => $terms->total(),
                    'locale' => $locale ?? app()->getLocale()
                ],
                GdprActivityCategory::PLATFORM_USAGE
            );

            $this->logger->info('[Vocabulary Service] Search completed successfully', [
                'query' => $query,
                'category' => $category,
                'results_count' => $terms->total(),
                'user_id' => Auth::id()
            ]);

            return $terms;
        } catch (\Exception $e) {
            $this->errorManager->handle('VOCABULARY_SEARCH_ERROR', [
                'query' => $query,
                'category' => $category,
                'per_page' => $perPage,
                'locale' => $locale,
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'ip_address' => request()->ip(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            throw $e;
        }
    }

    /**
     * Get vocabulary term by slug
     *
     * @param string $slug Term slug
     * @param string|null $locale Override locale for translations
     * @return VocabularyTerm|null
     * @privacy-safe Returns public vocabulary data only
     *
     * @oracode-dimension governance
     * @value-flow Provides detailed term information
     * @community-impact Enables term-specific information access
     * @transparency-level High - complete term transparency
     * @narrative-coherence Links specific terms to CoA traits
     */
    public function getTermBySlug(string $slug, ?string $locale = null): ?VocabularyTerm {
        try {
            $this->logger->info('[Vocabulary Service] Retrieving term by slug', [
                'slug' => $slug,
                'locale' => $locale ?? app()->getLocale(),
                'user_id' => Auth::id(),
                'ip_address' => request()->ip()
            ]);

            $term = VocabularyTerm::where('slug', $slug)->first();

            if (!$term) {
                $this->logger->warning('[Vocabulary Service] Term not found', [
                    'slug' => $slug,
                    'user_id' => Auth::id()
                ]);
                return null;
            }

            // Transform with translations
            $transformedTerm = $this->transformTermWithTranslation($term, $locale);

            // Log audit trail
            $this->auditService->logUserAction(
                Auth::user(),
                'vocabulary_term_accessed',
                [
                    'slug' => $slug,
                    'category' => $term->category,
                    'locale' => $locale ?? app()->getLocale()
                ],
                GdprActivityCategory::PLATFORM_USAGE
            );

            return $transformedTerm;
        } catch (\Exception $e) {
            $this->errorManager->handle('VOCABULARY_TERM_RETRIEVAL_ERROR', [
                'slug' => $slug,
                'locale' => $locale,
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'ip_address' => request()->ip(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            throw $e;
        }
    }

    /**
     * Get grouped vocabulary terms (all categories)
     *
     * @param string|null $locale Override locale for translations
     * @return array
     * @privacy-safe Returns public vocabulary data only
     *
     * @oracode-dimension governance
     * @value-flow Provides complete vocabulary structure
     * @community-impact Enables comprehensive trait selection
     * @transparency-level High - complete vocabulary transparency
     * @narrative-coherence Links complete vocabulary to CoA system
     */
    public function getGroupedTerms(?string $locale = null): array {
        try {
            $this->logger->info('[Vocabulary Service] Retrieving grouped terms', [
                'locale' => $locale ?? app()->getLocale(),
                'user_id' => Auth::id(),
                'ip_address' => request()->ip()
            ]);

            $grouped = VocabularyTerm::orderBy('category')->orderBy('slug')->get()
                ->groupBy('category')
                ->map(function ($terms, $category) use ($locale) {
                    return [
                        'category' => $category,
                        'name' => __("coa_vocabulary.category_{$category}", [], $locale ?? app()->getLocale()),
                        'count' => $terms->count(),
                        'terms' => $terms->map(function ($term) use ($locale) {
                            return $this->transformTermWithTranslation($term, $locale);
                        })->values()
                    ];
                })
                ->values()
                ->toArray();

            // Log audit trail
            $totalTerms = collect($grouped)->sum('count');
            $this->auditService->logUserAction(
                Auth::user(),
                'vocabulary_grouped_accessed',
                [
                    'categories_count' => count($grouped),
                    'total_terms' => $totalTerms,
                    'locale' => $locale ?? app()->getLocale()
                ],
                GdprActivityCategory::PLATFORM_USAGE
            );

            $this->logger->info('[Vocabulary Service] Grouped terms retrieved successfully', [
                'categories_count' => count($grouped),
                'total_terms' => $totalTerms,
                'user_id' => Auth::id()
            ]);

            return $grouped;
        } catch (\Exception $e) {
            $this->errorManager->handle('VOCABULARY_GROUPED_RETRIEVAL_ERROR', [
                'locale' => $locale,
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'ip_address' => request()->ip(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            throw $e;
        }
    }

    /**
     * Transform vocabulary term with translations
     *
     * @param VocabularyTerm $term
     * @param string|null $locale
     * @return VocabularyTerm
     * @privacy-safe Handles only public vocabulary data
     */
    private function transformTermWithTranslation(VocabularyTerm $term, ?string $locale = null): VocabularyTerm {
        $currentLocale = $locale ?? app()->getLocale();

        // Add translated fields
        $term->name = __("coa_vocabulary.{$term->slug}", [], $currentLocale);

        // Get professional description - all terms now have specific descriptions
        $descriptionKey = "coa_vocabulary.{$term->slug}_description";
        $term->description = __($descriptionKey, [], $currentLocale);

        // Add locale metadata
        $term->locale = $currentLocale;
        $term->original_slug = $term->slug;

        return $term;
    }
}