{{-- resources/views/components/creator-card-list.blade.php --}}
{{--
* @package App\View\Components
* @author AI Assistant for Fabio Cherici
* @version 1.0.0 (FlorenceEGI - Creator Card List)
* @date 2025-01-25
* @purpose List version of creator card for mobile-optimized display
--}}

@props([
'creator',
'context' => 'default',
'showOwnershipBadge' => false,
'showBadge' => null,
'rank' => null
])

@php
// Context configurations for different usage scenarios
$contextConfig = [
'default' => [
'show_stats' => true,
'show_collections' => true,
'badge_color' => 'bg-purple-500',
'badge_icon' => 'palette',
'badge_title' => __('creator.creator_badge')
],
'featured' => [
'show_stats' => true,
'show_collections' => true,
'badge_color' => 'bg-yellow-500',
'badge_icon' => 'star',
'badge_title' => __('creator.featured')
],
'following' => [
'show_stats' => false,
'show_collections' => true,
'badge_color' => 'bg-green-500',
'badge_icon' => 'check',
'badge_title' => __('creator.following')
]
];

$config = $contextConfig[$context] ?? $contextConfig['default'];

// Badge logic - può essere sovrascritto dal parametro showBadge
$showBadge = $showBadge ?? $showOwnershipBadge;

// Determina l'immagine da utilizzare
$logo = config('app.logo');
$imageUrl = '';

if ($creator) {
if ($creator->profile_photo_url) {
$imageUrl = $creator->profile_photo_url;
} else {
$imageUrl = asset("images/logo/$logo");
}
} else {
$imageUrl = asset("images/logo/$logo");
}
@endphp

