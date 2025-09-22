<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\DataExport;

echo "=== Generate Download URL for Testing ===\n\n";

$user = User::find(4);
if (!$user) {
    die("❌ Utente con ID 4 non trovato\n");
}

$export = $user->dataExports()
    ->where('status', 'completed')
    ->where('format', 'csv')
    ->orderBy('created_at', 'desc')
    ->first();

if (!$export) {
    die("❌ Nessun export CSV completato trovato\n");
}

echo "✅ Export trovato:\n";
echo "ID: {$export->id}\n";
echo "Token: {$export->token}\n";
echo "Format: {$export->format}\n";
echo "File: {$export->file_path}\n";
echo "Size: " . number_format($export->file_size) . " bytes\n";
echo "Created: {$export->created_at}\n";
echo "Expires: {$export->expires_at}\n\n";

// Generate URL per il download
$baseUrl = "http://localhost:8004";
$downloadUrl = "{$baseUrl}/gdpr/export-data/download/{$export->token}";

echo "🔗 URL per test download:\n";
echo "{$downloadUrl}\n\n";

echo "📋 Test con curl:\n";
echo "curl -L -o test_download.zip \"{$downloadUrl}\"\n\n";

echo "📋 Test con wget:\n";
echo "wget -O test_download.zip \"{$downloadUrl}\"\n\n";

echo "⚠️  NOTA: Devi essere autenticato come user ID 4 per scaricare\n";
echo "   Puoi testare direttamente nel browser loggandoti prima.\n";
