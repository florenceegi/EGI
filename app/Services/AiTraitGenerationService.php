<?php

namespace App\Services;

use App\Models\Egi;
use App\Models\AiTraitGeneration;
use App\Models\AiTraitProposal;
use App\Services\AnthropicService;
use App\Services\TraitMatchingService;
use App\Services\TraitCreationService;
use App\Services\TraitTranslationService;
use App\Services\Gdpr\AuditLogService;
use Illuminate\Support\Facades\DB;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * AiTraitGenerationService
 *
 * ORCHESTRATORE PRINCIPALE per il sistema AI Traits Generation
 *
 * Coordina:
 * 1. AnthropicService → Claude Vision analizza immagine
 * 2. TraitMatchingService → Fuzzy match con esistenti
 * 3. Salva AiTraitGeneration + AiTraitProposals nel DB
 * 4. Su approvazione utente → TraitCreationService crea traits
 * 5. TraitTranslationService → Traduzioni IT
 * 6. AuditLogService → GDPR compliance
 *
 * @package FlorenceEGI\Services
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0
 * @date 2025-10-21
 */
class AiTraitGenerationService
{
    public function __construct(
        private AnthropicService $anthropicService,
        private TraitMatchingService $matchingService,
        private TraitCreationService $creationService,
        private TraitTranslationService $translationService,
        private AuditLogService $auditService,
        private UltraLogManager $logger,
        private ErrorManagerInterface $errorManager
    ) {}

