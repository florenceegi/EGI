<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LiveLogController extends Controller
{
    public function show(Request $request)
    {
        if (!Auth::check()) abort(403);

        $logFile = storage_path('logs/laravel.log');
        
        if (!File::exists($logFile)) {
            return response('Log file not found', 404)->header('Content-Type', 'text/plain');
        }

        // Get requested number of lines (default 100)
        $lines = (int) $request->get('lines', 100);
        $lines = min($lines, 1000); // Cap at 1000
        
        // Read last N lines
        $fileLines = file($logFile);
        $relevantLines = array_slice($fileLines, -$lines);
        
        // Filter for MINT logs if requested
        if ($request->get('filter') === 'mint') {
            $relevantLines = array_filter($relevantLines, function($line) {
                return stripos($line, 'MINT') !== false || stripos($line, '===') !== false;
            });
        }
        
        $output = implode("", $relevantLines);
        
        // Auto-refresh header
        $html = "<!DOCTYPE html><html><head><meta charset='utf-8'><meta http-equiv='refresh' content='3'><title>Live Logs</title><style>body{background:#1a1a1a;color:#0f0;font-family:monospace;padding:20px;font-size:12px;}pre{white-space:pre-wrap;word-wrap:break-word;}</style></head><body><h2>🔴 LIVE LOGS (auto-refresh 3s)</h2><a href='?lines=50&filter=mint' style='color:#0ff'>MINT Only (50)</a> | <a href='?lines=200' style='color:#0ff'>All (200)</a> | <a href='?lines=500' style='color:#0ff'>All (500)</a><hr><pre>" . htmlspecialchars($output) . "</pre></body></html>";
        
        return response($html)->header('Content-Type', 'text/html');
    }
}
