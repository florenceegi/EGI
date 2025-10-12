<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Egi;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * RAG (Retrieval Augmented Generation) Service
 * 
 * Sistema di recupero intelligente dei dati per alimentare l'AI:
 * 1. Recupera atti PA rilevanti dal database
 * 2. Sanitizza i dati (solo metadati pubblici)
 * 3. Crea contesto strutturato per l'AI
 * 
 * GDPR COMPLIANCE:
 * Tutti i dati passano attraverso DataSanitizerService prima
 * di essere inviati all'AI.
 */
class RagService
{
    private UltraLogManager $logger;
    private DataSanitizerService $sanitizer;

    public function __construct(
        UltraLogManager $logger,
        DataSanitizerService $sanitizer
    ) {
        $this->logger = $logger;
        $this->sanitizer = $sanitizer;
    }

    /**
     * Recupera il contesto per una query utente
     * 
     * @param string $query Query dell'utente
     * @param User $user Utente corrente (per scope PA)
     * @param int $limit Numero massimo di atti da recuperare
     * @return array Contesto per l'AI
     */
    public function getContextForQuery(string $query, User $user, int $limit = 10): array
    {
        $this->logger->info('[RAG] Getting context for query', [
            'query_length' => strlen($query),
            'user_id' => $user->id,
            'limit' => $limit,
        ]);

        // Recupera atti rilevanti
        $relevantActs = $this->findRelevantActs($query, $user, $limit);

        // Sanitizza i dati
        $sanitizedActs = $this->sanitizer->sanitizeActsCollection($relevantActs);

        // Crea riassunto testuale
        $actsSummary = $this->sanitizer->createActsSummary($relevantActs);

        // Crea statistiche
        $stats = $this->sanitizer->createStatsContext($relevantActs);

        $context = [
            'acts' => $sanitizedActs,
            'acts_summary' => $actsSummary,
            'stats' => $stats,
            'retrieved_at' => now()->toIso8601String(),
        ];

        // Valida che non ci siano dati privati
        foreach ($sanitizedActs as $act) {
            $this->sanitizer->validateSafeData($act);
        }

        $this->logger->info('[RAG] Context created successfully', [
            'acts_count' => count($sanitizedActs),
            'summary_length' => strlen($actsSummary),
        ]);

        return $context;
    }

    /**
     * Trova atti rilevanti per una query
     * 
     * Usa ricerca semantica basata su:
     * - Keyword matching (protocollo, tipo, titolo)
     * - Date range (se menzionate)
     * - Importi (se menzionati)
     * - Status blockchain (se richiesto)
     */
    private function findRelevantActs(string $query, User $user, int $limit): Collection
    {
        $queryLower = strtolower($query);

        // Base query: atti PA dell'utente
        $baseQuery = Egi::query()
            ->where('user_id', $user->id)
            ->whereNotNull('pa_protocol_number') // Solo atti PA
            ->orderBy('pa_date', 'desc')
            ->orderBy('created_at', 'desc');

        // Ricerca per numero protocollo
        if (preg_match('/\b(\d{4,}\/\d{4}|\d{4,})\b/', $query, $matches)) {
            $protocolNumber = $matches[1];
            $this->logger->info('[RAG] Searching by protocol number', ['protocol' => $protocolNumber]);
            
            return $baseQuery
                ->where('pa_protocol_number', 'like', "%{$protocolNumber}%")
                ->limit($limit)
                ->get();
        }

        // Ricerca per tipo atto
        $types = ['delibera', 'determina', 'ordinanza', 'decreto'];
        foreach ($types as $type) {
            if (str_contains($queryLower, $type)) {
                $this->logger->info('[RAG] Searching by document type', ['type' => $type]);
                
                return $baseQuery
                    ->where('pa_type', 'like', "%{$type}%")
                    ->limit($limit)
                    ->get();
            }
        }

        // Ricerca per stato blockchain
        if (str_contains($queryLower, 'blockchain') || 
            str_contains($queryLower, 'certificat') ||
            str_contains($queryLower, 'tokenizzat') ||
            str_contains($queryLower, 'anchorat')) {
            $this->logger->info('[RAG] Searching blockchain-anchored acts');
            
            return $baseQuery
                ->where('pa_anchored', true)
                ->limit($limit)
                ->get();
        }

        // Ricerca per importo
        if (preg_match('/(\d+(?:\.\d+)?)\s*(?:euro|€)/', $queryLower, $matches)) {
            $amount = (float) $matches[1];
            $this->logger->info('[RAG] Searching by amount', ['amount' => $amount]);
            
            return $baseQuery
                ->where('pa_amount', '>=', $amount * 0.9) // ±10% tolerance
                ->where('pa_amount', '<=', $amount * 1.1)
                ->limit($limit)
                ->get();
        }

        // Ricerca per keyword nel titolo
        $keywords = $this->extractKeywords($query);
        if (!empty($keywords)) {
            $this->logger->info('[RAG] Searching by keywords', ['keywords' => $keywords]);
            
            $titleSearch = $baseQuery;
            foreach ($keywords as $keyword) {
                $titleSearch->where('pa_title', 'like', "%{$keyword}%");
            }
            
            $results = $titleSearch->limit($limit)->get();
            if ($results->isNotEmpty()) {
                return $results;
            }
        }

        // Ricerca per data (anno)
        if (preg_match('/\b(20\d{2})\b/', $query, $matches)) {
            $year = $matches[1];
            $this->logger->info('[RAG] Searching by year', ['year' => $year]);
            
            return $baseQuery
                ->whereYear('pa_date', $year)
                ->limit($limit)
                ->get();
        }

        // Fallback: ultimi N atti
        $this->logger->info('[RAG] Fallback to recent acts');
        return $baseQuery->limit($limit)->get();
    }

