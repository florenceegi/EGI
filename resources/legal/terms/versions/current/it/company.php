<?php

/**
 * @Oracode Legal Content: Termini e Condizioni per Utenti Company
 * 🎯 Purpose: Terms for Company user type - Corporate/enterprise users
 * 🛡️ Security: B2B agreements, KYB, volume licensing, enterprise features
 *
 * @version 2.0.0 (Prima versione completa Company/Enterprise)
 * @effective_date 2025-02-15
 * @user_type company
 * @locale it
 *
 * @package FlorenceEGI\Legal
 * @author Padmin D. Curtis (AI Partner OS3.0-Compliant) for Fabio Cherici
 */

return [
    'metadata' => [
        'title' => 'Termini e Condizioni per Aziende',
        'version' => '2.0.0',
        'effective_date' => '2025-02-15',
        'document_type' => 'Contratto B2B per Utenti Aziendali',
        'target_audience' => 'Aziende e organizzazioni corporate che utilizzano FlorenceEGI',
        'summary_of_changes' => 'Prima versione completa per utenti aziendali - Requisiti KYB, fatturazione, funzionalita enterprise',
        'jurisdiction_specifics_available' => true,
    ],

    'preambolo' => [
        'title' => 'Preambolo',
        'content' => 'Questo documento ("Contratto") costituisce un accordo legalmente vincolante tra la Vostra azienda ("Azienda", "Company", o "Voi") e FlorenceEGI S.r.l. ("FlorenceEGI", "noi").

FlorenceEGI offre alle aziende la possibilita di utilizzare la piattaforma per iniziative di branding sostenibile, collezioni corporate, employee engagement e progetti di responsabilita sociale attraverso NFT e contributi EPP.

Questo accordo B2B definisce i termini specifici per l\'utilizzo aziendale della piattaforma.'
    ],

    'articles' => [
        [
            'number' => 1,
            'category' => 'pact',
            'title' => 'Definizioni e Ambito',
            'subsections' => [
                [
                    'number' => '1.1',
                    'title' => 'Definizione di Account Aziendale',
                    'content' => 'Un Account Aziendale FlorenceEGI consente:
- Gestione centralizzata di wallet e asset aziendali
- Accesso multi-utente con ruoli e permessi
- Fatturazione centralizzata
- Accesso a funzionalita enterprise
- Reportistica avanzata per compliance'
                ],
                [
                    'number' => '1.2',
                    'title' => 'Tipologie di Utilizzo Aziendale',
                    'content' => 'Gli utilizzi aziendali tipici includono:
- **Branded Collections**: Collezioni NFT a marchio aziendale
- **Employee Rewards**: Premi e riconoscimenti per dipendenti
- **Customer Engagement**: NFT per programmi fedelta
- **CSR Initiatives**: Contributi EPP come parte di strategia sostenibilita
- **Event Collectibles**: NFT commemorativi per eventi aziendali'
                ]
            ]
        ],

        [
            'number' => 2,
            'category' => 'pact',
            'title' => 'Verifica Aziendale (KYB)',
            'subsections' => [
                [
                    'number' => '2.1',
                    'title' => 'Requisiti di Verifica',
                    'content' => 'Per attivare un account aziendale occorre fornire:
- Visura camerale o equivalente
- Documenti identita rappresentanti legali
- Procura/delega per il referente aziendale
- Partita IVA e codice fiscale aziendale
- Coordinate bancarie aziendali'
                ],
                [
                    'number' => '2.2',
                    'title' => 'Processo di Onboarding',
                    'content' => 'L\'onboarding aziendale include:
1. Submission documentazione
2. Verifica KYB (5-10 giorni lavorativi)
3. Setup account e configurazione ruoli
4. Training per amministratori
5. Attivazione funzionalita enterprise'
                ]
            ]
        ],

        [
            'number' => 3,
            'category' => 'platform',
            'title' => 'Funzionalita Enterprise',
            'subsections' => [
                [
                    'number' => '3.1',
                    'title' => 'Multi-User Management',
                    'content' => 'L\'account aziendale supporta:
- Utenti illimitati (piano Enterprise)
- Ruoli personalizzabili (Admin, Creator, Viewer, Approver)
- SSO integration (SAML 2.0)
- Audit log di tutte le attivita
- Gestione permessi granulare'
                ],
                [
                    'number' => '3.2',
                    'title' => 'Treasury Aziendale',
                    'content' => 'Gestione centralizzata fondi:
- Wallet aziendale con multi-signature opzionale
- Budget allocation per dipartimenti/progetti
- Approvazioni multi-livello per transazioni
- Integrazione con sistemi contabili (API)'
                ],
                [
                    'number' => '3.3',
                    'title' => 'White-Label Options',
                    'content' => 'Per Enterprise con necessita specifiche:
- Personalizzazione interfaccia con brand aziendale
- Subdomain dedicato (company.florenceegi.com)
- Email notifications brandizzate
- Landing page personalizzate per campagne'
                ]
            ]
        ],

        [
            'number' => 4,
            'category' => 'platform',
            'title' => 'Creazione e Gestione NFT',
            'subsections' => [
                [
                    'number' => '4.1',
                    'title' => 'Branded Collections',
                    'content' => 'L\'azienda puo creare collezioni a proprio marchio:
- Full ownership dei contenuti creati
- Gestione diritti IP tramite dashboard
- Possibilita di collaborare con artisti della piattaforma
- Distribuzione interna o pubblica'
                ],
                [
                    'number' => '4.2',
                    'title' => 'IP e Licensing',
                    'content' => 'Per contenuti creati dall\'azienda:
- L\'azienda mantiene piena proprieta IP
- FlorenceEGI riceve licenza operativa per la piattaforma
- Possibilita di definire licenze personalizzate per destinatari

Per collaborazioni con artisti:
- Termini definiti in accordi specifici
- FlorenceEGI puo facilitare ma non e parte degli accordi IP'
                ],
                [
                    'number' => '4.3',
                    'title' => 'Distribuzione NFT',
                    'content' => 'Modalita di distribuzione disponibili:
- **Airdrop**: Invio gratuito a lista destinatari
- **Claim Link**: Link univoci per riscatto
- **Marketplace**: Vendita pubblica standard
- **Private Sale**: Vendita a lista riservata
- **Employee Portal**: Distribuzione interna'
                ]
            ]
        ],

        [
            'number' => 5,
            'category' => 'rules',
            'title' => 'Fatturazione e Pagamenti',
            'subsections' => [
                [
                    'number' => '5.1',
                    'title' => 'Struttura Pricing',
                    'content' => 'I costi per account aziendali includono:
- **Setup Fee**: Una tantum per onboarding (variabile per tier)
- **Subscription**: Canone mensile/annuale per funzionalita
- **Transaction Fees**: Commissioni su transazioni (scaglionate per volume)
- **Add-ons**: Funzionalita aggiuntive opzionali

Dettaglio prezzi nel listino concordato.'
                ],
                [
                    'number' => '5.2',
                    'title' => 'Fatturazione',
                    'content' => 'Modalita di fatturazione:
- Fattura mensile o annuale anticipata (subscription)
- Fattura mensile posticipata (transaction fees)
- Pagamento bonifico SEPA o carta di credito
- Termini standard: 30 giorni DF'
                ],
                [
                    'number' => '5.3',
                    'title' => 'Volume Discounts',
                    'content' => 'Sconti applicabili:
- Commitment annuale: -15% su subscription
- Volume transazioni: scaglioni progressivi
- Bundle multi-servizio: pricing dedicato
- NFT: quotazioni specifiche per alto volume'
                ]
            ]
        ],

        [
            'number' => 6,
            'category' => 'rules',
            'title' => 'Compliance e Privacy',
            'subsections' => [
                [
                    'number' => '6.1',
                    'title' => 'Data Processing Agreement',
                    'content' => 'Ove FlorenceEGI tratti dati personali per conto dell\'azienda:
- Si applica DPA conforme GDPR (Allegato A)
- FlorenceEGI agisce come Responsabile del trattamento
- L\'azienda resta Titolare per i propri dati'
                ],
                [
                    'number' => '6.2',
                    'title' => 'Reportistica Compliance',
                    'content' => 'FlorenceEGI fornisce:
- Report audit trail su richiesta
- Documentazione per compliance interna
- Supporto per due diligence
- Certificazioni di sicurezza (ISO 27001 in corso)'
                ],
                [
                    'number' => '6.3',
                    'title' => 'Export Dati',
                    'content' => 'L\'azienda ha diritto a:
- Export completo dati in formato strutturato
- API per integrazione con sistemi interni
- Report personalizzati su richiesta'
                ]
            ]
        ],

        [
            'number' => 7,
            'category' => 'rules',
            'title' => 'SLA e Supporto',
            'subsections' => [
                [
                    'number' => '7.1',
                    'title' => 'Service Level Agreement',
                    'content' => 'Per clienti Enterprise:
- Uptime garantito: 99.5% (esclusa manutenzione programmata)
- Finestra manutenzione: comunicata con 72h anticipo
- Supporto: dedicato con SLA di risposta'
                ],
                [
                    'number' => '7.2',
                    'title' => 'Livelli di Supporto',
                    'content' => 'Supporto disponibile:
- **Standard**: Email, risposta 24h lavorative
- **Priority**: Email + chat, risposta 4h lavorative
- **Enterprise**: Dedicated Account Manager, risposta 1h

Livello incluso dipende dal piano sottoscritto.'
                ],
                [
                    'number' => '7.3',
                    'title' => 'Escalation',
                    'content' => 'Procedura escalation per issue critici:
1. Ticket supporto standard
2. Escalation a Team Lead (4h senza risposta)
3. Account Manager (issue non risolto 24h)
4. Management FlorenceEGI (issue critico business)'
                ]
            ]
        ],

        [
            'number' => 8,
            'category' => 'final',
            'title' => 'Responsabilita e Limitazioni',
            'subsections' => [
                [
                    'number' => '8.1',
                    'title' => 'Limitazione Responsabilita',
                    'content' => 'La responsabilita massima di FlorenceEGI e limitata:
- Al maggiore tra i fee pagati negli ultimi 12 mesi o 10.000 EUR
- Esclusione danni indiretti, consequenziali, perdita profitti
- Esclusione per cause di forza maggiore

Tali limitazioni non si applicano in caso di dolo o colpa grave.'
                ],
                [
                    'number' => '8.2',
                    'title' => 'Indennizzo',
                    'content' => 'L\'azienda indennizza FlorenceEGI per:
- Violazioni IP derivanti da contenuti caricati dall\'azienda
- Uso improprio della piattaforma da parte di utenti aziendali
- Violazioni normative imputabili all\'azienda'
                ]
            ]
        ],

        [
            'number' => 9,
            'category' => 'final',
            'title' => 'Durata e Terminazione',
            'subsections' => [
                [
                    'number' => '9.1',
                    'title' => 'Durata',
                    'content' => 'Il contratto ha durata:
- Iniziale: 12 mesi dalla data di attivazione
- Rinnovo: automatico per periodi di 12 mesi
- Disdetta: con preavviso di 60 giorni prima della scadenza'
                ],
                [
                    'number' => '9.2',
                    'title' => 'Terminazione Anticipata',
                    'content' => 'Terminazione anticipata possibile per:
- Violazione materiale non sanata entro 30 giorni da notifica
- Insolvenza o procedure concorsuali
- Mutuo accordo scritto'
                ],
                [
                    'number' => '9.3',
                    'title' => 'Effetti Terminazione',
                    'content' => 'Alla terminazione:
- Cessazione accesso alle funzionalita enterprise
- Export dati entro 30 giorni
- Fatturazione finale per servizi erogati
- Gli NFT creati rimangono sulla blockchain'
                ]
            ]
        ],

        [
            'number' => 10,
            'category' => 'final',
            'title' => 'Disposizioni Finali',
            'subsections' => [
                [
                    'number' => '10.1',
                    'title' => 'Legge Applicabile',
                    'content' => 'Contratto regolato dalla legge italiana.'
                ],
                [
                    'number' => '10.2',
                    'title' => 'Foro Competente',
                    'content' => 'Foro esclusivo: Firenze, Italia.'
                ],
                [
                    'number' => '10.3',
                    'title' => 'Intero Accordo',
                    'content' => 'Questo contratto, insieme agli allegati, costituisce l\'intero accordo tra le parti e sostituisce precedenti intese.'
                ],
                [
                    'number' => '10.4',
                    'title' => 'Modifiche',
                    'content' => 'Modifiche valide solo se in forma scritta e sottoscritte da entrambe le parti.'
                ],
                [
                    'number' => '10.5',
                    'title' => 'Separabilità',
                    'content' => 'Qualora una o più disposizioni del presente Contratto siano dichiarate nulle, invalide o inapplicabili da un tribunale competente, le restanti disposizioni rimarranno pienamente valide ed efficaci. Le parti si impegnano a sostituire la clausola invalida con una disposizione valida che si avvicini il più possibile all\'intento economico e giuridico originario.'
                ],
                [
                    'number' => '10.6',
                    'title' => 'Forza Maggiore',
                    'content' => 'Nessuna delle parti sarà responsabile per inadempimenti o ritardi nell\'esecuzione delle proprie obbligazioni causati da eventi di forza maggiore ai sensi dell\'art. 1218 del Codice Civile, inclusi a titolo esemplificativo: interruzioni della rete blockchain Algorand, indisponibilità dei servizi di pagamento, guasti dell\'infrastruttura cloud (AWS), attacchi informatici, calamità naturali, provvedimenti delle autorità, pandemie o altri eventi al di fuori del ragionevole controllo della parte inadempiente. In caso di forza maggiore, gli obblighi saranno sospesi per la durata dell\'evento. La parte coinvolta informerà tempestivamente l\'altra parte tramite i canali disponibili.'
                ],
                [
                    'number' => '10.7',
                    'title' => 'Comunicazioni',
                    'content' => 'Le comunicazioni relative al presente Contratto (incluse modifiche dei termini, fatture, sospensioni e notifiche legali) saranno inviate tramite email agli indirizzi comunicati in fase di registrazione o successivamente aggiornati. Le comunicazioni si considerano ricevute dopo 48 ore dall\'invio dell\'email, salvo prova contraria. Per comunicazioni urgenti relative alla sicurezza o all\'operatività, FlorenceEGI potrà utilizzare anche la Piattaforma e i contatti telefonici forniti.'
                ]
            ]
        ]
    ],

    'contact_info' => [
        'title' => 'Contatti',
        'content' => 'Per informazioni commerciali: enterprise@florenceegi.com
Per supporto clienti enterprise: support-enterprise@florenceegi.com
Per questioni legali/contrattuali: legal@florenceegi.com'
    ],

    'annexes' => [
        'title' => 'Allegati',
        'content' => 'Allegato A: Data Processing Agreement (DPA)
Allegato B: Service Level Agreement dettagliato
Allegato C: Listino prezzi Enterprise
Allegato D: Specifiche tecniche API

Gli allegati sono forniti separatamente e formano parte integrante del contratto.'
    ]
];
