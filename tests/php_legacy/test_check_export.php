<?php

require 'vendor/autoload.php';

use Illuminate\Foundation\Application;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    echo "🔍 Check Export Status\n";
    echo "=====================\n\n";

    // Token from previous test
    $token = "YSiE2DXY2LuJ42XXxsZci3P5jHcyGA39YOZiRO0zF6wDtfbJ9i5gxfI1x0k8nE0v";

    $user = \App\Models\User::find(4);
    if (!$user) {
        echo "❌ User 4 not found!\n";
        exit(1);
    }

    $logger = app(\Ultra\UltraLogManager\UltraLogManager::class);
    $errorManager = app(\Ultra\ErrorManager\Interfaces\ErrorManagerInterface::class);
    $exportService = new \App\Services\Gdpr\DataExportService($logger, $errorManager);

    $export = $exportService->getExportByToken($token, $user);

    if (!$export) {
        echo "❌ Export not found with token: $token\n";
        exit(1);
    }

    echo "📊 Export Status: {$export->status}\n";
    echo "📈 Progress: {$export->progress}%\n";
    echo "🕒 Created: {$export->created_at}\n";

    if ($export->completed_at) {
        echo "✅ Completed: {$export->completed_at}\n";
    }

    if ($export->file_path) {
        echo "📂 File path: {$export->file_path}\n";
        echo "📏 File size: " . number_format($export->file_size) . " bytes\n";

        // Check if file exists
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($export->file_path)) {
            echo "✅ File exists in storage\n";

            // Check file size on disk
            $actualSize = \Illuminate\Support\Facades\Storage::disk('public')->size($export->file_path);
            echo "📐 Actual file size: " . number_format($actualSize) . " bytes\n";
        } else {
            echo "❌ File does not exist in storage!\n";
        }
    }

    if ($export->error_message) {
        echo "💥 Error: {$export->error_message}\n";
    }

    echo "\n🎯 Status: " . ($export->status === 'completed' ? "✅ Ready for download!" : "⏳ Not ready yet") . "\n";
} catch (Exception $e) {
    echo "💥 Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    exit(1);
}
