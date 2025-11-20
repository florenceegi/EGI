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
        DB::table('collections')
            ->where('id', 7)
            ->update([
                'epp_project_id' => 1,
                'is_published' => true, // Assicuriamo sia visibile
                'status' => 'published',
                'updated_at' => now()
            ]);
            
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
