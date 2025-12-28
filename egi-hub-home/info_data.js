// info_data.js
// DATABASE DELLA PAGINA INFO - DOCUMENTAZIONE E RISORSE

const ecosystemData = {
    // --- CORE: INFO ---
    core: {
        label: "INFO",
        tagline: "Documentazione e risorse EGI.",
        cat: "DOCUMENTATION",
        color: 0x00BFFF, // Azzurro Cielo
        desc: "Tutto ciò che serve per comprendere e adottare EGI.",
        bullets: [
            "Whitepaper",
            "API Docs",
            "Guide"
        ],
        egi_link: "Rendere l'ecosistema comprensibile e adottabile.",
        route: "info.html"
    },

    // --- SATELLITI INFO ---
    whitepaper: {
        label: "WHITEPAPER",
        tagline: "Documento fondativo.",
        cat: "DOCUMENT",
        color: 0xFFFFFF, // Bianco
        desc: "Il whitepaper completo di FlorenceEGI con visione e architettura.",
        bullets: [
            "Vision",
            "Architecture",
            "Tokenomics"
        ],
        egi_link: "La bibbia di FlorenceEGI.",
        route: "#"
    },
    apidocs: {
        label: "API DOCS",
        tagline: "Riferimento tecnico.",
        cat: "TECHNICAL",
        color: 0x50C878, // Verde Smeraldo
        desc: "Documentazione completa delle API REST e SDK.",
        bullets: [
            "REST Endpoints",
            "SDK Reference",
            "Code Examples"
        ],
        egi_link: "Per gli sviluppatori che integrano.",
        route: "#"
    },
    guides: {
        label: "GUIDE",
        tagline: "Tutorial e how-to.",
        cat: "LEARNING",
        color: 0xF4C430, // Giallo Zafferano
        desc: "Guide passo-passo per iniziare con EGI.",
        bullets: [
            "Getting Started",
            "Best Practices",
            "Use Cases"
        ],
        egi_link: "Impara facendo.",
        route: "#"
    },
    faq: {
        label: "FAQ",
        tagline: "Domande frequenti.",
        cat: "SUPPORT",
        color: 0x4B0082, // Indaco
        desc: "Risposte alle domande più comuni su EGI.",
        bullets: [
            "Common Questions",
            "Troubleshooting",
            "Contact"
        ],
        egi_link: "Le risposte che cerchi.",
        route: "#"
    }
};

// Configurazione Orbite
const orbitalConfig = [
    { id: "whitepaper", orbit: 1 },
    { id: "apidocs", orbit: 1 },
    { id: "guides", orbit: 1 },
    { id: "faq", orbit: 2 }
];

// Export
window.ecosystemData = ecosystemData;
window.orbitalConfig = orbitalConfig;
