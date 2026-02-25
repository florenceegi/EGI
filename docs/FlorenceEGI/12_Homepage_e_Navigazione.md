# Homepage e Navigazione FlorenceEGI

> **Documento creato per il sistema RAG di NATAN AI**
> **Versione**: 1.0 — Febbraio 2026
> **Scopo**: Guida completa alla homepage e alla navigazione di art.florenceegi.com, ottimizzata per l'assistenza AI contestuale.

---

## Cos'è la Homepage di FlorenceEGI

La homepage di FlorenceEGI è il punto di ingresso principale della piattaforma, accessibile all'indirizzo `https://art.florenceegi.com` (o `/home`). È una vetrina dinamica dell'ecosistema che mostra:

- **EGI in evidenza**: opere d'arte digitali certificate su blockchain Algorand
- **Collections in evidenza**: serie artistiche dei creator della piattaforma
- **Creator in evidenza**: artisti e aziende attivi sulla piattaforma
- **Collectors top**: i collezionisti più attivi per volume di acquisti
- **Progetti EPP**: iniziative ambientali supportate dalla piattaforma
- **Sezione Attori**: presentazione dei 6 archetipi di utenti della piattaforma

La homepage è accessibile sia agli utenti non autenticati (guest) che agli utenti registrati di ogni tipo (creator, collector, company, patron, pa_entity, trader_pro).

---

## Struttura della Pagina

La homepage è composta da queste sezioni principali, dall'alto verso il basso:

### 1. Header/Navbar (fisso in cima)

Barra di navigazione sticky con sfondo semi-trasparente (`bg-gray-900/95`). Contiene:
- **Logo** FlorenceEGI (sinistra)
- **Messaggio di benvenuto** personalizzato per utente autenticato
- **Menu di navigazione** (desktop: orizzontale | mobile: hamburger)
- **Pulsanti di accesso** (login, registrazione, wallet) per utenti non autenticati
- **Menu utente** (dropdown) per utenti autenticati

### 2. Barra Statistiche della Piattaforma

Mostra le statistiche aggregate della piattaforma:
- **VOLUME** (€): totale dei pagamenti distribuiti sulla piattaforma
- **EPP** (€): totale destinato ai progetti ambientali
- **COLLECTIONS**: numero totale di collections pubblicate

Versione desktop: barra orizzontale in cima alla hero section.
Versione mobile: layout a carosello/verticale.

### 3. Hero Section — Carosello Collection in Evidenza

Grande area visiva con rotazione automatica (ogni 8 secondi) delle collection in evidenza. Per ogni collection mostra:
- Banner immagine della collection
- Nome della collection e nome del creator
- Statistiche: numero EGI, EGI riservati, volume totale (€), contributo EPP (€)
- Indicatori di navigazione (punti) e pulsanti prev/next

### 4. Carosello EGI

- **Desktop**: carosello orizzontale scrollabile di EGI con larghezza fissa 280px per card
- **Mobile**: toggle tra vista carosello e lista

Mostra EGI pubblicati in modo casuale (fino a 20 per il carosello principale).

### 5. Carosello Creator

Carosello orizzontale con card dei creator attivi sulla piattaforma (fino a 50, in ordine casuale). Ogni card mostra: avatar, nome, numero EGI, numero collections.

### 6. Carosello Collectors

I top 10 collectors per volume di spesa. Ogni card mostra: avatar, nome, ranking, statistiche.

### 7. Carosello Collections in Evidenza

Collections in stile "cubo" con immagini animate. Mostra le collections selezionate come featured.

### 8. Sezione Attori — "L'Ecosistema del Nuovo Rinascimento"

Grid con 6 card che presentano i diversi ruoli sulla piattaforma:
- **Artista Creativo (Creator)**: per chi crea opere digitali
- **Mecenate Visionario (Patron)**: per chi sostiene gli artisti
- **Collezionista Consapevole (Collector)**: per chi acquista opere
- **Imprenditore Innovatore (Company)**: per le aziende creative
- **Trader Pro**: per il trading professionale di arte
- **Pubblica Amministrazione (PA Entity)**: per enti istituzionali

