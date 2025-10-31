<?php

namespace App\Services;

use App\Models\NatanUnifiedContext;
use App\Services\EmbeddingService;
use App\Services\WebSearch\WebSearchService;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

/**
 * Unified Knowledge Service
 * 
 * Unifica tutte le fonti di conoscenza (Acts, Web, Memory, Files) in un'unica
 * knowledge base con semantic search. Ogni chunk ha source tracking per citazioni.
 */
class UnifiedKnowledgeService {
    protected EmbeddingService $embeddingService;
    protected WebSearchService $webSearchService;

    // TTL differenziati per tipo di fonte (strategia ibrida)
    protected array $ttlMap = [
        'act' => 30,    // 30 giorni - atti amministrativi cambiano raramente
        'web' => 0.25,  // 6 ore (0.25 giorni) - info web dinamiche
        'memory' => 7,  // 7 giorni - conversazioni
        'file' => 90,   // 90 giorni - documenti progetto stabili
    ];

    // Configurazione chunking adaptive
    protected int $maxChunkSize = 4000;  // 4000 caratteri per chunk
    protected int $overlapSize = 500;    // 500 caratteri di overlap

    public function __construct(
        EmbeddingService $embeddingService,
        WebSearchService $webSearchService
    ) {
        $this->embeddingService = $embeddingService;
        $this->webSearchService = $webSearchService;
    }

    /**
     * Main method: cerca in tutte le fonti e restituisce unified context
     * 
     * @param string $query User query
     * @param array $options Opzioni di ricerca
     * @return Collection Collection di NatanUnifiedContext ordinati per similarity
     */
    public function search(string $query, array $options = []): Collection {
        $sessionId = Str::uuid()->toString();

        Log::info('[UnifiedKnowledgeService] Starting unified search', [
            'query_length' => strlen($query),
            'session_id' => $sessionId,
            'options' => $options,
        ]);

        // STEP 1: Cerca se esiste già context cachato per query simili
        $cachedContext = $this->searchCachedContext($query, $options);
        if ($cachedContext->isNotEmpty()) {
            Log::info('[UnifiedKnowledgeService] Using cached context', [
                'cached_chunks' => $cachedContext->count(),
            ]);
            return $cachedContext;
        }

        // STEP 2: Raccogli dati da tutte le fonti
        $sources = $this->gatherFromAllSources($query, $options);

        // STEP 3: Normalizza e chunka i dati
        $chunks = $this->normalizeAndChunk($sources);

        Log::info('[UnifiedKnowledgeService] Sources chunked', [
            'total_chunks' => count($chunks),
            'by_type' => collect($chunks)->countBy('source_type')->toArray(),
        ]);

        // STEP 4: Genera embeddings per tutti i chunks
        $chunksWithEmbeddings = $this->generateEmbeddings($chunks, $query);

        // STEP 5: Salva in unified_context con TTL
        $this->storeUnifiedContext($sessionId, $chunksWithEmbeddings);

        // STEP 6: Semantic search su unified table
        Log::info('[UnifiedKnowledgeService] Generating query embedding for semantic search');
        $queryEmbedding = $this->embeddingService->callOpenAIEmbedding($query);
        Log::info('[UnifiedKnowledgeService] Query embedding generated, performing semantic search');
        $results = $this->semanticSearchUnified($sessionId, $queryEmbedding, $options['limit'] ?? 50);

        Log::info('[UnifiedKnowledgeService] Unified search completed', [
            'results_count' => $results->count(),
            'top_similarity' => $results->first()?->similarity_score,
            'avg_similarity' => $results->avg('similarity_score'),
        ]);

        return $results;
    }

    /**
     * Cerca context già cachato per query semanticamente simili
     */
    protected function searchCachedContext(string $query, array $options): Collection {
        // TODO: implementare similarity search su query precedenti
        // Per ora ritorna vuoto (sempre fresh search)
        return collect();
    }

