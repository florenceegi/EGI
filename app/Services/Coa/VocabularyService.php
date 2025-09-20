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
                ], new \Exception('Invalid vocabulary category'));

                throw new \Exception('Invalid vocabulary category provided.');
            }

            // Get ALL terms for category (no pagination for grouped display)
            // Order by ui_group first, then by sort_order
            $allTerms = VocabularyTerm::byCategory($category)
                ->orderBy('ui_group')
                ->orderBy('sort_order')
                ->get();

            // Transform with translations
            $transformedTerms = $allTerms->transform(function ($term) use ($locale) {
                return $this->transformTermWithTranslation($term, $locale);
            });

            // Create a fake pagination object to maintain API compatibility
            // but with all terms included
            $terms = new \Illuminate\Pagination\LengthAwarePaginator(
                $transformedTerms,
                $transformedTerms->count(),
                $transformedTerms->count(), // Show all terms on one "page"
                1, // Current page
                ['path' => request()->url(), 'pageName' => 'page']
            );

            // Log audit trail for GDPR compliance (only if user is authenticated)
            if (Auth::check()) {
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
            }

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

            // Log audit trail (only if user is authenticated)
            if (Auth::check()) {
                $this->auditService->logUserAction(
                    Auth::user(),
                    'vocabulary_categories_accessed',
                    [
                        'categories_count' => count($categories),
                        'locale' => $locale ?? app()->getLocale()
                    ],
                    GdprActivityCategory::PLATFORM_USAGE
                );
            }

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
    /**
     * Search vocabulary terms with translation support (Collection version)
     * RIFATTO DA CAPO - LOGICA SEMPLICE E FUNZIONANTE
     */
    public function searchTermsCollection(string $query, ?string $category = null, ?string $locale = null): \Illuminate\Support\Collection {

        $currentLocale = $locale ?? app()->getLocale();

        $this->logger->info('[Vocabulary Service] Simple search', [
            'query' => $query,
            'category' => $category,
            'locale' => $currentLocale
        ]);

        // PASSO 1: Prendo TUTTI i termini dal database (con eventuale filtro categoria)
        $allTermsQuery = VocabularyTerm::query();
        if ($category) {
            $allTermsQuery->where('category', $category);
        }
        $allTerms = $allTermsQuery->get();

        // PASSO 2: Per ogni termine, controllo se la query è contenuta nelle traduzioni locali
        $matchingTerms = collect();

        foreach ($allTerms as $term) {
            $translationKey = "coa_vocabulary.{$term->slug}";
            $descriptionKey = "coa_vocabulary.{$term->slug}_description";

            $translatedName = __($translationKey, [], $currentLocale);
            $translatedDescription = __($descriptionKey, [], $currentLocale);

            // Controllo se la query è contenuta nel nome tradotto O nella descrizione tradotta
            $foundInName = stripos($translatedName, $query) !== false;
            $foundInDescription = stripos($translatedDescription, $query) !== false;

            // Se trovo match, aggiungo il termine
            if ($foundInName || $foundInDescription) {
                $transformedTerm = $this->transformTermWithTranslation($term, $currentLocale);
                $matchingTerms->push($transformedTerm);
            }
        }

        $this->logger->info('[Vocabulary Service] Search completed', [
            'query' => $query,
            'total_checked' => $allTerms->count(),
            'matches_found' => $matchingTerms->count()
        ]);

        return $matchingTerms;
    }

    public function searchTerms(string $query, int $perPage = 20, ?string $category = null, ?string $locale = null): LengthAwarePaginator {
        try {
            $currentLocale = $locale ?? app()->getLocale();

            $this->logger->info('[Vocabulary Service] Searching terms', [
                'query' => $query,
                'category' => $category,
                'per_page' => $perPage,
                'locale' => $currentLocale,
                'user_id' => Auth::id(),
                'ip_address' => request()->ip()
            ]);

            // Strategy 1: Search in translated terms for the current locale
            $translatedResults = $this->searchInTranslations($query, $category, $currentLocale);

            // Strategy 2: Search in original database fields (slug, etc.)
            $originalResults = $this->searchInOriginalFields($query, $category);

            // Merge results, prioritizing translated matches
            $allResults = $translatedResults->merge($originalResults)->unique('id');

            // Transform with translations BEFORE pagination
            $transformedResults = $allResults->map(function ($term) use ($currentLocale) {
                if ($term instanceof VocabularyTerm) {
                    return $this->transformTermWithTranslation($term, $currentLocale);
                }
                return $term;
            });

            // Create pagination AFTER transformation
            $terms = new \Illuminate\Pagination\LengthAwarePaginator(
                $transformedResults->take($perPage)->values(),
                $transformedResults->count(),
                $perPage,
                1,
                ['path' => request()->url(), 'pageName' => 'page']
            );

            // Log audit trail
            if (Auth::user()) {
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
            }

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

            // Log audit trail (only if user is authenticated)
            if (Auth::check()) {
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
            }

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

            // Log audit trail (only if user is authenticated)
            $totalTerms = collect($grouped)->sum('count');
            if (Auth::check()) {
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
            }

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
     * @return object
     * @privacy-safe Handles only public vocabulary data
     */
    private function transformTermWithTranslation(VocabularyTerm $term, ?string $locale = null): object {
        $currentLocale = $locale ?? app()->getLocale();

        // Get translated name and description
        $translationKey = "coa_vocabulary.{$term->slug}";
        $descriptionKey = "coa_vocabulary.{$term->slug}_description";

        $translatedName = __($translationKey, [], $currentLocale);
        $translatedDescription = __($descriptionKey, [], $currentLocale);

        // If translation is missing (returns the key), use the slug as fallback
        // NEVER use $term->name because that field doesn't exist!
        $finalName = ($translatedName === $translationKey) ? $term->slug : $translatedName;
        $finalDescription = ($translatedDescription === $descriptionKey) ? null : $translatedDescription;

        // Create object with all necessary properties for the view
        $transformedTerm = new \stdClass();
        $transformedTerm->id = $term->id;
        $transformedTerm->slug = $term->slug;
        $transformedTerm->key = $term->slug; // Legacy compatibility
        $transformedTerm->name = $finalName;
        $transformedTerm->description = $finalDescription;
        $transformedTerm->category = $term->category;
        $transformedTerm->ui_group = $term->ui_group;
        $transformedTerm->sort_order = $term->sort_order;
        $transformedTerm->locale = $currentLocale;

        return $transformedTerm;
    }

    /**
     * Search in translated terms by checking translation files
     */
    private function searchInTranslations(string $query, ?string $category, string $locale): \Illuminate\Support\Collection {
        // Get all translation keys that match the search query
        $matchingSlugs = [];

        // Get all vocabulary terms to check their translations
        $allTermsQuery = VocabularyTerm::query();
        if ($category) {
            $allTermsQuery->where('category', $category);
        }
        $allTerms = $allTermsQuery->get();

        foreach ($allTerms as $term) {
            $translatedName = __("coa_vocabulary.{$term->slug}", [], $locale);
            $translatedDescription = __("coa_vocabulary.{$term->slug}_description", [], $locale);

            // Check if query matches translated name or description (case insensitive)
            if (
                stripos($translatedName, $query) !== false ||
                stripos($translatedDescription, $query) !== false
            ) {
                $matchingSlugs[] = $term->slug;
            }
        }

        // Return terms that match in translations
        $matchingTermsQuery = VocabularyTerm::whereIn('slug', $matchingSlugs);
        if ($category) {
            $matchingTermsQuery->where('category', $category);
        }

        return $matchingTermsQuery->get();
    }

    /**
     * Search in original database fields (slug, etc.)
     */
    private function searchInOriginalFields(string $query, ?string $category): \Illuminate\Support\Collection {
        $searchQuery = VocabularyTerm::where(function ($q) use ($query) {
            $q->where('slug', 'LIKE', "%{$query}%")
                ->orWhere('slug', 'LIKE', "%" . str_replace(' ', '-', strtolower($query)) . "%")
                ->orWhere('slug', 'LIKE', "%" . str_replace(' ', '_', strtolower($query)) . "%");
        });

        if ($category) {
            $searchQuery->where('category', $category);
        }

        return $searchQuery->get();
    }
}
