@vite(['resources/css/creator-home.css'])

<x-guest-layout :title="$company->name . ' - ' . __('company.collections.title')" :metaDescription="__('company.collections.meta_description', ['name' => $company->name])">

    @push('head')
        <style>
            :root {
                --company-primary: #1E3A5F;
                --company-accent: #C9A227;
                --company-success: #2D7D46;
            }
        </style>
    @endpush

    <x-slot name="platformInfoButtons">
        <div class="absolute inset-0 opacity-60" aria-hidden="true">
            <div class="absolute inset-0">
                <div class="absolute inset-0 bg-gradient-to-br from-[#1E3A5F] via-[#2A4A73] to-[#0F1F33]"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/70 to-transparent"></div>
            </div>
        </div>

        <div class="relative z-10 mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="flex flex-col items-center gap-4 sm:flex-row sm:items-center sm:gap-6">
                <div class="h-20 w-20 overflow-hidden rounded-xl bg-white shadow-xl ring-4 ring-[#C9A227]/40">
                    <img src="{{ $company->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($company->name) . '&size=80&background=1E3A5F&color=C9A227&bold=true' }}"
                        alt="{{ $company->name }}" class="h-full w-full object-contain p-1" loading="lazy">
                </div>
                <div class="text-center sm:text-left">
                    <p class="text-sm font-medium text-[#C9A227]">{{ __('company.collections.title') }}</p>
                    <h1 class="font-playfair text-3xl font-bold text-white md:text-4xl">
                        {{ $company->name }}
                    </h1>
                    <p class="text-gray-300">
                        {{ $collections->total() ?? 0 }} {{ __('company.home.collections_tab') }}
                    </p>
                </div>
            </div>
        </div>
    </x-slot>

    {{-- Navigation Tabs --}}
    <x-slot name="platformStats">
        @php $activeTab = 'collections'; @endphp
        <nav class="flex overflow-x-auto border-b border-gray-800 bg-gray-900/95">
            <div class="mx-auto flex w-full max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="scrollbar-hide flex w-full space-x-6">
                    <a href="{{ route('company.home', $company->id) }}"
                        class="whitespace-nowrap border-b-2 border-transparent px-6 py-4 text-sm font-medium text-gray-300 transition-colors hover:text-white">
                        {{ __('company.home.overview_tab') }}
                    </a>
                    <a href="{{ route('company.catalog', $company->id) }}"
                        class="whitespace-nowrap border-b-2 border-transparent px-6 py-4 text-sm font-medium text-gray-300 transition-colors hover:text-white">
                        {{ __('company.home.catalog_tab') }}
                    </a>
                    <a href="{{ route('company.collections', $company->id) }}"
                        class="whitespace-nowrap border-b-2 border-[#C9A227] px-6 py-4 text-sm font-medium text-[#C9A227] transition-colors">
                        {{ __('company.home.collections_tab') }}
                    </a>
                    <a href="{{ route('company.about', $company->id) }}"
                        class="whitespace-nowrap border-b-2 border-transparent px-6 py-4 text-sm font-medium text-gray-300 transition-colors hover:text-white">
                        {{ __('company.home.about_tab') }}
                    </a>
                </div>
            </div>
        </nav>
    </x-slot>

    <x-slot name="heroFullWidth">
        <section class="bg-gray-900 py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

                @if ($collections->count() > 0)
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($collections as $collection)
                            <article
                                class="group overflow-hidden rounded-xl border border-gray-800 bg-gray-900/70 transition-all duration-300 hover:border-[#C9A227]/60 hover:shadow-2xl hover:shadow-[#C9A227]/10">
                                {{-- Collection Cover --}}
                                <a href="{{ route('home.collections.show', $collection->id) }}" class="block">
                                    <div
                                        class="relative aspect-video overflow-hidden bg-gradient-to-br from-[#1E3A5F] to-[#0F1F33]">
                                        @if ($collection->cover_image)
                                            <img src="{{ $collection->cover_image }}"
                                                alt="{{ $collection->collection_name }}"
                                                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110">
                                        @else
                                            <div class="flex h-full items-center justify-center">
                                                <svg class="h-20 w-20 text-[#C9A227]/30" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="1.5"
                                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                                </svg>
                                            </div>
                                        @endif
                                        <div
                                            class="absolute inset-0 bg-gradient-to-t from-gray-900 via-transparent to-transparent">
                                        </div>
                                    </div>
                                </a>
                                <div class="p-6">
                                    <h3
                                        class="mb-2 text-xl font-semibold text-white transition-colors group-hover:text-[#C9A227]">
                                        <a href="{{ route('home.collections.show', $collection->id) }}">
                                            {{ $collection->collection_name }}
                                        </a>
                                    </h3>
                                    @if ($collection->description)
                                        <p class="mb-4 line-clamp-2 text-sm text-gray-400">
                                            {{ $collection->description }}
                                        </p>
                                    @endif
                                    <div class="flex items-center justify-between">
                                        <span class="flex items-center gap-2 text-sm text-gray-300">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                            </svg>
                                            {{ __('company.collections.products_count', ['count' => $collection->egis_count ?? 0]) }}
                                        </span>
                                        <a href="{{ route('home.collections.show', $collection->id) }}"
                                            class="text-sm font-medium text-[#C9A227] transition-colors hover:text-[#D4B445]">
                                            {{ __('company.collections.view_collection') }} →
                                        </a>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    @if ($collections->hasPages())
                        <div class="mt-8">
                            {{ $collections->links() }}
                        </div>
                    @endif
                @else
                    {{-- Empty State --}}
                    <div
                        class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-gray-700 bg-gray-900/60 px-6 py-16 text-center">
                        <div class="mb-6 rounded-full bg-[#1E3A5F]/30 p-6">
                            <svg class="h-16 w-16 text-[#C9A227]/50" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <h3 class="mb-2 text-2xl font-bold text-white">
                            {{ __('company.collections.empty_title') }}
                        </h3>
                        <p class="max-w-md text-gray-400">
                            {{ __('company.collections.empty_description') }}
                        </p>
                    </div>
                @endif

            </div>
        </section>
    </x-slot>
</x-guest-layout>
