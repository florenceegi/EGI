<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\SystemProject;

/**
 * System Projects Seeder
 * 
 * Seeds the system_projects table with known applications:
 * - FlorenceEGI (monotenant)
 * - NATAN_LOC (multitenant - PA)
 * 
 * Also cleans up FlorenceEGI from tenants table (it's a project, not a tenant).
 * 
 * @package Database\Seeders
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0
 * @date 2026-01-09
 */
class SystemProjectsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🏗️  Seeding System Projects...');

        // 1. FlorenceEGI (monotenant)
        $fegi = SystemProject::firstOrCreate(
            ['code' => 'FEGI'],
            [
                'name' => 'FlorenceEGI',
                'is_multitenant' => false,
                'is_active' => true,
                'settings' => [
                    'description' => 'Core EGI ecosystem - NFT, blockchain, COA',
                    'primary_color' => '#D4A574',
                ],
            ]
        );
        $this->command->info("  ✓ FlorenceEGI (ID: {$fegi->id}, monotenant)");

        // 2. NATAN_LOC (multitenant - PA)
        $natanLoc = SystemProject::firstOrCreate(
            ['code' => 'NATAN_LOC'],
            [
                'name' => 'NATAN PA',
                'is_multitenant' => true,
                'is_active' => true,
                'settings' => [
                    'description' => 'RAG assistant for Public Administration',
                    'primary_color' => '#ea4335',
                ],
            ]
        );
        $this->command->info("  ✓ NATAN PA (ID: {$natanLoc->id}, multitenant)");

        // 3. Update existing users without system_project_id
        $usersUpdated = DB::table('users')
            ->whereNull('system_project_id')
            ->update(['system_project_id' => $fegi->id]);
        
        if ($usersUpdated > 0) {
            $this->command->info("  ✓ Assigned {$usersUpdated} existing users to FlorenceEGI");
        }

        // 4. Clean up: remove "Florence EGI" from tenants if exists
        $deletedTenants = DB::table('tenants')
            ->where('slug', 'florence-egi')
            ->delete();
        
        if ($deletedTenants > 0) {
            $this->command->info("  ✓ Removed 'Florence EGI' from tenants table (it's a project, not a tenant)");
        }

        // 5. Update existing tenants without system_project_id to NATAN_LOC
        $tenantsUpdated = DB::table('tenants')
            ->whereNull('system_project_id')
            ->update(['system_project_id' => $natanLoc->id]);
        
        if ($tenantsUpdated > 0) {
            $this->command->info("  ✓ Assigned {$tenantsUpdated} existing tenants to NATAN_LOC");
        }

        $this->command->info('');
        $this->command->info('✅ System Projects seeded successfully!');
    }
}
