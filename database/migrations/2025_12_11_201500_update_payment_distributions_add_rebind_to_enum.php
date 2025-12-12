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
        if (DB::getDriverName() === 'pgsql') {
            // Drop the existing check constraint
            DB::statement("ALTER TABLE payment_distributions DROP CONSTRAINT IF EXISTS payment_distributions_source_type_check");
            // Re-add the check constraint with 'rebind' included
            DB::statement("ALTER TABLE payment_distributions ADD CONSTRAINT payment_distributions_source_type_check CHECK (source_type::text IN ('reservation', 'mint', 'transfer', 'rebind'))");
        } elseif (DB::getDriverName() === 'mysql' || DB::getDriverName() === 'mariadb') {
            DB::statement("ALTER TABLE payment_distributions MODIFY COLUMN source_type ENUM('reservation', 'mint', 'transfer', 'rebind') NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            // Revert to original constraint
            DB::statement("ALTER TABLE payment_distributions DROP CONSTRAINT IF EXISTS payment_distributions_source_type_check");
            DB::statement("ALTER TABLE payment_distributions ADD CONSTRAINT payment_distributions_source_type_check CHECK (source_type::text IN ('reservation', 'mint', 'transfer'))");
        } elseif (DB::getDriverName() === 'mysql' || DB::getDriverName() === 'mariadb') {
            DB::statement("ALTER TABLE payment_distributions MODIFY COLUMN source_type ENUM('reservation', 'mint', 'transfer') NOT NULL");
        }
    }
};
