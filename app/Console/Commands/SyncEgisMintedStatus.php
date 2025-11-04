<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * @Oracode Command: Sync egis table with egi_blockchain minted records
 * 🎯 Purpose: Update egis.mint, token_EGI, status for already minted EGI
 * 🧱 Core Logic: Sync data from egi_blockchain to egis for correct card display
 * 🛡️ Security: Read-only check before update, transaction-safe
 *
 * @package App\Console\Commands
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Mint Data Sync)
 * @date 2025-11-04
 * @purpose Sync egis table with egi_blockchain minted status
 */
class SyncEgisMintedStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'egis:sync-minted-status {--dry-run : Show what would be updated without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync egis table with egi_blockchain minted records (mint, token_EGI, status)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Checking for egis records to sync with egi_blockchain...');
        $this->newLine();

        // Find records that need sync
        $recordsToSync = DB::table('egis')
            ->join('egi_blockchain', 'egis.id', '=', 'egi_blockchain.egi_id')
            ->where('egi_blockchain.mint_status', 'minted')
            ->whereNotNull('egi_blockchain.asa_id')
            ->where(function($query) {
                $query->where('egis.mint', '!=', 1)
                      ->orWhereNull('egis.mint')
                      ->orWhereNull('egis.token_EGI');
            })
            ->select(
                'egis.id as egi_id',
                'egis.title',
                'egis.mint as current_mint',
                'egis.token_EGI as current_token_EGI',
                'egis.status as current_status',
                'egi_blockchain.asa_id',
                'egi_blockchain.buyer_user_id',
                'egi_blockchain.minted_at'
            )
            ->get();

        $count = $recordsToSync->count();

        if ($count === 0) {
            $this->info('✅ No records need syncing. All egis are already up to date!');
            return 0;
        }

        $this->warn("Found {$count} egis records to sync:");
        $this->newLine();

        // Display table of records to update
        $this->table(
            ['EGI ID', 'Title', 'Current mint', 'Current token_EGI', 'Current status', 'Will update to ASA'],
            $recordsToSync->map(function($record) {
                return [
                    $record->egi_id,
                    \Str::limit($record->title, 30),
                    $record->current_mint ? 'true' : 'false',
                    $record->current_token_EGI ?? 'NULL',
                    $record->current_status,
                    $record->asa_id
                ];
            })->toArray()
        );

        if ($this->option('dry-run')) {
            $this->info('🏃 DRY RUN mode - no changes made');
            return 0;
        }

        // Confirm before proceeding
        if (!$this->confirm("Do you want to update these {$count} records?", true)) {
            $this->warn('❌ Operation cancelled by user');
            return 1;
        }

        // Execute update in transaction
        DB::beginTransaction();

        try {
            $updated = DB::table('egis')
                ->join('egi_blockchain', 'egis.id', '=', 'egi_blockchain.egi_id')
                ->where('egi_blockchain.mint_status', 'minted')
                ->whereNotNull('egi_blockchain.asa_id')
                ->where(function($query) {
                    $query->where('egis.mint', '!=', 1)
                          ->orWhereNull('egis.mint')
                          ->orWhereNull('egis.token_EGI');
                })
                ->update([
                    'egis.mint' => 1,
                    'egis.token_EGI' => DB::raw('egi_blockchain.asa_id'),
                    'egis.status' => 'minted',
                    'egis.owner_id' => DB::raw('egi_blockchain.buyer_user_id'),
                    'egis.updated_at' => now()
                ]);

            DB::commit();

            $this->newLine();
            $this->info("✅ Successfully updated {$updated} egis records");
            $this->info('🎨 Cards will now display minted status correctly!');

            return 0;

        } catch (\Exception $e) {
            DB::rollBack();

            $this->error('❌ Failed to sync records: ' . $e->getMessage());
            return 1;
        }
    }
}

