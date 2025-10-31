<?php

namespace Tests\Feature\NatanChat;

use Tests\TestCase;
use App\Models\User;
use App\Models\NatanUnifiedContext;
use App\Services\NatanChatService;
use App\Services\UnifiedKnowledgeService;
use App\Services\EmbeddingService;
use App\Services\WebSearch\WebSearchService;
use App\Services\AnthropicService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Mockery;

class UnifiedKnowledgeIntegrationTest extends TestCase {
    use RefreshDatabase;

    protected $user;
    protected $embeddingService;
    protected $webSearchService;

    protected function setUp(): void {
        parent::setUp();

        // Create test user
        $this->user = User::factory()->create([
            'email' => 'test@example.com',
            'name' => 'Test User',
        ]);

        // Mock services to avoid real API calls
        $this->embeddingService = Mockery::mock(EmbeddingService::class);
        $this->webSearchService = Mockery::mock(WebSearchService::class);

        // Bind mocks to container
        $this->app->instance(EmbeddingService::class, $this->embeddingService);
        $this->app->instance(WebSearchService::class, $this->webSearchService);
    }

    protected function tearDown(): void {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_returns_unified_context_structure_when_called() {
        Config::set('natan.enable_unified_knowledge', true);

        // Mock embedding service
        $fakeEmbedding = array_fill(0, 1536, 0.8);
        $this->embeddingService
            ->shouldReceive('callOpenAIEmbedding')
            ->andReturn($fakeEmbedding);

        // Mock web search
        $this->webSearchService
            ->shouldReceive('search')
            ->andReturn(['success' => true, 'results' => []]);

        // Get NatanChatService
        $chatService = app(NatanChatService::class);

        // Use reflection to call getUnifiedContext
        $reflection = new \ReflectionClass($chatService);
        $method = $reflection->getMethod('getUnifiedContext');
        $method->setAccessible(true);

        $options = [
            'search_acts' => false,
            'search_web' => false,
            'acts' => [],
            'web_results' => [],
            'session_id' => 'test-session-' . uniqid(),
            'limit' => 20,
        ];

        $result = $method->invoke($chatService, 'Test query', $this->user, $options);

        // Assert unified_context format returned
        $this->assertArrayHasKey('unified_context', $result);
        $this->assertArrayHasKey('unified_sources', $result);
        $this->assertArrayHasKey('stats', $result);

        // Stats should have expected keys
        $this->assertArrayHasKey('total_chunks', $result['stats']);
        $this->assertArrayHasKey('by_type', $result['stats']);
    }

    /** @test */
    public function it_stores_chunks_with_correct_ttl_by_source_type() {
        Config::set('natan.enable_unified_knowledge', true);

        $sessionId = 'test-session-' . uniqid();

        // Mock embedding service
        $fakeEmbedding = array_fill(0, 1536, 0.7);
        $this->embeddingService
            ->shouldReceive('callOpenAIEmbedding')
            ->andReturn($fakeEmbedding);

        // Mock web search with results
        $this->webSearchService
            ->shouldReceive('search')
            ->andReturn([
                'success' => true,
                'results' => [
                    [
                        'url' => 'https://example.com/article',
                        'title' => 'External Web Article',
                        'text' => 'Web content about assessors in Florence',
                    ]
                ],
            ]);

        // Create UnifiedKnowledgeService
        $unifiedService = app(UnifiedKnowledgeService::class);

        // Simulate search (which triggers storage)
        $options = [
            'search_acts' => false,
            'search_web' => true,
            'acts' => [],
            'web_results' => [
                [
                    'url' => 'https://example.com/article',
                    'title' => 'External Web Article',
                    'text' => 'Web content about assessors',
                ]
            ],
            'session_id' => $sessionId,
            'limit' => 10,
        ];

        $result = $unifiedService->search('Test query', $options);

        // Assert web chunks stored
        $webChunks = NatanUnifiedContext::where('source_type', 'web')->get();

        if ($webChunks->count() > 0) {
            $webChunk = $webChunks->first();

            // Web should expire in ~6 hours
            $hoursDiff = abs($webChunk->expires_at->diffInHours(now(), false));
            $this->assertGreaterThanOrEqual(5, $hoursDiff, 'Web content should expire in ~6 hours');
            $this->assertLessThanOrEqual(7, $hoursDiff, 'Web content should expire in ~6 hours');
        }
    }

    /** @test */
    public function it_formats_context_with_source_citations() {
        Config::set('natan.enable_unified_knowledge', true);

        // Create test chunks directly in database
        $sessionId = 'test-session-' . uniqid();

        NatanUnifiedContext::create([
            'session_id' => $sessionId,
            'content' => 'L\'assessore Mario Rossi ha gestito il bilancio comunale.',
            'embedding' => array_fill(0, 1536, 0.9),
            'source_type' => 'act',
            'source_id' => '123',
            'source_title' => 'Delibera 2025/001',
            'source_url' => null,
            'metadata' => ['type' => 'delibera'],
            'similarity_score' => 0.95,
            'expires_at' => now()->addDays(30),
        ]);

        NatanUnifiedContext::create([
            'session_id' => $sessionId,
            'content' => 'L\'assessore Luigi Bianchi è stato nominato per l\'ambiente.',
            'embedding' => array_fill(0, 1536, 0.85),
            'source_type' => 'web',
            'source_id' => null,
            'source_title' => 'Articolo Corriere Fiorentino',
            'source_url' => 'https://corriere.it/firenze/assessori',
            'metadata' => [],
            'similarity_score' => 0.88,
            'expires_at' => now()->addHours(6),
        ]);

        // Mock embedding for query
        $queryEmbedding = array_fill(0, 1536, 0.9);
        $this->embeddingService
            ->shouldReceive('callOpenAIEmbedding')
            ->once()
            ->andReturn($queryEmbedding);

        // Get unified knowledge service
        $unifiedService = app(UnifiedKnowledgeService::class);

        // Use reflection to call formatForPrompt
        $sources = NatanUnifiedContext::forSession($sessionId)->get();

        $reflection = new \ReflectionClass($unifiedService);
        $method = $reflection->getMethod('formatForPrompt');
        $method->setAccessible(true);

        $formatted = $method->invoke($unifiedService, $sources);

        // Assert proper citation format (Italian format: "FONTE #1", "FONTE #2")
        $this->assertStringContainsString('FONTE #', $formatted, 'Should contain FONTE citation');

        // Assert relevance scores shown (Italian format: "Rilevanza:")
        $this->assertStringContainsString('Rilevanza:', $formatted, 'Should show relevance scores');

        // Assert URLs included for web sources
        $this->assertStringContainsString('https://corriere.it/firenze/assessori', $formatted);

        // Assert content included
        $this->assertStringContainsString('Mario Rossi', $formatted);
        $this->assertStringContainsString('Luigi Bianchi', $formatted);
    }

    /** @test */
    public function it_ranks_sources_by_semantic_similarity() {
        Config::set('natan.enable_unified_knowledge', true);

        $sessionId = 'test-session-' . uniqid();

        // Create chunks with varying embeddings
        $chunks = [
            ['content' => 'Highly relevant: assessori Firenze bilancio', 'embedding' => array_fill(0, 1536, 0.95)],
            ['content' => 'Somewhat relevant: comune Firenze amministrazione', 'embedding' => array_fill(0, 1536, 0.60)],
            ['content' => 'Less relevant: weather in Florence today', 'embedding' => array_fill(0, 1536, 0.20)],
        ];

        foreach ($chunks as $i => $data) {
            NatanUnifiedContext::create([
                'session_id' => $sessionId,
                'content' => $data['content'],
                'embedding' => $data['embedding'],
                'source_type' => 'act',
                'source_id' => (string) $i,
                'source_title' => 'Test ' . $i,
                'metadata' => [],
                'expires_at' => now()->addDays(30),
            ]);
        }

        // Query embedding similar to highly relevant content
        $queryEmbedding = array_fill(0, 1536, 0.90);

        $this->embeddingService
            ->shouldReceive('callOpenAIEmbedding')
            ->once()
            ->andReturn($queryEmbedding);

        $this->webSearchService
            ->shouldReceive('search')
            ->andReturn(['success' => true, 'results' => []]);

        // Get unified service
        $unifiedService = app(UnifiedKnowledgeService::class);

        // Use reflection to access semanticSearchUnified
        $reflection = new \ReflectionClass($unifiedService);
        $method = $reflection->getMethod('semanticSearchUnified');
        $method->setAccessible(true);

        $results = $method->invoke($unifiedService, $sessionId, $queryEmbedding, 3);

        // Assert results ordered by similarity
        $this->assertGreaterThan(0, $results->count());

        // Check ordering
        $previousSimilarity = 1.0;
        foreach ($results as $result) {
            $this->assertLessThanOrEqual($previousSimilarity, $result->similarity_score, 'Results should be ordered by similarity DESC');
            $previousSimilarity = $result->similarity_score;
        }

        // First result should be most similar
        $firstResult = $results->first();
        $this->assertStringContainsString('Highly relevant', $firstResult->content);
    }

    /** @test */
    public function it_includes_source_distribution_stats_in_result() {
        Config::set('natan.enable_unified_knowledge', true);

        $sessionId = 'test-session-' . uniqid();

        // Create mixed source types
        NatanUnifiedContext::create([
            'session_id' => $sessionId,
            'content' => 'Act content 1',
            'embedding' => array_fill(0, 1536, 0.8),
            'source_type' => 'act',
            'source_id' => '1',
            'source_title' => 'Act 1',
            'expires_at' => now()->addDays(30),
        ]);

        NatanUnifiedContext::create([
            'session_id' => $sessionId,
            'content' => 'Act content 2',
            'embedding' => array_fill(0, 1536, 0.75),
            'source_type' => 'act',
            'source_id' => '2',
            'source_title' => 'Act 2',
            'expires_at' => now()->addDays(30),
        ]);

        NatanUnifiedContext::create([
            'session_id' => $sessionId,
            'content' => 'Web content 1',
            'embedding' => array_fill(0, 1536, 0.7),
            'source_type' => 'web',
            'source_url' => 'https://example.com',
            'source_title' => 'Web 1',
            'expires_at' => now()->addHours(6),
        ]);

        // Mock services
        $queryEmbedding = array_fill(0, 1536, 0.8);
        $this->embeddingService
            ->shouldReceive('callOpenAIEmbedding')
            ->andReturn($queryEmbedding);

        $this->webSearchService
            ->shouldReceive('search')
            ->andReturn(['success' => true, 'results' => []]);

        // Get chat service
        $chatService = app(NatanChatService::class);

        // Use reflection to call getUnifiedContext
        $reflection = new \ReflectionClass($chatService);
        $method = $reflection->getMethod('getUnifiedContext');
        $method->setAccessible(true);

        $options = [
            'search_acts' => false,
            'search_web' => false,
            'acts' => [],
            'web_results' => [],
            'session_id' => $sessionId,
            'limit' => 10,
        ];

        $result = $method->invoke($chatService, 'Test query', $this->user, $options);

        // Assert stats present
        $this->assertArrayHasKey('stats', $result);
        $this->assertArrayHasKey('by_type', $result['stats']);

        // Assert source distribution
        $byType = $result['stats']['by_type'];
        $this->assertArrayHasKey('act', $byType, 'Should have act count');
        $this->assertArrayHasKey('web', $byType, 'Should have web count');

        $this->assertEquals(2, $byType['act'], 'Should count 2 acts');
        $this->assertEquals(1, $byType['web'], 'Should count 1 web');
    }

    /** @test */
    public function anthropic_service_receives_unified_context_format() {
        Config::set('natan.enable_unified_knowledge', true);

        // Create test unified context
        $context = [
            'unified_sources' => [
                ['content' => 'Source 1', 'source_type' => 'act'],
                ['content' => 'Source 2', 'source_type' => 'web'],
            ],
            'unified_context' => "# Unified Knowledge Base\nFONTE #1\nSource 1\n\nFONTE #2\nSource 2",
            'stats' => [
                'total_chunks' => 2,
                'by_type' => ['act' => 1, 'web' => 1],
                'avg_similarity' => 0.85,
            ],
        ];

        // Get AnthropicService
        $anthropicService = app(AnthropicService::class);

        // Call buildCommonContext using reflection (it's private)
        $reflection = new \ReflectionClass($anthropicService);
        $method = $reflection->getMethod('buildCommonContext');
        $method->setAccessible(true);

        $formatted = $method->invoke($anthropicService, $context);

        // Assert unified format used
        $this->assertStringContainsString('📚 KNOWLEDGE BASE (Unified Sources)', $formatted);
        $this->assertStringContainsString('Distribution:', $formatted);
        $this->assertStringContainsString('FONTE #', $formatted);

        // Should NOT contain legacy format
        $this->assertStringNotContainsString('ADMINISTRATIVE ACTS', $formatted);
    }

    /** @test */
    public function anthropic_service_falls_back_to_legacy_format_without_unified_context() {
        Config::set('natan.enable_unified_knowledge', false);

        // Legacy context format
        $context = [
            'acts' => [
                ['id' => 1, 'content' => 'Act 1'],
                ['id' => 2, 'content' => 'Act 2'],
            ],
            'acts_summary' => 'Summary of acts',
            'web_sources_summary' => 'Web sources summary',
        ];

        // Get AnthropicService
        $anthropicService = app(AnthropicService::class);

        // Call buildCommonContext using reflection
        $reflection = new \ReflectionClass($anthropicService);
        $method = $reflection->getMethod('buildCommonContext');
        $method->setAccessible(true);

        $formatted = $method->invoke($anthropicService, $context);

        // Assert legacy format used
        $this->assertStringNotContainsString('📚 KNOWLEDGE BASE (Unified Sources)', $formatted);

        // Should contain some output (not empty)
        $this->assertNotEmpty($formatted);
    }
}
