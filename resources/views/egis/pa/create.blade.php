{{--
/**
 * PA Heritage Upload Form (Universal EGI Architecture)
 *
 * @package Resources\Views\Egis\PA
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - PA Enterprise Architecture STEP 2.3)
 * @date 2025-10-04
 * @purpose Form caricamento nuovo bene culturale per PA entity
 *
 * Architecture:
 * - Routed by: ViewService (egis.pa.create)
 * - Controller: EgiController@create (universal) → EgiController@store
 * - Service: EgiService->store() (role-aware creation)
 * - Form submission: POST /egis (universal route)
 *
 * Features:
 * - PA brand design (#1B365D Blu Algoritmo, #D4A574 Oro Fiorentino)
 * - PA terminology ("Bene Culturale" instead of "Opera")
 * - Collection selector (PA-owned collections only, type='artwork')
 * - Image upload (single file, JPG/PNG/WebP, max 10MB)
 * - Required fields: title, artist, description, creation_date, collection_id, image
 * - Optional: price (hidden for PA MVP)
 *
 * Form Fields:
 * - title: Titolo del bene culturale
 * - artist: Artista/Autore
 * - description: Descrizione dettagliata
 * - creation_date: Anno di creazione
 * - collection_id: Collezione di appartenenza (PA-owned)
 * - image: Immagine principale (file upload)
 *
 * Design Pattern:
 * - Responsive (mobile-first)
 * - Tailwind CSS con PA brand colors
 * - Alpine.js per interazioni
 * - Client-side validation
 * - Accessibility WCAG 2.1 AA
 */
--}}

