{{-- resources/views/egis/show.blade.php --}}
<x-guest-layout :title="$egi->title . ' | ' . $collection->collection_name" :metaDescription="Str::limit($egi->description, 155) ?? __('egi.meta_description_default', ['title' => $egi->title])">

    @php
        // Controllo se l'utente loggato è il creator dell'EGI
        $isCreator = App\Helpers\FegiAuth::check() && App\Helpers\FegiAuth::id() === $egi->user_id;
    @endphp

    {{-- Schema.org nel head --}}
    <x-slot name="schemaMarkup">
        @include('egis.partials.schema-markup', compact('egi', 'collection', 'isCreator'))
    </x-slot>

    {{-- Slot personalizzato per disabilitare la hero section --}}
    <x-slot name="noHero">true</x-slot>

    {{-- Contenuto principale --}}
    <x-slot name="slot">
        {{-- Business Logic: Calcolo variabili per EGI --}}
        @php
            // CORREZIONE: Sostituito auth() con App\Helpers\FegiAuth::
            $canUpdateEgi =
                App\Helpers\FegiAuth::check() &&
                App\Helpers\FegiAuth::user()->can('update_EGI') &&
                $collection
                    ->users()
                    ->where('user_id', App\Helpers\FegiAuth::id())
                    ->whereIn('role', ['admin', 'editor', 'creator'])
                    ->exists();

            $canDeleteEgi =
                App\Helpers\FegiAuth::check() &&
                App\Helpers\FegiAuth::user()->can('delete_EGI') &&
                $collection
                    ->users()
                    ->where('user_id', App\Helpers\FegiAuth::id())
                    ->whereIn('role', ['admin', 'creator'])
                    ->exists();

            // Inizializzazione delle variabili di prenotazione e prezzo
            // Ottengo la prenotazione con priorità più alta per questo EGI
            $reservationService = app('App\Services\ReservationService');
            $highestPriorityReservation = $reservationService->getHighestPriorityReservation($egi);

            // Determino il prezzo da mostrare
            $displayPrice = $egi->price; // Prezzo base di default
            $displayUser = null;
            $priceLabel = __('egi.current_price');

            // Se c'è una prenotazione attiva, uso il suo prezzo e utente
