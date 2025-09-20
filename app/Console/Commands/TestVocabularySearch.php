<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Coa\VocabularyService;
use App\Models\VocabularyTerm;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Services\Gdpr\AuditLogService;

class TestVocabularySearch extends Command {
    protected $signature = 'test:vocabulary-search {query=oro} {category=materials} {locale=it}';
    protected $description = 'Test vocabulary search functionality';

    public function handle() {
        $query = $this->argument('query');
        $category = $this->argument('category');
        $locale = $this->argument('locale');

        $this->info("=== TEST VOCABULARY SERVICE ===");
        $this->info("Query: $query");
        $this->info("Category: $category");
        $this->info("Locale: $locale");
        $this->newLine();

        // Inizializza le dipendenze
        $logger = app(UltraLogManager::class);
        $errorManager = app(ErrorManagerInterface::class);
        $auditService = app(AuditLogService::class);

        $vocabularyService = new VocabularyService($logger, $errorManager, $auditService);

        $this->info("1. Test searchTermsCollection:");

        try {
            $results = $vocabularyService->searchTermsCollection($query, $category, $locale);
            $this->info("   Risultati trovati: " . $results->count());
            $this->newLine();

            foreach ($results as $index => $result) {
                $this->info("   RESULT #" . ($index + 1) . ":");
                $this->info("     Slug: {$result->slug}");
                $this->info("     Name: {$result->name}");
                $this->info("     Description: " . substr($result->description ?? 'N/A', 0, 100) . "...");
                $this->info("     Category: {$result->category}");
                $this->newLine();
            }
        } catch (\Exception $e) {
            $this->error("   ERRORE: " . $e->getMessage());
            $this->error("   File: " . $e->getFile() . ":" . $e->getLine());
        }

        $this->info("2. Test manuale step-by-step:");

        $allTerms = VocabularyTerm::query();
        if ($category && trim($category) !== '') {
            $allTerms->where('category', $category);
        }
        $allTerms = $allTerms->get();
        $this->info("   Termini $category nel DB: " . $allTerms->count());

        $matches = 0;
        foreach ($allTerms as $term) {
            $translationKey = "coa_vocabulary.{$term->slug}";
            $descriptionKey = "coa_vocabulary.{$term->slug}_description";

            $translatedName = __($translationKey, [], $locale);
            $translatedDescription = __($descriptionKey, [], $locale);

            $foundInName = stripos($translatedName, $query) !== false;
            $foundInDescription = stripos($translatedDescription, $query) !== false;

            if ($foundInName || $foundInDescription) {
                $matches++;
                $this->info("   MATCH #{$matches}: {$term->slug}");
                $this->info("     NAME: {$translatedName} " . ($foundInName ? '[MATCH]' : ''));
                if ($foundInDescription) {
                    $this->info("     DESC: " . substr($translatedDescription, 0, 80) . "... [MATCH]");
                }
                $this->newLine();
            }
        }

        $this->info("Totale matches manuali: $matches");

        if ($results->count() !== $matches) {
            $this->error("DISCREPANZA: Il servizio trova {$results->count()} risultati, il test manuale ne trova $matches");
        } else {
            $this->info("✅ Il servizio e il test manuale trovano lo stesso numero di risultati");
        }

        return Command::SUCCESS;
    }
}
