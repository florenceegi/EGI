<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Collection;
use App\Models\Egi;
use App\Models\Reservation;
use App\Services\PortfolioService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Helpers\FegiAuth;

/**
 * @Oracode Controller: Collector Home Page Management
 * 🎯 Purpose: Handle collector's public profile and portfolio pages
 * 🛡️ Security: Public access with privacy controls for collector portfolios
 * 🧱 Core Logic: Display collector's owned EGIs and purchase history with progressive enhancement
 *
 * @package App\Http\Controllers
 * @author Fabio Cherici & AI Assistant (FlorenceEGI Collector System)
 * @version 1.0.0 (FlorenceEGI MVP Collector Showcase)
 * @date 2025-08-07
 */
class CollectorHomeController extends Controller {

    /**
     * @var PortfolioService
     */
    protected PortfolioService $portfolioService;

    /**
     * Constructor with dependency injection
     *
     * @param PortfolioService $portfolioService
     */
    public function __construct(PortfolioService $portfolioService) {
        $this->portfolioService = $portfolioService;
    }
    /**
     * @Oracode Method: Display Collector Home Page
     * 🎯 Purpose: Show collector's main showcase page with stats and recent acquisitions
     * 📤 Output: Collector home view with stats and featured owned content
     */
    public function home(int $id, Request $request): View {
        return $this->portfolio($id, $request);
    }

    /**
     * @Oracode Method: Display Collector Index Page
     * 🎯 Purpose: List all collectors with filtering and search capabilities
     * 📤 Output: Collectors index view with pagination and filters
     */
    public function index(Request $request): View {
        $query = $request->input('query');
        $sort = $request->input('sort', 'latest'); // 'latest', 'most_egis', 'most_spent'

        // Calcola le statistiche per il banner
        $totalReservedWorks = Reservation::getTotalReservedWorks();
        $totalArtistsWithReservations = Reservation::getTotalArtistsWithReservations();

        $collectors = User::query()
            ->where(function ($collectorQuery) {
                $collectorQuery
                    ->whereHas('roles', function ($roleQuery) {
                        $roleQuery->where('name', 'collector');
                    })
                    ->orWhereHas('completedReservations', function ($reservationQuery) {
                        $reservationQuery->where('status', 'completed');
                    })
                    ->orWhereHas('publicOwnedEgis', function ($egiQuery) {
                        $egiQuery->where('is_published', true)
                            ->whereColumn('egis.user_id', '<>', 'egis.owner_id');
                    });
            })
            ->when($query, function ($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%');
            })
            ->withCount(['publicOwnedEgis as owned_egis_count' => function ($ownedQuery) {
                $ownedQuery->where('is_published', true)
                    ->whereColumn('egis.user_id', '<>', 'egis.owner_id');
            }])
            ->withSum(['completedReservations as total_spent' => function ($sumQuery) {
                $sumQuery->where('status', 'completed');
            }], 'offer_amount_fiat')
            ->with([
                'publicOwnedEgis' => function ($egiQuery) {
                    $egiQuery->where('is_published', true)
                        ->whereColumn('egis.user_id', '<>', 'egis.owner_id')
                        ->take(3);
                },
                'completedReservations' => function ($reservationQuery) {
                    $reservationQuery->where('status', 'completed')
                        ->with('egi')->take(3);
                },
            ])
            ->when($sort === 'most_egis', function ($q) {
                $q->orderByDesc('owned_egis_count')
                    ->orderByDesc('total_spent');
            })
            ->when($sort === 'most_spent', function ($q) {
                $q->orderByDesc('total_spent')
                    ->orderByDesc('owned_egis_count');
            })
            ->when($sort === 'latest', function ($q) {
                $q->latest();
            })
            ->distinct()
            ->paginate(20);

        return view('collector.index', compact(
            'collectors',
            'query',
            'sort',
            'totalReservedWorks',
            'totalArtistsWithReservations'
        ));
    }

