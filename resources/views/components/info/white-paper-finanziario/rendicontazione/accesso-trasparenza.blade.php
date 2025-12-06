{{--
    Componente: Accesso e Trasparenza
    Sezione: Rendicontazione & Archiviazione
    Descrizione: Modalità di accesso ai dati e trasparenza delle operazioni
--}}

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 bg-cyan-100 rounded-lg flex items-center justify-center">
            <x-heroicon-o-eye class="w-5 h-5 text-cyan-600" />
        </div>
        <h3 class="text-lg font-semibold text-gray-900">
            Accesso e Trasparenza
        </h3>
    </div>

    <p class="text-gray-600 mb-6">
        FlorenceEGI garantisce agli utenti il pieno accesso ai propri dati
        e la massima trasparenza nelle operazioni, in conformità al
        <a href="#glossario" class="text-primary-600 hover:underline font-medium">GDPR</a>
        e alle normative sulla trasparenza.
    </p>

    <!-- Diritti di Accesso -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-key class="w-5 h-5 text-cyan-500" />
            Diritti di Accesso (GDPR)
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-cyan-50 rounded-lg p-4">
                <div class="font-medium text-cyan-900 mb-2">Art. 15 - Accesso</div>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li>• Copia di tutti i dati personali</li>
                    <li>• Informazioni sul trattamento</li>
                    <li>• Destinatari dei dati</li>
                    <li>• Periodo di conservazione</li>
                </ul>
            </div>
            <div class="bg-blue-50 rounded-lg p-4">
                <div class="font-medium text-blue-900 mb-2">Art. 20 - Portabilità</div>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li>• Export dati in formato strutturato</li>
                    <li>• Formato leggibile da macchina</li>
                    <li>• Trasferimento a terzi</li>
                    <li>• JSON, CSV, XML disponibili</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Come Accedere -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-cursor-arrow-rays class="w-5 h-5 text-green-500" />
            Come Accedere ai Dati
        </h4>
        <div class="space-y-3">
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-2">
                    <span class="bg-green-100 text-green-700 text-xs font-semibold px-2 py-1 rounded">DASHBOARD</span>
                    <span class="font-medium text-gray-900">Area Personale</span>
                </div>
                <p class="text-sm text-gray-700">
                    Accedi a <code class="bg-gray-100 px-1 rounded">Impostazioni → I Miei Dati → Export</code>
                    per scaricare i tuoi dati in qualsiasi momento.
                </p>
            </div>
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-2">
                    <span class="bg-blue-100 text-blue-700 text-xs font-semibold px-2 py-1 rounded">API</span>
                    <span class="font-medium text-gray-900">Accesso Programmatico</span>
                </div>
                <p class="text-sm text-gray-700">
                    Utilizza l'endpoint <code class="bg-gray-100 px-1 rounded">GET /api/v1/user/data-export</code>
                    con il tuo token API.
                </p>
            </div>
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-2">
                    <span class="bg-purple-100 text-purple-700 text-xs font-semibold px-2 py-1 rounded">RICHIESTA</span>
                    <span class="font-medium text-gray-900">Richiesta Formale</span>
                </div>
                <p class="text-sm text-gray-700">
                    Invia richiesta a <a href="mailto:privacy@florenceegi.com" class="text-cyan-600 hover:underline">privacy@florenceegi.com</a>.
                    Risposta garantita entro 30 giorni.
                </p>
            </div>
        </div>
    </div>

    <!-- Cosa Puoi Vedere -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-document-magnifying-glass class="w-5 h-5 text-purple-500" />
            Dati Accessibili
        </h4>
        <div class="bg-purple-50 rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <div class="font-medium text-purple-900 mb-2">Dati Personali</div>
                    <ul class="text-sm text-gray-700 space-y-1">
                        <li>• Profilo e anagrafica</li>
                        <li>• Documenti KYC</li>
                        <li>• Preferenze e impostazioni</li>
                    </ul>
                </div>
                <div>
                    <div class="font-medium text-purple-900 mb-2">Dati Finanziari</div>
                    <ul class="text-sm text-gray-700 space-y-1">
                        <li>• Storico transazioni completo</li>
                        <li>• Fatture e ricevute</li>
                        <li>• Movimenti wallet</li>
                    </ul>
                </div>
                <div>
                    <div class="font-medium text-purple-900 mb-2">Dati Tecnici</div>
                    <ul class="text-sm text-gray-700 space-y-1">
                        <li>• Log di accesso</li>
                        <li>• Dispositivi autorizzati</li>
                        <li>• Storico modifiche</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Trasparenza Operazioni -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-sparkles class="w-5 h-5 text-amber-500" />
            Trasparenza delle Operazioni
        </h4>
        <div class="bg-amber-50 rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <div class="font-medium text-amber-900 mb-2">Per Ogni Transazione</div>
                    <ul class="text-sm text-gray-700 space-y-1">
                        <li class="flex items-start gap-2">
                            <x-heroicon-o-check class="w-4 h-4 text-amber-600 mt-0.5 shrink-0" />
                            <span>Breakdown completo delle fee</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <x-heroicon-o-check class="w-4 h-4 text-amber-600 mt-0.5 shrink-0" />
                            <span>Destinazione di ogni centesimo</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <x-heroicon-o-check class="w-4 h-4 text-amber-600 mt-0.5 shrink-0" />
                            <span>Timestamp e riferimenti</span>
                        </li>
                    </ul>
                </div>
                <div>
                    <div class="font-medium text-amber-900 mb-2">Per il Marketplace</div>
                    <ul class="text-sm text-gray-700 space-y-1">
                        <li class="flex items-start gap-2">
                            <x-heroicon-o-check class="w-4 h-4 text-amber-600 mt-0.5 shrink-0" />
                            <span>Statistiche pubbliche aggregate</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <x-heroicon-o-check class="w-4 h-4 text-amber-600 mt-0.5 shrink-0" />
                            <span>Volumi donazioni EPP visibili</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <x-heroicon-o-check class="w-4 h-4 text-amber-600 mt-0.5 shrink-0" />
                            <span>Impatto sociale tracciato</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Note -->
    <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-cyan-400">
        <h5 class="font-medium text-gray-900 mb-2">DPO - Data Protection Officer</h5>
        <p class="text-sm text-gray-600">
            Per qualsiasi richiesta relativa ai tuoi dati personali, puoi contattare il nostro DPO:
            <br><strong>Email:</strong> <a href="mailto:dpo@florenceegi.com" class="text-cyan-600 hover:underline">dpo@florenceegi.com</a>
        </p>
    </div>
</div>
