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
        Schema::create('pa_web_scrapers', function (Blueprint $table) {
            $table->id();

            // Configurazione base
            $table->string('name'); // Nome scraper (es: "Delibere Firenze")
            $table->string('type')->default('api'); // api, html, hybrid
            $table->string('source_entity'); // Es: "Comune di Firenze"
            $table->text('description')->nullable();

            // Configurazione tecnica
            $table->string('base_url'); // URL base (es: https://accessoconcertificato.comune.fi.it)
            $table->string('api_endpoint')->nullable(); // Endpoint API (es: /trasparenza-atti-cat/searchAtti)
            $table->string('method')->default('GET'); // GET, POST
            $table->json('headers')->nullable(); // Headers HTTP
            $table->json('payload_template')->nullable(); // Template payload per POST
            $table->json('query_params')->nullable(); // Parametri query per GET

            // Parsing e mappatura
            $table->json('data_mapping')->nullable(); // Come mappare i campi (JSON path)
            $table->string('pagination_type')->nullable(); // none, offset, page, cursor
            $table->json('pagination_config')->nullable(); // Config paginazione

            // Scheduling
            $table->boolean('is_active')->default(false);
            $table->string('schedule_frequency')->nullable(); // daily, weekly, monthly, manual
            $table->timestamp('last_run_at')->nullable();
            $table->timestamp('next_run_at')->nullable();
            $table->integer('total_items_scraped')->default(0);

            // Business logic
            $table->foreignId('business_id')->constrained('businesses')->onDelete('cascade');
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->onDelete('set null');

            // Status e logging
            $table->string('status')->default('draft'); // draft, active, paused, error
            $table->text('last_error')->nullable();
            $table->json('stats')->nullable(); // Statistiche ultima esecuzione

            // GDPR Compliance
            $table->string('data_source_type')->default('public'); // public, restricted, private
            $table->text('legal_basis')->nullable(); // Base giuridica (es: "Art. 23 D.Lgs 33/2013 - Trasparenza PA")
            $table->text('data_retention_policy')->nullable(); // Politica conservazione dati
            $table->boolean('gdpr_compliant')->default(true); // Flag compliance GDPR
            $table->json('pii_fields_to_exclude')->nullable(); // Campi PII da escludere
            $table->timestamp('last_gdpr_audit_at')->nullable(); // Ultimo audit GDPR

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['business_id', 'is_active']);
            $table->index('next_run_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pa_web_scrapers');
    }
};
