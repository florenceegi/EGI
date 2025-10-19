{{-- resources/views/components/egi-card-list.blade.php --}}
{{-- 📜 EGI List Card Component --}}
{{-- Displays a single EGI in horizontal list format --}}
{{-- Reusable for Collector, Creator, and Patron portfolios --}}

@props([
    'egi',
    'context' => 'collector', // 'collector', 'creator', 'patron', 'collection'
    'portfolioOwner' => null,
    'showPurchasePrice' => true,
    'showOwnershipBadge' => true,
    'showBadge' => null, // Override per nascondere badge se necessario
])

@php
    // 🔥 HYPER MODE: Leggiamo direttamente dal database il campo hyper dell'EGI
$isHyper = $egi->hyper ?? false;

// Context-specific configurations
$contextConfig = [
    'collector' => [
        'badge_color' => 'bg-green-500',
        'badge_icon' => 'check',
        'badge_title' => __('collector.portfolio.owned'),
        'show_purchase' => true,
        'show_creator' => true,
    ],
    'creator' => [
        'badge_color' => 'bg-blue-500',
        'badge_icon' => 'palette',
        'badge_title' => __('creator.portfolio.created'),
        'show_purchase' => false,
        'show_creator' => false,
    ],
    'patron' => [
        'badge_color' => 'bg-purple-500',
        'badge_icon' => 'heart',
        'badge_title' => __('patron.portfolio.supported'),
        'show_purchase' => true,
        'show_creator' => true,
    ],
    'collection' => [
        'badge_color' => 'bg-indigo-500',
        'badge_icon' => 'collections',
        'badge_title' => __('collection.show.from_collection'),
        'show_purchase' => false,
        'show_creator' => true,
    ],
];

$config = $contextConfig[$context] ?? $contextConfig['collector'];

// Controllo se l'utente loggato è il creator dell'EGI
    $isCreator = auth()->check() && auth()->id() === $egi->user_id;

    // Badge logic - può essere sovrascritto dal parametro showBadge
    $showBadge = $showBadge ?? $showOwnershipBadge;
@endphp

{{-- Include CSS hyper se necessario --}}
@if ($isHyper)
    @once
        <link rel="stylesheet" href="{{ asset('css/egi-hyper.css') }}">
        <style>
            .egi-hyper-badge-small {
                background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 50%, #d97706 100%);
                color: white;
                font-size: 0.625rem;
                font-weight: bold;
                padding: 2px 6px;
                border-radius: 6px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
                animation: hyperPulse 2s infinite;
                white-space: nowrap;
            }

            @keyframes hyperPulse {

                0%,
                100% {
                    transform: scale(1);
                }

                50% {
                    transform: scale(1.05);
                }
            }
        </style>
    @endonce
@endif

