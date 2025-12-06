{{--
    Componente: Gestione Ricevute di Donazione
    Sezione: EPP - Ente / Ente Progetto Partecipato
    Descrizione: Come vengono generate e gestite le ricevute per le donazioni
--}}

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
            <x-heroicon-o-document-check class="w-5 h-5 text-emerald-600" />
        </div>
        <h3 class="text-lg font-semibold text-gray-900">
            Gestione Ricevute di Donazione
        </h3>
    </div>

    <p class="text-gray-600 mb-6">
        La <a href="#glossario" class="text-primary-600 hover:underline font-medium">ricevuta di donazione</a>
        è il documento che attesta l'avvenuta erogazione liberale e permette al donatore
        di beneficiare delle agevolazioni fiscali.
    </p>

    <!-- Contenuto Obbligatorio -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-clipboard-document-list class="w-5 h-5 text-emerald-500" />
            Contenuto Obbligatorio della Ricevuta
        </h4>
        <div class="bg-emerald-50 rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <div class="font-medium text-emerald-900 mb-2">Dati dell'Ente (Beneficiario)</div>
                    <ul class="text-sm text-gray-700 space-y-1">
                        <li>• Denominazione completa</li>
                        <li>• Codice Fiscale / P.IVA</li>
                        <li>• Sede legale</li>
                        <li>• Numero iscrizione RUNTS (se ETS)</li>
                    </ul>
                </div>
                <div>
                    <div class="font-medium text-emerald-900 mb-2">Dati del Donatore</div>
                    <ul class="text-sm text-gray-700 space-y-1">
                        <li>• Nome e Cognome / Ragione Sociale</li>
                        <li>• Codice Fiscale</li>
                        <li>• Indirizzo (opzionale)</li>
                        <li>• Riferimento transazione</li>
                    </ul>
                </div>
            </div>
            <hr class="my-4 border-emerald-200" />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <div class="font-medium text-emerald-900 mb-2">Dati della Donazione</div>
                    <ul class="text-sm text-gray-700 space-y-1">
                        <li>• Importo in lettere e cifre</li>
                        <li>• Data della donazione</li>
                        <li>• Metodo di pagamento</li>
                        <li>• Numero progressivo ricevuta</li>
                    </ul>
                </div>
                <div>
                    <div class="font-medium text-emerald-900 mb-2">Dichiarazioni Obbligatorie</div>
                    <ul class="text-sm text-gray-700 space-y-1">
                        <li>• Natura liberale dell'erogazione</li>
                        <li>• Assenza di controprestazione</li>
                        <li>• Riferimento normativo (Art. 83 D.Lgs 117/2017)</li>
                        <li>• Firma del legale rappresentante</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Generazione Automatica -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-cog-6-tooth class="w-5 h-5 text-blue-500" />
            Generazione Automatica su FlorenceEGI
        </h4>
        <div class="space-y-3">
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-semibold text-sm shrink-0">1</div>
                    <div>
                        <div class="font-medium text-gray-900">Donazione Ricevuta</div>
                        <p class="text-sm text-gray-600">Il sistema registra automaticamente la transazione con tutti i dati del donatore e dell'importo.</p>
                    </div>
                </div>
            </div>
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-semibold text-sm shrink-0">2</div>
                    <div>
                        <div class="font-medium text-gray-900">Ricevuta Generata</div>
                        <p class="text-sm text-gray-600">La ricevuta viene creata in formato PDF con numero progressivo e tutti i campi obbligatori.</p>
                    </div>
                </div>
            </div>
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-semibold text-sm shrink-0">3</div>
                    <div>
                        <div class="font-medium text-gray-900">Invio al Donatore</div>
                        <p class="text-sm text-gray-600">La ricevuta viene inviata via email al donatore e archiviata nel sistema dell'ente.</p>
                    </div>
                </div>
            </div>
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-semibold text-sm shrink-0">4</div>
                    <div>
                        <div class="font-medium text-gray-900">Conservazione Digitale</div>
                        <p class="text-sm text-gray-600">Le ricevute sono conservate per 10 anni secondo le normative sulla conservazione sostitutiva.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tempistiche -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-clock class="w-5 h-5 text-amber-500" />
            Tempistiche di Emissione
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-green-50 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-green-600">Immediata</div>
                <div class="text-sm text-gray-600">Donazioni online</div>
                <p class="text-xs text-gray-500 mt-1">Invio automatico post-transazione</p>
            </div>
            <div class="bg-amber-50 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-amber-600">30 giorni</div>
                <div class="text-sm text-gray-600">Donazioni bancarie</div>
                <p class="text-xs text-gray-500 mt-1">Dopo verifica accredito</p>
            </div>
            <div class="bg-blue-50 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-blue-600">Fine anno</div>
                <div class="text-sm text-gray-600">Riepilogo annuale</div>
                <p class="text-xs text-gray-500 mt-1">Per dichiarazione dei redditi</p>
            </div>
        </div>
    </div>

    <!-- Info Box -->
    <div class="bg-blue-50 rounded-lg p-4 border-l-4 border-blue-400">
        <h5 class="font-medium text-blue-900 mb-2">Tracciabilità dei Pagamenti</h5>
        <p class="text-sm text-blue-700">
            Per beneficiare delle detrazioni/deduzioni fiscali, le donazioni devono essere effettuate
            con metodi tracciabili: bonifico, carta di credito/debito, assegno bancario.
            <strong>I contanti non danno diritto ad agevolazioni fiscali.</strong>
        </p>
    </div>
</div>
