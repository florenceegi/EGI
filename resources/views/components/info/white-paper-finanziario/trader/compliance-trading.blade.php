{{--
    Componente: Compliance Trading
    Sezione: Trader Pro
    Descrizione: Requisiti di conformità per i trader professionisti
--}}

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
            <x-heroicon-o-shield-check class="w-5 h-5 text-orange-600" />
        </div>
        <h3 class="text-lg font-semibold text-gray-900">
            Compliance per Trader Professionisti
        </h3>
    </div>

    <p class="text-gray-600 mb-6">
        I <a href="#glossario" class="text-primary-600 hover:underline font-medium">Trader Pro</a>
        operano con volumi elevati e hanno requisiti specifici di compliance normativa
        sia per la vendita di NFT che per le operazioni di trading.
    </p>

    <!-- Requisiti di Registrazione -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-identification class="w-5 h-5 text-orange-500" />
            Requisiti per Diventare Trader Pro
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-orange-50 rounded-lg p-4">
                <div class="font-medium text-orange-900 mb-2">Documentazione Richiesta</div>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li class="flex items-start gap-2">
                        <x-heroicon-o-check-circle class="w-4 h-4 text-orange-600 mt-0.5 shrink-0" />
                        <span>Partita IVA con codice ATECO appropriato</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <x-heroicon-o-check-circle class="w-4 h-4 text-orange-600 mt-0.5 shrink-0" />
                        <span>Visura camerale (se società)</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <x-heroicon-o-check-circle class="w-4 h-4 text-orange-600 mt-0.5 shrink-0" />
                        <span>Documento identità del legale rappresentante</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <x-heroicon-o-check-circle class="w-4 h-4 text-orange-600 mt-0.5 shrink-0" />
                        <span>Verifica KYC/KYB completata</span>
                    </li>
                </ul>
            </div>
            <div class="bg-blue-50 rounded-lg p-4">
                <div class="font-medium text-blue-900 mb-2">Codici ATECO Consigliati</div>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li><code class="bg-blue-100 px-1 rounded">47.91.10</code> - Commercio al dettaglio via internet</li>
                    <li><code class="bg-blue-100 px-1 rounded">63.11.19</code> - Altre elaborazioni elettroniche di dati</li>
                    <li><code class="bg-blue-100 px-1 rounded">90.03.09</code> - Altre creazioni artistiche</li>
                    <li><code class="bg-blue-100 px-1 rounded">64.99.60</code> - Intermediazione monetaria (se trading)</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Obblighi AML -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-eye class="w-5 h-5 text-red-500" />
            Obblighi Antiriciclaggio (AML)
        </h4>
        <div class="bg-red-50 rounded-lg p-4 border-l-4 border-red-400">
            <p class="text-sm text-gray-700 mb-3">
                I Trader Pro che operano con cripto-attività rientrano tra i <strong>soggetti obbligati</strong>
                ai sensi del D.Lgs. 231/2007 (normativa antiriciclaggio).
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <div class="font-medium text-red-900 mb-2">Obblighi Operativi</div>
                    <ul class="text-sm text-gray-700 space-y-1">
                        <li>• Adeguata verifica della clientela</li>
                        <li>• Conservazione documenti (10 anni)</li>
                        <li>• Segnalazione operazioni sospette (SOS)</li>
                        <li>• Formazione del personale</li>
                    </ul>
                </div>
                <div>
                    <div class="font-medium text-red-900 mb-2">Soglie di Attenzione</div>
                    <ul class="text-sm text-gray-700 space-y-1">
                        <li>• Operazioni > €10.000 → verifica rafforzata</li>
                        <li>• Operazioni frazionate sospette</li>
                        <li>• Clienti in paesi a rischio</li>
                        <li>• PEP (Persone Esposte Politicamente)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Registro OAM -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-building-office class="w-5 h-5 text-purple-500" />
            Iscrizione al Registro OAM
        </h4>
        <div class="bg-purple-50 rounded-lg p-4">
            <p class="text-sm text-gray-700 mb-3">
                Chi presta servizi relativi a cripto-attività deve iscriversi alla 
                <strong>Sezione Speciale del Registro OAM</strong> (Organismo Agenti e Mediatori).
            </p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-center">
                <div class="bg-white rounded-lg p-3 border border-purple-200">
                    <x-heroicon-o-document-plus class="w-6 h-6 text-purple-500 mx-auto mb-1" />
                    <div class="text-sm font-medium text-gray-900">Richiesta Online</div>
                    <p class="text-xs text-gray-500">Via portale OAM</p>
                </div>
                <div class="bg-white rounded-lg p-3 border border-purple-200">
                    <x-heroicon-o-banknotes class="w-6 h-6 text-purple-500 mx-auto mb-1" />
                    <div class="text-sm font-medium text-gray-900">Costo €500</div>
                    <p class="text-xs text-gray-500">Diritto di iscrizione</p>
                </div>
                <div class="bg-white rounded-lg p-3 border border-purple-200">
                    <x-heroicon-o-clock class="w-6 h-6 text-purple-500 mx-auto mb-1" />
                    <div class="text-sm font-medium text-gray-900">60 giorni</div>
                    <p class="text-xs text-gray-500">Tempo di approvazione</p>
                </div>
            </div>
        </div>
    </div>

    <!-- MiCA Regulation -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-4 border border-blue-200">
        <h5 class="font-semibold text-blue-900 mb-2 flex items-center gap-2">
            <x-heroicon-o-globe-europe-africa class="w-5 h-5" />
            Regolamento MiCA (EU 2023/1114)
        </h5>
        <p class="text-sm text-gray-700 mb-2">
            Dal <strong>30 dicembre 2024</strong> si applica il Regolamento MiCA (Markets in Crypto-Assets)
            che introduce requisiti uniformi per i prestatori di servizi cripto nell'UE.
        </p>
        <div class="flex flex-wrap gap-2 text-xs">
            <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded">Licenza CASP</span>
            <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded">Capitale minimo</span>
            <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded">Governance</span>
            <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded">White Paper obbligatorio</span>
        </div>
    </div>
</div>
