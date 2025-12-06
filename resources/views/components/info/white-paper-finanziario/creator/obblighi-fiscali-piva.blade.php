{{-- Componente: Obblighi Fiscali per Partita IVA --}}
{{-- Guida fiscale per Creator con Partita IVA --}}

<div class="mt-8">
    <div class="rounded-lg bg-gray-50 p-6">
        <h4 class="mb-3 text-lg font-semibold text-gray-800">
            <span class="material-icons mr-2 align-middle text-emerald-600">business</span>
            Se hai <a href="#glossary-partita-iva" class="glossary-link">Partita IVA</a>
        </h4>

        <p class="mb-4 text-gray-700">
            Per vendite professionali di <a href="#glossary-egi" class="glossary-link">EGI</a>,
            devi rispettare gli obblighi tipici della tua categoria:
        </p>

        <div class="space-y-4">
            {{-- Fatturazione Elettronica --}}
            <div class="rounded border-l-4 border-emerald-500 bg-white p-4">
                <h5 class="font-semibold text-emerald-800">📄 Fatturazione Elettronica Obbligatoria</h5>
                <p class="mt-1 text-sm text-gray-700">
                    È obbligatoria la <a href="#glossary-fatturazione-elettronica" class="glossary-link">fatturazione elettronica</a>
                    per ogni incasso ricevuto, da inviare tramite
                    <a href="#glossary-sdi" class="glossary-link">SDI</a>.
                </p>
                <p class="mt-2 text-xs text-emerald-600">
                    💡 FlorenceEGI ti fornisce i dati necessari per l'emissione della fattura nella tua dashboard.
                </p>
            </div>

            {{-- Regime IVA --}}
            <div class="rounded border-l-4 border-blue-500 bg-white p-4">
                <h5 class="font-semibold text-blue-800">💶 Applicazione IVA</h5>
                <p class="mt-1 text-sm text-gray-700">
                    Applica l'<a href="#glossary-iva" class="glossary-link">IVA</a> secondo il tuo regime fiscale:
                </p>
                <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-gray-600">
                    <li><strong>Regime Ordinario:</strong> IVA 22% (cessione beni digitali)</li>
                    <li><strong>Regime Forfettario:</strong> Esente IVA, ma con limitazioni</li>
                    <li><strong>Vendite B2B UE:</strong> <a href="#glossary-reverse-charge" class="glossary-link">Reverse Charge</a></li>
                </ul>
            </div>

            {{-- Codice ATECO --}}
            <div class="rounded border-l-4 border-purple-500 bg-white p-4">
                <h5 class="font-semibold text-purple-800">🏷️ Codice ATECO Consigliato</h5>
                <p class="mt-1 text-sm text-gray-700">
                    Per la vendita di opere d'arte digitali (NFT/EGI), i codici ATECO più utilizzati sono:
                </p>
                <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-gray-600">
                    <li><strong>90.03.09</strong> - Altre creazioni artistiche e letterarie</li>
                    <li><strong>74.10.21</strong> - Attività dei disegnatori grafici di pagine web</li>
                    <li><strong>59.11.00</strong> - Attività di produzione cinematografica, video e tv</li>
                </ul>
                <p class="mt-2 text-xs text-purple-600">
                    ⚠️ Consulta il tuo commercialista per la scelta del codice più appropriato.
                </p>
            </div>

            {{-- Obblighi Periodici --}}
            <div class="rounded border-l-4 border-amber-500 bg-white p-4">
                <h5 class="font-semibold text-amber-800">📅 Obblighi Periodici</h5>
                <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-gray-700">
                    <li>Liquidazione IVA (mensile/trimestrale)</li>
                    <li>Comunicazione LIPE (trimestrale)</li>
                    <li>Dichiarazione IVA annuale</li>
                    <li>Dichiarazione dei redditi</li>
                </ul>
            </div>
        </div>

        {{-- Export dati --}}
        <div class="mt-6 rounded-lg border border-emerald-300 bg-emerald-50 p-4">
            <h5 class="flex items-center font-semibold text-emerald-800">
                <span class="material-icons mr-2">download</span>
                Export per il Commercialista
            </h5>
            <p class="mt-1 text-sm text-emerald-700">
                Dalla tua <a href="#glossary-dashboard" class="glossary-link">dashboard</a> puoi scaricare:
            </p>
            <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-emerald-700">
                <li><a href="#glossary-export-csv" class="glossary-link">Export CSV/XML</a> di tutte le transazioni</li>
                <li>Report riepilogativi mensili/annuali</li>
                <li>Dati pre-compilati per fatturazione</li>
            </ul>
        </div>
    </div>
</div>
