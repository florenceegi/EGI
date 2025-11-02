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
        Schema::table('egili_merchant_purchases', function (Blueprint $table) {
            $table->string('return_url', 512)
                ->nullable()
                ->after('user_agent')
                ->comment('URL to return user after purchase completion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('egili_merchant_purchases', function (Blueprint $table) {
            $table->dropColumn('return_url');
        });
    }
};
