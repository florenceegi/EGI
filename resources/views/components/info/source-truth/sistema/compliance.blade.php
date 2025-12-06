{{--
    Componente: Compliance e Sicurezza
    Sezione: Sistema
    Descrizione: Conformità totale a GDPR e MiCA attraverso architettura by-design
--}}

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
            <x-heroicon-o-shield-check class="w-5 h-5 text-green-600" />
        </div>
        <h3 class="text-lg font-semibold text-gray-900">
            Compliance e Sicurezza
        </h3>
    </div>

    <p class="text-gray-600 mb-6">
        Conformità totale a GDPR e MiCA attraverso architettura by-design.
    </p>

    <!-- GDPR e MiCA Cards -->
    <div class="grid gap-6 md:grid-cols-2 mb-8">
        <!-- GDPR -->
        <div class="p-6 rounded-lg bg-emerald-50 border-2 border-emerald-200">
            <h4 class="mb-4 text-xl font-bold text-emerald-700 flex items-center gap-2">
                <x-heroicon-o-lock-closed class="w-6 h-6 text-emerald-600" />
                <a href="#glossario" class="hover:underline">GDPR</a>-by-design
            </h4>
            <p class="mb-4 text-gray-700">
                Ogni azione utente è tracciata e documentata tramite:
            </p>
            <ul class="space-y-3 text-gray-700">
                <li class="flex items-start gap-2">
                    <x-heroicon-o-document-text class="w-5 h-5 text-emerald-600 mt-0.5 shrink-0" />
                    <span>
                        <a href="#glossario" class="text-emerald-600 hover:underline font-medium">UltraLogManager (ULM)</a> 
                        per registrazione eventi
                    </span>
                </li>
                <li class="flex items-start gap-2">
                    <x-heroicon-o-clipboard-document-check class="w-5 h-5 text-emerald-600 mt-0.5 shrink-0" />
                    <span>
                        <a href="#glossario" class="text-emerald-600 hover:underline font-medium">AuditLogService</a> 
                        per <a href="#glossario" class="text-emerald-600 hover:underline font-medium">audit trail</a> verificabili
                    </span>
                </li>
                <li class="flex items-start gap-2">
                    <x-heroicon-o-check-badge class="w-5 h-5 text-emerald-600 mt-0.5 shrink-0" />
                    <span>
                        <a href="#glossario" class="text-emerald-600 hover:underline font-medium">ConsentService</a> 
                        per gestione consensi
                    </span>
                </li>
            </ul>
            <div class="mt-4 p-3 bg-emerald-100 rounded-lg">
                <p class="font-semibold text-emerald-800 text-sm">
                    ✅ Risultato: Auditabilità completa, protezione dati, responsabilità verificabile
                </p>
            </div>
        </div>

        <!-- MiCA -->
        <div class="p-6 rounded-lg bg-blue-50 border-2 border-blue-200">
            <h4 class="mb-4 text-xl font-bold text-blue-700 flex items-center gap-2">
                <x-heroicon-o-shield-exclamation class="w-6 h-6 text-blue-600" />
                <a href="#glossario" class="hover:underline">MiCA</a>-safe
            </h4>
            <p class="mb-4 text-gray-700">
                FlorenceEGI è progettata per conformità totale:
            </p>
            <ul class="space-y-3 text-gray-700">
                <li class="flex items-start gap-2">
                    <x-heroicon-o-x-circle class="w-5 h-5 text-blue-600 mt-0.5 shrink-0" />
                    <span><strong>Non gestisce</strong> fondi o crypto per conto terzi</span>
                </li>
                <li class="flex items-start gap-2">
                    <x-heroicon-o-check-circle class="w-5 h-5 text-blue-600 mt-0.5 shrink-0" />
                    <span>Pagamenti tramite <a href="#glossario" class="text-blue-600 hover:underline font-medium">PSP autorizzati</a></span>
                </li>
                <li class="flex items-start gap-2">
                    <x-heroicon-o-arrow-path class="w-5 h-5 text-blue-600 mt-0.5 shrink-0" />
                    <span>Fondi fluiscono <strong>direttamente</strong> tra le parti</span>
                </li>
                <li class="flex items-start gap-2">
                    <x-heroicon-o-receipt-percent class="w-5 h-5 text-blue-600 mt-0.5 shrink-0" />
                    <span>Piattaforma incassa solo propria fee (fatturata)</span>
                </li>
            </ul>
            <div class="mt-4 p-3 bg-blue-100 rounded-lg">
                <p class="font-semibold text-blue-800 text-sm">
                    ✅ Risultato: Piena legalità, fiscalità lineare, zero rischio normativo
                </p>
            </div>
        </div>
    </div>

    <!-- Sicurezza Dati -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-key class="w-5 h-5 text-purple-500" />
            Sicurezza dei Dati
        </h4>
        <div class="bg-purple-50 rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-lg p-3 text-center">
                    <x-heroicon-o-lock-closed class="w-8 h-8 text-purple-600 mx-auto mb-2" />
                    <div class="font-medium text-purple-900">Crittografia</div>
                    <p class="text-xs text-gray-500">AES-256 per chiavi e dati sensibili</p>
                </div>
                <div class="bg-white rounded-lg p-3 text-center">
                    <x-heroicon-o-finger-print class="w-8 h-8 text-purple-600 mx-auto mb-2" />
                    <div class="font-medium text-purple-900">Autenticazione</div>
                    <p class="text-xs text-gray-500">2FA, SCA, sessioni sicure</p>
                </div>
                <div class="bg-white rounded-lg p-3 text-center">
                    <x-heroicon-o-server-stack class="w-8 h-8 text-purple-600 mx-auto mb-2" />
                    <div class="font-medium text-purple-900">Infrastruttura</div>
                    <p class="text-xs text-gray-500">AWS EU, backup georidondanti</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Audit e Tracciabilità -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-magnifying-glass class="w-5 h-5 text-amber-500" />
            Audit e Tracciabilità
        </h4>
        <div class="bg-amber-50 rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <h5 class="font-medium text-amber-900 mb-2">Cosa viene tracciato</h5>
                    <ul class="text-sm text-gray-700 space-y-1">
                        <li>• Ogni accesso e login</li>
                        <li>• Ogni transazione economica</li>
                        <li>• Ogni modifica ai dati</li>
                        <li>• Ogni consenso rilasciato</li>
                    </ul>
                </div>
                <div>
                    <h5 class="font-medium text-amber-900 mb-2">Garanzie</h5>
                    <ul class="text-sm text-gray-700 space-y-1">
                        <li>• Immutabilità dei log</li>
                        <li>• Timestamp certificati</li>
                        <li>• Export per verifiche fiscali</li>
                        <li>• Conservazione 10 anni</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Certificazioni -->
    <div class="p-4 bg-gray-50 rounded-lg border-l-4 border-gray-400">
        <h5 class="font-medium text-gray-900 mb-2">Standard e Certificazioni</h5>
        <div class="flex flex-wrap gap-2">
            <span class="bg-white text-gray-700 text-sm px-3 py-1 rounded-full border border-gray-200">GDPR Compliant</span>
            <span class="bg-white text-gray-700 text-sm px-3 py-1 rounded-full border border-gray-200">MiCA-safe</span>
            <span class="bg-white text-gray-700 text-sm px-3 py-1 rounded-full border border-gray-200">ISO 27001 (roadmap)</span>
            <span class="bg-white text-gray-700 text-sm px-3 py-1 rounded-full border border-gray-200">SOC 2 (roadmap)</span>
        </div>
    </div>
</div>
