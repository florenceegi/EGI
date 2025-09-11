<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-900 scroll-smooth">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#121212">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    {{-- SEO & Semantica --}}
    <title>{{ $title ?? __('collection.default_page_title') }}</title>
    <meta name="description" content="{{ $metaDescription ?? __('collection.default_meta_description') }}">
    {!! $headMetaExtra ?? '
    <meta name="robots" content="index, follow">' !!}

    {{-- Favicon --}}
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="apple-touch-icon" href="{{ asset('images/logo/apple-touch-icon.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" href="{{ asset('images/logo/logo_1.webp') }}" as="image">

    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,400;1,500;1,600;1,700;1,800;1,900&family=Source+Sans+Pro:ital,wght@0,300;0,400;0,600;0,700;1,300;1,400;1,600;1,700&family=JetBrains+Mono:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <link rel="preload"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"
        as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet"
            href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">
    </noscript>


    {{-- Asset CSS (Vite) --}}
    @vite(['resources/css/app.css', 'resources/css/guest.css', 'resources/css/modal-fix.css'])

    {{-- Font Awesome --}}
    {{-- Schema.org Markup per il WebSite (Generale) --}}
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "WebSite",
            "url": "https://florenceegi.com/",
            "name": "{{ __('site.schema.website.name') }}",
            "description": "{{ __('site.schema.website.description') }}",
            "publisher": {
                "@type": "Organization",
                "name": "{{ __('site.schema.publisher.name') }}",
                "url": "https://frangette.com/",
                "logo": {
                "@type": "ImageObject",
                "url": "{{ asset('images/frangette-logo.png') }}"
                }
            }
        }
    </script>


    {{-- Slot per Schema.org specifico della pagina --}}
    {{ $schemaMarkup ?? '' }}

    {{-- Slot per meta tag aggiuntivi specifici della pagina --}}
    {{ $headExtra ?? '' }}

    {{-- Stack per stili specifici della pagina --}}
    @stack('styles')

</head>

