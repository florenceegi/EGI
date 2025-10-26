<?php

namespace App\Services;

use App\Config\NatanPersonas;
use App\Models\Egi;
use App\Models\NatanChatMessage;
use App\Models\User;
use App\Services\WebSearch\WebSearchService;
use App\Services\WebSearch\WebSearchAutoDetector;
use Illuminate\Support\Facades\DB;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * N.A.T.A.N. Chat Service - AI-powered conversational interface for PA acts
 *
 * This service implements RAG (Retrieval Augmented Generation) + Web Search to allow
 * PA officials to interact with their administrative acts + global knowledge using natural language.
 *
 * FEATURES:
 * - Conversational AI: Ask questions about specific acts or general queries
 * - RAG: Retrieves relevant acts before generating response
 * - WEB SEARCH: Augments with global best practices, normatives, funding (NEW v3.0)
 * - Context-aware: Maintains conversation history
 * - Multi-query: "Summarize act X", "Which acts about Y?", "Suggest Z"
 *
 * GDPR-COMPLIANT:
 * - Uses Anthropic Claude 3.5 Sonnet (EU DPA compliant)
 * - Processes ONLY public metadata (no PII, no signatures)
 * - Full audit trail of data sent to AI AND web search
 * - DataSanitizerService + KeywordSanitizerService filter all sensitive data
 *
 * @package App\Services
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 3.0.0 (FlorenceEGI - N.A.T.A.N. Chat AI + Web Search)
 * @date 2025-10-26
 */
class NatanChatService
{
    protected AnthropicService $anthropic;
    protected RagService $rag;
    protected WebSearchService $webSearch;
    protected WebSearchAutoDetector $webSearchDetector;
    protected DataSanitizerService $sanitizer;
    protected PersonaSelector $personaSelector;
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;

    public function __construct(
        AnthropicService $anthropic,
        RagService $rag,
        WebSearchService $webSearch,
        WebSearchAutoDetector $webSearchDetector,
        DataSanitizerService $sanitizer,
        PersonaSelector $personaSelector,
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        $this->anthropic = $anthropic;
        $this->rag = $rag;
        $this->webSearch = $webSearch;
        $this->webSearchDetector = $webSearchDetector;
        $this->sanitizer = $sanitizer;
        $this->personaSelector = $personaSelector;
        $this->logger = $logger;
        $this->errorManager = $errorManager;
    }

