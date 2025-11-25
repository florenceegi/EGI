<header
    class="navbar-simple-hide sticky top-0 z-50 w-full border-b border-gray-700 bg-gray-900/95 shadow-lg backdrop-blur-md"
    role="banner" aria-label="{{ __('guest_layout.header_aria_label') }}">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        @php
            $user = App\Helpers\FegiAuth::user(); // User object or null
        @endphp

        {{-- Container principale - adattivo per auth --}}
        <div class="@if ($user) flex flex-col @else h-16 @endif md:h-20">

            <div class="flex h-16 items-center justify-between md:h-20">

                @php
                    $navLinkClasses = 'text-gray-300 hover:text-emerald-400 hover:text-white transition px-3 py-2 rounded-md text-sm font-medium
                hover:bg-gray-800/40';
                @endphp

                {{-- Logo --}}
                <div class="flex flex-shrink-0 items-center">
                    <a href="{{ url('/home') }}" class="group flex items-center gap-2"
                        aria-label="{{ __('collection.logo_home_link_aria_label') }}">
                        <img src="{{ asset('images/logo/logo_1.webp') }}" alt="Frangette Logo"
                            class="h-7 w-auto sm:h-8 md:h-9" loading="lazy" decoding="async">
                        <span
                            class="hidden text-base font-semibold text-gray-400 transition group-hover:text-emerald-400 sm:inline md:text-lg">{{ __('Frangette') }}</span>
                    </a>
                    {{-- Welcome Message - Dopo il logo per tutte le dimensioni --}}
                    <x-user-welcome />

                    <x-section-border />

                    {{-- Notification Badge (Mobile) --}}
                    @if (App\Helpers\FegiAuth::check())
                        <x-notification-badge />
                    @endif
                </div>

                @php
                    $authType = App\Helpers\FegiAuth::getAuthType(); // 'strong', 'weak', 'guest'
                    $canCreateEgi = $user && $user->can('create_EGI');
                @endphp

                {{-- Nav Desktop --}}
                <nav class="hidden items-center space-x-1 md:flex" role="navigation"
                    aria-label="{{ __('collection.main_navigation_aria_label') }}">

                    @include('partials.nav-links', ['isMobile' => false, 'authType' => $authType])

                    @auth
                        <x-navigation.vanilla-desktop-menu />
                    @endauth

                    {{-- Cookie Preferences Link --}}
                    <button type="button" onclick="window.cookieBannerManager?.showBanner()"
                        class="{{ $navLinkClasses }} flex items-center gap-1"
                        title="{{ __('gdpr.cookie.banner.preferences_title') }}"
                        aria-label="{{ __('gdpr.cookie.banner.preferences_title') }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4" />
                        </svg>
                        <span class="hidden lg:inline">{{ __('gdpr.cookie.banner.preferences_short') }}</span>
                    </button>

                    {{-- Wallet e Auth --}}
                    <span class="mx-2 h-6 border-l border-gray-700" aria-hidden="true"></span>

                    @guest
                        <a href="{{ route('login') }}" id="login-link-desktop"
                            class="{{ $navLinkClasses }}">{{ __('collection.login') }}</a>
                        {{-- Real Wallet Connect Button (per utenti esperti con wallet Algorand) --}}
                        <button type="button" id="connect-real-wallet-button" data-action="open-real-wallet-modal"
                            class="ml-2 inline-flex items-center rounded-md border border-emerald-500 px-3 py-2 text-sm font-medium text-emerald-400 transition-colors hover:bg-emerald-600/20 hover:text-emerald-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                            title="{{ __('collection.wallet.real_connect_cta_tooltip') }}"
                            aria-label="{{ __('collection.wallet.real_connect_cta_tooltip') }}">
                            <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span class="hidden lg:inline">{{ __('collection.wallet.real_connect_cta') }}</span>
                            <span class="lg:hidden">Wallet</span>
                        </button>
                        <a href="{{ route('register') }}" id="register-link-desktop"
                            class="ml-2 inline-flex items-center rounded-md border border-emerald-600 bg-emerald-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-emerald-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                            {{ __('collection.register') }}</a>
                    @endguest
                </nav>

                {{-- Menu Mobile Button - Sempre visibile --}}
                <button type="button" data-mobile-menu-trigger
                    class="block rounded-full p-1 transition-colors hover:bg-gray-800/50 md:hidden">
                    @auth
                        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                            <img class="size-8 rounded-full object-cover ring-2 ring-gray-600"
                                src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                        @else
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-600 text-sm font-bold text-white">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                        @endif
                    @else
                        {{-- Icona hamburger per utenti guest --}}
                        <svg class="h-6 w-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    @endauth
                </button>

                {{-- Bottone Accedi (Solo per guest su mobile) --}}
                @guest
                    <div class="flex items-center gap-2 md:hidden">
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center rounded-md border border-gray-700 bg-gray-800 px-3 py-1.5 text-sm font-medium text-gray-300 transition-colors hover:bg-gray-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            {{ __('collection.login') }}
                        </a>
                        {{-- Real Wallet Connect Button Mobile --}}
                        <button type="button" id="connect-real-wallet-button-mobile" data-action="open-real-wallet-modal"
                            class="inline-flex items-center rounded-md border border-emerald-500 px-2.5 py-1.5 text-sm font-medium text-emerald-400 transition-colors hover:bg-emerald-600/20 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            title="{{ __('collection.wallet.real_connect_cta_tooltip') }}"
                            aria-label="{{ __('collection.wallet.real_connect_cta_tooltip') }}">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </button>
                        <a href="{{ route('register') }}" id="register-link-mobile"
                            class="inline-flex items-center rounded-md border border-emerald-600 bg-emerald-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-emerald-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                            {{ __('collection.register') }}
                        </a>
                    </div>
                @endguest

            </div>
        </div>
    </div>

</header>

{{-- Mobile Menu Component - Outside header for proper overlay positioning --}}
<x-navigation.vanilla-mobile-menu />

<script>
    window.addEventListener('load', function() {
        const header = document.querySelector('header[role="banner"]');
        if (header) {
            header.classList.remove('navbar-simple-hide');
            header.classList.add('navbar-simple-show');
        }
    });
</script>
