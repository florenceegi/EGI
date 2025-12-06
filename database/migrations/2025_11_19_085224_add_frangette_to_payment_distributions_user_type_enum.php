<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Helpers\DatabaseHelper;

/**
 * @Oracode Migration: Add 'frangette' to payment_distributions user_type enum
 * 🎯 Purpose: Extend user_type enum to support Frangette platform role
 * 🛡️ Privacy: Ensures PaymentDistribution can store all wallet platform roles
 *
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0
 * @date 2025-11-19
 */
return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        // Only MySQL/MariaDB support MODIFY COLUMN ENUM syntax
        // PostgreSQL uses VARCHAR which accepts any value
        if (!DatabaseHelper::isMysql()) {
            return;
        }

        DB::statement("ALTER TABLE payment_distributions MODIFY COLUMN user_type ENUM(
            'weak',
            'creator',
            'collector',
            'commissioner',
            'company',
            'epp',
            'trader-pro',
            'vip',
            'natan',
            'frangette'
        ) NOT NULL COMMENT 'Tipologia utente beneficiario'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        if (!DatabaseHelper::isMysql()) {
            return;
        }

        // Remove 'frangette' from enum (only if no records use it)
        // Note: This will fail if any records have user_type = 'frangette'
        DB::statement("ALTER TABLE payment_distributions MODIFY COLUMN user_type ENUM(
            'weak',
            'creator',
            'collector',
            'commissioner',
            'company',
            'epp',
            'trader-pro',
            'vip',
            'natan'
        ) NOT NULL COMMENT 'Tipologia utente beneficiario'");
    }
};