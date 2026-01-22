{{-- resources/views/components/collection-card-list.blade.php --}}
{{--
* @package App\View\Components
* @author AI Assistant for Fabio Cherici
* @version 1.0.0 (FlorenceEGI - Collection Card List)
* @date 2025-01-25
* @purpose List version of collection card for mobile-optimized display
--}}

@props([
'collection',
'context' => 'default',
'showOwnershipBadge' => false,
'showBadge' => null,
'imageType' => 'card'
])

@php
// Context configurations for different usage scenarios
$contextConfig = [
'default' => [
'show_creator' => true,
'show_stats' => true,
'badge_color' => 'bg-blue-500',
'badge_icon' => 'collections',
'badge_title' => __('collection.collection_badge')
],
'owner' => [
'show_creator' => false,
'show_stats' => true,
'badge_color' => 'bg-green-500',
'badge_icon' => 'check',
'badge_title' => __('collection.owned')
],
'public' => [
'show_creator' => true,
'show_stats' => false,
'badge_color' => 'bg-purple-500',
'badge_icon' => 'collections',
'badge_title' => __('collection.public')
]
];

$config = $contextConfig[$context] ?? $contextConfig['default'];

// Badge logic - può essere sovrascritto dal parametro showBadge
$showBadge = $showBadge ?? $showOwnershipBadge;

// Determina l'immagine da utilizzare - come in home-collection-card.blade.php
$logo = config('app.logo_01');
$imageUrl = '';

// Prova ad usare Spatie Media se disponibile
if ($collection) {
    if (method_exists($collection, 'getFirstMediaUrl')) {
        $imageUrl = $collection->getFirstMediaUrl('head', 'card');
        if ($imageUrl != '') {
            // OK, abbiamo un'immagine
        } else {
            // Nessuna immagine, usa il logo di default
            $imageUrl = asset($logo);
        }
    } else {
        $imageUrl = asset($logo);
    }
}
@endphp

{{-- Collection Card List Component --}}
<article
    class="relative p-4 transition-all duration-300 border collection-card-list group bg-gray-800/50 rounded-xl border-gray-700/50 hover:border-gray-600 hover:bg-gray-800/70"
    data-collection-id="{{ $collection->id }}">

    <div class="flex items-start gap-4">
        <!-- Image Section -->
        <a href="{{ route('home.collections.show', $collection->id) }}"
            class="relative flex-shrink-0 overflow-hidden transition-all duration-300 rounded-lg cursor-pointer w-28 h-28 bg-gradient-to-br from-gray-700 to-gray-800 group-hover:ring-2 group-hover:ring-blue-400">

            @if ($imageUrl)
            <img src="{{ $imageUrl }}" alt="{{ $collection->collection_name }}"
                class="object-cover w-full h-full transition-transform duration-300 group-hover:scale-110">
            @else
            <div
                class="flex items-center justify-center w-full h-full bg-gradient-to-br from-blue-600/20 to-purple-600/20">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
            @endif

            <!-- Hover overlay for visual feedback -->
            <div
                class="absolute inset-0 flex items-center justify-center transition-opacity duration-300 opacity-0 bg-blue-400/20 group-hover:opacity-100">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M13.5 6H5.25A2.25 2.25 0 003 8.25v7.5A2.25 2.25 0 005.25 18h7.5A2.25 2.25 0 0015 15.75v-7.5A2.25 2.25 0 0013.5 6z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 9 3.75 3.75-3.75 3.75" />
                </svg>
            </div>

            <!-- Context Badge -->
            @if ($showBadge)
            <div class="absolute -right-1 -top-1">
                <div class="flex h-6 w-6 items-center justify-center rounded-full {{ $config['badge_color'] }} ring-2 ring-gray-800"
                    title="{{ $config['badge_title'] }}">
                    @if ($config['badge_icon'] === 'check')
                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
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
            <!-- Title and Like Button -->
            <div class="flex items-start justify-between mb-1">
                <h3 class="flex-1 text-lg font-bold text-white truncate transition-colors group-hover:text-blue-300">
                    <a href="{{ route('home.collections.show', $collection->id) }}" class="hover:underline">
                        {{ $collection->collection_name ?? '#' . $collection->id }}
                    </a>
                </h3>

                <div class="flex-shrink-0 ml-2">
                    <x-like-button
                        :resourceType="'collection'"
                        :resourceId="$collection->id"
                        :isLiked="$collection->is_liked ?? false"
                        :likesCount="$collection->likes_count ?? 0"
                        size="small"
                    />
                </div>
            </div>

            <!-- Creator Info -->
            @if ($config['show_creator'] && $collection->user)
            <div class="flex items-center gap-2 mb-2 text-sm text-gray-400">
                <div class="w-3 h-3 rounded-full bg-gradient-to-r from-blue-500 to-purple-500"></div>
                <span class="truncate max-w-[120px]">{{ $collection->user->first_name }} {{ $collection->user->last_name
                    }}</span>
                <span class="text-xs text-gray-500">({{ __('profile.creator') }})</span>
            </div>
            @endif

            <!-- Description -->
            @if ($collection->description)
            <p class="mb-2 text-sm text-gray-400 line-clamp-2">
                {{ Str::limit($collection->description, 100) }}
            </p>
            @endif

            <!-- Stats Section - Always show basic stats -->
            <div class="flex flex-wrap items-center gap-4 mb-2 text-sm text-gray-400">
                <!-- EGI Count (sempre visualizzato) -->
                <div class="flex items-center gap-1">
                    <svg class="w-4 h-4 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="font-medium text-blue-300">{{ $collection->original_egis_count ?? $collection->egis_count ?? 0 }}</span>
                    <span>EGI</span>
                </div>

                <!-- Created Date -->
                @if ($collection->created_at)
                <div class="flex items-center gap-1">
                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="text-xs">{{ $collection->created_at->format('d/m/Y') }}</span>
                </div>
                @endif
            </div>

            <!-- User Roles Section (se presenti) -->
            @if(isset($collection->search_user_roles) && count($collection->search_user_roles))
            <div class="flex flex-wrap gap-1 mt-2">
                @foreach($collection->search_user_roles as $userName => $roles)
                    <div class="px-2 py-1 text-xs bg-gray-700/60 rounded text-gray-300">
                        <span class="font-medium">{{ $userName }}:</span>
                        @foreach($roles as $role)
                            <span class="text-blue-300">{{ $role }}</span>@if(!$loop->last), @endif
                        @endforeach
                    </div>
                @endforeach
            </div>
            @endif            <!-- Collection ID -->
            <div class="flex items-center justify-between mt-2">
                <div class="flex items-center gap-2">
                    <span
                        class="inline-flex items-center px-2 py-1 text-xs font-medium text-white rounded-full bg-gradient-to-r from-blue-500 to-purple-500">
                        {{ __('label.collection') }}
                    </span>
                </div>
                <span class="text-xs text-gray-500">ID: {{ $collection->id }}</span>
            </div>
        </div>
    </div>
</article>
