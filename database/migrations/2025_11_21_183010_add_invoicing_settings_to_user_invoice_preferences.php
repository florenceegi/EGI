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
        Schema::table('user_invoice_preferences', function (Blueprint $table) {
            // Invoicing mode
            $table->enum('invoicing_mode', ['platform_managed', 'user_managed'])->default('platform_managed');
            
            // External system details (for user_managed mode)
            $table->string('external_system_name')->nullable();
            $table->text('external_system_notes')->nullable();
            
            // Automation settings
            $table->boolean('auto_generate_monthly')->default(false);
            $table->enum('invoice_frequency', ['instant', 'monthly', 'manual'])->default('monthly');
            
            // Notification preferences
            $table->boolean('notify_on_invoice_generated')->default(true);
            $table->boolean('notify_buyer_on_invoice')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_invoice_preferences', function (Blueprint $table) {
            $table->dropColumn([
                'invoicing_mode',
                'external_system_name',
                'external_system_notes',
                'auto_generate_monthly',
                'invoice_frequency',
                'notify_on_invoice_generated',
                'notify_buyer_on_invoice',
            ]);
        });
    }
};
