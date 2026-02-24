<?php

/**
 * @Oracode Legal Content: Termini e Condizioni per Utenti Patron
 * 🎯 Purpose: Terms for Patron user type - supporters of artists and projects
 * 🛡️ Security: Patronage agreements, contribution transparency, GDPR compliance
 *
 * @version 3.0.0 (Riforma Egili: da utility token a crediti servizio AI + reward interni)
 * @effective_date 2026-02-24
 * @user_type patron
 * @locale it
 *
 * @package FlorenceEGI\Legal
 * @author Padmin D. Curtis (AI Partner OS3.0-Compliant) for Fabio Cherici
 */

return [
    'metadata' => [
        'title' => 'Termini e Condizioni per Patron',
        'version' => '3.0.0',
        'effective_date' => '2026-02-24',
        'document_type' => 'Contratto per Utenti Patron',
        'target_audience' => 'Utenti qualificati come "Patron" della Piattaforma FlorenceEGI - Sostenitori di artisti e progetti',
        'summary_of_changes' => 'v3.0.0: Riforma sistema Egili — da utility token a crediti servizio AI prepagati e sistema di premiazione interna.',
        'jurisdiction_specifics_available' => true,
    ],

    'preambolo' => [
        'title' => 'Preambolo',
        'content' => 'Benvenuto, Patron. Questo documento ("**Contratto**") costituisce un accordo legalmente vincolante tra Lei ("**Patron**", "**Sostenitore**", o "**Lei**") e FlorenceEGI S.r.l. ("**FlorenceEGI**", "**noi**").

Come Patron di FlorenceEGI, Lei sceglie di sostenere attivamente artisti digitali e progetti di protezione ambientale (EPP). Il Suo ruolo va oltre la semplice collezione: Lei diventa un mecenate dell\'arte digitale sostenibile, contribuendo alla crescita dell\'ecosistema creativo e ambientale.

Utilizzando la Piattaforma in qualità di Patron, Lei accetta integralmente questi termini, l\'Informativa Privacy e la Cookie Policy.'
    ],

    'articles' => [
        [
            'number' => 1,
            'category' => 'pact',
            'title' => 'Definizioni e Ruolo del Patron',
            'subsections' => [
                [
                    'number' => '1.1',
                    'title' => 'Definizione di Patron',
                    'content' => 'Il **Patron** è un utente che, oltre a poter acquistare EGI come un Collezionista, sceglie di:
- Sostenere direttamente artisti specifici
- Contribuire a progetti EPP selezionati
- Partecipare attivamente alla governance della community
- Supportare lo sviluppo dell\'ecosistema FlorenceEGI'
                ],
                [
                    'number' => '1.2',
                    'title' => 'Definizioni Aggiuntive',
                    'content' => '**Patronage**: Sostegno finanziario continuativo o una-tantum a un Creatore o EPP.

**Badge Patron**: Riconoscimento visivo del Suo status di Patron, visibile sul profilo e nelle interazioni.

**Early Access**: Accesso anticipato a drop, funzionalità e contenuti esclusivi riservati ai Patron.

**Governance Token**: Diritto di voto su determinate decisioni della community (quando disponibile).'
                ],
                [
                    'number' => '1.3',
                    'title' => 'Qualificazione',
                    'content' => 'Per diventare Patron occorre:
- Essere un utente registrato con account verificato
- Aver effettuato almeno un contributo qualificante
- Accettare i presenti Termini specifici per Patron'
                ]
            ]
        ],

        [
            'number' => 2,
            'category' => 'platform',
            'title' => 'Modalità di Patronage',
            'subsections' => [
                [
                    'number' => '2.1',
                    'title' => 'Patronage di Artisti',
                    'content' => 'Lei può scegliere di sostenere uno o più Creatori attraverso:
- **Contributi una-tantum**: Donazioni singole all\'artista
- **Abbonamenti**: Sostegno mensile ricorrente
- **Acquisti diretti**: Con percentuale maggiorata al Creatore

I contributi vengono trasferiti direttamente al wallet del Creatore, senza intermediazione finanziaria di FlorenceEGI.'
                ],
                [
                    'number' => '2.2',
                    'title' => 'Patronage di Progetti EPP',
                    'content' => 'Lei può sostenere direttamente progetti ambientali verificati:
- Contributi diretti a EPP specifici
- Partecipazione a campagne di raccolta fondi
- Sponsorizzazione di milestone progettuali

Tutti i contributi EPP sono tracciati su blockchain per trasparenza.'
                ],
                [
                    'number' => '2.3',
                    'title' => 'Trasparenza dei Contributi',
                    'content' => 'FlorenceEGI garantisce totale trasparenza:
- Dashboard personale con storico contributi
- Report pubblici sull\'utilizzo dei fondi EPP
- Certificati di contributo scaricabili
- Tracciamento blockchain delle transazioni'
                ]
            ]
        ],

        [
            'number' => 3,
            'category' => 'platform',
            'title' => 'Benefici Patron',
            'subsections' => [
                [
                    'number' => '3.1',
                    'title' => 'Badge e Riconoscimento',
                    'content' => 'Come Patron Lei riceve:
- **Badge Patron** sul profilo pubblico
- Riconoscimento nelle pagine degli artisti supportati
- Menzione nei report EPP (se autorizzato)
- Status speciale nelle interazioni community'
                ],
                [
                    'number' => '3.2',
                    'title' => 'Accessi Esclusivi',
                    'content' => 'I Patron godono di:
- Early access a nuovi drop (24-48h di anticipo)
- Inviti a eventi esclusivi (virtuali e fisici)
- Contenuti dietro le quinte dagli artisti supportati
- Accesso prioritario a N.A.T.A.N. AI assistant'
                ],
                [
                    'number' => '3.3',
                    'title' => 'Programma Egili Potenziato',
                    'content' => 'I Patron beneficiano di condizioni privilegiate nel sistema Egili:
- Bonus Egili aggiuntivi sui contributi di patronage
- Moltiplicatore sulla premiazione per acquisti regolari
- Egili speciali per milestone di sostegno
- Vantaggi esclusivi riscattabili tramite Egili
- Accesso a pacchetti AI con condizioni dedicate

Gli Egili sono crediti di servizio interni, non trasferibili, non rimborsabili e privi di valore monetario autonomo. Si applicano integralmente le disposizioni sugli Egili contenute nei Termini per Collezionisti.'
                ],
                [
                    'number' => '3.4',
                    'title' => 'Governance (Quando Disponibile)',
                    'content' => 'I Patron potranno partecipare a:
- Votazioni su feature della piattaforma
- Selezione di artisti emergenti da promuovere
- Decisioni sulla roadmap EPP
- Proposte alla community

Le modalità saranno comunicate al lancio del sistema di governance.'
                ]
            ]
        ],

        [
            'number' => 4,
            'category' => 'platform',
            'title' => 'Interazione con Artisti',
            'subsections' => [
                [
                    'number' => '4.1',
                    'title' => 'Comunicazioni Dirette',
                    'content' => 'Il patronage può includere:
- Accesso a canali di comunicazione esclusivi
- Aggiornamenti diretti dal Creatore
- Possibilità di feedback e suggerimenti

Le modalità specifiche dipendono da ciò che ogni Creatore offre ai propri Patron.'
                ],
                [
                    'number' => '4.2',
                    'title' => 'Limiti delle Interazioni',
                    'content' => 'Il patronage **non conferisce**:
- Diritti di proprietà sulle opere
- Controllo creativo sull\'artista
- Garanzia di reciprocità nelle interazioni
- Diritti di sfruttamento commerciale'
                ],
                [
                    'number' => '4.3',
                    'title' => 'Condotta Appropriata',
                    'content' => 'Nelle interazioni con artisti, Lei si impegna a:
- Mantenere un comportamento rispettoso
- Non fare richieste inappropriate
- Rispettare la privacy del Creatore
- Non utilizzare il patronage come forma di pressione'
                ]
            ]
        ],

        [
            'number' => 5,
            'category' => 'rules',
            'title' => 'Natura dei Contributi',
            'subsections' => [
                [
                    'number' => '5.1',
                    'title' => 'Carattere Volontario',
                    'content' => 'I contributi di patronage sono:
- **Volontari**: Nessun obbligo di continuità
- **Non rimborsabili**: Salvo diversa indicazione specifica
- **Definitivi**: Una volta confermati su blockchain'
                ],
                [
                    'number' => '5.2',
                    'title' => 'Aspetti Fiscali',
                    'content' => 'I contributi di patronage:
- Non costituiscono donazione deducibile (salvo specifiche convenzioni EPP)
- Possono essere soggetti a tassazione nel Suo paese di residenza
- Non comportano emissione di fattura da FlorenceEGI (sono trasferimenti diretti)

Consulti il Suo commercialista per la corretta gestione fiscale.'
                ],
                [
                    'number' => '5.3',
                    'title' => 'Annullamento Abbonamenti',
                    'content' => 'Gli abbonamenti di patronage possono essere annullati in qualsiasi momento:
- L\'annullamento ha effetto dal ciclo successivo
- I contributi già effettuati non sono rimborsabili
- I benefici Patron permangono fino a scadenza del periodo pagato'
                ]
            ]
        ],

        [
            'number' => 6,
            'category' => 'rules',
            'title' => 'Diritti e Responsabilità',
            'subsections' => [
                [
                    'number' => '6.1',
                    'title' => 'Diritti del Patron',
                    'content' => 'Lei ha diritto a:
- Trasparenza sull\'utilizzo dei contributi
- Accesso ai benefici promessi
- Annullamento degli abbonamenti
- Export dei dati delle Sue attività (GDPR)'
                ],
                [
                    'number' => '6.2',
                    'title' => 'Limitazioni di Responsabilità',
                    'content' => 'FlorenceEGI non garantisce:
- Risultati specifici dai progetti EPP supportati
- Continuità dell\'attività degli artisti sostenuti
- Rendimenti finanziari dai contributi
- Raggiungimento degli obiettivi dichiarati'
                ],
                [
                    'number' => '6.3',
                    'title' => 'Perdita Status Patron',
                    'content' => 'Lo status di Patron può essere revocato per:
- Violazione dei termini di servizio
- Comportamento inappropriato verso artisti o community
- Attività fraudolente o sospette
- Mancato rinnovo degli abbonamenti (dove applicabile)'
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
                    'title' => 'Termini di Collezionista',
                    'content' => 'In aggiunta ai presenti termini, si applicano integralmente i Termini e Condizioni per Collezionisti per tutto ciò che concerne acquisti, wallet e funzionalità base della piattaforma.'
                ],
                [
                    'number' => '7.2',
                    'title' => 'Modifiche',
                    'content' => 'FlorenceEGI può modificare i benefici Patron con preavviso di 30 giorni. I contributi già effettuati mantengono i benefici della versione accettata.'
                ],
                [
                    'number' => '7.3',
                    'title' => 'Legge Applicabile',
                    'content' => 'Questo Contratto è regolato dalla legge italiana. Foro competente: Firenze, Italia. Resta salvo il foro del consumatore ove applicabile.'
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
                    'content' => 'Per controversie relative a contratti online, Lei può utilizzare la piattaforma ODR (Online Dispute Resolution) dell\'Unione Europea: https://ec.europa.eu/consumers/odr. FlorenceEGI si impegna a partecipare in buona fede a procedure di risoluzione alternativa delle controversie (ADR) presso organismi certificati ai sensi del D.Lgs. 130/2015.'
                ],
                [
                    'number' => '7.8',
                    'title' => 'Limitazione di Responsabilità',
                    'content' => 'Nei limiti consentiti dalla legge applicabile, la responsabilità complessiva di FlorenceEGI nei confronti dell\'utente per qualsiasi danno derivante dall\'utilizzo della Piattaforma non potrà eccedere l\'importo complessivo delle somme effettivamente pagate dall\'utente a FlorenceEGI nei 12 mesi precedenti l\'evento che ha dato origine alla responsabilità. FlorenceEGI non sarà in alcun caso responsabile per danni indiretti, incidentali, consequenziali o punitivi, inclusi perdita di profitti, perdita di dati o interruzione dell\'attività. Questa limitazione non si applica in caso di dolo o colpa grave di FlorenceEGI, né pregiudica i diritti irrinunciabili del consumatore ai sensi del Codice del Consumo.'
                ],
                [
                    'number' => '7.9',
                    'title' => 'Comunicazioni',
                    'content' => 'Le comunicazioni relative al presente Contratto (incluse modifiche dei termini, sospensioni e notifiche legali) saranno inviate tramite email all\'indirizzo associato all\'account dell\'utente e/o tramite notifica sulla Piattaforma. Le comunicazioni si considerano ricevute al momento della visualizzazione sulla Piattaforma o dopo 48 ore dall\'invio dell\'email, in base a quale evento si verifichi per primo.'
                ]
            ]
        ]
    ],

    'contact_info' => [
        'title' => 'Contatti',
        'content' => 'Per domande sul programma Patron: patrons@florenceegi.com
Per supporto generale: support@florenceegi.com
Per questioni legali: legal@florenceegi.com'
    ],

    'jurisdiction_specifics' => [
        'title' => 'Clausole Specifiche per Giurisdizione',
        'description' => 'Disposizioni aggiuntive applicabili in base alla giurisdizione',
        'clauses' => [
            'IT' => [
                'title' => 'Disposizioni per l\'Italia',
                'content' => 'Per residenti in Italia:
• I contributi di patronage non costituiscono erogazioni liberali deducibili fiscalmente salvo specifiche convenzioni
• Si applicano le tutele del Codice del Consumo per i servizi digitali
• Diritto di recesso: 14 giorni per abbonamenti, esclusi contenuti digitali già fruiti'
            ]
        ]
    ]
];
