{{-- Impact Content - Partial View for Company --}}
<div class="min-h-screen bg-gray-800">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-8">
            <h2 class="mb-4 text-3xl font-bold text-white">{{ __('company.impact.title') }}</h2>
            <p class="text-lg text-gray-400">{{ __('company.impact.subtitle') }}</p>
        </div>

        {{-- EPP Stats --}}
        <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-3">
            <div class="rounded-2xl border border-[#2D7D46]/50 bg-gradient-to-br from-[#2D7D46]/20 to-transparent p-6 text-center">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-[#2D7D46]/30">
                    <svg class="h-8 w-8 text-[#2D7D46]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p class="text-3xl font-bold text-white">{{ $stats['impact_score'] ?? 0 }}</p>
                <p class="text-sm text-gray-400">{{ __('company.impact.impact_score') }}</p>
            </div>

            <div class="rounded-2xl border border-[#1E3A5F]/50 bg-gradient-to-br from-[#1E3A5F]/20 to-transparent p-6 text-center">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-[#1E3A5F]/30">
                    <svg class="h-8 w-8 text-[#C9A227]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <p class="text-3xl font-bold text-white">{{ $stats['total_egis'] ?? 0 }}</p>
                <p class="text-sm text-gray-400">{{ __('company.impact.egis_with_epp') }}</p>
            </div>

            <div class="rounded-2xl border border-[#C9A227]/50 bg-gradient-to-br from-[#C9A227]/20 to-transparent p-6 text-center">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-[#C9A227]/30">
                    <svg class="h-8 w-8 text-[#C9A227]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p class="text-3xl font-bold text-white">€0</p>
                <p class="text-sm text-gray-400">{{ __('company.impact.contributed') }}</p>
            </div>
        </div>

        {{-- EPP Projects Info --}}
        <div class="rounded-2xl border border-[#2D7D46]/30 bg-gradient-to-br from-gray-800/50 to-transparent p-8">
            <div class="flex items-start gap-6">
                <div class="flex-shrink-0">
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-[#2D7D46]/20">
                        <svg class="h-8 w-8 text-[#2D7D46]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                        </svg>
                    </div>
                </div>
                <div>
                    <h3 class="mb-2 text-xl font-bold text-white">{{ __('company.impact.epp_title') }}</h3>
                    <p class="mb-4 text-gray-400">{{ __('company.impact.epp_description') }}</p>
                    <div class="flex flex-wrap gap-2">
                        <span class="rounded-full bg-[#2D7D46]/20 px-3 py-1 text-xs font-medium text-[#2D7D46]">
                            {{ __('company.impact.tag_reforestation') }}
                        </span>
                        <span class="rounded-full bg-[#1E3A5F]/20 px-3 py-1 text-xs font-medium text-[#C9A227]">
                            {{ __('company.impact.tag_ocean') }}
                        </span>
                        <span class="rounded-full bg-[#2D7D46]/20 px-3 py-1 text-xs font-medium text-[#2D7D46]">
                            {{ __('company.impact.tag_biodiversity') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
