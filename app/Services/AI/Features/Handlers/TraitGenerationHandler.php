<?php

namespace App\Services\AI\Features\Handlers;

use App\Models\Egi;
use App\Models\User;
use App\Services\AI\Features\Contracts\AiFeatureInterface;
use App\Services\AI\Features\DTOs\AiFeatureResult;
use App\Services\AiTraitGenerationService;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * @package App\Services\AI\Features\Handlers
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - AI Feature Orchestration)
 * @date 2025-11-04
 * @purpose Handler for AI Trait Generation (wraps AiTraitGenerationService)
 */
class TraitGenerationHandler implements AiFeatureInterface
{
    private ?string $validationError = null;

    public function __construct(
        private AiTraitGenerationService $traitService,
        private UltraLogManager $logger
    ) {}

    public function getFeatureCode(): string
    {
        return 'ai_trait_generation';
    }

    public function validate(Egi $egi, User $user, array $params): bool
    {
        // Check if EGI is owned by user
        if ($egi->user_id !== $user->id) {
            $this->validationError = 'You can only generate traits for your own EGIs';
            return false;
        }

        // Check if EGI is already minted (traits are immutable after mint)
        if (!is_null($egi->token_EGI)) {
            $this->validationError = 'Cannot generate traits for minted EGI (traits are immutable)';
            return false;
        }

        // Check if EGI has image
        if (empty($egi->image_url)) {
            $this->validationError = 'EGI must have an image for AI trait generation';
            return false;
        }

        // Validate requested_count parameter
        $requestedCount = $params['requested_count'] ?? 5;
        if ($requestedCount < 1 || $requestedCount > 10) {
            $this->validationError = 'Requested trait count must be between 1 and 10';
            return false;
        }

        return true;
    }

    public function execute(Egi $egi, User $user, array $params): AiFeatureResult
    {
        try {
            $requestedCount = $params['requested_count'] ?? 5;
            $ipAddress = $params['ip_address'] ?? request()->ip();
            $userAgent = $params['user_agent'] ?? request()->userAgent();

            $this->logger->info('[TraitGenerationHandler] Executing trait generation', [
                'egi_id' => $egi->id,
                'user_id' => $user->id,
                'requested_count' => $requestedCount,
            ]);

            // Call existing service
            $generation = $this->traitService->requestTraitGeneration(
                egiId: $egi->id,
                userId: $user->id,
                requestedCount: $requestedCount,
                ipAddress: $ipAddress,
                userAgent: $userAgent
            );

            return AiFeatureResult::success(
                message: "AI trait generation completed. {$generation->proposed_traits_count} traits proposed.",
                data: [
                    'generation_id' => $generation->id,
                    'proposed_count' => $generation->proposed_traits_count,
                    'status' => $generation->status,
                ],
                featureCode: $this->getFeatureCode(),
                egiId: $egi->id,
                userId: $user->id,
                metadata: [
                    'requested_count' => $requestedCount,
                    'actual_proposed' => $generation->proposed_traits_count,
                ]
            );

        } catch (\Exception $e) {
            $this->logger->error('[TraitGenerationHandler] Execution failed', [
                'egi_id' => $egi->id,
                'error' => $e->getMessage(),
            ]);

            return AiFeatureResult::failure(
                message: 'Trait generation failed: ' . $e->getMessage(),
                featureCode: $this->getFeatureCode(),
                egiId: $egi->id,
                userId: $user->id
            );
        }
    }

    public function getValidationError(): ?string
    {
        return $this->validationError;
    }
}

