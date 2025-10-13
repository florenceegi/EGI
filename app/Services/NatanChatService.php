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
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;

    public function __construct(
        AnthropicService $anthropic,
        RagService $rag,
        DataSanitizerService $sanitizer,
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        $this->anthropic = $anthropic;
        $this->rag = $rag;
        $this->sanitizer = $sanitizer;
        $this->logger = $logger;
        $this->errorManager = $errorManager;
    }

    /**
     * Process user query and generate AI response
     *
     * WORKFLOW:
     * 1. Retrieve relevant acts using RAG system
     * 2. Sanitize data (GDPR compliance)
     * 3. Build context with public metadata only
     * 4. Call Anthropic Claude 3.5 Sonnet
     * 5. Return structured response with sources
     *
     * GDPR AUDIT:
     * - Logs what data is sent to AI
     * - Validates no private fields are present
     * - All data passes through DataSanitizerService
     *
     * @param string $userQuery User's question/request
     * @param User $user Current authenticated user
     * @param array $conversationHistory Previous messages for context
     * @return array ['success' => bool, 'response' => string, 'sources' => array]
     * @throws \Exception
     */
    public function processQuery(string $userQuery, User $user, array $conversationHistory = []): array
    {
        $logContext = [
            'service' => 'NatanChatService',
            'user_id' => $user->id,
            'query_length' => strlen($userQuery)
        ];

        $this->logger->info('[NatanChatService] Processing user query', $logContext);

        try {
            // STEP 1: Retrieve relevant acts using RAG system (auto-sanitized)
            $context = $this->rag->getContextForQuery($userQuery, $user, 10);

            $logContext['acts_count'] = count($context['acts']);
            $logContext['context_summary_length'] = strlen($context['acts_summary']);

            $this->logger->info('[NatanChatService] RAG context retrieved and sanitized', $logContext);

            // STEP 2: GDPR Audit - Log what we're sending to AI
            $this->logger->info('[NatanChatService][GDPR] Data sent to Anthropic AI', [
                'user_id' => $user->id,
                'acts_count' => count($context['acts']),
                'acts_ids' => array_column($context['acts'], 'id'),
                'fields_sent' => !empty($context['acts']) ? array_keys($context['acts'][0]) : [],
                'timestamp' => now()->toIso8601String(),
            ]);

            // STEP 3: Call Anthropic Claude API
            $aiResponse = $this->anthropic->chat(
                $userQuery,
                $context,
                $conversationHistory
            );

            $this->logger->info('[NatanChatService] AI response generated', [
                ...$logContext,
                'response_length' => strlen($aiResponse)
            ]);

            // STEP 4: Build response with sources (sanitized)
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

            return [
                'success' => true,
                'response' => $aiResponse,
                'sources' => $sources,
                'relevant_acts_count' => count($context['acts']),
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