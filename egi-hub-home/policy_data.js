// policy_data.js
// DATABASE DELLA PAGINA POLICY - GOVERNANCE E COMPLIANCE

const ecosystemData = {
    // --- CORE: POLICY ---
    core: {
        label: "POLICY",
        tagline: "Governance e compliance EGI.",
        cat: "GOVERNANCE",
        color: 0x4B0082, // Indaco
        desc: "Centro di governance per regole, consensi e audit dell'ecosistema.",
        bullets: [
            "Regolamenti",
            "Compliance",
            "Audit Trail"
        ],
        egi_link: "La governance che garantisce trasparenza e legalità.",
        route: "policy.html"
    },

    // --- SATELLITI POLICY ---
    gdpr: {
        label: "GDPR",
        tagline: "Privacy e protezione dati.",
        cat: "COMPLIANCE",
        color: 0x0047AB, // Blu Cobalto
        desc: "Gestione consensi, diritti utente e protezione dati personali.",
        bullets: [
            "Consent Management",
            "Data Subject Rights",
            "Privacy by Design"
        ],
        egi_link: "EGI è GDPR-compliant by design.",
        route: "#"
    },
    audit: {
        label: "AUDIT",
        tagline: "Tracciabilità completa.",
        cat: "COMPLIANCE",
        color: 0x228B22, // Verde Foresta
        desc: "Log immutabili di tutte le operazioni per audit e verifiche.",
        bullets: [
            "Audit Log Immutabile",
            "Event Tracking",
            "Compliance Reports"
        ],
        egi_link: "Ogni azione è tracciata e verificabile.",
        route: "#"
    },
    access: {
        label: "ACCESS",
        tagline: "Controllo accessi e ruoli.",
        cat: "SECURITY",
        color: 0xE34234, // Rosso Vermiglio
        desc: "Gestione ruoli, permessi e controllo accessi multi-tenant.",
        bullets: [
            "Role-Based Access",
            "Multi-Tenant Security",
            "Permission Matrix"
        ],
        egi_link: "Sicurezza e segregazione dati garantite.",
        route: "#"
    },
    mica: {
        label: "MiCA",
        tagline: "Compliance crypto europea.",
        cat: "REGULATORY",
        color: 0xF4C430, // Giallo Zafferano
        desc: "FlorenceEGI è MiCA-safe: nessuna custodia crypto per terzi.",
        bullets: [
            "No Crypto Custody",
            "Fiat-First Approach",
            "Regulatory Safe"
        ],
        egi_link: "Operare fuori dal perimetro MiCA per semplicità.",
        route: "#"
    }
};

// Configurazione Orbite
const orbitalConfig = [
    { id: "gdpr", orbit: 1 },
    { id: "audit", orbit: 1 },
    { id: "access", orbit: 1 },
    { id: "mica", orbit: 2 }
];

// Export
window.ecosystemData = ecosystemData;
window.orbitalConfig = orbitalConfig;
