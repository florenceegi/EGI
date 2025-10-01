@props([
    'userId' => null,
    'creatorId' => null,
    'period' => 'month'
])

@php
    $targetUserId = $userId ?? $creatorId ?? auth()->id();
    $statisticsService = app(\App\Services\StatisticsService::class);

    // Ottieni statistiche dei like ricevuti (EGI dell'utente) con periodo temporale
    $receivedLikesStats = $statisticsService->getLikesReceivedStatsByPeriod($targetUserId, $period);

    // Ottieni statistiche di chi ha dato like agli EGI dell'utente
    $whoLikedStats = \App\Services\StatisticsService::getWhoLikedUserEgisStats($targetUserId);
@endphp

<div class="p-6 bg-white bg-opacity-10 backdrop-blur-md rounded-xl">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h3 class="flex items-center text-xl font-semibold text-white">
            <svg class="w-6 h-6 mr-2 text-pink-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
            </svg>
            {{ __('statistics.likes_received_analysis') }}
        </h3>

        {{-- Total Received Likes --}}
        <div class="text-right">
            <div class="text-3xl font-bold text-pink-400">{{ number_format($receivedLikesStats['total_received']) }}</div>
            <div class="text-sm text-gray-300">{{ __('statistics.total_likes_received') }}</div>
        </div>
    </div>

    {{-- Content Tabs --}}
    <div class="mb-4">
        <div class="flex p-1 space-x-1 bg-black rounded-lg bg-opacity-20">
            <button
                class="flex items-center justify-center flex-1 px-3 py-2 space-x-2 text-sm font-medium text-white transition-colors duration-200 bg-pink-600 rounded-md likes-tab-btn"
                data-tab="received"
            >
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                </svg>
                <span>EGI</span>
            </button>
            <button
                class="flex items-center justify-center flex-1 px-3 py-2 space-x-2 text-sm font-medium text-gray-300 transition-colors duration-200 rounded-md likes-tab-btn hover:text-white"
                data-tab="given"
            >
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                </svg>
                <span>User</span>
            </button>
        </div>
    </div>

    {{-- Received Likes Tab Content --}}
    <div id="likes-received-tab" class="likes-tab-content">
        <div class="mb-4">
            <h4 class="mb-3 text-lg font-medium text-white">{{ __('statistics.top_liked_egis') }}</h4>

            @if(count($receivedLikesStats['top_egis']) > 0)
                <div class="space-y-2">
                    @foreach($receivedLikesStats['top_egis'] as $index => $egi)
                        @php
                            $percentage = $receivedLikesStats['total_received'] > 0
                                ? ($egi['likes_count'] / $receivedLikesStats['total_received']) * 100
                                : 0;
                        @endphp
                        <a href="/egis/{{ $egi['id'] }}" class="flex items-center p-2 space-x-3 transition-colors bg-black rounded-lg bg-opacity-20 hover:bg-opacity-30">
                            {{-- Rank --}}
                            <div class="flex items-center justify-center flex-shrink-0 w-6 h-6 text-xs font-bold text-white rounded-full bg-gradient-to-r from-pink-500 to-rose-500">
                                {{ $index + 1 }}
                            </div>

                            {{-- EGI Image --}}
                            <div class="flex-shrink-0">
                                @if($egi['avatar_image_url'])
                                    <img src="{{ $egi['avatar_image_url'] }}" alt="{{ $egi['title'] }}" class="object-cover w-8 h-8 rounded">
                                @elseif($egi['thumbnail_image_url'])
                                    <img src="{{ $egi['thumbnail_image_url'] }}" alt="{{ $egi['title'] }}" class="object-cover w-8 h-8 rounded">
                                @elseif($egi['main_image_url'])
                                    <img src="{{ $egi['main_image_url'] }}" alt="{{ $egi['title'] }}" class="object-cover w-8 h-8 rounded">
                                @else
                                    <div class="flex items-center justify-center w-8 h-8 bg-gray-600 rounded">
                                        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            {{-- Title --}}
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-medium text-white truncate">{{ $egi['title'] ?? 'Untitled EGI' }}</div>
                            </div>

                            {{-- Likes Count --}}
                            <div class="flex-shrink-0 text-sm font-bold text-pink-400">
                                {{ $egi['likes_count'] }}
                            </div>

                            {{-- Progress Bar --}}
                            <div class="flex-shrink-0 w-20">
                                <div class="w-full h-2 bg-gray-700 rounded-full">
                                    <div class="h-2 rounded-full bg-gradient-to-r from-pink-500 to-rose-500" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="py-8 text-center text-gray-400">
                    {{ __('statistics.no_liked_egis') }}
                </div>
            @endif
        </div>
    </div>

    {{-- Given Likes Tab Content --}}
    <div id="likes-given-tab" class="hidden likes-tab-content">
        <div class="mb-4">
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-lg font-medium text-white">{{ __('statistics.top_liking_users') }}</h4>
                <div class="text-sm text-gray-300">
                    {{ __('statistics.total_given') }}: <span class="font-bold text-pink-400">{{ number_format($whoLikedStats['total_given']) }}</span>
                </div>
            </div>

            @if(count($whoLikedStats['top_users']) > 0)
                <div class="space-y-2">
                    @foreach($whoLikedStats['top_users'] as $index => $user)
                        @php
                            $maxLikes = $whoLikedStats['top_users'][0]['likes_given'] ?? 1;
                            $percentage = ($user['likes_given'] / $maxLikes) * 100;
                            // Il middleware CreatorNicknameRedirect gestisce automaticamente la conversione ID -> nick_name
                            $userRoute = route('creator.home', $user['user_id']);
                        @endphp
                        <a href="{{ $userRoute }}" class="flex items-center p-2 space-x-3 transition-colors bg-black rounded-lg bg-opacity-20 hover:bg-opacity-30">
                            {{-- Rank --}}
                            <div class="flex items-center justify-center flex-shrink-0 w-6 h-6 text-xs font-bold text-white rounded-full bg-gradient-to-r from-blue-500 to-purple-500">
                                {{ $index + 1 }}
                            </div>

                            {{-- User Avatar --}}
                            <div class="flex-shrink-0">
                                @if($user['user']->profile_photo_url)
                                    <img src="{{ $user['user']->profile_photo_url }}" alt="{{ $user['nickname'] }}" class="object-cover w-8 h-8 rounded-full">
                                @else
                                    <div class="flex items-center justify-center w-8 h-8 rounded-full bg-gradient-to-r from-pink-500 to-rose-500">
                                        <span class="text-xs font-bold text-white">{{ strtoupper(substr($user['nickname'], 0, 1)) }}</span>
                                    </div>
                                @endif
                            </div>

                            {{-- User Name --}}
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-medium text-white truncate">{{ $user['nickname'] }}</div>
                            </div>

                            {{-- Likes Count --}}
                            <div class="flex-shrink-0 text-sm font-bold text-pink-400">
                                {{ $user['likes_given'] }}
                            </div>

                            {{-- Progress Bar --}}
                            <div class="flex-shrink-0 w-20">
                                <div class="w-full h-2 bg-gray-700 rounded-full">
                                    <div class="h-2 rounded-full bg-gradient-to-r from-blue-500 to-purple-500" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="py-8 text-center text-gray-400">
                    {{ __('statistics.no_liking_users') }}
                </div>
            @endif
        </div>
    </div>
</div>

{{-- JavaScript for Tab Switching --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching functionality
    const tabButtons = document.querySelectorAll('.likes-tab-btn');
    const tabContents = document.querySelectorAll('.likes-tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.dataset.tab;

            // Update button states
            tabButtons.forEach(btn => {
                btn.classList.remove('bg-pink-600', 'text-white');
                btn.classList.add('text-gray-300');
            });
            this.classList.add('bg-pink-600', 'text-white');
            this.classList.remove('text-gray-300');

            // Update content visibility
            tabContents.forEach(content => {
                content.classList.add('hidden');
            });

            const targetContent = document.getElementById(`likes-${targetTab}-tab`);
            if (targetContent) {
                targetContent.classList.remove('hidden');
            }
        });
    });
});
</script>
