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
            $table->string('commodity_type')->nullable()->after('type'); // OS3: Decoupled commodity type
            $table->json('commodity_metadata')->nullable()->after('commodity_type'); // OS3: JSON Metadata
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('egis', function (Blueprint $table) {
            $table->dropColumn(['commodity_type', 'commodity_metadata']);
        });
    }
};
