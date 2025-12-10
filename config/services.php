<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Ollama AI Service (N.A.T.A.N.)
    |--------------------------------------------------------------------------
    |
    | Local LLM service for AI-powered document analysis.
    | GDPR-COMPLIANT: All processing happens on-premise (localhost).
    |
    */
    'ollama' => [
        'base_url' => env('OLLAMA_BASE_URL', 'http://localhost:11434'),
        'model' => env('OLLAMA_MODEL', 'llama3.1:8b'),
        'timeout' => env('OLLAMA_TIMEOUT', 60), // seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Anthropic AI Service (N.A.T.A.N.)
    |--------------------------------------------------------------------------
    |
    | Cloud LLM service powered by Claude 3 Opus.
    | GDPR-COMPLIANT: Processes ONLY public metadata (no PII, no signatures).
    | DPA: Anthropic has Data Processing Agreement with EU customers.
    |
    | NOTE: Switched from Claude 3.5 Sonnet to Claude 3 Opus due to API key permissions.
    | Current key only has access to Claude 3 models (Opus/Sonnet/Haiku).
    |
    */
    'anthropic' => [
        'api_key' => env('ANTHROPIC_API_KEY'),
        'base_url' => env('ANTHROPIC_BASE_URL', 'https://api.anthropic.com'),
        'model' => env('ANTHROPIC_MODEL', 'claude-3-opus-20240229'),
        'timeout' => env('ANTHROPIC_TIMEOUT', 60), // seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | OpenAI Service (Embeddings for RAG)
    |--------------------------------------------------------------------------
    |
    | OpenAI Embeddings API for semantic search.
    | Model: text-embedding-ada-002 (1536 dimensions)
    | Cost: ~$0.0001 per 1K tokens (~$0.02 for 24k acts)
    | GDPR-COMPLIANT: Processes ONLY public metadata (no PII).
    |
    */
    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
        'embedding_model' => env('OPENAI_EMBEDDING_MODEL', 'text-embedding-ada-002'),
        'timeout' => env('OPENAI_TIMEOUT', 30), // seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Web Search Services (N.A.T.A.N. Enhanced)
    |--------------------------------------------------------------------------
    |
    | External web search APIs to augment N.A.T.A.N. responses with
    | global best practices, real-time normative updates, and funding opportunities.
    |
    | GDPR-COMPLIANT: Only sanitized keywords are sent (no internal document data).
    |
    | Supported providers:
    | - Perplexity AI: AI-powered search with citations (recommended)
    | - Google Custom Search: Traditional search with snippets
    |
    */
    'web_search' => [
        'enabled' => env('WEB_SEARCH_ENABLED', true),
        'default_provider' => env('WEB_SEARCH_PROVIDER', 'perplexity'), // perplexity|google
        'max_results' => env('WEB_SEARCH_MAX_RESULTS', 5),
        'timeout' => env('WEB_SEARCH_TIMEOUT', 15), // seconds
        'cache_ttl' => env('WEB_SEARCH_CACHE_TTL', 3600), // 1 hour cache

        // Perplexity AI (recommended)
        'perplexity' => [
            'api_key' => env('PERPLEXITY_API_KEY'),
            'base_url' => env('PERPLEXITY_BASE_URL', 'https://api.perplexity.ai'),
            'model' => env('PERPLEXITY_MODEL', 'llama-3.1-sonar-large-128k-online'),
            'timeout' => env('PERPLEXITY_TIMEOUT', 30),
        ],

        // Google Custom Search API (fallback)
        'google' => [
            'api_key' => env('GOOGLE_SEARCH_API_KEY'),
            'search_engine_id' => env('GOOGLE_SEARCH_ENGINE_ID'), // cx parameter
            'base_url' => 'https://www.googleapis.com/customsearch/v1',
            'timeout' => env('GOOGLE_SEARCH_TIMEOUT', 10),
        ],

        // Keyword sanitization (GDPR protection)
        'sanitization' => [
            'remove_protocols' => true, // Remove "protocollo 1234/2024"
            'remove_internal_refs' => true, // Remove "determina 847/2024"
            'remove_names' => true, // Remove person names
            'remove_locations' => true, // Remove specific locations (keep generic "Firenze")
            'max_keyword_length' => 100, // Truncate long keywords
        ],

        // Persona-specific search preferences
        'persona_preferences' => [
            'strategic' => [
                'domains_priority' => ['mckinsey.com', 'bcg.com', 'oecd.org', 'worldbank.org'],
                'keywords_boost' => ['best practices', 'case study', 'benchmark'],
            ],
            'legal' => [
                'domains_priority' => ['gazzettaufficiale.it', 'garanteprivacy.it', 'normattiva.it'],
                'keywords_boost' => ['sentenza', 'normativa', 'compliance'],
            ],
            'financial' => [
                'domains_priority' => ['pnrr.gov.it', 'europa.eu', 'mise.gov.it'],
                'keywords_boost' => ['funding', 'bando', 'finanziamento'],
            ],
            'technical' => [
                'domains_priority' => ['agid.gov.it', 'iso.org'],
                'keywords_boost' => ['technical specification', 'standard'],
            ],
            'urban_social' => [
                'domains_priority' => ['unhabitat.org', 'c40.org', 'eukn.eu'],
                'keywords_boost' => ['urban', 'city', 'sustainable'],
            ],
            'communication' => [
                'domains_priority' => ['formez.it', 'comunicazione.pa.gov.it'],
                'keywords_boost' => ['communication', 'engagement', 'stakeholder'],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Gold Price API Services
    |--------------------------------------------------------------------------
    |
    | API services for fetching real-time gold prices.
    | Used by GoldPriceService for Gold Bar EGI valuations.
    |
    | Free tier options:
    | - Gold-API.io: 300 requests/month free
    | - MetalPriceAPI: Limited free tier
    |
    */
    'gold_api' => [
        'key' => env('GOLD_API_KEY'),
    ],

    'metal_price_api' => [
        'key' => env('METAL_PRICE_API_KEY'),
    ],

];
