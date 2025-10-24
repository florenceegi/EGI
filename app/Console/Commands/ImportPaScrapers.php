<?php

namespace App\Console\Commands;

use App\Models\PaWebScraper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ImportPaScrapers extends Command
{
    protected $signature = 'pa:import-scrapers {--input=scrapers_export.json} {--clean : Delete existing scrapers before import}';
    
    protected $description = 'Import PA web scrapers configuration from JSON file';

    public function handle()
    {
        $input = $this->option('input');
        $filePath = storage_path("app/{$input}");
        
        if (!File::exists($filePath)) {
            $this->error("❌ File not found: {$filePath}");
            return 1;
        }
        
        $this->info("📥 Reading scrapers from: {$input}");
        
        $json = File::get($filePath);
        $scrapers = json_decode($json, true);
        
        if (!is_array($scrapers)) {
            $this->error("❌ Invalid JSON format");
            return 1;
        }
        
        $this->info("📦 Found {$scrapers->count()} scrapers to import");
        
        if ($this->option('clean')) {
            $this->warn("🗑️  Deleting existing scrapers...");
            PaWebScraper::truncate();
        }
        
        $imported = 0;
        
        foreach ($scrapers as $scraperData) {
            try {
                PaWebScraper::create([
                    'name' => $scraperData['name'],
                    'base_url' => $scraperData['base_url'],
                    'type' => $scraperData['type'],
                    'config' => $scraperData['config'],
                    'pa_entity_id' => $scraperData['pa_entity_id'],
                    'is_active' => $scraperData['is_active'] ?? true,
                    'schedule' => $scraperData['schedule'] ?? null,
                    'last_scrape_at' => $scraperData['last_scrape_at'] ?? null,
                    'last_error' => $scraperData['last_error'] ?? null,
                ]);
                
                $imported++;
                $this->info("  ✅ Imported: {$scraperData['name']}");
                
            } catch (\Exception $e) {
                $this->error("  ❌ Failed: {$scraperData['name']} - {$e->getMessage()}");
            }
        }
        
        $this->newLine();
        $this->info("✅ Import completed! Imported {$imported}/{$scrapers->count()} scrapers");
        
        return 0;
    }
}