### 9. Banner EPP — "Il Nostro Impegno per il Pianeta"

Sezione con sfondo verde che spiega il programma EPP (Environmental Protection Projects): ogni opera venduta su FlorenceEGI destina una percentuale a progetti di protezione ambientale reale.

---

## Navigazione Desktop

Il menu di navigazione desktop (visibile da md in su) include:

| Voce di Menu | Route | Visibilità |
|---|---|---|
| Home | `/home` | Sempre (nascosta quando sei già in home) |
| Creators | `/creator` | Sempre |
| Collections | `/home/collections` | Sempre |
| Collectors | `/collector` | Sempre |
| Companies | `/company` | Sempre |
| EPP Projects | `/epp-projects` | Sempre |
| Crea EGI | Azione contestuale | Sempre (richede autenticazione) |
| Crea Collection | Modal | Solo se autorizzato |
| Cerca | Modal ricerca universale | Solo desktop |

### Menu Utente Desktop (dropdown)

Per gli utenti autenticati, cliccando sull'avatar appare un mega-menu con:
- Accesso rapido al proprio profilo
- Link alle proprie collections
- Strumenti di gestione (create EGI, create collection)
- Impostazioni account
- Strumenti admin (solo se autorizzato)
- Logout

---

## Navigazione Mobile

Il menu mobile si apre come pannello slide-in da destra, attivato dal pulsante hamburger o dall'avatar utente. Contiene:

- **Card utente** (se autenticato): avatar, nome, usertype, badge collections
- **Links principali**: Home, Creators, Collections, Collectors, Companies, EPP
- **Azioni rapide**: Crea EGI, Crea Collection (se autorizzato)
- **Gestione account**: Impostazioni, Pagamenti, Sicurezza
- **GDPR/Privacy**: Privacy Policy, Cookie Policy
- **Logout**

---

## Esperienza per Utente Guest (Non Autenticato)

Un utente non registrato che visita `art.florenceegi.com` vede:

**Cosa può fare:**
- Sfogliare tutte le opere EGI in evidenza
- Esplorare le collections in evidenza
- Scoprire i creator e i collectors top
- Leggere le informazioni sui progetti EPP
- Navigare verso le pagine pubbliche (creator, company, collection)

**Cosa NON può fare:**
- Acquistare opere (richiede registrazione)
- Creare EGI o collections (richiede registrazione)
- Accedere al proprio profilo o dashboard
- Prenotare opere

**Call-to-Action per Guest:**
- Pulsante **Login** (per utenti già registrati) → `/login`
- Pulsante **Registrati** → `/join` (wizard di registrazione guidato)
- Pulsante **Connetti Wallet** → connessione wallet Algorand
- Nella sezione Attori: pulsanti specifici per tipo (`Crea la Tua Arte`, `Diventa Mecenate`, ecc.) → tutti reindirizzano alla registrazione con ruolo preselezionato

**Nota importante**: La route di registrazione è `/join` (wizard guidato), **NON** `/register`.

---

## Esperienza per Creator Autenticato

Un Creator loggato vede tutti i contenuti pubblici PLUS:

**Accesso rapido nel menu:**
- Il proprio profilo creator (`/creator/{id}/portfolio`)
- Le proprie collections
- Pulsante "Crea EGI" attivo (apre il flow di creazione)
- Pulsante "Crea Collection" attivo (apre modal)
- Impostazioni pagamento (configurazione Stripe/bonifico)

**Dashboard del Creator:**
- Non accessibile dalla homepage ma raggiungibile da `/creator/{id}/portfolio`
- Contiene: portfolio delle opere create, collections, biography, impact ambientale, community

