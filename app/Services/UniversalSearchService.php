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
class UniversalSearchService
{
    /**
     * Tokenizza la query testuale in termini (>=2 char)
     */
    protected function tokenize(?string $q): array
    {
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
    public function searchEgis(array $params): LengthAwarePaginator
    {
        $qTokens = $this->tokenize($params['q'] ?? null);
        $traits = $params['traits'] ?? [];
        $collectionIds = $params['collections'] ?? [];
        $perPage = $params['per_page'] ?? config('search.per_page');

        $query = Egi::query()
            ->with(['collection.creator', 'user', 'traits.category', 'traits.traitType'])
            ->where(function ($q) {
                $q->where('is_published', true)
                    ->orWhere('is_public', true); // include anche EGI resi pubblici ma non ancora "pubblicati"
            });

        if ($collectionIds) {
            $query->whereIn('collection_id', $collectionIds);
        }

        // Full text LIKE semplice (upgrade future: Scout / Meilisearch)
        foreach ($qTokens as $token) {
            $like = "%" . $token . "%";
            $query->where(function ($sub) use ($like) {
                $sub->where('title', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhereHas('collection.creator', function ($q) use ($like) {
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

        $freshTotal = (clone $query)->count();
        /** @var \Illuminate\Contracts\Pagination\LengthAwarePaginator $paginated */
        $paginated = $query->paginate($perPage);
        $paginated->fresh_total = $freshTotal;
        return $paginated;
    }

    /**
     * Ricerca Collections.
     */
    public function searchCollections(array $params): LengthAwarePaginator
    {
        $qTokens = $this->tokenize($params['q'] ?? null);
        $perPage = $params['per_page'] ?? config('search.per_page');

        $query = Collection::query()->with('creator')->where('is_published', true);

        foreach ($qTokens as $token) {
            $like = "%" . $token . "%";
            $query->where(function ($sub) use ($like) {
                $sub->where('collection_name', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    // Cerca anche per nome/nick del creator
                    ->orWhereHas('creator', function ($q) use ($like) {
                        $q->where('name', 'like', $like)
                            ->orWhere('nick_name', 'like', $like)
                            ->orWhere('last_name', 'like', $like);
                    })
                    // Cerca anche nelle collection dove l'utente compare in collection_users
                    ->orWhereHas('users', function ($q) use ($like) {
                        $q->where('name', 'like', $like)
                            ->orWhere('nick_name', 'like', $like)
                            ->orWhere('last_name', 'like', $like);
                    });
            });
        }

        $freshTotal = (clone $query)->count();
        /** @var \Illuminate\Contracts\Pagination\LengthAwarePaginator $paginated */
        $paginated = $query->paginate($perPage);
        $paginated->fresh_total = $freshTotal;

        // Aggiungi informazioni sul ruolo dell'utente per ogni collection usando makeHidden per evitare conflitti
        $collectionIds = $paginated->pluck('id');
        if ($collectionIds->isNotEmpty() && !empty($qTokens)) {
            $userRolesMap = [];
            foreach ($qTokens as $token) {
                $like = "%" . $token . "%";
                $userRoles = DB::table('collection_user as cu')
                    ->join('users as u', 'u.id', '=', 'cu.user_id')
                    ->select(
                        'cu.collection_id',
                        'u.name',
                        'u.nick_name',
                        'u.last_name',
                        'cu.is_owner',
                        'cu.role'
                    )
                    ->whereIn('cu.collection_id', $collectionIds)
                    ->where('cu.status', '!=', 'removed')
                    ->whereNull('cu.removed_at')
                    ->where(function ($q) use ($like) {
                        $q->where('u.name', 'like', $like)
                            ->orWhere('u.nick_name', 'like', $like)
                            ->orWhere('u.last_name', 'like', $like);
                    })
                    ->get();

                foreach ($userRoles as $ur) {
                    $role = $ur->is_owner ? 'creator' : $ur->role;
                    $userName = $ur->name . ($ur->nick_name ? ' (' . $ur->nick_name . ')' : '');

                    if (!isset($userRolesMap[$ur->collection_id])) {
                        $userRolesMap[$ur->collection_id] = [];
                    }
                    if (!isset($userRolesMap[$ur->collection_id][$userName])) {
                        $userRolesMap[$ur->collection_id][$userName] = [];
                    }
                    if (!in_array($role, $userRolesMap[$ur->collection_id][$userName])) {
                        $userRolesMap[$ur->collection_id][$userName][] = $role;
                    }
                }
            }

            // Assegna i ruoli alle collection usando un attributo temporaneo
            foreach ($paginated as $collection) {
                $collection->setAttribute('search_user_roles', $userRolesMap[$collection->id] ?? []);
            }
        }

        return $paginated;
    }

    /**
     * Ricerca Creators (users) con optional trait filter: selezioniamo creators che hanno EGIs con TUTTI i trait.
     */
    public function searchCreators(array $params): LengthAwarePaginator
    {
        $qTokens = $this->tokenize($params['q'] ?? null);
        $traits = $params['traits'] ?? [];
        $userTypes = $params['user_types'] ?? [];
        $perPage = $params['per_page'] ?? config('search.per_page');

        $query = User::query()
            ->withCount([
                'collections',
                // Conteggio totale EGIs creati (indipendente dallo stato) per riflettere attività reale
                'createdEgis as egis_count'
            ]);

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

        $freshTotal = (clone $query)->count();
        /** @var \Illuminate\Contracts\Pagination\LengthAwarePaginator $paginated */
        $paginated = $query->paginate($perPage);
        $paginated->fresh_total = $freshTotal;

        // --- Aggregazione collection per ruolo (incluso owner come 'creator') ---
        $userIds = $paginated->pluck('id');
        if ($userIds->isNotEmpty()) {
            // MariaDB strict mode richiede group by su tutte le colonne non aggregate: includiamo is_owner e role
            $pivotRows = DB::table('collection_user as cu')
                ->select(
                    'cu.user_id',
                    'cu.is_owner',
                    'cu.role',
                    DB::raw('COUNT(*) as total')
                )
                ->whereIn('cu.user_id', $userIds)
                ->where('cu.status', '!=', 'removed')
                ->whereNull('cu.removed_at')
                ->groupBy('cu.user_id', 'cu.is_owner', 'cu.role')
                ->get()
                ->groupBy('user_id');

            foreach ($paginated as $creator) {
                $rows = $pivotRows->get($creator->id, collect());
                $map = [];
                foreach ($rows as $r) {
                    $roleKey = $r->is_owner ? 'creator' : ($r->role ?: 'unknown');
                    $map[$roleKey] = ($map[$roleKey] ?? 0) + (int) $r->total;
                }
                // Ordina mettendo creator, admin, editor, viewer, resto alfabetico
                if ($map) {
                    $ordered = [];
                    $preferred = ['creator', 'owner', 'admin', 'editor', 'viewer'];
                    foreach ($preferred as $pr) {
                        if (isset($map[$pr])) {
                            $ordered[$pr] = $map[$pr];
                            unset($map[$pr]);
                        }
                    }
                    ksort($map);
                    foreach ($map as $k => $v) {
                        $ordered[$k] = $v;
                    }
                    $creator->collection_role_counts = $ordered;
                } else {
                    $creator->collection_role_counts = [];
                }
            }
        }

        return $paginated;
    }

    /**
     * Facets per trait value organizzati per trait_type (solo quelli presenti su EGIs pubblicati)
     */
    public function traitFacets(array $currentFilters = []): array
    {
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
    public function suggestions(string $q): array
    {
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
