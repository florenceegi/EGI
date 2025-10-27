<?php

namespace App\Jobs;

use App\Models\PaWebScraper;
use App\Models\User;
use App\Services\PaActs\PaWebScraperService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

/**
 * Execute Web Scraper Job - Background Processing
 *
 * @package App\Jobs
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0
 * @date 2025-10-27
 * @purpose Execute web scraper asynchronously to prevent HTTP timeout
 */
class ExecuteScraperJob implements ShouldQueue {
    use Queueable;

    public $timeout = 600; // 10 minutes max execution
    public $tries = 1; // Single attempt (scraping is idempotent)

    /**
     * Create a new job instance.
     */
    public function __construct(
        public PaWebScraper $scraper,
        public User $user,
        public array $options = []
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(PaWebScraperService $scraperService): void {
        Log::info('[ExecuteScraperJob] Starting background scraper execution', [
            'scraper_id' => $this->scraper->id,
            'user_id' => $this->user->id,
            'options' => $this->options,
        ]);

        try {
            // Execute scraper with GDPR compliance
            $result = $scraperService->execute($this->scraper, $this->user, $this->options);

            if ($result['success']) {
                $actsSaved = $result['stats']['acts_saved'] ?? 0;
                $actsSkipped = $result['stats']['acts_skipped'] ?? 0;
                $actsCount = $result['stats']['acts_count'] ?? 0;

                // Update scraper stats
                $this->scraper->update([
                    'total_acts_scraped' => $this->scraper->total_acts_scraped + $actsSaved,
                    'last_run_at' => now(),
                ]);

                Log::info('[ExecuteScraperJob] Scraper execution completed', [
                    'scraper_id' => $this->scraper->id,
                    'acts_extracted' => $actsCount,
                    'acts_saved' => $actsSaved,
                    'acts_skipped' => $actsSkipped,
                    'execution_time' => $result['stats']['execution_time'] ?? 'N/A',
                ]);

                // TODO: Notify user via notification/toast when done
                // $this->user->notify(new ScraperCompletedNotification($result));
            } else {
                Log::error('[ExecuteScraperJob] Scraper execution failed', [
                    'scraper_id' => $this->scraper->id,
                    'error' => $result['error'] ?? 'Unknown error',
                ]);
            }
        } catch (\Exception $e) {
            Log::error('[ExecuteScraperJob] Exception during scraper execution', [
                'scraper_id' => $this->scraper->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e; // Re-throw to mark job as failed
        }
    }
}
