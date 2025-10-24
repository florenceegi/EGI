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
                'type' => $scraper->type,
                'source_entity' => $scraper->source_entity,
                'description' => $scraper->description,
                'base_url' => $scraper->base_url,
                'api_endpoint' => $scraper->api_endpoint,
                'method' => $scraper->method,
                'headers' => $scraper->headers,
                'payload_template' => $scraper->payload_template,
                'query_params' => $scraper->query_params,
                'data_mapping' => $scraper->data_mapping,
                'pagination_type' => $scraper->pagination_type,
                'pagination_config' => $scraper->pagination_config,
                'is_active' => $scraper->is_active,
                'schedule_frequency' => $scraper->schedule_frequency,
                'status' => $scraper->status,
                'last_error' => $scraper->last_error,
                'data_source_type' => $scraper->data_source_type,
                'legal_basis' => $scraper->legal_basis,
                'gdpr_compliant' => $scraper->gdpr_compliant,
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
