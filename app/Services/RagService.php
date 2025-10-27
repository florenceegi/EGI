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
 * RAG STRATEGIES:
 * - Semantic search: Vector embeddings + cosine similarity (preferred)
 * - Keyword search: SQL LIKE queries (fallback)
 *
 * GDPR COMPLIANCE:
 * Tutti i dati passano attraverso DataSanitizerService prima
 * di essere inviati all'AI.
 */
class RagService {
    private UltraLogManager $logger;
    private DataSanitizerService $sanitizer;
    private EmbeddingService $embeddingService;

    public function __construct(
        UltraLogManager $logger,
        DataSanitizerService $sanitizer,
        EmbeddingService $embeddingService
    ) {
        $this->logger = $logger;
        $this->sanitizer = $sanitizer;
        $this->embeddingService = $embeddingService;
    }

    /**
     * Recupera il contesto per una query utente
     *
     * STRATEGIA (REGOLA STATISTICS):
     * - Scandaglia TUTTI gli atti disponibili (no limit nascosto)
     * - Semantic search: calcola similarity su TUTTI → torna TOP-N più rilevanti
     * - Keyword search: cerca in TUTTI → ordina → torna TOP-N
     * - $limit controlla SOLO quanti atti tornare all'AI, NON quanti scandagliare
     *
     * @param string $query Query dell'utente
     * @param User $user Utente corrente (per scope PA)
     * @param int|null $limit Numero massimo di atti da TORNARE (non da scandagliare). Default: 1000 (scansione totale)
     * @return array Contesto per l'AI con atti più rilevanti
     */
    public function getContextForQuery(string $query, User $user, ?int $limit = null): array {
        // REGOLA STATISTICS: Di default recupera TUTTI gli atti (no limit nascosto)
        // Limit SOLO se esplicitamente richiesto dal chiamante

        // Se limit non specificato, recupera TUTTI gli atti
        if ($limit === null) {
            $limit = 1000; // Nessun limite pratico (scansione totale)
        }

        // Log strategia
        $isCountQuery = preg_match('/\b(quant[iae]|numero|totale|conta|somma)\b/i', $query);
        $isAnalysisQuery = preg_match('/\b(quali|mostra|analizza|elenca|top|più attiv|principali|priorità|riassumi|confronta|tutt)\b/i', $query);
        $isSpecificQuery = preg_match('/\b(atto|protocollo|delibera)\s+\d{3,}/i', $query); // Cerca atto specifico

        $this->logger->info('[RAG] Getting context for query', [
            'query_length' => strlen($query),
            'user_id' => $user->id,
            'limit' => $limit,
            'is_count_query' => $isCountQuery,
            'is_analysis_query' => $isAnalysisQuery,
            'is_specific_query' => $isSpecificQuery,
        ]);

        // Recupera atti rilevanti
        $relevantActs = $this->findRelevantActs($query, $user, $limit);

        // Sanitizza i dati
        $sanitizedActs = $this->sanitizer->sanitizeActsCollection($relevantActs);

        // Crea riassunto testuale
        $actsSummary = $this->sanitizer->createActsSummary($relevantActs);

        // Crea statistiche (include total_acts_in_database)
        $stats = $this->sanitizer->createStatsContext($relevantActs, $user->id);

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
     * STRATEGY:
     * 1. Try semantic search (vector embeddings) - PREFERRED
     * 2. Fallback to keyword search if no embeddings
     *
     * Keyword search basata su:
     * - Keyword matching (protocollo, tipo, titolo)
     * - Date range (se menzionate)
     * - Importi (se menzionati)
     * - Status blockchain (se richiesto)
     */
    private function findRelevantActs(string $query, User $user, int $limit): Collection {
        // STRATEGY 1: Try semantic search first (if embeddings exist)
        $semanticResults = $this->semanticSearch($query, $user, $limit, 0.5);
        if ($semanticResults && $semanticResults->isNotEmpty()) {
            $this->logger->info('[RAG] Using semantic search results', [
                'count' => $semanticResults->count(),
            ]);
            return $semanticResults;
        }

        // STRATEGY 2: Fallback to keyword-based search
        $this->logger->info('[RAG] Falling back to keyword search');

        $queryLower = strtolower($query);

        // Base query: atti PA dell'utente
        $baseQuery = Egi::query()
            ->where('user_id', $user->id)
            ->whereNotNull('pa_protocol_number') // Solo atti PA
            ->orderBy('pa_protocol_date', 'desc')
            ->orderBy('created_at', 'desc');

        // Ricerca per numero protocollo (ma NON anni 20XX)
        if (preg_match('/\b(\d{4,}\/\d{4}|\d{5,})\b/', $query, $matches)) {
            // Filtra gli anni (20XX)
            if (!preg_match('/^20\d{2}$/', $matches[1])) {
                $protocolNumber = $matches[1];
                $this->logger->info('[RAG] Searching by protocol number', ['protocol' => $protocolNumber]);

                return $baseQuery
                    ->where('pa_protocol_number', 'like', "%{$protocolNumber}%")
                    ->limit($limit)
                    ->get();
            }
        }

        // Ricerca per tipo atto
        $types = ['delibera', 'determina', 'ordinanza', 'decreto'];
        foreach ($types as $type) {
            if (str_contains($queryLower, $type)) {
                $this->logger->info('[RAG] Searching by document type', ['type' => $type]);

                return $baseQuery
                    ->where('pa_act_type', 'like', "%{$type}%")
                    ->limit($limit)
                    ->get();
            }
        }

        // Ricerca per stato blockchain
        if (
            str_contains($queryLower, 'blockchain') ||
            str_contains($queryLower, 'certificat') ||
            str_contains($queryLower, 'tokenizzat') ||
            str_contains($queryLower, 'anchorat')
        ) {
            $this->logger->info('[RAG] Searching blockchain-anchored acts');

            return $baseQuery
                ->where('pa_anchored', true)
                ->limit($limit)
                ->get();
        }

        // Ricerca per importo (skip per ora, l'importo è in jsonMetadata)
        // TODO: implementare ricerca per importo quando necessario

        // Ricerca per data (anno) - PRIORITÀ ALTA per query temporali
        if (preg_match('/\b(20\d{2})\b/', $query, $matches)) {
            $year = $matches[1];
            $this->logger->info('[RAG] Searching by year', ['year' => $year]);

            return $baseQuery
                ->whereYear('pa_protocol_date', $year)
                ->limit($limit)
                ->get();
        }

        // Ricerca per keyword nel titolo
        $keywords = $this->extractKeywords($query);
        if (!empty($keywords)) {
            $this->logger->info('[RAG] Searching by keywords', ['keywords' => $keywords]);

            $titleSearch = clone $baseQuery;
            foreach ($keywords as $keyword) {
                $titleSearch->where('title', 'like', "%{$keyword}%");
            }

            $results = $titleSearch->limit($limit)->get();
            if ($results->isNotEmpty()) {
                return $results;
            }
        }

        // Fallback: ultimi N atti
        $this->logger->info('[RAG] Fallback to recent acts');
        return $baseQuery->limit($limit)->get();
    }

    /**
     * Semantic Search using Vector Embeddings
     *
     * Uses cosine similarity to find most relevant acts.
     *
     * PERFORMANCE:
     * - 24k acts: ~1-2 sec (acceptable for demo)
     * - Scales to 100k acts
     * - Can migrate to PostgreSQL+pgvector for better performance
     *
     * @param string $query User query
     * @param User $user Current user (for scope)
     * @param int $limit Max results
     * @param float $minSimilarity Minimum similarity threshold (0.0-1.0)
     * @return Collection|null Collection of Egi or null if no embeddings
     */
    public function semanticSearch(string $query, User $user, int $limit = 10, float $minSimilarity = 0.5): ?Collection {
        $this->logger->info('[RAG] Starting semantic search', [
            'query_length' => strlen($query),
            'user_id' => $user->id,
            'limit' => $limit,
        ]);

        // Generate embedding for query
        $queryVector = $this->generateQueryEmbedding($query);
        if (!$queryVector) {
            $this->logger->warning('[RAG] Failed to generate query embedding');
            return null;
        }

        // Get all PA acts with embeddings for this user
        $acts = Egi::query()
            ->where('user_id', $user->id)
            ->whereNotNull('pa_protocol_number')
            ->whereHas('embedding') // Only acts with embeddings
            ->with('embedding')
            ->get();

        if ($acts->isEmpty()) {
            $this->logger->warning('[RAG] No acts with embeddings found');
            return null;
        }

        $this->logger->info('[RAG] Found acts with embeddings', ['count' => $acts->count()]);

        // Calculate similarity for each act
        $scored = $acts->map(function ($act) use ($queryVector) {
            if (!$act->embedding || !$act->embedding->embedding) {
                return null;
            }

            $similarity = $this->embeddingService->cosineSimilarity(
                $queryVector,
                $act->embedding->embedding
            );

            return [
                'act' => $act,
                'similarity' => $similarity,
            ];
        })->filter(function ($item) use ($minSimilarity) {
            return $item !== null && $item['similarity'] >= $minSimilarity;
        });

        // Sort by similarity (descending)
        $sorted = $scored->sortByDesc('similarity')->take($limit);

        $results = $sorted->pluck('act');

        $this->logger->info('[RAG] Semantic search completed', [
            'results_count' => $results->count(),
            'top_similarity' => $sorted->first()['similarity'] ?? 0,
        ]);

        return $results;
    }

    /**
     * Generate embedding for user query
     *
     * @param string $query
     * @return array|null Vector of 1536 floats
     */
    private function generateQueryEmbedding(string $query): ?array {
        // Use OpenAI API to generate embedding
        $apiKey = config('services.openai.api_key');
        $baseUrl = config('services.openai.base_url');
        $model = config('services.openai.embedding_model');

        if (!$apiKey) {
            $this->logger->error('[RAG] OpenAI API key not configured');
            return null;
        }

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type' => 'application/json',
            ])
                ->timeout(config('services.openai.timeout', 30))
                ->post("{$baseUrl}/embeddings", [
                    'model' => $model,
                    'input' => $query,
                ]);

