<?php

namespace App\Http\Controllers\PA;

use App\Http\Controllers\Controller;
use App\Services\NatanChatService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * N.A.T.A.N. Chat Controller
 *
 * Provides REST API for AI-powered conversational interface with PA acts.
 *
 * ENDPOINTS:
 * - GET  /pa/natan/chat         → Show chat interface
 * - POST /pa/natan/chat/message → Process user message and return AI response
 * - GET  /pa/natan/chat/suggestions → Get suggested questions
 *
 * @package App\Http\Controllers\PA
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - N.A.T.A.N. Chat AI)
 * @date 2025-10-10
 */
class NatanChatController extends Controller
{
    protected NatanChatService $chatService;
    protected UltraLogManager $logger;

    public function __construct(
        NatanChatService $chatService,
        UltraLogManager $logger
    ) {
        $this->chatService = $chatService;
        $this->logger = $logger;
    }

    /**
     * Show N.A.T.A.N. chat interface
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = auth()->user();

        // Get suggested questions
        $suggestedQuestions = $this->chatService->getSuggestedQuestions($user);

        return view('pa.natan.chat', [
            'suggested_questions' => $suggestedQuestions
        ]);
    }

    /**
     * Process user message and return AI response
     *
     * POST /pa/natan/chat/message
     *
     * REQUEST BODY:
     * {
     *   "message": "Riassumi l'ultimo atto caricato",
     *   "conversation_history": [
     *     {"role": "user", "content": "..."},
     *     {"role": "assistant", "content": "..."}
     *   ]
     * }
     *
     * RESPONSE:
     * {
     *   "success": true,
     *   "response": "Ecco il riassunto dell'ultimo atto...",
     *   "sources": [
     *     {"id": 123, "protocol_number": "12345/2025", "title": "...", "url": "..."}
     *   ]
     * }
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $logContext = [
            'controller' => static::class,
            'user_id' => auth()->id(),
            'method' => __FUNCTION__
        ];

        try {
            // Validate input
            $validated = $request->validate([
                'message' => ['required', 'string', 'min:3', 'max:1000'],
                'conversation_history' => ['nullable', 'array', 'max:10'] // Max 10 previous messages
            ]);

            $user = auth()->user();
            $message = $validated['message'];
            $history = $validated['conversation_history'] ?? [];

            $this->logger->info('[NatanChatController] Processing user message', [
                ...$logContext,
                'message_length' => strlen($message),
                'history_length' => count($history)
            ]);

            // Process query with N.A.T.A.N. service
            $result = $this->chatService->processQuery($message, $user, $history);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'error' => $result['error'] ?? 'Errore sconosciuto',
                    'message' => $result['response']
                ], 500);
            }

            $this->logger->info('[NatanChatController] AI response generated successfully', [
                ...$logContext,
                'response_length' => strlen($result['response']),
                'sources_count' => count($result['sources'] ?? [])
            ]);

            return response()->json([
                'success' => true,
                'response' => $result['response'],
                'sources' => $result['sources'] ?? [],
                'timestamp' => now()->toIso8601String()
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'validation_error',
                'message' => 'Dati non validi',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            $this->logger->error('[NatanChatController] Message processing failed', [
                ...$logContext,
                'error' => $e->getMessage(),
                'exception' => get_class($e)
            ]);

            return response()->json([
                'success' => false,
                'error' => 'server_error',
                'message' => 'Si è verificato un errore. Riprova tra poco.'
            ], 500);
        }
    }

    /**
     * Get suggested questions for user
     *
     * GET /pa/natan/chat/suggestions
     *
     * RESPONSE:
     * {
     *   "success": true,
     *   "suggestions": [
     *     "Riassumi l'ultimo atto caricato",
     *     "Quali atti riguardano lavori pubblici?",
     *     ...
     *   ]
     * }
     *
     * @return JsonResponse
     */
    public function getSuggestions(): JsonResponse
    {
        try {
            $user = auth()->user();
            $suggestions = $this->chatService->getSuggestedQuestions($user);

            return response()->json([
                'success' => true,
                'suggestions' => $suggestions
            ]);

        } catch (\Exception $e) {
            $this->logger->error('[NatanChatController] Failed to get suggestions', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'suggestions' => [
                    "Riassumi l'ultimo atto caricato",
                    "Quali atti sono stati caricati questo mese?",
                    "Come funziona N.A.T.A.N.?"
                ]
            ]);
        }
    }
}

