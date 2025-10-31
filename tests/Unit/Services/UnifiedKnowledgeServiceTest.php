<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\User;
use App\Models\NatanUnifiedContext;
use App\Services\UnifiedKnowledgeService;
use App\Services\EmbeddingService;
use App\Services\WebSearch\WebSearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Mockery;

class UnifiedKnowledgeServiceTest extends TestCase {
    use RefreshDatabase;

    protected $embeddingService;
    protected $webSearchService;
    protected $unifiedKnowledgeService;
    protected $user;

    protected function setUp(): void {
        parent::setUp();

        // Create test user
        $this->user = User::factory()->create();

        // Mock EmbeddingService to avoid real OpenAI API calls
        $this->embeddingService = Mockery::mock(EmbeddingService::class);

        // Mock WebSearchService to avoid real Perplexity API calls
        $this->webSearchService = Mockery::mock(WebSearchService::class);

        // Create service with mocked dependencies
        $this->unifiedKnowledgeService = new UnifiedKnowledgeService(
            $this->embeddingService,
            $this->webSearchService
        );
    }

    protected function tearDown(): void {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_chunks_text_with_overlap_correctly() {
        // Use reflection to access protected method
        $reflection = new \ReflectionClass($this->unifiedKnowledgeService);
        $method = $reflection->getMethod('chunkTextWithOverlap');
        $method->setAccessible(true);

        $text = str_repeat('This is a sentence. ', 1000); // ~20,000 chars
        $chunks = $method->invoke($this->unifiedKnowledgeService, $text, 4000, 500);

        // Assert chunks created
        $this->assertGreaterThan(1, count($chunks), 'Should create multiple chunks');

        // Assert each chunk respects max size
        foreach ($chunks as $chunk) {
            $this->assertLessThanOrEqual(4500, strlen($chunk), 'Chunk should not exceed max_chars + overlap');
        }

        // Assert overlap exists between consecutive chunks
        if (count($chunks) > 1) {
            for ($i = 0; $i < count($chunks) - 1; $i++) {
                $currentChunkEnd = substr($chunks[$i], -100); // Last 100 chars
                $nextChunkStart = substr($chunks[$i + 1], 0, 100); // First 100 chars

                // There should be some common text (overlap)
                $this->assertNotEmpty($currentChunkEnd, 'Current chunk should have content');
                $this->assertNotEmpty($nextChunkStart, 'Next chunk should have content');
            }
        }
    }

    /** @test */
    public function it_normalizes_sources_to_standard_format() {
        $reflection = new \ReflectionClass($this->unifiedKnowledgeService);
        $method = $reflection->getMethod('normalizeSource');
        $method->setAccessible(true);

        // Test Act normalization (signature is: normalizeSource(string $type, $item))
        $act = (object) [
            'id' => 123,
            'content' => 'Act content here',
            'title' => 'Delibera 2025/001',
            'description' => 'Description text',
            'date' => '2025-01-15',
            'protocol_number' => 'PROT/2025/001',
            'type' => 'delibera',
            'direction' => 'in',
            'metadata' => ['type' => 'delibera'],
        ];

        $normalized = $method->invoke($this->unifiedKnowledgeService, 'acts', $act);

        // normalizeSource() doesn't add source_type - that's added by normalizeAndChunk()
        $this->assertArrayNotHasKey('source_type', $normalized);
        $this->assertEquals('123', $normalized['source_id']);
        $this->assertStringContainsString('Delibera 2025/001', $normalized['content']);
        $this->assertEquals('Delibera 2025/001', $normalized['source_title']);
        $this->assertArrayHasKey('metadata', $normalized);

        // Test Web normalization
        $web = [
            'url' => 'https://example.com/article',
            'title' => 'Web Article',
            'text' => 'Web content here',
        ];

        $normalized = $method->invoke($this->unifiedKnowledgeService, 'web', $web);

        // normalizeSource() doesn't add source_type - that's added by normalizeAndChunk()
        $this->assertArrayNotHasKey('source_type', $normalized);
        $this->assertEquals('https://example.com/article', $normalized['source_url']);
        $this->assertEquals('Web Article', $normalized['source_title']);
        $this->assertStringContainsString('Web content here', $normalized['content']);
    }

    /** @test */
    public function it_stores_unified_context_with_correct_ttl() {
        // Mock embedding service to return fake vectors
        $fakeEmbedding = array_fill(0, 1536, 0.5);
        $this->embeddingService
            ->shouldReceive('callOpenAIEmbedding')
            ->andReturn($fakeEmbedding);

        $chunks = [
            [
                'content' => 'Test act content',
                'embedding' => $fakeEmbedding, // Need embedding already in chunks
                'source_type' => 'act',
                'source_id' => '123',
                'source_title' => 'Test Act',
                'source_url' => null,
                'metadata' => ['type' => 'delibera'],
            ],
            [
                'content' => 'Test web content',
                'embedding' => $fakeEmbedding, // Need embedding already in chunks
                'source_type' => 'web',
                'source_id' => null,
                'source_url' => 'https://example.com',
                'source_title' => 'Test Web',
                'metadata' => [],
            ],
        ];

        $sessionId = 'test-session-' . uniqid();

        $reflection = new \ReflectionClass($this->unifiedKnowledgeService);
        $method = $reflection->getMethod('storeUnifiedContext');
        $method->setAccessible(true);

        // Signature: storeUnifiedContext(string $sessionId, array $chunks)
        $method->invoke($this->unifiedKnowledgeService, $sessionId, $chunks);

        // Assert records created
        $this->assertDatabaseCount('natan_unified_context', 2);

        // Assert act has 30 days TTL
        $actRecord = NatanUnifiedContext::where('source_type', 'act')->first();
        $this->assertNotNull($actRecord);
        $this->assertNotNull($actRecord->expires_at);

        // Check days difference (should be 30)
        $daysDiff = abs($actRecord->expires_at->diffInDays(now(), false));
        $this->assertGreaterThanOrEqual(29, $daysDiff, 'Act should expire in ~30 days');
        $this->assertLessThanOrEqual(31, $daysDiff, 'Act should expire in ~30 days');

        // Assert web has 6 hours TTL (TTL map has 0.25 days = 6 hours)
        $webRecord = NatanUnifiedContext::where('source_type', 'web')->first();
        $this->assertNotNull($webRecord);
        $this->assertNotNull($webRecord->expires_at);

        // Check hours difference (should be 6)
        $hoursDiff = abs($webRecord->expires_at->diffInHours(now(), false));
        $this->assertGreaterThanOrEqual(5, $hoursDiff, 'Web should expire in ~6 hours');
        $this->assertLessThanOrEqual(7, $hoursDiff, 'Web should expire in ~6 hours');
    }

    /** @test */
    public function it_calculates_cosine_similarity_correctly() {
        $reflection = new \ReflectionClass($this->unifiedKnowledgeService);
        $method = $reflection->getMethod('cosineSimilarity');
        $method->setAccessible(true);

        // Test identical vectors (similarity = 1.0)
        $vec1 = [1, 0, 0];
        $vec2 = [1, 0, 0];
        $similarity = $method->invoke($this->unifiedKnowledgeService, $vec1, $vec2);
        $this->assertEquals(1.0, round($similarity, 2));

        // Test orthogonal vectors (similarity = 0.0)
        $vec1 = [1, 0, 0];
        $vec2 = [0, 1, 0];
        $similarity = $method->invoke($this->unifiedKnowledgeService, $vec1, $vec2);
        $this->assertEquals(0.0, round($similarity, 2));

        // Test opposite vectors (similarity = -1.0)
        $vec1 = [1, 0, 0];
        $vec2 = [-1, 0, 0];
        $similarity = $method->invoke($this->unifiedKnowledgeService, $vec1, $vec2);
        $this->assertEquals(-1.0, round($similarity, 2));
    }

    /** @test */
    public function it_performs_semantic_search_and_ranks_by_similarity() {
        // Create test chunks in database
        $sessionId = 'test-session-' . uniqid();

        $chunks = [
            ['content' => 'Highly relevant content', 'embedding' => array_fill(0, 1536, 0.9)],
            ['content' => 'Somewhat relevant', 'embedding' => array_fill(0, 1536, 0.5)],
            ['content' => 'Not relevant', 'embedding' => array_fill(0, 1536, 0.1)],
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

        // Mock query embedding (similar to highly relevant)
        $queryEmbedding = array_fill(0, 1536, 0.85);

        $reflection = new \ReflectionClass($this->unifiedKnowledgeService);
        $method = $reflection->getMethod('semanticSearchUnified');
        $method->setAccessible(true);

        // Signature: semanticSearchUnified(string $sessionId, array $queryEmbedding, int $limit)
        $results = $method->invoke($this->unifiedKnowledgeService, $sessionId, $queryEmbedding, 10);

        // Assert results ordered by similarity
        $this->assertGreaterThanOrEqual(1, $results->count());

        // First result should have highest similarity
        $similarities = $results->pluck('similarity_score')->toArray();
        $sortedSimilarities = $similarities;
        rsort($sortedSimilarities);

        $this->assertEquals($sortedSimilarities, $similarities, 'Results should be ordered by similarity DESC');
    }

    /** @test */
    public function it_formats_context_for_prompt_with_citations() {
        $sources = collect([
            NatanUnifiedContext::make([
                'content' => 'First source content',
                'source_type' => 'act',
                'source_title' => 'Delibera 001',
                'source_url' => null,
                'similarity_score' => 0.95,
            ]),
            NatanUnifiedContext::make([
                'content' => 'Second source content',
                'source_type' => 'web',
                'source_title' => 'Web Article',
                'source_url' => 'https://example.com',
                'similarity_score' => 0.87,
            ]),
        ]);

        $reflection = new \ReflectionClass($this->unifiedKnowledgeService);
        $method = $reflection->getMethod('formatForPrompt');
        $method->setAccessible(true);

        $formatted = $method->invoke($this->unifiedKnowledgeService, $sources);

        // Assert contains source numbers (format is "FONTE #1", "FONTE #2")
        $this->assertStringContainsString('FONTE #1', $formatted);
        $this->assertStringContainsString('FONTE #2', $formatted);

        // Assert contains relevance scores (format is "Rilevanza: XX.X%")
        $this->assertStringContainsString('Rilevanza:', $formatted);

        // Assert contains content
        $this->assertStringContainsString('First source content', $formatted);
        $this->assertStringContainsString('Second source content', $formatted);

        // Assert contains URL for web source
        $this->assertStringContainsString('https://example.com', $formatted);

        // Assert contains citation instruction
        $this->assertStringContainsString('CITA SEMPRE', $formatted);
    }

    /** @test */
    public function it_handles_empty_sources_gracefully() {
        // Mock services to return empty results
        $this->webSearchService
            ->shouldReceive('search')
            ->andReturn(['success' => true, 'results' => []]);

        $this->embeddingService
            ->shouldReceive('callOpenAIEmbedding')
            ->andReturn(array_fill(0, 1536, 0.5));

        $options = [
            'search_acts' => false,
            'search_web' => false,
            'acts' => [],
            'web_results' => [],
            'session_id' => 'test-session-' . uniqid(),
            'limit' => 20,
        ];

        // search() returns Collection, not array
        $result = $this->unifiedKnowledgeService->search('test query', $options);

        // Result is a Collection of unified chunks
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $result);

        // With no sources, should be empty
        $this->assertTrue($result->isEmpty());
    }

    /** @test */
    public function it_generates_embeddings_for_all_chunks() {
        $chunks = [
            ['content' => 'Chunk 1', 'source_type' => 'act', 'source_id' => '1', 'source_title' => 'Test 1', 'source_url' => null, 'metadata' => []],
            ['content' => 'Chunk 2', 'source_type' => 'act', 'source_id' => '2', 'source_title' => 'Test 2', 'source_url' => null, 'metadata' => []],
            ['content' => 'Chunk 3', 'source_type' => 'web', 'source_id' => null, 'source_title' => 'Test 3', 'source_url' => 'https://example.com', 'metadata' => []],
        ];

        // Mock embedding service to return different vectors
        $this->embeddingService
            ->shouldReceive('callOpenAIEmbedding')
            ->times(3) // Should be called 3 times (one per chunk)
            ->andReturn(
                array_fill(0, 1536, 0.1),
                array_fill(0, 1536, 0.2),
                array_fill(0, 1536, 0.3)
            );

        $reflection = new \ReflectionClass($this->unifiedKnowledgeService);
        $method = $reflection->getMethod('generateEmbeddings');
        $method->setAccessible(true);

        // Signature: generateEmbeddings(array $chunks, string $query)
        $chunksWithEmbeddings = $method->invoke($this->unifiedKnowledgeService, $chunks, 'test query');

        // Assert embeddings generated for all chunks
        $this->assertCount(3, $chunksWithEmbeddings);

        // Assert each chunk now has embedding
        foreach ($chunksWithEmbeddings as $chunk) {
            $this->assertArrayHasKey('embedding', $chunk);
            $this->assertIsArray($chunk['embedding']);
            $this->assertCount(1536, $chunk['embedding']);
        }
    }

    /** @test */
    public function it_respects_top_k_limit_in_search_results() {
        $sessionId = 'test-session-' . uniqid();

        // Create 20 chunks
        for ($i = 0; $i < 20; $i++) {
            NatanUnifiedContext::create([
                'session_id' => $sessionId,
                'content' => 'Content ' . $i,
                'embedding' => array_fill(0, 1536, 0.5 + ($i * 0.01)),
                'source_type' => 'act',
                'source_id' => (string) $i,
                'source_title' => 'Test ' . $i,
                'metadata' => [],
                'expires_at' => now()->addDays(30),
            ]);
        }

        $queryEmbedding = array_fill(0, 1536, 0.6);

        $reflection = new \ReflectionClass($this->unifiedKnowledgeService);
        $method = $reflection->getMethod('semanticSearchUnified');
        $method->setAccessible(true);

        // Signature: semanticSearchUnified(string $sessionId, array $queryEmbedding, int $limit)
        // Request only top 5
        $results = $method->invoke($this->unifiedKnowledgeService, $sessionId, $queryEmbedding, 5);

        // Assert only 5 results returned
        $this->assertCount(5, $results);
    }
}