**Cosa può fare il Creator dalla homepage:**
- Cliccare "Crea EGI" per iniziare il processo di minting di una nuova opera
- Cliccare "Crea Collection" per creare una nuova serie artistica
- Navigare verso il proprio profilo dal menu utente
- Esplorare le opere degli altri creator

**Statistiche del Creator (nel suo profilo):**
- Numero di EGI pubblicati
- Numero di collections pubblicate
- Volume totale vendite (€)
- EPP contribuiti (€)

---

## Esperienza per Collector Autenticato

Un Collector loggato vede tutti i contenuti pubblici PLUS:

**Accesso rapido nel menu:**
- Il proprio portfolio collector (`/collector/{id}/portfolio`)
- Le opere nel suo portfolio (EGI acquistati)

**Cosa può fare il Collector dalla homepage:**
- Esplorare nuove opere e collections
- Cliccare su un EGI per aprire il dettaglio e potenzialmente acquistarlo/prenotarlo
- Navigare verso il proprio profilo
- Vedere il ranking dei top collectors (lui stesso potrebbe essere nella lista)

**Statistiche del Collector (nel suo profilo):**
- Numero EGI in portafoglio
- Valore totale investito (€)
- Numero di collections rappresentate

---

## Esperienza per Company (Azienda) Autenticata

Simile al Creator ma con identità aziendale:

**Accesso rapido:**
- Il proprio profilo company (`/company/{id}/portfolio`)
- Le proprie collections aziendali
- Pulsante "Crea EGI" e "Crea Collection"
- Impostazioni pagamento

**Differenza chiave Creator vs Company:**
- **Creator**: gli EPP (Environmental Protection Projects) sono OBBLIGATORI al 20% di ogni vendita
- **Company**: gli EPP sono opzionali (la company può scegliere tra EPP volontari o abbonamenti)

---

## Esperienza per Patron Autenticato

Il Patron è il mecenate della piattaforma — può sostenere i creator ma non creare EGI direttamente. Dalla homepage accede alla propria sezione nel menu e può esplorare i creator da supportare.

---

## Esperienza per PA Entity (Pubblica Amministrazione)

Le Pubbliche Amministrazioni hanno un ruolo istituzionale sulla piattaforma. Dalla homepage possono esplorare il catalogo e accedere alla propria area dedicata. Non creano EGI come i creator normali.

---

## Pagine di Scoperta (Discovery Pages)

Dalla homepage si accede facilmente alle pagine di scoperta:

| Pagina | Route | Descrizione |
|---|---|---|
| Tutte le Collections | `/home/collections` | Lista di tutte le collections pubblicate con filtri |
| Tutti i Creator | `/creator` | Lista dei creator con filtri |
| Tutti i Collector | `/collector` | Lista dei collector |
| Tutte le Companies | `/company` | Lista delle aziende |
| Progetti EPP | `/epp-projects` | Progetti ambientali attivi |

---

## Il Sistema EGI — Spiegazione per l'AI

**EGI** (Ecological Goods Invent) è il tipo di asset digitale principale della piattaforma:
- Ogni EGI è un'opera d'arte digitale certificata su blockchain Algorand
- Ogni EGI appartiene a una Collection
- Ogni EGI ha un prezzo in euro (€)
- La vendita di ogni EGI genera una distribuzione automatica dei pagamenti tra: Creator, Piattaforma, EPP (progetti ambientali)
- I Creator con ruolo normale devono destinare il 20% (EPP obbligatorio)
- Le Company possono scegliere liberamente la percentuale EPP

**Ciclo di vita di un EGI:**
1. Creator crea la Collection → aggiunge EGI → pubblica
2. Collector prenota/acquista l'EGI
3. Il pagamento viene distribuito automaticamente
4. L'EGI viene "mintato" (certificato) su blockchain Algorand
5. Il Collector diventa il nuovo owner dell'EGI
6. Il Collector può rivendere sul mercato secondario

---

## Domande Frequenti sulla Homepage

