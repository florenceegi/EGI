<?php

/**
 * @Oracode Legal Content: Allegato B — Sistema Egili e Pacchetti Servizi AI
 * 🎯 Purpose: Definizione del sistema Egili come contatore interno di consumo
 *             dei Pacchetti Servizi AI acquistati in valuta FIAT
 * 🛡️ Security: MiCA compliance — Egili NON sono acquistabili, NON sono token,
 *              NON sono asset. Il prodotto venduto sono i Pacchetti Servizi AI.
 *
 * @version 3.1.0
 * @effective_date 2026-02-25
 * @locale it
 * @supersedes 3.0.0 (eliminato modello "acquisto Egili" — illegale)
 *
 * Referenziato da:
 * - creator.php Art. 6.2, 11
 * - collector.php Art. 6
 * - patron.php Art. 3.3
 * - trader_pro.php Art. 3.4
 *
 * @package FlorenceEGI\Legal
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 */

return [
    'metadata' => [
        'title' => 'Allegato B — Sistema Egili e Pacchetti Servizi AI',
        'version' => '3.1.0',
        'effective_date' => '2026-02-24',
        'document_type' => 'Allegato ai Termini e Condizioni',
        'referenced_by' => ['creator.php', 'collector.php', 'patron.php', 'trader_pro.php'],
        'supersedes' => '3.0.0 — eliminato modello "acquisto Egili" (incompatibile con MiCA)',
    ],

    'introduzione' => [
        'title' => 'Introduzione',
        'content' => 'Il presente Allegato descrive il funzionamento del sistema Egili, la natura dei Pacchetti Servizi AI acquistabili, le modalità di accredito e utilizzo degli Egili e le regole applicabili. Gli Egili sono un contatore interno di consumo servizi AI, disciplinato dai Termini e Condizioni di ciascun tipo di utente.',
    ],

    'sections' => [
        [
            'number' => 1,
            'title' => 'Natura degli Egili — Definizione Giuridica',
            'content' => 'Gli **Egili** sono esclusivamente un **contatore interno di consumo** dei servizi AI della Piattaforma FlorenceEGI.

**🚫 Gli Egili NON sono un prodotto acquistabile direttamente.**
Il prodotto venduto dalla Piattaforma sono i **Pacchetti Servizi AI** in valuta FIAT.
Gli Egili vengono accreditati automaticamente come conseguenza dell\'acquisto di un Pacchetto Servizi AI.

**Cosa sono**:
- Un contatore di consumo per i servizi AI (N.A.T.A.N.) della Piattaforma
- Uno strumento di premiazione per azioni meritevoli nell\'ecosistema
- Uno strumento per ottenere sconti e riduzioni sulle commissioni di piattaforma

**Cosa NON sono**:
- **Non sono valuta** (legale o virtuale)
- **Non sono token finanziari** né crypto-asset ai sensi del Reg. (UE) 2023/1114 (MiCA)
- **Non sono titoli negoziabili** né strumenti finanziari
- **Non hanno valore monetario autonomo**
- **Non sono un prodotto in vendita** — non si "comprano Egili"
- **Non sono trasferibili** tra utenti
- **Non sono convertibili** in denaro né rimborsabili
- **Non sono utilizzabili** come mezzo di pagamento per l\'acquisto o la vendita di EGI

**Classificazione normativa**: Gli Egili sono assimilabili a unità di misura interne del consumo di un servizio prepagato (analoghi a "crediti API" come AWS Credits, crediti OpenAI), fuori dall\'ambito di applicazione del Regolamento MiCA e della normativa PSD2.',
        ],
        [
            'number' => 2,
            'title' => 'Pacchetti Servizi AI — Il Prodotto Acquistabile',
            'subsections' => [
                [
                    'number' => '2.1',
                    'title' => 'Cosa si Acquista',
                    'content' => 'L\'utente acquista **Pacchetti Servizi AI**: diritti di accesso prepagato ai servizi AI della Piattaforma (N.A.T.A.N. e relativi moduli).

A fronte dell\'acquisto di un Pacchetto Servizi AI, la Piattaforma accredita automaticamente un numero di **Egili** sul saldo dell\'utente. Il rapporto tra l\'importo pagato e gli Egili accreditati è **pubblicato nella sezione dedicata della Piattaforma** ed è soggetto ad aggiornamento con preavviso di 30 giorni.

I pacchetti disponibili, le denominazioni e i prezzi vigenti sono consultabili nella dashboard dell\'utente. I prezzi sono espressi in EUR (IVA inclusa ove applicabile).',
                ],
                [
                    'number' => '2.2',
                    'title' => 'Modalità di Pagamento',
                    'content' => 'I Pacchetti Servizi AI sono acquistabili esclusivamente tramite:
- **Stripe** (carte di credito/debito: Visa, Mastercard, American Express)
- **PayPal**

**Non sono accettati** pagamenti in criptovalute, token digitali o asset blockchain di nessun tipo.',
                ],
                [
                    'number' => '2.3',
                    'title' => 'Egili di Benvenuto (Free Tier)',
                    'content' => 'Alla registrazione, ogni nuovo utente riceve un accredito iniziale di Egili per provare i servizi AI della Piattaforma senza necessità di acquisto. L\'importo del credito iniziale è indicato nella sezione dedicata della dashboard.

Questi Egili sono soggetti alle stesse regole di utilizzo degli Egili accreditati tramite pacchetti.',
                ],
                [
                    'number' => '2.4',
                    'title' => 'Egili Reward (Premio)',
                    'content' => 'Gli utenti possono ricevere Egili aggiuntivi come riconoscimento per azioni meritevoli nell\'ecosistema, tra cui:
- Acquisti di EGI sulla Piattaforma
- Referral di nuovi utenti
- Milestone e traguardi di partecipazione
- Campagne e promozioni periodiche

I criteri e gli importi di premiazione sono definiti da FlorenceEGI, sono consultabili nella dashboard e possono variare. Gli Egili Reward possono avere scadenza (comunicata al momento dell\'accredito).',
                ],
            ],
        ],
        [
            'number' => 3,
            'title' => 'Utilizzo degli Egili',
            'subsections' => [
                [
                    'number' => '3.1',
                    'title' => 'Servizi AI (N.A.T.A.N.)',
                    'content' => 'L\'utilizzo primario degli Egili è il consumo dei servizi AI della Piattaforma:
- **Assistente N.A.T.A.N.**: ogni interazione consuma Egili in base al tipo e complessità della richiesta
- **Analisi opere**: analisi approfondita di caratteristiche e posizionamento di mercato
- **Suggerimenti personalizzati**: raccomandazioni basate su collezione e preferenze

Il costo in Egili di ogni servizio è visibile prima dell\'utilizzo. Il consumo è **definitivo e irrevocabile**: gli Egili consumati non sono recuperabili salvo malfunzionamento tecnico certificato.',
                ],
                [
                    'number' => '3.2',
                    'title' => 'Riduzione Commissioni di Piattaforma',
                    'content' => 'Gli Egili possono essere utilizzati per ridurre le commissioni di piattaforma sulle transazioni (Mint, Rebind). Le modalità e i rapporti di conversione sono definiti nella sezione Impostazioni della dashboard.

**Nota**: La riduzione si applica esclusivamente alla quota di commissione della Piattaforma (componente Natan), non alle royalties del Creator, ai contributi EPP né a Frangette.',
                ],
                [
                    'number' => '3.3',
                    'title' => 'Sconti e Vantaggi',
                    'content' => 'Gli Egili possono essere riscattati per ulteriori vantaggi sulla Piattaforma (accesso anticipato a drop esclusivi, sconti, benefici riservati per tipo di utente). I vantaggi disponibili e i relativi costi in Egili sono indicati nella sezione dedicata della dashboard.',
                ],
            ],
        ],
        [
            'number' => 4,
            'title' => 'Condizioni per Tipo di Utente',
            'content' => 'Le condizioni di accredito iniziale, i bonus mensili e i moltiplicatori reward variano per tipo di utente e sono pubblicati nella sezione dedicata della dashboard. Le condizioni specifiche sono dettagliate nei rispettivi Termini e Condizioni di ciascun tipo di utente (Creator, Collector, Patron, Trader Pro, Company).',
        ],
        [
            'number' => 5,
            'title' => 'Regole e Limitazioni',
            'content' => '**Consumo**:
• Il consumo degli Egili è **definitivo e irrevocabile**
• Quando il saldo è zero, i servizi AI non sono accessibili fino all\'acquisto di un nuovo Pacchetto Servizi AI

**Personalità e non-trasferibilità**:
• Gli Egili sono **strettamente personali** e legati all\'account dell\'utente
• Non possono essere trasferiti, ceduti, venduti o scambiati con altri utenti
• Non sono utilizzabili su piattaforme terze

**Scadenza**:
• Gli Egili accreditati tramite Pacchetti Servizi AI **non hanno scadenza**
• Gli Egili Reward possono avere scadenza, comunicata al momento dell\'accredito
• In caso di cancellazione dell\'account, il saldo Egili residuo è azzerato senza rimborso

**Non rimborsabilità**:
• Gli Egili non sono rimborsabili in alcun caso
• In caso di malfunzionamento tecnico certificato di un servizio AI, gli Egili consumati possono essere riaccreditati automaticamente',
        ],
        [
            'number' => 6,
            'title' => 'Modifiche al Sistema',
            'content' => 'FlorenceEGI può modificare i rapporti di accredito, i costi dei servizi, i pacchetti disponibili e le modalità di utilizzo per sconti e commissioni. Tali modifiche saranno comunicate con preavviso di **30 giorni** via email e notifica sulla Piattaforma. Le modifiche non hanno effetto retroattivo: gli Egili già nel saldo al momento della modifica mantengono il proprio potere di utilizzo.',
        ],
        [
            'number' => 7,
            'title' => 'Trasparenza e Assistenza',
            'content' => 'L\'utente può verificare in qualsiasi momento nella propria dashboard:
- Il saldo Egili corrente
- Lo storico degli accrediti (pacchetti, reward, benvenuto) e dei consumi
- Il costo in Egili dei servizi prima dell\'utilizzo
- I rapporti di accredito vigenti (Egili per Pacchetto AI)

Per assistenza: support@florenceegi.com',
        ],
    ],
];