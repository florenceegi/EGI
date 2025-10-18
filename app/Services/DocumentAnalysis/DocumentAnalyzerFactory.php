<?php

namespace App\Services\DocumentAnalysis;

use App\Contracts\DocumentAnalysisInterface;
use App\Services\DocumentAnalysis\Providers\RegexDocumentAnalyzer;
use App\Services\DocumentAnalysis\Providers\AisuruDocumentAnalyzer;
use InvalidArgumentException;

/**
 * Document Analyzer Factory
 *
 * @package App\Services\DocumentAnalysis
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - N.A.T.A.N.)
 * @date 2025-10-18
 * @purpose Factory for creating document analysis providers
 *          Enables provider switching via configuration without code changes
 */
class DocumentAnalyzerFactory
{
    /**
     * Create document analyzer instance
     *
     * @param string|null $provider Provider name (null = use default from config)
     * @return DocumentAnalysisInterface Provider instance
     * @throws InvalidArgumentException When provider is unknown or disabled
     */
    public static function make(?string $provider = null): DocumentAnalysisInterface
    {
        $provider = $provider ?? config('document_analysis.default_provider');

        // Validate provider is enabled
        if (!self::isProviderEnabled($provider)) {
            throw new InvalidArgumentException(
                "Provider '{$provider}' is not enabled. Check config/document_analysis.php"
            );
        }

        return match ($provider) {
            'regex' => app(RegexDocumentAnalyzer::class),
            'aisuru' => app(AisuruDocumentAnalyzer::class),
            // Future providers (commented out - implement when needed):
            // 'claude' => app(ClaudeDocumentAnalyzer::class),
            // 'openai' => app(OpenAIDocumentAnalyzer::class),
            // 'ollama' => app(OllamaDocumentAnalyzer::class),
            default => throw new InvalidArgumentException(
                "Unknown document analysis provider: {$provider}"
            ),
        };
    }

    /**
     * Create analyzer with automatic fallback support
     *
     * @param string|null $provider Primary provider
     * @return DocumentAnalysisInterface Provider instance with fallback logic
     * @throws InvalidArgumentException When no valid provider available
     */
    public static function makeWithFallback(?string $provider = null): DocumentAnalysisInterface
    {
        try {
            return self::make($provider);
        } catch (InvalidArgumentException $e) {
            // Try fallback if enabled
            if (config('document_analysis.fallback_enabled')) {
                $fallbackProvider = config('document_analysis.fallback_provider');

                if ($fallbackProvider && $fallbackProvider !== $provider) {
                    \Log::warning('Document analyzer fallback activated', [
                        'failed_provider' => $provider,
                        'fallback_provider' => $fallbackProvider,
                        'reason' => $e->getMessage(),
                    ]);

                    return self::make($fallbackProvider);
                }
            }

            throw $e;
        }
    }

    /**
     * Check if provider is enabled in configuration
     *
     * @param string $provider Provider name
     * @return bool True if enabled
     */
    protected static function isProviderEnabled(string $provider): bool
    {
        $providerConfig = config("document_analysis.providers.{$provider}");

        if (!$providerConfig) {
            return false;
        }

        // Regex is always enabled (no API required)
        if ($provider === 'regex') {
            return true;
        }

        // Check if provider is explicitly enabled
        return $providerConfig['enabled'] ?? false;
    }

    /**
     * Get list of available (enabled) providers
     *
     * @return array Provider names
     */
    public static function getAvailableProviders(): array
    {
        $providers = [];

        foreach (config('document_analysis.providers', []) as $name => $config) {
            if (self::isProviderEnabled($name)) {
                $providers[] = $name;
            }
        }

        return $providers;
    }

    /**
     * Get default provider name
     *
     * @return string Provider name
     */
    public static function getDefaultProvider(): string
    {
        return config('document_analysis.default_provider', 'regex');
    }
}

