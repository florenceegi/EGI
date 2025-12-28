// oracode_data.js
// DATABASE DELLA PAGINA ORACODE - PARADIGMA FILOSOFICO-TECNICO

const ecosystemData = {
    // --- CORE: ORACODE ---
    core: {
        label: "ORACODE",
        tagline: "Il paradigma della verità tecnologica.",
        cat: "TRUTH PROTOCOL",
        color: 0xE34234, // Rosso Vermiglio
        desc: "Documentazione totale e Verità Tecnologica. Il codice non mente.",
        bullets: [
            "Codice come Legge",
            "Trasparenza Radicale",
            "Etica by Design"
        ],
        egi_link: "Il fondamento filosofico di tutto l'ecosistema.",
        route: "oracode.html"
    },

    // --- PILASTRI ORACODE ---
    truth: {
        label: "VERITÀ",
        tagline: "La verità è tracciabile.",
        cat: "PRINCIPLE",
        color: 0xFFFFFF, // Bianco Puro
        desc: "Ogni azione lascia traccia immutabile, ricostruibile e interrogabile.",
        bullets: [
            "Audit Trail",
            "Append-Only Logs",
            "10 Years Retention"
        ],
        egi_link: "Nessun segreto che violi diritti fondamentali.",
        route: "#"
    },
    logic: {
        label: "LOGICA",
        tagline: "La logica è cosciente.",
        cat: "PRINCIPLE",
        color: 0x50C878, // Verde Smeraldo
        desc: "Il software non è neutro. Ogni algoritmo esprime valori.",
        bullets: [
            "Values in Code",
            "Ethical Testing",
            "Bias Detection"
        ],
        egi_link: "Responsabilità per gli effetti del codice.",
        route: "#"
    },
    docs: {
        label: "DOCS",
        tagline: "La documentazione è vita.",
        cat: "PRINCIPLE",
        color: 0x0047AB, // Blu Cobalto
        desc: "Codice senza documentazione è codice morto.",
        bullets: [
            "Total Documentation",
            "ADR Records",
            "Living Diagrams"
        ],
        egi_link: "Nessun monopolio della comprensione.",
        route: "#"
    },
    os3: {
        label: "OS3",
        tagline: "Oracode System 3.0",
        cat: "ARCHITECTURE",
        color: 0xF4C430, // Giallo Zafferano
        desc: "L'architettura cognitiva che documenta il perché di ogni decisione.",
        bullets: [
            "Cognitive Architecture",
            "Decision Records",
            "Self-Documenting"
        ],
        egi_link: "Il sistema operativo della verità.",
        route: "#"
    }
};

// Configurazione Orbite
const orbitalConfig = [
    { id: "truth", orbit: 1 },
    { id: "logic", orbit: 1 },
    { id: "docs", orbit: 1 },
    { id: "os3", orbit: 2 }
];

// Export
window.ecosystemData = ecosystemData;
window.orbitalConfig = orbitalConfig;
