// project_data.js
// DATABASE DELLA PAGINA PROJECT - PROGETTI VERTICALI

const ecosystemData = {
    // --- CORE: PROJECT ---
    core: {
        label: "PROJECT",
        tagline: "Progetti verticali EGI.",
        cat: "CATEGORY",
        color: 0x00FFAA, // Verde Acqua
        desc: "Hub dei progetti verticali costruiti sulla piattaforma EGI.",
        bullets: [
            "Prodotti Verticali",
            "Applicazioni Specifiche",
            "Soluzioni Tematiche"
        ],
        egi_link: "Centro di coordinamento per tutti i progetti verticali.",
        route: "project.html"
    },

    // --- SATELLITI: I 3 PROGETTI ---
    art: {
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
    natan: {
        label: "NATAN LOC",
        tagline: "AI documentale per la PA.",
        cat: "VERTICAL PROJECT",
        color: 0x0047AB, // Blu Cobalto
        desc: "Consultazione e gestione documentale con AI, audit e notarizzazione.",
        bullets: [
            "Scanner Albi Pretori",
            "Notarizzazione Atti",
            "Compliance L.69/2009"
        ],
        egi_link: "EGI garantisce Proof verificabile sui documenti pubblici.",
        route: "natan.html"
    },
    tosca: {
        label: "TOSCA BANDI",
        tagline: "Monitoraggio bandi pubblici.",
        cat: "VERTICAL PROJECT",
        color: 0xCC7722, // Arancio Ocra
        desc: "Piattaforma per il monitoraggio e matching di bandi e opportunità.",
        bullets: [
            "Monitoraggio Bandi",
            "Matching Opportunità",
            "Alert Scadenze"
        ],
        egi_link: "EGI abilita prodotti tematici verticali replicabili.",
        route: "tosca.html"
    }
};

// Configurazione Orbite - Solo 1 orbita con i 3 progetti
const orbitalConfig = [
    { id: "art", orbit: 1 },
    { id: "natan", orbit: 1 },
    { id: "tosca", orbit: 1 }
];

// Export for Global Access
window.ecosystemData = ecosystemData;
window.orbitalConfig = orbitalConfig;
