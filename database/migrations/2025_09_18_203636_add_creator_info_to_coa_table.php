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

            // Add index for creator queries if it doesn't exist
            if (!Schema::hasIndex('coa', ['creator_info'])) {
                $table->index('creator_info');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('coa', function (Blueprint $table) {
            // Drop index if it exists
            if (Schema::hasIndex('coa', ['creator_info'])) {
                $table->dropIndex(['creator_info']);
            }
            
            // Drop column if it exists
            if (Schema::hasColumn('coa', 'creator_info')) {
                $table->dropColumn('creator_info');
            }
        });
    }
};
