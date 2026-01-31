{{-- resources/views/egis/partials/sidebar/crud-panel.blade.php --}}
{{--
    Pannello CRUD per editing EGI
    ORIGINE: righe 129-381 di show.blade.php

    VARIABILI RUOLI (tre ruoli inscindibili):
    - $isCreator: vero autore dell'opera (user_id)
    - $isCoCreator: chi ha mintato l'EGI (co_creator_id, immutabile dopo mint)
    - $isOwner: proprietario commerciale (owner_id, variabile con vendite)
    - $isMinted: EGI già certificato su blockchain (token_EGI NOT NULL)

    VARIABILI PERMESSI GRANULARI:
    - $canEditMetadata: può modificare titolo, descrizione, traits (Creator + non mintato)
    - $canManageCoA: può gestire Certificate of Authenticity (sempre Creator, per legge)
    - $canManagePrice: può gestire prezzo/vendita (Creator se non mintato, Owner se mintato)
    - $canUpdateEgi: almeno un permesso sopra
    - $canDeleteEgi: può eliminare EGI

    ALTRE VARIABILI:
    - $isPriceLocked, $displayPrice, $displayUser, $highestPriorityReservation
    - $canSellEgis: passata da show.blade.php, indica se la collection può vendere
--}}

{{-- Col 2: CRUD Box Content --}}
<div class="h-full overflow-y-auto bg-gradient-to-b from-emerald-900/20 to-emerald-900/10 backdrop-blur-xl">
    {{-- CRUD Box Content - Padding ottimizzato --}}
    <div class="p-3 md:p-2 lg:p-3 xl:p-4">
        <div
            class="rounded-lg border border-emerald-700/30 bg-gradient-to-br from-emerald-800/20 to-emerald-900/20 p-2.5 md:p-2 lg:p-2.5 xl:p-3">

            {{-- Header - Ultra-compatto --}}
            {{-- DEBUG: canEditMetadata={{ $canEditMetadata ?? 'undefined' }}, isCreator={{ $isCreator ?? 'undefined' }}, isMinted={{ $isMinted ?? 'undefined' }} --}}
            <div class="mb-2 flex items-center justify-between md:mb-1.5 lg:mb-2">
                <h3 class="text-xs font-semibold text-emerald-400 md:text-[10px] lg:text-xs xl:text-sm">
                    <svg class="mr-1.5 inline h-4 w-4 md:h-3.5 md:w-3.5 lg:h-4 lg:w-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                        </path>
                    </svg>
                    {{ __('egi.crud.edit_egi') }}
                    @if (!($canEditMetadata ?? true))
                        <svg class="ml-1 inline h-3.5 w-3.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"
                            title="{{ __('egi.crud.metadata_locked_creator_hint') }}">
                            <path fill-rule="evenodd"
                                d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                clip-rule="evenodd" />
                        </svg>
                    @endif
                </h3>
                @if ($canEditMetadata ?? true)
                    <button id="egi-edit-toggle"
                        class="rounded-full p-1.5 text-emerald-400 transition-colors duration-200 hover:bg-emerald-800/30 md:p-1 lg:p-1.5"
                        title="{{ __('egi.crud.toggle_edit_mode') }}">
                        <svg class="h-3.5 w-3.5 md:h-3 md:w-3 lg:h-3.5 lg:w-3.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                            </path>
                        </svg>
                    </button>
                @else
                    {{-- Pulsante disabilitato con lucchetto per Creator su EGI mintato --}}
                    <div class="cursor-not-allowed rounded-full p-1.5 text-amber-400/60 md:p-1 lg:p-1.5"
                        title="{{ __('egi.crud.metadata_locked_creator_hint') }}">
                        <svg class="h-3.5 w-3.5 md:h-3 md:w-3 lg:h-3.5 lg:w-3.5" fill="currentColor"
                            viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                @endif
            </div>

            {{-- P0 Commerce: EGI Listing Wizard Button --}}
            @if (($isCreator ?? false) || ($isOwner ?? false))
                <a href="{{ route('egi.listing.wizard', $egi) }}"
                    class="mb-3 flex w-full items-center justify-center gap-2 rounded-lg border border-green-500/50 bg-gradient-to-r from-green-600/30 to-emerald-600/30 px-3 py-2 text-sm font-medium text-white backdrop-blur-sm transition-all hover:border-green-500 hover:from-green-600/40 hover:to-emerald-600/40">
                    <span class="material-symbols-outlined text-base">storefront</span>
                    <span>Configure Listing</span>
                </a>
            @endif

            {{-- ============================================ --}}
            {{-- DUAL ARCHITECTURE PANELS (Feature-flagged) --}}
            {{-- ============================================ --}}

            @php
                // LOGICA CORRETTA: token_EGI NULL = non mintato su blockchain
                // egi_type indica solo l'architettura (ASA/SmartContract), non lo stato di mint
