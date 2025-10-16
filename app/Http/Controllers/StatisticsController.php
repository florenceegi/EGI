<?php

namespace App\Http\Controllers;

use App\Services\StatisticsService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Ultra\ErrorManager\Facades\UltraError;
use Ultra\UltraLogManager\UltraLogManager;
use Illuminate\View\View;
use Throwable; // Import Throwable per il type hinting nelle eccezioni

/**
 * @Oracode Controller Statistics
 * 🎯 Purpose: Manages the display and data retrieval for the user statistics dashboard.
 *              Provides a view endpoint for the main statistics page and a JSON API endpoint
 *              for asynchronously fetching statistics data. Also includes endpoints for
 *              cache management and a data summary.
 * 📡 Service Dependencies: App\Services\StatisticsService, Ultra\UltraLogManager\UltraLogManager
 * 🛡️ GDPR Scope: Handles user-specific data, logs user activity for statistical requests.
 *                User ID is logged for all operations. No sensitive personal data beyond
 *                user ID and email (for logging) is directly processed for response here,
 *                as StatisticsService aggregates non-personal metrics.
 * 🧪 Testing Strategy: Feature tests for each endpoint ensuring correct responses for
 *                     authenticated users, proper error handling via UEM, and correct
 *                     view rendering or JSON structure. Unit tests for specific helper methods if any.
 *
 * @version 2.0.0
 * @author Padmin D. Curtis & Fabio Cherici
 */
class StatisticsController extends Controller {
    private UltraLogManager $logger;

    /**
     * 🎯 Constructor: Injects dependencies.
     * @param UltraLogManager $logger For structured, contextual logging via ULM.
     *
     * @signature: __construct(UltraLogManager $logger)
     * @context: Controller instantiation by Laravel's service container.
     */
    public function __construct(UltraLogManager $logger) {
        $this->logger = $logger;
        // Middleware 'auth' is applied at the route group level.
    }

    /**
     * 🎯 Displays the main statistics dashboard page.
     * This method renders the Blade view скелет, which then uses JavaScript
     * to fetch and display the actual statistics data asynchronously.
     *
     * @param Request $request The incoming HTTP request.
     * @return View The rendered Blade view for the statistics dashboard.
     *
     * @Oracode-HTTP GET
     * @Oracode-RouteName dashboard.statistics.index
     * @Oracode-Permissions Requires authenticated user (via route middleware).
     * @Oracode-Output Blade view ('dashboard.statistics.statistics_blade_view').
     * @Oracode-ErrorHandling Standard Laravel exceptions if view not found.
     *                          Authentication errors handled by middleware.
     *
     * @signature: showStatisticsPage(Request $request): View
     * @context: User navigates to the statistics dashboard page.
     * @log: STATS_PAGE_VIEW - User ID and request details.
     * @privacy-safe: Logs user ID and non-sensitive request details. View itself is a template.
     */
    public function showStatisticsPage(Request $request): View {
        $user = $request->user(); // Authenticated by route middleware

        // Get period from URL parameter
        $period = $request->query('period', 'day');
        $validPeriods = ['day', 'week', 'month', 'year', 'all'];
        if (!in_array($period, $validPeriods)) {
            $period = 'day';
        }

        $this->logger->info('Statistics page view request', [
            'user_id' => $user?->id,
            'period' => $period,
            'request_ip' => $request->ip(),
            'log_category' => 'STATS_PAGE_VIEW'
        ]);

        return view('dashboard.statistics.statistics_blade_view', [
            'pageTitle' => __('statistics.dashboard_title'), // Or __('menu.statistics')
            'user'      => $user, // For potential use in the main layout (e.g., x-app-layout)
            'period'    => $period, // Pass period to view
        ]);
    }