### Come mi registro su FlorenceEGI?
Vai su `/join` oppure clicca "Registrati" nella navbar. Il wizard di registrazione ti guida passo passo nella scelta del tuo ruolo (creator, collector, company, ecc.).

### Cosa significa "Creator" e "Collector"?
- **Creator**: artista o azienda che crea e vende opere digitali (EGI). Richiede EPP obbligatorio al 20%.
- **Collector**: appassionato o investitore che acquista opere. Può rivendere sul mercato secondario.

### Come funziona la connessione wallet?
Clicca su "Connetti Wallet" nella navbar. Supportiamo wallet Algorand (tipo Pera Wallet, Defly). La connessione wallet non è obbligatoria per registrarsi ma è necessaria per il minting su blockchain.

### Cos'è un EPP?
EPP = Environmental Protection Project. Sono progetti reali di protezione ambientale (recupero plastica, riforestazione, ecc.) sostenuti da ogni vendita sulla piattaforma. I Creator devono destinare il 20% di ogni vendita a un EPP scelto da loro.

### Come creo la mia prima opera (EGI)?
1. Registrati come Creator su `/join`
2. Crea la tua prima Collection (pulsante "Crea Collection")
3. Aggiungi EGI alla collection
4. Configura i pagamenti (Stripe o Bonifico)
5. Pubblica la collection e i tuoi EGI

### Posso comprare un'opera come guest (non registrato)?
No. Per acquistare o prenotare un'opera devi registrarti e autenticarti. La navigazione e la scoperta delle opere sono pubbliche.

### Come funziona il mercato secondario?
Quando un Collector acquista un EGI, diventa il nuovo owner. Può poi decidere di rivendere l'EGI sulla piattaforma. La rivendita genera nuovamente la distribuzione dei pagamenti (Creator riceve royalty, EPP riceve la sua parte, piattaforma trattiene la fee).

### Dove vedo le mie opere acquistate?
Nel tuo profilo Collector: `/collector/{id}/portfolio` → scheda "Owned".

### Dove vedo le mie opere create?
Nel tuo profilo Creator: `/creator/{id}/portfolio` → scheda "Created".

### Come configuro i pagamenti per ricevere le vendite?
Dal tuo profilo (creator o company), clicca sul pulsante "Payment Settings" in basso a destra (desktop) o il FAB arancione (mobile). Puoi configurare Stripe o Bonifico Bancario.

---

## Informazioni Tecniche per l'AI

### Dati Caricati nella Homepage (HomeController)
Il controller carica:
- 5 EGI random pubblicati (da creator con EPP attivo)
- 10 collections in evidenza (algoritmo `FeaturedCollectionService`)
- 8 collections più recenti
- 3 EPP attivi in evidenza
- 50 creator random (conteggio EGI e collections)
- 10 top collectors (per volume di spesa)
- 20 EGI random pubblicati (per carosello principale)
- Tutti gli EGI con `hyper=true`
- Volume totale plastica recuperata: 5.241,38 kg (dato temporaneamente hardcoded)

### Route della Homepage
- **Route name**: `home`
- **URL**: `/home` (redirect da `/`)
- **Controller**: `App\Http\Controllers\HomeController::index()`
- **View**: `resources/views/home.blade.php`
- **Layout**: `<x-guest-layout>`

### Autenticazione
La piattaforma usa un sistema di autenticazione doppia:
- **Strong auth**: `auth()->check()` — utente completamente autenticato
- **Weak auth**: `session('connected_user_id')` — utente collegato tramite wallet senza password
- **Helper**: `App\Helpers\FegiAuth` — astrae entrambi i tipi di auth

### SEO
- **Titolo**: "FlorenceEGI – Vendi una volta. Guadagna per sempre. Genera Equilibrio."
- **Meta description**: Ecosistema per arte digitale con impatto ambientale positivo
- **Schema.org**: WebSite type, Publisher: "Frangette Associazione Promozione Culturale"
