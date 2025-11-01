<?php

/**
 * @package App\Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Egili Token System)
 * @date 2025-11-01
 * @purpose Create egili_transactions table for complete audit trail
 * 
 * GDPR COMPLIANCE:
 * - Full audit trail for all Egili movements
 * - User consent tracking via GDPR audit log
 * - IP and User-Agent for security
 * - Polymorphic source tracking for transparency
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * 
     * Creates egili_transactions table with:
     * - Complete transaction history
     * - GDPR-compliant audit trail
     * - Polymorphic source tracking
     * - Balance snapshots (before/after)
     */
    public function up(): void
    {
        Schema::create('egili_transactions', function (Blueprint $table) {
            $table->id();
            
            // === RELATIONSHIPS ===
            $table->foreignId('wallet_id')
                ->constrained('wallets')
                ->onDelete('cascade')
                ->comment('Wallet proprietario (source of truth)');
            
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('User proprietario (denormalized per query performance)');
            
            // === TRANSACTION TYPE ===
            $table->enum('transaction_type', [
                'earned',           // Guadagnato (trading, milestone, gamification)
                'spent',            // Speso (fee discount, services, subscriptions)
                'admin_grant',      // Bonus admin (manual adjustment)
                'admin_deduct',     // Correzione admin (manual adjustment)
                'purchase',         // Acquisto pack Egili (futuro feature)
                'refund',           // Rimborso
                'expiration',       // Scadenza Egili earned (se implementato)
                'initial_bonus',    // Bonus iniziale welcome
            ])->comment('Tipo transazione (categoria operazione)');
            
            $table->enum('operation', [
                'add',      // Aggiunge al saldo (balance_after = balance_before + amount)
                'subtract'  // Sottrae dal saldo (balance_after = balance_before - amount)
            ])->comment('Operazione aritmetica (add/subtract)');
            
            // === AMOUNTS & BALANCE TRACKING ===
            $table->unsignedBigInteger('amount')
                ->comment('Quantità Egili transazione (sempre positivo)');
            
            $table->unsignedBigInteger('balance_before')
                ->comment('Saldo wallet prima della transazione (audit trail)');
            
            $table->unsignedBigInteger('balance_after')
                ->comment('Saldo wallet dopo la transazione (audit trail)');
            
            // === SOURCE TRACKING (Polymorphic) ===
            $table->string('source_type')->nullable()
                ->comment('Tipo entità sorgente (Egi, Reservation, EgiLivingSubscription, etc)');
            
            $table->unsignedBigInteger('source_id')->nullable()
                ->comment('ID entità sorgente (polymorphic relationship)');
            
            // === REASON & CATEGORY ===
            $table->string('reason')
                ->comment('Motivo transazione machine-readable (es: egi_sale_cashback, living_subscription_payment)');
            
            $table->string('category')->nullable()
                ->comment('Categoria per reporting (trading, service, milestone, gamification, admin)');
            
            // === METADATA (JSON Flexible) ===
            $table->json('metadata')->nullable()
                ->comment('Dati aggiuntivi context-specific (egi_id, subscription_id, tier, etc)');
            
            // === ADMIN TRACKING (Manual Operations) ===
            $table->foreignId('admin_user_id')->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->comment('Admin che ha eseguito operazione manuale (se transaction_type = admin_*)');
            
            $table->text('admin_notes')->nullable()
                ->comment('Note admin per operazioni manuali (reason per correzioni)');
            
            // === STATUS & ERROR HANDLING ===
            $table->enum('status', [
                'completed',  // Transazione completata con successo
                'pending',    // In attesa di conferma (es: payment pending)
                'failed',     // Transazione fallita (error message populated)
                'reversed',   // Transazione stornata (refund/chargeback)
            ])->default('completed')
                ->comment('Status transazione per error handling e reconciliation');
            
            $table->text('error_message')->nullable()
                ->comment('Messaggio errore se status = failed');
            
            // === GDPR & SECURITY AUDIT TRAIL ===
            $table->string('ip_address', 45)->nullable()
                ->comment('IP address user (IPv4/IPv6 - GDPR audit)');
            
            $table->text('user_agent')->nullable()
                ->comment('User-Agent browser/app (GDPR audit + security)');
            
            $table->timestamps();
            
            // === PERFORMANCE INDEXES ===
            $table->index('wallet_id', 'idx_egili_tx_wallet');
            $table->index('user_id', 'idx_egili_tx_user');
            $table->index('transaction_type', 'idx_egili_tx_type');
            $table->index('category', 'idx_egili_tx_category');
            $table->index(['source_type', 'source_id'], 'idx_egili_tx_source');
            $table->index('status', 'idx_egili_tx_status');
            $table->index('created_at', 'idx_egili_tx_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('egili_transactions');
    }
};