    /**
     * Raccoglie dati da tutte le fonti disponibili
     */
    protected function gatherFromAllSources(string $query, array $options): array {
        $sources = [];

        // Source 1: Acts (RAG esistente)
        if ($options['search_acts'] ?? true) {
            $sources['acts'] = $options['acts'] ?? [];
            Log::info('[UnifiedKnowledgeService] Acts provided', [
                'count' => count($sources['acts']),
            ]);
        }

        // Source 2: Web (Perplexity)
        if ($options['search_web'] ?? false) {
            try {
                // Se non abbiamo già web_results, chiamiamo Perplexity
                if (empty($options['web_results'])) {
                    Log::info('[UnifiedKnowledgeService] Calling Perplexity for web search', [
                        'query' => substr($query, 0, 100),
                    ]);
                    
                    $webSearchService = app(\App\Services\WebSearch\WebSearchService::class);
                    $webResults = $webSearchService->search($query);
                    
                    Log::info('[UnifiedKnowledgeService] Perplexity results received', [
                        'count' => count($webResults),
                    ]);
                } else {
                    $webResults = $options['web_results'];
                    Log::info('[UnifiedKnowledgeService] Web results provided externally', [
                        'count' => count($webResults),
                    ]);
                }
                
                $sources['web'] = $webResults;
            } catch (\Exception $e) {
                Log::error('[UnifiedKnowledgeService] Web search failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                $sources['web'] = [];
            }
        }

        // Source 3: Memory (conversazioni passate)
        if ($options['search_memory'] ?? false) {
            // TODO: implementare memory search
            $sources['memory'] = [];
        }

        // Source 4: Project files
        if ($options['project_id'] ?? null) {
            // TODO: implementare project files search
            $sources['files'] = [];
        }

        return $sources;
    }

    /**
     * Normalizza e chunka tutti i dati da diverse fonti
     */
    protected function normalizeAndChunk(array $sources): array {
        $allChunks = [];

        foreach ($sources as $sourceType => $items) {
            foreach ($items as $item) {
                $normalized = $this->normalizeSource($sourceType, $item);

                // Chunka il contenuto con overlap
                $textChunks = $this->chunkTextWithOverlap($normalized['content']);

                foreach ($textChunks as $index => $chunkText) {
                    $allChunks[] = [
                        'content' => $chunkText,
                        'source_type' => $sourceType,
                        'source_id' => $normalized['source_id'],
                        'source_url' => $normalized['source_url'],
                        'source_title' => $normalized['source_title'] . ($index > 0 ? " (parte " . ($index + 1) . ")" : ''),
                        'metadata' => $normalized['metadata'],
                    ];
                }
            }
        }

        return $allChunks;
    }

    /**
     * Normalizza una singola fonte in formato standard
     */
    protected function normalizeSource(string $type, $item): array {
        switch ($type) {
            case 'acts':
                // Item è un oggetto Egi (act)
                $content = $item->title . "\n\n";
                if (!empty($item->description)) {
                    $content .= $item->description;
                }

                return [
                    'content' => $content,
                    'source_id' => $item->id,
                    'source_url' => route('pa.acts.show', $item->id),
                    'source_title' => $item->title,
                    'metadata' => [
                        'date' => $item->date,
                        'protocol' => $item->protocol_number ?? null,
                        'type' => $item->type ?? 'act',
                        'direction' => $item->direction ?? null,
                    ],
                ];

            case 'web':
                // Item è un array da WebSearchService
                return [
                    'content' => $item['snippet'] ?? $item['text'] ?? '',
                    'source_id' => null,
                    'source_url' => $item['url'] ?? '',
                    'source_title' => $item['title'] ?? 'Web Result',
                    'metadata' => [
                        'relevance' => $item['relevance_score'] ?? null,
                        'provider' => $item['source'] ?? 'perplexity',
                        'date' => $item['date'] ?? null,
                    ],
                ];

            case 'memory':
                // TODO: implementare memory normalization
                return [
                    'content' => $item['message'] ?? '',
                    'source_id' => $item['id'] ?? null,
                    'source_url' => null,
                    'source_title' => "Conversazione del " . ($item['date'] ?? 'N/A'),
                    'metadata' => [
                        'role' => $item['role'] ?? 'user',
                        'date' => $item['date'] ?? null,
                    ],
                ];

            case 'files':
                // TODO: implementare files normalization
                return [
                    'content' => $item['content'] ?? '',
                    'source_id' => $item['id'] ?? null,
                    'source_url' => $item['url'] ?? null,
                    'source_title' => $item['filename'] ?? 'Document',
                    'metadata' => [
                        'project_id' => $item['project_id'] ?? null,
                        'mime_type' => $item['mime_type'] ?? null,
                    ],
                ];

            default:
                throw new \InvalidArgumentException("Unknown source type: {$type}");
        }
    }

    /**
     * Chunka testo con overlap (4000 char + 500 overlap)
     */
    protected function chunkTextWithOverlap(string $text): array {
        if (strlen($text) <= $this->maxChunkSize) {
            return [$text];
        }

        $chunks = [];

        // Split by sentences per evitare di tagliare a metà frase
        $sentences = preg_split('/(?<=[.!?])\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY);

        $currentChunk = '';
        $previousOverlap = '';

        foreach ($sentences as $sentence) {
            $potentialChunk = $currentChunk . $sentence . ' ';

            if (strlen($potentialChunk) > $this->maxChunkSize && !empty($currentChunk)) {
                // Salva chunk corrente
                $chunks[] = trim($currentChunk);

                // Crea overlap: ultimi X caratteri del chunk precedente
                $previousOverlap = substr($currentChunk, -$this->overlapSize);

                // Nuovo chunk inizia con overlap + frase corrente
                $currentChunk = $previousOverlap . $sentence . ' ';
            } else {
                $currentChunk = $potentialChunk;
            }
        }

        // Aggiungi ultimo chunk
        if (!empty(trim($currentChunk))) {
            $chunks[] = trim($currentChunk);
        }

        return $chunks;
    }

    /**
     * Genera embeddings per tutti i chunks
     */
    protected function generateEmbeddings(array $chunks, string $query): array {
        Log::info('[UnifiedKnowledgeService] Generating embeddings', [
            'chunks_count' => count($chunks),
        ]);

        $startTime = microtime(true);
        $chunksWithEmbeddings = [];

        // Genera embeddings uno alla volta (EmbeddingService non supporta batch)
        foreach ($chunks as $index => $chunk) {
            try {
                $embedding = $this->embeddingService->callOpenAIEmbedding($chunk['content']);

                if ($embedding) {
                    $chunk['embedding'] = $embedding;
                    $chunksWithEmbeddings[] = $chunk;

                    // Log ogni 50 chunks
                    if (($index + 1) % 50 === 0) {
                        Log::info('[UnifiedKnowledgeService] Embeddings progress', [
                            'processed' => $index + 1,
                            'total' => count($chunks),
                            'percentage' => round((($index + 1) / count($chunks)) * 100, 1),
                        ]);
                    }
                } else {
                    Log::warning('[UnifiedKnowledgeService] Empty embedding returned', [
                        'chunk_index' => $index,
                        'content_preview' => substr($chunk['content'], 0, 100),
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('[UnifiedKnowledgeService] Embedding generation failed', [
                    'chunk_index' => $index,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $duration = round((microtime(true) - $startTime) * 1000);

        Log::info('[UnifiedKnowledgeService] All embeddings generated', [
            'total_chunks' => count($chunksWithEmbeddings),
            'duration_ms' => $duration,
        ]);

        return $chunksWithEmbeddings;
    }

    /**
     * Salva chunks in unified_context con TTL appropriato
     * Usa content hash per evitare duplicati (updateOrCreate su source_type + source_id + content_hash)
     */
    protected function storeUnifiedContext(string $sessionId, array $chunks): void {
        $now = now();
        $stored = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($chunks as $chunk) {
            $sourceType = $chunk['source_type'];
            $ttlDays = $this->ttlMap[$sourceType] ?? 7;
            $expiresAt = $now->copy()->addDays($ttlDays);

            // Crea hash del contenuto per identificare chunk univoco
            $contentHash = md5($chunk['content']);

            // Cerca chunk esistente con stesso source_type + source_id + content
            $existing = NatanUnifiedContext::query()
                ->where('source_type', $sourceType)
                ->where('source_id', $chunk['source_id'])
                ->whereRaw('MD5(content) = ?', [$contentHash])
                ->first();

            if ($existing) {
                // Chunk già esistente
                if ($existing->expires_at < $now) {
                    // Scaduto, aggiorna TTL
                    $existing->update(['expires_at' => $expiresAt]);
                    $updated++;
                } else {
                    // Ancora valido, skip
                    $skipped++;
                }
            } else {
                // Nuovo chunk, crea
                NatanUnifiedContext::create([
                    'session_id' => $sessionId,
                    'content' => $chunk['content'],
                    'embedding' => $chunk['embedding'],
                    'source_type' => $sourceType,
                    'source_id' => $chunk['source_id'],
                    'source_url' => $chunk['source_url'],
                    'source_title' => $chunk['source_title'],
                    'metadata' => $chunk['metadata'],
                    'expires_at' => $expiresAt,
                ]);
                $stored++;
            }
        }

        Log::info('[UnifiedKnowledgeService] Chunks processed in unified_context', [
            'session_id' => $sessionId,
            'total_chunks' => count($chunks),
            'stored_new' => $stored,
            'updated_expired' => $updated,
            'skipped_valid' => $skipped,
        ]);
    }

    /**
     * Semantic search su unified_context table
     */
    protected function semanticSearchUnified(string $sessionId, array $queryEmbedding, int $limit): Collection {
        // Carica tutti i chunks attivi (condivisi tra tutte le PA entities, non multi-tenant per ora)
        // TODO: quando diventerà multi-tenant, aggiungere filtro per tenant_id
        $chunks = NatanUnifiedContext::query()
            ->active() // Solo chunks non scaduti
            ->get();

        // Calcola similarity_score per ogni chunk
        foreach ($chunks as $chunk) {
            $chunkEmbedding = $chunk->embedding; // Array da JSON
            $similarity = $this->cosineSimilarity($queryEmbedding, $chunkEmbedding);
            $chunk->similarity_score = $similarity;
        }

        // Ordina per similarity decrescente e prendi top N
        return $chunks->sortByDesc('similarity_score')->take($limit)->values();
    }

    /**
     * Calcola cosine similarity tra due vettori
     */
    protected function cosineSimilarity(array $a, array $b): float {
        $dotProduct = 0;
        $magnitudeA = 0;
        $magnitudeB = 0;

        for ($i = 0; $i < count($a); $i++) {
            $dotProduct += $a[$i] * $b[$i];
            $magnitudeA += $a[$i] ** 2;
            $magnitudeB += $b[$i] ** 2;
        }

        $magnitudeA = sqrt($magnitudeA);
        $magnitudeB = sqrt($magnitudeB);

        if ($magnitudeA == 0 || $magnitudeB == 0) {
            return 0;
        }

        return $dotProduct / ($magnitudeA * $magnitudeB);
    }

    /**
     * Formatta unified context per prompt Claude
     */
    public function formatForPrompt(Collection $results): string {
        if ($results->isEmpty()) {
            return "Nessuna fonte disponibile.";
        }

        $formatted = "# FONTI DISPONIBILI (ordinate per rilevanza)\n\n";
        $formatted .= "Hai accesso a {$results->count()} fonti rilevanti da diverse origini.\n";
        $formatted .= "Ogni fonte ha un punteggio di rilevanza (similarity) che indica quanto è pertinente alla query.\n\n";

        $groupedByType = $results->groupBy('source_type');

        $formatted .= "## Distribuzione fonti:\n";
        foreach ($groupedByType as $type => $items) {
            $typeLabel = match ($type) {
                'act' => 'Atti Amministrativi',
                'web' => 'Fonti Web',
                'memory' => 'Conversazioni Precedenti',
                'file' => 'Documenti Progetto',
                default => $type,
            };
            $formatted .= "- {$typeLabel}: {$items->count()} fonti\n";
        }

        $formatted .= "\n## ELENCO FONTI:\n\n";

        foreach ($results as $index => $result) {
            $formatted .= $this->formatSingleSource($index + 1, $result);
        }

        $formatted .= "\n---\n\n";
        $formatted .= "**IMPORTANTE:** Quando usi informazioni da queste fonti, CITA SEMPRE la fonte specifica ";
        $formatted .= "usando il formato: [FONTE #{numero}] o menzionando il titolo della fonte.\n";
        $formatted .= "Priorità informazioni: TUTTE le fonti hanno lo stesso peso - usa quelle più rilevanti per la query.\n";

        return $formatted;
    }

    /**
     * Formatta una singola fonte per il prompt
     */
    protected function formatSingleSource(int $index, NatanUnifiedContext $result): string {
        $typeLabel = match ($result->source_type) {
            'act' => 'ATTO',
            'web' => 'WEB',
            'memory' => 'MEMORIA',
            'file' => 'FILE',
        };

        $similarity = sprintf('%.0f%%', $result->similarity_score * 100);

        $formatted = "### FONTE #{$index} [{$typeLabel}] - Rilevanza: {$similarity}\n";
        $formatted .= "**Titolo:** {$result->source_title}\n";

        if ($result->source_url) {
            $formatted .= "**URL:** {$result->source_url}\n";
        }

        if ($result->metadata) {
            if (isset($result->metadata['date'])) {
                $formatted .= "**Data:** {$result->metadata['date']}\n";
            }
            if (isset($result->metadata['protocol'])) {
                $formatted .= "**Protocollo:** {$result->metadata['protocol']}\n";
            }
        }

        $formatted .= "\n**Contenuto:**\n```\n";
        $formatted .= substr($result->content, 0, 2000); // Max 2000 char per fonte
        if (strlen($result->content) > 2000) {
            $formatted .= "\n[...contenuto troncato...]";
        }
        $formatted .= "\n```\n\n";

        return $formatted;
    }
}
