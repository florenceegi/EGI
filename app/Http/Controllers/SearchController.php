<?php

namespace App\Http\Controllers;

use App\Services\UniversalSearchService;
use Illuminate\Http\Request;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

class SearchController extends Controller {
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;

    public function __construct(
        protected UniversalSearchService $service,
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
    }

    public function panel(Request $request) {
        // Endpoint JSON per il componente live (quick panel)
        $q = $request->get('q', '');
        $suggestions = $q ? $this->service->suggestions($q) : [];
        return response()->json([
            'suggestions' => $suggestions,
            'facets' => $this->service->traitFacets(),
        ]);
    }

    public function results(Request $request) {
        $q = $request->get('q');
        // Se l'utente ha cambiato query ma è rimasto su una pagina >1, reindirizziamo a page=1 per evitare impressione di "cache"
        $prevQ = $request->session()->get('last_search_q');
        $page = (int) $request->get('page', 1);
        if ($q !== null && $prevQ !== null && $q !== $prevQ && $page > 1) {
            $request->session()->put('last_search_q', $q);
            return redirect()->to(url('/search/results') . '?' . http_build_query(array_merge($request->except('page'), ['page' => 1])));
        }
        // Aggiorna la query corrente in session per il prossimo confronto
        if ($q !== null) {
            $request->session()->put('last_search_q', $q);
        }
        $types = array_filter(explode(',', $request->get('types', '')));
        if (!$types) {
            $types = ['egi', 'collection', 'creator'];
        }
        $traits = array_filter($request->get('traits', []));
        $userTypes = array_filter($request->get('user_types', []));
        $collections = array_filter($request->get('collections', []));

        $perPage = (int) $request->get('per_page', config('search.per_page'));

        $egiResults = in_array('egi', $types) ? $this->service->searchEgis(compact('q', 'traits', 'collections', 'perPage')) : null;
        $collectionResults = in_array('collection', $types) ? $this->service->searchCollections(compact('q', 'perPage')) : null;
        $creatorResults = in_array('creator', $types) ? $this->service->searchCreators(['q' => $q, 'traits' => $traits, 'user_types' => $userTypes, 'per_page' => $perPage]) : null;

        $facets = $this->service->traitFacets();

        // Diagnostic: log mismatch tra total() e count() se anomalo
        foreach (
            [
                'egis' => $egiResults,
                'collections' => $collectionResults,
                'creators' => $creatorResults,
            ] as $k => $paginator
        ) {
            if ($paginator) {
                try {
                    $global = method_exists($paginator, 'total') ? $paginator->total() : null;
                    $page = $paginator->count();
                    if ($global !== null && $global < $page) {
                        $this->logger->warning('Search paginator mismatch', [
                            'type' => $k,
                            'global_total' => $global,
                            'page_count' => $page,
                            'ids' => $paginator->pluck('id'),
                            'log_category' => 'SEARCH_DEBUG'
                        ]);
                    }
                } catch (\Throwable $e) {
                    $this->logger->error('Search paginator diagnostics failed', [
                        'type' => $k, 
                        'error' => $e->getMessage(),
                        'log_category' => 'SEARCH_ERROR'
                    ]);
                }
            }
        }

        return view('search.results', compact(
            'q',
            'types',
            'traits',
            'userTypes',
            'collections',
            'egiResults',
            'collectionResults',
            'creatorResults',
            'facets'
        ));
    }
}
