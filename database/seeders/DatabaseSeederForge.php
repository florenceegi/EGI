<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * @package Database\Seeders
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.1.0 (Forge Compatible + Florence EGI Tenant)
 * @date 2025-12-02
 * @purpose Non-atomic seeding for Laravel Forge environments
 *
 * 🎯 DIFFERENZE vs DatabaseSeeder:
 * - NO transazioni atomiche (compatibile con script Forge)
 * - Ogni seeder eseguito indipendentemente
 * - Errori bloccano esecuzione ma non rollback
 * - Ottimizzato per ambienti di produzione Forge
 */
class DatabaseSeederForge extends Seeder {
    /**
     * Seeder execution order (stesso ordine del DatabaseSeeder)
     */
    private array $seederSequence = [
        // === CORE INFRASTRUCTURE ===
        RolesAndPermissionsSeeder::class,       // 1. ruoli e permessi
        SystemUsersSeeder::class,               // 2. utenti di sistema (usa ruoli)
        ConsentTypeSeeder::class,               // 3. tipi consenso GDPR (completi)
        IconSeeder::class,                      // 4. icone sistema
        FlorenceEgiPrivacyPolicySeeder::class,  // 5. privacy policy GDPR-compliant e localizzata (it/en)
        VocabularyTermSeeder::class,            // 6. termini artistici (549 righe vocabolario)
        TraitDefaultsSeeder::class,             // 7. categorie e tipi trait NFT (858 righe)

        // === AI FEATURES ===
        PlatformKnowledgeSectionSeeder::class,  // 8. knowledge base AI Art Advisor (15 sezioni)
        AiFeaturePricingSeeder::class,          // 9. pricing feature AI (tokenomics)

        // === PA/ENTERPRISE FEATURES ===
        PaWebScraperSeeder::class,              // 10. web scrapers PA (demo Firenze)

        // === OPTIONAL DEMO DATA ===
        // FakeUserSeeder::class,               // OPZIONALE - solo per development
        // PAEnterpriseDemoSeeder::class,       // OPZIONALE - demo PA/Enterprise
        // EgiBlockchainSeeder::class,          // OPZIONALE - blockchain data testing
        // InitialSetupSeeder::class,           // OPZIONALE - setup iniziale legacy
        // SuperAdminUserSeeder::class,         // OPZIONALE - setup admin legacy
    ];

    /**
     * Seed the application's database (NON-ATOMIC per Forge)
     *
     * @return void
     */
    public function run(): void {
        $this->command->info('🔄 Starting NON-ATOMIC seeding for Forge...');
        $this->command->info('⚠️  Each seeder runs independently (no rollback on failure)');

        // Start timing
        $startTime = microtime(true);
        $failedSeeders = [];

        // === STEP 0: Create Florence EGI tenant FIRST ===
        try {
            $this->createFlorenceEgiTenant();
        } catch (\Exception $e) {
            $this->command->error("💥 Step 0: FAILED - {$e->getMessage()}");
            Log::error('[DatabaseSeederForge] Florence EGI tenant creation failed', [
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
            ]);
            $this->command->error('🛑 Cannot proceed without Florence EGI tenant');
            exit(1);
        }

        $this->command->info('📊 Seeding sequence:');

        foreach ($this->seederSequence as $index => $seederClass) {
            $step = $index + 1;
            $total = count($this->seederSequence);

            $this->command->info("🔄 Step {$step}/{$total}: {$seederClass}");

            try {
                // Execute seeder (NO transaction wrapper)
                $this->call($seederClass);
                $this->command->info("✅ Step {$step}/{$total}: Completed successfully");
            } catch (\Exception $e) {
                $this->command->error("💥 Step {$step}/{$total}: FAILED - {$e->getMessage()}");

                // Log detailed error
                Log::error('[DatabaseSeederForge] Seeder failed (no rollback)', [
                    'seeder_class' => $seederClass,
                    'step' => $step,
                    'total_steps' => $total,
                    'error_message' => $e->getMessage(),
                    'error_trace' => $e->getTraceAsString(),
                    'forge_compatible' => true,
                ]);

                $failedSeeders[] = $seederClass;

                // STOP execution on first failure (Forge-friendly)
                $this->command->error('🛑 Stopping execution due to seeder failure');
                break;
            }
        }

        // Calculate execution time
        $endTime = microtime(true);
        $executionTime = round($endTime - $startTime, 2);

        if (empty($failedSeeders)) {
            // SUCCESS
            $this->command->info('');
            $this->command->info('🎉 FORGE SEEDING COMPLETED SUCCESSFULLY!');
            $this->command->info('════════════════════════════════════════');
            $this->command->info("⏱️  Execution time: {$executionTime} seconds");
            $this->command->info('✅ All seeders completed (no transactions used)');
            $this->command->info('🚀 Forge-compatible execution');

            // Log success
            Log::info('[DatabaseSeederForge] Seeding completed successfully', [
                'seeders_executed' => $this->seederSequence,
                'execution_time_seconds' => $executionTime,
                'forge_compatible' => true,
                'transaction_used' => false,
            ]);
        } else {
            // PARTIAL FAILURE
            $this->command->error('');
            $this->command->error('💥 FORGE SEEDING PARTIALLY FAILED!');
            $this->command->error('═══════════════════════════════════════');
            $this->command->error("⏱️  Failed after: {$executionTime} seconds");
            $this->command->error('❌ Failed seeders: ' . implode(', ', $failedSeeders));
            $this->command->error('⚠️  No rollback performed (Forge mode)');

            // Exit with error code
            exit(1);
        }
    }

    /**
     * Create Florence EGI tenant (required for multi-tenant architecture)
     * Must be called BEFORE SystemUsersSeeder
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

        Log::info('[DatabaseSeederForge] Florence EGI tenant created', [
            'tenant_id' => $tenantId,
            'users_assigned' => $usersUpdated
        ]);
    }
}
