<?php

// Test VocabularyService con artisan tinker
// Crea questo file e poi esegui: php artisan tinker < test_vocabulary_direct.php

use App\Services\Coa\VocabularyService;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Services\Gdpr\AuditLogService;

echo "=== TEST VOCABULARY SERVICE DIRETTO ===\n";

// Inizializza le dipendenze
$logger = app(UltraLogManager::class);
$errorManager = app(ErrorManagerInterface::class);
$auditService = app(AuditLogService::class);

$vocabularyService = new VocabularyService($logger, $errorManager, $auditService);

echo "1. Test searchTermsCollection con 'oro' nella categoria 'materials':\n";

try {
    $results = $vocabularyService->searchTermsCollection('oro', 'materials', 'it');
    echo "   Risultati trovati: " . $results->count() . "\n\n";

    foreach ($results as $index => $result) {
        echo "   RESULT #" . ($index + 1) . ":\n";
        echo "     Slug: {$result->slug}\n";
        echo "     Name: {$result->name}\n";
        echo "     Description: " . substr($result->description ?? 'N/A', 0, 100) . "...\n";
        echo "     Category: {$result->category}\n\n";
    }
} catch (Exception $e) {
    echo "   ERRORE: " . $e->getMessage() . "\n";
    echo "   Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "2. Test manuale step-by-step:\n";

// Test manuale passo per passo
use App\Models\VocabularyTerm;

$allTerms = VocabularyTerm::where('category', 'materials')->get();
echo "   Termini materials nel DB: " . $allTerms->count() . "\n";

$matches = 0;
foreach ($allTerms as $term) {
    $translationKey = "coa_vocabulary.{$term->slug}";
    $descriptionKey = "coa_vocabulary.{$term->slug}_description";

    $translatedName = __($translationKey, [], 'it');
    $translatedDescription = __($descriptionKey, [], 'it');

    $foundInName = stripos($translatedName, 'oro') !== false;
    $foundInDescription = stripos($translatedDescription, 'oro') !== false;

    if ($foundInName || $foundInDescription) {
        $matches++;
        echo "   MATCH #{$matches}: {$term->slug}\n";
        echo "     NAME: {$translatedName} " . ($foundInName ? '[ORO]' : '') . "\n";
        if ($foundInDescription) {
            echo "     DESC: " . substr($translatedDescription, 0, 80) . "... [ORO]\n";
        }
        echo "\n";
    }
}

echo "Totale matches manuali: $matches\n";
echo "=== FINE TEST ===\n";

exit;
