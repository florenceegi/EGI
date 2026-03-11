<?php

namespace App\Console\Commands;

use App\Models\Egi;
use App\Models\EgiTrait;
use App\Services\Ipfs\IpfsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * @package App\Console\Commands
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Dev Cleanup)
 * @date 2026-03-11
 * @purpose Hard-delete ALL EGIs with full storage cleanup:
 *          S3 files (original + 4 WebP variants), Pinata IPFS unpins,
 *          EgiTrait (Eloquent→Spatie observer), Spatie media records.
 *
 * USAGE:
 *   php artisan egi:purge-all          → DRY-RUN (safe, no changes)
 *   php artisan egi:purge-all --force  → ACTUAL deletion (irreversible)
 */
class PurgeAllEgisCommand extends Command
{
    protected $signature = 'egi:purge-all
                            {--force : Execute actual deletion (default is dry-run)}
                            {--skip-s3 : Skip S3 file deletion}
                            {--skip-ipfs : Skip Pinata IPFS unpins}';

    protected $description = 'Purge ALL EGIs with full cleanup: S3, Pinata IPFS, Spatie media, DB records.';

    /** Image variants generated for each EGI (from config/image-optimization.php) */
    private const EGI_VARIANTS = ['thumbnail', 'mobile', 'tablet', 'desktop'];

