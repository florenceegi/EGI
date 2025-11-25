{{--
|--------------------------------------------------------------------------
| Layout: Creator Layout (Versione con Struttura Corretta)
|--------------------------------------------------------------------------
|
| Copia di guest.blade.php dove la complessa area <main> e gli slot
| dell'hero sono stati sostituiti da un unico <main> slot subito dopo
| l'header, per permettere alle pagine di definire il proprio layout.
|
--}}
<!DOCTYPE html>

@include('layouts.partials.header')


<main id="main-content" role="main" class="flex-grow">
    {{ $slot }}
</main>

<footer class="mt-auto border-t border-gray-800 bg-gray-900 py-6 md:py-8" role="contentinfo"
    aria-labelledby="footer-heading">
    {{-- IL FOOTER È IDENTICO AL 100% A GUEST.BLADE.PHP --}}
    <h2 id="footer-heading" class="sr-only">{{ __('guest_layout.footer_sr_heading') }}</h2>
    <div class="mx-auto max-w-7xl px-4 text-center sm:px-6 md:flex md:items-center md:justify-between lg:px-8">
        <p class="mb-4 text-sm text-gray-400 md:mb-0">© {{ date('Y') }} {{ __('guest_layout.copyright_holder') }}.
            {{ __('guest_layout.all_rights_reserved') }}</p>
        <div
            class="flex flex-col items-center justify-center space-y-2 md:flex-row md:justify-end md:space-x-4 md:space-y-0">
            <x-environmental-stats format="footer" />
            <div class="rounded-full border border-green-800 bg-green-900/50 px-2 py-0.5 text-xs text-green-400">
                {{ __('guest_layout.algorand_blue_mission') }}</div>
        </div>
    </div>
</footer>

{{-- I MODAL E GLI SCRIPT SONO IDENTICI AL 100% A GUEST.BLADE.PHP --}}
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
<form method="POST" action="{{ route('logout') }}" id="logout-form" style="display: none;">
    @csrf
    <button type="submit" class="sr-only">{{ __('guest_layout.logout_sr_button') }}</button>
</form>
@include('components.create-collection-modal')
@vite(['resources/js/guest.js', 'resources/js/polyfills.js', 'resources/ts/main.ts', 'resources/js/app.js', 'resources/css/reservation-history.css', 'resources/js/reservation-history.js'])
@stack('scripts')
</body>

</html>
