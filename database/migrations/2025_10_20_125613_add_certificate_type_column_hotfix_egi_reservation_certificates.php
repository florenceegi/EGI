<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        // Aggiungi certificate_type SOLO se non esiste
        if (!Schema::hasColumn('egi_reservation_certificates', 'certificate_type')) {
            Schema::table('egi_reservation_certificates', function (Blueprint $table) {
                $table->enum('certificate_type', ['standard', 'premium', 'eco', 'luxury'])
                    ->after('id')  // Usa 'id' che esiste sempre
                    ->default('standard')
                    ->comment('Type of certificate issued');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        if (Schema::hasColumn('egi_reservation_certificates', 'certificate_type')) {
            Schema::table('egi_reservation_certificates', function (Blueprint $table) {
                $table->dropColumn('certificate_type');
            });
        }
    }
};
