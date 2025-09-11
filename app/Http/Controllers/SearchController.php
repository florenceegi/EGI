<?php

namespace App\Http\Controllers;

use App\Services\UniversalSearchService;
use Illuminate\Http\Request;

class SearchController extends Controller {
    public function __construct(protected UniversalSearchService $service) {
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