    /**
     * STEP 1: Richiedi AI di analizzare immagine e generare traits
     *
     * Crea AiTraitGeneration (status=pending) → chiama Claude Vision →
     * fa fuzzy matching → salva proposals → marca analyzed
     *
     * @param int $egiId
     * @param int $userId
     * @param int $requestedCount (1-10)
     * @param string $ipAddress
     * @param string $userAgent
     * @return AiTraitGeneration
     * @throws \Exception
     */
    public function requestTraitGeneration(
        int $egiId,
        int $userId,
        int $requestedCount,
        string $ipAddress,
        string $userAgent
    ): AiTraitGeneration {
        $this->logger->info("[AiTraitGen] Starting trait generation request", [
            'egi_id' => $egiId,
            'user_id' => $userId,
            'requested_count' => $requestedCount,
        ]);

        // Validate requested count
        if ($requestedCount < 1 || $requestedCount > 10) {
            throw new \InvalidArgumentException("Requested count must be between 1 and 10");
        }

        try {
            return DB::transaction(function () use ($egiId, $userId, $requestedCount, $ipAddress, $userAgent) {
                // Load EGI
                $egi = Egi::findOrFail($egiId);

                // Get image URL (use optimized 'card' variant 400x400 for AI)
                $imageUrl = $egi->main_image_url; // Already optimized 400x400 WebP variant

                if (empty($imageUrl)) {
                    // Fallback to original if card variant not available
                    $imageUrl = $egi->original_image_url;
                }

                if (empty($imageUrl)) {
                    throw new \Exception("EGI has no image for AI analysis. Please upload an image first.");
                }

                // Create generation session (status=pending)
                $generation = AiTraitGeneration::create([
                    'egi_id' => $egiId,
                    'user_id' => $userId,
                    'requested_count' => $requestedCount,
                    'image_url' => $imageUrl,
                    'status' => 'pending',
                    'ip_address' => $ipAddress,
                    'user_agent' => $userAgent,
                ]);

                // GDPR Audit
                $user = \App\Models\User::findOrFail($userId);
                $this->auditService->logUserAction(
                    $user,
                    'ai_trait_generation_requested',
                    [
                        'generation_id' => $generation->id,
                        'egi_id' => $egiId,
                        'requested_count' => $requestedCount,
                        'ip_address' => $ipAddress,
                        'user_agent' => $userAgent,
                    ],
                    \App\Enums\Gdpr\GdprActivityCategory::AI_PROCESSING
                );

                try {
                    // Call Claude Vision
                    $aiResponse = $this->anthropicService->analyzeImageForTraits(
                        $imageUrl,
                        [
                            'title' => $egi->title,
                            'type' => $egi->type ?? 'artwork',
                            'creation_date' => $egi->creation_date?->format('Y-m-d'),
                        ],
                        $requestedCount
                    );

                    // Save AI raw response
                    $generation->update([
                        'ai_raw_response' => $aiResponse,
                        'total_confidence' => $aiResponse['total_confidence'] ?? null,
                        'analysis_notes' => $aiResponse['analysis_notes'] ?? null,
                    ]);

                    // Fuzzy matching
                    $matchResults = $this->matchingService->matchAllTraits($aiResponse['identified_traits']);

                    // Save proposals
                    $this->saveProposals($generation, $matchResults);

                    // Update generation stats
                    $generation->update([
                        'exact_matches_count' => count($matchResults['exact_matches']),
                        'fuzzy_matches_count' => count($matchResults['fuzzy_matches']),
                        'new_proposals_count' => count($matchResults['new_categories']) +
                            count($matchResults['new_types']) +
                            count($matchResults['new_values']),
                    ]);

                    // Mark as analyzed
                    $generation->markAsAnalyzed();

                    $this->logger->info("[AiTraitGen] Generation completed successfully", [
                        'generation_id' => $generation->id,
                        'exact_matches' => $generation->exact_matches_count,
                        'fuzzy_matches' => $generation->fuzzy_matches_count,
                        'new_proposals' => $generation->new_proposals_count,
                    ]);

                    return $generation->fresh(['proposals']);
                } catch (\Exception $e) {
                    // Mark as failed
                    $generation->markAsFailed('ai_trait_generation_failed', $e->getMessage());

                    $this->logger->error("[AiTraitGen] Generation failed", [
                        'generation_id' => $generation->id,
                        'error' => $e->getMessage(),
                    ]);

                    throw $e;
                }
            });
        } catch (\Exception $e) {
            $this->logger->error("[AiTraitGen] Request failed", [
                'egi_id' => $egiId,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * STEP 2: Utente approva/rifiuta proposte
     *
     * @param int $generationId
     * @param int $userId
     * @param array $decisions ['proposal_id' => 'approved'|'rejected'|'modified', ...]
     * @param string $ipAddress
     * @param string $userAgent
     * @return AiTraitGeneration
     * @throws \Exception
     */
    public function reviewProposals(
        int $generationId,
        int $userId,
        array $decisions,
        string $ipAddress,
        string $userAgent
    ): AiTraitGeneration {
        $this->logger->info("[AiTraitGen] Starting proposal review", [
            'generation_id' => $generationId,
            'user_id' => $userId,
            'decisions_count' => count($decisions),
        ]);

        try {
            return DB::transaction(function () use ($generationId, $userId, $decisions, $ipAddress, $userAgent) {
                $generation = AiTraitGeneration::with('proposals')->findOrFail($generationId);

                // Authorize: solo creator può revieware
                if ($generation->egi->user_id !== $userId) {
                    throw new \Exception("Unauthorized: only EGI creator can review proposals");
                }

                // Apply decisions
                foreach ($decisions as $proposalId => $decision) {
                    $proposal = $generation->proposals()->find($proposalId);

                    if (!$proposal) {
                        continue;
                    }

                    switch ($decision['action']) {
                        case 'approved':
                            $proposal->markAsApproved();
                            break;

                        case 'rejected':
                            $proposal->markAsRejected();
                            break;

                        case 'modified':
                            $proposal->markAsModified($decision['modifications'] ?? []);
                            break;
                    }
                }

                // Update generation status
                $approvedCount = $generation->approvedProposals()->count();
                $totalCount = $generation->proposals()->count();

                if ($approvedCount === 0) {
                    $generation->update(['status' => 'rejected', 'reviewed_at' => now()]);
                } elseif ($approvedCount === $totalCount) {
                    $generation->markAsApproved();
                } else {
                    $generation->update(['status' => 'partial', 'reviewed_at' => now()]);
                }

                // GDPR Audit
                $user = \App\Models\User::findOrFail($userId);
                $this->auditService->logUserAction(
                    $user,
                    'ai_trait_proposals_reviewed',
                    [
                        'generation_id' => $generationId,
                        'approved' => $approvedCount,
                        'total' => $totalCount,
                        'ip_address' => $ipAddress,
                        'user_agent' => $userAgent,
                    ],
                    \App\Enums\Gdpr\GdprActivityCategory::AI_PROCESSING
                );

                $this->logger->info("[AiTraitGen] Review completed", [
                    'generation_id' => $generationId,
                    'status' => $generation->status,
                    'approved' => $approvedCount,
                ]);

                return $generation->fresh(['proposals']);
            });
        } catch (\Exception $e) {
            $this->logger->error("[AiTraitGen] Review failed", [
                'generation_id' => $generationId,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * STEP 3: Applica traits approvati all'EGI
     *
     * Crea categorie/tipi/valori nuovi + EgiTraits + traduzioni IT
     *
     * @param int $generationId
     * @param int $userId
     * @param string $ipAddress
     * @param string $userAgent
     * @return array{generation: AiTraitGeneration, created_traits: array}
     * @throws \Exception
     */
    public function applyTraits(
        int $generationId,
        int $userId,
        string $ipAddress,
        string $userAgent
    ): array {
        $this->logger->info("[AiTraitGen] Starting trait application", [
            'generation_id' => $generationId,
            'user_id' => $userId,
        ]);

        try {
            return DB::transaction(function () use ($generationId, $userId, $ipAddress, $userAgent) {
                $generation = AiTraitGeneration::with(['proposals', 'egi'])->findOrFail($generationId);

                // Authorize
                if ($generation->egi->user_id !== $userId) {
                    throw new \Exception("Unauthorized: only EGI creator can apply traits");
                }

                $approvedProposals = $generation->approvedProposals()->get();
                $createdTraits = [];

                foreach ($approvedProposals as $proposal) {
                    try {
                        // Create trait (category + type + value + EgiTrait)
                        $result = $this->creationService->createTraitFromProposal(
                            $generation->egi_id,
                            $proposal->toArray()
                        );

                        // Update proposal with created references
                        $proposal->markAsApplied(
                            $result['category']->id,
                            $result['type']->id,
                            $result['egiTrait']->id
                        );

                        // Add translations
                        $this->translationService->addCompleteTraitTranslations([
                            'category' => [
                                'key' => $result['category']->name,
                                'translation' => $proposal->category_suggestion,
                            ],
                            'type' => [
                                'key' => $result['type']->name,
                                'translation' => $proposal->type_suggestion,
                            ],
                            'value' => [
                                'key' => $result['egiTrait']->value,
                                'translation' => $proposal->display_value_suggestion ?? $proposal->value_suggestion,
                            ],
                        ]);

                        $createdTraits[] = $result['egiTrait'];

                        $this->logger->info("[AiTraitGen] Trait applied successfully", [
                            'proposal_id' => $proposal->id,
                            'egi_trait_id' => $result['egiTrait']->id,
                        ]);
                    } catch (\Exception $e) {
                        $this->logger->error("[AiTraitGen] Failed to apply trait", [
                            'proposal_id' => $proposal->id,
                            'error' => $e->getMessage(),
                        ]);

                        // Continue con altri traits
                        continue;
                    }
                }

                // Mark generation as applied
                $generation->markAsApplied();

                // GDPR Audit
                $user = \App\Models\User::findOrFail($userId);
                $this->auditService->logUserAction(
                    $user,
                    'ai_traits_applied_to_egi',
                    [
                        'generation_id' => $generationId,
                        'egi_id' => $generation->egi_id,
                        'traits_created' => count($createdTraits),
                        'ip_address' => $ipAddress,
                        'user_agent' => $userAgent,
                    ],
                    \App\Enums\Gdpr\GdprActivityCategory::EGI_TRAIT_MANAGEMENT
                );

                $this->logger->info("[AiTraitGen] Application completed", [
                    'generation_id' => $generationId,
                    'traits_created' => count($createdTraits),
                ]);

                return [
                    'generation' => $generation->fresh(['proposals']),
                    'created_traits' => $createdTraits,
                ];
            });
        } catch (\Exception $e) {
            $this->logger->error("[AiTraitGen] Application failed", [
                'generation_id' => $generationId,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Salva proposals nel DB dai match results
     *
     * @param AiTraitGeneration $generation
     * @param array $matchResults
     * @return void
     */
    private function saveProposals(AiTraitGeneration $generation, array $matchResults): void
    {
        $sortOrder = 0;

        // Exact matches
        foreach ($matchResults['exact_matches'] as $match) {
            AiTraitProposal::create([
                'generation_id' => $generation->id,
                'category_suggestion' => $match['ai_suggestion']['category_suggestion'],
                'type_suggestion' => $match['ai_suggestion']['type_suggestion'],
                'value_suggestion' => $match['ai_suggestion']['value_suggestion'],
                'display_value_suggestion' => $match['ai_suggestion']['display_value_suggestion'] ?? null,
                'confidence' => $match['ai_suggestion']['confidence'],
                'reasoning' => $match['ai_suggestion']['reasoning'] ?? null,
                'match_type' => 'exact',
                'matched_category_id' => $match['matched_category']->id,
                'matched_type_id' => $match['matched_type']->id,
                'matched_value' => $match['matched_value'],
                'category_match_score' => $match['scores']['category'],
                'type_match_score' => $match['scores']['type'],
                'value_match_score' => $match['scores']['value'],
                'sort_order' => $sortOrder++,
            ]);
        }

        // Fuzzy matches
        foreach ($matchResults['fuzzy_matches'] as $match) {
            AiTraitProposal::create([
                'generation_id' => $generation->id,
                'category_suggestion' => $match['ai_suggestion']['category_suggestion'],
                'type_suggestion' => $match['ai_suggestion']['type_suggestion'],
                'value_suggestion' => $match['ai_suggestion']['value_suggestion'],
                'display_value_suggestion' => $match['ai_suggestion']['display_value_suggestion'] ?? null,
                'confidence' => $match['ai_suggestion']['confidence'],
                'reasoning' => $match['ai_suggestion']['reasoning'] ?? null,
                'match_type' => 'fuzzy',
                'matched_category_id' => $match['matched_category']->id,
                'matched_type_id' => $match['matched_type']->id,
                'matched_value' => $match['matched_value'],
                'category_match_score' => $match['scores']['category'],
                'type_match_score' => $match['scores']['type'],
                'value_match_score' => $match['scores']['value'],
                'sort_order' => $sortOrder++,
            ]);
        }

        // New values
        foreach ($matchResults['new_values'] as $match) {
            AiTraitProposal::create([
                'generation_id' => $generation->id,
                'category_suggestion' => $match['ai_suggestion']['category_suggestion'],
                'type_suggestion' => $match['ai_suggestion']['type_suggestion'],
                'value_suggestion' => $match['ai_suggestion']['value_suggestion'],
                'display_value_suggestion' => $match['ai_suggestion']['display_value_suggestion'] ?? null,
                'confidence' => $match['ai_suggestion']['confidence'],
                'reasoning' => $match['ai_suggestion']['reasoning'] ?? null,
                'match_type' => 'new_value',
                'matched_category_id' => $match['matched_category']->id,
                'matched_type_id' => $match['matched_type']->id,
                'sort_order' => $sortOrder++,
            ]);
        }

        // New types
        foreach ($matchResults['new_types'] as $match) {
            AiTraitProposal::create([
                'generation_id' => $generation->id,
                'category_suggestion' => $match['ai_suggestion']['category_suggestion'],
                'type_suggestion' => $match['ai_suggestion']['type_suggestion'],
                'value_suggestion' => $match['ai_suggestion']['value_suggestion'],
                'display_value_suggestion' => $match['ai_suggestion']['display_value_suggestion'] ?? null,
                'confidence' => $match['ai_suggestion']['confidence'],
                'reasoning' => $match['ai_suggestion']['reasoning'] ?? null,
                'match_type' => 'new_type',
                'matched_category_id' => $match['matched_category']->id,
                'sort_order' => $sortOrder++,
            ]);
        }

        // New categories
        foreach ($matchResults['new_categories'] as $match) {
            AiTraitProposal::create([
                'generation_id' => $generation->id,
                'category_suggestion' => $match['ai_suggestion']['category_suggestion'],
                'type_suggestion' => $match['ai_suggestion']['type_suggestion'],
                'value_suggestion' => $match['ai_suggestion']['value_suggestion'],
                'display_value_suggestion' => $match['ai_suggestion']['display_value_suggestion'] ?? null,
                'confidence' => $match['ai_suggestion']['confidence'],
                'reasoning' => $match['ai_suggestion']['reasoning'] ?? null,
                'match_type' => 'new_category',
                'sort_order' => $sortOrder++,
            ]);
        }
    }
}