{{-- resources/views/egis/partials/sidebar/price-purchase-section.blade.php --}}
{{--
    Sezione prezzo e acquisto
    ORIGINE: righe 139-271 di show.blade.php (Price & Purchase Section)
    VARIABILI: $egi, $isForSale, $displayPrice, $priceLabel, $displayUser, $highestPriorityReservation, $isCreator, $canBeReserved
--}}

{{-- Price & Purchase Section --}}
<div
    class="p-6 border bg-gradient-to-br from-gray-800/50 to-gray-900/50 rounded-xl border-gray-700/30">
    @if($isForSale)
    <div class="mb-6 text-center">
        <p class="mb-2 text-sm text-gray-400">{{ $priceLabel }}</p>
        <div class="flex items-baseline justify-center">
            <x-currency-price :price="$displayPrice"
                :egi="$egi"
                :reservation="$highestPriorityReservation"
                class="text-4xl font-bold text-white" :show-algo-conversion="true" />
            <span class="ml-2 text-lg font-medium text-gray-400">EUR</span>
        </div>

        {{-- Miglior offerente (STRONG vs WEAK) --}}
        @if($displayUser || $highestPriorityReservation)
        @php
            $isWeakReservation = $highestPriorityReservation &&
            $highestPriorityReservation->type === 'weak';
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
            class="flex items-center justify-center gap-2 mt-3 p-2 {{ $bgColor }} border {{ $borderColor }} rounded-lg">
            <div
                class="flex items-center justify-center flex-shrink-0 w-5 h-5 {{ $iconBg }} rounded-full">
                @if ($isWeakReservation)
                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"
                            clip-rule="evenodd" />
                    </svg>
                @else
                    {{-- Check if commissioner has avatar --}}
                    @if ($activatorDisplayTop && $activatorDisplayTop['is_commissioner'] &&
                        $activatorDisplayTop['avatar'])
                        <img src="{{ $activatorDisplayTop['avatar'] }}"
                            alt="{{ $activatorDisplayTop['name'] }}"
                            class="object-cover w-5 h-5 rounded-full">
                    @else
                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                clip-rule="evenodd" />
                        </svg>
                    @endif
                @endif
            </div>
            <span class="text-sm {{ $textColor }}">
                @if ($isWeakReservation)
                {{ __('egi.reservation.weak_bidder') }}: <span
                    class="font-semibold text-white">{{
                    $highestPriorityReservation->fegi_code ?? 'FG#******' }}</span>
                @else
                {{ __('egi.reservation.strong_bidder') }}: <span
                    class="font-semibold text-white">{{ $activatorDisplayTop['name']
                    }}</span>
                @endif
            </span>
        </div>
        @endif
    </div>
    @else
    <div class="mb-6 text-center">
        @if($egi->price && $egi->price > 0)
        <p class="text-lg font-semibold text-gray-300">{{ __('egi.not_currently_listed') }}
        </p>
        <p class="mt-1 text-sm text-gray-500">{{ __('egi.contact_owner_availability') }}</p>
        @else
        <p class="text-lg font-semibold text-gray-300">{{ __('egi.not_for_sale') }}</p>
        <p class="mt-1 text-sm text-gray-500">{{ __('egi.not_for_sale_description') }}</p>
        @endif
    </div>
    @endif

    {{-- Main Action Buttons --}}
    <div class="space-y-3">
        {{-- Like Button - Full Version OR Likes Received Widget for Creator --}}
        @if(!$isCreator)
            <button
                class="w-full inline-flex items-center justify-center px-6 py-4 bg-gradient-to-r from-pink-600/80 to-purple-600/80 hover:from-pink-600 hover:to-purple-600 backdrop-blur-sm text-white font-medium rounded-lg transition-all duration-200 border border-pink-500/30 hover:border-pink-400/50 like-button {{ $egi->is_liked ?? false ? 'is-liked ring-2 ring-pink-400/50' : '' }}"
                data-resource-type="egi" data-resource-id="{{ $egi->id }}">
                <svg class="-ml-1 mr-3 h-5 w-5 icon-heart {{ $egi->is_liked ?? false ? 'text-pink-300' : 'text-white' }}"
                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M3.172 5.172a4 4 0 0 1 5.656 0L10 6.343l1.172-1.171a4 4 0 1 1 5.656 5.656L10 17.657l-6.828-6.829a4 4 0 0 1 0-5.656Z"
                        clip-rule="evenodd" />
                </svg>
                <span class="like-text">{{ $egi->is_liked ?? false ? __('egi.liked') :
                    __('egi.add_to_favorites') }}</span>
                <span
                    class="ml-2 bg-white/20 px-2 py-0.5 rounded-full text-xs like-count-display">{{
                    $egi->likes_count ?? 0 }}</span>
            </button>
        @else
            {{-- EGI Likes Received Widget - Only for the creator --}}
            <x-stats.egi-likes-received-widget :egi-id="$egi->id" />
        @endif

        {{-- Reserve Button --}}
        @if($canBeReserved && $egi->price && $egi->price > 0)
        <button
            class="inline-flex items-center justify-center w-full px-6 py-4 font-medium text-white transition-all duration-200 border rounded-lg bg-gradient-to-r from-emerald-600/80 to-teal-600/80 hover:from-emerald-600 hover:to-teal-600 backdrop-blur-sm border-emerald-500/30 hover:border-emerald-400/50 reserve-button"
            data-egi-id="{{ $egi->id }}">
            <svg class="w-5 h-5 mr-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                fill="currentColor">
                <path fill-rule="evenodd"
                    d="M4.25 2A1.75 1.75 0 0 0 2.5 3.75v14.5a.75.75 0 0 0 1.218.582l5.534-4.426a.75.75 0 0 1 .496 0l5.534 4.427A.75.75 0 0 0 17.5 18.25V3.75A1.75 1.75 0 0 0 15.75 2h-11.5Z"
                    clip-rule="evenodd" />
            </svg>
            {{ __('egi.reserve_this_piece') }}
        </button>
        @else
        {{-- Non in vendita - Messaggio informativo --}}
        {{-- <div
            class="inline-flex items-center justify-center w-full px-6 py-4 font-medium text-gray-500 transition-all duration-200 bg-gray-100 border border-gray-300 rounded-lg">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            {{ __('egi.not_for_sale') }}
        </div> --}}
        @endif
    </div>
</div>
