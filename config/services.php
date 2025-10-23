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
    | Cloud LLM service powered by Claude 3.5 Sonnet.
    | GDPR-COMPLIANT: Processes ONLY public metadata (no PII, no signatures).
    | DPA: Anthropic has Data Processing Agreement with EU customers.
    |
    */
    'anthropic' => [
        'api_key' => env('ANTHROPIC_API_KEY'),
        'base_url' => env('ANTHROPIC_BASE_URL', 'https://api.anthropic.com'),
        'model' => env('ANTHROPIC_MODEL', 'claude-3-5-sonnet-20241022'),
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

];
