<?php

namespace App\Services\AI\Features;

use App\Services\AI\Features\Contracts\AiFeatureInterface;
use App\Services\AI\Features\Handlers\TraitGenerationHandler;
use App\Services\AI\Features\Handlers\DescriptionGenerationHandler;
use App\Services\AI\Features\Handlers\CollectionStrategyHandler;
use Illuminate\Support\Facades\App;

/**
 * @package App\Services\AI\Features
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - AI Feature Orchestration)
 * @date 2025-11-04
 * @purpose Factory Pattern - Creates correct AI Feature Handler
 */
class AiFeatureFactory
{
    /**
     * Map feature_code → Handler Class
     */
    private const FEATURE_HANDLERS = [
        'ai_trait_generation' => TraitGenerationHandler::class,
        'ai_description_generation' => DescriptionGenerationHandler::class,
        'ai_collection_strategy' => CollectionStrategyHandler::class,
        // Add more as needed
    ];

    /**
     * Create handler for given feature code
     * 
     * @param string $featureCode
     * @return AiFeatureInterface
     * @throws \InvalidArgumentException
     */
    public function create(string $featureCode): AiFeatureInterface
    {
        if (!isset(self::FEATURE_HANDLERS[$featureCode])) {
            throw new \InvalidArgumentException(
                "No handler found for feature code: {$featureCode}. Available: " . 
                implode(', ', array_keys(self::FEATURE_HANDLERS))
            );
        }

        $handlerClass = self::FEATURE_HANDLERS[$featureCode];
        
        // Use Laravel DI container for dependency injection
        return App::make($handlerClass);
    }

    /**
     * Check if feature code has a handler
     * 
     * @param string $featureCode
     * @return bool
     */
    public function exists(string $featureCode): bool
    {
        return isset(self::FEATURE_HANDLERS[$featureCode]);
    }

    /**
     * Get all available feature codes
     * 
     * @return array
     */
    public function getAvailableFeatures(): array
    {
        return array_keys(self::FEATURE_HANDLERS);
    }
}

