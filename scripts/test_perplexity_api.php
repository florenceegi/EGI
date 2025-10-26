<?php

/**
 * Test Perplexity API Configuration
 * 
 * Quick script to verify Perplexity API key is working
 * 
 * Usage: php scripts/test_perplexity_api.php
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Http;

echo "🧪 Testing Perplexity AI Configuration...\n\n";

$apiKey = config('services.web_search.perplexity.api_key');

if (!$apiKey) {
    echo "❌ ERROR: PERPLEXITY_API_KEY not found in .env\n";
    echo "   Add: PERPLEXITY_API_KEY=pplx-your-key-here\n";
    exit(1);
}

echo "✅ API Key found: " . substr($apiKey, 0, 10) . "...\n";
echo "🔍 Testing simple query...\n\n";

try {
    $response = Http::withHeaders([
        'Authorization' => "Bearer {$apiKey}",
        'Content-Type' => 'application/json',
    ])
    ->timeout(30)
    ->post('https://api.perplexity.ai/chat/completions', [
        'model' => 'llama-3.1-sonar-large-128k-online',
        'messages' => [
            [
                'role' => 'user',
                'content' => 'What are the best practices for waste management in European cities?',
            ],
        ],
        'max_tokens' => 500,
        'temperature' => 0.2,
        'return_citations' => true,
    ]);

    if ($response->successful()) {
        $data = $response->json();
        
        echo "✅ SUCCESS! Perplexity API is working!\n\n";
        echo "📊 Response:\n";
        echo "---\n";
        echo $data['choices'][0]['message']['content'] ?? 'No content';
        echo "\n---\n\n";
        
        $citations = $data['citations'] ?? [];
        echo "📚 Citations: " . count($citations) . " sources\n";
        
        if (!empty($citations)) {
            foreach (array_slice($citations, 0, 3) as $idx => $citation) {
                echo "  " . ($idx + 1) . ". " . ($citation['title'] ?? 'No title') . "\n";
                echo "     " . ($citation['url'] ?? 'No URL') . "\n";
            }
        }
        
        echo "\n✅ Configuration OK! Ready to use in N.A.T.A.N.\n";
        
    } else {
        echo "❌ ERROR: API returned status " . $response->status() . "\n";
        echo "Response: " . $response->body() . "\n";
        exit(1);
    }
    
} catch (\Exception $e) {
    echo "❌ EXCEPTION: " . $e->getMessage() . "\n";
    exit(1);
}

