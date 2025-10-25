<?php

namespace App\Console\Commands;

use App\Models\Egi;
use App\Models\EgiBlockchain;
use Illuminate\Console\Command;

class FixInconsistentEgis extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'egi:fix-inconsistent-data {--dry-run : Mostra cosa verrebbe fatto senza applicare modifiche}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Corregge EGI con token_EGI ma senza record blockchain e egi_type';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->warn('🔍 DRY RUN MODE - Nessuna modifica verrà applicata');
        }

        $this->info('🔍 Ricerca EGI inconsistenti...');

        // Trova tutti gli EGI con token_EGI ma senza blockchain record
        $inconsistentEgis = Egi::whereNotNull('token_EGI')
            ->whereDoesntHave('blockchain')
            ->get();

        if ($inconsistentEgis->isEmpty()) {
            $this->info('✅ Nessun EGI inconsistente trovato!');
            return 0;
        }

        $this->warn("🚨 Trovati {$inconsistentEgis->count()} EGI inconsistenti");
        $this->newLine();

        $fixed = 0;
        $errors = 0;

        foreach ($inconsistentEgis as $egi) {
            $this->line("📦 EGI #{$egi->id} - {$egi->title}");
            $this->line("   token_EGI: {$egi->token_EGI}");
            $this->line("   egi_type: " . ($egi->egi_type ?? 'NULL'));

            if ($isDryRun) {
                $this->info("   [DRY RUN] Imposterebbe:");
                $this->info("   - egi_type = 'ASA'");
                $this->info("   - Creerebbe record egi_blockchain con asa_id = {$egi->token_EGI}");
                $fixed++;
            } else {
                try {
                    // 1. Imposta egi_type e mint
                    $egi->egi_type = 'ASA';
                    $egi->mint = 1; // Marca come mintato
                    $egi->save();

                    // 2. Crea record blockchain
                    $blockchain = new EgiBlockchain();
                    $blockchain->egi_id = $egi->id;
                    $blockchain->asa_id = $egi->token_EGI;
                    $blockchain->blockchain_type = 'ASA';
                    $blockchain->mint_status = 'minted';
                    $blockchain->minted_at = $egi->created_at; // Usa la data di creazione dell'EGI
                    $blockchain->platform_wallet = 'RECOVERED_FROM_TOKEN_EGI'; // Marca come recuperato

                    // Se l'EGI ha un owner diverso dal creator, significa che è stato venduto
                    if ($egi->owner_id && $egi->owner_id !== $egi->user_id) {
                        $blockchain->buyer_user_id = $egi->owner_id;
                    }

                    $blockchain->save();

                    $this->info("   ✅ Corretto!");
                    $fixed++;
                } catch (\Exception $e) {
                    $this->error("   ❌ Errore: " . $e->getMessage());
                    $errors++;
                }
            }

            $this->newLine();
        }

        $this->newLine();

        if ($isDryRun) {
            $this->info("🔍 DRY RUN COMPLETATO");
            $this->info("   📊 {$fixed} EGI verrebbero corretti");
            $this->info("   💡 Esegui senza --dry-run per applicare le modifiche");
        } else {
            $this->info("✅ CORREZIONE COMPLETATA");
            $this->info("   ✅ {$fixed} EGI corretti");
            if ($errors > 0) {
                $this->warn("   ⚠️  {$errors} errori");
            }
        }

        return 0;
    }
}
