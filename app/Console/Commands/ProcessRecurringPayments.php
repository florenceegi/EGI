<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RecurringPaymentService;
use Ultra\UltraLogManager\UltraLogManager;

class ProcessRecurringPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'egi:process-recurring-payments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process all due recurring subscription payments (Egili)';

    /**
     * Execute the console command.
     */
    public function handle(RecurringPaymentService $service, UltraLogManager $logger)
    {
        $this->info('Starting recurring payments process...');
        $logger->info('Starting recurring payments process via Artisan', ['log_category' => 'RECURRING_PAYMENTS_START']);

        try {
            $service->processDueRenewals();
            $this->info('Recurring payments processed successfully.');
            $logger->info('Recurring payments process completed', ['log_category' => 'RECURRING_PAYMENTS_COMPLETE']);
        } catch (\Throwable $e) {
            $this->error('Error processing recurring payments: ' . $e->getMessage());
            $logger->error('Error processing recurring payments', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'log_category' => 'RECURRING_PAYMENTS_ERROR'
            ]);
            return 1;
        }

        return 0;
    }
}
