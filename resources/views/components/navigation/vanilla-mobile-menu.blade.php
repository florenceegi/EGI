{{-- Vanilla Mobile Navigation Component }}

{{--  --}}
    
@push('styles')
    @vite('resources/css/mega-menu.css')
@endpush

@push('scripts')
    @vite('resources/js/components/vanilla-mobile-menu.js')
@endpush

<!-- Mobile Menu Container -->
<style>
/* CSS INLINE DIRETTO - SUPER AGGRESSIVO PER CONTENUTO */
[data-mobile-menu] {
    z-index: 999999 !important;
}
.mobile-header-gradient {
    background: #ff0000 !important;
    color: yellow !important;
    font-size: 24px !important;
}
[data-mobile-content] {
    background-color: rgba(17, 24, 39, 0.9) !important;
    backdrop-filter: blur(24px) !important;
    opacity: 1 !important;
    visibility: visible !important;
}
.mobile-menu-content {
    background-color: transparent !important;
    backdrop-filter: blur(24px) !important;
    opacity: 1 !important;
}
.mobile-menu-content h4 {
    opacity: 1 !important;
    color: #e5e7eb !important;
}
.mobile-menu-content a {
    opacity: 1 !important;
    color: #e5e7eb !important;
}
.mobile-menu-content button {
    opacity: 1 !important;
    color: #e5e7eb !important;
}
.mobile-menu-content div {
    opacity: 1 !important;
}
.mobile-card {
    opacity: 1 !important;
}
.mobile-nav-item {
    color: #e5e7eb !important;
}
.space-y-2, .space-y-4, .space-y-1 {
    opacity: 1 !important;
}
</style>

@php
    $user = App\Helpers\FegiAuth::user(); // User object or null
    $authType = App\Helpers\FegiAuth::getAuthType(); // 'strong', 'weak', 'guest'
    $canCreateEgi = $user && $user->can('create_EGI');
@endphp

