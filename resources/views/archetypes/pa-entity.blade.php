<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>N.A.T.A.N. - Intelligenza Artificiale Civica per Atti Pubblici | Florence EGI</title>
    <meta name="description"
        content="N.A.T.A.N. unisce AI e Blockchain per certificare automaticamente ogni atto della PA. Trasparenza garantita, efficienza massima, conformità totale.">
    <meta name="keywords"
        content="NATAN,PA,Pubblica Amministrazione,AI,Blockchain,Certificazione,Trasparenza,Efficienza,FlorenceEGI">
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large">
    <meta name="author" content="FlorenceEGI">
    <meta name="language" content="it">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph Protocol -->
    <meta property="og:title" content="N.A.T.A.N. - AI Civica per Atti Pubblici">
    <meta property="og:description"
        content="Certificazione automatica atti PA con AI e Blockchain. Trasparenza e efficienza massima.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="FlorenceEGI">
    <meta property="og:locale" content="it_IT">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="N.A.T.A.N. - AI Civica per PA">
    <meta name="twitter:description" content="Certificazione automatica atti pubblici con AI e Blockchain.">
    <meta name="twitter:site" content="@FlorenceEGI">

    <!-- Schema.org Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "SoftwareApplication",
        "name": "N.A.T.A.N.",
        "applicationCategory": "GovernmentApplication",
        "description": "Intelligenza Artificiale Civica per certificazione automatica atti pubblici",
        "operatingSystem": "Web",
        "url": "{{ url()->current() }}",
        "author": {
            "@type": "Organization",
            "name": "FlorenceEGI",
            "url": "https://florence-egi.com"
        },
        "offers": {
            "@type": "Offer",
            "category": "Enterprise Solution for Public Administration"
        }
    }
    </script>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        'institutional-blue': '#1E3A8A',
                        'florence-gold': '#D97706',
                        'compliance-green': '#166534',
                        'ai-purple': '#7C3AED'
                    }
                }
            }
        }
    </script>

    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        .dropdown:hover .dropdown-menu {
            display: block;
        }

        .dropdown-menu {
            display: none;
        }

        html {
            scroll-behavior: smooth;
        }
    </style>
</head>

