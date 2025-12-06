{{--
    Componente: Glossario Source of Truth
    Descrizione: Definizioni dei termini chiave usati in FlorenceEGI Source of Truth
--}}

<div id="glossario" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
            <x-heroicon-o-book-open class="w-5 h-5 text-gray-600" />
        </div>
        <h3 class="text-lg font-semibold text-gray-900">
            Glossario
        </h3>
    </div>

    <p class="text-gray-600 mb-6">
        Definizioni dei termini chiave usati in FlorenceEGI.
    </p>

    <div class="space-y-4">
        {{-- A --}}
        <div class="p-4 rounded-lg bg-gray-50 border-l-4 border-gray-300">
            <h4 class="font-bold text-gray-800 mb-2">Algorand</h4>
            <p class="text-sm text-gray-700">
                Blockchain di terza generazione basata su Pure Proof-of-Stake, scelta da FlorenceEGI per 
                la sua sostenibilità (carbon-negative), velocità e sicurezza. Permette transazioni 
                immediate con fee trascurabili.
            </p>
        </div>

        <div class="p-4 rounded-lg bg-gray-50 border-l-4 border-gray-300">
            <h4 class="font-bold text-gray-800 mb-2">AMMk (Asset Market Maker)</h4>
            <p class="text-sm text-gray-700">
                Il cuore tecnologico di FlorenceEGI: un motore che origina, certifica, valuta e 
                rende liquidi gli EGI attraverso cinque engine specializzati (Market, Asset, 
                Distribution, Co-Creation, Compliance).
            </p>
        </div>

        <div class="p-4 rounded-lg bg-gray-50 border-l-4 border-gray-300">
            <h4 class="font-bold text-gray-800 mb-2">ASA (Algorand Standard Asset)</h4>
            <p class="text-sm text-gray-700">
                Token nativo su Algorand che rappresenta asset digitali. Ogni EGI è un ASA, 
                garantendo interoperabilità e verificabilità on-chain.
            </p>
        </div>

        <div class="p-4 rounded-lg bg-gray-50 border-l-4 border-gray-300">
            <h4 class="font-bold text-gray-800 mb-2">Audit Trail</h4>
            <p class="text-sm text-gray-700">
                Registro cronologico e immutabile di tutte le azioni eseguite nel sistema. 
                Garantisce tracciabilità completa per compliance GDPR e verifiche fiscali.
            </p>
        </div>

        <div class="p-4 rounded-lg bg-gray-50 border-l-4 border-gray-300">
            <h4 class="font-bold text-gray-800 mb-2">AuditLogService</h4>
            <p class="text-sm text-gray-700">
                Servizio FlorenceEGI che registra e certifica ogni azione significativa 
                (accessi, transazioni, modifiche) per conformità normativa.
            </p>
        </div>

        {{-- C --}}
        <div class="p-4 rounded-lg bg-blue-50 border-l-4 border-blue-300">
            <h4 class="font-bold text-gray-800 mb-2">Carbon-negative</h4>
            <p class="text-sm text-gray-700">
                Caratteristica di Algorand: la blockchain rimuove più CO₂ di quanta ne produca, 
                rendendo ogni transazione FlorenceEGI a impatto ambientale positivo.
            </p>
        </div>

        <div class="p-4 rounded-lg bg-blue-50 border-l-4 border-blue-300">
            <h4 class="font-bold text-gray-800 mb-2">CoA (Certificate of Authenticity)</h4>
            <p class="text-sm text-gray-700">
                Certificato digitale immutabile associato a ogni EGI. Contiene hash dell'opera, 
                metadati, firme digitali, storia del processo creativo (Oracode) e provenance.
            </p>
        </div>

        <div class="p-4 rounded-lg bg-blue-50 border-l-4 border-blue-300">
            <h4 class="font-bold text-gray-800 mb-2">Co-Creatore</h4>
            <p class="text-sm text-gray-700">
                Ruolo FlorenceEGI: l'utente che partecipa al processo creativo insieme all'artista, 
                contribuendo alla nascita dell'opera attraverso la metodologia Oracode.
            </p>
        </div>

        <div class="p-4 rounded-lg bg-blue-50 border-l-4 border-blue-300">
            <h4 class="font-bold text-gray-800 mb-2">Collector</h4>
            <p class="text-sm text-gray-700">
                Ruolo FlorenceEGI: l'utente che colleziona EGI. Può acquistare, vendere, e 
                partecipare al mercato secondario, ricevendo lo status di mecenate digitale.
            </p>
        </div>

        <div class="p-4 rounded-lg bg-blue-50 border-l-4 border-blue-300">
            <h4 class="font-bold text-gray-800 mb-2">ConsentService</h4>
            <p class="text-sm text-gray-700">
                Servizio FlorenceEGI per la gestione dei consensi GDPR. Traccia ogni consenso 
                rilasciato o revocato dagli utenti con timestamp certificato.
            </p>
        </div>

        <div class="p-4 rounded-lg bg-blue-50 border-l-4 border-blue-300">
            <h4 class="font-bold text-gray-800 mb-2">Creator</h4>
            <p class="text-sm text-gray-700">
                Ruolo FlorenceEGI: l'artista che crea opere attraverso la piattaforma. Mantiene 
                i diritti morali, riceve royalty perpetue (4.5%) su ogni rivendita.
            </p>
        </div>

        {{-- D --}}
        <div class="p-4 rounded-lg bg-green-50 border-l-4 border-green-300">
            <h4 class="font-bold text-gray-800 mb-2">Diritto di Seguito</h4>
            <p class="text-sm text-gray-700">
                Diritto previsto dalla L. 633/1941 Art. 19bis: l'artista riceve una percentuale 
                (4%-0.25%) sulle rivendite tramite professionisti del mercato dell'arte sopra €3,000. 
                Cumulabile con le royalty FlorenceEGI.
            </p>
        </div>

        {{-- E --}}
        <div class="p-4 rounded-lg bg-emerald-50 border-l-4 border-emerald-300">
            <h4 class="font-bold text-gray-800 mb-2">EGI (Embodied Generative Intelligence)</h4>
            <p class="text-sm text-gray-700">
                L'unità fondamentale di FlorenceEGI: un'opera d'arte digitale certificata su blockchain, 
                con CoA immutabile, royalty automatiche e quota EPP integrata. Non è un semplice NFT: 
                è un "Unicum" con storia, anima e impatto.
            </p>
        </div>

        <div class="p-4 rounded-lg bg-emerald-50 border-l-4 border-emerald-300">
            <h4 class="font-bold text-gray-800 mb-2">EPP (Environmental Protection Premium)</h4>
            <p class="text-sm text-gray-700">
                L'1% automatico su ogni transazione FlorenceEGI, destinato a progetti ambientali 
                certificati (riforestazione, protezione mari, carbon offset). Gestito via smart contract, 
                supervisionato da Frangette APS.
            </p>
        </div>

        <div class="p-4 rounded-lg bg-emerald-50 border-l-4 border-emerald-300">
            <h4 class="font-bold text-gray-800 mb-2">ESG</h4>
            <p class="text-sm text-gray-700">
                Environmental, Social, Governance: framework di sostenibilità che guida FlorenceEGI. 
                E = EPP e carbon-negative; S = supporto artisti e democratizzazione; G = governance duale 
                e trasparenza blockchain.
            </p>
        </div>

        {{-- F --}}
        <div class="p-4 rounded-lg bg-amber-50 border-l-4 border-amber-300">
            <h4 class="font-bold text-gray-800 mb-2">FlorenceEGI Core</h4>
            <p class="text-sm text-gray-700">
                Il tenant SaaS centrale che governa identità, permessi e policy condivise. 
                Coordina tutti i tenant verticali (Natan, FlorenceArtEGI, etc.).
            </p>
        </div>

        <div class="p-4 rounded-lg bg-amber-50 border-l-4 border-amber-300">
            <h4 class="font-bold text-gray-800 mb-2">Frangette APS</h4>
            <p class="text-sm text-gray-700">
                Associazione di Promozione Sociale che funge da custode dei valori e dell'etica 
                di FlorenceEGI nella governance duale. Vigila sulla destinazione dell'EPP e 
                sulla coerenza della missione.
            </p>
        </div>

        {{-- G --}}
        <div class="p-4 rounded-lg bg-purple-50 border-l-4 border-purple-300">
            <h4 class="font-bold text-gray-800 mb-2">GDPR</h4>
            <p class="text-sm text-gray-700">
                General Data Protection Regulation: regolamento europeo sulla protezione dei dati. 
                FlorenceEGI è GDPR-by-design con ULM, AuditLogService e ConsentService integrati.
            </p>
        </div>

        <div class="p-4 rounded-lg bg-purple-50 border-l-4 border-purple-300">
            <h4 class="font-bold text-gray-800 mb-2">Governance Duale</h4>
            <p class="text-sm text-gray-700">
                Modello organizzativo FlorenceEGI: FlorenceEGI SRL (motore operativo) + Frangette APS 
                (custode dei valori). Garantisce equilibrio tra profitto e missione.
            </p>
        </div>

        {{-- M --}}
        <div class="p-4 rounded-lg bg-rose-50 border-l-4 border-rose-300">
            <h4 class="font-bold text-gray-800 mb-2">Mecenatismo Digitale</h4>
            <p class="text-sm text-gray-700">
                Il modello FlorenceEGI dove il collezionista diventa co-finanziatore attivo della 
                carriera dell'artista (royalty perpetue) e della rigenerazione ambientale (EPP).
            </p>
        </div>

        <div class="p-4 rounded-lg bg-rose-50 border-l-4 border-rose-300">
            <h4 class="font-bold text-gray-800 mb-2">MiCA</h4>
            <p class="text-sm text-gray-700">
                Markets in Crypto-Assets Regulation: regolamento europeo sui crypto-asset. 
                FlorenceEGI è MiCA-safe: non gestisce fondi per conto terzi, usa PSP autorizzati, 
                incassa solo la propria fee fatturata.
            </p>
        </div>

        <div class="p-4 rounded-lg bg-rose-50 border-l-4 border-rose-300">
            <h4 class="font-bold text-gray-800 mb-2">Minting</h4>
            <p class="text-sm text-gray-700">
                Il processo di creazione di un EGI su blockchain: l'opera viene tokenizzata, 
                il CoA viene generato, e l'hash viene registrato immutabilmente su Algorand.
            </p>
        </div>

        {{-- N --}}
        <div class="p-4 rounded-lg bg-cyan-50 border-l-4 border-cyan-300">
            <h4 class="font-bold text-gray-800 mb-2">NATAN</h4>
            <p class="text-sm text-gray-700">
                Network for Art Tokenization, Authentication & Notarization: l'agente AI di FlorenceEGI 
                che coordina valutazione, autenticazione, distribuzione e compliance attraverso 5 engine 
                specializzati.
            </p>
        </div>

        {{-- O --}}
        <div class="p-4 rounded-lg bg-violet-50 border-l-4 border-violet-300">
            <h4 class="font-bold text-gray-800 mb-2">Oracode</h4>
            <p class="text-sm text-gray-700">
                Metodologia proprietaria FlorenceEGI per la co-creazione uomo-macchina. 
                4 pilastri: Intenzione Primaria, Dialogo Iterativo, Curation Umana, Documentazione. 
                L'AI amplifica l'artista, non lo sostituisce.
            </p>
        </div>

        <div class="p-4 rounded-lg bg-violet-50 border-l-4 border-violet-300">
            <h4 class="font-bold text-gray-800 mb-2">Owner</h4>
            <p class="text-sm text-gray-700">
                Ruolo FlorenceEGI: il proprietario attuale di un EGI. Ha diritto di possesso, 
                esposizione privata, rivendita e donazione. Non acquisisce i diritti d'autore (copyright).
            </p>
        </div>

        {{-- P --}}
        <div class="p-4 rounded-lg bg-orange-50 border-l-4 border-orange-300">
            <h4 class="font-bold text-gray-800 mb-2">Proof-of-Stake (PoS)</h4>
            <p class="text-sm text-gray-700">
                Meccanismo di consenso blockchain dove i validatori sono scelti in base alla 
                quantità di token posseduti, non alla potenza di calcolo. Algorand usa Pure PoS, 
                estremamente efficiente energeticamente.
            </p>
        </div>

        <div class="p-4 rounded-lg bg-orange-50 border-l-4 border-orange-300">
            <h4 class="font-bold text-gray-800 mb-2">Provenance</h4>
            <p class="text-sm text-gray-700">
                La storia completa di un'opera: creazione, proprietari successivi, transazioni. 
                Su FlorenceEGI è interamente on-chain, verificabile pubblicamente e immutabile.
            </p>
        </div>

        <div class="p-4 rounded-lg bg-orange-50 border-l-4 border-orange-300">
            <h4 class="font-bold text-gray-800 mb-2">PSP (Payment Service Provider)</h4>
            <p class="text-sm text-gray-700">
                Fornitore autorizzato di servizi di pagamento. FlorenceEGI usa esclusivamente 
                PSP regolamentati per gestire i flussi finanziari, garantendo conformità MiCA.
            </p>
        </div>

        {{-- R --}}
        <div class="p-4 rounded-lg bg-indigo-50 border-l-4 border-indigo-300">
            <h4 class="font-bold text-gray-800 mb-2">RBAC (Role-Based Access Control)</h4>
            <p class="text-sm text-gray-700">
                Sistema di controllo accessi basato sui ruoli. FlorenceEGI implementa RBAC 
                multi-livello: ruoli globali (Core) e ruoli locali (Tenant-specific).
            </p>
        </div>

        <div class="p-4 rounded-lg bg-indigo-50 border-l-4 border-indigo-300">
            <h4 class="font-bold text-gray-800 mb-2">Royalty</h4>
            <p class="text-sm text-gray-700">
                Compenso automatico al Creator su ogni rivendita secondaria di un EGI. 
                FlorenceEGI applica il 4.5% fisso, gestito via smart contract (trustless).
            </p>
        </div>

        {{-- S --}}
        <div class="p-4 rounded-lg bg-teal-50 border-l-4 border-teal-300">
            <h4 class="font-bold text-gray-800 mb-2">Smart Contract</h4>
            <p class="text-sm text-gray-700">
                Programma auto-eseguibile su blockchain. FlorenceEGI li usa per CoA, royalty, 
                EPP, escrow: garantiscono esecuzione automatica senza intermediari (trustless).
            </p>
        </div>

        {{-- T --}}
        <div class="p-4 rounded-lg bg-lime-50 border-l-4 border-lime-300">
            <h4 class="font-bold text-gray-800 mb-2">Trustless</h4>
            <p class="text-sm text-gray-700">
                Caratteristica dei sistemi blockchain: le transazioni sono garantite dal codice, 
                non dalla fiducia tra le parti. Le royalty FlorenceEGI sono trustless: non eludibili.
            </p>
        </div>

        {{-- U --}}
        <div class="p-4 rounded-lg bg-pink-50 border-l-4 border-pink-300">
            <h4 class="font-bold text-gray-800 mb-2">ULM (UltraLogManager)</h4>
            <p class="text-sm text-gray-700">
                Sistema FlorenceEGI per la registrazione completa di eventi e azioni. 
                Componente fondamentale per GDPR-by-design e auditabilità.
            </p>
        </div>

        <div class="p-4 rounded-lg bg-pink-50 border-l-4 border-pink-300">
            <h4 class="font-bold text-gray-800 mb-2">Unicum</h4>
            <p class="text-sm text-gray-700">
                Dal latino "unico, singolare". Ogni EGI è un Unicum: evento creativo irripetibile, 
                con DNA creativo, timestamp, CoA e provenance che lo rendono unico nel tempo e nello spazio.
            </p>
        </div>
    </div>
</div>
