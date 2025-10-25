{{-- Community Content - Partial View --}}
<div class="min-h-screen bg-gray-800">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-8">
            <h2 class="mb-4 text-3xl font-bold text-white">{{ __('creator.community.title') }}</h2>
            <p class="text-lg text-gray-400">{{ __('creator.community.subtitle') }}</p>
        </div>

        {{-- Stats Grid --}}
        <div class="mb-8 grid grid-cols-1 gap-6 sm:grid-cols-3">
            <div class="rounded-lg border border-gray-700 bg-gray-900/50 p-6">
                <div class="mb-2 flex items-center gap-3">
                    <svg class="h-8 w-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span class="block text-3xl font-bold text-purple-400">{{ $stats['total_supporters'] ?? 0 }}</span>
                </div>
                <span class="text-sm text-gray-300">{{ __('creator.community.total_supporters') }}</span>
            </div>

            <div class="rounded-lg border border-gray-700 bg-gray-900/50 p-6">
                <div class="mb-2 flex items-center gap-3">
                    <svg class="h-8 w-8 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    <span class="block text-3xl font-bold text-pink-400">0</span>
                </div>
                <span class="text-sm text-gray-300">{{ __('creator.community.total_likes') }}</span>
            </div>

            <div class="rounded-lg border border-gray-700 bg-gray-900/50 p-6">
                <div class="mb-2 flex items-center gap-3">
                    <svg class="h-8 w-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <span class="block text-3xl font-bold text-blue-400">0</span>
                </div>
                <span class="text-sm text-gray-300">{{ __('creator.community.total_comments') }}</span>
            </div>
        </div>

        {{-- Recent Supporters --}}
        <div class="mb-8">
            <h3 class="mb-4 text-xl font-bold text-white">{{ __('creator.community.recent_supporters') }}</h3>
            <div class="rounded-lg border border-gray-700 bg-gray-900/50 p-6">
                <p class="py-8 text-center text-gray-400">{{ __('creator.community.no_supporters_yet') }}</p>
            </div>
        </div>

        {{-- Activity Feed --}}
        <div class="mb-8">
            <h3 class="mb-4 text-xl font-bold text-white">{{ __('creator.community.activity_feed') }}</h3>
            <div class="rounded-lg border border-gray-700 bg-gray-900/50 p-6">
                <p class="py-8 text-center text-gray-400">{{ __('creator.community.no_activity_yet') }}</p>
            </div>
        </div>

        {{-- Engagement Tips --}}
        <div class="rounded-lg border border-purple-700/50 bg-gradient-to-br from-purple-900/30 to-blue-900/30 p-6">
            <h3 class="mb-4 text-lg font-bold text-white">{{ __('creator.community.engagement_tips_title') }}</h3>
            <ul class="space-y-3 text-gray-300">
                <li class="flex items-start gap-3">
                    <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-purple-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ __('creator.community.tip_1') }}</span>
                </li>
                <li class="flex items-start gap-3">
                    <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-purple-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ __('creator.community.tip_2') }}</span>
                </li>
                <li class="flex items-start gap-3">
                    <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-purple-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ __('creator.community.tip_3') }}</span>
                </li>
            </ul>
        </div>

    </div>
</div>
