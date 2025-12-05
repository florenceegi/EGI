{{-- Collections Content - Partial View for Company --}}
<div class="min-h-screen bg-gray-800">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-8">
            <h2 class="mb-4 text-3xl font-bold text-white">{{ __('company.collections.title') }}</h2>
            <p class="text-lg text-gray-400">{{ __('company.collections.subtitle') }}</p>
        </div>

        {{-- Stats --}}
        <div class="mb-8">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="rounded-lg border border-[#1E3A5F]/50 bg-gray-900/50 p-4">
                    <span class="text-[#C9A227] block text-2xl font-bold">{{ $stats['total_collections'] ?? 0 }}</span>
                    <span class="text-sm text-gray-300">{{ __('company.collections.total_collections') }}</span>
                </div>
                <div class="rounded-lg border border-[#1E3A5F]/50 bg-gray-900/50 p-4">
                    <span class="text-[#C9A227] block text-2xl font-bold">{{ $stats['total_egis'] ?? 0 }}</span>
                    <span class="text-sm text-gray-300">{{ __('company.collections.total_egis') }}</span>
                </div>
                <div class="rounded-lg border border-[#1E3A5F]/50 bg-gray-900/50 p-4">
                    <span class="text-[#C9A227] block text-2xl font-bold">{{ $stats['total_supporters'] ?? 0 }}</span>
                    <span class="text-sm text-gray-300">{{ __('company.collections.total_supporters') }}</span>
                </div>
            </div>
        </div>

        @if ($collections && $collections->count() > 0)
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach ($collections as $collection)
                    <x-home-collection-card :id="$collection->id" :collection="$collection" :editable="false" :show_save_button="false" />
                @endforeach
            </div>
        @else
            <div class="rounded-2xl border border-[#1E3A5F]/30 bg-gradient-to-br from-gray-800/50 to-transparent p-12 text-center">
                <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-[#1E3A5F]/20">
                    <svg class="h-10 w-10 text-[#C9A227]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <h3 class="mb-2 text-xl font-semibold text-white">{{ __('company.collections.empty_title') }}</h3>
                <p class="text-gray-400">{{ __('company.collections.empty_description') }}</p>
            </div>
        @endif

    </div>
</div>
