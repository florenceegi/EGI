<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentDistribution;
use App\Models\Egi;
use App\Models\Collection;
use Illuminate\Http\Request;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

class PaymentDistributionStatsController extends Controller {
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
     * Ottieni le statistiche globali di Payment Distribution
     * Utilizzato per aggiornare dinamicamente le statistiche sulla homepage
     */
    public function getGlobalStats(Request $request) {
        try {
            // Calcola le statistiche usando i metodi del modello PaymentDistribution
            $totalEgis = Egi::count();
            $sellEgis = Egi::whereHas('reservations', function ($query) {
                $query->where('is_current', true)->where('status', 'active');
            })->count();

            $distributionStats = PaymentDistribution::getDashboardStats();
            $totalVolume = $distributionStats['overview']['total_amount_distributed'];

            // COLLECTIONS totali
            $totalCollections = Collection::count();

            // SELL COLLECTIONS - quelle con distribuzioni attive
            $sellCollections = PaymentDistribution::join('reservations', 'payment_distributions.reservation_id', '=', 'reservations.id')
                ->join('egis', 'reservations.egi_id', '=', 'egis.id')
                ->distinct('egis.collection_id')
                ->count('egis.collection_id');

            $eppTotal = collect($distributionStats['by_user_type'])
                ->firstWhere('user_type', 'epp')['total_amount'] ?? 0;

            return response()->json([
                'success' => true,
                'data' => [
                    'volume' => $totalVolume,
                    'epp' => $eppTotal,
                    'collections' => $totalCollections,
                    'sell_collections' => $sellCollections,
                    'total_egis' => $totalEgis,
                    'sell_egis' => $sellEgis
                ],
                'formatted' => [
                    'volume' => $totalVolume > 0 ? '€' . number_format($totalVolume, 2) : '€0.00',
                    'epp' => $eppTotal > 0 ? '€' . number_format($eppTotal, 2) : '€0.00',
                    'collections' => number_format($totalCollections),
                    'sell_collections' => number_format($sellCollections),
                    'total_egis' => number_format($totalEgis),
                    'sell_egis' => number_format($sellEgis)
                ]
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Errore nel recupero delle statistiche globali di PaymentDistribution', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'log_category' => 'SYSTEM_MAINTENANCE'
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Errore nel recupero delle statistiche'
            ], 500);
        }
    }

    /**
     * Ottieni le statistiche per una collezione specifica
     */
    public function getCollectionStats(Request $request, $collectionId) {
        try {
            $distributionStats = PaymentDistribution::getDashboardStatsByCollection($collectionId);

            if (!$distributionStats) {
                return response()->json([
                    'success' => false,
                    'error' => 'Collezione non trovata'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $distributionStats
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Errore nel recupero delle statistiche della collezione', [
                'collection_id' => $collectionId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'log_category' => 'SYSTEM_MAINTENANCE'
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Errore nel recupero delle statistiche della collezione'
            ], 500);
        }
    }
}
