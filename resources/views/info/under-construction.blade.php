{{--
* @package Resources\Views\Info
* @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
* @version 1.0.0 (FlorenceEGI - Under Construction)
* @date 2025-07-07
* @purpose Vista generica per funzionalità non ancora disponibili
--}}

<x-guest-layout title="Pagina in costruzione" :noHero="false">

    @slot('heroFullWidth')
        <div class="relative flex min-h-[60vh] items-center bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 py-24">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute inset-0"
                    style="background-image: radial-gradient(circle at 25% 25%, #D4A574 2px, transparent 2px), radial-gradient(circle at 75% 75%, #2D5016 2px, transparent 2px); background-size: 40px 40px;">
                </div>
            </div>
            <div class="container relative z-10 mx-auto px-6">
                <div class="mx-auto max-w-4xl text-center">
                    <nav aria-label="breadcrumb" class="mb-8">
                        <ol class="flex justify-center space-x-2 text-sm text-gray-400">
                            <li><a href="{{ route('home') }}" class="transition-colors hover:text-white">Home</a></li>
                            <li class="text-gray-600">•</li>
                            <li class="text-white" aria-current="page">In costruzione</li>
                        </ol>
                    </nav>
                    <h1 class="mb-6 text-4xl font-bold text-white md:text-5xl lg:text-6xl"
                        style="font-family: 'Playfair Display', serif;">
                        {{ $title ?? 'Pagina in costruzione' }}
                    </h1>
                    <p class="mb-8 text-xl leading-relaxed text-gray-300 md:text-2xl">
                        {{ $subtitle ?? 'La funzionalità non è ancora disponibile.' }}<br>
                        Stiamo lavorando per renderla accessibile al più presto!
                    </p>
                    <div class="flex flex-col justify-center gap-4 sm:flex-row">
                        <a href="{{ url()->previous() }}"
                            class="inline-flex items-center rounded-lg border border-gray-600 px-6 py-3 font-medium text-gray-300 transition-all duration-200 hover:border-gray-500 hover:bg-gray-800"
                            aria-label="Torna indietro">
                            ← Torna indietro
                        </a>
                        <a href="{{ route('home') }}"
                            class="inline-flex items-center rounded-lg px-6 py-3 font-medium text-gray-900 transition-all duration-200"
                            style="background: linear-gradient(135deg, #D4A574 0%, #E6B885 100%);"
                            aria-label="Torna alla home">
                            Torna alla home →
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endslot

    @slot('belowHeroContent')
        <div class="container mx-auto px-6 py-16">
            <div class="mx-auto max-w-4xl text-center">
                <div class="rounded-2xl px-8 py-16" style="background: linear-gradient(135deg, #1B365D 0%, #2D4A7A 100%);">
                    <h2 class="mb-4 text-3xl font-bold text-white md:text-4xl"
                        style="font-family: 'Playfair Display', serif;">
                        Stiamo lavorando per te!
                    </h2>
                    <p class="mx-auto mb-8 max-w-2xl text-xl leading-relaxed text-gray-200">
                        {{ $subtitle ?? 'Questa sezione sarà disponibile a breve.' }} Grazie per la pazienza e la curiosità!
                    </p>
                    <a href="{{ route('home') }}"
                        class="inline-flex items-center rounded-lg px-8 py-4 text-lg font-bold text-gray-900 transition-all duration-200 hover:scale-105"
                        style="background: linear-gradient(135deg, #D4A574 0%, #E6B885 100%); box-shadow: 0 4px 12px rgba(212, 165, 116, 0.3);"
                        aria-label="Torna alla home">
                        Torna alla home
                        <span class="ml-2">→</span>
                    </a>
                </div>
            </div>
        </div>
    @endslot

    {{-- SEO e Meta Data Completo --}}
    @push('meta')
        <meta name="description"
            content="Questa sezione di FlorenceEGI è in fase di sviluppo. Stiamo lavorando per offrirti nuove funzionalità legate alla blockchain sostenibile e certificazione ambientale.">
        <meta name="keywords" content="FlorenceEGI,In costruzione,Sviluppo,Blockchain sostenibile,Coming soon">
        <meta name="robots" content="noindex, nofollow">
        <meta name="author" content="FlorenceEGI">
        <meta name="language" content="{{ app()->getLocale() }}">
        <link rel="canonical" href="{{ url()->current() }}">

        <!-- Open Graph Protocol -->
        <meta property="og:title" content="{{ $title ?? 'Pagina in costruzione' }} | FlorenceEGI">
        <meta property="og:description"
            content="Questa sezione è in fase di sviluppo. Stiamo lavorando per offrirti nuove funzionalità.">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:site_name" content="FlorenceEGI">
        <meta property="og:locale" content="{{ str_replace('_', '-', app()->getLocale()) }}">

        <!-- Twitter Card -->
        <meta name="twitter:card" content="summary">
        <meta name="twitter:title" content="{{ $title ?? 'Pagina in costruzione' }}">
        <meta name="twitter:description" content="Questa sezione è in fase di sviluppo.">
        <meta name="twitter:site" content="@FlorenceEGI">

        <!-- Schema.org Structured Data -->
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "WebPage",
            "name": "{{ $title ?? 'Pagina in costruzione' }}",
            "description": "Sezione in fase di sviluppo",
            "url": "{{ url()->current() }}",
            "isPartOf": {
                "@type": "WebSite",
                "@id": "https://florence-egi.com/#website",
                "name": "FlorenceEGI"
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
                        "name": "In costruzione",
                        "item": "{{ url()->current() }}"
                    }
                ]
            }
        }
        </script>
    @endpush

</x-guest-layout>
