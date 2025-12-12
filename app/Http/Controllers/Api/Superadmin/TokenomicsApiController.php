<?php

namespace App\Http\Controllers\Api\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * @Oracode API Controller: SuperAdmin Tokenomics
 * 🎯 Purpose: API per gestione Egili e Equilibrium
 */
class TokenomicsApiController extends Controller
{
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
     * Get Egili management data
     */
    public function egili(Request $request): JsonResponse
    {
        // Statistiche Egili
        $totalCirculating = User::sum('egili_balance') ?? 0;
        $totalMinted = DB::table('egili_transactions')
            ->where('type', 'mint')
            ->sum('amount') ?? 0;
        $totalBurned = DB::table('egili_transactions')
            ->where('type', 'burn')
            ->sum('amount') ?? 0;
        $holdersCount = User::where('egili_balance', '>', 0)->count();

        // Transazioni recenti
        $transactions = DB::table('egili_transactions')
            ->join('users', 'egili_transactions.user_id', '=', 'users.id')
            ->select('egili_transactions.*', 'users.name as user_name')
            ->latest('egili_transactions.created_at')
            ->take(20)
            ->get()
            ->map(fn($tx) => [
                'id' => $tx->id,
                'user_id' => $tx->user_id,
                'user_name' => $tx->user_name,
                'type' => $tx->type,
                'amount' => $tx->amount,
                'reason' => $tx->reason ?? '',
                'created_at' => $tx->created_at,
            ]);

        return response()->json([
            'stats' => [
                'total_circulating' => (int) $totalCirculating,
                'total_minted' => (int) $totalMinted,
                'total_burned' => (int) $totalBurned,
                'holders_count' => $holdersCount,
            ],
            'transactions' => $transactions,
        ]);
    }

    /**
     * Get Equilibrium data
     */
    public function equilibrium(Request $request): JsonResponse
    {
        // Calcolo indice di equilibrio
        // Formula semplificata: (crediti - debiti) / max(crediti, 1) * 100
        $totalCredits = DB::table('equilibrium_entries')
            ->where('type', 'credit')
            ->sum('amount') ?? 0;
        $totalDebits = DB::table('equilibrium_entries')
            ->where('type', 'debit')
            ->sum('amount') ?? 0;
        
        $balanceIndex = $totalCredits > 0 
            ? (($totalCredits - $totalDebits) / $totalCredits) * 100 
            : 0;

        $lastAdjustment = DB::table('equilibrium_entries')
            ->latest('created_at')
            ->value('created_at');

        $trend = $balanceIndex > 50 ? 'up' : ($balanceIndex < 50 ? 'down' : 'stable');

        // Entries recenti
        $entries = DB::table('equilibrium_entries')
            ->latest('created_at')
            ->take(20)
            ->get()
            ->map(fn($e) => [
                'id' => $e->id,
                'type' => $e->type,
                'amount' => $e->amount,
                'category' => $e->category ?? 'general',
                'description' => $e->description ?? '',
                'created_at' => $e->created_at,
            ]);

        return response()->json([
            'stats' => [
                'balance_index' => round($balanceIndex, 2),
                'total_credits' => (int) $totalCredits,
                'total_debits' => (int) $totalDebits,
                'last_adjustment' => $lastAdjustment,
                'trend' => $trend,
            ],
            'entries' => $entries,
        ]);
    }
}