<body class="overflow-x-hidden bg-gray-50 text-gray-900">

    <!-- Back to Top Button -->
    <button id="back-to-top"
        class="fixed bottom-8 right-8 z-50 hidden rounded-full bg-florence-gold p-4 text-white shadow-2xl transition-all hover:bg-yellow-600">
        <span class="material-icons">arrow_upward</span>
    </button>

    <!-- Header Sticky -->
    <header class="bg-institutional-blue sticky top-0 z-50 py-4 text-white shadow-lg">
        <div class="container mx-auto max-w-7xl px-4 sm:px-6">
            <div class="flex items-center justify-between">
                <!-- Logo + Branding -->
                <div class="flex items-center space-x-3">
                    <span class="material-icons text-4xl">account_balance</span>
                    <div>
                        <h1 class="text-2xl font-bold">N.A.T.A.N.</h1>
                        <p class="text-xs text-blue-200">by Florence EGI</p>
                    </div>
                </div>

                <!-- Desktop Navigation -->
                <nav class="hidden items-center space-x-8 lg:flex">
                    <a href="{{ route('home') }}" class="text-sm transition hover:text-blue-200 lg:text-base">Home</a>
                    <a href="#il-sistema" class="font-medium transition hover:text-blue-200">Il Sistema</a>

                    <!-- Dropdown Vantaggi -->
                    <div class="dropdown relative">
                        <button class="flex items-center font-medium transition hover:text-blue-200">
                            Vantaggi
                            <span class="material-icons ml-1 text-sm">arrow_drop_down</span>
                        </button>
                        <div
                            class="dropdown-menu absolute left-0 top-full mt-2 w-64 rounded-lg bg-white py-2 text-gray-900 shadow-xl">
                            <a href="#certezza-trasparenza" class="block px-4 py-3 transition hover:bg-gray-100">
                                <span class="font-semibold">🔒 Certezza e Trasparenza</span>
                            </a>
                            <a href="#efficienza-operativa" class="block px-4 py-3 transition hover:bg-gray-100">
                                <span class="font-semibold">⚡ Efficienza Operativa</span>
                            </a>
                            <a href="#compliance-governance" class="block px-4 py-3 transition hover:bg-gray-100">
                                <span class="font-semibold">✅ Compliance e Governance</span>
                            </a>
                            <a href="#innovazione-organizzativa" class="block px-4 py-3 transition hover:bg-gray-100">
                                <span class="font-semibold">📈 Innovazione Organizzativa</span>
                            </a>
                            <div class="my-2 border-t border-gray-200"></div>
                            <a href="#certezza-trasparenza"
                                class="block px-4 py-3 font-semibold text-florence-gold transition hover:bg-gray-100">
                                📋 Tutti i Vantaggi
                            </a>
                        </div>
                    </div>

                    <a href="#come-funziona" class="font-medium transition hover:text-blue-200">Come Funziona</a>
                    <a href="#contatti"
                        class="rounded-full bg-florence-gold px-6 py-2 font-bold transition hover:bg-yellow-600">Contatti</a>
                </nav>

                <!-- Mobile Menu Button -->
                <button id="mobile-menu-button" class="rounded-md p-2 transition hover:bg-blue-700 lg:hidden">
                    <span class="material-icons text-3xl">menu</span>
                </button>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="mt-4 hidden border-t border-blue-600 pb-4 pt-4 lg:hidden">
                <div class="space-y-3">
                    <a href="#home" class="block rounded-md px-4 py-2 transition hover:bg-blue-700">🏠 Home</a>
                    <a href="#il-sistema" class="block rounded-md px-4 py-2 transition hover:bg-blue-700">🎯 Il
                        Sistema</a>
                    <div class="my-2 border-t border-blue-600"></div>
                    <p class="px-4 text-sm font-semibold text-blue-300">VANTAGGI</p>
                    <a href="#certezza-trasparenza"
                        class="block rounded-md px-6 py-2 text-sm transition hover:bg-blue-700">🔒 Certezza e
                        Trasparenza</a>
                    <a href="#efficienza-operativa"
                        class="block rounded-md px-6 py-2 text-sm transition hover:bg-blue-700">⚡ Efficienza
                        Operativa</a>
                    <a href="#compliance-governance"
                        class="block rounded-md px-6 py-2 text-sm transition hover:bg-blue-700">✅ Compliance e
                        Governance</a>
                    <a href="#innovazione-organizzativa"
                        class="block rounded-md px-6 py-2 text-sm transition hover:bg-blue-700">📈 Innovazione
                        Organizzativa</a>
                    <div class="my-2 border-t border-blue-600"></div>
                    <a href="#come-funziona" class="block rounded-md px-4 py-2 transition hover:bg-blue-700">⚙️ Come
                        Funziona</a>
                    <a href="#contatti"
                        class="block rounded-md bg-florence-gold px-4 py-2 font-bold transition hover:bg-yellow-600">📧
                        Contatti</a>
                </div>
            </div>
        </div>
    </header>

    <!-- HERO SECTION -->
    <section id="home" class="from-institutional-blue bg-gradient-to-br via-blue-800 to-blue-900 py-20 md:py-32">
        <div class="container mx-auto max-w-6xl px-6 text-center text-white">

            <span
                class="fade-in-up mb-6 inline-block rounded-full bg-florence-gold px-5 py-2 text-sm font-bold text-white md:text-base">
                🤖 N.A.T.A.N. - Intelligenza Artificiale Civica
            </span>

            <h1 class="fade-in-up mb-6 text-4xl font-extrabold leading-tight md:text-6xl"
                style="animation-delay: 0.1s;">
                Ogni Atto Certificato,<br>
                Ogni Dubbio Eliminato
            </h1>

            <p class="fade-in-up mx-auto mb-10 max-w-4xl text-lg leading-relaxed text-blue-100 md:text-2xl"
                style="animation-delay: 0.2s;">
                <strong>N.A.T.A.N.</strong> trasforma la pubblicazione degli atti in un processo
                automatico, trasparente e a prova di contestazione.
                Intelligenza Artificiale + Blockchain per la prima vera trasparenza amministrativa.
            </p>

            <div class="fade-in-up flex flex-col justify-center gap-4 md:flex-row" style="animation-delay: 0.3s;">
                <a href="#come-funziona"
                    class="inline-flex items-center justify-center rounded-full bg-florence-gold px-8 py-4 font-bold text-white shadow-lg transition-all hover:bg-yellow-600 hover:shadow-xl">
                    <span class="material-icons mr-2">play_circle</span>
                    Scopri Come Funziona
                </a>
                <a href="#contatti"
                    class="text-institutional-blue inline-flex items-center justify-center rounded-full bg-white px-8 py-4 font-bold shadow-lg transition-all hover:bg-gray-100 hover:shadow-xl">
                    <span class="material-icons mr-2">calendar_today</span>
                    Richiedi Demo Gratuita
                </a>
            </div>

        </div>
    </section>

    <!-- COS'È N.A.T.A.N. -->
    <section id="il-sistema" class="bg-white py-20">
        <div class="container mx-auto max-w-6xl px-6">

            <div class="mb-16 text-center">
                <h2 class="mb-6 text-4xl font-bold text-gray-900 md:text-5xl">
                    Cos'è N.A.T.A.N.?
                </h2>
                <p class="mx-auto max-w-4xl text-xl leading-relaxed text-gray-600">
                    <strong class="text-ai-purple">N.A.T.A.N.</strong> (Nodo di Analisi e Tracciamento Atti
                    Notarizzati)
                    è il primo sistema italiano che unisce <strong>Intelligenza Artificiale</strong>
                    e <strong>Blockchain</strong> per certificare e catalogare automaticamente
                    ogni atto della Pubblica Amministrazione.
                </p>
            </div>

            <!-- Diagramma Flusso SVG -->
            <div class="mb-16 rounded-2xl bg-gradient-to-br from-blue-50 to-purple-50 p-8 shadow-lg md:p-12">
                <h3 class="mb-10 text-center text-2xl font-bold text-gray-900 md:text-3xl">Il Flusso N.A.T.A.N.</h3>

                <svg viewBox="0 0 1200 300" class="h-auto w-full" xmlns="http://www.w3.org/2000/svg">
                    <!-- Documento Firmato -->
                    <g>
                        <rect x="20" y="80" width="140" height="140" rx="10" fill="#1E3A8A" />
                        <text x="90" y="130" fill="white" font-size="40" text-anchor="middle">📄</text>
                        <text x="90" y="170" fill="white" font-size="14" font-weight="bold"
                            text-anchor="middle">Documento</text>
                        <text x="90" y="190" fill="white" font-size="14" font-weight="bold"
                            text-anchor="middle">Firmato</text>
                    </g>

                    <!-- Arrow -->
                    <path d="M 160 150 L 210 150" stroke="#D97706" stroke-width="4" fill="none"
                        marker-end="url(#arrowhead)" />

                    <!-- Upload -->
                    <g>
                        <rect x="210" y="80" width="140" height="140" rx="10" fill="#7C3AED" />
                        <text x="280" y="130" fill="white" font-size="40" text-anchor="middle">⬆️</text>
                        <text x="280" y="170" fill="white" font-size="14" font-weight="bold"
                            text-anchor="middle">Upload</text>
                        <text x="280" y="190" fill="white" font-size="14" font-weight="bold"
                            text-anchor="middle">N.A.T.A.N.</text>
                    </g>

                    <!-- Arrow -->
                    <path d="M 350 150 L 400 150" stroke="#D97706" stroke-width="4" fill="none"
                        marker-end="url(#arrowhead)" />

                    <!-- AI Parsing -->
                    <g>
                        <rect x="400" y="80" width="140" height="140" rx="10" fill="#10B981" />
                        <text x="470" y="130" fill="white" font-size="40" text-anchor="middle">🤖</text>
                        <text x="470" y="170" fill="white" font-size="14" font-weight="bold"
                            text-anchor="middle">AI Parsing</text>
                        <text x="470" y="190" fill="white" font-size="14" font-weight="bold"
                            text-anchor="middle">Automatico</text>
                    </g>

                    <!-- Arrow -->
                    <path d="M 540 150 L 590 150" stroke="#D97706" stroke-width="4" fill="none"
                        marker-end="url(#arrowhead)" />

                    <!-- Metadata JSON -->
                    <g>
                        <rect x="590" y="80" width="140" height="140" rx="10" fill="#F59E0B" />
                        <text x="660" y="130" fill="white" font-size="40" text-anchor="middle">📋</text>
                        <text x="660" y="170" fill="white" font-size="14" font-weight="bold"
                            text-anchor="middle">Metadata</text>
                        <text x="660" y="190" fill="white" font-size="14" font-weight="bold"
                            text-anchor="middle">Strutturati</text>
                    </g>

                    <!-- Arrow -->
                    <path d="M 730 150 L 780 150" stroke="#D97706" stroke-width="4" fill="none"
                        marker-end="url(#arrowhead)" />

                    <!-- Hash + Blockchain -->
                    <g>
                        <rect x="780" y="80" width="140" height="140" rx="10" fill="#DC2626" />
                        <text x="850" y="130" fill="white" font-size="40" text-anchor="middle">🔗</text>
                        <text x="850" y="170" fill="white" font-size="14" font-weight="bold"
                            text-anchor="middle">Blockchain</text>
                        <text x="850" y="190" fill="white" font-size="14" font-weight="bold"
                            text-anchor="middle">Algorand</text>
                    </g>

                    <!-- Arrow -->
                    <path d="M 920 150 L 970 150" stroke="#D97706" stroke-width="4" fill="none"
                        marker-end="url(#arrowhead)" />

                    <!-- Dashboard + QR -->
                    <g>
                        <rect x="970" y="80" width="140" height="140" rx="10" fill="#059669" />
                        <text x="1040" y="130" fill="white" font-size="40" text-anchor="middle">📊</text>
                        <text x="1040" y="170" fill="white" font-size="14" font-weight="bold"
                            text-anchor="middle">Dashboard</text>
                        <text x="1040" y="190" fill="white" font-size="14" font-weight="bold"
                            text-anchor="middle">+ QR Pubblico</text>
                    </g>

                    <!-- Arrow marker definition -->
                    <defs>
                        <marker id="arrowhead" markerWidth="10" markerHeight="10" refX="8" refY="3"
                            orient="auto">
                            <polygon points="0 0, 10 3, 0 6" fill="#D97706" />
                        </marker>
                    </defs>
                </svg>
            </div>

            <!-- 3 Pillars -->
            <div class="grid gap-8 md:grid-cols-3">

                <div
                    class="rounded-2xl bg-gradient-to-br from-purple-50 to-blue-50 p-8 text-center shadow-md transition-shadow hover:shadow-xl">
                    <span class="mb-6 block text-6xl">🤖</span>
                    <h3 class="text-ai-purple mb-4 text-2xl font-bold">Intelligenza Artificiale</h3>
                    <p class="leading-relaxed text-gray-600">
                        Legge e cataloga automaticamente ogni atto: tipo, oggetto,
                        responsabile, importo. Zero data-entry manuale.
                    </p>
                </div>

                <div
                    class="rounded-2xl bg-gradient-to-br from-green-50 to-emerald-50 p-8 text-center shadow-md transition-shadow hover:shadow-xl">
                    <span class="mb-6 block text-6xl">🔗</span>
                    <h3 class="text-compliance-green mb-4 text-2xl font-bold">Blockchain Certificazione</h3>
                    <p class="leading-relaxed text-gray-600">
                        Ogni atto registrato su Algorand: prova immutabile
                        di autenticità, accessibile a chiunque tramite QR Code.
                    </p>
                </div>

                <div
                    class="rounded-2xl bg-gradient-to-br from-yellow-50 to-orange-50 p-8 text-center shadow-md transition-shadow hover:shadow-xl">
                    <span class="mb-6 block text-6xl">🔒</span>
                    <h3 class="mb-4 text-2xl font-bold text-florence-gold">GDPR Compliance</h3>
                    <p class="leading-relaxed text-gray-600">
                        Dati minimizzati, processing EU-hosted, audit trail completo.
                        Privacy by design, trasparenza by default.
                    </p>
                </div>

            </div>

        </div>
    </section>
    <!-- CATEGORIA A: CERTEZZA E TRASPARENZA -->
    <section id="certezza-trasparenza" class="bg-gradient-to-br from-blue-50 to-indigo-50 py-20">
        <div class="container mx-auto max-w-6xl px-6">

            <div class="mb-16 text-center">
                <span class="bg-institutional-blue mb-4 inline-block rounded-full px-6 py-2 font-bold text-white">
                    🔒 CATEGORIA A
                </span>
                <h2 class="mb-4 text-4xl font-bold text-gray-900 md:text-5xl">
                    Certezza e Trasparenza
                </h2>
                <p class="mx-auto max-w-3xl text-xl text-gray-600">
                    Atti a prova di dubbio, trasparenza tangibile
                </p>
            </div>

            <div class="grid gap-8 md:grid-cols-2">

                <!-- Sfida 1 -->
                <div class="rounded-2xl bg-white p-8 shadow-lg transition-shadow hover:shadow-2xl">
                    <h3 class="mb-6 flex items-center text-2xl font-bold text-gray-900">
                        <span class="material-icons text-institutional-blue mr-3 text-3xl">verified</span>
                        1. Garantire l'Autenticità
                    </h3>
                    <div class="mb-4">
                        <p class="mb-2 font-semibold text-red-700">❌ Problema:</p>
                        <p class="text-gray-600">Circolano PDF modificati o versioni "alternative" di atti ufficiali.
                        </p>
                    </div>
                    <div class="mb-4">
                        <p class="mb-2 font-semibold text-orange-600">😰 Disagio Percepito:</p>
                        <p class="text-gray-600">PEC e contestazioni continue, con perdita di tempo e credibilità.</p>
                    </div>
                    <div class="rounded-lg bg-green-50 p-4">
                        <p class="text-compliance-green mb-2 font-semibold">✅ Soluzione N.A.T.A.N.:</p>
                        <p class="text-gray-700"><strong>N.A.T.A.N.</strong> genera automaticamente un'impronta
                            crittografica del documento post-firma e la registra su blockchain Algorand. Il QR Code
                            prodotto permette a chiunque di verificare in 1 click che il PDF corrisponde esattamente
                            all'originale certificato. Zero modifiche possibili, zero dubbi.</p>
                    </div>
                </div>

                <!-- Sfida 2 -->
                <div class="rounded-2xl bg-white p-8 shadow-lg transition-shadow hover:shadow-2xl">
                    <h3 class="mb-6 flex items-center text-2xl font-bold text-gray-900">
                        <span class="material-icons text-institutional-blue mr-3 text-3xl">visibility</span>
                        2. Rendere la Trasparenza "Visibile"
                    </h3>
                    <div class="mb-4">
                        <p class="mb-2 font-semibold text-red-700">❌ Problema:</p>
                        <p class="text-gray-600">L'Albo Pretorio mostra i file, ma non dà una prova immediata di
                            autenticità.</p>
                    </div>
                    <div class="mb-4">
                        <p class="mb-2 font-semibold text-orange-600">😰 Disagio Percepito:</p>
                        <p class="text-gray-600">Pressione mediatica e politica; fiducia dei cittadini fragile.</p>
                    </div>
                    <div class="rounded-lg bg-green-50 p-4">
                        <p class="text-compliance-green mb-2 font-semibold">✅ Soluzione N.A.T.A.N.:</p>
                        <p class="text-gray-700"><strong>N.A.T.A.N.</strong> crea automaticamente una scheda di
                            certificazione pubblica per ogni atto, accessibile tramite QR Code. La pagina mostra: ente,
                            tipo atto, oggetto, data, hash blockchain e transazione Algorand. Il cittadino vede
                            immediatamente che l'atto è certificato e immutabile. Trasparenza tangibile, non promessa.
                        </p>
                    </div>
                </div>

                <!-- Sfida 3 -->
                <div class="rounded-2xl bg-white p-8 shadow-lg transition-shadow hover:shadow-2xl">
                    <h3 class="mb-6 flex items-center text-2xl font-bold text-gray-900">
                        <span class="material-icons text-institutional-blue mr-3 text-3xl">search</span>
                        3. Trovare l'Atto Giusto, Subito
                    </h3>
                    <div class="mb-4">
                        <p class="mb-2 font-semibold text-red-700">❌ Problema:</p>
                        <p class="text-gray-600">È difficile e lento trovare subito l'atto corretto o la sua ultima
                            versione.</p>
                    </div>
                    <div class="mb-4">
                        <p class="mb-2 font-semibold text-orange-600">😰 Disagio Percepito:</p>
                        <p class="text-gray-600">URP sommerso di richieste; dirigenti coinvolti per dare chiarimenti.
                        </p>
                    </div>
                    <div class="rounded-lg bg-green-50 p-4">
                        <p class="text-compliance-green mb-2 font-semibold">✅ Soluzione N.A.T.A.N.:</p>
                        <p class="text-gray-700"><strong>N.A.T.A.N.</strong> indicizza semanticamente ogni atto: tipo,
                            oggetto, responsabile, importo, categoria. La dashboard PA permette ricerche full-text e
                            filtri avanzati (es: "tutti i bandi 2025 sopra 50k€"). Il QR Code porta direttamente
                            all'atto specifico. Cittadini e dipendenti trovano ciò che cercano in secondi, non ore.</p>
                    </div>
                </div>

                <!-- Sfida 4 -->
                <div class="rounded-2xl bg-white p-8 shadow-lg transition-shadow hover:shadow-2xl">
                    <h3 class="mb-6 flex items-center text-2xl font-bold text-gray-900">
                        <span class="material-icons text-institutional-blue mr-3 text-3xl">history</span>
                        4. Gestire Rettifiche e Revoche
                    </h3>
                    <div class="mb-4">
                        <p class="mb-2 font-semibold text-red-700">❌ Problema:</p>
                        <p class="text-gray-600">Non si capisce cosa è cambiato tra una versione e l'altra, e quando.
                        </p>
                    </div>
                    <div class="mb-4">
                        <p class="mb-2 font-semibold text-orange-600">😰 Disagio Percepito:</p>
                        <p class="text-gray-600">Malintesi interni ed esterni, con rischio di accuse di scarsa
                            trasparenza.</p>
                    </div>
                    <div class="rounded-lg bg-green-50 p-4">
                        <p class="text-compliance-green mb-2 font-semibold">✅ Soluzione N.A.T.A.N.:</p>
                        <p class="text-gray-700"><strong>N.A.T.A.N.</strong> crea automaticamente un grafo di versioni:
                            ogni rettifica genera un nuovo record collegato al precedente, con timestamp, responsabile
                            della modifica e hash blockchain univoco. La pagina pubblica mostra lo storico completo:
                            "Versione 1 → Rettificata il 15/03 → Versione 2 (corrente)". Trasparenza totale su ogni
                            modifica.</p>
                    </div>
                </div>

                <!-- Sfida 9 -->
                <div class="rounded-2xl bg-white p-8 shadow-lg transition-shadow hover:shadow-2xl">
                    <h3 class="mb-6 flex items-center text-2xl font-bold text-gray-900">
                        <span class="material-icons text-institutional-blue mr-3 text-3xl">badge</span>
                        5. Tracciare le Copie Ufficiali
                    </h3>
                    <div class="mb-4">
                        <p class="mb-2 font-semibold text-red-700">❌ Problema:</p>
                        <p class="text-gray-600">Attestati e permessi sono difficili da tracciare una volta emessi.</p>
                    </div>
                    <div class="mb-4">
                        <p class="mb-2 font-semibold text-orange-600">😰 Disagio Percepito:</p>
                        <p class="text-gray-600">Confusione su copie valide e revocate, con alti rischi di abuso.</p>
                    </div>
                    <div class="rounded-lg bg-green-50 p-4">
                        <p class="text-compliance-green mb-2 font-semibold">✅ Soluzione N.A.T.A.N.:</p>
                        <p class="text-gray-700"><strong>N.A.T.A.N.</strong> genera certificati numerati univoci: ogni
                            copia ufficiale rilasciata ha un QR Code specifico collegato al documento madre. Il sistema
                            traccia chi ha richiesto la copia, quando, e il suo stato (valida/revocata). In caso di
                            revoca (es: permesso annullato), il QR Code mostra immediatamente "COPIA REVOCATA - Non più
                            valida". Controlli incrociati automatici per forze dell'ordine o enti terzi.</p>
                    </div>
                </div>

            </div>

        </div>
    </section>
    <!-- CATEGORIA B: EFFICIENZA OPERATIVA -->
    <section id="efficienza-operativa" class="bg-white py-20">
        <div class="container mx-auto max-w-6xl px-6">

            <div class="mb-16 text-center">
                <span class="mb-4 inline-block rounded-full bg-yellow-500 px-6 py-2 font-bold text-white">
                    ⚡ CATEGORIA B
                </span>
                <h2 class="mb-4 text-4xl font-bold text-gray-900 md:text-5xl">
                    Efficienza Operativa
                </h2>
                <p class="mx-auto max-w-3xl text-xl text-gray-600">
                    Meno burocrazia, più valore pubblico
                </p>
            </div>

            <div class="grid gap-8 md:grid-cols-2">

                <!-- Sfida 5 -->
                <div
                    class="rounded-2xl border-2 border-gray-100 bg-white p-8 shadow-lg transition-shadow hover:shadow-2xl">
                    <h3 class="mb-6 flex items-center text-2xl font-bold text-gray-900">
                        <span class="material-icons mr-3 text-3xl text-yellow-600">flash_on</span>
                        1. Ottimizzare il Tempo Operativo
                    </h3>
                    <div class="mb-4">
                        <p class="mb-2 font-semibold text-red-700">❌ Problema:</p>
                        <p class="text-gray-600">Poco personale e troppi micro-adempimenti dopo la pubblicazione degli
                            atti.</p>
                    </div>
                    <div class="mb-4">
                        <p class="mb-2 font-semibold text-orange-600">😰 Disagio Percepito:</p>
                        <p class="text-gray-600">Ritardi, stress e arretrati che si accumulano inesorabilmente.</p>
                    </div>
                    <div class="rounded-lg bg-green-50 p-4">
                        <p class="text-compliance-green mb-2 font-semibold">✅ Soluzione N.A.T.A.N.:</p>
                        <p class="text-gray-700"><strong>N.A.T.A.N.</strong> automatizza l'intero processo post-firma:
                            upload del PDF, AI parsing per estrarre metadata (tipo atto, oggetto, importo), generazione
                            hash, registrazione blockchain e creazione QR. L'operatore carica il file, N.A.T.A.N. fa il
                            resto in 10-15 secondi. Zero catalogazione manuale, zero errori di trascrizione. Il
                            personale si concentra su attività a valore aggiunto.</p>
                    </div>
                </div>

                <!-- Sfida 10 -->
                <div
                    class="rounded-2xl border-2 border-gray-100 bg-white p-8 shadow-lg transition-shadow hover:shadow-2xl">
                    <h3 class="mb-6 flex items-center text-2xl font-bold text-gray-900">
                        <span class="material-icons mr-3 text-3xl text-yellow-600">rocket_launch</span>
                        2. Partire Subito con Poche Risorse
                    </h3>
                    <div class="mb-4">
                        <p class="mb-2 font-semibold text-red-700">❌ Problema:</p>
                        <p class="text-gray-600">Poche risorse economiche e poca tolleranza per progetti lunghi e
                            complessi.</p>
                    </div>
                    <div class="mb-4">
                        <p class="mb-2 font-semibold text-orange-600">😰 Disagio Percepito:</p>
                        <p class="text-gray-600">Buone iniziative che non partono mai, con la pressione a "fare presto
                            e bene".</p>
                    </div>
                    <div class="rounded-lg bg-green-50 p-4">
                        <p class="text-compliance-green mb-2 font-semibold">✅ Soluzione N.A.T.A.N.:</p>
                        <p class="text-gray-700"><strong>N.A.T.A.N.</strong> è operativo in 48 ore: setup account PA,
                            formazione operatori (2 ore), primo atto certificato. Zero hardware da acquistare, zero
                            software da installare. Costi pilot irrisori (€200-400 per 8 settimane su 200 atti).
                            Quick-win immediato: dopo 1 settimana l'ente può già mostrare atti certificati blockchain.
                            Investimento minimo, impatto massimo. Approccio pilota → valutazione → scale.</p>
                    </div>
                </div>

                <!-- Sfida 11 -->
                <div
                    class="rounded-2xl border-2 border-gray-100 bg-white p-8 shadow-lg transition-shadow hover:shadow-2xl">
                    <h3 class="mb-6 flex items-center text-2xl font-bold text-gray-900">
                        <span class="material-icons mr-3 text-3xl text-yellow-600">folder_special</span>
                        3. Classificare e Organizzare Automaticamente
                    </h3>
                    <div class="mb-4">
                        <p class="mb-2 font-semibold text-red-700">❌ Problema:</p>
                        <p class="text-gray-600">Gli atti vengono salvati in cartelle generiche o con naming
                            inconsistente. Trovare documenti passati richiede ricerche manuali in archivi
                            disorganizzati.</p>
                    </div>
                    <div class="mb-4">
                        <p class="mb-2 font-semibold text-orange-600">😰 Disagio Percepito:</p>
                        <p class="text-gray-600">Perdite di tempo per recuperare atti precedenti. Frustrazione nel
                            personale che "sa che esiste ma non lo trova".</p>
                    </div>
                    <div class="rounded-lg bg-green-50 p-4">
                        <p class="text-compliance-green mb-2 font-semibold">✅ Soluzione N.A.T.A.N.:</p>
                        <p class="text-gray-700"><strong>N.A.T.A.N.</strong> classifica automaticamente ogni atto
                            durante il parsing AI: estrae tipo, categoria tematica (urbanistica, bilancio, personale),
                            responsabile, ufficio. Crea un archivio semantico interrogabile: "mostrami tutte le
                            determine dirigenziali su appalti verdi degli ultimi 3 anni". Ricerca istantanea su migliaia
                            di atti. Organizzazione automatica, zero effort manuale.</p>
                    </div>
                </div>

                <!-- Sfida 12 -->
                <div
                    class="rounded-2xl border-2 border-gray-100 bg-white p-8 shadow-lg transition-shadow hover:shadow-2xl">
                    <h3 class="mb-6 flex items-center text-2xl font-bold text-gray-900">
                        <span class="material-icons mr-3 text-3xl text-yellow-600">query_stats</span>
                        4. Rispondere Rapidamente ad Accessi FOIA
                    </h3>
                    <div class="mb-4">
                        <p class="mb-2 font-semibold text-red-700">❌ Problema:</p>
                        <p class="text-gray-600">Richieste FOIA o accessi agli atti richiedono ricerche manuali lunghe.
                            Il personale deve setacciare archivi e preparare documentazione.</p>
                    </div>
                    <div class="mb-4">
                        <p class="mb-2 font-semibold text-orange-600">😰 Disagio Percepito:</p>
                        <p class="text-gray-600">Ritardi nelle risposte (con rischio sanzioni). Carico di lavoro
                            URP/ufficio legale. Cittadini insoddisfatti.</p>
                    </div>
                    <div class="rounded-lg bg-green-50 p-4">
                        <p class="text-compliance-green mb-2 font-semibold">✅ Soluzione N.A.T.A.N.:</p>
                        <p class="text-gray-700"><strong>N.A.T.A.N.</strong> permette ricerche granulari immediate:
                            l'operatore filtra per periodo, tipo atto, keyword e ottiene lista completa in secondi. I
                            metadata estratti dall'AI mostrano subito oggetto e responsabile, facilitando valutazione di
                            rilevanza. Export massivo in PDF zip per risposta FOIA. Tempo medio di risposta ridotto da
                            giorni a ore.</p>
                    </div>
                </div>

                <!-- Sfida 17 -->
                <div
                    class="rounded-2xl border-2 border-gray-100 bg-white p-8 shadow-lg transition-shadow hover:shadow-2xl">
                    <h3 class="mb-6 flex items-center text-2xl font-bold text-gray-900">
                        <span class="material-icons mr-3 text-3xl text-yellow-600">school</span>
                        5. Onboarding Rapido Nuovo Personale
                    </h3>
                    <div class="mb-4">
                        <p class="mb-2 font-semibold text-red-700">❌ Problema:</p>
                        <p class="text-gray-600">Nuovo personale impiega mesi per capire "cosa fa l'ente", quali
                            delibere sono in vigore, quale storico decisionale.</p>
                    </div>
                    <div class="mb-4">
                        <p class="mb-2 font-semibold text-orange-600">😰 Disagio Percepito:</p>
                        <p class="text-gray-600">Produttività ridotta prime settimane. Errori per mancanza contesto.
                            Dipendenza da colleghi senior. Turnover con perdita conoscenza.</p>
                    </div>
                    <div class="rounded-lg bg-green-50 p-4">
                        <p class="text-compliance-green mb-2 font-semibold">✅ Soluzione N.A.T.A.N.:</p>
                        <p class="text-gray-700"><strong>N.A.T.A.N.</strong> funge da "memoria istituzionale digitale":
                            nuovo dirigente può cercare "tutte delibere mia area ultimi 5 anni" e avere panorama
                            completo in minuti. Ricerca semantica permette query tipo "come abbiamo gestito emergenze
                            neve passate?". Onboarding accelerato, autonomia rapida. Conoscenza istituzionale preservata
                            e accessibile.</p>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- CATEGORIA C: COMPLIANCE E GOVERNANCE -->
    <section id="compliance-governance" class="bg-gradient-to-br from-green-50 to-emerald-50 py-20">
        <div class="container mx-auto max-w-6xl px-6">

            <div class="mb-16 text-center">
                <span class="bg-compliance-green mb-4 inline-block rounded-full px-6 py-2 font-bold text-white">
                    ✅ CATEGORIA C
                </span>
                <h2 class="mb-4 text-4xl font-bold text-gray-900 md:text-5xl">
                    Compliance e Governance
                </h2>
                <p class="mx-auto max-w-3xl text-xl text-gray-600">
                    Conformità garantita, audit senza stress
                </p>
            </div>

            <div class="grid gap-8 md:grid-cols-2">

                <!-- Sfida 6 -->
                <div class="rounded-2xl bg-white p-8 shadow-lg transition-shadow hover:shadow-2xl">
                    <h3 class="mb-6 flex items-center text-2xl font-bold text-gray-900">
                        <span class="material-icons text-compliance-green mr-3 text-3xl">shield</span>
                        1. Lavorare in Piena Conformità
                    </h3>
                    <div class="mb-4">
                        <p class="mb-2 font-semibold text-red-700">❌ Problema:</p>
                        <p class="text-gray-600">Timore costante di commettere errori su privacy (GDPR) e validità
                            legale (CAD).</p>
                    </div>
                    <div class="mb-4">
                        <p class="mb-2 font-semibold text-orange-600">😰 Disagio Percepito:</p>
                        <p class="text-gray-600">Blocchi decisionali, paura di esporre dati sensibili o di sbagliare
                            procedure.</p>
                    </div>
                    <div class="rounded-lg bg-green-50 p-4">
                        <p class="text-compliance-green mb-2 font-semibold">✅ Soluzione N.A.T.A.N.:</p>
                        <p class="text-gray-700"><strong>N.A.T.A.N.</strong> è GDPR-by-design: l'AI legge il documento
                            solo per estrarre metadata essenziali (tipo atto, oggetto, importo), mai dati personali
                            sensibili. Sulla blockchain finisce solo l'hash crittografico, non il contenuto. Ogni azione
                            è loggata (chi, quando, cosa) per audit trail completo. Il DPO dell'ente può verificare
                            compliance in tempo reale tramite dashboard dedicata.</p>
                    </div>
                </div>

                <!-- Sfida 7 -->
                <div class="rounded-2xl bg-white p-8 shadow-lg transition-shadow hover:shadow-2xl">
                    <h3 class="mb-6 flex items-center text-2xl font-bold text-gray-900">
                        <span class="material-icons text-compliance-green mr-3 text-3xl">lock_open</span>
                        2. Evitare il "Lock-in" Tecnologico
                    </h3>
                    <div class="mb-4">
                        <p class="mb-2 font-semibold text-red-700">❌ Problema:</p>
                        <p class="text-gray-600">Cambiare fornitore o piattaforma informatica spesso significa perdere
                            le "prove" di validità.</p>
                    </div>
                    <div class="mb-4">
                        <p class="mb-2 font-semibold text-orange-600">😰 Disagio Percepito:</p>
                        <p class="text-gray-600">Dipendenza da un unico vendor, con il rischio di dover rifare tutto da
                            capo.</p>
                    </div>
                    <div class="rounded-lg bg-green-50 p-4">
                        <p class="text-compliance-green mb-2 font-semibold">✅ Soluzione N.A.T.A.N.:</p>
                        <p class="text-gray-700"><strong>N.A.T.A.N.</strong> registra ogni atto su blockchain Algorand
                            pubblica: la prova di autenticità esiste indipendentemente dalla piattaforma FlorenceEGI.
                            Anche se l'ente cambia gestionale o fornitore, i QR Code e le transazioni blockchain
                            rimangono validi per sempre. Export completo dei metadata in JSON/CSV disponibile sempre.
                            Zero lock-in, massima portabilità.</p>
                    </div>
                </div>

                <!-- Sfida 8 -->
                <div class="rounded-2xl bg-white p-8 shadow-lg transition-shadow hover:shadow-2xl">
                    <h3 class="mb-6 flex items-center text-2xl font-bold text-gray-900">
                        <span class="material-icons text-compliance-green mr-3 text-3xl">analytics</span>
                        3. Misurare Efficienza e Risultati
                    </h3>
                    <div class="mb-4">
                        <p class="mb-2 font-semibold text-red-700">❌ Problema:</p>
                        <p class="text-gray-600">Mancano numeri chiari da portare in report e audit per dimostrare
                            l'efficienza.</p>
                    </div>
                    <div class="mb-4">
                        <p class="mb-2 font-semibold text-orange-600">😰 Disagio Percepito:</p>
                        <p class="text-gray-600">Non si riesce a dimostrare i miglioramenti ottenuti o i tempi reali di
                            gestione.</p>
                    </div>
                    <div class="rounded-lg bg-green-50 p-4">
                        <p class="text-compliance-green mb-2 font-semibold">✅ Soluzione N.A.T.A.N.:</p>
                        <p class="text-gray-700"><strong>N.A.T.A.N.</strong> offre analytics in tempo reale: atti
                            pubblicati per periodo, tempo medio upload→certificazione, distribuzione per categoria, top
                            responsabili per volume. Grafici interattivi e export Excel per report dirigenziali. Il
                            Segretario Generale può dimostrare con numeri precisi l'efficienza della macchina
                            amministrativa. Performance management basato su dati, non percezioni.</p>
                    </div>
                </div>

                <!-- Sfida 15 -->
                <div class="rounded-2xl bg-white p-8 shadow-lg transition-shadow hover:shadow-2xl">
                    <h3 class="mb-6 flex items-center text-2xl font-bold text-gray-900">
                        <span class="material-icons text-compliance-green mr-3 text-3xl">gavel</span>
                        4. Dimostrare Compliance a Enti Sovraordinati
                    </h3>
                    <div class="mb-4">
                        <p class="mb-2 font-semibold text-red-700">❌ Problema:</p>
                        <p class="text-gray-600">Corte dei Conti, ANAC, Prefettura richiedono evidenze su procedure
                            seguite, tempi di pubblicazione, regolarità atti. Preparare documentazione per audit è
                            oneroso.</p>
                    </div>
                    <div class="mb-4">
                        <p class="mb-2 font-semibold text-orange-600">😰 Disagio Percepito:</p>
                        <p class="text-gray-600">Ansia pre-audit. Rischio rilievi per documentazione incompleta.
                            Settimane di lavoro per preparare materiali. Possibili sanzioni.</p>
                    </div>
                    <div class="rounded-lg bg-green-50 p-4">
                        <p class="text-compliance-green mb-2 font-semibold">✅ Soluzione N.A.T.A.N.:</p>
                        <p class="text-gray-700"><strong>N.A.T.A.N.</strong> genera automaticamente report
                            compliance-ready: "tutti gli atti 2025 con tempi pubblicazione Albo Pretorio, responsabili,
                            CIG/CUP". Audit trail completo e immutabile (blockchain-backed). Export formattati per
                            specifici enti (es: tracciato ANAC). Un click, documentazione pronta. Audit da stress a
                            formalità.</p>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- CATEGORIA D: INNOVAZIONE ORGANIZZATIVA -->
    <section id="innovazione-organizzativa" class="bg-gradient-to-br from-purple-50 to-pink-50 py-20">
        <div class="container mx-auto max-w-6xl px-6">

            <div class="mb-16 text-center">
                <span class="bg-ai-purple mb-4 inline-block rounded-full px-6 py-2 font-bold text-white">
                    📈 CATEGORIA D
                </span>
                <h2 class="mb-4 text-4xl font-bold text-gray-900 md:text-5xl">
                    Innovazione Organizzativa
                </h2>
                <p class="mx-auto max-w-3xl text-xl text-gray-600">
                    Intelligenza amministrativa per decisioni migliori
                </p>
            </div>

            <div class="grid gap-8 md:grid-cols-2">

                <!-- Sfida 13 -->
                <div class="rounded-2xl bg-white p-8 shadow-lg transition-shadow hover:shadow-2xl">
                    <h3 class="mb-6 flex items-center text-2xl font-bold text-gray-900">
                        <span class="material-icons text-ai-purple mr-3 text-3xl">warning</span>
                        1. Prevenire Duplicazioni e Conflitti Normativi
                    </h3>
                    <div class="mb-4">
                        <p class="mb-2 font-semibold text-red-700">❌ Problema:</p>
                        <p class="text-gray-600">Atti contraddittori o duplicati vengono approvati per mancanza di
                            visibilità su delibere/determine precedenti.</p>
                    </div>
                    <div class="mb-4">
                        <p class="mb-2 font-semibold text-orange-600">😰 Disagio Percepito:</p>
                        <p class="text-gray-600">Imbarazzo politico quando emerge la contraddizione. Contenzioso
                            legale. Perdita di credibilità istituzionale.</p>
                    </div>
                    <div class="rounded-lg bg-green-50 p-4">
                        <p class="text-compliance-green mb-2 font-semibold">✅ Soluzione N.A.T.A.N.:</p>
                        <p class="text-gray-700"><strong>N.A.T.A.N.</strong> può implementare alert automatici: quando
                            un nuovo atto contiene keyword/oggetti simili ad atti precedenti sullo stesso tema, notifica
                            l'operatore pre-pubblicazione. Dashboard mostra "atti correlati" per ogni nuovo upload,
                            permettendo cross-check prima della firma definitiva. Previene conflitti normativi prima che
                            accadano.</p>
                    </div>
                </div>

                <!-- Sfida 14 -->
                <div class="rounded-2xl bg-white p-8 shadow-lg transition-shadow hover:shadow-2xl">
                    <h3 class="mb-6 flex items-center text-2xl font-bold text-gray-900">
                        <span class="material-icons text-ai-purple mr-3 text-3xl">hub</span>
                        2. Facilitare Collaborazione Inter-Ufficio
                    </h3>
                    <div class="mb-4">
                        <p class="mb-2 font-semibold text-red-700">❌ Problema:</p>
                        <p class="text-gray-600">Uffici diversi lavorano in silos: urbanistica non sa cosa fa lavori
                            pubblici, ragioneria scopre solo a posteriori impegni di spesa.</p>
                    </div>
                    <div class="mb-4">
                        <p class="mb-2 font-semibold text-orange-600">😰 Disagio Percepito:</p>
                        <p class="text-gray-600">Duplicazione sforzi. Mancanza di sinergie. Decisioni prese senza
                            contesto completo.</p>
                    </div>
                    <div class="rounded-lg bg-green-50 p-4">
                        <p class="text-compliance-green mb-2 font-semibold">✅ Soluzione N.A.T.A.N.:</p>
                        <p class="text-gray-700"><strong>N.A.T.A.N.</strong> offre dashboard condivisa cross-ufficio
                            (con permessi granulari): ogni ufficio vede atti pubblicati da altri reparti rilevanti.
                            Filtri personalizzabili: ragioneria vede automaticamente tutti gli atti con impegno di spesa
                            >10k. Notifiche configurabili: "avvisami quando urbanistica pubblica su centro storico".
                            Trasparenza interna, coordinamento facilitato.</p>
                    </div>
                </div>

                <!-- Sfida 16 -->
                <div class="rounded-2xl bg-white p-8 shadow-lg transition-shadow hover:shadow-2xl md:col-span-2">
                    <h3 class="mb-6 flex items-center text-2xl font-bold text-gray-900">
                        <span class="material-icons text-ai-purple mr-3 text-3xl">insights</span>
                        3. Supportare Decisioni Data-Driven
                    </h3>
                    <div class="mb-4">
                        <p class="mb-2 font-semibold text-red-700">❌ Problema:</p>
                        <p class="text-gray-600">Decisioni su allocazione risorse, priorità politiche, efficientamento
                            si basano su percezioni o dati parziali. Manca visione quantitativa su "cosa fa davvero
                            l'ente".</p>
                    </div>
                    <div class="mb-4">
                        <p class="mb-2 font-semibold text-orange-600">😰 Disagio Percepito:</p>
                        <p class="text-gray-600">Scelte sub-ottimali. Difficoltà nel giustificare decisioni a
                            Giunta/Consiglio. Impossibilità di dimostrare impatto di riforme organizzative. Mancanza di
                            KPI oggettivi.</p>
                    </div>
                    <div class="rounded-lg bg-green-50 p-4">
                        <p class="text-compliance-green mb-2 font-semibold">✅ Soluzione N.A.T.A.N.:</p>
                        <p class="text-gray-700"><strong>N.A.T.A.N.</strong> trasforma atti amministrativi in
                            intelligence operativa: "quale ufficio produce più atti? Quali categorie di spesa crescono?
                            Tempi medi per tipo di provvedimento?". Dashboard executive con trend multi-anno. Il
                            Sindaco/Segretario può dire: "Abbiamo ridotto del 30% i tempi medi di delibera grazie a
                            processo X", dati alla mano. Governance evidence-based.</p>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- COME FUNZIONA N.A.T.A.N. -->
    <section id="come-funziona"
        class="bg-gradient-to-br from-florence-gold via-yellow-500 to-orange-500 py-20 text-white">
        <div class="container mx-auto max-w-6xl px-6">

            <div class="mb-16 text-center">
                <span
                    class="mb-6 inline-block rounded-full bg-white px-6 py-2 text-sm font-bold text-florence-gold md:text-base">
                    💡 OPERATIVITÀ QUOTIDIANA
                </span>
                <h2 class="mb-6 text-4xl font-extrabold md:text-5xl">
                    Come Funziona N.A.T.A.N.
                </h2>
                <p class="mx-auto max-w-4xl text-xl leading-relaxed text-yellow-50 md:text-2xl">
                    Tre semplici passaggi dopo firma digitale e protocollo.<br>
                    Zero complessità, massima sicurezza.
                </p>
            </div>

            <!-- 3 Steps -->
            <div class="mb-16 grid gap-8 md:grid-cols-3">

                <!-- Step 1 -->
                <div class="transform transition-transform duration-300 hover:scale-105">
                    <div class="rounded-2xl bg-white p-8 text-gray-900 shadow-2xl">
                        <div
                            class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-florence-gold text-3xl font-extrabold text-white shadow-lg">
                            1
                        </div>
                        <h3 class="mb-4 text-center text-2xl font-bold">Carica il PDF</h3>
                        <p class="text-center leading-relaxed text-gray-600">
                            Accedi alla dashboard N.A.T.A.N., trascina e rilascia il documento già firmato digitalmente
                            e protocollato. Facilissimo.
                        </p>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="transform transition-transform duration-300 hover:scale-105">
                    <div class="rounded-2xl bg-white p-8 text-gray-900 shadow-2xl">
                        <div
                            class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-florence-gold text-3xl font-extrabold text-white shadow-lg">
                            2
                        </div>
                        <h3 class="mb-4 text-center text-2xl font-bold">N.A.T.A.N. Lavora</h3>
                        <p class="text-center leading-relaxed text-gray-600">
                            L'Intelligenza Artificiale legge l'atto, estrae metadata, genera hash, registra su
                            blockchain Algorand. Tutto automatico in 10-15 secondi.
                        </p>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="transform transition-transform duration-300 hover:scale-105">
                    <div class="rounded-2xl bg-white p-8 text-gray-900 shadow-2xl">
                        <div
                            class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-florence-gold text-3xl font-extrabold text-white shadow-lg">
                            3
                        </div>
                        <h3 class="mb-4 text-center text-2xl font-bold">Pubblica QR/Link</h3>
                        <p class="text-center leading-relaxed text-gray-600">
                            Copia il QR Code e il link di verifica. Incollali sull'Albo Pretorio, sito Trasparenza o
                            comunicazioni PEC. Fatto.
                        </p>
                    </div>
                </div>

            </div>

            <!-- Cosa Succede in Automatico -->
            <div
                class="mb-16 rounded-2xl border-2 border-white border-opacity-30 bg-white bg-opacity-10 p-8 backdrop-blur-sm md:p-12">
                <h3 class="mb-10 text-center text-3xl font-bold">✨ Cosa Succede in Automatico</h3>

                <div class="grid gap-8 md:grid-cols-3">

                    <div class="flex flex-col items-center text-center">
                        <span class="material-icons mb-4 text-6xl">verified</span>
                        <h4 class="mb-3 text-xl font-bold">Hash + Blockchain</h4>
                        <p class="leading-relaxed text-yellow-50">
                            N.A.T.A.N. genera l'impronta crittografica del documento e la registra su blockchain
                            Algorand carbon-negative. Prova immutabile e pubblica.
                        </p>
                    </div>

                    <div class="flex flex-col items-center text-center">
                        <span class="material-icons mb-4 text-6xl">auto_awesome</span>
                        <h4 class="mb-3 text-xl font-bold">Metadata AI-Extracted</h4>
                        <p class="leading-relaxed text-yellow-50">
                            L'Intelligenza Artificiale legge il documento ed estrae automaticamente: tipo atto, oggetto,
                            responsabile, importo, categoria. Zero data-entry manuale.
                        </p>
                    </div>

                    <div class="flex flex-col items-center text-center">
                        <span class="material-icons mb-4 text-6xl">qr_code_2</span>
                        <h4 class="mb-3 text-xl font-bold">Pagina Verifica Pubblica</h4>
                        <p class="leading-relaxed text-yellow-50">
                            Generata automaticamente con QR Code: titolo, protocollo, firma valida, link download
                            originale. Cittadini verificano in 1 click.
                        </p>
                    </div>

                </div>
            </div>

            <!-- Varianti Utilizzo -->
            <div
                class="bg-institutional-blue mb-16 rounded-2xl border-2 border-white border-opacity-30 bg-opacity-50 p-8 md:p-10">
                <h3 class="mb-6 flex items-center text-2xl font-bold">
                    <span class="material-icons mr-3 text-3xl">settings</span>
                    Modalità di Utilizzo Alternative
                </h3>

                <div class="space-y-4">
                    <div class="flex items-start rounded-xl bg-white bg-opacity-10 p-6">
                        <span class="material-icons mr-4 mt-1 flex-shrink-0 text-3xl">email</span>
                        <div>
                            <h4 class="mb-2 text-lg font-bold">📧 Upload via Email Dedicata</h4>
                            <p class="text-yellow-50">Inviate il PDF firmato a un indirizzo email riservato del vostro
                                ente. N.A.T.A.N. importa e processa automaticamente. Perfetto per chi preferisce
                                workflow email-based.</p>
                        </div>
                    </div>

                    <div class="flex items-start rounded-xl bg-white bg-opacity-10 p-6">
                        <span class="material-icons mr-4 mt-1 flex-shrink-0 text-3xl">integration_instructions</span>
                        <div>
                            <h4 class="mb-2 text-lg font-bold">🔌 Integrazione API con Gestionale</h4>
                            <p class="text-yellow-50">Se il vostro gestionale documentale supporta integrazioni,
                                possiamo aggiungere un bottone "Certifica con N.A.T.A.N." direttamente nel vostro
                                software. Parliamone con il vostro fornitore IT.</p>
                        </div>
                    </div>

                    <div class="flex items-start rounded-xl bg-white bg-opacity-10 p-6">
                        <span class="material-icons mr-4 mt-1 flex-shrink-0 text-3xl">upload_file</span>
                        <div>
                            <h4 class="mb-2 text-lg font-bold">📦 Batch Upload Massivo</h4>
                            <p class="text-yellow-50">Caricate fino a 50 atti contemporaneamente. N.A.T.A.N. processa
                                in parallelo. Ideale per recupero arretrati o pubblicazioni di fine mese.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Risultato Finale -->
            <div class="text-institutional-blue rounded-2xl bg-white p-10 text-center shadow-2xl">
                <span class="material-icons text-compliance-green mb-6 text-7xl">check_circle</span>
                <h3 class="mb-6 text-3xl font-bold">🎯 Risultato Finale</h3>
                <p class="mx-auto mb-6 max-w-3xl text-xl leading-relaxed">
                    <strong>Ogni atto pubblicato ha QR Code e prova pubblica blockchain di autenticità.</strong>
                </p>
                <p class="mx-auto max-w-2xl text-lg text-gray-700">
                    Il cittadino scansiona il QR, verifica in 1 click che il documento è autentico e scarica l'originale
                    certificato.
                    Zero dubbi, massima trasparenza. La Pubblica Amministrazione entra nell'era dell'Intelligenza
                    Civica.
                </p>
            </div>

        </div>
    </section>
    <!-- RISULTATI CONCRETI -->
    <section class="bg-white py-20">
        <div class="container mx-auto max-w-6xl px-6">

            <div class="mb-16 text-center">
                <h2 class="mb-6 text-4xl font-bold text-gray-900 md:text-5xl">
                    Risultati Misurabili dal Primo Giorno
                </h2>
                <p class="mx-auto max-w-3xl text-xl text-gray-600">
                    N.A.T.A.N. non è teoria. Sono metriche concrete che potrete mostrare nei report.
                </p>
            </div>

            <div class="mb-12 grid gap-8 md:grid-cols-2 lg:grid-cols-4">

                <!-- Metric 1 -->
                <div class="rounded-2xl bg-gradient-to-br from-blue-50 to-indigo-50 p-8 text-center shadow-lg">
                    <div class="text-institutional-blue mb-3 text-5xl font-extrabold">-70%</div>
                    <h4 class="mb-2 text-lg font-bold text-gray-900">Tempo Catalogazione</h4>
                    <p class="text-sm text-gray-600">Da 10 minuti manuali a 15 secondi automatici per atto</p>
                </div>

                <!-- Metric 2 -->
                <div class="rounded-2xl bg-gradient-to-br from-green-50 to-emerald-50 p-8 text-center shadow-lg">
                    <div class="text-compliance-green mb-3 text-5xl font-extrabold">100%</div>
                    <h4 class="mb-2 text-lg font-bold text-gray-900">GDPR Compliance</h4>
                    <p class="text-sm text-gray-600">Audit trail completo, data minimization automatica</p>
                </div>

                <!-- Metric 3 -->
                <div class="rounded-2xl bg-gradient-to-br from-yellow-50 to-orange-50 p-8 text-center shadow-lg">
                    <div class="mb-3 text-5xl font-extrabold text-florence-gold">48h</div>
                    <h4 class="mb-2 text-lg font-bold text-gray-900">Attivazione Rapida</h4>
                    <p class="text-sm text-gray-600">Da setup account a primo atto certificato in 2 giorni</p>
                </div>

                <!-- Metric 4 -->
                <div class="rounded-2xl bg-gradient-to-br from-purple-50 to-pink-50 p-8 text-center shadow-lg">
                    <div class="text-ai-purple mb-3 text-5xl font-extrabold">€200</div>
                    <h4 class="mb-2 text-lg font-bold text-gray-900">Costo Pilot 8 Sett.</h4>
                    <p class="text-sm text-gray-600">Budget accessibile per 200 atti. Zero hardware necessario</p>
                </div>

            </div>

            <!-- Quick Wins -->
            <div class="from-institutional-blue rounded-2xl bg-gradient-to-br to-blue-900 p-10 text-white">
                <h3 class="mb-10 text-center text-3xl font-bold">⚡ Quick-Win Immediati</h3>

                <div class="grid gap-8 md:grid-cols-3">

                    <div class="text-center">
                        <span class="material-icons mb-4 text-6xl">speed</span>
                        <h4 class="mb-3 text-xl font-bold">Settimana 1</h4>
                        <p class="text-blue-100">Primi 10 atti certificati. Team operativo formato. Primi QR Code
                            pubblicati sull'Albo Pretorio.</p>
                    </div>

                    <div class="text-center">
                        <span class="material-icons mb-4 text-6xl">trending_up</span>
                        <h4 class="mb-3 text-xl font-bold">Settimana 4</h4>
                        <p class="text-blue-100">100+ atti nel sistema. Dashboard con primi KPI. Report mensile per
                            dirigenza pronto.</p>
                    </div>

                    <div class="text-center">
                        <span class="material-icons mb-4 text-6xl">military_tech</span>
                        <h4 class="mb-3 text-xl font-bold">Settimana 8</h4>
                        <p class="text-blue-100">Processo consolidato. Personale autonomo. Decision point: conferma o
                            estensione progetto.</p>
                    </div>

                </div>
            </div>

        </div>
    </section>

    <!-- CTA FINALE -->
    <section id="contatti"
        class="from-institutional-blue to-ai-purple bg-gradient-to-br via-blue-800 py-20 text-white">
        <div class="container mx-auto max-w-5xl px-6 text-center">

            <h2 class="mb-6 text-4xl font-extrabold md:text-5xl">
                Pronti a Vedere N.A.T.A.N. in Azione?
            </h2>

            <p class="mx-auto mb-10 max-w-3xl text-xl leading-relaxed text-blue-100 md:text-2xl">
                Saremo lieti di mostrarvi, senza impegno, una demo di 15 minuti applicata a un vostro documento tipo.
                Vedrete con i vostri occhi come N.A.T.A.N. trasforma un dubbio in una certezza.
            </p>

            <div class="mb-12 flex flex-col justify-center gap-6 md:flex-row">
                <a href="mailto:pa-services@florenceegi.com?subject=Richiesta%20Demo%20N.A.T.A.N."
                    class="hover:shadow-3xl inline-flex transform items-center justify-center rounded-full bg-florence-gold px-10 py-5 text-lg font-bold text-white shadow-2xl transition-all hover:scale-105 hover:bg-yellow-600">
                    <span class="material-icons mr-3 text-3xl">videocam</span>
                    Richiedi Demo Gratuita
                </a>

                <a href="mailto:pa-services@florenceegi.com?subject=Informazioni%20Pilot%20N.A.T.A.N."
                    class="text-institutional-blue hover:shadow-3xl inline-flex transform items-center justify-center rounded-full bg-white px-10 py-5 text-lg font-bold shadow-2xl transition-all hover:scale-105 hover:bg-gray-100">
                    <span class="material-icons mr-3 text-3xl">description</span>
                    Scarica Scheda Tecnica
                </a>
            </div>

            <!-- Contact Info -->
            <div
                class="mx-auto max-w-2xl rounded-2xl border-2 border-white border-opacity-30 bg-white bg-opacity-10 p-8 backdrop-blur-sm">
                <h3 class="mb-6 text-2xl font-bold">📞 Contatti Istituzionali</h3>

                <div class="space-y-4 text-lg">
                    <div class="flex items-center justify-center">
                        <span class="material-icons mr-3">email</span>
                        <a href="mailto:pa-services@florenceegi.com" class="transition hover:text-florence-gold">
                            pa-services@florenceegi.com
                        </a>
                    </div>

                    <div class="flex items-center justify-center">
                        <span class="material-icons mr-3">phone</span>
                        <a href="tel:+390551234567" class="transition hover:text-florence-gold">
                            +39 055 123 4567
                        </a>
                    </div>

                    <div class="flex items-center justify-center">
                        <span class="material-icons mr-3">location_on</span>
                        <span>Firenze, Italia 🇮🇹</span>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-gray-900 py-16 text-white">
        <div class="container mx-auto max-w-6xl px-6">

            <div class="mb-12 grid gap-10 md:grid-cols-4">

                <!-- Branding -->
                <div class="md:col-span-2">
                    <div class="mb-4 flex items-center space-x-3">
                        <span class="material-icons text-4xl">account_balance</span>
                        <div>
                            <h3 class="text-2xl font-bold">N.A.T.A.N.</h3>
                            <p class="text-sm text-gray-400">by Florence EGI</p>
                        </div>
                    </div>
                    <p class="mb-4 leading-relaxed text-gray-400">
                        Nodo di Analisi e Tracciamento Atti Notarizzati.
                        Il primo sistema italiano di Intelligenza Artificiale Civica
                        per la certificazione automatica degli atti della Pubblica Amministrazione.
                    </p>
                    <p class="text-sm text-gray-500">
                        Servizi enterprise di certificazione digitale eIDAS-compliant
                        per Pubbliche Amministrazioni italiane.
                    </p>
                </div>

                <!-- Link Utili -->
                <div>
                    <h4 class="mb-4 text-lg font-bold text-florence-gold">Link Utili</h4>
                    <ul class="space-y-3">
                        <li><a href="#home" class="text-gray-400 transition hover:text-white">Home</a></li>
                        <li><a href="#il-sistema" class="text-gray-400 transition hover:text-white">Il Sistema
                                N.A.T.A.N.</a></li>
                        <li><a href="#certezza-trasparenza" class="text-gray-400 transition hover:text-white">Tutti i
                                Vantaggi</a></li>
                        <li><a href="#come-funziona" class="text-gray-400 transition hover:text-white">Come
                                Funziona</a></li>
                        <li><a href="#contatti" class="text-gray-400 transition hover:text-white">Richiedi Demo</a>
                        </li>
                    </ul>
                </div>

                <!-- Categorie Vantaggi -->
                <div>
                    <h4 class="mb-4 text-lg font-bold text-florence-gold">Vantaggi per Categoria</h4>
                    <ul class="space-y-3 text-sm">
                        <li><a href="#certezza-trasparenza" class="text-gray-400 transition hover:text-white">🔒
                                Certezza e Trasparenza</a></li>
                        <li><a href="#efficienza-operativa" class="text-gray-400 transition hover:text-white">⚡
                                Efficienza Operativa</a></li>
                        <li><a href="#compliance-governance" class="text-gray-400 transition hover:text-white">✅
                                Compliance e Governance</a></li>
                        <li><a href="#innovazione-organizzativa" class="text-gray-400 transition hover:text-white">📈
                                Innovazione Organizzativa</a></li>
                    </ul>
                </div>

            </div>

            <!-- Bottom Bar -->
            <div class="border-t border-gray-800 pt-8">
                <div class="flex flex-col items-center justify-between space-y-4 md:flex-row md:space-y-0">
                    <p class="text-sm text-gray-500">
                        &copy; 2025 Florence EGI - N.A.T.A.N. | Tutti i diritti riservati.
                    </p>
                    <div class="flex space-x-6 text-sm text-gray-500">
                        <a href="#" class="transition hover:text-white">Privacy Policy</a>
                        <a href="#" class="transition hover:text-white">Termini di Servizio</a>
                        <a href="#" class="transition hover:text-white">GDPR Compliance</a>
                    </div>
                </div>
            </div>

        </div>
    </footer>

    <!-- SCRIPTS -->
    <script>
        // Mobile Menu Toggle
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            const menuIcon = mobileMenuButton.querySelector('.material-icons');

            mobileMenuButton.addEventListener('click', function(event) {
                event.stopPropagation();
                if (mobileMenu.classList.contains('hidden')) {
                    mobileMenu.classList.remove('hidden');
                    menuIcon.textContent = 'close';
                } else {
                    mobileMenu.classList.add('hidden');
                    menuIcon.textContent = 'menu';
                }
            });

            // Close on link click
            const mobileMenuLinks = mobileMenu.querySelectorAll('a');
            mobileMenuLinks.forEach(link => {
                link.addEventListener('click', function() {
                    mobileMenu.classList.add('hidden');
                    menuIcon.textContent = 'menu';
                });
            });

            // Close on outside click
            document.addEventListener('click', function(event) {
                if (!mobileMenuButton.contains(event.target) && !mobileMenu.contains(event.target)) {
                    mobileMenu.classList.add('hidden');
                    menuIcon.textContent = 'menu';
                }
            });

            // Back to Top Button
            const backToTopButton = document.getElementById('back-to-top');

            window.addEventListener('scroll', function() {
                if (window.scrollY > 500) {
                    backToTopButton.classList.remove('hidden');
                } else {
                    backToTopButton.classList.add('hidden');
                }
            });

            backToTopButton.addEventListener('click', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });

            // Fade-in animations on scroll
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('fade-in-up');
                    }
                });
            }, observerOptions);

            // Observe all section elements
            document.querySelectorAll('section').forEach(section => {
                observer.observe(section);
            });
        });
    </script>

</body>

</html>
