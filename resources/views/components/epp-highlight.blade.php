{{-- resources/views/components/impact-projects-highlight.blade.php --}}

@props(['highlightedEpps'])

<section class="py-16 bg-gray-900 md:py-20"
        aria-labelledby="environmental-impact-heading">
    <div class="container px-4 mx-auto sm:px-6 lg:px-8">
        <h2 id="environmental-impact-heading"
            class="mb-4 text-3xl font-bold text-center text-emerald-400">
            {{ __('guest_home.your_impact_counts_title') }}
        </h2>
        <p class="max-w-3xl mx-auto mb-10 text-lg text-center text-gray-300">
            {{ __('guest_home.your_impact_counts_description') }}</p>
        <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
            @foreach ($highlightedEpps as $index => $epp)
            <article
                class="relative flex flex-col items-center p-6 text-center transition duration-300 ease-in-out rounded-xl border border-gray-700 shadow-sm bg-gray-800 hover:shadow-md hover:transform hover:translate-y-[-4px]">
                {{-- Rarity Badge --}}
                <div class="absolute right-2 top-2">
                    <span
                        class="@if ($index === 0) bg-amber-100 text-amber-800 border border-amber-200
                                @elseif($index === 1) bg-purple-100 text-purple-800 border border-purple-200
                                @else bg-blue-100 text-blue-800 border border-blue-200 @endif rounded-full px-3 py-1 text-xs font-bold">
                        @if ($index === 0)
                        LEGENDARY
                        @elseif($index === 1)
                        EPIC
                        @else
                        RARE
                        @endif
                    </span>
                </div>

                <div class="inline-block p-3 mb-4 rounded-full shadow-sm
                    @if ($index === 0) bg-amber-100 text-amber-700
                    @elseif($index === 1) bg-purple-100 text-purple-700
                    @else bg-blue-100 text-blue-700 @endif"
                    aria-hidden="true">
                    @if ($epp->type === 'ARF')
                    <span class="text-3xl material-symbols-outlined">forest</span>
                    @elseif($epp->type === 'APR')
                    <span class="text-3xl material-symbols-outlined">waves</span>
                    @elseif($epp->type === 'BPE')
                    <span class="text-3xl material-symbols-outlined">hive</span>
                    @else
                    <span class="text-3xl material-symbols-outlined">eco</span>
                    @endif
                </div>
                <h3 class="mb-2 text-xl font-semibold text-gray-100">{{ $epp->name }}</h3>
                <p class="flex-grow mb-4 text-sm text-gray-300 line-clamp-3">{{ $epp->description }}
                </p>
                <div class="relative mt-auto">
                    <a href="{{ route('epps.show', $epp->id) }}"
                        class="inline-flex items-center px-4 py-2 transition border rounded-md border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100">
                        <span>{{ __('guest_home.discover_more') }}</span>
                        <svg class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                        </svg>
                    </a>
                </div>
            </article>
            @endforeach
        </div>
        <div class="mt-12 text-center">
            <a href="{{ route('epps.index') }}"
                class="inline-flex items-center px-6 py-3 font-medium text-white transition rounded-md shadow-sm bg-emerald-600 hover:bg-emerald-700">
                {{ __('guest_home.view_all_supported_projects') }}
            </a>
        </div>
    </div>
</section>
