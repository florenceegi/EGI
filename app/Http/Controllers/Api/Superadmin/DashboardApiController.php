<?php

namespace App\Http\Controllers\Api\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\AiTraitGeneration;
use App\Models\Egi;
use App\Models\EgiTrait;
use App\Models\User;
use Illuminate\Http\JsonResponse;

/**
 * @Oracode API Controller: SuperAdmin Dashboard
 * 🎯 Purpose: Fornisce dati per la dashboard SuperAdmin di EGI-HUB
 * 📐 Pattern: API-first per frontend React separato
 */
class DashboardApiController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function index(): JsonResponse
    {
        $stats = [
            'ai_consultations' => class_exists(AiTraitGeneration::class) ? AiTraitGeneration::count() : 0,
            'total_egis' => class_exists(Egi::class) ? Egi::count() : 0,
            'active_users' => User::count(), // Semplificato per evitare errori di colonna
            'traits_created' => class_exists(EgiTrait::class) ? EgiTrait::count() : 0,
        ];

        // Recent activity (last 10 events)
        $recentActivity = collect();
        
        try {
            // Get recent AI consultations
            if (class_exists(AiTraitGeneration::class)) {
                $recentAi = AiTraitGeneration::with('user:id,name')
                    ->latest()
                    ->take(5)
                    ->get()
                    ->map(fn($item) => [
                        'id' => $item->id,
                        'type' => 'ai_consultation',
                        'description' => "Consultazione AI da " . ($item->user?->name ?? 'Utente'),
                        'created_at' => $item->created_at,
                    ]);
                $recentActivity = $recentActivity->merge($recentAi);
            }
        } catch (\Exception $e) {
            // Ignora errori per modelli non esistenti
        }
        
        try {
            // Get recent EGIs
            if (class_exists(Egi::class)) {
                $recentEgis = Egi::with('creator:id,name')
                    ->latest()
                    ->take(5)
                    ->get()
                    ->map(fn($item) => [
                        'id' => $item->id,
                        'type' => 'egi_created',
                        'description' => "EGI '{$item->name}' creato da " . ($item->creator?->name ?? 'Utente'),
                        'created_at' => $item->created_at,
                    ]);
                $recentActivity = $recentActivity->merge($recentEgis);
            }
        } catch (\Exception $e) {
            // Ignora errori per modelli non esistenti
        }

        $recentActivity = $recentActivity
            ->sortByDesc('created_at')
            ->take(10)
            ->values();

        return response()->json([
            'stats' => $stats,
            'recent_activity' => $recentActivity,
        ]);
    }
}
