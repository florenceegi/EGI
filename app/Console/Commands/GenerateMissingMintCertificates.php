<?php

namespace App\Console\Commands;

use App\Models\Egi;
use App\Models\EgiBlockchain;
use App\Models\EgiReservationCertificate;
use App\Services\CertificateGeneratorService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * @Oracode Artisan Command: GenerateMissingMintCertificates
 * 🎯 Purpose: Backfill mint certificates for EGIs that were minted before certificate system
 * 🛡️ Context: Historical data migration for mint/rebind history feature
 *
 * @package App\Console\Commands
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Data Migration)
 * @date 2025-10-17
 *
 * Usage: php artisan egi:generate-missing-mint-certificates [--dry-run] [--egi-id=16]
 */
class GenerateMissingMintCertificates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'egi:generate-missing-mint-certificates
                            {--dry-run : Run without making changes}
                            {--egi-id= : Generate certificate for specific EGI ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate mint certificates for EGIs that were minted before certificate system was implemented';

    /**
     * @var CertificateGeneratorService
     */
    protected CertificateGeneratorService $certificateGenerator;

    /**
     * @var UltraLogManager
     */
    protected UltraLogManager $logger;

    /**
     * Constructor
     */
    public function __construct(
        CertificateGeneratorService $certificateGenerator,
        UltraLogManager $logger
    ) {
        parent::__construct();
        $this->certificateGenerator = $certificateGenerator;
        $this->logger = $logger;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $egiId = $this->option('egi-id');

        $this->info('🔍 Scanning for EGIs with blockchain records but no mint certificates...');
        $this->newLine();

        // Build query
        $query = Egi::whereNotNull('token_EGI')
            ->has('blockchain') // Must have blockchain record
            ->doesntHave('mintCertificates'); // But no mint certificates

        // Filter by specific EGI if provided
        if ($egiId) {
            $query->where('id', $egiId);
        }

        $egisWithoutCerts = $query->get();

        if ($egisWithoutCerts->isEmpty()) {
            $this->info('✅ No EGIs found without mint certificates!');
            return Command::SUCCESS;
        }

        $this->warn("Found {$egisWithoutCerts->count()} EGI(s) without mint certificates:");
        $this->newLine();

        // Display table of EGIs
        $tableData = $egisWithoutCerts->map(function ($egi) {
            return [
                'ID' => $egi->id,
                'Title' => $egi->title ?? 'N/A',
                'ASA ID' => $egi->token_EGI,
                'Blockchain ID' => $egi->blockchain->id ?? 'N/A',
                'Minted At' => $egi->blockchain->minted_at ?? 'N/A',
            ];
        })->toArray();

        $this->table(
            ['ID', 'Title', 'ASA ID', 'Blockchain ID', 'Minted At'],
            $tableData
        );

        $this->newLine();

        if ($dryRun) {
            $this->warn('🔸 DRY RUN MODE - No changes will be made');
            $this->info('Remove --dry-run flag to generate certificates');
            return Command::SUCCESS;
        }

        // Confirm before proceeding
        if (!$this->confirm('Generate mint certificates for these EGIs?', true)) {
            $this->info('Operation cancelled.');
            return Command::SUCCESS;
        }

        $this->newLine();
        $this->info('🚀 Generating mint certificates...');
        $this->newLine();

        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        // Progress bar
        $progressBar = $this->output->createProgressBar($egisWithoutCerts->count());
        $progressBar->start();

        foreach ($egisWithoutCerts as $egi) {
            try {
                DB::transaction(function () use ($egi) {
                    // Get blockchain record
                    $egiBlockchain = $egi->blockchain;

                    if (!$egiBlockchain) {
                        throw new \Exception("No blockchain record found for EGI {$egi->id}");
                    }

                    // Generate certificate using existing service
                    $certificate = $this->certificateGenerator->generateBlockchainCertificate(
                        $egi,
                        $egiBlockchain
                    );

                    // Log success
                    $this->logger->info('MINT_CERT_BACKFILL: Certificate generated', [
                        'egi_id' => $egi->id,
                        'certificate_uuid' => $certificate->certificate_uuid,
                        'asa_id' => $egiBlockchain->asa_id,
                    ]);
                });

                $successCount++;
            } catch (\Exception $e) {
                $errorCount++;
                $errors[] = [
                    'egi_id' => $egi->id,
                    'error' => $e->getMessage(),
                ];

                $this->logger->error('MINT_CERT_BACKFILL_ERROR', [
                    'egi_id' => $egi->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Display results
        $this->info("✅ Successfully generated: {$successCount} certificate(s)");

        if ($errorCount > 0) {
            $this->error("❌ Failed: {$errorCount} certificate(s)");
            $this->newLine();
            $this->warn('Errors:');
            foreach ($errors as $error) {
                $this->line("  - EGI {$error['egi_id']}: {$error['error']}");
            }
        }

        $this->newLine();
        $this->info('🎉 Mint certificate backfill complete!');

        return Command::SUCCESS;
    }
}
