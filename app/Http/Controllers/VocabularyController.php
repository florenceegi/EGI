<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\Coa\VocabularyService;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Services\Gdpr\AuditLogService;
use App\Enums\Gdpr\GdprActivityCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * @Oracode Controller: CoA Pro Vocabulary Web Controller
 * 🎯 Purpose: Provide web endpoints for internal CoA traits modal system
 * 🛡️ Privacy: GDPR-compliant vocabulary access with full audit trail
 * 🧱 Core Logic: Coordinates VocabularyService for traits modal integration
 *
 * @package App\Http\Controllers
 * @author AI Assistant following FlorenceEGI patterns
 * @version 2.0.0 (Internal Web System)
 * @date 2025-09-19
 * @purpose Internal web controller for CoA traits modal
 */
class VocabularyController extends Controller {
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
     * Vocabulary management service
     * @var VocabularyService
     */
    protected VocabularyService $vocabularyService;

    /**
     * Constructor with dependency injection
     *
     * @param UltraLogManager $logger
     * @param ErrorManagerInterface $errorManager
     * @param AuditLogService $auditService
     * @param VocabularyService $vocabularyService
     * @privacy-safe All injected services handle GDPR compliance
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService,
        VocabularyService $vocabularyService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditService = $auditService;
        $this->vocabularyService = $vocabularyService;

        // Apply web middleware for authenticated EGI management
        $this->middleware('auth')->except(['getCategories', 'getByCategory', 'search']);
    }

    /**
     * Get vocabulary categories for traits modal
     *
     * @param Request $request
     * @return View
     * @privacy-safe Returns public vocabulary metadata only
     *
     * @oracode-dimension governance
     * @value-flow Provides category tabs for traits modal
     * @community-impact Enables vocabulary navigation in CRUD interface
     * @transparency-level High - complete category transparency
     * @narrative-coherence Links categories to CoA traits system
     */
    public function getCategories(Request $request) {
        try {
            $this->logger->info('[Vocabulary Web] Categories requested for modal', [
                'user_id' => Auth::id(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'locale' => app()->getLocale(),
                'is_ajax' => $request->ajax()
            ]);

            $locale = $request->get('locale', app()->getLocale());
            $categories = $this->vocabularyService->getCategories($locale);

            $this->auditService->logUserAction(
                Auth::user(),
                'vocabulary_categories_accessed',
                [
                    'categories_count' => count($categories),
                    'locale' => $locale,
                    'access_type' => 'modal_interface'
                ],
                GdprActivityCategory::DATA_ACCESS
            );

            $this->logger->info('[Vocabulary Web] Categories retrieved for modal', [
                'categories_count' => count($categories),
                'locale' => $locale,
                'user_id' => Auth::id()
            ]);

            // Return view for both AJAX and regular HTTP requests
            // The modal system expects HTML, not JSON
            return view('components.coa.vocabulary-categories', [
                'categories' => collect($categories),
                'locale' => $locale
            ]);
        } catch (\Exception $e) {
            $this->errorManager->handle('VOCABULARY_WEB_CATEGORIES_ERROR', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'ip_address' => $request->ip(),
                'locale' => $request->get('locale'),
                'timestamp' => now()->toIso8601String()
            ], $e);

            // Return view error for both AJAX and regular HTTP requests
            return view('components.coa.vocabulary-error', [
                'error' => 'Errore nel caricamento delle categorie'
            ]);
        }
    }

    /**
     * Get vocabulary terms by category for traits modal
     *
     * @param Request $request
     * @param string $category
     * @return View
     * @privacy-safe Returns public vocabulary data only
     */
    public function getByCategory(Request $request, string $category) {
        try {
            $validator = Validator::make(array_merge($request->all(), ['category' => $category]), [
                'category' => 'required|string|in:technique,materials,support',
                'search' => 'sometimes|string|max:100',
                'locale' => 'sometimes|string|size:2'
            ]);

            if ($validator->fails()) {
                if ($request->ajax() || $request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Parametri non validi per la categoria',
                        'errors' => $validator->errors()
                    ], 422);
                }

                return view('components.coa.vocabulary-error', [
                    'error' => 'Parametri non validi per la categoria'
                ]);
            }

