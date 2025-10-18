<?php

/**
 * Document Analysis Configuration
 *
 * @package Config
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - N.A.T.A.N.)
 * @date 2025-10-18
 * @purpose Configuration for provider-agnostic document analysis system
 *          Supports multiple AI providers: Claude, AISURU, OpenAI, Ollama
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Default Document Analysis Provider
    |--------------------------------------------------------------------------
    |
    | Provider used for document analysis. Switch between providers by changing
    | this value or using DOCUMENT_ANALYZER_PROVIDER env variable.
    |
    | Supported: "regex", "claude", "aisuru", "openai", "ollama"
    |
    */

    'default_provider' => env('DOCUMENT_ANALYZER_PROVIDER', 'regex'),

    /*
    |--------------------------------------------------------------------------
    | Fallback Configuration
    |--------------------------------------------------------------------------
    |
    | Enable fallback to secondary provider if primary fails.
    | Useful for high-availability scenarios.
    |
    */

    'fallback_enabled' => env('DOCUMENT_ANALYSIS_FALLBACK', false),
    'fallback_provider' => env('DOCUMENT_ANALYSIS_FALLBACK_PROVIDER', 'regex'),

    /*
    |--------------------------------------------------------------------------
    | Provider-Specific Settings
    |--------------------------------------------------------------------------
    */

    'providers' => [

        /**
         * Regex Provider (Basic)
         * Simple pattern matching for document metadata extraction
         * No AI, no cost, fast but limited accuracy
         */
        'regex' => [
            'enabled' => true,
            'timeout' => 5,
        ],

        /**
         * Claude Provider (Anthropic)
         * High-quality AI analysis, expensive
         * Best accuracy for complex documents
         */
        'claude' => [
            'enabled' => env('CLAUDE_ENABLED', false),
            'api_key' => env('CLAUDE_API_KEY'),
            'model' => env('CLAUDE_MODEL', 'claude-3-sonnet-20240229'),
            'timeout' => env('CLAUDE_TIMEOUT', 30),
            'max_tokens' => env('CLAUDE_MAX_TOKENS', 2048),
            'temperature' => env('CLAUDE_TEMPERATURE', 0.3), // Low = more deterministic
        ],

        /**
         * AISURU Provider (Sparavigna - memori.ai)
         * Italian AI platform, AI Act compliant
         * Can be deployed on-premise for PA requirements
         */
        'aisuru' => [
            'enabled' => env('AISURU_ENABLED', false),
            'api_url' => env('AISURU_API_URL', 'https://backend.memori.ai'),
            'api_key' => env('AISURU_API_KEY'),
            'memori_id' => env('AISURU_MEMORI_ID'), // Agent ID
            'timeout' => env('AISURU_TIMEOUT', 30),
        ],

        /**
         * OpenAI Provider (GPT-4)
         * Good balance between quality and cost
         * Alternative to Claude
         */
        'openai' => [
            'enabled' => env('OPENAI_ENABLED', false),
            'api_key' => env('OPENAI_API_KEY'),
            'model' => env('OPENAI_MODEL', 'gpt-4-turbo'),
            'timeout' => env('OPENAI_TIMEOUT', 30),
            'max_tokens' => env('OPENAI_MAX_TOKENS', 2048),
            'temperature' => env('OPENAI_TEMPERATURE', 0.3),
        ],

        /**
         * Ollama Provider (Self-Hosted)
         * Open-source LLMs (Llama 3, Mistral) running locally
         * Free, private, on-premise - ideal for PA requirements
         */
        'ollama' => [
            'enabled' => env('OLLAMA_ENABLED', false),
            'api_url' => env('OLLAMA_API_URL', 'http://localhost:11434'),
            'model' => env('OLLAMA_MODEL', 'llama3:70b'),
            'timeout' => env('OLLAMA_TIMEOUT', 60), // Self-hosted can be slower
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Document Type Configurations
    |--------------------------------------------------------------------------
    |
    | Specific settings for different document types
    |
    */

    'document_types' => [

        'pa_act' => [
            'name' => 'PA Administrative Act',
            'extract_fields' => ['act_type', 'protocol', 'protocol_date', 'title', 'description', 'entities'],
            'validation_rules' => [
                'act_type' => 'required|in:delibera,determina,ordinanza,decreto,atto',
                'protocol' => 'nullable|string',
            ],
        ],

        'contract' => [
            'name' => 'Legal Contract',
            'extract_fields' => ['contract_type', 'parties', 'effective_date', 'expiration_date', 'value'],
        ],

        'invoice' => [
            'name' => 'Invoice/Receipt',
            'extract_fields' => ['invoice_number', 'date', 'total', 'vendor', 'items'],
        ],

    ],

];

