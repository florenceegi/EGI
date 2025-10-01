@props([
    'userId' => null,
    'creatorId' => null,
    'period' => 'month',
])

@php
    $targetUserId = $userId ?? ($creatorId ?? auth()->id());
    $statisticsService = app(\App\Services\StatisticsService::class);

    // Ottieni statistiche dei like DATI dall'utente con periodo temporale
    $givenLikesStats = $statisticsService->getLikesGivenByUserStatsByPeriod($targetUserId, $period);
@endphp

<div class="p-6 bg-white rounded-xl bg-opacity-10 backdrop-blur-md">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h3 class="flex items-center text-xl font-semibold text-white">
            <svg class="w-6 h-6 mr-2 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                    clip-rule="evenodd" />
            </svg>
            {{ __('statistics.likes_given_analysis') }}
        </h3>

        {{-- Total Given Likes --}}
        <div class="text-right">
            <div class="text-3xl font-bold text-blue-400">{{ number_format($givenLikesStats['total_given']) }}</div>
            <div class="text-sm text-gray-300">{{ __('statistics.total_given') }}</div>
        </div>
    </div>

    {{-- Content Tabs --}}
    <div class="mb-4">
        <div class="flex p-1 space-x-1 bg-black rounded-lg bg-opacity-20">
            <button
                class="flex items-center justify-center flex-1 px-3 py-2 space-x-2 text-sm font-medium text-white transition-colors duration-200 bg-blue-600 rounded-md likes-given-tab-btn"
                data-tab="egis">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"
                        clip-rule="evenodd" />
                </svg>
                <span>EGI</span>
            </button>
            <button
                class="flex items-center justify-center flex-1 px-3 py-2 space-x-2 text-sm font-medium text-gray-300 transition-colors duration-200 rounded-md likes-given-tab-btn hover:text-white"
                data-tab="owners">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                        clip-rule="evenodd" />
                </svg>
                <span>Owner</span>
            </button>
        </div>
    </div>

    {{-- EGI Tab Content --}}
    <div id="likes-given-egis-tab" class="likes-given-tab-content">
        <div class="mb-4">
            <h4 class="mb-3 text-lg font-medium text-white">{{ __('statistics.liked_egis') }}</h4>

            @if (count($givenLikesStats['liked_egis']) > 0)
                <div class="space-y-2">
                    @foreach ($givenLikesStats['liked_egis'] as $index => $egi)
                        @php
                            // Ogni EGI rappresenta un like dato dall'utente, quindi la progressione è basata sull'ordine
                            $totalEgis = count($givenLikesStats['liked_egis']);
                            $percentage = $totalEgis > 0 ? (($totalEgis - $index) / $totalEgis) * 100 : 0;

                            // Switcher per la route corretta basata sul usertype del proprietario dell'EGI
                            $ownerId = $egi['owner_id'];
                            $ownerUser = \App\Models\User::find($ownerId);
                            $ownerRoute = '#'; // Fallback

                            if ($ownerUser) {
                                $ownerRoute = match ($ownerUser->usertype ?? 'creator') {
                                    'creator' => route('creator.home', $ownerId),
                                    'collector' => route('collector.home', $ownerId),
                                    'commissioner' => route(
                                        'profile.show',
                                    ), // Commissioner non ha pagina pubblica specifica
                                    default => route('creator.home', $ownerId), // Fallback a creator
                                                            };
                            }
                        @endphp
                        <div class="p-2 transition-colors bg-black rounded-lg bg-opacity-20 hover:bg-opacity-30">
                            <div class="flex items-center space-x-3">
                                {{-- Rank --}}
                                <div
                                    class="flex items-center justify-center flex-shrink-0 w-6 h-6 text-xs font-bold text-white rounded-full bg-gradient-to-r from-blue-500 to-cyan-500">
                                    {{ $index + 1 }}
                                </div>

                                {{-- EGI Image --}}
                                <div class="flex-shrink-0">
                                    <a href="/egis/{{ $egi['id'] }}">
                                        @if ($egi['avatar_image_url'])
                                            <img src="{{ $egi['avatar_image_url'] }}" alt="{{ $egi['title'] }}"
                                                class="object-cover w-8 h-8 rounded">
                                        @elseif($egi['thumbnail_image_url'])
                                            <img src="{{ $egi['thumbnail_image_url'] }}" alt="{{ $egi['title'] }}"
                                                class="object-cover w-8 h-8 rounded">
                                        @elseif($egi['main_image_url'])
                                            <img src="{{ $egi['main_image_url'] }}" alt="{{ $egi['title'] }}"
                                                class="object-cover w-8 h-8 rounded">
                                        @else
                                            <div class="flex items-center justify-center w-8 h-8 bg-gray-600 rounded">
                                                <svg class="w-4 h-4 text-gray-400" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        @endif
                                    </a>
                                </div>

                                {{-- EGI Info --}}
                                <div class="flex-1 min-w-0">
                                    <a href="/egis/{{ $egi['id'] }}" class="block">
                                        <div class="text-sm font-medium text-white truncate">
                                            {{ $egi['title'] ?? 'Untitled EGI' }}</div>
                                        <div class="text-xs text-gray-400">
                                            {{ __('statistics.by') }} <a href="{{ $ownerRoute }}"
                                                class="text-blue-400 hover:text-blue-300">{{ $egi['owner_nick_name'] ?? $egi['owner_name'] }}</a>
                                        </div>
                                    </a>
                                </div>

                                {{-- Like Badge --}}
                                <div class="flex-shrink-0">
                                    <div
                                        class="flex items-center justify-center w-6 h-6 text-xs font-bold text-white rounded-full bg-gradient-to-r from-blue-500 to-cyan-500">
                                        ❤️
                                    </div>
                                </div>

                                {{-- Progress Bar --}}
                                <div class="flex-shrink-0 w-20">
                                    <div class="w-full h-2 bg-gray-700 rounded-full">
                                        <div class="h-2 rounded-full bg-gradient-to-r from-blue-500 to-cyan-500"
                                            style="width: {{ $percentage }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-8 text-center text-gray-400">
                    {{ __('statistics.no_liked_egis') }}
                </div>
            @endif
        </div>
    </div>

    {{-- Owners Tab Content --}}
    <div id="likes-given-owners-tab" class="hidden likes-given-tab-content">
        <div class="mb-4">
            <h4 class="mb-3 text-lg font-medium text-white">{{ __('statistics.liked_owners') }}</h4>

            @if (count($givenLikesStats['owners']) > 0)
                <div class="space-y-2">
                    @foreach ($givenLikesStats['owners'] as $index => $owner)
                        @php
                            $maxLikes = $givenLikesStats['owners'][0]['likes_count'] ?? 1;
                            $percentage = ($owner['likes_count'] / $maxLikes) * 100;

                            // Switcher per la route corretta basata sul usertype dell'owner
                            $ownerUserId = $owner['user_id'];
                            $ownerUserObject = $owner['user'] ?? null; // Oggetto User già caricato dal service
                            $ownerRoute = '#'; // Fallback

                            if ($ownerUserObject) {
                                $ownerRoute = match ($ownerUserObject->usertype ?? 'creator') {
                                    'creator' => route('creator.home', $ownerUserId),
                                    'collector' => route('collector.home', $ownerUserId),
                                    'commissioner' => route(
                                        'profile.show',
                                    ), // Commissioner non ha pagina pubblica specifica
                                    default => route('creator.home', $ownerUserId), // Fallback a creator
                                };
                            }
                        @endphp
                        <a href="{{ $ownerRoute }}"
                            class="block p-2 transition-colors bg-black rounded-lg bg-opacity-20 hover:bg-opacity-30">
                            <div class="flex items-center space-x-3">
                                {{-- Rank --}}
                                <div
                                    class="flex items-center justify-center flex-shrink-0 w-6 h-6 text-xs font-bold text-white rounded-full bg-gradient-to-r from-purple-500 to-pink-500">
                                    {{ $index + 1 }}
                                </div>

                                {{-- Owner Avatar --}}
                                <div class="flex-shrink-0">
                                    @if ($owner['user']->profile_photo_url)
                                        <img src="{{ $owner['user']->profile_photo_url }}"
                                            alt="{{ $owner['nickname'] }}" class="object-cover w-8 h-8 rounded-full">
                                    @else
                                        <div
                                            class="flex items-center justify-center w-8 h-8 rounded-full bg-gradient-to-r from-purple-500 to-pink-500">
                                            <span
                                                class="text-xs font-bold text-white">{{ strtoupper(substr($owner['nickname'], 0, 1)) }}</span>
                                        </div>
                                    @endif
                                </div>

                                {{-- Owner Name --}}
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-medium text-white truncate">{{ $owner['nickname'] }}
                                    </div>
                                </div>

                                {{-- Likes Count --}}
                                <div class="flex-shrink-0 text-sm font-bold text-purple-400">
                                    {{ $owner['likes_count'] }}
                                </div>

                                {{-- Progress Bar --}}
                                <div class="flex-shrink-0 w-20">
                                    <div class="w-full h-2 bg-gray-700 rounded-full">
                                        <div class="h-2 rounded-full bg-gradient-to-r from-purple-500 to-pink-500"
                                            style="width: {{ $percentage }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="py-8 text-center text-gray-400">
                    {{ __('statistics.no_liked_owners') }}
                </div>
            @endif
        </div>
    </div>
