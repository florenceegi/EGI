<?php

namespace App\Console\Commands;

use App\Models\NatanUnifiedContext;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanupUnifiedContext extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'natan:cleanup-unified-context 
                            {--dry-run : Show what would be deleted without actually deleting}
                            {--force : Force deletion without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup expired chunks from natan_unified_context table (TTL: acts 30d, web 6h, memory 7d, files 90d)';

    /**
     * Execute the console command.
     */
    public function handle() {
        $isDryRun = $this->option('dry-run');
        $isForce = $this->option('force');

        $this->info('🧹 Starting cleanup of expired unified context chunks...');
        $this->newLine();

        // Count expired chunks by type
        $expiredChunks = NatanUnifiedContext::expired()->get();

        if ($expiredChunks->isEmpty()) {
            $this->info('✅ No expired chunks found. Database is clean!');
            return 0;
        }

        $totalExpired = $expiredChunks->count();
        $byType = $expiredChunks->groupBy('source_type')->map->count();

        // Show summary
        $this->table(
            ['Source Type', 'Expired Chunks'],
            $byType->map(fn($count, $type) => [$type, $count])->values()->toArray()
        );

        $this->info("Total expired chunks: {$totalExpired}");
        $this->newLine();

        // Calculate storage to be freed
        $storageKB = round($expiredChunks->sum(fn($chunk) => strlen($chunk->content)) / 1024, 2);
        $this->info("💾 Storage to be freed: ~{$storageKB} KB");
        $this->newLine();

        if ($isDryRun) {
            $this->warn('🔍 DRY RUN MODE - No data will be deleted');
            $this->info('Chunks that would be deleted:');

            foreach ($expiredChunks->take(10) as $chunk) {
                $this->line("  - [{$chunk->source_type}] {$chunk->source_title} (expired: {$chunk->expires_at->diffForHumans()})");
            }

            if ($totalExpired > 10) {
                $this->line("  ... and " . ($totalExpired - 10) . " more");
            }

            return 0;
        }

        // Confirmation (unless --force)
        if (!$isForce) {
            if (!$this->confirm("Delete {$totalExpired} expired chunks?", false)) {
                $this->warn('❌ Cleanup cancelled');
                return 1;
            }
        }

        // Delete expired chunks
        $this->info('🗑️  Deleting expired chunks...');

        $startTime = microtime(true);
        $deleted = NatanUnifiedContext::expired()->delete();
        $duration = round((microtime(true) - $startTime) * 1000);

        // Log cleanup
        Log::info('[CleanupUnifiedContext] Cleanup completed', [
            'deleted_count' => $deleted,
            'by_type' => $byType->toArray(),
            'storage_freed_kb' => $storageKB,
            'duration_ms' => $duration,
        ]);

        $this->newLine();
        $this->info("✅ Cleanup completed successfully!");
        $this->info("   Deleted: {$deleted} chunks");
        $this->info("   Freed: ~{$storageKB} KB");
        $this->info("   Duration: {$duration}ms");

        return 0;
    }
}
