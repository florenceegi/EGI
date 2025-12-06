{{--
    Componente: Ambito Normativo
    Sezione: Rendicontazione & Archiviazione
    Descrizione: Quadro normativo di riferimento per la piattaforma
--}}

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 bg-rose-100 rounded-lg flex items-center justify-center">
            <x-heroicon-o-scale class="w-5 h-5 text-rose-600" />
        </div>
        <h3 class="text-lg font-semibold text-gray-900">
            Ambito Normativo
        </h3>
    </div>

    <p class="text-gray-600 mb-6">
        FlorenceEGI opera nel rispetto di un articolato quadro normativo
        italiano ed europeo. Di seguito le principali normative di riferimento.
    </p>

    <!-- Normativa Fiscale -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-calculator class="w-5 h-5 text-rose-500" />
            Normativa Fiscale
        </h4>
        <div class="space-y-3">
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <span class="bg-rose-100 text-rose-700 text-xs font-semibold px-2 py-1 rounded mt-1">IVA</span>
                    <div>
                        <div class="font-medium text-gray-900">DPR 633/1972</div>
                        <p class="text-sm text-gray-600">Disciplina dell'Imposta sul Valore Aggiunto</p>
                    </div>
                </div>
            </div>
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <span class="bg-blue-100 text-blue-700 text-xs font-semibold px-2 py-1 rounded mt-1">IRPEF</span>
                    <div>
                        <div class="font-medium text-gray-900">DPR 917/1986 (TUIR)</div>
                        <p class="text-sm text-gray-600">Testo Unico delle Imposte sui Redditi</p>
                    </div>
                </div>
            </div>
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <span class="bg-purple-100 text-purple-700 text-xs font-semibold px-2 py-1 rounded mt-1">CRYPTO</span>
                    <div>
                        <div class="font-medium text-gray-900">L. 197/2022 (art. 1, commi 126-147)</div>
                        <p class="text-sm text-gray-600">Tassazione cripto-attività (plusvalenze 26%)</p>
                    </div>
                </div>
            </div>
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <span class="bg-green-100 text-green-700 text-xs font-semibold px-2 py-1 rounded mt-1">ETS</span>
                    <div>
                        <div class="font-medium text-gray-900">D.Lgs. 117/2017</div>
                        <p class="text-sm text-gray-600">Codice del Terzo Settore (donazioni, ETS, RUNTS)</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Normativa Pagamenti -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-credit-card class="w-5 h-5 text-blue-500" />
            Normativa Pagamenti
        </h4>
        <div class="bg-blue-50 rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <div class="font-medium text-blue-900 mb-2">Europa</div>
                    <ul class="text-sm text-gray-700 space-y-2">
                        <li>
                            <strong>PSD2</strong> - Direttiva 2015/2366/UE
                            <p class="text-xs text-gray-500">Servizi di pagamento nel mercato interno</p>
                        </li>
                        <li>
                            <strong>MiCA</strong> - Reg. UE 2023/1114
                            <p class="text-xs text-gray-500">Markets in Crypto-Assets (dal 30/12/2024)</p>
                        </li>
                    </ul>
                </div>
                <div>
                    <div class="font-medium text-blue-900 mb-2">Italia</div>
                    <ul class="text-sm text-gray-700 space-y-2">
                        <li>
                            <strong>D.Lgs. 11/2010</strong>
                            <p class="text-xs text-gray-500">Recepimento PSD2</p>
                        </li>
                        <li>
                            <strong>Registro OAM</strong>
                            <p class="text-xs text-gray-500">Operatori cripto-attività</p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Normativa Privacy -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-shield-check class="w-5 h-5 text-green-500" />
            Normativa Privacy e Sicurezza
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-green-50 rounded-lg p-4">
                <div class="font-medium text-green-900 mb-2">GDPR - Reg. UE 679/2016</div>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li>• Trattamento dati personali</li>
                    <li>• Diritti degli interessati</li>
                    <li>• Accountability del titolare</li>
                </ul>
            </div>
            <div class="bg-green-50 rounded-lg p-4">
                <div class="font-medium text-green-900 mb-2">D.Lgs. 196/2003 (mod.)</div>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li>• Codice Privacy italiano</li>
                    <li>• Adeguamento al GDPR</li>
                    <li>• Provvedimenti Garante</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Normativa AML -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-eye class="w-5 h-5 text-amber-500" />
            Normativa Antiriciclaggio (AML)
        </h4>
        <div class="bg-amber-50 rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <div class="font-medium text-amber-900 mb-2">D.Lgs. 231/2007</div>
                    <ul class="text-sm text-gray-700 space-y-1">
                        <li>• Obblighi antiriciclaggio</li>
                        <li>• Adeguata verifica</li>
                        <li>• Segnalazione operazioni sospette</li>
                    </ul>
                </div>
                <div>
                    <div class="font-medium text-amber-900 mb-2">Direttive UE AML</div>
                    <ul class="text-sm text-gray-700 space-y-1">
                        <li>• 5AMLD - Direttiva 2018/843</li>
                        <li>• 6AMLD - Direttiva 2018/1673</li>
                        <li>• Estensione a VASP</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Normativa Digitale -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-document-text class="w-5 h-5 text-purple-500" />
            Normativa Documentale
        </h4>
        <div class="bg-purple-50 rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <div class="font-medium text-purple-900 mb-2">Fatturazione Elettronica</div>
                    <ul class="text-sm text-gray-700 space-y-1">
                        <li>• D.Lgs. 127/2015</li>
                        <li>• D.M. 3/04/2013 n. 55</li>
                        <li>• Provvedimenti AdE</li>
                    </ul>
                </div>
                <div>
                    <div class="font-medium text-purple-900 mb-2">Conservazione Digitale</div>
                    <ul class="text-sm text-gray-700 space-y-1">
                        <li>• CAD - D.Lgs. 82/2005</li>
                        <li>• DPCM 3/12/2013</li>
                        <li>• Linee Guida AgID</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Disclaimer -->
    <div class="bg-gray-100 rounded-lg p-4 text-xs text-gray-500">
        <strong>Disclaimer:</strong> Questo documento ha finalità informative e non costituisce
        consulenza legale o fiscale. Le normative sono soggette a modifiche. Si raccomanda
        di consultare un professionista qualificato per la corretta applicazione delle norme
        al proprio caso specifico.
    </div>
</div>
