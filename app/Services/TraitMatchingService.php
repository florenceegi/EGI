<?php

namespace App\Services;

use App\Models\TraitCategory;
use App\Models\TraitType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * TraitMatchingService
 *
 * Fuzzy matching service per trovare trait categories/types/values esistenti
 * che corrispondono alle proposte dell'AI.
 *
 * Utilizza:
 * - Levenshtein distance per similarità stringa
 * - Scoring basato su confidence threshold
 * - Normalizzazione testo (lowercase, trim, etc.)
 *
 * @package FlorenceEGI\Services
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0
 * @date 2025-10-21
 */
class TraitMatchingService
{
    /**
     * Threshold per considerare un match valido (0-100%)
     * 80% = buona corrispondenza
     */
    private const MATCH_THRESHOLD = 80;

    /**
     * Peso massimo per Levenshtein distance (più basso = migliore)
     */
    private const MAX_LEVENSHTEIN_DISTANCE = 3;

    public function __construct(
        private UltraLogManager $logger
    ) {}

    /**
     * Trova category esistente che matcha con il suggestion dell'AI
     *
     * @param string $categorySuggestion Es: "Materials", "Visual"
     * @param int $minScore Punteggio minimo per considerare match valido (default 80)
     * @return array{match: TraitCategory|null, score: int, is_exact: bool}
     */
    public function findMatchingCategory(string $categorySuggestion, int $minScore = self::MATCH_THRESHOLD): array
    {
        $this->logger->info("[TraitMatching] Searching category", [
            'suggestion' => $categorySuggestion,
            'min_score' => $minScore,
        ]);

        $normalized = $this->normalizeString($categorySuggestion);

        // Cerca prima exact match (case-insensitive)
        $exactMatch = TraitCategory::whereRaw('LOWER(name) = ?', [$normalized])->first();

        if ($exactMatch) {
            $this->logger->info("[TraitMatching] Category EXACT match", [
                'suggestion' => $categorySuggestion,
                'matched_id' => $exactMatch->id,
                'matched_name' => $exactMatch->name,
            ]);

            return [
                'match' => $exactMatch,
                'score' => 100,
                'is_exact' => true,
            ];
        }

        // Fuzzy search su tutte le categories
        $categories = TraitCategory::all();
        $bestMatch = null;
        $bestScore = 0;

        foreach ($categories as $category) {
            $score = $this->calculateSimilarityScore($normalized, $this->normalizeString($category->name));

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestMatch = $category;
            }
        }

        $isMatch = $bestScore >= $minScore;

        $this->logger->info("[TraitMatching] Category fuzzy search result", [
            'suggestion' => $categorySuggestion,
            'best_match' => $bestMatch?->name,
            'best_score' => $bestScore,
            'is_valid_match' => $isMatch,
        ]);

