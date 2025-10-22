<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="florenceegi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-authenticated" content="{{ auth()->check() ? 'true' : 'false' }}">
    <meta name="user-preferred-currency"
        content="{{ auth()->check() ? auth()->user()->preferred_currency ?? 'USD' : 'USD' }}">

    {{--
    @oracode-dimension technical
    @value-flow SuperAdmin infrastructure - AI & Platform Management Interface
    @community-impact Platform oversight and AI feature administration for sustainable growth
    @transparency-level Full administrative transparency - secure access control
    @sustainability-factor High governance efficiency - centralized platform management
    @narrative-coherence Embodies FlorenceEGI's commitment to transparent platform governance
    --}}

    {{-- Oracode 3.0: SEO & Metadata (Pillar #1 - Explicitly Intentional) --}}
    <title>{{ isset($pageTitle) ? $pageTitle . ' - ' . config('app.name') : config('app.name', 'FlorenceEGI') }}</title>
    <meta name="description"
        content="{{ $pageDescription ?? 'FlorenceEGI SuperAdmin - AI & Platform Management Dashboard. Secure administrative interface for platform oversight, AI feature configuration, and tokenomics management.' }}">
    <meta name="robots" content="{{ $robotsContent ?? 'noindex, nofollow' }}">
    <meta name="author" content="FlorenceEGI">

    {{-- Open Graph / Social Media Meta Tags --}}
    <meta property="og:type" content="{{ $ogType ?? 'website' }}">
    <meta property="og:title"
        content="{{ isset($pageTitle) ? $pageTitle . ' - ' . config('app.name') : config('app.name') }}">
    <meta property="og:description"
        content="{{ $pageDescription ?? 'FlorenceEGI SuperAdmin Dashboard - Platform Management' }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    @if (isset($ogImage))
        <meta property="og:image" content="{{ $ogImage }}">
    @endif

    <script>
        console.log('resources/views/layouts/superadmin.blade.php');
    </script>

    {{-- Twitter Card Meta Tags --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title"
        content="{{ isset($pageTitle) ? $pageTitle . ' - ' . config('app.name') : config('app.name') }}">
    <meta name="twitter:description"
        content="{{ $pageDescription ?? 'FlorenceEGI SuperAdmin Dashboard - Platform Management' }}">

    {{-- Fonts (FlorenceEGI Brand Guidelines) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&family=Source+Sans+Pro:ital,wght@0,300;0,400;0,600;0,700;1,300;1,400;1,600;1,700&family=JetBrains+Mono:wght@400;700&display=swap"
        rel="stylesheet">

    {{-- Icon Libraries --}}
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" media="print"
        onload="this.media='all'">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" media="print"
        onload="this.media='all'">

    {{-- Flag Icons for Internationalization --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@6.6.6/css/flag-icons.min.css"
        media="print" onload="this.media='all'">

    {{-- Application Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Oracode 3.0: Allow child views to inject custom styles (Pillar #5 - Predisposed to Variation) --}}
    @stack('styles')

    {{-- Livewire Styles --}}
    @livewireStyles

    {{-- Schema.org Structured Data (Pillar #2 - Semantically Coherent) --}}
    @if (isset($schemaData))
        <script type="application/ld+json">
        {!! json_encode($schemaData, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
    @else
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "WebApplication",
            "name": "{{ config('app.name') }} - SuperAdmin",
            "description": "SuperAdmin Dashboard for AI & Platform Management",
            "url": "{{ config('app.url') }}",
            "applicationCategory": "BusinessApplication",
            "operatingSystem": "Web Browser",
            "accessMode": "admin"
        }
    </script>
    @endif
</head>

{{--
Oracode 3.0: Semantic HTML Structure (Pillar #2 - Semantically Coherent)
Using proper HTML5 landmarks for accessibility (Pillar #4 - Interpretable by Assistive Tech)
--}}

<body class="bg-base-100 font-body text-base-content antialiased" itemscope itemtype="https://schema.org/WebPage">
    {{-- Skip to main content for accessibility (WCAG 2.1) --}}
    <a href="#main-content"
        class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-md focus:bg-primary focus:px-4 focus:py-2 focus:text-primary-content">
        {{ __('Skip to main content') }}
    </a>

    {{--
    SuperAdmin Layout Container
    Oracode 3.0: Contextually autonomous - dedicated administrative interface
    --}}
    <div class="flex min-h-screen" role="application" aria-label="FlorenceEGI SuperAdmin Interface">
        {{-- SuperAdmin Sidebar Component --}}
        <x-enterprise-sidebar logo="FlorenceEGI" badge="SuperAdmin" theme="superadmin" />

        {{-- Main Content Area --}}
        <div class="flex flex-1 flex-col">
            {{-- Optional Page Header Section --}}
            @if (isset($header))
                {{-- Oracode 3.0: Semantic section with proper ARIA labeling --}}
                <section class="bg-base-200 shadow" role="complementary" aria-label="Page header">
                    <div class="mx-auto max-w-7xl px-4 py-6 text-base-content sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </section>
            @endif

            {{--
            Main Content Area
            Oracode 3.0: Semantic main landmark with proper ARIA labeling
            --}}
            <main id="main-content" class="flex-1 overflow-y-auto overflow-x-hidden bg-base-100 p-4 lg:p-8"
                role="main" aria-label="SuperAdmin content" tabindex="-1">
                {{--
                Content Slot - All page content goes here
                Oracode 3.0: Preserves existing slot mechanism for backward compatibility
                --}}
                {{ $slot }}
            </main>
        </div>
    </div>

    {{-- Modal Stack (for Livewire/Alpine modals) --}}
    @stack('modals')

    {{--
    Application Configuration
    Oracode 3.0: Contextually autonomous - provides necessary config to client-side
    --}}
    <script>
        // Global app configuration for client-side scripts
        window.appConfig = @json(config('app'));

        // Accessibility enhancement: Focus management
        document.addEventListener('DOMContentLoaded', function() {
            const skipLink = document.querySelector('a[href="#main-content"]');
            if (skipLink) {
                skipLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    const mainContent = document.getElementById('main-content');
                    if (mainContent) {
                        mainContent.focus();
                    }
                });
            }
        });
    </script>

    {{-- SweetAlert2 for Flash Messages --}}
    @if (session('success') || session('error') || session('warning'))
        <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: '{{ __('Successo') }}',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: '{{ __('Errore') }}',
                    text: '{{ session('error') }}',
                    confirmButtonText: '{{ __('Chiudi') }}',
                });
            @endif

            @if (session('warning'))
                Swal.fire({
                    icon: 'warning',
                    title: '{{ __('Attenzione') }}',
                    text: '{{ session('warning') }}',
                    confirmButtonText: '{{ __('OK') }}',
                });
            @endif
        </script>
    @endif

    {{-- Oracode 3.0: Allow child views to inject custom scripts --}}
    @stack('scripts')

    {{-- Livewire Scripts --}}
    @livewireScripts

    {{-- GDPR Cookie Consent Banner --}}
    <x-gdpr.cookie-banner />
</body>

</html>
