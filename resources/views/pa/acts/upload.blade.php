{{--
/**
 * PA Acts Upload Form
 * 
 * @package Resources\Views\Pa\Acts
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - PA Acts Tokenization)
 * @date 2025-10-04
 * @purpose Form per caricamento nuovo atto PA con firma digitale
 */
--}}

<x-pa-layout title="{{ __('pa_acts.upload.page_title') }}">
    <x-slot:breadcrumb>
        <a href="{{ route('pa.dashboard') }}" class="text-[#D4A574] hover:text-[#C39463]">Dashboard</a>
        <span class="mx-2 text-gray-400">/</span>
        <a href="{{ route('pa.acts.index') }}" class="text-[#D4A574] hover:text-[#C39463]">Atti Tokenizzati</a>
        <span class="mx-2 text-gray-400">/</span>
        <span class="text-gray-700">Carica Nuovo Atto</span>
    </x-slot:breadcrumb>

    <x-slot:pageTitle>Carica Nuovo Atto</x-slot:pageTitle>

    {{-- Instructions Card --}}
    <div class="mb-8 rounded-xl border-l-4 border-[#1B365D] bg-blue-50 p-6">
        <div class="flex items-start">
            <svg class="mr-3 h-6 w-6 flex-shrink-0 text-[#1B365D]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <h3 class="mb-2 font-semibold text-[#1B365D]">Requisiti documento:</h3>
                <ul class="list-inside list-disc space-y-1 text-sm text-gray-700">
                    <li>Formato: <strong>PDF</strong> con firma digitale QES/PAdES</li>
                    <li>Dimensione massima: <strong>20 MB</strong></li>
                    <li>La firma digitale verrà verificata automaticamente</li>
                    <li>Dopo il caricamento, l'atto sarà ancorato su blockchain Algorand</li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Upload Form --}}
    <form action="{{ route('pa.acts.upload.post') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- File Upload Area --}}
        <div class="rounded-xl border border-gray-200 bg-white p-8 shadow-sm">
            <h2 class="mb-6 text-xl font-bold text-[#1B365D]">1. Seleziona documento PDF</h2>

            <div class="mb-4">
                <label for="file"
                    class="mb-2 flex items-center text-sm font-semibold text-gray-700">
                    <svg class="mr-2 h-5 w-5 text-[#1B365D]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    Documento PDF firmato *
                </label>
                <input type="file" id="file" name="file" accept=".pdf,application/pdf" required
                    class="block w-full cursor-pointer rounded-lg border border-gray-300 bg-gray-50 text-sm text-gray-900 transition-colors file:mr-4 file:cursor-pointer file:border-0 file:bg-[#1B365D] file:px-4 file:py-2.5 file:text-sm file:font-semibold file:text-white hover:file:bg-[#0F2342] focus:outline-none focus:ring-2 focus:ring-[#D4A574]" />
                <p class="mt-2 text-xs text-gray-500">
                    Carica il PDF firmato digitalmente (QES/PAdES). Max 20 MB.
                </p>
                @error('file')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Metadata Section --}}
        <div class="rounded-xl border border-gray-200 bg-white p-8 shadow-sm">
            <h2 class="mb-6 text-xl font-bold text-[#1B365D]">2. Dati protocollo e classificazione</h2>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                {{-- Protocol Number --}}
                <div>
                    <label for="protocol_number" class="mb-2 block text-sm font-semibold text-gray-700">
                        Numero Protocollo *
                    </label>
                    <input type="text" id="protocol_number" name="protocol_number" required
                        placeholder="es. 12345/2025" value="{{ old('protocol_number') }}"
                        class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 transition-colors focus:border-[#D4A574] focus:ring-[#D4A574]" />
                    <p class="mt-1 text-xs text-gray-500">Formato: NUMERO/ANNO (es. 12345/2025)</p>
                    @error('protocol_number')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Protocol Date --}}
                <div>
                    <label for="protocol_date" class="mb-2 block text-sm font-semibold text-gray-700">
                        Data Protocollo *
                    </label>
                    <input type="date" id="protocol_date" name="protocol_date" required
                        value="{{ old('protocol_date', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}"
                        class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 transition-colors focus:border-[#D4A574] focus:ring-[#D4A574]" />
                    @error('protocol_date')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Doc Type --}}
                <div>
                    <label for="doc_type" class="mb-2 block text-sm font-semibold text-gray-700">
                        Tipo Documento *
                    </label>
                    <select id="doc_type" name="doc_type" required
                        class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 transition-colors focus:border-[#D4A574] focus:ring-[#D4A574]">
                        <option value="">-- Seleziona tipo --</option>
                        @foreach ($docTypes as $value => $label)
                            <option value="{{ $value }}" {{ old('doc_type') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('doc_type')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Collection --}}
                <div>
                    <label for="collection_id" class="mb-2 block text-sm font-semibold text-gray-700">
                        Collezione *
                    </label>
                    <select id="collection_id" name="collection_id" required
                        class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 transition-colors focus:border-[#D4A574] focus:ring-[#D4A574]">
                        <option value="">-- Seleziona collezione --</option>
                        @foreach ($collections as $collection)
                            <option value="{{ $collection->id }}"
                                {{ old('collection_id') == $collection->id ? 'selected' : '' }}>
                                {{ $collection->collection_name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Collezione di appartenenza dell'atto</p>
                    @error('collection_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Title and Description --}}
        <div class="rounded-xl border border-gray-200 bg-white p-8 shadow-sm">
            <h2 class="mb-6 text-xl font-bold text-[#1B365D]">3. Titolo e descrizione</h2>

            {{-- Title --}}
            <div class="mb-6">
                <label for="title" class="mb-2 block text-sm font-semibold text-gray-700">
                    Titolo Atto *
                </label>
                <input type="text" id="title" name="title" required maxlength="255"
                    placeholder="es. Approvazione bilancio preventivo 2025" value="{{ old('title') }}"
                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 transition-colors focus:border-[#D4A574] focus:ring-[#D4A574]" />
                <p class="mt-1 text-xs text-gray-500">Max 255 caratteri</p>
                @error('title')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="mb-2 block text-sm font-semibold text-gray-700">
                    Descrizione (opzionale)
                </label>
                <textarea id="description" name="description" rows="4" maxlength="5000"
                    placeholder="Descrizione dettagliata dell'atto (opzionale)"
                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 transition-colors focus:border-[#D4A574] focus:ring-[#D4A574]">{{ old('description') }}</textarea>
                <p class="mt-1 text-xs text-gray-500">Max 5000 caratteri</p>
                @error('description')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Submit Buttons --}}
        <div class="flex items-center justify-between rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <a href="{{ route('pa.acts.index') }}"
                class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-6 py-3 font-semibold text-gray-700 transition-colors hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-300">
                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Annulla
            </a>

            <button type="submit"
                class="inline-flex items-center rounded-lg bg-[#D4A574] px-8 py-3 font-bold text-white shadow-md transition-all duration-200 hover:scale-105 hover:bg-[#C39563] focus:outline-none focus:ring-2 focus:ring-[#D4A574] focus:ring-offset-2">
                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
                Carica e Tokenizza Atto
            </button>
        </div>
    </form>
</x-pa-layout>
