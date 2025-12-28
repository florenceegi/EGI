// epp_data.js
// DATABASE DELLA PAGINA EPP - ENVIRONMENTAL PROTECTION PROJECTS
// LE 3 CAMPAGNE REALI DI FLORENCEEGI

const ecosystemData = {
    // --- CORE: EPP ---
    core: {
        label: "EPP",
        tagline: "Environmental Protection Projects.",
        cat: "SUSTAINABILITY",
        color: 0x228B22, // Verde Foresta
        desc: "Impatto ambientale nativo. Ogni atto economico genera rigenerazione.",
        bullets: [
            "20% Automatico",
            "3 Campagne Attive",
            "Tracciabilità On-Chain"
        ],
        egi_link: "Ogni vendita EGI contribuisce automaticamente all'ambiente.",
        route: "epp.html"
    },

    // --- SATELLITI: LE 3 CAMPAGNE EPP ---
    riforestazione: {
        label: "RIFORESTAZIONE",
        tagline: "3 Trees for an NFT.",
        cat: "EPP CAMPAIGN",
        color: 0x228B22, // Verde Rinascita
        desc: "Piantumazione e habitat restoration. Per ogni EGI venduto, 3 alberi piantati.",
        bullets: [
            "3 Alberi per EGI",
            "Habitat Restoration",
            "Carbon Offset"
        ],
        egi_link: "La campagna di riforestazione FlorenceEGI.",
        route: "#riforestazione"
    },
    plastica: {
        label: "PLASTICA OCEANI",
        tagline: "Rimozione plastica marina.",
        cat: "EPP CAMPAIGN",
        color: 0x1E90FF, // Blu Algoritmo
        desc: "Rimozione plastica dagli oceani. Partnership con progetti di pulizia marina verificati.",
        bullets: [
            "Kg Plastica Rimossa",
            "Pulizia Oceanica",
            "Report Verificati"
        ],
        egi_link: "La campagna di pulizia degli oceani FlorenceEGI.",
        route: "#plastica"
    },
    api: {
        label: "PROTEZIONE API",
        tagline: "Salvaguardia impollinatori.",
        cat: "EPP CAMPAIGN",
        color: 0xFFD700, // Oro Fiorentino
        desc: "Protezione delle api e degli impollinatori. Supporto ad apiari e progetti di biodiversità.",
        bullets: [
            "Protezione Apiari",
            "Biodiversità",
            "Impollinatori"
        ],
        egi_link: "La campagna di protezione delle api FlorenceEGI.",
        route: "#api"
    }
};

// Configurazione Orbite - Le 3 campagne EPP
const orbitalConfig = [
    { id: "riforestazione", orbit: 1 },
    { id: "plastica", orbit: 1 },
    { id: "api", orbit: 1 }
];

// Export for Global Access
window.ecosystemData = ecosystemData;
window.orbitalConfig = orbitalConfig;
