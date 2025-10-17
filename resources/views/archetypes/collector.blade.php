{{--
    File: collector.blade.php
    Autore: Padmin D. Curtis
    Aggiornato il: 12 Agosto 2025
    Versione: 2.0
    Descrizione: Minisito per la figura del "Committente".
                 Questa versione ripristina la struttura e lo stile del minisito "Patron"
                 e integra la strategia semantica corretta per "Committente" e "Collezionista".
    Principi Oracode Applicati:
    - Esplicitamente Intenzionale: La struttura ora è coerente con il resto dei minisiti.
    - Semanticamente Coerente: La terminologia è stata allineata alla nuova strategia.
    - Tollerante alla Trasmissione Imperfetta: Il mio errore precedente è stato corretto, dimostrando la resilienza del nostro processo collaborativo.
--}}
<!DOCTYPE html>
<html lang="{{ config('app.locale', 'it') }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('collector.meta.title') }}</title>
    <meta name="description" content="{{ __('collector.meta.description') }}">
    <meta name="keywords" content="{{ __('collector.meta.keywords', 'Collezionista,EGI,Arte digitale,Sostenibilità,Blockchain,Certificazione ambientale') }}">
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large">
    <meta name="author" content="FlorenceEGI">
    <meta name="language" content="{{ config('app.locale', 'it') }}">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph Protocol -->
    <meta property="og:title" content="{{ __('collector.meta.title') }}">
    <meta property="og:description" content="{{ __('collector.meta.description') }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="FlorenceEGI">
    <meta property="og:locale" content="{{ str_replace('_', '-', config('app.locale', 'it')) }}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ __('collector.meta.title') }}">
    <meta name="twitter:description" content="{{ __('collector.meta.description') }}">
    <meta name="twitter:site" content="@FlorenceEGI">

    <!-- Schema.org Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebPage",
        "name": "{{ __('collector.meta.title') }}",
        "description": "{{ __('collector.meta.description') }}",
        "url": "{{ url()->current() }}",
        "inLanguage": "{{ config('app.locale', 'it') }}",
        "isPartOf": {
            "@type": "WebSite",
            "@id": "https://florence-egi.com/#website",
            "name": "FlorenceEGI"
        },
        "about": {
            "@type": "Thing",
            "name": "Collezionista Archetype",
            "description": "Figura che colleziona EGI per sostenibilità ambientale"
        }
    }
    </script>

    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Source+Sans+Pro:wght@300;400;600&display=swap"
        rel="stylesheet">
    {{-- Gli stili sono identici a patron.blade.php per coerenza visiva --}}
    <style>
        :root {
            --oro-fiorentino: #D4A574;
            --verde-rinascita: #2D5016;
            --blu-algoritmo: #1B365D;
            --grigio-pietra: #6B6B6B;
            --rosso-urgenza: #C13120;
            --arancio-energia: #E67E22;
            --viola-innovazione: #8E44AD;
            --white: #ffffff;
            --light-grey: #F8F8F8;
            --dark-grey: #333333;
        }

        body {
            font-family: 'Source Sans Pro', sans-serif;
            line-height: 1.6;
            color: var(--grigio-pietra);
            margin: 0;
            padding: 0;
            background-color: var(--white);
            overflow-x: hidden;
        }

        #app-container {
            max-width: 1200px;
            margin: 20px auto;
            background-color: var(--white);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            min-height: calc(100vh - 40px);
        }

        .sidebar {
            width: 100%;
            background-color: var(--blu-algoritmo);
            color: var(--white);
            padding: 20px;
            box-sizing: border-box;
            transform: translateX(-100%);
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            z-index: 1000;
            transition: transform 0.3s ease-in-out;
            border-radius: 0 15px 15px 0;
            overflow-y: auto;
        }

        .sidebar.open {
            transform: translateX(0);
        }

        .sidebar .logo-container {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            margin-bottom: 30px;
            padding: 0;
        }

        .sidebar .logo-container .logo {
            height: 40px;
            width: auto;
            max-width: 150px;
            object-fit: contain;
        }

        .sidebar .logo-container .logo-text {
            margin-left: 10px;
            font-size: 0.9em;
            font-weight: 600;
            color: var(--white);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar .logo-container a {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: inherit;
            transition: opacity 0.3s ease;
        }

        .sidebar .logo-container a:hover {
            opacity: 0.8;
        }

        .sidebar nav {
            position: static;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .sidebar nav a {
            color: var(--white);
            text-decoration: none;
            padding: 10px 15px;
            display: block;
            width: 100%;
            transition: background-color 0.3s ease, color 0.3s ease, border-left 0.3s ease;
            border-radius: 0;
            margin-bottom: 2px;
            border-left: 5px solid transparent;
            font-weight: 400;
        }

        .sidebar nav a:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: var(--oro-fiorentino);
            border-left-color: var(--oro-fiorentino);
        }

        .sidebar nav a.active {
            background-color: rgba(255, 255, 255, 0.15);
            font-weight: 600;
            border-left-color: var(--verde-rinascita);
            color: var(--verde-rinascita);
        }

        .hamburger {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1001;
            cursor: pointer;
            padding: 12px;
            background-color: rgba(27, 54, 93, 0.9);
            border: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            color: var(--white);
            font-size: 18px;
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .hamburger:hover {
            background-color: var(--blu-algoritmo);
            transform: scale(1.05);
        }

        .main-content {
            flex-grow: 1;
            padding-top: 60px;
            padding-left: 0;
            position: relative;
        }

        header {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)),
            url('{{ asset('images/default/collector_01.jpg') }}') no-repeat center center/cover;
            color: var(--white);
            padding: 80px 20px;
            text-align: center;
            position: relative;
            box-sizing: border-box;
        }

        .header-content {
            max-width: 800px;
            margin: 0 auto;
        }

        header .eyebrow {
            font-size: 1.1em;
            font-weight: 600;
            color: var(--oro-fiorentino);
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 2.5em;
            margin-bottom: 15px;
            color: var(--white);
        }

        header .lead {
            font-family: 'Source Sans Pro', sans-serif;
            font-size: 1.2em;
            margin-top: 0;
            color: var(--white);
            max-width: 650px;
            margin-left: auto;
            margin-right: auto;
        }

        .cta-group {
            margin-top: 40px;
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .cta-button {
            display: inline-block;
            background-color: var(--verde-rinascita);
            color: var(--white);
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: background-color 0.3s ease, transform 0.3s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            border: none;
            cursor: pointer;
        }

        .cta-button:hover {
            background-color: var(--oro-fiorentino);
            transform: translateY(-3px);
        }

        .cta-button.secondary {
             background-color: transparent;
             border: 2px solid var(--oro-fiorentino);
             color: var(--oro-fiorentino);
        }

        .cta-button.secondary:hover {
            background-color: var(--oro-fiorentino);
            color: var(--white);
        }

        .section {
            padding: 60px 20px;
            max-width: 960px;
            margin: 0 auto;
            box-sizing: border-box;
        }

        .section:nth-child(even) {
            background-color: var(--light-grey);
            border-radius: 12px;
            margin-top: 30px;
            margin-bottom: 30px;
        }

        .section h2 {
            font-family: 'Playfair Display', serif;
            font-size: 2em;
            color: var(--blu-algoritmo);
            text-align: center;
            margin-bottom: 30px;
        }

        .section h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.5em;
            color: var(--verde-rinascita);
            margin-bottom: 15px;
            text-align: center;
        }

        .text-block {
            text-align: center;
            margin-bottom: 30px;
        }

        .text-block p {
            max-width: 700px;
            margin: 15px auto;
            font-size: 1.1em;
            color: var(--dark-grey);
            line-height: 1.7;
        }

        .bullet-list {
            max-width: 600px;
            margin: 30px auto;
            text-align: left;
            padding: 0;
        }

        .bullet-list li {
            font-size: 1.1em;
            margin-bottom: 15px;
            list-style: none;
            position: relative;
            padding-left: 35px;
        }

        .bullet-list li::before {
            content: '•';
            color: var(--verde-rinascita);
            font-weight: bold;
            display: inline-block;
            position: absolute;
            left: 0;
            font-size: 2em;
            line-height: 1;
            top: -5px;
        }

        .timeline {
            position: relative;
            margin: 40px auto;
            padding: 0;
            max-width: 700px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 20px;
            top: 0;
            width: 4px;
            height: 100%;
            background-color: var(--verde-rinascita);
            border-radius: 2px;
        }

        .timeline-item {
            margin-bottom: 40px;
            position: relative;
            padding-left: 60px;
        }

        .timeline-item:last-child {
            margin-bottom: 0;
        }

        .timeline-item h4 {
            font-family: 'Playfair Display', serif;
            color: var(--blu-algoritmo);
            font-size: 1.3em;
            margin-bottom: 5px;
            text-align: left;
        }

        .timeline-item p {
            color: var(--grigio-pietra);
            font-size: 1em;
            text-align: left;
        }

        .two-column {
            display: grid;
            grid-template-columns: 1fr;
            gap: 30px;
            margin-top: 40px;
        }

        .col-item {
            background-color: var(--white);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.07);
            border-left: 4px solid var(--verde-rinascita);
        }

        .col-item h4 {
            font-family: 'Playfair Display', serif;
            color: var(--blu-algoritmo);
            text-align: left;
            margin-top: 0;
            font-size: 1.3em;
        }

        .col-item p {
            font-size: 1em;
            color: var(--dark-grey);
        }

        .note-block {
            background-color: rgba(212, 165, 116, 0.1);
            padding: 25px;
            border-radius: 12px;
            margin-top: 40px;
            border-left: 5px solid var(--oro-fiorentino);
        }

        .note-block h4 {
            margin-top: 0;
            color: var(--blu-algoritmo);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            text-align: center;
            margin-top: 30px;
        }

        .stat-item .value {
            font-family: 'Playfair Display', serif;
            font-size: 2.2em;
            color: var(--verde-rinascita);
            font-weight: bold;
        }

        .stat-item .label {
            font-size: 1em;
            color: var(--dark-grey);
            margin-top: 5px;
        }

        .community-features {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            margin-top: 30px;
        }

        .community-feature-item {
            text-align: center;
        }

        .community-feature-item strong {
            display: block;
            font-size: 1.2em;
            color: var(--blu-algoritmo);
            margin-bottom: 5px;
        }

        .accordion-container {
            margin-top: 30px;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }

        .accordion-item {
            border-bottom: 1px solid #ddd;
            margin-bottom: 10px;
            overflow: hidden;
        }

        .accordion-header {
            background: none;
            border: none;
            width: 100%;
            text-align: left;
            padding: 15px 5px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 1.1em;
            font-weight: 600;
            color: var(--dark-grey);
        }

        .accordion-header .icon {
            font-size: 1.2em;
            transition: transform 0.3s ease;
        }

        .accordion-header.active .icon {
            transform: rotate(180deg);
        }

        .accordion-content {
            padding: 0 5px;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease-out;
        }

        .accordion-content p {
            padding-bottom: 15px;
            margin: 0;
            font-size: 1em;
        }

        .final-cta-section {
            background-color: var(--blu-algoritmo);
            color: var(--white);
            padding: 60px 20px;
            text-align: center;
        }

        .final-cta-section h2 {
            color: var(--white);
            font-size: 2.2em;
        }

        .final-cta-section .lead {
            color: var(--light-grey);
            font-size: 1.2em;
            max-width: 600px;
            margin: 15px auto 30px;
        }

        footer {
            background-color: var(--dark-grey);
            color: var(--white);
            text-align: center;
            padding: 40px 20px;
            font-size: 0.9em;
        }

        footer a {
            color: var(--oro-fiorentino);
            text-decoration: none;
        }

        footer a:hover {
            text-decoration: underline;
        }

        @media (min-width: 769px) {
            #app-container {
                flex-direction: row;
            }

            .hamburger {
                display: none;
            }

            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                width: 250px;
                height: 100vh;
                padding: 30px;
                transform: translateX(0);
                border-radius: 15px 0 0 15px;
                z-index: 900;
            }

            .main-content {
                margin-left: 250px;
                padding-top: 0;
            }

            header {
                padding: 120px 20px;
            }

            header h1 {
                font-size: 3.5em;
            }

            header .lead {
                font-size: 1.4em;
            }

            .section {
                padding: 80px 20px;
            }

            .section h2 {
                font-size: 2.8em;
            }

            .two-column {
                grid-template-columns: 1fr 1fr;
            }

            .community-features {
                grid-template-columns: repeat(3, 1fr);
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>

    <div id="app-container" itemscope itemtype="https://schema.org/WebPage">

        <button class="hamburger" id="sidebar-hamburger" aria-label="Apri menu sezioni" aria-controls="sidebar-menu" aria-expanded="false">
            <i class="fas fa-bars" aria-hidden="true"></i>
        </button>

        <aside class="sidebar" id="sidebar" role="navigation" aria-label="Menu di navigazione della pagina Committente">
            <div class="logo-container">
                <a href="{{ url('/home') }}" aria-label="Torna alla home di Florence EGI">
                    <img src="{{ asset('images/logo/logo_1.webp') }}" alt="Logo Florence EGI" class="logo">
                    <span class="logo-text">FlorenceEGI</span>
                </a>
            </div>
            <nav id="sidebar-menu">
                <a href="#why" class="sidebar-link" role="menuitem">{{ __('collector.nav.why') }}</a>
                <a href="#how" class="sidebar-link" role="menuitem">{{ __('collector.nav.how') }}</a>
                <a href="#roles" class="sidebar-link" role="menuitem">{{ __('collector.nav.roles') }}</a>
                <a href="#benefits" class="sidebar-link" role="menuitem">{{ __('collector.nav.benefits') }}</a>
                <a href="#impact" class="sidebar-link" role="menuitem">{{ __('collector.nav.impact') }}</a>
                <a href="#community" class="sidebar-link" role="menuitem">{{ __('collector.nav.community') }}</a>
                <a href="#faq" class="sidebar-link" role="menuitem">{{ __('collector.nav.faq') }}</a>
            </nav>
        </aside>

        <div class="main-content" role="main">
            <header id="hero">
                <div class="header-content">
                    <div class="eyebrow">{{ __('collector.hero.eyebrow') }}</div>
                    <h1 itemprop="name">{{ __('collector.hero.title') }}</h1>
                    <p class="lead" itemprop="description">{!! __('collector.hero.lead_html') !!}</p>
                    <div class="cta-group">
                        <a href="/catalog" class="cta-button" role="button" aria-label="{{ __('collector.cta.catalog_aria') }}">{{ __('collector.cta.catalog') }}</a>
                        <a href="#how" class="cta-button secondary" role="button" aria-label="{{ __('collector.cta.how_aria') }}">{{ __('collector.cta.how') }}</a>
                    </div>
                </div>
            </header>

            <main>
                <section id="why" class="section">
                    <h2>{{ __('collector.why.title') }}</h2>
                    <div class="text-block">
                        <p>{!! __('collector.why.body_html') !!}</p>
                    </div>
                    <ul class="bullet-list">
                        <li>{!! __('collector.why.point_1') !!}</li>
                        <li>{!! __('collector.why.point_2') !!}</li>
                        <li>{!! __('collector.why.point_3') !!}</li>
                        <li>{!! __('collector.why.point_4') !!}</li>
                    </ul>
                </section>

                <section id="how" class="section">
                    <h2>{{ __('collector.how.title') }}</h2>
                    <div class="timeline">
                        <div class="timeline-item">
                            <h4>{{ __('collector.how.steps.1.title') }}</h4>
                            <p>{{ __('collector.how.steps.1.text') }}</p>
                        </div>
                        <div class="timeline-item">
                            <h4>{{ __('collector.how.steps.2.title') }}</h4>
                            <p>{{ __('collector.how.steps.2.text') }}</p>
                        </div>
                        <div class="timeline-item">
                            <h4>{{ __('collector.how.steps.3.title') }}</h4>
                            <p>{!! __('collector.how.steps.3.text') !!}</p>
                        </div>
                    </div>
                </section>

                <section id="roles" class="section">
                    <h2>{{ __('collector.roles.title') }}</h2>
                    <div class="text-block"><p>{{ __('collector.roles.intro') }}</p></div>
                    <div class="two-column">
                        <div class="col-item">
                            <h4>{{ __('collector.roles.public.title') }}</h4>
                            <p>{{ __('collector.roles.public.text') }}</p>
                        </div>
                        <div class="col-item">
                            <h4>{{ __('collector.roles.private.title') }}</h4>
                            <p>{{ __('collector.roles.private.text') }}</p>
                        </div>
                    </div>
                    <div class="note-block">
                        <h4>{{ __('collector.roles.creator_note.title') }}</h4>
                        <p>{!! __('collector.roles.creator_note.text_html') !!}</p>
                    </div>
                </section>

                <section id="benefits" class="section">
                    <h2>{{ __('collector.benefits.title') }}</h2>
                    <ul class="bullet-list">
                        @foreach(__('collector.benefits.items') as $item)
                            <li>{!! $item !!}</li>
                        @endforeach
                    </ul>
                    <div class="text-block" style="margin-top: 40px;">
                         <a href="/register" class="cta-button" role="button">{{ __('collector.cta.create_profile') }}</a>
                    </div>
                </section>

                <section id="impact" class="section">
                    <h2>{{ __('collector.impact.title') }}</h2>
                    <div class="text-block"><p>{!! __('collector.impact.lead') !!}</p></div>
                     <div class="stats-grid">
                        <div class="stat-item"><div class="value">...</div><div class="label">{{ __('collector.impact.stats.total_label') }}</div></div>
                        <div class="stat-item"><div class="value">...</div><div class="label">{{ __('collector.impact.stats.egi_label') }}</div></div>
                        <div class="stat-item"><div class="value">...</div><div class="label">{{ __('collector.impact.stats.epp_label') }}</div></div>
                    </div>
                </section>

                <section id="community" class="section">
                    <h2>{{ __('collector.community.title') }}</h2>
                    <div class="community-features text-block">
                       <p>{!! __('collector.community.like') !!}</p>
                       <p>{!! __('collector.community.follow') !!}</p>
                       <p>{!! __('collector.community.board') !!}</p>
                    </div>
                    <div class="text-block" style="margin-top: 30px;">
                        <a href="/board" class="cta-button" role="button">{{ __('collector.cta.board') }}</a>
                    </div>
                </section>

                <section id="privacy" class="section">
                     <h2>{{ __('collector.privacy.title') }}</h2>
                     <div class="text-block">
                         <p>{!! __('collector.privacy.text_html') !!}</p>
                         <p><a href="/privacy-policy">{{ __('collector.cta.policy') }}</a></p>
                     </div>
                </section>

                <section id="faq" class="section">
                    <h2>{{ __('collector.faq.title') }}</h2>
                    <div class="accordion-container" role="presentation">
                       @foreach(__('collector.faq.items') as $item)
                        <div class="accordion-item">
                            <button class="accordion-header" aria-expanded="false" role="button">
                                <span>{{ $item['q'] }}</span>
                                <i class="fas fa-chevron-down icon" aria-hidden="true"></i>
                            </button>
                            <div class="accordion-content" role="region">
                                <p>{{ $item['a'] }}</p>
                            </div>
                        </div>
                       @endforeach
                    </div>
                </section>

                <section class="final-cta-section">
                    <h2>{{ __('collector.final.title') }}</h2>
                    <p class="lead">{{ __('collector.final.lead') }}</p>
                    <div class="cta-group">
                        <a href="/catalog" class="cta-button" role="button">{{ __('collector.cta.view_catalog') }}</a>
                        <a href="/register" class="cta-button secondary" role="button">{{ __('collector.cta.create_profile_short') }}</a>
                    </div>
                </section>

            </main>
        </div>
    </div>

    @include('components.info-footer')

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sidebar = document.getElementById('sidebar');
            const hamburger = document.getElementById('sidebar-hamburger');

            hamburger.addEventListener('click', () => {
                sidebar.classList.toggle('open');
                hamburger.setAttribute('aria-expanded', sidebar.classList.contains('open'));
            });

            document.addEventListener('click', (event) => {
                if (!sidebar.contains(event.target) && !hamburger.contains(event.target) && sidebar.classList.contains('open')) {
                    sidebar.classList.remove('open');
                    hamburger.setAttribute('aria-expanded', 'false');
                }
            });

            document.querySelectorAll('.sidebar-link').forEach(link => {
                link.addEventListener('click', () => {
                    if (sidebar.classList.contains('open')) {
                        sidebar.classList.remove('open');
                        hamburger.setAttribute('aria-expanded', 'false');
                    }
                });
            });

            document.querySelectorAll('.accordion-header').forEach(header => {
                header.addEventListener('click', () => {
                    const content = header.nextElementSibling;
                    const isActive = header.classList.toggle('active');
                    header.setAttribute('aria-expanded', isActive);
                    content.style.maxHeight = isActive ? content.scrollHeight + 'px' : null;
                });
            });

            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href');
                    const targetElement = document.querySelector(targetId);
                    if (targetElement) {
                        targetElement.scrollIntoView({ behavior: 'smooth' });
                    }
                });
            });

            const sections = document.querySelectorAll('main > section[id]');
            const sidebarLinks = document.querySelectorAll('.sidebar-link');
            window.addEventListener('scroll', () => {
                let current = '';
                sections.forEach(section => {
                    const sectionTop = section.offsetTop;
                    if (pageYOffset >= sectionTop - 80) {
                        current = section.getAttribute('id');
                    }
                });
                sidebarLinks.forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('href').substring(1) === current) {
                        link.classList.add('active');
                    }
                });
            });
        });
    </script>
</body>
</html>
