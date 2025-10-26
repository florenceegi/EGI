<?php

/**
 * Test Web Search Providers (Perplexity + Google)
 * 
 * Tests both web search providers configuration
 * 
 * Usage: php scripts/test_web_search_providers.php [provider]
 *        php scripts/test_web_search_providers.php perplexity
 *        php scripts/test_web_search_providers.php google
 *        php scripts/test_web_search_providers.php all
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\WebSearch\WebSearchService;
use App\Services\WebSearch\KeywordSanitizerService;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

$provider = $argv[1] ?? 'all';

echo "🧪 N.A.T.A.N. Web Search Providers Test\n";
echo "=====================================\n\n";

// Initialize services
$logger = app(UltraLogManager::class);
$errorManager = app(ErrorManagerInterface::class);
$sanitizer = app(KeywordSanitizerService::class);
$webSearch = app(WebSearchService::class);

// Test query
$testQuery = "Best practices gestione rifiuti urbani Italia Europa";

echo "📝 Test Query: \"{$testQuery}\"\n";
echo "🔒 GDPR Sanitization...\n";

$sanitized = $sanitizer->sanitize($testQuery, 'strategic');

echo "   ✅ Sanitized: \"" . $sanitized['sanitized_query'] . "\"\n";
echo "   📊 Keywords: " . count($sanitized['keywords']) . "\n";
echo "   🗑️  Removed: " . count($sanitized['removed']) . "\n\n";

// Test Perplexity
if ($provider === 'perplexity' || $provider === 'all') {
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "🟣 PERPLEXITY AI TEST\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    $apiKey = config('services.web_search.perplexity.api_key');
    
    if (!$apiKey) {
        echo "❌ ERROR: PERPLEXITY_API_KEY not configured\n";
        echo "   Add to .env: PERPLEXITY_API_KEY=pplx-your-key\n";
        echo "   Get key from: https://www.perplexity.ai/settings/api\n\n";
    } else {
        echo "✅ API Key found: " . substr($apiKey, 0, 10) . "...\n";
        echo "🔍 Searching...\n\n";
        
        try {
            // Force Perplexity provider
            config(['services.web_search.default_provider' => 'perplexity']);
            
            $result = $webSearch->search($testQuery, 'strategic', 5);
            
            if ($result['success']) {
                echo "✅ SUCCESS!\n\n";
                echo "📊 Results: " . count($result['results']) . "\n";
                echo "⚡ Response time: " . ($result['metadata']['response_time_ms'] ?? '?') . "ms\n";
                echo "💾 From cache: " . ($result['metadata']['from_cache'] ? 'Yes' : 'No') . "\n\n";
                
                if (!empty($result['results'])) {
                    echo "📚 Top 3 Sources:\n";
                    foreach (array_slice($result['results'], 0, 3) as $idx => $source) {
                        echo "  " . ($idx + 1) . ". {$source['title']}\n";
                        echo "     URL: {$source['url']}\n";
                        echo "     Relevance: " . round($source['relevance_score'] * 100) . "%\n\n";
                    }
                }
                
                echo "✅ Perplexity configuration OK!\n\n";
            } else {
                echo "❌ FAILED: " . ($result['error'] ?? 'Unknown error') . "\n\n";
            }
            
        } catch (\Exception $e) {
            echo "❌ EXCEPTION: " . $e->getMessage() . "\n\n";
        }
    }
}

// Test Google
if ($provider === 'google' || $provider === 'all') {
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "🔵 GOOGLE CUSTOM SEARCH TEST\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    $apiKey = config('services.web_search.google.api_key');
    $searchEngineId = config('services.web_search.google.search_engine_id');
    
    if (!$apiKey || !$searchEngineId) {
        echo "❌ ERROR: Google Custom Search not configured\n";
        echo "   Missing:\n";
        if (!$apiKey) echo "   - GOOGLE_SEARCH_API_KEY\n";
        if (!$searchEngineId) echo "   - GOOGLE_SEARCH_ENGINE_ID\n";
        echo "\n";
        echo "   Get API key: https://console.cloud.google.com/apis/credentials\n";
        echo "   Create Search Engine: https://programmablesearchengine.google.com/\n\n";
    } else {
        echo "✅ API Key found: " . substr($apiKey, 0, 10) . "...\n";
        echo "✅ Search Engine ID: " . substr($searchEngineId, 0, 15) . "...\n";
        echo "🔍 Searching...\n\n";
        
        try {
            // Force Google provider
            config(['services.web_search.default_provider' => 'google']);
            
            $result = $webSearch->search($testQuery, 'strategic', 5);
            
            if ($result['success']) {
                echo "✅ SUCCESS!\n\n";
                echo "📊 Results: " . count($result['results']) . "\n";
                echo "⚡ Response time: " . ($result['metadata']['response_time_ms'] ?? '?') . "ms\n";
                echo "💾 From cache: " . ($result['metadata']['from_cache'] ? 'Yes' : 'No') . "\n\n";
                
                if (!empty($result['results'])) {
                    echo "📚 Top 3 Sources:\n";
                    foreach (array_slice($result['results'], 0, 3) as $idx => $source) {
                        echo "  " . ($idx + 1) . ". {$source['title']}\n";
                        echo "     URL: {$source['url']}\n";
                        echo "     Snippet: " . substr($source['snippet'], 0, 100) . "...\n\n";
                    }
                }
                
                echo "✅ Google configuration OK!\n\n";
            } else {
                echo "❌ FAILED: " . ($result['error'] ?? 'Unknown error') . "\n\n";
            }
            
        } catch (\Exception $e) {
            echo "❌ EXCEPTION: " . $e->getMessage() . "\n\n";
        }
    }
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "🎯 TEST COMPLETED\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

