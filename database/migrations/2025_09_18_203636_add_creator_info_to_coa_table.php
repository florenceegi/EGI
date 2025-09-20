<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('coa', function (Blueprint $table) {
            // Check if column doesn't exist before adding it
            if (!Schema::hasColumn('coa', 'creator_info')) {
                // JSON field to store creator information when Creator ≠ Author
                $table->json('creator_info')->nullable()->after('metadata');
            }
            
            // Note: Direct indexing on JSON columns is not supported in MySQL
            // If indexing is needed, use generated columns on specific JSON paths
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('coa', function (Blueprint $table) {
            // Drop column if it exists
            if (Schema::hasColumn('coa', 'creator_info')) {
                $table->dropColumn('creator_info');
            }
        });
    }
};