if ($highestPriorityReservation && $highestPriorityReservation->status === 'active') {
    // 🚀 DEBUG: Log per capire quale prenotazione viene selezionata
    \Log::info('EGI Show Debug', [
        'egi_id' => $egi->id,
        'reservation_id' => $highestPriorityReservation->id,
        'user_id' => $highestPriorityReservation->user_id,
        'offer_amount_fiat' => $highestPriorityReservation->offer_amount_fiat,
        'offer_amount_algo' => $highestPriorityReservation->offer_amount_algo,
        'is_current' => $highestPriorityReservation->is_current,
        'status' => $highestPriorityReservation->status,
        'created_at' => $highestPriorityReservation->created_at,
        'base_price' => $egi->price,
    ]);

    // 🔧 FIX: Proteggo da valori null o non numerici
    $fallbackPrice = $egi->price && is_numeric($egi->price) ? $egi->price * 0.3 : 0;
    $displayPrice = $highestPriorityReservation->offer_amount_fiat ?? $fallbackPrice;
    $displayUser = $highestPriorityReservation->user;

    // 🎯 EUR-ONLY SYSTEM: Sistema semplificato
    // - displayPrice = prezzo della prenotazione convertito in EUR
    // - Mostriamo sempre EUR con note per prenotazioni in altre valute

    // Convertiamo il prezzo della prenotazione in EUR se necessario
    if ($highestPriorityReservation->fiat_currency !== 'EUR') {
        // Per ora usiamo il prezzo EUR già convertito, in futuro potremo implementare conversione real-time
        $displayPrice = $highestPriorityReservation->amount_eur ?? $displayPrice;
    }

    // Label diversa per STRONG vs WEAK
    if ($highestPriorityReservation->type === 'weak') {
        $priceLabel = __('egi.reservation.fegi_reservation');
    } else {
        $priceLabel = __('egi.reservation.highest_bid');
    }
} else {
    // Se NON c'è prenotazione, usa il prezzo base dell'EGI (sempre in EUR)
                // Sistema semplificato: tutto in EUR
            }

            // 🔧 VALIDATION: Assicuro che displayPrice sia sempre un numero valido
            $displayPrice = is_numeric($displayPrice) ? (float) $displayPrice : 0;

            $isForSale = $displayPrice && $displayPrice > 0 && !$egi->mint;
            $canBeReserved =
                !$egi->mint &&
                ($egi->is_published ||
                    (App\Helpers\FegiAuth::check() && App\Helpers\FegiAuth::id() === $collection->creator_id)) &&
                $displayPrice &&
                $displayPrice > 0 &&
                !$isCreator;

            // 🔒 PRICE LOCK: Determina se il prezzo può essere modificato dal creator
            $canModifyPrice = $isCreator && !$highestPriorityReservation;
            $isPriceLocked = $isCreator && $highestPriorityReservation;
        @endphp

        {{-- Gallery Layout - Cinema Style con 3 Colonne --}}
        {{-- Background dinamico: gold/green per EGI mintati, standard per non mintati --}}
        <div
            class="{{ $egi->token_EGI
                ? 'bg-gradient-to-br from-amber-900/30 via-emerald-900/20 to-gray-900'
                : 'bg-gradient-to-br from-gray-900 via-black to-gray-900' }}">

            {{-- Background wrapper senza badge (li sposto nella sidebar) --}}
            <div class="container mx-auto px-0 py-0">

                {{-- Cinematic Artwork Display --}}
                <div class="relative w-full">

                    {{-- Main Gallery Grid - SEMPRE multi-colonna da tablet in su --}}
                    <div class="grid grid-cols-1 gap-0 md:grid-cols-12">

                        {{-- Left: Artwork Area - Ridotta quando CRUD visibile --}}
                        <div
                            class="@if ($canUpdateEgi) md:col-span-7 lg:col-span-4 xl:col-span-5 @else md:col-span-7 lg:col-span-6 xl:col-span-7 @endif relative p-2 md:p-3 lg:p-4 xl:p-6">

                            {{-- Artwork Container con Floating Card - Sempre visibile completamente --}}
                            <div class="relative mx-auto w-full max-w-full">

                                {{-- Collection Navigation Carousel - OpenSea Style --}}
                                <x-egi-collection-navigator :collectionEgis="$collectionEgis" :currentEgi="$egi" />

                                {{-- Main Image Display --}}
                                @include('egis.partials.artwork.main-image-display', compact('egi'))

                                {{-- Zoom Functionality --}}

                                {{-- Floating Title Card - ancorato ai piedi dell'immagine --}}
                                @include(
                                    'egis.partials.artwork.floating-title-card',
                                    compact('egi', 'collection', 'isCreator'))
                            </div>

                            {{-- Description sotto l'immagine (FUORI dal container relative) --}}
                            <div class="mt-3 md:mt-4">
                                @include('egis.partials.sidebar.description-section', compact('egi'))
                            </div>
                        </div>

                        {{-- Center: CRUD Box --}}
                        @include(
                            'egis.partials.sidebar.crud-panel',
                            compact(
                                'egi',
                                'canUpdateEgi',
                                'canDeleteEgi',
                                'isPriceLocked',
                                'displayPrice',
                                'displayUser',
                                'highestPriorityReservation'))

                        {{-- Right: Sidebar - Nascosta su tablet, visibile su laptop+ --}}
                        <div
                            class="overflow-y-auto border-t border-gray-700/50 bg-gray-900/95 backdrop-blur-xl md:hidden lg:col-span-3 lg:block lg:border-l lg:border-t-0 xl:col-span-3">

                            {{-- Sidebar Content - Padding compatto --}}
                            <div class="space-y-4 p-4 md:space-y-3 md:p-3 lg:space-y-4 lg:p-4 xl:space-y-5 xl:p-5">

                                {{-- Badge EGI Type e Mint in cima alla sidebar --}}
                                <div class="flex flex-wrap items-center gap-2 border-b border-gray-700/30 pb-3">
                                    {{-- EGI Type Badge --}}
                                    <x-egi-type-badge :type="$egi->egi_type ?? 'ASA'" size="md" />

                                    {{-- Badge MINTATO con link blockchain (solo se token_EGI presente) --}}
                                    @if ($egi->token_EGI)
                                        <a href="https://algoexplorer.io/asset/{{ $egi->token_EGI }}" target="_blank"
                                            rel="noopener noreferrer"
                                            class="inline-flex items-center space-x-1 rounded-full border border-amber-400/50 bg-gradient-to-r from-amber-500/20 to-emerald-500/20 px-2 py-1 shadow-md backdrop-blur-md transition-all hover:border-amber-400/70 hover:from-amber-500/30 hover:to-emerald-500/30 sm:space-x-1.5 sm:px-2.5 sm:py-1">
                                            <svg class="h-3 w-3 flex-shrink-0 animate-pulse text-amber-400 sm:h-3.5 sm:w-3.5"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                            </svg>
                                            <span
                                                class="whitespace-nowrap rounded bg-emerald-600/80 px-1.5 py-0.5 font-mono text-[9px] font-semibold text-white sm:text-[10px]">
                                                ASA #{{ $egi->token_EGI }}
                                            </span>
                                            <svg class="h-2.5 w-2.5 text-amber-300 sm:h-3 sm:w-3" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                        </a>
                                    @endif
                                </div>

                                {{-- Price & Purchase Section --}}
                                @include(
                                    'egis.partials.sidebar.price-purchase-section',
                                    compact(
                                        'egi',
                                        'isForSale',
                                        'displayPrice',
                                        'priceLabel',
                                        'displayUser',
                                        'highestPriorityReservation',
                                        'isCreator',
                                        'canBeReserved'))

                                {{-- Traits Section - SPOSTATO IN ALTO --}}
                                {{-- canManage = canUpdateEgi per consistency con traits-viewer component --}}
                                @php $canManage = $canUpdateEgi; @endphp
                                @include(
                                    'egis.partials.sidebar.traits-section',
                                    compact('egi', 'canManage'))

                                {{-- Utility Display Section --}}
                                @include('egis.partials.sidebar.utility-section', compact('egi'))

                                {{-- Mint/Rebind Blockchain History --}}
                                @include('egis.partials.sidebar.mint-history-section', compact('egi'))

                                {{-- Reservation History - PRIMA del CoA --}}
                                @include(
                                    'egis.partials.sidebar.reservation-history-section',
                                    compact('egi'))

                                {{-- CoA (Certificate of Authenticity) Section - Badge compatto se assente --}}
                                @include('egis.partials.sidebar.coa-section', compact('egi', 'isCreator'))

                                {{-- Collection Link --}}
                                @include(
                                    'egis.partials.sidebar.collection-link-section',
                                    compact('collection'))

                                {{-- Collection Collaborators --}}
                                @include(
                                    'egis.partials.sidebar.collection-collaborators-section',
                                    compact('collection'))

                                {{-- Component Utility Manager (solo per creator) --}}
                                @include('egis.partials.sidebar.utility-manager-section', compact('egi'))
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            {{-- Se utility presente e collection pubblicata, mostra solo in lettura --}}
            @if ($egi->utility && $egi->collection->status === 'published')
                <div class="mx-auto max-w-6xl">
                    {{-- TODO: Creare component utility-display per visualizzazione read-only --}}
                    {{-- <x-utility.utility-display :utility="$egi->utility" /> --}}
                </div>
            @endif

            {{-- Delete Confirmation Modal --}}
            @include('egis.partials.modals.delete-confirmation-modal', compact('canDeleteEgi', 'egi'))

            {{-- Custom Styles for Enhanced Interactivity --}}
            @include('egis.partials.styles.egi-show-styles')

            {{-- JavaScript per CRUD Interactions --}}
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const editStartBtn = document.getElementById('egi-edit-start');
                    const editToggleBtn = document.getElementById('egi-edit-toggle');
                    const editForm = document.getElementById('egi-edit-form');
                    const viewMode = document.getElementById('egi-view-mode');
                    const deleteBtn = document.getElementById('egi-delete-btn');
                    const deleteModal = document.getElementById('delete-modal');
                    const deleteCancel = document.getElementById('delete-cancel');

                    // Toggle edit mode
                    function toggleEditMode() {
                        const isEditing = editForm.style.display !== 'none';

                        if (isEditing) {
                            editForm.style.display = 'none';
                            viewMode.style.display = 'block';
                        } else {
                            editForm.style.display = 'block';
                            viewMode.style.display = 'none';
                        }
                    }

                    if (editStartBtn) {
                        editStartBtn.addEventListener('click', toggleEditMode);
                    }

                    if (editToggleBtn) {
                        editToggleBtn.addEventListener('click', toggleEditMode);
                    }

                    // Delete modal
                    if (deleteBtn && deleteModal) {
                        deleteBtn.addEventListener('click', function() {
                            deleteModal.classList.remove('hidden');
                            deleteModal.classList.add('flex');
                        });
                    }

                    if (deleteCancel) {
                        deleteCancel.addEventListener('click', function() {
                            deleteModal.classList.add('hidden');
                            deleteModal.classList.remove('flex');
                        });
                    }

                    // Close modal on background click
                    if (deleteModal) {
                        deleteModal.addEventListener('click', function(e) {
                            if (e.target === deleteModal) {
                                deleteModal.classList.add('hidden');
                                deleteModal.classList.remove('flex');
                            }
                        });
                    }

                    // Character counter for title
                    const titleInput = document.getElementById('title');
                    if (titleInput) {
                        titleInput.addEventListener('input', function() {
                            const remaining = 60 - this.value.length;
                            const hint = this.nextElementSibling;
                            if (hint) {
                                hint.textContent =
                                    `{{ __('egi.crud.title_hint') }} (${remaining} {{ __('egi.crud.characters_remaining') }})`;
                                hint.style.color = remaining < 10 ? '#fbbf24' : '#9ca3af';
                            }
                        });
                    }
                });
            </script>
            {{-- Lightbox Zoom Overlay - Responsive ottimizzato per schermi piccoli --}}
            <div id="zoom-overlay"
                class="fixed inset-0 z-50 hidden items-center justify-center bg-black/90 p-4 backdrop-blur-sm sm:p-6 md:p-8">
                <div id="zoom-content"
                    class="relative h-full max-h-[85vh] w-full max-w-[90vw] sm:max-h-[88vh] sm:max-w-[88vw] md:max-h-[85vh] md:max-w-[85vw]">
                    <img id="zoom-overlay-image" src="" alt=""
                        class="user-select-none h-full w-full touch-none object-contain" />
                    <button id="zoom-close" aria-label="Chiudi ingrandimento"
                        class="absolute right-1 top-1 z-10 flex h-9 w-9 items-center justify-center rounded-full bg-black/70 text-2xl font-bold text-white shadow-xl transition-all hover:scale-110 hover:bg-black/90 sm:right-2 sm:top-2 sm:h-10 sm:w-10 sm:text-3xl md:right-3 md:top-3 md:h-11 md:w-11">
                        ×
                    </button>
                </div>
            </div>

            {{-- Utility Details Modal - Responsive --}}
            @if ($egi->utility)
                <div id="utility-modal"
                    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 backdrop-blur-sm">
                    <div
                        class="relative mx-2 my-4 max-h-[95vh] w-full max-w-[95%] overflow-hidden sm:mx-4 sm:my-6 sm:max-h-[90vh] sm:max-w-3xl md:my-8 md:max-w-4xl">
                        {{-- Modal Content --}}
                        <div
                            class="rounded-xl border border-orange-500/30 bg-gradient-to-br from-gray-900 to-gray-800 shadow-2xl sm:rounded-2xl">
                            {{-- Modal Header - Responsive --}}
                            <div
                                class="flex items-center justify-between border-b border-orange-500/20 p-3 sm:p-4 md:p-6">
                                <div class="flex min-w-0 items-center space-x-2 sm:space-x-3">
                                    <div class="flex-shrink-0 rounded-md bg-orange-500/20 p-1.5 sm:rounded-lg sm:p-2">
                                        <svg class="h-4 w-4 text-orange-400 sm:h-5 sm:w-5 md:h-6 md:w-6" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <h2 class="truncate text-base font-bold text-white sm:text-lg md:text-xl">
                                            {{ $egi->utility->title }}</h2>
                                        <span
                                            class="mt-1 inline-block rounded-full border border-orange-400/30 bg-orange-500/20 px-2 py-0.5 text-[10px] font-medium text-white sm:px-3 sm:py-1 sm:text-xs">
                                            {{ __('utility.types.' . $egi->utility->type . '.label') }}
                                        </span>
                                    </div>
                                </div>
                                <button id="utility-modal-close"
                                    class="flex-shrink-0 p-1.5 text-gray-400 transition-colors hover:text-white sm:p-2">
                                    <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            {{-- Modal Body - Responsive --}}
                            <div
                                class="max-h-[calc(95vh-120px)] overflow-y-auto sm:max-h-[calc(90vh-140px)] md:max-h-[calc(90vh-180px)]">
                                <div class="grid grid-cols-1 gap-3 p-3 sm:gap-4 sm:p-4 md:gap-6 md:p-6 lg:grid-cols-2">
                                    {{-- Left Column: Images Carousel - Responsive --}}
                                    @if ($egi->utility->getMedia('utility_gallery')->count() > 0)
                                        <div class="space-y-3 sm:space-y-4">
                                            <h3 class="text-base font-semibold text-orange-400 sm:text-lg">
                                                {{ __('utility.media.title') }}</h3>

                                            {{-- Main Carousel Image - Responsive --}}
                                            <div class="relative">
                                                <div id="utility-carousel-container"
                                                    class="relative overflow-hidden rounded-lg bg-black/30 sm:rounded-xl">
                                                    <div id="utility-carousel-track"
                                                        class="flex transition-transform duration-300 ease-in-out">
                                                        @foreach ($egi->utility->getMedia('utility_gallery') as $index => $media)
                                                            <div class="w-full flex-shrink-0">
                                                                <img src="{{ $media->getUrl() }}"
                                                                    alt="Utility image {{ $index + 1 }}"
                                                                    class="h-48 w-full object-cover sm:h-56 md:h-64 lg:h-80">
                                                            </div>
                                                        @endforeach
                                                    </div>

                                                    {{-- Carousel Controls --}}
                                                    @if ($egi->utility->getMedia('utility_gallery')->count() > 1)
                                                        <button id="utility-carousel-prev"
                                                            class="absolute left-4 top-1/2 -translate-y-1/2 transform rounded-full bg-black/50 p-2 text-white transition-colors hover:bg-black/70">
                                                            <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M15 19l-7-7 7-7" />
                                                            </svg>
                                                        </button>
                                                        <button id="utility-carousel-next"
                                                            class="absolute right-4 top-1/2 -translate-y-1/2 transform rounded-full bg-black/50 p-2 text-white transition-colors hover:bg-black/70">
                                                            <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M9 5l7 7-7 7" />
                                                            </svg>
                                                        </button>
                                                    @endif
                                                </div>

                                                {{-- Carousel Indicators --}}
                                                @if ($egi->utility->getMedia('utility_gallery')->count() > 1)
                                                    <div class="mt-4 flex justify-center space-x-2">
                                                        @foreach ($egi->utility->getMedia('utility_gallery') as $index => $media)
                                                            <button
                                                                class="utility-carousel-indicator {{ $index === 0 ? 'bg-orange-500' : 'bg-gray-500 hover:bg-orange-400' }} h-2 w-2 rounded-full transition-colors"
                                                                data-slide="{{ $index }}"></button>
                                                        @endforeach
                                                    </div>
                                                @endif

                                                {{-- Auto-play Toggle --}}
                                                @if ($egi->utility->getMedia('utility_gallery')->count() > 1)
                                                    <div class="mt-3 flex justify-center">
                                                        <button id="utility-carousel-autoplay"
                                                            class="flex items-center space-x-2 rounded-lg bg-orange-500/20 px-3 py-1 text-orange-300 transition-colors hover:bg-orange-500/30">
                                                            <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h1m4 0h1m6-7a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                            <span class="text-xs">Auto-play</span>
                                                        </button>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Right Column: Utility Details - Responsive --}}
                                    <div class="space-y-4 sm:space-y-6">
                                        {{-- Description --}}
                                        <div>
                                            <h3
                                                class="mb-2 text-base font-semibold text-orange-400 sm:mb-3 sm:text-lg">
                                                {{ __('utility.fields.description') }}</h3>
                                            <p class="text-sm leading-relaxed text-gray-300 sm:text-base">
                                                {{ $egi->utility->description }}</p>
                                        </div>

                                        {{-- Type-specific Details --}}
                                        @if ($egi->utility->type === 'physical')
                                            {{-- Physical Item Details --}}
                                            <div class="rounded-lg border border-blue-500/20 bg-blue-500/10 p-4">
                                                <h4 class="mb-3 font-semibold text-blue-400">
                                                    {{ __('utility.shipping.title') }}</h4>
                                                <div class="grid grid-cols-2 gap-4 text-sm">
                                                    @if ($egi->utility->weight)
                                                        <div>
                                                            <span
                                                                class="text-gray-400">{{ __('utility.shipping.weight') }}:</span>
                                                            <span class="text-white">{{ $egi->utility->weight }}
                                                                kg</span>
                                                        </div>
                                                    @endif
                                                    @if ($egi->utility->dimensions_length || $egi->utility->dimensions_width || $egi->utility->dimensions_height)
                                                        <div>
                                                            <span
                                                                class="text-gray-400">{{ __('utility.shipping.dimensions') }}:</span>
                                                            <span
                                                                class="text-white">{{ $egi->utility->dimensions_length }}x{{ $egi->utility->dimensions_width }}x{{ $egi->utility->dimensions_height }}
                                                                cm</span>
                                                        </div>
                                                    @endif
                                                    @if ($egi->utility->shipping_days)
                                                        <div>
                                                            <span
                                                                class="text-gray-400">{{ __('utility.shipping.days') }}:</span>
                                                            <span
                                                                class="text-white">{{ $egi->utility->shipping_days }}
                                                                giorni</span>
                                                        </div>
                                                    @endif
                                                    @if ($egi->utility->is_fragile)
                                                        <div class="col-span-2">
                                                            <span
                                                                class="inline-flex items-center rounded-lg bg-yellow-500/20 px-2 py-1 text-xs text-yellow-300">
                                                                <svg class="mr-1 h-3 w-3" fill="currentColor"
                                                                    viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd"
                                                                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                                        clip-rule="evenodd" />
                                                                </svg>
                                                                {{ __('utility.shipping.fragile') }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>
                                                @if ($egi->utility->shipping_notes)
                                                    <div class="mt-3">
                                                        <span
                                                            class="text-sm text-gray-400">{{ __('utility.shipping.notes') }}:</span>
                                                        <p class="mt-1 text-sm text-white">
                                                            {{ $egi->utility->shipping_notes }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        @elseif($egi->utility->type === 'service')
                                            {{-- Service Details --}}
                                            <div class="rounded-lg border border-green-500/20 bg-green-500/10 p-4">
                                                <h4 class="mb-3 font-semibold text-green-400">
                                                    {{ __('utility.service.title') }}</h4>
                                                <div class="space-y-3 text-sm">
                                                    @if ($egi->utility->valid_from || $egi->utility->valid_until)
                                                        <div>
                                                            <span class="text-gray-400">Validità:</span>
                                                            <span class="text-white">
                                                                @if ($egi->utility->valid_from)
                                                                    Dal
                                                                    {{ $egi->utility->valid_from->format('d/m/Y') }}
                                                                @endif
                                                                @if ($egi->utility->valid_until)
                                                                    al
                                                                    {{ $egi->utility->valid_until->format('d/m/Y') }}
                                                                @endif
                                                            </span>
                                                        </div>
                                                    @endif
                                                    @if ($egi->utility->max_uses)
                                                        <div>
                                                            <span
                                                                class="text-gray-400">{{ __('utility.service.max_uses') }}:</span>
                                                            <span
                                                                class="text-white">{{ $egi->utility->max_uses }}</span>
                                                        </div>
                                                    @endif
                                                    @if ($egi->utility->service_instructions)
                                                        <div>
                                                            <span
                                                                class="text-gray-400">{{ __('utility.service.instructions') }}:</span>
                                                            <p class="mt-1 text-white">
                                                                {{ $egi->utility->service_instructions }}</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @elseif($egi->utility->type === 'hybrid')
                                            {{-- Hybrid: Both Physical and Service --}}
                                            <div class="space-y-4">
                                                {{-- Physical Part --}}
                                                <div class="rounded-lg border border-blue-500/20 bg-blue-500/10 p-4">
                                                    <h4 class="mb-3 font-semibold text-blue-400">Componente Fisico</h4>
                                                    <div class="grid grid-cols-2 gap-4 text-sm">
                                                        @if ($egi->utility->weight)
                                                            <div>
                                                                <span
                                                                    class="text-gray-400">{{ __('utility.shipping.weight') }}:</span>
                                                                <span class="text-white">{{ $egi->utility->weight }}
                                                                    kg</span>
                                                            </div>
                                                        @endif
                                                        @if ($egi->utility->shipping_days)
                                                            <div>
                                                                <span
                                                                    class="text-gray-400">{{ __('utility.shipping.days') }}:</span>
                                                                <span
                                                                    class="text-white">{{ $egi->utility->shipping_days }}
                                                                    giorni</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                {{-- Service Part --}}
                                                <div class="rounded-lg border border-green-500/20 bg-green-500/10 p-4">
                                                    <h4 class="mb-3 font-semibold text-green-400">Componente Servizio
                                                    </h4>
                                                    <div class="text-sm">
                                                        @if ($egi->utility->service_instructions)
                                                            <p class="text-white">
                                                                {{ $egi->utility->service_instructions }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif($egi->utility->type === 'digital')
                                            {{-- Digital Content --}}
                                            <div class="rounded-lg border border-purple-500/20 bg-purple-500/10 p-4">
                                                <h4 class="mb-3 font-semibold text-purple-400">Contenuto Digitale</h4>
                                                <div class="space-y-3 text-sm">
                                                    @if ($egi->utility->valid_from || $egi->utility->valid_until)
                                                        <div>
                                                            <span class="text-gray-400">Accesso valido:</span>
                                                            <span class="text-white">
                                                                @if ($egi->utility->valid_from)
                                                                    Dal
                                                                    {{ $egi->utility->valid_from->format('d/m/Y') }}
                                                                @endif
                                                                @if ($egi->utility->valid_until)
                                                                    al
                                                                    {{ $egi->utility->valid_until->format('d/m/Y') }}
                                                                @endif
                                                            </span>
                                                        </div>
                                                    @endif
                                                    @if ($egi->utility->service_instructions)
                                                        <div>
                                                            <span class="text-gray-400">Istruzioni di accesso:</span>
                                                            <p class="mt-1 text-white">
                                                                {{ $egi->utility->service_instructions }}</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Escrow Information --}}
                                        <div class="rounded-lg border border-gray-600/30 bg-gray-700/30 p-4">
                                            <h4 class="mb-3 font-semibold text-gray-300">
                                                {{ __('utility.escrow.' . $egi->utility->escrow_tier . '.label') }}
                                            </h4>
                                            <p class="text-sm text-gray-400">
                                                {{ __('utility.escrow.' . $egi->utility->escrow_tier . '.description') }}
                                            </p>
                                            @if ($egi->utility->escrow_tier !== 'immediate')
                                                <div class="mt-2 space-y-1">
                                                    <div class="flex items-center text-xs text-gray-400">
                                                        <svg class="mr-1 h-3 w-3 text-green-400" fill="currentColor"
                                                            viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd"
                                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                        {{ __('utility.escrow.' . $egi->utility->escrow_tier . '.requirement_tracking') }}
                                                    </div>
                                                    @if ($egi->utility->escrow_tier === 'premium')
                                                        <div class="flex items-center text-xs text-gray-400">
                                                            <svg class="mr-1 h-3 w-3 text-green-400"
                                                                fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd"
                                                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                                    clip-rule="evenodd" />
                                                            </svg>
                                                            {{ __('utility.escrow.' . $egi->utility->escrow_tier . '.requirement_signature') }}
                                                        </div>
                                                        <div class="flex items-center text-xs text-gray-400">
                                                            <svg class="mr-1 h-3 w-3 text-green-400"
                                                                fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd"
                                                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                                    clip-rule="evenodd" />
                                                            </svg>
                                                            {{ __('utility.escrow.' . $egi->utility->escrow_tier . '.requirement_insurance') }}
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
    </x-slot>



    <style>
        /* Prevenire selezione del testo durante il pan */
        .touch-none {
            touch-action: none;
            user-select: none;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
        }

        /* Migliorare l'overlay */
        #zoom-overlay {
            backdrop-filter: blur(4px);
        }

        /* Cursor style per indicare zoom disponibile */
        #zoom-image-trigger:hover {
            cursor: zoom-in;
        }

        /* Smooth transitions */
        #zoom-overlay {
            transition: opacity 0.2s ease-in-out;
        }

        #zoom-overlay.hidden {
            opacity: 0;
            pointer-events: none;
        }

        #zoom-overlay:not(.hidden) {
            opacity: 1;
            pointer-events: all;
        }

        /* Utility Modal Styles */
        #utility-modal {
            transition: opacity 0.3s ease-in-out;
        }

        #utility-modal.hidden {
            opacity: 0;
            pointer-events: none;
        }

        #utility-modal:not(.hidden) {
            opacity: 1;
            pointer-events: all;
        }

        /* Utility Carousel Styles */
        .utility-carousel-indicator {
            transition: all 0.3s ease;
        }

        .utility-carousel-indicator:hover {
            transform: scale(1.2);
        }

        /* Line clamp utility */
        .line-clamp-2 {
            overflow: hidden;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
        }

        /* Smooth scrolling for modal */
        #utility-modal .overflow-y-auto {
            scrollbar-width: thin;
            scrollbar-color: rgba(249, 115, 22, 0.3) transparent;
        }

        #utility-modal .overflow-y-auto::-webkit-scrollbar {
            width: 6px;
        }

        #utility-modal .overflow-y-auto::-webkit-scrollbar-track {
            background: transparent;
        }

        #utility-modal .overflow-y-auto::-webkit-scrollbar-thumb {
            background: rgba(249, 115, 22, 0.3);
            border-radius: 3px;
        }

        #utility-modal .overflow-y-auto::-webkit-scrollbar-thumb:hover {
            background: rgba(249, 115, 22, 0.5);
        }
    </style>

