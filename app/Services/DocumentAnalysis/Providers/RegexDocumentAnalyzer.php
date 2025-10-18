<?php

namespace App\Services\DocumentAnalysis\Providers;

use App\Contracts\DocumentAnalysisInterface;

/**
 * Regex Document Analyzer
 *
 * @package App\Services\DocumentAnalysis\Providers
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - N.A.T.A.N.)
 * @date 2025-10-18
 * @purpose Simple pattern-matching document analyzer
 *          No AI, no API costs, fast but limited accuracy
 *          Fallback provider when AI services unavailable
 */
class RegexDocumentAnalyzer implements DocumentAnalysisInterface
{
    /**
     * Analyze document using regex patterns
     *
     * @param string $text Full document text
     * @param string $documentType Document type hint
     * @return array Extracted metadata
     */
    public function analyzeDocument(string $text, string $documentType = 'pa_act'): array
    {
        $info = [
            'act_type' => 'atto',
            'protocol' => null,
            'protocol_date' => null,
            'title' => '',
            'description' => '',
            'entities' => [],
            'confidence' => 0.6, // Lower confidence for regex
        ];

        // Extract act type
        if (preg_match('/DELIBERA(?:ZIONE)?/i', $text)) {
            $info['act_type'] = 'delibera';
            $info['confidence'] = 0.8;
        } elseif (preg_match('/DETERMINA(?:ZIONE)?/i', $text)) {
            $info['act_type'] = 'determina';
            $info['confidence'] = 0.8;
        } elseif (preg_match('/ORDINANZA/i', $text)) {
            $info['act_type'] = 'ordinanza';
            $info['confidence'] = 0.8;
        } elseif (preg_match('/DECRETO/i', $text)) {
            $info['act_type'] = 'decreto';
            $info['confidence'] = 0.8;
        }

        // Extract protocol number (pattern: N. XXX/YYYY or n. XXX del YYYY)
        if (preg_match('/(?:N\.|n\.)\s*(\d+)(?:\/|del\s+)(\d{4})/i', $text, $matches)) {
            $info['protocol'] = $matches[1] . '/' . $matches[2];
            $info['protocol_date'] = $matches[2] . '-01-01'; // Default to year start
        }

        // Extract title (OGGETTO: ...)
        if (preg_match('/OGGETTO:\s*(.+?)(?:\n|$)/i', $text, $matches)) {
            $info['title'] = trim($matches[1]);
            $info['description'] = $info['title']; // Use as description too
        }

        // Fallback title from first line
        if (empty($info['title'])) {
            $lines = explode("\n", $text);
            $info['title'] = trim($lines[0] ?? '');
            $info['description'] = trim($lines[1] ?? '');
        }

        return $info;
    }

    /**
     * Health check (regex always available)
     *
     * @return bool Always true
     */
    public function healthCheck(): bool
    {
        return true;
    }

    /**
     * Get provider name
     *
     * @return string
     */
    public function getProviderName(): string
    {
        return 'regex';
    }

    /**
     * Get provider version
     *
     * @return string
     */
    public function getProviderVersion(): string
    {
        return '1.0.0';
    }

    /**
     * Check if document type is supported
     *
     * @param string $documentType Document type
     * @return bool
     */
    public function supportsDocumentType(string $documentType): bool
    {
        return in_array($documentType, ['pa_act', 'contract', 'invoice']);
    }
}
