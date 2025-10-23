<?php

namespace App\Services;

use App\Config\NatanPersonas;
use Illuminate\Support\Facades\Log;

/**
 * PersonaSelector Service
 * 
 * Intelligent routing system to select the most appropriate N.A.T.A.N. persona
 * based on user query analysis.
 * 
 * Strategies:
 * 1. Keyword-based matching (fast, rule-based)
 * 2. AI-based semantic analysis (slower, more accurate) - FUTURE
 * 
 * Returns:
 * - persona_id: selected persona
 * - confidence: 0.0 to 1.0
 * - reasoning: explanation of choice
 * - alternatives: other viable personas with scores
 */
class PersonaSelector
{
    private const MIN_CONFIDENCE_THRESHOLD = 0.3;
    private const DEFAULT_PERSONA = 'strategic';

    /**
     * Select the best persona for a given query
     * 
     * @param string $query User's question/message
     * @param string|null $manualOverride Manual persona selection from UI
     * @param array $context Additional context (conversation history, etc.)
     * @return array ['persona_id', 'confidence', 'reasoning', 'alternatives', 'method']
     */
    public function selectPersona(
        string $query,
        ?string $manualOverride = null,
        array $context = []
    ): array {
        // MANUAL OVERRIDE: User explicitly selected a persona
        if ($manualOverride && NatanPersonas::isValid($manualOverride)) {
            return [
                'persona_id' => $manualOverride,
                'confidence' => 1.0,
                'reasoning' => 'Selezione manuale da parte dell\'utente',
                'alternatives' => [],
                'method' => 'manual'
            ];
        }

        // AUTO MODE: Intelligent routing

        // Strategy 1: Keyword-based matching (primary)
        $keywordResult = $this->keywordBasedSelection($query);

        // Strategy 2: AI-based semantic analysis (future enhancement)
        // $aiResult = $this->aiBasedSelection($query, $context);

        // For now, use keyword-based result
        $result = $keywordResult;

        // Fallback to default if confidence too low
        if ($result['confidence'] < self::MIN_CONFIDENCE_THRESHOLD) {
            Log::info('[PersonaSelector] Low confidence, using default persona', [
                'query' => substr($query, 0, 100),
                'detected' => $result['persona_id'],
                'confidence' => $result['confidence']
            ]);

            return [
                'persona_id' => self::DEFAULT_PERSONA,
                'confidence' => 0.5, // neutral confidence for default
                'reasoning' => 'Query generale - uso consulente strategico (default)',
                'alternatives' => $result['alternatives'] ?? [],
                'method' => 'default'
            ];
        }

        Log::info('[PersonaSelector] Persona selected', [
            'persona' => $result['persona_id'],
            'confidence' => $result['confidence'],
            'method' => $result['method'],
            'query' => substr($query, 0, 100)
        ]);

        return $result;
    }

    /**
     * Keyword-based persona selection
     * Fast, rule-based matching using keyword dictionaries
     * 
     * @param string $query
     * @return array
     */
    private function keywordBasedSelection(string $query): array
    {
        $queryLower = mb_strtolower($query);
        $words = $this->extractWords($queryLower);

        // Get keyword map from personas config
        $keywordMap = NatanPersonas::getKeywordMap();

        // Score each persona based on keyword matches
        $scores = [];
        foreach (NatanPersonas::getAll() as $personaId => $persona) {
            $scores[$personaId] = 0.0;
        }

        foreach ($words as $word) {
            if (isset($keywordMap[$word])) {
                foreach ($keywordMap[$word] as $personaId => $weight) {
                    $scores[$personaId] += $weight;
                }
            }
        }

        // Normalize scores (0.0 to 1.0)
        $maxScore = max($scores) ?: 1;
        $normalizedScores = [];
        foreach ($scores as $personaId => $score) {
            $normalizedScores[$personaId] = $score / $maxScore;
        }

        // Sort by score (descending)
        arsort($normalizedScores);

        // Get top persona
        $topPersonaId = array_key_first($normalizedScores);
        $topScore = $normalizedScores[$topPersonaId];

        // Get alternatives (personas with score >= 30% of top score)
        $alternatives = [];
        foreach ($normalizedScores as $personaId => $score) {
            if ($personaId !== $topPersonaId && $score >= ($topScore * 0.3)) {
                $alternatives[] = [
                    'persona_id' => $personaId,
                    'persona_name' => NatanPersonas::getName($personaId),
                    'confidence' => round($score, 2)
                ];
            }
        }

        // Limit alternatives to top 2
        $alternatives = array_slice($alternatives, 0, 2);

        // Generate reasoning
        $reasoning = $this->generateReasoning($topPersonaId, $topScore, $words, $keywordMap);

        return [
            'persona_id' => $topPersonaId,
            'confidence' => round($topScore, 2),
            'reasoning' => $reasoning,
            'alternatives' => $alternatives,
            'method' => 'keyword'
        ];
    }