</x-guest-layout>

{{-- SOSTITUISCI questa riga alla fine del file show.blade.php --}}
{{-- DA: @vite(['resources/ts/zoom.ts']) --}}
{{-- A: Script inline JavaScript --}}

{{-- JavaScript Zoom Implementation - OS2.0 Compliant --}}
<script>
    /**
     * @Oracode ImageZoom: Timing-Fixed Implementation
     * 🎯 Purpose: Robust image zoom with element waiting mechanism
     * 🛡️ Security: Error handling and element availability checking
     * 🧱 Core Logic: Waits for all elements before initialization
     *
     * @package FlorenceEGI\Frontend\Zoom
     * @author Padmin D. Curtis (AI Partner OS2.0-Compliant) for Fabio Cherici
     * @version 1.1.0 (FlorenceEGI MVP Zoom - Timing Fixed)
     * @date 2025-06-30
     */

    class ImageZoom {
        constructor(triggerId) {
            this.triggerId = triggerId;
            this.maxRetries = 50; // 5 seconds max wait
            this.retryCount = 0;

            // Zoom state
            this.scale = 1;
            this.panX = 0;
            this.panY = 0;
            this.startX = 0;
            this.startY = 0;
            this.isPanning = false;
            this.startDistance = 0;
            this.isZoomOpen = false;

            // Bind methods to preserve context
            this.handleWheel = this.handleWheel.bind(this);
            this.handlePointerDown = this.handlePointerDown.bind(this);
            this.handlePointerMove = this.handlePointerMove.bind(this);
            this.handlePointerUp = this.handlePointerUp.bind(this);
            this.handleTouchStart = this.handleTouchStart.bind(this);
            this.handleTouchMove = this.handleTouchMove.bind(this);
            this.handleTouchEnd = this.handleTouchEnd.bind(this);

            // Start waiting for elements
            this.waitForElements();
        }

        waitForElements() {
            console.log(`🔍 ZOOM: Waiting for elements... (attempt ${this.retryCount + 1}/${this.maxRetries})`);

            // Try to find all required elements
            this.trigger = document.getElementById(this.triggerId);
            this.overlay = document.getElementById('zoom-overlay');
            this.overlayImage = document.getElementById('zoom-overlay-image');
            this.closeButton = document.getElementById('zoom-close');

            const elementsFound = {
                trigger: !!this.trigger,
                overlay: !!this.overlay,
                overlayImage: !!this.overlayImage,
                closeButton: !!this.closeButton
            };

            console.log('🔍 ZOOM: Elements status:', elementsFound);

            // Check if all elements are available
            const allElementsReady = this.trigger && this.overlay && this.overlayImage && this.closeButton;

            if (allElementsReady) {
                console.log('✅ ZOOM: All elements found! Initializing...');
                this.bindEvents();
            } else {
                this.retryCount++;

                if (this.retryCount >= this.maxRetries) {
                    console.error('❌ ZOOM: Failed to find all elements after maximum retries:', elementsFound);
                    return;
                }

                // Wait 100ms and try again
                setTimeout(() => this.waitForElements(), 100);
            }
        }

        bindEvents() {
            try {
                console.log('🔗 ZOOM: Binding events...');

                // Trigger click to open zoom
                this.trigger.addEventListener('click', (e) => {
                    console.log('🎯 ZOOM: Image clicked!');
                    e.preventDefault();
                    e.stopPropagation();
                    this.open();
                });

                // Close zoom
                this.closeButton.addEventListener('click', (e) => {
                    console.log('🎯 ZOOM: Close button clicked');
                    e.preventDefault();
                    e.stopPropagation();
                    this.close();
                });

                this.overlay.addEventListener('click', (e) => {
                    if (e.target === this.overlay) {
                        console.log('🎯 ZOOM: Overlay background clicked');
                        this.close();
                    }
                });

                // Escape key to close
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && this.isZoomOpen) {
                        console.log('🎯 ZOOM: Escape key pressed');
                        this.close();
                    }
                });

                // Desktop wheel zoom
                this.overlayImage.addEventListener('wheel', this.handleWheel, {
                    passive: false
                });

                // Pointer events for pan
                this.overlayImage.addEventListener('pointerdown', this.handlePointerDown);
                this.overlayImage.addEventListener('pointermove', this.handlePointerMove);
                this.overlayImage.addEventListener('pointerup', this.handlePointerUp);
                this.overlayImage.addEventListener('pointercancel', this.handlePointerUp);

                // Touch events for pinch-to-zoom
                this.overlayImage.addEventListener('touchstart', this.handleTouchStart, {
                    passive: false
                });
                this.overlayImage.addEventListener('touchmove', this.handleTouchMove, {
                    passive: false
                });
                this.overlayImage.addEventListener('touchend', this.handleTouchEnd);
                this.overlayImage.addEventListener('touchcancel', this.handleTouchEnd);

                console.log('✅ ZOOM: All events bound successfully!');

            } catch (error) {
                console.error('❌ ZOOM: Error binding events', error);
            }
        }

        open() {
            try {
                console.log('🚀 ZOOM: Opening zoom...');

                // Get image source with multiple fallbacks
                const src = this.trigger.dataset.zoomSrc ||
                    this.trigger.dataset.src ||
                    this.trigger.src ||
                    this.trigger.getAttribute('src');

                console.log('🔍 ZOOM: Image source:', src);

                if (!src) {
                    console.error('❌ ZOOM: No valid image source found');
                    return;
                }

                // Set overlay image source
                this.overlayImage.src = src;

                // Show overlay
                this.overlay.classList.remove('hidden');
                this.overlay.style.display = 'flex';

                this.isZoomOpen = true;

                // Prevent body scroll
                document.body.style.overflow = 'hidden';

                this.reset();

                console.log('✅ ZOOM: Zoom opened successfully!');
            } catch (error) {
                console.error('❌ ZOOM: Error opening zoom', error);
            }
        }

        close() {
            try {
                console.log('🔒 ZOOM: Closing zoom...');

                this.overlay.classList.add('hidden');
                this.overlay.style.display = 'none';
                this.isZoomOpen = false;

                // Restore body scroll
                document.body.style.overflow = '';

                this.reset();

                console.log('✅ ZOOM: Zoom closed successfully');
            } catch (error) {
                console.error('❌ ZOOM: Error closing zoom', error);
            }
        }

        reset() {
            this.scale = 0.25; // 🔧 FIX: Zoom iniziale al 25% (era 1 = 100%)
            this.panX = 0;
            this.panY = 0;
            this.isPanning = false;
            this.updateTransform();
        }

        handleWheel(e) {
            if (!this.isZoomOpen) return;

            try {
                e.preventDefault();

                const delta = -e.deltaY * 0.002;
                const newScale = Math.min(Math.max(0.1, this.scale + delta), 2.5); // 🔧 FIX: min 10%, max 250%

                const rect = this.overlayImage.getBoundingClientRect();
                const centerX = (e.clientX - rect.left) / rect.width;
                const centerY = (e.clientY - rect.top) / rect.height;

                if (newScale !== this.scale) {
                    const scaleDiff = newScale - this.scale;
                    this.panX -= (centerX - 0.5) * rect.width * scaleDiff * 0.5;
                    this.panY -= (centerY - 0.5) * rect.height * scaleDiff * 0.5;
                }

                this.scale = newScale;
                this.updateTransform();
            } catch (error) {
                console.error('❌ ZOOM: Error handling wheel', error);
            }
        }

        handlePointerDown(e) {
            if (!this.isZoomOpen) return;

            try {
                e.preventDefault();
                this.isPanning = true;
                this.startX = e.clientX - this.panX;
                this.startY = e.clientY - this.panY;

                if (this.overlayImage.setPointerCapture) {
                    this.overlayImage.setPointerCapture(e.pointerId);
                }
            } catch (error) {
                console.error('❌ ZOOM: Error handling pointer down', error);
            }
        }

        handlePointerMove(e) {
            if (!this.isPanning || !this.isZoomOpen) return;

            try {
                this.panX = e.clientX - this.startX;
                this.panY = e.clientY - this.startY;
                this.updateTransform();
            } catch (error) {
                console.error('❌ ZOOM: Error handling pointer move', error);
            }
        }

        handlePointerUp() {
            this.isPanning = false;
        }

        handleTouchStart(e) {
            if (!this.isZoomOpen) return;

            try {
                if (e.touches.length === 2) {
                    e.preventDefault();
                    const [t1, t2] = Array.from(e.touches);
                    this.startDistance = this.getDistance(t1, t2);
                }
            } catch (error) {
                console.error('❌ ZOOM: Error handling touch start', error);
            }
        }

        handleTouchMove(e) {
            if (!this.isZoomOpen) return;

            try {
                if (e.touches.length === 2) {
                    e.preventDefault();
                    const [t1, t2] = Array.from(e.touches);
                    const newDistance = this.getDistance(t1, t2);

                    if (this.startDistance > 0) {
                        const factor = newDistance / this.startDistance;
                        this.scale = Math.min(Math.max(0.1, this.scale * factor), 2.5); // 🔧 FIX: min 10%, max 250%
                        this.startDistance = newDistance;
                        this.updateTransform();
                    }
                }
            } catch (error) {
                console.error('❌ ZOOM: Error handling touch move', error);
            }
        }

        handleTouchEnd() {
            this.startDistance = 0;
        }

        getDistance(t1, t2) {
            return Math.hypot(t2.clientX - t1.clientX, t2.clientY - t1.clientY);
        }

        updateTransform() {
            try {
                this.overlayImage.style.transform =
                    `translate(${this.panX}px, ${this.panY}px) scale(${this.scale})`;
            } catch (error) {
                console.error('❌ ZOOM: Error updating transform', error);
            }
        }
    }

    // Multiple initialization strategies for maximum compatibility
    function initializeZoom() {
        console.log('🚀 ZOOM: Attempting to initialize ImageZoom...');
        try {
            new ImageZoom('zoom-image-trigger');
        } catch (error) {
            console.error('❌ ZOOM: Failed to initialize', error);
        }
    }

    // Strategy 1: DOMContentLoaded
    document.addEventListener('DOMContentLoaded', initializeZoom);

    // Strategy 2: Immediate if DOM is ready
    if (document.readyState !== 'loading') {
        initializeZoom();
    }

    // Strategy 3: Window load as final fallback
    window.addEventListener('load', () => {
        console.log('🔄 ZOOM: Window load event - final initialization attempt');
        setTimeout(initializeZoom, 100);
    });

    console.log('📝 ZOOM: Script loaded successfully');
