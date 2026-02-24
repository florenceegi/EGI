<?php

/**
 * @Oracode Legal Metadata: Version 2.0.0
 * 🎯 Purpose: Centralized metadata for legal document version management
 * 🛡️ Security: Immutable version tracking with cryptographic integrity
 *
 * @package FlorenceEGI\Legal
 * @version 3.0.0 - Riforma Egili: crediti servizio AI + reward interni
 * @author Padmin D. Curtis (AI Partner OS3.0-Compliant) for Fabio Cherici
 */

return [
    'version' => '3.0.0',
    'release_date' => '2026-02-24',
    'effective_date' => '2026-02-24',
    'created_by' => 'legal@florenceegi.com',
    'approved_by' => 'fabio.cherici@florenceegi.com',

    'summary_of_changes' => 'Riforma sistema Egili: da utility token a crediti servizio AI prepagati (FIAT) e sistema di premiazione interna. Eliminazione acquisto Egili come asset. Eliminazione pagamento EGI in Egili. Compliance MiCA rafforzata. Aggiornamento sistema pagamenti: disclosure PSP (Stripe Connect, PayPal), onboarding venditore, payout, rimborsi/chargeback, metodi di pagamento futuri (ALGO, Crypto via CASP).',

    'change_details' => [
        'major_changes_v3' => [
            'Egili ridefiniti: da utility token a crediti di servizio AI interni + reward',
            'Eliminato acquisto Egili come asset autonomo',
            'Eliminato pagamento EGI tramite Egili',
            'Introdotti pacchetti servizi AI acquistabili in FIAT (EUR) che accreditano Egili',
            'Egili utilizzabili per: servizi AI, riduzione/azzeramento fee, sconti piattaforma',
            'Egili ottenibili anche tramite premiazione per merito (reward system)',
            'Rafforzate clausole di non-trasferibilità, non-rimborsabilità, assenza valore monetario',
            'Rimosso riferimento a Fondo Egili (1% fee) — sostituito da sistema premiazione flessibile'
        ],
        'files_updated' => [
            'creator.php - Art. 1.1 (definizione), Art. 4.3 (restrizioni IP acquirente), Art. 5.2 (flusso pagamento: FIAT/ALGO/Crypto), Art. 5.4 (rapporto Egili), Art. 5.5 (onboarding PSP Stripe Connect), Art. 5.6 (payout), Art. 5.7 (rimborsi/chargeback), Art. 6 (completa riscrittura), Art. 7.6 (note fiscali), Art. 9.1 (riscrittura NATAN), Art. 9.3 (limitazioni AI), Art. 9.4 (NUOVO: disclosure AI Act), Art. 10.4 (royalties on-chain corretto), Art. 11 (allegati), Art. 12.5-12.11 (NUOVI: separabilità, forza maggiore, cessione, DSA, ODR, limitazione responsabilità, comunicazioni)',
            'collector.php - Art. 1.1 (definizione NATAN aggiornata), Art. 3.5 (metodi pagamento: FIAT/ALGO/Crypto), Art. 3.6 (politica rimborso), Art. 4.3 (diritti morali aggiornato), Art. 4.4 (NUOVO: restrizioni uso dettagliate), Art. 5 (completa riscrittura: PSP setup, rebind, royalties 4.5%, payout, chargeback), Art. 6 (completa riscrittura programma Egili), Art. 7.1 (riscrittura NATAN), Art. 7.3 (limitazioni AI), Art. 7.4 (NUOVO: disclosure AI Act), Art. 10.4 (ODR aggiornato), Art. 10.5-10.10 (NUOVI: separabilità, forza maggiore, cessione, DSA, limitazione responsabilità, comunicazioni)',
            'patron.php - Art. 3.3 (programma Egili potenziato), Art. 7.3 (foro consumatore), Art. 7.4-7.9 (NUOVI: separabilità, forza maggiore, DSA, ODR, limitazione responsabilità, comunicazioni)',
            'trader_pro.php - Art. 3.4 (programma Egili Pro), Art. 7.4-7.8 (NUOVI: separabilità, forza maggiore, DSA, ADR, comunicazioni)',
            'epp.php - Art. 9.4 (controversie aggiornato), Art. 9.5-9.7 (NUOVI: separabilità, forza maggiore, comunicazioni)',
            'company.php - Art. 10.5-10.7 (NUOVI: separabilità, forza maggiore, comunicazioni)',
            'allegato_a_fee.php - NUOVO: Tabella Fee Dinamiche (Contributor/Normal, Mint/Rebind, Commodity)',
            'allegato_b_egili.php - NUOVO: Guida al Sistema Egili (definizione, ottenimento, utilizzo, regole)'
        ],
        'compliance_rationale' => [
            'MiCA: Egili non più classificabili come utility token — ora sono crediti di servizio prepagati (fuori scope MiCA)',
            'Equivalenza: modello analogo a crediti SaaS (es. crediti OpenAI, AWS credits)',
            'Pagamento sempre in FIAT tramite PSP regolamentati (Stripe/PayPal)',
            'Nessuna trasferibilità, scambiabilità o convertibilità — eliminato rischio e-money classification',
            'PSD2: Disclosure esplicita PSP (Stripe, PayPal) come processori pagamenti terzi',
            'Pagamenti crypto futuri (ALGO diretto, stablecoin via CASP) previsti nei ToS ma non ancora attivi',
            'Politica rimborso e chargeback esplicitata per conformità Codice del Consumo'
        ]
    ],

    'available_user_types' => [
        'creator' => [
            'status' => 'ready',
            'version' => '3.0.0',
            'priority' => 'high',
            'notes' => 'v3.0.0: Riforma Egili + NATAN AI Act + clausole universali + allegati (Art. 4.3, 7.6, 9.1, 9.3, 9.4, 12.5-12.11)'
        ],
        'collector' => [
            'status' => 'ready',
            'version' => '3.0.0',
            'priority' => 'high',
            'notes' => 'v3.0.0: Riforma Egili + NATAN AI Act + restrizioni uso + clausole (Art. 1.1, 4.4, 7.1, 7.3, 7.4, 10.4-10.10)'
        ],
        'patron' => [
            'status' => 'ready',
            'version' => '3.0.0',
            'priority' => 'high',
            'notes' => 'v3.0.0: Riforma Egili + clausole universali (Art. 3.3, 7.3-7.9)'
        ],
        'epp' => [
            'status' => 'ready',
            'version' => '2.0.0',
            'priority' => 'high',
            'notes' => 'v2.0.0 + clausole compliance (Art. 9.4-9.7: separabilità, forza maggiore, comunicazioni)'
        ],
        'company' => [
            'status' => 'ready',
            'version' => '2.0.0',
            'priority' => 'medium',
            'notes' => 'v2.0.0 + clausole compliance (Art. 10.5-10.7: separabilità, forza maggiore, comunicazioni)'
        ],
        'trader_pro' => [
            'status' => 'ready',
            'version' => '3.0.0',
            'priority' => 'medium',
            'notes' => 'v3.0.0: Riforma Egili + clausole universali (Art. 3.4, 7.4-7.8)'
        ]
    ],

    'available_locales' => [
        'it' => [
            'status' => 'primary',
            'completion' => '100%',
            'notes' => 'All user types complete - source language'
        ],
        'en' => [
            'status' => 'pending_translation',
            'completion' => '0%',
            'notes' => 'AI translation + legal review required'
        ],
        'es' => [
            'status' => 'pending_translation',
            'completion' => '0%',
            'notes' => 'AI translation + legal review required'
        ],
        'pt' => [
            'status' => 'pending_translation',
            'completion' => '0%',
            'notes' => 'AI translation + legal review required'
        ],
        'fr' => [
            'status' => 'pending_translation',
            'completion' => '0%',
            'notes' => 'AI translation + legal review required'
        ],
        'de' => [
            'status' => 'pending_translation',
            'completion' => '0%',
            'notes' => 'AI translation + legal review required'
        ]
    ],

    'content_hashes' => [
        'it' => [
            'creator' => 'pending_calculation',
            'collector' => 'pending_calculation',
            'patron' => 'pending_calculation',
            'epp' => 'pending_calculation',
            'company' => 'pending_calculation',
            'trader_pro' => 'pending_calculation',
        ]
    ],

    'technology_disclosures' => [
        'ai_services' => [
            'N.A.T.A.N.' => 'Neural Adaptive Technology for Art Navigation — AI system analyzing artwork characteristics for market positioning (powered by Claude/Anthropic). No user profiling, no autonomous decisions.',
            'Claude' => 'Anthropic Claude for conversation and analysis',
            'OpenAI' => 'GPT models for content processing',
            'Processing' => 'EU-based where available, US with SCCs'
        ],
        'blockchain' => [
            'Network' => 'Algorand MainNet',
            'Token_Standard' => 'ARC-3, ARC-19, ARC-69',
            'Smart_Contracts' => 'PyTeal/Beaker',
            'Explorer' => 'algoexplorer.io'
        ],
        'custody' => [
            'Provider' => 'AWS Key Management Service',
            'Security' => 'HSM-backed, FIPS 140-2 Level 3',
            'Regions' => 'eu-west-1 (Ireland), eu-central-1 (Frankfurt)',
            'Insurance' => 'Covered under platform insurance policy'
        ]
    ],

    'legal_review' => [
        'reviewed_by' => 'legal@florenceegi.com',
        'review_date' => '2026-02-24',
        'review_status' => 'pending_approval',
        'review_notes' => 'v3.0.0: Riforma Egili + sistema pagamenti + compliance gap P0/P1. Egili: da utility token a crediti servizio AI + reward interni. Pagamenti: disclosure PSP, onboarding Stripe Connect, payout, rimborsi/chargeback. NATAN: riscrittura descrizione (analisi opere, no profilazione, no decisioni autonome), unificazione acronimo. EU AI Act: disclosure completa (Reg. 2024/1689). Clausole universali: separabilità, forza maggiore, DSA, ODR, cessione, limitazione responsabilità, comunicazioni. IP: restrizioni uso dettagliate. Fiscale: note obblighi creator. Allegati A (fee) e B (Egili) creati.',
        'next_review_due' => '2026-08-24',
        'ai_act_review' => 'Completato — Disclosure in creator.php Art. 9.4 e collector.php Art. 7.4'
    ],

    'technical_notes' => [
        'file_structure' => '/resources/legal/terms/versions/current/',
        'symlink_target' => '3.0.0',
        'integration_status' => 'ready',
        'consent_type_slug' => 'terms-of-service',
        'required_permissions' => ['legal.terms.edit', 'legal.terms.create_version'],
        'deployment_requirements' => [
            'Run FlorenceEgiPrivacyPolicySeeder for Privacy/Cookie updates',
            'Clear view cache: php artisan view:clear',
            'Clear config cache: php artisan config:clear',
            'Notify users of updated terms via email'
        ]
    ],

    'compliance_checklist' => [
        'gdpr_article_7' => true,   // Consent documentation
        'gdpr_article_13' => true,  // Information to be provided
        'gdpr_article_22' => true,  // Automated decision-making
        'dsa_2022_2065' => true,    // Digital Services Act
        'italian_privacy_code' => true, // D.Lgs. 196/2003
        'consumer_code' => true,    // D.Lgs. 206/2005
        'dac7_reporting' => true,   // Fiscal transparency
        'eu_ai_act' => true, // Regulation 2024/1689 — Disclosure AI in creator.php Art. 9.4 e collector.php Art. 7.4
        'mica_regulation' => true, // Markets in Crypto-Assets - Egili fuori scope (crediti servizio, non token)
        'psd2_compliance' => true // Payment Services Directive 2 - PSP disclosure, no direct payment handling
    ],

    'version_history' => [
        '1.0.0' => [
            'date' => '2025-06-22',
            'summary' => 'Initial versioned system - Creator terms only'
        ],
        '2.0.0' => [
            'date' => '2025-02-15',
            'summary' => 'Complete update - All user types, AI/blockchain integration'
        ],
        '3.0.0' => [
            'date' => '2026-02-24',
            'summary' => 'Riforma Egili + sistema pagamenti. Egili: da utility token a crediti servizio AI (FIAT) + reward interni. Pagamenti: disclosure PSP (Stripe Connect, PayPal), onboarding venditore, payout, rimborsi/chargeback, predisposizione ALGO e Crypto CASP. Compliance MiCA e PSD2 rafforzata.'
        ]
    ]
];
