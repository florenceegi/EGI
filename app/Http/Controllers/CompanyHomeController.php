<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\Egi;
use App\Models\User;
use App\Enums\User\MerchantUserTypeEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\View\View;

/**
 * @Oracode Controller: Company Home Page Management
 * 🎯 Purpose: Handle company's public profile and showcase pages
 * 🛡️ Security: Public access with business profile visibility
 * 🧱 Core Logic: Display company's EGI, collections with corporate style
 * 🎨 Palette: Corporate Blue (#1E3A5F), Business Gold (#C9A227), Success Green (#2D7D46)
 *
 * Architecture Note:
 * - Company funziona come Creator ma con identità aziendale
 * - Stesse funzionalità: Portfolio EGI, Collezioni, About
 * - Style corporate con palette dedicata
 * - Evolverà con funzionalità specifiche per aziende
 *
 * @package App\Http\Controllers
 * @author Fabio Cherici & AI Assistant (FlorenceEGI Company System)
 * @version 1.0.0
 * @date 2025-12-05
 */
class CompanyHomeController extends Controller
{
    /**
     * Risolve una company da ID numerico o nick_name
     */
    private function resolveCompany($identifier): User
    {
        if (is_numeric($identifier)) {
            $user = User::findOrFail($identifier);
        } else {
            $decodedIdentifier = urldecode($identifier);
            $user = User::where('nick_name', $decodedIdentifier)->firstOrFail();
        }

        // Verifica che sia una company
        if ($user->usertype !== MerchantUserTypeEnum::COMPANY->value) {
            abort(404, 'User is not a company');
        }

        return $user;
    }

    /**
     * Portfolio della Company: mostra tutti gli EGI CREATI dalla company
     */
    public function portfolio($id, Request $request)
    {
        $company = $this->resolveCompany($id);

        $query = $request->input('query');
        $collection_filter = $request->input('collection');
        $sort = $request->input('sort', 'latest');
        $view = $request->input('view', 'grid');
        $requestedMode = $request->input('mode', 'created');
        $canSwitchPortfolioMode = true;
        $portfolioMode = $requestedMode;
        if (!in_array($portfolioMode, ['created', 'owned'], true)) {
            $portfolioMode = 'created';
        }

        $baseWith = [
            'collection',
            'user',
            'owner',
            'blockchain.buyer',
            'traits.category',
            'reservations' => function ($q) {
                $q->where('is_current', true);
            },
        ];

        // EGIs creati dalla company
        $createdQuery = Egi::with($baseWith)
            ->whereHas('collection', function ($q) use ($company) {
                $q->where('creator_id', $company->id);
            })
            ->where('is_published', true)
            ->when($query, function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%");
            })
            ->when($collection_filter, function ($q) use ($collection_filter) {
                $q->where('collection_id', $collection_filter);
            });

        $createdQuery = $this->applyPortfolioSorting($createdQuery, $sort);
        $createdEgis = $createdQuery->get();

