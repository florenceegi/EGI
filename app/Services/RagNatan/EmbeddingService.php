<?php

namespace App\Services\RagNatan;

use App\Models\RagNatan\Chunk;
use App\Models\RagNatan\Embedding;
use Illuminate\Support\Facades\Http;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * Embedding Service
 *
 * Generates vector embeddings using OpenAI text-embedding-3-small.
 * Handles embedding creation, updates, and batch processing.
 * Adheres to Ultra Standards for logging and error handling.
 *
 * @package App\Services\RagNatan
 * @author Padmin D. Curtis (AI Partner OS3.0)
 */
class EmbeddingService
{
    private const EMBEDDING_MODEL = 'text-embedding-3-small';
    private const EMBEDDING_DIMENSIONS = 1536;
    private const BATCH_SIZE = 100; // OpenAI allows up to 2048 inputs per request
    private const MAX_TOKENS = 8191; // text-embedding-3-small max tokens

    public function __construct(
        private UltraLogManager $logger,
        private ErrorManagerInterface $errorManager
    ) {}

    /**
     * Generate embedding for a single text.
     */
    public function generateEmbedding(string $text): array
    {
        return $this->generateEmbeddings([$text])[0];
    }

    /**
     * Generate embeddings for multiple texts in batch.
     *
     * @param array<string> $texts
     * @return array<array<float>>
     * @throws \RuntimeException
     */
    public function generateEmbeddings(array $texts): array
    {
        if (empty($texts)) {
            $this->logger->debug('rag.embedding.empty_input');
            return [];
        }

        $apiKey = config('services.openai.api_key');

        if (!$apiKey) {
            $this->logger->error('rag.embedding.missing_api_key');
            throw new \RuntimeException(__('rag.error.embedding_failed'));
        }

        try {
            $this->logger->info('rag.embedding.generating', [
                'texts_count' => count($texts),
                'model' => self::EMBEDDING_MODEL
            ]);

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type' => 'application/json',
            ])->timeout(60)->post('https://api.openai.com/v1/embeddings', [
                'model' => self::EMBEDDING_MODEL,
                'input' => $texts,
                'dimensions' => self::EMBEDDING_DIMENSIONS,
            ]);

            if (!$response->successful()) {
                $this->logger->error('rag.embedding.api_error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'texts_count' => count($texts)
                ]);

                $this->errorManager->handle('RAG_EMBEDDING_API_FAILED', [
                    'status' => $response->status(),
                    'texts_count' => count($texts),
                    'error' => $response->body()
                ], new \RuntimeException($response->body()));

                throw new \RuntimeException(__('rag.error.embedding_failed'));
            }

            $data = $response->json();
            $embeddings = [];

            foreach ($data['data'] as $item) {
                $embeddings[] = $item['embedding'];
            }

            $this->logger->info('rag.embedding.generated', [
                'embeddings_count' => count($embeddings),
                'dimensions' => self::EMBEDDING_DIMENSIONS
            ]);

            return $embeddings;
        } catch (\RuntimeException $e) {
            // Re-throw RuntimeException as-is
            throw $e;
        } catch (\Exception $e) {
            $this->logger->error('rag.embedding.exception', [
                'message' => $e->getMessage(),
                'texts_count' => count($texts),
                'trace' => $e->getTraceAsString()
            ]);

            $this->errorManager->handle('RAG_EMBEDDING_EXCEPTION', [
                'texts_count' => count($texts),
                'error' => $e->getMessage()
            ], $e);

            throw $e;
        }
    }

    /**
     * Create or update embedding for a chunk.
     */
    public function embedChunk(Chunk $chunk): Embedding
    {
        $vector = $this->generateEmbedding($chunk->text);

        return $this->storeEmbedding($chunk->id, $vector);
    }

    /**
     * Batch embed multiple chunks.
     *
     * @param \Illuminate\Support\Collection<Chunk> $chunks
     * @return int Number of embeddings created
     * @throws \Exception
     */
    public function embedChunks($chunks): int
    {
        $totalChunks = $chunks->count();
        $this->logger->info('rag.embedding.batch_started', [
            'total_chunks' => $totalChunks,
            'batch_size' => self::BATCH_SIZE
        ]);

        try {
            $count = 0;
            $batches = $chunks->chunk(self::BATCH_SIZE);

            foreach ($batches as $batchIndex => $batch) {
                $this->logger->debug('rag.embedding.processing_batch', [
                    'batch_number' => $batchIndex + 1,
                    'batch_size' => $batch->count(),
                    'total_batches' => $batches->count()
                ]);

                $texts = $batch->pluck('text')->toArray();
                $vectors = $this->generateEmbeddings($texts);

                foreach ($batch as $index => $chunk) {
                    $this->storeEmbedding($chunk->id, $vectors[$index]);
                    $count++;
                }

                // Rate limiting: small delay between batches
                if ($batches->count() > 1) {
                    usleep(100000); // 100ms delay
                }
            }

            $this->logger->info('rag.embedding.batch_completed', [
                'total_embeddings' => $count,
                'batches_processed' => $batches->count()
            ]);

            return $count;
        } catch (\Exception $e) {
            $this->logger->error('rag.embedding.batch_failed', [
                'total_chunks' => $totalChunks,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->errorManager->handle('RAG_EMBEDDING_BATCH_FAILED', [
                'total_chunks' => $totalChunks,
                'error' => $e->getMessage()
            ], $e);

            throw $e;
        }
    }

    /**
     * Store or update embedding in database.
     */
    private function storeEmbedding(int $chunkId, array $vector): Embedding
    {
        try {
            $vectorString = '[' . implode(',', $vector) . ']';

            $embedding = Embedding::updateOrCreate(
                ['chunk_id' => $chunkId],
                [
                    'embedding' => $vectorString,
                    'model' => self::EMBEDDING_MODEL,
                    'model_version' => 'text-embedding-3-small-2024',
                ]
            );

            $this->logger->debug('rag.embedding.stored', [
                'chunk_id' => $chunkId,
                'embedding_id' => $embedding->id,
                'dimensions' => count($vector)
            ]);

            return $embedding;
        } catch (\Exception $e) {
            $this->logger->error('rag.embedding.store_failed', [
                'chunk_id' => $chunkId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->errorManager->handle('RAG_EMBEDDING_STORE_FAILED', [
                'chunk_id' => $chunkId,
                'error' => $e->getMessage()
            ], $e);

            throw $e;
        }
    }

    /**
     * Get embedding dimensions.
     */
    public function getDimensions(): int
    {
        return self::EMBEDDING_DIMENSIONS;
    }

    /**
     * Get embedding model name.
     */
    public function getModel(): string
    {
        return self::EMBEDDING_MODEL;
    }

    /**
     * Estimate token count for text (rough approximation).
     */
    public function estimateTokens(string $text): int
    {
        // Rough estimate: 1 token ≈ 4 characters
        return (int) ceil(strlen($text) / 4);
    }

    /**
     * Check if text exceeds max token limit.
     */
    public function exceedsMaxTokens(string $text): bool
    {
        return $this->estimateTokens($text) > self::MAX_TOKENS;
    }

    /**
     * Truncate text to fit within token limit.
     */
    public function truncateToMaxTokens(string $text): string
    {
        if (!$this->exceedsMaxTokens($text)) {
            return $text;
        }

        $maxChars = self::MAX_TOKENS * 4;
        return substr($text, 0, $maxChars);
    }
}