</script>

{{-- Utility Modal JavaScript --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Modal elements
        const utilityModal = document.getElementById('utility-modal');
        const utilityModalTrigger = document.getElementById('utility-modal-trigger');
        const utilityModalClose = document.getElementById('utility-modal-close');

        // Carousel elements
        const carouselTrack = document.getElementById('utility-carousel-track');
        const carouselPrev = document.getElementById('utility-carousel-prev');
        const carouselNext = document.getElementById('utility-carousel-next');
        const carouselAutoplay = document.getElementById('utility-carousel-autoplay');
        const carouselIndicators = document.querySelectorAll('.utility-carousel-indicator');

        let currentSlide = 0;
        let totalSlides = carouselIndicators.length;
        let autoplayInterval = null;
        let isAutoplayActive = false;

        // Modal functions
        function openModal() {
            utilityModal.classList.remove('hidden');
            utilityModal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            utilityModal.classList.add('hidden');
            utilityModal.classList.remove('flex');
            document.body.style.overflow = '';
            stopAutoplay();
        }

        // Carousel functions
        function updateCarousel() {
            if (carouselTrack && totalSlides > 0) {
                const translateX = -currentSlide * 100;
                carouselTrack.style.transform = `translateX(${translateX}%)`;

                // Update indicators
                carouselIndicators.forEach((indicator, index) => {
                    if (index === currentSlide) {
                        indicator.classList.remove('bg-gray-500');
                        indicator.classList.add('bg-orange-500');
                    } else {
                        indicator.classList.remove('bg-orange-500');
                        indicator.classList.add('bg-gray-500');
                    }
                });
            }
        }

        function nextSlide() {
            currentSlide = (currentSlide + 1) % totalSlides;
            updateCarousel();
        }

        function prevSlide() {
            currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
            updateCarousel();
        }

        function goToSlide(slideIndex) {
            currentSlide = slideIndex;
            updateCarousel();
        }

        function startAutoplay() {
            if (totalSlides > 1) {
                autoplayInterval = setInterval(nextSlide, 4000); // 4 seconds
                isAutoplayActive = true;
                if (carouselAutoplay) {
                    carouselAutoplay.classList.add('bg-orange-500/30', 'text-orange-200');
                    carouselAutoplay.classList.remove('bg-orange-500/20', 'text-orange-300');
                }
            }
        }

        function stopAutoplay() {
            if (autoplayInterval) {
                clearInterval(autoplayInterval);
                autoplayInterval = null;
                isAutoplayActive = false;
                if (carouselAutoplay) {
                    carouselAutoplay.classList.remove('bg-orange-500/30', 'text-orange-200');
                    carouselAutoplay.classList.add('bg-orange-500/20', 'text-orange-300');
                }
            }
        }

        function toggleAutoplay() {
            if (isAutoplayActive) {
                stopAutoplay();
            } else {
                startAutoplay();
            }
        }

        // Event listeners
        if (utilityModalTrigger) {
            utilityModalTrigger.addEventListener('click', openModal);
        }

        if (utilityModalClose) {
            utilityModalClose.addEventListener('click', closeModal);
        }

        // Close modal on background click
        if (utilityModal) {
            utilityModal.addEventListener('click', function(e) {
                if (e.target === utilityModal) {
                    closeModal();
                }
            });
        }

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !utilityModal.classList.contains('hidden')) {
                closeModal();
            }
        });

        // Carousel controls
        if (carouselNext) {
            carouselNext.addEventListener('click', function() {
                stopAutoplay(); // Stop autoplay when user manually navigates
                nextSlide();
            });
        }

        if (carouselPrev) {
            carouselPrev.addEventListener('click', function() {
                stopAutoplay(); // Stop autoplay when user manually navigates
                prevSlide();
            });
        }

        // Carousel indicators
        carouselIndicators.forEach((indicator, index) => {
            indicator.addEventListener('click', function() {
                stopAutoplay(); // Stop autoplay when user manually navigates
                goToSlide(index);
            });
        });

        // Autoplay toggle
        if (carouselAutoplay) {
            carouselAutoplay.addEventListener('click', toggleAutoplay);
        }

        // Touch/swipe support for mobile
        if (carouselTrack) {
            let startX = 0;
            let endX = 0;
            let isDragging = false;

            carouselTrack.addEventListener('touchstart', function(e) {
                startX = e.touches[0].clientX;
                isDragging = true;
            });

            carouselTrack.addEventListener('touchmove', function(e) {
                if (!isDragging) return;
                endX = e.touches[0].clientX;
            });

            carouselTrack.addEventListener('touchend', function() {
                if (!isDragging) return;
                isDragging = false;

                const diff = startX - endX;
                const threshold = 50; // Minimum swipe distance

                if (Math.abs(diff) > threshold) {
                    stopAutoplay(); // Stop autoplay on swipe
                    if (diff > 0) {
                        nextSlide(); // Swipe left - next slide
                    } else {
                        prevSlide(); // Swipe right - prev slide
                    }
                }
            });

            // Prevent default touch behavior
            carouselTrack.addEventListener('touchmove', function(e) {
                e.preventDefault();
            }, {
                passive: false
            });
        }

        // Initialize carousel
        if (totalSlides > 0) {
            updateCarousel();

            // Start autoplay if there are multiple slides
            if (totalSlides > 1) {
                // Start autoplay when modal opens
                if (utilityModalTrigger) {
                    utilityModalTrigger.addEventListener('click', function() {
                        setTimeout(startAutoplay, 500); // Small delay to let modal open
                    });
                }
            }
        }
    });
