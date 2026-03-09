{{--
    /partials/uploading_form_content.blade.php
    🎯 CONSERVATIVE MOBILE FIX - Mantiene tutto visibile
    📱 Miglioramenti mobile senza stravolgere la struttura
--}}
@vite(['vendor/ultra/ultra-upload-manager/resources/css/app.css'])


{{-- START: Schema.org Markup (JSON-LD) --}}
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebPage",
  "name": "{{ __('uploadmanager::uploadmanager.mint_your_masterpiece') }}",
  "description": "Form to upload and manage your EGI (Ecological Goods Invent) assets for minting on the FlorenceEGI platform, including features like secure storage, virus scan, and advanced validation. Part of the Frangette ecosystem.",
  "isPartOf": {
    "@type": "WebSite",
    "url": "https://florenceegi.com/"
  },
  "publisher": {
    "@type": "Organization",
    "name": "Frangette Cultural Promotion Association",
    "url": "https://frangette.com/",
    "logo": {
      "@type": "ImageObject",
      "url": "https://frangette.com/images/logo-frangette.png"
    }
  }
}
</script>
{{-- END: Schema.org Markup --}}

{{-- Container principale - MOBILE-FIRST RESPONSIVE --}}
<div class="relative w-full p-4 border-0 rounded-none shadow-xl nft-background max-w-none bg-gradient-to-br from-gray-800 via-purple-900 to-blue-900 md:rounded-xl md:border md:border-purple-500/30 md:p-5"
    id="upload-container" data-upload-type="egi" role="form"
    aria-label="{{ __('uploadmanager::uploadmanager.mint_your_masterpiece') }}">

    <!-- Title - dimensioni leggermente più piccole per mobile -->
    <h2 class="mb-4 text-xl font-extrabold tracking-wide text-center text-white nft-title drop-shadow-md md:text-2xl">
        💎 {{ __('uploadmanager::uploadmanager.mint_your_masterpiece') }}
    </h2>

    <!-- ╔════════════════════════════════════════════════════════════╗
         ║  NATAN Batch Mint — Trigger Button                         ║
         ║  Puramente additivo: tocca solo questo blocco.             ║
         ║  Non modifica nulla del form o dell'upload manager.        ║
         ╚════════════════════════════════════════════════════════════╝ -->
    <div class="mb-4 rounded-xl border border-purple-500/30 bg-purple-950/20 p-3
                flex flex-col sm:flex-row items-center gap-3">

        <div class="flex items-center gap-2 flex-1 min-w-0">
            <span class="text-2xl shrink-0" role="img" aria-hidden="true">🤖</span>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-white leading-tight">
                    Lascia fare a NATAN
                </p>
                <p class="text-xs text-gray-400 truncate">
                    Ti guido io: prezzo, titolo e file in 4 passi.
                </p>
            </div>
        </div>

        <button type="button"
                id="natan-batch-mint-trigger"
                class="shrink-0 rounded-full bg-purple-600 hover:bg-purple-500 active:bg-purple-700
                       px-4 py-1.5 text-xs font-semibold text-white transition-colors
                       focus:outline-none focus:ring-2 focus:ring-purple-400 focus:ring-offset-2
                       focus:ring-offset-gray-900"
                aria-label="Avvia NATAN Assistente Batch Mint">
            Inizia
        </button>
    </div>
    <!-- END NATAN Batch Mint Trigger -->

    <!-- Enhanced drag & drop upload area con bordo tratteggiato tradizionale -->
    <div class="flex flex-col items-center justify-center w-full p-6 mb-4 transition-all duration-300 border-2 border-gray-400 border-dashed rounded-lg group h-36 bg-gray-50/5 hover:border-blue-400 hover:bg-gray-50/10 md:h-44"
        id="upload-drop-zone" role="group" aria-label="{{ trans('uploadmanager::uploadmanager.drag_files_here') }}">

        <!-- Drag & drop icon/illustration -->
        <div
            class="mb-3 text-2xl text-gray-400 transition-transform duration-300 group-hover:scale-110 group-hover:text-blue-400 md:text-3xl">
            📤
        </div>

        <!-- Instructions with improved contrast -->
        <p class="mb-4 text-sm text-center text-gray-300 md:text-base">
            {{ trans('uploadmanager::uploadmanager.drag_files_here') }} <br>
            <span class="text-xs text-gray-400">{{ trans('uploadmanager::uploadmanager.or') }}</span>
        </p>

        <!-- Link semplice al posto del bottone -->
        <label for="files" id="file-label"
            class="relative text-sm text-blue-400 underline transition-colors duration-200 cursor-pointer hover:text-blue-300 md:text-base"
            aria-label="{{ trans('uploadmanager::uploadmanager.select_files_aria') }}">
            Carica i tuoi file
            <input type="file" id="files" multiple
                class="absolute top-0 left-0 w-full h-full opacity-0 cursor-pointer">
        </label>
        {{-- <div class="upload-dropzone text-center text-gray-200 text-xs mt-1.5">
            <!-- About upload size -->
        </div> --}}
    </div>

    {{-- Metadata partial --}}
    @include('egimodule::partials.metadata')

    <!-- Progress bar and virus switch -->
    <div class="mt-4 space-y-4">
        <div class="w-full h-2 overflow-hidden bg-gray-700 rounded-full" role="progressbar" aria-valuenow="0"
            aria-valuemin="0" aria-valuemax="100" aria-describedby="progress-text">
            <div class="h-2 transition-all duration-500 rounded-full bg-gradient-to-r from-green-400 to-blue-500"
                id="progress-bar"></div>
        </div>
        <p class="text-xs text-center text-gray-200"><span id="progress-text"></span></p>

        <div class="flex items-center justify-center gap-2">
            <input
                class="me-1 h-3 w-6 appearance-none rounded-full bg-gray-600 before:pointer-events-none before:absolute before:h-3 before:w-3 before:rounded-full before:bg-transparent after:absolute after:z-[2] after:-mt-0.5 after:h-4 after:w-4 after:rounded-full after:bg-white after:shadow-sm after:transition-all checked:bg-purple-600 checked:after:ms-3 checked:after:bg-purple-400 checked:after:shadow-sm hover:cursor-pointer focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 focus:ring-offset-gray-900"
                type="checkbox" role="switch" id="scanvirus"
                title="{{ trans('uploadmanager::uploadmanager.toggle_virus_scan') }}" aria-checked="false"
                aria-labelledby="scanvirus_label" />
            <label class="text-xs font-medium text-red-400 hover:pointer-events-none" id="scanvirus_label"
                for="scanvirus">{{ trans('uploadmanager::uploadmanager.virus_scan_disabled') }}</label>
        </div>
        <p class="text-xs text-center text-gray-200"><span id="virus-advise"></span></p>
    </div>

    <!-- Action buttons - affiancati anche su mobile con padding ridotto -->
    <div class="flex justify-center gap-2 mt-6 md:gap-4">
        <button type="button" id="uploadBtn"
            class="nft-button group relative max-w-xs flex-1 cursor-not-allowed rounded-full bg-green-500 px-2 py-1.5 text-xs font-semibold text-white opacity-50 disabled:hover:bg-green-500 disabled:hover:shadow-none md:px-5 md:py-2.5 md:text-base"
            aria-label="{{ trans('uploadmanager::uploadmanager.save_aria') }}" aria-disabled="true">
            💾 {{ trans('uploadmanager::uploadmanager.save_the_files') }}
            <span
                class="pointer-events-none absolute -top-8 left-1/2 hidden w-32 -translate-x-1/2 transform rounded bg-gray-800 px-1.5 py-0.5 text-center text-[10px] text-white opacity-0 transition-opacity duration-300 group-hover:opacity-100 md:block">
                {{ trans('uploadmanager::uploadmanager.save_tooltip') }}
            </span>
        </button>
        <button type="button" onclick="cancelUpload()" id="cancelUpload"
            class="nft-button group relative max-w-xs flex-1 cursor-not-allowed rounded-full bg-red-500 px-2 py-1.5 text-xs font-semibold text-white opacity-50 disabled:hover:bg-red-500 disabled:hover:shadow-none md:px-5 md:py-2.5 md:text-base"
            aria-label="{{ trans('uploadmanager::uploadmanager.cancel_aria') }}" aria-disabled="true">
            ❌ {{ trans('uploadmanager::uploadmanager.cancel') }}
            <span
                class="pointer-events-none absolute -top-8 left-1/2 hidden w-32 -translate-x-1/2 transform rounded bg-gray-800 px-1.5 py-0.5 text-center text-[10px] text-white opacity-0 transition-opacity duration-300 group-hover:opacity-100 md:block">
                {{ trans('uploadmanager::uploadmanager.cancel_tooltip') }}
            </span>
        </button>
    </div>

    <!-- Previews grid - quadratini piccoli su mobile -->
    <div id="collection" class="grid grid-cols-4 gap-2 mt-6 sm:grid-cols-3 sm:gap-3 lg:grid-cols-4 lg:gap-4"
        role="region" aria-label="Uploaded File Previews">
        <!-- Previews will be loaded dynamically via JS -->
    </div>

    <!-- Return to collection button with tooltip - dimensioni ridotte su mobile -->
    <div class="flex justify-center mt-6">
        <button type="button" onclick="redirectToCollection()" id="returnToCollection"
            class="relative px-4 py-2 text-base font-semibold text-white bg-gray-700 rounded-full nft-button group hover:bg-gray-600 md:px-8 md:py-4 md:text-lg"
            aria-label="{{ trans('uploadmanager::uploadmanager.return_aria') }}">
            🔙 {{ trans('uploadmanager::uploadmanager.return_to_collection') }}
            <span
                class="absolute hidden w-48 px-2 py-1 text-xs text-center text-white transition-opacity duration-300 transform -translate-x-1/2 bg-gray-800 rounded opacity-0 pointer-events-none -top-12 left-1/2 group-hover:opacity-100 md:block">
                {{ trans('uploadmanager::uploadmanager.return_tooltip') }}
            </span>
        </button>
    </div>

    <!-- Scan progress with improved contrast -->
    <div class="mt-6 text-center">
        <p class="text-xs text-gray-200"><span id="scan-progress-text" role="status"></span></p>
    </div>

    <!-- Status showEmoji-->
    <div id="status" class="w-32 p-2 mx-auto mt-4 text-xs text-center text-gray-200" role="status"></div>

    <!-- Upload status -->
    <div id="upload-status" class="mt-5 text-center text-gray-200">
        <p id="status-message" class="text-xs" role="status">
            {{ trans('uploadmanager::uploadmanager.preparing_to_mint') }}</p>
    </div>
</div>

{{-- CSS personalizzato per thumbnail più piccole su mobile --}}
<style>
    /* Thumbnail piccole su mobile - sovrascrive gli stili del modulo upload */
    @media (max-width: 640px) {

        #collection .upload-preview,
        #collection .file-preview,
        #collection .preview-item,
        #collection img {
            max-width: 60px !important;
            max-height: 60px !important;
            width: 60px !important;
            height: 60px !important;
            object-fit: cover !important;
        }

        #collection .upload-preview {
            min-height: 60px !important;
            padding: 4px !important;
        }

        /* Riduci anche il testo nelle preview se presente */
        #collection .preview-text,
        #collection .file-name {
            font-size: 8px !important;
            line-height: 1.2 !important;
        }
    }
</style>

{{-- Feature Purchase Modal (Egili Living) --}}
<x-feature-purchase-modal featureCode="egi_living_subscription" />

@vite(['resources/js/components/create-collection-modal.js'])
@vite(['resources/ts/components/natan-batch-mint/NatanBatchMint.ts'])
