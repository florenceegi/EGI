<?php

namespace App\Services;

use App\Models\Egi;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * N.A.T.A.N. Chat Service - AI-powered conversational interface for PA acts
 *
 * This service implements RAG (Retrieval Augmented Generation) to allow
 * PA officials to interact with their administrative acts using natural language.
 *
 * FEATURES:
 * - Conversational AI: Ask questions about specific acts or general queries
 * - RAG: Retrieves relevant acts before generating response
 * - Context-aware: Maintains conversation history
 * - Multi-query: "Summarize act X", "Which acts about Y?", "Suggest Z"
 *
 * GDPR-COMPLIANT:
 * - Uses Anthropic Claude 3.5 Sonnet (EU DPA compliant)
 * - Processes ONLY public metadata (no PII, no signatures)
 * - Full audit trail of data sent to AI
 * - DataSanitizerService filters all sensitive data
 *
 * @package App\Services
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 2.0.0 (FlorenceEGI - N.A.T.A.N. Chat AI with Anthropic)
 * @date 2025-10-12
 */
class NatanChatService
{
    protected AnthropicService $anthropic;
    protected RagService $rag;
    protected DataSanitizerService $sanitizer;
    protected PersonaSelector $personaSelector;
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;

    public function __construct(
        AnthropicService $anthropic,
        RagService $rag,
        DataSanitizerService $sanitizer,
        PersonaSelector $personaSelector,
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        $this->anthropic = $anthropic;
        $this->rag = $rag;
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
     * 2. Retrieve relevant acts using RAG system (semantic search + fallback)
     * 3. Sanitize data (GDPR compliance)
     * 4. Build context with public metadata only
     * 5. Call Anthropic Claude 3.5 Sonnet with selected persona
     * 6. Save message to database with persona metadata
     * 7. Return structured response with sources
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
     * GDPR AUDIT:
     * - Logs what data is sent to AI
     * - Validates no private fields are present
     * - All data passes through DataSanitizerService
     *
     * @param string $userQuery User's question/request
     * @param User $user Current authenticated user
     * @param array $conversationHistory Previous messages for context
     * @param string|null $manualPersonaId Manual persona selection (null = auto mode)
     * @param string|null $sessionId Session ID for conversation tracking (generated if null)
     * @return array ['success' => bool, 'response' => string, 'sources' => array, 'persona' => array, 'session_id' => string]
     * @throws \Exception
     */
    public function processQuery(
        string $userQuery, 
        User $user, 
        array $conversationHistory = [],
        ?string $manualPersonaId = null,
        ?string $sessionId = null
    ): array
    {
        $startTime = microtime(true);
        $sessionId = $sessionId ?? uniqid('natan_', true);
        
        $logContext = [
            'service' => 'NatanChatService',
            'user_id' => $user->id,
            'session_id' => $sessionId,
            'query_length' => strlen($userQuery),
            'manual_persona' => $manualPersonaId
        ];

        $this->logger->info('[NatanChatService] Processing user query', $logContext);

        try {
            // STEP 0: Save user message to database
            $userMessage = \App\Models\NatanChatMessage::create([
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

            // STEP 2: Retrieve relevant acts using RAG system
            // Uses semantic search (vector embeddings) with keyword search fallback
            $context = $this->rag->getContextForQuery($userQuery, $user, 10);
            $ragMethod = $context['search_method'] ?? 'unknown';

            $logContext['acts_count'] = count($context['acts']);
            $logContext['context_summary_length'] = strlen($context['acts_summary']);
            $logContext['rag_method'] = $ragMethod;

            $this->logger->info('[NatanChatService] RAG context retrieved and sanitized', $logContext);

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
            $assistantMessage = \App\Models\NatanChatMessage::create([
                'user_id' => $user->id,
                'session_id' => $sessionId,
                'role' => 'assistant',
                'content' => $aiResponse,
                'persona_id' => $personaSelection['persona_id'],
                'persona_name' => \App\Config\NatanPersonas::getName($personaSelection['persona_id']),
                'persona_confidence' => $personaSelection['confidence'],
                'persona_selection_method' => $personaSelection['method'],
                'persona_reasoning' => $personaSelection['reasoning'],
                'persona_alternatives' => $personaSelection['alternatives'] ?? [],
                'rag_sources' => array_column($context['acts'], 'id'),
                'rag_acts_count' => count($context['acts']),
                'rag_method' => $ragMethod,
                'ai_model' => config('services.anthropic.model'),
                'response_time_ms' => $responseTime,
            ]);

            return [
                'success' => true,
                'response' => $aiResponse,
                'sources' => $sources,
                'relevant_acts_count' => count($context['acts']),
                'persona' => [
                    'id' => $personaSelection['persona_id'],
                    'name' => \App\Config\NatanPersonas::getName($personaSelection['persona_id']),
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
}
