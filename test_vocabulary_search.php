<?php

/**
 * Test script per verificare la funzionalità di ricerca vocabulary
 * Simula le chiamate AJAX che fa il frontend
 */

require_once __DIR__ . '/bootstrap/app.php';

use App\Services\Coa\VocabularyService;
use Illuminate\Support\Facades\App;

// Configura l'ambiente
App::setLocale('it');

echo "🔍 Test Vocabulary Search Functionality\n";
echo "======================================\n\n";

try {
    // Crea un'istanza del servizio
    $vocabularyService = new VocabularyService(
        app('logger'),
        app(\Ultra\ErrorManager\Interfaces\ErrorManagerInterface::class),
        app(\App\Services\Gdpr\AuditLogService::class)
    );

    // Test 1: Ricerca con termine italiano "tela"
    echo "📍 Test 1: Ricerca termine italiano 'tela' nella categoria 'materials'\n";
    echo "-" . str_repeat("-", 60) . "\n";

    $result1 = $vocabularyService->searchTerms('tela', 20, 'materials', 'it');

    echo "Risultati trovati: " . $result1->total() . "\n";
    foreach ($result1->items() as $term) {
        echo "  - {$term->slug} => {$term->name} (categoria: {$term->category})\n";
        if (isset($term->translated_name)) {
            echo "    Traduzione: {$term->translated_name}\n";
        }
        if (isset($term->translated_description)) {
            echo "    Descrizione: " . substr($term->translated_description, 0, 80) . "...\n";
        }
    }
    echo "\n";

    // Test 2: Ricerca con termine inglese "canvas"
    echo "📍 Test 2: Ricerca termine inglese 'canvas' nella categoria 'materials'\n";
    echo "-" . str_repeat("-", 60) . "\n";

    $result2 = $vocabularyService->searchTerms('canvas', 20, 'materials', 'it');

    echo "Risultati trovati: " . $result2->total() . "\n";
    foreach ($result2->items() as $term) {
        echo "  - {$term->slug} => {$term->name} (categoria: {$term->category})\n";
        if (isset($term->translated_name)) {
            echo "    Traduzione: {$term->translated_name}\n";
        }
    }
    echo "\n";

    // Test 3: Ricerca generica senza categoria
    echo "📍 Test 3: Ricerca generale 'olio' (senza categoria)\n";
    echo "-" . str_repeat("-", 40) . "\n";

    $result3 = $vocabularyService->searchTerms('olio', 10, null, 'it');

    echo "Risultati trovati: " . $result3->total() . "\n";
    foreach ($result3->items() as $term) {
        echo "  - {$term->slug} => {$term->name} (categoria: {$term->category})\n";
        if (isset($term->translated_name)) {
            echo "    Traduzione: {$term->translated_name}\n";
        }
    }
    echo "\n";

    echo "✅ Tutti i test completati con successo!\n";
} catch (Exception $e) {
    echo "❌ Errore durante i test: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
