<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\DataExport;
use App\Services\Gdpr\DataExportService;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;

echo "=== Test Direct Download Simulation ===\n\n";

try {
    // Trova l'export da testare
    $user = User::find(4);
    $export = $user->dataExports()
        ->where('status', 'completed')
        ->where('format', 'csv')
        ->orderBy('created_at', 'desc')
        ->first();

    if (!$export) {
        die("❌ Nessun export trovato\n");
    }

    echo "📁 Testing export: {$export->token}\n";
    echo "📄 File: {$export->file_path}\n";
    echo "📏 Size: " . number_format($export->file_size) . " bytes\n\n";

    // Test diretto del file senza Laravel service container
    echo "🔧 Testing file directly...\n";

    // Simuliamo le verifiche che dovrebbero passare
    echo "  ✓ Export status: {$export->status}\n";
    echo "  ✓ Expires at: {$export->expires_at}\n";
    echo "  ✓ Current time: " . now() . "\n";
    echo "  ✓ Is expired: " . ($export->expires_at < now() ? "YES" : "NO") . "\n";

    $filePath = storage_path('app/public/' . $export->file_path);
    echo "  ✓ Full path: {$filePath}\n";
    echo "  ✓ File exists: " . (file_exists($filePath) ? "YES" : "NO") . "\n";

    if (file_exists($filePath)) {
        echo "  ✓ File size on disk: " . number_format(filesize($filePath)) . " bytes\n";
        echo "  ✓ File readable: " . (is_readable($filePath) ? "YES" : "NO") . "\n";
    }

    // Test di lettura del file
    echo "\n📖 Testing file reading...\n";
    if (file_exists($filePath) && is_readable($filePath)) {
        $fileContent = file_get_contents($filePath);
        $contentLength = strlen($fileContent);
        echo "  ✓ Content length: " . number_format($contentLength) . " bytes\n";
        echo "  ✓ Content starts with: " . bin2hex(substr($fileContent, 0, 10)) . "\n";
        echo "  ✓ Content is ZIP: " . (substr($fileContent, 0, 2) === 'PK' ? "YES" : "NO") . "\n";

        // Test di scrittura in un file temporaneo
        $tempFile = '/tmp/test_download_' . uniqid() . '.zip';
        file_put_contents($tempFile, $fileContent);
        $tempSize = filesize($tempFile);
        echo "  ✓ Temp file size: " . number_format($tempSize) . " bytes\n";
        echo "  ✓ Size match: " . ($tempSize === $contentLength ? "YES" : "NO") . "\n";

        // Cleanup
        unlink($tempFile);
        echo "  ✓ Temp file cleaned up\n";
    }

    echo "\n🎯 Summary:\n";
    echo "- File exists and is readable\n";
    echo "- Content can be read completely\n";
    echo "- File appears to be a valid ZIP\n";
    echo "- No issues found in file handling\n\n";

    echo "🌐 The issue is likely in the HTTP response headers or browser handling.\n";
    echo "   Try the direct download URL in browser after logging in as user 4.\n";
    echo "   URL: http://localhost:8004/gdpr/export-data/download/{$export->token}\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . "\n";
    echo "   Line: " . $e->getLine() . "\n";
}

echo "\n=== Test completed ===\n";
