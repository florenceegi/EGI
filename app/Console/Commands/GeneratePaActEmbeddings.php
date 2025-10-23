<?php

namespace App\Console\Commands;

use App\Models\Egi;
use App\Services\EmbeddingService;
use Illuminate\Console\Command;

/**
 * Generate PA Act Embeddings Command
 * 
 * Generates vector embeddings for all PA acts to enable semantic search.
 * 
 * USAGE:
 * php artisan pa:generate-embeddings              # All acts
 * php artisan pa:generate-embeddings --force      # Force regenerate
 * php artisan pa:generate-embeddings --limit=100  # First 100 acts
 * 
 * COST ESTIMATE:
 * - ~$0.0001 per 1K tokens
 * - Average act: ~200 tokens
 * - 24,000 acts: ~$0.02 (one-time)
 * 
 * TIME:
 * - ~0.5 sec per act (OpenAI API)
 * - 24,000 acts: ~3-4 hours
 * - TIP: Run in background with nohup
 */
class GeneratePaActEmbeddings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pa:generate-embeddings 
                            {--force : Force regenerate even if exists}
                            {--limit= : Limit number of acts to process}
                            {--skip-existing : Skip acts that already have embeddings}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate vector embeddings for PA acts (semantic search)';

    private EmbeddingService $embeddingService;

    public function __construct(EmbeddingService $embeddingService)
    {
        parent::__construct();
        $this->embeddingService = $embeddingService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $force = $this->option('force');
        $limit = $this->option('limit');
        $skipExisting = $this->option('skip-existing');

        $this->info('🚀 Starting PA Act Embeddings Generation...');
        $this->newLine();

        // Get PA acts
        $query = Egi::whereNotNull('pa_protocol_number')
            ->orderBy('created_at', 'desc');

        if ($skipExisting) {
            $query->doesntHave('embedding');
        }

        if ($limit) {
            $query->limit((int) $limit);
        }

        $acts = $query->get();
        $total = $acts->count();

        if ($total === 0) {
            $this->warn('⚠️  No PA acts found to process.');
            return 0;
        }

        $this->info("📊 Found {$total} PA acts to process");
        $this->newLine();

        // Confirm if large batch
        if ($total > 100 && !$this->confirm("This will process {$total} acts. Continue?", true)) {
            $this->warn('Operation cancelled.');
            return 0;
        }

        // Progress bar
        $bar = $this->output->createProgressBar($total);
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% - %message%');
        $bar->setMessage('Starting...');
        $bar->start();

        $success = 0;
        $failed = 0;
        $skipped = 0;

        foreach ($acts as $act) {
            $bar->setMessage("Processing EGI #{$act->id}...");

            try {
                $result = $this->embeddingService->generateForAct($act, $force);

                if ($result) {
                    $success++;
                } else {
                    $failed++;
                }
            } catch (\Exception $e) {
                $failed++;
                $this->newLine();
                $this->error("❌ Failed EGI #{$act->id}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Summary
        $this->info('✅ Embedding generation completed!');
        $this->newLine();
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total processed', $total],
                ['✅ Success', $success],
                ['❌ Failed', $failed],
                ['Success rate', number_format(($success / $total) * 100, 1) . '%'],
            ]
        );

        // Cost estimate
        $estimatedCost = ($total * 200 / 1000) * 0.0001; // 200 tokens avg per act
        $this->newLine();
        $this->info("💰 Estimated cost: ~$" . number_format($estimatedCost, 4));

        return $failed > 0 ? 1 : 0;
    }
}
