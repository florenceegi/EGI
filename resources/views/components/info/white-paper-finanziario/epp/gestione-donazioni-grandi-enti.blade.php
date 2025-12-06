{{--
    Componente: Gestione Donazioni Grandi Enti
    Sezione: EPP - Ente / Ente Progetto Partecipato
    Descrizione: Obblighi per enti di grandi dimensioni e aziende
--}}

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
            <x-heroicon-o-building-library class="w-5 h-5 text-indigo-600" />
        </div>
        <h3 class="text-lg font-semibold text-gray-900">
            Donazioni a Grandi Enti e Aziende
        </h3>
    </div>

    <p class="text-gray-600 mb-6">
        Gli enti di grandi dimensioni, le <a href="#glossario" class="text-primary-600 hover:underline font-medium">fondazioni</a>
        e le aziende che ricevono donazioni hanno obblighi più stringenti in termini di
        rendicontazione e trasparenza.
    </p>

    <!-- Categorie di Grandi Enti -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-squares-2x2 class="w-5 h-5 text-indigo-500" />
            Tipologie di Grandi Enti
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-indigo-50 rounded-lg p-4 text-center">
                <x-heroicon-o-building-office-2 class="w-8 h-8 text-indigo-600 mx-auto mb-2" />
                <div class="font-medium text-indigo-900">Fondazioni</div>
                <p class="text-xs text-gray-600 mt-1">Fondazioni bancarie, familiari, d'impresa</p>
            </div>
            <div class="bg-purple-50 rounded-lg p-4 text-center">
                <x-heroicon-o-user-group class="w-8 h-8 text-purple-600 mx-auto mb-2" />
                <div class="font-medium text-purple-900">Enti Pubblici</div>
                <p class="text-xs text-gray-600 mt-1">Comuni, ASL, Università, Musei</p>
            </div>
            <div class="bg-blue-50 rounded-lg p-4 text-center">
                <x-heroicon-o-briefcase class="w-8 h-8 text-blue-600 mx-auto mb-2" />
                <div class="font-medium text-blue-900">Società (SRL/SPA)</div>
                <p class="text-xs text-gray-600 mt-1">Aziende con programmi CSR</p>
            </div>
        </div>
    </div>

    <!-- Obblighi Contabili -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-calculator class="w-5 h-5 text-blue-500" />
            Obblighi Contabili e Fiscali
        </h4>
        <div class="space-y-3">
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center gap-3 mb-3">
                    <span class="bg-indigo-100 text-indigo-700 text-xs font-semibold px-2 py-1 rounded">FONDAZIONI</span>
                </div>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li>• Bilancio d'esercizio secondo principi OIC</li>
                    <li>• Bilancio di missione (per ETS)</li>
                    <li>• Revisione contabile obbligatoria se attivo > €1.100.000</li>
                    <li>• Pubblicazione trasparenza sul proprio sito</li>
                </ul>
            </div>
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center gap-3 mb-3">
                    <span class="bg-purple-100 text-purple-700 text-xs font-semibold px-2 py-1 rounded">ENTI PUBBLICI</span>
                </div>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li>• Contabilità pubblica secondo D.Lgs. 118/2011</li>
                    <li>• Obbligo di <strong>CIG</strong> per donazioni vincolate</li>
                    <li>• Registrazione su <strong>SIOPE+</strong></li>
                    <li>• Pubblicazione in Amministrazione Trasparente</li>
                </ul>
            </div>
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center gap-3 mb-3">
                    <span class="bg-blue-100 text-blue-700 text-xs font-semibold px-2 py-1 rounded">SOCIETÀ</span>
                </div>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li>• Donazioni ricevute = ricavi (soggetti a IRES)</li>
                    <li>• Fatturazione elettronica se donazione è corrispettivo</li>
                    <li>• Verifica natura: donazione vs sponsorizzazione</li>
                    <li>• Documentazione per deducibilità del donatore</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Differenza Donazione vs Sponsorizzazione -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-scale class="w-5 h-5 text-amber-500" />
            Donazione vs Sponsorizzazione
        </h4>
        <div class="bg-amber-50 rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <div class="font-medium text-amber-900 mb-2">✓ Donazione (Erogazione Liberale)</div>
                    <ul class="text-sm text-gray-700 space-y-1">
                        <li>• Nessuna controprestazione</li>
                        <li>• <strong>Esente IVA</strong></li>
                        <li>• Detrazione/deduzione per donatore</li>
                        <li>• Ricevuta di donazione sufficiente</li>
                    </ul>
                </div>
                <div>
                    <div class="font-medium text-amber-900 mb-2">⚠ Sponsorizzazione</div>
                    <ul class="text-sm text-gray-700 space-y-1">
                        <li>• Controprestazione pubblicitaria</li>
                        <li>• <strong>Soggetta a IVA 22%</strong></li>
                        <li>• Costo deducibile per sponsor</li>
                        <li>• Richiede fattura elettronica</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Importante -->
    <div class="bg-red-50 rounded-lg p-4 border-l-4 border-red-400">
        <div class="flex gap-3">
            <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-red-500 shrink-0" />
            <div>
                <h5 class="font-medium text-red-900 mb-1">Attenzione alla Qualificazione</h5>
                <p class="text-sm text-red-700">
                    L'Agenzia delle Entrate può riqualificare una donazione in sponsorizzazione
                    se emerge una controprestazione. Documentare sempre la natura liberale dell'erogazione.
                </p>
            </div>
        </div>
    </div>
</div>