    /**
     * AI-based persona selection (FUTURE IMPLEMENTATION)
     * Uses Claude to semantically analyze query and select best persona
     * 
     * @param string $query
     * @param array $context
     * @return array
     */
    private function aiBasedSelection(string $query, array $context): array
    {
        // TODO: Implement AI-based selection
        // - Call Anthropic API with lightweight prompt
        // - Ask Claude to classify query into persona categories
        // - Return persona_id + confidence + reasoning

        // For now, return placeholder
        return [
            'persona_id' => self::DEFAULT_PERSONA,
            'confidence' => 0.5,
            'reasoning' => 'AI-based selection not yet implemented',
            'alternatives' => [],
            'method' => 'ai'
        ];
    }

    /**
     * Extract meaningful words from query (remove stop words)
     * 
     * @param string $query
     * @return array
     */
    private function extractWords(string $query): array
    {
        // Common Italian stop words to ignore
        $stopWords = [
            'il',
            'lo',
            'la',
            'i',
            'gli',
            'le',
            'un',
            'uno',
            'una',
            'dei',
            'degli',
            'delle',
            'di',
            'a',
            'da',
            'in',
            'con',
            'su',
            'per',
            'tra',
            'fra',
            'e',
            'o',
            'ma',
            'se',
            'che',
            'chi',
            'cui',
            'mi',
            'ti',
            'ci',
            'vi',
            'si',
            'è',
            'sono',
            'ha',
            'hanno',
            'può',
            'possono',
            'come',
            'quando',
            'dove',
            'perché',
            'cosa',
            'quale'
        ];

        // Extract words (alphanumeric + accented chars)
        preg_match_all('/[\p{L}\p{N}]+/u', $query, $matches);
        $words = $matches[0] ?? [];

        // Filter out stop words and short words
        $words = array_filter($words, function ($word) use ($stopWords) {
            return strlen($word) >= 3 && !in_array($word, $stopWords, true);
        });

        return array_values($words);
    }

    /**
     * Generate human-readable reasoning for persona selection
     * 
     * @param string $personaId
     * @param float $confidence
     * @param array $words
     * @param array $keywordMap
     * @return string
     */
    private function generateReasoning(
        string $personaId,
        float $confidence,
        array $words,
        array $keywordMap
    ): string {
        $persona = NatanPersonas::get($personaId);

        // Find matched keywords
        $matchedKeywords = [];
        foreach ($words as $word) {
            if (isset($keywordMap[$word][$personaId])) {
                $matchedKeywords[] = $word;
            }
        }

        if (empty($matchedKeywords)) {
            return sprintf(
                'Query generica - selezionato %s (default)',
                $persona['name']
            );
        }

        // Build reasoning
        $keywordList = implode(', ', array_slice($matchedKeywords, 0, 3));

        return sprintf(
            'Rilevate parole chiave: "%s" → %s (confidenza: %d%%)',
            $keywordList,
            $persona['name'],
            (int)($confidence * 100)
        );
    }

    /**
     * Get suggestion message for alternative persona
     * Used when confidence is medium or when another persona might be better
     * 
     * @param array $alternatives
     * @return string|null
     */
    public function getSuggestionMessage(array $alternatives): ?string
    {
        if (empty($alternatives)) {
            return null;
        }

        $alt = $alternatives[0];

        if ($alt['confidence'] >= 0.6) {
            return sprintf(
                '💡 Questa domanda potrebbe essere più adatta per il <strong>%s</strong>. Vuoi cambiare?',
                $alt['persona_name']
            );
        }

        return null;
    }

    /**
     * Validate and sanitize manual persona selection
     * 
     * @param mixed $input
     * @return string|null
     */
    public function validateManualSelection($input): ?string
    {
        if (!is_string($input) || $input === 'auto') {
            return null;
        }

        return NatanPersonas::isValid($input) ? $input : null;
    }
}