<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\Egi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * @Oracode Controller: Creator Home Page Management
 * 🎯 Purpose: Handle creator's public profile and showcase pages
 * 🛡️ Security: Public access with privacy controls
 * 🧱 Core Logic: Display creator's work with progressive enhancement
 *
 * Architecture Note:
 * - Creator Portfolio = EGI CREATI dal creator (con status vendite/prenotazioni)
 * - Collection Portfolio = EGI ACQUISTATI dal creator (quando agisce da collector)
 * - Un Creator contiene naturalmente le funzionalità di Collector (usertype hierarchical)
 *
 * @package App\Http\Controllers
 * @author Padmin D. Curtis (AI Partner OS2.0-Compliant) for Fabio Cherici
 * @version 2.0.0 (Usertype Architecture Compliant)
 * @date 2025-08-10
 */
class CreatorHomeController extends Controller {
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
    }

    /**
     * Risolve un creator da ID numerico o nick_name
     *
     * @param string|int $identifier
     * @return User
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    private function resolveCreator($identifier): User {
        if (is_numeric($identifier)) {
            return User::findOrFail($identifier);
        } else {
            // Decodifica l'identifier per gestire spazi e caratteri URL-encoded
            $decodedIdentifier = urldecode($identifier);
            return User::where('nick_name', $decodedIdentifier)->firstOrFail();
        }
    }

    /**
     * Portfolio del Creator: mostra tutti gli EGI CREATI dal creator
     *
     * IMPORTANTE: Questo portfolio mostra le OPERE CREATE, non acquistate
     * - Mostra EGI delle collezioni dove il creator è il 'creator_id'
     * - Include statistiche di vendita/prenotazioni per ogni EGI
     * - Differente dal portfolio Collector che mostra EGI acquistati
     */
    public function portfolio($id, Request $request) {
        $creator = $this->resolveCreator($id);
        if (!$creator->hasRole('creator')) {
            abort(404);
        }

        $query = $request->input('query');
        $collection_filter = $request->input('collection');
        $sort = $request->input('sort', 'latest');
        $view = $request->input('view', 'grid');
        $requestedMode = $request->input('mode', 'created');
        // Allow everyone to switch between created and owned modes (public information)
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

        // EGIs creati dal creator
        $createdQuery = Egi::with($baseWith)
            ->whereHas('collection', function ($q) use ($creator) {
                $q->where('creator_id', $creator->id);
            })
            ->where('is_published', true)
            ->when($query, function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%");
            })
            ->when($collection_filter, function ($q) use ($collection_filter) {
                $q->where('collection_id', $collection_filter);
            })
            // EXCLUDE CLONES from the "Created" tab
            // Clones appear in "Owned" if the creator owns them, otherwise they are Buyer's property
            ->whereNull('parent_id');

        $createdQuery = $this->applyPortfolioSorting($createdQuery, $sort);
        $createdEgis = $createdQuery->get();

        // EGIs posseduti attualmente dal creator (mint o secondary market)
        // Always load owned EGIs for public display
        // Exclude EGIs created by the creator (only show purchased from others)
        // 🔒 Privacy: Owner viewing own portfolio sees all EGIs (including unpublished)
        $isOwnerViewing = auth()->check() && auth()->id() === $creator->id;

        $ownedQuery = Egi::with($baseWith)
            ->where('owner_id', $creator->id)
            ->whereDoesntHave('collection', function ($q) use ($creator) {
                $q->where('creator_id', $creator->id);
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

        $creatorCollectionsCount = $creator->collections()->where('creator_id', $creator->id)->count();

        $createdStats = $this->buildPortfolioStats($createdEgis, 'created', $creatorCollectionsCount);
        $ownedStats = $this->buildPortfolioStats($ownedEgis, 'owned', $creatorCollectionsCount);

        $displayEgis = $portfolioMode === 'owned' ? $ownedEgis : $createdEgis;
        $portfolioStats = $portfolioMode === 'owned' ? $ownedStats : $createdStats;

        // Check if AJAX request
        if ($request->ajax() || $request->get('partial')) {
            return view('creator.partials.portfolio-content', [
                'creator' => $creator,
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

        // Full page view with new layout
        return view('creator.home-spa', [
            'creator' => $creator,
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
     * @Oracode Method: Display Creator Home Page - Redirect to Portfolio
     * 🎯 Purpose: Redirect to portfolio (default section)
     * 📤 Output: Redirect to creator.portfolio
     */
    public function home($id) {
        // Redirect to portfolio as default view
        return redirect()->route('creator.portfolio', $id);
    }
    /**
     * Visualizza la pagina indice dei Creator con filtri e paginazione.
     *
     * 🎯 Permette agli utenti di scoprire e filtrare i Creator disponibili.
     * 📥 Accetta parametri di ricerca (query, category, etc.) dalla Request.
     * 📤 Restituisce la vista 'creators.index' popolata con i Creator filtrati e paginati.
     *
     * @param Request $request La richiesta HTTP contenente i parametri di filtro.
     * @return View La vista 'creators.index' con i dati dei Creator.
     */
    public function index(Request $request): View {
        $query = $request->input('query');
        $category = $request->input('category'); // Esempio di filtro per categoria
        $sort = $request->input('sort', 'latest'); // Ordine di default: 'latest'

        $creators = User::where('usertype', 'creator')
            // Assumiamo che i creator debbano essere "attivi" o "pubblicati"
            // Se non esiste un campo 'is_active' o 'is_published', puoi ometterlo per ora
            // ->where('is_active', true)
            ->when($query, function ($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%')
                    ->orWhere('bio', 'like', '%' . $query . '%'); // Cerca anche nella bio
            })
            ->when($category, function ($q) use ($category) {
                // TODO: Implementare logica di filtro per categoria.
                // Questo potrebbe richiedere una relazione many-to-many tra User e Category,
                // o un campo 'category' diretto nel modello User.
                // Per ora, è un placeholder.
                // $q->whereHas('categories', function ($sq) use ($category) {
                //     $sq->where('slug', $category);
                // });
            })
            ->when($sort, function ($q) use ($sort) {
                switch ($sort) {
                    case 'latest':
                        $q->latest(); // Ordina per data di creazione più recente
                        break;
                    case 'oldest':
                        $q->oldest(); // Ordina per data di creazione meno recente
                        break;
                    case 'name_asc':
                        $q->orderBy('name', 'asc'); // Ordina per nome A-Z
                        break;
                    case 'name_desc':
                        $q->orderBy('name', 'desc'); // Ordina per nome Z-A
                        break;
                    case 'random':
                        $q->inRandomOrder(); // Ordine casuale
                        break;
                        // TODO: Aggiungere altri criteri di ordinamento se necessari (es. per numero di collezioni, popolarità)
                }
            }, function ($q) {
                $q->latest(); // Default se nessun sort è specificato
            })
            ->paginate(12); // Paginazione: 12 creator per pagina

        // Puoi passare anche le categorie disponibili per il filtro, se implementate
        $categories = []; // TODO: Recuperare le categorie reali se necessario

        return view('creator.index', [
            'creators' => $creators,
            'categories' => $categories,
            'filters' => $request->only(['query', 'category', 'sort']) // Passa i filtri attivi alla vista
        ]);
    }

    /**
     * @Oracode Method: Display Creator's Collections (SPA)
     * 🎯 Purpose: Show collections in new SPA layout
     * 📤 Output: Collections view or partial
     */
    public function collections($id, Request $request) {
        return $this->collectionsSection($id, $request);
    }

    /**
     * @Oracode Method: Show Single Collection
     * 🎯 Purpose: Display specific collection details
     * 📤 Output: Redirect to existing collection show route
     */
    public function showCollection($id) {
        $creator = $this->resolveCreator($id);

        // La query ora cerca per ID della collezione, garantendo anche che appartenga al creator corretto.
        $collection = Collection::where('creator_id', $creator->id)->firstOrFail();

        // Redirect alla route esistente per mostrare la collection, passando l'oggetto collection
        // che ha lo slug corretto (se la route 'home.collections.show' lo richiede) o l'ID.
        // Assumendo che 'home.collections.show' si aspetti lo slug (o l'ID),
        // il redirect è ora più robusto. Se 'home.collections.show' vuole l'ID, useremo $collection->id
        return redirect()->route('home.collections.show', $collection); // Laravel userà lo slug o l'id in base alla definizione della route
    }

    /**
     * @Oracode Method: Collections Section
     * 🎯 Purpose: Display creator's collections
     * 📤 Output: Collections view or partial
     */
    public function collectionsSection($id, Request $request) {
        $creator = $this->resolveCreator($id);
        if (!$creator->hasRole('creator')) {
            abort(404);
        }

        $collections = $creator->collections()
            ->where('is_published', true)
            ->withCount('originalEgis')
            ->latest()
            ->get();

        $stats = [
            'total_collections' => $collections->count(),
            'total_egis' => $collections->sum('original_egis_count'),
            'total_supporters' => 0,
        ];

        // Check if AJAX request
        if ($request->ajax() || $request->get('partial')) {
            return view('creator.partials.collections-content', compact('creator', 'collections', 'stats'));
        }

        return view('creator.home-spa', compact('creator', 'collections', 'stats'))
            ->with('activeTab', 'collections');
    }

    /**
     * @Oracode Method: Biography Section
     * 🎯 Purpose: Display creator's biography
     * 📤 Output: Biography view or partial
     */
    public function biography($id, Request $request) {
        $creator = $this->resolveCreator($id);
        if (!$creator->hasRole('creator')) {
            abort(404);
        }

        $stats = [
            'total_collections' => $creator->collections()->count(),
            'total_egis' => $creator->createdEgis()->count(),
            'total_supporters' => 0,
        ];

        // Check if AJAX request
        if ($request->ajax() || $request->get('partial')) {
            return view('creator.partials.biography-content', compact('creator', 'stats'));
        }

        return view('creator.home-spa', compact('creator', 'stats'))
            ->with('activeTab', 'biography');
    }

    /**
     * @Oracode Method: Impact Section
     * 🎯 Purpose: Display creator's environmental impact
     * 📤 Output: Impact view or partial
     */
    public function impact($id, Request $request) {
        $creator = $this->resolveCreator($id);
        if (!$creator->hasRole('creator')) {
            abort(404);
        }

        $stats = [
            'total_collections' => $creator->collections()->count(),
            'total_egis' => $creator->createdEgis()->count(),
            'total_supporters' => 0,
            'impact_score' => 0, // TODO: Calculate from EPP
        ];

        // Check if AJAX request
        if ($request->ajax() || $request->get('partial')) {
            return view('creator.partials.impact-content', compact('creator', 'stats'));
        }

        return view('creator.home-spa', compact('creator', 'stats'))
            ->with('activeTab', 'impact');
    }

    /**
     * @Oracode Method: Community Section
     * 🎯 Purpose: Display creator's community/supporters
     * 📤 Output: Community view or partial
     */
    public function community($id, Request $request) {
        $creator = $this->resolveCreator($id);
        if (!$creator->hasRole('creator')) {
            abort(404);
        }

        $stats = [
            'total_collections' => $creator->collections()->count(),
            'total_egis' => $creator->createdEgis()->count(),
            'total_supporters' => 0,
        ];

        // Check if AJAX request
        if ($request->ajax() || $request->get('partial')) {
            return view('creator.partials.community-content', compact('creator', 'stats'));
        }

        return view('creator.home-spa', compact('creator', 'stats'))
            ->with('activeTab', 'community');
    }

    /**
     * @Oracode Method: Under Construction Page
     * 🎯 Purpose: Placeholder for future sections
     * 📤 Output: Coming soon page with back navigation
     */
    public function underConstruction($id): View {
        $creator = $this->resolveCreator($id);

        if (!$creator->hasRole('creator')) {
            abort(404);
        }

        $section = request()->route()->getName();
        $sectionName = str_replace('creator.', '', $section);

        return view('creator.under-construction', [
            'creator' => $creator,
            'section' => ucfirst($sectionName)
        ]);
    }

    /**
     * Applica l'ordinamento selezionato alla query del portfolio.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $sort
     * @return \Illuminate\Database\Eloquent\Builder
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
     * Costruisce le statistiche da mostrare nella vista portfolio, distinguendo
     * tra opere create e opere possedute.
     *
     * @param SupportCollection $egis
     * @param string $mode
     * @param int $creatorCollectionsCount
     * @return array<string, int|float>
     */
    private function buildPortfolioStats(SupportCollection $egis, string $mode, int $creatorCollectionsCount): array {
        $stats = [
            'total_egis' => $egis->count(),
            'total_collections' => $mode === 'owned'
                ? $egis->pluck('collection_id')->filter()->unique()->count()
                : $creatorCollectionsCount,
            'total_value_eur' => $egis->sum('price'),
            'total_reservations' => $egis->sum(static function ($egi) {
                return $egi->reservations->count();
            }),
            'highest_offer' => $egis->flatMap->reservations->max('offer_amount_fiat') ?? 0,
            'available_egis' => $egis->filter(static function ($egi) {
                return $egi->reservations->isEmpty();
            })->count(),
            'reserved_egis' => $egis->filter(static function ($egi) {
                return $egi->reservations->isNotEmpty();
            })->count(),
        ];

        return $stats;
    }
}
