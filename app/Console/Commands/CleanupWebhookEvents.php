<?php

namespace App\Console\Commands;

use App\Models\PspWebhookEvent;
use Illuminate\Console\Command;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * P0.5 GAP FIX: Cleanup stuck webhook events
 *
 * Identifies and resets webhook events stuck in 'processing' state
 * Should be run via cron every 5-10 minutes
 */
class CleanupWebhookEvents extends Command {
    protected $signature = 'webhooks:cleanup
                           {--timeout=5 : Minutes before considering event stuck}
                           {--dry-run : Only show what would be cleaned, don\'t actually clean}';

    protected $description = 'Clean up stuck webhook events and reset failed events for retry';

    public function __construct(
        private UltraLogManager $logger
    ) {
        parent::__construct();
    }

    public function handle(): int {
        $timeoutMinutes = (int) $this->option('timeout');
        $dryRun = $this->option('dry-run');

        $this->info("Webhook cleanup started (timeout: {$timeoutMinutes}min, dry-run: " . ($dryRun ? 'yes' : 'no') . ")");

        // Find stuck processing events
        $stuckEvents = PspWebhookEvent::stuckProcessing($timeoutMinutes)->get();

        if ($stuckEvents->isEmpty()) {
            $this->info('No stuck webhook events found');
        } else {
            $this->info("Found {$stuckEvents->count()} stuck webhook events");

            foreach ($stuckEvents as $event) {
                $this->warn("Stuck event: {$event->provider}:{$event->event_id} ({$event->event_type}) - processing for " .
                    $event->received_at->diffInMinutes(now()) . " minutes");

                if (!$dryRun) {
                    $event->markFailed("Stuck in processing for over {$timeoutMinutes} minutes - auto-failed by cleanup");

                    $this->logger->warning('Webhook event auto-failed due to timeout', [
                        'event_id' => $event->event_id,
                        'provider' => $event->provider,
                        'event_type' => $event->event_type,
                        'stuck_minutes' => $event->received_at->diffInMinutes(now()),
                    ]);
                }
            }

            if (!$dryRun) {
                $this->info("Marked {$stuckEvents->count()} stuck events as failed");
            }
        }

        // Show retry candidates (failed events with low retry count)
        $retryableEvents = PspWebhookEvent::forRetry(3)->get();

        if (!$retryableEvents->isEmpty()) {
            $this->info("Found {$retryableEvents->count()} events eligible for retry:");

            foreach ($retryableEvents as $event) {
                $this->line("  - {$event->provider}:{$event->event_id} ({$event->event_type}) - retry {$event->retry_count}/3");
            }

            $this->comment('Use webhooks:retry command to retry failed events');
        }

        // Statistics
        $stats = [
            'total' => PspWebhookEvent::count(),
            'processing' => PspWebhookEvent::where('status', 'processing')->count(),
            'processed' => PspWebhookEvent::where('status', 'processed')->count(),
            'failed' => PspWebhookEvent::where('status', 'failed')->count(),
        ];

        $this->info('Webhook events statistics:');
        $this->table(
            ['Status', 'Count'],
            [
                ['Total', $stats['total']],
                ['Processing', $stats['processing']],
                ['Processed', $stats['processed']],
                ['Failed', $stats['failed']],
            ]
        );

        return self::SUCCESS;
    }
}
