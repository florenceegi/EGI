<?php

namespace App\Services\RagNatan;

use App\Models\RagNatan\Chunk;
use App\Models\RagNatan\Response;
use App\Models\RagNatan\Source;
use Illuminate\Support\Facades\Http;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * Response Generation Service
 *
 * Generates RAG responses using retrieved context.
 * Supports Claude and OpenAI models with quality scoring (URS).
 * Adheres to Ultra Standards for logging and error handling.
 *
 * @package App\Services\RagNatan
 * @author Padmin D. Curtis (AI Partner OS3.0)
 */
class ResponseGenerationService
{
    private const DEFAULT_MODEL = 'claude-sonnet-4-5-20250929';
    private const MAX_CHUNKS = 10;
    private const MIN_SIMILARITY_SCORE = 70.0;

    public function __construct(
        private SearchService $searchService,
        private UltraLogManager $logger,
        private ErrorManagerInterface $errorManager
    ) {}

    /**
     * Generate RAG response for a question.
     */
    public function generateResponse(
        string $question,
        string $language = 'it',
        ?array $context = [],
        ?array $options = []
    ): array {
        $startTime = microtime(true);
        $timings = [];

        // Stage 1: Retrieve relevant chunks
        $retrievalStart = microtime(true);
        $chunks = $this->retrieveRelevantChunks(
            $question,
            $language,
            $options['max_chunks'] ?? self::MAX_CHUNKS,
            $options['min_similarity'] ?? self::MIN_SIMILARITY_SCORE,
            $context
        );
        $timings['retrieval'] = (int) ((microtime(true) - $retrievalStart) * 1000);

        if ($chunks->isEmpty()) {
            return $this->generateNoResultsResponse($question, $language, $timings);
        }

        // Stage 2: Build context
        $contextBuildStart = microtime(true);
        $contextText = $this->buildContext($chunks);
        $timings['context_build'] = (int) ((microtime(true) - $contextBuildStart) * 1000);

        // Stage 3: Generate response
        $generationStart = microtime(true);
        $llmResponse = $this->callLLM(
            $question,
            $contextText,
            $language,
            $options['model'] ?? self::DEFAULT_MODEL,
            $options
        );
        $timings['generation'] = (int) ((microtime(true) - $generationStart) * 1000);

        // Stage 4: Calculate URS score
        $scoringStart = microtime(true);
        $ursScore = $this->calculateURSScore($llmResponse, $chunks, $question);
        $timings['scoring'] = (int) ((microtime(true) - $scoringStart) * 1000);

        $totalTime = (int) ((microtime(true) - $startTime) * 1000);

        return [
            'answer' => $llmResponse['answer'],
            'answer_html' => $this->formatAsHtml($llmResponse['answer']),
            'urs_score' => $ursScore,
            'urs_explanation' => $llmResponse['urs_explanation'] ?? null,
            'claims_used' => $llmResponse['claims'] ?? [],
            'gaps_detected' => $llmResponse['gaps'] ?? [],
            'hallucinations' => $llmResponse['hallucinations'] ?? [],
            'sources_used' => $chunks->pluck('id')->toArray(),
            'sources' => $chunks->map(fn($chunk) => [
                'chunk_id' => $chunk->id,
                'document_id' => $chunk->document_id,
                'document_title' => $chunk->document->title,
                'section_title' => $chunk->section_title,
                'text' => $chunk->text,
                'relevance_score' => $chunk->similarity_score,
            ])->toArray(),
            'processing_time_ms' => $totalTime,
            'tokens_input' => $llmResponse['usage']['input_tokens'] ?? null,
            'tokens_output' => $llmResponse['usage']['output_tokens'] ?? null,
            'cost_usd' => $this->calculateCost($llmResponse['usage'] ?? [], $options['model'] ?? self::DEFAULT_MODEL),
            'model_used' => $options['model'] ?? self::DEFAULT_MODEL,
            'stage_timings' => $timings,
        ];
    }

    /**
     * Retrieve relevant chunks for the question.
     */
    private function retrieveRelevantChunks(
        string $question,
        string $language,
        int $maxChunks,
        float $minSimilarity,
        ?array $context
    ): \Illuminate\Support\Collection {
        // Use hybrid search for better results
        $chunks = $this->searchService->hybridSearch($question, $language, $maxChunks);

        // Filter by minimum similarity
        return $chunks->filter(function ($chunk) use ($minSimilarity) {
            return ($chunk->similarity_score ?? 0) >= $minSimilarity;
        });
    }

    /**
     * Build context from chunks.
     */
    private function buildContext(\Illuminate\Support\Collection $chunks): string
    {
        $contextParts = [];

        foreach ($chunks as $index => $chunk) {
            $source = $index + 1;
            $title = $chunk->document->title;
            $section = $chunk->section_title ? " - {$chunk->section_title}" : '';
            $similarity = $chunk->similarity_score ? " (rilevanza: {$chunk->similarity_score}%)" : '';

            $contextParts[] = "[Fonte {$source}: {$title}{$section}{$similarity}]\n{$chunk->text}\n";
        }

        return implode("\n---\n\n", $contextParts);
    }

