@vite(['resources/css/creator-home.css'])

<x-guest-layout :title="$company->name . ' - ' . __('company.about.title')" :metaDescription="__('company.about.meta_description', ['name' => $company->name])">

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
                    <p class="text-sm font-medium text-[#C9A227]">{{ __('company.about.title') }}</p>
                    <h1 class="font-playfair text-3xl font-bold text-white md:text-4xl">
                        {{ $company->name }}
                    </h1>
                </div>
            </div>
        </div>
    </x-slot>

    {{-- Navigation Tabs --}}
    <x-slot name="platformStats">
        @php $activeTab = 'about'; @endphp
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
                        class="whitespace-nowrap border-b-2 border-transparent px-6 py-4 text-sm font-medium text-gray-300 transition-colors hover:text-white">
                        {{ __('company.home.collections_tab') }}
                    </a>
                    <a href="{{ route('company.about', $company->id) }}"
                        class="whitespace-nowrap border-b-2 border-[#C9A227] px-6 py-4 text-sm font-medium text-[#C9A227] transition-colors">
                        {{ __('company.home.about_tab') }}
                    </a>
                </div>
            </div>
        </nav>
    </x-slot>

    <x-slot name="heroFullWidth">
        <section class="bg-gray-900 py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">

                    {{-- Main Content --}}
                    <div class="space-y-8 lg:col-span-2">
                        {{-- About Section --}}
                        @if ($company->bio)
                            <div class="rounded-2xl border border-gray-800 bg-gray-900/70 p-8">
                                <h2 class="font-playfair mb-6 text-2xl font-bold text-white">
                                    {{ __('company.about.our_story') }}
                                </h2>
                                <div class="prose prose-invert max-w-none">
                                    <p class="leading-relaxed text-gray-300">
                                        {{ $company->bio }}
                                    </p>
                                </div>
                            </div>
                        @endif

                        {{-- Mission Section --}}
                        @if ($company->mission ?? null)
                            <div class="rounded-2xl border border-gray-800 bg-gray-900/70 p-8">
                                <h2 class="font-playfair mb-6 text-2xl font-bold text-white">
                                    {{ __('company.about.our_mission') }}
                                </h2>
                                <p class="leading-relaxed text-gray-300">
                                    {{ $company->mission }}
                                </p>
                            </div>
                        @endif

                        {{-- Values Section --}}
                        @if ($company->values ?? null)
                            <div class="rounded-2xl border border-gray-800 bg-gray-900/70 p-8">
                                <h2 class="font-playfair mb-6 text-2xl font-bold text-white">
                                    {{ __('company.about.our_values') }}
                                </h2>
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    @foreach (explode(',', $company->values) as $value)
                                        <div class="flex items-start gap-3">
                                            <div class="mt-1 rounded-full bg-[#2D7D46]/20 p-1">
                                                <svg class="h-4 w-4 text-[#2D7D46]" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <span class="text-gray-300">{{ trim($value) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Default Content if no specific info --}}
                        @if (!($company->bio ?? null) && !($company->mission ?? null))
                            <div
                                class="rounded-2xl border border-dashed border-gray-700 bg-gray-900/60 p-8 text-center">
                                <svg class="mx-auto mb-4 h-12 w-12 text-gray-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <p class="text-gray-400">
                                    Questa azienda non ha ancora aggiunto informazioni dettagliate.
                                </p>
                            </div>
                        @endif
                    </div>

                    {{-- Sidebar --}}
                    <div class="space-y-6">
                        {{-- Company Info Card --}}
                        <div class="rounded-2xl border border-gray-800 bg-gray-900/70 p-6">
                            <h3 class="mb-6 text-lg font-semibold text-white">
                                Informazioni Aziendali
                            </h3>

                            <dl class="space-y-4">
                                {{-- Member Since --}}
                                <div class="flex items-start gap-3">
                                    <dt class="sr-only">{{ __('company.about.founded') }}</dt>
                                    <div class="rounded-lg bg-[#1E3A5F]/30 p-2">
                                        <svg class="h-5 w-5 text-[#C9A227]" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <dd class="flex flex-col">
                                        <span class="text-xs text-gray-400">{{ __('company.about.founded') }}</span>
                                        <span
                                            class="font-medium text-white">{{ $company->created_at->format('Y') }}</span>
                                    </dd>
                                </div>

                                {{-- Industry --}}
                                @if ($company->industry ?? null)
                                    <div class="flex items-start gap-3">
                                        <dt class="sr-only">{{ __('company.about.industry') }}</dt>
                                        <div class="rounded-lg bg-[#1E3A5F]/30 p-2">
                                            <svg class="h-5 w-5 text-[#C9A227]" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                        </div>
                                        <dd class="flex flex-col">
                                            <span
                                                class="text-xs text-gray-400">{{ __('company.about.industry') }}</span>
                                            <span class="font-medium text-white">{{ $company->industry }}</span>
                                        </dd>
                                    </div>
                                @endif

                                {{-- Location --}}
                                @if ($company->location ?? ($company->city ?? null))
                                    <div class="flex items-start gap-3">
                                        <dt class="sr-only">{{ __('company.about.location') }}</dt>
                                        <div class="rounded-lg bg-[#1E3A5F]/30 p-2">
                                            <svg class="h-5 w-5 text-[#C9A227]" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </div>
                                        <dd class="flex flex-col">
                                            <span
                                                class="text-xs text-gray-400">{{ __('company.about.location') }}</span>
                                            <span
                                                class="font-medium text-white">{{ $company->location ?? $company->city }}</span>
                                        </dd>
                                    </div>
                                @endif

                                {{-- Products Count --}}
                                <div class="flex items-start gap-3">
                                    <dt class="sr-only">{{ __('company.home.products') }}</dt>
                                    <div class="rounded-lg bg-[#1E3A5F]/30 p-2">
                                        <svg class="h-5 w-5 text-[#C9A227]" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                    </div>
                                    <dd class="flex flex-col">
                                        <span class="text-xs text-gray-400">{{ __('company.home.products') }}</span>
                                        <span
                                            class="font-medium text-white">{{ $stats['total_products'] ?? 0 }}</span>
                                    </dd>
                                </div>

                                {{-- Collections Count --}}
                                <div class="flex items-start gap-3">
                                    <dt class="sr-only">{{ __('company.home.collections_tab') }}</dt>
                                    <div class="rounded-lg bg-[#1E3A5F]/30 p-2">
                                        <svg class="h-5 w-5 text-[#C9A227]" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                        </svg>
                                    </div>
                                    <dd class="flex flex-col">
                                        <span
                                            class="text-xs text-gray-400">{{ __('company.home.collections_tab') }}</span>
                                        <span
                                            class="font-medium text-white">{{ $stats['collections_count'] ?? 0 }}</span>
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        {{-- Contact Card --}}
                        <div
                            class="rounded-2xl border border-[#1E3A5F]/50 bg-gradient-to-br from-[#1E3A5F]/20 to-transparent p-6">
                            <h3 class="mb-4 text-lg font-semibold text-white">
                                {{ __('company.about.contact_us') }}
                            </h3>
                            <p class="mb-6 text-sm text-gray-400">
                                Vuoi saperne di più sui nostri prodotti? Contattaci per informazioni.
                            </p>
                            @if (\App\Helpers\FegiAuth::check())
                                <a href="{{ route('chat.thread', $company->id) }}"
                                    class="block w-full rounded-lg bg-[#C9A227] px-4 py-3 text-center font-semibold text-gray-900 transition-colors hover:bg-[#D4B445]">
                                    {{ __('company.home.contact_button') }}
                                </a>
                            @else
                                <a href="{{ route('login') }}"
                                    class="block w-full rounded-lg bg-[#C9A227] px-4 py-3 text-center font-semibold text-gray-900 transition-colors hover:bg-[#D4B445]">
                                    Accedi per Contattare
                                </a>
                            @endif
                        </div>

                        {{-- Verified Badge --}}
                        @if ($company->is_verified ?? false)
                            <div
                                class="flex items-center gap-3 rounded-2xl border border-[#2D7D46]/30 bg-[#2D7D46]/10 p-4">
                                <div class="rounded-full bg-[#2D7D46]/20 p-2">
                                    <svg class="h-6 w-6 text-[#2D7D46]" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-white">{{ __('company.home.verified_business') }}</p>
                                    <p class="text-sm text-gray-400">Identità aziendale verificata</p>
                                </div>
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </section>
    </x-slot>
</x-guest-layout>
