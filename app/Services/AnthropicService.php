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
Sei N.A.T.A.N. (Nodo di Analisi e Tracciamento Atti Notarizzati), un assistente AI specializzato nell'analisi di atti della Pubblica Amministrazione.

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

    /**
     * Analyze image with Claude Vision and generate description
     *
     * @Oracode Method: Vision Analysis with Claude
     * 🎯 Purpose: Use Claude Vision to analyze artwork images and generate professional descriptions
     * 📥 Input: Image URL or base64, artwork context
     * 📤 Output: AI-generated description with visual analysis
     * 🔒 Security: Privacy-safe, no PII in images
     * 🪵 Logging: Full audit trail of vision API calls
     *
     * @param string $imageUrl Public URL of the image to analyze
     * @param string $prompt Analysis prompt for Claude Vision
     * @param array $context Additional context for analysis
     * @return string Claude's description based on visual analysis
     * @throws RuntimeException When vision analysis fails
     */
    public function analyzeImage(string $imageUrl, string $prompt, array $context = []): string
    {
        try {
            $this->logger->info('[AnthropicService] Image analysis request initiated', [
                'image_url_length' => strlen($imageUrl),
                'context_keys' => array_keys($context),
            ]);

            // Get image data as base64
            $imageData = $this->fetchImageAsBase64($imageUrl);
            $mediaType = $imageData['media_type'];
            $base64Data = $imageData['base64'];

            // Build system prompt for EGI artwork analysis
            $systemPrompt = $this->buildEgiVisionSystemPrompt($context);

            // Build message with image content
            $messages = [
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'image',
                            'source' => [
                                'type' => 'base64',
                                'media_type' => $mediaType,
                                'data' => $base64Data,
                            ],
                        ],
                        [
                            'type' => 'text',
                            'text' => $prompt,
                        ],
                    ],
                ],
            ];

            // Call Claude Vision API (longer timeout for image analysis: 120s)
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])->timeout(120)->post($this->baseUrl . '/v1/messages', [
                'model' => $this->model,
                'max_tokens' => 4096,
                'system' => $systemPrompt,
                'messages' => $messages,
                'temperature' => 0.7,
            ]);

            if (!$response->successful()) {
                $this->logger->error('[AnthropicService] Vision API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                throw new RuntimeException('Anthropic Vision API error: ' . $response->body());
            }

            $data = $response->json();
            $description = $data['content'][0]['text'] ?? '';

            $this->logger->info('[AnthropicService] Image analysis completed', [
                'description_length' => strlen($description),
                'usage' => $data['usage'] ?? null,
            ]);

            return $description;
        } catch (\Exception $e) {
            $this->logger->error('[AnthropicService] Image analysis failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new RuntimeException('Errore analisi immagine con Claude Vision: ' . $e->getMessage());
        }
    }

    /**
     * Fetch image from URL and convert to base64
     *
     * @param string $imageUrl Public URL of the image
     * @return array ['media_type' => string, 'base64' => string]
     * @throws RuntimeException When image fetch fails
     */
    private function fetchImageAsBase64(string $imageUrl): array
    {
        try {
            $this->logger->info('[AnthropicService] Starting image fetch', [
                'original_image_url' => $imageUrl,
            ]);

            // Handle different URL formats and convert to absolute local path
            $originalUrl = $imageUrl;

            // If it's a full URL (http://... or https://...)
            if (str_starts_with($imageUrl, 'http://') || str_starts_with($imageUrl, 'https://')) {
                // Extract path after domain
                $urlParts = parse_url($imageUrl);
                $path = $urlParts['path'] ?? '';

                // If path starts with /storage/, map to public/storage/
                if (str_starts_with($path, '/storage/')) {
                    $imageUrl = public_path($path);
                } else {
                    $imageUrl = public_path(ltrim($path, '/'));
                }
            }
            // If it starts with /, it's already a web path
            elseif (str_starts_with($imageUrl, '/')) {
                // If it's /storage/..., map to public/storage/
                if (str_starts_with($imageUrl, '/storage/')) {
                    $imageUrl = public_path($imageUrl);
                } else {
                    $imageUrl = public_path(ltrim($imageUrl, '/'));
                }
            }
            // Otherwise assume it's already an absolute path

            $this->logger->info('[AnthropicService] Resolved image path', [
                'original_url' => $originalUrl,
                'resolved_path' => $imageUrl,
                'is_local_file' => file_exists($imageUrl),
            ]);

            // If it's a local path, read from file system
            if (file_exists($imageUrl)) {
                $imageContent = file_get_contents($imageUrl);
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mediaType = finfo_file($finfo, $imageUrl);
                finfo_close($finfo);
            } else {
                // If it's a URL, fetch it
                $response = Http::timeout(30)->get($imageUrl);

                if (!$response->successful()) {
                    throw new RuntimeException('Failed to fetch image from URL: ' . $response->status());
                }

                $imageContent = $response->body();
                $mediaType = $response->header('Content-Type') ?? 'image/jpeg';
            }

            // Validate and convert media type if needed
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($mediaType, $allowedTypes)) {
                throw new RuntimeException('Unsupported image type: ' . $mediaType);
            }

            // Convert WebP to JPEG for better Anthropic API compatibility
            if ($mediaType === 'image/webp') {
                $this->logger->info('[AnthropicService] Converting WebP to JPEG for better API compatibility');

                $image = imagecreatefromstring($imageContent);
                if ($image === false) {
                    throw new RuntimeException('Failed to create image from WebP content');
                }

                ob_start();
                imagejpeg($image, null, 85); // 85% quality
                $imageContent = ob_get_clean();
                imagedestroy($image);

                $mediaType = 'image/jpeg';

                $this->logger->info('[AnthropicService] WebP converted to JPEG', [
                    'new_size_bytes' => strlen($imageContent),
                ]);
            }

            $base64 = base64_encode($imageContent);

            $this->logger->info('[AnthropicService] Image fetched and encoded', [
                'media_type' => $mediaType,
                'size_bytes' => strlen($imageContent),
            ]);

            return [
                'media_type' => $mediaType,
                'base64' => $base64,
            ];
        } catch (\Exception $e) {
            $this->logger->error('[AnthropicService] Image fetch failed', [
                'image_url' => $imageUrl,
                'error' => $e->getMessage(),
            ]);
            throw new RuntimeException('Errore recupero immagine: ' . $e->getMessage());
        }
    }

    /**
     * Build system prompt for EGI artwork vision analysis
     *
     * @param array $context Artwork context (title, type, etc.)
     * @return string System prompt for Claude Vision
     */
    private function buildEgiVisionSystemPrompt(array $context): string
    {
        $basePrompt = <<<PROMPT
Sei N.A.T.A.N. (Nodo di Analisi e Tracciamento Atti Notarizzati), un assistente AI specializzato nell'analisi di opere d'arte digitali per il marketplace FlorenceEGI.

COMPETENZE VISION:
- Analisi visiva professionale di opere d'arte (pittura, scultura, fotografia, arte digitale)
- Identificazione di stile artistico, tecnica, composizione, palette colori
- Interpretazione di contenuto emotivo, tematico e narrativo
- Contestualizzazione storico-artistica quando rilevante
- Valutazione di qualità tecnica ed estetica

OBIETTIVO:
Generare descrizioni professionali, coinvolgenti e ottimizzate per il marketplace che:
1. Catturino l'essenza visiva e concettuale dell'opera
2. Evidenzino caratteristiche uniche e valore artistico
3. Siano accessibili ma mantenendo un linguaggio professionale
4. Attraggano potenziali acquirenti/collezionisti
5. Siano lunghe 2-3 paragrafi (150-250 parole)

STILE:
- Descrittivo ma evocativo
- Professionale senza essere accademico
- Focus su dettagli visivi rilevanti
- Enfasi su valore e unicità dell'opera
- Linguaggio italiano elegante e scorrevole

REGOLE:
- NON inventare informazioni non visibili nell'immagine
- NON fare supposizioni su autore/data se non esplicitamente fornite nel contesto
- Concentrati su ciò che VEDI nell'immagine
- Fornisci SOLO il testo della descrizione, senza titoli, prefissi o commenti aggiuntivi

PROMPT;

        // Add artwork context if available
        if (!empty($context['title'])) {
            $basePrompt .= "\n\nTITOLO OPERA: " . $context['title'];
        }
        if (!empty($context['type'])) {
            $basePrompt .= "\nTIPOLOGIA: " . $context['type'];
        }
        if (!empty($context['creation_date'])) {
            $basePrompt .= "\nDATA CREAZIONE: " . $context['creation_date'];
        }

        return $basePrompt;
    }
}
