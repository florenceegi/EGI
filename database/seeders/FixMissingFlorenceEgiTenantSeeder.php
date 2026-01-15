<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FixMissingFlorenceEgiTenantSeeder extends Seeder
{
    public function run()
    {
        // Fix Postgres Sequence out of sync
        try {
            if (config('database.default') === 'pgsql') {
                $maxId = DB::table('tenants')->max('id') ?? 0;
                // Set sequence to max_id so next one is max_id + 1
                DB::statement("SELECT setval('tenants_id_seq', GREATEST($maxId, 1))");
                $this->command->info("✅ Reset tenants_id_seq to {$maxId}");
            }
        } catch (\Exception $e) {
            $this->command->warn("⚠️  Could not reset sequence: {$e->getMessage()}");
        }

        $this->command->info('Checking for Florence EGI tenant...');

        $existingTenant = DB::table('tenants')
            ->where('slug', 'florence-egi')
            ->orWhere('name', 'Florence EGI')
            ->first();

        if ($existingTenant) {
            $this->command->info("⚠️  Florence EGI tenant exists (ID: {$existingTenant->id}). Updating system_project_id...");
            DB::table('tenants')->where('id', $existingTenant->id)->update(['system_project_id' => 1]);
            $this->command->info("✅ Updated system_project_id to 1");
            return;
        }

        $this->command->info('🏛️  Creating Florence EGI tenant...');

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
            'system_project_id' => 1, // FlorenceEGI Project ID
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $this->command->info("✅ Florence EGI tenant created successfully (ID: {$tenantId})");
    }
}
