<?php

namespace App\Services\AI\Features\Contracts;

use App\Models\Egi;
use App\Models\User;
use App\Services\AI\Features\DTOs\AiFeatureResult;

/**
 * @package App\Services\AI\Features\Contracts
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - AI Feature Orchestration)
 * @date 2025-11-04
 * @purpose Contract for all AI Feature Handlers (Strategy Pattern)
 */
interface AiFeatureInterface
{
    /**
     * Get feature code from ai_feature_pricing table
     * 
     * @return string
     */
    public function getFeatureCode(): string;

    /**
     * Validate if feature can be executed for given EGI
     * 
     * @param Egi $egi
     * @param User $user
     * @param array $params
     * @return bool
     */
    public function validate(Egi $egi, User $user, array $params): bool;

    /**
     * Execute the AI feature
     * 
     * @param Egi $egi
     * @param User $user
     * @param array $params
     * @return AiFeatureResult
     * @throws \Exception
     */
    public function execute(Egi $egi, User $user, array $params): AiFeatureResult;

    /**
     * Get validation error message (if validate() returned false)
     * 
     * @return string|null
     */
    public function getValidationError(): ?string;
}

