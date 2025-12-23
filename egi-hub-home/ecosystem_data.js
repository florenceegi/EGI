// ecosystem_data.js
// DATABASE CENTRALE DELL'ECOSISTEMA FLORENCE EGI

const ecosystemData = {
    // --- CORE & REGIA ---
    core: {
        label: "HUB-EGI",
        tagline: "Multi-tenant control plane.",
        cat: "ORCHESTRATION",
        color: 0x00ffdd, // Cyan
        desc: "La regia che rende EGI scalabile su più progetti, tenant e partner.",
        bullets: [
            "Gestione Tenant & Ruoli",
            "Policy di Ecosistema",
            "Attivazione Servizi"
        ],
        egi_link: "Questo nodo esiste perché EGI richiede una regia centralizzata per scalare.",
        route: "index.html" // Self
    },

    // --- PRODOTTI VERTICALI ---
    art: {
        label: "FLORENCE ART",
        tagline: "EGI applicato ad arte e collezionismo.",
        cat: "VERTICAL PRODUCT",
        color: 0xaa00ff, // Purple
        desc: "Lifecycle dell'asset: creazione, listing, scambio e mercato secondario.",
        bullets: [
            "Marketplace NFT/Phygital",
            "Gestione Collezioni",
            "Secondary Market"
        ],
        egi_link: "Questo nodo esiste perché EGI certifica valore e ownership delle opere.",
        route: "art.html"
    },
    natan: {
        label: "NATAN LOC",
        tagline: "EGI applicato ai documenti: AI + audit + prova.",
        cat: "VERTICAL PRODUCT",
        color: 0xff0055, // Red/Pink
        desc: "Consultazione e gestione documentale per la PA con sicurezza e tracciabilità.",
        bullets: [
            "Scanner Albi Pretori",
            "Notarizzazione Atti",
            "Compliance L.69/2009"
        ],
        egi_link: "Questo nodo esiste perché EGI richiede Proof verificabile sui documenti.",
        route: "natan.html"
    },
    tosca: {
        label: "TOSCA BANDI",
        tagline: "Verticale tematico su atti/bandi.",
        cat: "VERTICAL PRODUCT",
        color: 0xffaa00, // Gold
        desc: "Esempio di prodotto replicabile sopra la stessa infrastruttura per bandi pubblici.",
        bullets: [
            "Monitoraggio Bandi",
            "Matching Opportunità",
            "Gestione Scadenze"
        ],
        egi_link: "Questo nodo esiste perché EGI abilita prodotti tematici verticali.",
        route: "tosca.html"
    },
    info: {
        label: "EGI INFO",
        tagline: "Portale pubblico e documentazione.",
        cat: "VERTICAL PRODUCT",
        color: 0x00aaff, // Light Blue
        desc: "Rendere il sistema comprensibile e adottabile per sviluppatori e partner.",
        bullets: [
            "Developer Docs",
            "Whitepaper",
            "API Reference"
        ],
        egi_link: "Questo nodo esiste per rendere l'ecosistema accessibile.",
        route: "info.html"
    },

    // --- INFRASTRUCTURE LAYERS ---
    algo: {
        label: "ALGORAND LAYER",
        tagline: "Prova, timestamp, ownership.",
        cat: "EGI PROOF",
        color: 0xffffff, // White
        desc: "Ancoraggio immutabile di prove e ownership su blockchain pubblica.",
        bullets: [
            "Timestamp Inalterabile",
            "Proof of Existence",
            "Asset Standard (ASA)"
        ],
        egi_link: "Questo nodo esiste perché EGI richiede una Proof verificabile.",
        route: "#"
    },
    ai: {
        label: "AI GATEWAY",
        tagline: "Interfaccia cognitiva, provider-agnostica.",
        cat: "EGI INTELLIGENCE",
        color: 0x00ff88, // Green
        desc: "Interfaccia cognitiva con policy di verità e routing multi-modello.",
        bullets: [
            "Routing Multi-LLM",
            "Controllo Allucinazioni",
            "Analisi Semantica"
        ],
        egi_link: "Questo nodo esiste perché EGI usa l'AI come interfaccia utente.",
        route: "#"
    },
    pay: {
        label: "FIAT PAYMENTS",
        tagline: "Paghi in euro, ricevi EGI.",
        cat: "EGI PAYMENTS",
        color: 0xffff00, // Yellow
        desc: "Onboarding semplice: pagamenti in valuta fiat, conversione trasparente.",
        bullets: [
            "Stripe/Adyen Integration",
            "Split Payment",
            "Fatturazione Automatica"
        ],
        egi_link: "Questo nodo esiste perché EGI deve essere fiat-first per l'adozione.",
        route: "#"
    },
    comp: {
        label: "COMPLIANCE",
        tagline: "Regole, consensi, audit trail.",
        cat: "EGI COMPLIANCE",
        color: 0x888888, // Grey
        desc: "Motore di regole per GDPR, tracciabilità e log delle operazioni.",
        bullets: [
            "GDPR Consent",
            "Audit Log Immutabile",
            "User Access Control"
        ],
        egi_link: "Questo nodo esiste perché EGI richiede compliance legale.",
        route: "#"
    },
    
    // --- ORACODE (Paradigm) ---
    oracode: {
        label: "ORACODE",
        tagline: "Il paradigma filosofico-tecnico.",
        cat: "TRUTH PROTOCOL",
        color: 0xffffff, // White Glow
        desc: "Documentazione totale e Verità Tecnologica. Garantisce che il codice non menta.",
        bullets: [
            "Codice come Legge",
            "Trasparenza Radicale",
            "Etica by Design"
        ],
        egi_link: "Questo nodo è il fondamento filosofico di EGI.",
        route: "#"
    }
};

// Configurazione Orbite (Posizione Spaziale)
const orbitalConfig = [
    // ORBIT 1: VERTICAL PRODUCTS (Vicini al Core)
    { id: "art", orbit: 1 },
    { id: "natan", orbit: 1 },
    { id: "tosca", orbit: 1 },
    { id: "info", orbit: 1 },

    // ORBIT 2: TECH & PROOF (Struttura)
    { id: "algo", orbit: 2 },
    { id: "ai", orbit: 2 },
    
    // ORBIT 3: OPS (Finance & Gov)
    { id: "pay", orbit: 3 },
    { id: "comp", orbit: 3 },

    // ORBIT 4: PARADIGM (Esterno)
    { id: "oracode", orbit: 4 } 
];

// Export for Global Access (required for Module Engine)
window.ecosystemData = ecosystemData;
window.orbitalConfig = orbitalConfig;
