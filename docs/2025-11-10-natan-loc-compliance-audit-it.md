# Audit di Conformità NATAN_LOC

-   **Data:** 10 novembre 2025  
-   **Auditor:** Padmin D. Curtis (AI Partner OS3.0)

---

## ✅ Implementazioni Esistenti

-   Il servizio chat principale integra completamente ULM, UEM, ConsentService e AuditLogService, con logging strutturato e controlli di consenso prima di ogni interazione AI.

    -   File di riferimento: `laravel_backend/app/Services/NatanChatService.php`

-   Il motore RAG effettua l’accesso ai dati solo dopo il controllo dei consensi, sanitizza gli atti prima dell’uso e registra audit dettagliati.

    -   File di riferimento: `laravel_backend/app/Services/RagService.php`

-   Il nuovo orchestratore USE coordina Laravel e il gateway Python con controlli di consenso, audit GDPR e gestione errori UEM in ogni fase della pipeline.

    -   File di riferimento: `laravel_backend/app/Services/USE/UseOrchestrator.php`

-   L’esecuzione degli scraper Python passa da un service dedicato che costruisce comandi sanificati (`escapeshellarg`), gestisce timeout, audit trail e logging completo.

    -   File di riferimento: `laravel_backend/app/Services/PythonScraperService.php`

-   Il controller degli scraper (UI) replica il pattern ULM/UEM/Audit, rendendo tracciabile ogni azione dell’utente e collegando i log ai risultati MongoDB.

    -   File di riferimento: `laravel_backend/app/Http/Controllers/NatanScrapersController.php`

-   L’infrastruttura GDPR riutilizza lo stack EGI: AuditLogService, ConsentService, DataSanitizerService e GdprActivityCategory sono già presenti con retention esplicita e mascheramento IP.

    -   File di riferimento: `laravel_backend/app/Services/Gdpr/AuditLogService.php`

-   Il gateway Python centralizza il logging all’avvio e organizza i router FastAPI per AI/chat/USE, permettendo tracciabilità server-side delle richieste.

    -   File di riferimento: `python_ai_service/app/main.py`

---

## ⚠️ Implementazioni Parziali o Mancanti

-   I controller CRUD principali (DocumentController, TenantController, UserController) non iniettano UltraLogManager, ErrorManager, AuditLogService né ConsentService: le operazioni su dati sensibili avvengono senza audit trail né gestione errori strutturata.

    -   File di riferimento: `laravel_backend/app/Http/Controllers/DocumentController.php`, `TenantController.php`, `UserController.php`

-   Anche l’autenticazione multi-tenant non registra azioni in AuditLogService e si affida a `Auth::attempt` senza UEM in caso di errori o tentativi falliti.

    -   File di riferimento: `laravel_backend/app/Http/Controllers/Auth/AuthController.php`

-   Il salvataggio conversazioni usa log diretti `\Log::info`, manca di ULM/UEM e non esegue audit GDPR per messaggi AI archiviati.

    -   File di riferimento: `laravel_backend/app/Http/Controllers/NatanConversationController.php`

-   L’estrazione delle statistiche MongoDB usa `shell_exec` concatenando direttamente l’anno; oggi è passato server-side, ma per sicurezza serve forzare `escapeshellarg` e whitelist dei parametri.

    -   File di riferimento: `laravel_backend/app/Http/Controllers/StatisticsController.php`

-   Nessuna cifratura a riposo per documenti e metadata sensibili in Laravel (`PaAct` memorizza path/hash in chiaro, assenza totale di `Crypt::encrypt*` nel progetto).

    -   File di riferimento: `laravel_backend/app/Models/PaAct.php` e mancanza di uso `Crypt::`

-   Il gateway Python abilita CORS `allow_origins=["*"]` anche in produzione, esponendo API AI a origini non autorizzate.

    -   File di riferimento: `python_ai_service/app/main.py`

-   Il router FastAPI per la chat è privo di audit/logging dedicato e non anonimizza i messaggi prima di inviarli agli adapter, demandando tutta la compliance al layer Laravel.

    -   File di riferimento: `python_ai_service/app/routers/chat.py`

-   I servizi terzi mantengono la default region `us-east-1` per SES e non documentano il controllo DPA/region-lock sulle chiavi Python (Perplexity/OpenAI/Anthropic).

    -   File di riferimento: `laravel_backend/config/services.php`, `python_ai_service` adapters

