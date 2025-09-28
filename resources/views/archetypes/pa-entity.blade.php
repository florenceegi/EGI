{{--
    File: pa-entity.blade.php
    Autore: Padmin D. Curtis per Fabio Cherici
    Data: 28 Settembre 2025
    Versione: 1.0 Silent Growth
    Descrizione: Minisito dedicato alle Pubbliche Amministrazioni per servizi CoA e digitalizzazione patrimonio culturale
    Principi: Linguaggio istituzionale, credibilità tecnica, compliance normativa
--}}
<!DOCTYPE html>
<html lang="{{ config('app.locale', 'it') }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servizi per Pubbliche Amministrazioni | FlorenceEGI</title>
    <meta name="description"
        content="Servizi enterprise di certificazione digitale eIDAS-compliant per Pubbliche Amministrazioni. Digitalizzazione patrimonio culturale con blockchain verification.">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Custom Colors -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'florence-blue': '#1E40AF',
                        'florence-gold': '#D97706',
                        'florence-green': '#059669',
                        'florence-gray': '#6B7280',
                        'institutional-blue': '#1E3A8A',
                        'compliance-green': '#166534'
                    }
                }
            }
        }
    </script>

    <!-- Custom CSS for mobile optimization -->
    <style>
        body {
            overflow-x: hidden;
        }

        .container {
            width: 100%;
            max-width: 100%;
        }

        @media (min-width: 640px) {
            .container {
                max-width: 640px;
            }
        }

        @media (min-width: 768px) {
            .container {
                max-width: 768px;
            }
        }

        @media (min-width: 1024px) {
            .container {
                max-width: 1024px;
            }
        }

        @media (min-width: 1280px) {
            .container {
                max-width: 1280px;
            }
        }
    </style>
</head>

