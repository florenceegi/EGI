{{-- resources/views/layouts/collection.blade.php --}}
{{-- 📜 Oracode Layout: Collection Detail with Guest Styling --}}
{{-- Layout specific for viewing a single collection's details. --}}
{{-- Excludes the main hero section for better focus on collection content. --}}
{{-- Uses Guest layout styling with dark theme and modern design. --}}
{{-- Includes Navbar, Footer, Upload Modal structure. --}}
{{-- Expects $title, $metaDescription, $headExtra, $headMetaExtra, $schemaMarkup slots/variables. --}}
<!DOCTYPE html>

@vite(['resources/css/collections-show.css', 'resources/css/creator-home.css', 'resources/js/creator-home.js'])

@include('layouts.partials.header')

{{-- NESSUNA SEZIONE HERO - Focus diretto sul contenuto --}}

{{-- Contenuto specifico della pagina - Ruolo ARIA main --}}
<main id="main-content" role="main" class="flex-grow bg-gray-900">
    {{ $slot }}
</main>

<!-- Footer - Stile Guest Layout -->
@include('components.info-footer')

{{-- Modal di Upload Ultra-Wide (10/12 del display) --}}
{{-- <div id="upload-modal" class="fixed inset-0 z-[10000] flex items-center justify-center bg-black bg-opacity-75 hidden" role="dialog" aria-modal="true" aria-hidden="true" tabindex="-1">
        <div class="relative p-4 bg-gray-800 rounded-lg shadow-xl modal-container md:p-6 lg:p-8 xl:p-10">
            <button id="close-upload-modal" class="absolute z-10 text-2xl leading-none text-gray-400 md:text-3xl top-3 right-4 md:top-6 md:right-6 hover:text-white" aria-label="Close upload modal">×</button>
            <div class="pr-8 modal-content md:pr-12">
                @include('egimodule::partials.uploading_form_content')
            </div>
        </div>
    </div> --}}

{{-- Universal Search Modal disponibile anche per i guest --}}
<x-universal-search-modal />

<div id="upload-modal" class="modal hidden" role="dialog" aria-modal="true" aria-hidden="true" tabindex="-1"
    aria-labelledby="upload-modal-title">
    <div role="document">
        <button id="close-upload-modal" type="button"
            aria-label="{{ __('guest_layout.close_upload_modal_aria_label') }}">
            <span aria-hidden="true">&times;</span>
        </button>
        @include('egimodule::partials.uploading_form_content')
    </div>
</div>

<x-wallet-connect-modal />
<x-real-wallet-connect-modal />

{{-- Egili Purchase Modal (global - accessible for subscriptions) --}
    <x-egili-purchase-modal />

    <!-- Create Collection Modal (OS1 Integration) -->
    @include('components.create-collection-modal')

    {{-- Asset JS (Vite) --}}
@vite(['resources/js/guest.js', 'resources/js/polyfills.js', 'resources/ts/main.ts', 'resources/js/app.js', 'resources/css/reservation-history.css', 'resources/js/reservation-history.js'])

{{-- Stack per script specifici della pagina --}}
@stack('scripts')

</body>

</html>
