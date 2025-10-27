<?php

return [
    /*
    |--------------------------------------------------------------------------
    | NATAN Chat Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for NATAN AI Assistant chat system
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Claude Context Limit
    |--------------------------------------------------------------------------
    |
    | ENTERPRISE STRATEGY: Search scans ALL sources (1M+ acts if present),
    | but only TOP N most relevant are sent to Claude API.
    |
    | This prevents rate limits while maintaining accuracy:
    | - Search: NO LIMIT (scan entire archive)
    | - Claude: TOP N by weighted similarity
    |
    | Values:
    | - 50: Safe for new accounts (25k tokens)
    | - 100: Recommended for production (50k tokens)
    | - 200: High accuracy mode (100k tokens)
    |
    | Note: 100 acts ≈ 50k tokens (Claude accepts 200k window)
    |
    */
    'claude_context_limit' => env('NATAN_CLAUDE_CONTEXT_LIMIT', 100),

    /*
    |--------------------------------------------------------------------------
    | Claude Context Limit - MINIMUM (Adaptive Retry)
    |--------------------------------------------------------------------------
    |
    | When rate_limit_error occurs, system automatically retries with reduced
    | context size. This is the MINIMUM limit before giving up.
    |
    | Retry sequence: 100 → 50 → 25 → 10 → 5
    |
    | Default: 5 (minimum viable context for meaningful response)
    |
    */
    'claude_context_limit_minimum' => env('NATAN_CLAUDE_CONTEXT_LIMIT_MINIMUM', 5),

];
