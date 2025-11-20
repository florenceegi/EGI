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
        // Trova la collezione di Frangette (user_id 3)
        $collection = DB::table('collections')
            ->where('creator_id', 3)
            ->first();

        if ($collection) {
            // 1. Collega al progetto EPP 1 (Rimini Clear)
            // 2. Pubblica la collezione
            DB::table('collections')
                ->where('id', $collection->id)
                ->update([
                    'epp_project_id' => 1,
                    'is_published' => true,
                    'status' => 'published',
                    'updated_at' => now()
                ]);

            // 3. Pubblica tutti gli EGI della collezione
            DB::table('egis')
                ->where('collection_id', $collection->id)
                ->update([
                    'is_published' => true,
                    'updated_at' => now()
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Non è necessario revertire questo fix di dati
    }
};
