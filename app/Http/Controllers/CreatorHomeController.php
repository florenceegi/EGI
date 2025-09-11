<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Collection;
use App\Models\Egi;
use Illuminate\Http\Request;
use Illuminate\View\View;

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
    /**
     * Portfolio del Creator: mostra tutti gli EGI CREATI dal creator
     *
     * IMPORTANTE: Questo portfolio mostra le OPERE CREATE, non acquistate
     * - Mostra EGI delle collezioni dove il creator è il 'creator_id'
     * - Include statistiche di vendita/prenotazioni per ogni EGI
     * - Differente dal portfolio Collector che mostra EGI acquistati
     */
    public function portfolio(int $id, Request $request): View {
        $creator = User::findOrFail($id);
        if (!$creator->hasRole('creator')) {
            abort(404);
        }

        $query = $request->input('query');
        $collection_filter = $request->input('collection');
        $sort = $request->input('sort', 'latest');
        $view = $request->input('view', 'grid');

        // Recupera tutti gli EGI pubblicati delle collezioni CREATE dal creator
        // Nota: creator_id identifica CHI HA CREATO la collezione, non chi la possiede
        $egis = Egi::with([
            'collection',
            'traits.category', // eager loading categoria per badge
            'reservations' => function ($q) {
            $q->where('is_current', true); // Solo prenotazioni attive
            }
        ])
            ->whereHas('collection', function ($q) use ($creator) {
                $q->where('creator_id', $creator->id); // Collection create dal creator
            })
            ->where('is_published', true)
            ->when($query, function ($q) use ($query) {
                $q->where('title', 'like', "%$query%");
            })
            ->when($collection_filter, function ($q) use ($collection_filter) {
                $q->where('collection_id', $collection_filter);
            });

        // Ordinamento
        switch ($sort) {
            case 'title':
                $egis = $egis->orderBy('title');
                break;
            case 'price_high':
                $egis = $egis->orderByDesc('price');
                break;
            case 'price_low':
                $egis = $egis->orderBy('price');
                break;
            case 'latest':
            default:
                $egis = $egis->orderByDesc('created_at');
                break;
        }

        $egis = $egis->get();

        // Statistiche del Creator Portfolio (opere CREATE, non acquistate)
        $stats = [
            'total_egis' => $egis->count(),
            'total_collections' => $creator->collections()->where('creator_id', $creator->id)->count(),
            'total_value_eur' => $egis->sum('price'),
            'total_reservations' => $egis->sum(function ($egi) {
                return $egi->reservations->count();
            }),
            'highest_offer' => $egis->flatMap->reservations->max('offer_amount_fiat') ?? 0,
            'available_egis' => $egis->filter(function ($egi) {
                return $egi->reservations->isEmpty(); // EGI senza prenotazioni
            })->count(),
            'reserved_egis' => $egis->filter(function ($egi) {
                return $egi->reservations->isNotEmpty(); // EGI con prenotazioni
            })->count(),
        ];

        // Statistiche avanzate per i widget usando PaymentDistribution
        $advancedStats = null;
        try {
            $advancedStats = \App\Models\PaymentDistribution::getCreatorPortfolioStats($creator->id);
        } catch (\Exception $e) {
            // Fallback silenzioso se non ci sono dati PaymentDistribution
            \Log::warning("Could not load advanced stats for creator {$creator->id}: " . $e->getMessage());
        }

        return view('creator.portfolio', compact('creator', 'egis', 'stats', 'advancedStats', 'query', 'collection_filter', 'sort', 'view'));
    }
    /**
     * @Oracode Method: Display Creator Home Page
     * 🎯 Purpose: Show creator's main showcase page
     * 📤 Output: Creator home view with stats and featured content
     */
    public function home(int $id): View {
        $creator = User::with(['collections' => function ($query) {
            $query->where('is_published', true)
                ->latest()
                ->take(6);
        }])
            ->findOrFail($id);

        // Verifica se l'utente è un creator
        if (!$creator->hasRole('creator')) {
            abort(404);
        }

        // Stats del creator con supporto per animazioni
        $stats = [
            'total_collections' => $creator->collections()->count(),
            'total_egis' => Egi::whereHas('collection', function ($q) use ($creator) {
                $q->where('user_id', $creator->id);
            })->count(),
            'total_likes' => 0, // TODO: Implementare quando avremo il sistema di likes
            'total_supporters' => 0, // TODO: Implementare con il sistema patronage
            'impact_score' => 0, // TODO: Calcolare basandosi su EPP
        ];

        // Aggiungi flag per animazioni se i numeri sono grandi
        $stats['animate'] = max($stats) > 10;

    $featuredEgis = Egi::with(['collection', 'traits.category'])
            ->where('is_published', true) // <-- Riga corretta
            ->whereHas('collection', function ($q) use ($creator) {
                $q->where('creator_id', $creator->id)
                    ->where('is_published', true);
            })
            ->latest()
            ->take(8)
            ->get();

        return view('creator.home', compact('creator', 'stats', 'featuredEgis'));
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
     * @package App\Http\Controllers\User
     * @author Padmin D. Curtis (AI Partner OS2.0-Compliant) for Fabio Cherici
     * @version 1.1.0 (FlorenceEGI MVP [Refactor])
     * @date 2025-06-29
     *
     * @Oracode Method: Redirect to Creator's Collections
     * 🎯 Purpose: Redirect to the collections index, filtered by this creator.
     * 📤 Output: A redirect response.
     */
    public function collections(int $id): \Illuminate\Http\RedirectResponse {
        $creator = User::findOrFail($id);

        if (!$creator->hasRole('creator')) {
            abort(404);
        }

        // 📜 Logica Oracode: Non costruiamo la vista qui. Deleghiamo.
        // Reindirizziamo alla rotta 'collections.index' (gestita da CollectionController)
        // passando l'ID del creator come parametro di query.
        // In questo modo, CreatorController non deve sapere nulla della vista delle collezioni.
        return redirect()->route('collections.index', ['creator' => $creator->id]);
    }

    /**
     * @Oracode Method: Show Single Collection
     * 🎯 Purpose: Display specific collection details
     * 📤 Output: Redirect to existing collection show route
     */
    public function showCollection(int $id) {
        $creator = User::findOrFail($id);

        // La query ora cerca per ID della collezione, garantendo anche che appartenga al creator corretto.
        $collection = Collection::where('creator_id', $creator->id)->firstOrFail();

        // Redirect alla route esistente per mostrare la collection, passando l'oggetto collection
        // che ha lo slug corretto (se la route 'home.collections.show' lo richiede) o l'ID.
        // Assumendo che 'home.collections.show' si aspetti lo slug (o l'ID),
        // il redirect è ora più robusto. Se 'home.collections.show' vuole l'ID, useremo $collection->id
        return redirect()->route('home.collections.show', $collection); // Laravel userà lo slug o l'id in base alla definizione della route
    }

    /**
     * @Oracode Method: Under Construction Page
     * 🎯 Purpose: Placeholder for future sections
     * 📤 Output: Coming soon page with back navigation
     */
    public function underConstruction(int $id): View {
        $creator = User::findOrFail($id);

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
}
