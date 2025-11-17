<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('user_personal_data', function (Blueprint $table) {
            $table->string('iban', 34)->nullable()->after('tax_id_number');
        });

        Schema::table('user_organization_data', function (Blueprint $table) {
            $table->string('iban', 34)->nullable()->after('org_vat_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('user_personal_data', function (Blueprint $table) {
            $table->dropColumn('iban');
        });

        Schema::table('user_organization_data', function (Blueprint $table) {
            $table->dropColumn('iban');
        });
    }
};
