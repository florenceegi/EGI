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
                    {{-- @if(App\Helpers\FegiAuth::check())
                    <div class="hidden md:block">
                        <x-notific                        /* MOBILE - Titolo cliccabile */
                        w-full flex justify-between    /* Occupa tutto lo spazio */
                        chevron visibile              /* Indica collassabilità */

                        /* DESKTOP - Titolo normale */
                        md:pointer-events-none        /* Non cliccabile */
                        md:hidden                     /* Chevron nascosto */ation-badge />
                    </div>
                    @endif --}}


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


                        <a href="{{ route('login') }}" id="login-link-desktop" class="{{ $navLinkClasses }}">{{__('collection.login') }}</a>
                        <a href="{{ route('register') }}" id="register-link-desktop"
                            class="inline-flex items-center px-4 py-2 ml-2 text-sm font-medium text-gray-300 bg-gray-800 border border-gray-700 rounded-md hover:bg-gray-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            {{ __('collection.register') }}</a>

                    </nav>



                    {{-- Notification Badge (Mobile) --}}
                    @if(App\Helpers\FegiAuth::check())
                    <x-notification-badge />
                    @endif


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
