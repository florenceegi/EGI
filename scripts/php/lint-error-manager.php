<?php

// lint-error-manager.php

/**
 * Linter & Fixer per il file di configurazione error-manager.php.
 * Analizza le definizioni degli errori, segnala le non conformità e può generare un file corretto.
 * * @author Padmin D. Curtis (AI Partner OS1.5.1-Compliant) for Fabio Cherici
 * @version 1.0.0
 */

// --- CONFIGURAZIONE ---
$configFile = __DIR__ . '/config/error-manager.php'; // Assicurati che il percorso sia corretto
$reportFile = __DIR__ . '/linter_report.log';
$correctedFile = __DIR__ . '/config/error-manager.corrected.php';
// --- FINE CONFIGURAZIONE ---


// Gestione degli argomenti da CLI
$runFix = isset($argv[1]) && $argv[1] === '--fix';

echo "=========================================\n";
echo " UEM Linter & Fixer by Padmin D. Curtis\n";
echo "=========================================\n\n";

if (!file_exists($configFile)) {
    echo "ERRORE: File di configurazione non trovato in: $configFile\n";
    exit(1);
}

echo "Caricamento del file: " . basename($configFile) . "\n";
$config = require $configFile;
$errors = $config['errors'] ?? [];
$correctedConfig = $config; // Partiamo da una copia identica

// --- DEFINIZIONE DELLO SCHEMA ---
$requiredKeys = [
    'type', 'blocking', 'dev_message_key', 'user_message_key',
    'http_status_code', 'devTeam_email_need', 'notify_slack', 'msg_to'
];
$optionalKeys = ['recovery_action'];
$allowedKeys = array_merge($requiredKeys, $optionalKeys);

$validBlockingLevels = array_keys($config['blocking_levels'] ?? []);
$validErrorTypes = array_keys($config['error_types'] ?? []);

$issues = [];
$corrections = 0;

echo "Analisi di " . count($errors) . " codici di errore...\n";

foreach ($errors as $errorCode => &$definition) {
    $errorPrefix = "[$errorCode]";

    // 1. Validazione delle chiavi presenti
    $definitionKeys = array_keys($definition);
    $unknownKeys = array_diff($definitionKeys, $allowedKeys);
    if (!empty($unknownKeys)) {
        $issues[] = "$errorPrefix Chiavi sconosciute rilevate: " . implode(', ', $unknownKeys);
    }

    // 2. Validazione delle chiavi mancanti
    $missingKeys = array_diff($requiredKeys, $definitionKeys);
    if (!empty($missingKeys)) {
        $issues[] = "$errorPrefix Chiavi obbligatorie mancanti: " . implode(', ', $missingKeys);
    }

    // 3. Validazione e correzione del valore di 'blocking'
    if (isset($definition['blocking'])) {
        $originalBlocking = $definition['blocking'];
        $correctedBlocking = strtolower(trim($originalBlocking));

        // Mappatura valori errati comuni -> corretti
        if ($correctedBlocking === 'yes' || $correctedBlocking === 'true' || $correctedBlocking === '1') {
             $definition['blocking'] = 'blocking';
             $issues[] = "$errorPrefix Valore di 'blocking' non standard ('$originalBlocking') corretto in 'blocking'.";
             $corrections++;
        } elseif (!in_array($originalBlocking, $validBlockingLevels)) {
            $issues[] = "$errorPrefix Valore di 'blocking' non valido: '$originalBlocking'. Valori attesi: " . implode(', ', $validBlockingLevels);
        }
    }

    // 4. Validazione dei tipi di dato
    if (isset($definition['http_status_code']) && !is_int($definition['http_status_code'])) {
        $issues[] = "$errorPrefix 'http_status_code' dovrebbe essere un intero.";
    }
     if (isset($definition['devTeam_email_need']) && !is_bool($definition['devTeam_email_need'])) {
        $issues[] = "$errorPrefix 'devTeam_email_need' dovrebbe essere un booleano.";
    }
    if (isset($definition['notify_slack']) && !is_bool($definition['notify_slack'])) {
        $issues[] = "$errorPrefix 'notify_slack' dovrebbe essere un booleano.";
    }
}
unset($definition); // Rompiamo la referenza

$correctedConfig['errors'] = $errors; // Aggiorniamo l'array con le correzioni

echo "Analisi completata.\n\n";

// --- REPORTING ---
if (empty($issues)) {
    echo "✅ Fantastico! Nessuna non conformità trovata.\n";
} else {
    echo "⚠️ Rilevate " . count($issues) . " non conformità.\n";
    file_put_contents($reportFile, "Report di analisi del " . date('Y-m-d H:i:s') . "\n");
    file_put_contents($reportFile, "==============================================\n\n", FILE_APPEND);
    file_put_contents($reportFile, implode("\n", $issues), FILE_APPEND);
    echo "I dettagli sono stati salvati nel file: $reportFile\n";
}

// --- CORREZIONE ---
if ($runFix) {
    if ($corrections > 0) {
        echo "\nApplicando $corrections correzioni e generando il nuovo file...\n";

        // Esporta l'array in formato PHP leggibile
        $exportedConfig = var_export($correctedConfig, true);

        // Aggiungi il return statement di PHP
        $fileContent = "<?php\n\ndeclare(strict_types=1);\n\nreturn " . $exportedConfig . ";\n";

        file_put_contents($correctedFile, $fileContent);

        echo "✅ File corretto generato con successo in: $correctedFile\n";
        echo "Per favore, confronta il file originale con quello corretto prima di sostituirlo.\n";
    } else {
        echo "\nNessuna correzione automatica da applicare. Il file è già conforme per i fix automatici.\n";
    }
} else {
    echo "\nEsegui lo script con l'argomento '--fix' per generare un file corretto.\n";
}

echo "\nFinito.\n";

exit(0);
