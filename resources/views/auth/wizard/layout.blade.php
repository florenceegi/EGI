{{-- resources/views/auth/wizard/layout.blade.php --}}
{{-- 🎯 Registration Wizard Layout - FlorenceEGI --}}
{{-- ✅ Schema.org + OpenGraph + ARIA Accessibility --}}
{{-- ✅ SEO Optimized + Mobile First + WCAG 2.1 AA --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth" dir="ltr"
    prefix="og: http://ogp.me/ns#">

<head>
    {{-- ═══════════════════════════════════════════════════════════════════════════
         ESSENTIAL META TAGS
    ═══════════════════════════════════════════════════════════════════════════ --}}
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#1B365D">
    <meta name="color-scheme" content="light">

    {{-- Mobile App Meta --}}
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="FlorenceEGI">
    <meta name="application-name" content="FlorenceEGI">

    {{-- ═══════════════════════════════════════════════════════════════════════════
         SEO META TAGS
    ═══════════════════════════════════════════════════════════════════════════ --}}
    <title>{{ __('register.seo_title') }} | FlorenceEGI</title>
    <meta name="description" content="{{ __('register.seo_description') }}">
    <meta name="keywords"
        content="registrazione, FlorenceEGI, NFT culturali, patrimonio digitale, Firenze, blockchain cultura">
    <meta name="author" content="Frangette S.r.l.">
    <meta name="robots" content="noindex, nofollow">
    {{-- Registration pages should not be indexed --}}
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Language Alternates for Multi-language Support --}}
    @foreach (['it', 'en', 'de', 'es', 'fr', 'pt'] as $lang)
        <link rel="alternate" hreflang="{{ $lang }}" href="{{ url('/join') }}?lang={{ $lang }}">
    @endforeach
    <link rel="alternate" hreflang="x-default" href="{{ url('/join') }}">

    {{-- ═══════════════════════════════════════════════════════════════════════════
         OPEN GRAPH META TAGS
    ═══════════════════════════════════════════════════════════════════════════ --}}
    <meta property="og:site_name" content="FlorenceEGI">
    <meta property="og:title" content="{{ __('register.seo_title') }} | FlorenceEGI">
    <meta property="og:description" content="{{ __('register.seo_description') }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset('images/og/registration-preview.jpg') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="{{ __('register.seo_title') }}">
    <meta property="og:locale" content="{{ str_replace('_', '-', app()->getLocale()) }}">
    @foreach (['it_IT', 'en_US', 'de_DE', 'es_ES', 'fr_FR', 'pt_PT'] as $locale)
        @if ($locale !== str_replace('-', '_', app()->getLocale()))
            <meta property="og:locale:alternate" content="{{ $locale }}">
        @endif
    @endforeach

    {{-- ═══════════════════════════════════════════════════════════════════════════
         TWITTER CARD META TAGS
    ═══════════════════════════════════════════════════════════════════════════ --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@FlorenceEGI">
    <meta name="twitter:creator" content="@FlorenceEGI">
    <meta name="twitter:title" content="{{ __('register.seo_title') }} | FlorenceEGI">
    <meta name="twitter:description" content="{{ __('register.seo_description') }}">
    <meta name="twitter:image" content="{{ asset('images/og/registration-preview.jpg') }}">
    <meta name="twitter:image:alt" content="{{ __('register.seo_title') }}">

    {{-- ═══════════════════════════════════════════════════════════════════════════
         FAVICON & ICONS
    ═══════════════════════════════════════════════════════════════════════════ --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/logo/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/logo/favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/logo/apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

    {{-- ═══════════════════════════════════════════════════════════════════════════
         PRECONNECT & FONTS
    ═══════════════════════════════════════════════════════════════════════════ --}}
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" href="{{ asset(config('app.logo')) }}" as="image">

    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Source+Sans+Pro:wght@300;400;600;700&display=swap"
        rel="stylesheet">

    {{-- ═══════════════════════════════════════════════════════════════════════════
         SCHEMA.ORG STRUCTURED DATA - WebPage
    ═══════════════════════════════════════════════════════════════════════════ --}}
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebPage",
        "@id": "{{ url()->current() }}#webpage",
        "url": "{{ url()->current() }}",
        "name": "{{ __('register.seo_title') }} | FlorenceEGI",
        "description": "{{ __('register.seo_description') }}",
        "isPartOf": {
            "@type": "WebSite",
            "@id": "{{ url('/') }}#website",
            "url": "{{ url('/') }}",
            "name": "FlorenceEGI",
            "description": "Piattaforma per la valorizzazione del patrimonio culturale attraverso tecnologie blockchain",
            "publisher": {
                "@type": "Organization",
                "@id": "{{ url('/') }}#organization",
                "name": "Frangette S.r.l.",
                "url": "https://frangette.com/",
                "logo": {
                    "@type": "ImageObject",
                    "url": "{{ asset('images/frangette-logo.png') }}",
                    "width": 200,
                    "height": 60
                }
            }
        },
        "breadcrumb": {
            "@type": "BreadcrumbList",
            "itemListElement": [
                {
                    "@type": "ListItem",
                    "position": 1,
                    "name": "Home",
                    "item": "{{ url('/') }}"
                },
                {
                    "@type": "ListItem",
                    "position": 2,
                    "name": "{{ __('register.seo_title') }}",
                    "item": "{{ url()->current() }}"
                }
            ]
        },
        "mainEntity": {
            "@type": "RegisterAction",
            "name": "{{ __('register.submit_button') }}",
            "description": "{{ __('register.seo_description') }}",
            "target": {
                "@type": "EntryPoint",
                "urlTemplate": "{{ route('register.wizard.step1') }}",
                "actionPlatform": [
                    "http://schema.org/DesktopWebPlatform",
                    "http://schema.org/MobileWebPlatform"
                ]
            }
        },
        "inLanguage": "{{ app()->getLocale() }}",
        "dateModified": "{{ now()->toIso8601String() }}"
    }
    </script>

    {{-- Schema.org - Organization --}}
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "@id": "{{ url('/') }}#organization",
        "name": "FlorenceEGI",
        "alternateName": "Florence EGI - Digital Cultural Heritage Platform",
        "url": "{{ url('/') }}",
        "logo": {
            "@type": "ImageObject",
            "url": "{{ asset('images/logo/logo-full.png') }}",
            "width": 400,
            "height": 100
        },
        "sameAs": [
            "https://twitter.com/FlorenceEGI",
            "https://www.linkedin.com/company/florenceegi"
        ],
        "contactPoint": {
            "@type": "ContactPoint",
            "contactType": "customer support",
            "email": "support@florenceegi.com",
            "availableLanguage": ["Italian", "English", "German", "Spanish", "French", "Portuguese"]
        }
    }
    </script>

    {{-- Schema.org - BreadcrumbList --}}
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [
            {
                "@type": "ListItem",
                "position": 1,
                "name": "Home",
                "item": "{{ url('/') }}"
            },
            {
                "@type": "ListItem",
                "position": 2,
                "name": "{{ __('register.seo_title') }}",
                "item": "{{ url('/join') }}"
            }
            @if(isset($currentStep) && $currentStep > 1)
            ,{
                "@type": "ListItem",
                "position": 3,
                "name": "Step {{ $currentStep }}",
                "item": "{{ url()->current() }}"
            }
            @endif
        ]
    }
    </script>

    {{-- ═══════════════════════════════════════════════════════════════════════════
         CSS ASSETS
    ═══════════════════════════════════════════════════════════════════════════ --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ═══════════════════════════════════════════════════════════════════════════
           DESIGN TOKENS - FlorenceEGI Palette
        ═══════════════════════════════════════════════════════════════════════════ */
        :root {
            --oro-fiorentino: #D4A574;
            --oro-fiorentino-light: #E6C9A8;
            --verde-rinascita: #2D5016;
            --verde-rinascita-light: #4A7A2C;
            --blu-algoritmo: #1B365D;
            --blu-algoritmo-light: #2D5087;
            --grigio-pietra: #6B6B6B;
            --grigio-pietra-light: #9CA3AF;
            --rosso-medici: #C13120;
            --bianco-marmo: #FAFAFA;
            --nero-carbon: #1F2937;
        }

        /* Typography */
        .font-rinascimento {
            font-family: 'Playfair Display', Georgia, serif;
        }

        .font-corpo {
            font-family: 'Source Sans Pro', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        /* ═══════════════════════════════════════════════════════════════════════════
           BACKGROUND & LAYOUT
        ═══════════════════════════════════════════════════════════════════════════ */
        .bg-rinascimento-gradient {
            background: linear-gradient(135deg,
                    #FEFEFE 0%,
                    #F8F5F0 25%,
                    #FAF8F5 50%,
                    #F5F3EF 75%,
                    #FAFAFA 100%);
            background-attachment: fixed;
        }

        /* ═══════════════════════════════════════════════════════════════════════════
           GLASS CARD EFFECT
        ═══════════════════════════════════════════════════════════════════════════ */
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(212, 165, 116, 0.25);
            box-shadow:
                0 10px 40px rgba(0, 0, 0, 0.08),
                0 2px 10px rgba(212, 165, 116, 0.1);
        }

        /* ═══════════════════════════════════════════════════════════════════════════
           BUTTONS - WCAG AA Compliant
        ═══════════════════════════════════════════════════════════════════════════ */
        .btn-primary {
            background: linear-gradient(135deg, var(--oro-fiorentino) 0%, var(--oro-fiorentino-light) 100%);
            color: var(--blu-algoritmo);
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 14px rgba(212, 165, 116, 0.35);
        }

        .btn-primary:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(212, 165, 116, 0.45);
        }

        .btn-primary:focus-visible {
            outline: 3px solid var(--blu-algoritmo);
            outline-offset: 2px;
        }

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .btn-secondary {
            background: #ffffff;
            border: 2px solid var(--blu-algoritmo);
            color: var(--blu-algoritmo);
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-secondary:hover:not(:disabled) {
            background: var(--blu-algoritmo);
            color: #ffffff;
        }

        .btn-secondary:focus-visible {
            outline: 3px solid var(--oro-fiorentino);
            outline-offset: 2px;
        }

        /* ═══════════════════════════════════════════════════════════════════════════
           FORM INPUTS - WCAG AA Compliant (4.5:1 contrast ratio)
        ═══════════════════════════════════════════════════════════════════════════ */
        .input-field {
            border: 2px solid #D1D5DB;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            line-height: 1.5;
            color: var(--nero-carbon);
            background-color: #ffffff;
            transition: all 0.2s ease;
        }

        .input-field:hover:not(:disabled) {
            border-color: var(--grigio-pietra);
        }

        .input-field:focus {
            border-color: var(--oro-fiorentino);
            box-shadow: 0 0 0 3px rgba(212, 165, 116, 0.25);
            outline: none;
        }

        .input-field::placeholder {
            color: var(--grigio-pietra-light);
        }

        .input-field:disabled {
            background-color: #F3F4F6;
            cursor: not-allowed;
        }

        .input-field[aria-invalid="true"] {
            border-color: var(--rosso-medici);
        }

        .input-field[aria-invalid="true"]:focus {
            box-shadow: 0 0 0 3px rgba(193, 49, 32, 0.25);
        }

        /* ═══════════════════════════════════════════════════════════════════════════
           TYPE SELECTION CARDS - Accessible Interactive Elements
        ═══════════════════════════════════════════════════════════════════════════ */
        .type-card {
            background: #ffffff;
            border: 2px solid #E5E7EB;
            border-radius: 16px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }

        .type-card:hover:not(.selected) {
            border-color: var(--oro-fiorentino);
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.1);
        }

        .type-card:focus-within {
            outline: 3px solid var(--blu-algoritmo);
            outline-offset: 2px;
        }

        .type-card.selected {
            border-color: var(--oro-fiorentino);
            background: rgba(212, 165, 116, 0.08);
            box-shadow:
                0 0 0 4px rgba(212, 165, 116, 0.15),
                0 8px 25px rgba(0, 0, 0, 0.08);
        }

        /* ═══════════════════════════════════════════════════════════════════════════
           STEP INDICATOR - Accessible Progress Navigation
        ═══════════════════════════════════════════════════════════════════════════ */
        .step-indicator {
            background: #E5E7EB;
            color: var(--grigio-pietra);
            transition: all 0.3s ease;
        }

        .step-indicator.active {
            background: var(--oro-fiorentino);
            color: var(--blu-algoritmo);
            font-weight: 700;
        }

        .step-indicator.completed {
            background: var(--verde-rinascita);
            color: #ffffff;
        }

        .step-connector {
            height: 3px;
            background: #E5E7EB;
            transition: background 0.3s ease;
        }

        .step-connector.completed {
            background: var(--verde-rinascita);
        }

        .step-label {
            color: var(--grigio-pietra-light);
            font-size: 0.75rem;
        }

        .step-label.active {
            color: var(--blu-algoritmo);
            font-weight: 600;
        }

        .step-label.completed {
            color: var(--verde-rinascita);
        }

        /* ═══════════════════════════════════════════════════════════════════════════
           COLOR UTILITIES
        ═══════════════════════════════════════════════════════════════════════════ */
        .bg-oro-fiorentino {
            background-color: var(--oro-fiorentino);
        }

        .bg-verde-rinascita {
            background-color: var(--verde-rinascita);
        }

        .bg-blu-algoritmo {
            background-color: var(--blu-algoritmo);
        }

        .text-oro-fiorentino {
            color: var(--oro-fiorentino);
        }

        .text-blu-algoritmo {
            color: var(--blu-algoritmo);
        }

        .text-verde-rinascita {
            color: var(--verde-rinascita);
        }

        .text-grigio-pietra {
            color: var(--grigio-pietra);
        }

        .text-rosso-medici {
            color: var(--rosso-medici);
        }

        .border-oro-fiorentino {
            border-color: var(--oro-fiorentino);
        }

        /* ═══════════════════════════════════════════════════════════════════════════
           ANIMATIONS
        ═══════════════════════════════════════════════════════════════════════════ */
        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Reduced motion preference - WCAG 2.1 */
        @media (prefers-reduced-motion: reduce) {

            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }

        /* ═══════════════════════════════════════════════════════════════════════════
           RESPONSIVE ADJUSTMENTS - Mobile First
        ═══════════════════════════════════════════════════════════════════════════ */
        @media (max-width: 640px) {
            .step-indicator {
                width: 32px;
                height: 32px;
                font-size: 0.75rem;
            }

            .step-label {
                display: none;
            }
        }

        /* High contrast mode support - WCAG 2.1 AAA */
        @media (prefers-contrast: high) {
            .glass-effect {
                border: 2px solid #000000;
            }

            .btn-primary {
                border: 2px solid #000000;
            }

            .input-field {
                border-width: 3px;
            }
        }

        /* Print styles */
        @media print {
            .bg-rinascimento-gradient {
                background: #ffffff !important;
            }

            .glass-effect {
                box-shadow: none !important;
                border: 1px solid #000000 !important;
            }
        }
    </style>

    @stack('styles')
</head>

<body class="bg-rinascimento-gradient font-corpo min-h-screen antialiased">
    {{-- Skip to main content link - WCAG 2.1 --}}
    <a href="#main-content"
        class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-blu-algoritmo focus:px-4 focus:py-2 focus:text-white focus:outline-none focus:ring-2 focus:ring-oro-fiorentino">
        {{ __('accessibility.skip_to_content', ['default' => 'Vai al contenuto principale']) }}
    </a>

    <div class="flex min-h-screen flex-col items-center justify-center px-4 py-6 sm:px-6 sm:py-8 lg:px-8">

        {{-- Header with Logo --}}
        <header class="mb-6 text-center sm:mb-8" role="banner">
            <a href="{{ url('/') }}"
                class="inline-flex items-center rounded-lg focus:outline-none focus:ring-2 focus:ring-oro-fiorentino focus:ring-offset-2"
                aria-label="{{ __('register.back_to_home', ['default' => 'Torna alla home di FlorenceEGI']) }}">
                <div class="bg-oro-fiorentino flex h-10 w-10 items-center justify-center rounded-full sm:h-12 sm:w-12"
                    aria-hidden="true">
                    <svg class="h-5 w-5 text-blu-algoritmo sm:h-6 sm:w-6" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <span class="font-rinascimento ml-3 text-xl font-bold text-blu-algoritmo sm:text-2xl">FlorenceEGI</span>
            </a>
        </header>

        {{-- Progress Indicator - ARIA Live Region for Accessibility --}}
        <nav class="mb-6 w-full max-w-sm px-2 sm:mb-8 sm:max-w-md"
            aria-label="{{ __('register.wizard_progress', ['default' => 'Progresso registrazione']) }}">
            {{-- Screen reader announcement --}}
            <div class="sr-only" aria-live="polite" aria-atomic="true">
                {{ __('register.step_of', ['current' => $currentStep, 'total' => $totalSteps, 'default' => "Passo $currentStep di $totalSteps"]) }}
            </div>

            <ol class="flex items-center justify-between" role="list">
                @php
                    $stepLabels = [
                        1 => __('register.user_type_legend'),
                        2 => __('register.privacy_legend'),
                        3 => __('register.form_title'),
                        4 => __('register.submit_button'),
                    ];
                    $stepAriaLabels = [
                        1 => 'Profilo utente',
                        2 => 'Consensi privacy',
                        3 => 'Dati personali',
                        4 => 'Conferma registrazione',
                    ];
                @endphp

                @for ($i = 1; $i <= $totalSteps; $i++)
                    @php
                        $stepStatus = $i < $currentStep ? 'completed' : ($i === $currentStep ? 'active' : 'pending');
                        $stepAriaLabel = $stepAriaLabels[$i] ?? "Passo $i";
                        $stepStatusText =
                            $stepStatus === 'completed'
                                ? 'completato'
                                : ($stepStatus === 'active'
                                    ? 'corrente'
                                    : 'da completare');
                    @endphp

                    <li class="flex flex-col items-center" role="listitem">
                        <div class="step-indicator {{ $stepStatus === 'completed' ? 'completed' : ($stepStatus === 'active' ? 'active' : '') }} flex h-8 w-8 items-center justify-center rounded-full text-xs font-semibold sm:h-10 sm:w-10 sm:text-sm"
                            role="img" aria-label="{{ $stepAriaLabel }} - {{ $stepStatusText }}"
                            @if ($stepStatus === 'active') aria-current="step" @endif>
                            @if ($stepStatus === 'completed')
                                <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="currentColor" viewBox="0 0 20 20"
                                    aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            @else
                                <span aria-hidden="true">{{ $i }}</span>
                            @endif
                        </div>
                        <span class="step-label {{ $stepStatus }} mt-1.5 hidden text-center sm:block"
                            aria-hidden="true">
                            {{ $i === 1 ? 'Profilo' : ($i === 2 ? 'Consensi' : ($i === 3 ? 'Dati' : 'Conferma')) }}
                        </span>
                    </li>

                    @if ($i < $totalSteps)
                        <li class="step-connector {{ $i < $currentStep ? 'completed' : '' }} mx-1 flex-1 sm:mx-2"
                            role="presentation" aria-hidden="true"></li>
                    @endif
                @endfor
            </ol>
        </nav>

        {{-- Main Content Area --}}
        <main id="main-content" role="main" class="w-full max-w-lg px-2 sm:max-w-xl sm:px-0 lg:max-w-2xl"
            aria-labelledby="wizard-step-title">
            <div class="glass-effect fade-in rounded-2xl p-4 shadow-xl sm:p-6 lg:p-8">
                @yield('content')
            </div>
        </main>

        {{-- Footer --}}
        <footer class="mt-6 text-center text-sm text-grigio-pietra sm:mt-8" role="contentinfo">
            <p>
                {{ __('register.already_registered_prompt') }}
                <a href="{{ route('login') }}"
                    class="rounded font-semibold text-blu-algoritmo underline decoration-1 underline-offset-2 hover:text-oro-fiorentino hover:decoration-2 focus:outline-none focus:ring-2 focus:ring-oro-fiorentino focus:ring-offset-2">
                    {{ __('register.login_link') }}
                </a>
            </p>

            {{-- Legal links --}}
            <nav class="mt-4 flex flex-wrap justify-center gap-4 text-xs" aria-label="Link legali">
                <a href="{{ route('gdpr.privacy-policy') }}"
                    class="rounded underline decoration-1 underline-offset-2 hover:text-blu-algoritmo focus:outline-none focus:ring-2 focus:ring-oro-fiorentino focus:ring-offset-2">
                    {{ __('footer.privacy_policy', ['default' => 'Privacy Policy']) }}
                </a>
                <span aria-hidden="true">•</span>
                <a href="{{ route('gdpr.terms') }}"
                    class="rounded underline decoration-1 underline-offset-2 hover:text-blu-algoritmo focus:outline-none focus:ring-2 focus:ring-oro-fiorentino focus:ring-offset-2">
                    {{ __('footer.terms_of_service', ['default' => 'Termini di Servizio']) }}
                </a>
            </nav>

            {{-- Copyright --}}
            <p class="mt-4 text-xs text-grigio-pietra">
                © {{ date('Y') }} FlorenceEGI - Frangette S.r.l. •
                {{ __('footer.all_rights_reserved', ['default' => 'Tutti i diritti riservati']) }}
            </p>
        </footer>
    </div>

    {{-- ARIA Live Regions for Accessibility Announcements --}}
    <div id="error-announcements" class="sr-only" aria-live="assertive" aria-atomic="true" role="alert"></div>
    <div id="success-announcements" class="sr-only" aria-live="polite" aria-atomic="true" role="status"></div>

    @stack('scripts')

    {{-- Accessibility Enhancement Script --}}
    <script>
        (function() {
            'use strict';

            // Announce form errors to screen readers
            document.addEventListener('DOMContentLoaded', function() {
                var errorContainer = document.getElementById('error-announcements');
                var errors = document.querySelectorAll('[role="alert"]');

                if (errors.length > 0 && errorContainer) {
                    var errorMessages = Array.from(errors)
                        .map(function(el) {
                            return el.textContent.trim();
                        })
                        .filter(Boolean)
                        .join('. ');

                    if (errorMessages) {
                        errorContainer.textContent = 'Errori nel modulo: ' + errorMessages;
                    }
                }

                // Focus management for wizard steps
                var mainContent = document.getElementById('main-content');
                if (mainContent) {
                    var firstFocusable = mainContent.querySelector(
                        'button:not([disabled]), [href], input:not([type="hidden"]):not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
                    );

                    if (firstFocusable) {
                        // Small delay to ensure content is rendered
                        setTimeout(function() {
                            firstFocusable.focus();
                        }, 100);
                    }
                }

                // Keyboard navigation enhancement
                document.addEventListener('keydown', function(e) {
                    // Escape key closes any open dialogs
                    if (e.key === 'Escape') {
                        var activeDialog = document.querySelector('[role="dialog"][aria-hidden="false"]');
                        if (activeDialog) {
                            var closeButton = activeDialog.querySelector('[data-dismiss]');
                            if (closeButton) closeButton.click();
                        }
                    }
                });
            });
        })();
    </script>
</body>

</html>
