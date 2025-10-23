<?php

namespace Database\Seeders;

use App\Models\PaWebScraper;
use App\Models\Business;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * PA Web Scraper Seeder
 *
 * Popola database con scraper pre-configurati per testing e demo.
 *
 * Usage:
 * php artisan db:seed --class=PaWebScraperSeeder
 *
 * @package Database\Seeders
 */
class PaWebScraperSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Seeding PA Web Scrapers...');

        // Find first PA business (or create demo one)
        $paBusiness = Business::where('type', 'pa_entity')->first();

        if (!$paBusiness) {
            $this->command->warn('⚠️  No PA business found. Creating demo business...');
            $paBusiness = Business::create([
                'type' => 'pa_entity',
                'name' => 'Comune di Firenze (Demo)',
                'is_active' => true,
            ]);
        }

        // Find or create demo user
        $user = User::where('business_id', $paBusiness->id)->first();

        if (!$user) {
            $this->command->warn('⚠️  No user found for PA business. Skipping user assignment.');
            $userId = null;
        } else {
            $userId = $user->id;
        }

        // Scraper 1: Deliberazioni Firenze
        $firenze_delibere = PaWebScraper::updateOrCreate(
            [
                'name' => 'Delibere Comune di Firenze',
                'business_id' => $paBusiness->id,
            ],
            [
                'type' => 'api',
                'source_entity' => 'Comune di Firenze',
                'description' => 'Scraper per deliberazioni di giunta e consiglio dal portale amministrazione trasparente. ' .
                                'Recupera automaticamente atti PA con metadati completi, allegati PDF, votazioni e relatori.',
                'base_url' => 'https://accessoconcertificato.comune.fi.it',
                'api_endpoint' => '/trasparenza-atti-cat/searchAtti',
                'method' => 'POST',
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'User-Agent' => 'FlorenceEGI-PA-Scraper/1.0',
                ],
                'payload_template' => [
                    'oggetto' => '',
                    'notLoadIniziale' => 'ok',
                    'numeroAdozione' => '',
                    'competenza' => 'DG',
                    'annoAdozione' => '{{year}}',
                    'tipiAtto' => ['DG', 'DC']
                ],
                'query_params' => null,
                'data_mapping' => null,
                'pagination_type' => 'none',
                'pagination_config' => null,
                'is_active' => false, // Start as inactive
                'schedule_frequency' => 'weekly',
                'created_by_user_id' => $userId,
                'status' => 'draft',
                // GDPR fields
                'data_source_type' => 'public',
                'legal_basis' => 'Art. 23 D.Lgs 33/2013 - Obblighi di pubblicazione atti PA + Art. 32 D.Lgs 33/2013 - Obblighi di trasparenza amministrativa',
                'data_retention_policy' => 'Conservazione permanente come previsto da CAD Art. 22 - Copie informatiche di documenti amministrativi',
                'gdpr_compliant' => true,
                'pii_fields_to_exclude' => ['email', 'telefono', 'indirizzo', 'codice_fiscale', 'partita_iva'],
            ]
        );

        $this->command->info("✅ Created: {$firenze_delibere->name}");

        // Scraper 2: Albo Pretorio Firenze
        $firenze_albo = PaWebScraper::updateOrCreate(
            [
                'name' => 'Albo Pretorio Comune di Firenze',
                'business_id' => $paBusiness->id,
            ],
            [
                'type' => 'html',
                'source_entity' => 'Comune di Firenze',
                'description' => 'Scraper per atti in pubblicazione sull\'albo pretorio online. ' .
                                'Include ordinanze, avvisi, bandi, concorsi e altri atti amministrativi in fase di pubblicazione legale.',
                'base_url' => 'https://accessoconcertificato.comune.fi.it',
                'api_endpoint' => '/AOL/Affissione/ComuneFi/Page',
                'method' => 'GET',
                'headers' => [
                    'User-Agent' => 'FlorenceEGI-PA-Scraper/1.0',
                    'Accept' => 'text/html,application/xhtml+xml',
                ],
                'payload_template' => null,
                'query_params' => [],
                'data_mapping' => [
                    'selector' => '.card.concorso-card.multi-line',
                    'fields' => [
                        'numero' => '.col-sm-6:contains("N° registro")',
                        'tipo' => 'h3.card-title',
                        'oggetto' => 'p',
                    ]
                ],
                'pagination_type' => 'page',
                'pagination_config' => [
                    'param_name' => 'page',
                    'start' => 1,
                    'max_pages' => 50
                ],
                'is_active' => false,
                'schedule_frequency' => 'daily',
                'created_by_user_id' => $userId,
                'status' => 'draft',
                // GDPR fields
                'data_source_type' => 'public',
                'legal_basis' => 'Art. 32 D.Lgs 33/2013 - Obblighi di pubblicazione e accessibilità degli atti amministrativi',
                'data_retention_policy' => 'Conservazione limitata al periodo di pubblicazione legale (15 giorni) + archiviazione storica',
                'gdpr_compliant' => true,
                'pii_fields_to_exclude' => ['email', 'telefono', 'indirizzo', 'codice_fiscale'],
            ]
        );

        $this->command->info("✅ Created: {$firenze_albo->name}");

        $this->command->info("\n🎉 Seeding completed!");
        $this->command->info("📊 Total scrapers created: 2");
        $this->command->info("🏛️  Business: {$paBusiness->name} (#{$paBusiness->id})");

        $this->command->info("\n💡 To activate scrapers:");
        $this->command->info("   1. Login as PA user");
        $this->command->info("   2. Visit /pa/scrapers");
        $this->command->info("   3. Click 'Attiva' on desired scraper");
        $this->command->info("\n💡 To test scraper from CLI:");
        $this->command->info("   php artisan pa:scraper:run {$firenze_delibere->id} --user_id={$userId} --year=2024");
    }
}