<div data-mobile-menu class="fixed inset-0 hidden sm:hidden" style="z-index: 999999 !important;">
    <!-- Mobile Menu Overlay -->
    <div data-mobile-overlay class="fixed inset-0 bg-black/60 backdrop-blur-md mobile-menu-overlay" style="opacity: 1 !important; visibility: visible !important; z-index: 999998 !important;"></div>

    <!-- Mobile Menu Content -->
    <div class="fixed inset-0 flex" style="z-index: 999999 !important;">
        <div data-mobile-content class="relative flex flex-col w-full max-w-sm ml-auto transition-transform duration-300 ease-out transform translate-x-full bg-gray-900/90 backdrop-blur-xl shadow-2xl border-l border-gray-700/50 mobile-menu-container" style="opacity: 1 !important; visibility: visible !important; z-index: 999999 !important;">

            <!-- Header Section with User Info -->
            <div class="flex items-center justify-between p-6 bg-gradient-to-r from-blue-500 to-purple-600 mobile-header-gradient" style="opacity: 1 !important; background: linear-gradient(to right, #3b82f6, #9333ea) !important; color: white !important;">
                <div class="flex items-center space-x-3">
                    @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                        @if(Auth::check() && Auth::user()->id)
                            <a href="{{ route('creator.home', Auth::user()->id) }}" class="block transition-transform duration-300 hover:scale-105">
                                <img class="object-cover transition-all duration-300 rounded-full size-12 ring-2 ring-white/30 hover:ring-white/60"
                                    src="{{ Auth::user()?->profile_photo_url ?? null }}"
                                    alt="{{ Auth::user()?->name ?? '' }}" />
                            </a>
                        @else
                            <img class="object-cover rounded-full size-12 ring-2 ring-white/30"
                                src="{{ Auth::user()?->profile_photo_url ?? null }}"
                                alt="{{ Auth::user()?->name ?? '' }}" />
                        @endif
                    @else
                        @if(Auth::check() && Auth::user()->id)
                            <a href="{{ route('creator.home', Auth::user()->id) }}" class="block transition-transform duration-300 hover:scale-105">
                                <div class="flex items-center justify-center w-12 h-12 text-lg font-bold text-white transition-all duration-300 rounded-full bg-white/20 hover:bg-white/30">
                                    {{ substr(Auth::user()?->name ?? 'U', 0, 1) }}
                                </div>
                            </a>
                        @else
                            <div class="flex items-center justify-center w-12 h-12 text-lg font-bold text-white rounded-full bg-white/20">
                                {{ substr(Auth::user()?->name ?? 'U', 0, 1) }}
                            </div>
                        @endif
                    @endif
                    <div>
                        <h3 class="font-semibold text-white">{{ Auth::user()?->name ?? '' }}</h3>
                        <p class="text-sm text-white/80">{{ Auth::user()?->email ?? '' }}</p>
                    </div>
                </div>
                <button data-mobile-close class="p-2 transition-colors rounded-lg text-white/80 hover:text-white hover:bg-white/10">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Collection Badge Section -->
            @auth
            <div class="px-4 py-2 border-b border-gray-200 dark:border-gray-700">
                <x-collection-badge size="mobile" :show-when-empty="true" position="mobile-menu" />
            </div>
            @endauth

            <!-- Navigation Content -->
            <div class="flex-1 p-4 space-y-4 overflow-y-auto mobile-menu-content" style="opacity: 1 !important;">

                <!-- Main Navigation -->
                <div class="space-y-2" style="opacity: 1 !important;">
                    <h4 class="px-3 text-xs font-semibold tracking-wider text-gray-500 uppercase dark:text-gray-400">{{ __('menu.navigation') }}</h4>
                    <div class="space-y-1" style="opacity: 1 !important;">

                        {{-- Se siamo nel guest layout (Home), mostra i link di nav-links.blade.php --}}
                        @if(View::getSection('title') === __('guest_home.page_title') || request()->routeIs('home') || request()->is('/'))
                            {{-- Home Link --}}
                            <a href="{{ url('/') }}" class="flex items-center space-x-3 px-4 py-3 text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-xl transition-colors mobile-nav-item {{ (request()->routeIs('home') || request()->is('/')) ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : '' }}">
                                <div class="flex items-center justify-center w-8 h-8 text-white bg-blue-500 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    </svg>
                                </div>
                                <span class="font-medium">{{ __('guest_layout.home') }}</span>
                            </a>

                            {{-- Creators Link --}}
                            <a href="{{ url('/creator') }}" class="flex items-center space-x-3 px-4 py-3 text-gray-700 dark:text-gray-200 hover:bg-purple-50 dark:hover:bg-purple-900/20 rounded-xl transition-colors mobile-nav-item {{ request()->routeIs('creator.index') ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400' : '' }}">
                                <div class="flex items-center justify-center w-8 h-8 text-white bg-purple-500 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <span class="font-medium">{{ __('guest_layout.creators') }}</span>
                            </a>

                            {{-- Collections Link --}}
                            <a href="{{ route('home.collections.index') }}" class="flex items-center space-x-3 px-4 py-3 text-gray-700 dark:text-gray-200 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 rounded-xl transition-colors mobile-nav-item {{ request()->routeIs('home.collections.*') ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400' : '' }}">
                                <div class="flex items-center justify-center w-8 h-8 text-white rounded-lg bg-emerald-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                    </svg>
                                </div>
                                <span class="font-medium">{{ __('guest_layout.collections') }}</span>
                            </a>

                            {{-- Collectors Link --}}
                            <a href="{{ route('collector.index') }}" class="flex items-center space-x-3 px-4 py-3 text-gray-700 dark:text-gray-200 hover:bg-cyan-50 dark:hover:bg-cyan-900/20 rounded-xl transition-colors mobile-nav-item {{ request()->routeIs('collector.*') ? 'bg-cyan-50 dark:bg-cyan-900/20 text-cyan-600 dark:text-cyan-400' : '' }}">
                                <div class="flex items-center justify-center w-8 h-8 text-white rounded-lg bg-cyan-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <span class="font-medium">{{ __('guest_layout.collectors') }}</span>
                            </a>

                            {{-- EPPs Link --}}
                            <a href="{{ route('epps.index') }}" class="flex items-center space-x-3 px-4 py-3 text-gray-700 dark:text-gray-200 hover:bg-orange-50 dark:hover:bg-orange-900/20 rounded-xl transition-colors mobile-nav-item {{ request()->routeIs('epps.*') ? 'bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400' : '' }}">
                                <div class="flex items-center justify-center w-8 h-8 text-white bg-orange-500 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                </div>
                                <span class="font-medium">{{ __('guest_layout.epps') }}</span>
                            </a>

                            {{-- Le mie Collezioni Dropdown - Solo per utenti loggati --}}
                            @can('create_EGI')
                                @auth
                                <button type="button" id="mobile-collection-list-dropdown-button"
                                    class="flex items-center justify-between w-full px-4 py-3 text-gray-700 transition-colors dark:text-gray-200 hover:bg-purple-50 dark:hover:bg-purple-900/20 rounded-xl mobile-nav-item"
                                    aria-expanded="false" aria-haspopup="true">
                                    <span class="flex items-center space-x-3">
                                        <div class="flex items-center justify-center w-8 h-8 text-white bg-purple-500 rounded-lg">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                            </svg>
                                        </div>
                                        <span class="font-medium">{{ __('collection.my_galleries') }}</span>
                                    </span>
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                        <path fill-rule="evenodd"
                                            d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                                {{-- Dropdown menu mobile --}}
                                <div id="mobile-collection-list-dropdown-menu"
                                    class="mx-4 mb-2 mt-1 hidden max-h-[40vh] overflow-y-auto rounded-xl border border-gray-600 bg-gray-800/90 py-2 shadow-lg backdrop-blur-lg"
                                    style="opacity: 1 !important;">
                                    <div id="mobile-collection-list-loading" class="px-4 py-3 text-sm text-center text-gray-500">
                                        {{ __('collection.loading_galleries') }}</div>
                                    <div id="mobile-collection-list-empty" class="hidden px-4 py-3 text-sm text-center text-gray-500">
                                        {{ __('collection.no_galleries_found') }} <button type="button" data-action="open-create-collection-modal"
                                            class="underline hover:text-emerald-400">{{ __('collection.create_one_question') }}</button></div>
                                    <div id="mobile-collection-list-error" class="hidden px-4 py-3 text-sm text-center text-red-500">
                                        {{ __('collection.error_loading_galleries') }}</div>
                                </div>
                                @endauth
                            @endcan

                            {{-- Create EGI Button - Sempre visibile, la logica di azione è gestita da JS in base allo stato utente --}}
                            @can('create_EGI')
                                <button type="button"
                                    class="flex items-center w-full px-4 py-3 space-x-3 text-gray-700 transition-colors js-create-egi-contextual-button dark:text-gray-200 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-xl mobile-nav-item"
                                    data-action="open-create-egi-contextual" data-auth-type="{{ $authType }}"
                                    aria-label="{{ __('guest_layout.create_egi') }}">
                                    <div class="flex items-center justify-center w-8 h-8 text-white bg-green-500 rounded-lg">
                                        <svg class="w-4 h-4 js-create-egi-button-icon" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
                                            <path d="M8.75 3.75a.75.75 0 0 0-1.5 0v3.5h-3.5a.75.75 0 0 0 0 1.5h3.5v3.5a.75.75 0 0 0 1.5 0v-3.5h3.5a.75.75 0 0 0 0-1.5h-3.5v-3.5Z" />
                                        </svg>
                                    </div>
                                    <span class="font-medium js-create-egi-button-text">{{ __('guest_layout.create_egi') }}</span>
                                </button>
                            @endcan

                            {{-- Create Collection CTA - Solo se l'utente ha il permesso --}}
                            @can('create_collection')
                                <button type="button" data-action="open-create-collection-modal"
                                    class="flex items-center w-full px-4 py-3 space-x-3 text-gray-700 transition-colors dark:text-gray-200 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-xl mobile-nav-item"
                                    aria-label="{{ __('collection.create_collection') }}">
                                    <div class="flex items-center justify-center w-8 h-8 text-white bg-indigo-500 rounded-lg">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
                                            <path d="M8.75 3.75a.75.75 0 0 0-1.5 0v3.5h-3.5a.75.75 0 0 0 0 1.5h3.5v3.5a.75.75 0 0 0 1.5 0v-3.5h3.5a.75.75 0 0 0 0-1.5h-3.5v-3.5Z" />
                                        </svg>
                                    </div>
                                    <span class="font-medium">{{ __('collection.create_collection') }}</span>
                                </button>
                            @endcan

                        @else
                            {{-- Se siamo nell'app layout (Dashboard), mostra i link standard --}}
                            <a href="{{ route('home') }}" class="flex items-center space-x-3 px-4 py-3 text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-xl transition-colors mobile-nav-item {{ request()->routeIs('home') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : '' }}">
                                <div class="flex items-center justify-center w-8 h-8 text-white bg-blue-500 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    </svg>
                                </div>
                                <span class="font-medium">{{ __('Home') }}</span>
                            </a>

                            <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-4 py-3 text-gray-700 dark:text-gray-200 hover:bg-purple-50 dark:hover:bg-purple-900/20 rounded-xl transition-colors mobile-nav-item {{ request()->routeIs('dashboard') ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400' : '' }}">
                                <div class="flex items-center justify-center w-8 h-8 text-white bg-purple-500 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                </div>
                                <span class="font-medium">{{ __('Dashboard') }}</span>
                            </a>

                            <a href="{{ route('home.collections.index') }}" class="flex items-center space-x-3 px-4 py-3 text-gray-700 dark:text-gray-200 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 rounded-xl transition-colors mobile-nav-item {{ request()->routeIs('home.collections.*') ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400' : '' }}">
                                <div class="flex items-center justify-center w-8 h-8 text-white rounded-lg bg-emerald-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                    </svg>
                                </div>
                                <span class="font-medium">{{ __('Collections') }}</span>
                            </a>

                            <a href="{{ route('epps.index') }}" class="flex items-center space-x-3 px-4 py-3 text-gray-700 dark:text-gray-200 hover:bg-orange-50 dark:hover:bg-orange-900/20 rounded-xl transition-colors mobile-nav-item {{ request()->routeIs('epps.*') ? 'bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400' : '' }}">
                                <div class="flex items-center justify-center w-8 h-8 text-white bg-orange-500 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                </div>
                                <span class="font-medium">{{ __('EPPS') }}</span>
                            </a>

                            {{-- Le mie Collezioni Dropdown - Solo per utenti loggati --}}
                            @can('create_EGI')
                                @auth
                                <button type="button" id="mobile-collection-list-dropdown-button-app"
                                    class="flex items-center justify-between w-full px-4 py-3 text-gray-700 transition-colors dark:text-gray-200 hover:bg-purple-50 dark:hover:bg-purple-900/20 rounded-xl mobile-nav-item"
                                    aria-expanded="false" aria-haspopup="true">
                                    <span class="flex items-center space-x-3">
                                        <div class="flex items-center justify-center w-8 h-8 text-white bg-purple-500 rounded-lg">
                                            <span class="text-sm material-symbols-outlined" aria-hidden="true">view_carousel</span>
                                        </div>
                                        <span class="font-medium">{{ __('collection.my_galleries') }}</span>
                                    </span>
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                        <path fill-rule="evenodd"
                                            d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                                {{-- Dropdown menu mobile app --}}
                                <div id="mobile-collection-list-dropdown-menu-app"
                                    class="mx-3 mb-2 mt-1 hidden max-h-[40vh] overflow-y-auto rounded-md border border-gray-700 bg-gray-800 py-1 shadow-lg">
                                    <div id="mobile-collection-list-loading-app" class="px-4 py-3 text-sm text-center text-gray-400">
                                        {{ __('collection.loading_galleries') }}</div>
                                    <div id="mobile-collection-list-empty-app" class="hidden px-4 py-3 text-sm text-center text-gray-400">
                                        {{ __('collection.no_galleries_found') }} <button type="button" data-action="open-create-collection-modal"
                                            class="underline hover:text-emerald-400">{{ __('collection.create_one_question') }}</button></div>
                                    <div id="mobile-collection-list-error-app" class="hidden px-4 py-3 text-sm text-center text-red-400">
                                        {{ __('collection.error_loading_galleries') }}</div>
                                </div>
                                @endauth
                            @endcan

                            {{-- Create EGI Button - Sempre visibile, la logica di azione è gestita da JS in base allo stato utente --}}
                            @can('create_EGI')
                                <button type="button"
                                    class="flex items-center w-full px-4 py-3 space-x-3 text-gray-700 transition-colors js-create-egi-contextual-button dark:text-gray-200 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-xl mobile-nav-item"
                                    data-action="open-create-egi-contextual" data-auth-type="{{ Auth::check() ? 'authenticated' : 'guest' }}"
                                    aria-label="{{ __('guest_layout.create_egi') }}">
                                    <div class="flex items-center justify-center w-8 h-8 text-white bg-green-500 rounded-lg">
                                        <svg class="w-4 h-4 js-create-egi-button-icon" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
                                            <path d="M8.75 3.75a.75.75 0 0 0-1.5 0v3.5h-3.5a.75.75 0 0 0 0 1.5h3.5v3.5a.75.75 0 0 0 1.5 0v-3.5h3.5a.75.75 0 0 0 0-1.5h-3.5v-3.5Z" />
                                        </svg>
                                    </div>
                                    <span class="font-medium js-create-egi-button-text">{{ __('guest_layout.create_egi') }}</span>
                                </button>
                            @endcan

                            {{-- Create Collection CTA - Solo se l'utente ha il permesso --}}
                            @can('create_collection')
                                <button type="button" data-action="open-create-collection-modal"
                                    class="flex items-center w-full px-4 py-3 space-x-3 text-gray-700 transition-colors dark:text-gray-200 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-xl mobile-nav-item"
                                    aria-label="{{ __('collection.create_collection') }}">
                                    <div class="flex items-center justify-center w-8 h-8 text-white bg-indigo-500 rounded-lg">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
                                            <path d="M8.75 3.75a.75.75 0 0 0-1.5 0v3.5h-3.5a.75.75 0 0 0 0 1.5h3.5v3.5a.75.75 0 0 0 1.5 0v-3.5h3.5a.75.75 0 0 0 0-1.5h-3.5v-3.5Z" />
                                        </svg>
                                    </div>
                                    <span class="font-medium">{{ __('collection.create_collection') }}</span>
                                </button>
                            @endcan
                        @endif
                    </div>
                </div>

                <!-- Dynamic Collections Carousel Card -->
                <x-menu-collections-carousel :collections="Auth::check() ? Auth::user()->ownedCollections()->orderBy('position')->get() : collect()" />

                <!-- Shared Collections Carousel Card - Collections where user is collaborator -->
                <x-menu-guest-collections-carousel :collections="Auth::check() ? Auth::user()->collaborations()->orderBy('position')->get() : collect()" />

                <!-- Account Management Card -->
                <div class="p-4 border bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 rounded-2xl border-emerald-200/30 dark:border-emerald-800/30 mobile-card">
                    <div class="flex items-center mb-3 space-x-2">
                        <div class="flex items-center justify-center w-6 h-6 rounded-lg bg-emerald-500">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ __('menu.manage_account') }}</h4>
                    </div>
                    <div class="space-y-2">
                        <a href="{{ route('user.domains.personal-data') }}" class="block px-2 py-1 text-sm text-gray-600 transition-colors duration-200 rounded-lg dark:text-gray-300 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-white/50 dark:hover:bg-black/20">
                            {{ __('menu.edit_personal_data') }}
                        </a>
                        @can('manage_profile')
                            <a href="{{ route('profile.show') }}" class="block px-2 py-1 text-sm text-gray-600 transition-colors duration-200 rounded-lg dark:text-gray-300 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-white/50 dark:hover:bg-black/20">
                                {{ __('Profile') }}
                            </a>
                        @endcan
                    </div>
                </div>

                <!-- Privacy & GDPR Card -->
                <div class="p-4 border bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-2xl border-blue-200/30 dark:border-blue-800/30 mobile-card">
                    <div class="flex items-center mb-3 space-x-2">
                        <div class="flex items-center justify-center w-6 h-6 bg-blue-500 rounded-lg">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ __('menu.gdpr_privacy') }}</h4>
                    </div>
                    <div class="space-y-2">
                        @can('manage_consents')
                            <a href="{{ route('gdpr.consent') }}" class="block px-2 py-1 text-sm text-gray-600 transition-colors duration-200 rounded-lg dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-white/50 dark:hover:bg-black/20">
                                {{ __('gdpr.menu.gdpr_center') }}
                            </a>
                        @endcan
                        <a href="{{ route('gdpr.security') }}" class="block px-2 py-1 text-sm text-gray-600 transition-colors duration-200 rounded-lg dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-white/50 dark:hover:bg-black/20">
                            {{ __('menu.security_password') }}
                        </a>
                        <a href="{{ route('gdpr.profile-images') }}" class="block px-2 py-1 text-sm text-gray-600 transition-colors duration-200 rounded-lg dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-white/50 dark:hover:bg-black/20">
                            {{ __('menu.profile_images') }}
                        </a>
                        @can('gdpr.export_data')
                            <a href="{{ route('gdpr.export-data') }}" class="block px-2 py-1 text-sm text-gray-600 transition-colors duration-200 rounded-lg dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-white/50 dark:hover:bg-black/20">
                                {{ __('menu.export_data') }}
                            </a>
                        @endcan
                    </div>
                </div>

                <!-- Activity & Notifications Card -->
                <div class="p-4 border bg-gradient-to-br from-orange-50 to-red-50 dark:from-orange-900/20 dark:to-red-900/20 rounded-2xl border-orange-200/30 dark:border-orange-800/30 mobile-card">
                    <div class="flex items-center mb-3 space-x-2">
                        <div class="flex items-center justify-center w-6 h-6 bg-orange-500 rounded-lg">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM9 7H4l5-5v5z"/>
                            </svg>
                        </div>
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ __('menu.activity') }}</h4>
                    </div>
                    <div class="space-y-2">
                        @can('view_activity_log')
                            <a href="{{ route('gdpr.activity-log') }}" class="block px-2 py-1 text-sm text-gray-600 transition-colors duration-200 rounded-lg dark:text-gray-300 hover:text-orange-600 dark:hover:text-orange-400 hover:bg-white/50 dark:hover:bg-black/20">
                                {{ __('menu.activity_log') }}
                            </a>
                        @endcan
                    </div>
                </div>

                <!-- Admin Tools Card -->
                @can('manage_roles')
                    <div class="p-4 border bg-gradient-to-br from-gray-50 to-slate-50 dark:from-gray-900/20 dark:to-slate-900/20 rounded-2xl border-gray-200/30 dark:border-gray-800/30 mobile-card">
                        <div class="flex items-center mb-3 space-x-2">
                            <div class="flex items-center justify-center w-6 h-6 bg-gray-600 rounded-lg">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ __('menu.admin_tools') }}</h4>
                        </div>
                        <div class="space-y-2">
                            <a href="{{ route('admin.roles.index') }}" class="block px-2 py-1 text-sm text-gray-600 transition-colors duration-200 rounded-lg dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-white/50 dark:hover:bg-black/20">
                                {{ __('menu.permissions_roles') }}
                            </a>
                            <a href="{{ route('admin.users.index') }}" class="block px-2 py-1 text-sm text-gray-600 transition-colors duration-200 rounded-lg dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-white/50 dark:hover:bg-black/20">
                                {{ __('menu.user_management') }}
                            </a>
                            @can('view_statistics')
                                <a href="{{ route('statistics.index') }}" class="block px-2 py-1 text-sm text-gray-600 transition-colors duration-200 rounded-lg dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-white/50 dark:hover:bg-black/20">
                                    {{ __('menu.statistics') }}
                                </a>
                            @endcan
                        </div>
                    </div>
                @endcan

            </div>

            <!-- Footer Section -->
            <div class="p-4 space-y-3 border-t border-gray-200 dark:border-gray-700">
                <!-- Support & Legal -->
                <div class="flex justify-center space-x-4">
                    <a href="{{ route('gdpr.privacy-policy') }}" class="text-xs text-gray-500 transition-colors hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        {{ __('menu.privacy_policy') }}
                    </a>
                    <a href="{{ route('gdpr.terms') }}" class="text-xs text-gray-500 transition-colors hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        {{ __('menu.terms_of_service') }}
                    </a>
                </div>

                <!-- Danger Zone & Logout -->
                <div class="flex items-center justify-between">
                    @can('gdpr.delete_account')
                        <a href="{{ route('gdpr.delete-account') }}" class="text-xs text-red-500 transition-colors hover:text-red-700">
                            {{ __('menu.delete_account') }}
                        </a>
                    @endcan

                    <form method="POST" action="{{ route('logout') }}" class="ml-auto">
                        @csrf
                        <button type="submit" class="flex items-center px-4 py-2 space-x-2 text-sm text-white transition-colors bg-red-500 rounded-lg hover:bg-red-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            <span>{{ __('menu.logout') }}</span>
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
