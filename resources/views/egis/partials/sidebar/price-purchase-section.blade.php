{{-- resources/views/egis/partials/sidebar/price-purchase-section.blade.php --}}
{{--
    Sezione prezzo e acquisto
    ORIGINE: righe 139-271 di show.blade.php (Price & Purchase Section)
    VARIABILI: $egi, $isForSale, $displayPrice, $priceLabel, $displayUser, $highestPriorityReservation, $isCreator, $canBeReserved
--}}

{{-- 🚀 MINT DETAILS BUTTON - ALWAYS VISIBLE (guest, auth, creator) - COMPLETELY STANDALONE --}}
@php
    // Get blockchain ID from new blockchain integration system
    // Only show button for EGIs minted via new system (with egi_blockchain record)
    $blockchainId = optional($egi->blockchain)->id;
@endphp

@if ($blockchainId)
    <div class="mb-3 sm:mb-4">
        <a href="{{ route('mint.show', $blockchainId) }}"
            class="inline-flex w-full items-center justify-center rounded-lg border border-green-600/30 bg-green-700/20 px-4 py-3 text-sm font-medium text-green-400 backdrop-blur-sm transition-all hover:border-green-500/50 hover:bg-green-600/30 hover:text-green-300 sm:px-5 sm:py-3.5 sm:text-base md:px-6 md:py-4">
            <svg class="mr-2 h-4 w-4 sm:mr-3 sm:h-5 sm:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ __('egi.status.view_mint_details') ?? 'Visualizza Dettagli Mint' }}
        </a>
    </div>
@endif

