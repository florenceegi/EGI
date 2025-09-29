{{--
/**
 * @package Resources\Views\Info
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 2.0.0 (FlorenceEGI - Why Can't Buy EGIs Explanation - Localized)
 * @date 2025-09-29
 * @purpose Vista informativa localizzata che spiega la fase MVP e roadmap EGI
 * @brand-compliant Segue FlorenceEGI Brand Guidelines: Oro Fiorentino #D4A574, Verde Rinascita #2D5016, Blu Algoritmo #1B365D
 * @localization Supporta 6 lingue: IT/EN/ES/PT/FR/DE
 * @seo SEO completo con Schema.org, Open Graph, meta tags
 * @accessibility WCAG 2.1 AA compliant con ARIA labels
 * @silent-growth Ottimizzato per crescita organica internazionale
 */
--}}

<x-guest-layout
    title="{{ __('info.why_cant_buy_egis_info.page_title') }}"
    :noHero="false">

    @slot('heroFullWidth')
        {{-- Hero Section Completo --}}
        <div class="relative bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 py-24 min-h-[60vh] flex items-center">

            {{-- Background Pattern (Rinascimento Style) --}}
            <div class="absolute inset-0 opacity-10">
                <div class="absolute inset-0" style="background-image: radial-gradient(circle at 25% 25%, #D4A574 2px, transparent 2px), radial-gradient(circle at 75% 75%, #2D5016 2px, transparent 2px); background-size: 40px 40px;"></div>
            </div>

            <div class="container relative z-10 px-6 mx-auto">
                <div class="max-w-4xl mx-auto text-center">
                    {{-- Breadcrumb --}}
                    <nav aria-label="{{ __('info.why_cant_buy_egis_info.breadcrumb_aria') }}" class="mb-8">
                        <ol class="flex justify-center space-x-2 text-sm text-gray-400">
                            <li><a href="{{ route('home') }}" class="transition-colors hover:text-white">Home</a></li>
                            <li class="text-gray-600">•</li>
                            <li class="text-white" aria-current="page">{{ __('info.why_cant_buy_egis_info.page_title') }}</li>
                        </ol>
                    </nav>

                    {{-- Hero Content --}}
                    <h1 class="mb-6 text-4xl font-bold text-white md:text-5xl lg:text-6xl"
                        style="font-family: 'Playfair Display', serif;">
                        {{ __('info.why_cant_buy_egis_info.hero_title') }}
                    </h1>

                    <p class="mb-8 text-xl leading-relaxed text-gray-300 md:text-2xl">
                        {{ __('info.why_cant_buy_egis_info.hero_subtitle') }}
                    </p>

                    {{-- CTA Buttons --}}
                    <div class="flex flex-col justify-center gap-4 sm:flex-row">
                        <a href="{{ route('home') }}"
                           class="inline-flex items-center px-6 py-3 font-medium text-gray-300 transition-all duration-200 border border-gray-600 rounded-lg hover:bg-gray-800 hover:border-gray-500"
                           aria-label="{{ __('info.why_cant_buy_egis_info.back_button_aria') }}">
                            ← {{ __('info.why_cant_buy_egis_info.back_button') }}
                        </a>

                        <a href="{{ route('home.collections.index') }}"
                           class="inline-flex items-center px-6 py-3 font-medium text-gray-900 transition-all duration-200 rounded-lg"
                           style="background: linear-gradient(135deg, #D4A574 0%, #E6B885 100%);"
                           aria-label="{{ __('info.why_cant_buy_egis_info.cta_button_aria') }}">
                            {{ __('info.why_cant_buy_egis_info.cta_button') }} →
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endslot

    @slot('heroNatanAssistant')
        {{-- Natan Assistant in posizione standard --}}
        @include('components.natan-assistant')
    @endslot

    @slot('belowHeroContent')
        {{-- Sezioni Informative Principali --}}
        <div class="container px-6 py-16 mx-auto">
            <div class="max-w-4xl mx-auto space-y-16">

                {{-- MVP Section --}}
                <section class="flex items-start gap-6" aria-labelledby="mvp-section-heading">
                    <div class="flex items-center justify-center flex-shrink-0 w-16 h-16 text-2xl rounded-full"
                         style="background: linear-gradient(135deg, #1B365D 0%, #2D4A7A 100%);"
                         role="img" aria-label="Icona fase MVP">
                        🚧
                    </div>
                    <div class="flex-1">
                        <h2 id="mvp-section-heading"
                            class="mb-4 text-2xl font-bold md:text-3xl"
                            style="color: #1B365D; font-family: 'Playfair Display', serif;">
                            {{ __('info.why_cant_buy_egis_info.mvp_section_title') }}
                        </h2>
                        <p class="text-lg leading-relaxed text-gray-700">
                            {{ __('info.why_cant_buy_egis_info.mvp_section_text') }}
                        </p>
                    </div>
                </section>

                {{-- Reservations Section --}}
                <section class="flex items-start gap-6" aria-labelledby="reservations-section-heading">
                    <div class="flex items-center justify-center flex-shrink-0 w-16 h-16 text-2xl rounded-full"
                         style="background: linear-gradient(135deg, #2D5016 0%, #3D6B1F 100%);"
                         role="img" aria-label="Icona sistema prenotazioni">
                        📋
                    </div>
                    <div class="flex-1">
                        <h2 id="reservations-section-heading"
                            class="mb-4 text-2xl font-bold md:text-3xl"
                            style="color: #2D5016; font-family: 'Playfair Display', serif;">
                            {{ __('info.why_cant_buy_egis_info.reservations_section_title') }}
                        </h2>
                        <p class="text-lg leading-relaxed text-gray-700">
                            {{ __('info.why_cant_buy_egis_info.reservations_section_text') }}
                        </p>
                    </div>
                </section>

                {{-- Roadmap Section --}}
                <section class="flex items-start gap-6" aria-labelledby="roadmap-section-heading">
                    <div class="flex items-center justify-center flex-shrink-0 w-16 h-16 text-2xl rounded-full"
                         style="background: linear-gradient(135deg, #D4A574 0%, #E6B885 100%);"
                         role="img" aria-label="Icona roadmap di sviluppo">
                        🗺️
                    </div>
                    <div class="flex-1">
                        <h2 id="roadmap-section-heading"
                            class="mb-4 text-2xl font-bold md:text-3xl"
                            style="color: #D4A574; font-family: 'Playfair Display', serif;">
                            {{ __('info.why_cant_buy_egis_info.roadmap_section_title') }}
                        </h2>
                        <p class="text-lg leading-relaxed text-gray-700">
                            {{ __('info.why_cant_buy_egis_info.roadmap_section_text') }}
                        </p>
                    </div>
                </section>

            </div>
        </div>
    @endslot

    @slot('belowHeroContent_1')
        {{-- CTA Section Finale --}}
        <div class="container px-6 py-16 mx-auto">
            <div class="max-w-4xl mx-auto">
                <section class="px-8 py-16 text-center rounded-2xl"
                         style="background: linear-gradient(135deg, #1B365D 0%, #2D4A7A 100%);"
                         aria-labelledby="cta-section-heading">
                    <h2 id="cta-section-heading"
                        class="mb-4 text-3xl font-bold text-white md:text-4xl"
                        style="font-family: 'Playfair Display', serif;">
                        {{ __('info.why_cant_buy_egis_info.cta_title') }}
                    </h2>
                    <p class="max-w-2xl mx-auto mb-8 text-xl leading-relaxed text-gray-200">
                        {{ __('info.why_cant_buy_egis_info.cta_text') }}
                    </p>
                    <a href="{{ route('home.collections.index') }}"
                       class="inline-flex items-center px-8 py-4 text-lg font-bold text-gray-900 transition-all duration-200 rounded-lg hover:scale-105"
                       style="background: linear-gradient(135deg, #D4A574 0%, #E6B885 100%); box-shadow: 0 4px 12px rgba(212, 165, 116, 0.3);"
                       aria-label="{{ __('info.why_cant_buy_egis_info.cta_button_aria') }}">
                        {{ __('info.why_cant_buy_egis_info.cta_button') }}
                        <span class="ml-2" aria-hidden="true">→</span>
                    </a>
                </section>
            </div>
        </div>
    @endslot

    {{-- SEO e Meta Data Completo --}}
    @push('meta')
        <meta name="description" content="{{ __('info.why_cant_buy_egis_info.page_description') }}">
        <meta name="keywords" content="{{ __('info.why_cant_buy_egis_info.meta_keywords') }}">
        <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large">
        <meta name="author" content="FlorenceEGI">
        <meta name="language" content="{{ app()->getLocale() }}">
        <link rel="canonical" href="{{ url()->current() }}">

        <!-- Open Graph Protocol -->
        <meta property="og:title" content="{{ __('info.why_cant_buy_egis_info.page_title') }} | FlorenceEGI">
        <meta property="og:description" content="{{ __('info.why_cant_buy_egis_info.page_description') }}">
        <meta property="og:type" content="article">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:site_name" content="FlorenceEGI">
        <meta property="og:locale" content="{{ str_replace('_', '-', app()->getLocale()) }}">
        <meta property="article:author" content="FlorenceEGI">
        <meta property="article:section" content="Info">
        <meta property="article:tag" content="EGI,MVP,Blockchain,Sostenibilità">

        <!-- Twitter Card -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ __('info.why_cant_buy_egis_info.page_title') }}">
        <meta name="twitter:description" content="{{ __('info.why_cant_buy_egis_info.page_description') }}">
        <meta name="twitter:site" content="@FlorenceEGI">

        <!-- Schema.org Structured Data -->
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "FAQPage",
            "mainEntity": {
                "@type": "Question",
                "name": "{{ __('info.why_cant_buy_egis_info.faq_question') }}",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "{{ __('info.why_cant_buy_egis_info.faq_answer') }}"
                }
            },
            "about": {
                "@type": "Organization",
                "@id": "https://florence-egi.com/#organization",
                "name": "FlorenceEGI",
                "url": "https://florence-egi.com",
                "description": "Beyond NFT: Rinascimento Ecologico Digitale",
                "foundingDate": "2024",
                "knowsAbout": ["Blockchain sostenibile", "Arte digitale", "Certificazione ambientale", "EGI"]
            },
            "breadcrumb": {
                "@type": "BreadcrumbList",
                "itemListElement": [
                    {
                        "@type": "ListItem",
                        "position": 1,
                        "name": "Home",
                        "item": "{{ route('home') }}"
                    },
                    {
                        "@type": "ListItem",
                        "position": 2,
                        "name": "{{ __('info.why_cant_buy_egis_info.page_title') }}",
                        "item": "{{ url()->current() }}"
                    }
                ]
            }
        }
        </script>
    @endpush

    {{-- Natan Integration Script --}}
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Se l'utente arriva da Natan, mostra un welcome speciale
                if (document.referrer.includes('natan') || window.location.hash === '#from-natan') {
                    setTimeout(() => {
                        // Crea un messaggio di benvenuto da Natan
                        const natanWelcome = document.createElement('div');
                        natanWelcome.className = 'fixed bottom-6 left-6 max-w-sm p-4 bg-gray-900 border border-emerald-600/30 rounded-lg shadow-lg z-50';
                        natanWelcome.setAttribute('role', 'dialog');
                        natanWelcome.setAttribute('aria-label', 'Messaggio di benvenuto da Natan Assistant');
                        natanWelcome.innerHTML = `
                            <div class="flex items-start gap-3">
                                <span class="text-2xl" role="img" aria-label="Cappello di Natan">🎩</span>
                                <div>
                                    <div class="mb-1 font-semibold text-emerald-300">{{ __('info.why_cant_buy_egis_info.natan_welcome_title') }}</div>
                                    <div class="text-sm text-gray-200">{{ __('info.why_cant_buy_egis_info.natan_welcome_text') }}</div>
                                    <button onclick="this.parentElement.parentElement.parentElement.remove()"
                                            class="mt-2 text-xs text-emerald-400 hover:text-emerald-300"
                                            aria-label="Chiudi messaggio di Natan">
                                        {{ __('info.why_cant_buy_egis_info.natan_thanks') }} ✨
                                    </button>
                                </div>
                            </div>
                        `;
                        document.body.appendChild(natanWelcome);

                        // Auto-rimuovi dopo 8 secondi
                        setTimeout(() => {
                            if (natanWelcome.parentNode) {
                                natanWelcome.remove();
                            }
                        }, 8000);
                    }, 1000);
                }
            });
        </script>
    @endpush

</x-guest-layout>