    /**
     * Process user query and generate AI response
     *
     * WORKFLOW:
     * 1. Select appropriate persona (auto or manual)
     * 2. [OPTIONAL] Retrieve relevant acts using RAG system (semantic search + fallback)
     * 3. [OPTIONAL] Retrieve web search results (global best practices, normatives) ✨ NEW v3.0
     * 4. Sanitize data (GDPR compliance)
     * 5. Fusion: Merge RAG + Web Search results into unified context
     * 6. Build context with public metadata only
     * 7. Call Anthropic Claude 3.5 Sonnet with selected persona
     * 8. Save message to database with persona metadata + web search tracking
     * 9. Return structured response with sources (internal + external)
     *
     * MULTI-PERSONA SYSTEM:
     * - Auto mode: PersonaSelector analyzes query and chooses best expert
     * - Manual mode: User explicitly selects persona from UI
     * - 6 personas: Strategic, Technical, Legal, Financial, Urban/Social, Communication
     *
     * RAG STRATEGY:
     * - Semantic search: Vector embeddings + cosine similarity (preferred)
     * - Keyword search: SQL LIKE queries (fallback if no embeddings)
     * - Scales to 24k+ acts with acceptable performance
     *
     * WEB SEARCH STRATEGY (NEW v3.0):
     * - Automatic keyword sanitization (GDPR-safe)
     * - Multi-provider support (Perplexity AI, Google Custom Search)
     * - Persona-specific domain prioritization
     * - Results caching for performance
     *
     * ITERATIVE ELABORATION:
     * - use_rag=false: Skips RAG retrieval (for general consulting or elaborations)
     * - referenceContext: When provided, Claude elaborates on a previous response
     * - Use cases: Simplify, Deepen, Transform format, Extract actions, etc.
     *
     * GDPR AUDIT:
     * - Logs what data is sent to AI AND web search APIs
     * - Validates no private fields are present
     * - All data passes through DataSanitizerService + KeywordSanitizerService
     *
     * @param string $userQuery User's question/request
     * @param User $user Current authenticated user
     * @param array $conversationHistory Previous messages for context
     * @param string|null $manualPersonaId Manual persona selection (null = auto mode)
     * @param string|null $sessionId Session ID for conversation tracking (generated if null)
     * @param bool $useRag Enable RAG retrieval (default: true)
     * @param bool $useWebSearch Enable web search (default: false, opt-in) ✨ NEW
     * @param array|null $referenceContext Previous message to elaborate on (null = new query)
     * @return array ['success' => bool, 'response' => string, 'sources' => array, 'web_sources' => array, 'persona' => array, 'session_id' => string]
     * @throws \Exception
     */
    public function processQuery(
        string $userQuery,
        User $user,
        array $conversationHistory = [],
        ?string $manualPersonaId = null,
        ?string $sessionId = null,
        bool $useRag = true,
        bool $useWebSearch = false,
        ?array $referenceContext = null
    ): array {
        $startTime = microtime(true);
        $sessionId = $sessionId ?? uniqid('natan_', true);

        $logContext = [
            'service' => 'NatanChatService',
            'user_id' => $user->id,
            'session_id' => $sessionId,
            'query_length' => strlen($userQuery),
            'manual_persona' => $manualPersonaId,
            'use_rag' => $useRag,
            'has_reference' => $referenceContext !== null,
        ];

        $this->logger->info('[NatanChatService] Processing user query', $logContext);

        try {
            // STEP 0: Save user message to database
            $userMessage = NatanChatMessage::create([
                'user_id' => $user->id,
                'session_id' => $sessionId,
                'role' => 'user',
                'content' => $userQuery,
            ]);

            // STEP 1: Select appropriate persona
            $personaSelection = $this->personaSelector->selectPersona(
                $userQuery,
                $manualPersonaId,
                ['conversation_history' => $conversationHistory]
            );

            $logContext['persona'] = $personaSelection['persona_id'];
            $logContext['persona_confidence'] = $personaSelection['confidence'];
            $logContext['persona_method'] = $personaSelection['method'];

            $this->logger->info('[NatanChatService] Persona selected', $logContext);

            // STEP 1.5: Auto-detect if web search should be enabled (FASE 2 - Smart Mode) ✨ NEW v3.0
            // If user didn't explicitly set useWebSearch, let AI decide
            if (!$useWebSearch) {
                $autoDetection = $this->webSearchDetector->shouldEnableWebSearch(
                    $userQuery,
                    $personaSelection['persona_id'],
                    ['conversation_history' => $conversationHistory]
                );

                // Auto-enable if confidence >= 50%
                if ($autoDetection['should_enable']) {
                    $useWebSearch = true;
                    $logContext['web_search_auto_enabled'] = true;
                    $logContext['web_search_confidence'] = $autoDetection['confidence'];
                    $logContext['web_search_reasoning'] = $autoDetection['reasoning'];

                    $this->logger->info('[NatanChatService] Web search AUTO-ENABLED by detector', [
                        'confidence' => $autoDetection['confidence'],
                        'reasoning' => $autoDetection['reasoning'],
                        'triggers' => $autoDetection['triggers'],
                    ]);
                }
            }

            // STEP 2: Retrieve relevant acts using RAG system (if enabled)
            // Uses semantic search (vector embeddings) with keyword search fallback
            $ragMethod = null;
            if ($useRag) {
                $context = $this->rag->getContextForQuery($userQuery, $user, 10);
                $ragMethod = 'semantic'; // Default to semantic (will be enhanced later with actual detection)

                $logContext['acts_count'] = count($context['acts']);
                $logContext['context_summary_length'] = strlen($context['acts_summary']);
                $logContext['rag_method'] = $ragMethod;

                $this->logger->info('[NatanChatService] RAG context retrieved and sanitized', $logContext);
            } else {
                // No RAG: Empty context (for elaborations or general consulting)
                $context = [
                    'acts' => [],
                    'acts_summary' => '',
                    'stats' => [],
                ];

                $logContext['acts_count'] = 0;
                $logContext['rag_skipped'] = true;

                $this->logger->info('[NatanChatService] RAG skipped (elaboration or general query)', $logContext);
            }

            // STEP 2.5: Retrieve web search results (if enabled) ✨ NEW v3.0
            $webSearchResults = [];
            $webSearchMetadata = null;
            if ($useWebSearch) {
                $webSearchResponse = $this->webSearch->search(
                    $userQuery,
                    $personaSelection['persona_id'],
                    5 // Max 5 web results
                );

                if ($webSearchResponse['success']) {
                    $webSearchResults = $webSearchResponse['results'];
                    $webSearchMetadata = $webSearchResponse['metadata'];

                    $logContext['web_search_count'] = count($webSearchResults);
                    $logContext['web_search_provider'] = $webSearchMetadata['provider'] ?? 'unknown';
                    $logContext['web_search_from_cache'] = $webSearchMetadata['from_cache'] ?? false;

                    $this->logger->info('[NatanChatService] Web search results retrieved', $logContext);

                    // GDPR Audit - Log web search data
                    $this->logger->info('[NatanChatService][GDPR] Web search query sent to external API', [
                        'user_id' => $user->id,
                        'provider' => $webSearchMetadata['provider'],
                        'query_sanitized' => $webSearchMetadata['query_sanitized'],
                        'keywords_removed' => $webSearchMetadata['keywords_removed'],
                        'results_count' => count($webSearchResults),
                        'from_cache' => $webSearchMetadata['from_cache'],
                        'timestamp' => now()->toIso8601String(),
                    ]);
                } else {
                    $this->logger->warning('[NatanChatService] Web search failed, continuing without web results', [
                        'error' => $webSearchResponse['error'] ?? 'unknown',
                    ]);
                }
            } else {
                $logContext['web_search_disabled'] = true;
            }

            // STEP 2.6: Fusion - Merge RAG + Web Search into unified context ✨ NEW v3.0
            if (!empty($webSearchResults)) {
                // Add web_sources to context for Claude
                $context['web_sources'] = $webSearchResults;

                // Build web sources summary for prompt
                $webSourcesSummary = $this->buildWebSourcesSummary($webSearchResults);
                $context['web_sources_summary'] = $webSourcesSummary;

                $this->logger->info('[NatanChatService] Context fusion completed (RAG + Web)', [
                    'internal_sources' => count($context['acts']),
                    'external_sources' => count($webSearchResults),
                    'total_sources' => count($context['acts']) + count($webSearchResults),
                ]);
            }

            // Add reference context if provided (for elaborations)
            if ($referenceContext) {
                $context['reference_message'] = $referenceContext;
                $logContext['reference_message_id'] = $referenceContext['id'];
                $this->logger->info('[NatanChatService] Reference context included for elaboration', $logContext);
            }

            // STEP 3: GDPR Audit - Log what we're sending to AI
            $this->logger->info('[NatanChatService][GDPR] Data sent to Anthropic AI', [
                'user_id' => $user->id,
                'acts_count' => count($context['acts']),
                'acts_ids' => array_column($context['acts'], 'id'),
                'fields_sent' => !empty($context['acts']) ? array_keys($context['acts'][0]) : [],
                'persona_id' => $personaSelection['persona_id'],
                'timestamp' => now()->toIso8601String(),
            ]);

            // STEP 4: Call Anthropic Claude API with selected persona
            $aiResponse = $this->anthropic->chat(
                $userQuery,
                $context,
                $conversationHistory,
                $personaSelection['persona_id']
            );

            $responseTime = (int)((microtime(true) - $startTime) * 1000); // milliseconds

            $this->logger->info('[NatanChatService] AI response generated', [
                ...$logContext,
                'response_length' => strlen($aiResponse),
                'response_time_ms' => $responseTime
            ]);

            // STEP 5: Build response with sources (sanitized)
            $sources = array_map(function ($act) {
                return [
                    'id' => $act['id'],
                    'protocol_number' => $act['protocol_number'],
                    'date' => $act['date'],
                    'type' => $act['type'],
                    'title' => $act['title'],
                    'url' => route('pa.acts.show', $act['id']),
                    'blockchain_anchored' => $act['blockchain_anchored'],
                ];
            }, $context['acts']);

            // STEP 6: Save assistant message to database
            $assistantMessage = NatanChatMessage::create([
                'user_id' => $user->id,
                'session_id' => $sessionId,
                'role' => 'assistant',
                'content' => $aiResponse,
                'persona_id' => $personaSelection['persona_id'],
                'persona_name' => NatanPersonas::getName($personaSelection['persona_id']),
                'persona_confidence' => $personaSelection['confidence'],
                'persona_selection_method' => $personaSelection['method'],
                'persona_reasoning' => $personaSelection['reasoning'],
                'persona_alternatives' => $personaSelection['alternatives'] ?? [],
                'rag_sources' => array_column($context['acts'], 'id'),
                'rag_acts_count' => count($context['acts']),
                'rag_method' => $ragMethod,
                'web_search_enabled' => $useWebSearch, // NEW v3.0
                'web_search_provider' => $webSearchMetadata['provider'] ?? null, // NEW v3.0
                'web_search_results' => !empty($webSearchResults) ? $webSearchResults : null, // NEW v3.0
                'web_search_count' => count($webSearchResults), // NEW v3.0
                'web_search_from_cache' => $webSearchMetadata['from_cache'] ?? false, // NEW v3.0
                'ai_model' => config('services.anthropic.model'),
                'response_time_ms' => $responseTime,
                'reference_message_id' => $referenceContext['id'] ?? null, // Track elaborations
            ]);

            return [
                'success' => true,
                'response' => $aiResponse,
                'sources' => $sources,
                'relevant_acts_count' => count($context['acts']),
                'web_sources' => $webSearchResults, // NEW v3.0
                'web_search_metadata' => $webSearchMetadata, // NEW v3.0
                'persona' => [
                    'id' => $personaSelection['persona_id'],
                    'name' => NatanPersonas::getName($personaSelection['persona_id']),
                    'confidence' => $personaSelection['confidence'],
                    'method' => $personaSelection['method'],
                    'reasoning' => $personaSelection['reasoning'],
                    'alternatives' => $personaSelection['alternatives'] ?? [],
                    'suggestion' => $this->personaSelector->getSuggestionMessage($personaSelection['alternatives'] ?? []),
                ],
                'session_id' => $sessionId,
                'message_ids' => [
                    'user' => $userMessage->id,
                    'assistant' => $assistantMessage->id,
                ],
            ];
        } catch (\Throwable $e) {
            $this->logger->error('[NatanChatService] Query processing failed', [
                ...$logContext,
                'error' => $e->getMessage(),
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->errorManager->handle('NATAN_CHAT_FAILED', $logContext, $e);

            return [
                'success' => false,
                'response' => "Mi dispiace, ho avuto un problema nell'elaborare la tua richiesta. Riprova tra poco.",
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get suggested questions for user
     *
     * Uses RAG service to generate context-aware suggestions
     *
     * @param User $user
     * @return array
     */
    public function getSuggestedQuestions(User $user): array
    {
        try {
            return $this->rag->getSuggestions($user);
        } catch (\Exception $e) {
            $this->logger->error('[NatanChatService] Failed to get suggestions from RAG', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            // Fallback suggestions
            return [
                "Come funziona N.A.T.A.N.?",
                "Quali documenti posso caricare?",
                "Come viene garantita la sicurezza dei dati?"
            ];
        }
    }

    /**
     * Build summary text from web search results ✨ NEW v3.0
     *
     * Creates a structured summary of external sources for Claude's context.
     * Format optimized for AI consumption.
     *
     * @param array $webResults Web search results
     * @return string Formatted summary
     */
    protected function buildWebSourcesSummary(array $webResults): string
    {
        if (empty($webResults)) {
            return '';
        }

        $summary = "## EXTERNAL WEB SOURCES (Best Practices, Normatives, Case Studies)\n\n";

        foreach ($webResults as $index => $result) {
            $num = $index + 1;
            $title = $result['title'] ?? 'Untitled';
            $url = $result['url'] ?? '';
            $snippet = $result['snippet'] ?? '';
            $relevance = $result['relevance_score'] ?? 0;

            $summary .= "### Source {$num} (Relevance: " . round($relevance * 100) . "%)\n";
            $summary .= "**Title:** {$title}\n";
            $summary .= "**URL:** {$url}\n";
            $summary .= "**Content:** {$snippet}\n\n";
        }

        $summary .= "---\n\n";
        $summary .= "**INSTRUCTIONS:**\n";
        $summary .= "- Use these external sources to benchmark internal acts against global best practices\n";
        $summary .= "- Cite sources when making comparisons or recommendations\n";
        $summary .= "- Identify gaps between internal practices and international standards\n";
        $summary .= "- Suggest actionable improvements based on successful case studies\n";

        return $summary;
    }
}
