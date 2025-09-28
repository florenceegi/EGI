# FlorenceEGI: Architettura Integrata GDPR e User Domain (OS1-Compliant)

**Autore:** Padmin D. Curtis (AI Partner OS1 per Fabio Cherici)
**Versione:** 1.0 (Derivata dall'analisi e strategia di unificazione)
**Data:** 5 Giugno 2025
**Target:** Team di Sviluppo FlorenceEGI, Fabio Cherici
**Compliance:** Oracode System 1 (OS1) Full Stack

---

## Sommario Esecutivo (Abstract)

Questo documento definisce l'architettura tecnica e funzionale unificata per la gestione dei dati personali degli utenti e la piena conformità al Regolamento Generale sulla Protezione dei Dati (GDPR UE 2016/679) all'interno della piattaforma FlorenceEGI. La sua redazione risponde alla necessità strategica di armonizzare e integrare due sistemi inizialmente sviluppati con focus distinti: un "Sistema GDPR" dedicato alla gestione dei diritti degli interessati e delle compliance di base, e un "Sistema User Domain" orientato alla gestione estesa e dettagliata dei dati anagrafici, fiscali e di contatto dell'utente. Tale sviluppo parallelo aveva condotto a sovrapposizioni funzionali, duplicazioni di logica (es. esportazione dati) e interfacce utente distinte per operazioni concettualmente correlate.

L'obiettivo primario di questa architettura integrata è consolidare le componenti esistenti in un **sistema coeso, efficiente, e intrinsecamente scalabile** per future normative globali e per l'espansione dei domini utente (es. Organizations, Documents, Invoices). Questa unificazione è guidata dai **principi di Oracode System 1 (OS1)**, mirando a massimizzare la chiarezza architetturale, la manutenibilità del codice, l'efficienza operativa e a fornire una user experience intuitiva e rispettosa della dignità dell'utente.

Le **soluzioni architetturali chiave** che definiscono questa integrazione sono:

1.  **Sistema di Ruoli e Permessi a Due Livelli (Globale e Contestuale):** Basato sul package Spatie Laravel Permission, questo sistema astrae completamente la logica di autorizzazione dai tipi utente (`usertype`).
    *   **Livello 1 (Globale):** Ad ogni `usertype` registrato sulla piattaforma corrisponde un ruolo Spatie globale che definisce le capacità generali dell'utente (es. `creator` ha il permesso `create_collection`).
    *   **Livello 2 (Contestuale):** All'interno di specifici contesti, come una `Collection`, gli utenti possono avere ruoli collaborativi (`admin`, `editor`, `guest`) che ne definiscono le capacità *solo* all'interno di quel contesto.
    Questa struttura elimina la necessità di controlli `if ($user->usertype === ...)` hardcoded, basando tutta la logica di business e l'accesso alle funzionalità sui permessi verificati tramite `$user->can('nome_permesso')` o sul ruolo contestuale.

2.  **Sidebar Dinamica Contestuale:** Un sistema di navigazione personalizzato (design "by Fabio Cherici") che adatta dinamicamente le voci di menu visualizzate in base al contesto applicativo corrente (es. dashboard, gestione collection, profilo utente) e ai permessi specifici dell'utente autenticato. Questo assicura che gli utenti vedano solo le opzioni pertinenti e autorizzate.

3.  **Ridefinizione e Centralizzazione delle Responsabilità dei Controller:**
    *   **`PersonalDataController` (`App\Http\Controllers\User`):** Designato come gestore primario per la visualizzazione e la modifica dell'intero spettro dei dati personali dettagliati dell'utente (anagrafici, residenza, contatto, fiscali). La sua interfaccia utente (vista `users.domains.personal-data.index`) diventa il punto di riferimento per queste operazioni. Integra nativamente la gestione contestuale dei principali consensi GDPR (es. marketing, analytics) attraverso l'utilizzo diretto del `ConsentService`.
    *   **`GdprController` (`App\Http\Controllers`):** Viene rifocalizzato per gestire:
        *   I dati di registrazione di base dell'account (es. `first_name`, `last_name` forniti alla registrazione, email – considerati quasi immutabili e separati dai dati anagrafici dettagliati).
        *   La sicurezza dell'account (modifica password, gestione 2FA, sessioni browser).
        *   La gestione completa e granulare di *tutti* i tipi di consenso privacy (attraverso un'interfaccia dedicata, il suo tab "Privacy").
        *   Come hub centrale per l'esercizio dei diritti GDPR globali (esportazione dati, limitazione del trattamento, cancellazione account, accesso al registro attività, segnalazione violazioni, consultazione policy).

4.  **Centralizzazione della Logica di Business nei Servizi Specializzati:**
    *   Funzionalità comuni come la gestione dei consensi (`ConsentService`), l'esportazione dei dati (`DataExportService`), e l'auditing (`AuditLogService`) sono incapsulate in servizi dedicati.
    *   Questi servizi sono utilizzati in modo coerente dai controller rilevanti, eliminando la duplicazione di logica (es. `PersonalDataController` delegherà le operazioni di export a `DataExportService` invece di mantenere una propria implementazione).
    *   Il `ConsentService` migrerà dalla gestione hardcoded dei tipi di consenso a un sistema basato su database (modello `ConsentType`) per garantire la scalabilità globale.

L'adozione di questa architettura integrata porterà a una maggiore chiarezza strutturale, ridurrà la ridondanza del codice, migliorerà la manutenibilità e l'efficienza dello sviluppo. Preparerà inoltre FlorenceEGI per una facile espansione verso nuove funzionalità e per l'adattamento a future normative internazionali in materia di privacy e fiscalità, mantenendo una piena aderenza ai principi OS1. Il presente documento dettaglia lo stato attuale dei componenti chiave, le specifiche azioni tecniche necessarie per completare l'unificazione, e fornisce una roadmap per il futuro sviluppo dello User Domain, con un focus iniziale sul raggiungimento degli obiettivi MVP.

---

## 1. Principi Architetturali OS1 Adottati

L'architettura integrata di FlorenceEGI per GDPR e User Domain è stata progettata e viene valutata secondo i Cinque Pilastri Cardinali di Oracode System 1 (OS1), assicurando che ogni decisione e componente contribuisca a un sistema robusto, etico ed evolutivo:

-   **🎯 Esplicitamente Intenzionale:** Ogni componente (Controller, Servizio, Modello, Vista), ogni permesso definito e ogni flusso utente ha uno scopo dichiarato, misurabile e verificabile. Le responsabilità dei `PersonalDataController` e `GdprController` sono state ridefinite esplicitamente per eliminare ambiguità e garantire che ogni sistema contribuisca in modo specifico e complementare alla gestione dei dati utente e alla compliance GDPR. La scelta di centralizzare la logica nei Servizi risponde all'intenzione di avere un Single Source of Truth per le operazioni critiche.
-   **🔧 Semplicità Potenziante:** L'unificazione mira a ridurre la complessità accidentale derivante da sistemi e interfacce duplicati. La scelta di designare `PersonalDataController` come gestore primario dei dati anagrafici dettagliati, semplificando il ruolo del `GdprController` in quest'area, è un esempio di ricerca della chiarezza. Il sistema di permessi a due livelli, pur essendo potente, offre una struttura concettuale semplice (globale vs. contestuale) che facilita la comprensione e l'estensione, massimizzando la libertà futura di aggiungere nuovi tipi di utente o contesti senza stravolgere la logica esistente.
-   **🎭 Coerenza Semantica:** Si persegue una nomenclatura unificata (es. per permessi, rotte, traduzioni) e un'esperienza utente coerente. La "fusione" dei sistemi assicura che l'utente interagisca con i propri dati e i propri diritti in modo logico e prevedibile, indipendentemente dal punto di accesso iniziale (che sarà guidato dalla Sidebar Dinamica). Anche a livello di codice, l'uso consistente dei Servizi da parte dei Controller garantisce che la stessa logica di business sia applicata uniformemente.
-   **🔄 Circolarità Virtuosa:** Un'architettura chiara, ben documentata e con responsabilità definite migliora l'efficienza del team di sviluppo, permettendo di rilasciare valore più rapidamente e con maggiore qualità. I sistemi di permessi e la navigazione contestuale sono progettati per facilitare una collaborazione sicura e l'empowerment dell'utente, che può controllare i propri dati in modo trasparente. Il feedback generato dall'uso del sistema (es. audit log, richieste DPO) alimenta informazioni utili per la sua stessa evoluzione.
-   **📈 Evoluzione Ricorsiva:** L'approccio adottato (analisi dettagliata dello stato attuale del codice e delle UI, identificazione delle aree di miglioramento e duplicazione, definizione di una strategia di unificazione chiara) è intrinsecamente evolutivo. Il sistema è progettato per adattarsi: la migrazione di `ConsentType` a un modello DB è un esempio di evoluzione per la scalabilità; la struttura a domini dello User Domain (`Organizations`, `Documents`, `Invoices`) è pensata per un'aggiunta incrementale di funzionalità. Ogni scelta di integrazione è pensata per migliorare la capacità del sistema di affrontare sfide future, incluse nuove normative globali.

---

## 2. Architettura Generale del Sistema Integrato

### 2.1. Visione d'Insieme

L'architettura integrata di FlorenceEGI per la gestione dei dati utente e la conformità GDPR è concepita come un ecosistema di componenti Laravel che collaborano sinergicamente. Questa architettura pone l'accento sulla separazione delle responsabilità (SoC), sulla sicurezza by design e sulla centralizzazione della logica di business critica in servizi riutilizzabili.

Il flusso operativo generale vede l'utente interagire con l'applicazione attraverso interfacce web responsive. L'accesso a queste interfacce e alle funzionalità sottostanti è rigorosamente controllato da due meccanismi principali:
1.  Il **Sistema di Navigazione Utente (Sidebar Dinamica)**, che presenta all'utente solo le opzioni di menu pertinenti al suo contesto e ai suoi permessi.
2.  Il **Sistema di Ruoli e Permessi a Due Livelli**, che applica le regole di autorizzazione a livello di piattaforma (permessi globali) e a livello di risorse specifiche (ruoli contestuali, es. per le Collections).

I **Controller** principali per quest'area sono:
*   `App\Http\Controllers\User\PersonalDataController`: Agisce come entry-point e orchestratore per la gestione dei dati personali dettagliati dell'utente (anagrafici, fiscali, di contatto). Si interfaccia con il modello `App\Models\UserPersonalData` e utilizza servizi come `App\Services\Gdpr\ConsentService` per operazioni correlate.
*   `App\Http\Controllers\GdprController`: Funge da centro di controllo per le impostazioni di sicurezza dell'account, la gestione completa e granulare dei consensi privacy, e come portale unificato per l'esercizio dei diritti GDPR (esportazione, restrizioni, cancellazione, ecc.). Utilizza un set più ampio di servizi GDPR.

I **Servizi** (`App\Services\Gdpr\*`) incapsulano la logica di business specifica e complessa:
*   `ConsentService`: Gestisce la creazione, l'aggiornamento, la revoca e l'interrogazione dei consensi utente.
*   `DataExportService`: Centralizza la generazione di esportazioni dati conformi al GDPR.
*   `AuditLogService`: Fornisce meccanismi per la registrazione dettagliata delle azioni rilevanti per la compliance.
*   `GdprService`: Gestisce operazioni GDPR specifiche non coperte dagli altri servizi (es. richieste di rettifica, gestione policy, comunicazioni DPO, esecuzione cancellazione account).
*   `ProcessingRestrictionService`: Gestisce le richieste di limitazione del trattamento dei dati.
*   (Altri servizi come `ActivityLogService` verranno dettagliati man mano che si analizza il codice).

I **Modelli Eloquent** (`App\Models\*`) rappresentano le entità dati (es. `User`, `UserPersonalData`, `UserConsent`, `ConsentVersion`, `GdprRequest`) e implementano logica a livello di dato (es. cast, relazioni, scopes, immutabilità).

L'**Integrazione con l'Ecosistema Ultra** (`UltraLogManager`, `ErrorManagerInterface`) è garantita attraverso l'iniezione delle dipendenze nei costruttori dei Controller e dei Servizi, assicurando logging standardizzato e gestione robusta degli errori.

L'obiettivo di questa architettura è che la logica relativa alla gestione dei dati e alla compliance GDPR sia chiara, testabile, sicura e facilmente estensibile, con `PersonalDataController` e `GdprController` che orchestrano le operazioni chiamando i servizi appropriati, i quali a loro volta interagiscono con i modelli e l'infrastruttura sottostante.

*(Un diagramma Mermaid o simile qui sarebbe molto utile per visualizzare le interazioni tra Controller, Servizi e Modelli. Potremmo provare a generarlo in seguito, una volta mappate tutte le interazioni chiave.)*

```mermaid
graph TD
    %% =============================================================================
    %% PRESENTATION LAYER - User Interface Components
    %% =============================================================================
    subgraph UI["🎨 PRESENTATION LAYER"]
        UI_Sidebar["Sidebar GDPR<br/>Navigation"]
        UI_PersonalData["Personal Data Views<br/>Forms & Display"]
        UI_Privacy["Privacy Center<br/>Dashboard & Settings"]
    end
    
    %% SPACER LAYER 1
    SPACER1[" "] 
    UI_Sidebar -.-> SPACER1
    UI_PersonalData -.-> SPACER1
    UI_Privacy -.-> SPACER1
    
    %% =============================================================================
    %% CONTROLLER LAYER - Request Handlers
    %% =============================================================================
    subgraph CTRL["🎮 CONTROLLER LAYER"]
        PersonalDataCtrl["PersonalDataController<br/>• CRUD Personal Data<br/>• Field Validation<br/>• Export & Delete"]
        GdprCtrl["GdprController<br/>• Consent Management<br/>• Privacy Dashboard<br/>• Breach Reports<br/>• DPO Contact"]
    end
    
    %% SPACER LAYER 2
    SPACER2[" "]
    PersonalDataCtrl -.-> SPACER2
    GdprCtrl -.-> SPACER2
    
    %% =============================================================================
    %% SERVICE LAYER - Business Logic
    %% =============================================================================
    subgraph SRV["⚙️ SERVICE LAYER"]
        ConsentSrv["ConsentService<br/>• Grant/Withdraw Consent<br/>• Consent History<br/>• Version Management<br/>• Audit Trail"]
        
        GdprSrv["GdprService<br/>• Data Export<br/>• Processing Restrictions<br/>• Account Deletion<br/>• DPO Communication"]
        
        ProcessingRestrictionSrv["ProcessingRestrictionService<br/>• Restrict Data Processing<br/>• Manage Limitations<br/>• Compliance Tracking"]
        
        ActivityLogSrv["ActivityLogService<br/>• GDPR Activity Logging<br/>• Audit Trail<br/>• Compliance Reports"]
        
        FiscalValidatorSrv["FiscalValidatorFactory<br/>• Country-specific Validation<br/>• Tax Code Validation<br/>• Business Type Rules"]
    end
    
    %% SPACER LAYER 3
    SPACER3[" "]
    ConsentSrv -.-> SPACER3
    GdprSrv -.-> SPACER3
    ProcessingRestrictionSrv -.-> SPACER3
    
    %% =============================================================================
    %% MODEL LAYER - Data & Relationships
    %% =============================================================================
    subgraph MDL["💾 MODEL LAYER"]
        subgraph UserModels["User Data"]
            User["User<br/>Base user model"]
            UserPersonalData["UserPersonalData<br/>Personal information"]
        end
        
        subgraph ConsentModels["Consent System"]
            UserConsent["UserConsent<br/>Individual consents"]
            ConsentVersion["ConsentVersion<br/>Policy versions"]
            ConsentType["ConsentType<br/>Available types"]
        end
        
        subgraph GdprModels["GDPR Compliance"]
            GdprRequest["GdprRequest<br/>Data subject requests"]
            ProcessingRestriction["ProcessingRestriction<br/>Processing limitations"]
            BreachReport["BreachReport<br/>Security incidents"]
        end
        
        subgraph PolicyModels["Policy & Communication"]
            PrivacyPolicy["PrivacyPolicy<br/>Policy versions"]
            DpoMessage["DpoMessage<br/>DPO communications"]
        end
    end
    
    %% SPACER LAYER 4
    SPACER4[" "]
    User -.-> SPACER4
    UserConsent -.-> SPACER4
    GdprRequest -.-> SPACER4
    
    %% =============================================================================
    %% INFRASTRUCTURE LAYER - Framework & Ultra
    %% =============================================================================
    subgraph INFRA["🏗️ INFRASTRUCTURE LAYER"]
        UEM["UltraErrorManager<br/>Error handling & reporting"]
        ULM["UltraLogManager<br/>Logging & audit trails"]
        Auth["FegiAuth<br/>Authentication system"]
        Permissions["Laravel Permissions<br/>Access control"]
    end
    
    %% =============================================================================
    %% PRIMARY FLOW CONNECTIONS - Simplified to reduce clutter
    %% =============================================================================
    
    %% UI to Controllers (through spacer)
    SPACER1 --> PersonalDataCtrl
    SPACER1 --> GdprCtrl
    
    %% Controllers to Services (through spacer)
    SPACER2 --> ConsentSrv
    SPACER2 --> GdprSrv
    SPACER2 --> FiscalValidatorSrv
    
    %% Services to Models (through spacer)
    SPACER3 --> User
    SPACER3 --> UserConsent
    SPACER3 --> GdprRequest
    
    %% Infrastructure (through spacer)
    SPACER4 --> UEM
    SPACER4 --> ULM
    SPACER4 --> Auth
    
    %% =============================================================================
    %% STYLING
    %% =============================================================================
    
    classDef uiClass fill:#4CAF50,stroke:#2E7D32,color:#fff,font-weight:bold
    classDef ctrlClass fill:#2196F3,stroke:#1565C0,color:#fff,font-weight:bold
    classDef serviceClass fill:#FF9800,stroke:#E65100,color:#fff,font-weight:bold
    classDef modelClass fill:#9C27B0,stroke:#6A1B9A,color:#fff,font-weight:bold
    classDef infraClass fill:#607D8B,stroke:#37474F,color:#fff,font-weight:bold
    classDef spacerClass fill:transparent,stroke:transparent
    
    class UI_Sidebar,UI_PersonalData,UI_Privacy uiClass
    class PersonalDataCtrl,GdprCtrl ctrlClass
    class ConsentSrv,GdprSrv,ProcessingRestrictionSrv,ActivityLogSrv,FiscalValidatorSrv serviceClass
    class User,UserPersonalData,UserConsent,ConsentVersion,ConsentType,GdprRequest,ProcessingRestriction,BreachReport,PrivacyPolicy,DpoMessage modelClass
    class UEM,ULM,Auth,Permissions infraClass
    class SPACER1,SPACER2,SPACER3,SPACER4 spacerClass
```

### 2.2. Sistema di Navigazione Utente (Sidebar Dinamica)

*(Questa sezione integrerà e adatterà il contenuto di `Sidebar Dinamica.md`, focalizzandosi sulla struttura di menu risultante dalla strategia di unificazione per le aree GDPR e User Domain).*

La navigazione utente all'interno di FlorenceEGI, specialmente per le sezioni relative alla gestione del profilo, dei dati personali, della privacy e dei diritti GDPR, è orchestrata da un sistema di **Sidebar Dinamica e Contestuale**, un'architettura personalizzata sviluppata da Fabio Cherici. Questo sistema è progettato secondo i principi OS1 per garantire:
*   **Chiarezza e Pertinenza:** L'utente visualizza solo le opzioni di menu appropriate al contesto corrente dell'applicazione (es. dashboard principale, gestione di una specifica Collection, impostazioni del profilo utente) e ai suoi permessi individuali.
*   **Manutenibilità e Scalabilità:** La definizione dei menu è centralizzata e modulare, facilitando l'aggiunta o la modifica di voci senza impattare altre parti del sistema.
*   **Sicurezza:** La visibilità degli item è strettamente legata al sistema di permessi, impedendo l'esposizione di link a funzionalità non autorizzate.

**Principi di Funzionamento Essenziali:**

1.  **Definizione della Struttura del Menu (PHP):**
    *   Le singole voci di menu sono rappresentate da classi che estendono `App\Services\Menu\MenuItem`. Ogni classe definisce la chiave di traduzione per il nome, la rotta Laravel, la chiave dell'icona e, opzionalmente, il permesso Laravel necessario per visualizzare l'item e un array di `MenuItem` figli.
    *   Questi `MenuItem` sono raggruppati in oggetti `App\Services\Menu\MenuGroup`, che definiscono anche un nome e un'icona per il gruppo.
    *   La logica che determina quali `MenuGroup` e `MenuItem` appaiono in quali contesti è centralizzata nella classe `App\Services\Menu\ContextMenus`, specificamente nel metodo `getMenusForContext(string $context): array`.

2.  **Gestione Centralizzata delle Icone (SVG):**
    *   Le icone sono definite come stringhe SVG (con colori intrinseci) nel file di configurazione `config/icons.php`. Un `IconSeeder` popola queste definizioni nella tabella `icons` del database.
    *   Il `App\Repositories\IconRepository` è responsabile del recupero, del processamento (sostituzione del placeholder `%class%` per lo styling) e del caching delle icone SVG.
    *   Prima del rendering della sidebar, la struttura del menu viene "arricchita": le chiavi delle icone vengono sostituite con l'HTML SVG completo e processato.

3.  **Rendering e Controllo dei Permessi (Blade & Gate):**
    *   La vista Blade `resources/views/components/sidebar.blade.php` (o percorso equivalente) itera sulla struttura del menu arricchita.
    *   Per ogni `MenuGroup` e `MenuItem`, la direttiva `Gate::allows($permission)` (o una logica equivalente in `MenuConditionEvaluator` che a sua volta usa il Gate) verifica se l'utente autenticato ha i permessi per visualizzare l'elemento.
    *   Le icone SVG vengono stampate direttamente (es. `{!! $item['icon'] !!}`).
    *   La vista gestisce la logica per i gruppi collassabili (usando `<details>`) e per evidenziare l'item o il gruppo attivo.

**Struttura di Navigazione Unificata per GDPR e User Domain:**

In linea con la strategia di unificazione e la rifocalizzazione delle responsabilità dei controller, la sidebar presenterà la seguente organizzazione logica per le aree di gestione utente e GDPR, garantendo percorsi chiari e minimizzando la ridondanza:

*   **Menu Principale (o Gruppo): "Il Mio Account"** (o etichetta simile, es. "Profilo e Impostazioni")
    *   **Sottovoce: "Dati Personali e Contatti"**
        *   *Descrizione:* Accesso alla gestione completa dei propri dati anagrafici, di residenza, contatto, e fiscali. Include la gestione contestuale dei consensi marketing e analytics.
        *   *Punta a:* Interfaccia utente gestita da `PersonalDataController` (la vista dettagliata dello User Domain).
        *   *Permesso Globale Esempio:* `view_own_personal_data` (o permesso implicito per utenti autenticati di vedere i propri dati).
    *   **Sottovoce: "Impostazioni Account e Sicurezza"**
        *   *Descrizione:* Gestione dei dati di registrazione base (nome, cognome, email), modifica password, configurazione Two-Factor Authentication (2FA), e gestione delle sessioni browser attive.
        *   *Punta a:* Interfaccia utente gestita da `GdprController`, specificamente alle sezioni/tab dedicate a questi aspetti (esclusi i dati anagrafici dettagliati).
        *   *Permesso Globale Esempio:* `manage_own_account_settings`.
    *   **Sottovoce: "Preferenze Privacy e Consensi"**
        *   *Descrizione:* Centro di controllo completo per visualizzare lo stato e gestire in modo granulare tutti i tipi di consenso privacy (funzionali, analytics, marketing, profilazione, trattamento dati). Include la visualizzazione della cronologia dei consensi.
        *   *Punta a:* Interfaccia utente gestita da `GdprController` (specificamente il tab "Privacy" o una vista dedicata alla gestione dettagliata dei consensi).
        *   *Permesso Globale Esempio:* `manage_own_privacy_consents`.

*   **Menu Principale (o Gruppo): "Diritti GDPR e Trasparenza"** (o etichetta simile)
    *   **Sottovoce: "Esporta i Tuoi Dati"**
        *   *Punta a:* Funzionalità di esportazione dati gestita da `GdprController` (che utilizzerà il centralizzato `DataExportService`).
        *   *Permesso Globale Esempio:* `request_own_data_export`.
    *   **Sottovoce: "Limita Trattamento Dati"**
        *   *Punta a:* Funzionalità di gestione delle restrizioni al trattamento, gestita da `GdprController`.
        *   *Permesso Globale Esempio:* `request_processing_restriction`.
    *   **Sottovoce: "Elimina Account"**
        *   *Punta a:* Processo di richiesta e conferma eliminazione account, gestito da `GdprController`.
        *   *Permesso Globale Esempio:* `request_account_deletion`.
    *   **Sottovoce: "Registro Attività Dati"**
        *   *Punta a:* Visualizzazione del log delle attività relative ai propri dati, gestita da `GdprController`.
        *   *Permesso Globale Esempio:* `view_own_activity_log`.
    *   **Sottovoce: "Segnala Violazione Dati"**
        *   *Punta a:* Funzionalità di segnalazione violazioni, gestita da `GdprController`.
        *   *Permesso Globale Esempio:* `report_data_breach`.
    *   **Sottovoce: "Policy e Informative"**
        *   *Punta a:* Sezione per la consultazione di Privacy Policy, Termini di Servizio, e altre informative sulla trasparenza del trattamento dati, gestita da `GdprController`.
        *   *Permesso:* Generalmente pubblico o per utenti autenticati.

*   **Menu Principale: "Domini Utente" (o etichetta più specifica se "Dati Personali e Contatti" è già sotto "Il Mio Account")**
    *   *(Questa sezione si espanderà con "Le Mie Organizzazioni", "I Miei Documenti", "Le Mie Collezioni Personali" etc., ognuna con i propri permessi e contesti)*
    *   Se "Dati Personali e Contatti" non è già sotto "Il Mio Account", potrebbe risiedere qui come voce principale dello User Domain.

Questa struttura di navigazione proposta mira a fornire percorsi logici e distinti, minimizzando la confusione per l'utente e riflettendo la specializzazione funzionale dei controller `PersonalDataController` e `GdprController` come discusso. La sidebar "GDPR Quick Actions" precedentemente presente nella vista User Domain verrà rimossa, poiché le sue funzionalità sono ora accessibili in modo più strutturato attraverso questi menu principali.

### 2.3. Sistema di Ruoli e Permessi a Due Livelli

*(Questa sezione integrerà e adatterà il contenuto di `Sistema Ruoli e Permessi FlorenceEGI.md`, contestualizzandolo all'interno dell'architettura GDPR e User Domain).*

L'autorizzazione all'interno di FlorenceEGI è governata da un sofisticato **sistema a due livelli**, progettato da Fabio Cherici e conforme ai principi OS1, che combina permessi globali a livello di piattaforma con ruoli specifici all'interno di contesti definiti (come le "Collections"). Questo approccio elimina la necessità di controlli hardcoded basati sui tipi utente (`usertype`), fondando invece tutta la logica di accesso e di business sulle *capacità* (permessi) effettive dell'utente.

**Livello 1: Identità Globale e Permessi di Piattaforma**

1.  **User Type e Ruolo Globale Spatie:**
    *   Al momento della registrazione, o successivamente, a un utente viene assegnato uno `usertype` (es. `creator`, `patron`, `collector`, `enterprise`, `trader_pro`, `epp_entity`).
    *   Ogni `usertype` è mappato direttamente (1:1) a un **ruolo globale** gestito dal package `spatie/laravel-permission` (es. `usertype='creator'` → ruolo Spatie `'creator'`).
    *   Questo ruolo globale definisce l'identità primaria dell'utente sulla piattaforma.

2.  **Permessi Globali Associati ai Ruoli:**
    *   A ciascun ruolo globale Spatie sono associati una serie di **permessi globali** che ne definiscono le capacità intrinseche a livello di piattaforma, indipendentemente da contesti specifici.
    *   Esempi di permessi globali:
        *   `create_collection`: Assegnato ai ruoli `creator`, `patron`, `enterprise`. Permette la creazione di nuove Collections.
        *   `buy_egi`: Assegnato a `collector`, `patron`. Permette l'acquisto di EGI.
        *   `advanced_trading`: Assegnato a `trader_pro`.
        *   `edit_own_personal_data`: Permesso fondamentale assegnato a tutti i ruoli utente per gestire i propri dati.
        *   `request_own_data_export`: Permesso per esercitare il diritto alla portabilità.
    *   La logica di business nei Controller e nei Servizi verifica questi permessi utilizzando `if (auth()->user()->can('nome_permesso')) { ... }` o il middleware `can:nome_permesso` sulle rotte.

**Livello 2: Contesto Specifico (es. Collection) e Ruoli Collaborativi Contestuali**

1.  **Ruoli all'Interno di un Contesto:**
    *   Per entità che implicano collaborazione o gestione condivisa, come una `Collection`, gli utenti possono avere ruoli specifici *validi solo all'interno di quel contesto*.
    *   Questi ruoli sono tipicamente memorizzati in una tabella pivot (es. `collection_user` con una colonna `role`).
    *   Esempi di ruoli contestuali per una `Collection`:
        *   `admin`: Controllo completo sulla Collection (invitare/rimuovere membri, modificare impostazioni, contenuti).
        *   `editor`: Può creare e modificare contenuti (EGI) all'interno della Collection.
        *   `guest`: Accesso in sola lettura.

2.  **Controllo dei Permessi Contestuali:**
    *   L'accesso a funzionalità specifiche di un contesto (es. modificare un EGI in una Collection) è protetto verificando il ruolo dell'utente *in quel contesto*.
    *   Questo è spesso implementato tramite **middleware personalizzati** (es. `CheckCollectionPermission` menzionato nel documento sui permessi, che verifica il ruolo dell'utente nella `$request->route('collection')`).
    *   Esempio: `Route::middleware(['auth', 'collection.permission:editor'])->post('/collections/{collection}/egi', ...);`

**Applicazione all'Architettura GDPR e User Domain:**

*   **Accesso alle Funzionalità GDPR:** La maggior parte delle funzionalità GDPR (es. visualizzare i propri consensi, richiedere l'esportazione dei propri dati, modificare i propri dati personali) sarà governata da **permessi globali** assegnati a tutti i ruoli utente (es. `view_own_consents`, `edit_own_personal_data`). Questo aderisce al principio "My Data" e alla compliance GDPR.
*   **Gestione Dati User Domain:** L'accesso alla vista del `PersonalDataController` per modificare i propri dati anagrafici dettagliati sarà protetto da un permesso globale come `edit_own_personal_data`. Le future sezioni dello User Domain (Organizations, Documents) avranno i loro set di permessi globali (es. `manage_own_organizations`, `manage_own_documents`) e, se implicano collaborazione, potrebbero introdurre ruoli contestuali specifici (es. un utente potrebbe essere `admin` della propria organizzazione, o `editor` di un documento condiviso).
*   **Interazione tra Livelli:** Alcune azioni potrebbero richiedere una combinazione di permessi. Ad esempio, per invitare un utente in una Collection (funzionalità di team building), l'utente che invita potrebbe aver bisogno di un permesso globale `create_team` E del ruolo `admin` nella Collection specifica.

Questo sistema a due livelli fornisce la flessibilità necessaria per gestire una piattaforma complessa come FlorenceEGI, mantenendo la logica di autorizzazione chiara, separata dal tipo utente primario, e facilmente estensibile per nuove funzionalità e normative. La Sidebar Dinamica rispetterà nativamente questi permessi, mostrando all'utente solo le azioni e le sezioni a cui è autorizzato ad accedere.

---
```

Fabio, questa prima parte è molto più densa e cerca di incorporare i dettagli discussi.
Ho provato a:
*   Riscrivere l'Abstract in modo più specifico.
*   Dettagliare meglio la Visione d'Insieme dell'architettura, includendo un diagramma Mermaid testuale (possiamo affinarlo).
*   Integrare in modo più completo le informazioni dai documenti sulla Sidebar e sui Permessi nelle sezioni 2.2 e 2.3, contestualizzandole per GDPR e User Domain e proponendo una struttura di navigazione unificata.

**Cosa ne pensi di questo livello di dettaglio iniziale?** Va nella direzione giusta rispetto alla tua aspettativa di un documento "autosufficiente"?

So che manca ancora l'analisi dei file di codice dei controller e dei servizi nelle sezioni successive (Sezione 3 in poi), ma volevo assicurarmi che l'impostazione generale e il dettaglio di queste sezioni introduttive fossero corretti prima di procedere.

Se questa base ti convince, il prossimo passo sarebbe **analizzare `DataExportService.php`** e poi iniziare a popolare la **Sezione 3.1 (Controller Principali)** con i dettagli di `PersonalDataController` e `GdprController` come nell'esempio che ti ho fatto precedentemente, seguiti dalla **Sezione 3.2 (Servizi GDPR Specializzati)**.