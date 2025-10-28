<?php

namespace App\Http\Controllers\PA;

use App\Http\Controllers\Controller;
use App\Models\NatanChatMessage;
use App\Services\NatanChatService;
use App\Services\Natan\NatanIntelligentChunkingService;
use App\Services\AiCreditsService;
use App\Services\Gdpr\AuditLogService;
use App\Services\Gdpr\ConsentService;
use App\Enums\Gdpr\GdprActivityCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

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
 * @version 2.0.0 (FlorenceEGI - N.A.T.A.N. Chat AI - UEM/GDPR Compliant)
 * @date 2025-10-28
 */
class NatanChatController extends Controller {
    protected NatanChatService $chatService;
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;
    protected AuditLogService $auditService;
    protected ConsentService $consentService;
    protected NatanIntelligentChunkingService $chunkingService;
    protected AiCreditsService $creditsService;

    public function __construct(
        NatanChatService $chatService,
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService,
        ConsentService $consentService,
        NatanIntelligentChunkingService $chunkingService,
        AiCreditsService $creditsService
    ) {
        $this->chatService = $chatService;
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditService = $auditService;
        $this->consentService = $consentService;
        $this->chunkingService = $chunkingService;
        $this->creditsService = $creditsService;
    }

    /**
     * Show N.A.T.A.N. chat interface
     *
     * @return \Illuminate\View\View
     */
    public function index() {
        $user = auth()->user();

        // Get 6 random strategic questions from the library
        $allStrategicQuestions = $this->getStrategicQuestionsLibrary();
        $suggestedQuestions = collect($allStrategicQuestions)->random(6)->toArray();

        // ✨ NEW v4.0 - Projects System integration
        $projects = $user->projects()->orderBy('created_at', 'desc')->get();
        $activeProjectId = session('active_project_id');
        $activeProject = $activeProjectId ? $projects->firstWhere('id', $activeProjectId) : null;

        return view('pa.natan.chat', [
            'suggested_questions' => $suggestedQuestions,
            'projects' => $projects,
            'activeProject' => $activeProject,
        ]);
    }

