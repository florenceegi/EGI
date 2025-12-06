{{--
    Componente: Registrazione e Autenticazione
    Sezione: Merchant & Pagamenti
    Descrizione: Processo di onboarding e verifica identità per merchant
--}}

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 bg-cyan-100 rounded-lg flex items-center justify-center">
            <x-heroicon-o-identification class="w-5 h-5 text-cyan-600" />
        </div>
        <h3 class="text-lg font-semibold text-gray-900">
            Registrazione e Autenticazione
        </h3>
    </div>

    <p class="text-gray-600 mb-6">
        L'onboarding dei <a href="#glossario" class="text-primary-600 hover:underline font-medium">merchant</a>
        (Creator, Trader, EPP) richiede una verifica dell'identità conforme alle normative
        <a href="#glossario" class="text-primary-600 hover:underline font-medium">KYC</a> e antiriciclaggio.
    </p>

    <!-- Livelli di Verifica -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-shield-check class="w-5 h-5 text-cyan-500" />
            Livelli di Verifica
        </h4>
        <div class="space-y-3">
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <span class="bg-gray-100 text-gray-700 text-xs font-semibold px-2 py-1 rounded">LIVELLO 1</span>
                        <span class="font-medium text-gray-900">Base</span>
                    </div>
                    <span class="text-sm text-gray-500">Limite: €1.000/mese</span>
                </div>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li>• Email verificata</li>
                    <li>• Numero telefono verificato</li>
                    <li>• Accettazione termini e condizioni</li>
                </ul>
            </div>
            <div class="border border-cyan-200 rounded-lg p-4 bg-cyan-50">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <span class="bg-cyan-200 text-cyan-800 text-xs font-semibold px-2 py-1 rounded">LIVELLO 2</span>
                        <span class="font-medium text-gray-900">Verificato</span>
                    </div>
                    <span class="text-sm text-cyan-700">Limite: €15.000/mese</span>
                </div>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li>• Documento identità (CI/Passaporto)</li>
                    <li>• Codice Fiscale</li>
                    <li>• Selfie con documento</li>
                    <li>• Indirizzo di residenza</li>
                </ul>
            </div>
            <div class="border border-green-200 rounded-lg p-4 bg-green-50">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <span class="bg-green-200 text-green-800 text-xs font-semibold px-2 py-1 rounded">LIVELLO 3</span>
                        <span class="font-medium text-gray-900">Pro / Business</span>
                    </div>
                    <span class="text-sm text-green-700">Limite: Illimitato</span>
                </div>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li>• Partita IVA / Visura camerale</li>
                    <li>• Statuto (per enti)</li>
                    <li>• Prova indirizzo recente</li>
                    <li>• Titolare effettivo (UBO)</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Processo KYC -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-user-circle class="w-5 h-5 text-blue-500" />
            Processo KYC (Know Your Customer)
        </h4>
        <div class="bg-blue-50 rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="text-center">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2">
                        <span class="text-blue-600 font-bold">1</span>
                    </div>
                    <div class="text-sm font-medium text-gray-900">Upload Documenti</div>
                    <p class="text-xs text-gray-500">CI fronte/retro</p>
                </div>
                <div class="text-center">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2">
                        <span class="text-blue-600 font-bold">2</span>
                    </div>
                    <div class="text-sm font-medium text-gray-900">Verifica Automatica</div>
                    <p class="text-xs text-gray-500">AI + OCR</p>
                </div>
                <div class="text-center">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2">
                        <span class="text-blue-600 font-bold">3</span>
                    </div>
                    <div class="text-sm font-medium text-gray-900">Controllo AML</div>
                    <p class="text-xs text-gray-500">Liste sanzioni</p>
                </div>
                <div class="text-center">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-2">
                        <x-heroicon-o-check class="w-5 h-5 text-green-600" />
                    </div>
                    <div class="text-sm font-medium text-gray-900">Approvazione</div>
                    <p class="text-xs text-gray-500">~24-48h</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Autenticazione -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-key class="w-5 h-5 text-amber-500" />
            Metodi di Autenticazione
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-amber-50 rounded-lg p-4 text-center">
                <x-heroicon-o-lock-closed class="w-8 h-8 text-amber-600 mx-auto mb-2" />
                <div class="font-medium text-amber-900">Password + 2FA</div>
                <p class="text-xs text-gray-500 mt-1">TOTP (Google Auth)</p>
            </div>
            <div class="bg-purple-50 rounded-lg p-4 text-center">
                <x-heroicon-o-finger-print class="w-8 h-8 text-purple-600 mx-auto mb-2" />
                <div class="font-medium text-purple-900">Biometrico</div>
                <p class="text-xs text-gray-500 mt-1">Face ID / Touch ID</p>
            </div>
            <div class="bg-blue-50 rounded-lg p-4 text-center">
                <x-heroicon-o-device-phone-mobile class="w-8 h-8 text-blue-600 mx-auto mb-2" />
                <div class="font-medium text-blue-900">SMS OTP</div>
                <p class="text-xs text-gray-500 mt-1">Codice via SMS</p>
            </div>
        </div>
    </div>

    <!-- Note Sicurezza -->
    <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-cyan-400">
        <h5 class="font-medium text-gray-900 mb-2">Sicurezza dei Dati</h5>
        <ul class="text-sm text-gray-600 space-y-1">
            <li>• I documenti sono crittografati (AES-256) e conservati su server EU</li>
            <li>• I dati KYC sono gestiti in conformità GDPR</li>
            <li>• Possibilità di richiedere cancellazione dati (art. 17 GDPR)</li>
        </ul>
    </div>
</div>
