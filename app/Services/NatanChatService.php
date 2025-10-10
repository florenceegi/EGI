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
 * GDPR-COMPLIANT: All AI processing is local (Ollama), no external APIs.
 *
 * @package App\Services
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - N.A.T.A.N. Chat AI)
 * @date 2025-10-10
 */
class NatanChatService
{
    protected OllamaService $ollama;
    protected PdfParserService $pdfParser;
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;

    public function __construct(
        OllamaService $ollama,
        PdfParserService $pdfParser,
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        $this->ollama = $ollama;
        $this->pdfParser = $pdfParser;
        $this->logger = $logger;
        $this->errorManager = $errorManager;
    }

    /**
     * Process user query and generate AI response
     *
     * WORKFLOW:
     * 1. Analyze user query intent
     * 2. Retrieve relevant acts (RAG)
     * 3. Extract text from relevant PDFs
     * 4. Build context prompt with act data
     * 5. Generate AI response using Ollama
     * 6. Return structured response
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
            // STEP 1: Retrieve relevant acts (RAG)
            $relevantActs = $this->retrieveRelevantActs($userQuery, $user);
            $logContext['relevant_acts_count'] = count($relevantActs);

            $this->logger->info('[NatanChatService] Retrieved relevant acts', $logContext);

            // STEP 2: Build context from acts
            $context = $this->buildContextFromActs($relevantActs);

            // STEP 3: Build AI prompt
            $prompt = $this->buildChatPrompt($userQuery, $context, $conversationHistory);

            // STEP 4: Call Ollama AI
            $aiResponse = $this->callOllamaChat($prompt);

            $this->logger->info('[NatanChatService] AI response generated', [
                ...$logContext,
                'response_length' => strlen($aiResponse)
            ]);

            // STEP 5: Build response with sources
            return [
                'success' => true,
                'response' => $aiResponse,
                'sources' => $this->formatSourcesForResponse($relevantActs),
                'relevant_acts_count' => count($relevantActs)
            ];

        } catch (\Throwable $e) {
            $this->logger->error('[NatanChatService] Query processing failed', [
                ...$logContext,
                'error' => $e->getMessage(),
                'exception' => get_class($e)
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
     * Retrieve relevant acts using keyword search and filters
     *
     * RAG (Retrieval Augmented Generation):
     * - Search by keywords in protocol_number, title, description
     * - Filter by doc_type if mentioned in query
     * - Order by relevance (recent acts first)
     * - Limit to top 5 most relevant acts
     *
     * @param string $query User query
     * @param User $user Current user
     * @return \Illuminate\Support\Collection
     */
    protected function retrieveRelevantActs(string $query, User $user): \Illuminate\Support\Collection
    {
        // Extract keywords from query (simple approach)
        $keywords = $this->extractKeywords($query);

        $this->logger->info('[NatanChatService] Extracted keywords', [
            'keywords' => $keywords,
            'query' => $query
        ]);

        // Detect doc_type in query
        $docType = $this->detectDocType($query);

        // Build query for relevant acts
        $actsQuery = Egi::whereHas('collection', function ($q) use ($user) {
            $q->where('creator_id', $user->id);
        })
        ->whereNotNull('pa_protocol_number')
        ->where('pa_anchored', true); // Only N.A.T.A.N. analyzed acts

        // Apply doc_type filter if detected
        if ($docType) {
            $actsQuery->where('pa_act_type', $docType);
        }

        // Apply keyword search if present
        if (!empty($keywords)) {
            $actsQuery->where(function ($q) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $q->orWhere('pa_protocol_number', 'LIKE', "%{$keyword}%")
                      ->orWhere('title', 'LIKE', "%{$keyword}%")
                      ->orWhere('description', 'LIKE', "%{$keyword}%");
                }
            });
        }

        // Order by relevance (recent first, then by protocol number desc)
        $acts = $actsQuery
            ->orderBy('pa_protocol_date', 'desc')
            ->orderBy('pa_protocol_number', 'desc')
            ->limit(5) // Top 5 most relevant
            ->get();

        return $acts;
    }

    /**
     * Extract keywords from user query
     *
     * Simple keyword extraction (can be improved with NLP)
     *
     * @param string $query
     * @return array
     */
    protected function extractKeywords(string $query): array
    {
        // Remove common Italian stop words
        $stopWords = ['il', 'lo', 'la', 'i', 'gli', 'le', 'di', 'a', 'da', 'in', 'con', 'su', 'per', 'tra', 'fra', 'mi', 'ti', 'si', 'ci', 'vi', 'e', 'o', 'ma', 'se', 'che', 'è', 'sono', 'del', 'della', 'dei', 'delle', 'un', 'una', 'uno', 'al', 'alla', 'agli', 'alle', 'dammi', 'quale', 'quali', 'quanto', 'quanti', 'come', 'dove', 'quando', 'perché'];

        // Extract words (minimum 3 chars)
        $words = preg_split('/\s+/', strtolower($query));
        $keywords = array_filter($words, function ($word) use ($stopWords) {
            return strlen($word) >= 3 && !in_array($word, $stopWords);
        });

        return array_values($keywords);
    }

    /**
     * Detect document type in query
     *
     * @param string $query
     * @return string|null
     */
    protected function detectDocType(string $query): ?string
    {
        $query = strtolower($query);

        if (preg_match('/\bdelibera|delibere\b/', $query)) {
            return 'delibera';
        }
        if (preg_match('/\bdetermina|determine\b/', $query)) {
            return 'determina';
        }
        if (preg_match('/\bordinanza|ordinanze\b/', $query)) {
            return 'ordinanza';
        }
        if (preg_match('/\bdecreto|decreti\b/', $query)) {
            return 'decreto';
        }

        return null;
    }

    /**
     * Build context from relevant acts
     *
     * @param \Illuminate\Support\Collection $acts
     * @return string
     */
    protected function buildContextFromActs(\Illuminate\Support\Collection $acts): string
    {
        if ($acts->isEmpty()) {
            return "Nessun atto rilevante trovato nel database.";
        }

        $context = "ATTI AMMINISTRATIVI RILEVANTI:\n\n";

        foreach ($acts as $index => $act) {
            $num = $index + 1;
            $context .= "--- ATTO {$num} ---\n";
            $context .= "Protocollo: {$act->pa_protocol_number}\n";
            $context .= "Data: {$act->pa_protocol_date->format('d/m/Y')}\n";
            $context .= "Tipo: " . ($act->pa_act_type ? __('pa_acts.doc_types.' . $act->pa_act_type . '.label') : 'N/A') . "\n";
            $context .= "Titolo: {$act->title}\n";
            
            if ($act->description) {
                $context .= "Descrizione: " . substr($act->description, 0, 500) . "...\n";
            }

            // Extract amount from metadata if present
            if (isset($act->jsonMetadata['amount'])) {
                $context .= "Importo: €" . number_format($act->jsonMetadata['amount'], 2, ',', '.') . "\n";
            }

            $context .= "\n";
        }

        return $context;
    }

    /**
     * Build chat prompt for Ollama
     *
     * @param string $userQuery
     * @param string $context
     * @param array $conversationHistory
     * @return string
     */
    protected function buildChatPrompt(string $userQuery, string $context, array $conversationHistory = []): string
    {
        $systemPrompt = <<<SYSTEM
Sei N.A.T.A.N. (Nodo di Analisi e Tracciamento Atti Notarizzati), un assistente AI specializzato nell'analisi di atti amministrativi della Pubblica Amministrazione italiana.

IL TUO RUOLO:
- Aiutare dirigenti e funzionari PA a comprendere e analizzare gli atti amministrativi
- Fornire riassunti chiari e concisi
- Rispondere a domande specifiche sugli atti
- Suggerire insight e analisi strategiche
- Essere preciso, professionale ma amichevole

REGOLE:
- Rispondi SEMPRE in italiano
- Usa i dati forniti nel CONTESTO
- Se non hai abbastanza informazioni, dillo chiaramente
- Non inventare dati
- Cita sempre i numeri di protocollo quando parli di atti specifici
- Usa un tono professionale ma accessibile

CONTESTO DISPONIBILE:
{$context}
SYSTEM;

        // Build conversation history
        $historyText = "";
        if (!empty($conversationHistory)) {
            $historyText = "\nSTORICO CONVERSAZIONE:\n";
            foreach ($conversationHistory as $msg) {
                $role = $msg['role'] === 'user' ? 'UTENTE' : 'N.A.T.A.N.';
                $historyText .= "{$role}: {$msg['content']}\n";
            }
            $historyText .= "\n";
        }

        $fullPrompt = $systemPrompt . "\n\n" . $historyText . "DOMANDA UTENTE:\n{$userQuery}\n\nRISPOSTA N.A.T.A.N.:";

        return $fullPrompt;
    }

    /**
     * Call Ollama API for chat completion
     *
     * @param string $prompt
     * @return string
     * @throws \Exception
     */
    protected function callOllamaChat(string $prompt): string
    {
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(90)
                ->retry(2, 1000)
                ->post(config('services.ollama.base_url') . '/api/generate', [
                    'model' => config('services.ollama.model'),
                    'prompt' => $prompt,
                    'stream' => false,
                    'options' => [
                        'temperature' => 0.7, // More creative for chat
                        'top_p' => 0.9,
                        'num_predict' => 1000 // Longer responses for chat
                    ]
                ]);

            if (!$response->successful()) {
                throw new \Exception("Ollama API error: {$response->status()}");
            }

            $result = $response->json();
            return trim($result['response'] ?? 'Nessuna risposta generata.');

        } catch (\Exception $e) {
            $this->logger->error('[NatanChatService] Ollama call failed', [
                'error' => $e->getMessage()
            ]);
            throw new \Exception("Errore nel servizio AI: {$e->getMessage()}");
        }
    }

    /**
     * Format sources for response
     *
     * @param \Illuminate\Support\Collection $acts
     * @return array
     */
    protected function formatSourcesForResponse(\Illuminate\Support\Collection $acts): array
    {
        return $acts->map(function ($act) {
            return [
                'id' => $act->id,
                'protocol_number' => $act->pa_protocol_number,
                'protocol_date' => $act->pa_protocol_date->format('d/m/Y'),
                'doc_type' => $act->pa_act_type,
                'title' => $act->title,
                'url' => route('pa.acts.show', $act->id)
            ];
        })->toArray();
    }

    /**
     * Get suggested questions for user
     *
     * @param User $user
     * @return array
     */
    public function getSuggestedQuestions(User $user): array
    {
        $actCount = Egi::whereHas('collection', fn($q) => $q->where('creator_id', $user->id))
            ->where('pa_anchored', true)
            ->count();

        if ($actCount === 0) {
            return [
                "Come funziona N.A.T.A.N.?",
                "Quali documenti posso caricare?",
                "Come viene garantita la sicurezza dei dati?"
            ];
        }

        return [
            "Riassumi l'ultimo atto caricato",
            "Quali atti riguardano lavori pubblici?",
            "Qual è la spesa totale delle delibere di quest'anno?",
            "Mostrami tutti gli atti di tipo determina",
            "Dammi suggerimenti per ottimizzare i processi amministrativi"
        ];
    }
}

