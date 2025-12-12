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
        Schema::table('collections', function (Blueprint $table) {
            // profile_type: 'contributor' (default) or 'normal' (company only)
            $table->enum('profile_type', ['contributor', 'normal'])->default('contributor')->after('description');
            
            // royalty_mode: 'standard' (20% EPP) or 'subscriber' (0% EPP + Sub)
            // Note: Normal profile implicitly behaves like 'subscriber' (0% EPP) but we track mode anyway
            $table->enum('royalty_mode', ['standard', 'subscriber'])->default('standard')->after('profile_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('collections', function (Blueprint $table) {
            $table->dropColumn(['profile_type', 'royalty_mode']);
        });
    }
};
