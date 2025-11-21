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
        Schema::create('invoice_aggregations', function (Blueprint $table) {
            $table->id();
            
            // User (seller)
            $table->unsignedBigInteger('user_id');
            
            // Period
            $table->date('period_start');
            $table->date('period_end');
            
            // Aggregated data
            $table->decimal('total_sales_eur', 10, 2)->default(0);
            $table->integer('total_items')->default(0);
            $table->integer('total_buyers')->default(0);
            
            // Status
            $table->enum('status', ['pending', 'invoiced', 'exported', 'cancelled'])->default('pending');
            
            // Invoice generated
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->timestamp('invoiced_at')->nullable();
            
            // Export (for user-managed mode)
            $table->string('export_format')->nullable(); // csv, excel, json
            $table->string('export_path')->nullable();
            $table->timestamp('exported_at')->nullable();
            
            // Metadata
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
            $table->index('status');
            $table->index('period_start');
            $table->index('period_end');
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'period_start', 'period_end']);
            
            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_aggregations');
    }
};
