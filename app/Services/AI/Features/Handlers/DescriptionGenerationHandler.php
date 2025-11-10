<?php

namespace App\Services\AI\Features\Handlers;

use App\Models\Egi;
use App\Models\User;
use App\Services\AI\Features\Contracts\AiFeatureInterface;
use App\Services\AI\Features\DTOs\AiFeatureResult;
use App\Services\EgiPreMintManagementService;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * @package App\Services\AI\Features\Handlers
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - AI Feature Orchestration)
 * @date 2025-11-04
 * @purpose Handler for AI Description Generation (wraps EgiPreMintManagementService)
 */
class DescriptionGenerationHandler implements AiFeatureInterface
{
    private ?string $validationError = null;

    public function __construct(
        private EgiPreMintManagementService $preMintService,
        private UltraLogManager $logger
    ) {}

    public function getFeatureCode(): string
    {
        return 'ai_description_generation';
    }

    public function validate(Egi $egi, User $user, array $params): bool
    {
        // Check if EGI is owned by user
        if ($egi->user_id !== $user->id) {
            $this->validationError = 'You can only generate descriptions for your own EGIs';
            return false;
        }

        // Check if EGI is already minted (description is immutable after mint)
        if (!is_null($egi->token_EGI)) {
            $this->validationError = 'Cannot modify description for minted EGI (immutable)';
            return false;
        }

        // Check if EGI has image (required for AI Vision analysis)
        $imageUrl = $egi->main_image_url ?? $egi->original_image_url ?? $egi->image_url;

        if (empty($imageUrl)) {
            $this->validationError = 'EGI must have an image for AI description generation';
            return false;
        }

        return true;
    }

    public function execute(Egi $egi, User $user, array $params): AiFeatureResult
    {
        try {
            $guidelines = $params['guidelines'] ?? null;
            $ipAddress = $params['ip_address'] ?? request()->ip();
            $userAgent = $params['user_agent'] ?? request()->userAgent();

            $this->logger->info('[DescriptionGenerationHandler] Executing description generation', [
                'egi_id' => $egi->id,
                'user_id' => $user->id,
                'has_guidelines' => !empty($guidelines),
            ]);

            $requestMetadata = [
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'request_timestamp' => now()->toIso8601String(),
            ];

            // Call existing service
            $result = $this->preMintService->generateDescription(
                egi: $egi,
                user: $user,
                requestMetadata: $requestMetadata,
                creatorGuidelines: $guidelines
            );

            return AiFeatureResult::success(
                message: 'AI description generated successfully',
                data: [
                    'description' => $result['description'],
                    'word_count' => str_word_count($result['description']),
                ],
                featureCode: $this->getFeatureCode(),
                egiId: $egi->id,
                userId: $user->id,
                metadata: [
                    'had_guidelines' => !empty($guidelines),
                    'previous_description' => $result['previous_description'] ?? null,
                ]
            );

        } catch (\Exception $e) {
            $this->logger->error('[DescriptionGenerationHandler] Execution failed', [
                'egi_id' => $egi->id,
                'error' => $e->getMessage(),
            ]);

            return AiFeatureResult::failure(
                message: 'Description generation failed: ' . $e->getMessage(),
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

