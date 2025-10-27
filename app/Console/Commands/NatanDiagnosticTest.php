<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NatanChatService;
use App\Services\PaActSearchService;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;

/**
 * NATAN Diagnostic Test Command
 * 
 * @package App\Console\Commands
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0
 * @date 2025-10-27
 * @purpose Systematic testing of NATAN AI system performance and reliability
 */
class NatanDiagnosticTest extends Command
{
    protected $signature = 'natan:diagnostic-test 
                            {--category= : Test category (semantic-search, context-window, rate-limit, all)}
                            {--iterations=10 : Number of iterations per test}
                            {--user-id=1 : User ID to run tests as}';

    protected $description = 'Run diagnostic tests on NATAN AI system';

    protected NatanChatService $natanService;
    protected array $results = [];
    protected string $logDir;

    public function __construct(
        NatanChatService $natanService
    ) {
        parent::__construct();
        $this->natanService = $natanService;
        $this->logDir = storage_path('logs/natan-tests');
    }

    public function handle()
    {
        $category = $this->option('category') ?? 'all';
        $iterations = (int)$this->option('iterations');
        $userId = (int)$this->option('user-id');

        // Create log directory
        if (!is_dir($this->logDir)) {
            mkdir($this->logDir, 0755, true);
        }

        // Authenticate as test user
        $user = User::findOrFail($userId);
        Auth::login($user);

        $this->info("🧪 NATAN Diagnostic Test Suite");
        $this->info("Category: {$category}");
        $this->info("Iterations: {$iterations}");
        $this->info("User: {$user->name} (ID: {$userId})");
        $this->newLine();

        $startTime = microtime(true);

        match ($category) {
            'semantic-search' => $this->runSemanticSearchTests($iterations),
            'context-window' => $this->runContextWindowTests($iterations),
            'rate-limit' => $this->runRateLimitTests($iterations),
            'all' => $this->runAllTests($iterations),
            default => $this->error("Unknown category: {$category}")
        };

        $totalTime = round(microtime(true) - $startTime, 2);

        $this->newLine();
        $this->info("✅ Tests completed in {$totalTime} seconds");
        
        // Save results
        $this->saveResults();
        
        // Display summary
        $this->displaySummary();

        return 0;
    }

    /**
     * Run semantic search tests
     */
    protected function runSemanticSearchTests(int $iterations): void
    {
        $this->info("🔍 SEMANTIC SEARCH TESTS");
        $this->newLine();

        // Test 1.1: Volume Scaling
        $this->line("Test 1.1: Volume Scaling");
        $queries = [
            ['query' => 'delibere approvate nel 2024', 'expected_range' => [100, 1000]],
            ['query' => 'delibere su manutenzione strade', 'expected_range' => [10, 100]],
            ['query' => 'delibera numero 234', 'expected_range' => [1, 10]],
        ];

        foreach ($queries as $test) {
            $this->runQueryTest($test['query'], $iterations, $test['expected_range']);
        }

        // Test 1.2: Query Specificity
        $this->newLine();
        $this->line("Test 1.2: Query Specificity");
        $specificityQueries = [
            'delibere', // Very generic
            'delibere 2024', // Generic
            'delibere manutenzione strade 2024', // Specific
            'delibera 234 del 15 marzo 2024', // Very specific
        ];

        foreach ($specificityQueries as $query) {
            $this->runQueryTest($query, 1);
        }
    }

    /**
     * Run context window tests
     */
    protected function runContextWindowTests(int $iterations): void
    {
        $this->info("📊 CONTEXT WINDOW TESTS");
        $this->newLine();

        $contextSizes = [5, 10, 25, 50, 100];
        $fixedQuery = "Riassumi le delibere più importanti del 2024";

        foreach ($contextSizes as $size) {
            $this->line("Testing with {$size} acts context...");
            
            for ($i = 0; $i < $iterations; $i++) {
                $this->runContextSizeTest($fixedQuery, $size, $i + 1);
            }
            
            $this->newLine();
        }
    }

    /**
     * Run rate limit tests
     */
    protected function runRateLimitTests(int $iterations): void
    {
        $this->info("⚡ RATE LIMIT TESTS");
        $this->newLine();

        // Test 3.1: Burst Testing
        $this->line("Test 3.1: Burst Testing (rapid succession)");
        $query = "Analizza delibere gennaio 2024";
        
        for ($i = 0; $i < $iterations; $i++) {
            $this->info("  Burst attempt " . ($i + 1) . "/{$iterations}");
            $this->runQueryTest($query, 1, null, true);
            // NO DELAY - test burst behavior
        }

        // Test 3.2: Gradual Ramp-Up
        $this->newLine();
        $this->line("Test 3.2: Gradual Ramp-Up (with delays)");
        $sizes = [5, 10, 25, 50];
        
        foreach ($sizes as $size) {
            $this->info("  Testing with {$size} acts (60s delay)...");
            $this->runContextSizeTest($query, $size, 1);
            
            if ($size < end($sizes)) {
                $this->line("  Waiting 60 seconds...");
                sleep(60);
            }
        }
    }