</script>

{{-- Debug Script for Vocabulary Modal --}}
<script>
    console.log("🔍 DEBUG: Inline script loading...");
    console.log("🔍 DEBUG: VocabularyModalController at page load:", window.VocabularyModalController);

    // Check if script files are loading
    document.addEventListener('DOMContentLoaded', function() {
        console.log("🔍 DEBUG: DOM loaded, checking VocabularyModalController:", window
            .VocabularyModalController);

        // Also check after a delay
        setTimeout(function() {
            console.log("🔍 DEBUG: After 2s, VocabularyModalController:", window
                .VocabularyModalController);
            console.log("🔍 DEBUG: Available window properties with 'vocabulary':", Object.keys(window)
                .filter(k => k.toLowerCase().includes('vocabulary')));

            // Try to manually load the script if it's not loaded
            if (!window.VocabularyModalController) {
                console.log("🔧 DEBUG: Attempting manual script load...");
                const script = document.createElement('script');
                script.src = '/build/assets/vocabulary-modal-DfF2CEeZ.js';
                script.onload = function() {
                    console.log("✅ DEBUG: Manual script loaded successfully");
                    console.log("🔍 DEBUG: VocabularyModalController after manual load:", window
                        .VocabularyModalController);
                };
                script.onerror = function() {
                    console.error("❌ DEBUG: Manual script load failed");
                };
                document.head.appendChild(script);
            }
        }, 2000);
    });
