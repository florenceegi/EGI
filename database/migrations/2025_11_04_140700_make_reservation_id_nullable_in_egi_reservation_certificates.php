<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @Oracode Migration: Make reservation_id nullable in egi_reservation_certificates
 * 🎯 Purpose: Allow mint certificates without prior reservation (direct mint)
 * 🧱 Core Logic: Alter reservation_id to be nullable for direct purchase/mint scenarios
 * 🛡️ Safety: Preserves foreign key constraint, only changes nullability
 *
 * @package Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Direct Mint Certificates)
 * @date 2025-11-04
 * @purpose Fix SQLSTATE[HY000]: 1364 Field 'reservation_id' doesn't have a default value
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('egi_reservation_certificates', function (Blueprint $table) {
            // Make reservation_id nullable (mint certificates can exist without reservation)
            $table->unsignedBigInteger('reservation_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('egi_reservation_certificates', function (Blueprint $table) {
            // Revert to NOT NULL (only if all records have reservation_id!)
            // WARNING: This will fail if there are NULL values in the column
            $table->unsignedBigInteger('reservation_id')->nullable(false)->change();
        });
    }
};

