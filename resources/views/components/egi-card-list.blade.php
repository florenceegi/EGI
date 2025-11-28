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

$isMinted = $egi->isMinted();
// Co-Creator: usa il nuovo campo co_creator_id (più efficiente, senza JOIN)
$coCreatorUser = $egi->coCreator;
$coCreatorDisplay = $coCreatorUser ? formatActivatorDisplay($coCreatorUser) : null;
$currentOwnerUser = $egi->owner;
$currentOwnerDisplay = $currentOwnerUser ? formatActivatorDisplay($currentOwnerUser) : null;
// Mostra secondary owner solo se owner attuale diverso da co-creator (c'è stata una rivendita)
$showSecondaryOwner =
    $isMinted && $currentOwnerUser && $coCreatorUser && $currentOwnerUser->id !== $coCreatorUser->id;

$listMintedClasses = $isMinted
    ? 'minted-list-card text-emerald-50'
    : 'border-gray-700/50 bg-gray-800/50 hover:border-gray-600 hover:bg-gray-800/70';

$listMintedBorder = $isMinted ? 'border-2' : 'border';

    // 🌱 EPP PROJECT INFO
    $egiCollection = $egi->collection ?? null;
    $eppProject = null;
    if ($egiCollection && $egiCollection->epp_project_id) {
        $eppProject = $egiCollection->eppProject ?? null;
    }
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

@once
    <style>
        .minted-list-card {
            background: linear-gradient(120deg, #1A102E 0%, #3B1F66 45%, #5D2E8A 100%);
            border-color: #D4A574 !important;
            box-shadow: 0 0 30px rgba(212, 165, 116, 0.55), inset 0 0 12px rgba(255, 255, 255, 0.08);
            position: relative;
        }

        .minted-list-card::before {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: 0.75rem;
            border: 1px solid rgba(212, 165, 116, 0.35);
            pointer-events: none;
        }

        .minted-list-card::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, transparent 0%, #D4A574 40%, #E6C9A8 60%, transparent 100%);
            opacity: 0.85;
            pointer-events: none;
        }
    </style>
@endonce

