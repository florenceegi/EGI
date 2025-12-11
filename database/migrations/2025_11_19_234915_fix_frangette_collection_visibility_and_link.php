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
        // 0. FIX SCHEMA DRIFT (Zombie Constraint)
        // La rinomina della tabella epp_milestones -> epp_projects non ha cancellato la vecchia FK su postgres
        // Questo è necessario per sbloccare le migrazioni successive
        $zombieConstraint = 'epp_milestones_epp_id_foreign';
        $tableConstraints = DB::select("
            SELECT conname 
            FROM pg_constraint 
            WHERE conname = ?
        ", [$zombieConstraint]);

        if (count($tableConstraints) > 0) {
            Schema::table('epp_projects', function (Blueprint $table) use ($zombieConstraint) {
                $table->dropForeign($zombieConstraint);
            });
        }

        // Trova la collezione di Frangette (user_id 3)
        $collection = DB::table('collections')
            ->where('creator_id', 3)
            ->first();

        if ($collection) {
            // 1. Trova o crea il progetto EPP "Rimini Clear"
            $projectId = DB::table('epp_projects')
                ->where('name', 'Rimini Clear')
                ->value('id');

            if (!$projectId) {
                // Se non esiste, crealo (fallback sicuro)
                // Cerchiamo l'utente EPP (id 2) o fallback admin (id 1)
                $eppUserId = DB::table('users')->where('id', 2)->exists() ? 2 : 1;

                $projectId = DB::table('epp_projects')->insertGetId([
                    'epp_user_id' => $eppUserId,
                    'name' => 'Rimini Clear',
                    'description' => 'Progetto creato automaticamente per fix migrazione',
                    'project_type' => 'cleanup', // o altro default valido
                    'status' => 'active',
                    'target_value' => 1000,
                    'current_value' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // 2. Collega al progetto EPP trovato/creato
            // 3. Pubblica la collezione
            DB::table('collections')
                ->where('id', $collection->id)
                ->update([
                    'epp_project_id' => $projectId,
                    'is_published' => true,
                    'status' => 'published',
                    'updated_at' => now()
                ]);

            // 4. Pubblica tutti gli EGI della collezione
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
