<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Project;
use App\Services\Projects\ProjectRagService;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * ProjectRagService Unit Tests
 * 
 * CRITICAL TEST: Verifica che TIER 3 (PA Acts) sia SEMPRE eseguito,
 * anche quando progetto non ha documenti o chat history.
 * 
 * @package Tests\Unit
 */
class ProjectRagServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test CRITICO: Verifica che con progetto vuoto (no docs, no chat)
     * il sistema cerchi comunque negli ATTI PA (TIER 3)
     * 
     * @return void
     */
    public function test_searches_pa_acts_even_when_project_is_empty(): void
    {
        // SETUP: Crea user e progetto VUOTO (no documenti, no chat)
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'user_id' => $user->id,
            'name' => 'Test Project Empty',
        ]);

        // Verifica progetto è effettivamente vuoto
        $this->assertEquals(0, $project->documents()->count(), 'Project should have NO documents');

        // ACT: Esegui ricerca
        $projectRagService = app(ProjectRagService::class);
        $results = $projectRagService->searchProjectContext(
            'criminalità sicurezza urbana',
            $project
        );

        // ASSERT: Deve avere risultati da PA Acts (TIER 3)
        $this->assertIsArray($results, 'Results should be an array');
        $this->assertArrayHasKey('results', $results, 'Should have results key');
        $this->assertArrayHasKey('stats', $results, 'Should have stats key');

        $stats = $results['stats'];

        // Verifica statistiche
        $this->assertEquals(0, $stats['documents'], 'Should have 0 documents (project is empty)');
        $this->assertEquals(0, $stats['chat'], 'Should have 0 chat messages (project is empty)');
        
        // 🔥 QUESTO È IL TEST CRITICO:
        $this->assertGreaterThan(
            0, 
            $stats['pa_acts'], 
            '🛑 CRITICAL FAILURE: PA Acts (TIER 3) returned 0 results! System is NOT searching in user acts when project is empty!'
        );

        // Verifica che total sia uguale a pa_acts (perché solo TIER 3 ha risultati)
        $this->assertEquals(
            $stats['pa_acts'], 
            $stats['total'],
            'Total should equal pa_acts count when project has no docs/chat'
        );

        // Verifica che i risultati contengano effettivamente atti PA
        $allResults = $results['results'];
        $paActResults = array_filter($allResults, fn($r) => ($r['type'] ?? '') === 'pa_act');
        
        $this->assertGreaterThan(
            0,
            count($paActResults),
            '🛑 CRITICAL: No PA act results in combined results! TIER 3 not executed!'
        );

        echo "\n✅ TEST PASSED: ProjectRagService searches PA acts even when project is empty\n";
        echo "   - Documents found: {$stats['documents']}\n";
        echo "   - Chat messages found: {$stats['chat']}\n";
        echo "   - PA Acts found: {$stats['pa_acts']} ✅\n";
        echo "   - Total results: {$stats['total']}\n";
    }
}
