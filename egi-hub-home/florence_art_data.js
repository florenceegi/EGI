// florence_art_data.js
// DATABASE DELLA PAGINA FLORENCE ART - COLLEZIONISMO DIGITALE

const ecosystemData = {
    // --- CORE: FLORENCE ART ---
    core: {
        label: "FLORENCE ART",
        tagline: "Arte e collezionismo digitale.",
        cat: "VERTICAL PROJECT",
        color: 0xE34234, // Rosso Vermiglio
        desc: "Marketplace per arte digitale e phygital con certificazione EGI.",
        bullets: [
            "NFT & Phygital",
            "Gestione Collezioni",
            "Mercato Secondario"
        ],
        egi_link: "EGI certifica valore e ownership delle opere d'arte.",
        route: "florence_art.html"
    },

    // --- SATELLITI: FUNZIONALITÀ FLORENCE ART ---
    egizzalo: {
        label: "EGI / EGIZZALO",
        tagline: "Certifica la tua opera.",
        cat: "FEATURE",
        color: 0xFFD700, // Oro - Certificazione
        desc: "Trasforma qualsiasi asset in un EGI certificato. Upload, metadati, mint on-chain.",
        bullets: [
            "Upload Asset",
            "Generazione Metadati",
            "Mint On-Chain Algorand"
        ],
        egi_link: "Il cuore della certificazione: Egizzalo il tuo lavoro!",
        route: "#egizzalo"
    },
    collection: {
        label: "COLLECTION",
        tagline: "Gestione collezioni EGI.",
        cat: "FEATURE",
        color: 0x9932CC, // Viola Orchidea
        desc: "Crea, organizza e gestisci collezioni di opere certificate. Curatori e gallery.",
        bullets: [
            "Creazione Collezioni",
            "Curator Dashboard",
            "Gallery Virtuali"
        ],
        egi_link: "Organizza le tue opere in collezioni curate.",
        route: "#collection"
    },
    cocreatore: {
        label: "CO-CREATORE",
        tagline: "Collaborazione artistica.",
        cat: "FEATURE",
        color: 0xFF6B35, // Arancio Corallo
        desc: "Crea opere a più mani. Split royalty automatico tra co-creatori.",
        bullets: [
            "Multi-Author Works",
            "Split Royalty Automatico",
            "Contratti Collaborativi"
        ],
        egi_link: "L'arte collaborativa con diritti garantiti per tutti.",
        route: "#cocreatore"
    },
    epp_link: {
        label: "EPP IMPACT",
        tagline: "Impatto ambientale.",
        cat: "SUSTAINABILITY",
        color: 0x228B22, // Verde Foresta
        desc: "Ogni opera venduta contribuisce automaticamente ai progetti ambientali EPP.",
        bullets: [
            "20% Automatico",
            "Tracciabilità Donazioni",
            "Report Impatto"
        ],
        egi_link: "L'arte che rigenera l'ambiente.",
        route: "epp.html"
    }
};

// Configurazione Orbite - 4 funzionalità
const orbitalConfig = [
    { id: "egizzalo", orbit: 1 },
    { id: "collection", orbit: 1 },
    { id: "cocreatore", orbit: 1 },
    { id: "epp_link", orbit: 1 }
];

// Export for Global Access
window.ecosystemData = ecosystemData;
window.orbitalConfig = orbitalConfig;