        // EGIs posseduti dalla company (acquistati da altri)
        $ownedQuery = Egi::with($baseWith)
            ->where('owner_id', $company->id)
            ->whereDoesntHave('collection', function ($q) use ($company) {
                $q->where('creator_id', $company->id);
            })
            ->when($query, function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%");
            })
            ->when($collection_filter, function ($q) use ($collection_filter) {
                $q->where('collection_id', $collection_filter);
            });

        $ownedQuery = $this->applyPortfolioSorting($ownedQuery, $sort);
        $ownedEgis = $ownedQuery->get();

        $companyCollectionsCount = $company->collections()->where('creator_id', $company->id)->count();

        $createdStats = $this->buildPortfolioStats($createdEgis, 'created', $companyCollectionsCount);
        $ownedStats = $this->buildPortfolioStats($ownedEgis, 'owned', $companyCollectionsCount);

        $displayEgis = $portfolioMode === 'owned' ? $ownedEgis : $createdEgis;
        $portfolioStats = $portfolioMode === 'owned' ? $ownedStats : $createdStats;

        // Check if AJAX request
        if ($request->ajax() || $request->get('partial')) {
            return view('company.partials.portfolio-content', [
                'company' => $company,
                'egis' => $displayEgis,
                'createdEgis' => $createdEgis,
                'ownedEgis' => $ownedEgis,
                'portfolioStats' => $portfolioStats,
                'portfolioMode' => $portfolioMode,
                'query' => $query,
                'collection_filter' => $collection_filter,
                'sort' => $sort,
                'view' => $view,
                'canSwitchPortfolioMode' => $canSwitchPortfolioMode,
            ]);
        }

        return view('company.home-spa', [
            'company' => $company,
            'egis' => $displayEgis,
            'createdEgis' => $createdEgis,
            'ownedEgis' => $ownedEgis,
            'stats' => $createdStats,
            'createdStats' => $createdStats,
            'ownedStats' => $ownedStats,
            'portfolioStats' => $portfolioStats,
            'portfolioMode' => $portfolioMode,
            'query' => $query,
            'collection_filter' => $collection_filter,
            'sort' => $sort,
            'view' => $view,
            'canSwitchPortfolioMode' => $canSwitchPortfolioMode,
        ])->with('activeTab', 'portfolio');
    }

    /**
     * Home page della company - redirect to portfolio
     */
    public function home($id)
    {
        return redirect()->route('company.portfolio', $id);
    }

    /**
     * Pagina indice delle Company
     */
    public function index(Request $request): View
    {
        $query = $request->input('query');
        $sort = $request->input('sort', 'latest');

        $companies = User::where('usertype', MerchantUserTypeEnum::COMPANY->value)
            ->when($query, function ($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%')
                    ->orWhere('bio', 'like', '%' . $query . '%');
            })
            ->when($sort, function ($q) use ($sort) {
                switch ($sort) {
                    case 'latest':
                        $q->latest();
                        break;
                    case 'oldest':
                        $q->oldest();
                        break;
                    case 'name_asc':
                        $q->orderBy('name', 'asc');
                        break;
                    case 'name_desc':
                        $q->orderBy('name', 'desc');
                        break;
                    default:
                        $q->latest();
                }
            }, function ($q) {
                $q->latest();
            })
            ->paginate(12);

        return view('company.index', [
            'companies' => $companies,
            'filters' => $request->only(['query', 'sort'])
        ]);
    }

    /**
     * Collections della company
     */
    public function collections($id, Request $request)
    {
        $company = $this->resolveCompany($id);

        $collections = $company->collections()
            ->where('is_published', true)
            ->withCount('egis')
            ->latest()
            ->get();

        $stats = [
            'total_collections' => $collections->count(),
            'total_egis' => $collections->sum('egis_count'),
            'total_supporters' => 0,
        ];

        if ($request->ajax() || $request->get('partial')) {
            return view('company.partials.collections-content', compact('company', 'collections', 'stats'));
        }

        return view('company.home-spa', compact('company', 'collections', 'stats'))
            ->with('activeTab', 'collections');
    }

    /**
     * About della company
     */
    public function about($id, Request $request)
    {
        $company = $this->resolveCompany($id);

        $stats = [
            'total_collections' => $company->collections()->count(),
            'total_egis' => $company->createdEgis()->count(),
            'total_supporters' => 0,
        ];

        if ($request->ajax() || $request->get('partial')) {
            return view('company.partials.about-content', compact('company', 'stats'));
        }

        return view('company.home-spa', compact('company', 'stats'))
            ->with('activeTab', 'about');
    }

    /**
     * Impact della company (EPP)
     */
    public function impact($id, Request $request)
    {
        $company = $this->resolveCompany($id);

        $stats = [
            'total_collections' => $company->collections()->count(),
            'total_egis' => $company->createdEgis()->count(),
            'total_supporters' => 0,
            'impact_score' => 0,
        ];

        if ($request->ajax() || $request->get('partial')) {
            return view('company.partials.impact-content', compact('company', 'stats'));
        }

        return view('company.home-spa', compact('company', 'stats'))
            ->with('activeTab', 'impact');
    }

    /**
     * Applica ordinamento
     */
    private function applyPortfolioSorting($query, string $sort)
    {
        switch ($sort) {
            case 'title':
                return $query->orderBy('title');
            case 'price_high':
                return $query->orderByDesc('price');
            case 'price_low':
                return $query->orderBy('price');
            case 'latest':
            default:
                return $query->orderByDesc('created_at');
        }
    }

    /**
     * Costruisce statistiche portfolio
     */
    private function buildPortfolioStats(SupportCollection $egis, string $mode, int $companyCollectionsCount): array
    {
        return [
            'total_egis' => $egis->count(),
            'total_collections' => $mode === 'owned'
                ? $egis->pluck('collection_id')->filter()->unique()->count()
                : $companyCollectionsCount,
            'total_value_eur' => $egis->sum('price'),
            'total_reservations' => $egis->sum(fn($egi) => $egi->reservations->count()),
            'highest_offer' => $egis->flatMap->reservations->max('offer_amount_fiat') ?? 0,
            'available_egis' => $egis->filter(fn($egi) => $egi->reservations->isEmpty())->count(),
            'reserved_egis' => $egis->filter(fn($egi) => $egi->reservations->isNotEmpty())->count(),
        ];
    }
}
