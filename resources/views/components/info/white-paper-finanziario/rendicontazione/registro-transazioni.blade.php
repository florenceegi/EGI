{{--
    Componente: Registro Transazioni
    Sezione: Rendicontazione & Archiviazione
    Descrizione: Sistema di registrazione e tracciamento delle transazioni
--}}

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 bg-slate-100 rounded-lg flex items-center justify-center">
            <x-heroicon-o-clipboard-document-list class="w-5 h-5 text-slate-600" />
        </div>
        <h3 class="text-lg font-semibold text-gray-900">
            Registro Transazioni
        </h3>
    </div>

    <p class="text-gray-600 mb-6">
        Ogni transazione su FlorenceEGI viene registrata in un
        <a href="#glossario" class="text-primary-600 hover:underline font-medium">registro immutabile</a>
        che garantisce tracciabilità completa e conformità alle normative fiscali e antiriciclaggio.
    </p>

    <!-- Dati Registrati -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-document-text class="w-5 h-5 text-slate-500" />
            Dati Registrati per Ogni Transazione
        </h4>
        <div class="bg-slate-50 rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <div class="font-medium text-slate-900 mb-2">Identificativi</div>
                    <ul class="text-sm text-gray-700 space-y-1">
                        <li>• ID transazione univoco (UUID)</li>
                        <li>• Riferimento PSP</li>
                        <li>• Hash blockchain (se NFT)</li>
                        <li>• Numero progressivo interno</li>
                    </ul>
                </div>
                <div>
                    <div class="font-medium text-slate-900 mb-2">Parti Coinvolte</div>
                    <ul class="text-sm text-gray-700 space-y-1">
                        <li>• ID pagante (anonimizzato)</li>
                        <li>• ID beneficiario</li>
                        <li>• Ruoli (Creator/EPP/Trader)</li>
                        <li>• Dati fiscali (se P.IVA)</li>
                    </ul>
                </div>
                <div>
                    <div class="font-medium text-slate-900 mb-2">Dettagli Economici</div>
                    <ul class="text-sm text-gray-700 space-y-1">
                        <li>• Importo lordo</li>
                        <li>• Fee applicate (dettaglio)</li>
                        <li>• IVA (se applicabile)</li>
                        <li>• Importo netto per beneficiario</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Tipi di Transazione -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-squares-2x2 class="w-5 h-5 text-blue-500" />
            Tipologie di Transazione Tracciate
        </h4>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div class="bg-green-50 rounded-lg p-3 text-center">
                <x-heroicon-o-shopping-cart class="w-6 h-6 text-green-600 mx-auto mb-1" />
                <div class="text-sm font-medium text-green-900">Vendita</div>
                <span class="text-xs text-gray-500">NFT / Servizi</span>
            </div>
            <div class="bg-purple-50 rounded-lg p-3 text-center">
                <x-heroicon-o-heart class="w-6 h-6 text-purple-600 mx-auto mb-1" />
                <div class="text-sm font-medium text-purple-900">Donazione</div>
                <span class="text-xs text-gray-500">EPP / Creator</span>
            </div>
            <div class="bg-blue-50 rounded-lg p-3 text-center">
                <x-heroicon-o-banknotes class="w-6 h-6 text-blue-600 mx-auto mb-1" />
                <div class="text-sm font-medium text-blue-900">Payout</div>
                <span class="text-xs text-gray-500">Prelievo IBAN</span>
            </div>
            <div class="bg-amber-50 rounded-lg p-3 text-center">
                <x-heroicon-o-arrow-path class="w-6 h-6 text-amber-600 mx-auto mb-1" />
                <div class="text-sm font-medium text-amber-900">Rimborso</div>
                <span class="text-xs text-gray-500">Storno</span>
            </div>
        </div>
    </div>

    <!-- Esempio Record -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-code-bracket class="w-5 h-5 text-purple-500" />
            Esempio Record Transazione
        </h4>
        <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
            <pre class="text-sm text-green-400"><code>{
  "transaction_id": "txn_8f7a2b3c-4d5e-6f7a-8b9c-0d1e2f3a4b5c",
  "type": "sale",
  "timestamp": "2025-12-06T14:32:18.456Z",
  "amount": {
    "gross": 100.00,
    "currency": "EUR",
    "fee_platform": 5.00,
    "fee_psp": 1.65,
    "net_creator": 68.00,
    "net_epp": 20.00,
    "net_frangette": 2.00
  },
  "parties": {
    "payer": "usr_***masked***",
    "beneficiaries": ["cre_abc123", "epp_xyz789"]
  },
  "asset": {
    "type": "NFT",
    "id": "nft_12345",
    "blockchain_hash": "0xabc...def"
  },
  "fiscal": {
    "vat_applicable": true,
    "vat_rate": 22,
    "invoice_id": "FE-2025-001234"
  }
}</code></pre>
        </div>
    </div>

    <!-- Ricerca e Filtri -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-magnifying-glass class="w-5 h-5 text-green-500" />
            Ricerca e Filtri
        </h4>
        <div class="bg-green-50 rounded-lg p-4">
            <p class="text-sm text-gray-700 mb-3">
                Il registro supporta ricerche avanzate per:
            </p>
            <div class="flex flex-wrap gap-2">
                <span class="bg-white text-green-700 text-sm px-3 py-1 rounded-full border border-green-200">Data/Periodo</span>
                <span class="bg-white text-green-700 text-sm px-3 py-1 rounded-full border border-green-200">Tipo transazione</span>
                <span class="bg-white text-green-700 text-sm px-3 py-1 rounded-full border border-green-200">Importo (range)</span>
                <span class="bg-white text-green-700 text-sm px-3 py-1 rounded-full border border-green-200">Beneficiario</span>
                <span class="bg-white text-green-700 text-sm px-3 py-1 rounded-full border border-green-200">Stato</span>
                <span class="bg-white text-green-700 text-sm px-3 py-1 rounded-full border border-green-200">NFT/Collezione</span>
            </div>
        </div>
    </div>

    <!-- Note -->
    <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-slate-400">
        <h5 class="font-medium text-gray-900 mb-2">Immutabilità del Registro</h5>
        <p class="text-sm text-gray-600">
            I record delle transazioni sono <strong>immutabili</strong>: eventuali modifiche
            (es. rimborsi) generano nuovi record collegati all'originale, mantenendo
            la tracciabilità completa della storia.
        </p>
    </div>
</div>
