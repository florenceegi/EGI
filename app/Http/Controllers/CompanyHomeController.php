<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\Egi;
use App\Models\User;
use App\Enums\User\MerchantUserTypeEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\View\View;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

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
class CompanyHomeController extends Controller {
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;
    protected \App\Services\OnboardingChecklistService $onboardingService;

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        \App\Services\OnboardingChecklistService $onboardingService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->onboardingService = $onboardingService;
    }
    /**
     * Risolve una company da ID numerico o nick_name
     */
    private function resolveCompany($identifier): User {
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
    public function portfolio($id, Request $request) {
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
            })
            // EXCLUDE CLONES from the "Created" tab
            // Clones appear in "Owned" if the company owns them, otherwise they are Buyer's property
            ->whereNull('parent_id');

        $createdQuery = $this->applyPortfolioSorting($createdQuery, $sort);
        $createdEgis = $createdQuery->get();

        // EGIs posseduti dalla company (acquistati da altri)
        // 🔒 Privacy: Owner viewing own portfolio sees all EGIs (including unpublished)
        $isOwnerViewing = auth()->check() && auth()->id() === $company->id;

        $ownedQuery = Egi::with($baseWith)
            ->where('owner_id', $company->id)
            ->whereDoesntHave('collection', function ($q) use ($company) {
                $q->where('creator_id', $company->id);
            })
            ->when(!$isOwnerViewing, function ($q) {
                // Se non è l'owner che visualizza, mostra solo gli EGI pubblicati
                $q->where('is_published', true);
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
            'onboardingChecklist' => auth()->check() && auth()->id() === $company->id
                ? $this->onboardingService->getChecklist($company, 'company')
                : [],
            'sidebarContextMessage' => $this->generateCompanySidebarContext($company),
        ])->with('activeTab', 'portfolio');
    }

    /**
     * Home page della company - redirect to portfolio
     */
    public function home($id) {
        return redirect()->route('company.portfolio', $id);
    }

    /**
     * Pagina indice delle Company
     */
    public function index(Request $request): View {
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
    public function collections($id, Request $request) {
        $company = $this->resolveCompany($id);

        $collections = $company->collections()
            ->where('is_published', true)
            ->withCount('originalEgis')
            ->latest()
            ->get();

        $stats = [
            'total_collections' => $collections->count(),
            'total_egis' => $collections->sum('original_egis_count'),
            'total_supporters' => 0,
        ];

        if ($request->ajax() || $request->get('partial')) {
            return view('company.partials.collections-content', compact('company', 'collections', 'stats'));
        }

        // Get onboarding checklist for owner
        $onboardingChecklist = auth()->check() && auth()->id() === $company->id
            ? $this->onboardingService->getChecklist($company, 'company')
            : [];

        $sidebarContextMessage = $this->generateCompanySidebarContext($company);

        return view('company.home-spa', compact('company', 'collections', 'stats', 'onboardingChecklist', 'sidebarContextMessage'))
            ->with('activeTab', 'collections');
    }

    /**
     * About della company
     */
    public function about($id, Request $request) {
        $company = $this->resolveCompany($id);

        $stats = [
            'total_collections' => $company->collections()->count(),
            'total_egis' => $company->createdEgis()->count(),
            'total_supporters' => 0,
        ];

        if ($request->ajax() || $request->get('partial')) {
            return view('company.partials.about-content', compact('company', 'stats'));
        }

        // Get onboarding checklist for owner
        $onboardingChecklist = auth()->check() && auth()->id() === $company->id
            ? $this->onboardingService->getChecklist($company, 'company')
            : [];

        $sidebarContextMessage = $this->generateCompanySidebarContext($company);

        return view('company.home-spa', compact('company', 'stats', 'onboardingChecklist', 'sidebarContextMessage'))
            ->with('activeTab', 'about');
    }

    /**
     * Update about field - Only for company owner
     */
    public function updateAbout($id, Request $request) {
        $company = $this->resolveCompany($id);

        // Check if current user is the company owner
        if (\Auth::id() !== $company->id) {
            abort(403, __('company.about.unauthorized'));
        }

        $validated = $request->validate([
            'about' => ['nullable', 'string', 'max:5000'],
        ]);

        // Update or create organization data
        if ($company->organizationData) {
            $company->organizationData->update([
                'about' => $validated['about'],
            ]);
        } else {
            $company->organizationData()->create([
                'about' => $validated['about'],
            ]);
        }

        return redirect()->route('company.about', $company->id)
            ->with('success', __('company.about.updated_success'));
    }

    /**
     * Impact della company (EPP)
     */
    public function impact($id, Request $request) {
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

        // Get onboarding checklist for owner
        $onboardingChecklist = auth()->check() && auth()->id() === $company->id
            ? $this->onboardingService->getChecklist($company, 'company')
            : [];

        $sidebarContextMessage = $this->generateCompanySidebarContext($company);

        return view('company.home-spa', compact('company', 'stats', 'onboardingChecklist', 'sidebarContextMessage'))
            ->with('activeTab', 'impact');
    }

    /**
     * Generate context-aware AI sidebar message for company portfolio page.
     *
     * Creates personalized welcome message based on user role:
     * - Owner: Portfolio stats, management actions, monetization guidance
     * - Visitor: Brand value proposition, discover EGI & collections
     * - Guest: Platform introduction, registration CTA
     *
     * @param User $company The company whose profile is being viewed
     * @return string HTML message for sidebar context
     */
    protected function generateCompanySidebarContext(User $company): string
    {
        $isOwner    = auth()->check() && auth()->id() === $company->id;
        $isLoggedIn = auth()->check();
        $data       = ['company_name' => $company->name];

        if ($isOwner) {
            $created_count     = $company->createdEgis()->where('is_published', true)->count();
            $collections_count = $company->collections()->where('creator_id', $company->id)->where('is_published', true)->count();
            $total_value       = $company->createdEgis()->where('is_published', true)->sum('price');

            return $this->renderSidebarMessage('company.portfolio.sidebar_contexts.owner', array_merge($data, [
                'name'              => $company->name,
                'created_count'     => $created_count,
                'collections_count' => $collections_count,
                'total_value'       => number_format($total_value, 2),
            ]));
        }

        if ($isLoggedIn) {
            return $this->renderSidebarMessage('company.portfolio.sidebar_contexts.visitor', array_merge($data, [
                'name' => auth()->user()->name,
            ]));
        }

        return $this->renderSidebarMessage('company.portfolio.sidebar_contexts.guest', $data);
    }

    /**
     * Render sidebar message from translation keys with variable interpolation.
     *
     * @param string $contextPath Translation path (e.g., 'company.portfolio.sidebar_contexts.owner')
     * @param array  $data        Variables to interpolate
     * @return string HTML message
     */
    protected function renderSidebarMessage(string $contextPath, array $data): string
    {
        $context = __("ai_contexts.{$contextPath}");

        if (!is_array($context)) {
            return '<p>' . __('ai_sidebar.default_message') . '</p>';
        }

        $html = [];

        if (isset($context['greeting'])) {
            $html[] = '<p>' . $this->interpolate($context['greeting'], $data) . '</p>';
        }

        if (isset($context['intro'])) {
            $html[] = '<p>' . $this->interpolate($context['intro'], $data) . '</p>';
        }

        // Status section (Owner only)
        if (isset($context['status_title'])) {
            $html[] = '<p class="mt-3"><strong>' . $context['status_title'] . '</strong></p>';
            $html[] = '<ul class="ml-4 list-disc space-y-1 text-gray-300">';
            foreach (['status_created', 'status_collections', 'status_value'] as $key) {
                if (isset($context[$key])) {
                    $html[] = '<li>' . $this->interpolate($context[$key], $data) . '</li>';
                }
            }
            $html[] = '</ul>';
        }

        // Actions section (Owner only)
        if (isset($context['actions_title']) && isset($context['actions'])) {
            $html[] = '<p class="mt-3"><strong>' . $context['actions_title'] . '</strong></p>';
            $html[] = '<ul class="ml-4 list-disc space-y-1 text-sm text-gray-300">';
            foreach ($context['actions'] as $action) {
                $html[] = '<li>' . $action . '</li>';
            }
            $html[] = '</ul>';
        }

        // Value Props (Visitor only)
        if (isset($context['value_title']) && isset($context['value_props'])) {
            $html[] = '<p class="mt-3"><strong>' . $context['value_title'] . '</strong></p>';
            $html[] = '<ul class="ml-4 list-disc space-y-1 text-sm text-gray-300">';
            foreach ($context['value_props'] as $prop) {
                $html[] = '<li>' . $prop . '</li>';
            }
            $html[] = '</ul>';
        }

        // Why Join (Guest only)
        if (isset($context['why_join_title']) && isset($context['why_join_reasons'])) {
            $html[] = '<p class="mt-3"><strong>' . $context['why_join_title'] . '</strong></p>';
            $html[] = '<ul class="ml-4 list-disc space-y-1 text-sm text-gray-300">';
            foreach ($context['why_join_reasons'] as $reason) {
                $html[] = '<li>' . $reason . '</li>';
            }
            $html[] = '</ul>';
        }

        if (isset($context['help_offer'])) {
            $html[] = '<p class="mt-3">' . $this->interpolate($context['help_offer'], $data) . '</p>';
        }

        if (isset($context['cta'])) {
            $html[] = '<p class="mt-2 text-sm text-indigo-300">' . $this->interpolate($context['cta'], $data) . '</p>';
        }

        if (isset($context['cta_register'])) {
            $html[] = '<div class="mt-4">' . $context['cta_register'] . '</div>';
        }

        return implode("\n", $html);
    }

    /**
     * Interpolate :placeholder variables in a translation string.
     *
     * @param string $string String with :var placeholders
     * @param array  $data   Associative array of variables
     * @return string Interpolated string
     */
    protected function interpolate(string $string, array $data): string
    {
        foreach ($data as $key => $value) {
            $string = str_replace(":{$key}", $value ?? '', $string);
        }
        return $string;
    }

    /**
     * Applica ordinamento
     */
    private function applyPortfolioSorting($query, string $sort) {
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
    private function buildPortfolioStats(SupportCollection $egis, string $mode, int $companyCollectionsCount): array {
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
