<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @Oracode Migration: Add missing organization fields
 * 🎯 Purpose: Add organization_type, is_verified, verification_level fields
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @date 2026-01-23
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_organization_data', function (Blueprint $table) {
            // Organization type (legal form)
            $table->string('organization_type', 50)->nullable()->after('business_type')
                ->comment('Legal organization type: sole_proprietorship, srl, spa, snc, sas, cooperative, association, foundation, ngo, public_entity, other');
            
            // Verification status
            $table->boolean('is_verified')->default(false)->after('is_seller_verified')
                ->comment('Whether organization data has been verified');
            
            // Verification level
            $table->string('verification_level', 20)->default('none')->after('is_verified')
                ->comment('Verification level: none, basic, standard, enhanced');
            
            // Verified at timestamp
            $table->timestamp('verified_at')->nullable()->after('verification_level')
                ->comment('When organization was verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_organization_data', function (Blueprint $table) {
            $table->dropColumn([
                'organization_type',
                'is_verified',
                'verification_level',
                'verified_at',
            ]);
        });
    }
};
