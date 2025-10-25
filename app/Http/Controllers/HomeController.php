<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\Egi;
use App\Models\Epp;
use App\Models\User;
use App\Services\CollectorCarouselService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Services\Gdpr\AuditLogService;
use App\Enums\Gdpr\GdprActivityCategory;

/**
 * @package App\Http\Controllers
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - HomeController)
 * @date 2025-09-30
 * @purpose GDPR-compliant homepage content management with Ultra Excellence standards
 *
 * @Oracode ULTRA+GDPR Controller: Homepage Content Management
 * 🎯 Purpose: Present essential data for FlorenceEGI homepage with GDPR compliance
 * 🛡️ Privacy: Uses only public data (is_published = true) with audit trail
 * 📊 GDPR: Activity logging for content access patterns
 * ⚡ UEM: Ultra Error Manager integration for error handling
 * 🔍 ULM: Operation logging with UltraLogManager
 * 🧱 Semantic: Clear purpose for each method relative to domain entities
 * 📡 Queryable: Methods clearly specify what they present and why
 *
 * Features:
 * - Public content aggregation with privacy compliance
 * - Comprehensive audit trail for homepage access
 * - Ultra Error Manager for robust error handling
 * - Performance optimized queries
 * - SEO and accessibility optimized content delivery
 *
 * @seo-purpose Provides dynamic content relevant to FlorenceEGI homepage
 * @schema-type WebPage
 */
class HomeController extends Controller
{

    protected CollectorCarouselService $collectorCarouselService;
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;
    protected AuditLogService $auditService;

    /**
     * @Oracode Constructor: ULTRA+GDPR Pattern Implementation
     * 🏗️ Purpose: Initialize all required dependencies for GDPR-compliant operation
     * 🛡️ Security: Dependency injection for logging and audit trail
     * 📊 GDPR: AuditLogService integration for activity tracking
     * ⚡ UEM: ErrorManagerInterface for robust error handling
     * 🔍 ULM: UltraLogManager for comprehensive logging
     *
     * @param CollectorCarouselService $collectorCarouselService Service for collector data
     * @param UltraLogManager $logger Ultra Log Manager for operation logging
     * @param ErrorManagerInterface $errorManager Ultra Error Manager for error handling
     * @param AuditLogService $auditService GDPR audit trail service
     * @return void
     */
    public function __construct(
        CollectorCarouselService $collectorCarouselService,
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService
    ) {
        $this->collectorCarouselService = $collectorCarouselService;
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditService = $auditService;
    }

