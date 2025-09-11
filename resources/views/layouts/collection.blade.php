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
    <footer class="py-6 mt-auto bg-gray-900 border-t border-gray-800 md:py-8" role="contentinfo">
        <div class="px-4 mx-auto text-center max-w-7xl sm:px-6 lg:px-8 md:flex md:justify-between md:items-center">
            <p class="mb-4 text-sm text-gray-400 md:mb-0">© {{ date('Y') }} {{ __('Frangette APS') }}. {{ __('All rights reserved') }}</p>
            <div class="flex flex-col items-center justify-center space-y-2 md:flex-row md:justify-end md:space-y-0 md:space-x-4">
                <x-environmental-stats format="footer" />
                <div class="text-xs px-2 py-0.5 rounded-full bg-green-900/50 text-green-400 border border-green-800">{{ __('Algorand Blue Mission') }}</div>
            </div>
        </div>
    </footer>

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

     <div id="upload-modal"
        class="hidden modal"
        role="dialog"
        aria-modal="true"
        aria-hidden="true"
        tabindex="-1"
        aria-labelledby="upload-modal-title">
        <div role="document">
            <button id="close-upload-modal"
                    type="button"
                    aria-label="{{ __('guest_layout.close_upload_modal_aria_label') }}">
                <span aria-hidden="true">&times;</span>
            </button>
            @include('egimodule::partials.uploading_form_content')
        </div>
    </div>

    <x-wallet-connect-modal />

    {{-- Asset JS (Vite) --}}
   @vite(['resources/js/guest.js', 'resources/js/polyfills.js', 'resources/ts/main.ts', 'resources/js/app.js', 'resources/css/reservation-history.css', 'resources/js/reservation-history.js'])

    {{-- Stack per script specifici della pagina --}}
    @stack('scripts')

</body>
</html>
