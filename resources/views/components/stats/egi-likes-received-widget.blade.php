{{--
/**
 * EGI Likes Received Widget Component
 *
 * @package FlorenceEGI
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - EGI Likes Analytics)
 * @date 2024-12-19
 * @purpose Display users who liked a specific EGI for creators
 */
--}}

@props(['egiId'])

@php
    use App\Services\StatisticsService;

    $statisticsService = app(StatisticsService::class);
    $likesData = $statisticsService->getEgiLikesReceived($egiId);
    $totalLikes = $likesData['total_likes'] ?? 0;
    $usersWhoLiked = $likesData['users_who_liked'] ?? [];
@endphp

<div class="p-6 bg-white max-h-96 rounded-xl bg-opacity-10 backdrop-blur-md">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h3 class="flex items-center text-xl font-semibold text-white">
            <svg class="w-6 h-6 mr-2 text-pink-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M3.172 5.172a4 4 0 515.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                    clip-rule="evenodd"></path>
            </svg>
            {{ __('statistics.likes_received_for_this_egi') }}
        </h3>

        {{-- Total Likes Count --}}
        <div class="text-right">
            <div class="text-3xl font-bold text-pink-400">{{ number_format($totalLikes) }}</div>
            <div class="text-sm text-gray-300">{{ __('statistics.total_likes') }}</div>
        </div>
    </div>

    {{-- Users Who Liked This EGI --}}
    @if ($totalLikes > 0)
        <div class="space-y-3 max-h-48 overflow-y-auto border border-gray-700 rounded-lg p-2" style="scrollbar-width: thin; scrollbar-color: #6b7280 #374151;">
            @foreach ($usersWhoLiked as $userLike)
                @php
                    $user = $userLike['user'];
                    $avatarUrl =
                        $user->profile_photo_url ??
                        'https://dicebear.com/api/initials/' . urlencode($userLike['nickname']) . '.svg';

                    // Determine route based on user type
                    $userId = $user->id;
                    $userObject = $user; // User object already loaded
                    $profileRoute = '#'; // Fallback

                    if ($userObject) {
                        $profileRoute = match ($userObject->usertype ?? 'creator') {
                            'creator' => route('creator.home', $userId),
                            'collector' => route('collector.home', $userId),
                            'commissioner' => route(
                                'profile.show',
                                $userId,
                            ), // Commissioner non ha pagina pubblica specifica
                            default => route('creator.home', $userId), // Fallback a creator
                        };
                    }
                @endphp

                <div
                    class="flex items-center justify-between p-3 transition-colors bg-black rounded-lg bg-opacity-20 hover:bg-opacity-30">
                    <div class="flex items-center space-x-3">
                        <a href="{{ $profileRoute }}" class="flex-shrink-0">
                            <img src="{{ $avatarUrl }}" alt="{{ $userLike['nickname'] }}"
                                class="object-cover w-10 h-10 transition-colors border-2 border-gray-600 rounded-full hover:border-pink-400"
                                onerror="this.src='https://dicebear.com/api/initials/{{ urlencode($userLike['nickname']) }}.svg'">
                        </a>
                        <div>
                            <a href="{{ $profileRoute }}"
                                class="font-medium text-white transition-colors hover:text-pink-400">
                                {{ $userLike['nickname'] }}
                            </a>
                            @if ($userLike['nick_name'] && $userLike['nick_name'] !== $userLike['nickname'])
                                <p class="text-sm text-gray-400">{{ $userLike['nick_name'] }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="flex items-center justify-end mb-1 text-pink-400">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm font-medium">Like</span>
                        </div>
                        <p class="text-xs text-gray-400">
                            {{ \Carbon\Carbon::parse($userLike['liked_at'])->diffForHumans() }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="py-8 text-center">
            <svg class="w-12 h-12 mx-auto mb-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                </path>
            </svg>
            <p class="text-sm text-gray-300">{{ __('statistics.no_likes_received_yet') }}</p>
        </div>
    @endif
</div>
