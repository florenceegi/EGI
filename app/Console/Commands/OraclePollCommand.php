<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EgiOracleService;

/**
 * @package App\Console\Commands
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Dual Architecture)
 * @date 2025-10-19
 * @purpose Console command to poll SmartContracts for AI triggers
 */
class OraclePollCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'oracle:poll 
                            {--force : Force polling even if oracle is disabled}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Poll SmartContracts for pending AI triggers and process them';

    private EgiOracleService $oracleService;

    /**
     * Create a new command instance.
     *
     * @param EgiOracleService $oracleService
     */
    public function __construct(EgiOracleService $oracleService)
    {
        parent::__construct();
        $this->oracleService = $oracleService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->info('🔮 FlorenceEGI Oracle - Starting polling...');

        try {
            // Check if oracle is enabled (unless forced)
            if (!config('egi_living.feature_flags.oracle_enabled') && !$this->option('force')) {
                $this->warn('⚠️  Oracle service is disabled. Use --force to poll anyway.');
                return Command::FAILURE;
            }

            // Poll for triggers
            $result = $this->oracleService->pollForTriggers();

            // Display results
            match ($result['status']) {
                'disabled' => $this->warn('⚠️  ' . $result['message']),
                'idle' => $this->line('✓ No SmartContracts ready for trigger.'),
                'completed' => $this->displayCompletedResults($result),
                'error' => $this->error('❌ Polling failed: ' . $result['message']),
                default => $this->warn('⚠️  Unknown status: ' . $result['status']),
            };

            return $result['status'] === 'error' ? Command::FAILURE : Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Oracle polling crashed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Display completed polling results
     *
     * @param array $result
     */
    private function displayCompletedResults(array $result): void
    {
        $this->info("✓ Polling completed:");
        $this->line("  - Processed: {$result['processed']} SmartContracts");
        $this->line("  - Success: {$result['success']}");
        $this->line("  - Failed: " . ($result['processed'] - $result['success']));

        // Show details if verbose
        if ($this->output->isVerbose()) {
            $this->line('');
            $this->line('📋 Details:');

            foreach ($result['results'] as $item) {
                $status = $item['success'] ? '✓' : '✗';
                $error = !$item['success'] && isset($item['error']) ? " ({$item['error']})" : '';

                $this->line("  {$status} App ID: {$item['app_id']}{$error}");
            }
        }
    }
}

