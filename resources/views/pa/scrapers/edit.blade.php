{{--
/**
 * PA Web Scraper Edit View
 *
 * @package Resources\Views\Pa\Scrapers
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0
 * @date 2025-10-23
 * @purpose Edit existing web scraper configuration
 */
--}}

<x-pa-layout title="Modifica Scraper">
    <x-slot:breadcrumb>
        <a href="{{ route('pa.dashboard') }}" class="text-[#D4A574] hover:text-[#C39463]">Dashboard</a>
        <span class="mx-2 text-gray-400">/</span>
        <a href="{{ route('pa.scrapers.index') }}" class="text-[#D4A574] hover:text-[#C39463]">Web Scraping Agent</a>
        <span class="mx-2 text-gray-400">/</span>
        <span class="text-gray-700">Modifica: {{ $scraper->name }}</span>
    </x-slot:breadcrumb>

    <x-slot:pageTitle>Modifica Scraper: {{ $scraper->name }}</x-slot:pageTitle>

    <div class="rounded-xl border border-gray-200 bg-white p-8 shadow-sm">
        <form method="POST" action="{{ route('pa.scrapers.update', $scraper) }}" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Nome --}}
            <div>
                <label for="name" class="mb-2 block text-sm font-semibold text-gray-700">
                    Nome Scraper <span class="text-red-600">*</span>
                </label>
                <input type="text" name="name" id="name" value="{{ old('name', $scraper->name) }}" required
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-[#D4A574] focus:outline-none focus:ring-2 focus:ring-[#D4A574]"
                    placeholder="Es: Delibere Comune di Firenze">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Type --}}
            <div>
                <label for="type" class="mb-2 block text-sm font-semibold text-gray-700">
                    Tipo Scraper <span class="text-red-600">*</span>
                </label>
                <select name="type" id="type" required
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-[#D4A574] focus:outline-none focus:ring-2 focus:ring-[#D4A574]">
                    <option value="api" {{ old('type', $scraper->type) == 'api' ? 'selected' : '' }}>API (JSON/XML)</option>
                    <option value="html" {{ old('type', $scraper->type) == 'html' ? 'selected' : '' }}>HTML Scraping</option>
                </select>
            </div>

            {{-- Source Entity --}}
            <div>
                <label for="source_entity" class="mb-2 block text-sm font-semibold text-gray-700">
                    Ente Fonte <span class="text-red-600">*</span>
                </label>
                <input type="text" name="source_entity" id="source_entity" value="{{ old('source_entity', $scraper->source_entity) }}" required
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-[#D4A574] focus:outline-none focus:ring-2 focus:ring-[#D4A574]"
                    placeholder="Es: Comune di Firenze">
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="mb-2 block text-sm font-semibold text-gray-700">
                    Descrizione
                </label>
                <textarea name="description" id="description" rows="3"
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-[#D4A574] focus:outline-none focus:ring-2 focus:ring-[#D4A574]"
                    placeholder="Breve descrizione del scraper e della fonte dati">{{ old('description', $scraper->description) }}</textarea>
            </div>

            {{-- Base URL --}}
            <div>
                <label for="base_url" class="mb-2 block text-sm font-semibold text-gray-700">
                    Base URL <span class="text-red-600">*</span>
                </label>
                <input type="url" name="base_url" id="base_url" value="{{ old('base_url', $scraper->base_url) }}" required
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-[#D4A574] focus:outline-none focus:ring-2 focus:ring-[#D4A574]"
                    placeholder="https://example.com">
            </div>

            {{-- API Endpoint --}}
            <div>
                <label for="api_endpoint" class="mb-2 block text-sm font-semibold text-gray-700">
                    API Endpoint
                </label>
                <input type="text" name="api_endpoint" id="api_endpoint" value="{{ old('api_endpoint', $scraper->api_endpoint) }}"
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-[#D4A574] focus:outline-none focus:ring-2 focus:ring-[#D4A574]"
                    placeholder="/api/atti">
            </div>

            {{-- Method --}}
            <div>
                <label for="method" class="mb-2 block text-sm font-semibold text-gray-700">
                    Metodo HTTP <span class="text-red-600">*</span>
                </label>
                <select name="method" id="method" required
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-[#D4A574] focus:outline-none focus:ring-2 focus:ring-[#D4A574]">
                    <option value="GET" {{ old('method', $scraper->method) == 'GET' ? 'selected' : '' }}>GET</option>
                    <option value="POST" {{ old('method', $scraper->method) == 'POST' ? 'selected' : '' }}>POST</option>
                </select>
            </div>

            {{-- Headers (JSON) --}}
            <div>
                <label for="headers" class="mb-2 block text-sm font-semibold text-gray-700">
                    Headers (JSON)
                </label>
                <textarea name="headers" id="headers" rows="4"
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 font-mono text-sm focus:border-[#D4A574] focus:outline-none focus:ring-2 focus:ring-[#D4A574]"
                    placeholder='{"Content-Type": "application/json"}'>{{ old('headers', json_encode($scraper->headers, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) }}</textarea>
                <p class="mt-1 text-xs text-gray-500">Formato JSON. Es: {"Content-Type": "application/json"}</p>
            </div>

            {{-- Payload Template (JSON) --}}
            <div>
                <label for="payload_template" class="mb-2 block text-sm font-semibold text-gray-700">
                    Payload Template (JSON)
                </label>
                <textarea name="payload_template" id="payload_template" rows="6"
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 font-mono text-sm focus:border-[#D4A574] focus:outline-none focus:ring-2 focus:ring-[#D4A574]"
                    placeholder='{"year": "{{year}}", "tipo": "DG"}'>{{ old('payload_template', json_encode($scraper->payload_template, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) }}</textarea>
                <p class="mt-1 text-xs text-gray-500">Supporta variabili: {{year}}, {{month}}, {{tipo}}, ecc.</p>
            </div>

            {{-- Schedule Frequency --}}
            <div>
                <label for="schedule_frequency" class="mb-2 block text-sm font-semibold text-gray-700">
                    Frequenza Esecuzione
                </label>
                <select name="schedule_frequency" id="schedule_frequency"
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-[#D4A574] focus:outline-none focus:ring-2 focus:ring-[#D4A574]">
                    <option value="daily" {{ old('schedule_frequency', $scraper->schedule_frequency) == 'daily' ? 'selected' : '' }}>Giornaliera</option>
                    <option value="weekly" {{ old('schedule_frequency', $scraper->schedule_frequency) == 'weekly' ? 'selected' : '' }}>Settimanale</option>
                    <option value="monthly" {{ old('schedule_frequency', $scraper->schedule_frequency) == 'monthly' ? 'selected' : '' }}>Mensile</option>
                </select>
            </div>

            {{-- GDPR: Legal Basis --}}
            <div>
                <label for="legal_basis" class="mb-2 block text-sm font-semibold text-gray-700">
                    Base Giuridica (GDPR) <span class="text-red-600">*</span>
                </label>
                <textarea name="legal_basis" id="legal_basis" rows="2" required
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-[#D4A574] focus:outline-none focus:ring-2 focus:ring-[#D4A574]"
                    placeholder="Es: Art. 23 D.Lgs 33/2013 - Obblighi pubblicazione atti PA">{{ old('legal_basis', $scraper->legal_basis) }}</textarea>
            </div>

            {{-- GDPR: Data Retention Policy --}}
            <div>
                <label for="data_retention_policy" class="mb-2 block text-sm font-semibold text-gray-700">
                    Politica Conservazione Dati (GDPR)
                </label>
                <input type="text" name="data_retention_policy" id="data_retention_policy"
                    value="{{ old('data_retention_policy', $scraper->data_retention_policy) }}"
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-[#D4A574] focus:outline-none focus:ring-2 focus:ring-[#D4A574]">
            </div>

            {{-- GDPR: PII Fields to Exclude (JSON Array) --}}
            <div>
                <label for="pii_fields_to_exclude" class="mb-2 block text-sm font-semibold text-gray-700">
                    Campi PII da Escludere (JSON Array)
                </label>
                <textarea name="pii_fields_to_exclude" id="pii_fields_to_exclude" rows="3"
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 font-mono text-sm focus:border-[#D4A574] focus:outline-none focus:ring-2 focus:ring-[#D4A574]"
                    placeholder='["email", "telefono", "codice_fiscale"]'>{{ old('pii_fields_to_exclude', json_encode($scraper->pii_fields_to_exclude)) }}</textarea>
                <p class="mt-1 text-xs text-gray-500">Array JSON. Es: ["email", "telefono", "indirizzo"]</p>
            </div>

            {{-- Active Status --}}
            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                    {{ old('is_active', $scraper->is_active) ? 'checked' : '' }}
                    class="h-5 w-5 rounded border-gray-300 text-[#D4A574] focus:ring-2 focus:ring-[#D4A574]">
                <label for="is_active" class="text-sm font-semibold text-gray-700">
                    Scraper attivo
                </label>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-between border-t pt-6">
                <a href="{{ route('pa.scrapers.show', $scraper) }}"
                    class="rounded-lg border border-gray-300 px-6 py-3 font-semibold text-gray-700 transition-colors hover:bg-gray-100">
                    Annulla
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-lg bg-[#D4A574] px-6 py-3 font-semibold text-white transition-colors hover:bg-[#C39563]">
                    <span class="material-symbols-outlined">save</span>
                    Salva Modifiche
                </button>
            </div>
        </form>
    </div>
</x-pa-layout>

