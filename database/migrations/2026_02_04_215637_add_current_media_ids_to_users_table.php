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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('current_banner_id')->nullable()->after('profile_photo_path');
            $table->unsignedBigInteger('current_profile_image_id')->nullable()->after('current_banner_id');

            // Foreign keys to media table (Spatie)
            $table->foreign('current_banner_id')->references('id')->on('media')->nullOnDelete();
            $table->foreign('current_profile_image_id')->references('id')->on('media')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['current_banner_id']);
            $table->dropForeign(['current_profile_image_id']);
            $table->dropColumn(['current_banner_id', 'current_profile_image_id']);
        });
    }
};
