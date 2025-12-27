<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds display mode (2D/3D) and prism configuration fields to egis table
     */
    public function up(): void
    {
        Schema::table('egis', function (Blueprint $table) {
            // Display mode: '2d' (default) or '3d' for prism view
            $table->enum('display_mode', ['2d', '3d'])->default('2d')->after('status');
            
            // Prism configuration JSON (colors, materials, bloom, etc.)
            $table->json('prism_config')->nullable()->after('display_mode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('egis', function (Blueprint $table) {
            $table->dropColumn(['display_mode', 'prism_config']);
        });
    }
};
