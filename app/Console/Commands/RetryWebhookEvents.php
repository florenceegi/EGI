<?php

namespace App\Console\Commands;

use App\Models\PspWebhookEvent;
use App\Http\Controllers\Payment\PspWebhookController;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * P0.5 GAP FIX: Retry failed webhook events
 *
 * Manually retry webhook events that failed due to temporary issues
 */
class RetryWebhookEvents extends Command {
    protected $signature = 'webhooks:retry
                           {--id= : Specific webhook event ID to retry}
                           {--max-retries=3 : Maximum retry attempts}
                           {--dry-run : Only show what would be retried}';

    protected $description = 'Retry failed webhook events';

    public function __construct(
        private UltraLogManager $logger,
        private PspWebhookController $webhookController
    ) {
        parent::__construct();
    }

    public function handle(): int {
        $eventId = $this->option('id');
        $maxRetries = (int) $this->option('max-retries');
        $dryRun = $this->option('dry-run');

        if ($eventId) {
            return $this->retrySingleEvent($eventId, $dryRun);
        }

        return $this->retryFailedEvents($maxRetries, $dryRun);
    }

    private function retrySingleEvent(string $eventId, bool $dryRun): int {
        $event = PspWebhookEvent::find($eventId);

        if (!$event) {
            $this->error("Webhook event {$eventId} not found");
            return self::FAILURE;
        }

        if ($event->status === 'processed') {
            $this->warn("Event {$eventId} already processed successfully");
            return self::SUCCESS;
        }

        $this->info("Retrying webhook event: {$event->provider}:{$event->event_id} ({$event->event_type})");

        if ($dryRun) {
            $this->info('[DRY RUN] Would retry this event');
            return self::SUCCESS;
        }

        return $this->executeRetry($event);
    }

    private function retryFailedEvents(int $maxRetries, bool $dryRun): int {
        $failedEvents = PspWebhookEvent::forRetry($maxRetries)
            ->orderBy('received_at')
            ->limit(10) // Process in small batches
            ->get();

        if ($failedEvents->isEmpty()) {
            $this->info('No failed webhook events found for retry');
            return self::SUCCESS;
        }

        $this->info("Found {$failedEvents->count()} failed events to retry");

        $successCount = 0;
        $failureCount = 0;

        foreach ($failedEvents as $event) {
            $this->info("Retrying: {$event->provider}:{$event->event_id} (attempt " . ($event->retry_count + 1) . ")");

            if ($dryRun) {
                $this->line('[DRY RUN] Would retry this event');
                continue;
            }

            if ($this->executeRetry($event) === self::SUCCESS) {
                $successCount++;
            } else {
                $failureCount++;
            }
        }

        if (!$dryRun) {
            $this->info("Retry completed: {$successCount} succeeded, {$failureCount} failed");
        }

        return self::SUCCESS;
    }

    private function executeRetry(PspWebhookEvent $event): int {
        try {
            // Reset event status for retry
            $event->resetForRetry();

            // Create a fake request from stored payload
            $request = new Request();
            $request->merge($event->payload);
            $request->headers->add(['content-type' => 'application/json']);

            // Determine the retry method based on provider
            $response = match ($event->provider) {
                'stripe' => $this->webhookController->stripe($request),
                'paypal' => $this->webhookController->paypal($request),
                default => throw new \Exception("Unsupported provider: {$event->provider}")
            };

            if ($response->getStatusCode() === 200) {
                $this->info("✓ Successfully retried {$event->event_id}");

                $this->logger->info('Webhook event manually retried successfully', [
                    'event_id' => $event->event_id,
                    'provider' => $event->provider,
                    'retry_count' => $event->retry_count,
                ]);

                return self::SUCCESS;
            } else {
                throw new \Exception("Webhook returned status: " . $response->getStatusCode());
            }
        } catch (\Exception $e) {
            $this->error("✗ Failed to retry {$event->event_id}: " . $e->getMessage());

            $this->logger->error('Webhook event retry failed', [
                'event_id' => $event->event_id,
                'provider' => $event->provider,
                'retry_count' => $event->retry_count,
                'error' => $e->getMessage(),
            ]);

            return self::FAILURE;
        }
    }
}
