// payments_data.js
// DATABASE DELLA PAGINA PAYMENTS - SISTEMA PAGAMENTI

const ecosystemData = {
    // --- CORE: PAYMENTS ---
    core: {
        label: "PAYMENTS",
        tagline: "Sistema pagamenti fiat-first.",
        cat: "FINANCE",
        color: 0xF4C430, // Giallo Zafferano
        desc: "Paghi in euro, ricevi EGI. Onboarding semplice per tutti.",
        bullets: [
            "Fiat-First",
            "Multi-Gateway",
            "Auto Billing"
        ],
        egi_link: "Accessibilità massima con pagamenti tradizionali.",
        route: "payments.html"
    },

    // --- SATELLITI PAYMENTS ---
    stripe: {
        label: "STRIPE",
        tagline: "Gateway pagamenti principale.",
        cat: "PAYMENT GATEWAY",
        color: 0x635BFF, // Viola Stripe
        desc: "Integrazione nativa con Stripe per carte e bonifici.",
        bullets: [
            "Card Payments",
            "SEPA Transfer",
            "Subscription"
        ],
        egi_link: "Il gateway più affidabile per l'Europa.",
        route: "#"
    },
    split: {
        label: "SPLIT PAY",
        tagline: "Divisione automatica.",
        cat: "DISTRIBUTION",
        color: 0x50C878, // Verde Smeraldo
        desc: "Split automatico tra Creator, Piattaforma ed EPP.",
        bullets: [
            "Creator Royalty 4.5%",
            "Platform Fee",
            "EPP Donation"
        ],
        egi_link: "Distribuzione trasparente e automatica.",
        route: "#"
    },
    billing: {
        label: "BILLING",
        tagline: "Fatturazione automatica.",
        cat: "INVOICING",
        color: 0x0047AB, // Blu Cobalto
        desc: "Fatture elettroniche, ricevute e gestione fiscale.",
        bullets: [
            "E-Invoice",
            "Receipt Generation",
            "Tax Compliance"
        ],
        egi_link: "Compliance fiscale automatizzata.",
        route: "#"
    },
    wallet: {
        label: "WALLET",
        tagline: "Wallet auto-generati.",
        cat: "CUSTODY",
        color: 0xE34234, // Rosso Vermiglio
        desc: "Wallet tecnici per utenti senza crypto experience.",
        bullets: [
            "Auto-Generated",
            "Technical Custody",
            "NFT Only"
        ],
        egi_link: "Esperienza e-commerce tradizionale.",
        route: "#"
    }
};

// Configurazione Orbite
const orbitalConfig = [
    { id: "stripe", orbit: 1 },
    { id: "split", orbit: 1 },
    { id: "billing", orbit: 1 },
    { id: "wallet", orbit: 2 }
];

// Export
window.ecosystemData = ecosystemData;
window.orbitalConfig = orbitalConfig;
