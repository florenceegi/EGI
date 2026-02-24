<?php

/**
 * @Oracode Legal Content: Allegato B — Guida al Sistema Egili
 * 🎯 Purpose: Complete guide to Egili credits system
 * 🛡️ Security: MiCA compliance, non-token classification, consumer transparency
 *
 * @version 3.0.0
 * @effective_date 2026-02-24
 * @locale it
 *
 * Referenziato da:
 * - creator.php Art. 6.2, 11
 * - collector.php Art. 6
 * - patron.php Art. 3.3
 * - trader_pro.php Art. 3.4
 *
 * Fonte dati verificata:
 * - config/ai-credits.php
 * - config/egili.php
 * - creator.php Art. 6, collector.php Art. 6
 *
 * @package FlorenceEGI\Legal
 * @author Padmin D. Curtis (AI Partner OS3.0-Compliant) for Fabio Cherici
 */

return [
    'metadata' => [
        'title' => 'Allegato B — Guida al Sistema Egili',
        'version' => '3.0.0',
        'effective_date' => '2026-02-24',
        'document_type' => 'Allegato ai Termini e Condizioni',
        'referenced_by' => ['creator.php', 'collector.php', 'patron.php', 'trader_pro.php'],
    ],

    'introduzione' => [
        'title' => 'Introduzione',
        'content' => 'Il presente Allegato descrive il funzionamento del sistema Egili, le modalità di ottenimento, di utilizzo e le regole applicabili. Gli Egili sono parte integrante dell\'ecosistema FlorenceEGI e sono disciplinati dai Termini e Condizioni di ciascun tipo di utente.',
    ],

    'sections' => [
        [
            'number' => 1,
            'title' => 'Definizione e Natura Giuridica',
            'content' => 'Gli **Egili** sono unità di credito interne alla Piattaforma FlorenceEGI, concepiti come:

**Cosa sono**:
- Crediti di servizio prepagati per l\'utilizzo dei servizi AI della Piattaforma
- Unità di premiazione per azioni meritevoli all\'interno dell\'ecosistema
- Strumento per ottenere sconti e riduzioni sulle commissioni di piattaforma

**Cosa NON sono**:
- **Non sono valuta** (legale o virtuale)
- **Non sono token finanziari** né crypto-asset ai sensi del Reg. (UE) 2023/1114 (MiCA)
- **Non sono titoli negoziabili** né strumenti finanziari
- **Non hanno valore monetario autonomo**
- **Non sono trasferibili** tra utenti
- **Non sono scambiabili** su mercati esterni
- **Non sono rimborsabili** né convertibili in denaro
- **Non sono utilizzabili** come mezzo di pagamento per l\'acquisto o la vendita di EGI

**Classificazione normativa**: Gli Egili sono assimilabili a crediti di servizio prepagati (analoghi a crediti SaaS come AWS Credits, crediti OpenAI, punti fedeltà non monetizzabili), fuori dall\'ambito di applicazione del Regolamento MiCA e della normativa sui servizi di pagamento (PSD2).',
        ],
        [
            'number' => 2,
            'title' => 'Come Ottenere Egili',
            'subsections' => [
                [
                    'number' => '2.1',
                    'title' => 'Acquisto di Pacchetti Servizi AI (FIAT)',
                    'content' => 'L\'utente può acquistare pacchetti di servizi AI pagando in valuta FIAT (EUR) tramite i PSP integrati (Stripe, PayPal). L\'acquisto accredita Egili sul saldo dell\'utente.

**Pacchetti disponibili**:

| Pacchetto | Egili | Prezzo (EUR) | Sconto |
|---|---|---|---|
| **Starter** | 1.000 | €10,00 | — |
| **Professional** | 5.000 | €45,00 | 10% |
| **Business** | 10.000 | €85,00 | 15% |
| **Enterprise** | 50.000 | €400,00 | 20% |

Il tasso di conversione base è: **1 EUR = 100 Egili** (€0,01 per Egili).

I pacchetti e i prezzi possono essere aggiornati periodicamente. I prezzi vigenti al momento dell\'acquisto sono sempre visibili nella sezione dedicata della dashboard.',
                ],
                [
                    'number' => '2.2',
                    'title' => 'Premiazione per Merito (Reward)',
                    'content' => 'Gli utenti possono guadagnare Egili attraverso azioni meritevoli all\'interno dell\'ecosistema, tra cui:

- **Acquisti sulla Piattaforma**: Bonus Egili per ogni acquisto di EGI
- **Referral**: Egili per ogni nuovo utente invitato che completa la registrazione
- **Partecipazione alla community**: Contributi significativi, feedback, segnalazioni
- **Eventi e promozioni**: Campagne periodiche con premiazione Egili
- **Milestone**: Raggiungimento di traguardi (es. primo acquisto, 10° acquisto, primo anno di attività)

I criteri e gli importi di premiazione sono definiti da FlorenceEGI e possono variare. Le condizioni vigenti sono consultabili nella dashboard dell\'utente.',
                ],
                [
                    'number' => '2.3',
                    'title' => 'Egili Iniziali (Free Tier)',
                    'content' => 'Alla registrazione, ogni utente riceve un accredito iniziale di **1.000 Egili** (equivalente a €10 di servizi AI) per provare le funzionalità della Piattaforma. Inoltre, ogni mese viene accreditato un bonus di **500 Egili** per gli utenti con piano gratuito.

Questi Egili hanno le stesse condizioni di utilizzo degli Egili acquistati.',
                ],
            ],
        ],
        [
            'number' => 3,
            'title' => 'Come Utilizzare gli Egili',
            'subsections' => [
                [
                    'number' => '3.1',
                    'title' => 'Servizi AI (N.A.T.A.N.)',
                    'content' => 'L\'utilizzo principale degli Egili è il consumo dei servizi AI della Piattaforma:

- **Assistenza N.A.T.A.N.**: Ogni interazione con l\'assistente AI consuma Egili in base alla complessità della richiesta
- **Analisi opere**: Analisi approfondita delle caratteristiche e del posizionamento di mercato delle opere
- **Suggerimenti personalizzati**: Raccomandazioni basate sulle preferenze e sulla collezione

Il costo in Egili per ogni servizio è visibile prima dell\'utilizzo. L\'utilizzo è definitivo e comporta la rimozione irrevocabile degli Egili dal saldo.',
                ],
                [
                    'number' => '3.2',
                    'title' => 'Riduzione e Azzeramento Commissioni',
                    'content' => 'Gli Egili possono essere utilizzati per ridurre o azzerare le commissioni di piattaforma sulle transazioni:

- Riduzione parziale delle fee di servizio sul Mint
- Riduzione parziale delle fee di servizio sul Rebind
- Le modalità e i tassi di conversione sono definiti nella sezione Impostazioni della dashboard

**Nota**: La riduzione si applica esclusivamente alla quota di commissione della Piattaforma (Natan), non alle royalties del Creator, ai contributi EPP o a Frangette.',
                ],
                [
                    'number' => '3.3',
                    'title' => 'Sconti e Vantaggi',
                    'content' => 'Gli Egili possono essere riscattati per vantaggi sulla Piattaforma:

- Accesso anticipato a drop esclusivi
- Sconti sulle commissioni di acquisto
- Vantaggi dedicati in base al tipo di utente (vedi condizioni specifiche per Patron, Trader Pro)
- Ulteriori benefici periodicamente annunciati

I vantaggi disponibili e i costi in Egili sono consultabili nella sezione dedicata della dashboard.',
                ],
            ],
        ],
        [
            'number' => 4,
            'title' => 'Condizioni per Tipo di Utente',
            'content' => '| Tipo Utente | Egili Iniziali | Bonus Mensile | Moltiplicatore Reward | Condizioni Speciali |
|---|---|---|---|---|
| **Creator** | 1.000 | 500 | 1x | Egili per servizi AI di analisi opere |
| **Collector** | 1.000 | 500 | 1x | — |
| **Patron** | 1.000 | 500 | Bonus maggiorato | Bonus su patronage, milestone, pacchetti dedicati |
| **Trader Pro** | 1.000 | 500 | 1,5x | Bonus volume trimestrale, pacchetti Pro |
| **Company** | Variabile | Variabile | Variabile | Condizioni Enterprise negoziabili |

Le condizioni specifiche per ciascun tipo di utente sono dettagliate nei rispettivi Termini e Condizioni.',
        ],
        [
            'number' => 5,
            'title' => 'Regole e Limitazioni',
            'content' => '**Utilizzo**:
• L\'utilizzo degli Egili è **definitivo e irrevocabile**: una volta consumati, non possono essere recuperati
• Al termine degli Egili disponibili, i servizi che ne richiedono il consumo non saranno accessibili fino all\'ottenimento di nuovi Egili

**Trasferibilità**:
• Gli Egili sono **strettamente personali** e associati all\'account dell\'utente
• **Non possono** essere trasferiti, ceduti, venduti o scambiati con altri utenti
• **Non possono** essere utilizzati su piattaforme terze

**Scadenza**:
• Gli Egili acquistati tramite pacchetti AI non hanno scadenza
• Gli Egili ottenuti tramite premiazione possono avere scadenza (comunicata al momento dell\'accredito)
• In caso di cancellazione dell\'account, gli Egili residui vanno persi

**Rimborso**:
• Gli Egili **non sono rimborsabili** in nessun caso
• In caso di malfunzionamento tecnico di un servizio AI, gli Egili consumati possono essere riaccreditati automaticamente sul saldo dell\'utente',
        ],
        [
            'number' => 6,
            'title' => 'Modifiche al Sistema',
            'content' => 'FlorenceEGI si riserva il diritto di modificare:
- I criteri di attribuzione degli Egili
- I costi di consumo dei servizi
- I pacchetti disponibili e i relativi prezzi
- Le modalità di utilizzo per sconti e riduzioni

Tali modifiche saranno comunicate con un preavviso di **30 giorni** tramite email e notifica sulla Piattaforma. Le modifiche **non avranno effetto retroattivo** sugli Egili già accumulati: gli Egili già nel saldo dell\'utente al momento della modifica manterranno il proprio valore di utilizzo.',
        ],
        [
            'number' => 7,
            'title' => 'Assistenza e Saldo',
            'content' => 'L\'utente può verificare in qualsiasi momento:
- Il **saldo Egili** corrente nella propria dashboard
- Lo **storico** di acquisti, premiazioni e utilizzi
- I **costi** dei servizi prima di utilizzarli

Per problemi relativi al saldo Egili o ai servizi AI: support@florenceegi.com',
        ],
    ],
];
