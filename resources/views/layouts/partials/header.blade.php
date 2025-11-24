<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-900 scroll-smooth">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#121212">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    {{-- 🎬 SPLASH SCREEN CSS - Overlay sopra la home --}}
    @if (request()->is('home'))
        <style>
            body {
                visibility: hidden;
                /* Nascosto all'inizio, lo splash lo mostrerà dopo il fade */
            }

            #home-splash-root {
                position: fixed;
                inset: 0;
                z-index: 9999;
                visibility: visible;
            }
        </style>
    @endif

    {{-- SEO & Semantica --}}
    <title>{{ $title ?? __('collection.default_page_title') }}</title>
    <meta name="description" content="{{ $metaDescription ?? __('collection.default_meta_description') }}">
    {!! $headMetaExtra ??
        '
            <meta name="robots" content="index, follow">' !!}

    {{-- Favicon --}}
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="apple-touch-icon" href="{{ asset('images/logo/apple-touch-icon.png') }}">

    {{-- Dark Mode Detection Script --}}
    <script>
        // Detecta preferenze tema del sistema e applica immediatamente
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia(
                '(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>

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

    <script>
        console.log('resources/views/layouts/partials/header.blade.php');
    </script>
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

    {{-- Livewire Styles --}}
    @livewireStyles


</head>

<body class="flex flex-col min-h-screen antialiased text-gray-300 bg-gray-900 font-body">

    {{-- 🎬 SPLASH SCREEN - React 3D Animation (solo per home) --}}
    @if (request()->is('home'))
        <div id="home-splash-root"></div>
        @vite(['resources/react/home/home-splash.tsx'])
    @endif

    {{-- Cookie Consent Banner - Universal GDPR compliance for all visitors --}}
    @include('components.gdpr.cookie-banner')

    @include('layouts.partials.header-navbar')
