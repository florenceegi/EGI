<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Test route for HEIC/HEIF file upload debugging
 * ONLY FOR DEVELOPMENT - REMOVE IN PRODUCTION
 */
Route::post('/test/heic-upload', function (Request $request) {
    if (!app()->environment(['local', 'development'])) {
        abort(404);
    }

    Log::info('[HEIC Test] Starting test upload', [
        'files' => $request->allFiles(),
        'has_file' => $request->hasFile('file'),
        'user_agent' => $request->userAgent()
    ]);

    if (!$request->hasFile('file')) {
        return response()->json([
            'error' => 'No file uploaded',
            'debug' => [
                'all_files' => $request->allFiles(),
                'all_input' => $request->all()
            ]
        ], 400);
    }

    $file = $request->file('file');

    $debug = [
        'original_name' => $file->getClientOriginalName(),
        'extension' => $file->getClientOriginalExtension(),
        'guessed_extension' => $file->guessExtension(),
        'mime_type' => $file->getMimeType(),
        'size' => $file->getSize(),
        'is_valid' => $file->isValid(),
        'error' => $file->getError(),
        'real_path' => $file->getRealPath(),
        'path' => $file->path(),
    ];

    // Check file content start (for debugging fake files)
    if ($file->isValid() && $file->getRealPath()) {
        $content = file_get_contents($file->getRealPath(), false, null, 0, 20);
        $debug['content_hex'] = bin2hex($content);
        $debug['content_preview'] = substr($content, 0, 10);
    }

    // Check config
    $config = [
        'allowed_extensions' => config('AllowedFileType.collection.allowed_extensions'),
        'allowed_mime_types' => config('AllowedFileType.collection.allowed_mime_types'),
        'max_size' => config('AllowedFileType.collection.max_size')
    ];

    Log::info('[HEIC Test] File analysis complete', [
        'debug' => $debug,
        'config' => $config
    ]);

    return response()->json([
        'success' => true,
        'message' => 'File analyzed successfully',
        'debug' => $debug,
        'config' => $config,
        'validation' => [
            'extension_allowed' => in_array(strtolower($file->getClientOriginalExtension()), $config['allowed_extensions']),
            'mime_allowed' => in_array($file->getMimeType(), $config['allowed_mime_types']),
            'size_ok' => $file->getSize() <= $config['max_size']
        ]
    ]);
});

/**
 * Debug route for FegiAuth troubleshooting
 * ONLY FOR DEVELOPMENT - REMOVE IN PRODUCTION
 */
Route::get('/test/fegiauth-debug', function () {
    if (!app()->environment(['local', 'development'])) {
        abort(404);
    }

    // Get complete debug information
    $debugInfo = \App\Helpers\FegiAuth::debugUserResolution();

    // Add additional context
    $debugInfo['additional_checks'] = [
        'session_all' => session()->all(),
        'auth_guards' => config('auth.guards'),
        'current_guard' => config('auth.defaults.guard'),
        'request_user_agent' => request()->userAgent(),
        'request_ip' => request()->ip()
    ];

    return response()->json($debugInfo, 200, [], JSON_PRETTY_PRINT);
});
