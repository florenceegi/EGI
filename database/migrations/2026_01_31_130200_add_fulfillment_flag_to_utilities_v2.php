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
        Schema::table('utilities', function (Blueprint $table) {
            $table->boolean('requires_fulfillment')->default(false)->after('status');
        });

        // Backfill: Copy legacy 'requires_shipping' to 'requires_fulfillment' if it exists and is true
        // This ensures existing data compatibility for P0
        if (Schema::hasColumn('utilities', 'requires_shipping')) {
            DB::statement("UPDATE utilities SET requires_fulfillment = requires_shipping WHERE requires_shipping = TRUE");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('utilities', function (Blueprint $table) {
            $table->dropColumn(['requires_fulfillment']);
        });
    }
};
