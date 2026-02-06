<?php

namespace App\Services\RagNatan;

use App\Models\RagNatan\Chunk;
use App\Models\RagNatan\Embedding;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Embedding Service
 *
 * Generates vector embeddings using OpenAI text-embedding-3-small.
 * Handles embedding creation, updates, and batch processing.
 */
class EmbeddingService
{
    private const EMBEDDING_MODEL = 'text-embedding-3-small';
    private const EMBEDDING_DIMENSIONS = 1536;
    private const BATCH_SIZE = 100; // OpenAI allows up to 2048 inputs per request
    private const MAX_TOKENS = 8191; // text-embedding-3-small max tokens

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
     */
    public function generateEmbeddings(array $texts): array
    {
        if (empty($texts)) {
            return [];
        }

        $apiKey = config('services.openai.api_key');

        if (!$apiKey) {
            throw new \RuntimeException('OpenAI API key not configured');
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type' => 'application/json',
            ])->timeout(60)->post('https://api.openai.com/v1/embeddings', [
                'model' => self::EMBEDDING_MODEL,
                'input' => $texts,
                'dimensions' => self::EMBEDDING_DIMENSIONS,
            ]);

            if (!$response->successful()) {
                Log::error('OpenAI embedding API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                throw new \RuntimeException('Failed to generate embeddings: ' . $response->body());
            }

            $data = $response->json();
            $embeddings = [];

            foreach ($data['data'] as $item) {
                $embeddings[] = $item['embedding'];
            }

            return $embeddings;
        } catch (\Exception $e) {
            Log::error('Exception generating embeddings', [
                'message' => $e->getMessage(),
                'texts_count' => count($texts),
            ]);
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
     */
    public function embedChunks($chunks): int
    {
        $count = 0;
        $batches = $chunks->chunk(self::BATCH_SIZE);

        foreach ($batches as $batch) {
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

        return $count;
    }

    /**
     * Store or update embedding in database.
     */
    private function storeEmbedding(int $chunkId, array $vector): Embedding
    {
        $vectorString = '[' . implode(',', $vector) . ']';

        return Embedding::updateOrCreate(
            ['chunk_id' => $chunkId],
            [
                'embedding' => $vectorString,
                'model' => self::EMBEDDING_MODEL,
                'model_version' => 'text-embedding-3-small-2024',
            ]
        );
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
