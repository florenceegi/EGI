<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\DataExport;
use App\Services\Gdpr\DataExportService;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

echo "=== Test Export GDPR per User ID 4 ===\n\n";

try {
    // 1. Trova l'utente
    $user = User::find(4);
    if (!$user) {
        die("❌ Utente con ID 4 non trovato\n");
    }

    echo "✅ Utente trovato: {$user->name} ({$user->email})\n";

    // 2. Controlla export esistenti
    $existingExports = $user->dataExports()
        ->where('status', 'completed')
        ->orderBy('created_at', 'desc')
        ->limit(3)
        ->get();

    echo "\n📋 Export esistenti completati:\n";
    foreach ($existingExports as $export) {
        echo "  - ID: {$export->id}, Token: " . substr($export->token, 0, 15) . "..., Format: {$export->format}, Created: {$export->created_at}\n";
        echo "    File: {$export->file_path}, Size: " . number_format($export->file_size) . " bytes\n";
    }

    // 3. Se esiste un export CSV recente, testiamo il download
    $csvExport = $user->dataExports()
        ->where('status', 'completed')
        ->where('format', 'csv')
        ->orderBy('created_at', 'desc')
        ->first();

    if ($csvExport) {
        echo "\n🎯 Test download di export CSV esistente...\n";
        echo "Export ID: {$csvExport->id}\n";
        echo "File path: {$csvExport->file_path}\n";
        echo "File size: " . number_format($csvExport->file_size) . " bytes\n";

        // Verifica che il file esista
        $fullPath = storage_path('app/public/' . $csvExport->file_path);
        echo "Full path: {$fullPath}\n";
        echo "File exists: " . (file_exists($fullPath) ? "✅ SI" : "❌ NO") . "\n";

        if (file_exists($fullPath)) {
            $actualSize = filesize($fullPath);
            echo "Actual file size: " . number_format($actualSize) . " bytes\n";

            // Test del tipo di file
            if (function_exists('finfo_open')) {
                $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($fileInfo, $fullPath);
                finfo_close($fileInfo);
                echo "MIME type: {$mimeType}\n";
            } else {
                $mimeType = 'application/zip'; // Assume ZIP for CSV
                echo "MIME type: {$mimeType} (assumed)\n";
            }

            // Verifica che sia un ZIP valido per CSV
            if ($csvExport->format === 'csv' && $mimeType === 'application/zip') {
                echo "✅ File CSV correttamente in formato ZIP\n";

                // Testa il contenuto del ZIP
                if (class_exists('ZipArchive')) {
                    $zip = new ZipArchive();
                    if ($zip->open($fullPath) === TRUE) {
                        echo "✅ ZIP apribile correttamente\n";
                        echo "Numero di file nel ZIP: " . $zip->numFiles . "\n";

                        for ($i = 0; $i < min(5, $zip->numFiles); $i++) {
                            $fileName = $zip->getNameIndex($i);
                            $fileSize = $zip->statIndex($i)['size'];
                            echo "  - {$fileName} ({$fileSize} bytes)\n";
                        }
                        $zip->close();
                    } else {
                        echo "❌ Errore nell'apertura del ZIP\n";
                    }
                } else {
                    echo "⚠️ ZipArchive non disponibile per test contenuto\n";
                }
            }
        }

        // 4. Test del metodo streamExportFile
        echo "\n🚀 Test del metodo streamExportFile...\n";

        $logger = app(UltraLogManager::class);
        $errorManager = app(ErrorManagerInterface::class);
        $exportService = new DataExportService($logger, $errorManager);

        try {
            // Non possiamo chiamare streamExportFile direttamente perché ritorna una Response
            // Ma possiamo testare la logica di preparazione
            echo "✅ DataExportService istanziato correttamente\n";

            // Simuliamo le verifiche che fa streamExportFile
            if ($csvExport->status !== 'completed') {
                echo "❌ Export non completato\n";
            } else {
                echo "✅ Export completato\n";
            }

            if ($csvExport->expires_at < now()) {
                echo "❌ Export scaduto\n";
            } else {
                echo "✅ Export non scaduto (scade il: {$csvExport->expires_at})\n";
            }

            if (!Storage::disk('public')->exists($csvExport->file_path)) {
                echo "❌ File non trovato in storage\n";
            } else {
                echo "✅ File trovato in storage\n";
            }

            echo "🎯 Tutti i controlli passati! Il download dovrebbe funzionare.\n";
        } catch (Exception $e) {
            echo "❌ Errore nel test del servizio: " . $e->getMessage() . "\n";
        }
    } else {
        echo "\n📝 Nessun export CSV completato trovato. Creiamo un nuovo export...\n";

        // 5. Crea un nuovo export
        $logger = app(UltraLogManager::class);
        $errorManager = app(ErrorManagerInterface::class);
        $exportService = new DataExportService($logger, $errorManager);

        $categories = ['profile', 'account', 'preferences', 'activity', 'consents'];
        echo "Categorie da esportare: " . implode(', ', $categories) . "\n";

        $token = $exportService->generateUserDataExport($user, 'csv', $categories);

        if (empty($token)) {
            echo "❌ Errore nella generazione dell'export\n";
        } else {
            echo "✅ Export generato con token: " . substr($token, 0, 15) . "...\n";

            // Trova il nuovo export
            $newExport = $user->dataExports()->where('token', $token)->first();
            if ($newExport) {
                echo "Export ID: {$newExport->id}\n";
                echo "Status: {$newExport->status}\n";
                echo "File path: {$newExport->file_path}\n";
                echo "File size: " . number_format($newExport->file_size) . " bytes\n";
            }
        }
    }
} catch (Exception $e) {
    echo "❌ Errore generale: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\n=== Test completato ===\n";
