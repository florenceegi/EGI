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
        $driver = Schema::getConnection()->getDriverName();
        
        if ($driver === 'mysql' || $driver === 'mariadb') {
            // MariaDB: Drop CHECK constraint first using TABLE_CONSTRAINTS, then rename
            // Get CHECK constraint name using correct MariaDB syntax
            $constraints = DB::select("
                SELECT CONSTRAINT_NAME
                FROM information_schema.TABLE_CONSTRAINTS
                WHERE CONSTRAINT_SCHEMA = DATABASE()
                AND TABLE_NAME = 'epp_projects'
                AND CONSTRAINT_TYPE = 'CHECK'
            ");
            
            // Drop all check constraints (we'll filter by name pattern since we can't check clause content easily)
            foreach ($constraints as $constraint) {
                if (str_contains($constraint->CONSTRAINT_NAME, 'media') || str_contains($constraint->CONSTRAINT_NAME, 'chk')) {
                    try {
                        DB::statement("ALTER TABLE epp_projects DROP CONSTRAINT {$constraint->CONSTRAINT_NAME}");
                    } catch (\Exception $e) {
                        // Ignore if constraint doesn't exist
                    }
                }
            }
            
            // Rename column
            if (Schema::hasColumn('epp_projects', 'media')) {
                DB::statement("ALTER TABLE epp_projects CHANGE COLUMN media media_data LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL");
                
                // Recreate JSON check constraint on new column name
                try {
                    DB::statement("ALTER TABLE epp_projects ADD CONSTRAINT epp_projects_media_data_json CHECK (json_valid(`media_data`))");
                } catch (\Exception $e) {
                    // Ignore if constraint already exists or JSON check not supported
                }
            }
        } else {
            // Other DB: use Laravel's renameColumn
            Schema::table('epp_projects', function (Blueprint $table) {
                $table->renameColumn('media', 'media_data');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        
        if ($driver === 'mysql' || $driver === 'mariadb') {
            // Drop new CHECK constraint
            $constraints = DB::select("
                SELECT CONSTRAINT_NAME
                FROM information_schema.TABLE_CONSTRAINTS
                WHERE CONSTRAINT_SCHEMA = DATABASE()
                AND TABLE_NAME = 'epp_projects'
                AND CONSTRAINT_TYPE = 'CHECK'
            ");
            
            foreach ($constraints as $constraint) {
                if (str_contains($constraint->CONSTRAINT_NAME, 'media_data')) {
                    try {
                        DB::statement("ALTER TABLE epp_projects DROP CONSTRAINT {$constraint->CONSTRAINT_NAME}");
                    } catch (\Exception $e) {
                        // Ignore if constraint doesn't exist
                    }
                }
            }
            
            // Rename back
            if (Schema::hasColumn('epp_projects', 'media_data')) {
                DB::statement("ALTER TABLE epp_projects CHANGE COLUMN media_data media LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL");
                
                // Recreate original JSON check constraint
                try {
                    DB::statement("ALTER TABLE epp_projects ADD CONSTRAINT epp_projects_chk_1 CHECK (json_valid(`media`))");
                } catch (\Exception $e) {
                    // Ignore
                }
            }
        } else {
            Schema::table('epp_projects', function (Blueprint $table) {
                $table->renameColumn('media_data', 'media');
            });
        }
    }
};
