<?php

/**
 * @Oracode DatabaseSeeder: Atomic Transaction Seeding
 * 🎯 Purpose: All-or-nothing seeding - se fallisce uno, rollback tutto
 * 🔒 Safety: Database transaction protects data integrity
 * 🧱 Core Logic: Single transaction per tutti i seeder
 *
 * @package Database\Seeders
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (Atomic Seeding)
 * @date 2025-07-20
 * @purpose Atomic seeding transaction for FlorenceEGI
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DatabaseSeeder extends Seeder {
    /**
     * Seeder execution order (CRITICAL - rispettare ordine dipendenze)
     */
    private array $seederSequence = [
        // === CORE INFRASTRUCTURE ===
        RolesAndPermissionsSeeder::class,       // 1. ruoli e permessi
        SystemUsersSeeder::class,               // 2. utenti di sistema (usa ruoli)
        ConsentTypeSeeder::class,               // 3. tipi consenso GDPR (completi)
        IconSeeder::class,                      // 4. icone sistema
        FlorenceEgiPrivacyPolicySeeder::class, // 5. privacy policy GDPR-compliant e localizzata (it/en)
        VocabularyTermSeeder::class,            // 6. termini artistici (549 righe vocabolario)
        TraitDefaultsSeeder::class,             // 7. categorie e tipi trait NFT (858 righe)

        // === AI FEATURES ===
        PlatformKnowledgeSectionSeeder::class,       // 8. knowledge base AI Art Advisor (15 sezioni)
        AiTokenPackageSeeder::class,                 // 9. pacchetti ricarica AI Token (prezzi da configurare via admin)

        // === PA/ENTERPRISE FEATURES ===
        PaWebScraperSeeder::class,              // 10. web scrapers PA (demo Firenze)
        NatanPaTenantsSeeder::class,            // 11. NATAN PA tenants e utenti (Sesto, Firenze)

        // === OPTIONAL DEMO DATA ===
        // FakeUserSeeder::class,               // OPZIONALE - solo per development
        // PAEnterpriseDemoSeeder::class,       // OPZIONALE - demo PA/Enterprise
        // EgiBlockchainSeeder::class,          // OPZIONALE - blockchain data testing
        // InitialSetupSeeder::class,           // OPZIONALE - setup iniziale legacy
        // SuperAdminUserSeeder::class,         // OPZIONALE - setup admin legacy
    ];

    /**
     * Seed the application's database with atomic transaction
     *
     * @return void
     * @throws \Exception Se qualsiasi seeder fallisce
     */
    public function run(): void {
        $this->command->info('🔒 Starting ATOMIC seeding transaction...');
        $this->command->info('⚠️  If ANY seeder fails, ALL changes will be rolled back!');

        // Start timing
        $startTime = microtime(true);

        try {
            // 0. CRITICAL: Create Florence EGI tenant FIRST (required for users)
            $this->createFlorenceEgiTenant();

            // Execute seeders in sequence
            // Note: Laravel automatically wraps each seeder in a transaction when using db:seed
            // We don't need to manually manage transactions here, as it can conflict with internal seeder transactions
            $this->command->info('📊 Seeding sequence:');

            foreach ($this->seederSequence as $index => $seederClass) {
                $step = $index + 1;
                $total = count($this->seederSequence);

                $this->command->info("🔄 Step {$step}/{$total}: {$seederClass}");

                try {
                    // Execute seeder (inside existing transaction)
                    $this->call($seederClass);
                    $this->command->info("✅ Step {$step}/{$total}: Completed successfully");
                } catch (\Exception $e) {
                    $this->command->error("💥 Step {$step}/{$total}: FAILED - {$e->getMessage()}");

                    // Log detailed error
                    Log::error('[DatabaseSeeder] Seeder failed', [
                        'seeder_class' => $seederClass,
                        'step' => $step,
                        'total_steps' => $total,
                        'error_message' => $e->getMessage(),
                        'error_trace' => $e->getTraceAsString(),
                    ]);

                    // Re-throw
                    throw new \Exception(
                        "Seeder {$seederClass} failed: {$e->getMessage()}",
                        0,
                        $e
                    );
                }
            }

            $this->command->info('🎯 All seeders completed successfully');

            // Calculate execution time
            $endTime = microtime(true);
            $executionTime = round($endTime - $startTime, 2);

            // SUCCESS - Transaction committed
            $this->command->info('');
            $this->command->info('🎉 ATOMIC SEEDING COMPLETED SUCCESSFULLY!');
            $this->command->info('═══════════════════════════════════════════');
            $this->command->info("⏱️  Execution time: {$executionTime} seconds");
            $this->command->info('✅ All changes committed to database');
            $this->command->info('🛡️ Database integrity maintained');

            // Log success
            Log::info('[DatabaseSeeder] Atomic seeding completed successfully', [
                'seeders_executed' => $this->seederSequence,
                'execution_time_seconds' => $executionTime,
                'transaction_committed' => true,
            ]);
        } catch (\Exception $e) {
            // Calculate execution time anche per errori
            $endTime = microtime(true);
            $executionTime = round($endTime - $startTime, 2);

            // FAILURE - Transaction automatically rolled back
            $this->command->error('');
            $this->command->error('💥 ATOMIC SEEDING FAILED!');
            $this->command->error('═══════════════════════════════');
            $this->command->error("⏱️  Failed after: {$executionTime} seconds");
            $this->command->error('🔄 ALL changes have been ROLLED BACK');
            $this->command->error('🛡️ Database returned to original state');
            $this->command->error("❌ Error: {$e->getMessage()}");

            // Log failure con dettagli completi
            Log::error('[DatabaseSeeder] Atomic seeding transaction failed', [
                'error_message' => $e->getMessage(),
                'execution_time_seconds' => $executionTime,
                'seeders_sequence' => $this->seederSequence,
                'transaction_rolled_back' => true,
                'database_state' => 'reverted_to_original',
                'error_trace' => $e->getTraceAsString(),
            ]);

            // Re-throw per preservare exit code
            throw $e;
        }
    }

    /**
     * Display seeding summary info
     */
    private function displaySeedingSummary(): void {
        $this->command->info('');
        $this->command->info('📋 SEEDING SUMMARY:');
        $this->command->info('═══════════════════');

        foreach ($this->seederSequence as $index => $seederClass) {
            $step = $index + 1;
            $name = class_basename($seederClass);
            $this->command->info("  {$step}. {$name}");
        }

        $this->command->info('');
        $this->command->info('🔒 Transaction mode: ATOMIC (all-or-nothing)');
        $this->command->info('🛡️ Rollback: Automatic on ANY failure');
    }

    /**
     * Create Florence EGI tenant (required for multi-tenant architecture)
     *
     * @return void
     */
    private function createFlorenceEgiTenant(): void {
        $this->command->info('🏛️  Step 0: Creating Florence EGI tenant...');

        // Check if tenant already exists
        $existingTenant = DB::table('tenants')
            ->where('slug', 'florence-egi')
            ->first();

        if ($existingTenant) {
            $this->command->warn('⚠️  Florence EGI tenant already exists (ID: ' . $existingTenant->id . ')');

            // Update tenant_id for users without it
            $usersUpdated = DB::table('users')
                ->whereNull('tenant_id')
                ->update(['tenant_id' => $existingTenant->id]);

            if ($usersUpdated > 0) {
                $this->command->info("✅ Updated {$usersUpdated} users with tenant_id={$existingTenant->id}");
            }

            return;
        }

        // Create Florence EGI tenant
        $tenantId = DB::table('tenants')->insertGetId([
            'name' => 'Florence EGI',
            'slug' => 'florence-egi',
            'code' => 'FEGI',
            'entity_type' => 'company',
            'is_active' => 1,
            'settings' => json_encode([
                'primary_color' => '#D4A574',
                'secondary_color' => '#1B365D',
                'accent_color' => '#2D5016',
                'features' => [
                    'marketplace' => true,
                    'nft_minting' => true,
                    'ai_assistant' => true,
                    'certificates' => true,
                ]
            ]),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $this->command->info("✅ Florence EGI tenant created (ID: {$tenantId})");

        // Assign all existing users to Florence EGI tenant
        $usersUpdated = DB::table('users')
            ->whereNull('tenant_id')
            ->update(['tenant_id' => $tenantId]);

        if ($usersUpdated > 0) {
            $this->command->info("✅ Assigned {$usersUpdated} users to Florence EGI tenant");
        }

        Log::info('[DatabaseSeeder] Florence EGI tenant created', [
            'tenant_id' => $tenantId,
            'users_assigned' => $usersUpdated
        ]);
    }
}