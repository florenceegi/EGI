<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Enums\BusinessType;

/**
 * NATAN PA Tenants and Users Seeder
 * 
 * Crea tenant PA (Comuni) e relativi utenti per NATAN_LOC.
 * 
 * IMPORTANT: Firenze MUST have tenant_id = 2 (mapped in MongoDB)
 * 
 * @package Database\Seeders
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0
 * @date 2026-01-09
 */
class NatanPaTenantsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🏛️  Creating NATAN PA Tenants and Users...');

        // ============================================
        // 1. Create PA Tenants
        // ============================================
        
        // Comune di Sesto Fiorentino (ID: 1)
        $sestoId = 1;
        $sestoExists = DB::table('tenants')->where('id', $sestoId)->exists();
        if (!$sestoExists) {
            DB::table('tenants')->insert([
                'id' => $sestoId,
                'name' => 'Comune di Sesto Fiorentino',
                'slug' => 'sesto-fiorentino',
                'entity_type' => 'pa',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->command->info("  ✓ Created Tenant: Comune di Sesto Fiorentino (ID: {$sestoId})");
        } else {
            $this->command->info("  ⚠ Tenant exists: Sesto Fiorentino (ID: {$sestoId})");
        }

        // Comune di Firenze (ID: 2) - MUST be 2 for MongoDB mapping
        $firenzeId = 2;
        $firenzeExists = DB::table('tenants')->where('id', $firenzeId)->exists();
        if (!$firenzeExists) {
            DB::table('tenants')->insert([
                'id' => $firenzeId,
                'name' => 'Comune di Firenze',
                'slug' => 'firenze',
                'entity_type' => 'pa',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->command->info("  ✓ Created Tenant: Comune di Firenze (ID: {$firenzeId})");
        } else {
            $this->command->info("  ⚠ Tenant exists: Firenze (ID: {$firenzeId})");
        }

        // Reset users sequence to avoid conflicts
        $maxUserId = DB::table('users')->max('id') ?? 0;
        if ($maxUserId > 0) {
            DB::statement("SELECT setval('users_id_seq', {$maxUserId})");
        }

        // ============================================
        // 2. Create Users for Firenze
        // ============================================
        $this->command->info('');
        $this->command->info('  Creating Firenze users...');

        $firenzeUsers = [
            [
                'name' => 'Mario Rossi',
                'email' => 'mario.rossi@comune.firenze.it',
                'role' => 'pa_entity_admin',  // Admin
            ],
            [
                'name' => 'Laura Bianchi',
                'email' => 'laura.bianchi@comune.firenze.it',
                'role' => 'pa_entity',
            ],
            [
                'name' => 'Giuseppe Verdi',
                'email' => 'giuseppe.verdi@comune.firenze.it',
                'role' => 'pa_entity',
            ],
            [
                'name' => 'Anna Neri',
                'email' => 'anna.neri@comune.firenze.it',
                'role' => 'pa_entity',
            ],
        ];

        foreach ($firenzeUsers as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('password'),
                    'tenant_id' => $firenzeId,
                ]
            );

            // Assign role using Spatie (if available)
            if (method_exists($user, 'assignRole')) {
                $user->syncRoles([$userData['role']]);
                $this->command->info("    ✓ {$user->name} - role: {$userData['role']}");
            } else {
                $this->command->info("    ✓ {$user->name}");
            }

            // Add ai_processing consent
            $this->addAiProcessingConsent($user);
        }

        // ============================================
        // 3. Create User for Sesto Fiorentino
        // ============================================
        $this->command->info('');
        $this->command->info('  Creating Sesto Fiorentino users...');

        $sestoUser = User::firstOrCreate(
            ['email' => 'operatore@comune.sesto-fiorentino.fi.it'],
            [
                'name' => 'Paolo Marchetti',
                'password' => Hash::make('password'),
                'tenant_id' => $sestoId,
            ]
        );

        if (method_exists($sestoUser, 'assignRole')) {
            $sestoUser->syncRoles(['pa_entity']);
            $this->command->info("    ✓ {$sestoUser->name} - role: pa_entity");
        } else {
            $this->command->info("    ✓ {$sestoUser->name}");
        }

        $this->addAiProcessingConsent($sestoUser);

        // ============================================
        // Summary
        // ============================================
        $this->command->info('');
        $this->command->info('  ✅ NATAN PA Tenants and Users seeded successfully!');
        $this->command->info('  Login: mario.rossi@comune.firenze.it / password');
    }

    /**
     * Add ai_processing consent for a user
     */
    private function addAiProcessingConsent(User $user): void
    {
        try {
            if (\Schema::hasTable('user_consents')) {
                DB::table('user_consents')->updateOrInsert(
                    [
                        'user_id' => $user->id,
                        'consent_type' => 'ai_processing',
                    ],
                    [
                        'granted' => true,
                        'legal_basis' => 'consent',
                        'status' => 'active',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        } catch (\Exception $e) {
            // Silently skip if consent table doesn't exist or has different structure
        }
    }
}