            $this->logger->info('[Vocabulary Web] Category terms requested for modal', [
                'category' => $category,
                'search' => $request->get('search'),
                'user_id' => Auth::id(),
                'ip_address' => $request->ip(),
                'is_ajax' => $request->ajax()
            ]);

            $search = $request->get('search');
            $locale = $request->get('locale', app()->getLocale());
            $perPage = $request->get('per_page', 20);

            // Use search if provided, otherwise get all terms for category
            if (!empty($search)) {
                $terms = $this->vocabularyService->searchTerms($search, $perPage, $category, $locale);
            } else {
                $terms = $this->vocabularyService->getTermsByCategory($category, $perPage, $locale);
            }

            $this->auditService->logUserAction(
                Auth::user(),
                'vocabulary_terms_accessed',
                [
                    'category' => $category,
                    'terms_count' => $terms->count(),
                    'search_query' => $search,
                    'locale' => $locale,
                    'access_type' => 'modal_interface'
                ],
                GdprActivityCategory::DATA_ACCESS
            );

            // Return view for both AJAX and regular HTTP requests
            // The modal system expects HTML, not JSON
            return view('components.coa.vocabulary-terms', [
                'category' => $category,
                'terms' => $terms,
                'search' => $search,
                'locale' => $locale
            ]);
        } catch (\InvalidArgumentException $e) {
            return view('components.coa.vocabulary-error', [
                'error' => 'Categoria non valida: ' . $e->getMessage()
            ]);
        } catch (\Exception $e) {
            $this->errorManager->handle('VOCABULARY_WEB_CATEGORY_TERMS_ERROR', [
                'category' => $category,
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'ip_address' => $request->ip(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Errore nel caricamento dei termini',
                    'message' => 'Si è verificato un errore durante il caricamento dei termini del vocabolario.'
                ], 500);
            }

            return view('components.coa.vocabulary-error', [
                'error' => 'Errore nel caricamento dei termini'
            ]);
        }
    }

    /**
     * Search vocabulary terms for modal
     *
     * @param Request $request
     * @return View
     * @privacy-safe Returns public vocabulary data only
     */
    public function search(Request $request): View {
        try {
            $validator = Validator::make($request->all(), [
                'q' => 'required|string|min:2|max:100',
                'category' => 'sometimes|string|in:technique,materials,support',
                'locale' => 'sometimes|string|size:2'
            ]);

            if ($validator->fails()) {
                return view('components.coa.vocabulary-error', [
                    'error' => 'Query di ricerca non valida'
                ]);
            }

            $query = $request->get('q');
            $category = $request->get('category');
            $locale = $request->get('locale', app()->getLocale());

            $this->logger->info('[Vocabulary Web] Search requested for modal', [
                'query' => $query,
                'category' => $category,
                'user_id' => Auth::id(),
                'ip_address' => $request->ip()
            ]);

            $terms = $this->vocabularyService->searchTerms($query, 50, $category, $locale);

            $this->auditService->log(
                GdprActivityCategory::DATA_ACCESS,
                'vocabulary_search_performed',
                [
                    'search_query' => $query,
                    'category_filter' => $category,
                    'results_count' => $terms->count(),
                    'locale' => $locale,
                    'access_type' => 'modal_search'
                ]
            );

            return view('components.coa.vocabulary-search-results', [
                'query' => $query,
                'category' => $category,
                'terms' => $terms,
                'locale' => $locale
            ]);
        } catch (ValidationException $e) {
            return view('components.coa.vocabulary-error', [
                'error' => 'Errore di validazione nella ricerca'
            ]);
        } catch (\Exception $e) {
            $this->errorManager->handle('VOCABULARY_WEB_SEARCH_ERROR', [
                'query' => $request->get('q'),
                'category' => $request->get('category'),
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'ip_address' => $request->ip(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            return view('components.coa.vocabulary-error', [
                'error' => 'Errore nella ricerca del vocabolario'
            ]);
        }
    }
}
