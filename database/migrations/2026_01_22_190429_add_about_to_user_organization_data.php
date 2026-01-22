<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @Oracode Migration: Add About/Chi Siamo field to Organization Data
 * 🎯 Purpose: Allow companies to have a public "About Us" description
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @date 2026-01-22
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_organization_data', function (Blueprint $table) {
            $table->text('about')->nullable()->after('org_email')->comment('Chi siamo / About Us description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_organization_data', function (Blueprint $table) {
            $table->dropColumn('about');
        });
    }
};
