<?php

namespace App\Jobs;

use App\Models\Egi;
use App\Services\NatanChatService;
use App\Services\Natan\NatanIntelligentChunkingService;
use App\Services\AiCreditsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * Process Chunked Analysis Job
 *
 * @package App\Jobs
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - NATAN Intelligent Chunking)
 * @date 2025-10-27
 * @purpose Background job to process large datasets in chunks with Claude AI
 *
 * WORKFLOW:
 * 1. Load session from cache
 * 2. Fetch acts from database
 * 3. Chunk acts using intelligent strategy
 * 4. Process each chunk sequentially with Claude
 * 5. Update cache progress after each chunk
 * 6. Aggregate all chunk results
 * 7. Store final response in cache
 *
 * CACHE SESSION STRUCTURE:
 * {
 *   "user_id": 123,
 *   "query": "delibere sostenibilità",
 *   "user_limit": 500,
 *   "total_acts": 489,
 *   "total_chunks": 5,
 *   "keywords": ["delibere", "sostenibilità"],
 *   "current_chunk": 2,
 *   "chunk_progress": 75,
 *   "acts_in_current_chunk": 180,
 *   "completed_chunks": [0, 1],
 *   "last_completed": true,
 *   "last_completed_index": 1,
 *   "last_chunk_relevant_acts": 23,
 *   "last_chunk_summary": "...",
 *   "status": "processing|aggregating|completed|failed",
 *   "final_response": "...",
 *   "total_relevant_acts": 67,
 *   "total_time_seconds": 52,
 *   "sources": [...],
 *   "strategy": "token-based",
 *   "started_at": "2025-10-27T...",
 *   "completed_at": "2025-10-27T...",
 * }
 */