<body class="flex flex-col min-h-screen antialiased text-gray-300 bg-gray-900 font-body">

    <header
        class="sticky top-0 z-50 w-full border-b border-gray-800 shadow-lg navbar-simple-hide bg-gray-900/90 backdrop-blur-md"
        role="banner" aria-label="{{ __('guest_layout.header_aria_label') }}">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            @php
            $user = App\Helpers\FegiAuth::user(); // User object or null
            @endphp

            {{-- Container principale - adattivo per auth --}}
            <div class="@if($user) flex flex-col @else h-16 @endif md:h-20">

                {{-- Prima riga: sempre presente --}}
                <div class="flex items-center justify-between h-16 md:h-20">

                    @php
                    $navLinkClasses =
                    'text-gray-300 hover:text-emerald-400 transition px-3 py-2 rounded-md text-sm font-medium
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
                    </div>

                    {{-- Notification Badge (Desktop) --}}
                    @if(App\Helpers\FegiAuth::check())
                    <div class="hidden md:block">
                        <x-notific                        /* MOBILE - Titolo cliccabile */
                        w-full flex justify-between    /* Occupa tutto lo spazio */
                        chevron visibile              /* Indica collassabilità */

                        /* DESKTOP - Titolo normale */
                        md:pointer-events-none        /* Non cliccabile */
                        md:hidden                     /* Chevron nascosto */ation-badge />
                    </div>
                    @endif

                    {{-- EUR → ALGO Badge (Desktop) - Fixed Currency, Live Rate --}}
                    {{-- <x-currency-badge size="desktop" position="header" /> --}}

                    {{-- Collection Badge (Desktop) --}}
                    {{-- <div id="current-collection-badge-container-desktop" class="items-center hidden ml-3 md:flex">
                        <a href="#" id="current-collection-badge-link-desktop"
                            class="flex items-center px-3 py-1.5 text-sm font-semibold transition border rounded-lg border-sky-700 bg-sky-900/60 text-sky-300 hover:border-sky-600 hover:bg-sky-800">
                            <span class="mr-2 text-sm leading-none material-symbols-outlined"
                                aria-hidden="true">folder_managed</span>
                            <span id="current-collection-badge-name-desktop"></span>
                        </a>
                    </div> --}}

                    {{-- NEW: Test Autonomous Collection Badge Component --}}

                    {{-- Professional Currency Badge (Always Visible - Mobile First) --}}

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

                        {{-- Create EGI Button - Solo per utenti con permesso --}}

                        {{-- Wallet e Auth --}}
                        <span class="h-6 mx-2 border-l border-gray-700" aria-hidden="true"></span>

                        {{-- <div id="wallet-cta-container" class="ml-2">
                            <button id="connect-wallet-button" type="button"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                <svg class="w-5 h-5 mr-2 -ml-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21 12a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 1-6 0H5.25A2.25 2.25 0 0 0 3 12m18 0v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6m18 0V9M3 12V9m18 3a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 0-6 0H5.25A2.25 2.25 0 0 0 3 12m15-3a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3m12 6v2.25a2.25 2.25 0 0 1-2.25 2.25H9a2.25 2.25 0 0 1-2.25-2.25V15m3 0a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3m9 0a3 3 0 0 0 3-3h1.5a3 3 0 0 0 3 3" />
                                </svg>
                                {{ __('collection.wallet.button_wallet_connect') }}
                            </button>
                            <div id="wallet-dropdown-container" class="relative hidden">
                                <button id="wallet-dropdown-button" type="button"
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                                    aria-expanded="false" aria-haspopup="true">
                                    <svg class="w-5 h-5 mr-2 -ml-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                        stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M21 12a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 1-6 0H5.25A2.25 2.25 0 0 0 3 12m18 0v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6m18 0V9M3 12V9m18 3a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 0-6 0H5.25A2.25 2.25 0 0 0 3 12m15-3a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3m12 6v2.25a2.25 2.25 0 0 1-2.25 2.25H9a2.25 2.25 0 0 1-2.25-2.25V15m3 0a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3m9 0a3 3 0 0 0 3-3h1.5a3 3 0 0 0 3 3" />
                                    </svg>
                                    <span id="wallet-display-text" class="hidden sm:inline">{{
                                        __('collection.wallet.wallet') }}</span>
                                    <svg class="w-4 h-4 ml-1 -mr-1" fill="currentColor" viewBox="0 0 20 20"
                                        aria-hidden="true">
                                        <path fill-rule="evenodd"
                                            d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <div id="wallet-dropdown-menu"
                                    class="absolute right-0 z-20 hidden w-56 py-1 mt-2 origin-top-right bg-gray-900 border border-gray-800 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 backdrop-blur-sm focus:outline-none">
                                    <a href="{{ route('dashboard') }}" id="wallet-dashboard-link"
                                        class="flex items-center w-full px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 hover:text-white">
                                        <span class="w-5 h-5 mr-2 text-gray-400 material-symbols-outlined"
                                            aria-hidden="true">dashboard</span>
                                        {{ __('collection.dashboard') }}
                                    </a>
                                    <button id="wallet-copy-address"
                                        class="flex items-center w-full px-4 py-2 text-sm text-left text-gray-300 hover:bg-gray-800 hover:text-white">
                                        <span class="w-5 h-5 mr-2 text-gray-400 material-symbols-outlined"
                                            aria-hidden="true">content_copy</span>
                                        {{ __('collection.wallet.copy_address') }}
                                    </button>
                                    <button id="wallet-disconnect"
                                        class="flex items-center w-full px-4 py-2 text-sm text-left text-gray-300 hover:bg-gray-800 hover:text-white">
                                        <span class="w-5 h-5 mr-2 text-gray-400 material-symbols-outlined"
                                            aria-hidden="true">logout</span>
                                        {{ __('collection.wallet.button_wallet_disconnect') }}
                                    </button>
                                </div>
                            </div>
                        </div> --}}
                        <a href="{{ route('login') }}" id="login-link-desktop" class="{{ $navLinkClasses }}">{{__('collection.login') }}</a>
                        <a href="{{ route('register') }}" id="register-link-desktop"
                            class="inline-flex items-center px-4 py-2 ml-2 text-sm font-medium text-gray-300 bg-gray-800 border border-gray-700 rounded-md hover:bg-gray-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            {{ __('collection.register') }}</a>
                        {{-- Guest Universal Search Trigger --}}
                        {{-- <button type="button" class="{{ $navLinkClasses }} inline-flex items-center gap-1"
                                onclick="window.UniversalSearch ? window.UniversalSearch.open() : window.dispatchEvent(new CustomEvent('universal-search-open'))"
                                aria-label="{{ __('collection.search') }}">
                            <span class="text-base material-symbols-outlined" aria-hidden="true">search</span>
                            <span>{{ __('collection.search') }}</span>
                        </button> --}}
                    </nav>

                    {{-- Menu Mobile Button --}}
                    {{-- <div class="flex items-center gap-2 -mr-2 md:hidden"> --}}

                        {{-- Notification Badge (Mobile) --}}
                        @if(App\Helpers\FegiAuth::check())
                        <x-notification-badge />
                        @endif

                        {{-- EUR → ALGO Badge (Mobile) - Fixed Currency, Live Rate --}}
                        {{-- <x-currency-badge size="mobile" position="header" /> --}}

                        {{-- Menu Mobile Button --}}
                        @auth
                        <button type="button" data-mobile-menu-trigger class="block p-1 transition-colors rounded-full md:hidden hover:bg-gray-800/50">
                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                <img class="object-cover rounded-full size-8 ring-2 ring-gray-600"
                                    src="{{ Auth::user()->profile_photo_url }}"
                                    alt="{{ Auth::user()->name }}" />
                            @else
                                <div class="flex items-center justify-center w-8 h-8 text-sm font-bold text-white bg-gray-600 rounded-full">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                            @endif
                        </button>
                        @endauth

                        {{-- Bottone Accedi (Solo per guest su mobile) --}}
                        @guest
                        <div class="block md:hidden">
                            <a href="{{ route('login') }}"
                                class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-gray-300 bg-gray-800 border border-gray-700 rounded-md hover:bg-gray-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                                {{ __('collection.login') }}
                            </a>
                            <a href="{{ route('register') }}" id="register-link-desktop"
                                class="inline-flex items-center px-4 py-2 ml-2 text-sm font-medium text-gray-300 bg-gray-800 border border-gray-700 rounded-md hover:bg-gray-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                {{ __('collection.register') }}
                            </a>
                        </div>
                        @endguest

                    {{-- </div> --}}
                    {{-- Fine Prima Riga --}}

                </div>

                {{-- Fine Container Principale --}}
            </div>
        </div>

        {{-- CLEAN: Removed old mobile menu system - now using vanilla components --}}
    </header>

    {{-- Mobile Menu Component - Outside header for proper overlay positioning --}}
    @auth
    <x-navigation.vanilla-mobile-menu />
    @endauth

    <script>
        window.addEventListener('load', function() {
        const header = document.querySelector('header[role="banner"]');
        if (header) {
            header.classList.remove('navbar-simple-hide');
            header.classList.add('navbar-simple-show');
        }
    });

    // Currency Badge Manager
    // class CurrencyBadgeManager {
    //     constructor() {
    //         this.elements = {
    //             // Desktop elements
    //             symbol: document.getElementById('currency-symbol'),
    //             rateValue: document.getElementById('currency-rate-value'),
    //             lastUpdated: document.getElementById('currency-last-updated'),
    //             // Mobile elements
    //             symbolMobile: document.getElementById('currency-symbol-mobile'),
    //             rateValueMobile: document.getElementById('currency-rate-value-mobile'),
    //             lastUpdatedMobile: document.getElementById('currency-last-updated-mobile'),
    //             // Switch elements (future use)
    //             switchButton: document.getElementById('currency-switch-button'),
    //             switchMenu: document.getElementById('currency-switch-menu'),
    //             currencyOptions: document.querySelectorAll('.currency-option')
    //         };

    //         this.currentCurrency = 'EUR'; // Fixed to EUR in simplified system
    //         this.updateInterval = null;
    //         this.init();
    //     }

    //     init() {
    //         this.fetchAndUpdateRate();
    //         this.startAutoUpdate();
    //         this.bindEvents();
    //     }

    //     startAutoUpdate() {
    //         // Update every 30 seconds
    //         this.updateInterval = setInterval(() => {
    //             this.fetchAndUpdateRate();
    //         }, 60000);
    //     }

    //     async fetchAndUpdateRate() {
    //         try {
    //             // Step 1: Always get EUR rate (simplified EUR-only system)
    //             const response = await fetch('/api/currency/rate/EUR', {
    //                 headers: {
    //                     'Accept': 'application/json',
    //                     'X-Requested-With': 'XMLHttpRequest'
    //                 }
    //             });

    //             if (!response.ok) {
    //                 throw new Error(`Failed to fetch EUR rate: ${response.status}`);
    //             }

    //             const data = await response.json();

    //             if (data.success) {
    //                 // Use the standardized API response format
    //                 const currency = data.data?.fiat_currency || userCurrency;
    //                 const rate = data.data?.rate_to_algo || 0;
    //                 const timestamp = data.data?.timestamp || new Date().toISOString();

    //                 this.updateBadge(currency, rate, timestamp);
    //             } else {
    //                 this.showError();
    //             }
    //         } catch (error) {
    //             console.error('Failed to fetch currency rate:', error);
    //             this.showError();
    //         }
    //     }

    //     updateBadge(currency, rate, updatedAt) {
    //         // Check if currency has actually changed
    //         const currencyChanged = this.currentCurrency !== currency;

    //         // Animate currency symbol change (Desktop)
    //         if (this.elements.symbol && this.elements.symbol.textContent !== currency) {
    //             this.animateValueChange(this.elements.symbol, currency);
    //         }

    //         // Animate currency symbol change (Mobile)
    //         if (this.elements.symbolMobile && this.elements.symbolMobile.textContent !== currency) {
    //             this.animateValueChange(this.elements.symbolMobile, currency);
    //         }

    //         // Format and animate rate change (Desktop)
    //         if (this.elements.rateValue) {
    //             const formattedRate = this.formatRate(rate);
    //             if (this.elements.rateValue.textContent !== formattedRate) {
    //                 this.animateValueChange(this.elements.rateValue, formattedRate);
    //             }
    //         }

    //         // Format and animate rate change (Mobile)
    //         if (this.elements.rateValueMobile) {
    //             const formattedRate = this.formatRate(rate);
    //             if (this.elements.rateValueMobile.textContent !== formattedRate) {
    //                 this.animateValueChange(this.elements.rateValueMobile, formattedRate);
    //             }
    //         }

    //         // Update timestamp with elegant formatting (Desktop)
    //         if (this.elements.lastUpdated) {
    //             const time = this.formatTimestamp(updatedAt);
    //             this.elements.lastUpdated.textContent = time;
    //         }

    //         // Update timestamp with elegant formatting (Mobile)
    //         if (this.elements.lastUpdatedMobile) {
    //             const time = this.formatTimestamp(updatedAt);
    //             this.elements.lastUpdatedMobile.textContent = time;
    //         }

    //         this.currentCurrency = currency;

    //         // 🚀 NOTIFY CURRENCY DISPLAY COMPONENT IF CURRENCY CHANGED
    //         if (currencyChanged) {
    //             const currencyChangeEvent = new CustomEvent('currencyChanged', {
    //                 detail: { currency: currency }
    //             });
    //             document.dispatchEvent(currencyChangeEvent);
    //         }

    //         // Add success flash animation
    //         this.flashSuccess();
    //     }

    //     formatRate(rate) {
    //         if (rate === 0 || !rate) return '--';

    //         // Professional number formatting with appropriate decimals
    //         if (rate >= 1) {
    //             return rate.toFixed(4);
    //         } else if (rate >= 0.01) {
    //             return rate.toFixed(6);
    //         } else {
    //             return rate.toFixed(8);
    //         }
    //     }

    //     formatTimestamp(timestamp) {
    //         try {
    //             const date = new Date(timestamp);
    //             const now = new Date();
    //             const diffMs = now - date;
    //             const diffSecs = Math.floor(diffMs / 1000);

    //             if (diffSecs < 30) return 'Just now';
    //             if (diffSecs < 60) return `${diffSecs}s ago`;
    //             if (diffSecs < 3600) return `${Math.floor(diffSecs / 60)}m ago`;

    //             return date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
    //         } catch (e) {
    //             return 'Updated';
    //         }
    //     }

    //     animateValueChange(element, newValue) {
    //         // Professional fade-in animation for value changes
    //         element.style.opacity = '0.5';
    //         element.style.transform = 'scale(0.95)';

    //         setTimeout(() => {
    //             element.textContent = newValue;
    //             element.style.opacity = '1';
    //             element.style.transform = 'scale(1)';
    //             element.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
    //         }, 150);
    //     }

    //     flashSuccess() {
    //         // Add subtle success animation to badge
    //         const badge = document.getElementById('currency-badge');
    //         if (badge) {
    //             badge.style.boxShadow = '0 20px 25px -5px rgba(16, 185, 129, 0.3), 0 10px 10px -5px rgba(16, 185, 129, 0.2)';
    //             setTimeout(() => {
    //                 badge.style.boxShadow = '';
    //                 badge.style.transition = 'box-shadow 1s ease-out';
    //             }, 500);
    //         }
    //     }

    //     showError() {
    //         // Professional error state with visual feedback
    //         const badge = document.getElementById('currency-badge');

    //         // Desktop elements
    //         if (this.elements.rateValue) {
    //             this.animateValueChange(this.elements.rateValue, 'ERROR');
    //         }

    //         if (this.elements.lastUpdated) {
    //             this.elements.lastUpdated.textContent = 'Connection failed';
    //         }

    //         // Mobile elements
    //         if (this.elements.rateValueMobile) {
    //             this.animateValueChange(this.elements.rateValueMobile, 'ERROR');
    //         }

    //         if (this.elements.lastUpdatedMobile) {
    //             this.elements.lastUpdatedMobile.textContent = 'Connection failed';
    //         }

    //         // Add error visual feedback
    //         if (badge) {
    //             badge.style.boxShadow = '0 20px 25px -5px rgba(239, 68, 68, 0.3), 0 10px 10px -5px rgba(239, 68, 68, 0.2)';
    //             badge.style.borderColor = 'rgba(239, 68, 68, 0.5)';

    //             setTimeout(() => {
    //                 badge.style.boxShadow = '';
    //                 badge.style.borderColor = '';
    //                 badge.style.transition = 'all 1s ease-out';
    //             }, 2000);
    //         }

    //         // Retry after a short delay
    //         setTimeout(() => {
    //             this.fetchAndUpdateRate();
    //         }, 5000);
    //     }

    //     bindEvents() {
    //         // Currency switch dropdown
    //         if (this.elements.switchButton && this.elements.switchMenu) {
    //             this.elements.switchButton.addEventListener('click', (e) => {
    //                 e.preventDefault();
    //                 e.stopPropagation();
    //                 this.elements.switchMenu.classList.toggle('hidden');
    //             });

    //             // Close dropdown when clicking outside
    //             document.addEventListener('click', (e) => {
    //                 if (!this.elements.switchButton.contains(e.target) &&
    //                     !this.elements.switchMenu.contains(e.target)) {
    //                     this.elements.switchMenu.classList.add('hidden');
    //                 }
    //             });
    //         }

    //         // Currency option clicks
    //         this.elements.currencyOptions.forEach(option => {
    //             option.addEventListener('click', async (e) => {
    //                 e.preventDefault();
    //                 const newCurrency = option.getAttribute('data-currency');
    //                 await this.switchCurrency(newCurrency);
    //                 this.elements.switchMenu.classList.add('hidden');
    //             });
    //         });
    //     }

    //     async switchCurrency(newCurrency) {
    //         try {
    //             const response = await fetch('/api/user/preferred-currency', {
    //                 method: 'POST',
    //                 headers: {
    //                     'Content-Type': 'application/json',
    //                     'Accept': 'application/json',
    //                     'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    //                     'X-Requested-With': 'XMLHttpRequest'
    //                 },
    //                 body: JSON.stringify({ preferred_currency: newCurrency })
    //             });

    //             if (response.ok) {
    //                 // Update current currency
    //                 this.currentCurrency = newCurrency;

    //                 // Immediately fetch new rate (this will emit the currencyChanged event via updateBadge)
    //                 this.fetchAndUpdateRate();

    //                 // Show success feedback
    //                 this.showSuccess(`Currency switched to ${newCurrency}`);
    //             } else {
    //                 throw new Error('Failed to update currency preference');
    //             }
    //         } catch (error) {
    //             console.error('Failed to switch currency:', error);
    //             this.showError('Failed to switch currency');
    //         }
    //     }

    //     showSuccess(message) {
    //         // Simple success indicator - you can enhance this
    //         const badge = document.getElementById('currency-badge');
    //         if (badge) {
    //             badge.classList.add('ring-2', 'ring-emerald-400');
    //             setTimeout(() => {
    //                 badge.classList.remove('ring-2', 'ring-emerald-400');
    //             }, 1500);
    //         }
    //     }

    //     destroy() {
    //         if (this.updateInterval) {
    //             clearInterval(this.updateInterval);
    //         }
    //     }
    // }

    // Initialize Currency Badge Manager
    // let currencyBadgeManager;
    // document.addEventListener('DOMContentLoaded', function() {
    //     currencyBadgeManager = new CurrencyBadgeManager();
    // });

    // // Cleanup on page unload
    // window.addEventListener('beforeunload', function() {
    //     if (currencyBadgeManager) {
    //         currencyBadgeManager.destroy();
    //     }
    // });
    </script>