-   La cartella `storage/` nel progetto contiene asset e log locali non cifrati; manca una policy di rotazione/retention condivisa con il layer GDPR.

    -   Directory: `storage/` (Laravel) e `python_ai_service/app/scripts` output progress

---

## 🧩 Raccomandazioni per la Conformità Completa

1. Estendere il pattern ULM/UEM/AuditLogService/ConsentService a tutti i controller CRUD (Documenti, Tenant, Utenti, Autenticazione), riutilizzando un BaseController condiviso o trait OS3.
2. Aggiungere audit trail e gestione errori alla login/logout: log su ULM, audit su `GdprActivityCategory::AUTHENTICATION`, gestione fallimenti via UEM.
3. Proteggere i documenti caricati (path e contenuto) con envelope encryption e firmare le versioni storiche tramite hash registrati in blockchain/mongo.
4. Rendere sicura l’esecuzione degli script: usare sempre `escapeshellarg` per parametri dinamici, limitare gli input temporali a whitelist e validare path.
5. Limitare CORS nel gateway Python alle origini NATAN nette, introducendo API key o mutual TLS fra Laravel e FastAPI.
6. Centralizzare audit/logging lato FastAPI (decorator/ middleware) per registrare ogni richiesta con tenant, modello selezionato e costo stimato.
7. Convalidare regioni e DPA delle chiavi cloud: forzare endpoint EU (es. `eu-west-1`) e documentare i contratti di trattamento; introdurre health check automatici.
8. Definire una politica di retention/rotation per i file in `storage/` e per i progress JSON generati dagli scraper, allineandola ai 7 anni richiesti dallo stack GDPR.

---

## 📊 Mappa delle Priorità

| Gap                                                                 | Priorità    | Note                                                                                                    |
| ------------------------------------------------------------------- | ----------- | ------------------------------------------------------------------------------------------------------- |
| Controller CRUD senza ULM/UEM/Audit/ConsentService                  | **Critica** | Operazioni su dati personali senza logging né gestione errori enterprise.                              |
| Nessuna cifratura a riposo per documenti/metadati sensibili         | **Critica** | I file e i path restano in chiaro nel filesystem/database multi-tenant.                                 |
| Login/log-out senza audit o gestione errori centralizzata           | **Critica** | Accessi non tracciati e assenza di alert in caso di tentativi falliti o abusi.                          |
| CORS `*` e assenza di autenticazione sul gateway Python             | **Media**   | Rischio esposizione pubblica API AI; limitare alle origini di piattaforma.                              |
| `shell_exec` con parametri concatenati senza escaping completo      | **Media**   | Possibili command injection futuri (soprattutto per `--year` o altri flag dinamici).                    |
| Router FastAPI privo di audit/logging dedicato                      | **Media**   | Nessuna visibilità sulle richieste AI lato microservizio.                                               |
| Config servizi terzi senza hardening su regioni/DPA                 | **Media**   | Default US-East per SES e mancanza di documentazione su Perplexity/OpenAI/Anthropic lato Python.       |
| Retention assente per log/progress in `storage/`                    | **Bassa**   | Possibile accumulo di PII/metadata non necessari nel filesystem condiviso.                              |

---

## 🔎 Verifiche Manuali Necessarie

-   Confermare l’assenza di trait o helper che forniscano già `handleError()`/`logUserAction()` ai controller (se non esistono, implementarli).
-   Verificare `.env` e segreti per assicurare endpoint UE e firme DPA su SES, Postmark, OpenAI, Anthropic, Perplexity.
-   Valutare le directory `storage/` e `python_ai_service/app/scripts` per eliminare artefatti o log contenenti dati sensibili.
-   Controllare i livelli di log di UltraLogManager per garantire che non vengano stampati contenuti confidenziali in produzione.

---

## 🚀 Prossimi Passi

1. Refactor immediato dei controller CRUD per applicare lo stesso pattern ULM/UEM/Audit/ConsentService già presente nei servizi core.
2. Implementare envelope encryption (KMS) per i documenti in `PaAct` e predisporre una migrazione di cifratura retroattiva.
3. Limitare il gateway Python a origini e credenziali controllate, aggiungendo middleware di audit sulle route FastAPI.
4. Eseguire un hardening delle configurazioni cloud (regioni UE, DPA) e documentare il processo nel repository NATAN_LOC.
5. Definire e automatizzare la retention dei file di output/log (progress scraper, storage locale, log Python).

---

_Documento destinato alla pianificazione delle prossime azioni NATAN_LOC._



















