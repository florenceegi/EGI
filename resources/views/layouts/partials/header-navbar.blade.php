<header
    class="sticky top-0 z-50 w-full border-b border-gray-700 shadow-lg navbar-simple-hide bg-gray-900/95 backdrop-blur-md"
    role="banner" aria-label="{{ __('guest_layout.header_aria_label') }}">
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        @php
        $user = App\Helpers\FegiAuth::user(); // User object or null
        @endphp

        {{-- Container principale - adattivo per auth --}}
        <div class="@if($user) flex flex-col @else h-16 @endif md:h-20">

            <div class="flex items-center justify-between h-16 md:h-20">

                @php
                $navLinkClasses =
                'text-gray-300 hover:text-emerald-400 hover:text-white transition px-3 py-2 rounded-md text-sm font-medium
                hover:bg-gray-800/40';
                @endphp

                {{-- Logo --}}
                <div class="flex items-center flex-shrink-0">
                    <a href="{{ url('/home') }}" class="flex items-center gap-2 group"
                        aria-label="{{ __('collection.logo_home_link_aria_label') }}">
                        <img src="{{ asset('images/logo/logo_1.webp') }}" alt="Frangette Logo"
                            class="w-auto h-7 sm:h-8 md:h-9" loading="lazy" decoding="async">
                        <span
                            class="hidden text-base font-semibold text-gray-400 transition group-hover:text-emerald-400 sm:inline md:text-lg">{{
                            __('Frangette') }}</span>
                    </a>
                    {{-- Welcome Message - Dopo il logo per tutte le dimensioni --}}
                    <x-user-welcome />

                    <x-section-border />

                    {{-- Notification Badge (Mobile) --}}
                    @if(App\Helpers\FegiAuth::check())
                    <x-notification-badge />
                    @endif
                </div>

                @php
                $authType = App\Helpers\FegiAuth::getAuthType(); // 'strong', 'weak', 'guest'
                $canCreateEgi = $user && $user->can('create_EGI');
                @endphp

                {{-- Nav Desktop --}}
                <nav class="items-center hidden space-x-1 md:flex" role="navigation"
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
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"/>
                        </svg>
                        <span class="hidden lg:inline">{{ __('gdpr.cookie.banner.preferences_short') }}</span>
                    </button>

                    {{-- Wallet e Auth --}}
                    <span class="h-6 mx-2 border-l border-gray-700" aria-hidden="true"></span>

                    @guest
                    <a href="{{ route('login') }}" id="login-link-desktop" class="{{ $navLinkClasses }}">{{__('collection.login') }}</a>
                    <a href="{{ route('register') }}" id="register-link-desktop"
                        class="inline-flex items-center px-4 py-2 ml-2 text-sm font-medium text-white bg-emerald-600 border border-emerald-600 rounded-md hover:bg-emerald-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-colors">
                        {{ __('collection.register') }}</a>
                    @endguest
                </nav>

                {{-- Menu Mobile Button - Sempre visibile --}}
                <button type="button" data-mobile-menu-trigger class="block p-1 transition-colors rounded-full md:hidden hover:bg-gray-800/50">
                    @auth
                        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                            <img class="object-cover rounded-full size-8 ring-2 ring-gray-600"
                                src="{{ Auth::user()->profile_photo_url }}"
                                alt="{{ Auth::user()->name }}" />
                        @else
                            <div class="flex items-center justify-center w-8 h-8 text-sm font-bold text-white bg-gray-600 rounded-full">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                        @endif
                    @else
                        {{-- Icona hamburger per utenti guest --}}
                        <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    @endauth
                </button>

                {{-- Bottone Accedi (Solo per guest su mobile) --}}
                @guest
                <div class="block md:hidden">
                    <a href="{{ route('login') }}"
                        class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-gray-300 bg-gray-800 border border-gray-700 rounded-md hover:bg-gray-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors">
                        {{ __('collection.login') }}
                    </a>
                    <a href="{{ route('register') }}" id="register-link-mobile"
                        class="inline-flex items-center px-4 py-2 ml-2 text-sm font-medium text-white bg-emerald-600 border border-emerald-600 rounded-md hover:bg-emerald-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-colors">
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
