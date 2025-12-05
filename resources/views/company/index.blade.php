@vite(['resources/css/creator-home.css'])

<x-guest-layout :title="__('company.index.title')" :metaDescription="__('company.index.meta_description')">

    @push('head')
        <style>
            :root {
                --company-primary: #1E3A5F;
                --company-primary-light: #2A4A73;
                --company-accent: #C9A227;
                --company-accent-light: #D4B445;
                --company-success: #2D7D46;
            }
        </style>
    @endpush

    <x-slot name="platformInfoButtons">
        <div class="absolute inset-0 opacity-60" aria-hidden="true">
            <div class="absolute inset-0 bg-gradient-to-br from-[#1E3A5F] via-[#2A4A73] to-[#0F1F33]"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/70 to-transparent"></div>
        </div>

        <div class="relative z-10 mx-auto max-w-7xl px-4 py-16 text-center sm:px-6 lg:px-8">
            <div class="mb-4 inline-flex items-center gap-2 rounded-full bg-[#C9A227]/20 px-4 py-2 text-sm font-medium text-[#C9A227]">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                {{ __('company.index.badge') }}
            </div>
            <h1 class="font-playfair mb-4 text-4xl font-bold text-white md:text-5xl">
                {{ __('company.index.title') }}
            </h1>
            <p class="mx-auto max-w-2xl text-lg text-gray-300">
                {{ __('company.index.subtitle') }}
            </p>
        </div>
    </x-slot>

    <x-slot name="heroFullWidth">
        <section class="bg-gray-900 py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

                {{-- Search & Filters --}}
                <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <form action="{{ route('company.index') }}" method="GET" class="flex-1 sm:max-w-md">
                        <div class="relative">
                            <input type="text" name="query" value="{{ $query ?? '' }}"
                                placeholder="{{ __('company.index.search_placeholder') }}"
                                class="w-full rounded-lg border-gray-700 bg-gray-800/80 py-3 pl-12 pr-4 text-white placeholder-gray-400 focus:border-[#C9A227] focus:ring-[#C9A227]">
                            <svg class="absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </form>

                    <div class="flex items-center gap-3">
                        <span class="text-sm text-gray-400">{{ __('company.index.sort_by') }}:</span>
                        <div class="flex gap-2">
                            <a href="{{ route('company.index', array_merge(request()->except('sort'), ['sort' => 'latest'])) }}"
                                class="rounded-lg px-4 py-2 text-sm font-medium transition-colors {{ ($sort ?? 'latest') === 'latest' ? 'bg-[#1E3A5F] text-[#C9A227]' : 'bg-gray-800 text-gray-300 hover:bg-gray-700' }}">
                                {{ __('company.index.sort_latest') }}
                            </a>
                            <a href="{{ route('company.index', array_merge(request()->except('sort'), ['sort' => 'most_products'])) }}"
                                class="rounded-lg px-4 py-2 text-sm font-medium transition-colors {{ ($sort ?? '') === 'most_products' ? 'bg-[#1E3A5F] text-[#C9A227]' : 'bg-gray-800 text-gray-300 hover:bg-gray-700' }}">
                                {{ __('company.index.sort_most_products') }}
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Companies Grid --}}
                @if ($companies->count() > 0)
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                        @foreach ($companies as $company)
                            <article class="group overflow-hidden rounded-xl border border-gray-800 bg-gray-900/70 transition-all duration-300 hover:border-[#C9A227]/60 hover:shadow-2xl hover:shadow-[#C9A227]/10">
                                <a href="{{ route('company.home', $company->id) }}" class="block">
                                    {{-- Company Logo --}}
                                    <div class="relative aspect-[4/3] overflow-hidden bg-gradient-to-br from-[#1E3A5F] to-[#0F1F33]">
                                        @if ($company->profile_photo_url)
                                            <div class="flex h-full items-center justify-center p-8">
                                                <img src="{{ $company->profile_photo_url }}" alt="{{ $company->name }}"
                                                    class="max-h-full max-w-full object-contain transition-transform duration-500 group-hover:scale-110">
                                            </div>
                                        @else
                                            <div class="flex h-full items-center justify-center">
                                                <div class="flex h-24 w-24 items-center justify-center rounded-xl bg-[#1E3A5F] text-4xl font-bold text-[#C9A227]">
                                                    {{ substr($company->name, 0, 2) }}
                                                </div>
                                            </div>
                                        @endif
                                        <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-transparent to-transparent"></div>

                                        {{-- Verified Badge --}}
                                        @if ($company->is_verified ?? false)
                                            <div class="absolute right-3 top-3 rounded-full bg-[#2D7D46] p-2 shadow-lg">
                                                <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                </a>

                                <div class="p-5">
                                    {{-- Company Name --}}
                                    <h3 class="mb-2 text-lg font-semibold text-white transition-colors group-hover:text-[#C9A227]">
                                        <a href="{{ route('company.home', $company->id) }}">
                                            {{ $company->name }}
                                        </a>
                                    </h3>

                                    {{-- Industry/Tagline --}}
                                    @if ($company->tagline ?? $company->industry ?? null)
                                        <p class="mb-3 line-clamp-2 text-sm text-gray-400">
                                            {{ $company->tagline ?? $company->industry }}
                                        </p>
                                    @endif

                                    {{-- Stats --}}
                                    <div class="flex items-center justify-between border-t border-gray-800 pt-4">
                                        <div class="flex items-center gap-1 text-sm text-gray-300">
                                            <svg class="h-4 w-4 text-[#C9A227]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                            </svg>
                                            {{ $company->products_count ?? 0 }} {{ __('company.index.products') }}
                                        </div>
                                        <span class="text-xs text-gray-500">
                                            {{ __('company.index.member_since', ['year' => $company->created_at->format('Y')]) }}
                                        </span>
                                    </div>

                                    {{-- Preview Products --}}
                                    @if ($company->createdEgis->count() > 0)
                                        <div class="mt-4 flex -space-x-2">
                                            @foreach ($company->createdEgis->take(3) as $egi)
                                                <div class="h-10 w-10 overflow-hidden rounded-lg border-2 border-gray-800 bg-gray-700">
                                                    @if ($egi->thumbnail_url ?? $egi->main_image_url ?? null)
                                                        <img src="{{ $egi->thumbnail_url ?? $egi->main_image_url }}" alt="{{ $egi->title }}"
                                                            class="h-full w-full object-cover">
                                                    @endif
                                                </div>
                                            @endforeach
                                            @if ($company->products_count > 3)
                                                <div class="flex h-10 w-10 items-center justify-center rounded-lg border-2 border-gray-800 bg-[#1E3A5F] text-xs font-medium text-[#C9A227]">
                                                    +{{ $company->products_count - 3 }}
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    @if ($companies->hasPages())
                        <div class="mt-8">
                            {{ $companies->withQueryString()->links() }}
                        </div>
                    @endif
                @else
                    {{-- Empty State --}}
                    <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-gray-700 bg-gray-900/60 px-6 py-16 text-center">
                        <div class="mb-6 rounded-full bg-[#1E3A5F]/30 p-6">
                            <svg class="h-16 w-16 text-[#C9A227]/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <h3 class="mb-2 text-2xl font-bold text-white">
                            {{ __('company.index.empty_title') }}
                        </h3>
                        <p class="max-w-md text-gray-400">
                            @if ($query)
                                {{ __('company.index.empty_search', ['query' => $query]) }}
                            @else
                                {{ __('company.index.empty_description') }}
                            @endif
                        </p>
                    </div>
                @endif

            </div>
        </section>
    </x-slot>
</x-guest-layout>
