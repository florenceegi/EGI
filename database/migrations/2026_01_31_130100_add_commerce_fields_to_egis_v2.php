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
        Schema::table('egis', function (Blueprint $table) {
            // P0 Commerce Listing Fields
            $table->boolean('is_physical')->default(false)->after('is_sellable');
            $table->json('shipping_profile')->nullable()->after('is_physical')->comment('Structured shipping data: weight, dimensions, fragile, notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('egis', function (Blueprint $table) {
            $table->dropColumn(['is_physical', 'shipping_profile']);
        });
    }
};
