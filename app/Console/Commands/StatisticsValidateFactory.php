<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Factories\StatisticsTestFactory;
use App\Services\StatisticsService;
use App\Models\User;
use Ultra\UltraLogManager\UltraLogManager;

class StatisticsValidateFactory extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statistics:validate-factory {--cleanup : Clean up test data after validation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Validate statistics calculations using deterministic factory data';

    /**
     * Target user ID per i test
     */
    private const TARGET_USER_ID = 3;

    /**
     * Execute the console command.
     */
    public function handle(): int {
        $this->info('🚀 Starting Statistics Factory Validation...');
        $this->newLine();

        try {
            // 1. Seed deterministic data
            $this->info('📊 Seeding deterministic data for User ID ' . self::TARGET_USER_ID . '...');
            $factory = new StatisticsTestFactory();
            $expectedResults = $factory->seedDeterministicData();
            $this->info('✅ Deterministic data seeded successfully');
            $this->newLine();

            // 2. Run actual statistics queries
            $this->info('🔍 Running actual statistics queries...');
            $actualResults = $this->runActualStatisticsQueries();
            $this->info('✅ Actual statistics calculated');
            $this->newLine();

            // 3. Compare results
            $this->info('⚖️ Comparing expected vs actual results...');
            $validationResults = $this->compareResults($expectedResults, $actualResults);

            // 4. Display validation report
            $this->displayValidationReport($validationResults);

            // 5. Cleanup if requested
            if ($this->option('cleanup')) {
                $this->info('🧼 Cleaning up test data...');
                $factory->cleanup();
                $this->info('✅ Cleanup completed');
            }

            // 6. Return appropriate exit code
            $allPassed = collect($validationResults)->every(fn($result) => $result['passed']);

            if ($allPassed) {
                $this->info('🎉 ALL VALIDATIONS PASSED! Statistics are perfectly aligned.');
                return self::SUCCESS;
            } else {
                $this->error('❌ Some validations failed. Check the detailed report above.');
                return self::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error('💥 Error during validation: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return self::FAILURE;
        }
    }

    /**
     * Esegue tutte le query statistiche reali
     */
    private function runActualStatisticsQueries(): array {
        $user = User::find(self::TARGET_USER_ID);
        $logger = app(UltraLogManager::class);
        $statisticsService = new StatisticsService($user, $logger);

        // Get comprehensive stats (main method)
        $comprehensiveStats = $statisticsService->getComprehensiveStats(true); // Force refresh

        // Get individual method results for detailed validation
        return [
            'likes_statistics' => $comprehensiveStats['likes'],
            'reservations_statistics' => $comprehensiveStats['reservations'],
            'amounts_statistics' => $comprehensiveStats['amounts'],
            'epp_potential_statistics' => $comprehensiveStats['epp_potential'],
            'portfolio_statistics' => $comprehensiveStats['portfolio'],
            'creator_earnings' => $statisticsService->getCreatorEarnings(self::TARGET_USER_ID, 'all'),
            'user_total_earnings' => $statisticsService->getUserTotalEarnings(self::TARGET_USER_ID, 'all'),
        ];
    }

    /**
     * Confronta risultati attesi vs risultati reali
     */
    private function compareResults(array $expected, array $actual): array {
        $validationResults = [];

        foreach ($expected as $key => $expectedValue) {
            if ($key === 'metadata') continue; // Skip metadata

            $actualValue = $actual[$key] ?? null;
            $validationResults[$key] = $this->validateMetric($key, $expectedValue, $actualValue);
        }

        return $validationResults;
    }

    /**
     * Valida una singola metrica
     */
    private function validateMetric(string $metricName, $expected, $actual): array {
        $differences = [];
        $passed = true;

        if (is_null($actual)) {
            $passed = false;
            $differences[] = "Metric not found in actual results";
        } elseif (is_array($expected) && is_array($actual)) {
            $differences = $this->compareArrays($expected, $actual);
            $passed = empty($differences);
        } elseif (is_numeric($expected) && is_numeric($actual)) {
            $tolerance = 0.01; // Tolleranza per float
            $passed = abs($expected - $actual) <= $tolerance;
            if (!$passed) {
                $differences[] = "Expected: {$expected}, Actual: {$actual}";
            }
        } else {
            $passed = $expected === $actual;
            if (!$passed) {
                $differences[] = "Expected: " . json_encode($expected) . ", Actual: " . json_encode($actual);
            }
        }

        return [
            'metric' => $metricName,
            'passed' => $passed,
            'differences' => $differences,
            'expected_type' => gettype($expected),
            'actual_type' => gettype($actual),
        ];
    }

    /**
     * Confronta array ricorsivamente
     */
    private function compareArrays(array $expected, array $actual, string $path = ''): array {
        $differences = [];

        // Check numeric differences with tolerance
        foreach ($expected as $key => $value) {
            $currentPath = $path ? "{$path}.{$key}" : $key;

            if (!array_key_exists($key, $actual)) {
                $differences[] = "Missing key: {$currentPath}";
                continue;
            }

            if (is_array($value) && is_array($actual[$key])) {
                $nestedDifferences = $this->compareArrays($value, $actual[$key], $currentPath);
                $differences = array_merge($differences, $nestedDifferences);
            } elseif (is_numeric($value) && is_numeric($actual[$key])) {
                $tolerance = 0.01;
                if (abs($value - $actual[$key]) > $tolerance) {
                    $differences[] = "{$currentPath}: Expected {$value}, Actual {$actual[$key]}";
                }
            } elseif ($value !== $actual[$key]) {
                $differences[] = "{$currentPath}: Expected " . json_encode($value) . ", Actual " . json_encode($actual[$key]);
            }
        }

        return $differences;
    }

    /**
     * Mostra il report di validazione
     */
    private function displayValidationReport(array $validationResults): void {
        $this->info('📋 VALIDATION REPORT');
        $this->info('==================');
        $this->newLine();

        $totalMetrics = count($validationResults);
        $passedMetrics = collect($validationResults)->where('passed', true)->count();
        $failedMetrics = $totalMetrics - $passedMetrics;

        // Summary
        $this->info("📊 SUMMARY: {$passedMetrics}/{$totalMetrics} metrics passed");
        $this->newLine();

        // Detailed results
        foreach ($validationResults as $result) {
            $status = $result['passed'] ? '✅ PASS' : '❌ FAIL';
            $this->line("{$status} {$result['metric']}");

            if (!$result['passed'] && !empty($result['differences'])) {
                foreach (array_slice($result['differences'], 0, 5) as $difference) { // Limit output
                    $this->line("    └─ {$difference}");
                }
                if (count($result['differences']) > 5) {
                    $this->line("    └─ ... and " . (count($result['differences']) - 5) . " more differences");
                }
            }
        }

        $this->newLine();

        // Final status
        if ($failedMetrics === 0) {
            $this->info('🎉 ALL METRICS PASSED! Statistics calculations are perfectly accurate.');
        } else {
            $this->warn("⚠️ {$failedMetrics} metrics failed validation. Review differences above.");
        }
    }
}
