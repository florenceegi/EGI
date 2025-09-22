<?php

require 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    echo "🔍 Simple Test\n";
    echo "==============\n\n";

    $user = \App\Models\User::find(4);
    echo "User found: " . $user->name . "\n";

    $logger = app(\Ultra\UltraLogManager\UltraLogManager::class);
    $errorManager = app(\Ultra\ErrorManager\Interfaces\ErrorManagerInterface::class);

    echo "Dependencies loaded\n";

    $exportService = new \App\Services\Gdpr\DataExportService($logger, $errorManager);
    echo "Service created\n";

    // Try to create an export with minimal categories
    $token = $exportService->generateUserDataExport($user, 'csv', ['profile']);
    echo "Export token: " . ($token ?: 'FAILED') . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
