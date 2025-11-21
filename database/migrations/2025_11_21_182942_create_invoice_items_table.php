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
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            
            // Invoice relationship
            $table->unsignedBigInteger('invoice_id');
            
            // Item details
            $table->string('code', 50)->nullable(); // Product code (es: "EGI-001")
            $table->text('description');
            $table->decimal('quantity', 10, 2)->default(1);
            $table->decimal('unit_price_eur', 10, 2);
            
            // Tax
            $table->decimal('tax_rate', 5, 2)->default(22.00); // IVA 22% default
            $table->decimal('tax_amount_eur', 10, 2)->default(0);
            
            // Total
            $table->decimal('subtotal_eur', 10, 2)->default(0); // quantity * unit_price
            $table->decimal('total_eur', 10, 2)->default(0); // subtotal + tax
            
            // Links to platform entities
            $table->unsignedBigInteger('egi_id')->nullable();
            $table->unsignedBigInteger('payment_distribution_id')->nullable();
            
            // Metadata
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('invoice_id');
            $table->index('egi_id');
            $table->index('payment_distribution_id');
            
            // Foreign keys
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            $table->foreign('egi_id')->references('id')->on('egis')->onDelete('set null');
            $table->foreign('payment_distribution_id')->references('id')->on('payment_distributions')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