    public function handle(IpfsService $ipfsService): int
    {
        $isDryRun = !$this->option('force');
        $skipS3   = $this->option('skip-s3');
        $skipIpfs = $this->option('skip-ipfs');

        $this->newLine();
        if ($isDryRun) {
            $this->warn('╔══════════════════════════════════════════════════╗');
            $this->warn('║           DRY-RUN — NO CHANGES MADE             ║');
            $this->warn('║   Run with --force to execute actual deletion   ║');
            $this->warn('╚══════════════════════════════════════════════════╝');
        } else {
            $this->error('╔══════════════════════════════════════════════════╗');
            $this->error('║        ⚠️  LIVE MODE — IRREVERSIBLE ⚠️            ║');
            $this->error('╚══════════════════════════════════════════════════╝');
            if (!$this->confirm('Are you sure you want to permanently delete ALL EGIs, their S3 files and Pinata pins?', false)) {
                $this->info('Aborted.');
                return self::SUCCESS;
            }
        }
        $this->newLine();

        // ─────────────────────────────────────────────────────────────
        // 1. Load ALL EGIs (including soft-deleted)
        // ─────────────────────────────────────────────────────────────
        $egis = Egi::withTrashed()->get();
        $this->info("Found <comment>{$egis->count()}</comment> EGIs to process.");
        $this->newLine();

        // ─────────────────────────────────────────────────────────────
        // 2. Build S3 file list & deduplicated IPFS CID list
        // ─────────────────────────────────────────────────────────────
        $s3FilesToDelete  = [];
        $ipfsCidsToUnpin  = []; // keyed by CID → EGI IDs that share it

        foreach ($egis as $egi) {
            // S3 paths
            if ($egi->collection_id && $egi->user_id && $egi->key_file) {
                $basePath = sprintf(
                    'users_files/collections_%d/creator_%d',
                    $egi->collection_id,
                    $egi->user_id
                );

                // Original file (e.g. 37.jpg)
                if ($egi->extension) {
                    $s3FilesToDelete[] = "{$basePath}/{$egi->key_file}.{$egi->extension}";
                }

                // WebP variants: 37_thumbnail.webp, 37_mobile.webp, etc.
                foreach (self::EGI_VARIANTS as $variant) {
                    $s3FilesToDelete[] = "{$basePath}/{$egi->key_file}_{$variant}.webp";
                }
            }

            // Collect unique IPFS CIDs (ipfs_cid column)
            if (!empty($egi->ipfs_cid)) {
                $ipfsCidsToUnpin[$egi->ipfs_cid][] = $egi->id;
            }
            // Also handle legacy file_IPFS column
            if (!empty($egi->file_IPFS)) {
                $ipfsCidsToUnpin[$egi->file_IPFS][] = $egi->id;
            }
        }

        // ─────────────────────────────────────────────────────────────
        // 3. Report plan
        // ─────────────────────────────────────────────────────────────
        $this->line('<info>── S3 FILES ──────────────────────────────────</info>');
        if ($skipS3) {
            $this->warn('  [SKIP] S3 deletion skipped (--skip-s3)');
        } else {
            $this->line("  Files to attempt delete: <comment>" . count($s3FilesToDelete) . "</comment>");
            foreach ($s3FilesToDelete as $path) {
                $this->line("  → {$path}");
            }
        }

        $this->newLine();
        $this->line('<info>── PINATA IPFS PINS ──────────────────────────</info>');
        if ($skipIpfs) {
            $this->warn('  [SKIP] IPFS unpins skipped (--skip-ipfs)');
        } elseif (!$ipfsService->isEnabled()) {
            $this->warn('  [SKIP] IPFS service disabled in config (IPFS_ENABLED=false or no JWT)');
        } else {
            $this->line("  Unique CIDs to unpin: <comment>" . count($ipfsCidsToUnpin) . "</comment> (from " . collect($ipfsCidsToUnpin)->map(fn($ids) => count($ids))->sum() . " EGI records)");
            foreach ($ipfsCidsToUnpin as $cid => $egiIds) {
                $shared = count($egiIds) > 1 ? " ⚠️  shared by EGI IDs: " . implode(',', $egiIds) : '';
                $this->line("  → {$cid}{$shared}");
            }
        }

        $this->newLine();
        $this->line('<info>── DB RECORDS ────────────────────────────────</info>');
        $traitCount = EgiTrait::whereIn('egi_id', $egis->pluck('id'))->count();
        $this->line("  EgiTrait records (+ Spatie media): <comment>{$traitCount}</comment>");
        $this->line("  EGI records (hard delete):         <comment>{$egis->count()}</comment>");

        $this->newLine();

        if ($isDryRun) {
            $this->warn('DRY-RUN complete. No changes made.');
            $this->warn('Run with --force to execute.');
            return self::SUCCESS;
        }

        // ─────────────────────────────────────────────────────────────
        // 4. EXECUTE — S3 deletion
        // ─────────────────────────────────────────────────────────────
        if (!$skipS3) {
            $this->line('<info>Deleting S3 files...</info>');
            $disk       = Storage::disk('s3');
            $s3Deleted  = 0;
            $s3Missing  = 0;
            $s3Errors   = 0;

            foreach ($s3FilesToDelete as $path) {
                try {
                    if ($disk->exists($path)) {
                        $disk->delete($path);
                        $this->line("  <info>✓</info> Deleted: {$path}");
                        $s3Deleted++;
                    } else {
                        $this->line("  <comment>–</comment> Not found (skip): {$path}");
                        $s3Missing++;
                    }
                } catch (\Exception $e) {
                    $this->error("  ✗ Error deleting {$path}: " . $e->getMessage());
                    $s3Errors++;
                }
            }
            $this->info("  S3 result: deleted={$s3Deleted}, not_found={$s3Missing}, errors={$s3Errors}");
            $this->newLine();
        }

        // ─────────────────────────────────────────────────────────────
        // 5. EXECUTE — Pinata IPFS unpin
        // ─────────────────────────────────────────────────────────────
        if (!$skipIpfs && $ipfsService->isEnabled()) {
            $this->line('<info>Unpinning from Pinata...</info>');
            $ipfsUnpinned = 0;
            $ipfsErrors   = 0;

            foreach ($ipfsCidsToUnpin as $cid => $egiIds) {
                try {
                    $result = $ipfsService->unpin($cid);
                    if ($result) {
                        $this->line("  <info>✓</info> Unpinned: {$cid}");
                        $ipfsUnpinned++;
                    } else {
                        $this->warn("  <comment>?</comment> Unpin returned false (already gone?): {$cid}");
                        $ipfsErrors++;
                    }
                } catch (\Exception $e) {
                    $this->error("  ✗ Unpin error {$cid}: " . $e->getMessage());
                    $ipfsErrors++;
                }
            }
            $this->info("  IPFS result: unpinned={$ipfsUnpinned}, errors={$ipfsErrors}");
            $this->newLine();
        }

        // ─────────────────────────────────────────────────────────────
        // 6. EXECUTE — DB cleanup (in transaction)
        // ─────────────────────────────────────────────────────────────
        $this->line('<info>Cleaning DB records...</info>');

        DB::transaction(function () use ($egis) {
            $egiIds = $egis->pluck('id')->toArray();

            // 6a. EgiTrait via Eloquent → triggers Spatie MediaLibrary observer
            //     (deletes Spatie media files + media table rows)
            $traits = EgiTrait::whereIn('egi_id', $egiIds)->get();
            $traitDeleted = 0;
            foreach ($traits as $trait) {
                $trait->delete();
                $traitDeleted++;
            }
            $this->line("  <info>✓</info> EgiTrait deleted (Eloquent): {$traitDeleted}");

            // 6b. Hard delete all EGIs (forceDelete handles soft-deleted too)
            //     FK cascade: coa → cascade
            //     FK set null: epp_egis, invoice_items, payment_distributions, reservations, etc.
            $count = Egi::withTrashed()->whereIn('id', $egiIds)->forceDelete();
            $this->line("  <info>✓</info> EGI hard deleted: {$count}");
        });

        $this->newLine();
        $this->info('════════════════════════════════════════════════');
        $this->info('✅  PURGE COMPLETE');
        $this->info('════════════════════════════════════════════════');

        return self::SUCCESS;
    }
}
