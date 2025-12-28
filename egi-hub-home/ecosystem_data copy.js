// ecosystem_data.js
// DATABASE CENTRALE DELL'ECOSISTEMA FLORENCE EGI - HOME PAGE

const ecosystemData = {
    // --- CORE: HUB-EGI ---
    core: {
        label: "HUB-EGI",
        tagline: "Centro di controllo ecosistema.",
        cat: "ORCHESTRATION",
        color: 0xFFD700, // Oro Brillante
        desc: "La regia che rende EGI scalabile su più progetti, tenant e partner.",
        bullets: [
            "Gestione Tenant & Ruoli",
            "Policy di Ecosistema",
            "Attivazione Servizi"
        ],
        egi_link: "Questo nodo esiste perché EGI richiede una regia centralizzata per scalare.",
        route: "index.html"
    },

    // --- CATEGORIE PRINCIPALI ---
    project: {
        label: "PROJECT",
        tagline: "Progetti verticali EGI.",
        cat: "CATEGORY",
        color: 0x00FFAA, // Verde Acqua
        desc: "Hub dei progetti verticali: Florence Art, NATAN LOC, Tosca Bandi.",
        bullets: [
            "Florence Art - Collezionismo",
            "NATAN LOC - Documenti PA",
            "Tosca Bandi - Monitoraggio"
        ],
        egi_link: "I progetti verticali applicano EGI a domini specifici.",
        route: "project.html"
    },
    policy: {
        label: "POLICY",
        tagline: "Governance e compliance.",
        cat: "CATEGORY",
        color: 0x4B0082, // Indaco
        desc: "Regole, consensi, GDPR, audit trail e governance dell'ecosistema.",
        bullets: [
            "GDPR Consent",
            "Audit Log Immutabile",
            "User Access Control"
        ],
        egi_link: "EGI richiede compliance legale e governance trasparente.",
        route: "policy.html"
    },
    tech: {
        label: "TECH",
        tagline: "Infrastruttura tecnologica.",
        cat: "CATEGORY",
        color: 0x50C878, // Verde Smeraldo
        desc: "Blockchain Algorand, AI Gateway, Smart Contracts e infrastruttura.",
        bullets: [
            "Algorand Blockchain",
            "AI Gateway Multi-LLM",
            "Smart Contracts"
        ],
        egi_link: "La tecnologia che rende possibile la certificazione EGI.",
        route: "tech.html"
    },
    payments: {
        label: "PAYMENTS",
        tagline: "Sistema pagamenti fiat-first.",
        cat: "CATEGORY",
        color: 0xF4C430, // Giallo Zafferano
        desc: "Onboarding semplice con pagamenti in euro e conversione trasparente.",
        bullets: [
            "Stripe/Adyen Integration",
            "Split Payment",
            "Fatturazione Automatica"
        ],
        egi_link: "EGI deve essere fiat-first per massimizzare l'adozione.",
        route: "payments.html"
    },
    info: {
        label: "INFO",
        tagline: "Documentazione e risorse.",
        cat: "CATEGORY",
        color: 0x00BFFF, // Azzurro Cielo
        desc: "Whitepaper, API Reference, Developer Docs e guide.",
        bullets: [
            "Whitepaper",
            "API Reference",
            "Developer Docs"
        ],
        egi_link: "Rendere l'ecosistema comprensibile e adottabile.",
        route: "info.html"
    },
    oracode: {
        label: "ORACODE",
        tagline: "Il paradigma filosofico-tecnico.",
        cat: "TRUTH PROTOCOL",
        color: 0xE34234, // Rosso Vermiglio
        desc: "Documentazione totale e Verità Tecnologica. Il codice non mente.",
        bullets: [
            "Codice come Legge",
            "Trasparenza Radicale",
            "Etica by Design"
        ],
        egi_link: "Oracode è il fondamento filosofico di tutto l'ecosistema EGI.",
        route: "oracode.html"
    },
    epp: {
        label: "EPP",
        tagline: "Environmental Protection Projects.",
        cat: "SUSTAINABILITY",
        color: 0x228B22, // Verde Foresta
        desc: "Impatto ambientale nativo. Ogni atto economico genera rigenerazione.",
        bullets: [
            "Riforestazione",
            "Ocean Cleanup",
            "Biodiversità"
        ],
        egi_link: "20% automatico di ogni transazione va a progetti ambientali verificati.",
        route: "epp.html"
    }
};

// Configurazione Orbite - Categorie distribuite
const orbitalConfig = [
    // ORBIT 1: Categorie principali
    { id: "project", orbit: 1 },
    { id: "policy", orbit: 1 },
    { id: "tech", orbit: 1 },
    
    // ORBIT 2: Servizi e Sostenibilità
    { id: "payments", orbit: 2 },
    { id: "info", orbit: 2 },
    { id: "epp", orbit: 2 },

    // ORBIT 3: Paradigma (esterno)
    { id: "oracode", orbit: 3 }
];

// Export for Global Access
window.ecosystemData = ecosystemData;
window.orbitalConfig = orbitalConfig;
