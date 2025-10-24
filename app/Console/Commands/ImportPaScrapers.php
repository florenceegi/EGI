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

        $this->info("📦 Found " . count($scrapers) . " scrapers to import");

        if ($this->option('clean')) {
            $this->warn("🗑️  Deleting existing scrapers...");
            PaWebScraper::truncate();
        }

        $imported = 0;

        foreach ($scrapers as $scraperData) {
            try {
                // Get authenticated user for user_id
                $userId = auth()->id() ?? 1; // Fallback to user ID 1 if not authenticated

                PaWebScraper::create([
                    'name' => $scraperData['name'],
                    'type' => $scraperData['type'] ?? 'api',
                    'source_entity' => $scraperData['source_entity'] ?? 'Unknown',
                    'description' => $scraperData['description'] ?? null,
                    'base_url' => $scraperData['base_url'],
                    'api_endpoint' => $scraperData['api_endpoint'] ?? null,
                    'method' => $scraperData['method'] ?? 'GET',
                    'headers' => $scraperData['headers'] ?? null,
                    'payload_template' => $scraperData['payload_template'] ?? null,
                    'query_params' => $scraperData['query_params'] ?? null,
                    'data_mapping' => $scraperData['data_mapping'] ?? null,
                    'pagination_type' => $scraperData['pagination_type'] ?? null,
                    'pagination_config' => $scraperData['pagination_config'] ?? null,
                    'is_active' => $scraperData['is_active'] ?? false,
                    'schedule_frequency' => $scraperData['schedule_frequency'] ?? null,
                    'user_id' => $userId,
                    'status' => $scraperData['status'] ?? 'draft',
                    'last_error' => $scraperData['last_error'] ?? null,
                    'data_source_type' => $scraperData['data_source_type'] ?? 'public',
                    'legal_basis' => $scraperData['legal_basis'] ?? null,
                    'gdpr_compliant' => $scraperData['gdpr_compliant'] ?? true,
                ]);

                $imported++;
                $this->info("  ✅ Imported: {$scraperData['name']}");
            } catch (\Exception $e) {
                $this->error("  ❌ Failed: {$scraperData['name']} - {$e->getMessage()}");
            }
        }

        $this->newLine();
        $this->info("✅ Import completed! Imported {$imported}/" . count($scrapers) . " scrapers");

        return 0;
    }
}