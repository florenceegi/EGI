<?php

namespace App\Services\WebSearch;

use Ultra\UltraLogManager\UltraLogManager;

/**
 * Web Search Auto-Detector - Intelligent routing for web search activation
 *
 * Analyzes user queries to automatically determine when web search should be enabled.
 * Uses keyword matching + heuristics to decide if external sources are needed.
 *
 * DETECTION STRATEGIES:
 * 1. Keyword-based: Query contains "best practices", "benchmark", "normativa"
 * 2. Question-type: "Come fanno in...", "Quali sono le migliori...", "Funding per..."
 * 3. Persona-based: Some personas always benefit from web (Strategic, Legal, Financial)
 * 4. Intent recognition: Comparison, regulation check, funding search
 *
 * CONFIDENCE SCORING:
 * - High (>80%): Auto-enable web search
 * - Medium (50-80%): Suggest to user
 * - Low (<50%): Keep internal only
 *
 * @package App\Services\WebSearch
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - N.A.T.A.N. Web Search Auto-Detection)
 * @date 2025-10-26
 * @purpose Intelligent web search activation based on query analysis
 */
class WebSearchAutoDetector {
    protected UltraLogManager $logger;

    public function __construct(UltraLogManager $logger) {
        $this->logger = $logger;
    }

    /**
     * Detect if web search should be enabled for this query
     *
     * Returns confidence score + reasoning for transparency.
     *
     * @param string $query User query
     * @param string|null $personaId Selected persona
     * @param array $context Additional context (conversation history, etc.)
     * @return array ['should_enable' => bool, 'confidence' => float, 'reasoning' => string, 'triggers' => array]
     */
    public function shouldEnableWebSearch(
        string $query,
        ?string $personaId = null,
        array $context = []
    ): array {
        $query = mb_strtolower($query, 'UTF-8');
        $triggers = [];
        $score = 0.0;

        // TRIGGER 1: Benchmarking keywords (high confidence)
        $benchmarkingKeywords = [
            'best practice',
            'migliori pratiche',
            'benchmark',
            'confronta',
            'compare',
            'come fanno',
            'altri comuni',
            'europa',
            'internazionale',
            'case study',
        ];

        foreach ($benchmarkingKeywords as $keyword) {
            if (str_contains($query, $keyword)) {
                $score += 0.3;
                $triggers[] = "benchmarking_keyword:{$keyword}";
            }
        }

        // TRIGGER 2: Normative/Legal keywords (high confidence)
        $legalKeywords = [
            'normativa',
            'legge',
            'decreto',
            'sentenza',
            'giurisprudenza',
            'compliance',
            'gdpr',
            'aggiornamento normativo',
            'nuova normativa',
            'regolamento',
        ];

        foreach ($legalKeywords as $keyword) {
            if (str_contains($query, $keyword)) {
                $score += 0.3;
                $triggers[] = "legal_keyword:{$keyword}";
            }
        }

        // TRIGGER 3: Funding keywords (high confidence)
        $fundingKeywords = [
            'funding',
            'finanziamento',
            'bando',
            'contributo',
            'pnrr',
            'fondi europei',
            'opportunità',
            'grant',
            'incentivi',
            'agevolazioni',
        ];

        foreach ($fundingKeywords as $keyword) {
            if (str_contains($query, $keyword)) {
                $score += 0.4; // High weight (funding is always external)
                $triggers[] = "funding_keyword:{$keyword}";
            }
        }

        // TRIGGER 4: Question patterns (medium confidence)
        $questionPatterns = [
            'come possiamo' => 0.2, // Strategy question
            'quali sono' => 0.15, // Information gathering
            'dove troviamo' => 0.25, // External resource search
            'chi ha fatto' => 0.2, // Case study search
            'come migliorare' => 0.2, // Optimization (benefits from benchmarks)
        ];

        foreach ($questionPatterns as $pattern => $weight) {
            if (str_contains($query, $pattern)) {
                $score += $weight;
                $triggers[] = "question_pattern:{$pattern}";
            }
        }

        // TRIGGER 5: Persona-based boosting
        $personaBoosts = [
            'strategic' => 0.2, // Strategic often needs benchmarks
            'legal' => 0.25, // Legal needs updated regulations
            'financial' => 0.3, // Financial needs funding opportunities
            'communication' => 0.1, // Communication sometimes needs PR case studies
            'urban_social' => 0.15, // Urban planning benefits from case studies
            'technical' => 0.0, // Technical usually internal-focused
        ];

        if ($personaId && isset($personaBoosts[$personaId])) {
            $score += $personaBoosts[$personaId];
            $triggers[] = "persona_boost:{$personaId}";
        }

        // TRIGGER 6: Explicit user intent
        $explicitIntents = [
            'cerca sul web',
            'search web',
            'internet',
            'online',
            'fonti esterne',
        ];

        foreach ($explicitIntents as $intent) {
            if (str_contains($query, $intent)) {
                $score = 1.0; // Override: explicit request
                $triggers[] = "explicit_intent:{$intent}";
                break;
            }
        }

        // Cap score at 1.0
        $score = min($score, 1.0);

        // Decision threshold
        $shouldEnable = $score >= 0.5; // 50% confidence threshold

        // Build reasoning
        $reasoning = $this->buildReasoning($score, $triggers);

        // Log decision
        $this->logger->info('[WebSearchAutoDetector] Auto-detection completed', [
            'query_length' => strlen($query),
            'persona_id' => $personaId,
            'should_enable' => $shouldEnable,
            'confidence' => $score,
            'triggers_count' => count($triggers),
        ]);

        return [
            'should_enable' => $shouldEnable,
            'confidence' => round($score, 2),
            'reasoning' => $reasoning,
            'triggers' => $triggers,
        ];
    }

    /**
     * Build human-readable reasoning for decision
     */
    protected function buildReasoning(float $score, array $triggers): string {
        if (empty($triggers)) {
            return 'No indicators for external sources detected.';
        }

        $reasons = [];

        // Extract trigger types
        foreach ($triggers as $trigger) {
            [$type, $value] = explode(':', $trigger, 2);

            switch ($type) {
                case 'benchmarking_keyword':
                    $reasons[] = "Query mentions benchmarking or comparisons";
                    break;
                case 'legal_keyword':
                    $reasons[] = "Regulatory or legal information requested";
                    break;
                case 'funding_keyword':
                    $reasons[] = "Funding opportunities search detected";
                    break;
                case 'question_pattern':
                    $reasons[] = "Question pattern suggests external research";
                    break;
                case 'persona_boost':
                    $reasons[] = "Persona \"{$value}\" typically benefits from web sources";
                    break;
                case 'explicit_intent':
                    $reasons[] = "User explicitly requested web search";
                    break;
            }
        }

        // Deduplicate
        $reasons = array_unique($reasons);

        return implode('. ', $reasons) . ".";
    }
}
