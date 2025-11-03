<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FeatureConsumptionService;
use App\Models\FeatureConsumptionLedger;
use App\Models\User;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * @package App\Console\Commands
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Batch Charge Cron)
 * @date 2025-11-03
 * @purpose Daily batch charging of pending feature consumption debt
 * 
 * Schedule:
 * - Runs daily at 02:00 AM (low traffic time)
 * - Processes ALL users with pending debt >= threshold
 * - Logs all charges for audit
 * 
 * Usage:
 * - php artisan egili:batch-charge-consumption [--force]
 * - --force: Charge ALL pending debt (even below threshold)
 */
class BatchChargeConsumptionDebt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'egili:batch-charge-consumption
                            {--force : Force charge ALL pending debt (even below threshold)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Batch charge pending feature consumption debt for all users';

    private FeatureConsumptionService $consumptionService;
    private UltraLogManager $logger;
    
    /**
     * Execute the console command.
     */
    public function handle(
        FeatureConsumptionService $consumptionService,
        UltraLogManager $logger
    ): int {
        $this->consumptionService = $consumptionService;
        $this->logger = $logger;
        
        $force = $this->option('force');
        
        $this->info('🔄 Starting batch charge process...');
        $this->info('Force mode: ' . ($force ? 'YES (charge all)' : 'NO (threshold only)'));
        
        // Get all users with pending consumption debt
        $usersWithDebt = FeatureConsumptionLedger::select('user_id')
            ->where('billing_status', 'pending')
            ->groupBy('user_id')
            ->havingRaw('SUM(total_cost_egili) > 0')
            ->pluck('user_id');
        
        $this->info("📊 Found {$usersWithDebt->count()} users with pending debt");
        
        if ($usersWithDebt->isEmpty()) {
            $this->info('✅ No pending debt to charge. Done!');
            return Command::SUCCESS;
        }
        
        $totalCharged = 0;
        $totalEntries = 0;
        $usersCharged = 0;
        $usersFailed = 0;
        
        $progressBar = $this->output->createProgressBar($usersWithDebt->count());
        $progressBar->start();
        
        foreach ($usersWithDebt as $userId) {
            try {
                $user = User::findOrFail($userId);
                $pendingDebt = $this->consumptionService->getPendingDebt($user);
                
                // Attempt batch charge
                $result = $this->consumptionService->batchChargePendingDebt($user, $force);
                
                if ($result['charged_egili'] > 0) {
                    $totalCharged += $result['charged_egili'];
                    $totalEntries += $result['charged_entries'];
                    $usersCharged++;
                    
                    $this->logger->info('User consumption debt charged', [
                        'user_id' => $userId,
                        'pending_debt' => $pendingDebt,
                        'charged_egili' => $result['charged_egili'],
                        'entries' => $result['charged_entries'],
                        'log_category' => 'BATCH_CHARGE_USER_SUCCESS'
                    ]);
                }
                
                $progressBar->advance();
                
            } catch (\Exception $e) {
                $usersFailed++;
                
                $this->error("\n❌ Failed to charge user {$userId}: " . $e->getMessage());
                
                $this->logger->error('User consumption debt charge failed', [
                    'user_id' => $userId,
                    'error' => $e->getMessage(),
                    'log_category' => 'BATCH_CHARGE_USER_FAILED'
                ]);
                
                $progressBar->advance();
            }
        }
        
        $progressBar->finish();
        $this->newLine(2);
        
        // Summary
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('📊 BATCH CHARGE SUMMARY');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info("✅ Users charged: {$usersCharged}");
        $this->info("💎 Total Egili charged: {$totalCharged}");
        $this->info("📋 Ledger entries processed: {$totalEntries}");
        
        if ($usersFailed > 0) {
            $this->warn("⚠️  Users failed: {$usersFailed}");
        }
        
        $this->logger->info('Batch charge process completed', [
            'users_with_debt' => $usersWithDebt->count(),
            'users_charged' => $usersCharged,
            'users_failed' => $usersFailed,
            'total_egili_charged' => $totalCharged,
            'total_entries_processed' => $totalEntries,
            'force_mode' => $force,
            'log_category' => 'BATCH_CHARGE_COMPLETED'
        ]);
        
        return Command::SUCCESS;
    }
}
