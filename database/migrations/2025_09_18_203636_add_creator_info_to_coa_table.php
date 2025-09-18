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
        Schema::table('coa', function (Blueprint $table) {
            // JSON field to store creator information when Creator ≠ Author
            $table->json('creator_info')->nullable()->after('metadata');
            
            // Add index for creator queries
            $table->index('creator_info');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coa', function (Blueprint $table) {
            $table->dropIndex(['creator_info']);
            $table->dropColumn('creator_info');
        });
    }
};
