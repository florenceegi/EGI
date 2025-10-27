<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Egi;
use App\Models\PaActEmbedding;
use Illuminate\Support\Facades\Http;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * Embedding Service
 *
 * Generates and manages vector embeddings for PA acts using OpenAI API.
 *
 * ARCHITECTURE:
 * - Uses OpenAI text-embedding-ada-002 (1536 dimensions)
 * - Embeddings stored in MariaDB as JSON
 * - GDPR-compliant: only public metadata (no PII)
 *
 * COST:
 * - ~$0.0001 per 1K tokens
 * - ~$0.02 for 24,000 acts (one-time)
 *
 * @see https://platform.openai.com/docs/guides/embeddings
 */
class EmbeddingService {
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
     * Generate embedding for a single PA act
     *
     * @param Egi $egi PA act EGI
     * @param bool $force Force regeneration even if exists
     * @return PaActEmbedding|null
     */
    public function generateForAct(Egi $egi, bool $force = false): ?PaActEmbedding {
        // Check if already exists
        $existing = $egi->embedding;
        if ($existing && !$force) {
            $this->logger->info('[Embedding] Already exists', ['egi_id' => $egi->id]);
            return $existing;
        }

        // Build content to embed (GDPR-safe)
        $content = $this->buildEmbeddingContent($egi);
        $contentHash = hash('sha256', $content);

        // Check if content changed (skip if same)
        if ($existing && $existing->content_hash === $contentHash && !$force) {
            $this->logger->info('[Embedding] Content unchanged', ['egi_id' => $egi->id]);
            return $existing;
        }

        // Call OpenAI API
        $embeddingData = $this->callOpenAIEmbedding($content);
        if (!$embeddingData) {
            $this->logger->error('[Embedding] Failed to generate', ['egi_id' => $egi->id]);
            return null;
        }

        $vector = $embeddingData['vector'] ?? $embeddingData; // Backward compatibility
        $usage = $embeddingData['usage'] ?? null;

        // Save or update embedding
        $embedding = PaActEmbedding::updateOrCreate(
            ['egi_id' => $egi->id],
            [
                'embedding' => $vector,
                'model' => config('services.openai.embedding_model'),
                'content_hash' => $contentHash,
                'vector_dimension' => count($vector),
            ]
        );

        $this->logger->info('[Embedding] Generated successfully', [
            'egi_id' => $egi->id,
            'dimension' => count($vector),
            'tokens_used' => $usage['total_tokens'] ?? null,
        ]);

        return $embedding;
    }

    /**
     * Build content to embed from PA act
     *
     * GDPR-COMPLIANT:
     * - Only public metadata
     * - No PII, signatures, internal paths
     *
     * @param Egi $egi
     * @return string
     */
    private function buildEmbeddingContent(Egi $egi): string {
        $parts = [];

        // Protocol number (searchable identifier)
        if ($egi->pa_protocol_number) {
            $parts[] = "Protocollo: {$egi->pa_protocol_number}";
        }

        // Document type
        if ($egi->pa_act_type) {
            $parts[] = "Tipo: {$egi->pa_act_type}";
        }

        // Title (most important)
        if ($egi->title) {
            $parts[] = "Titolo: {$egi->title}";
        }

        // Description (detailed content)
        if ($egi->description) {
            $parts[] = "Descrizione: {$egi->description}";
        }

        // Date for temporal context
        if ($egi->pa_protocol_date) {
            $parts[] = "Data: {$egi->pa_protocol_date->format('Y-m-d')}";
        }

        $content = implode("\n", $parts);

        // Validate safety (no PII)
        $this->sanitizer->validateSafeData(['content' => $content]);

        return $content;
    }

    /**
     * Call OpenAI Embeddings API
     *
     * @param string $text Text to embed
     * @return array|null Vector of 1536 floats
     */
    private function callOpenAIEmbedding(string $text): ?array {
        $apiKey = config('services.openai.api_key');
        $baseUrl = config('services.openai.base_url');
        $model = config('services.openai.embedding_model');

        if (!$apiKey) {
            $this->logger->error('[Embedding] OpenAI API key not configured');
            return null;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type' => 'application/json',
            ])
                ->timeout(config('services.openai.timeout', 30))
                ->post("{$baseUrl}/embeddings", [
                    'model' => $model,
                    'input' => $text,
                ]);

            if (!$response->successful()) {
                $this->logger->error('[Embedding] OpenAI API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            $data = $response->json();
            $vector = $data['data'][0]['embedding'] ?? null;
            $usage = $data['usage'] ?? null; // Track token usage

            if (!$vector || !is_array($vector)) {
                $this->logger->error('[Embedding] Invalid response format');
                return null;
            }

            // Log usage for cost tracking
            if ($usage) {
                $this->logger->info('[Embedding] OpenAI usage', [
                    'prompt_tokens' => $usage['prompt_tokens'] ?? 0,
                    'total_tokens' => $usage['total_tokens'] ?? 0,
                ]);
            }

            return [
                'vector' => $vector,
                'usage' => $usage,
            ];
        } catch (\Exception $e) {
            $this->logger->error('[Embedding] Exception calling OpenAI', [
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Calculate cosine similarity between two vectors
     *
     * @param array $vec1 First vector
     * @param array $vec2 Second vector
     * @return float Similarity score (0.0 to 1.0)
     */
    public function cosineSimilarity(array $vec1, array $vec2): float {
        if (count($vec1) !== count($vec2)) {
            throw new \InvalidArgumentException('Vectors must have same dimension');
        }

        $dotProduct = 0.0;
        $magnitude1 = 0.0;
        $magnitude2 = 0.0;

        for ($i = 0; $i < count($vec1); $i++) {
            $dotProduct += $vec1[$i] * $vec2[$i];
            $magnitude1 += $vec1[$i] * $vec1[$i];
            $magnitude2 += $vec2[$i] * $vec2[$i];
        }

        $magnitude1 = sqrt($magnitude1);
        $magnitude2 = sqrt($magnitude2);

        if ($magnitude1 == 0 || $magnitude2 == 0) {
            return 0.0;
        }

        return $dotProduct / ($magnitude1 * $magnitude2);
    }
}
