<?php

namespace App\Services;

use App\Models\Egi;
use App\Models\User;
use App\Models\Reservation;
use App\Models\Collection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\DB;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * @Oracode Service: PortfolioService
 * 🎯 Purpose: Manages collector portfolio logic with accurate ownership tracking
 * 🚀 Enhancement: Ensures portfolio shows only WINNING reservations
 * 🛡️ GDPR: Handles minimal data necessary for portfolio display
 *
 * @package App\Services
 * @author Padmin D. Curtis (for Fabio Cherici)
 * @version 2.0.0 - Portfolio Fix
 * @date 2025-08-08
 */
class PortfolioService {
    /**
     * @var UltraLogManager
     */
    protected UltraLogManager $logger;

    /**
     * Constructor with dependency injection
     *
     * @param UltraLogManager $logger
     */
    public function __construct(UltraLogManager $logger) {
        $this->logger = $logger;
    }

    /**
     * Get collector's active portfolio (winning reservations + owned EGIs)
     * Criteria:
     * - EGIs con almeno una reservation del collector (attive o storiche)
     * - EGIs dove il collector è owner attuale (mint/rebind) e l'opera è pubblicata
     *
     * @param User $collector The collector
     * @return EloquentCollection Collection of EGIs associated with the collector
     */
    public function getCollectorActivePortfolio(User $collector): EloquentCollection {
        $reservedEgis = $this->getReservedEgisForCollector($collector);
        $ownedEgis = $this->getOwnedEgisForCollector($collector);

        return $reservedEgis
            ->concat($ownedEgis)
            ->unique('id')
            ->values();
    }

