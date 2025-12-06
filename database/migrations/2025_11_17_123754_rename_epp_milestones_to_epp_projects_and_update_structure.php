<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Helpers\DatabaseHelper;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * Transform epp_milestones → epp_projects:
     * - User (usertype='epp') can have multiple EppProject
     * - Each project represents a specific environmental initiative
     * - Collections select ONE EppProject to support
     */
    public function up(): void {
        // Step 1: Check if table needs renaming (skip if already renamed)
        if (Schema::hasTable('epp_milestones')) {
            Schema::rename('epp_milestones', 'epp_projects');
        }

        // Step 2: Modify epp_projects structure - MariaDB compatible approach
        // First drop ALL foreign keys on epp_id before renaming column
        if (Schema::hasColumn('epp_projects', 'epp_id')) {
            $driver = Schema::getConnection()->getDriverName();
            if ($driver === 'mysql' || $driver === 'mariadb') {
                // Get all FK names referencing epp_id column
                $fks = DB::select("
                    SELECT CONSTRAINT_NAME
                    FROM information_schema.KEY_COLUMN_USAGE
                    WHERE TABLE_SCHEMA = DATABASE()
                    AND TABLE_NAME = 'epp_projects'
                    AND COLUMN_NAME = 'epp_id'
                    AND REFERENCED_TABLE_NAME IS NOT NULL
                ");

                foreach ($fks as $fk) {
                    DB::statement("ALTER TABLE epp_projects DROP FOREIGN KEY {$fk->CONSTRAINT_NAME}");
                }
            } else {
                // For other DB, try standard Laravel drop
                Schema::table('epp_projects', function (Blueprint $table) {
                    if ($this->hasForeignKey('epp_projects', 'epp_projects_epp_id_foreign')) {
                        $table->dropForeign(['epp_id']);
                    }
                });
            }
        }

        // Now rename the column using raw SQL for MariaDB compatibility
        if (Schema::hasColumn('epp_projects', 'epp_id') && !Schema::hasColumn('epp_projects', 'epp_user_id')) {
            $driver = Schema::getConnection()->getDriverName();
            if ($driver === 'mysql' || $driver === 'mariadb') {
                // MariaDB/MySQL: use CHANGE COLUMN
                DB::statement('ALTER TABLE epp_projects CHANGE COLUMN epp_id epp_user_id BIGINT UNSIGNED NOT NULL');
            } else {
                // Other DB: use Laravel's renameColumn
                Schema::table('epp_projects', function (Blueprint $table) {
                    $table->renameColumn('epp_id', 'epp_user_id');
                });
            }
        }

        // Step 3: Add foreign key to users table for EPP users
        Schema::table('epp_projects', function (Blueprint $table) {
            // Add foreign key only if not exists
            if (!$this->hasForeignKey('epp_projects', 'epp_projects_epp_user_id_foreign')) {
                $table->foreign('epp_user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
            }

            // Rename columns if not already renamed
            if (Schema::hasColumn('epp_projects', 'type') && !Schema::hasColumn('epp_projects', 'project_type')) {
                $table->renameColumn('type', 'project_type');
            }

            if (Schema::hasColumn('epp_projects', 'title') && !Schema::hasColumn('epp_projects', 'name')) {
                $table->renameColumn('title', 'name');
            }

            // Add target_funds and current_funds for financial tracking if not exists
            if (!Schema::hasColumn('epp_projects', 'target_funds')) {
                $table->decimal('target_funds', 20, 2)->nullable()->after('target_value');
            }
            if (!Schema::hasColumn('epp_projects', 'current_funds')) {
                $table->decimal('current_funds', 20, 2)->default(0)->after('target_funds');
            }

            // Update indexes - drop old ones if exist
            if ($this->hasIndex('epp_projects', 'epp_projects_epp_id_status_index')) {
                $table->dropIndex('epp_projects_epp_id_status_index');
            }
            if ($this->hasIndex('epp_projects', 'epp_projects_epp_id_type_index')) {
                $table->dropIndex('epp_projects_epp_id_type_index');
            }

            // Add new indexes if not exist
            if (!$this->hasIndex('epp_projects', 'epp_projects_epp_user_id_status_index')) {
                $table->index(['epp_user_id', 'status']);
            }
            if (!$this->hasIndex('epp_projects', 'epp_projects_epp_user_id_project_type_index')) {
                $table->index(['epp_user_id', 'project_type']);
            }
            if (!$this->hasIndex('epp_projects', 'epp_projects_project_type_status_index')) {
                $table->index(['project_type', 'status']);
            }
        });

        // Step 4: Update collections table - rename epp_id to epp_project_id
        // First drop ALL FK on epp_id, then rename using raw SQL for MariaDB compatibility
        if (Schema::hasColumn('collections', 'epp_id')) {
            $driver = Schema::getConnection()->getDriverName();
            if ($driver === 'mysql' || $driver === 'mariadb') {
                // Get all FK names referencing epp_id column
                $fks = DB::select("
                    SELECT CONSTRAINT_NAME
                    FROM information_schema.KEY_COLUMN_USAGE
                    WHERE TABLE_SCHEMA = DATABASE()
                    AND TABLE_NAME = 'collections'
                    AND COLUMN_NAME = 'epp_id'
                    AND REFERENCED_TABLE_NAME IS NOT NULL
                ");

                foreach ($fks as $fk) {
                    DB::statement("ALTER TABLE collections DROP FOREIGN KEY {$fk->CONSTRAINT_NAME}");
                }
            } else {
                Schema::table('collections', function (Blueprint $table) {
                    if ($this->hasForeignKey('collections', 'collections_epp_id_foreign')) {
                        $table->dropForeign(['epp_id']);
                    }
                });
            }
        }

        // Rename column using raw SQL for MariaDB compatibility
        if (Schema::hasColumn('collections', 'epp_id') && !Schema::hasColumn('collections', 'epp_project_id')) {
            $driver = Schema::getConnection()->getDriverName();
            if ($driver === 'mysql' || $driver === 'mariadb') {
                DB::statement('ALTER TABLE collections CHANGE COLUMN epp_id epp_project_id BIGINT UNSIGNED NULL');
            } else {
                Schema::table('collections', function (Blueprint $table) {
                    $table->renameColumn('epp_id', 'epp_project_id');
                });
            }
        }

        // Change type if needed (column already exists with wrong type)
        Schema::table('collections', function (Blueprint $table) {
            if (Schema::hasColumn('collections', 'epp_project_id')) {
                // Drop existing foreign key if exists before changing type
                if ($this->hasForeignKey('collections', 'collections_epp_project_id_foreign')) {
                    $table->dropForeign(['epp_project_id']);
                }
            }
        });

        // Clean up orphaned epp_project_id values before adding foreign key
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('
                UPDATE collections c
                LEFT JOIN epp_projects ep ON c.epp_project_id = ep.id
                SET c.epp_project_id = NULL
                WHERE c.epp_project_id IS NOT NULL AND ep.id IS NULL
            ');
        } else {
            // SQLite compatible version
            DB::statement('
                UPDATE collections
                SET epp_project_id = NULL
                WHERE epp_project_id IS NOT NULL
                AND epp_project_id NOT IN (SELECT id FROM epp_projects)
            ');
        }

        // Step 5: Add proper foreign key from collections to epp_projects
        Schema::table('collections', function (Blueprint $table) {
            if (!Schema::hasColumn('collections', 'epp_project_id')) {
                return; // Column doesn't exist yet, skip
            }

            // Add foreign key only if not exists
            if (!$this->hasForeignKey('collections', 'collections_epp_project_id_foreign')) {
                $table->foreign('epp_project_id')
                    ->references('id')
                    ->on('epp_projects')
                    ->nullOnDelete();
            }

            // Add index only if not exists
            if (!$this->hasIndex('collections', 'collections_epp_project_id_index')) {
                $table->index('epp_project_id');
            }
        });
    }

    /**
     * Check if a foreign key exists
     */
    private function hasForeignKey($table, $name) {
        $conn = Schema::getConnection();
        $driver = $conn->getDriverName();

        // SQLite doesn't support information_schema, skip check (test will work anyway)
        if ($driver === 'sqlite') {
            return false; // In SQLite test, let migration attempt to drop (will fail silently if not exists)
        }

        $database = $conn->getDatabaseName();

        if (DatabaseHelper::isPostgres()) {
            // PostgreSQL: use pg_constraint
            $result = $conn->select("
                SELECT conname
                FROM pg_constraint
                WHERE conrelid = ?::regclass
                AND conname = ?
                AND contype = 'f'
            ", [$table, $name]);
        } else {
            // MySQL/MariaDB
            $result = $conn->select("
                SELECT CONSTRAINT_NAME
                FROM information_schema.TABLE_CONSTRAINTS
                WHERE TABLE_SCHEMA = ?
                AND TABLE_NAME = ?
                AND CONSTRAINT_NAME = ?
                AND CONSTRAINT_TYPE = 'FOREIGN KEY'
            ", [$database, $table, $name]);
        }

        return count($result) > 0;
    }

    /**
     * Check if an index exists
     */
    private function hasIndex($table, $name) {
        $conn = Schema::getConnection();
        $driver = $conn->getDriverName();

        // SQLite doesn't support information_schema, skip check
        if ($driver === 'sqlite') {
            return false;
        }

        $database = $conn->getDatabaseName();

        if (DatabaseHelper::isPostgres()) {
            // PostgreSQL: use pg_indexes
            $result = $conn->select("
                SELECT indexname
                FROM pg_indexes
                WHERE tablename = ?
                AND indexname = ?
            ", [$table, $name]);
        } else {
            // MySQL/MariaDB
            $result = $conn->select("
                SELECT INDEX_NAME
                FROM information_schema.STATISTICS
                WHERE TABLE_SCHEMA = ?
                AND TABLE_NAME = ?
                AND INDEX_NAME = ?
            ", [$database, $table, $name]);
        }

        return count($result) > 0;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        // Reverse Step 5
        Schema::table('collections', function (Blueprint $table) {
            $table->dropForeign(['epp_project_id']);
            $table->dropIndex(['epp_project_id']);
        });

        // Reverse Step 4
        Schema::table('collections', function (Blueprint $table) {
            $table->renameColumn('epp_project_id', 'epp_id');

            // Restore old foreign key to epps table
            $table->foreign('epp_id')
                ->references('id')
                ->on('epps')
                ->nullOnDelete();
        });

        // Reverse Step 3
        Schema::table('epp_projects', function (Blueprint $table) {
            // Drop new indexes
            $table->dropIndex(['epp_user_id', 'status']);
            $table->dropIndex(['epp_user_id', 'project_type']);
            $table->dropIndex(['project_type', 'status']);

            // Remove financial tracking columns
            $table->dropColumn(['target_funds', 'current_funds']);

            // Restore old column names
            $table->renameColumn('name', 'title');
            $table->renameColumn('project_type', 'type');

            // Restore old indexes
            $table->index(['epp_user_id', 'status']);
            $table->index(['epp_user_id', 'type']);
        });

        // Reverse Step 2 & 3 combined
        Schema::table('epp_projects', function (Blueprint $table) {
            // Drop foreign key to users
            $table->dropForeign(['epp_user_id']);

            // Rename back to epp_id
            $table->renameColumn('epp_user_id', 'epp_id');
        });

        // Add foreign key back to epps table
        Schema::table('epp_projects', function (Blueprint $table) {
            $table->foreign('epp_id')
                ->references('id')
                ->on('epps')
                ->onDelete('cascade');
        });

        // Reverse Step 1
        Schema::rename('epp_projects', 'epp_milestones');
    }
};