</div>

{{-- JavaScript for Tab Switching --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab switching functionality for likes given widget
        const tabButtons = document.querySelectorAll('.likes-given-tab-btn');
        const tabContents = document.querySelectorAll('.likes-given-tab-content');

        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetTab = this.dataset.tab;

                // Update button states
                tabButtons.forEach(btn => {
                    btn.classList.remove('bg-blue-600', 'text-white');
                    btn.classList.add('text-gray-300');
                });
                this.classList.add('bg-blue-600', 'text-white');
                this.classList.remove('text-gray-300');

                // Update content visibility
                tabContents.forEach(content => {
                    content.classList.add('hidden');
                });

                const targetContent = document.getElementById(`likes-given-${targetTab}-tab`);
                if (targetContent) {
                    targetContent.classList.remove('hidden');
                }
            });
        });
    });
</script>
<path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"
    clip-rule="evenodd" />
</svg>
<span>EGI</span>
</button>
<button
    class="flex items-center justify-center flex-1 px-3 py-2 space-x-2 text-sm font-medium text-gray-300 transition-colors duration-200 rounded-md likes-given-tab-btn hover:text-white"
    data-tab="owners">
    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
    </svg>
    <span>User</span>
</button>



{{-- EGIs Liked Tab Content --}}
<div id="likes-given-egis-tab" class="likes-given-tab-content">
    <div class="mb-4">
        <h4 class="mb-3 text-lg font-medium text-white">{{ __('statistics.egis_you_liked') }}</h4>

        @if (count($givenLikesStats['liked_egis']) > 0)
            <div class="space-y-2">
                @foreach ($givenLikesStats['liked_egis'] as $index => $egi)
                    @php
                        // Switcher per la route corretta basata sul usertype del proprietario dell'EGI
                        $ownerId = $egi['owner_id'];
                        $ownerUser = \App\Models\User::find($ownerId);
                        $ownerRoute = '#'; // Fallback

                        if ($ownerUser) {
                            $ownerRoute = match ($ownerUser->usertype ?? 'creator') {
                                'creator' => $egi['owner_nick_name']
                                    ? route('creator.home.nickname', $egi['owner_nick_name'])
                                    : route('creator.home', $ownerId),
                                'collector' => route('collector.home', $ownerId),
                                'commissioner' => route(
                                    'profile.show',
                                ), // Commissioner non ha pagina pubblica specifica
                                default => route('creator.home', $ownerId), // Fallback a creator
                            };
                        }
                    @endphp
                    <div
                        class="flex items-center p-3 space-x-3 transition-all duration-200 bg-black rounded-lg bg-opacity-20 hover:bg-opacity-30">
                        {{-- Rank --}}
                        <div
                            class="flex items-center justify-center flex-shrink-0 w-8 h-8 text-sm font-bold text-white rounded-full bg-gradient-to-r from-blue-500 to-purple-500">
                            {{ $index + 1 }}
                        </div>

                        {{-- EGI Image --}}
                        <div class="flex-shrink-0">
                            <a href="/egi/{{ $egi['id'] }}" class="block transition-transform hover:scale-105">
                                @if ($egi['avatar_image_url'])
                                    <img src="{{ $egi['avatar_image_url'] }}" alt="{{ $egi['title'] }}"
                                        class="object-cover w-12 h-12 border-2 border-blue-400 rounded-lg">
                                @elseif($egi['thumbnail_image_url'])
                                    <img src="{{ $egi['thumbnail_image_url'] }}" alt="{{ $egi['title'] }}"
                                        class="object-cover w-12 h-12 border-2 border-blue-400 rounded-lg">
                                @elseif($egi['main_image_url'])
                                    <img src="{{ $egi['main_image_url'] }}" alt="{{ $egi['title'] }}"
                                        class="object-cover w-12 h-12 border-2 border-blue-400 rounded-lg">
                                @else
                                    <div
                                        class="flex items-center justify-center w-12 h-12 bg-gray-600 border-2 border-blue-400 rounded-lg">
                                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                @endif
                            </a>
                        </div>

                        {{-- EGI Info --}}
                        <div class="flex-1 min-w-0">
                            <a href="/egi/{{ $egi['id'] }}" class="block transition-colors hover:text-blue-300">
                                <div class="text-sm font-medium text-white truncate">
                                    {{ $egi['title'] ?? 'Untitled EGI' }}</div>
                                <div class="text-xs text-gray-400 truncate">
                                    {{ $egi['collection_name'] ?? 'Unknown Collection' }}</div>
                            </a>
                            <a href="{{ $ownerRoute }}" class="block transition-colors hover:text-blue-300">
                                <div class="text-xs text-blue-300">di {{ $egi['owner_name'] ?? 'Unknown Owner' }}
                                </div>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="py-8 text-center text-gray-400">
                {{ __('statistics.no_egis_liked_yet') }}
            </div>
        @endif
    </div>