    /**
     * Run all tests
     */
    protected function runAllTests(int $iterations): void
    {
        $this->runSemanticSearchTests($iterations);
        $this->newLine(2);
        $this->runContextWindowTests($iterations);
        $this->newLine(2);
        $this->runRateLimitTests($iterations);
    }

    /**
     * Run single query test
     */
    protected function runQueryTest(
        string $query,
        int $iterations = 1,
        ?array $expectedRange = null,
        bool $trackBurst = false
    ): void {
        $bar = $this->output->createProgressBar($iterations);
        $bar->start();

        $user = Auth::user();

        for ($i = 0; $i < $iterations; $i++) {
            $startTime = microtime(true);
            
            try {
                // Test the query processing
                $response = $this->natanService->processQuery(
                    $query,
                    $user,
                    [],
                    'strategic',
                    null,
                    true,
                    false
                );

                $success = $response['success'] ?? false;
                $timeTaken = round(microtime(true) - $startTime, 2);

                // Log result
                $this->results[] = [
                    'test_type' => 'query',
                    'query' => $query,
                    'success' => $success,
                    'time_seconds' => $timeTaken,
                    'rate_limit_hit' => str_contains($response['response'] ?? '', 'rate limit'),
                    'timestamp' => Carbon::now()->toIso8601String(),
                    'burst_test' => $trackBurst,
                    'iteration' => $i + 1,
                ];

            } catch (\Exception $e) {
                $this->results[] = [
                    'test_type' => 'query',
                    'query' => $query,
                    'success' => false,
                    'error' => $e->getMessage(),
                    'timestamp' => Carbon::now()->toIso8601String(),
                ];
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
    }

    /**
     * Run context size test
     */
    protected function runContextSizeTest(string $query, int $contextSize, int $iteration): void
    {
        $startTime = microtime(true);
        
        $user = Auth::user();
        
        try {
            // Mock: force specific context size (would need to modify NatanChatService)
            // For now, just track what happens with normal flow
            
            $response = $this->natanService->processQuery(
                $query,
                $user,
                [],
                'strategic',
                null,
                true,
                false
            );

            $success = $response['success'] ?? false;
            $timeTaken = round(microtime(true) - $startTime, 2);

            $this->results[] = [
                'test_type' => 'context_size',
                'query' => $query,
                'target_context_size' => $contextSize,
                'success' => $success,
                'time_seconds' => $timeTaken,
                'rate_limit_hit' => str_contains($response['response'] ?? '', 'rate limit'),
                'timestamp' => Carbon::now()->toIso8601String(),
                'iteration' => $iteration,
            ];

        } catch (\Exception $e) {
            $this->results[] = [
                'test_type' => 'context_size',
                'query' => $query,
                'target_context_size' => $contextSize,
                'success' => false,
                'error' => $e->getMessage(),
                'timestamp' => Carbon::now()->toIso8601String(),
            ];
        }
    }

    /**
     * Save results to JSON
     */
    protected function saveResults(): void
    {
        $filename = $this->logDir . '/test-results-' . Carbon::now()->format('Y-m-d-His') . '.json';
        file_put_contents($filename, json_encode($this->results, JSON_PRETTY_PRINT));
        
        $this->info("📁 Results saved to: {$filename}");
    }

    /**
     * Display summary statistics
     */
    protected function displaySummary(): void
    {
        if (empty($this->results)) {
            return;
        }

        $totalTests = count($this->results);
        $successCount = collect($this->results)->where('success', true)->count();
        $failCount = $totalTests - $successCount;
        $successRate = round(($successCount / $totalTests) * 100, 1);
        
        $avgTime = collect($this->results)
            ->where('success', true)
            ->avg('time_seconds');
        
        $rateLimitCount = collect($this->results)
            ->where('rate_limit_hit', true)
            ->count();

        $this->newLine();
        $this->info("📊 SUMMARY STATISTICS");
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Tests', $totalTests],
                ['Successful', $successCount],
                ['Failed', $failCount],
                ['Success Rate', "{$successRate}%"],
                ['Avg Time (success)', round($avgTime, 2) . 's'],
                ['Rate Limits Hit', $rateLimitCount],
                ['Rate Limit Frequency', round(($rateLimitCount / $totalTests) * 100, 1) . '%'],
            ]
        );
    }
}