{{-- Price & Purchase Section - Responsive --}}
<div
    class="rounded-lg border border-gray-700/30 bg-gradient-to-br from-gray-800/50 to-gray-900/50 p-4 sm:rounded-xl sm:p-5 md:p-6">
    @if ($isForSale)
        <div class="mb-4 text-center sm:mb-5 md:mb-6">
            <p class="mb-1.5 text-xs text-gray-400 sm:mb-2 sm:text-sm">{{ $priceLabel }}</p>
            <div class="flex items-baseline justify-center">
                <x-currency-price :price="$displayPrice" :egi="$egi" :reservation="$highestPriorityReservation"
                    class="text-2xl font-bold text-white sm:text-3xl md:text-4xl" :show-algo-conversion="true" />
                <span class="ml-1.5 text-base font-medium text-gray-400 sm:ml-2 sm:text-lg">EUR</span>
            </div>

            {{-- Miglior offerente (STRONG vs WEAK) --}}
            @if ($displayUser || $highestPriorityReservation)
                @php
                    $isWeakReservation = $highestPriorityReservation && $highestPriorityReservation->type === 'weak';
                    $bgColor = $isWeakReservation ? 'bg-amber-500/10' : 'bg-emerald-500/10';
                    $borderColor = $isWeakReservation ? 'border-amber-500/20' : 'border-emerald-500/20';
                    $iconBg = $isWeakReservation ? 'bg-amber-500' : 'bg-emerald-500';
                    $textColor = $isWeakReservation ? 'text-amber-300' : 'text-emerald-300';

                    // Prepare activator display for both icon and text
                    $activatorDisplayTop = null;
                    if ($displayUser && !$isWeakReservation) {
                        $activatorDisplayTop = formatActivatorDisplay($displayUser);
                    }
                @endphp

                <div
                    class="{{ $bgColor }} {{ $borderColor }} mt-2 flex items-center justify-center gap-1.5 rounded-md border p-1.5 sm:mt-3 sm:gap-2 sm:rounded-lg sm:p-2">
                    <div
                        class="{{ $iconBg }} flex h-4 w-4 flex-shrink-0 items-center justify-center rounded-full sm:h-5 sm:w-5">
                        @if ($isWeakReservation)
                            <svg class="h-3 w-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"
                                    clip-rule="evenodd" />
                            </svg>
                        @else
                            {{-- Check if commissioner has avatar --}}
                            @if ($activatorDisplayTop && $activatorDisplayTop['is_commissioner'] && $activatorDisplayTop['avatar'])
                                <img src="{{ $activatorDisplayTop['avatar'] }}" alt="{{ $activatorDisplayTop['name'] }}"
                                    class="h-5 w-5 rounded-full object-cover">
                            @else
                                <svg class="h-3 w-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                        clip-rule="evenodd" />
                                </svg>
                            @endif
                        @endif
                    </div>
                    <span class="{{ $textColor }} text-xs sm:text-sm">
                        @if ($isWeakReservation)
                            {{ __('egi.reservation.weak_bidder') }}: <span
                                class="font-semibold text-white">{{ $highestPriorityReservation->fegi_code ?? 'FG#******' }}</span>
                        @else
                            {{ __('egi.reservation.strong_bidder') }}: <span
                                class="inline-block max-w-[150px] truncate font-semibold text-white">{{ $activatorDisplayTop['name'] }}</span>
                        @endif
                    </span>
                </div>
            @endif
        </div>
    @else
        <div class="mb-4 text-center sm:mb-5 md:mb-6">
            @if ($egi->price && $egi->price > 0)
                <p class="text-base font-semibold text-gray-300 sm:text-lg">{{ __('egi.not_currently_listed') }}</p>
                <p class="mt-1 text-xs text-gray-500 sm:text-sm">{{ __('egi.contact_owner_availability') }}</p>
            @else
                <p class="text-base font-semibold text-gray-300 sm:text-lg">{{ __('egi.not_for_sale') }}</p>
                <p class="mt-1 text-xs text-gray-500 sm:text-sm">{{ __('egi.not_for_sale_description') }}</p>
            @endif
        </div>
    @endif

    {{-- Main Action Buttons --}}
    <div class="space-y-3">

        {{-- Like Button - Full Version OR Likes Received Widget for Creator --}}
        @if (!$isCreator)
            <button
                class="like-button {{ $egi->is_liked ?? false ? 'is-liked ring-2 ring-pink-400/50' : '' }} inline-flex w-full items-center justify-center rounded-lg border border-pink-500/30 bg-gradient-to-r from-pink-600/80 to-purple-600/80 px-6 py-4 font-medium text-white backdrop-blur-sm transition-all duration-200 hover:border-pink-400/50 hover:from-pink-600 hover:to-purple-600"
                data-resource-type="egi" data-resource-id="{{ $egi->id }}">
                <svg class="icon-heart {{ $egi->is_liked ?? false ? 'text-pink-300' : 'text-white' }} -ml-1 mr-3 h-5 w-5"
                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M3.172 5.172a4 4 0 0 1 5.656 0L10 6.343l1.172-1.171a4 4 0 1 1 5.656 5.656L10 17.657l-6.828-6.829a4 4 0 0 1 0-5.656Z"
                        clip-rule="evenodd" />
                </svg>
                <span
                    class="like-text">{{ $egi->is_liked ?? false ? __('egi.liked') : __('egi.add_to_favorites') }}</span>
                <span
                    class="like-count-display ml-2 rounded-full bg-white/20 px-2 py-0.5 text-xs">{{ $egi->likes_count ?? 0 }}</span>
            </button>
        @else
            {{-- EGI Likes Received Widget - Only for the creator --}}
            <x-stats.egi-likes-received-widget :egi-id="$egi->id" />
        @endif

        {{-- Dual Path Mint/Reserve Buttons (same logic as egi-card) --}}

        @if (!$blockchainId)
            @if (!$isCreator && auth()->check())
                @php
                    // EgiAvailabilityService integration
                    $availabilityService = app(\App\Services\EgiAvailabilityService::class);
                    $availability = $availabilityService->checkAvailability($egi, auth()->user());
                    $canMint = $availability['can_mint'];
                    $canReserve = $availability['can_reserve'];
                    $isReservedByUser = $availability['is_reserved_by_user'];
                    $availableActions = $availability['available_actions'];

                    // Get user's current reservation if exists
$userReservation =
    $egi->reservations && is_iterable($egi->reservations)
        ? $egi->reservations
            ->where('user_id', auth()->id())
            ->where('is_current', true)
            ->first()
        : null;