        return [
            'match' => $isMatch ? $bestMatch : null,
            'score' => $bestScore,
            'is_exact' => false,
        ];
    }

    /**
     * Trova trait type esistente che matcha con il suggestion dell'AI
     *
     * @param int $categoryId ID della category in cui cercare
     * @param string $typeSuggestion Es: "Primary Material", "Color Palette"
     * @param int $minScore Punteggio minimo per match valido
     * @return array{match: TraitType|null, score: int, is_exact: bool}
     */
    public function findMatchingType(int $categoryId, string $typeSuggestion, int $minScore = self::MATCH_THRESHOLD): array
    {
        $this->logger->info("[TraitMatching] Searching trait type", [
            'category_id' => $categoryId,
            'suggestion' => $typeSuggestion,
            'min_score' => $minScore,
        ]);

        $normalized = $this->normalizeString($typeSuggestion);

        // Exact match
        $exactMatch = TraitType::where('category_id', $categoryId)
            ->whereRaw('LOWER(name) = ?', [$normalized])
            ->first();

        if ($exactMatch) {
            $this->logger->info("[TraitMatching] Type EXACT match", [
                'suggestion' => $typeSuggestion,
                'matched_id' => $exactMatch->id,
                'matched_name' => $exactMatch->name,
            ]);

            return [
                'match' => $exactMatch,
                'score' => 100,
                'is_exact' => true,
            ];
        }

        // Fuzzy search
        $types = TraitType::where('category_id', $categoryId)->get();
        $bestMatch = null;
        $bestScore = 0;

        foreach ($types as $type) {
            $score = $this->calculateSimilarityScore($normalized, $this->normalizeString($type->name));

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestMatch = $type;
            }
        }

        $isMatch = $bestScore >= $minScore;

        $this->logger->info("[TraitMatching] Type fuzzy search result", [
            'suggestion' => $typeSuggestion,
            'best_match' => $bestMatch?->name,
            'best_score' => $bestScore,
            'is_valid_match' => $isMatch,
        ]);

        return [
            'match' => $isMatch ? $bestMatch : null,
            'score' => $bestScore,
            'is_exact' => false,
        ];
    }

    /**
     * Trova value esistente nell'array allowed_values del trait type
     *
     * @param TraitType $traitType
     * @param string $valueSuggestion Es: "Gold", "Bronze Patina"
     * @param int $minScore
     * @return array{match: string|null, score: int, is_exact: bool}
     */
    public function findMatchingValue(TraitType $traitType, string $valueSuggestion, int $minScore = self::MATCH_THRESHOLD): array
    {
        $this->logger->info("[TraitMatching] Searching trait value", [
            'trait_type_id' => $traitType->id,
            'trait_type_name' => $traitType->name,
            'suggestion' => $valueSuggestion,
            'min_score' => $minScore,
        ]);

        // Se il trait type non ha allowed_values, il valore è sempre valido
        if (!$traitType->hasPredefinedValues()) {
            $this->logger->info("[TraitMatching] Trait type has no predefined values, accepting suggestion", [
                'suggestion' => $valueSuggestion,
            ]);

            return [
                'match' => $valueSuggestion,
                'score' => 100,
                'is_exact' => true,
            ];
        }

        $normalized = $this->normalizeString($valueSuggestion);
        $allowedValues = $traitType->allowed_values ?? [];

        // Exact match
        foreach ($allowedValues as $allowedValue) {
            if ($this->normalizeString($allowedValue) === $normalized) {
                $this->logger->info("[TraitMatching] Value EXACT match", [
                    'suggestion' => $valueSuggestion,
                    'matched_value' => $allowedValue,
                ]);

                return [
                    'match' => $allowedValue,
                    'score' => 100,
                    'is_exact' => true,
                ];
            }
        }

        // Fuzzy search
        $bestMatch = null;
        $bestScore = 0;

        foreach ($allowedValues as $allowedValue) {
            $score = $this->calculateSimilarityScore($normalized, $this->normalizeString($allowedValue));

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestMatch = $allowedValue;
            }
        }

        $isMatch = $bestScore >= $minScore;

        $this->logger->info("[TraitMatching] Value fuzzy search result", [
            'suggestion' => $valueSuggestion,
            'best_match' => $bestMatch,
            'best_score' => $bestScore,
            'is_valid_match' => $isMatch,
        ]);

        return [
            'match' => $isMatch ? $bestMatch : null,
            'score' => $bestScore,
            'is_exact' => false,
        ];
    }

    /**
     * Processa TUTTI i trait suggestions dall'AI in una volta
     *
     * @param array $aiTraits Array di traits dall'AI (output di AnthropicService::analyzeImageForTraits)
     * @return array Array di match results con categoria matched, nuove proposte, etc.
     */
    public function matchAllTraits(array $aiTraits): array
    {
        $this->logger->info("[TraitMatching] Starting bulk trait matching", [
            'total_traits' => count($aiTraits),
        ]);

        $results = [
            'exact_matches' => [],      // Traits che hanno match esatto
            'fuzzy_matches' => [],      // Traits con match fuzzy (score >= threshold)
            'new_categories' => [],     // Nuove categories da creare
            'new_types' => [],          // Nuovi types da creare
            'new_values' => [],         // Nuovi values da aggiungere
        ];

        foreach ($aiTraits as $aiTrait) {
            $categoryResult = $this->findMatchingCategory($aiTrait['category_suggestion']);

            // CASO 1: Category esiste
            if ($categoryResult['match']) {
                $typeResult = $this->findMatchingType(
                    $categoryResult['match']->id,
                    $aiTrait['type_suggestion']
                );

                // CASO 1.1: Category + Type esistono
                if ($typeResult['match']) {
                    $valueResult = $this->findMatchingValue(
                        $typeResult['match'],
                        $aiTrait['value_suggestion']
                    );

                    // CASO 1.1.1: Category + Type + Value esistono → EXACT/FUZZY MATCH
                    if ($valueResult['match']) {
                        $matchType = ($categoryResult['is_exact'] && $typeResult['is_exact'] && $valueResult['is_exact'])
                            ? 'exact_matches'
                            : 'fuzzy_matches';

                        $results[$matchType][] = [
                            'ai_suggestion' => $aiTrait,
                            'matched_category' => $categoryResult['match'],
                            'matched_type' => $typeResult['match'],
                            'matched_value' => $valueResult['match'],
                            'scores' => [
                                'category' => $categoryResult['score'],
                                'type' => $typeResult['score'],
                                'value' => $valueResult['score'],
                                'average' => ($categoryResult['score'] + $typeResult['score'] + $valueResult['score']) / 3,
                            ],
                        ];
                    }
                    // CASO 1.1.2: Category + Type esistono, Value NUOVO
                    else {
                        $results['new_values'][] = [
                            'ai_suggestion' => $aiTrait,
                            'matched_category' => $categoryResult['match'],
                            'matched_type' => $typeResult['match'],
                            'new_value' => $aiTrait['value_suggestion'],
                            'new_display_value' => $aiTrait['display_value_suggestion'] ?? $aiTrait['value_suggestion'],
                        ];
                    }
                }
                // CASO 1.2: Category esiste, Type NUOVO
                else {
                    $results['new_types'][] = [
                        'ai_suggestion' => $aiTrait,
                        'matched_category' => $categoryResult['match'],
                        'new_type' => $aiTrait['type_suggestion'],
                        'new_value' => $aiTrait['value_suggestion'],
                        'new_display_value' => $aiTrait['display_value_suggestion'] ?? $aiTrait['value_suggestion'],
                    ];
                }
            }
            // CASO 2: Category NUOVA
            else {
                $results['new_categories'][] = [
                    'ai_suggestion' => $aiTrait,
                    'new_category' => $aiTrait['category_suggestion'],
                    'new_type' => $aiTrait['type_suggestion'],
                    'new_value' => $aiTrait['value_suggestion'],
                    'new_display_value' => $aiTrait['display_value_suggestion'] ?? $aiTrait['value_suggestion'],
                ];
            }
        }

        $this->logger->info("[TraitMatching] Bulk matching completed", [
            'exact_matches' => count($results['exact_matches']),
            'fuzzy_matches' => count($results['fuzzy_matches']),
            'new_categories' => count($results['new_categories']),
            'new_types' => count($results['new_types']),
            'new_values' => count($results['new_values']),
        ]);

        return $results;
    }

    /**
     * Calcola similarity score tra due stringhe (0-100)
     *
     * Combina:
     * - Levenshtein distance (errori di battitura)
     * - Substring match (abbreviazioni)
     * - Word overlap (ordine parole diverso)
     *
     * @param string $str1
     * @param string $str2
     * @return int Score 0-100
     */
    private function calculateSimilarityScore(string $str1, string $str2): int
    {
        // Exact match = 100
        if ($str1 === $str2) {
            return 100;
        }

        $maxLength = max(strlen($str1), strlen($str2));

        // Levenshtein distance (peso 60%)
        $levDistance = levenshtein($str1, $str2);
        $levScore = max(0, 100 - ($levDistance / $maxLength * 100)) * 0.6;

        // Substring match (peso 20%)
        $substringScore = 0;
        if (str_contains($str1, $str2) || str_contains($str2, $str1)) {
            $substringScore = 20;
        }

        // Word overlap (peso 20%)
        $words1 = explode(' ', $str1);
        $words2 = explode(' ', $str2);
        $commonWords = count(array_intersect($words1, $words2));
        $totalWords = max(count($words1), count($words2));
        $wordOverlapScore = ($commonWords / $totalWords) * 20;

        $finalScore = (int) round($levScore + $substringScore + $wordOverlapScore);

        return min(100, max(0, $finalScore));
    }

    /**
     * Normalizza stringa per confronto
     *
     * @param string $str
     * @return string
     */
    private function normalizeString(string $str): string
    {
        return strtolower(trim($str));
    }
}

