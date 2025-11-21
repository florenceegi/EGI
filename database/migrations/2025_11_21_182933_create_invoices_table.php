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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            
            // Invoice identification
            $table->unsignedBigInteger('invoice_number'); // Auto-increment per seller
            $table->string('invoice_code', 50)->unique(); // es: "INV-2025-0001"
            $table->enum('invoice_type', ['sales', 'purchase', 'credit_note'])->default('sales');
            $table->enum('invoice_status', ['draft', 'pending', 'sent', 'delivered', 'paid', 'cancelled', 'rejected'])->default('draft');
            
            // Parties (seller/buyer)
            $table->unsignedBigInteger('seller_user_id');
            $table->unsignedBigInteger('buyer_user_id')->nullable();
            
            // Dates
            $table->date('issue_date');
            $table->date('due_date')->nullable();
            $table->date('payment_date')->nullable();
            
            // Amounts
            $table->decimal('subtotal_eur', 10, 2)->default(0);
            $table->decimal('tax_amount_eur', 10, 2)->default(0);
            $table->decimal('total_eur', 10, 2)->default(0);
            $table->string('currency', 3)->default('EUR');
            
            // Payment
            $table->string('payment_method', 50)->nullable();
            $table->text('notes')->nullable();
            
            // Files
            $table->string('pdf_path')->nullable();
            $table->string('xml_path')->nullable(); // FatturaPA XML
            
            // SDI (Sistema di Interscambio - Italian e-invoicing)
            $table->string('sdi_id')->nullable(); // External SDI ID
            $table->enum('sdi_status', ['not_sent', 'pending', 'sent', 'delivered', 'rejected'])->default('not_sent');
            $table->timestamp('sdi_sent_at')->nullable();
            $table->timestamp('sdi_delivered_at')->nullable();
            $table->text('sdi_rejection_reason')->nullable();
            
            // External system (for users managing their own invoicing)
            $table->string('external_system_id')->nullable(); // ID in user's external system
            $table->string('external_system_name')->nullable(); // es: "TeamSystem", "SAP"
            
            // Management mode
            $table->enum('managed_by', ['platform', 'user_external'])->default('platform');
            
            // Metadata
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('seller_user_id');
            $table->index('buyer_user_id');
            $table->index('invoice_number');
            $table->index('invoice_status');
            $table->index('sdi_status');
            $table->index('issue_date');
            $table->index(['seller_user_id', 'invoice_number']);
            
            // Foreign keys
            $table->foreign('seller_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('buyer_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
