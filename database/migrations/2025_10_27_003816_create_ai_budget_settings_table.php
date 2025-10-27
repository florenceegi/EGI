<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('ai_budget_settings', function (Blueprint $table) {
            $table->id();
            $table->string('provider')->index(); // anthropic, openai, perplexity, etc.
            $table->decimal('monthly_budget', 10, 2)->default(0); // Budget mensile in USD
            $table->decimal('alert_threshold', 5, 2)->default(75.00); // Soglia alert in percentuale (es. 75%)
            $table->boolean('alerts_enabled')->default(true); // Abilita notifiche
            $table->string('alert_email')->nullable(); // Email per alert (opzionale)
            $table->text('notes')->nullable(); // Note opzionali
            $table->timestamps();

            // Constraint: un solo setting per provider
            $table->unique('provider');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('ai_budget_settings');
    }
};