<x-pa-layout title="Carica Nuovo Bene Culturale">
    <x-slot:breadcrumb>
        <a href="{{ route('pa.dashboard') }}" class="text-[#D4A574] hover:text-[#C39463]">Dashboard</a>
        <span class="mx-2 text-gray-400">/</span>
        <a href="{{ route('egis.index') }}" class="text-[#D4A574] hover:text-[#C39463]">Patrimonio Culturale</a>
        <span class="mx-2 text-gray-400">/</span>
        <span class="text-gray-700">Carica Nuovo Bene</span>
    </x-slot:breadcrumb>

    <x-slot:pageTitle>Carica Nuovo Bene Culturale</x-slot:pageTitle>

    {{-- Page Header --}}
    <div class="mb-8 rounded-xl bg-gradient-to-r from-[#1B365D] to-[#0F2342] p-6 text-white shadow-lg">
        <div class="flex items-center gap-4">
            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-[#D4A574]">
                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
            </div>
            <div>
                <h1 class="mb-1 text-2xl font-bold md:text-3xl">Carica Nuovo Bene Culturale</h1>
                <p class="text-white/80">{{ __('pa_heritage.create_subtitle') }}</p>
            </div>
        </div>
    </div>

    {{-- Upload Form --}}
    <div class="mx-auto max-w-4xl">
        <form action="{{ route('egis.store') }}" method="POST" enctype="multipart/form-data"
            class="rounded-xl bg-white p-8 shadow-lg" x-data="{
                imagePreview: null,
                fileName: '',
                handleFileSelect(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.fileName = file.name;
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.imagePreview = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                }
            }">
            @csrf

            {{-- Form Grid --}}
            <div class="space-y-6">
                {{-- Collection Selector --}}
                <div>
                    <label for="collection_id" class="mb-2 block text-sm font-medium text-gray-700">
                        {{ __('pa_heritage.field_collection') }} <span class="text-red-500">*</span>
                    </label>
                    <select id="collection_id" name="collection_id" required
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-900 focus:border-[#1B365D] focus:outline-none focus:ring-2 focus:ring-[#1B365D]/20">
                        <option value="">{{ __('pa_heritage.field_select_collection') }}</option>
                        @foreach ($collections as $collection)
                            <option value="{{ $collection->id }}"
                                {{ old('collection_id') == $collection->id ? 'selected' : '' }}>
                                {{ $collection->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('collection_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">
                        {{ __('pa_heritage.field_collection_help') }}
                    </p>
                </div>

                {{-- Title --}}
                <div>
                    <label for="title" class="mb-2 block text-sm font-medium text-gray-700">
                        {{ __('pa_heritage.field_title') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" required
                        maxlength="255" placeholder="Es: Statua di David, Palazzo Vecchio, etc."
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-900 placeholder-gray-400 focus:border-[#1B365D] focus:outline-none focus:ring-2 focus:ring-[#1B365D]/20">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Artist --}}
                <div>
                    <label for="artist" class="mb-2 block text-sm font-medium text-gray-700">
                        {{ __('pa_heritage.field_artist') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="artist" name="artist" value="{{ old('artist') }}" required
                        maxlength="255" placeholder="Es: Michelangelo, Leonardo da Vinci, etc."
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-900 placeholder-gray-400 focus:border-[#1B365D] focus:outline-none focus:ring-2 focus:ring-[#1B365D]/20">
                    @error('artist')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="mb-2 block text-sm font-medium text-gray-700">
                        {{ __('pa_heritage.field_description') }} <span class="text-red-500">*</span>
                    </label>
                    <textarea id="description" name="description" rows="6" required
                        placeholder="{{ __('pa_heritage.field_description_placeholder') }}"
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-900 placeholder-gray-400 focus:border-[#1B365D] focus:outline-none focus:ring-2 focus:ring-[#1B365D]/20">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">
                        Fornisci informazioni complete sul bene culturale per facilitare la catalogazione
                    </p>
                </div>

                {{-- Creation Date --}}
                <div>
                    <label for="creation_date" class="mb-2 block text-sm font-medium text-gray-700">
                        Anno di Creazione / Datazione <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="creation_date" name="creation_date" value="{{ old('creation_date') }}"
                        required placeholder="Es: 1504, XVI secolo, 1450-1460, etc." maxlength="100"
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-900 placeholder-gray-400 focus:border-[#1B365D] focus:outline-none focus:ring-2 focus:ring-[#1B365D]/20">
                    @error('creation_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">
                        Indica l'anno o il periodo di creazione del bene culturale
                    </p>
                </div>

                {{-- Image Upload --}}
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">
                        {{ __('pa_heritage.field_image') }} <span class="text-red-500">*</span>
                    </label>

                    {{-- Upload Area --}}
                    <div class="relative">
                        <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp"
                            required @change="handleFileSelect($event)" class="hidden">

                        <label for="image"
                            class="flex cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 p-8 transition-colors hover:border-[#1B365D] hover:bg-gray-100">
                            {{-- Upload Icon --}}
                            <svg class="mb-3 h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                </path>
                            </svg>

                            {{-- Upload Text --}}
                            <p class="mb-1 text-sm font-medium text-gray-700" x-show="!fileName">
                                {{ __('pa_heritage.field_image_click') }}
                            </p>
                            <p class="mb-1 text-sm font-medium text-[#1B365D]" x-show="fileName" x-text="fileName">
                            </p>
                            <p class="text-xs text-gray-500">
                                JPG, PNG o WebP (Max 10MB)
                            </p>
                        </label>
                    </div>

                    {{-- Image Preview --}}
                    <div x-show="imagePreview" class="mt-4">
                        <p class="mb-2 text-sm font-medium text-gray-700">Anteprima:</p>
                        <div class="overflow-hidden rounded-lg border-2 border-gray-200">
                            <img :src="imagePreview" alt="Preview" class="h-auto w-full max-w-md object-cover">
                        </div>
                    </div>

                    @error('image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Form Actions --}}
                <div class="flex flex-col gap-4 border-t border-gray-200 pt-6 sm:flex-row sm:justify-between">
                    <a href="{{ route('egis.index') }}"
                        class="inline-flex items-center justify-center rounded-lg border-2 border-gray-300 bg-white px-6 py-3 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50">
                        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        {{ __('pa_heritage.btn_cancel') }}
                    </a>

                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-[#1B365D] to-[#0F2342] px-8 py-3 text-sm font-medium text-white transition-all hover:from-[#0F2342] hover:to-[#1B365D] hover:shadow-lg">
                        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                            </path>
                        </svg>
                        {{ __('pa_heritage.create_title') }}
                    </button>
                </div>
            </div>
        </form>

        {{-- Help Section --}}
        <div class="mt-8 rounded-xl bg-[#1B365D]/5 p-6">
            <h3 class="mb-3 flex items-center text-lg font-semibold text-[#1B365D]">
                <svg class="mr-2 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Linee Guida per il Caricamento
            </h3>
            <ul class="space-y-2 text-sm text-gray-700">
                <li class="flex items-start">
                    <span class="mr-2 text-[#D4A574]">•</span>
                    <span><strong>Collezione:</strong> Seleziona la collezione appropriata tra quelle gestite dal tuo
                        ente</span>
                </li>
                <li class="flex items-start">
                    <span class="mr-2 text-[#D4A574]">•</span>
                    <span><strong>Titolo:</strong> Usa il nome ufficiale o storico del bene culturale</span>
                </li>
                <li class="flex items-start">
                    <span class="mr-2 text-[#D4A574]">•</span>
                    <span><strong>Descrizione:</strong> Includi informazioni sulla storia, caratteristiche artistiche e
                        stato di conservazione</span>
                </li>
                <li class="flex items-start">
                    <span class="mr-2 text-[#D4A574]">•</span>
                    <span><strong>Immagine:</strong> Carica un'immagine di alta qualità che rappresenti fedelmente il
                        bene culturale</span>
                </li>
                <li class="flex items-start">
                    <span class="mr-2 text-[#D4A574]">•</span>
                    <span><strong>Certificazione:</strong> Dopo il caricamento, potrai richiedere l'emissione del
                        Certificato di Autenticità (CoA)</span>
                </li>
            </ul>
        </div>
    </div>

    {{-- Alpine.js CDN (if not already included in layout) --}}
    @push('scripts')
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @endpush
</x-pa-layout>
