<?php

/**
 * Test script per verificare il download degli export
 */

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Storage;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Test Export Download ===\n";

// Test file esistente
$testFile = 'export_7aF4gC51PaN79rwkQPyIflIP7tBRwEbmSkgwKeBk6PZaPvnnG2OC2c2gNu2UPd3V_csv.zip';

echo "Testing file: {$testFile}\n";

// Test 1: File exists check
if (Storage::disk('public')->exists($testFile)) {
    echo "✅ File exists in storage\n";
} else {
    echo "❌ File does not exist in storage\n";
    exit(1);
}

// Test 2: Get file size
$size = Storage::disk('public')->size($testFile);
echo "📁 File size via Storage: {$size} bytes\n";

// Test 3: Get file content
$content = Storage::disk('public')->get($testFile);
echo "📄 Content length: " . strlen($content) . " bytes\n";

// Test 4: Get file path
$fullPath = Storage::disk('public')->path($testFile);
echo "📍 Full path: {$fullPath}\n";

// Test 5: Direct file system check
if (file_exists($fullPath)) {
    echo "✅ File exists on filesystem\n";
    echo "📁 File size via filesystem: " . filesize($fullPath) . " bytes\n";
} else {
    echo "❌ File does not exist on filesystem\n";
}

// Test 6: ZIP validation
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $fullPath);
finfo_close($finfo);
echo "🗂️ MIME type: {$mimeType}\n";

// Test 7: ZIP content check using shell
echo "🗜️ ZIP content:\n";
$output = shell_exec("unzip -l '{$fullPath}' 2>/dev/null");
if ($output) {
    echo $output;
} else {
    echo "❌ Could not read ZIP content\n";
}

echo "\n=== Test Complete ===\n";
