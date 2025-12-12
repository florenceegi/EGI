<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. FIX FRANGETTE (ID 1 o creator_id 3): Scollega da EPP Project
        // Cerchiamo per sicurezza sia per ID che per creator_id per beccare quella giusta
        DB::table('collections')
            ->where('id', 1) // Assumendo ID 1 come detto da te
            ->orWhere('creator_id', 3) // Frangette user
            ->update([
                'epp_project_id' => null,
                'updated_at' => now()
            ]);

        // 2. FIX OCEANO (ID 7): Collega a EPP Project 1 (Rimini Clear)
        // Lookup dynamic ID instead of hardcoded 1
        $riminiProject = DB::table('epp_projects')->where('name', 'Rimini Clear')->first();
        $projectId = $riminiProject ? $riminiProject->id : (DB::table('epp_projects')->first()->id ?? null);

        if ($projectId) {
            // Disable FK checks to prevents legacy constraint issues during fix
            if (DB::getDriverName() === 'mysql' || DB::getDriverName() === 'mariadb') {
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            }
            
            DB::table('collections')
                ->where('id', 7)
                ->update([
                    'epp_project_id' => $projectId,
                    'is_published' => true, // Assicuriamo sia visibile
                    'status' => 'published',
                    'updated_at' => now()
                ]);

            if (DB::getDriverName() === 'mysql' || DB::getDriverName() === 'mariadb') {
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            }
        }
            
        // Pubblica anche gli EGI di Oceano per sicurezza
        DB::table('egis')
            ->where('collection_id', 7)
            ->update([
                'is_published' => true,
                'updated_at' => now()
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Non revertiamo correzioni di dati manuali
    }
};