    /**
     * @Oracode Method: Homepage Display - ULTRA+GDPR Pattern
     * 🎯 Purpose: Present comprehensive overview of FlorenceEGI ecosystem with GDPR compliance
     * �️ Privacy: Uses only public data (is_published = true) with activity tracking
     * 📊 GDPR: Logs homepage access for analytics and compliance
     * ⚡ UEM: Complete error handling with Ultra Error Manager
     * 🔍 ULM: Homepage access logging and performance monitoring
     *
     * 📥 Input: HTTP request for homepage
     * 📤 Output: Homepage view with all aggregated public content
     *
     * Content includes:
     * - Random EGIs showcase
     * - Featured collections carousel
     * - Latest collections grid
     * - Highlighted EPPs
     * - Featured creators
     * - Top collectors
     * - Environmental impact statistics
     * - Hyper EGIs collection
     *
     * @seo-purpose Main site page with NFT collections showcase and environmental impact
     * @accessibility-trait Contains counters and statistics with explicit labels
     *
     * @return View Homepage view populated with all necessary data
     * @throws \Exception On data retrieval or rendering failures
     */
    public function index(): View
    {
        try {
            // 1. ULM: Log homepage access start
            $this->logger->info('Homepage access initiated', [
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'user_id' => auth()->id(),
                'is_authenticated' => auth()->check(),
                'timestamp' => now()->toIso8601String(),
                'log_category' => 'HOMEPAGE_ACCESS'
            ]);

            // 2. GDPR: Log activity for authenticated users
            if (auth()->check()) {
                $this->auditService->logUserAction(
                    auth()->user(),
                    'homepage_viewed',
                    [
                        'user_agent' => request()->userAgent(),
                        'referrer' => request()->header('referer'),
                        'timestamp' => now()->toIso8601String()
                    ],
                    GdprActivityCategory::PLATFORM_USAGE
                );
            }

            // 3. Retrieve homepage data
            $randomEgis = $this->getRandomEgis();
            $featuredCollections = $this->getRandomCollections();
            $latestCollections = $this->getLatestCollections($featuredCollections->pluck('id'));
            $highlightedEpps = $this->getHighlightedEpps();
            $featuredCreators = $this->getFeaturedCreators();
            $topCollectors = $this->collectorCarouselService->getTopCollectors(10);
            $featuredEgis = $this->getFeaturedEgis();
            $hyperEgis = $this->getHyperEgis();
            $totalPlasticRecovered = $this->getTotalPlasticRecovered();

            // 4. ULM: Log successful data retrieval
            $this->logger->info('Homepage data retrieved successfully', [
                'user_id' => auth()->id(),
                'data_counts' => [
                    'random_egis' => $randomEgis->count(),
                    'featured_collections' => $featuredCollections->count(),
                    'latest_collections' => $latestCollections->count(),
                    'highlighted_epps' => $highlightedEpps->count(),
                    'featured_creators' => $featuredCreators->count(),
                    'top_collectors' => $topCollectors->count(),
                    'featured_egis' => $featuredEgis->count(),
                    'hyper_egis' => $hyperEgis->count()
                ],
                'total_plastic_recovered' => $totalPlasticRecovered,
                'log_category' => 'HOMEPAGE_SUCCESS'
            ]);

            // 5. Return homepage view
            return view('home', [
                'randomEgis' => $randomEgis,
                'featuredCollections' => $featuredCollections,
                'latestCollections' => $latestCollections,
                'highlightedEpps' => $highlightedEpps,
                'totalPlasticRecovered' => $totalPlasticRecovered,
                'featuredCreators' => $featuredCreators,
                'topCollectors' => $topCollectors,
                'featuredEgis' => $featuredEgis,
                'allEgis' => null, // Performance: Disabled for now
                'hyperEgis' => $hyperEgis,
            ]);
        } catch (\Exception $e) {
            // 6. UEM: Error handling
            $this->errorManager->handle('HOMEPAGE_LOAD_ERROR', [
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'error_message' => $e->getMessage(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            // Return error view with fallback content
            return view('error.homepage', [
                'message' => __('errors.homepage_unavailable'),
                'return_url' => route('dashboard')
            ]);
        }
    }

    /**
     * @Oracode Method: Get Random EGIs for Carousel
     * 🎯 Purpose: Retrieve random published EGIs for homepage carousel display (CREATOR ONLY)
     * 🛡️ Privacy: Uses only publicly published EGIs (is_published = true)
     * 🎲 Random: Uses inRandomOrder() for variety on each page load
     * 🎨 Filter: Only EGIs from creators with usertype = 'creator' (excludes PA fake EGIs)
     *
     * @return \Illuminate\Database\Eloquent\Collection Collection of 5 random published EGIs with collection data
     * @privacy-safe Only public published content from creators
     */
    private function getRandomEgis()
    {
        // 🎲 RANDOM: Ordinamento casuale ad ogni reload
        // 🎯 FILTER: Solo EGI di creator con usertype = 'creator' (NO PA)
        // 🚫 EXCLUDE: Esclusi EGI con type = 'pa_act'
        return Egi::where('is_published', true)
            ->where('type', '!=', 'pa_act') // Escludi PA Act
            ->whereHas('user', function ($query) {
                $query->where('usertype', 'creator');
            })
            ->with([
                'collection',
                'user',
                'blockchain.buyer', // 🤝 Co-Creator data
                'reservations' => function ($query) {
                    $query->where('sub_status', 'highest')
                        ->where('status', 'active')
                        ->with('user');
                }
            ])
            ->inRandomOrder() // Random ad ogni reload
            ->take(5)
            ->get();
    }

    /**
     * @Oracode Method: Get Featured Collections for Hero Carousel
     * 🎯 Purpose: Retrieve collections for main homepage carousel with advanced selection algorithm
     * 🛡️ Privacy: Uses only publicly published collections
     * 🧠 Algorithm: Advanced selection with priority and impact-based ordering
     *
     * Selection criteria:
     * - Filter: featured_in_guest = true AND is_published = true
     * - Priority: Forced positions (featured_position 1-10)
     * - Secondary: Estimated impact (EPP quota 20% of highest bookings)
     * - Limit: Maximum 10 collections in carousel
     *
     * @return \Illuminate\Database\Eloquent\Collection Featured collections for hero carousel
     * @privacy-safe Only public published collections
     */
    private function getFeaturedCollections()
    {
        // Utilizziamo il service dedicato per la logica complessa di selezione
        $featuredService = app(\App\Services\FeaturedCollectionService::class);

        return $featuredService->getFeaturedCollections(10);
    }

    /**
     * @Oracode Method: Get Random Collections for Testing/Development
     * 🎯 Purpose: Retrieve random collections for test and development scenarios
     * 🛡️ Privacy: Uses only publicly published collections
     * 🎲 Algorithm: Simple random selection without featured filters
     *
     * Selection criteria:
     * - Filter: Only is_published = true (NO featured_in_guest filter)
     * - Method: Completely random selection
     * - Includes: Collections with Spatie media for visualization testing
     * - Limit: 10 collections
     *
     * @return \Illuminate\Database\Eloquent\Collection Random collections for testing
     * @privacy-safe Only public published collections
     */
    private function getRandomCollections()
    {
        // Utilizziamo il service dedicato per la selezione random
        $featuredService = app(\App\Services\FeaturedCollectionService::class);

        return $featuredService->getRandomCollections(10);
    }

    /**
     * @Oracode Method: Get Featured EGIs for Homepage Carousel
     * 🎯 Purpose: Retrieve latest 20 EGIs for homepage carousel display
     * 🛡️ Privacy: Uses only publicly published EGIs (is_published = true)
     * ⚡ Performance: Optimized query with collection relationships preloaded
     *
     * @return \Illuminate\Database\Eloquent\Collection Latest 20 published EGIs with collection data
     * @privacy-safe Only public published content
     */
    /**
     * @Oracode Method: Get Featured EGIs for Homepage
     * 🎯 Purpose: Retrieve random published EGIs for homepage display (CREATOR ONLY)
     * 🛡️ Privacy: Uses only publicly published EGIs (is_published = true)
     * 🎲 Random: Uses inRandomOrder() for variety on each page load
     * 🎨 Filter: Only EGIs from creators with usertype = 'creator' (excludes PA fake EGIs)
     *
     * @return \Illuminate\Database\Eloquent\Collection Random 20 published EGIs from creators
     * @privacy-safe Only public published content from creators
     */
    private function getFeaturedEgis()
    {
        return Egi::where('is_published', true)
            ->where('type', '!=', 'pa_act') // Escludi PA Act
            ->whereHas('user', function ($query) {
                $query->where('usertype', 'creator');
            })
            ->with([
                'collection',
                'user',
                'blockchain.buyer', // 🤝 Co-Creator data
                'reservations' => function ($query) {
                    $query->where('sub_status', 'highest')
                        ->where('status', 'active')
                        ->with('user');
                }
            ])
            ->inRandomOrder() // Random ad ogni reload
            ->take(20)
            ->get();
    }

    /**
     * @Oracode Method: Get Latest Collections Grid
     * 🎯 Purpose: Retrieve latest created collections for homepage grid display
     * 🛡️ Privacy: Uses only publicly published collections
     * 🚫 Exclusion: Excludes IDs already shown in featured collections
     * ⚡ Performance: Includes creator relationship and EGI counts
     *
     * @param \Illuminate\Support\Collection $excludeIds IDs to exclude (e.g., already featured collections)
     * @return \Illuminate\Database\Eloquent\Collection Latest 8 published collections with creator and EGI count
     * @privacy-safe Only public published collections
     */
    private function getLatestCollections($excludeIds)
    {
        return Collection::where('is_published', true)
            ->whereNotIn('id', $excludeIds)
            ->with(['creator'])
            ->withCount('egis')
            ->latest()
            ->take(8)
            ->get();
    }

    /**
     * @Oracode Method: Get Highlighted Environmental Projects (EPP)
     * 🎯 Purpose: Retrieve active environmental projects for homepage showcase
     * 🌱 Environmental: Highlights active environmental impact projects
     * 📊 Ordering: Most recent projects first
     *
     * @return \Illuminate\Database\Eloquent\Collection Top 3 active environmental projects
     * @privacy-safe Public environmental project data
     */
    private function getHighlightedEpps()
    {
        return Epp::where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();
    }

    /**
     * @Oracode Method: Get Featured Creators for Carousel
     * 🎯 Purpose: Retrieve creator users for homepage creator showcase carousel
     * 👥 Users: Filters users with usertype 'creator'
     * 📊 Metrics: Includes EGI and collection counts for each creator
     * 🎲 Selection: Random order to provide variety
     *
     * @return \Illuminate\Database\Eloquent\Collection Random 50 creators with their EGI and collection counts
     * @privacy-safe Public creator profiles only
     */
    private function getFeaturedCreators()
    {
        return User::where('usertype', 'creator')
            ->withCount(['createdEgis as egis_count', 'createdCollections as collections_count'])
            ->inRandomOrder()
            ->take(50) // Puoi regolare il numero di creator da mostrare
            ->get();
    }

    /**
     * @Oracode Method: Get Total Plastic Recovered Statistics
     * 🎯 Purpose: Provide environmental impact statistics for homepage display
     * 🌊 Environmental: Ocean plastic recovery data in kilograms
     * 📊 Data: Currently hardcoded for MVP, future database integration planned
     * 📡 Queryable: Provides environmental impact data
     *
     * @return float Total quantity in kg of plastic recovered from oceans
     * @schema-type QuantitativeValue
     * @todo Future: Calculate sum from transactions or retrieve from dedicated API
     */
    private function getTotalPlasticRecovered(): float
    {
        // MVP: Valore hardcoded
        // TODO: In futuro, calcolare somma da transazioni o recuperare da API dedicata
        return 5241.38;

        // Implementazione futura:
        // return Transaction::where('type', 'plastic_recovery')
        //      ->where('status', 'confirmed')
        //      ->sum('amount');
    }

    /**
     * @Oracode Method: Get Hyper EGIs for Carousel
     * 🎯 Purpose: Retrieve all published Hyper EGIs for special carousel display
     * ⚡ Hyper: Filters specifically for EGIs marked as 'hyper' category
     * 🛡️ Privacy: Uses only publicly published EGIs (is_published = true)
     * 🔗 Relationships: Includes collection data for complete display
     *
     * @return \Illuminate\Database\Eloquent\Collection All published Hyper EGIs with collection data
     * @privacy-safe Only public published Hyper content
     */
    /**
     * @Oracode Method: Get Hyper EGIs for Homepage
     * 🎯 Purpose: Retrieve hyper-flagged published EGIs for special display (CREATOR ONLY)
     * 🛡️ Privacy: Uses only publicly published hyper EGIs (is_published = true, hyper = true)
     * 🎲 Random: Uses inRandomOrder() for variety on each page load
     * 🎨 Filter: Only EGIs from creators with usertype = 'creator' (excludes PA fake EGIs)
     *
     * @return \Illuminate\Database\Eloquent\Collection Hyper-flagged published EGIs from creators (random order)
     * @privacy-safe Only public published hyper content from creators
     */
    private function getHyperEgis()
    {
        return Egi::where('is_published', true)
            ->where('hyper', true)
            ->where('type', '!=', 'pa_act') // Escludi PA Act
            ->whereHas('user', function ($query) {
                $query->where('usertype', 'creator');
            })
            ->with([
                'collection',
                'user',
                'blockchain.buyer', // 🤝 Co-Creator data
                'reservations' => function ($query) {
                    $query->where('sub_status', 'highest')
                        ->where('status', 'active')
                        ->with('user');
                }
            ])
            ->inRandomOrder() // Random ad ogni reload
            ->get();
    }
}