<?php

/**
 * @Oracode Legal Content: Termini e Condizioni per Trader Pro
 * 🎯 Purpose: Terms for Trader Pro user type - Professional NFT traders
 * 🛡️ Security: Enhanced KYC, trading limits, professional obligations
 *
 * @version 3.0.0 (Riforma Egili: da utility token a crediti servizio AI + reward interni)
 * @effective_date 2026-02-24
 * @user_type trader_pro
 * @locale it
 *
 * @package FlorenceEGI\Legal
 * @author Padmin D. Curtis (AI Partner OS3.0-Compliant) for Fabio Cherici
 */

return [
    'metadata' => [
        'title' => 'Termini e Condizioni per Trader Professionisti',
        'version' => '3.0.0',
        'effective_date' => '2026-02-24',
        'document_type' => 'Contratto per Operatori Professionali',
        'target_audience' => 'Trader professionisti e operatori di mercato NFT',
        'summary_of_changes' => 'v3.0.0: Riforma sistema Egili — da utility token a crediti servizio AI prepagati e sistema di premiazione interna.',
        'jurisdiction_specifics_available' => true,
    ],

    'preambolo' => [
        'title' => 'Preambolo',
        'content' => 'Questo documento ("Contratto") e un accordo legalmente vincolante tra Lei ("Trader Pro", "Operatore", o "Lei") e FlorenceEGI S.r.l. ("FlorenceEGI", "noi").

Lo status di Trader Pro e riservato a operatori professionali che intendono svolgere attivita di trading NFT con volumi significativi sulla piattaforma FlorenceEGI. Questo status comporta benefici (commissioni ridotte, accesso prioritario) e obblighi aggiuntivi (verifica avanzata, reporting).

Accettando questi termini, Lei dichiara di operare in qualita professionale e non come consumatore.'
    ],

    'articles' => [
        [
            'number' => 1,
            'category' => 'pact',
            'title' => 'Definizioni e Qualificazione',
            'subsections' => [
                [
                    'number' => '1.1',
                    'title' => 'Definizione di Trader Pro',
                    'content' => 'Un Trader Pro e un utente che:
- Opera in qualita di professionista ai sensi del Codice del Consumo
- Svolge attivita di trading NFT come attivita economica
- Soddisfa i requisiti di volume e verifica
- Ha accettato i presenti termini specifici'
                ],
                [
                    'number' => '1.2',
                    'title' => 'Requisiti di Accesso',
                    'content' => 'Per ottenere lo status Trader Pro occorre:
- Completare verifica identita avanzata (KYC Tier 2)
- Fornire documentazione fiscale (P.IVA o equivalente)
- Dimostrare esperienza nel trading di asset digitali
- Superare assessment di idoneita
- Accettare limiti e obblighi specifici'
                ],
                [
                    'number' => '1.3',
                    'title' => 'Rinuncia alle Tutele Consumeristiche',
                    'content' => 'Accettando lo status Trader Pro, Lei riconosce di operare come professionista e rinuncia alle tutele previste per i consumatori dal Codice del Consumo (D.Lgs. 206/2005), incluso il diritto di recesso per acquisti digitali.'
                ]
            ]
        ],

        [
            'number' => 2,
            'category' => 'pact',
            'title' => 'Verifica Avanzata (KYC Tier 2)',
            'subsections' => [
                [
                    'number' => '2.1',
                    'title' => 'Documentazione Richiesta',
                    'content' => 'Per la verifica Tier 2:
- Documento identita (fronte/retro, foto selfie)
- Prova di residenza recente (max 3 mesi)
- Partita IVA o registrazione attivita
- Dichiarazione fonte fondi
- Questionario esperienza trading'
                ],
                [
                    'number' => '2.2',
                    'title' => 'Monitoraggio Continuo',
                    'content' => 'Lo status Trader Pro comporta:
- Monitoraggio transazioni per AML
- Possibili richieste di documentazione aggiuntiva
- Verifica periodica (annuale) dello status
- Segnalazione automatica per operazioni sospette'
                ],
                [
                    'number' => '2.3',
                    'title' => 'Limiti Operativi',
                    'content' => 'I Trader Pro hanno limiti operativi elevati:
- Transazione singola: fino a 50.000 EUR equivalente
- Volume mensile: fino a 500.000 EUR equivalente
- Limiti superiori disponibili con verifica aggiuntiva

I limiti sono soggetti a revisione in base al profilo di rischio.'
                ]
            ]
        ],

        [
            'number' => 3,
            'category' => 'platform',
            'title' => 'Benefici Trader Pro',
            'subsections' => [
                [
                    'number' => '3.1',
                    'title' => 'Commissioni Ridotte',
                    'content' => 'Struttura commissioni Trader Pro:
- Fee base ridotte del 25% rispetto ai collector
- Ulteriori sconti per volume (vedi tabella fee dinamiche)
- Commissioni maker/taker competitive
- Possibilita di negoziare fee personalizzate per volumi elevati'
                ],
                [
                    'number' => '3.2',
                    'title' => 'Accesso Prioritario',
                    'content' => 'I Trader Pro godono di:
- Early access a drop (48h prima del pubblico)
- Priorita nelle liste whitelist
- Accesso a drop esclusivi Pro
- Esecuzione prioritaria degli ordini'
                ],
                [
                    'number' => '3.3',
                    'title' => 'Strumenti Avanzati',
                    'content' => 'Funzionalita esclusive:
- Dashboard analytics avanzata
- API trading con rate limits elevati
- Ordini avanzati (limit, stop-loss quando disponibili)
- Export dati per contabilita professionale
- Supporto prioritario dedicato'
                ],
                [
                    'number' => '3.4',
                    'title' => 'Programma Egili Pro',
                    'content' => 'I Trader Pro beneficiano di condizioni privilegiate nel sistema Egili:
- Moltiplicatore 1.5x sulla premiazione Egili per merito
- Bonus volume trimestrale
- Egili esclusivi per milestone trading
- Vantaggi riscattabili dedicati
- Accesso a pacchetti AI con condizioni Pro

Gli Egili sono crediti di servizio interni, non trasferibili, non rimborsabili e privi di valore monetario autonomo. Si applicano integralmente le disposizioni sugli Egili contenute nei Termini per Collezionisti.'
                ]
            ]
        ],

        [
            'number' => 4,
            'category' => 'rules',
            'title' => 'Obblighi del Trader Pro',
            'subsections' => [
                [
                    'number' => '4.1',
                    'title' => 'Obblighi Fiscali',
                    'content' => 'Il Trader Pro e responsabile per:
- Corretta tenuta della contabilita
- Dichiarazione dei redditi da trading
- Adempimenti IVA ove applicabili
- Comunicazioni alle autorita fiscali (es. quadro RW)

FlorenceEGI fornisce report per facilitare gli adempimenti.'
                ],
                [
                    'number' => '4.2',
                    'title' => 'Reporting DAC7',
                    'content' => 'In conformita alla Direttiva DAC7:
- FlorenceEGI comunichera annualmente all autorita fiscale i dati delle transazioni
- Lei sara informato dei dati comunicati
- Obbligo di fornire dati fiscali accurati e aggiornati'
                ],
                [
                    'number' => '4.3',
                    'title' => 'Condotta di Mercato',
                    'content' => 'Il Trader Pro si impegna a:
- Non manipolare il mercato (wash trading, spoofing)
- Non utilizzare informazioni privilegiate
- Non coordinare attivita con altri per alterare i prezzi
- Rispettare le regole di integrita del mercato'
                ],
                [
                    'number' => '4.4',
                    'title' => 'Aggiornamento Dati',
                    'content' => 'Obbligo di comunicare entro 14 giorni:
- Cambiamenti nei dati identificativi
- Modifiche alla situazione fiscale
- Variazioni nella struttura societaria (se applicabile)
- Ogni circostanza che possa influire sullo status'
                ]
            ]
        ],

        [
            'number' => 5,
            'category' => 'rules',
            'title' => 'Rischi e Limitazioni',
            'subsections' => [
                [
                    'number' => '5.1',
                    'title' => 'Consapevolezza dei Rischi',
                    'content' => 'Il Trader Pro riconosce e accetta che:
- Il trading NFT comporta rischi significativi
- Il valore degli NFT puo fluttuare drasticamente
- Non vi e garanzia di liquidita o profitto
- I mercati crypto/NFT sono volatili e speculativi

FlorenceEGI non fornisce consulenza finanziaria.'
                ],
                [
                    'number' => '5.2',
                    'title' => 'Limitazione Responsabilita',
                    'content' => 'FlorenceEGI non e responsabile per:
- Perdite da trading o fluttuazioni di mercato
- Decisioni di investimento dell operatore
- Perdite da malfunzionamenti non imputabili
- Azioni di terzi o eventi di mercato'
                ],
                [
                    'number' => '5.3',
                    'title' => 'No Investment Advice',
                    'content' => 'FlorenceEGI:
- Non fornisce consulenza finanziaria o di investimento
- Non raccomanda specifici NFT o strategie
- Non garantisce rendimenti o performance
- Non e responsabile per decisioni basate su informazioni della piattaforma'
                ]
            ]
        ],

        [
            'number' => 6,
            'category' => 'rules',
            'title' => 'Sospensione e Revoca Status',
            'subsections' => [
                [
                    'number' => '6.1',
                    'title' => 'Cause di Sospensione',
                    'content' => 'Lo status puo essere sospeso per:
- Attivita sospette o anomale
- Violazione regole di condotta
- Mancato aggiornamento documentazione
- Richieste delle autorita competenti'
                ],
                [
                    'number' => '6.2',
                    'title' => 'Revoca Definitiva',
                    'content' => 'Lo status viene revocato per:
- Violazioni gravi e ripetute
- Frode o attivita illegali
- False dichiarazioni nella verifica
- Manipolazione di mercato accertata'
                ],
                [
                    'number' => '6.3',
                    'title' => 'Effetti della Revoca',
                    'content' => 'In caso di revoca:
- Ritorno a status Collector standard
- Applicazione fee standard
- Mantenimento degli NFT posseduti
- Possibile blocco temporaneo per indagini'
                ]
            ]
        ],

        [
            'number' => 7,
            'category' => 'final',
            'title' => 'Disposizioni Finali',
            'subsections' => [
                [
                    'number' => '7.1',
                    'title' => 'Integrazione con Altri Termini',
                    'content' => 'Questi termini integrano e prevalgono sui Termini per Collezionisti per tutto cio che concerne l attivita professionale.'
                ],
                [
                    'number' => '7.2',
                    'title' => 'Modifiche',
                    'content' => 'FlorenceEGI puo modificare le condizioni Trader Pro con preavviso di 30 giorni. In caso di modifiche sostanziali, possibilita di recedere senza penali.'
                ],
                [
                    'number' => '7.3',
                    'title' => 'Legge Applicabile e Foro',
                    'content' => 'Contratto regolato dalla legge italiana. Foro competente esclusivo: Firenze, Italia.'
                ],
                [
                    'number' => '7.4',
                    'title' => 'Separabilità',
                    'content' => 'Qualora una o più disposizioni del presente Contratto siano dichiarate nulle, invalide o inapplicabili da un tribunale competente, le restanti disposizioni rimarranno pienamente valide ed efficaci. Le parti si impegnano a sostituire la clausola invalida con una disposizione valida che si avvicini il più possibile all\'intento economico e giuridico originario.'
                ],
                [
                    'number' => '7.5',
                    'title' => 'Forza Maggiore',
                    'content' => 'FlorenceEGI non sarà responsabile per inadempimenti o ritardi nell\'esecuzione delle proprie obbligazioni causati da eventi di forza maggiore ai sensi dell\'art. 1218 del Codice Civile, inclusi a titolo esemplificativo: interruzioni della rete blockchain Algorand, indisponibilità dei servizi dei PSP (Stripe, PayPal), guasti dell\'infrastruttura cloud (AWS), attacchi informatici, calamità naturali, provvedimenti delle autorità, pandemie o altri eventi al di fuori del ragionevole controllo di FlorenceEGI. In caso di forza maggiore, gli obblighi saranno sospesi per la durata dell\'evento. FlorenceEGI informerà tempestivamente gli utenti tramite i canali disponibili.'
                ],
                [
                    'number' => '7.6',
                    'title' => 'Segnalazione Contenuti e Reclami (DSA)',
                    'content' => 'In conformità con il Regolamento (UE) 2022/2065 (Digital Services Act), FlorenceEGI mette a disposizione un meccanismo per la segnalazione di contenuti ritenuti illeciti e per la presentazione di reclami contro decisioni di moderazione. Le segnalazioni possono essere inviate tramite l\'apposita funzionalità sulla Piattaforma o all\'indirizzo legal@florenceegi.com. Ogni segnalazione sarà esaminata tempestivamente e la decisione motivata sarà comunicata al segnalante e, ove applicabile, al titolare del contenuto segnalato.'
                ],
                [
                    'number' => '7.7',
                    'title' => 'Risoluzione Alternativa Controversie',
                    'content' => 'In alternativa al ricorso giudiziario, FlorenceEGI si impegna a partecipare in buona fede a procedure di mediazione o conciliazione presso organismi di risoluzione alternativa delle controversie (ADR) iscritti negli elenchi previsti dal D.Lgs. 28/2010 e dal D.Lgs. 130/2015. Trattandosi di rapporto professionale, la piattaforma ODR per consumatori non è applicabile.'
                ],
                [
                    'number' => '7.8',
                    'title' => 'Comunicazioni',
                    'content' => 'Le comunicazioni relative al presente Contratto (incluse modifiche dei termini, sospensioni e notifiche legali) saranno inviate tramite email all\'indirizzo associato all\'account dell\'utente e/o tramite notifica sulla Piattaforma. Le comunicazioni si considerano ricevute al momento della visualizzazione sulla Piattaforma o dopo 48 ore dall\'invio dell\'email, in base a quale evento si verifichi per primo.'
                ]
            ]
        ]
    ],

    'contact_info' => [
        'title' => 'Contatti',
        'content' => 'Per richieste status Trader Pro: pro@florenceegi.com
Per supporto Trader Pro: support-pro@florenceegi.com
Per compliance e reporting: compliance@florenceegi.com'
    ],

    'risk_disclaimer' => [
        'title' => 'Avvertenza sui Rischi',
        'content' => 'IL TRADING DI NFT E ASSET DIGITALI COMPORTA RISCHI SIGNIFICATIVI. LEI POTREBBE PERDERE PARTE O TUTTO IL CAPITALE INVESTITO. I RENDIMENTI PASSATI NON SONO INDICATIVI DI RISULTATI FUTURI. OPERI SOLO CON FONDI CHE PUO PERMETTERSI DI PERDERE. SE NON COMPRENDE I RISCHI, NON OPERI.'
    ]
];
