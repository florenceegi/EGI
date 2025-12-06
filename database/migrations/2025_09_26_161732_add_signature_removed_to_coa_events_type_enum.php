<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Helpers\DatabaseHelper;

return new class extends Migration {
    /**
     * Run the migrations.
     * 
     * Note: ENUM modification only works on MySQL/MariaDB.
     * PostgreSQL uses VARCHAR for enums and doesn't need modification.
     */
    public function up(): void {
        // Skip on PostgreSQL - it uses VARCHAR which accepts any string
        if (!DatabaseHelper::isMysql()) {
            return;
        }

        Schema::table('coa_events', function (Blueprint $table) {
            // Add SIGNATURE_REMOVED to the type enum
            $table->enum('type', [
                'ISSUED',
                'REVOKED',
                'ANNEX_ADDED',
                'ADDENDUM_ISSUED',
                'AUTHOR_SIGNED',
                'INSPECTOR_SIGNED',
                'PDF_REGENERATED',
                'PDF_DOWNLOADED',
                'SIGNATURE_VALIDATED',
                'SIGNATURE_REMOVED'
            ])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        // Skip on PostgreSQL
        if (!DatabaseHelper::isMysql()) {
            return;
        }

        Schema::table('coa_events', function (Blueprint $table) {
            // Remove SIGNATURE_REMOVED from the type enum
            $table->enum('type', [
                'ISSUED',
                'REVOKED',
                'ANNEX_ADDED',
                'ADDENDUM_ISSUED',
                'AUTHOR_SIGNED',
                'INSPECTOR_SIGNED',
                'PDF_REGENERATED',
                'PDF_DOWNLOADED',
                'SIGNATURE_VALIDATED'
            ])->change();
        });
    }
};