    /**
     * @Oracode Method: Display Collector Portfolio Page
     * 🎯 Purpose: Show detailed portfolio with all purchased EGIs, filters and search
     * 📤 Output: Portfolio view with purchased EGIs grid/list and filtering options
     * 🚀 Enhancement: Uses PortfolioService for accurate ownership tracking
     */
    public function portfolio(int $id, Request $request): View {
        $collector = User::findOrFail($id);

        if (!$collector->isCollector()) {
            abort(404, 'User is not a collector');
        }

        $query = $request->input('query');
        $collection_filter = $request->input('collection');
        $creator_filter = $request->input('creator');
        $sort = $request->input('sort', 'latest');
        $view = $request->input('view', 'grid'); // 'grid' or 'list'

        // 🚀 FIX: Usa PortfolioService per ottenere tutti gli EGI su cui il collector ha fatto offerte (vincente o superato)
        $activePortfolio = $this->portfolioService->getCollectorActivePortfolio($collector);

        // Applica filtri e ordinamento alla collection
        $filteredEgis = $activePortfolio
            ->when($query, function ($collection) use ($query) {
                return $collection->filter(function ($egi) use ($query) {
                    return stripos($egi->title, $query) !== false;
                });
            })
            ->when($collection_filter, function ($collection) use ($collection_filter) {
                return $collection->filter(function ($egi) use ($collection_filter) {
                    return $egi->collection_id == $collection_filter;
                });
            })
            ->when($creator_filter, function ($collection) use ($creator_filter) {
                return $collection->filter(function ($egi) use ($creator_filter) {
                    return $egi->collection && $egi->collection->creator_id == $creator_filter;
                });
            });

        // Applica ordinamento
        switch ($sort) {
            case 'title':
                $filteredEgis = $filteredEgis->sortBy('title');
                break;
            case 'price_high':
                $filteredEgis = $filteredEgis->sortByDesc(function ($egi) {
                    return $egi->reservations->first()?->offer_amount_fiat ?? 0;
                });
                break;
            case 'price_low':
                $filteredEgis = $filteredEgis->sortBy(function ($egi) {
                    return $egi->reservations->first()?->offer_amount_fiat ?? 0;
                });
                break;
            case 'latest':
            default:
                $filteredEgis = $filteredEgis->sortByDesc(function ($egi) {
                    return $egi->reservations->first()?->created_at ?? '';
                });
                break;
        }

        // Simula paginazione (per ora manteniamo il comportamento esistente)
        $purchasedEgis = $filteredEgis->take(20);

        // 🚀 FIX: Usa PortfolioService per filtri accurati
        $availableCollections = $this->portfolioService->getAvailableCollections($collector);
        $availableCreators = $this->portfolioService->getAvailableCreators($collector);

        // 🚀 FIX: Usa PortfolioService per stats accurate
        $stats = $this->portfolioService->getCollectorPortfolioStats($collector);

        return view('collector.portfolio', compact(
            'collector',
            'purchasedEgis',
            'availableCollections',
            'availableCreators',
            'stats',
            'query',
            'collection_filter',
            'creator_filter',
            'sort',
            'view'
        ));
    }

    /**
     * @Oracode Method: Display Collector Collections Page
     * 🎯 Purpose: Show collections organized by creator/collection groups
     * 📤 Output: Collections view grouped by collection origin
     */
    public function collections(int $id): View {
        $collector = User::findOrFail($id);

        if (!$collector->isCollector()) {
            abort(404, 'User is not a collector');
        }

        $collectorCollections = $collector->getCollectorCollectionsAttribute();
        $stats = $collector->getCollectorStats();

        return view('collector.collections', compact('collector', 'collectorCollections', 'stats'));
    }

    /**
     * @Oracode Method: Show Individual Collection for Collector
     * 🎯 Purpose: Display specific collection with collector's purchased items
     * 📤 Output: Collection detail view filtered for collector's purchased items
     */
    public function showCollection(int $id, int $collection): View {
        $collector = User::findOrFail($id);
        $collection = Collection::with([
            'creator',
            'egis' => function ($query) use ($collector) {
                $query->whereHas('reservations', function ($subQuery) use ($collector) {
                    $subQuery->where('user_id', $collector->id)
                        ->whereIn('status', ['active', 'completed']);
                })->where('is_published', true);
            },
            'egis.user',
            'egis.blockchain.buyer', // 🤝 Co-Creator data
            'egis.reservations' => function ($query) {
                $query->where('sub_status', 'highest')
                    ->where('status', 'active')
                    ->with('user');
            }
        ])->findOrFail($collection);

        // Check if collector has purchased any EGIs from this collection
        if ($collection->egis->isEmpty()) {
            abort(404, 'No EGIs purchased from this collection');
        }

        $stats = $collector->getCollectorStats();

        return view('collector.collection-show', compact('collector', 'collection', 'stats'));
    }

    /**
     * @Oracode Method: Under Construction Placeholder
     * 🎯 Purpose: Temporary placeholder for future collector features
     * 📤 Output: Under construction view for planned features
     */
    public function underConstruction() {
        return view('collector.under-construction');
    }

    /**
     * @Oracode Method: Get Collector Stats API
     * 🎯 Purpose: Return collector statistics as JSON for AJAX/API calls
     * 📤 Output: JSON response with collector stats
     */
    public function getStats(int $id): JsonResponse {
        $collector = User::findOrFail($id);

        if (!$collector->isCollector()) {
            return response()->json(['error' => 'User is not a collector'], 404);
        }

        $stats = $collector->getCollectorStats();

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'collector' => [
                'id' => $collector->id,
                'name' => $collector->name,
                'profile_photo_url' => $collector->profile_photo_url
            ]
        ]);
    }
}
