<?php

namespace App\Console\Commands\PaActs;

use App\Models\PaWebScraper;
use App\Models\User;
use App\Services\PaActs\PaWebScraperService;
use Illuminate\Console\Command;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * Run Web Scraper Command
 *
 * Execute PA web scrapers from CLI for manual or scheduled execution
 *
 * Usage:
 * php artisan pa:scraper:run {scraper_id} {--user_id=} {--year=} {--all}
 *
 * Examples:
 * php artisan pa:scraper:run 1 --user_id=5
 * php artisan pa:scraper:run 1 --user_id=5 --year=2024
 * php artisan pa:scraper:run --all  (Run all active scrapers)
 *
 * @package App\Console\Commands\PaActs
 */
class RunWebScraperCommand extends Command
{
    protected $signature = 'pa:scraper:run 
                            {scraper_id? : ID dello scraper da eseguire}
                            {--user_id= : ID dell\'utente che esegue lo scraper (per audit GDPR)}
                            {--year= : Anno da scrapare (opzionale)}
                            {--month= : Mese da scrapare (opzionale)}
                            {--all : Esegui tutti gli scraper attivi}';

    protected $description = 'Esegue web scraper PA per recupero atti da fonti esterne (GDPR compliant)';

    protected PaWebScraperService $scraperService;
    protected UltraLogManager $logger;

    public function __construct(
        PaWebScraperService $scraperService,
        UltraLogManager $logger
    ) {
        parent::__construct();
        $this->scraperService = $scraperService;
        $this->logger = $logger;
    }

    public function handle(): int
    {
        $this->info('🚀 PA Web Scraper Execution');
        $this->info('═══════════════════════════════════════');

        try {
            // Execute all active scrapers
            if ($this->option('all')) {
                return $this->runAllScrapers();
            }

            // Execute single scraper
            $scraperId = $this->argument('scraper_id');

            if (!$scraperId) {
                $this->error('❌ Devi specificare scraper_id o usare --all');
                return self::FAILURE;
            }

            $scraper = PaWebScraper::find($scraperId);

            if (!$scraper) {
                $this->error("❌ Scraper #{$scraperId} non trovato");
                return self::FAILURE;
            }

            // Get user for GDPR audit
            $userId = $this->option('user_id');

            if (!$userId) {
                // Use scraper creator as fallback
                $userId = $scraper->created_by_user_id;
            }

            $user = User::find($userId);

            if (!$user) {
                $this->error("❌ User #{$userId} non trovato");
                return self::FAILURE;
            }

            return $this->runSingleScraper($scraper, $user);

        } catch (\Exception $e) {
            $this->error("❌ Errore: {$e->getMessage()}");
            $this->logger->error('[RunWebScraperCommand] Command execution failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return self::FAILURE;
        }
    }

    protected function runSingleScraper(PaWebScraper $scraper, User $user): int
    {
        $this->info("\n📋 Scraper: {$scraper->name}");
        $this->info("🏛️  Ente: {$scraper->source_entity}");
        $this->info("🔗 URL: {$scraper->getFullUrl()}");
        $this->info("👤 User: {$user->name} (#{$user->id})");

        // Options
        $options = array_filter([
            'year' => $this->option('year'),
            'month' => $this->option('month'),
        ]);

        if ($options) {
            $this->info("⚙️  Options: " . json_encode($options));
        }

        $this->info("\n⏳ Esecuzione in corso...\n");

        // Execute
        $result = $this->scraperService->execute($scraper, $user, $options);

        if ($result['success']) {
            $this->info("✅ Scraping completato con successo!");
            $this->info("   📊 Atti estratti: {$result['stats']['acts_count']}");
            $this->info("   ⏱️  Tempo: {$result['stats']['execution_time']} secondi");
            $this->info("   📅 Scraped at: {$result['stats']['scraped_at']}");

            // Show sample acts
            if (count($result['acts']) > 0) {
                $this->info("\n📄 Primi 3 atti:");
                foreach (array_slice($result['acts'], 0, 3) as $act) {
                    $this->line("   • {$act['numero_atto']} - {$act['tipo_atto']}");
                    $this->line("     {$act['oggetto']}");
                }
            }

            return self::SUCCESS;
        } else {
            $this->error("❌ Scraping fallito: {$result['error']}");
            return self::FAILURE;
        }
    }

    protected function runAllScrapers(): int
    {
        $scrapers = PaWebScraper::active()->get();

        if ($scrapers->isEmpty()) {
            $this->warn('⚠️  Nessuno scraper attivo trovato');
            return self::SUCCESS;
        }

        $this->info("📋 Trovati {$scrapers->count()} scraper attivi\n");

        $successCount = 0;
        $failureCount = 0;

        foreach ($scrapers as $scraper) {
            $this->info("▶️  Esecuzione: {$scraper->name}");

            // Use scraper creator for audit
            $user = User::find($scraper->created_by_user_id);

            if (!$user) {
                $this->error("   ❌ User non trovato per scraper #{$scraper->id}");
                $failureCount++;
                continue;
            }

            $result = $this->scraperService->execute($scraper, $user, []);

            if ($result['success']) {
                $this->info("   ✅ {$result['stats']['acts_count']} atti estratti");
                $successCount++;
            } else {
                $this->error("   ❌ Fallito: {$result['error']}");
                $failureCount++;
            }

            $this->line(''); // Empty line
        }

        $this->info("\n═══════════════════════════════════════");
        $this->info("✅ Successi: {$successCount}");
        if ($failureCount > 0) {
            $this->error("❌ Fallimenti: {$failureCount}");
        }

        return $failureCount === 0 ? self::SUCCESS : self::FAILURE;
    }
}
