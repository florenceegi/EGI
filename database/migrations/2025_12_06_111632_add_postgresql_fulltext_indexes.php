<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Helpers\DatabaseHelper;

/**
 * Migration to add PostgreSQL full-text search support using tsvector and GIN indexes.
 * 
 * This migration adds:
 * - tsvector columns for full-text search
 * - GIN indexes for fast text search
 * - Triggers to automatically update tsvector columns
 * 
 * Tables affected:
 * - egi_acts (oggetto column)
 * - security_events (description column)
 * - consent_histories (reason_for_action, admin_notes columns)
 * 
 * On MySQL/MariaDB, this migration does nothing (FULLTEXT indexes already exist).
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Only run on PostgreSQL
        if (!DatabaseHelper::isPostgres()) {
            return;
        }

        // 1. egi_acts - Add tsvector column for 'oggetto'
        if ($this->tableExists('egi_acts') && !$this->columnExists('egi_acts', 'oggetto_tsv')) {
            DB::statement("ALTER TABLE egi_acts ADD COLUMN oggetto_tsv tsvector");
            
            // Populate the tsvector column from existing data
            DB::statement("UPDATE egi_acts SET oggetto_tsv = to_tsvector('italian', COALESCE(oggetto, ''))");
            
            // Create GIN index
            DB::statement("CREATE INDEX idx_egi_acts_oggetto_tsv ON egi_acts USING GIN (oggetto_tsv)");
            
            // Create trigger function
            DB::statement("
                CREATE OR REPLACE FUNCTION egi_acts_update_oggetto_tsv() RETURNS trigger AS \$\$
                BEGIN
                    NEW.oggetto_tsv := to_tsvector('italian', COALESCE(NEW.oggetto, ''));
                    RETURN NEW;
                END;
                \$\$ LANGUAGE plpgsql
            ");
            
            // Create trigger
            DB::statement("
                CREATE TRIGGER trg_egi_acts_oggetto_tsv
                BEFORE INSERT OR UPDATE OF oggetto ON egi_acts
                FOR EACH ROW EXECUTE FUNCTION egi_acts_update_oggetto_tsv()
            ");
        }

        // 2. security_events - Add tsvector column for 'description'
        if ($this->tableExists('security_events') && !$this->columnExists('security_events', 'description_tsv')) {
            DB::statement("ALTER TABLE security_events ADD COLUMN description_tsv tsvector");
            
            // Populate the tsvector column from existing data
            DB::statement("UPDATE security_events SET description_tsv = to_tsvector('english', COALESCE(description, ''))");
            
            // Create GIN index
            DB::statement("CREATE INDEX idx_security_events_description_tsv ON security_events USING GIN (description_tsv)");
            
            // Create trigger function
            DB::statement("
                CREATE OR REPLACE FUNCTION security_events_update_description_tsv() RETURNS trigger AS \$\$
                BEGIN
                    NEW.description_tsv := to_tsvector('english', COALESCE(NEW.description, ''));
                    RETURN NEW;
                END;
                \$\$ LANGUAGE plpgsql
            ");
            
            // Create trigger
            DB::statement("
                CREATE TRIGGER trg_security_events_description_tsv
                BEFORE INSERT OR UPDATE OF description ON security_events
                FOR EACH ROW EXECUTE FUNCTION security_events_update_description_tsv()
            ");
        }

        // 3. consent_histories - Add tsvector columns for 'reason_for_action' and 'admin_notes'
        if ($this->tableExists('consent_histories')) {
            // reason_for_action column
            if (!$this->columnExists('consent_histories', 'reason_for_action_tsv')) {
                DB::statement("ALTER TABLE consent_histories ADD COLUMN reason_for_action_tsv tsvector");
                
                // Populate the tsvector column from existing data
                DB::statement("UPDATE consent_histories SET reason_for_action_tsv = to_tsvector('italian', COALESCE(reason_for_action, ''))");
                
                // Create GIN index
                DB::statement("CREATE INDEX idx_consent_histories_reason_tsv ON consent_histories USING GIN (reason_for_action_tsv)");
                
                // Create trigger function
                DB::statement("
                    CREATE OR REPLACE FUNCTION consent_histories_update_reason_tsv() RETURNS trigger AS \$\$
                    BEGIN
                        NEW.reason_for_action_tsv := to_tsvector('italian', COALESCE(NEW.reason_for_action, ''));
                        RETURN NEW;
                    END;
                    \$\$ LANGUAGE plpgsql
                ");
                
                // Create trigger
                DB::statement("
                    CREATE TRIGGER trg_consent_histories_reason_tsv
                    BEFORE INSERT OR UPDATE OF reason_for_action ON consent_histories
                    FOR EACH ROW EXECUTE FUNCTION consent_histories_update_reason_tsv()
                ");
            }

            // admin_notes column
            if (!$this->columnExists('consent_histories', 'admin_notes_tsv')) {
                DB::statement("ALTER TABLE consent_histories ADD COLUMN admin_notes_tsv tsvector");
                
                // Populate the tsvector column from existing data
                DB::statement("UPDATE consent_histories SET admin_notes_tsv = to_tsvector('italian', COALESCE(admin_notes, ''))");
                
                // Create GIN index
                DB::statement("CREATE INDEX idx_consent_histories_notes_tsv ON consent_histories USING GIN (admin_notes_tsv)");
                
                // Create trigger function
                DB::statement("
                    CREATE OR REPLACE FUNCTION consent_histories_update_notes_tsv() RETURNS trigger AS \$\$
                    BEGIN
                        NEW.admin_notes_tsv := to_tsvector('italian', COALESCE(NEW.admin_notes, ''));
                        RETURN NEW;
                    END;
                    \$\$ LANGUAGE plpgsql
                ");
                
                // Create trigger
                DB::statement("
                    CREATE TRIGGER trg_consent_histories_notes_tsv
                    BEFORE INSERT OR UPDATE OF admin_notes ON consent_histories
                    FOR EACH ROW EXECUTE FUNCTION consent_histories_update_notes_tsv()
                ");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only run on PostgreSQL
        if (!DatabaseHelper::isPostgres()) {
            return;
        }

        // 1. egi_acts - Remove trigger, function, index, and column
        if ($this->tableExists('egi_acts') && $this->columnExists('egi_acts', 'oggetto_tsv')) {
            DB::statement("DROP TRIGGER IF EXISTS trg_egi_acts_oggetto_tsv ON egi_acts");
            DB::statement("DROP FUNCTION IF EXISTS egi_acts_update_oggetto_tsv()");
            DB::statement("DROP INDEX IF EXISTS idx_egi_acts_oggetto_tsv");
            DB::statement("ALTER TABLE egi_acts DROP COLUMN IF EXISTS oggetto_tsv");
        }

        // 2. security_events - Remove trigger, function, index, and column
        if ($this->tableExists('security_events') && $this->columnExists('security_events', 'description_tsv')) {
            DB::statement("DROP TRIGGER IF EXISTS trg_security_events_description_tsv ON security_events");
            DB::statement("DROP FUNCTION IF EXISTS security_events_update_description_tsv()");
            DB::statement("DROP INDEX IF EXISTS idx_security_events_description_tsv");
            DB::statement("ALTER TABLE security_events DROP COLUMN IF EXISTS description_tsv");
        }

        // 3. consent_histories - Remove triggers, functions, indexes, and columns
        if ($this->tableExists('consent_histories')) {
            // reason_for_action
            if ($this->columnExists('consent_histories', 'reason_for_action_tsv')) {
                DB::statement("DROP TRIGGER IF EXISTS trg_consent_histories_reason_tsv ON consent_histories");
                DB::statement("DROP FUNCTION IF EXISTS consent_histories_update_reason_tsv()");
                DB::statement("DROP INDEX IF EXISTS idx_consent_histories_reason_tsv");
                DB::statement("ALTER TABLE consent_histories DROP COLUMN IF EXISTS reason_for_action_tsv");
            }

            // admin_notes
            if ($this->columnExists('consent_histories', 'admin_notes_tsv')) {
                DB::statement("DROP TRIGGER IF EXISTS trg_consent_histories_notes_tsv ON consent_histories");
                DB::statement("DROP FUNCTION IF EXISTS consent_histories_update_notes_tsv()");
                DB::statement("DROP INDEX IF EXISTS idx_consent_histories_notes_tsv");
                DB::statement("ALTER TABLE consent_histories DROP COLUMN IF EXISTS admin_notes_tsv");
            }
        }
    }

    /**
     * Check if a table exists in the database.
     */
    private function tableExists(string $table): bool
    {
        return DB::getSchemaBuilder()->hasTable($table);
    }

    /**
     * Check if a column exists in a table.
     */
    private function columnExists(string $table, string $column): bool
    {
        return DB::getSchemaBuilder()->hasColumn($table, $column);
    }
};