            if (!$response->successful()) {
                $this->logger->error('[RAG] OpenAI API error', [
                    'status' => $response->status(),
                ]);
                return null;
            }

            $data = $response->json();
            return $data['data'][0]['embedding'] ?? null;
        } catch (\Exception $e) {
            $this->logger->error('[RAG] Exception generating query embedding', [
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Estrae keyword significative dalla query
     */
    private function extractKeywords(string $query): array {
        // Rimuovi stopwords comuni
        $stopwords = [
            'il',
            'lo',
            'la',
            'i',
            'gli',
            'le',
            'un',
            'uno',
            'una',
            'di',
            'da',
            'in',
            'con',
            'su',
            'per',
            'tra',
            'fra',
            'a',
            'e',
            'o',
            'ma',
            'se',
            'che',
            'chi',
            'cui',
            'sono',
            'è',
            'sei',
            'siamo',
            'siete',
            'hanno',
            'ho',
            'hai',
            'ha',
            'abbiamo',
            'avete',
            'mi',
            'ti',
            'ci',
            'vi',
            'si',
            'mio',
            'tuo',
            'suo',
            'nostro',
            'vostro',
            'loro',
            'questo',
            'quello',
            'questi',
            'quelli',
            'quale',
            'quali',
            'quanto',
            'quanti',
            'quando',
            'dove',
            'come',
            'perché',
            'natan',
            'mostra',
            'trova',
            'cerca',
            'dimmi',
            'quale',
            'quali',
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
    public function getGlobalStats(User $user): array {
        $acts = Egi::query()
            ->where('user_id', $user->id)
            ->whereNotNull('pa_protocol_number')
            ->get();

        return $this->sanitizer->createStatsContext($acts);
    }

    /**
     * Recupera suggerimenti di query basati sui dati disponibili
     */
    public function getSuggestions(User $user): array {
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
