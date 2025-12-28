// tech_data.js
// DATABASE DELLA PAGINA TECH - INFRASTRUTTURA TECNOLOGICA

const ecosystemData = {
    // --- CORE: TECH ---
    core: {
        label: "TECH",
        tagline: "Infrastruttura tecnologica EGI.",
        cat: "INFRASTRUCTURE",
        color: 0x50C878, // Verde Smeraldo
        desc: "L'infrastruttura che rende possibile la certificazione EGI.",
        bullets: [
            "Blockchain",
            "AI Gateway",
            "Smart Contracts"
        ],
        egi_link: "Tecnologia al servizio della verità certificata.",
        route: "tech.html"
    },

    // --- SATELLITI TECH ---
    algorand: {
        label: "ALGORAND",
        tagline: "Blockchain carbon-negative.",
        cat: "BLOCKCHAIN",
        color: 0xFFFFFF, // Bianco
        desc: "Proof of Existence, timestamp immutabile e Asset Standard (ASA).",
        bullets: [
            "Carbon Negative",
            "Pure Proof of Stake",
            "ASA Standard"
        ],
        egi_link: "La blockchain più sostenibile per certificare.",
        route: "#"
    },
    ai: {
        label: "AI GATEWAY",
        tagline: "Intelligenza artificiale etica.",
        cat: "AI",
        color: 0x1E90FF, // Blu Dodger
        desc: "NATAN: routing multi-LLM con controllo allucinazioni.",
        bullets: [
            "Multi-LLM Routing",
            "Hallucination Control",
            "Semantic Analysis"
        ],
        egi_link: "AI come interfaccia utente, non sostituto.",
        route: "#"
    },
    contracts: {
        label: "CONTRACTS",
        tagline: "Smart Contracts intelligenti.",
        cat: "BLOCKCHAIN",
        color: 0xF4C430, // Giallo Zafferano
        desc: "Contratti automatici per royalty, pagamenti e certificazioni.",
        bullets: [
            "Royalty Automatiche",
            "Escrow System",
            "Event Triggers"
        ],
        egi_link: "Il codice come legge eseguibile.",
        route: "#"
    },
    api: {
        label: "API",
        tagline: "Interfacce di integrazione.",
        cat: "INTEGRATION",
        color: 0x00BFFF, // Azzurro Cielo
        desc: "REST API, Webhooks e SDK per integrare EGI ovunque.",
        bullets: [
            "REST API",
            "Webhooks",
            "SDK Multi-Language"
        ],
        egi_link: "Integrazione semplice per tutti.",
        route: "#"
    }
};

// Configurazione Orbite
const orbitalConfig = [
    { id: "algorand", orbit: 1 },
    { id: "ai", orbit: 1 },
    { id: "contracts", orbit: 1 },
    { id: "api", orbit: 2 }
];

// Export
window.ecosystemData = ecosystemData;
window.orbitalConfig = orbitalConfig;
