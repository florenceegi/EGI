<?php

namespace App\Contracts;

/**
 * Document Analysis Interface
 *
 * @package App\Contracts
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - N.A.T.A.N. Provider-Agnostic Architecture)
 * @date 2025-10-18
 * @purpose Unified interface for document analysis providers (Claude, AISURU, OpenAI, Ollama)
 *          Enables easy provider switching without code changes
 */
interface DocumentAnalysisInterface
{
    /**
     * Analyze document text and extract structured metadata
     *
     * @param string $text Full document text to analyze
     * @param string $documentType Type hint for analysis (pa_act, contract, invoice, etc.)
     * @return array Structured metadata array with keys:
     *               - act_type: string (delibera, determina, ordinanza, decreto, atto)
     *               - protocol: string|null (protocol number, e.g., "123/2024")
     *               - protocol_date: string|null (Y-m-d format)
     *               - title: string (document title/subject)
     *               - description: string (short description)
     *               - entities: array (extracted entities: names, places, dates)
     *               - confidence: float (0-1, analysis confidence score)
     * @throws \App\Exceptions\DocumentAnalysisException When analysis fails
     */
    public function analyzeDocument(string $text, string $documentType = 'pa_act'): array;

    /**
     * Check if provider is available and healthy
     *
     * @return bool True if provider is operational, false otherwise
     */
    public function healthCheck(): bool;

    /**
     * Get provider unique identifier
     *
     * @return string Provider name (claude, aisuru, openai, ollama)
     */
    public function getProviderName(): string;

    /**
     * Get provider version/model information
     *
     * @return string Model version or configuration identifier
     */
    public function getProviderVersion(): string;

    /**
     * Check if provider supports given document type
     *
     * @param string $documentType Document type to check
     * @return bool True if supported, false otherwise
     */
    public function supportsDocumentType(string $documentType): bool;
}
