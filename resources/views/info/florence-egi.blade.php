{{--
    File: florence-egi.blade.php
    Versione: 1.1 FlorenceEGI Istituzionale (Localized)
    Data: 28 Settembre 2025
    Descrizione: Pagina istituzionale localizzata con chiavi di traduzione. Connessa a /resources/lang/[locale]/info_florence_egi.php
    Caratteristiche: Brand Guidelines, CoA Integration, Vision tecnica completa, i18n Ready
--}}
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('info_florence_egi.meta_title') }}</title>
    <meta name="description" content="{{ __('info_florence_egi.meta_description') }}">
    <meta name="keywords" content="{{ __('info_florence_egi.meta_keywords') }}">
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large">
    <meta name="author" content="{{ __('info_florence_egi.meta_author') }}">
    <meta name="language" content="{{ app()->getLocale() }}">
    <link rel="canonical" href="{{ url()->current() }}">

    <meta property="og:title" content="{{ __('info_florence_egi.og_title') }}">
    <meta property="og:description" content="{{ __('info_florence_egi.og_description') }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="{{ __('info_florence_egi.og_site_name') }}">
    <meta property="og:locale" content="{{ str_replace('_', '-', app()->getLocale()) }}">
    <meta property="og:image" content="{{ asset('images/og/florence-egi-social.jpg') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="{{ __('info_florence_egi.og_image_alt') }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ __('info_florence_egi.twitter_title') }}">
    <meta name="twitter:description" content="{{ __('info_florence_egi.twitter_description') }}">
    <meta name="twitter:site" content="@FlorenceEGI">
    <meta name="twitter:creator" content="@FlorenceEGI">
    <meta name="twitter:image" content="{{ asset('images/twitter/florence-egi-twitter.jpg') }}">
    <meta name="twitter:image:alt" content="{{ __('info_florence_egi.twitter_image_alt') }}">

    <meta name="theme-color" content="#D4A574">
    <meta name="apple-mobile-web-app-title" content="{{ __('info_florence_egi.meta_app_title') }}">
    <meta name="application-name" content="{{ __('info_florence_egi.meta_app_name') }}">
    <meta name="msapplication-TileColor" content="#D4A574">

    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Source+Sans+Pro:wght@300;400;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
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

        .golden-ratio-container {
            max-width: 1618px;
            margin: 0 auto;
        }

        .elegant-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .elegant-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .cta-primary {
            background: linear-gradient(135deg, #D4A574 0%, #B8956A 100%);
            box-shadow: 0 4px 15px rgba(212, 165, 116, 0.3);
        }

        .cta-primary:hover {
            box-shadow: 0 6px 20px rgba(212, 165, 116, 0.4);
            transform: translateY(-1px);
        }

        .hero-background {
            background: linear-gradient(135deg, rgba(27, 54, 93, 0.95) 0%, rgba(45, 80, 22, 0.85) 100%),
                url('{{ asset('images/default/patron_banner_background_rinascimento_1.png') }}') no-repeat center center/cover;
            min-height: 70vh;
        }

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

        .section-dark {
            background: linear-gradient(135deg, #1B365D 0%, #2D5016 100%);
        }

        .code-block {
            background: #1a1a1a;
            border-left: 4px solid #D4A574;
            font-family: 'Monaco', 'Menlo', monospace;
            font-size: 0.85rem;
        }

        .trilemma-point {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            text-align: center;
            font-size: 0.9rem;
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
                "name": "{{ __('info_florence_egi.schema.organization.name') }}",
                "url": "{{ url('/') }}",
                "logo": {
                    "@type": "ImageObject",
                    "url": "{{ asset('images/logo-florence-egi.png') }}",
                    "width": 200,
                    "height": 200
                },
                "description": "{{ __('info_florence_egi.schema.organization.description') }}",
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
                "name": "{{ __('info_florence_egi.schema.website.name') }}",
                "publisher": {
                    "@id": "{{ url('/') }}#organization"
                },
                "inLanguage": "{{ str_replace('_', '-', app()->getLocale()) }}"
            },
            {
                "@type": "TechArticle",
                "@id": "{{ url()->current() }}#webpage",
                "url": "{{ url()->current() }}",
                "name": "{{ __('info_florence_egi.schema.article.name') }}",
                "headline": "{{ __('info_florence_egi.schema.article.headline') }}",
                "description": "{{ __('info_florence_egi.schema.article.description') }}",
                "inLanguage": "{{ str_replace('_', '-', app()->getLocale()) }}",
                "isPartOf": {
                    "@id": "{{ url('/') }}#website"
                },
                "author": {
                    "@id": "{{ url('/') }}#organization"
                },
                "publisher": {
                    "@id": "{{ url('/') }}#organization"
                },
                "datePublished": "2024-09-29",
                "dateModified": "2025-09-29",
                "about": [
                    {
                        "@type": "Thing",
                        "name": "{{ __('info_florence_egi.schema.topics.blockchain_art') }}"
                    },
                    {
                        "@type": "Thing",
                        "name": "{{ __('info_florence_egi.schema.topics.nft_marketplace') }}"
                    },
                    {
                        "@type": "Thing",
                        "name": "{{ __('info_florence_egi.schema.topics.environmental_impact') }}"
                    },
                    {
                        "@type": "Thing",
                        "name": "{{ __('info_florence_egi.schema.topics.algorand_blockchain') }}"
                    }
                ]
            }
        ]
    }
    </script>
</head>

