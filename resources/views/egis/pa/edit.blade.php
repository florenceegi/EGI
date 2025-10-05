{{--
/**
 * PA Heritage Edit Form (Universal EGI Architecture)
 *
 * @package Resources\Views\Egis\PA
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - PA Enterprise Architecture STEP 2.3)
 * @date 2025-10-04
 * @purpose Form modifica bene culturale esistente per PA entity
 *
 * Architecture:
 * - Routed by: ViewService (egis.pa.edit)
 * - Controller: EgiController@edit (universal) → EgiController@update
 * - Service: EgiService->update() (role-aware update)
 * - Form submission: PUT /egis/{egi} (universal route)
 *
 * Features:
 * - PA brand design (#1B365D Blu Algoritmo, #D4A574 Oro Fiorentino)
 * - PA terminology ("Bene Culturale" instead of "Opera")
 * - Pre-populated form fields from $egi model
 * - CoA information display (read-only if exists)
 * - Image update optional (keep existing or upload new)
 * - Collection change allowed (PA-owned collections only)
 *
 * Form Fields:
 * - title: Titolo del bene culturale (editable)
 * - artist: Artista/Autore (editable)
 * - description: Descrizione dettagliata (editable)
 * - creation_date: Anno di creazione (editable)
 * - collection_id: Collezione di appartenenza (editable, PA-owned)
 * - image: Immagine principale (optional update)
 *
 * CoA Info (read-only if exists):
 * - Status: verified/pending/no_coa
 * - Serial number
 * - Issued date
 * - Warning: CoA fields not editable after issuance
 *
 * Design Pattern:
 * - Responsive (mobile-first)
 * - Tailwind CSS con PA brand colors
 * - Alpine.js per interazioni
 * - Client-side validation
 * - Accessibility WCAG 2.1 AA
 */
--}}

