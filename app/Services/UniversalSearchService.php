<?php

namespace App\Services;

use App\Models\Egi;
use App\Models\Collection;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Str; // (Non usato al momento)

/**
 * UniversalSearchService
 * Unifica ricerche su EGIs, Collections, Creators con faccette su trait values.
 * Design: stateless, ogni chiamata riceve parametri e produce risultati + facets.
 */
class UniversalSearchService {
    /**
     * Tokenizza la query testuale in termini (>=2 char)
     */
    protected function tokenize(?string $q): array {
        if (!$q) return [];
        $normalized = trim(str_replace(["\n", "\r", "\t"], ' ', $q));
        // Collassa spazi multipli
        while (str_contains($normalized, '  ')) {
            $normalized = str_replace('  ', ' ', $normalized);
        }
        $parts = explode(' ', $normalized);
        return array_values(array_filter(array_unique(array_map(fn($p) => mb_strtolower($p), $parts)), fn($p) => mb_strlen($p) >= 2));
    }

    /**
     * Ricerca EGIs con filtri.
     * @param array $params ['q','traits'=>[],'collections'=>[],'per_page'=>int]
     */
    public function searchEgis(array $params): LengthAwarePaginator {
        $qTokens = $this->tokenize($params['q'] ?? null);
        $traits = $params['traits'] ?? [];
        $collectionIds = $params['collections'] ?? [];
        $perPage = $params['per_page'] ?? config('search.per_page');

        $query = Egi::query()
            ->with(['collection.creator', 'traits.category', 'traits.traitType'])
            ->where('is_published', true);

        if ($collectionIds) {
            $query->whereIn('collection_id', $collectionIds);
        }

        // Full text LIKE semplice (upgrade future: Scout / Meilisearch)
        foreach ($qTokens as $token) {
            $like = "%" . $token . "%";
            $query->where(function ($sub) use ($like) {
                $sub->where('title', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhereHas('collection.creator', function($q) use ($like) {
                        $q->where('name', 'like', $like)
                           ->orWhere('nick_name', 'like', $like)
                           ->orWhere('last_name', 'like', $like);
                    });
            });
        }

        if ($traits) {
            // Intersezione: tutti i trait values richiesti
            $query->whereIn('id', function ($sub) use ($traits) {
                $sub->select('egi_id')
                    ->from('egi_traits')
                    ->whereIn('value', $traits)
                    ->groupBy('egi_id')
                    ->havingRaw('COUNT(DISTINCT value) >= ?', [count(array_unique($traits))]);
            });
        }

        /** @var \Illuminate\Contracts\Pagination\LengthAwarePaginator $paginated */
        $paginated = $query->paginate($perPage);
        return $paginated;
    }

    /**
     * Ricerca Collections.
     */
    public function searchCollections(array $params): LengthAwarePaginator {
        $qTokens = $this->tokenize($params['q'] ?? null);
        $perPage = $params['per_page'] ?? config('search.per_page');

        $query = Collection::query()->with('creator')->where('is_published', true);

        foreach ($qTokens as $token) {
            $like = "%" . $token . "%";
            $query->where(function ($sub) use ($like) {
                $sub->where('collection_name', 'like', $like)
                    ->orWhere('description', 'like', $like);
            });
        }

        /** @var \Illuminate\Contracts\Pagination\LengthAwarePaginator $paginated */
        $paginated = $query->paginate($perPage);
        return $paginated;
    }

    /**
     * Ricerca Creators (users) con optional trait filter: selezioniamo creators che hanno EGIs con TUTTI i trait.
     */
    public function searchCreators(array $params): LengthAwarePaginator {
        $qTokens = $this->tokenize($params['q'] ?? null);
        $traits = $params['traits'] ?? [];
        $userTypes = $params['user_types'] ?? [];
        $perPage = $params['per_page'] ?? config('search.per_page');

        $query = User::query()
            ->withCount(['collections', 'createdEgis as egis_count' => function($q){ $q->where('is_published', true); }]);

        if ($userTypes) {
            $query->whereIn('usertype', $userTypes);
        } else {
            // Limit default to creators only if none specified? Keep broad.
        }

        foreach ($qTokens as $token) {
            $like = "%" . $token . "%";
            $query->where(function ($sub) use ($like) {
                $sub->where('name', 'like', $like)
                    ->orWhere('nick_name', 'like', $like)
                    ->orWhere('last_name', 'like', $like)
                    ->orWhere('wallet', 'like', $like);
            });
        }

        if ($traits) {
            $query->whereIn('id', function ($sub) use ($traits) {
                $sub->select('user_id')
                    ->from('egis')
                    ->whereIn('id', function ($s2) use ($traits) {
                        $s2->select('egi_id')
                            ->from('egi_traits')
                            ->whereIn('value', $traits)
                            ->groupBy('egi_id')
                            ->havingRaw('COUNT(DISTINCT value) >= ?', [count(array_unique($traits))]);
                    });
            });
        }

        /** @var \Illuminate\Contracts\Pagination\LengthAwarePaginator $paginated */
        $paginated = $query->paginate($perPage);
        return $paginated;
    }

    /**
     * Facets per trait value organizzati per trait_type (solo quelli presenti su EGIs pubblicati)
     */
    public function traitFacets(array $currentFilters = []): array {
        // Base query EGIs pubblicati
        $baseEgiIds = Egi::query()->where('is_published', true)->pluck('id');
        if ($baseEgiIds->isEmpty()) return [];

        $rows = DB::table('egi_traits as et')
            ->join('trait_types as tt', 'tt.id', '=', 'et.trait_type_id')
            ->select('tt.name as type_name', 'et.value', DB::raw('COUNT(DISTINCT et.egi_id) as egis_count'))
            ->whereIn('et.egi_id', $baseEgiIds)
            ->groupBy('tt.name', 'et.value')
            ->orderByDesc('egis_count')
            ->limit(5000)
            ->get();

        $out = [];
        foreach ($rows as $r) {
            $out[$r->type_name][$r->value] = (int)$r->egis_count;
        }
        ksort($out);
        return $out;
    }

    /**
     * Quick suggestions (union of top matches across entities)
     */
    public function suggestions(string $q): array {
        $limit = config('search.suggestions_limit');
        $token = mb_strtolower(trim($q));
        if ($token === '') return [];

        $like = "%" . $token . "%";
        $egis = Egi::query()->where('is_published', true)->where('title', 'like', $like)->limit($limit)->get(['id', 'title']);
        $collections = Collection::query()->where('is_published', true)->where('collection_name', 'like', $like)->limit($limit)->get(['id', 'collection_name']);
        $creators = User::query()->where('usertype', 'creator')->where(function ($q2) use ($like) {
            $q2->where('name', 'like', $like)->orWhere('nick_name', 'like', $like)->orWhere('wallet', 'like', $like);
        })->limit($limit)->get(['id', 'name', 'nick_name']);

        return [
            'egis' => $egis,
            'collections' => $collections,
            'creators' => $creators,
        ];
    }
}
