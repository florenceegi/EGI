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
        Schema::table('epp_projects', function (Blueprint $table) {
            // Rinomina 'media' in 'media_data' per evitare conflitto con Spatie Media Library
            $table->renameColumn('media', 'media_data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('epp_projects', function (Blueprint $table) {
            $table->renameColumn('media_data', 'media');
        });
    }
};
