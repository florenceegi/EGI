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
        // Fix for Staging Issue: Legacy FK constraint pointing to 'epps' table was not dropped
        // preventing creation of EPP Projects when epp_user_id (User ID) does not exist in 'epps' table.
        
        $table = 'epp_projects';
        $constraintName = 'epp_milestones_epp_id_foreign';

        try {
             Schema::table($table, function (Blueprint $table) use ($constraintName) {
                // Try dropping by name explicitly
                $table->dropForeign($constraintName);
            });
        } catch (\Exception $e) {
            // If dropping by name fails (e.g. not found), ignore.
            // But we can try raw SQL for Postgres just to be sure if the above wrapper fails differently.
            if (DB::getDriverName() === 'pgsql') {
                DB::statement("ALTER TABLE {$table} DROP CONSTRAINT IF EXISTS \"{$constraintName}\"");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse needed as we are removing a bad constraint.
    }
};
