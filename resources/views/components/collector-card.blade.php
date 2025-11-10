{{-- resources/views/components/collector-card.blade.php --}}
{{--
* @package App\View\Components
* @author AI Assistant for Fabio Cherici
* @version 1.0.0 (FlorenceEGI - Collector Card)
* @date 2025-08-07
* @purpose Collector card with NFT-style mobile layout, based on creator-card
--}}

@props(['collector', 'imageType' => 'card', 'displayType' => 'default', 'rank' => null])

@php
$logo = config('app.logo');
$imageUrl = '';

if ($collector) {
if ($collector->profile_photo_url) {
$imageUrl = $collector->profile_photo_url;
} else {
$imageUrl = asset("images/logo/$logo");
}
} else {
$imageUrl = asset("images/logo/$logo");
}

// Get collector stats if available
$stats = [];
if ($collector && method_exists($collector, 'getCollectorStats')) {
$stats = $collector->getCollectorStats();
}
@endphp

@if ($collector)
@php
    $isCreatorProfile = strtolower($collector->usertype ?? '') === 'creator' || $collector->hasRole('creator');
    $profileRoute = $isCreatorProfile
        ? route('creator.home', ['id' => $collector->id])
        : route('collector.home', ['id' => $collector->id]);
    $ariaLabel = $isCreatorProfile
        ? __('collector.card.view_creator_profile_aria', ['name' => $collector->name])
        : __('collector.card.view_collector_profile_aria', ['name' => $collector->name]);
@endphp
{{-- NFT-STYLE CARD (Blur-inspired for Collectors) --}}
<a href="{{ $profileRoute }}"
    class="group block w-full overflow-hidden rounded-xl bg-gray-900 shadow-lg transition-all duration-300 hover:scale-[1.02] hover:shadow-2xl"
    aria-label="{{ $ariaLabel }}">

    {{-- Collector Image Section --}}
    <div class="relative w-full aspect-square bg-gradient-to-br from-gray-800 to-gray-900">
        <img src="{{ $imageUrl }}" alt="{{ $collector->name }}"
            class="object-cover w-full h-full transition-transform duration-300 group-hover:scale-105" loading="lazy"
            decoding="async">

        {{-- Rank Badge (if provided) --}}
        @if($rank)
        <div class="absolute left-3 bottom-3 z-10">
            <div class="flex items-center justify-center w-8 h-8 rounded-full shadow-lg text-white text-sm font-bold
                        {{ $rank <= 3
                            ? ($rank == 1 ? 'bg-gradient-to-r from-yellow-400 to-yellow-600' :
                               ($rank == 2 ? 'bg-gradient-to-r from-gray-300 to-gray-500' :
                                'bg-gradient-to-r from-amber-500 to-amber-700'))
                            : 'bg-gradient-to-r from-blue-500 to-blue-700'
                        }}">
                {{ $rank }}
            </div>
        </div>
        @endif

        {{-- Corner Badge "COLLECTOR" --}}
        <div class="absolute left-3 top-3">
            <span
                class="flex items-center justify-center text-sm font-bold text-white rounded-full shadow-lg h-7 w-7 bg-blu-algoritmo">
                📦
            </span>
        </div>

        {{-- Collector ID --}}
        <div class="absolute right-3 top-3">
            <span class="px-2 py-1 text-sm font-bold text-white bg-black bg-opacity-75 rounded-lg backdrop-blur-sm">
                #{{ $collector->id }}
            </span>
        </div>
    </div>

    {{-- Info Section --}}
    <div class="p-4 bg-gray-800">
        {{-- Collector Name --}}
        <h3
            class="mb-3 text-lg font-bold text-white truncate transition-colors duration-200 group-hover:text-blu-algoritmo">
            {{ $collector->name }}
        </h3>

        {{-- Stats Section (NFT-style) --}}
        <div class="flex items-center justify-between">
            <div class="flex flex-col">
                <span class="text-xs tracking-wide text-gray-400 uppercase">OWNED</span>
                <span class="text-sm font-semibold text-white">
                    {{ $stats['total_owned_egis'] ?? ($collector->owned_egis_count ?? 0) }} 🎨
                </span>
            </div>

            <div class="flex flex-col text-right">
                <span class="text-xs tracking-wide text-gray-400 uppercase">SPENT</span>
                <span class="text-sm font-semibold text-white">
                    {{-- Desktop: formato standard, Mobile: formato abbreviato --}}
                    <span class="hidden md:inline">€{{ number_format($stats['total_spent'] ?? ($collector->total_spent ?? 0), 0) }}</span>
                    <span class="md:hidden">{{ formatPriceAbbreviated($stats['total_spent'] ?? ($collector->total_spent ?? 0), 0) }}</span>
                </span>
            </div>
        </div>

        {{-- Stats Badges --}}
        @if ((isset($stats['total_collections']) && $stats['total_collections'] > 0) || (isset($stats['active_reservations']) && $stats['active_reservations'] > 0))
        <div class="mt-2 flex flex-wrap justify-center gap-1.5">
            {{-- Collections Badge --}}
            @if (isset($stats['total_collections']) && $stats['total_collections'] > 0)
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-verde-rinascita/20 text-verde-rinascita border border-verde-rinascita/30">
                <span class="material-symbols-outlined text-sm mr-1">folder_open</span>
                {{ $stats['total_collections'] }} {{ __('collector.card.collections') }}
            </span>
            @endif

            {{-- EGI Reservations Badge --}}
            @if (isset($stats['active_reservations']) && $stats['active_reservations'] > 0)
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-500/20 text-blue-400 border border-blue-500/30">
                <span class="material-symbols-outlined text-sm mr-1">bookmark</span>
                {{ $stats['active_reservations'] }} {{ __('collector.card.reservations') }}
            </span>
            @endif
        </div>
        @endif
    </div>
</a>
@else
<div class="flex items-center justify-center w-full h-full p-4 text-center text-gray-500 bg-gray-800 rounded-xl">
    {{ __('Collector data not available.') }}
</div>
@endif
