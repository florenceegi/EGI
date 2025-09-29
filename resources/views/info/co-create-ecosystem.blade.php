{{--
    File: co-create-ecosystem.blade.php
    Versione: 1.0 FlorenceEGI Co-Creation Ecosystem
    Data: 29 Settembre 2025
    Descrizione: Pagina unificata Co-Creare, Co-Creatore, Trader Pro
    Caratteristiche: Brand Guidelines, sezioni strutturate, navbar info standard
--}}
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('info_co_create.meta.title') }}</title>
    <meta name="description" content="{{ __('info_co_create.meta.description') }}">

    <!-- SEO Meta Tags -->
    <meta name="keywords" content="{{ __('info_co_create.meta.keywords') }}">
    <meta name="author" content="{{ __('info_co_create.meta.author') }}">
    <meta name="robots" content="index, follow, max-image-preview:large">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph Protocol (Facebook, LinkedIn) -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ __('info_co_create.meta.og_title') }}">
    <meta property="og:description" content="{{ __('info_co_create.meta.og_description') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="FlorenceEGI">
    <meta property="og:locale" content="it_IT">
    <meta property="og:image" content="{{ asset('images/co-create-ecosystem-social.jpg') }}">
    <meta property="og:image:alt" content="{{ __('info_co_create.meta.og_image_alt') }}">

    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ __('info_co_create.meta.twitter_title') }}">
    <meta name="twitter:description" content="{{ __('info_co_create.meta.twitter_description') }}">
    <meta name="twitter:image" content="{{ asset('images/co-create-ecosystem-social.jpg') }}">
    <meta name="twitter:image:alt" content="{{ __('info_co_create.meta.twitter_image_alt') }}">

    <!-- Multilingual Support -->
    <link rel="alternate" hreflang="it" href="{{ url()->current() }}">
    <link rel="alternate" hreflang="en" href="{{ str_replace('/it/', '/en/', url()->current()) }}">
    <link rel="alternate" hreflang="es" href="{{ str_replace('/it/', '/es/', url()->current()) }}">
    <link rel="alternate" hreflang="pt" href="{{ str_replace('/it/', '/pt/', url()->current()) }}">
    <link rel="alternate" hreflang="fr" href="{{ str_replace('/it/', '/fr/', url()->current()) }}">
    <link rel="alternate" hreflang="de" href="{{ str_replace('/it/', '/de/', url()->current()) }}">

    <!-- Google Fonts - Brand Guidelines -->
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Source+Sans+Pro:wght@300;400;600&display=swap"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Brand Colors e Configurazione -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'oro-fiorentino': '#D4A574',
                        'verde-rinascita': '#2D5016',
                        'blu-algoritmo': '#1B365D',
                        'grigio-pietra': '#6B6B6B',
                        'rosso-urgenza': '#C13120',
                        'arancio-energia': '#E67E22',
                        'viola-innovazione': '#8E44AD'
                    },
                    fontFamily: {
                        'renaissance': ['Playfair Display', 'serif'],
                        'body': ['Source Sans Pro', 'sans-serif']
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Source Sans Pro', sans-serif;
            overflow-x: hidden;
        }

        .renaissance-title {
            font-family: 'Playfair Display', serif;
        }

        /* Layout Rinascimentale - Sezione Aurea */
        .golden-ratio-container {
            max-width: 1618px;
            margin: 0 auto;
        }

        /* Animazioni eleganti */
        .elegant-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .elegant-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        /* CTA Oro Fiorentino */
        .cta-primary {
            background: linear-gradient(135deg, #D4A574 0%, #B8956A 100%);
            box-shadow: 0 4px 15px rgba(212, 165, 116, 0.3);
        }

        .cta-primary:hover {
            box-shadow: 0 6px 20px rgba(212, 165, 116, 0.4);
            transform: translateY(-1px);
        }

        /* Hero Background */
        .hero-background {
            background: linear-gradient(135deg, rgba(27, 54, 93, 0.95) 0%, rgba(45, 80, 22, 0.85) 100%);
            min-height: 50vh;
        }

        /* Cards eleganti */
        .renaissance-card {
            background: linear-gradient(145deg, #ffffff 0%, #fafafa 100%);
            border: 1px solid rgba(212, 165, 116, 0.2);
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .renaissance-card:hover {
            border-color: #D4A574;
            box-shadow: 0 8px 30px rgba(212, 165, 116, 0.15);
        }

        /* Sezioni alternate */
        .section-alt {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        /* Co-Create gradient */
        .co-create-gradient {
            background: linear-gradient(135deg, #8E44AD 0%, #9B59B6 100%);
        }

        /* Co-Creator gradient */
        .co-creator-gradient {
            background: linear-gradient(135deg, #E67E22 0%, #F39C12 100%);
        }

        /* Trader Pro gradient */
        .trader-gradient {
            background: linear-gradient(135deg, #C13120 0%, #E74C3C 100%);
        }

        /* Professional highlight boxes */
        .highlight-box {
            background: rgba(212, 165, 116, 0.1);
            border-left: 4px solid #D4A574;
            padding: 1.5rem;
            margin: 1rem 0;
            border-radius: 0 8px 8px 0;
        }

        /* Section Navigation Styles */
        .section-nav-link,
        .section-nav-link-mobile {
            color: #6B6B6B;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .section-nav-link:hover {
            transform: translateY(-1px);
        }

        /* Scrollbar nascosta per desktop */
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        /* Desktop section nav - scroll orizzontale fluido */
        #desktop-section-nav {
            scroll-behavior: smooth;
        }

        /* Mobile sections menu animation */
        #mobile-sections-menu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-in-out;
        }

        #mobile-sections-menu.show {
            max-height: 300px;
        }

        /* Smooth scroll behavior */
        html {
            scroll-behavior: smooth;
        }

        /* Section spacing for fixed header + sticky navbar */
        section[id] {
            scroll-margin-top: 140px;
            /* Fixed header (80px) + sticky nav (60px) */
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            section[id] {
                scroll-margin-top: 160px;
                /* More space for mobile */
            }
        }
    </style>

    <!-- Schema.org Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@graph": [
            {
                "@type": "Organization",
                "@id": "{{ url('/') }}#organization",
                "name": "{{ __('info_co_create.schema.website_name') }}",
                "url": "{{ url('/') }}",
                "logo": {
                    "@type": "ImageObject",
                    "url": "{{ asset('images/logo-florence-egi.png') }}",
                    "width": 200,
                    "height": 200
                },
                "description": "{{ __('info_co_create.schema.organization_description') }}",
                "foundingDate": "2024",
                "sameAs": [
                    "https://www.linkedin.com/company/florence-egi",
                    "https://twitter.com/florence_egi"
                ]
            },
            {
                "@type": "WebSite",
                "@id": "{{ url('/') }}#website",
                "url": "{{ url('/') }}",
                "name": "{{ __('info_co_create.schema.website_name') }}",
                "publisher": {
                    "@id": "{{ url('/') }}#organization"
                },
                "inLanguage": "it-IT"
            },
            {
                "@type": "WebPage",
                "@id": "{{ url()->current() }}#webpage",
                "url": "{{ url()->current() }}",
                "name": "{{ __('info_co_create.schema.webpage_name') }}",
                "description": "{{ __('info_co_create.schema.webpage_description') }}",
                "inLanguage": "it-IT",
                "isPartOf": {
                    "@id": "{{ url('/') }}#website"
                },
                "about": [
                    {
                        "@type": "Thing",
                        "name": "{{ __('info_co_create.schema.about_cocreation') }}"
                    },
                    {
                        "@type": "Thing",
                        "name": "{{ __('info_co_create.schema.about_trading') }}"
                    },
                    {
                        "@type": "Thing",
                        "name": "{{ __('info_co_create.schema.about_community') }}"
                    },
                    {
                        "@type": "Thing",
                        "name": "{{ __('info_co_create.schema.about_blockchain') }}"
                    }
                ]
            },
            {
                "@type": "Service",
                "name": "{{ __('info_co_create.schema.service_name') }}",
                "description": "{{ __('info_co_create.schema.service_description') }}",
                "provider": {
                    "@id": "{{ url('/') }}#organization"
                },
                "serviceType": "{{ __('info_co_create.schema.service_type') }}",
                "hasOfferCatalog": {
                    "@type": "OfferCatalog",
                    "name": "{{ __('info_co_create.schema.catalog_name') }}",
                    "itemListElement": [
                        {
                            "@type": "Service",
                            "name": "{{ __('info_co_create.schema.service_cocreate_name') }}",
                            "description": "{{ __('info_co_create.schema.service_cocreate_description') }}"
                        },
                        {
                            "@type": "Service",
                            "name": "{{ __('info_co_create.schema.service_cocreator_name') }}",
                            "description": "{{ __('info_co_create.schema.service_cocreator_description') }}"
                        },
                        {
                            "@type": "Service",
                            "name": "{{ __('info_co_create.schema.service_trader_name') }}",
                            "description": "{{ __('info_co_create.schema.service_trader_description') }}"
                        }
                    ]
                }
            },
            {
                "@type": "CreativeWork",
                "name": "{{ __('info_co_create.schema.creative_work_name') }}",
                "description": "{{ __('info_co_create.schema.creative_work_description') }}",
                "creator": {
                    "@id": "{{ url('/') }}#organization"
                },
                "genre": "{{ __('info_co_create.schema.creative_work_genre') }}",
                "audience": {
                    "@type": "Audience",
                    "name": "{{ __('info_co_create.schema.audience_name') }}"
                }
            }
        ]
    }
    </script>
</head>

<body class="bg-gray-50 pt-20 text-grigio-pietra">

    <!-- Header con Navigazione - Fixed -->
    <header class="fixed left-0 right-0 top-0 z-50 bg-blu-algoritmo text-white shadow-lg">
        <div class="golden-ratio-container px-4 py-4 sm:px-6 sm:py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3 sm:space-x-4">
                    <i class="fas fa-infinity text-oro-fiorentino text-3xl sm:text-4xl"></i>
                    <div>
                        <h1 class="renaissance-title text-xl font-bold sm:text-2xl">
                            {{ __('info_co_create.header.brand_title') }}</h1>
                        <p class="font-body text-sm text-blue-200 sm:text-base">
                            {{ __('info_co_create.header.brand_subtitle') }}</p>
                    </div>
                </div>

                <!-- Desktop Navigation -->
                <nav class="hidden space-x-3 md:flex" aria-label="Navigazione principale FlorenceEGI">
                    <a href="{{ route('home') }}"
                        class="hover:text-oro-fiorentino font-body text-sm transition lg:text-base">{{ __('info_co_create.header.nav_home') }}</a>
                    <a href="{{ route('info.florence-egi') }}"
                        class="hover:text-oro-fiorentino font-body text-sm transition lg:text-base">{{ __('info_co_create.header.nav_florence_egi') }}</a>
                    <a href="{{ route('info.egi') }}"
                        class="hover:text-oro-fiorentino font-body text-sm transition lg:text-base">{{ __('info_co_create.header.nav_egi') }}</a>
                    <a href="{{ route('info.epp') }}"
                        class="hover:text-oro-fiorentino font-body text-sm transition lg:text-base">{{ __('info_co_create.header.nav_epp') }}</a>
                    <a href="{{ route('archetypes.patron') }}"
                        class="hover:text-oro-fiorentino font-body text-sm transition lg:text-base">{{ __('info_co_create.header.nav_patron') }}</a>
                    <a href="{{ route('gdpr.privacy-policy') }}"
                        class="hover:text-oro-fiorentino font-body text-sm transition lg:text-base">{{ __('info_co_create.header.nav_privacy') }}</a>
                </nav>

                <!-- Mobile Menu Button -->
                <button id="mobile-menu-button"
                    class="block rounded-md p-2 transition-colors hover:bg-blue-700 md:hidden">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>

            <!-- Mobile Navigation Menu -->
            <div id="mobile-menu" class="mt-4 hidden border-t border-blue-600 pb-4 md:hidden">
                <div class="space-y-3 pt-4">
                    <a href="{{ route('home') }}"
                        class="flex items-center rounded-md px-4 py-2 text-sm transition-colors hover:bg-blue-700">
                        <i class="fas fa-home text-oro-fiorentino mr-3 text-lg"></i>
                        {{ __('info_co_create.header.nav_home') }}
                    </a>
                    <a href="{{ route('info.florence-egi') }}"
                        class="flex items-center rounded-md px-4 py-2 text-sm transition-colors hover:bg-blue-700">
                        <i class="fas fa-infinity text-oro-fiorentino mr-3 text-lg"></i>
                        {{ __('info_co_create.header.nav_florence_egi') }}
                    </a>
                    <a href="{{ route('info.egi') }}"
                        class="flex items-center rounded-md px-4 py-2 text-sm transition-colors hover:bg-blue-700">
                        <i class="fas fa-gem text-oro-fiorentino mr-3 text-lg"></i>
                        {{ __('info_co_create.header.nav_egi') }}
                    </a>
                    <a href="{{ route('info.epp') }}"
                        class="flex items-center rounded-md px-4 py-2 text-sm transition-colors hover:bg-blue-700">
                        <i class="fas fa-leaf text-oro-fiorentino mr-3 text-lg"></i>
                        {{ __('info_co_create.header.nav_epp') }}
                    </a>
                    <a href="{{ route('archetypes.patron') }}"
                        class="flex items-center rounded-md px-4 py-2 text-sm transition-colors hover:bg-blue-700">
                        <i class="fas fa-crown text-oro-fiorentino mr-3 text-lg"></i>
                        {{ __('info_co_create.header.nav_patron') }}
                    </a>
                    <a href="{{ route('gdpr.privacy-policy') }}"
                        class="flex items-center rounded-md px-4 py-2 text-sm transition-colors hover:bg-blue-700">
                        <i class="fas fa-shield-alt text-oro-fiorentino mr-3 text-lg"></i>
                        {{ __('info_co_create.header.nav_privacy') }}
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero-background">
        <div class="golden-ratio-container flex min-h-[50vh] items-center justify-center px-4">
            <div class="mx-auto max-w-4xl text-center text-white">
                <h1 class="renaissance-title mb-6 text-4xl font-bold sm:text-5xl lg:text-6xl">
                    {{ __('info_co_create.hero.title') }}
                </h1>
                <p class="mb-8 font-body text-lg leading-relaxed text-blue-100 sm:text-xl">
                    {{ __('info_co_create.hero.subtitle') }}
                </p>
                <div class="flex flex-wrap justify-center gap-4 font-body text-sm">
                    <div class="flex items-center rounded-full bg-white bg-opacity-20 px-4 py-2">
                        <i class="fas fa-palette text-oro-fiorentino mr-2"></i>
                        {{ __('info_co_create.hero.badge_cocreate') }}
                    </div>
                    <div class="flex items-center rounded-full bg-white bg-opacity-20 px-4 py-2">
                        <i class="fas fa-users text-oro-fiorentino mr-2"></i>
                        {{ __('info_co_create.hero.badge_cocreator') }}
                    </div>
                    <div class="flex items-center rounded-full bg-white bg-opacity-20 px-4 py-2">
                        <i class="fas fa-chart-line text-oro-fiorentino mr-2"></i>
                        {{ __('info_co_create.hero.badge_trader') }}
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Navbar Interna delle Sezioni - Sticky & Responsive -->
    <nav class="sticky top-20 z-40 border-b border-gray-200 bg-white shadow-sm"
        aria-label="Navigazione sezioni Ecosistema Co-Creazione">
        <div class="golden-ratio-container px-4 sm:px-6 lg:px-8">

            <!-- Desktop: Scroll orizzontale -->
            <div class="hidden py-4 md:block">
                <div class="flex justify-center">
                    <div class="scrollbar-hide flex max-w-full space-x-2 overflow-x-auto px-4"
                        id="desktop-section-nav">

                        <!-- Co-Creare -->
                        <a href="#co-creare"
                            class="section-nav-link flex-shrink-0 rounded-lg border border-transparent px-4 py-2 text-sm font-medium transition-all duration-200 hover:border-purple-200 hover:bg-purple-50 hover:text-purple-700"
                            data-section="co-creare">
                            <i class="fas fa-palette mr-2 text-purple-600"></i>
                            {{ __('info_co_create.navigation.cocreate_nav') }}
                        </a>

                        <!-- Co-Creatore -->
                        <a href="#co-creatore"
                            class="section-nav-link flex-shrink-0 rounded-lg border border-transparent px-4 py-2 text-sm font-medium transition-all duration-200 hover:border-orange-200 hover:bg-orange-50 hover:text-orange-700"
                            data-section="co-creatore">
                            <i class="fas fa-users mr-2 text-orange-600"></i>
                            {{ __('info_co_create.navigation.cocreator_nav') }}
                        </a>

                        <!-- Trader Pro -->
                        <a href="#trader-pro"
                            class="section-nav-link flex-shrink-0 rounded-lg border border-transparent px-4 py-2 text-sm font-medium transition-all duration-200 hover:border-red-200 hover:bg-red-50 hover:text-red-700"
                            data-section="trader-pro">
                            <i class="fas fa-chart-line mr-2 text-red-600"></i>
                            {{ __('info_co_create.navigation.trader_nav') }}
                        </a>

                    </div>
                </div>
            </div>

            <!-- Mobile: Menu Hamburger -->
            <div class="block py-3 md:hidden">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-list text-oro-fiorentino mr-2 text-lg"></i>
                        <span
                            class="text-sm font-medium text-grigio-pietra">{{ __('info_co_create.navigation.sections_title') }}</span>
                    </div>
                    <button id="mobile-sections-toggle"
                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-100 transition-colors hover:bg-gray-200">
                        <i class="fas fa-chevron-down text-sm text-grigio-pietra" id="mobile-sections-icon"></i>
                    </button>
                </div>

                <!-- Mobile Dropdown Menu -->
                <div id="mobile-sections-menu" class="mt-3 hidden space-y-1">

                    <!-- Co-Creare -->
                    <a href="#co-creare"
                        class="section-nav-link-mobile flex items-center rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 hover:bg-purple-50 hover:text-purple-700"
                        data-section="co-creare">
                        <i class="fas fa-palette mr-3 text-purple-600"></i>
                        {{ __('info_co_create.navigation.cocreate_nav') }}
                    </a>

                    <!-- Co-Creatore -->
                    <a href="#co-creatore"
                        class="section-nav-link-mobile flex items-center rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 hover:bg-orange-50 hover:text-orange-700"
                        data-section="co-creatore">
                        <i class="fas fa-users mr-3 text-orange-600"></i>
                        {{ __('info_co_create.navigation.cocreator_nav') }}
                    </a>

                    <!-- Trader Pro -->
                    <a href="#trader-pro"
                        class="section-nav-link-mobile flex items-center rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 hover:bg-red-50 hover:text-red-700"
                        data-section="trader-pro">
                        <i class="fas fa-chart-line mr-3 text-red-600"></i>
                        {{ __('info_co_create.navigation.trader_nav') }}
                    </a>

                </div>
            </div>

        </div>
    </nav>

    <!-- Sezione Co-Creare -->
    <section id="co-creare" class="py-16" aria-label="{{ __('info_co_create.cocreate.aria_label') }}">
        <div class="golden-ratio-container px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-6xl">

                <!-- Header Sezione -->
                <div class="mb-12 text-center">
                    <div
                        class="co-create-gradient mb-6 inline-flex h-20 w-20 items-center justify-center rounded-full text-white">
                        <i class="fas fa-palette text-3xl"></i>
                    </div>
                    <h2 class="renaissance-title mb-4 text-3xl font-bold text-blu-algoritmo sm:text-4xl">
                        {{ __('info_co_create.cocreate.title') }}
                    </h2>
                    <p class="font-body text-lg text-grigio-pietra">
                        {{ __('info_co_create.cocreate.subtitle') }}
                    </p>
                </div>

                <!-- Contenuto Co-Creare -->
                <div class="grid gap-8 lg:grid-cols-2">
                    <div class="renaissance-card elegant-hover p-8">
                        <h3 class="renaissance-title mb-4 text-xl font-semibold text-blu-algoritmo">
                            {{ __('info_co_create.cocreate.shared_creation_title') }}
                        </h3>
                        <p class="mb-4 font-body leading-relaxed text-grigio-pietra">
                            {!! __('info_co_create.cocreate.shared_creation_content') !!}
                        </p>
                        <div class="highlight-box">
                            <p class="font-semibold text-blu-algoritmo">
                                <i class="fas fa-lightbulb text-oro-fiorentino mr-2"></i>
                                {{ __('info_co_create.cocreate.shared_creation_highlight') }}
                            </p>
                        </div>
                    </div>

                    <div class="renaissance-card elegant-hover p-8">
                        <h3 class="renaissance-title mb-4 text-xl font-semibold text-blu-algoritmo">
                            {{ __('info_co_create.cocreate.private_to_public_title') }}
                        </h3>
                        <p class="mb-4 font-body leading-relaxed text-grigio-pietra">
                            {!! __('info_co_create.cocreate.private_to_public_content') !!}
                        </p>
                        <ul class="space-y-2 font-body text-sm text-grigio-pietra">
                            <li class="flex items-center">
                                <i class="fas fa-check-circle mr-2 text-verde-rinascita"></i>
                                {{ __('info_co_create.cocreate.benefit_active_participation') }}
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check-circle mr-2 text-verde-rinascita"></i>
                                {{ __('info_co_create.cocreate.benefit_artist_visibility') }}
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check-circle mr-2 text-verde-rinascita"></i>
                                {{ __('info_co_create.cocreate.benefit_epp_support') }}
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="renaissance-card mt-8 p-8 text-center">
                    <h3 class="renaissance-title mb-4 text-2xl font-bold text-blu-algoritmo">
                        {{ __('info_co_create.cocreate.unique_opportunity_title') }}
                    </h3>
                    <p class="mx-auto max-w-3xl font-body text-lg leading-relaxed text-grigio-pietra">
                        {!! __('info_co_create.cocreate.unique_opportunity_content') !!}
                    </p>
                </div>

            </div>
        </div>
    </section>

    <!-- Sezione Co-Creatore -->
    <section id="co-creatore" class="section-alt py-16"
        aria-label="{{ __('info_co_create.cocreator.aria_label') }}">
        <div class="golden-ratio-container px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-6xl">

                <!-- Header Sezione -->
                <div class="mb-12 text-center">
                    <div
                        class="co-creator-gradient mb-6 inline-flex h-20 w-20 items-center justify-center rounded-full text-white">
                        <i class="fas fa-users text-3xl"></i>
                    </div>
                    <h2 class="renaissance-title mb-4 text-3xl font-bold text-blu-algoritmo sm:text-4xl">
                        {{ __('info_co_create.cocreator.title') }}
                    </h2>
                    <p class="font-body text-lg text-grigio-pietra">
                        {{ __('info_co_create.cocreator.subtitle') }}
                    </p>
                </div>

                <!-- Contenuto Co-Creatore -->
                <div class="grid gap-8 lg:grid-cols-3">
                    <div class="renaissance-card elegant-hover p-6">
                        <div class="mb-4">
                            <i class="fas fa-infinity text-oro-fiorentino text-3xl"></i>
                        </div>
                        <h3 class="renaissance-title mb-3 text-lg font-semibold text-blu-algoritmo">
                            {{ __('info_co_create.cocreator.permanent_bond_title') }}
                        </h3>
                        <p class="font-body text-grigio-pietra">
                            {!! __('info_co_create.cocreator.permanent_bond_content') !!}
                        </p>
                    </div>

                    <div class="renaissance-card elegant-hover p-6">
                        <div class="mb-4">
                            <i class="fas fa-globe text-oro-fiorentino text-3xl"></i>
                        </div>
                        <h3 class="renaissance-title mb-3 text-lg font-semibold text-blu-algoritmo">
                            {{ __('info_co_create.cocreator.global_visibility_title') }}
                        </h3>
                        <p class="font-body text-grigio-pietra">
                            {!! __('info_co_create.cocreator.global_visibility_content') !!}
                        </p>
                    </div>

                    <div class="renaissance-card elegant-hover p-6">
                        <div class="mb-4">
                            <i class="fas fa-user-edit text-oro-fiorentino text-3xl"></i>
                        </div>
                        <h3 class="renaissance-title mb-3 text-lg font-semibold text-blu-algoritmo">
                            {{ __('info_co_create.cocreator.personalized_identity_title') }}
                        </h3>
                        <p class="font-body text-grigio-pietra">
                            {!! __('info_co_create.cocreator.personalized_identity_content') !!}
                        </p>
                    </div>
                </div>

                <div class="renaissance-card mt-8 p-8">
                    <h3 class="renaissance-title mb-6 text-center text-2xl font-bold text-blu-algoritmo">
                        {{ __('info_co_create.cocreator.profile_title') }}
                    </h3>
                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <h4 class="mb-3 text-lg font-semibold text-blu-algoritmo">
                                {{ __('info_co_create.cocreator.customization_title') }}</h4>
                            <ul class="space-y-2 font-body text-grigio-pietra">
                                <li class="flex items-center">
                                    <i class="fas fa-tag mr-3 text-verde-rinascita"></i>
                                    <span>{!! __('info_co_create.cocreator.custom_nickname') !!}</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-id-card mr-3 text-verde-rinascita"></i>
                                    <span>{!! __('info_co_create.cocreator.real_name') !!}</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-camera mr-3 text-verde-rinascita"></i>
                                    <span>{!! __('info_co_create.cocreator.profile_image') !!}</span>
                                </li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="mb-3 text-lg font-semibold text-blu-algoritmo">
                                {{ __('info_co_create.cocreator.exclusive_benefits_title') }}</h4>
                            <ul class="space-y-2 font-body text-grigio-pietra">
                                <li class="flex items-center">
                                    <i class="text-oro-fiorentino fas fa-crown mr-3"></i>
                                    <span>{!! __('info_co_create.cocreator.perpetual_recognition') !!}</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="text-oro-fiorentino fas fa-trophy mr-3"></i>
                                    <span>{!! __('info_co_create.cocreator.exclusive_status') !!}</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="text-oro-fiorentino fas fa-network-wired mr-3"></i>
                                    <span>{!! __('info_co_create.cocreator.global_visibility_benefit') !!}</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="from-oro-fiorentino mt-6 rounded-lg bg-gradient-to-r to-yellow-400 p-6 text-center">
                        <p class="text-lg font-semibold text-white">
                            <i class="fas fa-star mr-2"></i>
                            {!! __('info_co_create.cocreator.cofounder_message') !!}
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Sezione Trader Pro -->
    <section id="trader-pro" class="py-16" aria-label="{{ __('info_co_create.trader.aria_label') }}">
        <div class="golden-ratio-container px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-6xl">

                <!-- Header Sezione -->
                <div class="mb-12 text-center">
                    <div
                        class="trader-gradient mb-6 inline-flex h-20 w-20 items-center justify-center rounded-full text-white">
                        <i class="fas fa-chart-line text-3xl"></i>
                    </div>
                    <h2 class="renaissance-title mb-4 text-3xl font-bold text-blu-algoritmo sm:text-4xl">
                        {{ __('info_co_create.trader.title') }}
                    </h2>
                    <p class="font-body text-lg text-grigio-pietra">
                        {{ __('info_co_create.trader.subtitle') }}
                    </p>
                </div>

                <!-- La Strategia Duplice -->
                <div class="renaissance-card mb-8 p-8">
                    <h3 class="renaissance-title mb-6 text-center text-2xl font-bold text-blu-algoritmo">
                        {{ __('info_co_create.trader.strategic_vision_title') }}
                    </h3>
                    <div class="grid gap-8 lg:grid-cols-2">
                        <div class="border-l-4 border-verde-rinascita bg-green-50 p-6">
                            <h4 class="mb-3 text-lg font-semibold text-verde-rinascita">
                                <i class="fas fa-tools mr-2"></i>
                                {{ __('info_co_create.trader.for_traders_title') }}
                            </h4>
                            <p class="font-body text-grigio-pietra">
                                {!! __('info_co_create.trader.for_traders_content') !!}
                            </p>
                        </div>
                        <div class="border-l-4 border-arancio-energia bg-orange-50 p-6">
                            <h4 class="mb-3 text-lg font-semibold text-arancio-energia">
                                <i class="fas fa-rocket mr-2"></i>
                                {{ __('info_co_create.trader.for_creators_title') }}
                            </h4>
                            <p class="font-body text-grigio-pietra">
                                {!! __('info_co_create.trader.for_creators_content') !!}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Sistema EGI PT -->
                <div class="mb-8">
                    <h3 class="renaissance-title mb-6 text-center text-2xl font-bold text-blu-algoritmo">
                        {{ __('info_co_create.trader.egi_pt_system_title') }}
                    </h3>

                    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                        <div class="renaissance-card elegant-hover p-6 text-center">
                            <div class="mb-4">
                                <i class="fas fa-clone text-4xl text-viola-innovazione"></i>
                            </div>
                            <h4 class="mb-3 text-lg font-semibold text-blu-algoritmo">
                                {{ __('info_co_create.trader.max_5_title') }}</h4>
                            <p class="font-body text-sm text-grigio-pietra">
                                {!! __('info_co_create.trader.max_5_content') !!}
                            </p>
                        </div>

                        <div class="renaissance-card elegant-hover p-6 text-center">
                            <div class="mb-4">
                                <i class="fas fa-fingerprint text-4xl text-viola-innovazione"></i>
                            </div>
                            <h4 class="mb-3 text-lg font-semibold text-blu-algoritmo">
                                {{ __('info_co_create.trader.unique_numbering_title') }}</h4>
                            <p class="font-body text-sm text-grigio-pietra">
                                {!! __('info_co_create.trader.unique_numbering_content') !!}
                            </p>
                        </div>

                        <div class="renaissance-card elegant-hover p-6 text-center">
                            <div class="mb-4">
                                <i class="fas fa-paper-plane text-4xl text-viola-innovazione"></i>
                            </div>
                            <h4 class="mb-3 text-lg font-semibold text-blu-algoritmo">
                                {{ __('info_co_create.trader.digital_emissaries_title') }}</h4>
                            <p class="font-body text-sm text-grigio-pietra">
                                {!! __('info_co_create.trader.digital_emissaries_content') !!}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Vantaggi per Trader -->
                <div class="renaissance-card mb-8 p-8">
                    <h3 class="renaissance-title mb-6 text-xl font-bold text-blu-algoritmo">
                        <i class="text-oro-fiorentino fas fa-chart-line mr-3"></i>
                        {{ __('info_co_create.trader.trader_benefits_title') }}
                    </h3>
                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <h4 class="mb-3 text-lg font-semibold text-verde-rinascita">
                                {{ __('info_co_create.trader.guaranteed_rarity_title') }}</h4>
                            <p class="mb-4 font-body text-grigio-pietra">
                                {!! __('info_co_create.trader.guaranteed_rarity_content') !!}
                            </p>
                            <div class="highlight-box">
                                <p class="text-sm font-semibold text-blu-algoritmo">
                                    {{ __('info_co_create.trader.guaranteed_rarity_highlight') }}
                                </p>
                            </div>
                        </div>
                        <div>
                            <h4 class="mb-3 text-lg font-semibold text-verde-rinascita">
                                {{ __('info_co_create.trader.real_art_title') }}</h4>
                            <p class="mb-4 font-body text-grigio-pietra">
                                {!! __('info_co_create.trader.real_art_content') !!}
                            </p>
                            <ul class="space-y-1 text-sm text-grigio-pietra">
                                <li><i
                                        class="fas fa-check mr-2 text-verde-rinascita"></i>{{ __('info_co_create.trader.physical_link') }}
                                </li>
                                <li><i
                                        class="fas fa-check mr-2 text-verde-rinascita"></i>{{ __('info_co_create.trader.real_artistic_value') }}
                                </li>
                                <li><i
                                        class="fas fa-check mr-2 text-verde-rinascita"></i>{{ __('info_co_create.trader.guaranteed_collectibility') }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Struttura Economica -->
                <div class="renaissance-card mb-8 bg-gradient-to-br from-blue-50 to-green-50 p-8">
                    <h3 class="renaissance-title mb-6 text-xl font-bold text-blu-algoritmo">
                        <i class="text-oro-fiorentino fas fa-calculator mr-3"></i>
                        {{ __('info_co_create.trader.economic_structure_title') }}
                    </h3>
                    <div class="grid gap-6 lg:grid-cols-3">
                        <div class="rounded-lg bg-white p-4 shadow-sm">
                            <h4 class="mb-2 text-lg font-semibold text-rosso-urgenza">
                                {{ __('info_co_create.trader.zero_creator_fee_title') }}</h4>
                            <p class="text-sm text-grigio-pietra">
                                {!! __('info_co_create.trader.zero_creator_fee_content') !!}
                            </p>
                        </div>
                        <div class="rounded-lg bg-white p-4 shadow-sm">
                            <h4 class="mb-2 text-lg font-semibold text-rosso-urgenza">
                                {{ __('info_co_create.trader.zero_platform_fee_title') }}</h4>
                            <p class="text-sm text-grigio-pietra">
                                {!! __('info_co_create.trader.zero_platform_fee_content') !!}
                            </p>
                        </div>
                        <div class="rounded-lg bg-white p-4 shadow-sm">
                            <h4 class="mb-2 text-lg font-semibold text-verde-rinascita">
                                {{ __('info_co_create.trader.epp_fee_title') }}</h4>
                            <p class="text-sm text-grigio-pietra">
                                {!! __('info_co_create.trader.epp_fee_content') !!}
                            </p>
                        </div>
                    </div>
                    <div class="mt-6 rounded-lg bg-gradient-to-r from-verde-rinascita to-green-600 p-4 text-center">
                        <p class="font-semibold text-white">
                            <i class="fas fa-trophy mr-2"></i>
                            {{ __('info_co_create.trader.profitability_message') }}
                        </p>
                    </div>
                </div>

                <!-- Impatto Ecosistema -->
                <div class="renaissance-card p-8">
                    <h3 class="renaissance-title mb-6 text-center text-2xl font-bold text-blu-algoritmo">
                        {{ __('info_co_create.trader.ecosystem_impact_title') }}
                    </h3>
                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <h4 class="mb-3 text-lg font-semibold text-arancio-energia">
                                {{ __('info_co_create.trader.volumes_visibility_title') }}</h4>
                            <p class="mb-4 font-body text-grigio-pietra">
                                {!! __('info_co_create.trader.volumes_visibility_content') !!}
                            </p>
                        </div>
                        <div>
                            <h4 class="mb-3 text-lg font-semibold text-arancio-energia">
                                {{ __('info_co_create.trader.return_for_all_title') }}</h4>
                            <p class="mb-4 font-body text-grigio-pietra">
                                {!! __('info_co_create.trader.return_for_all_content') !!}
                            </p>
                        </div>
                    </div>
                    <div class="from-oro-fiorentino mt-6 rounded-lg bg-gradient-to-r to-yellow-400 p-6 text-center">
                        <p class="text-lg font-semibold text-white">
                            <i class="fas fa-infinity mr-2"></i>
                            {{ __('info_co_create.trader.synergy_message') }}
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    @include('components.info-footer')

    <!-- Mobile Menu & Section Navigation Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile Menu Logic
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            const menuIcon = mobileMenuButton.querySelector('i');

            mobileMenuButton.addEventListener('click', function() {
                if (mobileMenu.classList.contains('hidden')) {
                    mobileMenu.classList.remove('hidden');
                    menuIcon.className = 'fas fa-times text-2xl';
                } else {
                    mobileMenu.classList.add('hidden');
                    menuIcon.className = 'fas fa-bars text-2xl';
                }
            });

            // Close menu when clicking on a link
            const mobileMenuLinks = mobileMenu.querySelectorAll('a');
            mobileMenuLinks.forEach(link => {
                link.addEventListener('click', function() {
                    mobileMenu.classList.add('hidden');
                    menuIcon.className = 'fas fa-bars text-2xl';
                });
            });

            // Close menu when clicking outside
            document.addEventListener('click', function(event) {
                if (!mobileMenuButton.contains(event.target) && !mobileMenu.contains(event.target)) {
                    mobileMenu.classList.add('hidden');
                    menuIcon.className = 'fas fa-bars text-2xl';
                }
            });

            // Mobile Sections Menu Logic
            const mobileSectionsToggle = document.getElementById('mobile-sections-toggle');
            const mobileSectionsMenu = document.getElementById('mobile-sections-menu');
            const mobileSectionsIcon = document.getElementById('mobile-sections-icon');

            if (mobileSectionsToggle) {
                mobileSectionsToggle.addEventListener('click', function() {
                    if (mobileSectionsMenu.classList.contains('hidden')) {
                        mobileSectionsMenu.classList.remove('hidden');
                        mobileSectionsMenu.classList.add('show');
                        mobileSectionsIcon.className = 'fas fa-chevron-up text-sm text-grigio-pietra';
                    } else {
                        mobileSectionsMenu.classList.add('hidden');
                        mobileSectionsMenu.classList.remove('show');
                        mobileSectionsIcon.className = 'fas fa-chevron-down text-sm text-grigio-pietra';
                    }
                });
            }

            // Section Navigation Logic (Desktop + Mobile)
            const sectionNavLinks = document.querySelectorAll('.section-nav-link, .section-nav-link-mobile');
            const sections = document.querySelectorAll('section[id]');

            // Smooth scrolling for section navigation
            sectionNavLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href').substring(1);
                    const targetSection = document.getElementById(targetId);

                    if (targetSection) {
                        // Calculate offset for fixed header + sticky navbar
                        const headerHeight = document.querySelector('header.fixed').offsetHeight;
                        const navbarHeight = document.querySelector('nav.sticky').offsetHeight;
                        const extraOffset = window.innerWidth < 768 ? 20 : 10;
                        const totalOffset = headerHeight + navbarHeight + extraOffset;
                        const targetPosition = targetSection.offsetTop - totalOffset;

                        window.scrollTo({
                            top: targetPosition,
                            behavior: 'smooth'
                        });

                        // Close mobile menu after click
                        if (window.innerWidth < 768 && mobileSectionsMenu) {
                            mobileSectionsMenu.classList.add('hidden');
                            mobileSectionsMenu.classList.remove('show');
                            mobileSectionsIcon.className =
                                'fas fa-chevron-down text-sm text-grigio-pietra';
                        }
                    }
                });
            });

            // Highlight active section on scroll
            function highlightActiveSection() {
                const headerHeight = document.querySelector('header.fixed').offsetHeight;
                const navbarHeight = document.querySelector('nav.sticky').offsetHeight;
                const scrollPosition = window.scrollY + headerHeight + navbarHeight + 50;

                sections.forEach(section => {
                    const sectionTop = section.offsetTop;
                    const sectionBottom = sectionTop + section.offsetHeight;
                    const sectionId = section.getAttribute('id');

                    // Get both desktop and mobile links
                    const desktopLink = document.querySelector(
                        `.section-nav-link[data-section="${sectionId}"]`);
                    const mobileLink = document.querySelector(
                        `.section-nav-link-mobile[data-section="${sectionId}"]`);

                    if (scrollPosition >= sectionTop && scrollPosition < sectionBottom) {
                        // Remove active class from all links
                        sectionNavLinks.forEach(link => {
                            link.classList.remove('bg-oro-fiorentino', 'text-white',
                                'border-oro-fiorentino');
                            link.classList.add('text-grigio-pietra');
                        });

                        // Add active class to current links (both desktop and mobile)
                        [desktopLink, mobileLink].forEach(link => {
                            if (link) {
                                link.classList.add('bg-oro-fiorentino', 'text-white',
                                    'border-oro-fiorentino');
                                link.classList.remove('text-grigio-pietra');
                            }
                        });
                    }
                });
            }

            // Close mobile menu when clicking outside
            document.addEventListener('click', function(event) {
                if (mobileSectionsToggle && mobileSectionsMenu &&
                    !mobileSectionsToggle.contains(event.target) &&
                    !mobileSectionsMenu.contains(event.target)) {
                    mobileSectionsMenu.classList.add('hidden');
                    mobileSectionsMenu.classList.remove('show');
                    mobileSectionsIcon.className = 'fas fa-chevron-down text-sm text-grigio-pietra';
                }
            });

            // Run on scroll with throttling
            let scrollTimeout;
            window.addEventListener('scroll', function() {
                if (scrollTimeout) {
                    clearTimeout(scrollTimeout);
                }
                scrollTimeout = setTimeout(highlightActiveSection, 10);
            });

            // Initial highlight on page load
            setTimeout(highlightActiveSection, 100);
        });
    </script>

</body>

</html>