    /**
     * Get collector's complete bidding history
     *
     * @param User $collector The collector
     * @return EloquentCollection Collection of all user reservations
     */
    public function getCollectorBiddingHistory(User $collector): EloquentCollection {
        return Reservation::where('user_id', $collector->id)
            ->with(['egi.collection', 'certificate'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get collector portfolio statistics (accurate counts)
     *
     * @param User $collector The collector
     * @return array Portfolio statistics
     */
    public function getCollectorPortfolioStats(User $collector): array {
        $activeEgis = $this->getCollectorActivePortfolio($collector);
        $totalBids = Reservation::where('user_id', $collector->id)->count();
        $activeBids = Reservation::where('user_id', $collector->id)
            ->where('is_current', true)
            ->where('status', 'active')
            ->whereNull('superseded_by_id')
            ->count();

        // Calcola il numero di collezioni diverse rappresentate nel portfolio
        $collectionsRepresented = $activeEgis->pluck('collection_id')->unique()->count();

        return [
            'total_owned_egis' => $activeEgis->count(),
            'collections_represented' => $collectionsRepresented,
            'total_spent_eur' => $activeEgis->sum(function ($egi) {
                if ($egi->reservations->isNotEmpty()) {
                    return $egi->reservations->first()->offer_amount_fiat ?? 0;
                }

                return $egi->price ?? 0;
            }),
            'total_bids_made' => $totalBids,
            'active_winning_bids' => $activeBids,
            'outbid_count' => $totalBids - $activeBids,
        ];
    }

    /**
     * Check for status updates since last check
     *
     * @param User $collector The collector
     * @return array Array of status changes
     */
    public function checkForStatusUpdates(User $collector): array {
        // Get reservations that were recently superseded
        $recentlySuperseded = Reservation::where('user_id', $collector->id)
            ->whereNotNull('superseded_by_id')
            ->where('updated_at', '>=', now()->subMinutes(60)) // Ultimi 60 minuti per test
            ->with(['egi', 'supersededBy.user'])
            ->get();

        $updates = [];
        foreach ($recentlySuperseded as $reservation) {
            $updates[] = [
                'type' => 'outbid',
                'egi_id' => $reservation->egi_id,
                'egi_title' => $reservation->egi->title,
                'old_amount' => $reservation->offer_amount_fiat,
                'new_amount' => $reservation->supersededBy->offer_amount_fiat,
                'superseded_at' => $reservation->updated_at,
                'message' => "Your bid on '{$reservation->egi->title}' has been outbid!"
            ];
        }

        return $updates;
    }

    /**
     * Get available collections for portfolio filtering
     *
     * @param User $collector The collector
     * @return EloquentCollection Available collections
     */
    public function getAvailableCollections(User $collector): EloquentCollection {
        $portfolio = $this->getCollectorActivePortfolio($collector);
        $collectionIds = $portfolio->pluck('collection_id')->filter()->unique()->values();

        if ($collectionIds->isEmpty()) {
            return new EloquentCollection();
        }

        return Collection::whereIn('id', $collectionIds)->get();
    }

    /**
     * Get available creators for portfolio filtering
     *
     * @param User $collector The collector
     * @return EloquentCollection Available creators
     */
    public function getAvailableCreators(User $collector): EloquentCollection {
        $portfolio = $this->getCollectorActivePortfolio($collector);
        $creatorIds = $portfolio
            ->map(function ($egi) {
                return optional($egi->collection)->creator_id;
            })
            ->filter()
            ->unique()
            ->values();

        if ($creatorIds->isEmpty()) {
            return new EloquentCollection();
        }

        return User::whereIn('id', $creatorIds)->get();
    }

    /**
     * Check if collector has winning reservation for specific EGI
     *
     * @param User $collector The collector
     * @param int $egiId The EGI ID
     * @return bool Whether collector has winning reservation
     */
    public function hasWinningReservation(User $collector, int $egiId): bool {
        return Reservation::where('user_id', $collector->id)
            ->where('egi_id', $egiId)
            ->where('is_current', true)
            ->where('status', 'active')
            ->whereNull('superseded_by_id')
            ->exists();
    }

    /**
     * Get collector's reservation status for an EGI
     *
     * @param User $collector The collector
     * @param int $egiId The EGI ID
     * @return array Reservation status information
     */
    public function getEgiReservationStatus(User $collector, int $egiId): array {
        $reservation = Reservation::where('user_id', $collector->id)
            ->where('egi_id', $egiId)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$reservation) {
            return [
                'has_reservation' => false,
                'status' => 'none'
            ];
        }

        $isWinning = $reservation->is_current &&
            $reservation->status === 'active' &&
            !$reservation->superseded_by_id;

        return [
            'has_reservation' => true,
            'status' => $isWinning ? 'winning' : 'outbid',
            'offer_amount_fiat' => $reservation->offer_amount_fiat,
            'created_at' => $reservation->created_at,
            'is_winning' => $isWinning,
            'reservation_id' => $reservation->id
        ];
    }

    /**
     * Recupera gli EGIs riservati dal collector (storico + attuali)
     *
     * @param User $collector
     * @return EloquentCollection
     */
    private function getReservedEgisForCollector(User $collector): EloquentCollection {
        return Egi::whereHas('reservations', function ($query) use ($collector) {
            $query->where('user_id', $collector->id);
        })
            ->with($this->defaultPortfolioRelations($collector))
            ->get();
    }

    /**
     * Recupera gli EGIs posseduti attualmente dal collector (mint/rebind)
     *
     * @param User $collector
     * @return EloquentCollection
     */
    private function getOwnedEgisForCollector(User $collector): EloquentCollection {
        return Egi::where('owner_id', $collector->id)
            ->where('is_published', true)
            ->with($this->defaultPortfolioRelations($collector))
            ->get();
    }

    /**
     * Relazioni predefinite da caricare per il portfolio collector.
     *
     * @param User $collector
     * @return array<int, mixed>
     */
    private function defaultPortfolioRelations(User $collector): array {
        return [
            'collection.creator',
            'user',
            'owner',
            'blockchain.buyer',
            'reservations' => function ($query) use ($collector) {
                $query->where('user_id', $collector->id)
                    ->orderByDesc('created_at')
                    ->with('user');
            },
        ];
    }
}