    /**
     * Call LLM (Claude or OpenAI) to generate response.
     */
    private function callLLM(
        string $question,
        string $context,
        string $language,
        string $model,
        array $options
    ): array {
        if (str_starts_with($model, 'claude')) {
            return $this->callClaude($question, $context, $language, $model, $options);
        } else {
            return $this->callOpenAI($question, $context, $language, $model, $options);
        }
    }

    /**
     * Call Anthropic Claude API.
     */
    private function callClaude(
        string $question,
        string $context,
        string $language,
        string $model,
        array $options
    ): array {
        $apiKey = config('services.anthropic.api_key');

        if (!$apiKey) {
            throw new \RuntimeException('Anthropic API key not configured');
        }

        $systemPrompt = $this->buildSystemPrompt($language);
        $userPrompt = $this->buildUserPrompt($question, $context, $language);

        try {
            $response = Http::withHeaders([
                'x-api-key' => $apiKey,
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
            ])->timeout(120)->post('https://api.anthropic.com/v1/messages', [
                'model' => $model,
                'max_tokens' => $options['max_tokens'] ?? 4096,
                'temperature' => $options['temperature'] ?? 0.7,
                'system' => $systemPrompt,
                'messages' => [
                    ['role' => 'user', 'content' => $userPrompt],
                ],
            ]);

            if (!$response->successful()) {
                $this->logger->error('rag.response.claude_api_error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                $this->errorManager->handle('RAG_CLAUDE_API_FAILED', [
                    'status' => $response->status(),
                    'error' => $response->body()
                ], new \RuntimeException($response->body()));
                throw new \RuntimeException(__('rag.error.response_generation_failed'));
            }

            $data = $response->json();

            return [
                'answer' => $data['content'][0]['text'],
                'usage' => [
                    'input_tokens' => $data['usage']['input_tokens'],
                    'output_tokens' => $data['usage']['output_tokens'],
                ],
            ];
        } catch (\Exception $e) {
            $this->logger->error('rag.response.claude_exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->errorManager->handle('RAG_CLAUDE_EXCEPTION', [
                'error' => $e->getMessage()
            ], $e);
            throw $e;
        }
    }

    /**
     * Call OpenAI API.
     */
    private function callOpenAI(
        string $question,
        string $context,
        string $language,
        string $model,
        array $options
    ): array {
        $apiKey = config('services.openai.api_key');

        if (!$apiKey) {
            throw new \RuntimeException('OpenAI API key not configured');
        }

        $systemPrompt = $this->buildSystemPrompt($language);
        $userPrompt = $this->buildUserPrompt($question, $context, $language);

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type' => 'application/json',
            ])->timeout(120)->post('https://api.openai.com/v1/chat/completions', [
                'model' => $model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
                'temperature' => $options['temperature'] ?? 0.7,
                'max_tokens' => $options['max_tokens'] ?? 4096,
            ]);

            if (!$response->successful()) {
                $this->logger->error('rag.response.openai_api_error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                $this->errorManager->handle('RAG_OPENAI_API_FAILED', [
                    'status' => $response->status(),
                    'error' => $response->body()
                ], new \RuntimeException($response->body()));
                throw new \RuntimeException(__('rag.error.response_generation_failed'));
            }

            $data = $response->json();

            return [
                'answer' => $data['choices'][0]['message']['content'],
                'usage' => [
                    'input_tokens' => $data['usage']['prompt_tokens'],
                    'output_tokens' => $data['usage']['completion_tokens'],
                ],
            ];
        } catch (\Exception $e) {
            $this->logger->error('rag.response.openai_exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->errorManager->handle('RAG_OPENAI_EXCEPTION', [
                'error' => $e->getMessage()
            ], $e);
            throw $e;
        }
    }

    /**
     * Build system prompt for LLM.
     */
    private function buildSystemPrompt(string $language): string
    {
        $prompts = [
            'it' => 'Sei un assistente AI esperto che risponde a domande basandosi esclusivamente sulle fonti fornite. Devi essere preciso, affidabile e trasparente. Se le fonti non contengono informazioni sufficienti per rispondere, dillo chiaramente. Non inventare informazioni. Cita sempre le fonti usando il formato [Fonte N].',
            'en' => 'You are an expert AI assistant that answers questions based exclusively on the provided sources. You must be precise, reliable, and transparent. If the sources don\'t contain enough information to answer, say so clearly. Don\'t make up information. Always cite sources using the format [Source N].',
            'de' => 'Sie sind ein KI-Assistent, der Fragen ausschließlich auf der Grundlage der bereitgestellten Quellen beantwortet. Sie müssen präzise, zuverlässig und transparent sein. Wenn die Quellen nicht genügend Informationen enthalten, sagen Sie dies klar. Erfinden Sie keine Informationen. Zitieren Sie immer die Quellen mit dem Format [Quelle N].',
            'es' => 'Eres un asistente de IA experto que responde preguntas basándose exclusivamente en las fuentes proporcionadas. Debes ser preciso, confiable y transparente. Si las fuentes no contienen suficiente información para responder, dilo claramente. No inventes información. Siempre cita las fuentes usando el formato [Fuente N].',
            'fr' => 'Vous êtes un assistant IA expert qui répond aux questions en se basant exclusivement sur les sources fournies. Vous devez être précis, fiable et transparent. Si les sources ne contiennent pas suffisamment d\'informations pour répondre, dites-le clairement. N\'inventez pas d\'informations. Citez toujours les sources en utilisant le format [Source N].',
            'pt' => 'Você é um assistente de IA especializado que responde perguntas com base exclusivamente nas fontes fornecidas. Você deve ser preciso, confiável e transparente. Se as fontes não contiverem informações suficientes para responder, diga claramente. Não invente informações. Sempre cite as fontes usando o formato [Fonte N].',
        ];

        return $prompts[$language] ?? $prompts['en'];
    }

    /**
     * Build user prompt with question and context.
     */
    private function buildUserPrompt(string $question, string $context, string $language): string
    {
        $templates = [
            'it' => "Contesto dalle fonti:\n\n{$context}\n\n---\n\nDomanda: {$question}\n\nRispondi alla domanda basandoti esclusivamente sulle fonti fornite sopra. Cita le fonti rilevanti nel formato [Fonte N].",
            'en' => "Context from sources:\n\n{$context}\n\n---\n\nQuestion: {$question}\n\nAnswer the question based exclusively on the sources provided above. Cite relevant sources in the format [Source N].",
        ];

        return $templates[$language] ?? $templates['en'];
    }

    /**
     * Calculate URS (Unified Reliability Score).
     */
    private function calculateURSScore(array $llmResponse, \Illuminate\Support\Collection $chunks, string $question): float
    {
        // Simplified URS calculation
        // In production, this would use a separate LLM call or ML model

        $score = 50.0; // Base score

        // Factor 1: Number of sources (more sources = higher confidence)
        $sourceCount = $chunks->count();
        $score += min(20, $sourceCount * 2);

        // Factor 2: Average chunk similarity
        $avgSimilarity = $chunks->avg('similarity_score') ?? 0;
        $score += ($avgSimilarity / 100) * 20;

        // Factor 3: Answer length (reasonable length indicates comprehensive answer)
        $answerLength = strlen($llmResponse['answer']);
        if ($answerLength > 200 && $answerLength < 2000) {
            $score += 10;
        }

        return min(100, max(0, round($score, 2)));
    }

    /**
     * Store source citations.
     */
    public function storeSources(Response $response, array $sources): void
    {
        foreach ($sources as $index => $source) {
            Source::create([
                'response_id' => $response->id,
                'chunk_id' => $source['chunk_id'],
                'relevance_score' => $source['relevance_score'],
                'citation_order' => $index + 1,
            ]);
        }
    }

    /**
     * Generate response when no results found.
     */
    private function generateNoResultsResponse(string $question, string $language, array $timings): array
    {
        $messages = [
            'it' => 'Mi dispiace, non ho trovato informazioni sufficienti nella base di conoscenza per rispondere a questa domanda.',
            'en' => 'I\'m sorry, I couldn\'t find enough information in the knowledge base to answer this question.',
        ];

        return [
            'answer' => $messages[$language] ?? $messages['en'],
            'answer_html' => '<p>' . ($messages[$language] ?? $messages['en']) . '</p>',
            'urs_score' => 0,
            'sources' => [],
            'processing_time_ms' => array_sum($timings),
            'stage_timings' => $timings,
        ];
    }

    /**
     * Format answer as HTML.
     */
    private function formatAsHtml(string $answer): string
    {
        // Simple markdown-to-HTML conversion
        $html = $answer;

        // Convert line breaks
        $html = nl2br($html);

        // Convert bold (**text** or __text__)
        $html = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $html);
        $html = preg_replace('/__(.+?)__/', '<strong>$1</strong>', $html);

        // Convert italic (*text* or _text_)
        $html = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $html);
        $html = preg_replace('/_(.+?)_/', '<em>$1</em>', $html);

        // Convert citations [Fonte N]
        $html = preg_replace('/\[Fonte (\d+)\]/', '<sup class="source-citation">[$1]</sup>', $html);
        $html = preg_replace('/\[Source (\d+)\]/', '<sup class="source-citation">[$1]</sup>', $html);

        return "<div class=\"rag-response\">{$html}</div>";
    }

    /**
     * Calculate API cost based on usage.
     */
    private function calculateCost(array $usage, string $model): float
    {
        // Pricing as of 2025 (approximate)
        $pricing = [
            'claude-sonnet-4-5-20250929' => ['input' => 0.003, 'output' => 0.015], // per 1K tokens
            'gpt-4-turbo' => ['input' => 0.01, 'output' => 0.03],
            'gpt-3.5-turbo' => ['input' => 0.0005, 'output' => 0.0015],
        ];

        $rates = $pricing[$model] ?? ['input' => 0.001, 'output' => 0.002];

        $inputCost = (($usage['input_tokens'] ?? 0) / 1000) * $rates['input'];
        $outputCost = (($usage['output_tokens'] ?? 0) / 1000) * $rates['output'];

        return round($inputCost + $outputCost, 6);
    }
}
