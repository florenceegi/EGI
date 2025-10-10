<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * Ollama AI Service - Local LLM Integration for N.A.T.A.N.
 *
 * This service integrates with Ollama (running locally on WSL2) to provide
 * AI-powered document analysis and metadata extraction for PA administrative acts.
 *
 * GDPR-COMPLIANT: All data processing happens locally on-premise, no external AI calls.
 *
 * @package App\Services
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - N.A.T.A.N. AI Integration)
 * @date 2025-10-10
 */
class OllamaService
{
    protected string $baseUrl;
    protected string $model;
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        
        // Ollama running on localhost (WSL2)
        $this->baseUrl = config('services.ollama.base_url', 'http://localhost:11434');
        $this->model = config('services.ollama.model', 'llama3.1:8b');
    }

    /**
     * Extract structured metadata from PA administrative act text
     *
     * Uses local Llama 3.1 model to parse document and extract:
     * - protocol_number (numero di protocollo)
     * - protocol_date (data protocollo)
     * - doc_type (delibera/determina/ordinanza/decreto/atto)
     * - object (oggetto dell'atto)
     * - amount (importo, if present)
     *
     * @param string $documentText Full text extracted from PDF
     * @return array Structured metadata
     * @throws \Exception
     * @privacy-safe All processing is local, no data sent to external services
     */
    public function extractPaActMetadata(string $documentText): array
    {
        $logContext = [
            'service' => 'OllamaService',
            'method' => 'extractPaActMetadata',
            'text_length' => strlen($documentText),
            'model' => $this->model
        ];

        $this->logger->info('[OllamaService] Starting metadata extraction', $logContext);

        try {
            // Truncate text if too long (Llama context limit ~4096 tokens)
            $maxChars = 8000; // ~2000 tokens
            if (strlen($documentText) > $maxChars) {
                $documentText = substr($documentText, 0, $maxChars) . '...';
                $this->logger->warning('[OllamaService] Document truncated for AI processing', [
                    ...$logContext,
                    'truncated_to' => $maxChars
                ]);
            }

            // Prepare prompt for structured extraction
            $prompt = $this->buildExtractionPrompt($documentText);

            // Call Ollama API
            $response = Http::timeout(60)
                ->retry(2, 500)
                ->post("{$this->baseUrl}/api/generate", [
                    'model' => $this->model,
                    'prompt' => $prompt,
                    'stream' => false,
                    'options' => [
                        'temperature' => 0.1, // Low temperature for factual extraction
                        'top_p' => 0.9,
                        'num_predict' => 500 // Max tokens to generate
                    ]
                ]);

            if (!$response->successful()) {
                throw new \Exception("Ollama API error: {$response->status()} - {$response->body()}");
            }

            $result = $response->json();
            $aiResponse = $result['response'] ?? '';

            $this->logger->info('[OllamaService] AI response received', [
                ...$logContext,
                'ai_response_length' => strlen($aiResponse)
            ]);

            // Parse AI response to structured data
            $metadata = $this->parseAiResponse($aiResponse);

            $this->logger->info('[OllamaService] Metadata extraction completed', [
                ...$logContext,
                'extracted_fields' => array_keys($metadata)
            ]);

            return $metadata;

        } catch (\Throwable $e) {
            $this->logger->error('[OllamaService] Metadata extraction failed', [
                ...$logContext,
                'error' => $e->getMessage(),
                'exception' => get_class($e)
            ]);

            $this->errorManager->handle('OLLAMA_EXTRACTION_FAILED', $logContext, $e);

            // Return empty metadata on failure (non-blocking)
            return [
                'protocol_number' => null,
                'protocol_date' => null,
                'doc_type' => null,
                'object' => null,
                'amount' => null,
                'ai_extraction_failed' => true,
                'ai_error' => $e->getMessage()
            ];
        }
    }

    /**
     * Build extraction prompt for PA act metadata
     *
     * @param string $documentText
     * @return string
     */
    protected function buildExtractionPrompt(string $documentText): string
    {
        return <<<PROMPT
Sei un assistente AI esperto nell'analisi di atti amministrativi della Pubblica Amministrazione italiana.

Analizza il seguente testo di un atto amministrativo ed estrai SOLO le seguenti informazioni in formato JSON:

1. **protocol_number**: Numero di protocollo (es: "12345/2024", "PG/2024/123456")
2. **protocol_date**: Data del protocollo in formato YYYY-MM-DD
3. **doc_type**: Tipo di documento (DEVE essere UNO di: "delibera", "determina", "ordinanza", "decreto", "atto")
4. **object**: Oggetto/descrizione breve dell'atto (max 200 caratteri)
5. **amount**: Importo in euro se presente nel testo (solo numero, es: "15000.50")

REGOLE:
- Rispondi SOLO con JSON valido, nessun testo aggiuntivo
- Se un campo non è presente, usa null
- Per doc_type, cerca parole chiave come "DELIBERA", "DETERMINA", "ORDINANZA", "DECRETO"
- Per protocol_date, cerca formati come "del 10/01/2024" o "10 gennaio 2024"
- Per amount, cerca "€", "euro", "importo", "spesa" seguito da numeri

TESTO DELL'ATTO:
---
{$documentText}
---

JSON RESPONSE:
PROMPT;
    }

    /**
     * Parse AI response to structured metadata
     *
     * @param string $aiResponse
     * @return array
     */
    protected function parseAiResponse(string $aiResponse): array
    {
        // Try to extract JSON from AI response (might have extra text)
        if (preg_match('/\{[^}]+\}/', $aiResponse, $matches)) {
            try {
                $json = json_decode($matches[0], true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    return [
                        'protocol_number' => $json['protocol_number'] ?? null,
                        'protocol_date' => $json['protocol_date'] ?? null,
                        'doc_type' => $this->normalizeDocType($json['doc_type'] ?? null),
                        'object' => $json['object'] ?? null,
                        'amount' => $this->normalizeAmount($json['amount'] ?? null),
                    ];
                }
            } catch (\Exception $e) {
                $this->logger->warning('[OllamaService] Failed to parse AI JSON response', [
                    'ai_response' => $aiResponse,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Fallback: return empty metadata
        return [
            'protocol_number' => null,
            'protocol_date' => null,
            'doc_type' => null,
            'object' => null,
            'amount' => null,
        ];
    }

    /**
     * Normalize document type to allowed values
     *
     * @param string|null $docType
     * @return string|null
     */
    protected function normalizeDocType(?string $docType): ?string
    {
        if (!$docType) {
            return null;
        }

        $docType = strtolower(trim($docType));
        $allowed = ['delibera', 'determina', 'ordinanza', 'decreto', 'atto'];

        return in_array($docType, $allowed) ? $docType : null;
    }

    /**
     * Normalize amount to float
     *
     * @param mixed $amount
     * @return float|null
     */
    protected function normalizeAmount($amount): ?float
    {
        if ($amount === null || $amount === '') {
            return null;
        }

        // Remove currency symbols and spaces
        $amount = preg_replace('/[€$\s]/', '', (string) $amount);
        
        // Convert comma to dot
        $amount = str_replace(',', '.', $amount);

        return is_numeric($amount) ? (float) $amount : null;
    }

    /**
     * Check if Ollama service is available
     *
     * @return bool
     */
    public function isAvailable(): bool
    {
        try {
            $response = Http::timeout(5)->get("{$this->baseUrl}/api/version");
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get available models
     *
     * @return array
     */
    public function getModels(): array
    {
        try {
            $response = Http::timeout(10)->get("{$this->baseUrl}/api/tags");
            if ($response->successful()) {
                return $response->json()['models'] ?? [];
            }
        } catch (\Exception $e) {
            $this->logger->error('[OllamaService] Failed to fetch models', [
                'error' => $e->getMessage()
            ]);
        }

        return [];
    }
}

