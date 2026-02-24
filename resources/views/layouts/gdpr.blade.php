{{-- resources/views/components/gdpr-layout.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- SEO & Metadata --}}
    <title>{{ $pageTitle . ' - ' . config('app.name') }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <meta name="robots" content="noindex, nofollow">{{-- Privacy pages should not be indexed --}}

    {{-- Fonts (FlorenceEGI Brand Guidelines) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700&family=Source+Sans+Pro:ital,wght@0,300;0,400;0,600;0,700&family=JetBrains+Mono:wght@400;700&display=swap"
        rel="stylesheet">

    {{-- Application Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- GDPR Specific Styles (moved to external file for better performance) --}}
    <link rel="stylesheet" href="{{ asset('css/gdpr.css') }}">

    @stack('styles')
</head>

<body class="gdpr-container font-body antialiased">
    {{-- Skip to main content for accessibility --}}
    <a href="#gdpr-main-content"
        class="gdpr-btn-primary sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-md focus:px-4 focus:py-2">
        {{ __('Skip to main content') }}
    </a>

    {{-- GDPR Header --}}
    <header class="gdpr-header sticky top-0 z-40" role="banner" aria-label="{{ __('gdpr.navigation_label') }}">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                {{-- Logo/Brand --}}
                <div class="flex items-center">
                    <a href="{{ route('dashboard') }}" class="gdpr-link flex items-center space-x-3">
                        <x-florence-egi-logo class="h-8 w-8" />
                        <span class="gdpr-title text-lg">{{ config('app.name') }}</span>
                    </a>
                </div>

                {{-- User Info --}}
                <div class="flex items-center space-x-4">
                    <span class="gdpr-text text-sm">{{ auth()->user()->name }}</span>
                    <a href="{{ route('dashboard') }}" class="gdpr-btn-secondary rounded-lg px-3 py-1 text-sm">
                        {{ __('gdpr.back_to_dashboard') }}
                    </a>
                </div>
            </div>
        </div>
    </header>

    {{-- Main Content --}}
    <main id="gdpr-main-content" class="flex-1" role="main" aria-label="{{ __('gdpr.main_content_label') }}"
        tabindex="-1">
        {{-- Page Header --}}
        @isset($header)
            <section class="px-4 py-6 sm:px-6 lg:px-8" role="complementary" aria-label="Page header">
                <div class="mx-auto max-w-7xl">
                    {{ $header }}
                </div>
            </section>
        @endisset

        {{-- Content Section --}}
        <div class="px-4 pb-8 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
                @yield('content')
            </div>
        </div>
    </main>

    {{-- Footer --}}
    @include('components.info-footer')

    {{-- Application Configuration --}}
    <script>
        window.appConfig = @json(config('app'));

        // GDPR specific configuration
        window.gdprConfig = {
            locale: '{{ app()->getLocale() }}',
            csrfToken: '{{ csrf_token() }}',
            routes: {
                consent: '{{ route('gdpr.consent') }}',
                export: '{{ route('gdpr.export-data') }}',
                restrict: '{{ route('gdpr.limit-processing') }}',
                delete: '{{ route('gdpr.delete-account') }}'
            }
        };
    </script>

    @stack('scripts')
</body>

</html>