<body class="bg-gray-50 pt-20 text-grigio-pietra">

    <header class="fixed left-0 right-0 top-0 z-50 bg-blu-algoritmo text-white shadow-lg">
        <div class="golden-ratio-container px-4 py-4 sm:px-6 sm:py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3 sm:space-x-4">
                    <i class="fas fa-infinity text-oro-fiorentino text-3xl sm:text-4xl"></i>
                    <div>
                        <h1 class="renaissance-title text-xl font-bold sm:text-2xl">
                            {{ __('info_florence_egi.header_brand_name') }}</h1>
                        <p class="font-body text-sm text-blue-200 sm:text-base">
                            {{ __('info_florence_egi.header_brand_slogan') }}</p>
                    </div>
                </div>
                <nav class="hidden space-x-3 md:flex" aria-label="{{ __('info_florence_egi.nav_aria_label') }}">
                    <a href="{{ route('home') }}"
                        class="hover:text-oro-fiorentino font-body text-sm transition lg:text-base">{{ __('info_florence_egi.nav_home') }}</a>
                    <a href="#visione"
                        class="hover:text-oro-fiorentino font-body text-sm transition lg:text-base">{{ __('info_florence_egi.nav_vision') }}</a>
                    <a href="#problema"
                        class="hover:text-oro-fiorentino font-body text-sm transition lg:text-base">{{ __('info_florence_egi.nav_problem') }}</a>
                    <a href="#egi"
                        class="hover:text-oro-fiorentino font-body text-sm transition lg:text-base">{{ __('info_florence_egi.nav_egi') }}</a>
                    <a href="#soluzione"
                        class="hover:text-oro-fiorentino font-body text-sm transition lg:text-base">{{ __('info_florence_egi.nav_solution') }}</a>
                    <a href="#tecnologia"
                        class="hover:text-oro-fiorentino font-body text-sm transition lg:text-base">{{ __('info_florence_egi.nav_technology') }}</a>
                    <a href="#gdpr"
                        class="hover:text-oro-fiorentino font-body text-sm transition lg:text-base">{{ __('info_florence_egi.nav_gdpr') }}</a>
                    <a href="#archetipi"
                        class="hover:text-oro-fiorentino font-body text-sm transition lg:text-base">{{ __('info_florence_egi.nav_archetypes') }}</a>
                    <a href="#valori"
                        class="hover:text-oro-fiorentino font-body text-sm transition lg:text-base">{{ __('info_florence_egi.nav_values') }}</a>
                    <a href="#equilibrium"
                        class="hover:text-oro-fiorentino font-body text-sm transition lg:text-base">{{ __('info_florence_egi.nav_equilibrium') }}</a>
                </nav>
                <button id="mobile-menu-button"
                    class="block rounded-md p-2 transition-colors hover:bg-blue-700 md:hidden"><i
                        class="fas fa-bars text-2xl"></i></button>
            </div>
            <div id="mobile-menu" class="mt-4 hidden border-t border-blue-600 pb-4 md:hidden">
                <div class="space-y-3 pt-4">
                    <a href="{{ route('home') }}"
                        class="flex items-center rounded-md px-4 py-2 text-sm transition-colors hover:bg-blue-700"><i
                            class="fas fa-home text-oro-fiorentino mr-3 text-lg"></i>
                        {{ __('info_florence_egi.nav_home') }}</a>
                    <a href="#visione"
                        class="flex items-center rounded-md px-4 py-2 text-sm transition-colors hover:bg-blue-700"><i
                            class="fas fa-eye text-oro-fiorentino mr-3 text-lg"></i>
                        {{ __('info_florence_egi.nav_vision') }}</a>
                    <a href="#problema"
                        class="flex items-center rounded-md px-4 py-2 text-sm transition-colors hover:bg-blue-700"><i
                            class="fas fa-exclamation-triangle text-oro-fiorentino mr-3 text-lg"></i>
                        {{ __('info_florence_egi.nav_problem') }}</a>
                    <a href="#egi"
                        class="flex items-center rounded-md px-4 py-2 text-sm transition-colors hover:bg-blue-700"><i
                            class="fas fa-gem text-oro-fiorentino mr-3 text-lg"></i>
                        {{ __('info_florence_egi.nav_egi') }}</a>
                    <a href="#soluzione"
                        class="flex items-center rounded-md px-4 py-2 text-sm transition-colors hover:bg-blue-700"><i
                            class="fas fa-lightbulb text-oro-fiorentino mr-3 text-lg"></i>
                        {{ __('info_florence_egi.nav_solution') }}</a>
                    <a href="#tecnologia"
                        class="flex items-center rounded-md px-4 py-2 text-sm transition-colors hover:bg-blue-700"><i
                            class="fas fa-cogs text-oro-fiorentino mr-3 text-lg"></i>
                        {{ __('info_florence_egi.nav_technology') }}</a>
                    <a href="#gdpr"
                        class="flex items-center rounded-md px-4 py-2 text-sm transition-colors hover:bg-blue-700"><i
                            class="fas fa-shield-alt text-oro-fiorentino mr-3 text-lg"></i>
                        {{ __('info_florence_egi.nav_gdpr') }}</a>
                    <a href="#archetipi"
                        class="flex items-center rounded-md px-4 py-2 text-sm transition-colors hover:bg-blue-700"><i
                            class="fas fa-users text-oro-fiorentino mr-3 text-lg"></i>
                        {{ __('info_florence_egi.nav_archetypes') }}</a>
                    <a href="#valori"
                        class="flex items-center rounded-md px-4 py-2 text-sm transition-colors hover:bg-blue-700"><i
                            class="fas fa-heart text-oro-fiorentino mr-3 text-lg"></i>
                        {{ __('info_florence_egi.nav_values') }}</a>
                    <a href="#equilibrium"
                        class="flex items-center rounded-md px-4 py-2 text-sm transition-colors hover:bg-blue-700"><i
                            class="fas fa-atom text-oro-fiorentino mr-3 text-lg"></i>
                        {{ __('info_florence_egi.nav_equilibrium') }}</a>
                </div>
            </div>
        </div>
    </header>

    <main>
        <section id="visione" class="hero-background text-white"
            aria-label="{{ __('info_florence_egi.hero.aria_label') }}">
            <div class="golden-ratio-container px-4 py-16 sm:px-6 sm:py-24">
                <div class="mx-auto max-w-5xl text-center">
                    <h1 class="renaissance-title mb-6 text-4xl font-bold leading-tight sm:text-5xl md:text-6xl">
                        {!! __('info_florence_egi.hero.title_html') !!}</h1>
                    <p class="mx-auto mb-8 max-w-4xl font-body text-xl text-green-100 sm:text-2xl">
                        {!! __('info_florence_egi.hero.subtitle_html') !!}</p>
                    <div class="mx-auto mb-8 max-w-4xl font-body text-lg">
                        {{ __('info_florence_egi.hero.description') }}
                    </div>
                    <div class="flex flex-col gap-4 sm:flex-row sm:justify-center">
                        <a href="#soluzione" aria-label="{{ __('info_florence_egi.hero.cta_primary_aria') }}"
                            class="cta-primary elegant-hover inline-flex items-center justify-center rounded-xl px-8 py-4 text-lg font-semibold text-white"><i
                                class="fas fa-rocket mr-3"></i> {{ __('info_florence_egi.hero.cta_primary') }}</a>
                        <a href="#tecnologia" aria-label="{{ __('info_florence_egi.hero.cta_secondary_aria') }}"
                            class="border-oro-fiorentino text-oro-fiorentino hover:bg-oro-fiorentino elegant-hover inline-flex items-center justify-center rounded-xl border-2 px-8 py-4 text-lg font-semibold transition-all hover:text-blu-algoritmo"><i
                                class="fas fa-cogs mr-3"></i> {{ __('info_florence_egi.hero.cta_secondary') }}</a>
                    </div>
                </div>
            </div>
        </section>

        <section id="problema" class="bg-white py-16 sm:py-24"
            aria-label="{{ __('info_florence_egi.problem.aria_label') }}">
            <div class="golden-ratio-container px-4 sm:px-6">
                <div class="mb-12 text-center sm:mb-16">
                    <h2 class="renaissance-title mb-4 text-3xl font-bold text-grigio-pietra sm:text-4xl md:text-5xl">
                        {!! __('info_florence_egi.problem.title_html') !!}</h2>
                    <p class="mx-auto max-w-3xl font-body text-xl text-grigio-pietra">
                        {{ __('info_florence_egi.problem.subtitle') }}</p>
                </div>
                <div class="relative mx-auto mb-16 max-w-4xl">
                    <div class="flex h-96 items-center justify-center">
                        <div class="relative">
                            <div class="absolute -top-16 left-1/2 -translate-x-1/2 transform">
                                <div class="trilemma-point bg-viola-innovazione">
                                    <div><i class="fas fa-palette mb-2 text-2xl"></i><br>{!! __('info_florence_egi.problem.trilemma_quality') !!}</div>
                                </div>
                            </div>
                            <div class="absolute -left-16 top-32">
                                <div class="trilemma-point bg-blu-algoritmo">
                                    <div><i class="fas fa-chart-line mb-2 text-2xl"></i><br>{!! __('info_florence_egi.problem.trilemma_liquidity') !!}
                                    </div>
                                </div>
                            </div>
                            <div class="absolute -right-16 top-32">
                                <div class="trilemma-point bg-verde-rinascita">
                                    <div><i class="fas fa-leaf mb-2 text-2xl"></i><br>{!! __('info_florence_egi.problem.trilemma_impact') !!}</div>
                                </div>
                            </div>
                            <div
                                class="flex h-32 w-32 items-center justify-center rounded-full bg-rosso-urgenza text-center font-bold text-white">
                                <div><i class="fas fa-times mb-2 text-3xl"></i><br>{!! __('info_florence_egi.problem.trilemma_impossible') !!}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-16 grid gap-8 md:grid-cols-3">
                    <div class="renaissance-card p-6 text-center">
                        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100">
                            <i class="fas fa-times text-2xl text-rosso-urgenza"></i></div>
                        <h3 class="renaissance-title mb-2 text-xl font-bold text-grigio-pietra">
                            {{ __('info_florence_egi.problem.competitor_opensea') }}</h3>
                        <p class="font-body text-sm text-grigio-pietra">{!! __('info_florence_egi.problem.competitor_opensea_desc') !!}</p>
                    </div>
                    <div class="renaissance-card p-6 text-center">
                        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100">
                            <i class="fas fa-times text-2xl text-rosso-urgenza"></i></div>
                        <h3 class="renaissance-title mb-2 text-xl font-bold text-grigio-pietra">
                            {{ __('info_florence_egi.problem.competitor_blur') }}</h3>
                        <p class="font-body text-sm text-grigio-pietra">{!! __('info_florence_egi.problem.competitor_blur_desc') !!}</p>
                    </div>
                    <div class="renaissance-card p-6 text-center">
                        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100">
                            <i class="fas fa-times text-2xl text-rosso-urgenza"></i></div>
                        <h3 class="renaissance-title mb-2 text-xl font-bold text-grigio-pietra">
                            {{ __('info_florence_egi.problem.competitor_foundation') }}</h3>
                        <p class="font-body text-sm text-grigio-pietra">{!! __('info_florence_egi.problem.competitor_foundation_desc') !!}</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="egi" class="bg-gray-50 py-16 sm:py-24"
            aria-label="{{ __('info_florence_egi.egi.aria_label') }}">
            <div class="golden-ratio-container px-4 sm:px-6">
                <div class="mb-12 text-center sm:mb-16">
                    <h2 class="renaissance-title mb-4 text-3xl font-bold text-grigio-pietra sm:text-4xl md:text-5xl">
                        {!! __('info_florence_egi.egi.title_html') !!}</h2>
                    <p class="mx-auto max-w-3xl font-body text-xl text-grigio-pietra">{!! __('info_florence_egi.egi.subtitle_html') !!}</p>
                </div>
                <div class="mb-16 grid gap-8 md:grid-cols-3">
                    <div class="renaissance-card elegant-hover p-8">
                        <div class="mb-6 flex items-center justify-center">
                            <div class="flex h-20 w-20 items-center justify-center rounded-full bg-verde-rinascita/20">
                                <i class="fas fa-seedling text-3xl text-verde-rinascita"></i>
                            </div>
                        </div>
                        <h3 class="renaissance-title mb-4 text-center text-xl font-bold">
                            {{ __('info_florence_egi.egi.card1_title') }}</h3>
                        <p class="text-center font-body text-grigio-pietra">
                            {{ __('info_florence_egi.egi.card1_desc') }}
                        </p>
                    </div>
                    <div class="renaissance-card elegant-hover p-8">
                        <div class="mb-6 flex items-center justify-center">
                            <div class="bg-oro-fiorentino/20 flex h-20 w-20 items-center justify-center rounded-full">
                                <i class="fas fa-box text-oro-fiorentino text-3xl"></i></div>
                        </div>
                        <h3 class="renaissance-title mb-4 text-center text-xl font-bold">
                            {{ __('info_florence_egi.egi.card2_title') }}</h3>
                        <p class="text-center font-body text-grigio-pietra">
                            {{ __('info_florence_egi.egi.card2_desc') }}
                        </p>
                    </div>
                    <div class="renaissance-card elegant-hover p-8">
                        <div class="mb-6 flex items-center justify-center">
                            <div
                                class="flex h-20 w-20 items-center justify-center rounded-full bg-viola-innovazione/20">
                                <i class="fas fa-lightbulb text-3xl text-viola-innovazione"></i>
                            </div>
                        </div>
                        <h3 class="renaissance-title mb-4 text-center text-xl font-bold">
                            {{ __('info_florence_egi.egi.card3_title') }}</h3>
                        <p class="text-center font-body text-grigio-pietra">
                            {{ __('info_florence_egi.egi.card3_desc') }}
                        </p>
                    </div>
                </div>
                <div class="rounded-2xl bg-white p-8 md:p-12">
                    <h3 class="renaissance-title mb-6 text-center text-2xl font-bold text-grigio-pietra">
                        {!! __('info_florence_egi.egi.cocreation_title_html') !!}</h3>
                    <div class="grid items-center gap-8 md:grid-cols-2">
                        <div class="space-y-4 font-body">
                            <p class="text-lg">{!! __('info_florence_egi.egi.cocreation_p1') !!}</p>
                            <p>{{ __('info_florence_egi.egi.cocreation_p2') }}</p>
                            <div class="bg-oro-fiorentino/10 rounded-lg p-4">
                                <p class="text-oro-fiorentino font-semibold"><i class="fas fa-star mr-2"></i>
                                    {{ __('info_florence_egi.egi.cocreation_highlight') }}</p>
                            </div>
                        </div>
                        <div class="text-center">
                            <div
                                class="bg-oro-fiorentino/20 mb-4 inline-flex h-32 w-32 items-center justify-center rounded-full">
                                <i class="fas fa-handshake text-oro-fiorentino text-4xl"></i>
                            </div>
                            <p class="font-body text-grigio-pietra">{!! __('info_florence_egi.egi.cocreation_p3') !!}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="soluzione" class="section-dark py-16 text-white sm:py-24"
            aria-label="{{ __('info_florence_egi.solution.aria_label') }}">
            <div class="golden-ratio-container px-4 sm:px-6">
                <div class="mb-12 text-center sm:mb-16">
                    <h2 class="renaissance-title mb-4 text-3xl font-bold sm:text-4xl md:text-5xl">
                        {!! __('info_florence_egi.solution.title_html') !!}</h2>
                    <p class="mx-auto max-w-3xl font-body text-xl text-green-100">
                        {{ __('info_florence_egi.solution.subtitle') }}</p>
                </div>
                <div class="mb-16 grid gap-8 lg:grid-cols-2">
                    <div
                        class="renaissance-card bg-gradient-to-br from-viola-innovazione/10 to-viola-innovazione/5 p-8 text-grigio-pietra">
                        <div class="mb-6 flex items-center">
                            <div
                                class="mr-4 flex h-16 w-16 items-center justify-center rounded-full bg-viola-innovazione/20">
                                <i class="fas fa-gem text-2xl text-viola-innovazione"></i>
                            </div>
                            <h3 class="renaissance-title text-2xl font-bold">
                                {{ __('info_florence_egi.solution.card_physical_title') }}</h3>
                        </div>
                        <div class="space-y-4 font-body">
                            <p><strong>{{ __('info_florence_egi.solution.card_physical_l1_b') }}</strong>{{ __('info_florence_egi.solution.card_physical_l1') }}
                            </p>
                            <p><strong>{{ __('info_florence_egi.solution.card_physical_l2_b') }}</strong>{{ __('info_florence_egi.solution.card_physical_l2') }}
                            </p>
                            <p><strong>{{ __('info_florence_egi.solution.card_physical_l3_b') }}</strong>{{ __('info_florence_egi.solution.card_physical_l3') }}
                            </p>
                            <p class="font-semibold text-viola-innovazione">
                                {{ __('info_florence_egi.solution.card_physical_highlight') }}</p>
                        </div>
                    </div>
                    <div
                        class="renaissance-card bg-gradient-to-br from-blu-algoritmo/10 to-blu-algoritmo/5 p-8 text-grigio-pietra">
                        <div class="mb-6 flex items-center">
                            <div
                                class="mr-4 flex h-16 w-16 items-center justify-center rounded-full bg-blu-algoritmo/20">
                                <i class="fas fa-bolt text-2xl text-blu-algoritmo"></i>
                            </div>
                            <h3 class="renaissance-title text-2xl font-bold">
                                {{ __('info_florence_egi.solution.card_pt_title') }}</h3>
                        </div>
                        <div class="space-y-4 font-body">
                            <p><strong>{{ __('info_florence_egi.solution.card_pt_l1_b') }}</strong>{{ __('info_florence_egi.solution.card_pt_l1') }}
                            </p>
                            <p><strong>{{ __('info_florence_egi.solution.card_pt_l2_b') }}</strong>{{ __('info_florence_egi.solution.card_pt_l2') }}
                            </p>
                            <p><strong>{{ __('info_florence_egi.solution.card_pt_l3_b') }}</strong>{{ __('info_florence_egi.solution.card_pt_l3') }}
                            </p>
                            <p class="font-semibold text-blu-algoritmo">
                                {{ __('info_florence_egi.solution.card_pt_highlight') }}</p>
                        </div>
                    </div>
                </div>
                <div class="text-center">
                    <div
                        class="renaissance-card mx-auto max-w-4xl bg-gradient-to-br from-verde-rinascita/10 to-verde-rinascita/5 p-8 text-grigio-pietra">
                        <div class="mb-6 flex items-center justify-center">
                            <div
                                class="mr-4 flex h-16 w-16 items-center justify-center rounded-full bg-verde-rinascita/20">
                                <i class="fas fa-infinity text-2xl text-verde-rinascita"></i>
                            </div>
                            <h3 class="renaissance-title text-2xl font-bold">
                                {{ __('info_florence_egi.solution.infra_title') }}</h3>
                        </div>
                        <div class="grid gap-6 font-body md:grid-cols-3">
                            <div><i class="fas fa-leaf mb-2 text-3xl text-verde-rinascita"></i>
                                <p><strong>{{ __('info_florence_egi.solution.infra_f1_b') }}</strong><br>{{ __('info_florence_egi.solution.infra_f1') }}
                                </p>
                            </div>
                            <div><i class="fas fa-tachometer-alt mb-2 text-3xl text-verde-rinascita"></i>
                                <p><strong>{{ __('info_florence_egi.solution.infra_f2_b') }}</strong><br>{{ __('info_florence_egi.solution.infra_f2') }}
                                </p>
                            </div>
                            <div><i class="fas fa-shield-alt mb-2 text-3xl text-verde-rinascita"></i>
                                <p><strong>{{ __('info_florence_egi.solution.infra_f3_b') }}</strong><br>{{ __('info_florence_egi.solution.infra_f3') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="tecnologia" class="bg-white py-16 sm:py-24"
            aria-label="{{ __('info_florence_egi.tech.aria_label') }}">
            <div class="golden-ratio-container px-4 sm:px-6">
                <div class="mb-12 text-center sm:mb-16">
                    <h2 class="renaissance-title mb-4 text-3xl font-bold text-grigio-pietra sm:text-4xl md:text-5xl">
                        {!! __('info_florence_egi.tech.title_html') !!}</h2>
                    <p class="mx-auto max-w-3xl font-body text-xl text-grigio-pietra">
                        {{ __('info_florence_egi.tech.subtitle') }}</p>
                </div>
                <div class="mx-auto max-w-4xl space-y-8">
                    <div class="renaissance-card border-l-4 border-verde-rinascita p-6">
                        <div class="mb-4 flex items-center">
                            <div
                                class="mr-4 flex h-12 w-12 items-center justify-center rounded-full bg-verde-rinascita font-bold text-white">
                                1</div>
                            <h3 class="renaissance-title text-xl font-bold text-grigio-pietra">
                                {{ __('info_florence_egi.tech.layer1_title') }}</h3>
                        </div>
                        <p class="font-body text-grigio-pietra">
                            <strong>{{ __('info_florence_egi.tech.layer1_p1_b') }}</strong><br>{{ __('info_florence_egi.tech.layer1_p1') }}
                        </p>
                    </div>
                    <div class="renaissance-card border-oro-fiorentino border-l-4 p-6">
                        <div class="mb-4 flex items-center">
                            <div
                                class="bg-oro-fiorentino mr-4 flex h-12 w-12 items-center justify-center rounded-full font-bold text-white">
                                2</div>
                            <h3 class="renaissance-title text-xl font-bold text-grigio-pietra">
                                {{ __('info_florence_egi.tech.layer2_title') }}</h3>
                        </div>
                        <p class="font-body text-grigio-pietra">
                            <strong>{{ __('info_florence_egi.tech.layer2_p1_b') }}</strong><br>{{ __('info_florence_egi.tech.layer2_p1') }}
                        </p>
                    </div>
                    <div class="renaissance-card border-l-4 border-viola-innovazione p-6">
                        <div class="mb-4 flex items-center">
                            <div
                                class="mr-4 flex h-12 w-12 items-center justify-center rounded-full bg-viola-innovazione font-bold text-white">
                                3</div>
                            <h3 class="renaissance-title text-xl font-bold text-grigio-pietra">
                                {{ __('info_florence_egi.tech.layer3_title') }}</h3>
                        </div>
                        <p class="font-body text-grigio-pietra">
                            <strong>{{ __('info_florence_egi.tech.layer3_p1_b') }}</strong><br>{{ __('info_florence_egi.tech.layer3_p1') }}
                        </p>
                    </div>
                </div>
                <div class="mt-16">
                    <h3 class="renaissance-title mb-8 text-center text-2xl font-bold text-grigio-pietra">
                        {{ __('info_florence_egi.tech.sc_title') }}</h3>
                    <div class="grid gap-8 md:grid-cols-3">
                        <div class="renaissance-card p-6 text-center"><i
                                class="fas fa-chart-line text-oro-fiorentino mb-4 text-3xl"></i>
                            <h4 class="renaissance-title mb-2 text-lg font-bold text-grigio-pietra">
                                {{ __('info_florence_egi.tech.sc1_title') }}</h4>
                            <p class="font-body text-sm text-grigio-pietra">
                                {{ __('info_florence_egi.tech.sc1_desc') }}
                            </p>
                        </div>
                        <div class="renaissance-card p-6 text-center"><i
                                class="fas fa-share-alt mb-4 text-3xl text-verde-rinascita"></i>
                            <h4 class="renaissance-title mb-2 text-lg font-bold text-grigio-pietra">
                                {{ __('info_florence_egi.tech.sc2_title') }}</h4>
                            <p class="font-body text-sm text-grigio-pietra">
                                {{ __('info_florence_egi.tech.sc2_desc') }}
                            </p>
                        </div>
                        <div class="renaissance-card p-6 text-center"><i
                                class="fas fa-infinity mb-4 text-3xl text-viola-innovazione"></i>
                            <h4 class="renaissance-title mb-2 text-lg font-bold text-grigio-pietra">
                                {{ __('info_florence_egi.tech.sc3_title') }}</h4>
                            <p class="font-body text-sm text-grigio-pietra">
                                {{ __('info_florence_egi.tech.sc3_desc') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="coa" class="section-dark py-16 text-white sm:py-24">
            <div class="golden-ratio-container px-4 sm:px-6">
                <div class="mb-12 text-center sm:mb-16">
                    <h2 class="renaissance-title mb-4 text-3xl font-bold sm:text-4xl md:text-5xl">
                        {!! __('info_florence_egi.coa.title_html') !!}</h2>
                    <p class="mx-auto max-w-3xl font-body text-xl text-green-100">
                        {{ __('info_florence_egi.coa.subtitle') }}</p>
                </div>
                <div class="mb-16 grid gap-8 lg:grid-cols-2">
                    <div
                        class="renaissance-card from-oro-fiorentino/10 to-oro-fiorentino/5 bg-gradient-to-br p-8 text-grigio-pietra">
                        <div class="mb-6 flex items-center">
                            <div
                                class="bg-oro-fiorentino/20 mr-4 flex h-16 w-16 items-center justify-center rounded-full">
                                <i class="fas fa-certificate text-oro-fiorentino text-2xl"></i>
                            </div>
                            <h3 class="renaissance-title text-2xl font-bold">
                                {{ __('info_florence_egi.coa.card1_title') }}</h3>
                        </div>
                        <div class="space-y-4 font-body">
                            <p><strong>{{ __('info_florence_egi.coa.card1_l1_b') }}</strong>{{ __('info_florence_egi.coa.card1_l1') }}
                            </p>
                            <p><strong>{{ __('info_florence_egi.coa.card1_l2_b') }}</strong>{{ __('info_florence_egi.coa.card1_l2') }}
                            </p>
                            <p><strong>{{ __('info_florence_egi.coa.card1_l3_b') }}</strong>{{ __('info_florence_egi.coa.card1_l3') }}
                            </p>
                            <p><strong>{{ __('info_florence_egi.coa.card1_l4_b') }}</strong>{{ __('info_florence_egi.coa.card1_l4') }}
                            </p>
                            <p class="text-oro-fiorentino font-semibold">
                                {{ __('info_florence_egi.coa.card1_highlight') }}</p>
                        </div>
                    </div>
                    <div
                        class="renaissance-card bg-gradient-to-br from-blu-algoritmo/10 to-blu-algoritmo/5 p-8 text-grigio-pietra">
                        <div class="mb-6 flex items-center">
                            <div
                                class="mr-4 flex h-16 w-16 items-center justify-center rounded-full bg-blu-algoritmo/20">
                                <i class="fas fa-building text-2xl text-blu-algoritmo"></i>
                            </div>
                            <h3 class="renaissance-title text-2xl font-bold">
                                {{ __('info_florence_egi.coa.card2_title') }}</h3>
                        </div>
                        <div class="space-y-4 font-body">
                            <p><strong>{{ __('info_florence_egi.coa.card2_l1_b') }}</strong>{{ __('info_florence_egi.coa.card2_l1') }}
                            </p>
                            <p><strong>{{ __('info_florence_egi.coa.card2_l2_b') }}</strong>{{ __('info_florence_egi.coa.card2_l2') }}
                            </p>
                            <p><strong>{{ __('info_florence_egi.coa.card2_l3_b') }}</strong>{{ __('info_florence_egi.coa.card2_l3') }}
                            </p>
                            <p><strong>{{ __('info_florence_egi.coa.card2_l4_b') }}</strong>{{ __('info_florence_egi.coa.card2_l4') }}
                            </p>
                            <p class="font-semibold text-blu-algoritmo">
                                {{ __('info_florence_egi.coa.card2_highlight') }}</p>
                        </div>
                    </div>
                </div>
                <div class="mx-auto max-w-4xl">
                    <h3 class="renaissance-title mb-8 text-center text-2xl font-bold">
                        {{ __('info_florence_egi.coa.workflow_title') }}</h3>
                    <div class="space-y-6">
                        <div class="flex items-start space-x-4">
                            <div
                                class="bg-oro-fiorentino flex h-8 w-8 items-center justify-center rounded-full text-sm font-bold text-white">
                                1</div>
                            <div class="font-body">
                                <h4 class="text-lg font-semibold">{{ __('info_florence_egi.coa.workflow1_title') }}
                                </h4>
                                <p class="text-green-100">{{ __('info_florence_egi.coa.workflow1_desc') }}</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-4">
                            <div
                                class="bg-oro-fiorentino flex h-8 w-8 items-center justify-center rounded-full text-sm font-bold text-white">
                                2</div>
                            <div class="font-body">
                                <h4 class="text-lg font-semibold">{{ __('info_florence_egi.coa.workflow2_title') }}
                                </h4>
                                <p class="text-green-100">{{ __('info_florence_egi.coa.workflow2_desc') }}</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-4">
                            <div
                                class="bg-oro-fiorentino flex h-8 w-8 items-center justify-center rounded-full text-sm font-bold text-white">
                                3</div>
                            <div class="font-body">
                                <h4 class="text-lg font-semibold">{{ __('info_florence_egi.coa.workflow3_title') }}
                                </h4>
                                <p class="text-green-100">{{ __('info_florence_egi.coa.workflow3_desc') }}</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-4">
                            <div
                                class="bg-oro-fiorentino flex h-8 w-8 items-center justify-center rounded-full text-sm font-bold text-white">
                                4</div>
                            <div class="font-body">
                                <h4 class="text-lg font-semibold">{{ __('info_florence_egi.coa.workflow4_title') }}
                                </h4>
                                <p class="text-green-100">{{ __('info_florence_egi.coa.workflow4_desc') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="gdpr" class="section-dark py-16 text-white sm:py-24">
            <div class="golden-ratio-container px-4 sm:px-6">
                <div class="mb-12 text-center sm:mb-16">
                    <h2 class="renaissance-title mb-4 text-3xl font-bold sm:text-4xl md:text-5xl">
                        {!! __('info_florence_egi.gdpr.title_html') !!}</h2>
                    <p class="mx-auto max-w-4xl font-body text-xl text-green-100">
                        {{ __('info_florence_egi.gdpr.subtitle') }}</p>
                </div>
                <div class="grid gap-8 lg:grid-cols-2">
                    <div
                        class="renaissance-card elegant-hover bg-gradient-to-br from-blu-algoritmo/20 to-green-800/20 p-8">
                        <div class="mb-6 flex items-center">
                            <div
                                class="bg-oro-fiorentino/20 mr-4 flex h-16 w-16 items-center justify-center rounded-full">
                                <i class="fas fa-shield-alt text-oro-fiorentino text-2xl"></i>
                            </div>
                            <h3 class="renaissance-title text-xl font-bold">
                                {{ __('info_florence_egi.gdpr.card1_title') }}</h3>
                        </div>
                        <div class="space-y-4 font-body text-green-100">
                            <div class="flex items-start"><i
                                    class="fas fa-check-circle mr-3 mt-1 text-verde-rinascita"></i>
                                <div>
                                    <h4 class="font-semibold text-white">
                                        {{ __('info_florence_egi.gdpr.card1_f1_title') }}</h4>
                                    <p>{{ __('info_florence_egi.gdpr.card1_f1_desc') }}</p>
                                </div>
                            </div>
                            <div class="flex items-start"><i
                                    class="fas fa-check-circle mr-3 mt-1 text-verde-rinascita"></i>
                                <div>
                                    <h4 class="font-semibold text-white">
                                        {{ __('info_florence_egi.gdpr.card1_f2_title') }}</h4>
                                    <p>{{ __('info_florence_egi.gdpr.card1_f2_desc') }}</p>
                                </div>
                            </div>
                            <div class="flex items-start"><i
                                    class="fas fa-check-circle mr-3 mt-1 text-verde-rinascita"></i>
                                <div>
                                    <h4 class="font-semibold text-white">
                                        {{ __('info_florence_egi.gdpr.card1_f3_title') }}</h4>
                                    <p>{{ __('info_florence_egi.gdpr.card1_f3_desc') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="renaissance-card elegant-hover bg-gradient-to-br from-green-800/20 to-blu-algoritmo/20 p-8">
                        <div class="mb-6 flex items-center">
                            <div
                                class="mr-4 flex h-16 w-16 items-center justify-center rounded-full bg-verde-rinascita/20">
                                <i class="fas fa-cogs text-2xl text-verde-rinascita"></i>
                            </div>
                            <h3 class="renaissance-title text-xl font-bold">
                                {{ __('info_florence_egi.gdpr.card2_title') }}</h3>
                        </div>
                        <div class="space-y-4 font-body text-green-100">
                            <div class="flex items-start"><i
                                    class="fas fa-check-circle text-oro-fiorentino mr-3 mt-1"></i>
                                <div>
                                    <h4 class="font-semibold text-white">
                                        {{ __('info_florence_egi.gdpr.card2_f1_title') }}</h4>
                                    <p>{{ __('info_florence_egi.gdpr.card2_f1_desc') }}</p>
                                </div>
                            </div>
                            <div class="flex items-start"><i
                                    class="fas fa-check-circle text-oro-fiorentino mr-3 mt-1"></i>
                                <div>
                                    <h4 class="font-semibold text-white">
                                        {{ __('info_florence_egi.gdpr.card2_f2_title') }}</h4>
                                    <p>{{ __('info_florence_egi.gdpr.card2_f2_desc') }}</p>
                                </div>
                            </div>
                            <div class="flex items-start"><i
                                    class="fas fa-check-circle text-oro-fiorentino mr-3 mt-1"></i>
                                <div>
                                    <h4 class="font-semibold text-white">
                                        {{ __('info_florence_egi.gdpr.card2_f3_title') }}</h4>
                                    <p>{{ __('info_florence_egi.gdpr.card2_f3_desc') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-12">
                    <h3 class="renaissance-title mb-8 text-center text-2xl font-bold">{!! __('info_florence_egi.gdpr.rights_title_html') !!}</h3>
                    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                        <div
                            class="renaissance-card from-oro-fiorentino/10 to-oro-fiorentino/5 bg-gradient-to-br p-6 text-center">
                            <i class="fas fa-eye text-oro-fiorentino mb-4 text-3xl"></i>
                            <h4 class="mb-2 font-bold">{{ __('info_florence_egi.gdpr.right1_title') }}</h4>
                            <p class="text-sm text-green-100">{{ __('info_florence_egi.gdpr.right1_desc') }}</p>
                        </div>
                        <div
                            class="renaissance-card bg-gradient-to-br from-verde-rinascita/10 to-verde-rinascita/5 p-6 text-center">
                            <i class="fas fa-download mb-4 text-3xl text-verde-rinascita"></i>
                            <h4 class="mb-2 font-bold">{{ __('info_florence_egi.gdpr.right2_title') }}</h4>
                            <p class="text-sm text-green-100">{{ __('info_florence_egi.gdpr.right2_desc') }}</p>
                        </div>
                        <div
                            class="renaissance-card bg-gradient-to-br from-rosso-urgenza/10 to-rosso-urgenza/5 p-6 text-center">
                            <i class="fas fa-ban mb-4 text-3xl text-rosso-urgenza"></i>
                            <h4 class="mb-2 font-bold">{{ __('info_florence_egi.gdpr.right3_title') }}</h4>
                            <p class="text-sm text-green-100">{{ __('info_florence_egi.gdpr.right3_desc') }}</p>
                        </div>
                        <div
                            class="renaissance-card bg-gradient-to-br from-viola-innovazione/10 to-viola-innovazione/5 p-6 text-center">
                            <i class="fas fa-trash-alt mb-4 text-3xl text-viola-innovazione"></i>
                            <h4 class="mb-2 font-bold">{{ __('info_florence_egi.gdpr.right4_title') }}</h4>
                            <p class="text-sm text-green-100">{{ __('info_florence_egi.gdpr.right4_desc') }}</p>
                        </div>
                    </div>
                </div>
                <div class="mt-12">
                    <div
                        class="renaissance-card mx-auto max-w-4xl bg-gradient-to-r from-blu-algoritmo/20 to-verde-rinascita/20 p-8">
                        <div class="text-center">
                            <h3 class="renaissance-title mb-6 text-2xl font-bold">{!! __('info_florence_egi.gdpr.ultra_title_html') !!}</h3>
                            <p class="mb-6 font-body text-lg text-green-100">
                                {{ __('info_florence_egi.gdpr.ultra_desc') }}</p>
                            <div class="grid gap-4 md:grid-cols-3">
                                <div class="text-center"><i
                                        class="fas fa-clipboard-list text-oro-fiorentino mb-2 text-2xl"></i>
                                    <h4 class="font-bold">{{ __('info_florence_egi.gdpr.ultra1_title') }}</h4>
                                    <p class="text-sm text-green-100">{{ __('info_florence_egi.gdpr.ultra1_desc') }}
                                    </p>
                                </div>
                                <div class="text-center"><i
                                        class="fas fa-exclamation-triangle mb-2 text-2xl text-verde-rinascita"></i>
                                    <h4 class="font-bold">{{ __('info_florence_egi.gdpr.ultra2_title') }}</h4>
                                    <p class="text-sm text-green-100">{{ __('info_florence_egi.gdpr.ultra2_desc') }}
                                    </p>
                                </div>
                                <div class="text-center"><i
                                        class="fas fa-chart-line mb-2 text-2xl text-rosso-urgenza"></i>
                                    <h4 class="font-bold">{{ __('info_florence_egi.gdpr.ultra3_title') }}</h4>
                                    <p class="text-sm text-green-100">{{ __('info_florence_egi.gdpr.ultra3_desc') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="archetipi" class="bg-white py-16 sm:py-24">
            <div class="golden-ratio-container px-4 sm:px-6">
                <div class="mb-12 text-center sm:mb-16">
                    <h2 class="renaissance-title mb-4 text-3xl font-bold text-grigio-pietra sm:text-4xl md:text-5xl">
                        {!! __('info_florence_egi.archetypes.title_html') !!}</h2>
                    <p class="mx-auto max-w-3xl font-body text-xl text-grigio-pietra">
                        {{ __('info_florence_egi.archetypes.subtitle') }}</p>
                </div>
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    <a href="{{ route('info.creator') }}"
                        class="renaissance-card elegant-hover block p-6 text-center transition-transform hover:scale-105">
                        <div
                            class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-viola-innovazione/10">
                            <i class="fas fa-brush text-2xl text-viola-innovazione"></i>
                        </div>
                        <h3 class="renaissance-title mb-2 text-lg font-bold text-grigio-pietra">
                            {{ __('info_florence_egi.archetypes.creator_title') }}</h3>
                        <p class="font-body text-sm text-grigio-pietra">
                            {{ __('info_florence_egi.archetypes.creator_desc') }}</p>
                    </a>
                    <a href="{{ route('archetypes.collector') }}"
                        class="renaissance-card elegant-hover block p-6 text-center transition-transform hover:scale-105">
                        <div
                            class="bg-oro-fiorentino/10 mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full">
                            <i class="fas fa-gem text-oro-fiorentino text-2xl"></i>
                        </div>
                        <h3 class="renaissance-title mb-2 text-lg font-bold text-grigio-pietra">
                            {{ __('info_florence_egi.archetypes.collector_title') }}</h3>
                        <p class="font-body text-sm text-grigio-pietra">
                            {{ __('info_florence_egi.archetypes.collector_desc') }}</p>
                    </a>
                    <a href="{{ route('archetypes.patron') }}"
                        class="renaissance-card elegant-hover block p-6 text-center transition-transform hover:scale-105">
                        <div
                            class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-rosso-urgenza/10">
                            <i class="fas fa-heart text-2xl text-rosso-urgenza"></i>
                        </div>
                        <h3 class="renaissance-title mb-2 text-lg font-bold text-grigio-pietra">
                            {{ __('info_florence_egi.archetypes.patron_title') }}</h3>
                        <p class="font-body text-sm text-grigio-pietra">
                            {{ __('info_florence_egi.archetypes.patron_desc') }}</p>
                    </a>
                    <a href="{{ route('info.epp') }}"
                        class="renaissance-card elegant-hover block p-6 text-center transition-transform hover:scale-105">
                        <div
                            class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-verde-rinascita/10">
                            <i class="fas fa-seedling text-2xl text-verde-rinascita"></i>
                        </div>
                        <h3 class="renaissance-title mb-2 text-lg font-bold text-grigio-pietra">
                            {{ __('info_florence_egi.archetypes.epp_title') }}</h3>
                        <p class="font-body text-sm text-grigio-pietra">
                            {{ __('info_florence_egi.archetypes.epp_desc') }}</p>
                    </a>
                    <a href="{{ route('info.aziende') }}"
                        class="renaissance-card elegant-hover block p-6 text-center transition-transform hover:scale-105">
                        <div
                            class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-blu-algoritmo/10">
                            <i class="fas fa-building text-2xl text-blu-algoritmo"></i>
                        </div>
                        <h3 class="renaissance-title mb-2 text-lg font-bold text-grigio-pietra">
                            {{ __('info_florence_egi.archetypes.companies_title') }}</h3>
                        <p class="font-body text-sm text-grigio-pietra">
                            {{ __('info_florence_egi.archetypes.companies_desc') }}</p>
                    </a>
                    <a href="{{ route('archetypes.pa-entity') }}"
                        class="renaissance-card elegant-hover block p-6 text-center transition-transform hover:scale-105">
                        <div
                            class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-arancio-energia/10">
                            <i class="fas fa-university text-2xl text-arancio-energia"></i>
                        </div>
                        <h3 class="renaissance-title mb-2 text-lg font-bold text-grigio-pietra">
                            {{ __('info_florence_egi.archetypes.pa_title') }}</h3>
                        <p class="font-body text-sm text-grigio-pietra">
                            {{ __('info_florence_egi.archetypes.pa_desc') }}
                        </p>
                    </a>
                    <a href="{{ route('info.trader-pro') }}"
                        class="renaissance-card elegant-hover block p-6 text-center transition-transform hover:scale-105">
                        <div
                            class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-viola-innovazione/10">
                            <i class="fas fa-chart-line text-2xl text-viola-innovazione"></i>
                        </div>
                        <h3 class="renaissance-title mb-2 text-lg font-bold text-grigio-pietra">
                            {{ __('info_florence_egi.archetypes.trader_title') }}</h3>
                        <p class="font-body text-sm text-grigio-pietra">
                            {{ __('info_florence_egi.archetypes.trader_desc') }}</p>
                    </a>
                </div>
            </div>
        </section>

        <section id="impatto" class="section-dark py-16 text-white sm:py-24">
            <div class="golden-ratio-container px-4 sm:px-6">
                <div class="mb-12 text-center sm:mb-16">
                    <h2 class="renaissance-title mb-4 text-3xl font-bold sm:text-4xl md:text-5xl">
                        {!! __('info_florence_egi.impact.title_html') !!}</h2>
                    <p class="mx-auto max-w-3xl font-body text-xl text-green-100">
                        {{ __('info_florence_egi.impact.subtitle') }}</p>
                </div>
                <div class="mb-12 text-center">
                    <div
                        class="renaissance-card from-oro-fiorentino/10 to-oro-fiorentino/5 mx-auto max-w-4xl bg-gradient-to-br p-8 text-grigio-pietra">
                        <h3 class="renaissance-title mb-6 text-2xl font-bold">
                            {{ __('info_florence_egi.impact.card_title') }}</h3>
                        <div class="grid gap-6 font-body md:grid-cols-3">
                            <div class="text-center">
                                <div class="text-oro-fiorentino text-3xl font-bold">
                                    {{ __('info_florence_egi.impact.stat1_title') }}</div>
                                <p class="text-sm">{{ __('info_florence_egi.impact.stat1_desc') }}</p>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold text-verde-rinascita">
                                    {{ __('info_florence_egi.impact.stat2_title') }}</div>
                                <p class="text-sm">{{ __('info_florence_egi.impact.stat2_desc') }}</p>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold text-blu-algoritmo">
                                    {{ __('info_florence_egi.impact.stat3_title') }}</div>
                                <p class="text-sm">{{ __('info_florence_egi.impact.stat3_desc') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-16 grid gap-8 md:grid-cols-3">
                    <div
                        class="renaissance-card bg-gradient-to-br from-verde-rinascita/10 to-verde-rinascita/5 p-8 text-grigio-pietra">
                        <div class="mb-6 flex items-center">
                            <div
                                class="mr-4 flex h-16 w-16 items-center justify-center rounded-full bg-verde-rinascita/20">
                                <i class="fas fa-tree text-2xl text-verde-rinascita"></i>
                            </div>
                            <h3 class="renaissance-title text-xl font-bold">
                                {{ __('info_florence_egi.impact.epp1_title') }}</h3>
                        </div>
                        <div class="space-y-2 font-body">
                            <p><strong>{{ __('info_florence_egi.impact.epp1_l1_b') }}</strong></p>
                            <p>{{ __('info_florence_egi.impact.epp1_l2') }}</p>
                            <p>{{ __('info_florence_egi.impact.epp1_l3') }}</p>
                            <p>{{ __('info_florence_egi.impact.epp1_l4') }}</p>
                            <p class="text-sm text-verde-rinascita"><i
                                    class="fas fa-exclamation-triangle mr-1"></i>{{ __('info_florence_egi.impact.epp1_highlight') }}
                            </p>
                        </div>
                    </div>
                    <div
                        class="renaissance-card bg-gradient-to-br from-blu-algoritmo/10 to-blu-algoritmo/5 p-8 text-grigio-pietra">
                        <div class="mb-6 flex items-center">
                            <div
                                class="mr-4 flex h-16 w-16 items-center justify-center rounded-full bg-blu-algoritmo/20">
                                <i class="fas fa-water text-2xl text-blu-algoritmo"></i>
                            </div>
                            <h3 class="renaissance-title text-xl font-bold">
                                {{ __('info_florence_egi.impact.epp2_title') }}</h3>
                        </div>
                        <div class="space-y-2 font-body">
                            <p><strong>{{ __('info_florence_egi.impact.epp2_l1_b') }}</strong></p>
                            <p>{{ __('info_florence_egi.impact.epp2_l2') }}</p>
                            <p>{{ __('info_florence_egi.impact.epp2_l3') }}</p>
                            <p>{{ __('info_florence_egi.impact.epp2_l4') }}</p>
                            <p class="text-sm text-blu-algoritmo"><i
                                    class="fas fa-exclamation-triangle mr-1"></i>{{ __('info_florence_egi.impact.epp2_highlight') }}
                            </p>
                        </div>
                    </div>
                    <div
                        class="renaissance-card from-oro-fiorentino/10 to-oro-fiorentino/5 bg-gradient-to-br p-8 text-grigio-pietra">
                        <div class="mb-6 flex items-center">
                            <div
                                class="bg-oro-fiorentino/20 mr-4 flex h-16 w-16 items-center justify-center rounded-full">
                                <i class="fas fa-bee text-oro-fiorentino text-2xl"></i>
                            </div>
                            <h3 class="renaissance-title text-xl font-bold">
                                {{ __('info_florence_egi.impact.epp3_title') }}</h3>
                        </div>
                        <div class="space-y-2 font-body">
                            <p><strong>{{ __('info_florence_egi.impact.epp3_l1_b') }}</strong></p>
                            <p>{{ __('info_florence_egi.impact.epp3_l2') }}</p>
                            <p>{{ __('info_florence_egi.impact.epp3_l3') }}</p>
                            <p>{{ __('info_florence_egi.impact.epp3_l4') }}</p>
                            <p class="text-oro-fiorentino text-sm"><i
                                    class="fas fa-check mr-1"></i>{{ __('info_florence_egi.impact.epp3_highlight') }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="text-center">
                    <div
                        class="renaissance-card mx-auto max-w-4xl bg-gradient-to-br from-viola-innovazione/10 to-viola-innovazione/5 p-8 text-grigio-pietra">
                        <h3 class="renaissance-title mb-6 text-2xl font-bold">
                            {{ __('info_florence_egi.impact.growth_title') }}</h3>
                        <div class="grid gap-6 font-body md:grid-cols-2">
                            <div>
                                <h4 class="mb-2 font-bold text-viola-innovazione">
                                    {{ __('info_florence_egi.impact.growth1_title') }}</h4>
                                <p>{{ __('info_florence_egi.impact.growth1_desc') }}</p>
                            </div>
                            <div>
                                <h4 class="mb-2 font-bold text-viola-innovazione">
                                    {{ __('info_florence_egi.impact.growth2_title') }}</h4>
                                <p>{{ __('info_florence_egi.impact.growth2_desc') }}</p>
                            </div>
                        </div>
                        <div class="mt-6"><a href="{{ route('info.epp') }}"
                                class="inline-flex items-center rounded-lg bg-viola-innovazione px-6 py-3 text-white transition-all hover:bg-viola-innovazione/80"><i
                                    class="fas fa-seedling mr-2"></i> {{ __('info_florence_egi.impact.cta') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="valori" class="bg-gray-50 py-16 sm:py-24">
            <div class="golden-ratio-container px-4 sm:px-6">
                <div class="mb-12 text-center sm:mb-16">
                    <h2 class="renaissance-title mb-4 text-3xl font-bold text-grigio-pietra sm:text-4xl md:text-5xl">
                        {!! __('info_florence_egi.values.title_html') !!}</h2>
                    <p class="mx-auto max-w-3xl font-body text-xl text-grigio-pietra">
                        {{ __('info_florence_egi.values.subtitle') }}</p>
                </div>
                <div class="mb-16 grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                    <div class="renaissance-card elegant-hover p-8">
                        <div class="mb-6 flex items-center">
                            <div
                                class="mr-4 flex h-16 w-16 items-center justify-center rounded-full bg-verde-rinascita/20">
                                <i class="fas fa-seedling text-2xl text-verde-rinascita"></i>
                            </div>
                            <h3 class="renaissance-title text-xl font-bold">
                                {{ __('info_florence_egi.values.value1_title') }}</h3>
                        </div>
                        <p class="font-body text-grigio-pietra">{{ __('info_florence_egi.values.value1_desc') }}</p>
                    </div>
                    <div class="renaissance-card elegant-hover p-8">
                        <div class="mb-6 flex items-center">
                            <div
                                class="mr-4 flex h-16 w-16 items-center justify-center rounded-full bg-viola-innovazione/20">
                                <i class="fas fa-palette text-2xl text-viola-innovazione"></i>
                            </div>
                            <h3 class="renaissance-title text-xl font-bold">
                                {{ __('info_florence_egi.values.value2_title') }}</h3>
                        </div>
                        <p class="font-body text-grigio-pietra">{{ __('info_florence_egi.values.value2_desc') }}</p>
                    </div>
                    <div class="renaissance-card elegant-hover p-8">
                        <div class="mb-6 flex items-center">
                            <div
                                class="bg-oro-fiorentino/20 mr-4 flex h-16 w-16 items-center justify-center rounded-full">
                                <i class="fas fa-lightbulb text-oro-fiorentino text-2xl"></i>
                            </div>
                            <h3 class="renaissance-title text-xl font-bold">
                                {{ __('info_florence_egi.values.value3_title') }}</h3>
                        </div>
                        <p class="font-body text-grigio-pietra">{{ __('info_florence_egi.values.value3_desc') }}</p>
                    </div>
                    <div class="renaissance-card elegant-hover p-8">
                        <div class="mb-6 flex items-center">
                            <div
                                class="mr-4 flex h-16 w-16 items-center justify-center rounded-full bg-blu-algoritmo/20">
                                <i class="fas fa-shield-alt text-2xl text-blu-algoritmo"></i>
                            </div>
                            <h3 class="renaissance-title text-xl font-bold">
                                {{ __('info_florence_egi.values.value4_title') }}</h3>
                        </div>
                        <p class="font-body text-grigio-pietra">{{ __('info_florence_egi.values.value4_desc') }}</p>
                    </div>
                    <div class="renaissance-card elegant-hover p-8">
                        <div class="mb-6 flex items-center">
                            <div
                                class="mr-4 flex h-16 w-16 items-center justify-center rounded-full bg-rosso-urgenza/20">
                                <i class="fas fa-exchange-alt text-2xl text-rosso-urgenza"></i>
                            </div>
                            <h3 class="renaissance-title text-xl font-bold">
                                {{ __('info_florence_egi.values.value5_title') }}</h3>
                        </div>
                        <p class="font-body text-grigio-pietra">{{ __('info_florence_egi.values.value5_desc') }}</p>
                    </div>
                    <div class="renaissance-card elegant-hover p-8">
                        <div class="mb-6 flex items-center">
                            <div
                                class="mr-4 flex h-16 w-16 items-center justify-center rounded-full bg-arancio-energia/20">
                                <i class="fas fa-balance-scale text-2xl text-arancio-energia"></i>
                            </div>
                            <h3 class="renaissance-title text-xl font-bold">
                                {{ __('info_florence_egi.values.value6_title') }}</h3>
                        </div>
                        <p class="font-body text-grigio-pietra">{{ __('info_florence_egi.values.value6_desc') }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="equilibrium"
            class="from-oro-fiorentino/10 bg-gradient-to-br to-verde-rinascita/10 py-16 sm:py-24">
            <div class="golden-ratio-container px-4 sm:px-6">
                <div class="mx-auto max-w-6xl">
                    <div class="mb-12 text-center">
                        <h2
                            class="renaissance-title mb-4 text-3xl font-bold text-grigio-pietra sm:text-4xl md:text-5xl">
                            {!! __('info_florence_egi.equilibrium.title_html') !!}</h2>
                        <p class="mx-auto max-w-3xl font-body text-xl text-grigio-pietra">
                            {{ __('info_florence_egi.equilibrium.subtitle') }}</p>
                    </div>
                    <div class="grid gap-8 lg:grid-cols-2">
                        <div class="renaissance-card elegant-hover p-8">
                            <div class="mb-6 flex items-center">
                                <div
                                    class="bg-oro-fiorentino/20 mr-4 flex h-16 w-16 items-center justify-center rounded-full">
                                    <i class="fas fa-atom text-oro-fiorentino text-2xl"></i>
                                </div>
                                <h3 class="renaissance-title text-xl font-bold">
                                    {{ __('info_florence_egi.equilibrium.card1_title') }}</h3>
                            </div>
                            <p class="mb-4 font-body text-grigio-pietra">
                                {{ __('info_florence_egi.equilibrium.card1_p1') }}</p>
                            <p class="font-body text-grigio-pietra">
                                {{ __('info_florence_egi.equilibrium.card1_p2') }}
                            </p>
                        </div>
                        <div class="renaissance-card elegant-hover p-8">
                            <div class="mb-6 flex items-center">
                                <div
                                    class="mr-4 flex h-16 w-16 items-center justify-center rounded-full bg-verde-rinascita/20">
                                    <i class="fas fa-cogs text-2xl text-verde-rinascita"></i>
                                </div>
                                <h3 class="renaissance-title text-xl font-bold">
                                    {{ __('info_florence_egi.equilibrium.card2_title') }}</h3>
                            </div>
                            <p class="mb-4 font-body text-grigio-pietra">
                                {{ __('info_florence_egi.equilibrium.card2_p1') }}</p>
                            <p class="font-body text-grigio-pietra">
                                {{ __('info_florence_egi.equilibrium.card2_p2') }}
                            </p>
                        </div>
                        <div class="renaissance-card elegant-hover p-8 lg:col-span-2">
                            <div class="mb-6 flex items-center justify-center">
                                <div
                                    class="mr-4 flex h-16 w-16 items-center justify-center rounded-full bg-blu-algoritmo/20">
                                    <i class="fas fa-heart text-2xl text-blu-algoritmo"></i>
                                </div>
                                <h3 class="renaissance-title text-xl font-bold">
                                    {{ __('info_florence_egi.equilibrium.card3_title') }}</h3>
                            </div>
                            <p class="mx-auto max-w-4xl text-center font-body text-grigio-pietra">
                                {!! __('info_florence_egi.equilibrium.card3_p1') !!}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-white py-16 sm:py-24">
            <div class="golden-ratio-container px-4 sm:px-6">
                <div class="mx-auto max-w-4xl text-center">
                    <h2 class="renaissance-title mb-6 text-3xl font-bold text-grigio-pietra sm:text-4xl">
                        {!! __('info_florence_egi.cta.title_html') !!}</h2>
                    <p class="mb-8 font-body text-xl text-grigio-pietra">
                        <em>"{{ __('info_florence_egi.cta.quote') }}"</em>
                    </p>
                    <div class="flex flex-col gap-4 sm:flex-row sm:justify-center">
                        <a href="{{ route('register') }}" aria-label="{{ __('info_florence_egi.cta.cta1_aria') }}"
                            class="cta-primary elegant-hover inline-flex items-center justify-center rounded-xl px-8 py-4 text-lg font-semibold text-white"><i
                                class="fas fa-rocket mr-3"></i> {{ __('info_florence_egi.cta.cta1') }}</a>
                        <a href="{{ route('archetypes.patron') }}"
                            aria-label="{{ __('info_florence_egi.cta.cta2_aria') }}"
                            class="border-oro-fiorentino text-oro-fiorentino hover:bg-oro-fiorentino elegant-hover inline-flex items-center justify-center rounded-xl border-2 px-8 py-4 text-lg font-semibold transition-all hover:text-white"><i
                                class="fas fa-users mr-3"></i> {{ __('info_florence_egi.cta.cta2') }}</a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    @include('components.info-footer')

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
            const mobileMenuLinks = mobileMenu.querySelectorAll('a');
            mobileMenuLinks.forEach(link => {
                link.addEventListener('click', function() {
                    mobileMenu.classList.add('hidden');
                    menuIcon.className = 'fas fa-bars text-2xl';
                });
            });
            document.addEventListener('click', function(event) {
                if (!mobileMenuButton.contains(event.target) && !mobileMenu.contains(event.target)) {
                    mobileMenu.classList.add('hidden');
                    menuIcon.className = 'fas fa-bars text-2xl';
                }
            });
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