    /**
     * Estrae keyword significative dalla query
     */
    private function extractKeywords(string $query): array
    {
        // Rimuovi stopwords comuni
        $stopwords = [
            'il', 'lo', 'la', 'i', 'gli', 'le',
            'un', 'uno', 'una',
            'di', 'da', 'in', 'con', 'su', 'per', 'tra', 'fra',
            'a', 'e', 'o', 'ma', 'se', 'che', 'chi', 'cui',
            'sono', 'è', 'sei', 'siamo', 'siete', 'hanno',
            'ho', 'hai', 'ha', 'abbiamo', 'avete',
            'mi', 'ti', 'ci', 'vi', 'si',
            'mio', 'tuo', 'suo', 'nostro', 'vostro', 'loro',
            'questo', 'quello', 'questi', 'quelli',
            'quale', 'quali', 'quanto', 'quanti',
            'quando', 'dove', 'come', 'perché',
            'natan', 'mostra', 'trova', 'cerca', 'dimmi', 'quale', 'quali',
        ];

        $words = preg_split('/\s+/', strtolower($query));
        $keywords = array_filter($words, function ($word) use ($stopwords) {
            return strlen($word) >= 4 && !in_array($word, $stopwords);
        });

        return array_values($keywords);
    }

    /**
     * Recupera statistiche globali per il dashboard
     */
    public function getGlobalStats(User $user): array
    {
        $acts = Egi::query()
            ->where('user_id', $user->id)
            ->whereNotNull('pa_protocol_number')
            ->get();

        return $this->sanitizer->createStatsContext($acts);
    }

    /**
     * Recupera suggerimenti di query basati sui dati disponibili
     */
    public function getSuggestions(User $user): array
    {
        $stats = $this->getGlobalStats($user);

        $suggestions = [];

        // Suggerimento: totale atti
        if ($stats['total_acts'] > 0) {
            $suggestions[] = "Quanti atti PA ho caricato?";
        }

        // Suggerimento: atti per tipo
        if (!empty($stats['by_type'])) {
            $topType = array_key_first($stats['by_type']);
            $suggestions[] = "Mostrami tutte le {$topType}";
        }

        // Suggerimento: blockchain
        if ($stats['anchored_acts'] > 0) {
            $suggestions[] = "Quali atti sono certificati su blockchain?";
        }

        // Suggerimento: importi
        if ($stats['total_amount'] > 0) {
            $suggestions[] = "Qual è il valore totale degli atti?";
        }

        // Suggerimento: date
        if (!empty($stats['date_range']['first'])) {
            $year = date('Y', strtotime($stats['date_range']['last']));
            $suggestions[] = "Mostrami gli atti del {$year}";
        }

        // Suggerimenti generici
        $suggestions[] = "Come funziona N.A.T.A.N.?";
        $suggestions[] = "Cosa posso fare con la blockchain?";

        return array_slice($suggestions, 0, 6); // Max 6 suggerimenti
    }
}

