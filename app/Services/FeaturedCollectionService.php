<?php

namespace App\Services;

use App\Models\Collection;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection as IlluminateCollection;
use Illuminate\Support\Facades\DB;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * FeaturedCollectionService
 *
 * 🎯 Gestisce la selezione e l'ordinamento delle Collection in evidenza per il carousel guest
 * 📊 Calcola l'impatto stimato basato sulle prenotazioni più alte di ciascun EGI
 * 🏆 Applica logica di override manuale tramite featured_position
 *
 * @package App\Services
 */
class FeaturedCollectionService {
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
     * EPP ID da considerare per il calcolo dell'impatto (MVP: solo EPP id=2)
     */
    private const TARGET_EPP_ID = 2;

    /**
     * Percentuale EPP applicata alle prenotazioni (20%)
     */
    private const EPP_PERCENTAGE = 0.20;

    /**
     * Numero massimo di Collection nel carousel
     */
    private const MAX_CAROUSEL_ITEMS = 10;

    /**
     * Ottiene le Collection in evidenza per il carousel guest
     *
     * 🎯 Applica l'algoritmo di selezione completo:
     * 1. Filtra per featured_in_guest = true e is_published = true
     * 2. Ordina per featured_position (se presente), poi per impatto stimato
     * 3. Limita a massimo 10 elementi
     *
     * @param int $limit Numero massimo di Collection da restituire
     * @return IlluminateCollection Collection di Collection con impatto calcolato
     */
    public function getFeaturedCollections(int $limit = self::MAX_CAROUSEL_ITEMS): IlluminateCollection {
        try {
            // ⚡ PERFORMANCE FIX: Selezione randomica semplice senza calcoli pesanti
            // Eliminiamo completamente le query alle reservations per ora
            $candidateCollections = Collection::where('is_published', true)
                ->where('featured_in_guest', true) // Solo collections featured
                ->with(['creator']) // Solo creator, niente egis.reservations
                ->withCount('egis') // Solo conteggio egis
                ->inRandomOrder() // ⚡ Selezione randomica
                ->take($limit) // Prendiamo direttamente il numero richiesto
                ->get();

            // ⚡ SEMPLIFICATO: Nessun calcolo di impatto, solo ordinamento per posizione
            $collections = $candidateCollections->sortBy([
                // Ordinamento per posizione forzata
                function ($collection) {
                    return $collection->featured_position ?? 999;
                }
            ]);            // Log per debugging in ambiente di sviluppo
            if (config('app.debug')) {
                $this->logger->info('Featured Collections retrieved', [
                    'count' => $collections->count(),
                    'collections' => $collections->map(function ($collection) {
                        return [
                            'id' => $collection->id,
                            'name' => $collection->collection_name,
                            'featured_position' => $collection->featured_position,
                            'egis_count' => $collection->egis_count,
                        ];
                    })->toArray()
                ]);
            }

            return $collections;
        } catch (\Exception $e) {
            $this->logger->error('Error retrieving featured collections', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Fallback: restituisce Collection senza il calcolo dell'impatto
            return $this->getFallbackFeaturedCollections($limit);
        }
    }

    /**
     * Ottiene Collection casuali per il carousel guest (SENZA filtro featured_in_guest)
     *
     * 🎯 Selezione completamente random per sviluppo e test:
     * 1. Filtra solo per is_published = true
     * 2. Ordinamento casuale
     * 3. Include tutte le Collection, anche quelle con media Spatie
     *
     * @param int $limit Numero massimo di Collection da restituire
     * @return IlluminateCollection Collection di Collection casuali
     */
    public function getRandomCollections(int $limit = self::MAX_CAROUSEL_ITEMS): IlluminateCollection {
        try {
            $collections = Collection::where('is_published', true)
                ->whereHas('creator', function ($query) {
                    $query->where('usertype', 'creator'); // Escludi EPP e PA Entity
                })
                ->with(['creator', 'media']) // Include anche media per verificare immagini
                ->withCount('egis') // Conteggio egis
                ->inRandomOrder() // Ordinamento casuale
                ->take($limit)
                ->get();

            // Log per debugging in ambiente di sviluppo
            if (config('app.debug')) {
                $this->logger->info('Random Collections retrieved', [
                    'count' => $collections->count(),
                    'collections' => $collections->map(function ($collection) {
                        return [
                            'id' => $collection->id,
                            'name' => $collection->collection_name,
                            'egis_count' => $collection->egis_count,
                            'has_media' => $collection->media->count() > 0,
                            'has_banner' => !empty($collection->image_banner),
                        ];
                    })->toArray()
                ]);
            }

            return $collections;
        } catch (\Exception $e) {
            $this->logger->error('Error retrieving random collections', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Fallback: usa il metodo getFallbackFeaturedCollections ma senza filtro featured
            return $this->getFallbackRandomCollections($limit);
        }
    }

    /**
     * Calcola l'impatto stimato per una Collection specifica
     *
     * 🎯 Utilizza la stessa logica del carousel ma per una singola Collection
     * 📊 Somma delle quote EPP (20%) delle prenotazioni più alte per EGI
     *
     * @param Collection $collection La Collection di cui calcolare l'impatto
     * @return float L'impatto stimato in EUR
     */
    public function calculateEstimatedImpact(Collection $collection): float {
        try {
            $impact = $collection->egis()
                ->whereHas('reservations', function (Builder $query) {
                    $query->where('is_current', true);
                })
                ->with(['reservations' => function ($query) {
                    $query->where('is_current', true)
                        ->orderBy('offer_amount_fiat', 'desc')
                        ->orderBy('created_at', 'asc'); // Tiebreaker per stesso importo
                }])
                ->get()
                ->sum(function ($egi) {
                    // Ottieni la prenotazione con l'offerta più alta per questo EGI
                    $highestReservation = $egi->reservations->first();
                    if (!$highestReservation) {
                        return 0;
                    }

                    // Calcola la quota EPP
                    return $highestReservation->offer_amount_fiat * self::EPP_PERCENTAGE;
                });

            return round($impact, 2);
        } catch (\Exception $e) {
            $this->logger->error('Error calculating estimated impact', [
                'collection_id' => $collection->id,
                'error' => $e->getMessage()
            ]);

            return 0.0;
        }
    }

    /**
     * Verifica se una Collection può essere inclusa nel carousel guest
     *
     * @param Collection $collection
     * @return bool True se può essere inclusa
     */
    public function canBeFeaturedinGuest(Collection $collection): bool {
        return $collection->is_published && $collection->featured_in_guest;
    }

    /**
     * Imposta una Collection come in evidenza con posizione opzionale
     *
     * @param Collection $collection
     * @param int|null $position Posizione forzata (1-10) o null per automatica
     * @return bool True se l'operazione è riuscita
     */
    public function setAsFeatured(Collection $collection, ?int $position = null): bool {
        try {
            // Validazione posizione
            if ($position !== null && ($position < 1 || $position > self::MAX_CAROUSEL_ITEMS)) {
                throw new \Exception("Featured position must be between 1 and " . self::MAX_CAROUSEL_ITEMS);
            }

            // Se viene specificata una posizione, verifica conflitti
            if ($position !== null) {
                $existingCollection = Collection::where('featured_position', $position)
                    ->where('id', '!=', $collection->id)
                    ->first();

                if ($existingCollection) {
                    // Sposta la Collection esistente in posizione automatica
                    $existingCollection->update(['featured_position' => null]);
                }
            }

            return $collection->update([
                'featured_in_guest' => true,
                'featured_position' => $position
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Error setting collection as featured', [
                'collection_id' => $collection->id,
                'position' => $position,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Rimuove una Collection dal carousel featured
     *
     * @param Collection $collection
     * @return bool True se l'operazione è riuscita
     */
    public function removeFromFeatured(Collection $collection): bool {
        try {
            return $collection->update([
                'featured_in_guest' => false,
                'featured_position' => null
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Error removing collection from featured', [
                'collection_id' => $collection->id,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Metodo di fallback nel caso di errori nella query principale
     *
     * @param int $limit
     * @return IlluminateCollection
     */
    private function getFallbackFeaturedCollections(int $limit): IlluminateCollection {
        return Collection::where('is_published', true)
            ->where('featured_in_guest', true)
            ->with(['creator'])
            ->withCount('egis')
            ->orderByRaw('CASE WHEN featured_position IS NOT NULL THEN featured_position ELSE 999 END ASC')
            ->orderBy('updated_at', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Metodo di fallback per collezioni random nel caso di errori
     *
     * @param int $limit
     * @return IlluminateCollection
     */
    private function getFallbackRandomCollections(int $limit): IlluminateCollection {
        return Collection::where('is_published', true)
            ->whereHas('creator', function ($query) {
                $query->where('usertype', 'creator');
            })
            ->with(['creator', 'media'])
            ->withCount('egis')
            ->orderBy('updated_at', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Ottiene statistiche sui featured collections per l'admin
     *
     * @return array Array con statistiche
     */
    public function getFeaturedCollectionsStats(): array {
        try {
            $totalFeatured = Collection::where('featured_in_guest', true)->count();
            $withForcedPosition = Collection::where('featured_in_guest', true)
                ->whereNotNull('featured_position')->count();
            $withoutPosition = $totalFeatured - $withForcedPosition;

            return [
                'total_featured' => $totalFeatured,
                'with_forced_position' => $withForcedPosition,
                'automatic_position' => $withoutPosition,
                'max_allowed' => self::MAX_CAROUSEL_ITEMS
            ];
        } catch (\Exception $e) {
            $this->logger->error('Error getting featured collections stats', [
                'error' => $e->getMessage()
            ]);

            return [
                'total_featured' => 0,
                'with_forced_position' => 0,
                'automatic_position' => 0,
                'max_allowed' => self::MAX_CAROUSEL_ITEMS
            ];
        }
    }
}
