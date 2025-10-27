<?php

namespace Tests\Unit\Services\WebSearch;

use App\Services\WebSearch\KeywordSanitizerService;
use Tests\TestCase;
use Ultra\UltraLogManager\UltraLogManager;
use Mockery;

/**
 * KeywordSanitizerService Unit Tests
 *
 * Tests GDPR-safe keyword sanitization for external web search.
 * Ensures NO internal/sensitive data leaks.
 *
 * @package Tests\Unit\Services\WebSearch
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - N.A.T.A.N. Web Search Tests)
 * @date 2025-10-26
 */
class KeywordSanitizerServiceTest extends TestCase {
    protected KeywordSanitizerService $service;

    protected function setUp(): void {
        parent::setUp();

        $logger = Mockery::mock(UltraLogManager::class);
        $logger->shouldReceive('info')->andReturn(true);
        $logger->shouldReceive('warning')->andReturn(true);

        $this->service = new KeywordSanitizerService($logger);
    }

    /** @test */
    public function it_removes_protocol_numbers() {
        $query = "Analizza protocollo 1234/2024 e determina 847/2024";
        $result = $this->service->sanitize($query);

        $this->assertNotContains('1234', $result['keywords']);
        $this->assertNotContains('2024', $result['keywords']);
        $this->assertNotContains('847', $result['keywords']);
        $this->assertArrayHasKey('numbers', $result['removed']);
    }

    /** @test */
    public function it_removes_internal_reference_keywords() {
        $query = "Confronta determina delibera con best practices";
        $result = $this->service->sanitize($query);

        $this->assertNotContains('determina', $result['keywords']);
        $this->assertNotContains('delibera', $result['keywords']);
        $this->assertContains('confronta', $result['keywords']);
        $this->assertContains('best', $result['keywords']);
        $this->assertContains('practices', $result['keywords']);
    }

    /** @test */
    public function it_keeps_generic_locations() {
        $query = "Best practices Firenze Italia Europa";
        $result = $this->service->sanitize($query);

        $this->assertContains('firenze', $result['keywords']);
        $this->assertContains('italia', $result['keywords']);
        $this->assertContains('europa', $result['keywords']);
    }

    /** @test */
    public function it_validates_sanitized_keywords_are_safe() {
        $safeKeywords = ['best', 'practices', 'firenze', 'europa'];
        $validation = $this->service->validate($safeKeywords);

        $this->assertTrue($validation['is_safe']);
        $this->assertEmpty($validation['violations']);
    }

    /** @test */
    public function it_detects_unsafe_keywords_with_numbers() {
        $unsafeKeywords = ['best', '1234', 'practices', '847/2024'];
        $validation = $this->service->validate($unsafeKeywords);

        $this->assertFalse($validation['is_safe']);
        $this->assertNotEmpty($validation['violations']);
        $this->assertCount(2, $validation['violations']);
    }

    /** @test */
    public function it_boosts_keywords_with_persona_specific_terms() {
        $query = "analizza gestione rifiuti";
        $result = $this->service->sanitize($query, 'strategic');

        // Should add strategic boost keywords
        $this->assertGreaterThan(2, count($result['keywords']));
    }

    protected function tearDown(): void {
        Mockery::close();
        parent::tearDown();
    }
}
