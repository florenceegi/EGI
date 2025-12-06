{{--
    Componente: Gestione Donazioni ETS/ONLUS
    Sezione: EPP - Ente / Ente Progetto Partecipato
    Descrizione: Obblighi e adempimenti per piccoli enti del terzo settore
--}}

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
            <x-heroicon-o-heart class="w-5 h-5 text-purple-600" />
        </div>
        <h3 class="text-lg font-semibold text-gray-900">
            Donazioni a Piccoli Enti (ETS/ONLUS)
        </h3>
    </div>

    <p class="text-gray-600 mb-6">
        Gli <a href="#glossario" class="text-primary-600 hover:underline font-medium">Enti del Terzo Settore (ETS)</a>
        e le <a href="#glossario" class="text-primary-600 hover:underline font-medium">ONLUS</a> beneficiano
        di un regime agevolato per le donazioni ricevute. Ecco gli obblighi fiscali:
    </p>

    <!-- Requisiti per ETS -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-clipboard-document-check class="w-5 h-5 text-purple-500" />
            Requisiti per l'iscrizione al RUNTS
        </h4>
        <div class="bg-purple-50 rounded-lg p-4">
            <ul class="space-y-2 text-sm text-gray-700">
                <li class="flex items-start gap-2">
                    <x-heroicon-o-check-circle class="w-4 h-4 text-purple-600 mt-0.5 shrink-0" />
                    <span>Iscrizione al <strong>RUNTS</strong> (Registro Unico Nazionale Terzo Settore)</span>
                </li>
                <li class="flex items-start gap-2">
                    <x-heroicon-o-check-circle class="w-4 h-4 text-purple-600 mt-0.5 shrink-0" />
                    <span>Statuto conforme al <strong>D.Lgs. 117/2017</strong> (Codice del Terzo Settore)</span>
                </li>
                <li class="flex items-start gap-2">
                    <x-heroicon-o-check-circle class="w-4 h-4 text-purple-600 mt-0.5 shrink-0" />
                    <span>Bilancio o rendiconto annuale depositato</span>
                </li>
                <li class="flex items-start gap-2">
                    <x-heroicon-o-check-circle class="w-4 h-4 text-purple-600 mt-0.5 shrink-0" />
                    <span>Assenza di scopo di lucro (divieto di distribuzione utili)</span>
                </li>
            </ul>
        </div>
    </div>

    <!-- Obblighi Fiscali -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-document-text class="w-5 h-5 text-blue-500" />
            Obblighi Fiscali per le Donazioni
        </h4>
        <div class="space-y-3">
            <div class="bg-blue-50 rounded-lg p-4">
                <div class="font-medium text-blue-900 mb-2">Donazioni fino a €220.000/anno</div>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li>• <strong>Nessun obbligo</strong> di fatturazione (le donazioni sono esenti IVA)</li>
                    <li>• Obbligo di <strong>ricevuta di donazione</strong> se richiesta dal donatore</li>
                    <li>• Registrazione nel <strong>libro giornale</strong> o registro donazioni</li>
                </ul>
            </div>
            <div class="bg-amber-50 rounded-lg p-4">
                <div class="font-medium text-amber-900 mb-2">Donazioni oltre €220.000/anno</div>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li>• Obbligo di <strong>bilancio sociale</strong></li>
                    <li>• Pubblicazione sul sito dell'ente</li>
                    <li>• Revisione legale se richiesta da statuto</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Benefici per i Donatori -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-gift class="w-5 h-5 text-green-500" />
            Benefici Fiscali per i Donatori
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-green-50 rounded-lg p-4">
                <div class="font-medium text-green-900 mb-2">Persone Fisiche</div>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li>• <strong>Detrazione 30%</strong> fino a €30.000/anno</li>
                    <li>• Oppure <strong>deduzione</strong> fino al 10% del reddito</li>
                    <li>• Necessaria ricevuta con dati completi</li>
                </ul>
            </div>
            <div class="bg-green-50 rounded-lg p-4">
                <div class="font-medium text-green-900 mb-2">Aziende</div>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li>• <strong>Deduzione</strong> fino al 10% del reddito</li>
                    <li>• Eccedenza riportabile nei 4 anni successivi</li>
                    <li>• Necessaria documentazione tracciabile</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Note Importanti -->
    <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-purple-400">
        <h5 class="font-medium text-gray-900 mb-2">Note per gli ETS su FlorenceEGI</h5>
        <ul class="text-sm text-gray-600 space-y-1">
            <li>• La piattaforma genera automaticamente le <strong>ricevute di donazione</strong></li>
            <li>• I dati sono pre-compilati per la rendicontazione RUNTS</li>
            <li>• Le donazioni EPP sono tracciate separatamente nel registro</li>
        </ul>
    </div>
</div>
