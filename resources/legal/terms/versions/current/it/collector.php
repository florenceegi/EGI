<?php

/**
 * @Oracode Legal Content: Termini e Condizioni per Utenti Collezionisti
 * 🎯 Purpose: Terms for Collector user type - NFT buyers and collectors
 * 🛡️ Security: Consumer protection, wallet security, GDPR compliance
 *
 * @version 3.0.0 (Riforma Egili: da utility token a crediti servizio AI + reward interni)
 * @effective_date 2026-02-24
 * @user_type collector
 * @locale it
 *
 * @package FlorenceEGI\Legal
 * @author Padmin D. Curtis (AI Partner OS3.0-Compliant) for Fabio Cherici
 */

return [
    'metadata' => [
        'title' => 'Termini e Condizioni per Collezionisti',
        'version' => '3.0.0',
        'effective_date' => '2026-02-24',
        'document_type' => 'Contratto per Utenti Collezionisti',
        'target_audience' => 'Utenti qualificati come "Collezionista" (Collector) della Piattaforma FlorenceEGI',
        'summary_of_changes' => 'v3.0.0: Riforma sistema Egili — da utility token a crediti servizio AI prepagati e sistema di premiazione interna.',
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

**Egili**: Unità di credito interna alla Piattaforma, non trasferibile, non scambiabile esternamente e priva di valore monetario autonomo. Possono essere ottenuti tramite l\'acquisto di pacchetti di servizi AI (in valuta FIAT) o guadagnati attraverso azioni meritevoli. Utilizzabili per servizi AI, riduzione commissioni e sconti sulla Piattaforma. Non sono rimborsabili, non convertibili in denaro, e non costituiscono valuta, token finanziario o asset negoziabile.

**EPP (Environmental Protection Projects)**: Progetti ambientali verificati a cui viene destinato il 20% di ogni transazione.

**N.A.T.A.N.** (Neural Adaptive Technology for Art Navigation): Sistema AI integrato che analizza le caratteristiche delle opere d\'arte per fornire supporto informativo sulla Piattaforma. Non effettua profilazione personale e non prende decisioni autonome.'
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
                ],
                [
                    'number' => '3.5',
                    'title' => 'Metodi di Pagamento Accettati',
                    'content' => 'Per l\'acquisto di EGI sono disponibili i seguenti metodi di pagamento:

**A) Pagamento in valuta FIAT (EUR)** — Metodo principale
Pagamento in Euro tramite i fornitori di servizi di pagamento (\"**PSP**\") integrati nella Piattaforma: attualmente **Stripe** e **PayPal**. Utilizzando questi metodi, Lei accetta anche i termini di servizio del PSP selezionato. I dati di pagamento sono trattati esclusivamente dal PSP e non sono accessibili a FlorenceEGI.

**B) Pagamento in ALGO (Algorand)** — Disponibile quando attivato dal Creator
Per gli EGI il cui Creator ha abilitato il pagamento in ALGO, Lei può pagare direttamente dal Suo wallet Algorand. Il trasferimento avviene on-chain con tracciabilità completa. FlorenceEGI non custodisce né intermedia fondi in criptovaluta.

**C) Pagamento in criptovaluta tramite PSP Partner (CASP)** — Disponibile quando attivato
Pagamento in criptovaluta (stablecoin o altre crypto supportate) tramite un Crypto-Asset Service Provider (\"**CASP**\") partner autorizzato. Il pagamento è gestito interamente dal CASP, con cui Lei ha un rapporto contrattuale diretto. FlorenceEGI non esegue conversioni FIAT/crypto e non custodisce fondi crypto.

La disponibilità di ciascun metodo è indicata nella pagina di acquisto di ogni EGI.'
                ],
                [
                    'number' => '3.6',
                    'title' => 'Politica di Rimborso',
                    'content' => 'Gli acquisti di EGI costituiscono acquisti di **beni digitali** ai sensi del Codice del Consumo. Una volta completato il trasferimento su blockchain:

• L\'acquisto è **definitivo e non rimborsabile**
• Il diritto di recesso si estingue con il completamento del trasferimento, ai sensi dell\'art. 59 lett. o) del D.Lgs. 206/2005, previo Suo esplicito consenso
• In caso di mancato completamento del trasferimento su blockchain per cause tecniche, il pagamento sarà integralmente rimborsato tramite il PSP utilizzato

Per contestazioni relative a pagamenti effettuati tramite PSP, si applicano le procedure di dispute del PSP stesso (es. chargeback Stripe/PayPal). FlorenceEGI collaborerà fornendo documentazione sulla legittimità della transazione.'
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
                    'content' => 'Il Creatore mantiene sempre i diritti morali sull\'opera ai sensi dell\'Art. 20 della L. 633/1941, incluso il diritto di paternità e integrità dell\'opera. Tali diritti sono inalienabili e irrinunciabili.'
                ],
                [
                    'number' => '4.4',
                    'title' => 'Restrizioni d\'Uso Dettagliate',
                    'content' => 'L\'acquisto di un EGI **non trasferisce il copyright** né alcun diritto patrimoniale d\'autore (L. 633/1941 Artt. 12-19). Lei acquista il token digitale e una licenza d\'uso limitata.

**Lei può**:
• Esporre privatamente l\'opera e documentarla (fotografie personali)
• Esibire pubblicamente l\'opera senza scopo di lucro (con attribuzione al Creatore)
• Condividere l\'opera sui social media con attribuzione
• Rivendere l\'EGI sulla Piattaforma o su marketplace compatibili
• Donare o lasciare in eredità l\'EGI

**Lei NON può**, senza esplicita autorizzazione scritta del Creatore:
• Riprodurre, stampare o creare copie dell\'opera per fini commerciali
• Creare opere derivate o merchandise basati sull\'opera
• Utilizzare l\'opera per scopi commerciali (pubblicità, marketing, promozioni)
• Rimuovere o alterare l\'attribuzione dell\'autore
• Sub-licenziare i diritti sull\'opera a terzi

Per l\'uso commerciale dei diritti, è necessario negoziare una licenza separata direttamente con il Creatore.'
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
                    'title' => 'Configurazione Account di Pagamento per la Vendita',
                    'content' => 'Per vendere EGI sul mercato secondario della Piattaforma, è necessario configurare un account di pagamento presso il PSP supportato.

**Stripe Connect**: La Piattaforma utilizza Stripe Connect Express. Per abilitare la vendita, Lei deve:
• Completare l\'onboarding Stripe Connect dalla sezione Impostazioni Pagamenti della dashboard
• Fornire a Stripe i dati identificativi e bancari richiesti
• Accettare i Termini di Servizio di Stripe (Connected Account Agreement)

Senza un account PSP attivo e verificato, la funzionalità di vendita non sarà disponibile. FlorenceEGI non ha accesso ai dati bancari forniti a Stripe.'
                ],
                [
                    'number' => '5.3',
                    'title' => 'Processo di Rivendita (Rebind)',
                    'content' => 'La rivendita di un EGI sulla Piattaforma (\"**Rebind**\") segue il seguente processo:

1. Lei imposta il prezzo di vendita e abilita la rivendita dell\'EGI dalla propria dashboard
2. Il sistema verifica che Lei abbia un account PSP attivo e verificato
3. L\'acquirente procede al pagamento tramite i metodi disponibili (Art. 3.5)
4. Il PSP incassa il pagamento e lo ripartisce automaticamente tra:
   - **Venditore (Lei)**: Importo netto dopo le detrazioni
   - **Creatore originale**: Royalty del 4,5% sul prezzo di vendita
   - **EPP**: Contributo ambientale
   - **Piattaforma**: Commissione di servizio
5. La proprietà dell\'EGI viene trasferita all\'acquirente su blockchain'
                ],
                [
                    'number' => '5.4',
                    'title' => 'Royalties al Creatore',
                    'content' => 'Ogni rivendita comporta il pagamento automatico di una royalty contrattuale del **4,5%** al Creatore originale, gestita automaticamente dalla Piattaforma. Questa royalty è:
- Applicata su **ogni** rivendita, indipendentemente dal prezzo
- **Separata e aggiuntiva** rispetto al Diritto di Seguito legale (L. 633/1941 Art. 19bis), applicabile per vendite ≥ €3.000 tramite professionisti
- Visibile nei metadati dell\'EGI e nel riepilogo pre-acquisto'
                ],
                [
                    'number' => '5.5',
                    'title' => 'Contributo EPP su Rivendita',
                    'content' => 'Anche le rivendite contribuiscono ai progetti EPP, garantendo un impatto ambientale continuo per ogni transazione.'
                ],
                [
                    'number' => '5.6',
                    'title' => 'Payout dei Ricavi di Vendita',
                    'content' => 'I ricavi netti dalle vendite dei Suoi EGI vengono trasferiti sul conto bancario collegato al Suo account Stripe Connect:

• **Tempistiche**: I payout sono erogati dal PSP secondo il proprio calendario (generalmente 2-7 giorni lavorativi). Le tempistiche possono variare.
• **Valuta**: EUR.
• **Trattenute**: Dal ricavo lordo sono automaticamente detratte le royalties al Creatore, il contributo EPP e la commissione di piattaforma.

FlorenceEGI non è responsabile per ritardi o mancati accrediti imputabili al PSP o a errori nei dati bancari.'
                ],
                [
                    'number' => '5.7',
                    'title' => 'Chargeback su Vendite Secondarie',
                    'content' => 'In caso di chargeback (contestazione del pagamento da parte dell\'acquirente presso il PSP), il PSP potrebbe stornare l\'importo già accreditato al venditore. FlorenceEGI:
- Non è responsabile per perdite derivanti da chargeback
- Collaborerà per fornire documentazione a supporto della legittimità della transazione
- I costi di gestione del chargeback sono a carico della parte cui viene attribuito secondo le policy del PSP'
                ]
            ]
        ],

        [
            'number' => 6,
            'category' => 'platform',
            'title' => 'Sistema Egili',
            'subsections' => [
                [
                    'number' => '6.1',
                    'title' => 'Natura degli Egili',
                    'content' => 'Gli Egili sono crediti di servizio interni e unità di premiazione della Piattaforma:
- **Non trasferibili** tra utenti
- **Non scambiabili** su mercati esterni
- **Non rappresentano** valuta, token finanziario, titolo negoziabile o credito
- **Non rimborsabili** né convertibili in denaro
- **Non utilizzabili** come mezzo di pagamento per acquisto o vendita di EGI'
                ],
                [
                    'number' => '6.2',
                    'title' => 'Come Ottenere Egili',
                    'content' => 'I Collezionisti possono ottenere Egili attraverso:
- **Acquisto di pacchetti AI**: Pagamento in valuta FIAT (EUR) tramite i metodi supportati, che accredita Egili sul proprio saldo
- **Premiazione per merito**: Acquisti sulla Piattaforma, referral di nuovi utenti, partecipazione a eventi della community, promozioni e attività speciali definite periodicamente'
                ],
                [
                    'number' => '6.3',
                    'title' => 'Utilizzo degli Egili',
                    'content' => 'Gli Egili possono essere utilizzati per:
- Servizi AI della Piattaforma (es. assistenza N.A.T.A.N.)
- Sconti e riduzioni sulle commissioni di acquisto
- Accesso anticipato a drop esclusivi
- Vantaggi definiti nella sezione dedicata della dashboard

L\'utilizzo è definitivo e comporta la rimozione irrevocabile dal saldo. Al termine degli Egili disponibili, i servizi che ne richiedono il consumo non saranno accessibili fino all\'ottenimento di nuovi Egili.'
                ],
                [
                    'number' => '6.4',
                    'title' => 'Modifiche al Sistema',
                    'content' => 'FlorenceEGI si riserva il diritto di modificare i criteri di attribuzione, i costi di consumo e le modalità di utilizzo degli Egili, con un preavviso di 30 giorni. Tali modifiche non avranno effetto retroattivo sugli Egili già accumulati.'
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
                    'content' => 'La Piattaforma integra N.A.T.A.N. (Neural Adaptive Technology for Art Navigation), un sistema AI basato su Claude di Anthropic, che può aiutarLa a:
- Esplorare e navigare la Piattaforma
- Comprendere le caratteristiche delle opere (titoli, descrizioni, categorie, traits) e il loro posizionamento sul mercato
- Ricevere informazioni sui Creator e sui progetti EPP
- Ricevere assistenza generale sull\'utilizzo della Piattaforma

N.A.T.A.N. analizza le **caratteristiche delle opere d\'arte** per fornire suggerimenti informativi. **Non effettua profilazione personale** dell\'utente e **non prende decisioni autonome**: ogni suggerimento è puramente informativo e la decisione finale resta sempre a Lei.'
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
                    'content' => 'Le risposte di N.A.T.A.N. sono generate algoritmicamente e possono contenere imprecisioni. Non costituiscono:
- Consulenza legale o fiscale
- Consulenza finanziaria o di investimento
- Valutazione certificata delle opere d\'arte
- Garanzia di accuratezza assoluta

Nessuna decisione relativa al Suo account, ai pagamenti o alla moderazione dei contenuti viene presa in modo automatizzato senza intervento umano. Per decisioni importanti, consulti sempre un professionista qualificato.'
                ],
                [
                    'number' => '7.4',
                    'title' => 'Disclosure AI (Reg. UE 2024/1689)',
                    'content' => 'In conformità con il Regolamento (UE) 2024/1689 (AI Act), FlorenceEGI comunica che:
- N.A.T.A.N. è un sistema di intelligenza artificiale classificato come sistema a **rischio limitato** ai sensi del Regolamento
- Il sistema analizza esclusivamente **metadati pubblici delle opere d\'arte** (titoli, descrizioni, categorie, caratteristiche), non dati personali degli utenti
- I metadati delle opere vengono elaborati tramite API di fornitori terzi (Anthropic) con DPA (Data Processing Agreement) conforme al GDPR. I dati inviati non includono informazioni personali identificative
- Le risposte di N.A.T.A.N. sono generate algoritmicamente e possono contenere imprecisioni
- L\'utente è sempre informato quando interagisce con un sistema di intelligenza artificiale'
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
                    'content' => 'Per controversie relative a contratti online, Lei può utilizzare la piattaforma ODR (Online Dispute Resolution) dell\'Unione Europea: https://ec.europa.eu/consumers/odr. FlorenceEGI si impegna a partecipare in buona fede a procedure di risoluzione alternativa delle controversie (ADR) presso organismi certificati ai sensi del D.Lgs. 130/2015.'
                ],
                [
                    'number' => '10.5',
                    'title' => 'Separabilità',
                    'content' => 'Qualora una o più disposizioni del presente Contratto siano dichiarate nulle, invalide o inapplicabili da un tribunale competente, le restanti disposizioni rimarranno pienamente valide ed efficaci. Le parti si impegnano a sostituire la clausola invalida con una disposizione valida che si avvicini il più possibile all\'intento economico e giuridico originario.'
                ],
                [
                    'number' => '10.6',
                    'title' => 'Forza Maggiore',
                    'content' => 'FlorenceEGI non sarà responsabile per inadempimenti o ritardi nell\'esecuzione delle proprie obbligazioni causati da eventi di forza maggiore ai sensi dell\'art. 1218 del Codice Civile, inclusi a titolo esemplificativo: interruzioni della rete blockchain Algorand, indisponibilità dei servizi dei PSP (Stripe, PayPal), guasti dell\'infrastruttura cloud (AWS), attacchi informatici, calamità naturali, provvedimenti delle autorità, pandemie o altri eventi al di fuori del ragionevole controllo di FlorenceEGI. In caso di forza maggiore, gli obblighi saranno sospesi per la durata dell\'evento. FlorenceEGI informerà tempestivamente gli utenti tramite i canali disponibili.'
                ],
                [
                    'number' => '10.7',
                    'title' => 'Cessione del Contratto',
                    'content' => 'L\'utente non può cedere o trasferire i propri diritti e obblighi derivanti dal presente Contratto senza il previo consenso scritto di FlorenceEGI. FlorenceEGI può cedere il Contratto a terzi in caso di fusione, acquisizione o trasferimento di ramo d\'azienda, previo avviso all\'utente con almeno 30 giorni di anticipo.'
                ],
                [
                    'number' => '10.8',
                    'title' => 'Segnalazione Contenuti e Reclami (DSA)',
                    'content' => 'In conformità con il Regolamento (UE) 2022/2065 (Digital Services Act), FlorenceEGI mette a disposizione un meccanismo per la segnalazione di contenuti ritenuti illeciti e per la presentazione di reclami contro decisioni di moderazione. Le segnalazioni possono essere inviate tramite l\'apposita funzionalità sulla Piattaforma o all\'indirizzo legal@florenceegi.com. Ogni segnalazione sarà esaminata tempestivamente e la decisione motivata sarà comunicata al segnalante e, ove applicabile, al titolare del contenuto segnalato. Lei ha il diritto di presentare reclamo contro qualsiasi decisione di moderazione che riguardi i Suoi contenuti o il Suo account.'
                ],
                [
                    'number' => '10.9',
                    'title' => 'Limitazione di Responsabilità',
                    'content' => 'Nei limiti consentiti dalla legge applicabile, la responsabilità complessiva di FlorenceEGI nei confronti dell\'utente per qualsiasi danno derivante dall\'utilizzo della Piattaforma non potrà eccedere l\'importo complessivo delle somme effettivamente pagate dall\'utente a FlorenceEGI (commissioni di servizio) nei 12 mesi precedenti l\'evento che ha dato origine alla responsabilità. FlorenceEGI non sarà in alcun caso responsabile per danni indiretti, incidentali, consequenziali o punitivi, inclusi perdita di profitti, perdita di dati o interruzione dell\'attività. Questa limitazione non si applica in caso di dolo o colpa grave di FlorenceEGI, né pregiudica i diritti irrinunciabili del consumatore ai sensi del Codice del Consumo.'
                ],
                [
                    'number' => '10.10',
                    'title' => 'Comunicazioni',
                    'content' => 'Le comunicazioni relative al presente Contratto (incluse modifiche dei termini, sospensioni e notifiche legali) saranno inviate tramite email all\'indirizzo associato all\'account dell\'utente e/o tramite notifica sulla Piattaforma. Le comunicazioni si considerano ricevute al momento della visualizzazione sulla Piattaforma o dopo 48 ore dall\'invio dell\'email, in base a quale evento si verifichi per primo.'
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
