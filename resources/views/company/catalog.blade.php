@vite(['resources/css/creator-home.css'])

<x-guest-layout :title="$company->name . ' - ' . __('company.catalog.title')" :metaDescription="__('company.catalog.meta_description', ['name' => $company->name])">

    @push('head')
        <style>
            /* Company Corporate Palette */
            :root {
                --company-primary: #1E3A5F;
                --company-primary-light: #2A4A73;
                --company-accent: #C9A227;
                --company-accent-light: #D4B445;
                --company-success: #2D7D46;
                --company-success-light: #3A9A5A;
            }

            .bg-company-primary {
                background-color: var(--company-primary);
            }

            .bg-company-accent {
                background-color: var(--company-accent);
            }

            .text-company-primary {
                color: var(--company-primary);
            }

            .text-company-accent {
                color: var(--company-accent);
            }

            .border-company-accent {
                border-color: var(--company-accent);
            }
        </style>
    @endpush

    <x-slot name="platformInfoButtons">
        <div class="absolute inset-0 opacity-60" aria-hidden="true">
            <div class="absolute inset-0">
                @php
                    $bannerUrl = method_exists($company, 'getCreatorBannerUrl')
                        ? $company->getCreatorBannerUrl('banner')
                        : null;
                @endphp
                @if ($bannerUrl)
                    <img src="{{ $bannerUrl }}" alt="Banner for {{ $company->name }}"
                        class="h-full w-full object-cover">
                @else
                    <div class="absolute inset-0 bg-gradient-to-br from-[#1E3A5F] via-[#2A4A73] to-[#0F1F33]"></div>
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/70 to-transparent"></div>
            </div>
        </div>

        <div class="relative z-10 mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="flex flex-col items-center gap-4 sm:flex-row sm:items-center sm:gap-6">
                {{-- Company Logo --}}
                <div class="h-20 w-20 overflow-hidden rounded-xl bg-white shadow-xl ring-4 ring-[#C9A227]/40">
                    <img src="{{ $company->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($company->name) . '&size=80&background=1E3A5F&color=C9A227&bold=true' }}"
                        alt="{{ $company->name }}" class="h-full w-full object-contain p-1" loading="lazy">
                </div>
                <div class="text-center sm:text-left">
                    <p class="text-sm font-medium text-[#C9A227]">{{ __('company.catalog.catalog_of') }}</p>
                    <h1 class="font-playfair text-3xl font-bold text-white md:text-4xl">
                        {{ $company->name }}
                    </h1>
                    <p class="text-gray-300">
                        {{ $stats['total_products'] ?? 0 }} {{ __('company.catalog.products_available') }}
                    </p>
                </div>
            </div>
        </div>
    </x-slot>

    {{-- Navigation Tabs --}}
    <x-slot name="platformStats">
        @php
            $activeTab = 'catalog';
        @endphp
        <nav class="flex overflow-x-auto border-b border-gray-800 bg-gray-900/95"
            aria-label="{{ __('company.home.navigation_aria') }}">
            <div class="mx-auto flex w-full max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="scrollbar-hide flex w-full space-x-6">
                    <a href="{{ route('company.home', $company->id) }}"
                        class="whitespace-nowrap border-b-2 border-transparent px-6 py-4 text-sm font-medium text-gray-300 transition-colors hover:text-white">
                        {{ __('company.home.overview_tab') }}
                    </a>
                    <a href="{{ route('company.catalog', $company->id) }}"
                        class="whitespace-nowrap border-b-2 border-[#C9A227] px-6 py-4 text-sm font-medium text-[#C9A227] transition-colors">
                        {{ __('company.home.catalog_tab') }}
                    </a>
                    <a href="{{ route('company.collections', $company->id) }}"
                        class="whitespace-nowrap border-b-2 border-transparent px-6 py-4 text-sm font-medium text-gray-300 transition-colors hover:text-white">
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
        <section class="bg-gray-900 py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

                {{-- Filters and Search --}}
                <div class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    {{-- Search --}}
                    <div class="flex-1 lg:max-w-md">
                        <form action="{{ route('company.catalog', $company->id) }}" method="GET" class="relative">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="{{ __('company.catalog.search_placeholder') }}"
                                class="w-full rounded-lg border-gray-700 bg-gray-800/80 py-3 pl-12 pr-4 text-white placeholder-gray-400 focus:border-[#C9A227] focus:ring-[#C9A227]">
                            <svg class="absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            @if (request('category'))
                                <input type="hidden" name="category" value="{{ request('category') }}">
                            @endif
                        </form>
                    </div>

                    {{-- Filters --}}
                    <div class="flex flex-wrap items-center gap-3">
                        {{-- Category Filter --}}
                        @if (isset($categories) && $categories->count() > 0)
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" type="button"
                                    class="flex items-center gap-2 rounded-lg border border-gray-700 bg-gray-800/80 px-4 py-2.5 text-sm font-medium text-white transition-colors hover:border-[#C9A227]">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                    </svg>
                                    {{ request('category') ? $categories->firstWhere('slug', request('category'))?->name ?? __('company.catalog.all_categories') : __('company.catalog.all_categories') }}
                                    <svg class="h-4 w-4" :class="{ 'rotate-180': open }" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div x-show="open" @click.away="open = false" x-transition
                                    class="absolute right-0 z-20 mt-2 w-56 origin-top-right rounded-lg border border-gray-700 bg-gray-800 py-2 shadow-xl">
                                    <a href="{{ route('company.catalog', array_merge(['company' => $company->id], request()->except('category', 'page'))) }}"
                                        class="{{ !request('category') ? 'bg-[#1E3A5F]/50 text-[#C9A227]' : 'text-gray-300 hover:bg-gray-700' }} block px-4 py-2 text-sm">
                                        {{ __('company.catalog.all_categories') }}
                                    </a>
                                    @foreach ($categories as $category)
                                        <a href="{{ route('company.catalog', array_merge(['company' => $company->id, 'category' => $category->slug], request()->except('page'))) }}"
                                            class="{{ request('category') === $category->slug ? 'bg-[#1E3A5F]/50 text-[#C9A227]' : 'text-gray-300 hover:bg-gray-700' }} block px-4 py-2 text-sm">
                                            {{ $category->name }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Sort --}}
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" type="button"
                                class="flex items-center gap-2 rounded-lg border border-gray-700 bg-gray-800/80 px-4 py-2.5 text-sm font-medium text-white transition-colors hover:border-[#C9A227]">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                                </svg>
                                {{ __('company.catalog.sort_' . request('sort', 'newest')) }}
                                <svg class="h-4 w-4" :class="{ 'rotate-180': open }" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition
                                class="absolute right-0 z-20 mt-2 w-48 origin-top-right rounded-lg border border-gray-700 bg-gray-800 py-2 shadow-xl">
                                @foreach (['newest', 'oldest', 'price_low', 'price_high', 'popular'] as $sortOption)
                                    <a href="{{ route('company.catalog', array_merge(['company' => $company->id, 'sort' => $sortOption], request()->except('page', 'sort'))) }}"
                                        class="{{ request('sort', 'newest') === $sortOption ? 'bg-[#1E3A5F]/50 text-[#C9A227]' : 'text-gray-300 hover:bg-gray-700' }} block px-4 py-2 text-sm">
                                        {{ __('company.catalog.sort_' . $sortOption) }}
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        {{-- View Toggle --}}
                        <div class="flex items-center gap-1 rounded-lg border border-gray-700 bg-gray-800/80 p-1">
                            <button type="button" onclick="setViewMode('grid')"
                                class="rounded-md p-2 transition-colors"
                                :class="viewMode === 'grid' ? 'bg-[#1E3A5F] text-[#C9A227]' :
                                    'text-gray-400 hover:text-white'"
                                x-data
                                x-bind:class="viewMode === 'grid' ? 'bg-[#1E3A5F] text-[#C9A227]' :
                                    'text-gray-400 hover:text-white'">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                </svg>
                            </button>
                            <button type="button" onclick="setViewMode('list')"
                                class="rounded-md p-2 transition-colors" x-data
                                x-bind:class="viewMode === 'list' ? 'bg-[#1E3A5F] text-[#C9A227]' :
                                    'text-gray-400 hover:text-white'">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Active Filters --}}
                @if (request('search') || request('category'))
                    <div class="mb-6 flex flex-wrap items-center gap-2">
                        <span class="text-sm text-gray-400">{{ __('company.catalog.active_filters') }}:</span>
                        @if (request('search'))
                            <span
                                class="inline-flex items-center gap-1 rounded-full bg-[#1E3A5F]/50 px-3 py-1 text-sm text-[#C9A227]">
                                "{{ request('search') }}"
                                <a href="{{ route('company.catalog', array_merge(['company' => $company->id], request()->except('search', 'page'))) }}"
                                    class="ml-1 rounded-full p-0.5 hover:bg-[#1E3A5F]">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </a>
                            </span>
                        @endif
                        @if (request('category'))
                            <span
                                class="inline-flex items-center gap-1 rounded-full bg-[#1E3A5F]/50 px-3 py-1 text-sm text-[#C9A227]">
                                {{ $categories->firstWhere('slug', request('category'))?->name ?? request('category') }}
                                <a href="{{ route('company.catalog', array_merge(['company' => $company->id], request()->except('category', 'page'))) }}"
                                    class="ml-1 rounded-full p-0.5 hover:bg-[#1E3A5F]">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </a>
                            </span>
                        @endif
                        <a href="{{ route('company.catalog', $company->id) }}"
                            class="text-sm text-gray-400 hover:text-white">
                            {{ __('company.catalog.clear_all') }}
                        </a>
                    </div>
                @endif

                {{-- Products Grid --}}
                @include('company.partials.catalog-content', [
                    'products' => $products,
                    'company' => $company,
                ])

                {{-- Pagination --}}
                @if (isset($products) && $products->hasPages())
                    <div class="mt-8">
                        {{ $products->withQueryString()->links() }}
                    </div>
                @endif

            </div>
        </section>
    </x-slot>

    @push('scripts')
        <script>
            let viewMode = localStorage.getItem('catalogViewMode') || 'grid';

            function setViewMode(mode) {
                viewMode = mode;
                localStorage.setItem('catalogViewMode', mode);
                document.getElementById('products-grid').className = mode === 'grid' ?
                    'grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4' :
                    'flex flex-col gap-4';
                // Toggle card styles based on view mode
                document.querySelectorAll('.product-card').forEach(card => {
                    card.dataset.viewMode = mode;
                });
            }

            document.addEventListener('DOMContentLoaded', function() {
                setViewMode(viewMode);
            });
        </script>
    @endpush
</x-guest-layout>
