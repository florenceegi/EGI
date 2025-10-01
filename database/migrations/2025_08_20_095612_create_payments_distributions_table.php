<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @Oracode Migration: Payments Distributions Table
 * 🎯 Purpose: Core payment distribution tracking for FlorenceEGI
 * 🛡️ Privacy: Financial distribution tracking with GDPR compliance
 * 🧱 Core Logic: Automatic percentage-based distribution system
 *
 * @author GitHub Copilot for Fabio Cherici
 * @version 1.0.0
 * @date 2025-08-20
 */
return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('payment_distributions', function (Blueprint $table) {
            $table->id();

            // ===== FOREIGN KEYS =====
            $table->foreignId('reservation_id')
                ->constrained('reservations')
                ->onDelete('cascade')
                ->comment('Collegamento alla prenotazione originale');

            $table->foreignId('collection_id')
                ->constrained('collections')
                ->onDelete('cascade')
                ->comment('Collection di appartenenza per query rapide');

            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('Beneficiario della distribuzione');

            // ===== DISTRIBUTION DETAILS =====
            $table->enum('user_type', [
                'weak',
                'creator',
                'collector',
                'commissioner',
                'company',
                'epp',
                'trader-pro',
                'vip',
                'natan'
            ])->comment('Tipologia utente beneficiario');

            $table->decimal('percentage', 5, 2)
                ->comment('Percentuale di distribuzione (es: 15.50%)');

            $table->decimal('amount_eur', 12, 2)
                ->comment('Valore in EUR (fonte di verità)');

            $table->decimal('exchange_rate', 20, 10)
                ->comment('Tasso EUR/ALGO al momento della transazione');

            // ===== EPP TRACKING =====
            $table->boolean('is_epp')
                ->default(false)
                ->comment('Flag per donazioni ambientali');

            // ===== AUDIT FIELDS =====
            $table->json('metadata')->nullable()
                ->comment('Dati aggiuntivi (wallet_address, platform_role, etc.)');

            $table->string('distribution_status', 20)
                ->default('pending')
                ->comment('pending, processed, confirmed, failed');

            // ===== TIMESTAMPS =====
            $table->timestamps();

            // ===== INDEXES FOR PERFORMANCE =====
            $table->index(['reservation_id'], 'idx_payments_dist_reservation');
            $table->index(['collection_id'], 'idx_payments_dist_collection');
            $table->index(['user_id'], 'idx_payments_dist_user');
            $table->index(['user_type'], 'idx_payments_dist_user_type');
            $table->index(['is_epp'], 'idx_payments_dist_epp');
            $table->index(['distribution_status'], 'idx_payments_dist_status');
            $table->index(['created_at'], 'idx_payments_dist_created');

            // Composite indexes for common queries
            $table->index(['collection_id', 'user_type'], 'idx_payments_dist_coll_utype');
            $table->index(['reservation_id', 'user_id'], 'idx_payments_dist_res_user');
            $table->index(['is_epp', 'created_at'], 'idx_payments_dist_epp_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('payment_distributions');
    }
};
