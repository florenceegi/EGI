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
            DB::statement("ALTER TABLE payment_distributions DROP CONSTRAINT IF EXISTS payment_distributions_user_type_check");
            
            // Re-add the check constraint with all enum values including 'frangette'
            // ENUM values from UserTypeEnum:
            // weak, creator, collector, commissioner, company, epp, trader-pro, vip, natan, frangette,
            // patron, admin, editor, guest
            DB::statement("ALTER TABLE payment_distributions ADD CONSTRAINT payment_distributions_user_type_check CHECK (user_type::text IN (
                'weak', 'creator', 'collector', 'commissioner', 'company', 'epp', 'trader-pro', 'vip', 
                'natan', 'frangette', 'patron', 'admin', 'editor', 'guest'
            ))");
        } elseif (DB::getDriverName() === 'mysql' || DB::getDriverName() === 'mariadb') {
             DB::statement("ALTER TABLE payment_distributions MODIFY COLUMN user_type ENUM(
                'weak', 'creator', 'collector', 'commissioner', 'company', 'epp', 'trader-pro', 'vip', 
                'natan', 'frangette', 'patron', 'admin', 'editor', 'guest'
             ) NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Can't easily revert to "previous" state without knowing exactly what it was.
        // But we can just leave it or revert to a safe subset if needed.
        // For now, no-op or re-add constraint without frangette if strictly needed, but better to leave generic.
    }
};
