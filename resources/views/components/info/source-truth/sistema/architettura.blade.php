{{--
    Componente: Architettura Tecnica
    Sezione: Sistema
    Descrizione: La tecnologia che garantisce immutabilità, sicurezza e sostenibilità
--}}

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
            <x-heroicon-o-cube-transparent class="w-5 h-5 text-indigo-600" />
        </div>
        <h3 class="text-lg font-semibold text-gray-900">
            Architettura Tecnica
        </h3>
    </div>

    <p class="text-gray-600 mb-6">
        La tecnologia che garantisce immutabilità, sicurezza e sostenibilità.
    </p>

    <!-- Stack Tecnologico -->
    <div class="p-6 mb-6 rounded-lg bg-emerald-50">
        <h4 class="mb-3 text-xl font-bold text-emerald-700">Stack Tecnologico FlorenceEGI</h4>
        <p class="text-gray-700">
            SaaS multi-tenant con <a href="#glossario" class="text-emerald-600 hover:underline font-medium">FlorenceEGI Core</a> 
            (governance centrale) e tenant specializzati come 
            <a href="#glossario" class="text-emerald-600 hover:underline font-medium">Natan</a> e FlorenceArtEGI. 
            Marketplace pubblico e protocol layer su 
            <a href="#glossario" class="text-emerald-600 hover:underline font-medium">Algorand</a>.
        </p>
    </div>

    <!-- Componenti Principali -->
    <div class="mb-8">
        <h4 class="mb-4 text-lg font-bold text-gray-800 flex items-center gap-2">
            <x-heroicon-o-building-office class="w-5 h-5 text-indigo-500" />
            Componenti Principali
        </h4>
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            <div class="p-4 rounded-lg bg-blue-50 border-l-4 border-blue-500">
                <h5 class="font-bold text-blue-800 mb-2">App Web</h5>
                <p class="text-sm text-gray-700">Laravel + TypeScript + Tailwind CSS</p>
            </div>
            <div class="p-4 rounded-lg bg-purple-50 border-l-4 border-purple-500">
                <h5 class="font-bold text-purple-800 mb-2">AMMk Core</h5>
                <p class="text-sm text-gray-700">Coordina i cinque engine: NATAN Market, Asset, Distribution, Co-Creation, Compliance</p>
            </div>
            <div class="p-4 rounded-lg bg-green-50 border-l-4 border-green-500">
                <h5 class="font-bold text-green-800 mb-2">Marketplace Pubblico</h5>
                <p class="text-sm text-gray-700">Discovery, listing, transazioni P2P</p>
            </div>
            <div class="p-4 rounded-lg bg-orange-50 border-l-4 border-orange-500">
                <h5 class="font-bold text-orange-800 mb-2">Protocol Layer</h5>
                <p class="text-sm text-gray-700">Algorand: ASA/SC, Proof, Fee Routing</p>
            </div>
            <div class="p-4 rounded-lg bg-yellow-50 border-l-4 border-yellow-500">
                <h5 class="font-bold text-yellow-800 mb-2">Event Bus</h5>
                <p class="text-sm text-gray-700">Trigger on/off-chain → azioni NATAN</p>
            </div>
            <div class="p-4 rounded-lg bg-red-50 border-l-4 border-red-500">
                <h5 class="font-bold text-red-800 mb-2">Observability</h5>
                <p class="text-sm text-gray-700">ULM, UEM, AuditTrail, GDPR</p>
            </div>
        </div>
    </div>

    <!-- AMMk Core -->
    <div class="mb-8 p-6 rounded-lg bg-gradient-to-br from-purple-50 to-blue-50 border-2 border-purple-300">
        <h4 class="mb-4 text-xl font-bold text-purple-800 flex items-center gap-2">
            <x-heroicon-o-cog-6-tooth class="w-6 h-6 text-purple-600" />
            Asset Market Maker (AMMk) Core
        </h4>
        <p class="mb-4 text-gray-700">
            Il cuore di FlorenceEGI: un motore che origina, certifica, valuta e rende liquidi gli 
            <a href="#glossario" class="text-emerald-600 hover:underline font-medium">EGI</a>.
        </p>
        <div class="grid gap-4 md:grid-cols-2">
            <div class="bg-white rounded-lg p-4">
                <h5 class="font-bold text-purple-700 mb-2 flex items-center gap-2">
                    <span>🧠</span> NATAN Market Engine
                </h5>
                <p class="text-sm text-gray-700">
                    <strong>Valuation</strong> – definisce valore, floor price e traiettoria.<br>
                    <strong>Activation</strong> – orchestra campagne e suggerimenti.
                </p>
            </div>
            <div class="bg-white rounded-lg p-4">
                <h5 class="font-bold text-purple-700 mb-2 flex items-center gap-2">
                    <span>🧱</span> Asset Engine
                </h5>
                <p class="text-sm text-gray-700">Gestisce listing, aste, vendite secondarie e liquidità degli EGI.</p>
            </div>
            <div class="bg-white rounded-lg p-4">
                <h5 class="font-bold text-purple-700 mb-2 flex items-center gap-2">
                    <span>🔄</span> Distribution Engine
                </h5>
                <p class="text-sm text-gray-700">Automatizza royalty, fee piattaforma e quota EPP.</p>
            </div>
            <div class="bg-white rounded-lg p-4">
                <h5 class="font-bold text-purple-700 mb-2 flex items-center gap-2">
                    <span>🤝</span> Co-Creation Engine
                </h5>
                <p class="text-sm text-gray-700">Orchestra minting, notarizzazione, firme e catena di custodia.</p>
            </div>
            <div class="bg-white rounded-lg p-4 md:col-span-2">
                <h5 class="font-bold text-purple-700 mb-2 flex items-center gap-2">
                    <span>🛡️</span> Compliance Engine
                </h5>
                <p class="text-sm text-gray-700">GDPR by design, audit trail completo, MiCA-safe e policy condivise.</p>
            </div>
        </div>
    </div>

    <!-- Tenancy & RBAC -->
    <div class="mb-8">
        <h4 class="mb-4 text-lg font-bold text-gray-800 flex items-center gap-2">
            <x-heroicon-o-user-group class="w-5 h-5 text-blue-500" />
            Tenancy & RBAC
        </h4>
        <div class="p-6 rounded-lg bg-blue-50">
            <p class="mb-4 text-gray-700">
                <strong>FlorenceEGI è multi-tenant</strong>: il core SaaS governa identità e permessi,
                mentre i tenant verticali applicano policy specifiche.
            </p>
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <h5 class="font-bold text-blue-800 mb-2">Ruoli Globali (Core)</h5>
                    <ul class="text-sm text-gray-700 list-disc list-inside space-y-1">
                        <li>User / Creator / Collector</li>
                        <li>Tenant Admin</li>
                        <li>Platform Admin</li>
                    </ul>
                </div>
                <div>
                    <h5 class="font-bold text-blue-800 mb-2">Ruoli Locali (Tenant)</h5>
                    <ul class="text-sm text-gray-700 list-disc list-inside space-y-1">
                        <li>Natan: operatori RAG, notarizzazione</li>
                        <li>FlorenceArtEGI: curator, inspector</li>
                        <li>Collection: owner, editor, viewer</li>
                    </ul>
                </div>
            </div>
            <div class="mt-4 p-3 bg-white rounded-lg text-sm text-blue-700">
                <strong>Collection workspace:</strong> max 8 wallet utente + 4 wallet collection = 
                <strong>12 slot</strong> (4 riservati al core → rispetto limite Algorand 16 account per gruppo atomico).
            </div>
        </div>
    </div>

    <!-- On-chain -->
    <div class="mb-8">
        <h4 class="mb-4 text-lg font-bold text-gray-800 flex items-center gap-2">
            <x-heroicon-o-link class="w-5 h-5 text-green-500" />
            On-chain & Smart Contract
        </h4>
        <div class="p-6 rounded-lg bg-green-50">
            <ul class="space-y-3 text-gray-700">
                <li class="flex items-start gap-2">
                    <x-heroicon-o-check-circle class="w-5 h-5 text-green-600 mt-0.5 shrink-0" />
                    <span><strong>Mint ASA</strong>: Creazione token EGI su Algorand</span>
                </li>
                <li class="flex items-start gap-2">
                    <x-heroicon-o-check-circle class="w-5 h-5 text-green-600 mt-0.5 shrink-0" />
                    <span><strong>Smart Contract per CoA</strong>: Certificato di autenticità immutabile</span>
                </li>
                <li class="flex items-start gap-2">
                    <x-heroicon-o-check-circle class="w-5 h-5 text-green-600 mt-0.5 shrink-0" />
                    <span><strong>Escrow</strong>: Gestione sicura fondi durante transazioni</span>
                </li>
                <li class="flex items-start gap-2">
                    <x-heroicon-o-check-circle class="w-5 h-5 text-green-600 mt-0.5 shrink-0" />
                    <span><strong>Smart Contract "Intelligenti"</strong>: Emettono hook/trigger → Event Bus → NATAN</span>
                </li>
                <li class="flex items-start gap-2">
                    <x-heroicon-o-check-circle class="w-5 h-5 text-green-600 mt-0.5 shrink-0" />
                    <span><strong>Attestazioni</strong>: Provenance, ownership, EPP allocation on-chain</span>
                </li>
            </ul>
        </div>
    </div>

    <!-- Perché Algorand -->
    <div class="p-6 rounded-lg bg-emerald-50 border-l-4 border-emerald-500">
        <h4 class="mb-3 text-lg font-bold text-emerald-700">Perché Algorand?</h4>
        <p class="text-gray-700 mb-4">
            Blockchain sostenibile e <a href="#glossario" class="text-emerald-600 hover:underline font-medium">carbon-negative</a> 
            basata su <a href="#glossario" class="text-emerald-600 hover:underline font-medium">Proof-of-Stake</a> pura.
        </p>
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <h5 class="font-semibold text-gray-800 mb-2">Garanzie Tecniche</h5>
                <ul class="space-y-1 text-sm text-gray-700 list-disc list-inside">
                    <li>Immutabilità e autenticità</li>
                    <li>Assenza di volatilità nei flussi</li>
                    <li>Scalabilità e sicurezza</li>
                </ul>
            </div>
            <div>
                <h5 class="font-semibold text-gray-800 mb-2">Collegamento Fisico-Digitale</h5>
                <ul class="space-y-1 text-sm text-gray-700 list-disc list-inside">
                    <li>CoA verificato per ogni EGI</li>
                    <li>QR/NFC unidirezionali</li>
                    <li>Verifica pubblica immediata</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Principio Fondamentale -->
    <div class="mt-6 p-4 border-l-4 border-blue-500 rounded-r-lg bg-blue-50">
        <p class="font-semibold text-blue-800">
            <x-heroicon-o-shield-check class="w-5 h-5 inline text-blue-600" />
            FlorenceEGI certifica, non custodisce.
        </p>
        <p class="text-sm text-blue-700 mt-1">
            Il merchant/creator resta proprietario del bene; la piattaforma garantisce solo la verità 
            della proprietà e dell'autenticità.
        </p>
    </div>
</div>
