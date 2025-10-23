{{--
/**
 * PA Web Scraper Create View
 *
 * @package Resources\Views\Pa\Scrapers
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0
 * @date 2025-10-23
 * @purpose Create new web scraper configuration with templates
 */
--}}

<x-pa-layout title="Nuovo Scraper">
    <x-slot:breadcrumb>
        <a href="{{ route('pa.dashboard') }}" class="text-[#D4A574] hover:text-[#C39463]">Dashboard</a>
        <span class="mx-2 text-gray-400">/</span>
        <a href="{{ route('pa.scrapers.index') }}" class="text-[#D4A574] hover:text-[#C39463]">Web Scraping Agent</a>
        <span class="mx-2 text-gray-400">/</span>
        <span class="text-gray-700">Nuovo Scraper</span>
    </x-slot:breadcrumb>

    <x-slot:pageTitle>Crea Nuovo Scraper</x-slot:pageTitle>

    {{-- Template Selection (Pre-configured options) --}}
    <div class="mb-8 rounded-xl border border-blue-200 bg-blue-50 p-6">
        <h3 class="mb-4 flex items-center gap-2 text-xl font-bold text-blue-900">
            <span class="material-symbols-outlined">lightbulb</span>
            Template Pre-configurati
        </h3>
        <p class="mb-4 text-sm text-blue-800">
            Seleziona un template per iniziare velocemente con una configurazione già ottimizzata.
            Potrai personalizzarla successivamente.
        </p>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            @foreach ($templates as $key => $template)
                <form method="POST" action="{{ route('pa.scrapers.store') }}">
                    @csrf
                    <input type="hidden" name="template" value="{{ $key }}">
                    <button type="submit"
                        class="w-full rounded-lg border-2 border-blue-300 bg-white p-4 text-left transition-all hover:border-blue-500 hover:shadow-md">
                        <h4 class="mb-1 font-bold text-blue-900">{{ $template['name'] }}</h4>
                        <p class="mb-2 text-sm text-gray-600">{{ $template['description'] }}</p>
                        <span
                            class="inline-block rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-800">
                            {{ strtoupper($template['type']) }}
                        </span>
                        <span
                            class="ml-2 inline-block rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-800">
                            {{ $template['source_entity'] }}
                        </span>
                    </button>
                </form>
            @endforeach
        </div>
    </div>

    {{-- Manual Configuration Form --}}
    <div class="rounded-xl border border-gray-200 bg-white p-8 shadow-sm">
        <h3 class="mb-6 flex items-center gap-2 text-xl font-bold text-[#1B365D]">
            <span class="material-symbols-outlined">tune</span>
            Configurazione Manuale
        </h3>

        <form method="POST" action="{{ route('pa.scrapers.store') }}" class="space-y-6">
            @csrf

            {{-- Nome --}}
            <div>
                <label for="name" class="mb-2 block text-sm font-semibold text-gray-700">
                    Nome Scraper <span class="text-red-600">*</span>
                </label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
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
                    <option value="api" {{ old('type') == 'api' ? 'selected' : '' }}>API (JSON/XML)</option>
                    <option value="html" {{ old('type') == 'html' ? 'selected' : '' }}>HTML Scraping</option>
                </select>
            </div>

            {{-- Source Entity --}}
            <div>
                <label for="source_entity" class="mb-2 block text-sm font-semibold text-gray-700">
                    Ente Fonte <span class="text-red-600">*</span>
                </label>
                <input type="text" name="source_entity" id="source_entity" value="{{ old('source_entity') }}"
                    required
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
                    placeholder="Breve descrizione del scraper e della fonte dati">{{ old('description') }}</textarea>
            </div>

            {{-- Base URL --}}
            <div>
                <label for="base_url" class="mb-2 block text-sm font-semibold text-gray-700">
                    Base URL <span class="text-red-600">*</span>
                </label>
                <input type="url" name="base_url" id="base_url" value="{{ old('base_url') }}" required
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-[#D4A574] focus:outline-none focus:ring-2 focus:ring-[#D4A574]"
                    placeholder="https://example.com">
            </div>

            {{-- API Endpoint --}}
            <div>
                <label for="api_endpoint" class="mb-2 block text-sm font-semibold text-gray-700">
                    API Endpoint
                </label>
                <input type="text" name="api_endpoint" id="api_endpoint" value="{{ old('api_endpoint') }}"
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
                    <option value="GET" {{ old('method') == 'GET' ? 'selected' : '' }}>GET</option>
                    <option value="POST" {{ old('method') == 'POST' ? 'selected' : '' }}>POST</option>
                </select>
            </div>

            {{-- Schedule Frequency --}}
            <div>
                <label for="schedule_frequency" class="mb-2 block text-sm font-semibold text-gray-700">
                    Frequenza Esecuzione
                </label>
                <select name="schedule_frequency" id="schedule_frequency"
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-[#D4A574] focus:outline-none focus:ring-2 focus:ring-[#D4A574]">
                    <option value="daily" {{ old('schedule_frequency') == 'daily' ? 'selected' : '' }}>Giornaliera
                    </option>
                    <option value="weekly" {{ old('schedule_frequency') == 'weekly' ? 'selected' : '' }}>Settimanale
                    </option>
                    <option value="monthly" {{ old('schedule_frequency') == 'monthly' ? 'selected' : '' }}>Mensile
                    </option>
                </select>
            </div>

            {{-- GDPR: Legal Basis --}}
            <div>
                <label for="legal_basis" class="mb-2 block text-sm font-semibold text-gray-700">
                    Base Giuridica (GDPR) <span class="text-red-600">*</span>
                </label>
                <textarea name="legal_basis" id="legal_basis" rows="2" required
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-[#D4A574] focus:outline-none focus:ring-2 focus:ring-[#D4A574]"
                    placeholder="Es: Art. 23 D.Lgs 33/2013 - Obblighi pubblicazione atti PA">{{ old('legal_basis', 'Art. 23 D.Lgs 33/2013 - Obblighi pubblicazione atti PA + Art. 32 D.Lgs 33/2013 - Trasparenza amministrativa') }}</textarea>
            </div>

            {{-- GDPR: Data Retention Policy --}}
            <div>
                <label for="data_retention_policy" class="mb-2 block text-sm font-semibold text-gray-700">
                    Politica Conservazione Dati (GDPR)
                </label>
                <input type="text" name="data_retention_policy" id="data_retention_policy"
                    value="{{ old('data_retention_policy', 'Conservazione permanente come da CAD Art. 22 - Documenti amministrativi informatici') }}"
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-[#D4A574] focus:outline-none focus:ring-2 focus:ring-[#D4A574]">
            </div>

            {{-- Active Status --}}
            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                    {{ old('is_active') ? 'checked' : '' }}
                    class="h-5 w-5 rounded border-gray-300 text-[#D4A574] focus:ring-2 focus:ring-[#D4A574]">
                <label for="is_active" class="text-sm font-semibold text-gray-700">
                    Attiva subito lo scraper
                </label>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-end gap-4 border-t pt-6">
                <a href="{{ route('pa.scrapers.index') }}"
                    class="rounded-lg border border-gray-300 px-6 py-3 font-semibold text-gray-700 transition-colors hover:bg-gray-100">
                    Annulla
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-lg bg-[#D4A574] px-6 py-3 font-semibold text-white transition-colors hover:bg-[#C39563]">
                    <span class="material-symbols-outlined">save</span>
                    Crea Scraper
                </button>
            </div>
        </form>
    </div>
</x-pa-layout>