</div>

{{-- Owners Tab Content --}}
<div id="likes-given-owners-tab" class="hidden likes-given-tab-content">
    <div class="mb-4">
        <h4 class="mb-3 text-lg font-medium text-white">{{ __('statistics.users_you_liked') }}</h4>

        @if (count($givenLikesStats['owners']) > 0)
            <div class="space-y-2">
                @foreach ($givenLikesStats['owners'] as $index => $owner)
                    @php
                        $maxLikes = $givenLikesStats['owners'][0]['likes_count'] ?? 1;
                        $percentage = ($owner['likes_count'] / $maxLikes) * 100;

                        // Switcher per la route corretta basata sul usertype dell'owner
                        $ownerUserId = $owner['user_id'];
                        $ownerUserObject = $owner['user'] ?? null; // Oggetto User già caricato dal service
                        $ownerRoute = '#'; // Fallback

                        if ($ownerUserObject) {
                            $ownerRoute = match ($ownerUserObject->usertype ?? 'creator') {
                                'creator' => $owner['nick_name']
                                    ? route('creator.home.nickname', $owner['nick_name'])
                                    : route('creator.home', $ownerUserId),
                                'collector' => route('collector.home', $ownerUserId),
                                'commissioner' => route(
                                    'profile.show',
                                ), // Commissioner non ha pagina pubblica specifica
                                default => route('creator.home', $ownerUserId), // Fallback a creator
                            };
                        }
                    @endphp
                    <div
                        class="flex items-center p-3 space-x-3 transition-all duration-200 bg-black rounded-lg bg-opacity-20 hover:bg-opacity-30">
                        {{-- Rank --}}
                        <div
                            class="flex items-center justify-center flex-shrink-0 w-8 h-8 text-sm font-bold text-white rounded-full bg-gradient-to-r from-green-500 to-teal-500">
                            {{ $index + 1 }}
                        </div>

                        {{-- User Avatar --}}
                        <div class="relative flex-shrink-0">
                            @if ($owner['user']->profile_photo_url)
                                <img src="{{ $owner['user']->profile_photo_url }}" alt="{{ $owner['nickname'] }}"
                                    class="object-cover w-10 h-10 border-2 border-green-400 rounded-full">
                            @else
                                <div
                                    class="flex items-center justify-center w-10 h-10 border-2 border-green-400 rounded-full bg-gradient-to-r from-green-500 to-teal-500">
                                    <span
                                        class="text-sm font-bold text-white">{{ strtoupper(substr($owner['nickname'], 0, 1)) }}</span>
                                </div>
                            @endif
                        </div>

                        {{-- Owner Info --}}
                        <div class="flex-1 min-w-0">
                            <a href="{{ $ownerRoute }}" class="block transition-colors hover:text-green-300">
                                <div class="text-sm font-medium text-white truncate">{{ $owner['nickname'] }}</div>
                                <div class="text-xs text-gray-400">{{ $owner['likes_count'] }}
                                    {{ __('statistics.likes_received_from_you') }}
                                </div>
                            </a>
                        </div>

                        {{-- Likes Count --}}
                        <div class="flex-shrink-0 text-sm font-bold text-green-400">
                            {{ $owner['likes_count'] }}
                        </div>

                        {{-- Progress Bar --}}
                        <div class="flex-shrink-0 w-20">
                            <div class="w-full h-2 bg-gray-700 rounded-full">
                                <div class="h-2 rounded-full bg-gradient-to-r from-green-500 to-teal-500"
                                    style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="py-8 text-center text-gray-400">
                {{ __('statistics.no_users_liked_yet') }}
            </div>
        @endif
    </div>
</div>


{{-- JavaScript for Tab Switching --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab switching functionality for given likes
        const tabButtons = document.querySelectorAll('.likes-given-tab-btn');
        const tabContents = document.querySelectorAll('.likes-given-tab-content');

        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetTab = this.dataset.tab;

                // Update button states
                tabButtons.forEach(btn => {
                    btn.classList.remove('bg-blue-600', 'text-white');
                    btn.classList.add('text-gray-300');
                });
                this.classList.add('bg-blue-600', 'text-white');
                this.classList.remove('text-gray-300');

                // Update content visibility
                tabContents.forEach(content => {
                    content.classList.add('hidden');
                });

                const targetContent = document.getElementById(`likes-given-${targetTab}-tab`);
                if (targetContent) {
                    targetContent.classList.remove('hidden');
                }
            });
        });
    });
</script>
