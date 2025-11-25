<?php

/**
 * @Oracode Legal Content: Termini e Condizioni per Utenti Collezionisti
 * 🎯 Purpose: Terms for Collector user type - NFT buyers and collectors
 * 🛡️ Security: Consumer protection, wallet security, GDPR compliance
 *
 * @version 2.0.0 (Aggiornamento AI/Blockchain/Wallet)
 * @effective_date 2025-02-15
 * @user_type collector
 * @locale it
 *
 * @package FlorenceEGI\Legal
 * @author Padmin D. Curtis (AI Partner OS3.0-Compliant) for Fabio Cherici
 */

return [
    'metadata' => [
        'title' => 'Termini e Condizioni per Collezionisti',
        'version' => '2.0.0',
        'effective_date' => '2025-02-15',
        'document_type' => 'Contratto per Utenti Collezionisti',
        'target_audience' => 'Utenti qualificati come "Collezionista" (Collector) della Piattaforma FlorenceEGI',
        'summary_of_changes' => 'Aggiornamento completo per integrazione AI, Algorand blockchain, wallet custodial/non-custodial, EPP',
        'jurisdiction_specifics_available' => true,
    ],

    'preambolo' => [
        'title' => 'Preambolo',
        'content' => 'Benvenuto su FlorenceEGI. Questo documento ("**Contratto**") costituisce un accordo legalmente vincolante tra Lei ("**Collezionista**", "**Utente**", o "**Lei**") e FlorenceEGI S.r.l. ("**FlorenceEGI**", "**noi**").

FlorenceEGI è un marketplace NFT sostenibile che unisce arte digitale e protezione ambientale. Come Collezionista, Lei può esplorare, acquistare e collezionare opere digitali uniche ("**EGI**") create da artisti verificati, contribuendo simultaneamente a progetti di protezione ambientale ("**EPP**" - Environmental Protection Projects).

Utilizzando la Piattaforma, Lei accetta integralmente questi termini, l\'Informativa Privacy e la Cookie Policy. **La preghiamo di leggere attentamente questo documento.**'
    ],

    'articles' => [
        [
            'number' => 1,
            'category' => 'pact',
            'title' => 'Definizioni',
            'subsections' => [
                [
                    'number' => '1.1',
                    'title' => 'Definizioni Chiave',
                    'content' => '**EGI**: Opera digitale unica (NFT) registrata sulla blockchain Algorand, rappresentante un\'opera d\'arte o contenuto digitale.

**Wallet**: Portafoglio digitale per la custodia di asset blockchain. Può essere:
- **Custodial**: Gestito da FlorenceEGI con chiavi crittografate via AWS KMS
- **Non-custodial**: Gestito autonomamente dall\'utente (es. PeraWallet)

**Egili**: Token di utilità interno, non trasferibile, guadagnato per azioni meritevoli sulla piattaforma.

**EPP (Environmental Protection Projects)**: Progetti ambientali verificati a cui viene destinato il 20% di ogni transazione.

**N.A.T.A.N.**: Assistente AI integrato per supporto e consulenza sulla piattaforma.'
                ],
                [
                    'number' => '1.2',
                    'title' => 'Qualificazione dell\'Utente',
                    'content' => 'Lei dichiara di avere almeno 18 anni e la piena capacità giuridica per stipulare questo Contratto. Se agisce per conto di un\'organizzazione, garantisce di avere l\'autorità per vincolarla.'
                ]
            ]
        ],

        [
            'number' => 2,
            'category' => 'pact',
            'title' => 'Account e Wallet',
            'subsections' => [
                [
                    'number' => '2.1',
                    'title' => 'Registrazione Account',
                    'content' => 'Per utilizzare la Piattaforma è necessario creare un account fornendo informazioni accurate e aggiornate. Lei è responsabile della sicurezza delle Sue credenziali.'
                ],
                [
                    'number' => '2.2',
                    'title' => 'Opzioni Wallet',
                    'content' => 'FlorenceEGI offre due modalità di gestione wallet:

**Wallet Custodial (FEGI Wallet)**:
- Creato automaticamente alla registrazione
- Chiavi private crittografate con AWS KMS
- FlorenceEGI non ha mai accesso alle chiavi in chiaro
- Backup automatico e recupero tramite autenticazione

**Wallet Non-Custodial (Connessione Esterna)**:
- Connessione di wallet esterni (es. PeraWallet)
- Piena responsabilità dell\'utente per la custodia delle chiavi
- Nessun recupero possibile in caso di perdita chiavi'
                ],
                [
                    'number' => '2.3',
                    'title' => 'Sicurezza Wallet',
                    'content' => 'Lei è l\'unico responsabile della sicurezza del Suo wallet. FlorenceEGI declina ogni responsabilità per perdite derivanti da:
- Compromissione delle credenziali
- Perdita delle chiavi private (wallet non-custodial)
- Transazioni non autorizzate per negligenza dell\'utente'
                ]
            ]
        ],

        [
            'number' => 3,
            'category' => 'platform',
            'title' => 'Acquisto di EGI',
            'subsections' => [
                [
                    'number' => '3.1',
                    'title' => 'Processo di Acquisto',
                    'content' => 'L\'acquisto di un EGI avviene tramite transazione sulla blockchain Algorand. Il processo include:
1. Selezione dell\'opera
2. Conferma del prezzo (incluse commissioni)
3. Autorizzazione della transazione
4. Registrazione sulla blockchain
5. Trasferimento dell\'EGI al Suo wallet'
                ],
                [
                    'number' => '3.2',
                    'title' => 'Ripartizione del Prezzo',
                    'content' => 'Il prezzo di acquisto è ripartito secondo il modello trasparente FlorenceEGI:
- **Creatore**: Percentuale variabile (fee dinamiche in base al volume)
- **EPP**: 20% destinato al progetto ambientale selezionato dal Creatore
- **Piattaforma**: Commissione di servizio

I dettagli sono visibili nella pagina di ogni EGI prima dell\'acquisto.'
                ],
                [
                    'number' => '3.3',
                    'title' => 'Irreversibilità delle Transazioni',
                    'content' => '**ATTENZIONE**: Le transazioni blockchain sono **irreversibili**. Una volta confermata, una transazione non può essere annullata. Verifichi attentamente tutti i dettagli prima di procedere.'
                ],
                [
                    'number' => '3.4',
                    'title' => 'Gas Fee e Costi Blockchain',
                    'content' => 'Le transazioni su Algorand comportano costi di rete minimi (gas fee). Tali costi sono a carico dell\'acquirente e non sono rimborsabili.'
                ]
            ]
        ],

        [
            'number' => 4,
            'category' => 'platform',
            'title' => 'Diritti sull\'EGI Acquistato',
            'subsections' => [
                [
                    'number' => '4.1',
                    'title' => 'Proprietà del Token',
                    'content' => 'Acquistando un EGI, Lei diventa proprietario del token digitale registrato sulla blockchain. Questo include:
- Diritto di possesso e custodia
- Diritto di trasferimento/rivendita
- Diritto di esposizione in collezione personale'
                ],
                [
                    'number' => '4.2',
                    'title' => 'Licenza sul Contenuto',
                    'content' => 'Il Creatore Le concede una licenza **non esclusiva, personale e non commerciale** per:
- Visualizzare l\'opera
- Esporre l\'opera in ambienti personali (fisici o virtuali)
- Condividere sui social media con attribuzione

**NON è consentito** senza esplicita autorizzazione:
- Uso commerciale dell\'immagine
- Riproduzione per vendita
- Creazione di opere derivate
- Utilizzo in pubblicità o merchandising'
                ],
                [
                    'number' => '4.3',
                    'title' => 'Diritti Morali del Creatore',
                    'content' => 'Il Creatore mantiene sempre i diritti morali sull\'opera, incluso il diritto di paternità e integrità dell\'opera.'
                ]
            ]
        ],

        [
            'number' => 5,
            'category' => 'platform',
            'title' => 'Rivendita e Mercato Secondario',
            'subsections' => [
                [
                    'number' => '5.1',
                    'title' => 'Diritto di Rivendita',
                    'content' => 'Lei può rivendere gli EGI posseduti sulla Piattaforma o su marketplace esterni compatibili con Algorand.'
                ],
                [
                    'number' => '5.2',
                    'title' => 'Royalties al Creatore',
                    'content' => 'Ogni rivendita comporta il pagamento automatico di royalties al Creatore originale, come definito dallo smart contract. Le percentuali sono visibili nei metadati dell\'EGI.'
                ],
                [
                    'number' => '5.3',
                    'title' => 'Contributo EPP su Rivendita',
                    'content' => 'Anche le rivendite contribuiscono ai progetti EPP, garantendo un impatto ambientale continuo per ogni transazione.'
                ]
            ]
        ],

        [
            'number' => 6,
            'category' => 'platform',
            'title' => 'Programma Egili',
            'subsections' => [
                [
                    'number' => '6.1',
                    'title' => 'Natura degli Egili',
                    'content' => 'Gli Egili sono token di utilità interni alla Piattaforma:
- **Non trasferibili** tra utenti
- **Non scambiabili** su mercati esterni
- **Non rappresentano** valuta, titolo finanziario o credito

Possono essere utilizzati esclusivamente per ottenere vantaggi sulla Piattaforma.'
                ],
                [
                    'number' => '6.2',
                    'title' => 'Come Guadagnare Egili',
                    'content' => 'I Collezionisti possono guadagnare Egili attraverso:
- Acquisti sulla Piattaforma
- Referral di nuovi utenti
- Partecipazione a eventi della community
- Attività speciali definite periodicamente'
                ],
                [
                    'number' => '6.3',
                    'title' => 'Utilizzo degli Egili',
                    'content' => 'Gli Egili possono essere utilizzati per:
- Sconti sulle commissioni di acquisto
- Accesso anticipato a drop esclusivi
- Vantaggi definiti nella sezione Tokenomics

L\'utilizzo è definitivo (burn) e non reversibile.'
                ]
            ]
        ],

        [
            'number' => 7,
            'category' => 'platform',
            'title' => 'Servizi AI (N.A.T.A.N.)',
            'subsections' => [
                [
                    'number' => '7.1',
                    'title' => 'Descrizione del Servizio',
                    'content' => 'N.A.T.A.N. (Neuro-Analytical Text Analysis Network) è l\'assistente AI integrato che può aiutarLa a:
- Esplorare la piattaforma
- Comprendere le opere e gli artisti
- Ricevere informazioni sui progetti EPP
- Assistenza generale'
                ],
                [
                    'number' => '7.2',
                    'title' => 'Privacy AI',
                    'content' => 'N.A.T.A.N. processa esclusivamente:
- Metadati pubblici delle opere
- Sue domande e interazioni volontarie

**Non vengono mai processati** dati personali identificativi senza Suo esplicito consenso. Per dettagli, consulti l\'Informativa Privacy.'
                ],
                [
                    'number' => '7.3',
                    'title' => 'Limitazioni AI',
                    'content' => 'Le risposte di N.A.T.A.N. hanno carattere informativo e non costituiscono:
- Consulenza legale o fiscale
- Consulenza finanziaria o di investimento
- Garanzia di accuratezza assoluta

Per decisioni importanti, consulti sempre un professionista.'
                ]
            ]
        ],

        [
            'number' => 8,
            'category' => 'rules',
            'title' => 'Obblighi e Responsabilità',
            'subsections' => [
                [
                    'number' => '8.1',
                    'title' => 'Obblighi dell\'Utente',
                    'content' => 'Lei si impegna a:
- Fornire informazioni accurate e aggiornate
- Mantenere sicure le proprie credenziali
- Non utilizzare la Piattaforma per attività illegali
- Rispettare i diritti di proprietà intellettuale
- Non tentare di manipolare o compromettere la Piattaforma'
                ],
                [
                    'number' => '8.2',
                    'title' => 'Limitazione di Responsabilità',
                    'content' => 'FlorenceEGI non è responsabile per:
- Fluttuazioni di valore degli EGI
- Perdite derivanti da volatilità del mercato
- Danni da interruzioni di servizio non imputabili
- Perdite per compromissione wallet per negligenza utente
- Contenuti creati da terzi (artisti)'
                ],
                [
                    'number' => '8.3',
                    'title' => 'Dati Blockchain',
                    'content' => '**IMPORTANTE**: I dati registrati su blockchain (transazioni, proprietà EGI) sono:
- Pubblici e visibili a chiunque
- Permanenti e non cancellabili
- Fuori dal controllo di FlorenceEGI una volta registrati'
                ]
            ]
        ],

        [
            'number' => 9,
            'category' => 'rules',
            'title' => 'Sospensione e Terminazione',
            'subsections' => [
                [
                    'number' => '9.1',
                    'title' => 'Sospensione Account',
                    'content' => 'FlorenceEGI può sospendere l\'account in caso di:
- Violazione dei presenti termini
- Attività sospette o fraudolente
- Richiesta di autorità competenti

La sospensione non influisce sugli EGI già posseduti nel wallet.'
                ],
                [
                    'number' => '9.2',
                    'title' => 'Cancellazione Volontaria',
                    'content' => 'Lei può richiedere la cancellazione del Suo account in qualsiasi momento. Prima della cancellazione:
- Trasferisca gli EGI a un wallet esterno
- Utilizzi o perda gli Egili accumulati
- Richieda export dei Suoi dati (GDPR)'
                ],
                [
                    'number' => '9.3',
                    'title' => 'Effetti della Terminazione',
                    'content' => 'Alla terminazione:
- L\'accesso alla Piattaforma verrà revocato
- Gli EGI nel wallet rimangono di Sua proprietà
- I dati blockchain rimangono permanenti
- I dati personali saranno gestiti secondo l\'Informativa Privacy'
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
                    'title' => 'Modifiche al Contratto',
                    'content' => 'FlorenceEGI può modificare questi termini con preavviso di 15 giorni via email o notifica in-app. Il proseguimento dell\'utilizzo dopo tale periodo costituisce accettazione.'
                ],
                [
                    'number' => '10.2',
                    'title' => 'Legge Applicabile',
                    'content' => 'Questo Contratto è regolato dalla legge italiana, con particolare riferimento al Codice del Consumo (D.Lgs. 206/2005) per le tutele applicabili.'
                ],
                [
                    'number' => '10.3',
                    'title' => 'Foro Competente',
                    'content' => 'Per le controversie, il foro competente è quello di Firenze, Italia. Resta salvo il foro del consumatore ove applicabile.'
                ],
                [
                    'number' => '10.4',
                    'title' => 'Risoluzione Alternativa Controversie',
                    'content' => 'Per controversie relative a contratti online, Lei può utilizzare la piattaforma ODR dell\'UE: https://ec.europa.eu/consumers/odr'
                ]
            ]
        ]
    ],

    'contact_info' => [
        'title' => 'Contatti',
        'content' => 'Per domande su questi Termini: legal@florenceegi.com
Per supporto: support@florenceegi.com
Per privacy: privacy@florenceegi.com'
    ],

    'jurisdiction_specifics' => [
        'title' => 'Clausole Specifiche per Giurisdizione',
        'description' => 'Disposizioni aggiuntive applicabili in base alla giurisdizione dell\'utente',
        'clauses' => [
            'IT' => [
                'title' => 'Disposizioni Specifiche per l\'Italia',
                'content' => 'Per utenti residenti in Italia:
• **Diritto di Recesso**: Per acquisti di beni digitali, il diritto di recesso si estingue con l\'inizio del download/streaming se Lei ha espressamente acconsentito.
• **Garanzia Legale**: Gli EGI sono beni digitali; si applicano le disposizioni del Codice del Consumo per i contenuti digitali.
• **ADR**: Possibilità di ricorso a procedure di risoluzione alternativa delle controversie.'
            ],
            'EU' => [
                'title' => 'Disposizioni per l\'Unione Europea',
                'content' => 'Per utenti residenti nell\'UE:
• Applicazione del Regolamento (UE) 2022/2065 (Digital Services Act)
• Diritti GDPR (Reg. UE 2016/679) come descritto nell\'Informativa Privacy
• Piattaforma ODR per controversie online'
            ]
        ]
    ]
];
