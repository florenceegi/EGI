<?php

namespace App\Console\Commands;

use App\Models\PaWebScraper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ExportPaScrapers extends Command
{
    protected $signature = 'pa:export-scrapers {--output=scrapers_export.json}';
    
    protected $description = 'Export PA web scrapers configuration to JSON file';

    public function handle()
    {
        $output = $this->option('output');
        
        $this->info('🔍 Fetching scrapers from database...');
        
        $scrapers = PaWebScraper::all()->map(function ($scraper) {
            return [
                'name' => $scraper->name,
                'base_url' => $scraper->base_url,
                'type' => $scraper->type,
                'config' => $scraper->config,
                'pa_entity_id' => $scraper->pa_entity_id,
                'is_active' => $scraper->is_active,
                'schedule' => $scraper->schedule,
                'last_scrape_at' => $scraper->last_scrape_at?->toDateTimeString(),
                'last_error' => $scraper->last_error,
            ];
        });
        
        $this->info("📦 Found {$scrapers->count()} scrapers");
        
        $json = json_encode($scrapers, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        
        File::put(storage_path("app/{$output}"), $json);
        
        $this->info("✅ Scrapers exported to: storage/app/{$output}");
        $this->newLine();
        $this->info("📋 To import on staging:");
        $this->info("1. Copy file to staging: scp storage/app/{$output} user@staging:/path/to/egi/storage/app/");
        $this->info("2. Run: php artisan pa:import-scrapers --input={$output}");
        
        return 0;
    }
}

