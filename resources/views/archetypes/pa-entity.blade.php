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
</head>

<body class="bg-gray-50 text-gray-900">
    <!-- Header Istituzionale -->
    <header class="bg-institutional-blue py-6 text-white shadow-lg">
        <div class="container mx-auto px-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <span class="material-icons text-4xl">account_balance</span>
                    <div>
                        <h1 class="text-2xl font-bold">FlorenceEGI</h1>
                        <p class="text-blue-200">Servizi per Pubbliche Amministrazioni</p>
                    </div>
                </div>
                <div class="hidden space-x-6 md:flex">
                    <a href="#servizi" class="transition hover:text-blue-200">Servizi</a>
                    <a href="#compliance" class="transition hover:text-blue-200">Compliance</a>
                    <a href="#tecnologia" class="transition hover:text-blue-200">Tecnologia</a>
                    <a href="#contatti" class="transition hover:text-blue-200">Contatti</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section PA -->
    <section class="from-institutional-blue bg-gradient-to-r to-blue-700 py-16 text-white">
        <div class="container mx-auto px-6 text-center">
            <div class="mx-auto max-w-4xl">
                <h1 class="mb-6 text-4xl font-bold md:text-6xl">
                    Digitalizzazione Certificata del Patrimonio Culturale
                </h1>
                <p class="mb-8 text-xl text-blue-100 md:text-2xl">
                    Servizi enterprise di certificazione digitale eIDAS-compliant per Enti Pubblici, Musei, Biblioteche
                    e Archivi Storici
                </p>
                <div class="flex flex-col justify-center gap-4 md:flex-row">
                    <button
                        class="rounded-lg bg-florence-gold px-8 py-4 text-lg font-semibold text-white transition hover:bg-yellow-600">
                        <span class="material-icons mr-2">description</span>
                        Richiedi Documentazione
                    </button>
                    <button
                        class="hover:text-institutional-blue rounded-lg border-2 border-white bg-transparent px-8 py-4 text-lg font-semibold text-white transition hover:bg-white">
                        <span class="material-icons mr-2">play_circle</span>
                        Demo Piattaforma
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Valore per PA -->
    <section id="servizi" class="bg-white py-16">
        <div class="container mx-auto px-6">
            <div class="mx-auto max-w-6xl">
                <div class="mb-12 text-center">
                    <h2 class="mb-4 text-3xl font-bold md:text-4xl">
                        Perché le PA Scelgono FlorenceEGI
                    </h2>
                    <p class="mx-auto max-w-3xl text-xl text-gray-600">
                        La prima piattaforma di certificazione digitale progettata specificamente per le esigenze delle
                        Pubbliche Amministrazioni italiane ed europee
                    </p>
                </div>

                <div class="mb-12 grid gap-8 md:grid-cols-3">
                    <!-- Compliance Normativa -->
                    <div class="border-compliance-green rounded-xl border-l-4 bg-gray-50 p-8">
                        <div class="mb-4 flex items-center">
                            <span class="material-icons text-compliance-green mr-3 text-3xl">security</span>
                            <h3 class="text-xl font-bold">Compliance Normativa</h3>
                        </div>
                        <ul class="space-y-2 text-gray-700">
                            <li>✓ Conformità Regolamento eIDAS</li>
                            <li>✓ GDPR-compliant by design</li>
                            <li>✓ ISO 27001 certified infrastructure</li>
                            <li>✓ Audit trail completo</li>
                            <li>✓ Firma digitale qualificata</li>
                        </ul>
                    </div>

                    <!-- Efficienza Operativa -->
                    <div class="border-florence-blue rounded-xl border-l-4 bg-gray-50 p-8">
                        <div class="mb-4 flex items-center">
                            <span class="material-icons text-florence-blue mr-3 text-3xl">speed</span>
                            <h3 class="text-xl font-bold">Efficienza Operativa</h3>
                        </div>
                        <ul class="space-y-2 text-gray-700">
                            <li>✓ Batch processing automatizzato</li>
                            <li>✓ Integrazione API sistemi esistenti</li>
                            <li>✓ Riduzione 70% tempi certificazione</li>
                            <li>✓ Dashboard gestionale centralizzata</li>
                            <li>✓ Reportistica automatica</li>
                        </ul>
                    </div>

                    <!-- Sostenibilità -->
                    <div class="border-florence-green rounded-xl border-l-4 bg-gray-50 p-8">
                        <div class="mb-4 flex items-center">
                            <span class="material-icons text-florence-green mr-3 text-3xl">eco</span>
                            <h3 class="text-xl font-bold">Sostenibilità Ambientale</h3>
                        </div>
                        <ul class="space-y-2 text-gray-700">
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
    <section class="bg-gray-50 py-16">
        <div class="container mx-auto px-6">
            <div class="mx-auto max-w-6xl">
                <h2 class="mb-12 text-center text-3xl font-bold md:text-4xl">
                    Servizi Enterprise per PA
                </h2>

                <div class="grid gap-8 md:grid-cols-2">
                    <!-- CoA per Patrimonio Culturale -->
                    <div class="rounded-xl bg-white p-8 shadow-lg">
                        <div class="mb-6 flex items-center">
                            <span class="material-icons mr-4 text-4xl text-florence-gold">museum</span>
                            <div>
                                <h3 class="text-2xl font-bold">Certificazione Patrimonio Culturale</h3>
                                <p class="text-gray-600">Per Musei, Biblioteche, Archivi Storici</p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <span class="material-icons mr-3 mt-1 text-florence-gold">verified</span>
                                <div>
                                    <h4 class="font-semibold">Certificate of Authenticity (CoA)</h4>
                                    <p class="text-gray-600">Certificazione digitale opere d'arte e beni culturali con
                                        blockchain verification</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <span class="material-icons mr-3 mt-1 text-florence-gold">batch_prediction</span>
                                <div>
                                    <h4 class="font-semibold">Batch Processing</h4>
                                    <p class="text-gray-600">Certificazione massiva per collezioni complete e archivi
                                        digitali</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <span
                                    class="material-icons mr-3 mt-1 text-florence-gold">integration_instructions</span>
                                <div>
                                    <h4 class="font-semibold">Integrazione Sistemi</h4>
                                    <p class="text-gray-600">API per integrazione con sistemi di catalogazione esistenti
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Digitalizzazione Documenti -->
                    <div class="rounded-xl bg-white p-8 shadow-lg">
                        <div class="mb-6 flex items-center">
                            <span class="material-icons text-institutional-blue mr-4 text-4xl">folder_managed</span>
                            <div>
                                <h3 class="text-2xl font-bold">Digitalizzazione Documentale</h3>
                                <p class="text-gray-600">Per Enti Pubblici e Amministrazioni</p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <span class="material-icons text-institutional-blue mr-3 mt-1">gavel</span>
                                <div>
                                    <h4 class="font-semibold">Valore Legale</h4>
                                    <p class="text-gray-600">Documenti digitali con pieno valore legale e opponibilità
                                        ai terzi</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <span class="material-icons text-institutional-blue mr-3 mt-1">schedule</span>
                                <div>
                                    <h4 class="font-semibold">Timestamp Qualificato</h4>
                                    <p class="text-gray-600">Marca temporale eIDAS per certificazione data e ora</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <span class="material-icons text-institutional-blue mr-3 mt-1">cloud_sync</span>
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
    <section id="compliance" class="bg-white py-16">
        <div class="container mx-auto px-6">
            <div class="mx-auto max-w-6xl">
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
                                <span class="material-icons text-compliance-green mr-3">verified_user</span>
                                <span><strong>Laravel Enterprise:</strong> Framework PHP enterprise-grade con Ultra
                                    ecosystem</span>
                            </div>
                            <div class="flex items-center">
                                <span class="material-icons text-compliance-green mr-3">account_tree</span>
                                <span><strong>Algorand Blockchain:</strong> Carbon-negative, 6000 TPS, finalità 2.5
                                    secondi</span>
                            </div>
                            <div class="flex items-center">
                                <span class="material-icons text-compliance-green mr-3">security</span>
                                <span><strong>ISO 27001:</strong> Gestione sicurezza informazioni certificata</span>
                            </div>
                            <div class="flex items-center">
                                <span class="material-icons text-compliance-green mr-3">policy</span>
                                <span><strong>eIDAS Ready:</strong> Firma digitale qualificata e marca temporale</span>
                            </div>
                            <div class="flex items-center">
                                <span class="material-icons text-compliance-green mr-3">shield</span>
                                <span><strong>GDPR Native:</strong> Privacy by design e data protection nativa</span>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-xl bg-gray-50 p-8">
                        <h4 class="mb-4 text-xl font-bold">Certificazioni e Compliance</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="rounded-lg bg-white p-4 text-center">
                                <span class="material-icons text-compliance-green mb-2 text-3xl">verified</span>
                                <div class="font-semibold">eIDAS</div>
                                <div class="text-sm text-gray-600">Compliant</div>
                            </div>
                            <div class="rounded-lg bg-white p-4 text-center">
                                <span class="material-icons text-compliance-green mb-2 text-3xl">policy</span>
                                <div class="font-semibold">GDPR</div>
                                <div class="text-sm text-gray-600">Native</div>
                            </div>
                            <div class="rounded-lg bg-white p-4 text-center">
                                <span class="material-icons text-compliance-green mb-2 text-3xl">security</span>
                                <div class="font-semibold">ISO 27001</div>
                                <div class="text-sm text-gray-600">Certified</div>
                            </div>
                            <div class="rounded-lg bg-white p-4 text-center">
                                <span class="material-icons text-compliance-green mb-2 text-3xl">eco</span>
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
    <section class="bg-institutional-blue py-16 text-white">
        <div class="container mx-auto px-6">
            <div class="mx-auto max-w-6xl">
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
                        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-blue-600">
                            <span class="material-icons text-2xl">trending_down</span>
                        </div>
                        <h3 class="mb-2 text-2xl font-bold">-70%</h3>
                        <p class="text-blue-200">Riduzione costi di certificazione</p>
                    </div>

                    <!-- Velocità -->
                    <div class="text-center">
                        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-blue-600">
                            <span class="material-icons text-2xl">speed</span>
                        </div>
                        <h3 class="mb-2 text-2xl font-bold">5x</h3>
                        <p class="text-blue-200">Velocità di elaborazione documenti</p>
                    </div>

                    <!-- Sicurezza -->
                    <div class="text-center">
                        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-blue-600">
                            <span class="material-icons text-2xl">security</span>
                        </div>
                        <h3 class="mb-2 text-2xl font-bold">100%</h3>
                        <p class="text-blue-200">Tracciabilità e immutabilità</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pilot Program -->
    <section class="bg-florence-gold py-16">
        <div class="container mx-auto px-6">
            <div class="mx-auto max-w-4xl text-center text-white">
                <h2 class="mb-6 text-3xl font-bold md:text-4xl">
                    Programma Pilota per PA
                </h2>
                <p class="mb-8 text-xl text-yellow-100">
                    Testa la piattaforma FlorenceEGI per 6 mesi senza costi di implementazione.
                    Valuta l'efficacia sui tuoi processi di certificazione prima dell'adozione definitiva.
                </p>
                <div class="rounded-xl bg-white p-8 text-florence-gold">
                    <h3 class="mb-4 text-2xl font-bold">Incluso nel Pilot Program:</h3>
                    <div class="grid gap-4 text-left md:grid-cols-2">
                        <div class="flex items-center">
                            <span class="material-icons mr-3">check_circle</span>
                            <span>Setup e configurazione completa</span>
                        </div>
                        <div class="flex items-center">
                            <span class="material-icons mr-3">check_circle</span>
                            <span>Formazione team tecnico</span>
                        </div>
                        <div class="flex items-center">
                            <span class="material-icons mr-3">check_circle</span>
                            <span>Integrazione sistemi esistenti</span>
                        </div>
                        <div class="flex items-center">
                            <span class="material-icons mr-3">check_circle</span>
                            <span>Supporto dedicato H24</span>
                        </div>
                        <div class="flex items-center">
                            <span class="material-icons mr-3">check_circle</span>
                            <span>Certificazione fino a 1000 asset</span>
                        </div>
                        <div class="flex items-center">
                            <span class="material-icons mr-3">check_circle</span>
                            <span>Report di impatto e ROI</span>
                        </div>
                    </div>
                </div>
                <div class="mt-8">
                    <button
                        class="bg-institutional-blue rounded-lg px-8 py-4 text-lg font-bold text-white transition hover:bg-blue-800">
                        <span class="material-icons mr-2">contact_mail</span>
                        Richiedi Pilot Program
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Contatti Istituzionali -->
    <section id="contatti" class="bg-gray-900 py-16 text-white">
        <div class="container mx-auto px-6">
            <div class="mx-auto max-w-4xl">
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
                                <span class="material-icons mr-3">email</span>
                                <span>pa-services@florenceegi.com</span>
                            </div>
                            <div class="flex items-center">
                                <span class="material-icons mr-3">phone</span>
                                <span>+39 055 XXX XXXX</span>
                            </div>
                            <div class="flex items-center">
                                <span class="material-icons mr-3">schedule</span>
                                <span>Lun-Ven 9:00-18:00</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="mb-4 text-xl font-bold">Supporto Tecnico</h3>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <span class="material-icons mr-3">support</span>
                                <span>support@florenceegi.com</span>
                            </div>
                            <div class="flex items-center">
                                <span class="material-icons mr-3">description</span>
                                <span>Documentazione API</span>
                            </div>
                            <div class="flex items-center">
                                <span class="material-icons mr-3">help</span>
                                <span>Centro Assistenza 24/7</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-12 rounded-xl bg-gray-800 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold">Richiedi una Demo Personalizzata</h3>
                            <p class="text-gray-300">Vedi FlorenceEGI in azione con i tuoi dati di test</p>
                        </div>
                        <button
                            class="rounded-lg bg-florence-gold px-6 py-3 font-semibold text-white transition hover:bg-yellow-600">
                            Prenota Demo
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-black py-8 text-white">
        <div class="container mx-auto px-6">
            <div class="flex flex-col items-center justify-between md:flex-row">
                <div class="mb-4 flex items-center space-x-4 md:mb-0">
                    <span class="material-icons text-2xl">account_balance</span>
                    <div>
                        <div class="font-bold">FlorenceEGI</div>
                        <div class="text-sm text-gray-400">Powered by Algorand • Built with Laravel</div>
                    </div>
                </div>
                <div class="flex space-x-6 text-sm">
                    <a href="/privacy-policy" class="transition hover:text-florence-gold">Privacy Policy</a>
                    <a href="/terms-conditions" class="transition hover:text-florence-gold">Terms & Conditions</a>
                    <a href="/compliance" class="transition hover:text-florence-gold">Compliance</a>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>
