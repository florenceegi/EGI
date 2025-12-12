<?php

namespace App\Http\Controllers\Api\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * @Oracode API Controller: SuperAdmin Padmin OS3 Analyzer
 * 🎯 Purpose: API per dashboard, violations, symbols, search, statistics del Padmin OS3 Guardian
 */
class PadminApiController extends Controller
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
     * Get Padmin dashboard data
     */
    public function dashboard(Request $request): JsonResponse
    {
        // Statistiche OS3 compliance
        $totalScans = DB::table('padmin_scans')->count();
        $violationsFound = DB::table('padmin_violations')->count();
        $autoFixed = DB::table('padmin_violations')->where('auto_fixed', true)->count();
        
        // Compliance rate
        $totalFiles = DB::table('padmin_scans')->distinct('file_path')->count('file_path');
        $cleanFiles = DB::table('padmin_scans')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('padmin_violations')
                    ->whereColumn('padmin_violations.scan_id', 'padmin_scans.id');
            })
            ->count();
        
        $complianceRate = $totalFiles > 0 ? ($cleanFiles / $totalFiles) * 100 : 100;
        
        $lastScan = DB::table('padmin_scans')->latest('created_at')->value('created_at');

        // Recent scans
        $recentScans = DB::table('padmin_scans')
            ->leftJoin('padmin_violations', 'padmin_scans.id', '=', 'padmin_violations.scan_id')
            ->select(
                'padmin_scans.id',
                'padmin_scans.file_path',
                'padmin_scans.created_at as scanned_at',
                DB::raw('COUNT(padmin_violations.id) as violations_count')
            )
            ->groupBy('padmin_scans.id', 'padmin_scans.file_path', 'padmin_scans.created_at')
            ->latest('padmin_scans.created_at')
            ->take(10)
            ->get()
            ->map(fn($s) => [
                'id' => $s->id,
                'file_path' => $s->file_path,
                'violations_count' => $s->violations_count,
                'status' => $s->violations_count > 0 ? 'violations' : 'clean',
                'scanned_at' => $s->scanned_at,
            ]);

        return response()->json([
            'stats' => [
                'total_scans' => $totalScans,
                'violations_found' => $violationsFound,
                'auto_fixed' => $autoFixed,
                'compliance_rate' => round($complianceRate, 1),
                'last_scan' => $lastScan,
            ],
            'recent_scans' => $recentScans,
        ]);
    }

    /**
     * Get violations list
     */
    public function violations(Request $request): JsonResponse
    {
        $violations = DB::table('padmin_violations')
            ->latest('created_at')
            ->take(50)
            ->get()
            ->map(fn($v) => [
                'id' => $v->id,
                'file_path' => $v->file_path,
                'line_number' => $v->line_number ?? 0,
                'rule' => $v->rule ?? 'unknown',
                'severity' => $v->severity ?? 'warning',
                'message' => $v->message ?? '',
                'suggestion' => $v->suggestion ?? '',
                'can_auto_fix' => (bool) ($v->can_auto_fix ?? false),
                'created_at' => $v->created_at,
            ]);

        $bySeverity = [
            'critical' => DB::table('padmin_violations')->where('severity', 'critical')->count(),
            'warning' => DB::table('padmin_violations')->where('severity', 'warning')->count(),
            'info' => DB::table('padmin_violations')->where('severity', 'info')->count(),
        ];

        return response()->json([
            'violations' => $violations,
            'total' => $violations->count(),
            'by_severity' => $bySeverity,
        ]);
    }

    /**
     * Get symbols catalog
     */
    public function symbols(Request $request): JsonResponse
    {
        $search = $request->get('search', '');

        $query = DB::table('padmin_symbols');
        
        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $symbols = $query->take(100)->get()->map(fn($s) => [
            'id' => $s->id,
            'name' => $s->name,
            'type' => $s->type ?? 'function',
            'file_path' => $s->file_path,
            'line_number' => $s->line_number ?? 0,
            'description' => $s->description ?? '',
            'tags' => json_decode($s->tags ?? '[]', true),
            'compliance_status' => $s->compliance_status ?? 'needs_review',
        ]);

        $byType = DB::table('padmin_symbols')
            ->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type');

        return response()->json([
            'symbols' => $symbols,
            'total' => $symbols->count(),
            'by_type' => $byType,
        ]);
    }

    /**
     * Search codebase
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([
                'results' => [],
                'total' => 0,
                'query' => $query,
            ]);
        }

        // Cerca nei simboli
        $symbolResults = DB::table('padmin_symbols')
            ->where('name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->take(20)
            ->get()
            ->map(fn($s) => [
                'id' => $s->id,
                'file_path' => $s->file_path,
                'line_number' => $s->line_number ?? 0,
                'content' => $s->name . ': ' . ($s->description ?? ''),
                'match_type' => 'symbol',
                'relevance_score' => 0.9,
            ]);

        return response()->json([
            'results' => $symbolResults,
            'total' => $symbolResults->count(),
            'query' => $query,
        ]);
    }

    /**
     * Get Padmin statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        $totalFilesScanned = DB::table('padmin_scans')->distinct('file_path')->count('file_path');
        $totalViolations = DB::table('padmin_violations')->count();
        
        $cleanFiles = DB::table('padmin_scans')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('padmin_violations')
                    ->whereColumn('padmin_violations.scan_id', 'padmin_scans.id');
            })
            ->distinct('file_path')
            ->count('file_path');
        
        $complianceRate = $totalFilesScanned > 0 ? ($cleanFiles / $totalFilesScanned) * 100 : 100;
        $avgFixTime = 2.5; // Placeholder hours

        // By rule
        $byRule = DB::table('padmin_violations')
            ->selectRaw('rule, COUNT(*) as count')
            ->groupBy('rule')
            ->get()
            ->map(fn($r) => [
                'rule' => $r->rule ?? 'unknown',
                'count' => $r->count,
                'percentage' => $totalViolations > 0 ? ($r->count / $totalViolations) * 100 : 0,
            ]);

        // By severity
        $bySeverity = DB::table('padmin_violations')
            ->selectRaw('severity, COUNT(*) as count')
            ->groupBy('severity')
            ->get()
            ->map(fn($s) => [
                'severity' => $s->severity ?? 'warning',
                'count' => $s->count,
                'percentage' => $totalViolations > 0 ? ($s->count / $totalViolations) * 100 : 0,
            ]);

        // Trends (placeholder)
        $trends = collect();

        return response()->json([
            'overview' => [
                'total_files_scanned' => $totalFilesScanned,
                'total_violations' => $totalViolations,
                'compliance_rate' => round($complianceRate, 1),
                'avg_fix_time' => $avgFixTime,
            ],
            'by_rule' => $byRule,
            'by_severity' => $bySeverity,
            'trends' => $trends,
        ]);
    }
}
