<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Add Company EPP Donation Fields
 *
 * @purpose Aggiunge campi per gestire donazione EPP volontaria per utenti company
 * @context Gli utenti company NON hanno obbligo EPP ma DEVONO avere subscription.
 *          Possono però scegliere di donare una percentuale a un progetto EPP.
 * @date 2025-12-05
 */
return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('collections', function (Blueprint $table) {
            // Percentuale donazione EPP volontaria (solo per company)
            // Range: 0.01 (0.01%) a 100.00 (100%), NULL = nessuna donazione
            $table->decimal('epp_donation_percentage', 5, 2)
                ->nullable()
                ->default(null)
                ->after('epp_project_id')
                ->comment('Percentuale donazione EPP volontaria (solo company, 0.01-100.00)');

            // Flag per indicare se EPP è volontario (company) vs obbligatorio (altri)
            $table->boolean('is_epp_voluntary')
                ->default(false)
                ->after('epp_donation_percentage')
                ->comment('True = EPP volontario (company), False = EPP obbligatorio (altri usertype)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('collections', function (Blueprint $table) {
            $table->dropColumn(['epp_donation_percentage', 'is_epp_voluntary']);
        });
    }
};
