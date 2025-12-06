{{--
    Componente: Uso PSP Autorizzati
    Sezione: Merchant & Pagamenti
    Descrizione: I Payment Service Provider utilizzati dalla piattaforma
--}}

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 bg-violet-100 rounded-lg flex items-center justify-center">
            <x-heroicon-o-server-stack class="w-5 h-5 text-violet-600" />
        </div>
        <h3 class="text-lg font-semibold text-gray-900">
            PSP Autorizzati
        </h3>
    </div>

    <p class="text-gray-600 mb-6">
        FlorenceEGI utilizza esclusivamente
        <a href="#glossario" class="text-primary-600 hover:underline font-medium">PSP</a>
        (Payment Service Provider) autorizzati e conformi alla normativa europea
        <a href="#glossario" class="text-primary-600 hover:underline font-medium">PSD2</a>.
    </p>

    <!-- PSP Principale -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-star class="w-5 h-5 text-violet-500" />
            PSP Principale: Stripe
        </h4>
        <div class="bg-violet-50 rounded-lg p-4">
            <div class="flex items-start gap-4">
                <div class="bg-violet-600 text-white font-bold text-2xl rounded-lg px-4 py-2">
                    stripe
                </div>
                <div class="flex-1">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <div class="font-medium text-violet-900 mb-2">Licenze</div>
                            <ul class="text-sm text-gray-700 space-y-1">
                                <li>• E-Money License (EU/EEA)</li>
                                <li>• PCI DSS Level 1 Certified</li>
                                <li>• Autorizzato Banca d'Irlanda</li>
                            </ul>
                        </div>
                        <div>
                            <div class="font-medium text-violet-900 mb-2">Funzionalità</div>
                            <ul class="text-sm text-gray-700 space-y-1">
                                <li>• Stripe Connect per split payments</li>
                                <li>• 3D Secure 2.0</li>
                                <li>• Fraud detection AI</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PSP Alternativo -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-arrows-right-left class="w-5 h-5 text-blue-500" />
            PSP Alternativo: MangoPay
        </h4>
        <div class="bg-blue-50 rounded-lg p-4">
            <div class="flex items-start gap-4">
                <div class="bg-orange-500 text-white font-bold text-xl rounded-lg px-4 py-2">
                    MANGO
                </div>
                <div class="flex-1">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <div class="font-medium text-blue-900 mb-2">Specializzazione</div>
                            <ul class="text-sm text-gray-700 space-y-1">
                                <li>• Marketplace e crowdfunding</li>
                                <li>• E-wallet integrati</li>
                                <li>• KYC/KYB integrato</li>
                            </ul>
                        </div>
                        <div>
                            <div class="font-medium text-blue-900 mb-2">Conformità</div>
                            <ul class="text-sm text-gray-700 space-y-1">
                                <li>• Licenza EMI (Lussemburgo)</li>
                                <li>• Passporting EU</li>
                                <li>• AML compliant</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Requisiti PSD2 -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-shield-check class="w-5 h-5 text-green-500" />
            Conformità PSD2
        </h4>
        <div class="bg-green-50 rounded-lg p-4">
            <p class="text-sm text-gray-700 mb-3">
                La direttiva <strong>PSD2</strong> (Payment Services Directive 2) impone requisiti
                stringenti per la sicurezza dei pagamenti:
            </p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-lg p-3 border border-green-200 text-center">
                    <x-heroicon-o-lock-closed class="w-8 h-8 text-green-600 mx-auto mb-2" />
                    <div class="font-medium text-green-900">SCA</div>
                    <p class="text-xs text-gray-500">Strong Customer Authentication</p>
                </div>
                <div class="bg-white rounded-lg p-3 border border-green-200 text-center">
                    <x-heroicon-o-shield-exclamation class="w-8 h-8 text-green-600 mx-auto mb-2" />
                    <div class="font-medium text-green-900">3D Secure 2</div>
                    <p class="text-xs text-gray-500">Autenticazione a due fattori</p>
                </div>
                <div class="bg-white rounded-lg p-3 border border-green-200 text-center">
                    <x-heroicon-o-document-check class="w-8 h-8 text-green-600 mx-auto mb-2" />
                    <div class="font-medium text-green-900">API Sicure</div>
                    <p class="text-xs text-gray-500">Open Banking compliant</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Split Payments -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-arrows-pointing-out class="w-5 h-5 text-amber-500" />
            Split Payments
        </h4>
        <div class="bg-amber-50 rounded-lg p-4">
            <p class="text-sm text-gray-700 mb-3">
                Il PSP gestisce automaticamente la <strong>suddivisione dei pagamenti</strong>:
            </p>
            <div class="bg-white rounded-lg p-4 border border-amber-200">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div class="text-center">
                        <div class="text-lg font-bold text-blue-600">€100</div>
                        <div class="text-xs text-gray-500">Pagamento Utente</div>
                    </div>
                    <x-heroicon-o-arrow-right class="w-5 h-5 text-gray-400 hidden md:block" />
                    <div class="text-center">
                        <div class="text-lg font-bold text-green-600">€68</div>
                        <div class="text-xs text-gray-500">Creator</div>
                    </div>
                    <div class="text-center">
                        <div class="text-lg font-bold text-purple-600">€20</div>
                        <div class="text-xs text-gray-500">EPP</div>
                    </div>
                    <div class="text-center">
                        <div class="text-lg font-bold text-amber-600">€10</div>
                        <div class="text-xs text-gray-500">Piattaforma</div>
                    </div>
                    <div class="text-center">
                        <div class="text-lg font-bold text-gray-600">€2</div>
                        <div class="text-xs text-gray-500">Frangette</div>
                    </div>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-2">
                * Le percentuali variano in base al tipo di transazione e configurazione
            </p>
        </div>
    </div>

    <!-- Note -->
    <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-violet-400">
        <h5 class="font-medium text-gray-900 mb-2">Fondi Segregati</h5>
        <p class="text-sm text-gray-600">
            I fondi degli utenti sono mantenuti in <strong>conti segregati</strong> presso
            i PSP, separati dai fondi operativi della piattaforma, come richiesto dalla normativa.
        </p>
    </div>
</div>