$isNotMinted = !($isMinted ?? !is_null($egi->token_EGI)); // Usa variabile passata se disponibile
$isASA = $egi->egi_type === 'ASA';
$isSmartContract = $egi->egi_type === 'SmartContract';
                // Usa le variabili di permesso passate da show.blade.php
                $canEditMetadataLocal = $canEditMetadata ?? ($isCreator ?? false) && $isNotMinted;
            @endphp


            {{-- Pannello Unificato: Prepara e Minta (Solo Creator di EGI non mintati) --}}
            @if ($isNotMinted && ($isCreator ?? false))
                <details class="group mb-4" open>
                    <summary
                        class="flex cursor-pointer items-center justify-between rounded-lg bg-blue-800/30 px-3 py-2 text-blue-300 transition-colors hover:bg-blue-800/40">
                        <span class="text-sm font-semibold">🎨 Prepara e Minta il tuo EGI</span>
                        <svg class="h-4 w-4 transition-transform group-open:rotate-180" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </summary>
                    <div class="mt-3">
                        <x-egi-unified-mint-panel :egi="$egi" :isCreator="$isCreator ?? false" />
                    </div>
                </details>
            @endif

            {{-- Vecchio pannello Traits separato rimosso - ora integrato nel pannello unificato --}}
            {{-- Vecchio Pre-Mint Panel rimosso - ora unificato sopra --}}

            {{-- EGI Vivente Panel (Solo SmartContract mintati) - COLLAPSABILE --}}
            @if ($isSmartContract && $egi->smartContract)
                <details class="group mb-4">
                    <summary
                        class="flex cursor-pointer items-center justify-between rounded-lg bg-orange-800/30 px-3 py-2 text-orange-300 transition-colors hover:bg-orange-800/40">
                        <span class="text-sm font-semibold">EGI Vivente</span>
                        <svg class="h-4 w-4 transition-transform group-open:rotate-180" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </summary>
                    <div class="mt-3">
                        <x-egi-living-panel :egi="$egi" />
                    </div>
                </details>
            @endif

            {{-- ============================================ --}}

            {{-- 🔒 EGI CERTIFICATO SU BLOCKCHAIN - Info complete mint --}}
            @if ($egi->token_EGI)
                <div class="mb-4 rounded-lg border border-amber-500/30 bg-amber-500/10 p-4">
                    <div class="flex items-start space-x-3">
                        <svg class="h-5 w-5 flex-shrink-0 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                                clip-rule="evenodd" />
                        </svg>
                        <div class="w-full">
                            <h4 class="font-semibold text-amber-300">🔒
                                {{ __('egi.crud.blockchain_warning_title') }}</h4>
                            <p class="mt-1 text-sm text-amber-200/80">
                                {!! __('egi.crud.blockchain_warning_message', ['asa_id' => $egi->token_EGI]) !!}
                            </p>

                            {{-- Badge ASA ID (spostato da Col 4) --}}
                            <div class="mt-3">
                                <a href="https://algoexplorer.io/asset/{{ $egi->token_EGI }}" target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex items-center space-x-1.5 rounded-full border border-amber-400/50 bg-gradient-to-r from-amber-500/20 to-emerald-500/20 px-3 py-1.5 shadow-md backdrop-blur-md transition-all hover:border-amber-400/70 hover:from-amber-500/30 hover:to-emerald-500/30">
                                    <svg class="h-4 w-4 flex-shrink-0 text-amber-400" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-xs font-bold text-emerald-400">ASA #{{ $egi->token_EGI }}</span>
                                    <svg class="h-3.5 w-3.5 flex-shrink-0 text-amber-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                </a>
                            </div>

                            {{-- Bottone Visualizza Dettagli Mint (spostato da Col 4) --}}
                            @if ($egi->blockchain && $egi->blockchain->id)
                                <div class="mt-3">
                                    <a href="{{ route('mint.show', $egi->blockchain->id) }}"
                                        class="inline-flex w-full items-center justify-center rounded-lg border border-green-600/30 bg-green-700/20 px-4 py-2.5 font-medium text-green-400 backdrop-blur-sm transition-all hover:border-green-500/50 hover:bg-green-600/30 hover:text-green-300">
                                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ __('egi.status.view_mint_details') ?? 'Visualizza Dettagli Mint' }}
                                    </a>
                                </div>
                            @endif

                            {{-- Link Verifica su Blockchain --}}
                            <a href="https://testnet.explorer.perawallet.app/asset/{{ $egi->token_EGI }}"
                                target="_blank"
                                class="mt-3 inline-flex items-center text-xs font-medium text-amber-300 transition-colors hover:text-amber-200">
                                {{ __('egi.crud.blockchain_verify_link') }}
                                <svg class="ml-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            {{-- CRUD Dati EGI - COLLAPSABILE --}}
            <details class="group mb-4" open>
                <summary
                    class="mb-4 flex cursor-pointer items-center justify-between rounded-lg bg-emerald-800/30 px-3 py-2 text-emerald-300 transition-colors hover:bg-emerald-800/40">
                    <span class="text-sm font-semibold">{{ __('egi.crud.edit_egi') }}</span>
                    <svg class="h-4 w-4 transition-transform group-open:rotate-180" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </summary>
                <div>

                    {{-- Edit Form --}}
                    <form id="egi-edit-form" action="{{ route('egis.update', $egi->id) }}" method="POST"
                        class="space-y-4" style="display: none;">
                        @csrf
                        @method('PUT')

                        {{-- Title Field --}}
                        @php $metadataLocked = !($canEditMetadata ?? false); @endphp
                        <div class="{{ $metadataLocked ? 'opacity-50 pointer-events-none' : '' }}">
                            <label for="title" class="mb-2 block text-sm font-medium text-emerald-300">
                                {{ __('egi.crud.title') }}
                                @if ($metadataLocked)
                                    <svg class="ml-1 inline h-4 w-4 text-amber-400" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                @endif
                            </label>
                            <input type="text" id="title" name="title"
                                value="{{ old('title', $egi->title) }}"
                                {{ $metadataLocked ? 'disabled readonly' : '' }}
                                class="{{ $metadataLocked ? 'bg-black/10 cursor-not-allowed border-gray-600' : 'bg-black/30 border-emerald-700/50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500' }} w-full rounded-lg border px-3 py-2 text-white placeholder-gray-400"
                                placeholder="{{ __('egi.crud.title_placeholder') }}" maxlength="60"
                                {{ $metadataLocked ? '' : 'required' }}>
                            <div class="{{ $metadataLocked ? 'text-amber-400' : 'text-gray-400' }} mt-1 text-xs">
                                @if ($metadataLocked)
                                    🔒 {{ __('egi.crud.field_immutable_hint') }}
                                @else
                                    {{ __('egi.crud.title_hint') }}
                                @endif
                            </div>
                        </div>

                        {{-- Description Field --}}
                        <div class="{{ $metadataLocked ? 'opacity-50 pointer-events-none' : '' }}">
                            <label for="description" class="mb-2 block text-sm font-medium text-emerald-300">
                                {{ __('egi.crud.description') }}
                                @if ($metadataLocked)
                                    <svg class="ml-1 inline h-4 w-4 text-amber-400" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                @endif
                            </label>
                            <textarea id="description" name="description" rows="4" {{ $metadataLocked ? 'disabled readonly' : '' }}
                                class="{{ $metadataLocked ? 'bg-black/10 cursor-not-allowed border-gray-600' : 'bg-black/30 border-emerald-700/50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500' }} w-full resize-none rounded-lg border px-3 py-2 text-white placeholder-gray-400"
                                placeholder="{{ __('egi.crud.description_placeholder') }}">{{ old('description', $egi->description) }}</textarea>

                            {{-- AI Generate Description Button --}}
                            @if (!$metadataLocked)
                                <button type="button" onclick="handleGenerateDescription({{ $egi->id }})"
                                    class="mt-2 flex w-full items-center justify-center gap-2 rounded-lg border border-blue-600/50 bg-gradient-to-r from-blue-600/20 to-purple-600/20 px-3 py-2 text-sm font-medium text-blue-400 transition-all hover:border-blue-500 hover:from-blue-600/30 hover:to-purple-600/30">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                    </svg>
                                    <span>🤖 {{ __('egi.crud.generate_description_ai') }}</span>
                                </button>
                            @endif

                            <div class="{{ $metadataLocked ? 'text-amber-400' : 'text-gray-400' }} mt-1 text-xs">
                                @if ($metadataLocked)
                                    🔒 {{ __('egi.crud.field_immutable_hint') }}
                                @else
                                    {{ __('egi.crud.description_hint') }}
                                @endif
                            </div>
                        </div>

                        {{-- Price Field --}}
                        @php
                            // Il prezzo è controllato da $canManagePrice O da $isPriceLocked (prenotazione attiva)
                            // INOLTRE: se la collection non può vendere, il prezzo è sempre bloccato
                            $priceLocked = !($canManagePrice ?? false) || ($isPriceLocked ?? false) || !$canSellEgis;
                        @endphp
                        <div>
                            <label for="price"
                                class="{{ $priceLocked ? 'opacity-60' : '' }} mb-2 block text-sm font-medium text-emerald-300">
                                {{ __('egi.crud.price') }}
                                @if ($priceLocked)
                                    <svg class="ml-1 inline h-4 w-4 text-yellow-500" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                @endif
                            </label>
                            <div class="relative">
                                <input type="number" id="price" name="price"
                                    value="{{ old('price', $egi->price) }}" step="0.01" min="0"
                                    {{ $priceLocked ? 'disabled readonly' : '' }}
                                    class="{{ $priceLocked ? 'bg-black/10 opacity-60 cursor-not-allowed border-gray-600' : 'bg-black/30 border-emerald-700/50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500' }} w-full rounded-lg border px-3 py-2 text-white placeholder-gray-400"
                                    placeholder="{{ __('egi.crud.price_placeholder') }}">
                                <span
                                    class="{{ $priceLocked ? 'text-gray-500' : 'text-gray-400' }} absolute right-3 top-2 text-sm">ALGO</span>
                                @if ($priceLocked)
                                    <div
                                        class="absolute inset-0 flex items-center justify-center rounded-lg bg-black/20">
                                        <svg class="h-6 w-6 text-yellow-500 opacity-80" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="{{ $priceLocked ? 'text-yellow-400' : 'text-gray-400' }} mt-1 text-xs">
                                @if ($priceLocked)
                                    🔒 {{ __('egi.crud.price_locked_message') }}
                                @else
                                    {{ __('egi.crud.price_hint') }} (Prezzo base in ALGO)
                                @endif
                            </div>
                        </div>

                        {{-- 🥇 COMMODITY FIELDS (Dynamic Partial) --}}
                        @if ($egi->isGoldBar() || $egi->commodity_type)
                            @php
                                $cType = $egi->commodity_type;
                                if (!$cType && $egi->isGoldBar()) {
                                    $cType = 'goldbar';
                                }
                                if ($cType) {
                                    $cType = str_replace('-', '', $cType);
                                }
                                $commData = $egi->commodity_metadata ?? [];
                            @endphp

                            @if ($cType)
                                @includeIf("egis.commodity.{$cType}", ['data' => $commData])
                            @endif
                        @endif

                        {{-- Sale Mode Selector - Controllato da canManagePrice E canSellEgis --}}
                        @if (($canManagePrice ?? false) && $canSellEgis)
                            <div>
                                @php
                                    $merchantPspStatus = $merchantPspStatus ?? [
                                        'has_any_psp' => false,
                                        'has_stripe' => false,
                                        'has_paypal' => false,
                                        'can_accept_payments' => false,
                                    ];
                                    $saleModeLocked = !($merchantPspStatus['can_accept_payments'] ?? false);
                                    $saleModeVal = $saleModeLocked
                                        ? 'not_for_sale'
                                        : old('sale_mode', $egi->sale_mode ?? 'not_for_sale');
                                @endphp
                                <label for="sale_mode" class="mb-2 block text-sm font-medium text-emerald-300">
                                    {{ __('egi.crud.sale_mode') }}
                                </label>
                                @if ($saleModeLocked)
                                    <input type="hidden" name="sale_mode" value="not_for_sale">
                                @endif
                                <select id="sale_mode" name="sale_mode"
                                    class="{{ $saleModeLocked ? 'opacity-60 cursor-not-allowed' : '' }} w-full rounded-lg border border-emerald-700/50 bg-black/30 px-3 py-2 text-white focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                    {{ $saleModeLocked ? 'disabled' : '' }}>
                                    <option value="not_for_sale"
                                        {{ $saleModeVal === 'not_for_sale' ? 'selected' : '' }}>
                                        {{ __('egi.crud.sale_mode_not_for_sale') }}</option>
                                    <option value="fixed_price"
                                        {{ $saleModeVal === 'fixed_price' ? 'selected' : '' }}>
                                        {{ __('egi.crud.sale_mode_fixed_price') }}</option>
                                    <option value="auction" {{ $saleModeVal === 'auction' ? 'selected' : '' }}>
                                        {{ __('egi.crud.sale_mode_auction') }}</option>
                                </select>
                                <div class="mt-1 text-xs text-gray-400">
                                    {{ __('egi.crud.sale_mode_hint') }}
                                </div>

                                @if ($saleModeLocked)
                                    <div
                                        class="mt-3 rounded-lg border-2 border-amber-500/60 bg-amber-900/40 p-4 text-amber-100 shadow-lg shadow-amber-900/20">
                                        <div class="flex items-start gap-3">
                                            <svg class="h-6 w-6 flex-shrink-0 text-amber-400" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                            <div class="flex-1">
                                                <p class="text-sm font-bold text-amber-300">
                                                    {{ __('egi.crud.psp_required_title') }}
                                                </p>
                                                <p class="mt-1 text-sm leading-relaxed text-amber-100">
                                                    {{ __('egi.crud.psp_required_description') }}
                                                </p>
                                                <p class="mt-2 text-xs font-medium text-amber-200/90">
                                                    {{ __('egi.crud.psp_only_egili_hint') }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="mt-4 flex flex-wrap gap-2">
                                            <button type="button"
                                                onclick="window.openWalletWelcomeModalSafe && window.openWalletWelcomeModalSafe();"
                                                class="inline-flex items-center justify-center rounded-lg border border-amber-400/50 bg-amber-500/30 px-4 py-2.5 text-xs font-bold uppercase tracking-wide text-amber-50 transition-all hover:border-amber-400 hover:bg-amber-500/50 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2 focus:ring-offset-amber-900">
                                                {{ __('egi.crud.psp_open_modal') }}
                                            </button>
                                            <a href="{{ route('creator.onboarding.summary') }}"
                                                class="inline-flex items-center justify-center rounded-lg border border-amber-400/40 bg-transparent px-4 py-2.5 text-xs font-bold uppercase tracking-wide text-amber-200 transition-all hover:border-amber-400/60 hover:bg-amber-500/20 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2 focus:ring-offset-amber-900">
                                                {{ __('egi.crud.psp_onboarding_link') }}
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- Egili Payment Toggle --}}
                            <div>
                                <label class="mb-2 block text-sm font-medium text-emerald-300" for="payment_by_egili">
                                    {{ __('egi.crud.payment_by_egili') }}
                                </label>
                                <div
                                    class="flex items-start gap-3 rounded-lg border border-emerald-700/30 bg-black/15 p-3">
                                    <input type="hidden" name="payment_by_egili" value="0">
                                    <input type="checkbox" id="payment_by_egili" name="payment_by_egili"
                                        value="1"
                                        {{ old('payment_by_egili', $egi->payment_by_egili) ? 'checked' : '' }}
                                        class="mt-1 h-4 w-4 rounded border-emerald-700/50 bg-black/30 text-emerald-500 focus:ring-2 focus:ring-emerald-500">
                                    <div class="text-xs text-emerald-100/80">
                                        <p class="font-semibold text-emerald-200">
                                            {{ __('egi.crud.payment_by_egili') }}
                                        </p>
                                        <p class="mt-1 leading-relaxed text-emerald-100/70">
                                            {{ __('egi.crud.payment_by_egili_hint') }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- Auction Configuration (visible only if sale_mode = auction) --}}
                            <div id="auction-config" class="mt-2 hidden">
                                <div class="rounded-lg border border-emerald-700/30 bg-black/10 p-3">
                                    <div class="mb-2 text-sm font-medium text-emerald-300">
                                        {{ __('egi.crud.auction_section_title') }}
                                    </div>

                                    {{-- Minimum Price --}}
                                    <div class="mb-3">
                                        <label for="auction_minimum_price"
                                            class="mb-1 block text-xs font-medium text-emerald-300">
                                            {{ __('egi.crud.auction_minimum_price') }} (EUR)
                                        </label>
                                        <input type="number" id="auction_minimum_price" name="auction_minimum_price"
                                            value="{{ old('auction_minimum_price', $egi->auction_minimum_price) }}"
                                            min="0" step="0.01"
                                            class="w-full rounded-lg border border-emerald-700/50 bg-black/30 px-3 py-2 text-white placeholder-gray-400 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                            placeholder="0.00">
                                        <div class="mt-1 text-xs text-gray-400">
                                            {{ __('egi.crud.auction_minimum_price_hint') }}
                                        </div>
                                    </div>

                                    {{-- Start Datetime --}}
                                    <div class="mb-3">
                                        <label for="auction_start"
                                            class="mb-1 block text-xs font-medium text-emerald-300">
                                            {{ __('egi.crud.auction_start') }}
                                        </label>
                                        <input type="datetime-local" id="auction_start" name="auction_start"
                                            value="{{ old('auction_start', optional($egi->auction_start)->format('Y-m-d\\TH:i')) }}"
                                            class="w-full rounded-lg border border-emerald-700/50 bg-black/30 px-3 py-2 text-white placeholder-gray-400 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                        <div class="mt-1 text-xs text-gray-400">
                                            {{ __('egi.crud.auction_start_hint') }}
                                        </div>
                                    </div>

                                    {{-- End Datetime --}}
                                    <div class="mb-3">
                                        <label for="auction_end"
                                            class="mb-1 block text-xs font-medium text-emerald-300">
                                            {{ __('egi.crud.auction_end') }}
                                        </label>
                                        <input type="datetime-local" id="auction_end" name="auction_end"
                                            value="{{ old('auction_end', optional($egi->auction_end)->format('Y-m-d\\TH:i')) }}"
                                            class="w-full rounded-lg border border-emerald-700/50 bg-black/30 px-3 py-2 text-white placeholder-gray-400 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                        <div class="mt-1 text-xs text-gray-400">
                                            {{ __('egi.crud.auction_end_hint') }}
                                        </div>
                                    </div>

                                    {{-- Auto-mint to highest bidder --}}
                                    <div class="mt-1">
                                        <label class="flex items-center">
                                            <input type="hidden" name="auto_mint_highest" value="0">
                                            <input type="checkbox" id="auto_mint_highest" name="auto_mint_highest"
                                                value="1"
                                                {{ old('auto_mint_highest', $egi->auto_mint_highest) ? 'checked' : '' }}
                                                class="h-4 w-4 rounded border-emerald-700/50 bg-black/30 text-emerald-600 focus:ring-2 focus:ring-emerald-500">
                                            <span class="ml-3 text-xs font-medium text-emerald-300">
                                                {{ __('egi.crud.auto_mint_highest') }}
                                            </span>
                                        </label>
                                        <div class="ml-7 mt-1 text-[11px] text-gray-400">
                                            {{ __('egi.crud.auto_mint_highest_hint') }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @push('scripts')
                                <script>
                                    (function() {
                                        const saleModeSel = document.getElementById('sale_mode');
                                        const auctionBox = document.getElementById('auction-config');

                                        function refreshAuctionVisibility() {
                                            const v = saleModeSel?.value || 'not_for_sale';
                                            if (!auctionBox) return;
                                            if (v === 'auction') {
                                                auctionBox.classList.remove('hidden');
                                            } else {
                                                auctionBox.classList.add('hidden');
                                            }
                                        }
                                        saleModeSel?.addEventListener('change', refreshAuctionVisibility);
                                        // init
                                        refreshAuctionVisibility();
                                    })();
                                </script>
                            @endpush
                        @endif
                        {{-- END canManagePrice wrapper per sezioni vendita --}}

                        {{-- Creation Date Field - Parte dei metadati --}}
                        <div class="{{ $metadataLocked ? 'opacity-50 pointer-events-none' : '' }}">
                            <label for="creation_date" class="mb-2 block text-sm font-medium text-emerald-300">
                                {{ __('egi.crud.creation_date') }}
                                @if ($metadataLocked)
                                    <svg class="ml-1 inline h-4 w-4 text-amber-400" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                @endif
                            </label>
                            <input type="date" id="creation_date" name="creation_date"
                                value="{{ old('creation_date', $egi->creation_date?->format('Y-m-d')) }}"
                                {{ $metadataLocked ? 'disabled readonly' : '' }}
                                class="{{ $metadataLocked ? 'bg-black/10 cursor-not-allowed border-gray-600' : 'bg-black/30 border-emerald-700/50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500' }} w-full rounded-lg border px-3 py-2 text-white">
                            <div class="{{ $metadataLocked ? 'text-amber-400' : 'text-gray-400' }} mt-1 text-xs">
                                @if ($metadataLocked)
                                    🔒 {{ __('egi.crud.field_immutable_hint') }}
                                @else
                                    {{ __('egi.crud.creation_date_hint') }}
                                @endif
                            </div>
                        </div>

                        {{-- Published Toggle - NON è un metadato blockchain, è gestione commerciale --}}
                        {{-- L'Owner può sempre modificarlo per mettere/togliere dalla vendita --}}
                        @php
                            // is_published può essere modificato da:
                            // - Creator su EGI non mintato (canEditMetadata)
                            // - Owner su EGI mintato (canManagePrice per mercato secondario)
                            $canEditPublished = ($canEditMetadata ?? false) || ($canManagePrice ?? false);
                        @endphp
                        <div>
                            <label class="flex items-center">
                                <input type="hidden" name="is_published" value="0">
                                <input type="checkbox" id="is_published" name="is_published" value="1"
                                    {{ old('is_published', $egi->is_published) ? 'checked' : '' }}
                                    {{ !$canEditPublished ? 'disabled' : '' }}
                                    class="{{ !$canEditPublished ? 'bg-black/10 border-gray-600 cursor-not-allowed' : 'bg-black/30 border-emerald-700/50 focus:ring-emerald-500 focus:ring-2' }} h-4 w-4 rounded text-emerald-600">
                                <span class="ml-3 text-sm font-medium text-emerald-300">
                                    {{ __('egi.crud.is_published') }}
                                </span>
                            </label>
                            <div class="ml-7 mt-1 text-xs text-gray-400">
                                {{ __('egi.crud.is_published_hint') }}
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex gap-3 pt-4">
                            <button type="submit"
                                class="inline-flex flex-1 items-center justify-center rounded-lg bg-gradient-to-r from-emerald-600 to-emerald-700 px-4 py-2 font-medium text-white transition-all duration-200 hover:from-emerald-700 hover:to-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12">
                                    </path>
                                </svg>
                                {{ __('egi.crud.save_changes') }}
                            </button>

                            {{-- Delete button: only if user can delete AND EGI is NOT minted (blockchain immutability) --}}
                            @if ($canDeleteEgi && is_null($egi->token_EGI))
                                <button type="button" id="egi-delete-btn"
                                    class="rounded-lg bg-gradient-to-r from-red-600 to-red-700 px-4 py-2 font-medium text-white transition-all duration-200 hover:from-red-700 hover:to-red-800 focus:outline-none focus:ring-2 focus:ring-red-500"
                                    title="{{ __('egi.crud.delete_egi') }}">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </form>

                    {{-- View Mode (Default) --}}
                    <div id="egi-view-mode" class="space-y-4">
                        <div class="rounded-lg bg-black/20 p-4">
                            <div class="mb-1 text-sm text-emerald-300">{{ __('egi.crud.current_title') }}
                            </div>
                            <div class="font-medium text-white">{{ $egi->title ?: __('egi.crud.no_title') }}
                            </div>
                        </div>

                        <div class="rounded-lg bg-black/20 p-4">
                            <div class="mb-1 text-sm text-emerald-300">
                                {{ $highestPriorityReservation ? __('egi.price.highest_bid') : __('egi.crud.current_price') }}
                            </div>
                            <div class="font-medium text-white">
                                @if ($displayPrice)
                                    <x-currency-price :price="$displayPrice" :egi="$egi" :reservation="$highestPriorityReservation" />
                                @else
                                    {{ __('egi.crud.price_not_set') }}
                                @endif
                            </div>
                            @if ($displayUser || $highestPriorityReservation)
                                @php
                                    $isWeakReservation =
                                        $highestPriorityReservation && $highestPriorityReservation->type === 'weak';
                                    $textColor = $isWeakReservation ? 'text-amber-400' : 'text-emerald-400';
                                @endphp

                                <div class="{{ $textColor }} mt-2 flex items-center gap-1 text-xs">
                                    @if ($isWeakReservation)
                                        <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ __('egi.reservation.by') }}:
                                        {{ $highestPriorityReservation->fegi_code ?? 'FG#******' }}
                                    @else
                                        @php
                                            $activatorDisplay = formatActivatorDisplay($displayUser);
                                        @endphp

                                        @if ($activatorDisplay['is_commissioner'] && $activatorDisplay['avatar'])
                                            {{-- Commissioner with avatar --}}
                                            <img src="{{ $activatorDisplay['avatar'] }}"
                                                alt="{{ $activatorDisplay['name'] }}"
                                                class="h-3 w-3 rounded-full border border-emerald-400/30 object-cover">
                                        @else
                                            {{-- Regular collector or commissioner without avatar --}}
                                            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        @endif

                                        {{ __('egi.reservation.by') }}: {{ $activatorDisplay['name'] }}
                                    @endif
                                </div>
                            @endif
                        </div>

                        <div class="rounded-lg bg-black/20 p-4">
                            <div class="mb-1 text-sm text-emerald-300">{{ __('egi.crud.current_status') }}
                            </div>
                            <div class="flex items-center">
                                <span
                                    class="{{ $egi->is_published ? 'bg-green-400' : 'bg-gray-400' }} mr-2 h-2 w-2 rounded-full"></span>
                                <span class="font-medium text-white">
                                    {{ $egi->is_published ? __('egi.crud.status_published') : __('egi.crud.status_draft') }}
                                </span>
                            </div>
                        </div>

                        <div class="rounded-lg bg-black/20 p-4">
                            <div class="mb-1 text-sm text-emerald-300">{{ __('egi.crud.payment_by_egili_status') }}
                            </div>
                            <div class="flex items-center gap-2">
                                <span
                                    class="{{ $egi->payment_by_egili ? 'bg-emerald-400' : 'bg-gray-500' }} h-2 w-2 rounded-full"></span>
                                <span class="font-medium text-white">
                                    {{ $egi->payment_by_egili ? __('egi.crud.payment_by_egili_enabled') : __('egi.crud.payment_by_egili_disabled') }}
                                </span>
                            </div>
                        </div>

                        <button id="egi-edit-start"
                            class="inline-flex w-full items-center justify-center rounded-lg bg-gradient-to-r from-emerald-600/80 to-emerald-700/80 px-4 py-3 font-medium text-white transition-all duration-200 hover:from-emerald-600 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                </path>
                            </svg>
                            {{ __('egi.crud.start_editing') }}
                        </button>
                    </div>

                </div>{{-- Fine details content --}}
            </details>{{-- Fine CRUD Dati Collapsabile --}}

        </div>
    </div>
</div>

{{-- Script CRUD spostato in @push per evitare interferenze con grid --}}
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('egi-edit-form');
            if (!form) return;

            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(form);
                const submitButton = form.querySelector('button[type="submit"]');

                // Convert FormData to object for JSON with support for nested keys like commodity_data[weight]
                const formObject = {};
                formData.forEach((value, key) => {
                    // Check if key matches array notation: name[key]
                    if (key.includes('[') && key.includes(']')) {
                        const parts = key.split('[');
                        const mainKey = parts[0];
                        const subKey = parts[1].replace(']', '');

                        if (!formObject[mainKey]) {
                            formObject[mainKey] = {};
                        }
                        formObject[mainKey][subKey] = value;
                    } else {
                        formObject[key] = value;
                    }
                });

                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.textContent = '{{ __('egi.crud.saving') }}...';
                }

                fetch(form.action, {
                        method: 'POST',
                        body: JSON.stringify(formObject),
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Success - show SweetAlert
                            Swal.fire({
                                icon: 'success',
                                title: '{{ __('egi.crud.update_success') }}',
                                text: data.message || '{{ __('egi.crud.update_success') }}',
                                timer: 3000,
                                timerProgressBar: true,
                                showConfirmButton: false
                            }).then(() => {
                                // Reload page to reflect changes
                                window.location.reload();
                            });
                        } else {
                            // Validation errors - show first error in SweetAlert
                            const firstError = Object.values(data.errors)[0][0];
                            Swal.fire({
                                icon: 'error',
                                title: '{{ __('egi.validation.validation_failed') }}',
                                text: firstError,
                                confirmButtonText: '{{ __('egi.crud.ok') }}'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __('egi.crud.update_error') }}',
                            text: '{{ __('egi.crud.generic_error') }}',
                            confirmButtonText: '{{ __('egi.crud.ok') }}'
                        });
                    })
                    .finally(() => {
                        if (submitButton) {
                            submitButton.disabled = false;
                            submitButton.textContent = '{{ __('egi.crud.save_changes') }}';
                        }
                    });
            });
        });
    </script>
@endpush
