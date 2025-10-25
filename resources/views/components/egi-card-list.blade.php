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

// 🎯 BADGE STATUS LOGIC (same as egi-card)
$isMinted = $egi->isMinted();
$hasActiveReservations = $egi->reservations
    ? $egi->reservations->where('is_current', true)->where('status', 'active')->count() > 0
    : false;
$saleMode = $egi->sale_mode ?? null;
$isNotForSale = $saleMode === 'not_for_sale';
$egiPrice = $egi->price ?? 0;
$isAvailable = $egiPrice > 0;
$isPublished = $egi->is_published ?? true;

// Determina badge status
if ($isMinted) {
    $badgeStatus = 'minted';
    $badgeColor = 'bg-[#8E44AD]/90'; // Viola
    $badgeLabel = __('egi.badge.minted');
} elseif ($hasActiveReservations) {
    $badgeStatus = 'reserved';
    $badgeColor = 'bg-[#E67E22]/90'; // Arancio
    $badgeLabel = __('egi.badge.reserved');
} elseif ($isNotForSale) {
    $badgeStatus = 'not_for_sale';
    $badgeColor = 'bg-[#34495E]/90'; // Blu scuro
    $badgeLabel = __('egi.badge.not_for_sale');
} elseif (!$isAvailable) {
    $badgeStatus = 'not_available';
    $badgeColor = 'bg-[#7F8C8D]/90'; // Grigio
    $badgeLabel = __('egi.status.not_available');
} elseif (
    !$isPublished &&
    auth()->check() &&
    (auth()->id() === $egi->user_id || auth()->id() === ($egi->collection->creator_id ?? null))
) {
    $badgeStatus = 'not_published';
    $badgeColor = 'bg-[#C13120]/90'; // Rosso
    $badgeLabel = __('egi.badge.not_published');
} else {
    $badgeStatus = 'not_activated';
    $badgeColor = 'bg-[#2D5016]/90'; // Verde
    $badgeLabel = __('egi.badge.to_activate');
}

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

            <!-- Base Price Info -->
            @if ($egi->price && $saleMode !== 'not_for_sale')
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

            {{-- 🎯 Auction Info Section (compact for list view) --}}
            @if (!$egi->isMinted() && $egi->sale_mode === 'auction')
                @php
                    // Get highest bid from reservations
                    $highestBid = 0;
                    if ($egi->reservations && $egi->reservations->count() > 0) {
                        $highestReservation = $egi->reservations
                            ->where('is_highest', true)
                            ->where('amount_eur', '>=', $egi->auction_minimum_price ?? 0)
                            ->first();
                        if ($highestReservation) {
                            $highestBid = $highestReservation->amount_eur;
                        }
                    }

                    // Calculate time remaining
                    $now = now();
                    $auctionStart = $egi->auction_start ? \Carbon\Carbon::parse($egi->auction_start) : null;
                    $auctionEnd = $egi->auction_end ? \Carbon\Carbon::parse($egi->auction_end) : null;

                    $auctionStatus = 'not_started';
                    $timeRemaining = '';
                    $timeRemainingClass = 'text-gray-400';

                    if ($auctionStart && $auctionEnd) {
                        if ($now->lt($auctionStart)) {
                            $auctionStatus = 'not_started';
                            $diff = $now->diff($auctionStart);
                            $timeRemaining = sprintf(
                                '%d %s, %d %s',
                                $diff->days,
                                __('egi.auction.days'),
                                $diff->h,
                                __('egi.auction.hours'),
                            );
                            $timeRemainingClass = 'text-blue-400';
                        } elseif ($now->between($auctionStart, $auctionEnd)) {
                            $auctionStatus = 'active';
                            $diff = $now->diff($auctionEnd);
                            $timeRemaining = sprintf(
                                '%d %s, %d %s',
                                $diff->days,
                                __('egi.auction.days'),
                                $diff->h,
                                __('egi.auction.hours'),
                            );
                            $timeRemainingClass = $diff->days > 1 ? 'text-green-400' : 'text-orange-400';
                        } else {
                            $auctionStatus = 'ended';
                            $timeRemaining = __('egi.auction.ended');
                            $timeRemainingClass = 'text-red-400';
                        }
                    }
                @endphp

                <div
                    class="mt-2 rounded-lg border border-amber-500/30 bg-gradient-to-r from-amber-500/5 to-orange-500/5 p-2">
                    <button type="button" class="mb-1 flex w-full items-center gap-1.5 focus:outline-none"
                        onclick="this.nextElementSibling.classList.toggle('hidden')">
                        <svg class="h-3.5 w-3.5 text-amber-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-xs font-bold text-amber-300">{{ __('egi.auction.auction_details') }}</span>
                        <svg class="ml-1 h-3 w-3 text-amber-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="grid hidden grid-cols-2 gap-2 text-xs">
                        {{-- Minimum Price --}}
                        <div>
                            <span class="text-gray-400">{{ __('egi.auction.minimum_price') }}:</span>
                            <span class="font-bold text-amber-300">
                                €{{ number_format($egi->auction_minimum_price ?? 0, 2, ',', '.') }}
                            </span>
                        </div>

                        {{-- Current Bid --}}
                        <div>
                            <span class="text-gray-400">{{ __('egi.auction.current_bid') }}:</span>
                            <span class="font-bold text-green-300">
                                @if ($highestBid > 0)
                                    €{{ number_format($highestBid, 2, ',', '.') }}
                                @else
                                    <span class="text-gray-500">{{ __('egi.auction.no_bids') }}</span>
                                @endif
                            </span>
                        </div>

                        {{-- Auction Dates (if available) --}}
                        @if ($auctionStart && $auctionEnd)
                            <div class="col-span-2 mt-1 border-t border-amber-500/20 pt-1">
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-400">
                                        @if ($auctionStatus === 'not_started')
                                            {{ __('egi.auction.starts_at') }}:
                                        @elseif ($auctionStatus === 'active')
                                            {{ __('egi.auction.ends_at') }}:
                                        @else
                                            {{ __('egi.auction.status') }}:
                                        @endif
                                    </span>
                                    <span class="{{ $timeRemainingClass }} font-bold">{{ $timeRemaining }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- 🎯 BADGE STATUS (sempre visibile per tutti) --}}
    <div class="mt-3">
        <span
            class="{{ $badgeColor }} inline-flex w-full items-center justify-center gap-1.5 rounded-lg px-4 py-2.5 text-sm font-semibold text-white shadow-md ring-1 ring-white/10"
            title="{{ $badgeLabel }}">
            @if ($badgeStatus === 'minted')
                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z"
                        clip-rule="evenodd" />
                </svg>
            @elseif($badgeStatus === 'reserved')
                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z" />
                </svg>
            @elseif($badgeStatus === 'not_for_sale')
                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z"
                        clip-rule="evenodd" />
                </svg>
            @elseif($badgeStatus === 'not_available')
                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                        clip-rule="evenodd" />
                </svg>
            @elseif($badgeStatus === 'not_published')
                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z"
                        clip-rule="evenodd" />
                </svg>
            @else
                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8.108v3.784a1 1 0 001.555.94l3.108-1.892a1 1 0 000-1.688L9.555 7.168z"
                        clip-rule="evenodd" />
                </svg>
            @endif
            {{ $badgeLabel }}
        </span>
    </div>

    {{-- � PHASE 2: DUAL PATH BUTTONS (Mint Direct OR Reserve) - SOLO PER NON CREATOR --}}
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

            // 🎯 Sale mode detection
            $isAuctionMode = $egi->sale_mode === 'auction';

            // Price for display
            $displayPriceForAction = $egi->price ?? 0;

            // 🚨 CRITICAL: For auction mode, buttons visible even if price = 0
            // For fixed_price mode, hide buttons if price = 0
            $showButtons = $isAuctionMode || $displayPriceForAction > 0;
        @endphp

        <div class="mt-3">
            @if ($showButtons && count($availableActions) > 0)
                {{-- ✅ SCENARIO 1: User has reservation → Show complete purchase button --}}
                @if ($isReservedByUser && $canMint && $userReservation)
                    <a href="{{ route('mint.payment-form', ['egiId' => $egi->id]) }}?reservation_id={{ $userReservation->id }}"
                        class="mint-button flex w-full transform items-center justify-center rounded-lg bg-gradient-to-r from-[#8E44AD] to-[#9b59b6] px-4 py-2 text-sm font-bold text-white shadow-lg transition-all hover:scale-[1.01] hover:from-[#7d3c98] hover:to-[#8e44ad]">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        @if ($isAuctionMode)
                            {{ __('egi.actions.complete_purchase') }}
                        @else
                            {{ __('egi.actions.mint_now') }}
                        @endif
                        · €{{ number_format($displayPriceForAction, 2, ',', '.') }}
                    </a>

                    {{-- ✅ SCENARIO 2: Both MINT and RESERVE available (dual path) → Split by mode --}}
                @elseif($canMint && $canReserve)
                    @if ($isAuctionMode && $displayPriceForAction > 0)
                        {{-- 2a: Auction mode WITH fixed price → Dual buttons --}}
                        <div class="grid grid-cols-2 gap-2">
                            {{-- Make Offer Button (left) - AMBER --}}
                            <button type="button"
                                class="reserve-button flex transform items-center justify-center rounded-lg bg-gradient-to-r from-amber-500 to-orange-500 px-3 py-2 text-xs font-bold text-white transition-all hover:scale-[1.02] hover:from-amber-600 hover:to-orange-600"
                                data-egi-id="{{ $egi->id }}">
                                <span class="mr-1.5" aria-label="Asta" title="Asta">🔨</span>
                                {{ __('egi.actions.make_offer') }}
                            </button>

                            {{-- Mint Direct Button (right, emphasized) - PURPLE --}}
                            <a href="{{ route('egi.mint-direct', $egi->id) }}"
                                class="mint-direct-button flex transform items-center justify-center rounded-lg bg-gradient-to-r from-[#8E44AD] to-[#9b59b6] px-3 py-2 text-xs font-bold text-white shadow-md transition-all hover:scale-[1.02] hover:from-[#7d3c98] hover:to-[#8e44ad]">
                                <svg class="mr-1.5 h-3 w-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                {{ __('egi.actions.mint_now') }}
                            </a>
                        </div>
                    @elseif ($isAuctionMode && $displayPriceForAction == 0)
                        {{-- 2b: Auction mode WITHOUT fixed price → Only "Fai un'Offerta" --}}
                        <button type="button"
                            class="reserve-button flex w-full transform items-center justify-center rounded-lg bg-gradient-to-r from-amber-500 to-orange-500 px-4 py-2 text-sm font-bold text-white shadow-lg transition-all hover:scale-[1.01] hover:from-amber-600 hover:to-orange-600"
                            data-egi-id="{{ $egi->id }}">
                            <span class="mr-2" aria-label="Asta" title="Asta">🔨</span>
                            {{ __('egi.actions.make_offer') }}
                        </button>
                    @else
                        {{-- 2c: Fixed price mode → Only "Minta Subito" --}}
                        <a href="{{ route('egi.mint-direct', $egi->id) }}"
                            class="mint-direct-button flex w-full transform items-center justify-center rounded-lg bg-gradient-to-r from-[#8E44AD] to-[#9b59b6] px-4 py-2 text-sm font-bold text-white shadow-lg transition-all hover:scale-[1.01] hover:from-[#7d3c98] hover:to-[#8e44ad]">
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            {{ __('egi.actions.mint_now') }} ·
                            €{{ number_format($displayPriceForAction, 2, ',', '.') }}
                        </a>
                    @endif

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

                    {{-- ✅ SCENARIO 4: Only RESERVE available → "Fai Offerta" (auction) or "Attivalo" (fixed) --}}
                @elseif(!$canMint && $canReserve)
                    <button type="button"
                        class="reserve-button {{ $isAuctionMode ? 'from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600' : 'from-[#E67E22] to-[#d35400] hover:from-[#d35400] hover:to-[#ba4a00]' }} flex w-full transform items-center justify-center rounded-lg bg-gradient-to-r px-4 py-2 text-sm font-bold text-white transition-all hover:scale-[1.01]"
                        data-egi-id="{{ $egi->id }}">
                        @if ($hasCurrentReservation)
                            <span class="mr-2" aria-label="Asta" title="Asta">🔨</span>
                            {{ $isAuctionMode ? __('egi.actions.make_offer') : __('egi.actions.outbid') }}
                        @else
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 2v6M9 8l-2 2m2-2l2 2M3 22h18M6 22V12a3 3 0 013-3h6a3 3 0 013 3v10" />
                            </svg>
                            {{ $isAuctionMode ? __('egi.actions.make_offer') : __('egi.actions.reserve') }}
                        @endif
                    </button>
                @endif
            @else
                {{-- ❌ No actions available --}}
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
                        @if (!$showButtons && !$isAuctionMode)
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
