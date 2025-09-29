{{--
    File: epp-info.blade.php
    Versione: 1.0 EPP Istituzionale
    Data: 29 Settembre 2025
    Descrizione: Pagina istituzionale Environment Protection Programs con stile PA e colori impatto ambientale
    Caratteristiche: Layout formale, credibilità istituzionale, focus conservazione ambientale
--}}
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('info_epp.meta.title') }}</title>
    <meta name="description" content="{{ __('info_epp.meta.description') }}">

    <!-- SEO Meta Tags -->
    <meta name="keywords" content="{{ __('info_epp.meta.keywords') }}">
    <meta name="author" content="FlorenceEGI">
    <meta name="robots" content="index, follow, max-image-preview:large">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph Protocol (Facebook, LinkedIn) -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ __('info_epp.meta.og_title') }}">
    <meta property="og:description" content="{{ __('info_epp.meta.og_description') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="FlorenceEGI">
    <meta property="og:locale" content="it_IT">
    <meta property="og:image" content="{{ asset('images/epp-programs-social.jpg') }}">
    <meta property="og:image:alt" content="{{ __('info_epp.meta.og_image_alt') }}">

    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ __('info_epp.meta.twitter_title') }}">
    <meta name="twitter:description" content="{{ __('info_epp.meta.twitter_description') }}">
    <meta name="twitter:image" content="{{ asset('images/epp-programs-social.jpg') }}">
    <meta name="twitter:image:alt" content="{{ __('info_epp.meta.twitter_image_alt') }}">

    <!-- Multilingual Support -->
    <link rel="alternate" hreflang="it" href="{{ url()->current() }}">
    <link rel="alternate" hreflang="en" href="{{ str_replace('/it/', '/en/', url()->current()) }}">
    <link rel="alternate" hreflang="es" href="{{ str_replace('/it/', '/es/', url()->current()) }}">
    <link rel="alternate" hreflang="pt" href="{{ str_replace('/it/', '/pt/', url()->current()) }}">
    <link rel="alternate" hreflang="fr" href="{{ str_replace('/it/', '/fr/', url()->current()) }}">
    <link rel="alternate" hreflang="de" href="{{ str_replace('/it/', '/de/', url()->current()) }}">

    <!-- Google Fonts - Professional -->
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;700&display=swap"
        rel="stylesheet">

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Environmental Impact Colors -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'forest-green': '#1B5E20',
                        'forest-light': '#2E7D32',
                        'ocean-blue': '#0277BD',
                        'ocean-light': '#0288D1',
                        'bee-amber': '#FF8F00',
                        'bee-light': '#FFA000',
                        'earth-brown': '#5D4037',
                        'institutional-navy': '#1A237E',
                        'conservation-teal': '#00695C',
                        'ecosystem-gray': '#455A64',
                        'impact-orange': '#E65100'
                    },
                    fontFamily: {
                        'institutional': ['Inter', 'sans-serif'],
                        'heading': ['Playfair Display', 'serif']
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
        }

        .institutional-title {
            font-family: 'Playfair Display', serif;
        }

        /* Professional Layout */
        .institutional-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Professional Cards */
        .institutional-card {
            background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid rgba(203, 213, 225, 0.5);
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .institutional-card:hover {
            border-color: rgba(26, 35, 126, 0.3);
            box-shadow: 0 10px 40px rgba(26, 35, 126, 0.1);
        }

        /* Environmental Impact Sections */
        .forest-section {
            background: linear-gradient(135deg, rgba(27, 94, 32, 0.05) 0%, rgba(46, 125, 50, 0.05) 100%);
            border-left: 4px solid #1B5E20;
        }

        .ocean-section {
            background: linear-gradient(135deg, rgba(2, 119, 189, 0.05) 0%, rgba(2, 136, 209, 0.05) 100%);
            border-left: 4px solid #0277BD;
        }

        .bee-section {
            background: linear-gradient(135deg, rgba(255, 143, 0, 0.05) 0%, rgba(255, 160, 0, 0.05) 100%);
            border-left: 4px solid #FF8F00;
        }

        /* Hero Environmental Background */
        .hero-environmental {
            background: linear-gradient(135deg, rgba(26, 35, 126, 0.9) 0%, rgba(0, 105, 92, 0.85) 100%),
                linear-gradient(45deg, rgba(27, 94, 32, 0.3) 0%, rgba(2, 119, 189, 0.3) 100%);
            min-height: 70vh;
        }

        /* Statistics Cards */
        .stat-card {
            background: linear-gradient(135deg, #ffffff 0%, #f1f5f9 100%);
            border: 2px solid transparent;
            background-clip: padding-box;
            position: relative;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: 8px;
            padding: 2px;
            background: linear-gradient(135deg, #1B5E20, #0277BD, #FF8F00);
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: exclude;
            mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            mask-composite: exclude;
        }

        /* Impact Metrics */
        .impact-metric {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #1B5E20 0%, #0277BD 50%, #FF8F00 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
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
                "name": "FlorenceEGI",
                "url": "{{ url('/') }}",
                "logo": {
                    "@type": "ImageObject",
                    "url": "{{ asset('images/logo-florence-egi.png') }}",
                    "width": 200,
                    "height": 200
                },
                "description": "{{ __('info_epp.schema.organization_description') }}",
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
                "name": "FlorenceEGI",
                "publisher": {
                    "@id": "{{ url('/') }}#organization"
                },
                "inLanguage": "it-IT"
            },
            {
                "@type": "WebPage",
                "@id": "{{ url()->current() }}#webpage",
                "url": "{{ url()->current() }}",
                "name": "{{ __('info_epp.schema.webpage_name') }}",
                "description": "{{ __('info_epp.schema.webpage_description') }}",
                "inLanguage": "it-IT",
                "isPartOf": {
                    "@id": "{{ url('/') }}#website"
                },
                "about": [
                    {
                        "@type": "Thing",
                        "name": "{{ __('info_epp.schema.about_environmental_protection') }}"
                    },
                    {
                        "@type": "Thing",
                        "name": "{{ __('info_epp.schema.about_reforestation') }}"
                    },
                    {
                        "@type": "Thing",
                        "name": "{{ __('info_epp.schema.about_marine_conservation') }}"
                    },
                    {
                        "@type": "Thing",
                        "name": "{{ __('info_epp.schema.about_bee_biodiversity') }}"
                    }
                ]
            },
            {
                "@type": "GovernmentService",
                "name": "{{ __('info_epp.schema.service_name') }}",
                "description": "{{ __('info_epp.schema.service_description') }}",
                "provider": {
                    "@id": "{{ url('/') }}#organization"
                },
                "serviceType": "Environmental Protection",
                "areaServed": {
                    "@type": "Place",
                    "name": "{{ __('info_epp.schema.service_area') }}"
                },
                "hasOfferCatalog": {
                    "@type": "OfferCatalog",
                    "name": "{{ __('info_epp.schema.catalog_name') }}",
                    "itemListElement": [
                        {
                            "@type": "Service",
                            "name": "{{ __('info_epp.schema.arf_service_name') }}",
                            "description": "{{ __('info_epp.schema.arf_service_description') }}"
                        },
                        {
                            "@type": "Service",
                            "name": "{{ __('info_epp.schema.apr_service_name') }}",
                            "description": "{{ __('info_epp.schema.apr_service_description') }}"
                        },
                        {
                            "@type": "Service",
                            "name": "{{ __('info_epp.schema.bpe_service_name') }}",
                            "description": "{{ __('info_epp.schema.bpe_service_description') }}"
                        }
                    ]
                }
            }
        ]
    }
    </script>
</head>

<body class="pt-20 text-gray-900 bg-gray-50">

    <!-- Header Istituzionale - Fixed -->
    <header class="fixed top-0 left-0 right-0 z-50 text-white shadow-lg bg-institutional-navy">
        <div class="px-4 py-4 institutional-container sm:px-6 sm:py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3 sm:space-x-4">
                    <div
                        class="flex items-center justify-center w-12 h-12 text-white rounded-full bg-conservation-teal sm:h-16 sm:w-16">
                        <i class="text-2xl fas fa-leaf sm:text-3xl"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold institutional-title sm:text-2xl">{{ __('info_epp.header.title') }}
                        </h1>
                        <p class="text-sm text-blue-200 font-institutional sm:text-base">
                            {{ __('info_epp.header.subtitle') }}
                        </p>
                    </div>
                </div>

                <!-- Desktop Navigation -->
                <nav class="hidden space-x-4 md:flex" aria-label="{{ __('info_epp.header.nav_aria_label') }}">
                    <a href="{{ route('home') }}"
                        class="text-sm transition font-institutional hover:text-blue-200 lg:text-base">{{ __('info_epp.header.nav_home') }}</a>
                    <a href="#programmi"
                        class="text-sm transition font-institutional hover:text-blue-200 lg:text-base">{{ __('info_epp.header.nav_programs') }}</a>
                    <a href="#impatto"
                        class="text-sm transition font-institutional hover:text-blue-200 lg:text-base">{{ __('info_epp.header.nav_impact') }}</a>
                    <a href="#iniziative"
                        class="text-sm transition font-institutional hover:text-blue-200 lg:text-base">{{ __('info_epp.header.nav_initiatives') }}</a>
                    <a href="#partecipazione"
                        class="text-sm transition font-institutional hover:text-blue-200 lg:text-base">{{ __('info_epp.header.nav_participation') }}</a>
                </nav>

                <!-- Mobile Menu Button -->
                <button id="mobile-menu-button"
                    class="block p-2 transition-colors rounded-md hover:bg-blue-700 md:hidden">
                    <i class="text-2xl fas fa-bars"></i>
                </button>
            </div>

            <!-- Mobile Navigation Menu -->
            <div id="mobile-menu" class="hidden pb-4 mt-4 border-t border-blue-600 md:hidden">
                <div class="pt-4 space-y-3">
                    <a href="{{ route('home') }}"
                        class="flex items-center px-4 py-2 text-sm transition-colors rounded-md hover:bg-blue-700">
                        <i class="mr-3 text-lg fas fa-home text-conservation-teal"></i>
                        {{ __('info_epp.header.mobile_home') }}
                    </a>
                    <a href="#programmi"
                        class="flex items-center px-4 py-2 text-sm transition-colors rounded-md hover:bg-blue-700">
                        <i class="mr-3 text-lg fas fa-tree text-conservation-teal"></i>
                        {{ __('info_epp.header.mobile_programs_label') }}
                    </a>
                    <a href="#impatto"
                        class="flex items-center px-4 py-2 text-sm transition-colors rounded-md hover:bg-blue-700">
                        <i class="mr-3 text-lg fas fa-chart-line text-conservation-teal"></i>
                        {{ __('info_epp.header.mobile_impact_label') }}
                    </a>
                    <a href="#iniziative"
                        class="flex items-center px-4 py-2 text-sm transition-colors rounded-md hover:bg-blue-700">
                        <i class="mr-3 text-lg fas fa-lightbulb text-conservation-teal"></i>
                        {{ __('info_epp.header.mobile_initiatives_label') }}
                    </a>
                    <a href="#partecipazione"
                        class="flex items-center px-4 py-2 text-sm transition-colors rounded-md hover:bg-blue-700">
                        <i class="mr-3 text-lg fas fa-handshake text-conservation-teal"></i>
                        {{ __('info_epp.header.mobile_participation_label') }}
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero-environmental">
        <div class="institutional-container flex min-h-[70vh] items-center justify-center px-4">
            <div class="max-w-5xl mx-auto text-center text-white">
                <div class="mb-8">
                    <div
                        class="inline-flex items-center px-6 py-3 mb-6 text-sm font-medium text-white bg-white border border-white rounded-full border-opacity-30 bg-opacity-10 backdrop-blur-sm">
                        <i class="mr-2 fas fa-globe-americas"></i>
                        {{ __('info_epp.hero.badge_text') }}
                    </div>
                </div>
                <h1 class="mb-8 text-4xl font-bold institutional-title sm:text-5xl lg:text-6xl">
                    {{ __('info_epp.hero.title_line1') }}<br>
                    <span
                        class="text-transparent bg-gradient-to-r from-green-300 via-blue-300 to-yellow-300 bg-clip-text">
                        {{ __('info_epp.hero.title_line2') }}
                    </span>
                </h1>
                <p class="mb-10 text-lg leading-relaxed text-blue-100 font-institutional sm:text-xl lg:text-2xl">
                    {!! __('info_epp.hero.description') !!}
                </p>

                <!-- Impact Statistics -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <div class="p-6 rounded-lg stat-card">
                        <div class="impact-metric">{{ __('info_epp.hero.stats.areas.number') }}</div>
                        <p class="text-sm font-medium tracking-wide text-gray-600 uppercase">
                            {{ __('info_epp.hero.stats.areas.label') }}</p>
                    </div>
                    <div class="p-6 rounded-lg stat-card">
                        <div class="impact-metric">{{ __('info_epp.hero.stats.impact.number') }}</div>
                        <p class="text-sm font-medium tracking-wide text-gray-600 uppercase">
                            {{ __('info_epp.hero.stats.impact.label') }}</p>
                    </div>
                    <div class="p-6 rounded-lg stat-card">
                        <div class="impact-metric">{{ __('info_epp.hero.stats.contribution.number') }}</div>
                        <p class="text-sm font-medium tracking-wide text-gray-600 uppercase">
                            {{ __('info_epp.hero.stats.contribution.label') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Le Nostre Tre Aree di Intervento -->
    <section id="programmi" class="py-20" aria-label="{{ __('info_epp.programs.aria_label') }}">
        <div class="px-4 institutional-container sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto mb-16 text-center">
                <h2 class="mb-6 text-3xl font-bold text-institutional-navy institutional-title sm:text-4xl">
                    {{ __('info_epp.programs.section_title') }}
                </h2>
                <p class="text-lg leading-relaxed text-ecosystem-gray font-institutional">
                    {{ __('info_epp.programs.section_description') }}
                </p>
            </div>

            <div class="grid gap-8 lg:grid-cols-3">

                <!-- Appropriate Restoration Forestry -->
                <div class="institutional-card forest-section">
                    <div class="p-8">
                        <div class="flex items-center mb-6">
                            <div
                                class="flex items-center justify-center w-16 h-16 text-white rounded-full bg-forest-green">
                                <i class="text-2xl fas fa-tree"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-xl font-bold text-forest-green institutional-title">
                                    {{ __('info_epp.programs.arf.title') }}
                                </h3>
                                <p class="text-sm text-gray-600">{{ __('info_epp.programs.arf.subtitle') }}</p>
                            </div>
                        </div>
                        <p class="mb-6 leading-relaxed text-gray-700 font-institutional">
                            {{ __('info_epp.programs.arf.description') }}
                        </p>
                        <div class="p-4 mb-6 bg-white border rounded-lg border-forest-green border-opacity-20">
                            <h4 class="mb-2 font-semibold text-forest-green">
                                {{ __('info_epp.programs.arf.objectives_title') }}</h4>
                            <ul class="space-y-1 text-sm text-gray-600">
                                @foreach (__('info_epp.programs.arf.objectives') as $objective)
                                    <li><i class="mr-2 text-forest-green fas fa-check"></i>{{ $objective }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <a href="#forestry-details" aria-label="{{ __('info_epp.programs.arf.cta_aria_label') }}"
                            class="inline-flex items-center px-6 py-3 text-sm font-medium text-white transition-all duration-300 rounded-lg bg-forest-green hover:bg-forest-light">
                            {{ __('info_epp.programs.arf.cta_text') }}
                            <i class="ml-2 fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Aquatic Plastic Removal -->
                <div class="institutional-card ocean-section">
                    <div class="p-8">
                        <div class="flex items-center mb-6">
                            <div
                                class="flex items-center justify-center w-16 h-16 text-white rounded-full bg-ocean-blue">
                                <i class="text-2xl fas fa-water"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-xl font-bold text-ocean-blue institutional-title">
                                    {{ __('info_epp.programs.apr.title') }}
                                </h3>
                                <p class="text-sm text-gray-600">{{ __('info_epp.programs.apr.subtitle') }}</p>
                            </div>
                        </div>
                        <p class="mb-6 leading-relaxed text-gray-700 font-institutional">
                            {{ __('info_epp.programs.apr.description') }}
                        </p>
                        <div class="p-4 mb-6 bg-white border rounded-lg border-ocean-blue border-opacity-20">
                            <h4 class="mb-2 font-semibold text-ocean-blue">
                                {{ __('info_epp.programs.apr.strategy_title') }}</h4>
                            <ul class="space-y-1 text-sm text-gray-600">
                                @foreach (__('info_epp.programs.apr.strategy') as $strategy)
                                    <li><i class="mr-2 text-ocean-blue fas fa-check"></i>{{ $strategy }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <a href="#plastic-details" aria-label="{{ __('info_epp.programs.apr.cta_aria_label') }}"
                            class="inline-flex items-center px-6 py-3 text-sm font-medium text-white transition-all duration-300 rounded-lg bg-ocean-blue hover:bg-ocean-light">
                            {{ __('info_epp.programs.apr.cta_text') }}
                            <i class="ml-2 fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Bee Population Enhancement -->
                <div class="institutional-card bee-section">
                    <div class="p-8">
                        <div class="flex items-center mb-6">
                            <div
                                class="flex items-center justify-center w-16 h-16 text-white rounded-full bg-bee-amber">
                                <i class="text-2xl fas fa-spa"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-xl font-bold text-bee-amber institutional-title">
                                    {{ __('info_epp.programs.bpe.title') }}
                                </h3>
                                <p class="text-sm text-gray-600">{{ __('info_epp.programs.bpe.subtitle') }}</p>
                            </div>
                        </div>
                        <p class="mb-6 leading-relaxed text-gray-700 font-institutional">
                            {{ __('info_epp.programs.bpe.description') }}
                        </p>
                        <div class="p-4 mb-6 bg-white border rounded-lg border-bee-amber border-opacity-20">
                            <h4 class="mb-2 font-semibold text-bee-amber">
                                {{ __('info_epp.programs.bpe.initiatives_title') }}</h4>
                            <ul class="space-y-1 text-sm text-gray-600">
                                @foreach (__('info_epp.programs.bpe.initiatives') as $initiative)
                                    <li><i class="mr-2 text-bee-amber fas fa-check"></i>{{ $initiative }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <a href="#bee-details" aria-label="{{ __('info_epp.programs.bpe.cta_aria_label') }}"
                            class="inline-flex items-center px-6 py-3 text-sm font-medium text-white transition-all duration-300 rounded-lg bg-bee-amber hover:bg-bee-light">
                            {{ __('info_epp.programs.bpe.cta_text') }}
                            <i class="ml-2 fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Sezione Impatto Dettagliato -->
    <section id="impatto" class="py-20 bg-gray-100" aria-label="{{ __('info_epp.impact.aria_label') }}">
        <div class="px-4 institutional-container sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto mb-16 text-center">
                <h2 class="mb-6 text-3xl font-bold text-institutional-navy institutional-title sm:text-4xl">
                    {{ __('info_epp.impact.section_title') }}
                </h2>
                <p class="text-lg leading-relaxed text-ecosystem-gray font-institutional">
                    {{ __('info_epp.impact.section_description') }}
                </p>
            </div>

            <!-- Forestry Details -->
            <div id="forestry-details" class="mb-16">
                <div class="p-8 bg-white shadow-lg institutional-card rounded-xl">
                    <div class="grid gap-8 lg:grid-cols-2">
                        <div>
                            <div class="flex items-center mb-6">
                                <div class="w-3 h-12 mr-4 rounded bg-forest-green"></div>
                                <h3 class="text-2xl font-bold text-forest-green institutional-title">
                                    {{ __('info_epp.impact.forestry_details.title') }}
                                </h3>
                            </div>
                            <div class="space-y-4 text-gray-700 font-institutional">
                                <p>
                                    {!! __('info_epp.impact.forestry_details.paragraph1') !!}
                                </p>
                                <p>
                                    {!! __('info_epp.impact.forestry_details.paragraph2') !!}
                                </p>
                                <p>
                                    {{ __('info_epp.impact.forestry_details.paragraph3') }}
                                </p>
                            </div>
                        </div>
                        <div>
                            <div class="p-6 rounded bg-forest-green bg-opacity-5">
                                <p class="text-gray-700 font-institutional">
                                    {!! __('info_epp.impact.forestry_details.highlight1') !!}
                                </p>
                                <br>
                                <p class="text-gray-700 font-institutional">
                                    {!! __('info_epp.impact.forestry_details.highlight2') !!}
                                    che favoriscano la salute a lungo termine delle foreste.
                                </p>
                                <br>
                                <p class="text-gray-700 font-institutional">
                                    Con questo programma, ci impegniamo a creare un futuro in cui le foreste possano
                                    essere fonte di vita, diversità e sostenibilità per le generazioni a venire.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Plastic Removal Details -->
            <div id="plastic-details" class="mb-16">
                <div class="p-8 bg-white shadow-lg institutional-card rounded-xl">
                    <div class="grid gap-8 lg:grid-cols-2">
                        <div>
                            <div class="flex items-center mb-6">
                                <div class="w-3 h-12 mr-4 rounded bg-ocean-blue"></div>
                                <h3 class="text-2xl font-bold text-ocean-blue institutional-title">
                                    {{ __('info_epp.impact.plastic_details.title') }}
                                </h3>
                            </div>
                            <div class="space-y-4 text-gray-700 font-institutional">
                                <p>
                                    {!! __('info_epp.impact.plastic_details.paragraph1') !!}
                                </p>
                                <p>
                                    {!! __('info_epp.impact.plastic_details.paragraph2') !!}
                                </p>
                                <p>
                                    {{ __('info_epp.impact.plastic_details.paragraph3') }}
                                </p>
                            </div>
                        </div>
                        <div>
                            <div class="p-6 rounded bg-ocean-blue bg-opacity-5">
                                <p class="text-gray-700 font-institutional">
                                    {!! __('info_epp.impact.plastic_details.highlight1') !!}
                                </p>
                                <br>
                                <p class="text-gray-700 font-institutional">
                                    {!! __('info_epp.impact.plastic_details.highlight2') !!}
                                </p>
                                <br>
                                <p class="text-gray-700 font-institutional">
                                    {!! __('info_epp.impact.plastic_details.highlight3') !!}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bee Enhancement Details -->
            <div id="bee-details">
                <div class="p-8 bg-white shadow-lg institutional-card rounded-xl">
                    <div class="grid gap-8 lg:grid-cols-2">
                        <div>
                            <div class="flex items-center mb-6">
                                <div class="w-3 h-12 mr-4 rounded bg-bee-amber"></div>
                                <h3 class="text-2xl font-bold text-bee-amber institutional-title">
                                    {{ __('info_epp.impact.bee_details.title') }}
                                </h3>
                            </div>
                            <div class="space-y-4 text-gray-700 font-institutional">
                                <p>
                                    {!! __('info_epp.impact.bee_details.paragraph1') !!}
                                </p>
                                <p>
                                    {!! __('info_epp.impact.bee_details.paragraph2') !!}
                                </p>
                            </div>
                        </div>
                        <div>
                            <div class="p-6 rounded bg-bee-amber bg-opacity-5">
                                <p class="text-gray-700 font-institutional">
                                    {!! __('info_epp.impact.bee_details.highlight1') !!}
                                </p>
                                <br>
                                <p class="text-gray-700 font-institutional">
                                    {!! __('info_epp.impact.bee_details.highlight2') !!}
                                </p>
                                <br>
                                <p class="text-gray-700 font-institutional">
                                    {!! __('info_epp.impact.bee_details.highlight3') !!}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- Come Partecipare -->
    <section id="partecipazione" class="py-20" aria-label="{{ __('info_epp.participation.aria_label') }}">
        <div class="px-4 institutional-container sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto">
                <div class="p-8 text-center bg-white shadow-lg institutional-card rounded-xl">
                    <div class="mb-8">
                        <div
                            class="flex items-center justify-center w-20 h-20 mx-auto mb-6 text-white rounded-full bg-conservation-teal">
                            <i class="text-3xl fas fa-handshake"></i>
                        </div>
                        <h2 class="mb-4 text-3xl font-bold text-institutional-navy institutional-title">
                            {{ __('info_epp.participation.section_title') }}
                        </h2>
                        <p class="text-lg text-ecosystem-gray font-institutional">
                            {!! __('info_epp.participation.section_description') !!}
                        </p>
                    </div>

                    <div class="grid gap-6 mb-8 md:grid-cols-3">
                        <div class="p-6 border border-gray-200 rounded-lg">
                            <i class="mb-4 text-3xl text-forest-green fas fa-plus-circle"></i>
                            <h3 class="mb-2 font-semibold text-institutional-navy">
                                {{ __('info_epp.participation.steps.create.title') }}</h3>
                            <p class="text-sm text-gray-600 font-institutional">
                                {{ __('info_epp.participation.steps.create.description') }}
                            </p>
                        </div>
                        <div class="p-6 border border-gray-200 rounded-lg">
                            <i class="mb-4 text-3xl text-ocean-blue fas fa-heart"></i>
                            <h3 class="mb-2 font-semibold text-institutional-navy">
                                {{ __('info_epp.participation.steps.choose.title') }}</h3>
                            <p class="text-sm text-gray-600 font-institutional">
                                {{ __('info_epp.participation.steps.choose.description') }}
                            </p>
                        </div>
                        <div class="p-6 border border-gray-200 rounded-lg">
                            <i class="mb-4 text-3xl text-bee-amber fas fa-chart-line"></i>
                            <h3 class="mb-2 font-semibold text-institutional-navy">
                                {{ __('info_epp.participation.steps.monitor.title') }}</h3>
                            <p class="text-sm text-gray-600 font-institutional">
                                {{ __('info_epp.participation.steps.monitor.description') }}
                            </p>
                        </div>
                    </div>

                    <div class="space-y-4 sm:flex sm:justify-center sm:space-x-4 sm:space-y-0">
                        <a href="{{ route('home') }}"
                            aria-label="{{ __('info_epp.participation.cta.start_now_aria_label') }}"
                            class="inline-flex items-center px-8 py-4 text-white transition-all duration-300 rounded-lg bg-conservation-teal hover:bg-opacity-90">
                            <i class="mr-2 fas fa-rocket"></i>
                            {{ __('info_epp.participation.cta.start_now') }}
                        </a>
                        <a href="{{ route('info.florence-egi') }}"
                            aria-label="{{ __('info_epp.participation.cta.discover_platform_aria_label') }}"
                            class="inline-flex items-center px-8 py-4 transition-all duration-300 border-2 rounded-lg text-conservation-teal border-conservation-teal hover:bg-conservation-teal hover:text-white">
                            <i class="mr-2 fas fa-info-circle"></i>
                            {{ __('info_epp.participation.cta.discover_platform') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('components.info-footer')

    <!-- Mobile Menu Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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

            // Smooth scrolling per i link interni
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
        });
    </script>

</body>

</html>