// Check if there are ANY current reservations (not just user's)
                    $hasCurrentReservation =
                        $egi->reservations && is_iterable($egi->reservations)
                            ? $egi->reservations->where('is_current', true)->count() > 0
                            : false;

                    $displayPriceForAction = $egi->price ?? 0;
                    $showButtons = $displayPriceForAction > 0;
                @endphp

                @if ($showButtons && count($availableActions) > 0)
                    {{-- ✅ SCENARIO 1: User has reservation → Show MINT button (complete purchase) - VIOLA --}}
                    @if ($isReservedByUser && $canMint && $userReservation)
                        <a href="{{ route('mint.payment-form', ['egiId' => $egi->id]) }}?reservation_id={{ $userReservation->id }}"
                            class="mint-button inline-flex w-full transform items-center justify-center rounded-lg border border-purple-500/30 bg-gradient-to-r from-[#8E44AD] to-[#9b59b6] px-6 py-4 font-medium text-white shadow-lg backdrop-blur-sm transition-all hover:scale-[1.02] hover:border-purple-400/50 hover:from-[#7d3c98] hover:to-[#8e44ad]">
                            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m-3-6h6" />
                            </svg>
                            {{ __('egi.actions.mint_now') }} ·
                            €{{ number_format($displayPriceForAction, 2, ',', '.') }}
                        </a>

                        {{-- SCENARIO 2: Both MINT and RESERVE available (dual path) → Show both buttons --}}
                    @elseif($canMint && $canReserve)
                        <div class="grid grid-cols-2 gap-3">
                            {{-- Reserve Button (left) - ARANCIONE --}}
                            <button type="button"
                                class="reserve-button inline-flex transform items-center justify-center rounded-lg border border-orange-500/30 bg-gradient-to-r from-[#E67E22] to-[#d35400] px-4 py-3 text-sm font-medium text-white backdrop-blur-sm transition-all hover:scale-[1.02] hover:border-orange-400/50 hover:from-[#d35400] hover:to-[#ba4a00]"
                                data-egi-id="{{ $egi->id }}">
                                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                {{ __('egi.actions.reserve') }}
                            </button>

                            {{-- Mint Direct Button (right, emphasized) - VIOLA --}}
                            <a href="{{ route('egi.mint-direct', $egi->id) }}"
                                class="mint-direct-button inline-flex transform items-center justify-center rounded-lg border border-purple-500/30 bg-gradient-to-r from-[#8E44AD] to-[#9b59b6] px-4 py-3 text-sm font-bold text-white shadow-md backdrop-blur-sm transition-all hover:scale-[1.02] hover:border-purple-400/50 hover:from-[#7d3c98] hover:to-[#8e44ad]">
                                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                {{ __('egi.actions.mint_direct') }}
                            </a>
                        </div>

                        {{-- SCENARIO 3: Only MINT available (no reservation possible) - VIOLA --}}
                    @elseif($canMint && !$canReserve)
                        <a href="{{ route('egi.mint-direct', $egi->id) }}"
                            class="mint-direct-button inline-flex w-full transform items-center justify-center rounded-lg border border-purple-500/30 bg-gradient-to-r from-[#8E44AD] to-[#9b59b6] px-6 py-4 font-medium text-white shadow-lg backdrop-blur-sm transition-all hover:scale-[1.02] hover:border-purple-400/50 hover:from-[#7d3c98] hover:to-[#8e44ad]">
                            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            {{ __('egi.actions.mint_direct') }} ·
                            €{{ number_format($displayPriceForAction, 2, ',', '.') }}
                        </a>

                        {{-- SCENARIO 4: Only RESERVE available (already reserved by others or user can only reserve) - ARANCIONE --}}
                    @elseif(!$canMint && $canReserve)
                        <button type="button"
                            class="reserve-button inline-flex w-full transform items-center justify-center rounded-lg border border-orange-500/30 bg-gradient-to-r from-[#E67E22] to-[#d35400] px-6 py-4 font-medium text-white backdrop-blur-sm transition-all hover:scale-[1.02] hover:border-orange-400/50 hover:from-[#d35400] hover:to-[#ba4a00]"
                            data-egi-id="{{ $egi->id }}">
                            @if ($hasCurrentReservation)
                                <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                                {{ __('egi.actions.outbid') }}
                            @else
                                <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                {{ __('egi.actions.reserve') }}
                            @endif
                        </button>
                    @endif
                @elseif(!auth()->check())
                    {{-- User not logged in (azioni disponibili solo dopo login) --}}
                    <div
                        class="inline-flex w-full items-center justify-center rounded-lg border border-gray-600/30 bg-gray-700/50 px-6 py-4 font-medium text-gray-400 backdrop-blur-sm">
                        <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        {{ __('egi.status.login_required') ?? 'Login richiesto' }}
                    </div>
                @else
                    {{-- Stato non disponibile o prezzo non impostato --}}
                    <div
                        class="inline-flex w-full items-center justify-center rounded-lg border border-gray-600/30 bg-gray-700/50 px-6 py-4 font-medium text-gray-400 backdrop-blur-sm">
                        <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                        @if (!$showButtons)
                            {{ __('egi.crud.price_not_set') }}
                        @else
                            {{ __('egi.status.not_available') ?? 'Non disponibile' }}
                        @endif
                    </div>
                @endif
            @endif
        @endif
    </div>
</div>