    /**
     * 🎯 Fetches and returns comprehensive statistics data as a JSON response.
     * This endpoint is called asynchronously by the JavaScript on the statistics dashboard.
     * It handles force refresh requests via a 'refresh' query parameter.
     *
     * @param Request $request The incoming HTTP request.
     * @return JsonResponse JSON containing statistics data or a UEM-handled error response.
     *
     * @Oracode-HTTP GET
     * @Oracode-RouteName dashboard.statistics.data.json
     * @Oracode-Permissions Requires authenticated user.
     * @Oracode-Input Query parameter 'refresh' (optional, '1' for force refresh).
     * @Oracode-Output JSON structure: { success: bool, data: array|null, meta: array|null, error?: string, message?: string }
     * @Oracode-ErrorHandling Uses UEM ('STATISTICS_CALCULATION_FAILED') for structured error responses.
     *
     * @signature: getStatisticsDataAsJson(Request $request): JsonResponse
     * @context: JavaScript fetch call from the statistics dashboard.
     * @log: STATS_API_REQUEST - User ID, force_refresh status.
     * @log: STATS_API_SUCCESS - User ID, cache status on successful data retrieval.
     * @log: STATS_API_ERROR_DETAIL - Detailed exception info on failure (ULM log).
     * @privacy-safe: User ID and request details logged. Data returned is aggregated statistics.
     *                UEM context for error might include user ID.
     * @data-input: $request->query('refresh')
     * @data-output: JSON response with aggregated statistics.
     */
    public function getStatisticsDataAsJson(Request $request): JsonResponse {
        try {
            /** @var \App\Models\User $user */
            $user = $request->user(); // Authenticated by route middleware

            // Redundant check, middleware should handle, but good for defense.
            if (!$user) {
                // @privacy-safe: Logs IP of unauthenticated attempt.
                $this->logger->warning('Unauthenticated attempt to access statistics API.', [
                    'request_ip' => $request->ip(),
                    'log_category' => 'STATS_API_AUTH_FAILURE'
                ]);
                return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
            }

            $forceRefresh = $request->query('refresh') === '1';
            $period = $request->query('period', 'day'); // Default to 'day' if not specified

            // Validate period parameter
            $validPeriods = ['day', 'week', 'month', 'year', 'all'];
            if (!in_array($period, $validPeriods)) {
                $period = 'day'; // Fallback to default
            }

            $this->logger->info('Statistics JSON data request initiated', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'force_refresh' => $forceRefresh,
                'period' => $period,
                'log_category' => 'STATS_API_REQUEST'
            ]);

            $statisticsService = new StatisticsService($user, $this->logger);
            
            // DEBUG: Log user collection IDs
            $reflection = new \ReflectionClass($statisticsService);
            $property = $reflection->getProperty('userCollectionIds');
            $property->setAccessible(true);
            $userCollectionIds = $property->getValue($statisticsService);
            
            $this->logger->info('StatisticsService initialized', [
                'user_id' => $user->id,
                'collection_ids' => $userCollectionIds,
                'collection_count' => count($userCollectionIds),
                'log_category' => 'STATS_SERVICE_DEBUG'
            ]);
            
            $stats = $statisticsService->getComprehensiveStats($forceRefresh);

            $this->logger->info('Statistics JSON data calculation completed successfully', [
                'user_id' => $user->id,
                'cache_status' => $stats['loaded_from_cache'] ? 'HIT' : 'MISS',
                'log_category' => 'STATS_API_SUCCESS'
            ]);

            return response()->json([
                'success' => true,
                'data' => $stats,
                'meta' => [
                    'user_id' => $user->id,
                    'calculated_at' => $stats['generated_at'],
                    'cache_expires_at' => $stats['cache_expires_at'],
                    'mvp_version' => '2.0.0'
                ]
            ]);
        } catch (Throwable $e) { // Catching Throwable for broader error capture
            $userId = auth()->check() ? auth()->id() : 'Guest'; // Defensive check for user ID

            // @log: Detailed internal log of the failure using ULM.
            $this->logger->error('Statistics JSON data calculation failed', [
                'user_id' => $userId,
                'error_message' => $e->getMessage(),
                'exception_class' => get_class($e),
                'exception_file' => $e->getFile(),
                'exception_line' => $e->getLine(),
                'log_category' => 'STATS_API_ERROR_DETAIL'
            ]);

            // @error-boundary: Delegates to UEM for standardized error response.
            return UltraError::handle(
                'STATISTICS_CALCULATION_FAILED', // UEM Error Code
                [ // Context for UEM
                    'user_id' => $userId,
                    'error_context' => 'statistics_api_data_fetch',
                    'request_route_name' => $request->route()?->getName(),
                ],
                $e // Pass the original exception to UEM
            );
        }
    }

    /**
     * 🎯 Clears the statistics cache for the authenticated user.
     *
     * @param Request $request The incoming HTTP request.
     * @return JsonResponse JSON indicating success or a UEM-handled error response.
     *
     * @Oracode-HTTP POST
     * @Oracode-RouteName dashboard.statistics.clear-cache
     * @Oracode-Permissions Requires authenticated user.
     * @Oracode-Output JSON structure: { success: bool, message: string, meta?: array }
     * @Oracode-ErrorHandling Uses UEM ('STATISTICS_CACHE_CLEAR_FAILED') for structured error responses.
     *
     * @signature: clearCache(Request $request): JsonResponse
     * @context: User or admin action to force a cache clear for statistics.
     * @log: STATS_CACHE_CLEAR_REQUEST - User ID.
     * @log: STATS_CACHE_CLEAR_ERROR_DETAIL - Detailed exception info on failure (ULM log).
     * @privacy-safe: Logs user ID. Operation affects user-specific cache.
     */
    public function clearCache(Request $request): JsonResponse {
        try {
            /** @var \App\Models\User $user */
            $user = $request->user();
            if (!$user) {
                $this->logger->warning('Unauthenticated attempt to clear statistics cache.', ['log_category' => 'STATS_CACHE_AUTH_FAILURE']);
                return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
            }

            $this->logger->info('Statistics cache clear requested', [
                'user_id' => $user->id,
                'log_category' => 'STATS_CACHE_CLEAR_REQUEST'
            ]);

            $statisticsService = new StatisticsService($user, $this->logger);
            $cleared = $statisticsService->clearUserStatisticsCache();

            if ($cleared) {
                return response()->json([
                    'success' => true,
                    'message' => __('statistics.cache_cleared_successfully'), // Use translation
                    'meta' => [
                        'user_id' => $user->id,
                        'cleared_at' => now()->toISOString()
                    ]
                ]);
            } else {
                $this->logger->warning('Statistics cache not found or clear operation reported false.', [
                    'user_id' => $user->id,
                    'log_category' => 'STATS_CACHE_CLEAR_NOT_FOUND'
                ]);
                // Still return success=true as the state (no cache) is achieved,
                // or decide if this should be a different kind of response.
                // For simplicity, if Cache::forget returns false, it means key didn't exist.
                return response()->json([
                    'success' => true, // Or false, depending on desired strictness
                    'message' => __('statistics.cache_already_clear_or_not_found'), // Use translation
                    'meta' => [
                        'user_id' => $user->id,
                    ]
                ]);
            }
        } catch (Throwable $e) {
            $userId = auth()->check() ? auth()->id() : 'Guest';
            $this->logger->error('Statistics cache clear failed', [
                'user_id' => $userId,
                'error_message' => $e->getMessage(),
                'exception_class' => get_class($e),
                'exception_file' => $e->getFile(),
                'exception_line' => $e->getLine(),
                'log_category' => 'STATS_CACHE_CLEAR_ERROR_DETAIL'
            ]);

            return UltraError::handle(
                'STATISTICS_CACHE_CLEAR_FAILED', // UEM Error Code
                ['user_id' => $userId],
                $e
            );
        }
    }

    /**
     * 🎯 Fetches and returns a lightweight summary of statistics as a JSON response.
     *
     * @param Request $request The incoming HTTP request.
     * @return JsonResponse JSON containing summary statistics or a UEM-handled error response.
     *
     * @Oracode-HTTP GET
     * @Oracode-RouteName api.statistics.summary
     * @Oracode-Permissions Requires authenticated user.
     * @Oracode-Input Query parameter 'refresh' (optional).
     * @Oracode-Output JSON structure: { success: bool, data: array|null, meta: array|null }
     * @Oracode-ErrorHandling Uses UEM ('STATISTICS_SUMMARY_FAILED') for structured error responses.
     *
     * @signature: summary(Request $request): JsonResponse
     * @context: Request for a quick statistics summary, e.g., for a dashboard widget.
     * @log: STATS_SUMMARY_SUCCESS - User ID on successful retrieval.
     * @log: STATS_SUMMARY_ERROR_DETAIL - Detailed exception info on failure (ULM log).
     * @privacy-safe: Logs user ID. Data returned is aggregated summary statistics.
     */
    public function summary(Request $request): JsonResponse {
        try {
            /** @var \App\Models\User $user */
            $user = $request->user();
            if (!$user) {
                $this->logger->warning('Unauthenticated attempt to access statistics summary API.', ['log_category' => 'STATS_SUMMARY_AUTH_FAILURE']);
                return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
            }

            $this->logger->info('Statistics summary data request initiated', [
                'user_id' => $user->id,
                'force_refresh' => $request->query('refresh') === '1',
                'log_category' => 'STATS_SUMMARY_REQUEST'
            ]);

            $statisticsService = new StatisticsService($user, $this->logger);
            $fullStats = $statisticsService->getComprehensiveStats($request->query('refresh') === '1');

            $this->logger->info('Statistics summary data retrieved successfully', [
                'user_id' => $user->id,
                'log_category' => 'STATS_SUMMARY_SUCCESS'
            ]);

            return response()->json([
                'success' => true,
                'data' => $fullStats['summary'], // Only the summary part
                'meta' => [
                    'user_id' => $user->id,
                    'generated_at' => $fullStats['generated_at'], // From the full stats object
                    'endpoint_type' => 'summary'
                ]
            ]);
        } catch (Throwable $e) {
            $userId = auth()->check() ? auth()->id() : 'Guest';
            $this->logger->error('Statistics summary data retrieval failed', [
                'user_id' => $userId,
                'error_message' => $e->getMessage(),
                'exception_class' => get_class($e),
                'exception_file' => $e->getFile(),
                'exception_line' => $e->getLine(),
                'log_category' => 'STATS_SUMMARY_ERROR_DETAIL'
            ]);

            return UltraError::handle(
                'STATISTICS_SUMMARY_FAILED', // UEM Error Code
                ['user_id' => $userId],
                $e
            );
        }
    }
}
