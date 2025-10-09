<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the business_type ENUM to use approved values from BusinessType enum
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE user_organization_data MODIFY COLUMN business_type ENUM('individual','sole_proprietorship','partnership','corporation','non_profit','pa_entity')");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original ENUM values
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE user_organization_data MODIFY COLUMN business_type ENUM('individual','sole_proprietorship','partnership','corporation','non_profit','other')");
        }
    }
};
