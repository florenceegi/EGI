{{-- resources/views/egis/partials/sidebar/crud-panel.blade.php --}}
{{--
    Pannello CRUD per editing (solo per creator)
    ORIGINE: righe 129-381 di show.blade.php
    VARIABILI: $egi, $canUpdateEgi, $canDeleteEgi, $isPriceLocked, $canModifyPrice, $displayPrice, $displayUser, $highestPriorityReservation
--}}

{{-- Center: CRUD Box - Responsive --}}
@if ($canUpdateEgi)
    <div
        class="overflow-y-auto border-t border-emerald-700/30 bg-gradient-to-b from-emerald-900/20 to-emerald-900/10 backdrop-blur-xl md:col-span-12 md:border-l md:border-r md:border-t-0 lg:col-span-12 xl:col-span-2">
        {{-- CRUD Box Content - Responsive padding --}}
        <div class="p-3 md:p-4 lg:p-5 xl:p-6">
            <div
                class="rounded-xl border border-emerald-700/30 bg-gradient-to-br from-emerald-800/20 to-emerald-900/20 p-4 md:p-5 lg:p-6">

                {{-- Header - Responsive text --}}
                <div class="mb-4 flex items-center justify-between md:mb-6">
                    <h3 class="text-base font-semibold text-emerald-400 md:text-lg">
                        <svg class="mr-2 inline h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                            </path>
                        </svg>
                        {{ __('egi.crud.edit_egi') }}
                    </h3>
                    <button id="egi-edit-toggle"
                        class="rounded-full p-2 text-emerald-400 transition-colors duration-200 hover:bg-emerald-800/30"
                        title="{{ __('egi.crud.toggle_edit_mode') }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                            </path>
                        </svg>
                    </button>
                </div>

                {{-- ============================================ --}}
                {{-- DUAL ARCHITECTURE PANELS (Feature-flagged) --}}
                {{-- ============================================ --}}

                @php
                    // LOGICA CORRETTA: NULL = non mintato (Pre-Mint virtuale)
                    $isNotMinted = is_null($egi->egi_type);
                    $isASA = $egi->egi_type === 'ASA';
                    $isSmartContract = $egi->egi_type === 'SmartContract';
                    $isCreatorCheck = App\Helpers\FegiAuth::check() && App\Helpers\FegiAuth::id() === $egi->user_id;
                @endphp

                {{-- 🐛 DEBUG PANEL (RIMUOVERE IN PRODUCTION) --}}
                @if (config('app.debug'))
                    <div class="mb-4 rounded-lg border-2 border-yellow-500 bg-yellow-500/10 p-3">
                        <div class="font-mono text-xs text-yellow-300">
                            <div><strong>DEBUG:</strong> EGI #{{ $egi->id }}</div>
                            <div>egi_type: <strong>{{ $egi->egi_type ?? 'NULL' }}</strong></div>
                            <div>pre_mint_mode:
                                <strong>{{ $egi->pre_mint_mode ? 'TRUE (riservato creator)' : 'FALSE (marketplace)' }}</strong>
                            </div>
                            <div>token_EGI: <strong>{{ $egi->token_EGI ?? 'NULL' }}</strong></div>
                            <div>isNotMinted: <strong>{{ $isNotMinted ? 'YES' : 'NO' }}</strong></div>
                            <div>isASA: <strong>{{ $isASA ? 'YES' : 'NO' }}</strong></div>
                            <div>isSmartContract: <strong>{{ $isSmartContract ? 'YES' : 'NO' }}</strong></div>
                            <div>isCreator: <strong>{{ $isCreatorCheck ? 'YES' : 'NO' }}</strong></div>
                            <div class="mt-2 border-t border-yellow-600 pt-2">
                                <strong>Pannelli visibili:</strong><br>
                                Auto-Mint Panel: {{ $isNotMinted && $isCreatorCheck ? '✓ SHOW' : '✗ HIDE' }}<br>
                                Pre-Mint Panel: {{ $isNotMinted && !$egi->pre_mint_mode ? '✓ SHOW' : '✗ HIDE' }}<br>
                                Living Panel: {{ $isSmartContract ? '✓ SHOW' : '✗ HIDE' }}
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Auto-Mint Panel (Solo Creator di EGI non mintati) --}}
                @if ($isNotMinted && $isCreatorCheck)
                    <div class="mb-6">
                        <x-egi-auto-mint-panel :egi="$egi" :isCreator="$isCreatorCheck" />
                    </div>
                @endif

                {{-- AI Traits Panel (Solo Creator di EGI non mintati) --}}
                @if ($isNotMinted && $isCreatorCheck)
                    <div class="mb-6">
                        <x-egi-ai-traits-panel :egi="$egi" :isCreator="$isCreatorCheck" />
                    </div>
                @endif

                {{-- Pre-Mint Panel (EGI non mintati e disponibili sul marketplace) --}}
                @if ($isNotMinted && !$egi->pre_mint_mode)
                    <div class="mb-6">
                        <x-egi-pre-mint-panel :egi="$egi" />
                    </div>
                @endif

                {{-- EGI Vivente Panel (Solo SmartContract mintati) --}}
                @if ($isSmartContract && $egi->smartContract)
                    <div class="mb-6">
                        <x-egi-living-panel :egi="$egi" />
                    </div>
                @endif

                {{-- ============================================ --}}

                {{-- 🔒 BLOCKCHAIN IMMUTABILITY WARNING --}}
                @if ($egi->token_EGI)
                    <div class="mb-4 rounded-lg border border-amber-500/30 bg-amber-500/10 p-4">
                        <div class="flex items-start space-x-3">
                            <svg class="h-5 w-5 flex-shrink-0 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                                    clip-rule="evenodd" />
                            </svg>
                            <div>
                                <h4 class="font-semibold text-amber-300">🔒
                                    {{ __('egi.crud.blockchain_warning_title') }}</h4>
                                <p class="mt-1 text-sm text-amber-200/80">
                                    {!! __('egi.crud.blockchain_warning_message', ['asa_id' => $egi->token_EGI]) !!}
                                </p>
                                <a href="https://testnet.explorer.perawallet.app/asset/{{ $egi->token_EGI }}"
                                    target="_blank"
                                    class="mt-2 inline-flex items-center text-xs font-medium text-amber-300 transition-colors hover:text-amber-200">
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

                {{-- Edit Form --}}
                <form id="egi-edit-form" action="{{ route('egis.update', $egi->id) }}" method="POST" class="space-y-4"
                    style="display: none;">
                    @csrf
                    @method('PUT')

                    {{-- Title Field --}}
                    <div class="{{ $egi->token_EGI ? 'opacity-50 pointer-events-none' : '' }}">
                        <label for="title" class="mb-2 block text-sm font-medium text-emerald-300">
                            {{ __('egi.crud.title') }}
                            @if ($egi->token_EGI)
                                <svg class="ml-1 inline h-4 w-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            @endif
                        </label>
                        <input type="text" id="title" name="title" value="{{ old('title', $egi->title) }}"
                            {{ $egi->token_EGI ? 'disabled readonly' : '' }}
                            class="{{ $egi->token_EGI ? 'bg-black/10 cursor-not-allowed border-gray-600' : 'bg-black/30 border-emerald-700/50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500' }} w-full rounded-lg border px-3 py-2 text-white placeholder-gray-400"
                            placeholder="{{ __('egi.crud.title_placeholder') }}" maxlength="60"
                            {{ $egi->token_EGI ? '' : 'required' }}>
                        <div class="{{ $egi->token_EGI ? 'text-amber-400' : 'text-gray-400' }} mt-1 text-xs">
                            @if ($egi->token_EGI)
                                🔒 {{ __('egi.crud.field_immutable_hint') }}
                            @else
                                {{ __('egi.crud.title_hint') }}
                            @endif
                        </div>
                    </div>

                    {{-- Description Field --}}
                    <div class="{{ $egi->token_EGI ? 'opacity-50 pointer-events-none' : '' }}">
                        <label for="description" class="mb-2 block text-sm font-medium text-emerald-300">
                            {{ __('egi.crud.description') }}
                            @if ($egi->token_EGI)
                                <svg class="ml-1 inline h-4 w-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            @endif
                        </label>
                        <textarea id="description" name="description" rows="4" {{ $egi->token_EGI ? 'disabled readonly' : '' }}
                            class="{{ $egi->token_EGI ? 'bg-black/10 cursor-not-allowed border-gray-600' : 'bg-black/30 border-emerald-700/50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500' }} w-full resize-none rounded-lg border px-3 py-2 text-white placeholder-gray-400"
                            placeholder="{{ __('egi.crud.description_placeholder') }}">{{ old('description', $egi->description) }}</textarea>
                        <div class="{{ $egi->token_EGI ? 'text-amber-400' : 'text-gray-400' }} mt-1 text-xs">
                            @if ($egi->token_EGI)
                                🔒 {{ __('egi.crud.field_immutable_hint') }}
                            @else
                                {{ __('egi.crud.description_hint') }}
                            @endif
                        </div>
                    </div>

                    {{-- Price Field --}}
                    <div>
                        <label for="price"
                            class="{{ $isPriceLocked ? 'opacity-60' : '' }} mb-2 block text-sm font-medium text-emerald-300">
                            {{ __('egi.crud.price') }}
                            @if ($isPriceLocked)
                                <svg class="ml-1 inline h-4 w-4 text-yellow-500" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            @endif
                        </label>
                        <div class="relative">
                            <input type="number" id="price" name="price" value="{{ old('price', $egi->price) }}"
                                step="0.01" min="0" {{ $isPriceLocked ? 'disabled readonly' : '' }}
                                class="{{ $isPriceLocked ? 'bg-black/10 opacity-60 cursor-not-allowed border-gray-600' : 'bg-black/30 border-emerald-700/50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500' }} w-full rounded-lg border px-3 py-2 text-white placeholder-gray-400"
                                placeholder="{{ __('egi.crud.price_placeholder') }}">
                            <span
                                class="{{ $isPriceLocked ? 'text-gray-500' : 'text-gray-400' }} absolute right-3 top-2 text-sm">ALGO</span>
                            @if ($isPriceLocked)
                                <div class="absolute inset-0 flex items-center justify-center rounded-lg bg-black/20">
                                    <svg class="h-6 w-6 text-yellow-500 opacity-80" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="{{ $isPriceLocked ? 'text-yellow-400' : 'text-gray-400' }} mt-1 text-xs">
                            @if ($isPriceLocked)
                                🔒 {{ __('egi.crud.price_locked_message') }}
                            @else
                                {{ __('egi.crud.price_hint') }} (Prezzo base in ALGO)
                            @endif
                        </div>
                    </div>

                    {{-- Sale Mode Selector --}}
                    <div>
                        <label for="sale_mode" class="mb-2 block text-sm font-medium text-emerald-300">
                            {{ __('egi.crud.sale_mode') }}
                        </label>
                        <select id="sale_mode" name="sale_mode"
                            class="w-full rounded-lg border border-emerald-700/50 bg-black/30 px-3 py-2 text-white focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            @php $saleModeVal = old('sale_mode', $egi->sale_mode ?? 'not_for_sale'); @endphp
                            <option value="not_for_sale" {{ $saleModeVal === 'not_for_sale' ? 'selected' : '' }}>
                                {{ __('egi.crud.sale_mode_not_for_sale') }}</option>
                            <option value="fixed_price" {{ $saleModeVal === 'fixed_price' ? 'selected' : '' }}>
                                {{ __('egi.crud.sale_mode_fixed_price') }}</option>
                            <option value="auction" {{ $saleModeVal === 'auction' ? 'selected' : '' }}>
                                {{ __('egi.crud.sale_mode_auction') }}</option>
                        </select>
                        <div class="mt-1 text-xs text-gray-400">
                            {{ __('egi.crud.sale_mode_hint') }}
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
                                <label for="auction_start" class="mb-1 block text-xs font-medium text-emerald-300">
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
                                <label for="auction_end" class="mb-1 block text-xs font-medium text-emerald-300">
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

                    {{-- Creation Date Field --}}
                    <div class="{{ $egi->token_EGI ? 'opacity-50 pointer-events-none' : '' }}">
                        <label for="creation_date" class="mb-2 block text-sm font-medium text-emerald-300">
                            {{ __('egi.crud.creation_date') }}
                            @if ($egi->token_EGI)
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
                            {{ $egi->token_EGI ? 'disabled readonly' : '' }}
                            class="{{ $egi->token_EGI ? 'bg-black/10 cursor-not-allowed border-gray-600' : 'bg-black/30 border-emerald-700/50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500' }} w-full rounded-lg border px-3 py-2 text-white">
                        <div class="{{ $egi->token_EGI ? 'text-amber-400' : 'text-gray-400' }} mt-1 text-xs">
                            @if ($egi->token_EGI)
                                🔒 {{ __('egi.crud.field_immutable_hint') }}
                            @else
                                {{ __('egi.crud.creation_date_hint') }}
                            @endif
                        </div>
                    </div>

                    {{-- Published Toggle --}}
                    <div class="{{ $egi->token_EGI ? 'opacity-50 pointer-events-none' : '' }}">
                        <label class="flex items-center">
                            <input type="hidden" name="is_published" value="0">
                            <input type="checkbox" id="is_published" name="is_published" value="1"
                                {{ old('is_published', $egi->is_published) ? 'checked' : '' }}
                                {{ $egi->token_EGI ? 'disabled' : '' }}
                                class="{{ $egi->token_EGI ? 'bg-black/10 border-gray-600 cursor-not-allowed' : 'bg-black/30 border-emerald-700/50 focus:ring-emerald-500 focus:ring-2' }} h-4 w-4 rounded text-emerald-600">
                            <span class="ml-3 text-sm font-medium text-emerald-300">
                                {{ __('egi.crud.is_published') }}
                                @if ($egi->token_EGI)
                                    <svg class="ml-1 inline h-4 w-4 text-amber-400" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                @endif
                            </span>
                        </label>
                        <div class="{{ $egi->token_EGI ? 'text-amber-400' : 'text-gray-400' }} ml-7 mt-1 text-xs">
                            @if ($egi->token_EGI)
                                🔒 {{ __('egi.crud.field_immutable_hint') }}
                            @else
                                {{ __('egi.crud.is_published_hint') }}
                            @endif
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
            </div>
        </div>
    </div>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('egi-edit-form');
        if (!form) return;

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(form);
            const submitButton = form.querySelector('button[type="submit"]');

            // Convert FormData to object for JSON
            const formObject = {};
            formData.forEach((value, key) => {
                formObject[key] = value;
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
