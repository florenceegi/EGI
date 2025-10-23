<?php

declare(strict_types=1);

namespace App\Console\Commands\Padmin;

use Illuminate\Console\Command;
use App\Services\Padmin\RuleEngine\RuleEngineService;
use App\Models\User;

/**
 * Padmin Rule Engine Scanner Command
 *
 * Scans codebase for Copilot Instructions violations
 *
 * @package App\Console\Commands\Padmin
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Rule Engine)
 * @date 2025-10-23
 * @purpose CLI interface for rule checking
 */
class RuleScanCommand extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'padmin:scan
                            {--path= : Specific path to scan (default: app/)}
                            {--rules= : Comma-separated rule names (default: all)}
                            {--store : Store violations in Redis Stack}
                            {--user-id=1 : User ID for audit logging}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan codebase for Copilot Instructions rule violations';

    protected RuleEngineService $ruleEngine;

    public function __construct(RuleEngineService $ruleEngine) {
        parent::__construct();
        $this->ruleEngine = $ruleEngine;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int {
        $this->info('🔍 Padmin Rule Engine Scanner');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        // Get options
        $path = $this->option('path') ?: base_path('app');
        $rulesOption = $this->option('rules');
        $shouldStore = $this->option('store');
        $userId = (int) $this->option('user-id');

        $rules = $rulesOption ? explode(',', $rulesOption) : [];

        $this->info("📁 Scanning path: {$path}");
        $this->info("📋 Rules: " . ($rulesOption ?: 'all'));
        $this->newLine();

        // Scan
        $this->info('🚀 Starting scan...');
        $startTime = microtime(true);

        $violations = $this->ruleEngine->scanDirectory($path, $rules);

        $duration = round((microtime(true) - $startTime), 2);

        $this->newLine();
        $this->info("✅ Scan completed in {$duration}s");
        $this->info("📊 Found " . count($violations) . " violations");
        $this->newLine();

        // Display violations summary
        if (count($violations) > 0) {
            $this->displayViolationsSummary($violations);

            // Store violations if requested
            if ($shouldStore) {
                $this->newLine();
                $this->info('💾 Storing violations in Redis Stack...');

                $user = User::find($userId);
                if (!$user) {
                    $this->error("User with ID {$userId} not found");
                    return self::FAILURE;
                }

                $stored = $this->ruleEngine->storeViolations($violations, $user);
                $this->info("✅ Stored {$stored} violations");
            }
        } else {
            $this->info('🎉 No violations found! Code is compliant.');
        }

        return self::SUCCESS;
    }

    /**
     * Display violations summary
     */
    protected function displayViolationsSummary(array $violations): void {
        // Group by priority
        $byPriority = [
            'P0' => [],
            'P1' => [],
            'P2' => [],
            'P3' => [],
        ];

        foreach ($violations as $violation) {
            $priority = $violation['priority'] ?? 'P2';
            if (!isset($byPriority[$priority])) {
                $byPriority[$priority] = [];
            }
            $byPriority[$priority][] = $violation;
        }

        // Display P0 (critical) first
        foreach (['P0', 'P1', 'P2', 'P3'] as $priority) {
            if (count($byPriority[$priority]) > 0) {
                $this->displayPrioritySection($priority, $byPriority[$priority]);
            }
        }
    }

    /**
     * Display violations for specific priority
     */
    protected function displayPrioritySection(string $priority, array $violations): void {
        $colors = [
            'P0' => 'red',
            'P1' => 'yellow',
            'P2' => 'blue',
            'P3' => 'gray',
        ];

        $color = $colors[$priority] ?? 'white';
        $count = count($violations);

        $this->newLine();
        $this->line("<fg={$color};options=bold>━━━ {$priority} VIOLATIONS ({$count}) ━━━</>");
        $this->newLine();

        $table = [];
        foreach ($violations as $violation) {
            $table[] = [
                'Rule' => $violation['rule'],
                'Type' => $violation['type'],
                'File' => basename($violation['filePath']),
                'Line' => $violation['line'] ?? 'N/A',
                'Message' => mb_strimwidth($violation['message'], 0, 60, '...'),
            ];
        }

        $this->table(
            ['Rule', 'Type', 'File', 'Line', 'Message'],
            $table
        );
    }
}
