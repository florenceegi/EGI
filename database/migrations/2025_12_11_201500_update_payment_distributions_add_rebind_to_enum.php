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
        // Drop the existing check constraint
        DB::statement("ALTER TABLE payment_distributions DROP CONSTRAINT IF EXISTS payment_distributions_source_type_check");
        // Re-add the check constraint with 'rebind' included
        DB::statement("ALTER TABLE payment_distributions ADD CONSTRAINT payment_distributions_source_type_check CHECK (source_type::text IN ('reservation', 'mint', 'transfer', 'rebind'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original constraint
        DB::statement("ALTER TABLE payment_distributions DROP CONSTRAINT IF EXISTS payment_distributions_source_type_check");
        DB::statement("ALTER TABLE payment_distributions ADD CONSTRAINT payment_distributions_source_type_check CHECK (source_type::text IN ('reservation', 'mint', 'transfer'))");
    }
};
