<?php

require 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    // Test per verificare che il sistema di queue per l'export funzioni
    echo "🔍 Test Queue Export System\n";
    echo "========================\n\n";

    // Get user ID 4 (our test user)
    $user = \App\Models\User::find(4);
    if (!$user) {
        echo "❌ User 4 not found!\n";
        exit(1);
    }

    echo "👤 User found: {$user->name} ({$user->email})\n";

    // Initialize the export service
    $logger = app(\Ultra\UltraLogManager\UltraLogManager::class);
    $errorManager = app(\Ultra\ErrorManager\Interfaces\ErrorManagerInterface::class);
    $exportService = new \App\Services\Gdpr\DataExportService($logger, $errorManager);

    // Test categories
    $categories = ['profile', 'account', 'preferences', 'activity'];
    $format = 'csv';

    echo "📦 Categories to export: " . implode(', ', $categories) . "\n";
    echo "📄 Format: $format\n\n";

    // Generate export (should now use queue)
    echo "🚀 Generating export (should use queue now)...\n";
    $token = $exportService->generateUserDataExport($user, $format, $categories);

    if (empty($token)) {
        echo "❌ Export generation failed!\n";
        exit(1);
    }

    echo "✅ Export queued successfully!\n";
    echo "🎫 Token: $token\n\n";

    // Check the export status
    $export = $exportService->getExportByToken($token, $user);
    if ($export) {
        echo "📊 Export Status: {$export->status}\n";
        echo "📈 Progress: {$export->progress}%\n";
        echo "🕒 Created: {$export->created_at}\n";

        if ($export->status === 'pending') {
            echo "\n🎯 Export is pending - job should be in queue.\n";
            echo "📋 To process the queue, run: php artisan queue:work --queue=exports\n";
        } elseif ($export->status === 'processing') {
            echo "\n⚡ Export is currently being processed...\n";
        } elseif ($export->status === 'completed') {
            echo "\n✅ Export completed!\n";
            echo "📂 File path: {$export->file_path}\n";
            echo "📏 File size: " . number_format($export->file_size) . " bytes\n";
        } elseif ($export->status === 'failed') {
            echo "\n❌ Export failed!\n";
            echo "💥 Error: {$export->error_message}\n";
        }
    }

    echo "\n🎉 Queue test completed!\n";
} catch (Exception $e) {
    echo "💥 Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    exit(1);
}