    /**
     * Get all strategic questions from the library
     *
     * @return array
     */
    private function getStrategicQuestionsLibrary(): array {
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
    public function sendMessage(Request $request): JsonResponse {
        $logContext = [
            'controller' => static::class,
            'user_id' => auth()->id(),
            'method' => __FUNCTION__
        ];

        try {
            $user = auth()->user();

            // GDPR: Check AI processing consent
            if (!$this->consentService->hasConsent($user, 'allow-ai-processing')) {
                $this->errorManager->handle('NATAN_AI_CONSENT_REQUIRED', [
                    'user_id' => $user->id,
                ]);

                return response()->json([
                    'success' => false,
                    'error' => 'consent_required',
                    'message' => __('natan.errors.ai_consent_required'),
                ], 403);
            }

            // Validate input
            $validated = $request->validate([
                'message' => ['required', 'string', 'min:3', 'max:10000'],
                'conversation_history' => ['nullable', 'array', 'max:10'],
                'persona_id' => ['nullable', 'string', 'in:strategic,technical,legal,financial,urban_social,communication'],
                'session_id' => ['nullable', 'string', 'max:100'],
                'use_rag' => ['nullable', 'boolean'],
                'use_web_search' => ['nullable', 'boolean'],
                'reference_message_id' => ['nullable', 'integer', 'exists:natan_chat_messages,id'],
                'project_id' => ['nullable', 'integer', 'exists:projects,id'],
            ]);

            $message = $validated['message'];
            $history = $validated['conversation_history'] ?? [];
            $personaId = $validated['persona_id'] ?? null;
            $sessionId = $validated['session_id'] ?? null;
            $useRag = $validated['use_rag'] ?? true;
            $useWebSearch = $validated['use_web_search'] ?? false;
            $referenceMessageId = $validated['reference_message_id'] ?? null;
            $projectId = $validated['project_id'] ?? session('active_project_id');

            $this->logger->info('[NatanChatController] Processing user message', [
                ...$logContext,
                'message_length' => strlen($message),
                'history_length' => count($history),
                'persona_id' => $personaId,
                'session_id' => $sessionId,
                'use_rag' => $useRag,
                'use_web_search' => $useWebSearch,
                'reference_message_id' => $referenceMessageId,
            ]);

            // Load reference message if provided
            $referenceContext = null;
            if ($referenceMessageId) {
                $referenceMessage = NatanChatMessage::find($referenceMessageId);

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

            // Process query with N.A.T.A.N. service
            $result = $this->chatService->processQuery(
                $message,
                $user,
                $history,
                $personaId,
                $sessionId,
                $useRag,
                $useWebSearch,
                $referenceContext,
                $projectId
            );

            if (!$result['success']) {
                $this->errorManager->handle('NATAN_QUERY_PROCESSING_FAILED', [
                    'user_id' => $user->id,
                    'error' => $result['error'] ?? 'unknown',
                ]);

                return response()->json([
                    'success' => false,
                    'error' => $result['error'] ?? 'Errore sconosciuto',
                    'message' => $result['response']
                ], 500);
            }

            // GDPR: Audit log AI interaction
            $this->auditService->logUserAction(
                $user,
                'natan_message_sent',
                [
                    'session_id' => $result['session_id'] ?? $sessionId,
                    'message_length' => strlen($message),
                    'persona_used' => $result['persona']['id'] ?? null,
                    'sources_count' => count($result['sources'] ?? []),
                    'use_web_search' => $useWebSearch,
                ],
                GdprActivityCategory::AI_PROCESSING
            );

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
                'web_sources' => $result['web_sources'] ?? [],
                'web_search_metadata' => $result['web_search_metadata'] ?? null,
                'persona' => $result['persona'] ?? null,
                'session_id' => $result['session_id'] ?? null,
                'message_ids' => $result['message_ids'] ?? null,
                'is_elaboration' => $referenceContext !== null,
                'reference_message_id' => $referenceMessageId,
                'reference_content' => $referenceContext['content'] ?? null,
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
            $this->errorManager->handle('NATAN_MESSAGE_PROCESSING_FAILED', [
                'user_id' => auth()->id(),
                'message_length' => strlen($request->input('message', '')),
            ], $e);

            return response()->json([
                'success' => false,
                'error' => 'server_error',
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
    public function getSuggestions(): JsonResponse {
        try {
            $user = auth()->user();
            $suggestions = $this->chatService->getSuggestedQuestions($user);

            return response()->json([
                'success' => true,
                'suggestions' => $suggestions
            ]);
        } catch (\Exception $e) {
            $this->errorManager->handle('NATAN_SUGGESTIONS_FAILED', [
                'user_id' => auth()->id(),
            ], $e);

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

    /**
     * Get user chat history (list of sessions)
     *
     * GDPR-COMPLIANT: Returns only authenticated user's sessions
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getHistory(Request $request): JsonResponse {
        try {
            $user = auth()->user();

            // GDPR: Audit log data access
            $result = $this->chatService->getUserChatHistory($user, 50);

            $this->auditService->logUserAction(
                $user,
                'natan_history_accessed',
                [
                    'sessions_count' => count($result['sessions'] ?? []),
                ],
                GdprActivityCategory::DATA_ACCESS
            );

            return response()->json($result);
        } catch (\Exception $e) {
            $this->errorManager->handle('NATAN_HISTORY_ACCESS_FAILED', [
                'user_id' => auth()->id(),
            ], $e);

            return response()->json([
                'success' => false,
                'error' => 'history_access_failed',
            ], 500);
        }
    }

    /**
     * Get messages from a specific session
     *
     * GDPR-COMPLIANT: Authorization check (only owner can access)
     *
     * @param Request $request
     * @param string $sessionId
     * @return JsonResponse
     */
    public function getSession(Request $request, string $sessionId): JsonResponse {
        try {
            $user = auth()->user();

            // GDPR: Audit log session access
            $result = $this->chatService->getSessionMessages($sessionId, $user);

            $this->auditService->logUserAction(
                $user,
                'natan_session_accessed',
                [
                    'session_id' => $sessionId,
                    'messages_count' => count($result['messages'] ?? []),
                ],
                GdprActivityCategory::DATA_ACCESS
            );

            return response()->json($result);
        } catch (\Exception $e) {
            $this->errorManager->handle('NATAN_SESSION_ACCESS_FAILED', [
                'user_id' => auth()->id(),
                'session_id' => $sessionId,
            ], $e);

            return response()->json([
                'success' => false,
                'error' => 'session_access_failed',
            ], 500);
        }
    }

    /**
     * Delete a user session (GDPR: Right to be forgotten)
     *
     * GDPR-COMPLIANT: Authorization check + Audit trail
     *
     * @param Request $request
     * @param string $sessionId
     * @return JsonResponse
     */
    public function deleteSession(Request $request, string $sessionId): JsonResponse {
        try {
            $user = auth()->user();

            $this->logger->info('[NATAN][Controller] Deleting session', [
                'user_id' => $user->id,
                'session_id' => $sessionId,
            ]);

            // Delete session via service
            $result = $this->chatService->deleteUserSession($sessionId, $user);

            if ($result['success']) {
                // GDPR: Audit log deletion (Right to be forgotten)
                $this->auditService->logUserAction(
                    $user,
                    'natan_session_deleted',
                    [
                        'session_id' => $sessionId,
                        'messages_deleted' => $result['messages_deleted'] ?? 0,
                    ],
                    GdprActivityCategory::DATA_DELETION
                );
            }

            return response()->json($result);
        } catch (\Exception $e) {
            $this->errorManager->handle('NATAN_SESSION_DELETE_FAILED', [
                'user_id' => auth()->id(),
                'session_id' => $sessionId,
            ], $e);

            return response()->json([
                'success' => false,
                'error' => 'session_delete_failed',
            ], 500);
        }
    }

    /**
     * Search Preview - PHASE 1: Count matching acts
     *
     * POST /pa/natan/search-preview
     *
     * REQUEST:
     * {
     *   "query": "delibere sulla sostenibilità ambientale"
     * }
     *
     * RESPONSE:
     * {
     *   "success": true,
     *   "total_found": 3427,
     *   "keywords_used": ["sostenibilità", "ambiente", "green"],
     *   "slider_config": {
     *     "min": 50,
     *     "max": 5000,
     *     "default": 500
     *   }
     * }
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function searchPreview(Request $request): JsonResponse {
        try {
            $user = auth()->user();

            $validated = $request->validate([
                'query' => 'required|string|min:3|max:500',
            ]);

            $query = $validated['query'];

            $this->logger->info('[NATAN] Search preview requested', [
                'user_id' => $user->id,
                'query' => $query,
            ]);

            // Extract keywords from query
            $keywords = $this->extractKeywords($query);

            // Count matching acts in user's collection
            $totalFound = \App\Models\Egi::query()
                ->whereHas('collection', function ($q) use ($user) {
                    $q->where('creator_id', $user->id);
                })
                ->whereNotNull('pa_act_type')
                ->where(function ($q) use ($keywords) {
                    foreach ($keywords as $keyword) {
                        $q->orWhere('title', 'LIKE', "%{$keyword}%")
                            ->orWhere('description', 'LIKE', "%{$keyword}%");
                    }
                })
                ->count();

            $sliderConfig = $this->chunkingService->getSliderConfig();

            return response()->json([
                'success' => true,
                'total_found' => $totalFound,
                'keywords_used' => $keywords,
                'slider_config' => $sliderConfig,
            ]);
        } catch (\Exception $e) {
            $this->errorManager->handle('NATAN_SEARCH_PREVIEW_FAILED', [
                'user_id' => auth()->id(),
                'query' => $request->input('query', ''),
            ], $e);

            return response()->json([
                'success' => false,
                'error' => 'search_preview_failed',
            ], 500);
        }
    }

    /**
     * Analyze Acts - PHASE 2: Process with user-selected limit
     *
     * POST /pa/natan/analyze
     *
     * REQUEST:
     * {
     *   "query": "delibere sulla sostenibilità",
     *   "limit": 1000,
     *   "session_id": "natan_xxx" (optional)
     * }
     *
     * RESPONSE (streaming or final):
     * {
     *   "success": true,
     *   "status": "processing|completed",
     *   "progress": 40,
     *   "current_chunk": 2,
     *   "total_chunks": 5,
     *   "partial_results": [...],
     *   "final_response": "...",
     *   "session_id": "natan_xxx"
     * }
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function analyzeActs(Request $request): JsonResponse {
        try {
            $user = auth()->user();

            // GDPR: Check AI processing consent
            if (!$this->consentService->hasConsent($user, 'allow-ai-processing')) {
                $this->errorManager->handle('NATAN_AI_CONSENT_REQUIRED', [
                    'user_id' => $user->id,
                ]);

                return response()->json([
                    'success' => false,
                    'error' => 'consent_required',
                ], 403);
            }

            $validated = $request->validate([
                'query' => 'required|string|min:3|max:500',
                'limit' => 'required|integer|min:' . config('natan.slider_min_acts', 50)
                    . '|max:' . config('natan.slider_max_acts', 5000),
                'session_id' => 'nullable|string|max:100',
            ]);

            $query = $validated['query'];
            $limit = $validated['limit'];
            $sessionId = $validated['session_id'] ?? 'natan_' . \Str::random(10);

            $this->logger->info('[NATAN] Analysis requested', [
                'user_id' => $user->id,
                'query' => $query,
                'limit' => $limit,
                'session_id' => $sessionId,
            ]);

            // Use RAG semantic search instead of keyword matching
            $ragContext = app(\App\Services\RagService::class)->getContextForQuery(
                $query,
                $user,
                $limit // Search with user-specified limit
            );

            $acts = collect($ragContext['acts'] ?? [])
                ->map(function ($actData) {
                    // RAG returns array data, convert to Egi models
                    return \App\Models\Egi::find($actData['id']);
                })
                ->filter(); // Remove nulls

            $this->logger->info('[NATAN] Acts retrieved via semantic search', [
                'user_id' => $user->id,
                'total_acts' => $acts->count(),
                'limit' => $limit,
            ]);

            if ($acts->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'status' => 'completed',
                    'final_response' => __('natan.no_acts_found'),
                    'session_id' => $sessionId,
                ]);
            }

            // Determine chunking strategy
            $strategy = $this->chunkingService->determineOptimalStrategy($acts, $query);

            // Chunk the acts
            $chunks = match ($strategy) {
                'token-based' => $this->chunkingService->chunkByTokenBudget($acts),
                'relevance-based' => collect([$acts]), // Simplified for now
                'adaptive' => $this->chunkingService->adaptiveChunk($acts),
                default => $this->chunkingService->chunkByTokenBudget($acts),
            };

            $this->logger->info('[NATAN] Acts chunked for processing', [
                'user_id' => $user->id,
                'total_acts' => $acts->count(),
                'num_chunks' => $chunks->count(),
                'strategy' => $strategy,
            ]);

            // Get processing estimation
            $estimation = $this->chunkingService->estimateProcessing($acts->count(), $limit);

            // Get cost estimation using AiCreditsService
            $chunkSize = config('natan.chunk_size', 100);
            $costEstimation = $this->creditsService->getEstimatedCost($acts->count(), $chunkSize);

            // Check if user has enough credits for chunking mode
            $needsChunking = $chunks->count() > 1;

            if ($needsChunking && $costEstimation['estimated_credits'] > 0) {
                // Check credit sufficiency
                if (!$this->creditsService->hasEnoughCredits($user, $costEstimation['estimated_credits'])) {
                    $this->logger->warning('[NATAN] Insufficient credits for analysis', [
                        'user_id' => $user->id,
                        'required_credits' => $costEstimation['estimated_credits'],
                        'user_balance' => $user->ai_credits_balance ?? 0,
                    ]);

                    return response()->json([
                        'success' => false,
                        'error' => 'insufficient_credits',
                        'required_credits' => $costEstimation['estimated_credits'],
                        'user_balance' => $user->ai_credits_balance ?? 0,
                        'estimated_cost_eur' => $costEstimation['estimated_cost_eur'],
                        'message' => __('natan.errors.insufficient_credits', [
                            'required' => $costEstimation['estimated_credits'],
                            'balance' => $user->ai_credits_balance ?? 0,
                        ]),
                    ], 402); // 402 Payment Required
                }
            }

            // Decide: normal processing or chunking mode

            if ($needsChunking) {
                // CHUNKING MODE: Create session and dispatch background job
                $sessionData = [
                    'user_id' => $user->id,
                    'query' => $query,
                    'user_limit' => $limit,
                    'total_acts' => $acts->count(),
                    'total_chunks' => $chunks->count(),
                    'current_chunk' => 0,
                    'chunk_progress' => 0,
                    'acts_in_current_chunk' => 0,
                    'completed_chunks' => [],
                    'last_completed' => false,
                    'status' => 'started',
                    'strategy' => $strategy,
                    'started_at' => now()->toISOString(),
                ];

                // Store session in cache (TTL: 2 hours)
                \Cache::put("natan_chunking_{$sessionId}", $sessionData, now()->addHours(2));

                // Dispatch background job
                \App\Jobs\ProcessChunkedAnalysis::dispatch($sessionId);

                $this->logger->info('[NATAN] Chunking job dispatched', [
                    'user_id' => $user->id,
                    'session_id' => $sessionId,
                    'chunks' => $chunks->count(),
                ]);

                // GDPR: Audit log analysis start
                $this->auditService->logUserAction(
                    $user,
                    'natan_chunked_analysis_started',
                    [
                        'session_id' => $sessionId,
                        'query' => $query,
                        'total_acts' => $acts->count(),
                        'total_chunks' => $chunks->count(),
                        'strategy' => $strategy,
                        'estimated_credits' => $costEstimation['estimated_credits'],
                    ],
                    GdprActivityCategory::AI_PROCESSING
                );

                return response()->json([
                    'success' => true,
                    'mode' => 'chunking',
                    'session_id' => $sessionId,
                    'total_acts' => $acts->count(),
                    'total_chunks' => $chunks->count(),
                    'estimated_time_seconds' => $estimation['estimated_time_seconds'],
                    'estimated_time_human' => $estimation['estimated_time_human'],
                    'estimated_cost_eur' => $costEstimation['estimated_cost_eur'],
                    'estimated_credits' => $costEstimation['estimated_credits'],
                    'user_balance' => $user->ai_credits_balance ?? 0,
                    'strategy' => $strategy,
                    'message' => 'Elaborazione avviata in background. Polling su /chunking-progress/{sessionId}',
                ]);
            } else {
                // NORMAL MODE: Single chunk, process immediately
                $this->logger->info('[NATAN] Normal processing (single chunk)', [
                    'user_id' => $user->id,
                    'session_id' => $sessionId,
                    'acts' => $acts->count(),
                ]);

                // GDPR: Audit log normal analysis
                $this->auditService->logUserAction(
                    $user,
                    'natan_normal_analysis_started',
                    [
                        'session_id' => $sessionId,
                        'query' => $query,
                        'total_acts' => $acts->count(),
                    ],
                    GdprActivityCategory::AI_PROCESSING
                );

                // TODO: Process immediately with chatService
                // For now, return ready status
                return response()->json([
                    'success' => true,
                    'mode' => 'normal',
                    'session_id' => $sessionId,
                    'total_acts' => $acts->count(),
                    'message' => __('natan.analysis_ready_to_start'),
                ]);
            }
        } catch (\Exception $e) {
            $this->errorManager->handle('NATAN_ANALYSIS_FAILED', [
                'user_id' => auth()->id(),
                'query' => $request->input('query', ''),
                'limit' => $request->input('limit', 0),
            ], $e);

            return response()->json([
                'success' => false,
                'error' => 'analysis_failed',
            ], 500);
        }
    }

    /**
     * Get chunking progress - Polling endpoint
     *
     * GET /pa/natan/chunking-progress/{sessionId}
     *
     * RESPONSE:
     * {
     *   "success": true,
     *   "current_chunk": 2,
     *   "chunk_progress": 75,
     *   "acts_in_chunk": 180,
     *   "completed_chunks": 1,
     *   "total_chunks": 5,
     *   "chunk_completed": true,
     *   "completed_chunk_index": 1,
     *   "relevant_acts_found": 23,
     *   "chunk_summary": "...",
     *   "all_completed": false,
     *   "status": "processing"
     * }
     *
     * @param string $sessionId
     * @return JsonResponse
     */
    public function getChunkingProgress(string $sessionId): JsonResponse {
        try {
            $user = auth()->user();

            // Retrieve session from cache
            $session = \Cache::get("natan_chunking_{$sessionId}");

            if (!$session) {
                return response()->json([
                    'success' => false,
                    'error' => 'session_not_found',
                    'message' => 'Sessione non trovata o scaduta',
                ], 404);
            }

            // Security check: user owns this session
            if ($session['user_id'] !== $user->id) {
                return response()->json([
                    'success' => false,
                    'error' => 'unauthorized',
                    'message' => 'Non autorizzato ad accedere a questa sessione',
                ], 403);
            }

            $this->logger->debug('[NATAN Chunking] Progress polled', [
                'session_id' => $sessionId,
                'user_id' => $user->id,
                'status' => $session['status'],
                'current_chunk' => $session['current_chunk'] ?? null,
            ]);

            // Return progress data
            return response()->json([
                'success' => true,
                'current_chunk' => $session['current_chunk'] ?? 0,
                'chunk_progress' => $session['chunk_progress'] ?? 0,
                'acts_in_chunk' => $session['acts_in_current_chunk'] ?? 0,
                'completed_chunks' => count($session['completed_chunks'] ?? []),
                'total_chunks' => $session['total_chunks'] ?? 0,
                'chunk_completed' => $session['last_completed'] ?? false,
                'completed_chunk_index' => $session['last_completed_index'] ?? null,
                'relevant_acts_found' => $session['last_chunk_relevant_acts'] ?? null,
                'chunk_summary' => $session['last_chunk_summary'] ?? null,
                'all_completed' => $session['status'] === 'aggregating' || $session['status'] === 'completed',
                'status' => $session['status'],
            ]);
        } catch (\Exception $e) {
            $this->errorManager->handle('NATAN_CHUNKING_PROGRESS_FAILED', [
                'session_id' => $sessionId,
                'user_id' => auth()->id(),
            ], $e);

            return response()->json([
                'success' => false,
                'error' => 'progress_poll_failed',
            ], 500);
        }
    }

    /**
     * Get final aggregated response - Called when all chunks completed
     *
     * GET /pa/natan/chunking-final/{sessionId}
     *
     * RESPONSE:
     * {
     *   "success": true,
     *   "aggregated_response": "...",
     *   "total_relevant_acts": 67,
     *   "chunks_processed": 5,
     *   "total_time_seconds": 52,
     *   "sources": [...]
     * }
     *
     * @param string $sessionId
     * @return JsonResponse
     */
    public function getChunkingFinal(string $sessionId): JsonResponse {
        try {
            $user = auth()->user();

            // Retrieve session from cache
            $session = \Cache::get("natan_chunking_{$sessionId}");

            if (!$session) {
                return response()->json([
                    'success' => false,
                    'error' => 'session_not_found',
                    'message' => 'Sessione non trovata o scaduta',
                ], 404);
            }

            // Security check
            if ($session['user_id'] !== $user->id) {
                return response()->json([
                    'success' => false,
                    'error' => 'unauthorized',
                    'message' => 'Non autorizzato ad accedere a questa sessione',
                ], 403);
            }

            // Check if processing is complete
            if ($session['status'] !== 'completed') {
                return response()->json([
                    'success' => false,
                    'error' => 'not_ready',
                    'message' => 'Elaborazione non ancora completata',
                    'status' => $session['status'],
                ], 425); // 425 Too Early
            }

            $this->logger->info('[NATAN Chunking] Final response retrieved', [
                'session_id' => $sessionId,
                'user_id' => $user->id,
                'chunks_processed' => count($session['completed_chunks'] ?? []),
            ]);

            // GDPR: Audit log completed chunked analysis retrieval
            $this->auditService->logUserAction(
                $user,
                'natan_chunked_analysis_completed',
                [
                    'session_id' => $sessionId,
                    'chunks_processed' => count($session['completed_chunks'] ?? []),
                    'total_relevant_acts' => $session['total_relevant_acts'] ?? 0,
                ],
                GdprActivityCategory::AI_PROCESSING
            );

            // Return final aggregated response
            return response()->json([
                'success' => true,
                'aggregated_response' => $session['final_response'] ?? '',
                'total_relevant_acts' => $session['total_relevant_acts'] ?? 0,
                'chunks_processed' => count($session['completed_chunks'] ?? []),
                'total_time_seconds' => $session['total_time_seconds'] ?? 0,
                'sources' => $session['sources'] ?? [],
                'metadata' => [
                    'query' => $session['query'] ?? '',
                    'strategy' => $session['strategy'] ?? 'token-based',
                    'completed_at' => $session['completed_at'] ?? now()->toISOString(),
                ],
            ]);
        } catch (\Exception $e) {
            $this->errorManager->handle('NATAN_CHUNKING_FINAL_FAILED', [
                'session_id' => $sessionId,
                'user_id' => auth()->id(),
            ], $e);

            return response()->json([
                'success' => false,
                'error' => 'final_response_failed',
            ], 500);
        }
    }

    /**
     * Estimate AI Credits cost for analysis (before execution)
     *
     * @package App\Http\Controllers\PA
     * @author Padmin D. Curtis (AI Partner OS3.0)
     * @version 1.0.0 (FlorenceEGI - AI Credits Cost Tracking)
     * @date 2025-10-28
     * @purpose Provide cost estimation to frontend before user commits to analysis
     *
     * @param Request $request - { query, acts_ids[], chunks_count? }
     * @return JsonResponse
     */
    public function estimateCost(Request $request): JsonResponse {
        try {
            $user = auth()->user();

            // Validate request
            $validated = $request->validate([
                'query' => 'required|string|max:1000',
                'acts_ids' => 'nullable|array',
                'acts_ids.*' => 'integer|exists:acts,id',
                'chunks_count' => 'nullable|integer|min:1|max:20',
            ]);

            $query = $validated['query'];
            $actsIds = $validated['acts_ids'] ?? [];
            $chunksCount = $validated['chunks_count'] ?? 1;

            // If no acts provided, estimate from typical query
            $actsCount = count($actsIds);
            if ($actsCount === 0) {
                // Estimate typical result set (conservative estimate: 50 acts)
                $actsCount = 50;
            }

            // Calculate estimation
            $estimation = $this->creditsService->getEstimatedCost(
                $query,
                $actsCount,
                $chunksCount
            );

            // Add user balance
            $userBalance = $user->ai_credits_balance ?? 0;
            $estimation['user_balance'] = $userBalance;
            $estimation['sufficient_balance'] = $userBalance >= $estimation['estimated_credits'];

            $this->logger->info('[NatanChatController] Cost estimation provided', [
                'user_id' => $user->id,
                'acts_count' => $actsCount,
                'chunks_count' => $chunksCount,
                'estimated_credits' => $estimation['estimated_credits'],
                'user_balance' => $userBalance,
            ]);

            return response()->json($estimation);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'validation_failed',
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            $this->logger->error('[NatanChatController] Cost estimation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'estimation_failed',
                'message' => 'Failed to estimate cost. Please try again.',
            ], 500);
        }
    }

    /**
     * Analyze Acts with Server-Sent Events (SSE) Streaming
     *
     * Real-time progress tracking for semantic search + AI analysis.
     *
     * EVENTS EMITTED:
     * 1. 'semantic_search_start' → Search begins
     * 2. 'semantic_search_progress' → Search in progress (if large dataset)
     * 3. 'semantic_search_complete' → Found acts with REAL counts
     * 4. 'chunking_start' → If chunking mode activated
     * 5. 'chunk_start' → Each chunk begins
     * 6. 'chunk_progress' → Chunk progress updates (0-100%)
     * 7. 'chunk_complete' → Chunk done with partial results
     * 8. 'ai_analysis_start' → Claude processing starts
     * 9. 'cost_update' → Real-time cost tracking
     * 10. 'response_generation_start' → Markdown rendering begins
     * 11. 'response_generation_complete' → Final response ready
     * 12. 'done' → Stream ends
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function analyzeActsStream(Request $request) {
        // Capture controller dependencies for use inside closure
        $chatService = $this->chatService;
        $creditsService = $this->creditsService;
        $buildEnterprisePrompt = [$this, 'buildEnterprisePrompt'];
        $formatActsForClaude = [$this, 'formatActsForClaude'];

        // Helper function to emit SSE events (defined outside closure)
        $emitSSE = function (string $event, array $data) {
            echo "event: {$event}\n";
            echo "data: " . json_encode($data) . "\n\n";
            
            // Force immediate send (disable all buffering)
            if (ob_get_level() > 0) {
                ob_end_flush();
            }
            flush();
        };

        return response()->stream(function () use ($request, $emitSSE, $chatService, $creditsService, $buildEnterprisePrompt, $formatActsForClaude) {
            // Disable output buffering completely
            @ini_set('output_buffering', 'off');
            @ini_set('zlib.output_compression', false);
            
            // Disable implicit flush (we control it manually)
            @ob_implicit_flush(true);
            
            // Clear any existing buffers
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
            
            try {
                $user = auth()->user();

                // Validate input
                $validated = $request->validate([
                    'query' => 'required|string|min:3|max:500',
                    'limit' => 'required|integer|min:50|max:5000',
                ]);

                $query = $validated['query'];
                $limit = $validated['limit'];

                // EVENT 1: Semantic Search Start
                $emitSSE('semantic_search_start', [
                    'model' => 'text-embedding-3-small',
                    'query' => $query,
                    'timestamp' => now()->toISOString(),
                ]);

                // Execute RAG semantic search (BLOCKING - may take 2-3 minutes!)
                \Log::info('[SSE] Starting RAG search...', ['query' => $query, 'limit' => $limit]);
                
                $ragContext = app(\App\Services\RagService::class)->getContextForQuery(
                    $query,
                    $user,
                    $limit
                );
                
                \Log::info('[SSE] RAG search completed', [
                    'acts_count' => count($ragContext['acts'] ?? []),
                    'first_act_structure' => isset($ragContext['acts'][0]) ? array_keys($ragContext['acts'][0]) : [],
                    'first_act_has_similarity' => isset($ragContext['acts'][0]['similarity']),
                ]);

                // Extract acts and calculate average similarity
                $acts = collect($ragContext['acts'] ?? [])
                    ->map(fn($actData) => $actData['act'] ?? \App\Models\Egi::find($actData['id']))
                    ->filter();

                // FIXED: avg() on array keys not working - extract similarity values first
                $similarities = collect($ragContext['acts'] ?? [])
                    ->pluck('similarity')
                    ->filter(fn($val) => $val !== null);
                
                \Log::info('[SSE] Similarities extracted', [
                    'count' => $similarities->count(),
                    'first_5' => $similarities->take(5)->toArray(),
                    'avg' => $similarities->avg(),
                ]);
                
                $avgRelevance = $similarities->isNotEmpty() 
                    ? $similarities->avg() 
                    : 0;

                // EVENT 2: Semantic Search Complete
                $emitSSE('semantic_search_complete', [
                    'acts_found' => $acts->count(),
                    'avg_relevance' => round($avgRelevance * 100, 1),
                    'timestamp' => now()->toISOString(),
                ]);

                if ($acts->isEmpty()) {
                    $emitSSE('done', [
                        'status' => 'no_acts',
                        'message' => __('natan.no_acts_found'),
                    ]);
                    return;
                }

                // EVENT 3: AI Analysis Start
                $emitSSE('ai_analysis_start', [
                    'model' => 'claude-3-5-sonnet-20241022',
                    'total_acts' => $acts->count(),
                    'timestamp' => now()->toISOString(),
                ]);

                // EVENT 4: Response Generation Start
                $emitSSE('response_generation_start', [
                    'timestamp' => now()->toISOString(),
                ]);

                // Build enterprise-grade prompt for PA analysis
                $strategicPrompt = call_user_func($buildEnterprisePrompt, $query, $acts);

                // Call Claude via NatanChatService
                $result = $chatService->processQuery(
                    userQuery: $strategicPrompt,
                    user: $user,
                    conversationHistory: [],
                    manualPersonaId: 'strategic',
                    useRag: false, // We provide acts directly
                    useWebSearch: false,
                    referenceContext: ['acts' => call_user_func($formatActsForClaude, $acts)]
                );

                // EVENT 5: Cost Update
                $usage = $result['usage'] ?? null;
                if ($usage) {
                    $credits = $creditsService->calculateCreditsFromTokens(
                        $usage['input_tokens'] ?? 0,
                        $usage['output_tokens'] ?? 0
                    );

                    $emitSSE('cost_update', [
                        'input_tokens' => $usage['input_tokens'] ?? 0,
                        'output_tokens' => $usage['output_tokens'] ?? 0,
                        'total_tokens' => ($usage['input_tokens'] ?? 0) + ($usage['output_tokens'] ?? 0),
                        'credits_used' => $credits,
                        'cost_eur' => round($credits / 100, 2),
                        'timestamp' => now()->toISOString(),
                    ]);
                }

                // EVENT 6: Response Generation Complete
                $emitSSE('response_generation_complete', [
                    'response' => $result['response'] ?? '',
                    'sources_count' => count($result['sources'] ?? []),
                    'timestamp' => now()->toISOString(),
                ]);

                // EVENT 7: Done
                $emitSSE('done', [
                    'status' => 'completed',
                    'total_acts' => $acts->count(),
                    'avg_relevance' => round($avgRelevance * 100, 1),
                    'timestamp' => now()->toISOString(),
                ]);
            } catch (\Exception $e) {
                $emitSSE('error', [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                    'timestamp' => now()->toISOString(),
                ]);
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no', // Disable nginx buffering
            'Connection' => 'keep-alive',
        ]);
    }

    /**
     * Emit Server-Sent Event
     *
     * @param string $event Event name
     * @param array $data Event data
     * @return void
     */
    private function emitSSE(string $event, array $data): void {
        echo "event: {$event}\n";
        echo "data: " . json_encode($data) . "\n\n";
        ob_flush();
        flush();
    }

    /**
     * Build Enterprise-Grade Prompt for PA Analysis
     *
     * Generates strategic consultant-level prompts with quantitative data extraction.
     *
     * @param string $query User query
     * @param \Illuminate\Support\Collection $acts Acts to analyze
     * @return string
     */
    private function buildEnterprisePrompt(string $query, $acts): string {
        $actsCount = $acts->count();

        return <<<PROMPT
Sei un CONSULENTE STRATEGICO SENIOR specializzato in Pubblica Amministrazione italiana.

**QUERY DELL'UTENTE:**
"{$query}"

**DATASET DISPONIBILE:**
{$actsCount} atti amministrativi rilevanti.

**OBIETTIVO:**
Fornire un'analisi ENTERPRISE-GRADE con dati quantitativi estratti, non un indice generico.

**REQUISITI OBBLIGATORI:**

1. **ESTRAZIONE DATI QUANTITATIVI:**
   - Per OGNI atto rilevante, estrai:
     • Importi in EUR (esatti, non arrotondati)
     • Date precise (pubblicazione, scadenza, esecuzione)
     • Codici identificativi (CIG, CUP, numero determina/delibera)
     • Fornitori/Beneficiari (nome completo)
   - Crea tabelle formattate Markdown con questi dati
   - Calcola TOTALI, MEDIE, TREND temporali

2. **ANALISI FINANZIARIA REALE:**
   - Budget complessivo investito (somma importi)
   - Costo medio per intervento
   - Trend spesa mensile/annuale
   - Distribuzione budget per categoria
   - ROI stimato dove applicabile

3. **GAP ANALYSIS CONCRETA:**
   - Identificare atti con problemi: ritardi, budget sforato, compliance issues
   - Quantificare GAP: giorni di ritardo, EUR mancanti, % completamento
   - Lista prioritizzata azioni correttive

4. **PIANO IMPLEMENTAZIONE ESECUTIVO:**
   - Timeline precisa con milestone
   - Budget necessario per azioni correttive (stima in EUR)
   - Risorse umane richieste (FTE)
   - KPI misurabili

5. **FORMATO OUTPUT:**
   - Usa Markdown strutturato
   - Tabelle con dati reali (NO esempio fittizi)
   - Grafici ASCII dove utile
   - Sezioni: Executive Summary → Dati Estratti → Analisi Finanziaria → Gap Analysis → Piano Azione

**ESEMPIO TABELLA RICHIESTA:**

| Atto | Data | Importo EUR | CIG | Fornitore | Status |
|------|------|-------------|-----|-----------|--------|
| DET-2024-123 | 2024-10-15 | 250.000,00 | 98765XYZ | Acme SPA | Completato |
| DEL-2024-456 | 2024-09-20 | 180.500,00 | 12345ABC | Beta SRL | In corso |

**TOTALE INVESTITO:** 430.500,00 EUR

**NON FARE:**
❌ Indici generici senza dati
❌ Frasi vaghe tipo "si nota un trend positivo" (SPECIFICA: +12% YoY)
❌ Citazioni atti senza estrarre dati quantitativi
❌ Analisi qualitativa senza numeri

**FAI:**
✅ Estrai TUTTI i dati quantitativi disponibili
✅ Calcola totali, medie, percentuali
✅ Crea tabelle con dati reali
✅ Fornisci raccomandazioni con budget stimato in EUR

Procedi con l'analisi enterprise-grade.
PROMPT;
    }

    /**
     * Format acts for Claude context
     *
     * @param \Illuminate\Support\Collection $acts
     * @return array
     */
    private function formatActsForClaude($acts): array {
        return $acts->map(function ($item) {
            // Handle both old format (Egi object) and new format (array with 'act' and 'similarity')
            $act = is_array($item) ? ($item['act'] ?? $item) : $item;
            
            return [
                'id' => $act->id,
                'number' => $act->jsonMetadata['pa_act']['number'] ?? 'N/A',
                'title' => $act->title,
                'description' => $act->description ?? '',
                'date' => $act->jsonMetadata['pa_act']['date'] ?? null,
                'category' => $act->jsonMetadata['pa_act']['category'] ?? null,
                'url' => $act->jsonMetadata['pa_act']['url'] ?? null,
                'amount' => $act->jsonMetadata['pa_act']['amount'] ?? null,
                'cig' => $act->jsonMetadata['pa_act']['cig'] ?? null,
                'cup' => $act->jsonMetadata['pa_act']['cup'] ?? null,
            ];
        })->toArray();
    }
}