<x-pa-layout title="{{ __('pa_heritage.edit_title') }}">
    <x-slot:breadcrumb>
        <a href="{{ route('pa.dashboard') }}" class="text-[#D4A574] hover:text-[#C39463]">Dashboard</a>
        <span class="mx-2 text-gray-400">/</span>
        <a href="{{ route('egis.index') }}" class="text-[#D4A574] hover:text-[#C39463]">Patrimonio Culturale</a>
        <span class="mx-2 text-gray-400">/</span>
        <a href="{{ route('egis.show', $egi) }}"
            class="text-[#D4A574] hover:text-[#C39463]">{{ Str::limit($egi->title, 30) }}</a>
        <span class="mx-2 text-gray-400">/</span>
        <span class="text-gray-700">Modifica</span>
    </x-slot:breadcrumb>

    <x-slot:pageTitle>{{ __('pa_heritage.edit_title') }}</x-slot:pageTitle>

    {{-- Page Header --}}
    <div class="mb-8 rounded-xl bg-gradient-to-r from-[#1B365D] to-[#0F2342] p-6 text-white shadow-lg">
        <div class="flex items-center gap-4">
            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-[#D4A574]">
                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                    </path>
                </svg>
            </div>
            <div>
                <h1 class="mb-1 text-2xl font-bold md:text-3xl">{{ __('pa_heritage.edit_title') }}</h1>
                <p class="text-white/80">{{ $egi->title }}</p>
            </div>
        </div>
    </div>

    {{-- CoA Warning (if exists) --}}
    @if ($egi->coa && $egi->coa->status === 'verified')
        <div class="mx-auto mb-6 max-w-4xl rounded-lg border-l-4 border-amber-500 bg-amber-50 p-4">
            <div class="flex items-start">
                <svg class="mr-3 h-6 w-6 flex-shrink-0 text-amber-500" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                    </path>
                </svg>
                <div>
                    <h3 class="text-sm font-medium text-amber-800">{{ __('pa_heritage.coa_active_warning_title') }}</h3>
                    <p class="mt-1 text-sm text-amber-700">
                        {{ __('pa_heritage.coa_active_warning_message') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- Edit Form --}}
    <div class="mx-auto max-w-4xl">
        <form action="{{ route('egis.update', $egi) }}" method="POST" enctype="multipart/form-data"
            class="rounded-xl bg-white p-8 shadow-lg" x-data="{
                imagePreview: '{{ $egi->getFirstMediaUrl('default', 'medium') }}',
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
            @method('PUT')

            {{-- Form Grid --}}
            <div class="space-y-6">
                {{-- Collection Selector --}}
                <div>
                    <label for="collection_id" class="mb-2 block text-sm font-medium text-gray-700">
                        {{ __('pa_heritage.field_collection_label') }} <span class="text-red-500">*</span>
                    </label>
                    <select id="collection_id" name="collection_id" required
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-900 focus:border-[#1B365D] focus:outline-none focus:ring-2 focus:ring-[#1B365D]/20">
                        @foreach ($collections as $collection)
                            <option value="{{ $collection->id }}"
                                {{ $egi->collection_id == $collection->id ? 'selected' : '' }}>
                                {{ $collection->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('collection_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Title --}}
                <div>
                    <label for="title" class="mb-2 block text-sm font-medium text-gray-700">
                        {{ __('pa_heritage.field_title_label') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="title" name="title" value="{{ old('title', $egi->title) }}"
                        required maxlength="255" placeholder="{{ __('pa_heritage.field_title_placeholder') }}"
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-900 placeholder-gray-400 focus:border-[#1B365D] focus:outline-none focus:ring-2 focus:ring-[#1B365D]/20">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Artist --}}
                <div>
                    <label for="artist" class="mb-2 block text-sm font-medium text-gray-700">
                        {{ __('pa_heritage.field_artist_label') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="artist" name="artist" value="{{ old('artist', $egi->artist) }}"
                        required maxlength="255" placeholder="{{ __('pa_heritage.field_artist_placeholder') }}"
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-900 placeholder-gray-400 focus:border-[#1B365D] focus:outline-none focus:ring-2 focus:ring-[#1B365D]/20">
                    @error('artist')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="mb-2 block text-sm font-medium text-gray-700">
                        {{ __('pa_heritage.field_description_label') }} <span class="text-red-500">*</span>
                    </label>
                    <textarea id="description" name="description" rows="6" required
                        placeholder="{{ __('pa_heritage.field_description_placeholder_edit') }}"
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-900 placeholder-gray-400 focus:border-[#1B365D] focus:outline-none focus:ring-2 focus:ring-[#1B365D]/20">{{ old('description', $egi->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Creation Date --}}
                <div>
                    <label for="creation_date" class="mb-2 block text-sm font-medium text-gray-700">
                        {{ __('pa_heritage.field_creation_date') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="creation_date" name="creation_date"
                        value="{{ old('creation_date', $egi->creation_date) }}" required
                        placeholder="{{ __('pa_heritage.field_creation_date_placeholder') }}" maxlength="100"
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-900 placeholder-gray-400 focus:border-[#1B365D] focus:outline-none focus:ring-2 focus:ring-[#1B365D]/20">
                    @error('creation_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Current Image & Upload New --}}
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">
                        {{ __('pa_heritage.field_image_label') }}
                    </label>

                    {{-- Current Image Preview --}}
                    <div class="mb-4">
                        <p class="mb-2 text-sm text-gray-600">{{ __('pa_heritage.field_image_current') }}</p>
                        <div class="overflow-hidden rounded-lg border-2 border-gray-200">
                            <img :src="imagePreview" alt="{{ $egi->title }}"
                                class="h-auto w-full max-w-md object-cover">
                        </div>
                    </div>

                    {{-- Upload New Image (Optional) --}}
                    <div class="relative">
                        <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp"
                            @change="handleFileSelect($event)" class="hidden">

                        <label for="image"
                            class="flex cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 p-6 transition-colors hover:border-[#1B365D] hover:bg-gray-100">
                            <svg class="mb-2 h-10 w-10 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                            </svg>

                            <p class="mb-1 text-sm font-medium text-gray-700" x-show="!fileName">
                                {{ __('pa_heritage.field_upload_new_optional') }}
                            </p>
                            <p class="mb-1 text-sm font-medium text-[#1B365D]" x-show="fileName" x-text="fileName">
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ __('pa_heritage.image_format') }}
                            </p>
                        </label>
                    </div>

                    @error('image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-xs text-gray-500">
                        {{ __('pa_heritage.field_keep_current') }}
                    </p>
                </div>

                {{-- CoA Info (if exists) --}}
                @if ($egi->coa)
                    <div class="rounded-lg border border-[#1B365D]/20 bg-[#1B365D]/5 p-4">
                        <h3 class="mb-3 flex items-center text-sm font-semibold text-[#1B365D]">
                            <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ __('pa_heritage.coa_info_title') }}
                        </h3>
                        <div class="grid gap-3 text-sm md:grid-cols-2">
                            <div>
                                <span class="font-medium text-gray-700">{{ __('pa_heritage.coa_info_status') }}</span>
                                <span
                                    class="@if ($egi->coa->status === 'verified') bg-green-100 text-green-800
                                @elseif($egi->coa->status === 'pending') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800 @endif ml-2 rounded px-2 py-1 text-xs font-medium">
                                    {{ ucfirst($egi->coa->status) }}
                                </span>
                            </div>
                            @if ($egi->coa->serial_number)
                                <div>
                                    <span class="font-medium text-gray-700">{{ __('pa_heritage.coa_info_serial') }}</span>
                                    <span class="ml-2 text-gray-600">{{ $egi->coa->serial_number }}</span>
                                </div>
                            @endif
                            @if ($egi->coa->issued_at)
                                <div>
                                    <span class="font-medium text-gray-700">{{ __('pa_heritage.coa_info_issued') }}</span>
                                    <span
                                        class="ml-2 text-gray-600">{{ $egi->coa->issued_at->format('d/m/Y') }}</span>
                                </div>
                            @endif
                        </div>
                        <p class="mt-3 text-xs text-gray-600">
                            {{ __('pa_heritage.coa_info_readonly') }}
                        </p>
                    </div>
                @endif

                {{-- Form Actions --}}
                <div class="flex flex-col gap-4 border-t border-gray-200 pt-6 sm:flex-row sm:justify-between">
                    <a href="{{ route('egis.show', $egi) }}"
                        class="inline-flex items-center justify-center rounded-lg border-2 border-gray-300 bg-white px-6 py-3 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50">
                        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        {{ __('pa_heritage.btn_cancel_edit') }}
                    </a>

                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-[#1B365D] to-[#0F2342] px-8 py-3 text-sm font-medium text-white transition-all hover:from-[#0F2342] hover:to-[#1B365D] hover:shadow-lg">
                        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        {{ __('pa_heritage.btn_save') }}
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Alpine.js CDN (if not already included in layout) --}}
    @push('scripts')
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @endpush
</x-pa-layout>
