@vite(['resources/css/creator-home.css'])

<x-guest-layout :title="$company->name . ' - ' . __('company.home.title_suffix')" :metaDescription="__('company.home.meta_description', ['name' => $company->name])">

    @push('head')
        <script type="application/ld+json">
            {
                "@context": "https://schema.org",
                "@type": "Organization",
                "@id": "{{ url('/company/' . $company->id) }}",
                "name": "{{ $company->name }}",
                "url": "{{ url('/company/' . $company->id) }}",
                "description": "{{ $company->bio ?? __('company.home.default_bio') }}",
                "logo": "{{ $company->profile_photo_url }}"
            }
        </script>
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

            .bg-company-success {
                background-color: var(--company-success);
            }

            .text-company-primary {
                color: var(--company-primary);
            }

            .text-company-accent {
                color: var(--company-accent);
            }

            .text-company-success {
                color: var(--company-success);
            }

            .border-company-primary {
                border-color: var(--company-primary);
            }

            .border-company-accent {
                border-color: var(--company-accent);
            }

            .ring-company-accent {
                --tw-ring-color: var(--company-accent);
            }

            .hover\:bg-company-primary-light:hover {
                background-color: var(--company-primary-light);
            }

            .hover\:bg-company-accent-light:hover {
                background-color: var(--company-accent-light);
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
                    {{-- Corporate gradient background --}}
                    <div class="absolute inset-0 bg-gradient-to-br from-[#1E3A5F] via-[#2A4A73] to-[#0F1F33]"></div>
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/70 to-transparent"></div>
                <div class="absolute inset-0 bg-gradient-to-r from-gray-900/40 to-transparent"></div>
            </div>
        </div>

        <div class="relative z-10 mx-auto max-w-7xl px-4 py-16 sm:px-6 md:py-24 lg:px-8">
            <div class="grid grid-cols-1 items-end gap-12 md:grid-cols-12 md:gap-8">
                <div class="flex flex-col items-center gap-6 sm:flex-row sm:items-end sm:gap-8 md:col-span-8">
                    {{-- Company Logo --}}
                    <div class="group relative flex-shrink-0">
                        <div
                            class="h-32 w-32 overflow-hidden rounded-2xl bg-white shadow-2xl ring-4 ring-[#C9A227]/40 md:h-40 md:w-40">
                            <img src="{{ $company->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($company->name) . '&size=160&background=1E3A5F&color=C9A227&bold=true' }}"
                                alt="{{ __('company.home.logo_alt', ['name' => $company->name]) }}"
                                class="h-full w-full object-contain p-2 transition-transform duration-300 group-hover:scale-105"
                                loading="lazy">
                        </div>
                        {{-- Verified Business Badge --}}
                        <div class="absolute -bottom-2 -right-2 rounded-full bg-[#2D7D46] p-2 shadow-lg"
                            title="{{ __('company.home.verified_business_title') }}">
                            <svg class="h-6 w-6 text-white" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="sr-only">{{ __('company.home.verified_sr') }}</span>
                        </div>
                    </div>

                    <div class="flex flex-col text-center sm:text-left">
                        <div class="mb-2 flex items-center justify-center gap-2 sm:justify-start">
                            <span
                                class="rounded-full bg-[#1E3A5F] px-3 py-1 text-xs font-semibold uppercase tracking-wider text-[#C9A227]">
                                {{ __('company.home.business_badge') }}
                            </span>
                            @if ($company->is_verified ?? false)
                                <span
                                    class="rounded-full bg-[#2D7D46]/20 px-3 py-1 text-xs font-semibold text-[#2D7D46]">
                                    {{ __('company.home.verified_badge') }}
                                </span>
                            @endif
                        </div>
                        <h1 class="font-playfair mb-1 text-3xl font-bold text-white md:text-5xl">
                            {{ $company->name }}
                        </h1>
                        @if ($company->tagline)
                            <p class="font-source-sans text-lg italic text-[#C9A227] md:text-xl">
                                "{{ $company->tagline }}"
                            </p>
                        @endif
                        <p class="mt-3 text-gray-300">
                            @if ($company->industry)
                                <span class="font-medium">{{ $company->industry }}</span> &middot;
                            @endif
                            {{ __('company.home.member_since', ['year' => $company->created_at->format('Y')]) }}
                        </p>
                    </div>
                </div>

                {{-- Stats & Actions --}}
                <div class="flex flex-col items-center gap-6 md:col-span-4 md:items-end">
                    <div class="flex gap-3">
                        @if (\App\Helpers\FegiAuth::check() && \App\Helpers\FegiAuth::id() !== $company->id)
                            <button type="button"
                                class="rounded-full bg-[#C9A227] px-6 py-2.5 font-semibold text-gray-900 shadow-lg transition-all duration-300 hover:bg-[#D4B445] hover:shadow-xl"
                                aria-label="{{ __('company.home.follow_aria', ['name' => $company->name]) }}">
                                <span class="flex items-center gap-2">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    {{ __('company.home.follow_button') }}
                                </span>
                            </button>
                            <button type="button"
                                class="rounded-full bg-[#1E3A5F] px-6 py-2.5 font-semibold text-white shadow-lg transition-all duration-300 hover:bg-[#2A4A73] hover:shadow-xl"
                                aria-label="{{ __('company.home.contact_aria', ['name' => $company->name]) }}">
                                {{ __('company.home.contact_button') }}
                            </button>
                        @elseif (\App\Helpers\FegiAuth::guest())
                            <button type="button" onclick="window.location.href='{{ route('login') }}'"
                                class="rounded-full bg-[#C9A227] px-6 py-2.5 font-semibold text-gray-900 shadow-lg transition-all duration-300 hover:bg-[#D4B445] hover:shadow-xl">
                                {{ __('company.home.login_to_follow') }}
                            </button>
                        @endif
                    </div>

                    {{-- Business Stats --}}
                    <div class="grid grid-cols-3 gap-6 text-center">
                        <div class="flex flex-col">
                            <span class="text-2xl font-bold text-[#C9A227] md:text-3xl">
                                {{ $stats['total_products'] ?? 0 }}
                            </span>
                            <span class="text-xs text-gray-300 md:text-sm">{{ __('company.home.products') }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-2xl font-bold text-[#C9A227] md:text-3xl">
                                {{ $stats['total_sales'] ?? 0 }}
                            </span>
                            <span class="text-xs text-gray-300 md:text-sm">{{ __('company.home.sales') }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-2xl font-bold text-[#C9A227] md:text-3xl">
                                {{ $stats['followers_count'] ?? 0 }}
                            </span>
                            <span class="text-xs text-gray-300 md:text-sm">{{ __('company.home.followers') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    {{-- Navigation Tabs --}}
    <x-slot name="platformStats">
        @php
            $activeTab = $activeTab ?? 'overview';
        @endphp
        <nav class="flex overflow-x-auto border-b border-gray-800 bg-gray-900/95"
            aria-label="{{ __('company.home.navigation_aria') }}">
            <div class="mx-auto flex w-full max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="scrollbar-hide flex w-full space-x-6">
                    <a href="{{ route('company.home', $company->id) }}"
                        class="{{ $activeTab === 'overview' ? 'border-[#C9A227] text-[#C9A227]' : 'border-transparent text-gray-300 hover:text-white' }} whitespace-nowrap border-b-2 px-6 py-4 text-sm font-medium transition-colors">
                        {{ __('company.home.overview_tab') }}
                    </a>
                    <a href="{{ route('company.catalog', $company->id) }}"
                        class="{{ $activeTab === 'catalog' ? 'border-[#C9A227] text-[#C9A227]' : 'border-transparent text-gray-300 hover:text-white' }} whitespace-nowrap border-b-2 px-6 py-4 text-sm font-medium transition-colors">
                        {{ __('company.home.catalog_tab') }}
                    </a>
                    <a href="{{ route('company.collections', $company->id) }}"
                        class="{{ $activeTab === 'collections' ? 'border-[#C9A227] text-[#C9A227]' : 'border-transparent text-gray-300 hover:text-white' }} whitespace-nowrap border-b-2 px-6 py-4 text-sm font-medium transition-colors">
                        {{ __('company.home.collections_tab') }}
                    </a>
                    <a href="{{ route('company.about', $company->id) }}"
                        class="{{ $activeTab === 'about' ? 'border-[#C9A227] text-[#C9A227]' : 'border-transparent text-gray-300 hover:text-white' }} whitespace-nowrap border-b-2 px-6 py-4 text-sm font-medium transition-colors">
                        {{ __('company.home.about_tab') }}
                    </a>
                </div>
            </div>
        </nav>
    </x-slot>

    <x-slot name="heroFullWidth">
        <section class="bg-gray-900">
            <div class="mx-auto max-w-7xl space-y-12 px-4 py-12 sm:px-6 lg:px-8">

                {{-- Company Bio/Description --}}
                @if ($company->bio)
                    <div
                        class="rounded-2xl border border-[#1E3A5F]/30 bg-gradient-to-r from-[#1E3A5F]/10 to-transparent p-8">
                        <h2 class="font-playfair mb-4 text-2xl font-bold text-white">
                            {{ __('company.home.about_us') }}
                        </h2>
                        <p class="leading-relaxed text-gray-300">
                            {{ $company->bio }}
                        </p>
                    </div>
                @endif

                {{-- Featured Products --}}
                @if (isset($featuredProducts) && $featuredProducts->count() > 0)
                    <div>
                        <div class="mb-8 flex items-center justify-between">
                            <h2 class="font-playfair text-3xl font-bold text-white">
                                {{ __('company.home.featured_products_title') }}
                            </h2>
                            <a href="{{ route('company.catalog', $company->id) }}"
                                class="flex items-center font-medium text-[#C9A227] transition-colors hover:text-[#D4B445]">
                                {{ __('company.home.view_all_products') }}
                                <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </a>
                        </div>

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                            @foreach ($featuredProducts->take(8) as $product)
                                @include('company.partials.product-card', [
                                    'product' => $product,
                                    'company' => $company,
                                ])
                            @endforeach
                        </div>
                    </div>
                @else
                    {{-- Empty State --}}
                    <div class="rounded-2xl border border-dashed border-gray-700 bg-gray-900/60 px-6 py-12 text-center">
                        <svg class="mx-auto mb-6 h-16 w-16 text-gray-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <h3 class="mb-2 text-2xl font-bold text-white">
                            {{ __('company.home.empty_catalog_title') }}
                        </h3>
                        <p class="mb-6 text-gray-400">
                            {{ __('company.home.empty_catalog_description') }}
                        </p>
                    </div>
                @endif

                {{-- Collections Preview --}}
                @if (isset($companyCollections) && $companyCollections->count() > 0)
                    <div>
                        <div class="mb-8 flex items-center justify-between">
                            <h2 class="font-playfair text-3xl font-bold text-white">
                                {{ __('company.home.collections_preview_title') }}
                            </h2>
                            <a href="{{ route('company.collections', $company->id) }}"
                                class="flex items-center font-medium text-[#C9A227] transition-colors hover:text-[#D4B445]">
                                {{ __('company.home.view_all_collections') }}
                                <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </a>
                        </div>

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ($companyCollections->take(6) as $collection)
                                <article
                                    class="group overflow-hidden rounded-xl border border-gray-800 bg-gray-900/70 transition-all duration-300 hover:border-[#C9A227]/60 hover:shadow-2xl hover:shadow-[#C9A227]/10">
                                    {{-- Collection Cover --}}
                                    <div
                                        class="relative aspect-video overflow-hidden bg-gradient-to-br from-[#1E3A5F] to-[#0F1F33]">
                                        @if ($collection->cover_image)
                                            <img src="{{ $collection->cover_image }}"
                                                alt="{{ $collection->collection_name }}"
                                                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110">
                                        @else
                                            <div class="flex h-full items-center justify-center">
                                                <svg class="h-16 w-16 text-[#C9A227]/30" fill="none"
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
                                        <div class="flex items-center justify-between text-sm text-gray-300">
                                            <span class="flex items-center gap-1">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                </svg>
                                                {{ $collection->egis_count ?? 0 }}
                                                {{ __('company.home.products_in_collection') }}
                                            </span>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Trust Indicators --}}
                <div class="rounded-2xl border border-gray-800 bg-gray-900/50 p-8">
                    <h3 class="mb-6 text-center text-lg font-semibold text-gray-400">
                        {{ __('company.home.trust_indicators_title') }}
                    </h3>
                    <div class="grid grid-cols-2 gap-6 md:grid-cols-4">
                        <div class="flex flex-col items-center text-center">
                            <div class="mb-3 rounded-full bg-[#2D7D46]/20 p-3">
                                <svg class="h-8 w-8 text-[#2D7D46]" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <span
                                class="text-sm font-medium text-white">{{ __('company.home.verified_business') }}</span>
                        </div>
                        <div class="flex flex-col items-center text-center">
                            <div class="mb-3 rounded-full bg-[#1E3A5F]/20 p-3">
                                <svg class="h-8 w-8 text-[#C9A227]" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <span
                                class="text-sm font-medium text-white">{{ __('company.home.secure_payments') }}</span>
                        </div>
                        <div class="flex flex-col items-center text-center">
                            <div class="mb-3 rounded-full bg-[#1E3A5F]/20 p-3">
                                <svg class="h-8 w-8 text-[#C9A227]" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <span
                                class="text-sm font-medium text-white">{{ __('company.home.certified_products') }}</span>
                        </div>
                        <div class="flex flex-col items-center text-center">
                            <div class="mb-3 rounded-full bg-[#1E3A5F]/20 p-3">
                                <svg class="h-8 w-8 text-[#C9A227]" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <span
                                class="text-sm font-medium text-white">{{ __('company.home.blockchain_tracked') }}</span>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </x-slot>
</x-guest-layout>
