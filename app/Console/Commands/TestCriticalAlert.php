<?php

namespace App\Console\Commands;

use App\Services\CriticalAlertService;
use Illuminate\Console\Command;

/**
 * Test Critical Alert System
 *
 * @package App\Console\Commands
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 */
class TestCriticalAlert extends Command
{
    protected $signature = 'alert:test 
                            {--type=email : Alert type (email, sms, all)}
                            {--severity=critical : Alert severity (critical, error, warning)}';

    protected $description = 'Test critical alert system (email, SMS, EGI-HUB)';

    protected CriticalAlertService $alertService;

    public function __construct(CriticalAlertService $alertService)
    {
        parent::__construct();
        $this->alertService = $alertService;
    }

    public function handle(): int
    {
        $type = $this->option('type');
        $severity = $this->option('severity');

        $this->info("🧪 Testing Critical Alert System");
        $this->info("Type: {$type}");
        $this->info("Severity: {$severity}");
        $this->newLine();

        $testContext = [
            'test' => true,
            'missing_accounts' => [
                ['wallet_id' => 15, 'platform_role' => 'Natan'],
                ['wallet_id' => 14, 'platform_role' => 'Frangette'],
            ],
            'missing_count' => 2,
            'timestamp' => now()->toIso8601String(),
        ];

        try {
            $this->alertService->sendCriticalAlert(
                'TEST_MINT_MISSING_STRIPE_ACCOUNTS',
                $testContext,
                $severity
            );

            $this->newLine();
            $this->info("✅ Alert sent successfully!");
            $this->newLine();

            $this->line("📧 Email: " . (config('app.critical_alerts.admin_email') ?: 'NOT CONFIGURED'));
            $this->line("📱 Phone: " . (config('app.critical_alerts.admin_phone') ?: 'NOT CONFIGURED'));
            $this->line("💬 SMS Enabled: " . (config('app.critical_alerts.sms_enabled') ? 'YES' : 'NO'));
            $this->line("🔌 SMS Provider: " . (config('app.critical_alerts.sms_provider') ?: 'NONE'));

            $this->newLine();
            $this->warn("⚠️  Check:");
            $this->line("  • Email inbox for alert message");
            $this->line("  • storage/logs/laravel.log for log entries");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("❌ Failed to send alert: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