@if ($creator)
{{-- Creator Card List Component --}}
<article
    class="relative p-4 transition-all duration-300 border creator-card-list group bg-gray-800/50 rounded-xl border-gray-700/50 hover:border-gray-600 hover:bg-gray-800/70"
    data-creator-id="{{ $creator->id }}">

    <div class="flex items-start gap-4">
        <!-- Avatar Section -->
        <a href="{{ route('creator.home', ['id' => $creator->id]) }}"
            class="relative flex-shrink-0 overflow-hidden transition-all duration-300 rounded-lg cursor-pointer w-28 h-28 bg-gradient-to-br from-gray-700 to-gray-800 group-hover:ring-2 group-hover:ring-purple-400">

            <img src="{{ $imageUrl }}" alt="{{ $creator->first_name }} {{ $creator->last_name }}"
                class="object-cover w-full h-full transition-transform duration-300 group-hover:scale-110">

            <!-- Hover overlay for visual feedback -->
            <div
                class="absolute inset-0 flex items-center justify-center transition-opacity duration-300 opacity-0 bg-purple-400/20 group-hover:opacity-100">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M13.5 6H5.25A2.25 2.25 0 003 8.25v7.5A2.25 2.25 0 005.25 18h7.5A2.25 2.25 0 0015 15.75v-7.5A2.25 2.25 0 0013.5 6z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 9 3.75 3.75-3.75 3.75" />
                </svg>
            </div>

            <!-- Rank Badge (if provided) -->
            @if ($rank)
            <div class="absolute -left-2 -top-2">
                <div
                    class="flex items-center justify-center w-8 h-8 text-sm font-bold text-white rounded-full bg-gradient-to-r from-yellow-400 to-orange-500 ring-2 ring-gray-800">
                    #{{ $rank }}
                </div>
            </div>
            @endif

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
                    @elseif ($config['badge_icon'] === 'palette')
                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M4 2a2 2 0 00-2 2v11a2 2 0 002 2h4a2 2 0 002-2V4a2 2 0 00-2-2H4zm0 2h4v11H4V4zm8-2a2 2 0 00-2 2v11a2 2 0 002 2h4a2 2 0 002-2V4a2 2 0 00-2-2h-4zm0 2h4v11h-4V4z"
                            clip-rule="evenodd" />
                    </svg>
                    @elseif ($config['badge_icon'] === 'star')
                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 15.585l-6.16 3.251a.5.5 0 01-.725-.527l1.176-6.859L.146 7.41a.5.5 0 01.277-.854l6.877-.999L10.44.69a.5.5 0 01.894 0l3.14 6.367 6.877.999a.5.5 0 01.277.854l-4.972 4.04 1.176 6.859a.5.5 0 01-.725.527L10 15.585z"
                            clip-rule="evenodd" />
                    </svg>
                    @endif
                </div>
            </div>
            @endif
        </a>

        <!-- Content Section -->
        <div class="flex-1 min-w-0 mr-4">
            <!-- Name and Username -->
            <h3 class="mb-1 text-lg font-bold text-white truncate transition-colors group-hover:text-purple-300">
                <a href="{{ route('creator.home', ['id' => $creator->id]) }}" class="hover:underline">
                    {{ $creator->name }} {{ $creator->last_name }}
                </a>
            </h3>

            @if ($creator->username)
            <p class="mb-2 text-sm text-gray-400">@{{ $creator->username }}</p>
            @endif

            <!-- Bio/Description -->
            @if ($creator->bio)
            <p class="mb-2 text-sm text-gray-400 line-clamp-2">
                {{ Str::limit($creator->bio, 120) }}
            </p>
            @endif

            <!-- Stats Section - Always show basic stats -->
            <div class="flex flex-wrap items-center gap-4 mb-2 text-sm text-gray-400">
                <!-- EGI Count (sempre visualizzato) -->
                <div class="flex items-center gap-1">
                    <svg class="w-4 h-4 text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="font-medium text-purple-300">{{ $creator->egis_count ?? 0 }}</span>
                    <span>EGI</span>
                </div>

                <!-- Collections Count (sempre visualizzato) -->
                <div class="flex items-center gap-1">
                    <svg class="w-4 h-4 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="font-medium text-blue-300">{{ $creator->collections_count ?? 0 }}</span>
                    <span>{{ __('egi.carousel.collections') }}</span>
                </div>

                <!-- Followers Count (se disponibile) -->
                @if (isset($creator->followers_count) && $creator->followers_count > 0)
                <div class="flex items-center gap-1">
                    <svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="font-medium text-green-300">{{ $creator->followers_count }}</span>
                    <span>{{ __('creator.home.stats.followers') }}</span>
                </div>
                @endif
            </div>

            @if(isset($creator->collection_role_counts) && count($creator->collection_role_counts))
            <div class="flex flex-wrap gap-2 mt-2">
                @foreach($creator->collection_role_counts as $role => $cnt)
                    <span class="px-2 py-1 text-xs font-medium text-gray-300 rounded bg-gray-700/60">
                        {{ $role }}: <span class="text-purple-300">{{ $cnt }}</span>
                    </span>
                @endforeach
            </div>
            @endif

            <!-- 🎯 Top Activator Section - Sistema Commissioner Integration -->
            @php
            // Trova il top activator per questo creator (utente con più EGI attivati)
            $topActivator = null;
            $activatorEgiCount = 0;

            if ($creator && $creator->id) {
            try {
            // Query per trovare l'utente che ha più prenotazioni attive sui EGI di questo creator
            $topActivatorData = \DB::table('reservations as r')
            ->join('egis as e', 'r.egi_id', '=', 'e.id')
            ->join('collections as c', 'e.collection_id', '=', 'c.id')
            ->join('users as u', 'r.user_id', '=', 'u.id')
            ->where('c.creator_id', $creator->id)
            ->where('r.is_current', true)
            ->where('r.status', 'active')
            ->whereNull('r.superseded_by_id')
            ->select('r.user_id', \DB::raw('COUNT(*) as egi_count'))
            ->groupBy('r.user_id')
            ->orderByDesc('egi_count')
            ->first();

            if ($topActivatorData && $topActivatorData->egi_count > 0) {
            $topActivator = \App\Models\User::find($topActivatorData->user_id);
            $activatorEgiCount = $topActivatorData->egi_count;
            }
            } catch (\Throwable $th) {
            // Gestione errori silente
            $topActivator = null;
            $activatorEgiCount = 0;
            }
            }
            @endphp

            @if ($topActivator && $activatorEgiCount > 0)
            @php
            // 🎯 Sistema Commissioner: Formattiamo le informazioni del top activator
            $activatorDisplay = formatActivatorDisplay($topActivator);
            @endphp
            <div class="p-2 mb-2 border rounded-lg border-gray-700/50 bg-gray-800/30">
                <!-- Header con stella e testo -->
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 15.585l-6.16 3.251a.5.5 0 01-.725-.527l1.176-6.859L.146 7.41a.5.5 0 01.277-.854l6.877-.999L10.44.69a.5.5 0 01.894 0l3.14 6.367 6.877.999a.5.5 0 01.277-.854l-4.972 4.04 1.176 6.859a.5.5 0 01-.725.527L10 15.585z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="text-xs font-medium text-yellow-300">{{ __('creator.top_activator') }}</span>
                </div>

                <!-- Attivatore su riga separata -->
                <div class="flex items-center gap-2">
                    @if ($activatorDisplay && $activatorDisplay['is_commissioner'])
                    {{-- 👤 Commissioner: Mostra avatar personalizzato --}}
                    @if ($activatorDisplay['avatar'])
                    <img src="{{ $activatorDisplay['avatar'] }}" alt="{{ $activatorDisplay['name'] }}"
                        class="flex-shrink-0 object-cover w-5 h-5 border rounded-full shadow-sm border-yellow-400/30">
                    @else
                    {{-- Fallback per commissioner senza avatar --}}
                    <div
                        class="flex items-center justify-center flex-shrink-0 w-5 h-5 bg-yellow-500 rounded-full shadow-sm">
                        <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    @endif
                    <span class="text-sm font-medium text-yellow-200 truncate">
                        {{ $activatorDisplay['name'] }}
                    </span>
                    @else
                    {{-- 💰 Regular Collector: Icona generica + nome abbreviato --}}
                    <div class="flex items-center justify-center flex-shrink-0 w-5 h-5 bg-gray-600 rounded-full">
                        <svg class="w-2.5 h-2.5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-300 truncate">
                        {{ $activatorDisplay ? $activatorDisplay['name'] : ($topActivator->wallet_address ?
                        Str::limit($topActivator->wallet_address, 12, '...') : $topActivator->first_name . ' ' .
                        $topActivator->last_name) }}
                    </span>
                    @endif

                    <div class="flex items-center gap-1 ml-auto">
                        <span class="text-xs text-gray-400">{{ $activatorEgiCount }}</span>
                        <span class="text-xs text-gray-500">EGI</span>
                    </div>
                </div>
            </div>
            @endif

            <!-- Join Date Only -->
            @if ($creator->created_at)
            <div class="flex items-center justify-end mt-2">
                <div class="flex items-center gap-1 text-xs text-gray-500">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                            clip-rule="evenodd" />
                    </svg>
                    <span>{{ $creator->created_at->format('M Y') }}</span>
                </div>
            </div>
            @endif
        </div>
    </div>
</article>
@endif
