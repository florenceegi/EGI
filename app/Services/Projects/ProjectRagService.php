<?php

declare(strict_types=1);

namespace App\Services\Projects;

use App\Models\Project;
use App\Models\ProjectDocumentChunk;
use App\Models\NatanChatMessage;
use App\Services\EmbeddingService;
use Illuminate\Support\Collection;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * Project RAG Service
 * 
 * Priority RAG search for Projects System:
 * 1. Search project documents (uploaded files)
 * 2. Search chat history (searchable knowledge base)
 * 3. Search PA acts (fallback to general knowledge)
 * 
 * SEARCH PRIORITY:
 * - TIER 1: Project-specific documents (highest relevance)
 * - TIER 2: Chat history within project (context awareness)
 * - TIER 3: User's PA acts (general knowledge fallback)
 * 
 * ALGORITHM:
 * - Cosine similarity on embeddings
 * - Configurable similarity threshold (default: 0.5)
 * - Returns top-N results across all tiers
 * 
 * @package App\Services\Projects
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Projects RAG System)
 * @date 2025-10-27
 * @purpose Priority RAG search for project-specific context
 */
class ProjectRagService
{
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;
    protected EmbeddingService $embeddingService;

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        EmbeddingService $embeddingService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->embeddingService = $embeddingService;
    }

    /**
     * Search across project documents, chat history, and PA acts
     * 
     * PRIORITY TIERS:
     * 1. Project documents (uploaded files with embeddings)
     * 2. Chat history (previous conversations in this project)
     * 3. PA acts (user's general knowledge base - if available)
     * 
     * @param string $query User query
     * @param Project $project Current project
     * @param int $limit Total results to return
     * @param float $minSimilarity Minimum similarity threshold (0.0-1.0)
     * @return array Structured results with attribution
     */
    public function searchProjectContext(
        string $query,
        Project $project,
        int $limit = 10,
        float $minSimilarity = 0.5
    ): array {
        $logContext = [
            'service' => 'ProjectRagService',
            'query_length' => strlen($query),
            'project_id' => $project->id,
            'user_id' => $project->user_id,
            'limit' => $limit,
        ];

        $this->logger->info('[ProjectRAG] Starting priority RAG search', $logContext);

        try {
            // Generate query embedding
            $queryEmbedding = $this->generateQueryEmbedding($query);

            if (!$queryEmbedding) {
                $this->logger->warning('[ProjectRAG] Failed to generate query embedding', $logContext);
                return $this->emptyResults();
            }

            // TIER 1: Search project documents
            $documentResults = $this->searchProjectDocuments(
                $queryEmbedding,
                $project,
                $limit,
                $minSimilarity
            );

            // TIER 2: Search chat history
            $chatResults = $this->searchChatHistory(
                $queryEmbedding,
                $project,
                $limit,
                $minSimilarity
            );

            // TODO FASE 4.4: TIER 3 - Search PA acts (requires integration with RagService)

            // Merge and rank results
            $mergedResults = $this->mergeAndRankResults([
                'documents' => $documentResults,
                'chat' => $chatResults,
            ], $limit);

            $this->logger->info('[ProjectRAG] Search completed', [
                ...$logContext,
                'total_results' => count($mergedResults),
                'documents_found' => count($documentResults),
                'chat_found' => count($chatResults),
            ]);

            return [
                'results' => $mergedResults,
                'stats' => [
                    'total' => count($mergedResults),
                    'documents' => count($documentResults),
                    'chat' => count($chatResults),
                    'query_embedding_generated' => true,
                ],
            ];

        } catch (\Throwable $e) {
            $this->logger->error('[ProjectRAG] Search failed', [
                ...$logContext,
                'error' => $e->getMessage(),
                'exception' => get_class($e),
            ]);

            $this->errorManager->handle('PROJECT_RAG_SEARCH_FAILED', $logContext, $e);

            return $this->emptyResults();
        }
    }

    /**
     * Search project document chunks
     * 
     * @param array $queryEmbedding Query vector (1536 dims)
     * @param Project $project
     * @param int $limit
     * @param float $minSimilarity
     * @return array Results with similarity scores
     */
    protected function searchProjectDocuments(
        array $queryEmbedding,
        Project $project,
        int $limit,
        float $minSimilarity
    ): array {
        $this->logger->info('[ProjectRAG] Searching project documents', [
            'project_id' => $project->id,
        ]);

        // Get all chunks from project documents with embeddings
        $chunks = ProjectDocumentChunk::query()
            ->whereHas('projectDocument', function ($query) use ($project) {
                $query->where('project_id', $project->id)
                    ->where('status', 'ready'); // Only processed documents
            })
            ->with(['projectDocument'])
            ->whereNotNull('embedding')
            ->get();

        if ($chunks->isEmpty()) {
            $this->logger->info('[ProjectRAG] No document chunks found');
            return [];
        }

        // Calculate similarity for each chunk
        $scored = $chunks->map(function ($chunk) use ($queryEmbedding) {
            $similarity = $this->embeddingService->cosineSimilarity(
                $queryEmbedding,
                $chunk->embedding
            );

            return [
                'type' => 'document',
                'chunk_id' => $chunk->id,
                'document_id' => $chunk->project_document_id,
                'document_name' => $chunk->projectDocument->filename,
                'text' => $chunk->chunk_text,
                'similarity' => $similarity,
                'page_number' => $chunk->page_number,
                'metadata' => $chunk->metadata,
            ];
        })->filter(function ($item) use ($minSimilarity) {
            return $item['similarity'] >= $minSimilarity;
        })->sortByDesc('similarity')
          ->take($limit)
          ->values()
          ->toArray();

        $this->logger->info('[ProjectRAG] Document search completed', [
            'chunks_scanned' => $chunks->count(),
            'results_found' => count($scored),
            'top_similarity' => $scored[0]['similarity'] ?? 0,
        ]);

        return $scored;
    }

    /**
     * Search chat history within project
     * 
     * Note: Chat messages don't have embeddings in MVP.
     * Future enhancement: add embeddings to natan_chat_messages
     * For now: keyword matching fallback
     * 
     * @param array $queryEmbedding Query vector (1536 dims)
     * @param Project $project
     * @param int $limit
     * @param float $minSimilarity
     * @return array Results with similarity scores
     */
    protected function searchChatHistory(
        array $queryEmbedding,
        Project $project,
        int $limit,
        float $minSimilarity
    ): array {
        $this->logger->info('[ProjectRAG] Searching chat history', [
            'project_id' => $project->id,
        ]);

        // TODO FASE 4.4: Add embeddings to natan_chat_messages
        // For MVP: Simple keyword matching
        $messages = NatanChatMessage::query()
            ->where('project_id', $project->id)
            ->where('user_id', $project->user_id)
            ->orderBy('created_at', 'desc')
            ->limit(100) // Scan recent 100 messages
            ->get();

        if ($messages->isEmpty()) {
            $this->logger->info('[ProjectRAG] No chat history found');
            return [];
        }

        // Simple keyword matching (temporary until we add embeddings)
        $keywords = $this->extractKeywords($this->getQueryFromEmbedding($queryEmbedding));
        
        $results = [];
        foreach ($messages as $message) {
            $score = $this->calculateKeywordSimilarity(
                $keywords,
                $message->message . ' ' . ($message->response ?? '')
            );

            if ($score >= $minSimilarity) {
                $results[] = [
                    'type' => 'chat',
                    'message_id' => $message->id,
                    'text' => $message->message,
                    'response' => $message->response,
                    'similarity' => $score,
                    'created_at' => $message->created_at->toIso8601String(),
                ];
            }
        }

        // Sort by score
        usort($results, function ($a, $b) {
            return $b['similarity'] <=> $a['similarity'];
        });

        $results = array_slice($results, 0, $limit);

        $this->logger->info('[ProjectRAG] Chat search completed', [
            'messages_scanned' => $messages->count(),
            'results_found' => count($results),
        ]);

        return $results;
    }

    /**
     * Generate embedding for query
     * 
     * @param string $query
     * @return array|null Vector (1536 floats) or null on failure
     */
    protected function generateQueryEmbedding(string $query): ?array
    {
        try {
            $result = $this->embeddingService->callOpenAIEmbedding($query);

            if (!$result) {
                return null;
            }

            // Handle both old format (array) and new format (array with 'vector' key)
            if (isset($result['vector'])) {
                return $result['vector'];
            }

            return $result;

        } catch (\Throwable $e) {
            $this->logger->error('[ProjectRAG] Query embedding generation failed', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Merge and rank results from multiple tiers
     * 
     * RANKING STRATEGY:
     * - Documents get 1.0x weight (highest priority)
     * - Chat gets 0.8x weight (context aware)
     * - PA acts get 0.6x weight (general knowledge)
     * 
     * @param array $tierResults Results from each tier
     * @param int $limit Total results to return
     * @return array Merged and ranked results
     */
    protected function mergeAndRankResults(array $tierResults, int $limit): array
    {
        $merged = [];

        // Apply tier weights
        foreach ($tierResults['documents'] ?? [] as $result) {
            $result['weighted_similarity'] = $result['similarity'] * 1.0;
            $merged[] = $result;
        }

        foreach ($tierResults['chat'] ?? [] as $result) {
            $result['weighted_similarity'] = $result['similarity'] * 0.8;
            $merged[] = $result;
        }

        // Sort by weighted similarity
        usort($merged, function ($a, $b) {
            return $b['weighted_similarity'] <=> $a['weighted_similarity'];
        });

        // Return top-N
        return array_slice($merged, 0, $limit);
    }

    /**
     * Extract keywords from query for fallback search
     * 
     * @param string $query
     * @return array Keywords
     */
    protected function extractKeywords(string $query): array
    {
        // Remove common words
        $stopWords = ['il', 'lo', 'la', 'i', 'gli', 'le', 'di', 'a', 'da', 'in', 'con', 'su', 'per', 'tra', 'fra', 'e', 'o', 'ma', 'the', 'a', 'an', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for'];
        
        $words = \preg_split('/\s+/', strtolower($query));
        $keywords = array_filter($words, function ($word) use ($stopWords) {
            return strlen($word) > 3 && !in_array($word, $stopWords);
        });

        return array_values($keywords);
    }

    /**
     * Calculate keyword-based similarity (fallback)
     * 
     * @param array $keywords
     * @param string $text
     * @return float Similarity score (0.0-1.0)
     */
    protected function calculateKeywordSimilarity(array $keywords, string $text): float
    {
        if (empty($keywords)) {
            return 0.0;
        }

        $textLower = strtolower($text);
        $matches = 0;

        foreach ($keywords as $keyword) {
            if (str_contains($textLower, $keyword)) {
                $matches++;
            }
        }

        return $matches / count($keywords);
    }

    /**
     * Get query text from embedding (placeholder)
     * 
     * Note: We can't reverse embeddings to text.
     * This is a placeholder - in real usage, we keep original query.
     * 
     * @param array $embedding
     * @return string Empty string (can't reverse embeddings)
     */
    protected function getQueryFromEmbedding(array $embedding): string
    {
        // This is a design limitation - we should pass original query separately
        // For now, return empty (chat search will be skipped in MVP)
        return '';
    }

    /**
     * Empty results structure
     * 
     * @return array
     */
    protected function emptyResults(): array
    {
        return [
            'results' => [],
            'stats' => [
                'total' => 0,
                'documents' => 0,
                'chat' => 0,
                'query_embedding_generated' => false,
            ],
        ];
    }
}
