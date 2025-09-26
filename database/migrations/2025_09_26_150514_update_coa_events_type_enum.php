<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('coa_events', function (Blueprint $table) {
            // Update the type enum to include new chain of custody event types
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coa_events', function (Blueprint $table) {
            // Revert to original enum values
            $table->enum('type', [
                'ISSUED',
                'REVOKED',
                'ANNEX_ADDED',
                'ADDENDUM_ISSUED'
            ])->change();
        });
    }
};