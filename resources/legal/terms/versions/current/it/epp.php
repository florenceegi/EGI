<?php

/**
 * @Oracode Legal Content: Termini e Condizioni per EPP (Environmental Protection Projects)
 * 🎯 Purpose: Terms for EPP user type - Environmental organizations and projects
 * 🛡️ Security: KYC/KYB requirements, fund transparency, impact reporting
 *
 * @version 2.0.0 (Prima versione completa EPP)
 * @effective_date 2025-02-15
 * @user_type epp
 * @locale it
 *
 * @package FlorenceEGI\Legal
 * @author Padmin D. Curtis (AI Partner OS3.0-Compliant) for Fabio Cherici
 */

return [
    'metadata' => [
        'title' => 'Termini e Condizioni per Progetti EPP',
        'version' => '2.0.0',
        'effective_date' => '2025-02-15',
        'document_type' => 'Contratto per Organizzazioni Ambientali Partner',
        'target_audience' => 'Organizzazioni e progetti qualificati come EPP (Environmental Protection Projects)',
        'summary_of_changes' => 'Prima versione completa - Requisiti verifica, gestione fondi, reporting impatto',
        'jurisdiction_specifics_available' => true,
    ],

    'preambolo' => [
        'title' => 'Preambolo',
        'content' => 'Benvenuto nel programma EPP di FlorenceEGI. Questo documento ("**Contratto**") costituisce un accordo legalmente vincolante tra la Sua organizzazione ("**EPP**", "**Progetto**", "**Organizzazione**", o "**Voi**") e FlorenceEGI S.r.l. ("**FlorenceEGI**", "**noi**").

FlorenceEGI è un marketplace NFT sostenibile che destina il **20% di ogni transazione** a progetti di protezione ambientale verificati. Come EPP Partner, la Vostra organizzazione riceverà contributi automatici dalla community FlorenceEGI, con l\'impegno di utilizzarli per attività di protezione ambientale e di fornire report trasparenti sull\'impatto generato.

Questo accordo definisce i requisiti per diventare e rimanere un EPP Partner, le modalità di gestione dei fondi e gli obblighi di trasparenza e reporting.'
    ],

    'articles' => [
        [
            'number' => 1,
            'category' => 'pact',
            'title' => 'Definizioni e Requisiti',
            'subsections' => [
                [
                    'number' => '1.1',
                    'title' => 'Definizione di EPP',
                    'content' => 'Un **EPP (Environmental Protection Project)** è un\'organizzazione o progetto che:
- Ha come missione principale la protezione ambientale
- È legalmente costituita e operativa
- Può dimostrare attività concrete di impatto ambientale
- Accetta gli standard di trasparenza FlorenceEGI'
                ],
                [
                    'number' => '1.2',
                    'title' => 'Requisiti di Ammissibilità',
                    'content' => 'Per diventare EPP Partner è necessario:
- Essere un\'entità giuridica riconosciuta (associazione, fondazione, ONG, impresa sociale, etc.)
- Avere almeno 12 mesi di attività documentata
- Fornire documentazione legale e fiscale completa
- Superare la procedura di verifica (KYB)
- Fornire referenze verificabili'
                ],
                [
                    'number' => '1.3',
                    'title' => 'Tipologie di EPP Ammessi',
                    'content' => 'Sono ammesse organizzazioni attive in:
- Conservazione della biodiversità
- Riforestazione e tutela forestale
- Protezione oceani e risorse idriche
- Energia rinnovabile e sostenibilità
- Economia circolare e riduzione rifiuti
- Educazione ambientale
- Ricerca e innovazione ambientale
- Attivismo e advocacy ambientale'
                ]
            ]
        ],

        [
            'number' => 2,
            'category' => 'pact',
            'title' => 'Processo di Verifica (KYB)',
            'subsections' => [
                [
                    'number' => '2.1',
                    'title' => 'Documentazione Richiesta',
                    'content' => 'Per la verifica iniziale occorre fornire:
- Atto costitutivo e statuto
- Certificato di iscrizione al registro pertinente
- Bilanci degli ultimi 2 anni (o report attività se no-profit)
- Documenti identità dei rappresentanti legali
- Codice fiscale/Partita IVA dell\'organizzazione
- Coordinate bancarie/wallet per ricevere i fondi
- Portfolio di progetti realizzati'
                ],
                [
                    'number' => '2.2',
                    'title' => 'Processo di Valutazione',
                    'content' => 'La valutazione include:
1. **Verifica documentale**: Controllo autenticità e completezza
2. **Due diligence**: Verifica reputazionale e operativa
3. **Valutazione impatto**: Analisi delle attività e risultati
4. **Approvazione**: Decisione del comitato FlorenceEGI

Il processo richiede mediamente 2-4 settimane.'
                ],
                [
                    'number' => '2.3',
                    'title' => 'Rinnovo Verifica',
                    'content' => 'La verifica deve essere rinnovata annualmente con:
- Bilancio/report anno precedente
- Aggiornamento documentazione legale (se modificata)
- Report attività FlorenceEGI dell\'anno
- Eventuali nuove certificazioni ottenute'
                ]
            ]
        ],

        [
            'number' => 3,
            'category' => 'platform',
            'title' => 'Gestione dei Contributi',
            'subsections' => [
                [
                    'number' => '3.1',
                    'title' => 'Fonte dei Contributi',
                    'content' => 'I contributi EPP derivano da:
- **20% automatico**: Su ogni vendita di EGI dove il Creatore ha selezionato il Vostro progetto
- **Contributi diretti**: Da Patron e utenti che scelgono di supportarVi
- **Campagne speciali**: Iniziative dedicate di raccolta fondi

Tutti i flussi sono tracciati su blockchain Algorand.'
                ],
                [
                    'number' => '3.2',
                    'title' => 'Modalità di Ricezione',
                    'content' => 'I fondi possono essere ricevuti:
- **Wallet Algorand**: Trasferimento diretto in ALGO o asset digitali
- **Conversione FIAT**: Conversione e bonifico bancario (fee applicabili)

La scelta può essere modificata con preavviso di 30 giorni.'
                ],
                [
                    'number' => '3.3',
                    'title' => 'Tempistiche',
                    'content' => 'I fondi vengono:
- Accumulati in tempo reale nel Treasury dedicato
- Distribuiti mensilmente (entro il 15 del mese successivo)
- Accompagnati da report dettagliato delle transazioni'
                ],
                [
                    'number' => '3.4',
                    'title' => 'Utilizzo dei Fondi',
                    'content' => 'I contributi ricevuti devono essere utilizzati per:
- Attività direttamente connesse alla missione ambientale
- Costi operativi ragionevoli (max 15% per amministrazione)
- Progetti documentabili e verificabili

**È vietato** utilizzare i fondi per:
- Attività politiche di partito
- Compensi sproporzionati
- Attività non connesse alla missione ambientale'
                ]
            ]
        ],

        [
            'number' => 4,
            'category' => 'platform',
            'title' => 'Obblighi di Trasparenza e Reporting',
            'subsections' => [
                [
                    'number' => '4.1',
                    'title' => 'Report Mensile',
                    'content' => 'Entro il 5 di ogni mese, fornire:
- Conferma ricezione fondi del mese precedente
- Breve aggiornamento attività in corso
- Eventuali risultati raggiunti'
                ],
                [
                    'number' => '4.2',
                    'title' => 'Report Trimestrale',
                    'content' => 'Entro 15 giorni dalla fine di ogni trimestre:
- Report dettagliato utilizzo fondi FlorenceEGI
- Metriche di impatto ambientale
- Documentazione fotografica/video
- Aggiornamento obiettivi'
                ],
                [
                    'number' => '4.3',
                    'title' => 'Report Annuale',
                    'content' => 'Entro 60 giorni dalla fine dell\'anno:
- Bilancio completo dell\'anno
- Report impatto comprensivo
- Piano attività anno successivo
- Audit esterno (se richiesto per importi > 50.000 EUR)'
                ],
                [
                    'number' => '4.4',
                    'title' => 'Metriche di Impatto',
                    'content' => 'Le metriche richieste variano per tipologia, esempi:
- **Riforestazione**: Alberi piantati, ettari recuperati, CO2 sequestrata
- **Oceani**: Kg plastica rimossa, km costa pulita, specie protette
- **Biodiversità**: Animali salvati, habitat protetti, specie monitorate
- **Educazione**: Persone formate, scuole raggiunte, materiali prodotti

FlorenceEGI fornirà template standardizzati per il reporting.'
                ]
            ]
        ],

        [
            'number' => 5,
            'category' => 'platform',
            'title' => 'Visibilità sulla Piattaforma',
            'subsections' => [
                [
                    'number' => '5.1',
                    'title' => 'Pagina EPP',
                    'content' => 'Ogni EPP Partner ha una pagina dedicata con:
- Descrizione missione e attività
- Team e contatti
- Progetti attivi
- Metriche di impatto in tempo reale
- Storico contributi ricevuti (aggregato)'
                ],
                [
                    'number' => '5.2',
                    'title' => 'Associazione con Creatori',
                    'content' => 'I Creatori possono scegliere il Vostro EPP per le loro opere. Questo comporta:
- Visibilità nella pagina dell\'opera
- Menzione nelle comunicazioni di vendita
- Link diretto al Vostro profilo'
                ],
                [
                    'number' => '5.3',
                    'title' => 'Contenuti e Comunicazione',
                    'content' => 'Potete:
- Pubblicare aggiornamenti sulla piattaforma
- Interagire con Creatori e Collezionisti
- Organizzare eventi e campagne
- Utilizzare il badge "EPP Verified by FlorenceEGI"

Tutti i contenuti devono rispettare le linee guida della community.'
                ]
            ]
        ],

        [
            'number' => 6,
            'category' => 'rules',
            'title' => 'Standard di Condotta',
            'subsections' => [
                [
                    'number' => '6.1',
                    'title' => 'Impegni dell\'EPP',
                    'content' => 'L\'organizzazione si impegna a:
- Operare con integrità e trasparenza
- Utilizzare i fondi esclusivamente per la missione dichiarata
- Fornire report accurati e verificabili
- Rispondere tempestivamente alle richieste di FlorenceEGI
- Notificare cambiamenti organizzativi significativi'
                ],
                [
                    'number' => '6.2',
                    'title' => 'Divieti',
                    'content' => 'È severamente vietato:
- Fornire informazioni false o fuorvianti
- Utilizzare i fondi per scopi non autorizzati
- Rappresentare falsamente il rapporto con FlorenceEGI
- Condurre attività illegali o non etiche
- Greenwashing o claims ambientali non verificabili'
                ],
                [
                    'number' => '6.3',
                    'title' => 'Audit e Verifiche',
                    'content' => 'FlorenceEGI si riserva il diritto di:
- Richiedere audit indipendenti sull\'uso dei fondi
- Condurre verifiche in loco (con preavviso ragionevole)
- Richiedere documentazione aggiuntiva
- Sospendere i trasferimenti in caso di irregolarità'
                ]
            ]
        ],

        [
            'number' => 7,
            'category' => 'rules',
            'title' => 'Aspetti Legali e Fiscali',
            'subsections' => [
                [
                    'number' => '7.1',
                    'title' => 'Natura dei Trasferimenti',
                    'content' => 'I contributi da FlorenceEGI costituiscono:
- Erogazioni a titolo di liberalità (se l\'organizzazione è idonea)
- Corrispettivi per servizi (se strutturato come partnership)

La qualificazione fiscale dipende dalla forma giuridica dell\'EPP.'
                ],
                [
                    'number' => '7.2',
                    'title' => 'Obblighi Fiscali',
                    'content' => 'L\'EPP è responsabile per:
- Corretta registrazione contabile dei contributi
- Adempimenti fiscali nel proprio paese
- Emissione di ricevute/attestazioni se richiesto
- Conformità con normative locali sul fundraising'
                ],
                [
                    'number' => '7.3',
                    'title' => 'Indipendenza',
                    'content' => 'L\'EPP resta un\'entità indipendente. Questo accordo:
- Non crea rapporto di lavoro subordinato
- Non crea joint venture o società di fatto
- Non conferisce poteri di rappresentanza reciproci'
                ]
            ]
        ],

        [
            'number' => 8,
            'category' => 'rules',
            'title' => 'Sospensione e Terminazione',
            'subsections' => [
                [
                    'number' => '8.1',
                    'title' => 'Cause di Sospensione',
                    'content' => 'Lo status EPP può essere sospeso per:
- Mancato invio report richiesti
- Irregolarità nell\'uso dei fondi
- Violazione degli standard di condotta
- Cambiamenti significativi non comunicati
- Indagini in corso su attività dell\'organizzazione'
                ],
                [
                    'number' => '8.2',
                    'title' => 'Procedura di Sospensione',
                    'content' => 'In caso di sospensione:
1. Notifica scritta con motivazione
2. 15 giorni per presentare controdeduzioni
3. Decisione definitiva entro 30 giorni
4. Durante la sospensione, i fondi vengono trattenuti'
                ],
                [
                    'number' => '8.3',
                    'title' => 'Terminazione',
                    'content' => 'L\'accordo può essere terminato:
- **Da FlorenceEGI**: Per violazioni gravi, con effetto immediato
- **Dall\'EPP**: Con preavviso di 60 giorni
- **Di comune accordo**: Con termini concordati

I fondi accumulati non distribuiti seguiranno le disposizioni di terminazione.'
                ],
                [
                    'number' => '8.4',
                    'title' => 'Effetti della Terminazione',
                    'content' => 'Alla terminazione:
- Cessano nuovi flussi di contributi
- Fondi accumulati distribuiti secondo report finale
- Rimozione dalla piattaforma come EPP attivo
- Permanenza dello storico pubblico (per trasparenza)'
                ]
            ]
        ],

        [
            'number' => 9,
            'category' => 'final',
            'title' => 'Disposizioni Finali',
            'subsections' => [
                [
                    'number' => '9.1',
                    'title' => 'Modifiche',
                    'content' => 'FlorenceEGI può modificare questi termini con preavviso di 60 giorni. In caso di disaccordo, l\'EPP può recedere senza penali.'
                ],
                [
                    'number' => '9.2',
                    'title' => 'Legge Applicabile',
                    'content' => 'Questo Contratto è regolato dalla legge italiana.'
                ],
                [
                    'number' => '9.3',
                    'title' => 'Foro Competente',
                    'content' => 'Per le controversie, il foro competente è quello di Firenze, Italia.'
                ],
                [
                    'number' => '9.4',
                    'title' => 'Controversie',
                    'content' => 'Prima di adire le vie legali, le parti si impegnano a tentare una risoluzione amichevole entro 30 giorni dalla contestazione. In alternativa, le parti possono ricorrere a procedure di mediazione presso organismi iscritti negli elenchi previsti dal D.Lgs. 28/2010.'
                ],
                [
                    'number' => '9.5',
                    'title' => 'Separabilità',
                    'content' => 'Qualora una o più disposizioni del presente Contratto siano dichiarate nulle, invalide o inapplicabili da un tribunale competente, le restanti disposizioni rimarranno pienamente valide ed efficaci. Le parti si impegnano a sostituire la clausola invalida con una disposizione valida che si avvicini il più possibile all\'intento economico e giuridico originario.'
                ],
                [
                    'number' => '9.6',
                    'title' => 'Forza Maggiore',
                    'content' => 'Nessuna delle parti sarà responsabile per inadempimenti o ritardi nell\'esecuzione delle proprie obbligazioni causati da eventi di forza maggiore ai sensi dell\'art. 1218 del Codice Civile, inclusi a titolo esemplificativo: interruzioni della rete blockchain Algorand, indisponibilità dei servizi di pagamento, guasti dell\'infrastruttura cloud (AWS), attacchi informatici, calamità naturali, provvedimenti delle autorità, pandemie o altri eventi al di fuori del ragionevole controllo della parte inadempiente. In caso di forza maggiore, gli obblighi saranno sospesi per la durata dell\'evento. La parte coinvolta informerà tempestivamente l\'altra parte tramite i canali disponibili.'
                ],
                [
                    'number' => '9.7',
                    'title' => 'Comunicazioni',
                    'content' => 'Le comunicazioni relative al presente Contratto (incluse modifiche dei termini, sospensioni, richieste di documentazione e notifiche legali) saranno inviate tramite email agli indirizzi comunicati in fase di registrazione. Le comunicazioni si considerano ricevute dopo 48 ore dall\'invio dell\'email, salvo prova contraria.'
                ]
            ]
        ]
    ],

    'contact_info' => [
        'title' => 'Contatti',
        'content' => 'Per candidarsi come EPP: epp@florenceegi.com
Per supporto EPP Partner: epp-support@florenceegi.com
Per questioni legali: legal@florenceegi.com'
    ],

    'annexes' => [
        'title' => 'Allegati',
        'content' => '**Allegato A**: Checklist documenti per candidatura EPP
**Allegato B**: Template report mensile/trimestrale
**Allegato C**: Linee guida metriche di impatto per categoria
**Allegato D**: Codice etico EPP Partner

Gli allegati sono disponibili su richiesta a epp@florenceegi.com'
    ]
];
