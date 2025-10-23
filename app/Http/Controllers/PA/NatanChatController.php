<?php

namespace App\Http\Controllers\PA;

use App\Http\Controllers\Controller;
use App\Models\NatanChatMessage;
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

        // Get 6 random strategic questions from the library
        $allStrategicQuestions = $this->getStrategicQuestionsLibrary();
        $suggestedQuestions = collect($allStrategicQuestions)->random(6)->toArray();

        return view('pa.natan.chat', [
            'suggested_questions' => $suggestedQuestions
        ]);
    }

    /**
     * Get all strategic questions from the library
     * 
     * @return array
     */
    private function getStrategicQuestionsLibrary(): array
    {
        return [
            // Strategic & Governance
            "Analizza le principali aree di investimento del Comune negli ultimi 12 mesi e suggerisci una strategia di ottimizzazione basata su ROI e priorità strategiche",
            "Identifica i ritardi nei progetti PNRR e proponi un piano di recovery con milestone specifiche e azioni correttive immediate",
            "Crea una matrice decisionale per prioritizzare i progetti in base a impatto, urgenza, costo e fattibilità tecnica",
            "Confronta le performance del Comune con best practices nazionali e internazionali, identificando gap e opportunità di miglioramento",

            // Technical
            "Valuta la fattibilità tecnica dei progetti infrastrutturali in corso e identifica rischi critici con strategie di mitigazione",
            "Analizza lo stato manutentivo delle infrastrutture pubbliche e proponi un piano di manutenzione predittiva basato su priorità",
            "Identifica progetti con problemi di compliance normativa tecnica e proponi azioni correttive con timeline",
            "Valuta le specifiche tecniche degli appalti recenti e suggerisci miglioramenti per future gare",

            // Financial
            "Analizza l'efficienza della spesa pubblica per settore, calcola costo per cittadino servito e identifica aree di ottimizzazione",
            "Identifica tutte le opportunità di funding EU disponibili (PNRR, PON, FSE) e valuta quali progetti possono candidarsi",
            "Crea un modello finanziario NPV/IRR per i progetti a lungo termine e calcola il break-even point",
            "Analizza il budget variance degli ultimi 3 anni e proponi strategie per migliorare la previsione finanziaria",

            // Legal
            "Verifica la compliance GDPR di tutti gli atti che trattano dati personali e identifica eventuali violazioni con azioni correttive",
            "Analizza i procedimenti amministrativi con rischio contenzioso e proponi strategie di de-risking legale",
            "Identifica tutti gli atti con problemi di trasparenza o anticorruzione secondo la normativa vigente",
            "Valuta la regolarità delle procedure di gara recenti e suggerisci best practices per future procedure",

            // Urban & Social
            "Analizza l'impatto sociale dei progetti di rigenerazione urbana e calcola il SROI (Social Return on Investment)",
            "Identifica le aree sottoutilizzate della città e proponi strategie di riqualificazione con focus su accessibilità e inclusione",
            "Valuta l'equità territoriale nella distribuzione dei servizi pubblici e proponi azioni per ridurre i gap",
            "Crea un piano di partecipazione cittadina per i prossimi progetti urbani con metodologie innovative",

            // Communication
            "Crea una strategia di comunicazione per annunciare i risultati dei progetti PNRR con key messages e piano media",
            "Identifica i progetti con maggior potenziale mediatico e sviluppa storytelling efficace per massimizzare l'impatto",
            "Analizza il sentiment pubblico sui progetti in corso e proponi strategie di engagement per aumentare il supporto",
            "Sviluppa un piano di crisis communication per gestire eventuali controversie su progetti sensibili",

            // Power Questions
            "Crea una dashboard strategica per il Sindaco con i 10 KPI più critici della città, analisi trend e early warning systems",
            "Identifica le 3 azioni quick-win con massimo impatto politico e minimo costo, con timeline 60 giorni e piano esecutivo dettagliato",
            "Analizza tutti i progetti e crea una roadmap strategica 2024-2026 con prioritization matrix, dependencies e critical path",
            "Simula 3 scenari futuri (ottimistico, realistico, pessimistico) per il portfolio progetti e proponi strategie di adattamento per ciascuno",
        ];
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
     *   ],
     *   "persona_id": "strategic" | "technical" | "legal" | "financial" | "urban_social" | "communication" | null,
     *   "session_id": "optional_session_id",
     *   "use_rag": true | false (default: true),
     *   "reference_message_id": 123 (optional: elaborates on previous message)
     * }
     *
     * RESPONSE:
     * {
     *   "success": true,
     *   "response": "Ecco il riassunto dell'ultimo atto...",
     *   "sources": [...],
     *   "persona": {
     *     "id": "strategic",
     *     "name": "Consulente Strategico",
     *     "confidence": 0.85,
     *     "method": "auto" | "manual" | "keyword" | "ai" | "default",
     *     "reasoning": "...",
     *     "alternatives": [...],
     *     "suggestion": "..."
     *   },
     *   "session_id": "natan_xxxx",
     *   "is_elaboration": true | false
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
                'message' => ['required', 'string', 'min:3', 'max:10000'], // Increased for long texts/documents
                'conversation_history' => ['nullable', 'array', 'max:10'], // Max 10 previous messages
                'persona_id' => ['nullable', 'string', 'in:strategic,technical,legal,financial,urban_social,communication'],
                'session_id' => ['nullable', 'string', 'max:100'],
                'use_rag' => ['nullable', 'boolean'], // Enable/disable RAG retrieval
                'reference_message_id' => ['nullable', 'integer', 'exists:natan_chat_messages,id'], // Elaborate on previous message
            ]);

            $user = auth()->user();
            $message = $validated['message'];
            $history = $validated['conversation_history'] ?? [];
            $personaId = $validated['persona_id'] ?? null;
            $sessionId = $validated['session_id'] ?? null;
            $useRag = $validated['use_rag'] ?? true; // Default: use RAG
            $referenceMessageId = $validated['reference_message_id'] ?? null;

            $this->logger->info('[NatanChatController] Processing user message', [
                ...$logContext,
                'message_length' => strlen($message),
                'history_length' => count($history),
                'persona_id' => $personaId,
                'session_id' => $sessionId,
                'use_rag' => $useRag,
                'reference_message_id' => $referenceMessageId,
            ]);

            // Load reference message if provided
            $referenceContext = null;
            if ($referenceMessageId) {
                $referenceMessage = NatanChatMessage::find($referenceMessageId);

                // Verify user owns this message session
                if ($referenceMessage && $referenceMessage->user_id === $user->id) {
                    $referenceContext = [
                        'id' => $referenceMessage->id,
                        'role' => $referenceMessage->role,
                        'content' => $referenceMessage->content,
                        'persona_id' => $referenceMessage->persona_id,
                        'persona_name' => $referenceMessage->persona_name,
                        'created_at' => $referenceMessage->created_at->toIso8601String(),
                    ];

                    $this->logger->info('[NatanChatController] Reference message loaded for elaboration', [
                        ...$logContext,
                        'reference_message_id' => $referenceMessageId,
                        'reference_persona' => $referenceMessage->persona_name,
                    ]);
                } else {
                    $this->logger->warning('[NatanChatController] Invalid reference message (not found or access denied)', [
                        ...$logContext,
                        'reference_message_id' => $referenceMessageId,
                    ]);
                }
            }

            // Process query with N.A.T.A.N. service (with persona support + elaboration)
            $result = $this->chatService->processQuery(
                $message,
                $user,
                $history,
                $personaId,
                $sessionId,
                $useRag,
                $referenceContext
            );

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
                'sources_count' => count($result['sources'] ?? []),
                'persona_used' => $result['persona']['id'] ?? null
            ]);

            return response()->json([
                'success' => true,
                'response' => $result['response'],
                'sources' => $result['sources'] ?? [],
                'persona' => $result['persona'] ?? null,
                'session_id' => $result['session_id'] ?? null,
                'message_ids' => $result['message_ids'] ?? null, // IDs for user and assistant messages
                'is_elaboration' => $referenceContext !== null,
                'reference_message_id' => $referenceMessageId,
                'reference_content' => $referenceContext['content'] ?? null, // Original message content for UI
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
