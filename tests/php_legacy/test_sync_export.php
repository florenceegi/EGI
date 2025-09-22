<?php

require 'vendor/autoload.php';

use Illuminate\Foundation\Application;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    echo "🔍 Test Sync Export System (Development Mode)\n";
    echo "============================================\n\n";

    // Get user ID 4 (our test user)
    $user = \App\Models\User::find(4);
    if (!$user) {
        echo "❌ User 4 not found!\n";
        exit(1);
    }

    echo "👤 User found: {$user->name} ({$user->email})\n";

    // Initialize the export service in SYNC mode
    $logger = app(\Ultra\UltraLogManager\UltraLogManager::class);
    $errorManager = app(\Ultra\ErrorManager\Interfaces\ErrorManagerInterface::class);
    $exportService = new \App\Services\Gdpr\DataExportService($logger, $errorManager);

    // Force sync mode for development
    $exportService->setQueueMode(false);

    // Test categories
    $categories = ['profile', 'account', 'preferences', 'activity'];
    $format = 'csv';

    echo "📦 Categories to export: " . implode(', ', $categories) . "\n";
    echo "📄 Format: $format\n";
    echo "⚙️  Mode: SYNC (development)\n\n";

    // Generate export (should process immediately now)
    echo "🚀 Generating export (sync mode)...\n";
    $startTime = microtime(true);

    $token = $exportService->generateUserDataExport($user, $format, $categories);

    $endTime = microtime(true);
    $executionTime = round(($endTime - $startTime) * 1000, 2);

    if (empty($token)) {
        echo "❌ Export generation failed!\n";
        exit(1);
    }

    echo "✅ Export completed successfully!\n";
    echo "🎫 Token: $token\n";
    echo "⏱️  Execution time: {$executionTime}ms\n\n";

    // Check the export status immediately
    $export = $exportService->getExportByToken($token, $user);
    if ($export) {
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

        if ($export->status === 'completed') {
            echo "\n🎯 Status: ✅ Ready for download immediately!\n";
            echo "🌐 Test download at: http://localhost:8004/gdpr/export/{$token}/download\n";
        } else {
            echo "\n🎯 Status: ❌ Something went wrong\n";
        }
    }

    echo "\n🎉 Sync export test completed!\n";
} catch (Exception $e) {
    echo "💥 Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    exit(1);
}
