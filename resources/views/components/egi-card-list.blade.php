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
'showBadge' => null // Override per nascondere badge se necessario
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
'show_creator' => true
],
'creator' => [
'badge_color' => 'bg-blue-500',
'badge_icon' => 'palette',
'badge_title' => __('creator.portfolio.created'),
'show_purchase' => false,
'show_creator' => false
],
'patron' => [
'badge_color' => 'bg-purple-500',
'badge_icon' => 'heart',
'badge_title' => __('patron.portfolio.supported'),
'show_purchase' => true,
'show_creator' => true
],
'collection' => [
'badge_color' => 'bg-indigo-500',
'badge_icon' => 'collections',
'badge_title' => __('collection.show.from_collection'),
'show_purchase' => false,
'show_creator' => true
]
];

$config = $contextConfig[$context] ?? $contextConfig['collector'];

// Controllo se l'utente loggato è il creator dell'EGI
$isCreator = auth()->check() && auth()->id() === $egi->user_id;

// Badge logic - può essere sovrascritto dal parametro showBadge
$showBadge = $showBadge ?? $showOwnershipBadge;
@endphp

{{-- Include CSS hyper se necessario --}}
@if($isHyper)
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
    class="egi-card-list {{ $isHyper ? 'egi-card--hiper' : '' }} group relative bg-gray-800/50 rounded-xl p-3 border border-gray-700/50 hover:border-gray-600 hover:bg-gray-800/70 transition-all duration-300"
    data-egi-id="{{ $egi->id }}" data-hyper="{{ $isHyper ? '1' : '0' }}"
    style="{{ $isHyper ? '--energy:0.95; --foilHue:265; --edge:#9b5cf6; --accent:#a78bfa;' : '' }}">

    @if($isHyper)
    {{-- Sparkles Effect per HYPER --}}
    <div class="egi-sparkles" aria-hidden="true"></div>

    {{-- Badge HYPER per modalità lista --}}
    <div class="absolute z-10 -top-2 -right-2">
        <div class="egi-hyper-badge-small">⭐ HYPER ⭐</div>
    </div>
    @endif

    <div class="flex items-start gap-4">
        <!-- Image Section -->
        <a href="{{ route('egis.show', $egi->id) }}"
            class="relative flex-shrink-0 w-28 h-28 overflow-hidden rounded-lg bg-gradient-to-br from-gray-700 to-gray-800 cursor-pointer {{ $isHyper ? 'group-hover:ring-2 group-hover:ring-yellow-400' : 'group-hover:ring-2 group-hover:ring-purple-400' }} transition-all duration-300">
            @if ($egi->main_image_url)
            <img src="{{ $egi->main_image_url }}" alt="{{ $egi->title }}"
                class="object-cover w-full h-full transition-transform duration-300 group-hover:scale-110 {{ $isHyper ? 'filter brightness-110' : '' }}">
            @else
            <div
                class="flex items-center justify-center w-full h-full bg-gradient-to-br {{ $isHyper ? 'from-yellow-600/20 to-orange-600/20' : 'from-purple-600/20 to-blue-600/20' }}">
                <svg class="w-8 h-8 {{ $isHyper ? 'text-yellow-400' : 'text-gray-400' }}" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            @endif

            <!-- Hover overlay for visual feedback -->
            <div
                class="absolute inset-0 {{ $isHyper ? 'bg-yellow-400/20' : 'bg-black/20' }} opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                <svg class="w-6 h-6 {{ $isHyper ? 'text-yellow-300' : 'text-white' }}" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M13.5 6H5.25A2.25 2.25 0 003 8.25v7.5A2.25 2.25 0 005.25 18h7.5A2.25 2.25 0 0015 15.75v-7.5A2.25 2.25 0 0013.5 6z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 9 3.75 3.75-3.75 3.75" />
                </svg>
            </div>

            <!-- Like Button - Top Right -->
            @if(!$isCreator)
            <div class="absolute z-10 top-2 right-2">
                <button
                    class="p-2 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-full transition-all duration-200 border border-white/20 like-button {{ $egi->is_liked ?? false ? 'is-liked bg-pink-500/20 border-pink-400/50' : '' }}"
                    data-resource-type="egi" data-resource-id="{{ $egi->id }}"
                    title="{{ __('egi.like_button_title') }}">
                    <svg class="w-4 h-4 icon-heart {{ $egi->is_liked ?? false ? 'text-pink-400' : 'text-white' }}"
                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M3.172 5.172a4 4 0 0 1 5.656 0L10 6.343l1.172-1.171a4 4 0 1 1 5.656 5.656L10 17.657l-6.828-6.829a4 4 0 0 1 0-5.656Z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
            @endif

            {{-- Badge Categoria (top-left) --}}
            @php
                $categoryName = $egi->category_name;
                $categoryClasses = $egi->category_badge_classes;
            @endphp
            <div class="absolute left-2 top-2">
                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold leading-none shadow ring-1 ring-white/10 {{ $categoryClasses }}"
                    title="{{ $categoryName }}" aria-label="EGI Category: {{ $categoryName }}">
                    {{ Str::limit($categoryName, 16) }}
                </span>
            </div>

            <!-- Context Badge -->
            @if ($showOwnershipBadge)
            <div class="absolute right-2 top-12">
                <div class="flex h-6 w-6 items-center justify-center rounded-full {{ $config['badge_color'] }} ring-2 ring-gray-800
                     {{ $isHyper ? 'animate-pulse' : '' }}" title="{{ $config['badge_title'] }}">
                    @if ($config['badge_icon'] === 'check')
                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                            clip-rule="evenodd" />
                    </svg>
                    @elseif ($config['badge_icon'] === 'palette')
                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M4 2a2 2 0 00-2 2v11a2 2 0 002 2h4a2 2 0 002-2V4a2 2 0 00-2-2H4zm0 2h4v11H4V4zm8-2a2 2 0 00-2 2v11a2 2 0 002 2h4a2 2 0 002-2V4a2 2 0 00-2-2h-4zm0 2h4v11h-4V4z"
                            clip-rule="evenodd" />
                    </svg>
                    @elseif ($config['badge_icon'] === 'heart')
                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                            clip-rule="evenodd" />
                    </svg>
                    @elseif ($config['badge_icon'] === 'collections')
                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
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
        <div class="flex-1 min-w-0 mr-4">
            <!-- Title -->
            <h3 class="mb-1 text-lg font-bold text-white truncate transition-colors
                {{ $isHyper ? 'group-hover:text-yellow-300' : 'group-hover:text-purple-300' }}">
                <a href="{{ route('egis.show', $egi->id) }}" class="hover:underline">
                    {{ $egi->title ?? '#' . $egi->id }}
                </a>
            </h3>

            <!-- Collection and Creator Info -->
            <div class="flex flex-wrap items-center gap-4 mb-2 text-sm text-gray-400">
                @if ($egi->collection)
                <div class="flex items-center gap-1">
                    <div
                        class="w-3 h-3 rounded-full {{ $isHyper ? 'bg-gradient-to-r from-yellow-400 to-yellow-600' : 'bg-gradient-to-r from-purple-500 to-blue-500' }}">
                    </div>
                    <a href="{{ route('home.collections.show', $egi->collection->id) }}" class="transition-colors truncate max-w-[120px]
                        {{ $isHyper ? 'hover:text-yellow-400' : 'hover:text-purple-400' }}">
                        {{ $egi->collection->collection_name }}
                    </a>
                </div>
                @endif

                @if ($config['show_creator'] && $egi->user)
                <div class="flex items-center gap-1">
                    <div class="w-3 h-3 rounded-full {{ $isHyper ? 'bg-yellow-600' : 'bg-gray-600' }}"></div>
                    <span class="truncate max-w-[100px]">{{ $egi->user->first_name }} {{ $egi->user->last_name }}</span>
                </div>
                @endif
            </div>

            <!-- Activator Info -->
            @if ($context === 'creator' || $context === 'collection')
            @php
            // Find the current reservation (activated collector)
            $currentReservation = $egi->reservations ? $egi->reservations->where('is_current', true)->first() : null;
            @endphp
            @if ($currentReservation && $currentReservation->user)
            @php
            // 🎯 Sistema Commissioner: Formattiamo le informazioni dell'attivatore
            $activatorDisplay = formatActivatorDisplay($currentReservation->user);
            @endphp
            <div class="flex items-center gap-2 mb-1 text-sm" data-activation-status>
                {{-- Avatar sempre presente dal backend (gestisce automaticamente la privacy) --}}
                @if ($activatorDisplay['avatar'])
                <img src="{{ $activatorDisplay['avatar'] }}" alt="{{ $activatorDisplay['name'] }}"
                    class="object-cover w-4 h-4 border rounded-full shadow-sm border-green-400/30">
                @else
                {{-- Fallback solo se non c'è avatar dal backend (caso molto raro) --}}
                <div class="flex items-center justify-center w-4 h-4 bg-green-500 rounded-full shadow-sm">
                    <svg class="w-2 h-2 text-white" fill="currentColor" viewBox="0 0 20 20">
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
            <div class="flex items-center gap-2 mb-1 text-sm" data-activation-status="available">
                <svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 20 20">
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
            $hasCurrentReservation = ($context === 'creator' || $context === 'collection') &&
            $egi->reservations &&
            $egi->reservations->where('is_current', true)->first();
            @endphp
            <div class="flex items-center justify-between gap-2 mb-1 text-sm">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 {{ $isHyper ? 'text-yellow-400' : 'text-orange-400' }}" fill="currentColor"
                        viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"
                            clip-rule="evenodd" />
                    </svg>
                    <div class="flex items-center gap-1">
                        <span class="font-bold {{ $isHyper ? 'text-yellow-300' : 'text-orange-300' }}">
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
            <div class="flex items-center gap-2 mt-1 text-sm">
                <span class="text-gray-400">{{ __('collector.portfolio.purchased_for') }}</span>
                <span class="font-bold {{ $isHyper ? 'text-yellow-400' : 'text-green-400' }}">
                    <x-currency-price :price="$egi->pivot->offer_amount_fiat" :egi="$egi" />
                </span>
            </div>
            @elseif ($context === 'patron' && isset($egi->support_amount))
            <div class="flex items-center gap-2 mt-1 text-sm">
                <span class="text-gray-400">{{ __('patron.portfolio.supported_for') }}</span>
                <span class="font-bold {{ $isHyper ? 'text-yellow-300' : 'text-yellow-400' }}">
                    <x-currency-price :price="$egi->support_amount" />
                </span>
            </div>
            @endif
            @endif
        </div>
    </div>

    {{-- 🔥 Pulsante Prenota/Rilancia in fondo alla card --}}
    @if(!$isCreator)
    <div class="mt-3">
        @if($egi->price && $egi->price > 0)
        <button type="button" class="reserve-button w-full flex items-center justify-center px-4 py-2 text-sm font-medium text-white
                {{ $hasCurrentReservation ? 'bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700' : 'bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700' }}
                rounded-t-none rounded-b-lg transition-all transform hover:scale-[1.01]" data-egi-id="{{ $egi->id }}">
            @if($hasCurrentReservation)
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
            </svg>
            {{ __('egi.actions.outbid') ?? 'Rilancia' }}
            @else
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            {{ __('egi.actions.reserve') ?? 'Prenota' }}
            @endif
        </button>
        @else
        <div
            class="flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-gray-500 bg-gray-100 rounded-t-none rounded-b-lg">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L5.636 5.636" />
            </svg>
            {{ __('egi.status.not_for_sale') ?? 'Non in vendita' }}
        </div>
        @endif
    </div>
    @endif
</article>
