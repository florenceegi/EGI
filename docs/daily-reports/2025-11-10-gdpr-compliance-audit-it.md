# Audit di Conformità GDPR FlorenceEGI

-   **Data:** 10 novembre 2025
-   **Auditor:** Padmin D. Curtis (AI Partner OS3.0)

---

## ✅ Implementazioni Esistenti

-   Il controller GDPR principale inietta tutti e quattro i servizi obbligatori (UltraLogManager, Ultra Error Manager, AuditLogService, ConsentService) e gestisce il rendering delle pagine con logging strutturato e gestione errori centralizzata.

    -   File di riferimento: `app/Http/Controllers/GdprController.php`

-   Il flusso di minting applica audit logging e recupero tramite Ultra Error Manager per i controlli di pagamento e l’orchestrazione blockchain.

    -   File di riferimento: `app/Http/Controllers/MintController.php`

-   La gestione delle immagini profilo verifica il consenso prima di ogni modifica, registra tutte le fasi e utilizza l’AuditLogService per mantenere la tracciabilità.

    -   File di riferimento: `app/Http/Controllers/ProfileImageController.php`

-   Il servizio di anchoring blockchain converte i certificati in hash prima dell’ancoraggio, garantendo che nessun dato personale venga trasferito on-chain.

    -   File di riferimento: `app/Services/CertificateAnchorService.php`

-   L’AuditLogService memorizza contesti mascherati e applica periodi di conservazione per ogni attività loggata.

    -   File di riferimento: `app/Services/Gdpr/AuditLogService.php`

-   Le funzionalità DSAR (consenso, esportazione, cancellazione, segnalazione di violazioni) sono disponibili tramite route dedicate e middleware di sicurezza.

    -   File di riferimento: `routes/gdpr.php`

-   I fornitori terzi sono centralizzati in configurazione con note di conformità e dipendono da variabili d’ambiente (Postmark, SES, Anthropic, OpenAI, Perplexity).
    -   File di riferimento: `config/services.php`

---

## ⚠️ Implementazioni Parziali o Mancanti

-   Il `BaseUserDomainController` inietta solo logger ed error manager; mancano AuditLogService e ConsentService, nonostante i controller derivati richiedano `$this->logUserAction()` e controlli di consenso.

    -   File di riferimento: `app/Http/Controllers/User/BaseUserDomainController.php`

-   `UserDocumentsController` salva i documenti di identità su disco “private” senza cifratura envelope e senza verifica del consenso; solo i metadati sono memorizzati nel modello `UserDocument`.

    -   File di riferimento: `app/Http/Controllers/User/UserDocumentsController.php`

-   Non esiste un gate riutilizzabile di consenso per gli aggiornamenti dei domini utente (documenti, fatturazione, dati organizzativi); solo `ProfileImageController` esegue un controllo esplicito su `ConsentService::hasConsent()`.

-   La copertura di cifratura è disomogenea: wallet e API key sono protetti, ma documenti di identità, preferenze di fatturazione e dati dell’organizzazione restano in chiaro.

    -   File di riferimento esemplificativo: `app/Console/Commands/GeneratePaApiKey.php`

-   Necessario verificare il collegamento fra logging ULM e gestione errori UEM nei controller dei domini utente: `$this->handleError()` è richiamato, ma la relativa implementazione non è visibile nel repository.

-   Le note su fornitori terzi indicano la conformità, ma non esiste un controllo automatico che garantisca endpoint UE o la presenza dei DPA firmati; serve validazione manuale.

-   Revisione dei principi di minimizzazione per moduli organizzativi e di fatturazione: numerosi campi facoltativi restano memorizzati senza politiche di retention dedicate.

---

## 🧩 Raccomandazioni per la Conformità Completa

1. **Iniettare AuditLogService e ConsentService nel BaseUserDomainController** (o fornire trait condivisi) così che ogni controller domini esegua logging tramite il servizio ufficiale.
2. **Introdurre controlli di consenso** prima di scrivere preferenze di fatturazione, profili organizzativi e documenti caricati; preferire un helper tipo `requireConsent('allow-personal-data-processing')`.
3. **Cifrare i documenti di identità a riposo** riutilizzando l’envelope encryption già disponibile nel `WalletProvisioningService` e aggiornando gli asset esistenti.
4. **Estendere il pattern di hashing blockchain** ad eventuali altre prove on-chain, garantendo che nessuna metadata sensibile venga esposta.
5. **Rendere visibile un helper UEM condiviso**: esporre in `BaseUserDomainController` un `handleError()` che deleghi direttamente a `$this->errorManager->handle(...)` assicurando risposte uniformi.
6. **Automatizzare retention e cancellazione** per documenti utente e dati organizzativi, sfruttando i timestamp `retention_until` e pianificando job di pulizia.
7. **Rafforzare la governance dei fornitori terzi** con health check automatici su endpoint, regioni e presenza di DPA.
8. **Aumentare la copertura di test DSAR** garantendo che tutte le route `dashboard/gdpr` siano autenticate, auditabili e correttamente localizzate.

---

## 📊 Mappa delle Priorità

| Gap                                                                        | Priorità    | Note                                                   |
| -------------------------------------------------------------------------- | ----------- | ------------------------------------------------------ |
| Mancato DI di AuditLogService/ConsentService nei controller dominio utente | **Critica** | Rischio di errori runtime e mancanza di audit trail.   |
| Documenti di identità non cifrati nello storage privato                    | **Critica** | PII a riposo senza cifratura; necessario usare KMS.    |
| Assenza di verifica del consenso per fatturazione/organizzazione           | **Media**   | Modifiche dati personali senza controllo preventivo.   |
| Controlli automatizzati sui fornitori terzi                                | **Media**   | Possibile drift di configurazione; serve monitoraggio. |
| Visibilità helper UEM nei controller dominio utente                        | **Media**   | Garantire che tutte le eccezioni passino da UEM.       |
| Strategia di retention per dati facoltativi                                | **Bassa**   | Valutare politiche di conservazione o anonimizzazione. |

---

## 🔎 Verifiche Manuali Necessarie

-   Confermare l’esistenza o implementare i metodi condivisi `handleError()` e `logUserAction()` sui controller dominio utente.
-   Validare le configurazioni `.env` per SES/AWS, Anthropic, OpenAI, Perplexity, assicurando endpoint UE e DPA archiviati.
-   Controllare i processi di backup e relativi job di retention per garantire l’esecuzione in produzione.

---

## 🚀 Prossimi Passi

1. Refactor del `BaseUserDomainController` per includere AuditLogService e ConsentService, con helper standardizzati per logging e consenso.
2. Adozione dell’envelope encryption per i documenti caricati dagli utenti e migrazione degli asset esistenti.
3. Introduzione di health check per i fornitori terzi, con alert automatici in caso di configurazioni non conformi.
4. Estensione della suite di test per coprire tutte le route GDPR/DSAR, prevenendo regressioni future.

---

_Documento destinato alla pianificazione delle prossime azioni GDPR per FlorenceEGI._