{{-- EGI Card List Component --}}
<article
    class="egi-card-list {{ $isHyper ? 'egi-card--hiper' : '' }} {{ $listMintedBorder }} {{ $listMintedClasses }} group relative rounded-xl p-3 transition-all duration-300"
    data-egi-id="{{ $egi->id }}" data-hyper="{{ $isHyper ? '1' : '0' }}"
    style="{{ $isHyper ? '--energy:0.95; --foilHue:265; --edge:#9b5cf6; --accent:#a78bfa;' : '' }}">

    @if ($isMinted)
        <div class="absolute inset-y-3 -left-2 hidden h-auto w-1 rounded-full bg-gradient-to-b from-[#D4A574] via-[#E6C9A8] to-[#8E44AD] shadow-[0_0_20px_rgba(212,165,116,0.6)] md:block"
            aria-hidden="true"></div>
        <div class="pointer-events-none absolute inset-0 rounded-xl border border-[#D4A574]/20 opacity-80"
            aria-hidden="true"></div>
        <div class="pointer-events-none absolute left-0 right-0 top-0 h-0.5 rounded-t-xl bg-gradient-to-r from-transparent via-[#D4A574] to-transparent opacity-80"
            aria-hidden="true"></div>
        <div
            class="absolute -right-1 -top-2 flex items-center gap-2 rounded-bl-xl rounded-tr-xl bg-[#D4A574] px-3 py-1 text-[11px] font-semibold uppercase tracking-wide text-[#1B1B1B] shadow-lg">
            <svg class="h-3.5 w-3.5 text-[#1B1B1B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ __('egi.badge.minted') }}
        </div>
    @endif

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
            <x-egi-media-display :egi="$egi"
                class="{{ $isHyper ? 'filter brightness-110' : '' }} h-full w-full object-cover transition-transform duration-300 group-hover:scale-110" />

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

                {{-- 🌱 EPP PROJECT INFO --}}
                @if ($eppProject)
                    <div class="flex items-center gap-1 text-[#2D5016] dark:text-green-400"
                        title="{{ __('egi.epp.supports_project', ['project' => $eppProject->name]) }}">
                        @if ($eppProject->getFirstMediaUrl('project_avatar'))
                            <img src="{{ $eppProject->getFirstMediaUrl('project_avatar') }}"
                                alt="{{ $eppProject->name }}"
                                class="h-5 w-5 rounded-full object-cover ring-1 ring-[#2D5016]">
                        @else
                            <div class="flex h-5 w-5 items-center justify-center rounded-full bg-[#2D5016] text-white">
                                <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M4.632 3.533A2 2 0 016.577 2h6.846a2 2 0 011.945 1.533l1.976 8.234A3.489 3.489 0 0016 11.5H4c-.476 0-.93.095-1.344.267l1.976-8.234z"
                                        clip-rule="evenodd" />
                                    <path d="M4 19a2 2 0 100-4 2 2 0 000 4zM16 19a2 2 0 100-4 2 2 0 000 4z" />
                                </svg>
                            </div>
                        @endif
                        <span
                            class="max-w-[120px] truncate text-xs font-medium">{{ Str::limit($eppProject->name, 15) }}</span>
                    </div>
                @endif
            </div>

            @if ($isMinted && ($coCreatorDisplay || $currentOwnerDisplay))
                <div class="mb-2 flex flex-col gap-1">
                    @if ($coCreatorDisplay)
                        <div class="flex items-center gap-2">
                            <div
                                class="flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full bg-purple-600">
                                @if ($coCreatorDisplay['avatar'])
                                    <img src="{{ $coCreatorDisplay['avatar'] }}"
                                        alt="{{ $coCreatorDisplay['name'] }}"
                                        class="h-5 w-5 rounded-full border border-white/20 object-cover">
                                @else
                                    <svg class="h-3 w-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                            clip-rule="evenodd" />
                                    </svg>
                                @endif
                            </div>
                            <span class="truncate text-xs text-purple-200">
                                {{ __('egi.creator.co_creator') }}
                                <span class="font-semibold text-white">{{ $coCreatorDisplay['name'] }}</span>
                            </span>
                        </div>
                    @endif

                    @if ($showSecondaryOwner && $currentOwnerDisplay)
                        <div class="flex items-center gap-2">
                            <div
                                class="flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full bg-emerald-600">
                                @if ($currentOwnerDisplay['avatar'])
                                    <img src="{{ $currentOwnerDisplay['avatar'] }}"
                                        alt="{{ $currentOwnerDisplay['name'] }}"
                                        class="h-5 w-5 rounded-full border border-white/20 object-cover">
                                @else
                                    <svg class="h-3 w-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zm0 8a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zm6-6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zm0 8a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"
                                            clip-rule="evenodd" />
                                    </svg>
                                @endif
                            </div>
                            <span class="truncate text-xs text-emerald-200">
                                {{ __('egi.ownership.current_owner') }}
                                <span class="font-semibold text-white">{{ $currentOwnerDisplay['name'] }}</span>
                            </span>
                        </div>
                    @endif
                </div>
            @endif

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
                    @php
                        $statusLabel = '';
                        $statusColor = 'text-gray-400';
                        if ($egi->isMinted()) {
                            $statusLabel = __('egi.badge.minted');
                            $statusColor = 'text-purple-400';
                        } elseif ($egi->sale_mode === 'auction') {
                            $statusLabel = __('egi.badge.auction_active');
                            $statusColor = 'text-amber-400';
                        } elseif ($egi->activated) {
                            $statusLabel = __('egi.badge.activated');
                            $statusColor = 'text-green-400';
                        } else {
                            $statusLabel = __('egi.badge.to_activate');
                            $statusColor = 'text-gray-400';
                        }
                    @endphp
                    <div class="mb-1 flex items-center gap-2 text-sm" data-activation-status="available">
                        <svg class="{{ $statusColor }} h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="{{ $statusColor }} text-sm font-medium">{{ $statusLabel }}</span>
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

            // 🎯 Sale mode detection
            $isAuctionMode = $egi->sale_mode === 'auction';
            $isNotForSale = $egi->sale_mode === 'not_for_sale';

            // Price for display
            $displayPriceForAction = $egi->price ?? 0;

            // 🚨 CRITICAL: For auction mode, buttons visible even if price = 0
            // For fixed_price mode, hide buttons if price = 0
            $showButtons = $isAuctionMode || $displayPriceForAction > 0;
        @endphp

        <div class="mt-3">
            @if ($isNotForSale)
                {{-- 🚫 Sale mode = not_for_sale → Show "Non in Vendita" message --}}
                <div
                    class="flex w-full items-center justify-center rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-500">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                    {{ __('egi.status.not_for_sale') }}
                </div>
            @elseif ($showButtons && count($availableActions) > 0)
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