<body class="overflow-x-hidden text-gray-900 bg-gray-50">
    <!-- Header Istituzionale -->
    <header class="py-4 text-white shadow-lg bg-institutional-blue sm:py-6">
        <div class="container px-4 mx-auto sm:px-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2 sm:space-x-4">
                    <span class="text-3xl material-icons sm:text-4xl">account_balance</span>
                    <div>
                        <h1 class="text-xl font-bold sm:text-2xl">FlorenceEGI</h1>
                        <p class="text-sm text-blue-200 sm:text-base">Servizi per Pubbliche Amministrazioni</p>
                    </div>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden space-x-4 sm:space-x-6 md:flex">
                    <a href="{{ route('home') }}" class="text-sm transition hover:text-blue-200 lg:text-base">Home</a>
                    <a href="#servizi" class="text-sm transition hover:text-blue-200 lg:text-base">Servizi</a>
                    <a href="#compliance" class="text-sm transition hover:text-blue-200 lg:text-base">Compliance</a>
                    <a href="#tecnologia" class="text-sm transition hover:text-blue-200 lg:text-base">Tecnologia</a>
                    <a href="#contatti" class="text-sm transition hover:text-blue-200 lg:text-base">Contatti</a>
                </div>

                <!-- Mobile Menu Button -->
                <button id="mobile-menu-button"
                    class="block p-2 transition-colors rounded-md hover:bg-blue-700 md:hidden">
                    <span class="text-2xl material-icons">menu</span>
                </button>
            </div>

            <!-- Mobile Navigation Menu -->
            <div id="mobile-menu" class="hidden pb-4 mt-4 border-t border-blue-600 md:hidden">
                <div class="pt-4 space-y-3">
                    <a href="{{ route('home') }}"
                        class="flex items-center px-4 py-2 text-sm transition-colors rounded-md hover:bg-blue-700">
                        <span class="mr-3 text-lg material-icons">home</span>
                        Torna alla Home
                    </a>
                    <a href="#servizi"
                        class="flex items-center px-4 py-2 text-sm transition-colors rounded-md hover:bg-blue-700">
                        <span class="mr-3 text-lg material-icons">business_center</span>
                        Servizi
                    </a>
                    <a href="#compliance"
                        class="flex items-center px-4 py-2 text-sm transition-colors rounded-md hover:bg-blue-700">
                        <span class="mr-3 text-lg material-icons">verified_user</span>
                        Compliance
                    </a>
                    <a href="#tecnologia"
                        class="flex items-center px-4 py-2 text-sm transition-colors rounded-md hover:bg-blue-700">
                        <span class="mr-3 text-lg material-icons">settings</span>
                        Tecnologia
                    </a>
                    <a href="#contatti"
                        class="flex items-center px-4 py-2 text-sm transition-colors rounded-md hover:bg-blue-700">
                        <span class="mr-3 text-lg material-icons">contact_mail</span>
                        Contatti
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section PA -->
    <section class="py-12 text-white from-institutional-blue bg-gradient-to-r to-blue-700 sm:py-16">
        <div class="container px-4 mx-auto text-center sm:px-6">
            <div class="max-w-4xl mx-auto">
                <h1 class="mb-4 text-2xl font-bold leading-tight sm:mb-6 sm:text-4xl md:text-5xl lg:text-6xl">
                    Digitalizzazione Certificata del Patrimonio Culturale
                </h1>
                <p class="mb-6 text-lg text-blue-100 sm:mb-8 sm:text-xl md:text-2xl">
                    Servizi enterprise di certificazione digitale eIDAS-compliant per Enti Pubblici, Musei, Biblioteche
                    e Archivi Storici
                </p>
                <div
                    class="flex flex-col justify-center w-full max-w-md gap-3 mx-auto sm:gap-4 md:max-w-none md:flex-row">
                    <button
                        class="w-full px-6 py-3 text-base font-semibold text-white transition rounded-lg bg-florence-gold hover:bg-yellow-600 sm:px-8 sm:py-4 sm:text-lg md:w-auto">
                        <span class="mr-2 text-base material-icons sm:text-lg">description</span>
                        Richiedi Documentazione
                    </button>
                    <button
                        class="w-full px-6 py-3 text-base font-semibold text-white transition bg-transparent border-2 border-white rounded-lg hover:text-institutional-blue hover:bg-white sm:px-8 sm:py-4 sm:text-lg md:w-auto">
                        <span class="mr-2 text-base material-icons sm:text-lg">play_circle</span>
                        Demo Piattaforma
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Valore per PA -->
    <section id="servizi" class="py-12 bg-white sm:py-16">
        <div class="container px-4 mx-auto sm:px-6">
            <div class="max-w-6xl mx-auto">
                <div class="mb-8 text-center sm:mb-12">
                    <h2 class="mb-3 text-2xl font-bold sm:mb-4 sm:text-3xl md:text-4xl">
                        Perché le PA Scelgono FlorenceEGI
                    </h2>
                    <p class="max-w-3xl px-4 mx-auto text-lg text-gray-600 sm:px-0 sm:text-xl">
                        La prima piattaforma di certificazione digitale progettata specificamente per le esigenze delle
                        Pubbliche Amministrazioni italiane ed europee
                    </p>
                </div>

                <div class="grid gap-6 mb-8 sm:mb-12 sm:gap-8 md:grid-cols-3">
                    <!-- Compliance Normativa -->
                    <div class="p-6 border-l-4 border-compliance-green rounded-xl bg-gray-50 sm:p-8">
                        <div class="flex items-center mb-4">
                            <span
                                class="mr-2 text-2xl material-icons text-compliance-green sm:mr-3 sm:text-3xl">security</span>
                            <h3 class="text-lg font-bold sm:text-xl">Compliance Normativa</h3>
                        </div>
                        <ul class="space-y-2 text-sm text-gray-700 sm:text-base">
                            <li>✓ Conformità Regolamento eIDAS</li>
                            <li>✓ GDPR-compliant by design</li>
                            <li>✓ ISO 27001 certified infrastructure</li>
                            <li>✓ Audit trail completo</li>
                            <li>✓ Firma digitale qualificata</li>
                        </ul>
                    </div>

                    <!-- Efficienza Operativa -->
                    <div class="p-6 border-l-4 border-florence-blue rounded-xl bg-gray-50 sm:p-8">
                        <div class="flex items-center mb-4">
                            <span
                                class="mr-2 text-2xl material-icons text-florence-blue sm:mr-3 sm:text-3xl">speed</span>
                            <h3 class="text-lg font-bold sm:text-xl">Efficienza Operativa</h3>
                        </div>
                        <ul class="space-y-2 text-sm text-gray-700 sm:text-base">
                            <li>✓ Batch processing automatizzato</li>
                            <li>✓ Integrazione API sistemi esistenti</li>
                            <li>✓ Riduzione 70% tempi certificazione</li>
                            <li>✓ Dashboard gestionale centralizzata</li>
                            <li>✓ Reportistica automatica</li>
                        </ul>
                    </div>

                    <!-- Sostenibilità -->
                    <div class="p-6 border-l-4 border-florence-green rounded-xl bg-gray-50 sm:p-8">
                        <div class="flex items-center mb-4">
                            <span
                                class="mr-2 text-2xl material-icons text-florence-green sm:mr-3 sm:text-3xl">eco</span>
                            <h3 class="text-lg font-bold sm:text-xl">Sostenibilità Ambientale</h3>
                        </div>
                        <ul class="space-y-2 text-sm text-gray-700 sm:text-base">
                            <li>✓ Blockchain carbon-negative Algorand</li>
                            <li>✓ 20% automatico a progetti EPP</li>
                            <li>✓ Certificazione impatto ambientale</li>
                            <li>✓ Reporting sostenibilità integrato</li>
                            <li>✓ Compliance ESG automatica</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Servizi Specifici PA -->
    <section class="py-12 bg-gray-50 sm:py-16">
        <div class="container px-4 mx-auto sm:px-6">
            <div class="max-w-6xl mx-auto">
                <h2 class="mb-8 text-2xl font-bold text-center sm:mb-12 sm:text-3xl md:text-4xl">
                    Servizi Enterprise per PA
                </h2>

                <div class="grid gap-6 sm:gap-8 md:grid-cols-2">
                    <!-- CoA per Patrimonio Culturale -->
                    <div class="p-6 bg-white shadow-lg rounded-xl sm:p-8">
                        <div class="flex items-center mb-4 sm:mb-6">
                            <span
                                class="mr-3 text-3xl material-icons text-florence-gold sm:mr-4 sm:text-4xl">museum</span>
                            <div>
                                <h3 class="text-xl font-bold sm:text-2xl">Certificazione Patrimonio Culturale</h3>
                                <p class="text-sm text-gray-600 sm:text-base">Per Musei, Biblioteche, Archivi Storici
                                </p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <span class="mt-1 mr-3 material-icons text-florence-gold">verified</span>
                                <div>
                                    <h4 class="font-semibold">Certificate of Authenticity (CoA)</h4>
                                    <p class="text-gray-600">Certificazione digitale opere d'arte e beni culturali con
                                        blockchain verification</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <span class="mt-1 mr-3 material-icons text-florence-gold">batch_prediction</span>
                                <div>
                                    <h4 class="font-semibold">Batch Processing</h4>
                                    <p class="text-gray-600">Certificazione massiva per collezioni complete e archivi
                                        digitali</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <span
                                    class="mt-1 mr-3 material-icons text-florence-gold">integration_instructions</span>
                                <div>
                                    <h4 class="font-semibold">Integrazione Sistemi</h4>
                                    <p class="text-gray-600">API per integrazione con sistemi di catalogazione
                                        esistenti
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Digitalizzazione Documenti -->
                    <div class="p-6 bg-white shadow-lg rounded-xl sm:p-8">
                        <div class="flex items-center mb-4 sm:mb-6">
                            <span
                                class="mr-3 text-3xl material-icons text-institutional-blue sm:mr-4 sm:text-4xl">folder_managed</span>
                            <div>
                                <h3 class="text-xl font-bold sm:text-2xl">Digitalizzazione Documentale</h3>
                                <p class="text-sm text-gray-600 sm:text-base">Per Enti Pubblici e Amministrazioni</p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <span class="mt-1 mr-3 material-icons text-institutional-blue">gavel</span>
                                <div>
                                    <h4 class="font-semibold">Valore Legale</h4>
                                    <p class="text-gray-600">Documenti digitali con pieno valore legale e opponibilità
                                        ai terzi</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <span class="mt-1 mr-3 material-icons text-institutional-blue">schedule</span>
                                <div>
                                    <h4 class="font-semibold">Timestamp Qualificato</h4>
                                    <p class="text-gray-600">Marca temporale eIDAS per certificazione data e ora</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <span class="mt-1 mr-3 material-icons text-institutional-blue">cloud_sync</span>
                                <div>
                                    <h4 class="font-semibold">Archiviazione Sicura</h4>
                                    <p class="text-gray-600">Storage certificato con backup ridondanti e disaster
                                        recovery</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Tecnologia e Compliance -->
    <section id="compliance" class="py-16 bg-white">
        <div class="container px-6 mx-auto">
            <div class="max-w-6xl mx-auto">
                <div class="mb-12 text-center">
                    <h2 class="mb-4 text-3xl font-bold md:text-4xl">
                        Tecnologia Enterprise-Grade
                    </h2>
                    <p class="text-xl text-gray-600">
                        Architettura progettata per la sicurezza e la compliance delle Pubbliche Amministrazioni
                    </p>
                </div>

                <div class="grid items-center gap-12 md:grid-cols-2">
                    <div>
                        <h3 class="mb-6 text-2xl font-bold">Infrastruttura Certificata</h3>
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <span class="mr-3 material-icons text-compliance-green">verified_user</span>
                                <span><strong>Laravel Enterprise:</strong> Framework PHP enterprise-grade con Ultra
                                    ecosystem</span>
                            </div>
                            <div class="flex items-center">
                                <span class="mr-3 material-icons text-compliance-green">account_tree</span>
                                <span><strong>Algorand Blockchain:</strong> Carbon-negative, 6000 TPS, finalità 2.5
                                    secondi</span>
                            </div>
                            <div class="flex items-center">
                                <span class="mr-3 material-icons text-compliance-green">security</span>
                                <span><strong>ISO 27001:</strong> Gestione sicurezza informazioni certificata</span>
                            </div>
                            <div class="flex items-center">
                                <span class="mr-3 material-icons text-compliance-green">policy</span>
                                <span><strong>eIDAS Ready:</strong> Firma digitale qualificata e marca temporale</span>
                            </div>
                            <div class="flex items-center">
                                <span class="mr-3 material-icons text-compliance-green">shield</span>
                                <span><strong>GDPR Native:</strong> Privacy by design e data protection nativa</span>
                            </div>
                        </div>
                    </div>
                    <div class="p-8 rounded-xl bg-gray-50">
                        <h4 class="mb-4 text-xl font-bold">Certificazioni e Compliance</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 text-center bg-white rounded-lg">
                                <span class="mb-2 text-3xl material-icons text-compliance-green">verified</span>
                                <div class="font-semibold">eIDAS</div>
                                <div class="text-sm text-gray-600">Compliant</div>
                            </div>
                            <div class="p-4 text-center bg-white rounded-lg">
                                <span class="mb-2 text-3xl material-icons text-compliance-green">policy</span>
                                <div class="font-semibold">GDPR</div>
                                <div class="text-sm text-gray-600">Native</div>
                            </div>
                            <div class="p-4 text-center bg-white rounded-lg">
                                <span class="mb-2 text-3xl material-icons text-compliance-green">security</span>
                                <div class="font-semibold">ISO 27001</div>
                                <div class="text-sm text-gray-600">Certified</div>
                            </div>
                            <div class="p-4 text-center bg-white rounded-lg">
                                <span class="mb-2 text-3xl material-icons text-compliance-green">eco</span>
                                <div class="font-semibold">Carbon</div>
                                <div class="text-sm text-gray-600">Negative</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Vantaggi per PA -->
    <section class="py-16 text-white bg-institutional-blue">
        <div class="container px-6 mx-auto">
            <div class="max-w-6xl mx-auto">
                <div class="mb-12 text-center">
                    <h2 class="mb-4 text-3xl font-bold md:text-4xl">
                        ROI Documentato per le PA
                    </h2>
                    <p class="text-xl text-blue-200">
                        Benefici misurabili dall'adozione della piattaforma FlorenceEGI
                    </p>
                </div>

                <div class="grid gap-8 md:grid-cols-3">
                    <!-- Riduzione Costi -->
                    <div class="text-center">
                        <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-blue-600 rounded-full">
                            <span class="text-2xl material-icons">trending_down</span>
                        </div>
                        <h3 class="mb-2 text-2xl font-bold">-70%</h3>
                        <p class="text-blue-200">Riduzione costi di certificazione</p>
                    </div>

                    <!-- Velocità -->
                    <div class="text-center">
                        <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-blue-600 rounded-full">
                            <span class="text-2xl material-icons">speed</span>
                        </div>
                        <h3 class="mb-2 text-2xl font-bold">5x</h3>
                        <p class="text-blue-200">Velocità di elaborazione documenti</p>
                    </div>

                    <!-- Sicurezza -->
                    <div class="text-center">
                        <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-blue-600 rounded-full">
                            <span class="text-2xl material-icons">security</span>
                        </div>
                        <h3 class="mb-2 text-2xl font-bold">100%</h3>
                        <p class="text-blue-200">Tracciabilità e immutabilità</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pilot Program -->
    <section class="py-12 bg-florence-gold sm:py-16">
        <div class="container px-4 mx-auto sm:px-6">
            <div class="max-w-4xl mx-auto text-center text-white">
                <h2 class="mb-4 text-2xl font-bold sm:mb-6 sm:text-3xl md:text-4xl">
                    Programma Pilota per PA
                </h2>
                <p class="px-4 mb-6 text-lg text-yellow-100 sm:mb-8 sm:px-0 sm:text-xl">
                    Testa la piattaforma FlorenceEGI per 6 mesi senza costi di implementazione.
                    Valuta l'efficacia sui tuoi processi di certificazione prima dell'adozione definitiva.
                </p>
                <div class="p-6 bg-white rounded-xl text-florence-gold sm:p-8">
                    <h3 class="mb-4 text-xl font-bold sm:text-2xl">Incluso nel Pilot Program:</h3>
                    <div class="grid gap-3 text-sm text-left sm:gap-4 sm:text-base md:grid-cols-2">
                        <div class="flex items-center">
                            <span class="mr-3 material-icons">check_circle</span>
                            <span>Setup e configurazione completa</span>
                        </div>
                        <div class="flex items-center">
                            <span class="mr-3 material-icons">check_circle</span>
                            <span>Formazione team tecnico</span>
                        </div>
                        <div class="flex items-center">
                            <span class="mr-3 material-icons">check_circle</span>
                            <span>Integrazione sistemi esistenti</span>
                        </div>
                        <div class="flex items-center">
                            <span class="mr-3 material-icons">check_circle</span>
                            <span>Supporto dedicato H24</span>
                        </div>
                        <div class="flex items-center">
                            <span class="mr-3 material-icons">check_circle</span>
                            <span>Certificazione fino a 1000 asset</span>
                        </div>
                        <div class="flex items-center">
                            <span class="mr-3 material-icons">check_circle</span>
                            <span>Report di impatto e ROI</span>
                        </div>
                    </div>
                </div>
                <div class="mt-6 sm:mt-8">
                    <button
                        class="w-full px-6 py-3 text-base font-bold text-white transition rounded-lg bg-institutional-blue hover:bg-blue-800 sm:w-auto sm:px-8 sm:py-4 sm:text-lg">
                        <span class="mr-2 text-base material-icons sm:text-lg">contact_mail</span>
                        Richiedi Pilot Program
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Contatti Istituzionali -->
    <section id="contatti" class="py-16 text-white bg-gray-900">
        <div class="container px-6 mx-auto">
            <div class="max-w-4xl mx-auto">
                <div class="mb-12 text-center">
                    <h2 class="mb-4 text-3xl font-bold">Contatti Istituzionali</h2>
                    <p class="text-xl text-gray-300">
                        Parla direttamente con i nostri esperti in digitalizzazione per PA
                    </p>
                </div>

                <div class="grid gap-8 md:grid-cols-2">
                    <div>
                        <h3 class="mb-4 text-xl font-bold">Informazioni Commerciali</h3>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <span class="mr-3 material-icons">email</span>
                                <span>pa-services@florenceegi.com</span>
                            </div>
                            <div class="flex items-center">
                                <span class="mr-3 material-icons">phone</span>
                                <span>+39 055 XXX XXXX</span>
                            </div>
                            <div class="flex items-center">
                                <span class="mr-3 material-icons">schedule</span>
                                <span>Lun-Ven 9:00-18:00</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="mb-4 text-xl font-bold">Supporto Tecnico</h3>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <span class="mr-3 material-icons">support</span>
                                <span>support@florenceegi.com</span>
                            </div>
                            <div class="flex items-center">
                                <span class="mr-3 material-icons">description</span>
                                <span>Documentazione API</span>
                            </div>
                            <div class="flex items-center">
                                <span class="mr-3 material-icons">help</span>
                                <span>Centro Assistenza 24/7</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-6 mt-12 bg-gray-800 rounded-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold">Richiedi una Demo Personalizzata</h3>
                            <p class="text-gray-300">Vedi FlorenceEGI in azione con i tuoi dati di test</p>
                        </div>
                        <button
                            class="px-6 py-3 font-semibold text-white transition rounded-lg bg-florence-gold hover:bg-yellow-600">
                            Prenota Demo
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
   @include('components.info-footer')

    <!-- Mobile Menu Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            const menuIcon = mobileMenuButton.querySelector('.material-icons');

            mobileMenuButton.addEventListener('click', function() {
                if (mobileMenu.classList.contains('hidden')) {
                    mobileMenu.classList.remove('hidden');
                    menuIcon.textContent = 'close';
                } else {
                    mobileMenu.classList.add('hidden');
                    menuIcon.textContent = 'menu';
                }
            });

            // Close menu when clicking on a link
            const mobileMenuLinks = mobileMenu.querySelectorAll('a');
            mobileMenuLinks.forEach(link => {
                link.addEventListener('click', function() {
                    mobileMenu.classList.add('hidden');
                    menuIcon.textContent = 'menu';
                });
            });

            // Close menu when clicking outside
            document.addEventListener('click', function(event) {
                if (!mobileMenuButton.contains(event.target) && !mobileMenu.contains(event.target)) {
                    mobileMenu.classList.add('hidden');
                    menuIcon.textContent = 'menu';
                }
            });
        });
    </script>
</body>

</html>
