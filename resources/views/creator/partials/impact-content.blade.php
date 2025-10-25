{{-- Impact Content - Partial View --}}
<div class="min-h-screen bg-gray-800">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-8">
            <h2 class="mb-4 text-3xl font-bold text-white">{{ __('creator.impact.title') }}</h2>
            <p class="text-lg text-gray-400">{{ __('creator.impact.subtitle') }}</p>
        </div>

        {{-- Impact Score Card --}}
        <div class="mb-8">
            <div class="rounded-lg border border-green-700 bg-gradient-to-br from-green-900/50 to-emerald-900/50 p-6">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-xl font-bold text-white">{{ __('creator.impact.impact_score') }}</h3>
                    <svg class="h-8 w-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="text-center">
                    <span class="mb-2 block text-5xl font-bold text-green-400">{{ $stats['impact_score'] ?? 0 }}</span>
                    <p class="text-gray-300">{{ __('creator.impact.impact_score_description') }}</p>
                </div>
            </div>
        </div>

        {{-- Stats Grid --}}
        <div class="mb-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-lg border border-gray-700 bg-gray-900/50 p-4">
                <div class="mb-2 flex items-center gap-3">
                    <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <span class="block text-2xl font-bold text-blue-400">{{ $stats['total_collections'] ?? 0 }}</span>
                </div>
                <span class="text-sm text-gray-300">{{ __('creator.impact.total_collections') }}</span>
            </div>

            <div class="rounded-lg border border-gray-700 bg-gray-900/50 p-4">
                <div class="mb-2 flex items-center gap-3">
                    <svg class="h-6 w-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="block text-2xl font-bold text-purple-400">{{ $stats['total_egis'] ?? 0 }}</span>
                </div>
                <span class="text-sm text-gray-300">{{ __('creator.impact.total_artworks') }}</span>
            </div>

            <div class="rounded-lg border border-gray-700 bg-gray-900/50 p-4">
                <div class="mb-2 flex items-center gap-3">
                    <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span class="block text-2xl font-bold text-green-400">{{ $stats['total_supporters'] ?? 0 }}</span>
                </div>
                <span class="text-sm text-gray-300">{{ __('creator.impact.total_supporters') }}</span>
            </div>

            <div class="rounded-lg border border-gray-700 bg-gray-900/50 p-4">
                <div class="mb-2 flex items-center gap-3">
                    <svg class="h-6 w-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span class="block text-2xl font-bold text-yellow-400">0</span>
                </div>
                <span class="text-sm text-gray-300">{{ __('creator.impact.total_energy_saved') }}</span>
            </div>
        </div>

        {{-- Environmental Impact --}}
        <div class="mb-8">
            <h3 class="mb-4 text-xl font-bold text-white">{{ __('creator.impact.environmental_impact') }}</h3>
            <div class="rounded-lg border border-gray-700 bg-gray-900/50 p-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                            </svg>
                            <span class="text-gray-300">{{ __('creator.impact.carbon_footprint') }}</span>
                        </div>
                        <span class="text-xl font-bold text-green-400">0 kg CO₂</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                            </svg>
                            <span class="text-gray-300">{{ __('creator.impact.trees_planted') }}</span>
                        </div>
                        <span class="text-xl font-bold text-blue-400">0</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <svg class="h-6 w-6 text-yellow-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span class="text-gray-300">{{ __('creator.impact.renewable_energy') }}</span>
                        </div>
                        <span class="text-xl font-bold text-yellow-400">100%</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Info Message --}}
        <div class="rounded-lg border border-blue-700/50 bg-blue-900/30 p-4">
            <div class="flex items-start gap-3">
                <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-blue-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-sm text-blue-200">{{ __('creator.impact.info_message') }}</p>
            </div>
        </div>

    </div>
</div>