{{-- EGI Card List Component --}}
<article
    class="egi-card-list {{ $isHyper ? 'egi-card--hiper' : '' }} group relative rounded-xl border border-gray-700/50 bg-gray-800/50 p-3 transition-all duration-300 hover:border-gray-600 hover:bg-gray-800/70"
    data-egi-id="{{ $egi->id }}" data-hyper="{{ $isHyper ? '1' : '0' }}"
    style="{{ $isHyper ? '--energy:0.95; --foilHue:265; --edge:#9b5cf6; --accent:#a78bfa;' : '' }}">

    @if ($isHyper)
        {{-- Sparkles Effect per HYPER --}}
        <div class="egi-sparkles" aria-hidden="true"></div>

        {{-- Badge HYPER per modalità lista --}}
        <div class="absolute -right-2 -top-2 z-10">
            <div class="egi-hyper-badge-small">⭐ HYPER ⭐</div>
        </div>
    @endif

    <div class="flex items-start gap-4">
        <!-- Image Section -->
        <a href="{{ route('egis.show', $egi->id) }}"
            class="{{ $isHyper ? 'group-hover:ring-2 group-hover:ring-yellow-400' : 'group-hover:ring-2 group-hover:ring-purple-400' }} relative h-28 w-28 flex-shrink-0 cursor-pointer overflow-hidden rounded-lg bg-gradient-to-br from-gray-700 to-gray-800 transition-all duration-300">
            @if ($egi->main_image_url)
                <img src="{{ $egi->main_image_url }}" alt="{{ $egi->title }}"
                    class="{{ $isHyper ? 'filter brightness-110' : '' }} h-full w-full object-cover transition-transform duration-300 group-hover:scale-110">
            @else
                <div
                    class="{{ $isHyper ? 'from-yellow-600/20 to-orange-600/20' : 'from-purple-600/20 to-blue-600/20' }} flex h-full w-full items-center justify-center bg-gradient-to-br">
                    <svg class="{{ $isHyper ? 'text-yellow-400' : 'text-gray-400' }} h-8 w-8" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            @endif

            <!-- Hover overlay for visual feedback -->
            <div
                class="{{ $isHyper ? 'bg-yellow-400/20' : 'bg-black/20' }} absolute inset-0 flex items-center justify-center opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                <svg class="{{ $isHyper ? 'text-yellow-300' : 'text-white' }} h-6 w-6" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M13.5 6H5.25A2.25 2.25 0 003 8.25v7.5A2.25 2.25 0 005.25 18h7.5A2.25 2.25 0 0015 15.75v-7.5A2.25 2.25 0 0013.5 6z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 9 3.75 3.75-3.75 3.75" />
                </svg>
            </div>

            {{-- Badge Categoria (top-left) --}}
            @php
                $categoryName = $egi->category_name;
                $categoryClasses = $egi->category_badge_classes;
            @endphp
            <div class="absolute left-2 top-2">
                <span
                    class="{{ $categoryClasses }} inline-flex items-center rounded-md px-2 py-0.5 text-[10px] font-semibold leading-none shadow ring-1 ring-white/10"
                    title="{{ $categoryName }}" aria-label="EGI Category: {{ $categoryName }}">
                    {{ Str::limit($categoryName, 16) }}
                </span>
            </div>

            {{-- 🎯 Badge Asta (bottom-left) --}}
            @if (!$egi->isMinted() && $egi->sale_mode === 'auction')
                <div class="absolute bottom-2 left-2">
                    <span
                        class="inline-flex items-center rounded-md bg-gradient-to-r from-amber-500 to-orange-500 px-2 py-0.5 text-[10px] font-bold text-white shadow-lg ring-1 ring-white/20"
                        title="{{ __('egi.auction.to_mint') }}">
                        {{ __('egi.auction.to_mint') }}
                    </span>
                </div>
            @endif

            <!-- Context Badge -->
            @if ($showOwnershipBadge)
                <div class="absolute right-2 top-12">
                    <div class="{{ $config['badge_color'] }} {{ $isHyper ? 'animate-pulse' : '' }} flex h-6 w-6 items-center justify-center rounded-full ring-2 ring-gray-800"
                        title="{{ $config['badge_title'] }}">
                        @if ($config['badge_icon'] === 'check')
                            <svg class="h-3 w-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        @elseif ($config['badge_icon'] === 'palette')
                            <svg class="h-3 w-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M4 2a2 2 0 00-2 2v11a2 2 0 002 2h4a2 2 0 002-2V4a2 2 0 00-2-2H4zm0 2h4v11H4V4zm8-2a2 2 0 00-2 2v11a2 2 0 002 2h4a2 2 0 002-2V4a2 2 0 00-2-2h-4zm0 2h4v11h-4V4z"
                                    clip-rule="evenodd" />
                            </svg>
                        @elseif ($config['badge_icon'] === 'heart')
                            <svg class="h-3 w-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                                    clip-rule="evenodd" />
                            </svg>
                        @elseif ($config['badge_icon'] === 'collections')
                            <svg class="h-3 w-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        @endif
                    </div>
                </div>
            @endif
        </a>

        <!-- Content Section -->
        <div class="mr-4 min-w-0 flex-1">
            <!-- Title and Like Button -->
            <div class="mb-1 flex items-start justify-between">
                <h3
                    class="{{ $isHyper ? 'group-hover:text-yellow-300' : 'group-hover:text-purple-300' }} flex-1 truncate text-lg font-bold text-white transition-colors">
                    <a href="{{ route('egis.show', $egi->id) }}" class="hover:underline">
                        {{ $egi->title ?? '#' . $egi->id }}
                    </a>
                </h3>

                @if (!$isCreator)
                    <div class="ml-2 flex-shrink-0">
                        <x-like-button :resourceType="'egi'" :resourceId="$egi->id" :isLiked="$egi->is_liked ?? false" :likesCount="$egi->likes_count ?? 0"
                            size="small" />
                    </div>
                @endif
            </div>

            <!-- Collection and Creator Info -->
            <div class="mb-2 flex flex-wrap items-center gap-4 text-sm text-gray-400">
                @if ($egi->collection)
                    <div class="flex items-center gap-1">
                        @php
                            $collectionImageUrl = '';
                            if (method_exists($egi->collection, 'getFirstMediaUrl')) {
                                $collectionImageUrl = $egi->collection->getFirstMediaUrl('head', 'card');
                            }
                        @endphp
                        @if ($collectionImageUrl)
                            <img src="{{ $collectionImageUrl }}" alt="{{ $egi->collection->collection_name }}"
                                class="h-3 w-3 rounded-full object-cover transition-transform duration-300 hover:scale-110"
                                loading="lazy" decoding="async">
                        @else
                            <div
                                class="{{ $isHyper ? 'bg-gradient-to-r from-yellow-400 to-yellow-600' : 'bg-gradient-to-r from-purple-500 to-blue-500' }} h-3 w-3 rounded-full">
                            </div>
                        @endif
                        <a href="{{ route('home.collections.show', $egi->collection->id) }}"
                            class="{{ $isHyper ? 'hover:text-yellow-400' : 'hover:text-purple-400' }} max-w-[120px] truncate transition-colors">
                            {{ $egi->collection->collection_name }}
                        </a>
                    </div>
                @endif

                @if ($config['show_creator'] && $egi->user)
                    <div class="flex items-center gap-1">
                        <div class="{{ $isHyper ? 'bg-yellow-600' : 'bg-gray-600' }} h-3 w-3 rounded-full"></div>
                        <span class="max-w-[100px] truncate">{{ $egi->user->first_name }}
                            {{ $egi->user->last_name }}</span>
                    </div>
                @endif
            </div>

            <!-- Activator Info -->
            @if ($context === 'creator' || $context === 'collection')
                @php
                    // Find the current reservation (activated collector)
                    $currentReservation = $egi->reservations
                        ? $egi->reservations->where('is_current', true)->first()
                        : null;
                @endphp
                @if ($currentReservation && $currentReservation->user)
                    @php
                        // 🎯 Sistema Commissioner: Formattiamo le informazioni dell'attivatore
                        $activatorDisplay = formatActivatorDisplay($currentReservation->user);
                    @endphp
                    <div class="mb-1 flex items-center gap-2 text-sm" data-activation-status>
                        {{-- Avatar sempre presente dal backend (gestisce automaticamente la privacy) --}}
                        @if ($activatorDisplay['avatar'])
                            <img src="{{ $activatorDisplay['avatar'] }}" alt="{{ $activatorDisplay['name'] }}"
                                class="h-4 w-4 rounded-full border border-green-400/30 object-cover shadow-sm">
                        @else
                            {{-- Fallback solo se non c'è avatar dal backend (caso molto raro) --}}
                            <div class="flex h-4 w-4 items-center justify-center rounded-full bg-green-500 shadow-sm">
                                <svg class="h-2 w-2 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        @endif
                        <span class="font-medium text-green-300" data-activator-name>
                            {{ $activatorDisplay['name'] }}
                        </span>
                        <span class="text-xs text-gray-400">({{ __('egi.reservation.activator') }})</span>
                    </div>
                @elseif ($context === 'creator')
                    <div class="mb-1 flex items-center gap-2 text-sm" data-activation-status="available">
                        <svg class="h-4 w-4 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="text-sm font-medium text-green-300">{{ __('egi.not_currently_listed') }}</span>
                    </div>
                @endif
            @endif

            <!-- Base Price Info -->
            @if ($egi->price)
                @php
                    // 🚀 FIX: Calcola il prezzo da mostrare basato sulla prenotazione più alta
                    $reservationService = app('App\Services\ReservationService');
                    $highestPriorityReservation = $reservationService->getHighestPriorityReservation($egi);
                    $displayPriceEur = $egi->price; // Default al prezzo base

                    // Se c'è una prenotazione attiva, usa il suo prezzo EUR
if ($highestPriorityReservation && $highestPriorityReservation->status === 'active') {
    $displayPriceEur = $highestPriorityReservation->offer_amount_fiat;

    // 🎯 EUR-ONLY SYSTEM: Convertiamo in EUR se necessario
    if ($highestPriorityReservation->fiat_currency !== 'EUR') {
        $displayPriceEur = $highestPriorityReservation->amount_eur ?? $displayPriceEur;
    }
}
// Altrimenti usa il prezzo base dell'EGI (sempre in EUR)

                    // Controlla se c'è una prenotazione corrente per mostrare il pulsante Rilancia
$hasCurrentReservation =
    ($context === 'creator' || $context === 'collection') &&
    $egi->reservations &&
    $egi->reservations->where('is_current', true)->first();
                @endphp
                <div class="mb-1 flex items-center justify-between gap-2 text-sm">
                    <div class="flex items-center gap-2">
                        <svg class="{{ $isHyper ? 'text-yellow-400' : 'text-orange-400' }} h-4 w-4"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"
                                clip-rule="evenodd" />
                        </svg>
                        <div class="flex items-center gap-1">
                            <span class="{{ $isHyper ? 'text-yellow-300' : 'text-orange-300' }} font-bold">
                                <x-currency-price :price="$displayPriceEur" :egi="$egi" />
                            </span>
                            {{-- EUR-only system: il componente currency-price gestisce automaticamente le note --}}

                        </div>
                    </div>

                    {{-- RIMUOVO il pulsante Rilancia da qui perché va in fondo alla card --}}
                </div>
            @endif

            <!-- Purchase/Context Info -->
            @if ($config['show_purchase'] && $showPurchasePrice)
                @if ($context === 'collector' && $egi->pivot && $egi->pivot->offer_amount_fiat)
                    <div class="mt-1 flex items-center gap-2 text-sm">
                        <span class="text-gray-400">{{ __('collector.portfolio.purchased_for') }}</span>
                        <span class="{{ $isHyper ? 'text-yellow-400' : 'text-green-400' }} font-bold">
                            <x-currency-price :price="$egi->pivot->offer_amount_fiat" :egi="$egi" />
                        </span>
                    </div>
                @elseif ($context === 'patron' && isset($egi->support_amount))
                    <div class="mt-1 flex items-center gap-2 text-sm">
                        <span class="text-gray-400">{{ __('patron.portfolio.supported_for') }}</span>
                        <span class="{{ $isHyper ? 'text-yellow-300' : 'text-yellow-400' }} font-bold">
                            <x-currency-price :price="$egi->support_amount" />
                        </span>
                    </div>
                @endif
            @endif
        </div>
    </div>

    {{-- � PHASE 2: DUAL PATH BUTTONS (Mint Direct OR Reserve) --}}
    @if (!$isCreator)
        @php
            // Use EgiAvailabilityService for comprehensive availability check
            $availabilityService = app(\App\Services\EgiAvailabilityService::class);
            $availability = $availabilityService->checkAvailability($egi, auth()->user());

            $canMint = $availability['can_mint'];
            $canReserve = $availability['can_reserve'];
            $isReservedByUser = $availability['is_reserved_by_user'];
            $recommendedAction = $availability['recommended_action'];
            $availableActions = $availability['available_actions'];

            // Get user reservation if exists
            $userReservation = null;
            if (auth()->check() && $egi->reservations) {
                $userReservation = $egi->reservations
                    ->where('user_id', auth()->id())
                    ->where('is_current', true)
                    ->where('status', 'active')
                    ->first();
            }

            // Price for display
            $displayPriceForAction = $egi->price ?? 0;

            // 🚨 CRITICAL: Hide buttons if price is zero (EGI not available)
            $showButtons = $displayPriceForAction > 0;
        @endphp

        <div class="mt-3">
            @if ($showButtons && count($availableActions) > 0)
                {{-- ✅ SCENARIO 1: User has reservation → Show MINT button (complete purchase) - VIOLA --}}
                @if ($isReservedByUser && $canMint && $userReservation)
                    <a href="{{ route('mint.payment-form', ['egiId' => $egi->id]) }}?reservation_id={{ $userReservation->id }}"
                        class="mint-button flex w-full transform items-center justify-center rounded-lg bg-gradient-to-r from-[#8E44AD] to-[#9b59b6] px-4 py-2 text-sm font-bold text-white shadow-lg transition-all hover:scale-[1.01] hover:from-[#7d3c98] hover:to-[#8e44ad]">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m-3-6h6" />
                        </svg>
                        {{ __('egi.actions.mint_now') }} · €{{ number_format($displayPriceForAction, 2, ',', '.') }}
                    </a>

                    {{-- ✅ SCENARIO 2: Both MINT and RESERVE available (dual path) → Show both buttons --}}
                @elseif($canMint && $canReserve)
                    <div class="grid grid-cols-2 gap-2">
                        {{-- Reserve Button (left) - ARANCIONE --}}
                        <button type="button"
                            class="reserve-button flex transform items-center justify-center rounded-lg bg-gradient-to-r from-[#E67E22] to-[#d35400] px-3 py-2 text-xs font-medium text-white transition-all hover:scale-[1.02] hover:from-[#d35400] hover:to-[#ba4a00]"
                            data-egi-id="{{ $egi->id }}">
                            <svg class="mr-1.5 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            {{ __('egi.actions.reserve') }}
                        </button>

                        {{-- Mint Direct Button (right, emphasized) - VIOLA --}}
                        <a href="{{ route('egi.mint-direct', $egi->id) }}"
                            class="mint-direct-button flex transform items-center justify-center rounded-lg bg-gradient-to-r from-[#8E44AD] to-[#9b59b6] px-3 py-2 text-xs font-bold text-white shadow-md transition-all hover:scale-[1.02] hover:from-[#7d3c98] hover:to-[#8e44ad]">
                            <svg class="mr-1.5 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            {{ __('egi.actions.mint_direct') }}
                        </a>
                    </div>

                    {{-- ✅ SCENARIO 3: Only MINT available (no reservation possible) - VIOLA --}}
                @elseif($canMint && !$canReserve)
                    <a href="{{ route('egi.mint-direct', $egi->id) }}"
                        class="mint-direct-button flex w-full transform items-center justify-center rounded-lg bg-gradient-to-r from-[#8E44AD] to-[#9b59b6] px-4 py-2 text-sm font-bold text-white shadow-lg transition-all hover:scale-[1.01] hover:from-[#7d3c98] hover:to-[#8e44ad]">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        {{ __('egi.actions.mint_direct') }} ·
                        €{{ number_format($displayPriceForAction, 2, ',', '.') }}
                    </a>

                    {{-- ✅ SCENARIO 4: Only RESERVE available (already reserved by others or user can only reserve) - ARANCIONE --}}
                @elseif(!$canMint && $canReserve)
                    <button type="button"
                        class="reserve-button flex w-full transform items-center justify-center rounded-lg bg-gradient-to-r from-[#E67E22] to-[#d35400] px-4 py-2 text-sm font-medium text-white transition-all hover:scale-[1.01] hover:from-[#d35400] hover:to-[#ba4a00]"
                        data-egi-id="{{ $egi->id }}">
                        @if ($hasCurrentReservation)
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                            {{ __('egi.actions.outbid') }}
                        @else
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            {{ __('egi.actions.reserve') }}
                        @endif
                    </button>
                @endif
            @else
                {{-- ❌ No actions available (or price = 0) --}}
                @if ($egi->isMinted())
                    {{-- EGI già mintato - Link cliccabile per visualizzare dettagli mint --}}
                    <a href="{{ route('egi.mint-direct', $egi->id) }}"
                        class="flex w-full items-center justify-center rounded-lg bg-green-50 px-4 py-2 text-sm font-medium text-green-700 transition-colors hover:bg-green-100">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ __('egi.status.view_mint_details') ?? 'Vedi Mint' }}
                    </a>
                @else
                    {{-- Stato non disponibile --}}
                    <div
                        class="flex w-full items-center justify-center rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-500">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                        @if (!$showButtons)
                            {{ __('egi.crud.price_not_set') }}
                        @elseif (!auth()->check())
                            {{ __('egi.status.login_required') ?? 'Login richiesto' }}
                        @else
                            {{ __('egi.status.not_available') ?? 'Non disponibile' }}
                        @endif
                    </div>
                @endif
            @endif
        </div>
    @endif
</article>
