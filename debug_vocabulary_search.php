<?php

// Script di debug per testare la ricerca vocabolario
// Senza dipendenze Laravel complesse

echo "=== DEBUG RICERCA VOCABOLARIO ===\n\n";

// Test 1: Controllo del file di traduzione
echo "1. Controllo file di traduzione italiano:\n";
$translationFile = '/home/fabio/EGI/resources/lang/it/coa_vocabulary.php';

if (file_exists($translationFile)) {
    $translations = include $translationFile;
    echo "   File caricato con " . count($translations) . " traduzioni\n";

    // Conta quante chiavi contengono "oro"
    $oroCount = 0;
    $oroMatches = [];

    foreach ($translations as $key => $value) {
        if (stripos($value, 'oro') !== false) {
            $oroCount++;
            $oroMatches[] = [
                'key' => $key,
                'value' => substr($value, 0, 100) . (strlen($value) > 100 ? '...' : '')
            ];
        }
    }

    echo "   Traduzioni che contengono 'oro': $oroCount\n\n";

    // Mostra i primi 10 match
    echo "   Prime 10 occorrenze di 'oro':\n";
    foreach (array_slice($oroMatches, 0, 10) as $i => $match) {
        echo "   " . ($i + 1) . ". {$match['key']}: {$match['value']}\n";
    }
    echo "\n";
} else {
    echo "   ERRORE: File di traduzione non trovato!\n\n";
}

// Test 2: Simula la logica della funzione di ricerca
echo "2. Simulazione logica di ricerca:\n";

if (isset($translations)) {
    // Simula la ricerca di termini con slug che iniziano con "material-"
    echo "   Cerco traduzioni per slug che iniziano con 'material-':\n";

    $materialSlugs = [];
    foreach ($translations as $key => $value) {
        if (strpos($key, 'material-') === 0 && !strpos($key, '_description')) {
            $materialSlugs[] = $key;
        }
    }

    echo "   Trovati " . count($materialSlugs) . " slug di materiali\n";

    // Per ogni slug, controlla se nome o descrizione contengono "oro"
    $matchingSlugTerms = [];
    foreach ($materialSlugs as $slug) {
        $name = $translations[$slug] ?? '';
        $description = $translations[$slug . '_description'] ?? '';

        $foundInName = stripos($name, 'oro') !== false;
        $foundInDesc = stripos($description, 'oro') !== false;

        if ($foundInName || $foundInDesc) {
            $matchingSlugTerms[] = [
                'slug' => $slug,
                'name' => $name,
                'description' => substr($description, 0, 100) . '...',
                'found_in_name' => $foundInName,
                'found_in_desc' => $foundInDesc
            ];
        }
    }

    echo "   Termini di materiale che contengono 'oro': " . count($matchingSlugTerms) . "\n\n";

    foreach ($matchingSlugTerms as $i => $term) {
        echo "   MATCH " . ($i + 1) . ": {$term['slug']}\n";
        echo "     NAME: {$term['name']} " . ($term['found_in_name'] ? '[ORO QUI]' : '') . "\n";
        echo "     DESC: {$term['description']} " . ($term['found_in_desc'] ? '[ORO QUI]' : '') . "\n\n";
    }
}

echo "=== FINE DEBUG ===\n";
