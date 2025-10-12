<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * Service per interazione con Anthropic Claude API
 * 
 * GDPR Compliance:
 * - Processa SOLO dati pubblici (metadati PA)
 * - Non invia MAI: firme digitali, nominativi, file_path, IP
 * - Logging audit completo di cosa viene inviato
 */
class AnthropicService
{
    private UltraLogManager $logger;
    private string $apiKey;
    private string $baseUrl;
    private string $model;
    private int $timeout;

    public function __construct(UltraLogManager $logger)
    {
        $this->logger = $logger;
        $this->apiKey = config('services.anthropic.api_key');
        $this->baseUrl = config('services.anthropic.base_url', 'https://api.anthropic.com');
        $this->model = config('services.anthropic.model', 'claude-3-5-sonnet-20241022');
        $this->timeout = config('services.anthropic.timeout', 60);

        if (empty($this->apiKey)) {
            throw new RuntimeException('Anthropic API key not configured in services.anthropic.api_key');
        }
    }

    /**
     * Verifica se il servizio Anthropic è disponibile
     */
    public function isAvailable(): bool
    {
        try {
            // Semplice test di connessione
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
            ])->timeout(5)->get($this->baseUrl);

            return $response->successful() || $response->status() === 404; // 404 è OK, significa che l'endpoint base risponde
        } catch (\Exception $e) {
            $this->logger->error('[AnthropicService] Availability check failed', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Invia un messaggio a Claude e ottiene la risposta
     * 
     * @param string $userMessage Il messaggio dell'utente
     * @param array $context Contesto aggiuntivo (metadati pubblici)
     * @param array $conversationHistory Storia della conversazione
     * @return string La risposta di Claude
     */
    public function chat(string $userMessage, array $context = [], array $conversationHistory = []): string
    {
        try {
            $this->logger->info('[AnthropicService] Chat request initiated', [
                'user_message_length' => strlen($userMessage),
                'context_keys' => array_keys($context),
                'history_count' => count($conversationHistory),
            ]);

            // Costruisci il system prompt con il contesto
            $systemPrompt = $this->buildSystemPrompt($context);

            // Costruisci i messaggi per l'API
            $messages = $this->buildMessages($userMessage, $conversationHistory);

            // Chiamata API
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])->timeout($this->timeout)->post($this->baseUrl . '/v1/messages', [
                'model' => $this->model,
                'max_tokens' => 4096,
                'system' => $systemPrompt,
                'messages' => $messages,
                'temperature' => 0.7,
            ]);

            if (!$response->successful()) {
                $this->logger->error('[AnthropicService] API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                throw new RuntimeException('Anthropic API error: ' . $response->body());
            }

            $data = $response->json();
            $assistantMessage = $data['content'][0]['text'] ?? '';

            $this->logger->info('[AnthropicService] Chat response received', [
                'response_length' => strlen($assistantMessage),
                'usage' => $data['usage'] ?? null,
            ]);

            return $assistantMessage;
        } catch (\Exception $e) {
            $this->logger->error('[AnthropicService] Chat error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new RuntimeException('Errore comunicazione con Claude: ' . $e->getMessage());
        }
    }

    /**
     * Costruisce il system prompt con il contesto dei dati pubblici
     */
    private function buildSystemPrompt(array $context): string
    {
        $basePrompt = <<<PROMPT
Sei N.A.T.A.N. (Neuro-Analytical Text Analysis Network), un assistente AI specializzato nell'analisi di atti della Pubblica Amministrazione.

COMPETENZE:
- Analisi e interpretazione di atti amministrativi (delibere, determine, ordinanze)
- Ricerca intelligente basata su metadati (protocollo, data, tipo, importo)
- Analisi strategica e insight su processi PA
- Supporto alla governance e compliance

REGOLE GDPR:
- Processi SOLO dati pubblici (metadati estratti e salvati)
- NON hai accesso a: firme digitali, nominativi personali, documenti originali
- Tutti i dati che vedi sono già stati validati e sanitizzati

STILE:
- Professionale ma accessibile
- Risposte concise e actionable
- Usa emoji con parsimonia (solo per enfasi)
- Cita sempre i riferimenti (protocollo, data)

PROMPT;

        // Aggiungi contesto se presente
        if (!empty($context['acts_summary'])) {
            $basePrompt .= "\n\nCONTESTO ATTUALE:\n" . $context['acts_summary'];
        }

        if (!empty($context['stats'])) {
            $basePrompt .= "\n\nSTATISTICHE SISTEMA:\n" . json_encode($context['stats'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }

        return $basePrompt;
    }

    /**
     * Costruisce l'array di messaggi per l'API
     */
    private function buildMessages(string $userMessage, array $conversationHistory): array
    {
        $messages = [];

        // Aggiungi storia conversazione (max ultimi 10 messaggi)
        foreach (array_slice($conversationHistory, -10) as $msg) {
            $messages[] = [
                'role' => $msg['role'], // 'user' o 'assistant'
                'content' => $msg['content'],
            ];
        }

        // Aggiungi messaggio corrente
        $messages[] = [
            'role' => 'user',
            'content' => $userMessage,
        ];

        return $messages;
    }

    /**
     * Estrae metadati da un atto PA usando Claude
     * 
     * @param string $pdfText Il testo estratto dal PDF
     * @return array Metadati estratti
     */
    public function extractMetadata(string $pdfText): array
    {
        try {
            $this->logger->info('[AnthropicService] Extracting metadata from PDF text', [
                'text_length' => strlen($pdfText),
            ]);

            $prompt = <<<PROMPT
Analizza il seguente testo di un atto della Pubblica Amministrazione ed estrai i seguenti metadati in formato JSON:

{
  "protocol_number": "numero protocollo (es: 12345/2024)",
  "date": "data atto in formato YYYY-MM-DD",
  "type": "tipologia (DELIBERA/DETERMINA/ORDINANZA/DECRETO/ALTRO)",
  "title": "oggetto/titolo dell'atto",
  "amount": "importo in euro se presente (solo numero, es: 15000.50)"
}

Se un campo non è presente, usa null.

TESTO ATTO:
---
$pdfText
---

Rispondi SOLO con il JSON, senza commenti aggiuntivi.
PROMPT;

            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])->timeout($this->timeout)->post($this->baseUrl . '/v1/messages', [
                'model' => $this->model,
                'max_tokens' => 1024,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.3, // Bassa temperatura per estrazione precisa
            ]);

            if (!$response->successful()) {
                throw new RuntimeException('Anthropic API error: ' . $response->body());
            }

            $data = $response->json();
            $jsonText = $data['content'][0]['text'] ?? '';

            // Estrai JSON dalla risposta (potrebbe contenere markdown)
            if (preg_match('/\{[\s\S]*\}/', $jsonText, $matches)) {
                $metadata = json_decode($matches[0], true);
                
                if (json_last_error() === JSON_ERROR_NONE) {
                    $this->logger->info('[AnthropicService] Metadata successfully extracted', [
                        'metadata' => $metadata,
                    ]);
                    return $metadata;
                }
            }

            throw new RuntimeException('Unable to parse JSON from Claude response');
        } catch (\Exception $e) {
            $this->logger->error('[AnthropicService] Metadata extraction failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}