</script>

{{-- Direct Vite inclusion --}}
@vite('resources/js/coa/vocabulary-modal.js')

{{-- Art Advisor JavaScript - Load before component --}}
<script src="{{ asset('js/art-advisor.js') }}"></script>

@push('scripts')
    {{-- Also try in push for redundancy --}}
    @vite('resources/js/coa/vocabulary-modal.js')
@endpush

{{-- AI Art Advisor Modal Component --}}
@if ($isCreator)
    <x-art-advisor-modal :context="[
        'egi_id' => $egi->id,
        'egi_number' => $egi->egi_number,
        'title' => $egi->title,
        'current_description' => $egi->description,
        'creation_date' => $egi->creation_date?->format('Y-m-d'),
        'price_eur' => $egi->price,
        'has_image' => !empty($egi->image_url),
        'image_url' => $egi->image_url,
        'collection_name' => $collection->collection_name,
        'collection_type' => $collection->type,
        'existing_traits' => $egi->traits->mapWithKeys(fn($t) => [$t->category => $t->display_value])->toArray(),
        'traits_count' => $egi->traits->count(),
        'has_reservations' => $egi->reservations()->count() > 0,
        'is_minted' => !is_null($egi->token_EGI),
    ]" :mode="'general'" :auto-open="false" />
@endif
