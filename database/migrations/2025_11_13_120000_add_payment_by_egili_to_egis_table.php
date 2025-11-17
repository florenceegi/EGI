<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @Oracode Migration: Add payment_by_egili flag to egis table
 * 🎯 Purpose: Allow creators to enable Egili payments per EGI
 * 🛡️ Compliance: Default disabled to preserve existing behaviour
 */
return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('egis', function (Blueprint $table) {
            $table->boolean('payment_by_egili')
                ->default(false)
                ->after('price')
                ->comment('Indicates if this EGI accepts Egili as payment method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('egis', function (Blueprint $table) {
            $table->dropColumn('payment_by_egili');
        });
    }
};