class ProcessChunkedAnalysis implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 600; // 10 minutes max (can process ~60 chunks @ 10s each)

    /**
     * Session ID for cache lookup
     *
     * @var string
     */
    protected string $sessionId;

    /**
     * Create a new job instance.
     *
     * @param string $sessionId
     */
    public function __construct(string $sessionId) {
        $this->sessionId = $sessionId;
    }

    /**
     * Execute the job.
     *
     * @param NatanChatService $chatService
     * @param NatanIntelligentChunkingService $chunkingService
     * @param AiCreditsService $creditsService
     * @param UltraLogManager $logger
     * @param ErrorManagerInterface $errorManager
     * @return void
     */
    public function handle(
        NatanChatService $chatService,
        NatanIntelligentChunkingService $chunkingService,
        AiCreditsService $creditsService,
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ): void {
        $startTime = now();

        // Initialize cost tracking variables
        $totalInputTokens = 0;
        $totalOutputTokens = 0;
        $totalCreditsConsumed = 0;
        $creditsTransactionId = null; // Will store initial deduct transaction ID for potential refund

        try {
            $logger->info('[NATAN Job] Starting chunked analysis', [
                'session_id' => $this->sessionId,
            ]);

            // 1. Load session from cache
            $session = Cache::get("natan_chunking_{$this->sessionId}");

            if (!$session) {
                $errorManager->handle('NATAN_JOB_SESSION_NOT_FOUND', [
                    'session_id' => $this->sessionId,
                ]);
                return;
            }

            // Load user for credits management
            $user = \App\Models\User::find($session['user_id']);

            // 2. Fetch acts from database
            $keywords = $session['keywords'] ?? [];
            $userId = $session['user_id'];
            $limit = $session['user_limit'] ?? 500;

            $acts = Egi::query()
                ->whereHas('collection', function ($q) use ($userId) {
                    $q->where('creator_id', $userId);
                })
                ->whereNotNull('pa_act_type')
                ->where(function ($q) use ($keywords) {
                    foreach ($keywords as $keyword) {
                        $q->orWhere('title', 'LIKE', "%{$keyword}%")
                            ->orWhere('description', 'LIKE', "%{$keyword}%");
                    }
                })
                ->limit($limit)
                ->get();

            if ($acts->isEmpty()) {
                $this->completeWithNoResults($session, $logger);
                return;
            }

            $logger->info('[NATAN Job] Acts fetched', [
                'session_id' => $this->sessionId,
                'total_acts' => $acts->count(),
            ]);

            // 3. Chunk acts using intelligent strategy
            $strategy = $session['strategy'] ?? 'token-based';

            $chunks = match ($strategy) {
                'token-based' => $chunkingService->chunkByTokenBudget($acts),
                'relevance-based' => collect([$acts]),
                'adaptive' => $chunkingService->adaptiveChunk($acts),
                default => $chunkingService->chunkByTokenBudget($acts),
            };

            // Update session with actual chunk count
            $session['total_chunks'] = $chunks->count();
            $session['status'] = 'processing';
            Cache::put("natan_chunking_{$this->sessionId}", $session, now()->addHours(2));

            $logger->info('[NATAN Job] Acts chunked', [
                'session_id' => $this->sessionId,
                'num_chunks' => $chunks->count(),
                'strategy' => $strategy,
            ]);

            // 4. Process each chunk sequentially
            $chunkResults = [];
            $allSources = [];

            foreach ($chunks as $chunkIndex => $chunk) {
                $logger->info('[NATAN Job] Processing chunk', [
                    'session_id' => $this->sessionId,
                    'chunk_index' => $chunkIndex,
                    'acts_in_chunk' => $chunk->count(),
                ]);

                // Update cache: chunk started
                $session['current_chunk'] = $chunkIndex;
                $session['chunk_progress'] = 0;
                $session['acts_in_current_chunk'] = $chunk->count();
                $session['last_completed'] = false;
                Cache::put("natan_chunking_{$this->sessionId}", $session, now()->addHours(2));

                // Process chunk with Claude AI (simulated progress updates)
                $chunkResult = $this->processChunkWithClaude(
                    $chunk,
                    $session['query'],
                    $chunkIndex,
                    $chatService,
                    $logger,
                    $errorManager
                );

                $chunkResults[] = $chunkResult;
                $allSources = array_merge($allSources, $chunkResult['sources'] ?? []);

                // Track token usage from chunk
                if (isset($chunkResult['usage'])) {
                    $totalInputTokens += $chunkResult['usage']['input_tokens'] ?? 0;
                    $totalOutputTokens += $chunkResult['usage']['output_tokens'] ?? 0;

                    // Calculate cumulative cost in credits
                    $totalCreditsConsumed = $creditsService->calculateCreditsFromTokens(
                        $totalInputTokens,
                        $totalOutputTokens
                    );

                    $logger->debug('[NATAN Job] Token usage updated', [
                        'session_id' => $this->sessionId,
                        'chunk_index' => $chunkIndex,
                        'chunk_input_tokens' => $chunkResult['usage']['input_tokens'] ?? 0,
                        'chunk_output_tokens' => $chunkResult['usage']['output_tokens'] ?? 0,
                        'cumulative_input_tokens' => $totalInputTokens,
                        'cumulative_output_tokens' => $totalOutputTokens,
                        'cumulative_credits' => $totalCreditsConsumed,
                    ]);
                }

                // Update cache: chunk completed
                $session['completed_chunks'][] = $chunkIndex;
                $session['last_completed'] = true;
                $session['last_completed_index'] = $chunkIndex;
                $session['last_chunk_relevant_acts'] = $chunkResult['relevant_acts'] ?? 0;
                $session['last_chunk_summary'] = $chunkResult['summary'] ?? '';
                $session['total_input_tokens'] = $totalInputTokens;      // NEW: Track in cache
                $session['total_output_tokens'] = $totalOutputTokens;    // NEW: Track in cache
                $session['total_credits_consumed'] = $totalCreditsConsumed; // NEW: Track in cache
                Cache::put("natan_chunking_{$this->sessionId}", $session, now()->addHours(2));

                $logger->info('[NATAN Job] Chunk completed', [
                    'session_id' => $this->sessionId,
                    'chunk_index' => $chunkIndex,
                    'relevant_acts' => $chunkResult['relevant_acts'] ?? 0,
                ]);

                // Small delay between chunks to avoid rate limits
                if ($chunkIndex < $chunks->count() - 1) {
                    sleep(2);
                }
            }

            // 5. Aggregate all chunk results
            $logger->info('[NATAN Job] Starting aggregation', [
                'session_id' => $this->sessionId,
                'chunks_to_aggregate' => count($chunkResults),
            ]);

            $session['status'] = 'aggregating';
            Cache::put("natan_chunking_{$this->sessionId}", $session, now()->addHours(2));

            $aggregationResult = $this->aggregateChunkResults(
                $chunkResults,
                $session['query'],
                $chatService,
                $logger,
                $errorManager
            );

            // Track aggregation token usage
            if (isset($aggregationResult['usage'])) {
                $totalInputTokens += $aggregationResult['usage']['input_tokens'] ?? 0;
                $totalOutputTokens += $aggregationResult['usage']['output_tokens'] ?? 0;

                // Recalculate total credits including aggregation
                $totalCreditsConsumed = $creditsService->calculateCreditsFromTokens(
                    $totalInputTokens,
                    $totalOutputTokens
                );

                $logger->info('[NATAN Job] Aggregation token usage', [
                    'session_id' => $this->sessionId,
                    'aggregation_input_tokens' => $aggregationResult['usage']['input_tokens'] ?? 0,
                    'aggregation_output_tokens' => $aggregationResult['usage']['output_tokens'] ?? 0,
                    'final_total_input_tokens' => $totalInputTokens,
                    'final_total_output_tokens' => $totalOutputTokens,
                    'final_total_credits' => $totalCreditsConsumed,
                ]);
            }

            // 6. Deduct credits from user
            try {
                if ($totalCreditsConsumed > 0 && $user) {
                    $transaction = $creditsService->deductCredits(
                        user: $user,
                        credits: $totalCreditsConsumed,
                        sourceType: 'ai_pa_analysis_chunked',
                        sourceId: null,
                        metadata: [
                            'tokens_consumed' => $totalInputTokens + $totalOutputTokens,
                            'input_tokens' => $totalInputTokens,
                            'output_tokens' => $totalOutputTokens,
                            'ai_model' => 'claude-3-5-sonnet-20241022',
                            'feature_parameters' => [
                                'session_id' => $this->sessionId,
                                'total_acts' => $session['total_acts'] ?? 0,
                                'total_chunks' => $session['total_chunks'] ?? 0,
                                'strategy' => $session['strategy'] ?? 'token-based',
                                'query' => $session['query'] ?? '',
                            ],
                        ]
                    );

                    $creditsTransactionId = $transaction->id;

                    $logger->info('[NATAN Job] Credits deducted successfully', [
                        'session_id' => $this->sessionId,
                        'credits_deducted' => $totalCreditsConsumed,
                        'transaction_id' => $creditsTransactionId,
                    ]);
                }
            } catch (\Exception $e) {
                $logger->error('[NATAN Job] Credits deduction failed', [
                    'session_id' => $this->sessionId,
                    'credits' => $totalCreditsConsumed,
                    'error' => $e->getMessage(),
                ]);

                // Continue anyway - credits issue shouldn't block user from seeing results
                // Admin can fix credits manually if needed
            }

            // 7. Store final response
            $totalTime = now()->diffInSeconds($startTime);

            $session['status'] = 'completed';
            $session['final_response'] = $aggregationResult['response'];
            $session['total_relevant_acts'] = collect($chunkResults)->sum('relevant_acts');
            $session['total_time_seconds'] = $totalTime;
            $session['sources'] = array_slice($allSources, 0, 20); // Limit to 20 sources
            $session['completed_at'] = now()->toISOString();
            $session['total_input_tokens'] = $totalInputTokens;
            $session['total_output_tokens'] = $totalOutputTokens;
            $session['total_credits_consumed'] = $totalCreditsConsumed;
            $session['credits_transaction_id'] = $creditsTransactionId;
            Cache::put("natan_chunking_{$this->sessionId}", $session, now()->addHours(2));

            $logger->info('[NATAN Job] Chunked analysis completed', [
                'session_id' => $this->sessionId,
                'total_time_seconds' => $totalTime,
                'total_relevant_acts' => $session['total_relevant_acts'],
            ]);
        } catch (\Exception $e) {
            $errorManager->handle('NATAN_JOB_ANALYSIS_FAILED', [
                'session_id' => $this->sessionId,
            ], $e);

            // Refund credits if they were deducted
            if ($creditsTransactionId && $totalCreditsConsumed > 0 && isset($user)) {
                try {
                    $creditsService->refundCredits(
                        user: $user,
                        credits: $totalCreditsConsumed,
                        reason: "Chunked analysis failed: {$e->getMessage()}",
                        originalTransactionId: $creditsTransactionId
                    );

                    $logger->info('[NATAN Job] Credits refunded after failure', [
                        'session_id' => $this->sessionId,
                        'credits_refunded' => $totalCreditsConsumed,
                        'original_transaction_id' => $creditsTransactionId,
                    ]);
                } catch (\Exception $refundError) {
                    $logger->error('[NATAN Job] Credits refund failed', [
                        'session_id' => $this->sessionId,
                        'error' => $refundError->getMessage(),
                    ]);
                }
            }

            // Mark session as failed
            $session = Cache::get("natan_chunking_{$this->sessionId}") ?? [];
            $session['status'] = 'failed';
            $session['error'] = $e->getMessage();
            Cache::put("natan_chunking_{$this->sessionId}", $session, now()->addHours(2));

            throw $e; // Re-throw to trigger retry
        }
    }

    /**
     * Process a single chunk with Claude AI
     *
     * @param Collection $chunk
     * @param string $query
     * @param int $chunkIndex
     * @param NatanChatService $chatService
     * @param UltraLogManager $logger
     * @return array
     */
    protected function processChunkWithClaude(
        Collection $chunk,
        string $query,
        int $chunkIndex,
        NatanChatService $chatService,
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ): array {
        // Get user from session
        $session = Cache::get("natan_chunking_{$this->sessionId}") ?? [];
        $user = \App\Models\User::find($session['user_id']);

        if (!$user) {
            $errorManager->handle('NATAN_JOB_USER_NOT_FOUND', [
                'session_id' => $this->sessionId,
                'user_id' => $session['user_id'] ?? null,
            ]);
            throw new \Exception('User not found for chunking session');
        }

        // Update progress: Starting chunk processing
        $session['chunk_progress'] = 10;
        Cache::put("natan_chunking_{$this->sessionId}", $session, now()->addHours(2));

        $logger->info('[NATAN Job] Starting Claude processing for chunk', [
            'session_id' => $this->sessionId,
            'chunk_index' => $chunkIndex,
            'acts_count' => $chunk->count(),
        ]);

        try {
            // Build context array for Claude (format expected by AnthropicService)
            $contextActs = $chunk->map(function ($act) {
                return [
                    'id' => $act->id,
                    'number' => $act->jsonMetadata['pa_act']['number'] ?? 'N/A',
                    'title' => $act->title,
                    'description' => $act->description ?? '',
                    'date' => $act->jsonMetadata['pa_act']['date'] ?? null,
                    'category' => $act->jsonMetadata['pa_act']['category'] ?? null,
                    'url' => $act->jsonMetadata['pa_act']['url'] ?? null,
                ];
            })->toArray();

            // Progress update: Context built
            $session['chunk_progress'] = 25;
            Cache::put("natan_chunking_{$this->sessionId}", $session, now()->addHours(2));

            // Craft specific query for this chunk analysis
            $chunkQuery = "Analizza i seguenti atti amministrativi in relazione alla query: \"{$query}\"\n\n";
            $chunkQuery .= "Identifica gli atti più rilevanti e fornisci un'analisi sintetica.\n";
            $chunkQuery .= "Questo è il chunk {$chunkIndex} di un'analisi più ampia.\n";
            $chunkQuery .= "Concentrati su: pertinenza, temi principali, implicazioni strategiche.\n";

            // Progress update: Calling Claude
            $session['chunk_progress'] = 40;
            Cache::put("natan_chunking_{$this->sessionId}", $session, now()->addHours(2));

            // Call Claude via NatanChatService with RAG disabled (we provide context directly)
            $result = $chatService->processQuery(
                userQuery: $chunkQuery,
                user: $user,
                conversationHistory: [], // Each chunk is independent
                manualPersonaId: 'strategic', // Always use strategic persona for PA acts analysis
                sessionId: $this->sessionId . "_chunk_{$chunkIndex}",
                useRag: false, // We're providing the acts directly as context
                useWebSearch: false, // No web search for chunk processing
                referenceContext: ['acts' => $contextActs] // Pass acts as reference context
            );

            // Progress update: Claude responded
            $session['chunk_progress'] = 90;
            Cache::put("natan_chunking_{$this->sessionId}", $session, now()->addHours(2));

            $logger->info('[NATAN Job] Claude processing completed for chunk', [
                'session_id' => $this->sessionId,
                'chunk_index' => $chunkIndex,
                'response_length' => strlen($result['response'] ?? ''),
                'success' => $result['success'] ?? false,
            ]);

            // Extract relevant acts from sources
            $relevantActs = collect($result['sources'] ?? [])->filter(function ($source) {
                return ($source['relevance_score'] ?? 0) >= 0.5; // Filter by relevance
            });

            // Final progress update
            $session['chunk_progress'] = 100;
            Cache::put("natan_chunking_{$this->sessionId}", $session, now()->addHours(2));

            return [
                'chunk_index' => $chunkIndex,
                'relevant_acts' => $relevantActs->count(),
                'summary' => $result['response'] ?? "Chunk {$chunkIndex} processed",
                'sources' => $relevantActs->values()->toArray(),
                'partial_response' => $result['response'] ?? '',
                'usage' => $result['usage'] ?? null, // Claude token usage for cost tracking
            ];
        } catch (\Exception $e) {
            $logger->error('[NATAN Job] Error processing chunk with Claude', [
                'session_id' => $this->sessionId,
                'chunk_index' => $chunkIndex,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Mark chunk as failed but continue
            $session['chunk_progress'] = 100; // Mark as complete even if failed
            Cache::put("natan_chunking_{$this->sessionId}", $session, now()->addHours(2));

            return [
                'chunk_index' => $chunkIndex,
                'relevant_acts' => 0,
                'summary' => "Errore nel processamento del chunk {$chunkIndex}: " . $e->getMessage(),
                'sources' => [],
                'partial_response' => "⚠️ Errore nel chunk {$chunkIndex}",
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Aggregate chunk results into final Claude synthesis
     *
     * @param array $chunkResults
     * @param string $query
     * @param NatanChatService $chatService
     * @param UltraLogManager $logger
     * @param ErrorManagerInterface $errorManager
     * @return array ['response' => string, 'usage' => array]
     */
    protected function aggregateChunkResults(
        array $chunkResults,
        string $query,
        NatanChatService $chatService,
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ): array {
        $session = Cache::get("natan_chunking_{$this->sessionId}") ?? [];
        $user = \App\Models\User::find($session['user_id']);

        if (!$user) {
            $errorManager->handle('NATAN_JOB_USER_NOT_FOUND', [
                'session_id' => $this->sessionId,
            ]);
            throw new \Exception('User not found for aggregation');
        }

        $logger->info('[NATAN Job] Starting Claude aggregation', [
            'session_id' => $this->sessionId,
            'chunks_count' => count($chunkResults),
        ]);

        try {
            // Build aggregation context from chunk summaries
            $chunkSummaries = collect($chunkResults)->map(function ($result, $index) {
                return "### Chunk {$index}\n" .
                    "Atti rilevanti: {$result['relevant_acts']}\n" .
                    "Analisi: {$result['partial_response']}\n";
            })->implode("\n\n");

            $totalRelevant = collect($chunkResults)->sum('relevant_acts');
            $totalActs = collect($chunkResults)->sum(function ($result) {
                return count($result['sources'] ?? []);
            });

            // Craft aggregation prompt for Claude
            $aggregationQuery = "Sei un consulente strategico per Pubbliche Amministrazioni.\n\n";
            $aggregationQuery .= "**CONTESTO:**\n";
            $aggregationQuery .= "L'utente ha richiesto un'analisi su: \"{$query}\"\n";
            $aggregationQuery .= "Il dataset è stato analizzato in {count($chunkResults)} chunk sequenziali per gestire il volume.\n";
            $aggregationQuery .= "Totale atti analizzati: {$totalActs}\n";
            $aggregationQuery .= "Atti rilevanti identificati: {$totalRelevant}\n\n";
            $aggregationQuery .= "**RISULTATI PARZIALI DAI CHUNK:**\n\n";
            $aggregationQuery .= $chunkSummaries;
            $aggregationQuery .= "\n\n**COMPITO:**\n";
            $aggregationQuery .= "Aggrega questi risultati parziali in una risposta COERENTE, SINTETICA e STRATEGICA.\n";
            $aggregationQuery .= "Evidenzia:\n";
            $aggregationQuery .= "1. Pattern e tendenze principali\n";
            $aggregationQuery .= "2. Atti più rilevanti (max 5-7)\n";
            $aggregationQuery .= "3. Raccomandazioni operative per la PA\n";
            $aggregationQuery .= "4. Eventuali gap o criticità emerse\n\n";
            $aggregationQuery .= "Mantieni un tono professionale, accessibile e orientato all'azione.\n";
            $aggregationQuery .= "La risposta deve essere self-contained (non menzionare 'chunk' o processi tecnici).";

            // Call Claude for aggregation
            $result = $chatService->processQuery(
                userQuery: $aggregationQuery,
                user: $user,
                conversationHistory: [],
                manualPersonaId: 'strategic',
                sessionId: $this->sessionId . "_aggregation",
                useRag: false,
                useWebSearch: false,
                referenceContext: null
            );

            $logger->info('[NATAN Job] Claude aggregation completed', [
                'session_id' => $this->sessionId,
                'response_length' => strlen($result['response'] ?? ''),
                'success' => $result['success'] ?? false,
                'input_tokens' => $result['usage']['input_tokens'] ?? 0,
                'output_tokens' => $result['usage']['output_tokens'] ?? 0,
            ]);

            return [
                'response' => $result['response'] ?? $this->buildFallbackAggregation($chunkResults, $query, $totalRelevant),
                'usage' => $result['usage'] ?? [],
            ];
        } catch (\Exception $e) {
            $logger->error('[NATAN Job] Error during Claude aggregation', [
                'session_id' => $this->sessionId,
                'error' => $e->getMessage(),
            ]);

            // Fallback to simple aggregation (no Claude usage)
            return [
                'response' => $this->buildFallbackAggregation($chunkResults, $query, collect($chunkResults)->sum('relevant_acts')),
                'usage' => [], // No tokens consumed in fallback
            ];
        }
    }

    /**
     * Build fallback aggregation if Claude fails
     *
     * @param array $chunkResults
     * @param string $query
     * @param int $totalRelevant
     * @return string
     */
    protected function buildFallbackAggregation(array $chunkResults, string $query, int $totalRelevant): string {
        $summaries = collect($chunkResults)->pluck('partial_response')->implode("\n\n---\n\n");

        return "**Analisi completata su {$totalRelevant} atti rilevanti:**\n\n" .
            "Ho analizzato il dataset completo in relazione alla query: \"{$query}\"\n\n" .
            "**Risultati per chunk:**\n\n" .
            $summaries .
            "\n\n**Nota:** Questa è un'aggregazione semplificata. " .
            "L'analisi dettagliata è disponibile nei risultati parziali sopra.";
    }

    /**
     * Complete job with no results found
     *
     * @param array $session
     * @param UltraLogManager $logger
     * @return void
     */
    protected function completeWithNoResults(array $session, UltraLogManager $logger): void {
        $session['status'] = 'completed';
        $session['final_response'] = 'Nessun atto trovato corrispondente alla ricerca.';
        $session['total_relevant_acts'] = 0;
        $session['total_time_seconds'] = 0;
        $session['sources'] = [];
        $session['completed_at'] = now()->toISOString();
        Cache::put("natan_chunking_{$this->sessionId}", $session, now()->addHours(2));

        $logger->info('[NATAN Job] Completed with no results', [
            'session_id' => $this->sessionId,
        ]);
    }

    /**
     * Handle a job failure.
     *
     * @param \Throwable $exception
     * @return void
     */
    public function failed(\Throwable $exception): void {
        // Mark session as failed in cache
        $session = Cache::get("natan_chunking_{$this->sessionId}") ?? [];
        $session['status'] = 'failed';
        $session['error'] = $exception->getMessage();
        Cache::put("natan_chunking_{$this->sessionId}", $session, now()->addHours(2));

        app(UltraLogManager::class)->error('[NATAN Job] Job failed permanently', [
            'session_id' => $this->sessionId,
            'error' => $exception->getMessage(),
        ]);
    }
}
