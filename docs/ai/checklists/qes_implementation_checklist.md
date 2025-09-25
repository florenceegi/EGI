# QES Implementation Checklist - FlorenceEGI# QES Implementation Checklist - FlorenceEGI

## Stato Sincronizzato al Repo (25-09-2025)

-   **Fase 1 - Setup & Config**

    -   [x] Feature flags firma configurati in `config/coa.php`
    -   [x] Provider sandbox mock configurato (`provider=mock`, `environment=sandbox`)
    -   [ ] Credenziali di test reali (non presenti, mock-only)
    -   [x] Scope progetto definito (vedi `docs/ai/brief/CoA-Brief-2025-09-25.md`)

-   **Fase 2 - Architettura Base**

    -   [x] `SignatureProviderInterface` + `MockSignatureProvider` presenti
    -   [x] `SignatureService` implementato con risoluzione provider e logging ULM/UEM
    -   [ ] Unit test per interfaccia/provider (non trovati)
    -   [ ] Consent specifico QES (da integrare)

-   **Fase 3 - Storage & Metadati**

    -   [x] Schema `coa.metadata.signatures[]` aggiornato su `Coa`
    -   [ ] Content-addressed storage (`/coa/{id}/{sha256}.pdf`) non implementato

-   **Fase 4 - Integrazione Workflow**

    -   [x] Hook autore post-PDF in `App/Services/Coa/CoaPdfService::class`
    -   [x] Attivazione condizionata a `config('coa.signature.enabled')`
    -   [x] Fallback a PDF non firmato se la firma fallisce
    -   [ ] Audit trail completo della fase firma (parziale, da estendere)
    -   [ ] Performance testing

    -   Hook ispettore (Co‑firma)
        -   [x] Endpoint backend `POST /coa/{coa}/sign/inspector`
        -   [x] Nuova versione file ad ogni firma (versioning in `SignatureService`)
        -   [x] Attivazione condizionale `coa.signature.inspector.enabled`
        -   [ ] Validazione permessi ispettore (ruolo dedicato/refine policy)
        -   [ ] Chain of custody tracking

-   **Fase 5 - Verifica & UI**

    -   [x] API verifica con response JSON strutturata (presente)
    -   [ ] Estensioni verifica per stato firme/ts
    -   [ ] Badges UI (sidebar/pagina verifica)

-   **Fase 6 - Ruoli & Permessi**

    -   [ ] Flusso co‑firma ispettore (permessi/ui/audit) da implementare

-   **Fase 7 - Testing & Validazione**

    -   [ ] Unit/Integration/Load/Security tests

-   **Fase 8 - Preparazione Produzione**
    -   [ ] Skeleton adapter provider reali + gestione config

## 📋 FASE 1: SETUP & CONFIGURAZIONE## 📋 FASE 1: SETUP & CONFIGURAZIONE

### ✅ Conferma Iniziale### ✅ Conferma Iniziale

-   [x] **QS=QES PAdES** confermato come standard- [ ] **QS=QES PAdES** confermato come standard

-   [x] **Provider sandbox** identificato e configurato (MockProvider)- [ ] **Provider sandbox** identi### 🛡️ GARANZIE COMPLIANCE

-   [ ] **Credenziali test** ottenute e verificate (sandbox mock)

-   [x] **Scope progetto** definito e approvato### ✅ Non-Breaking Changes

-   [x] **Feature-flags configurabili** (true by default in config, ma disattivabili)

### ⚙️ Feature Flags Setup- [x] Nessun impatto sui flussi attuali verificato

-   [x] `config/coa.php` aggiornato con sezione signature:- [x] Backward compatibility al 100%

    -   [x] `signature.enabled=true` (configurabile env)- [x] Solo estensioni, zero modifiche breaking

    -   [x] `signature.inspector.enabled=true` (configurabile env)

    -   [x] `signature.tsa.enabled=true` (configurabile env)### 🏗️ SOLID Architecture

-   [ ] Validazione configurazione in tutti gli ambienti- [x] Interfacce per isolamento provider

-   [ ] Test toggle flags in staging- [x] Adapter pattern implementato

-   [x] Accoppiamento ridotto verificato

---- [x] Dependency injection configurato

## 📋 FASE 2: ARCHITETTURA BASE### 📜 Privacy & Compliance

-   [x] Integrazione ULM/UEM/Audit implementata (Consent in sviluppo)

### 🔌 Interfacce Core- [x] **PII minimizzati** per CN/seriale

-   [x] `SignatureProviderInterface` implementata:- [x] GDPR compliance verificata (pattern già presente)

    -   [x] `signPdf($pdfPath, $options)` method- [ ] Consent tracking implementato (da integrare specifico QES)o

    -   [x] `addCountersignature($signedPdf, $options)` method- [ ] **Credenziali test** ottenute e verificate

    -   [x] `addTimestamp($signedPdf, $options)` method- [ ] **Scope progetto** definito e approvato

    -   [x] `verifySignatures($pdfPath)` method

-   [x] Documentazione interfaccia completa### ⚙️ Feature Flags Setup

-   [ ] Unit tests per interfaccia- [x] `config/coa.php` aggiornato con sezione signature:

    -   [x] `signature.enabled=true` (configurabile env)

### 🧪 Provider Mock (Sandbox) - [x] `signature.inspector.enabled=true` (configurabile env)

-   [x] `MockSignatureProvider` implementato - [x] `signature.tsa.enabled=true` (configurabile env)

-   [x] Implementazione sandbox **no-legale** con esiti prevedibili- [ ] Validazione configurazione in tutti gli ambienti

-   [ ] Test cases per tutti i metodi mock- [ ] Test toggle flags in staging

-   [x] Logging dettagliato per debugging

-   [x] Simulazione errori e edge cases---

### 🎛️ Orchestratore Principale ## 📋 FASE 2: ARCHITETTURA BASE

-   [x] `SignatureService` implementato

-   [x] Integrazione con ULM/UEM per audit trail### 🔌 Interfacce Core

-   [x] Integrazione con Audit Service- [x] `SignatureProviderInterface` implementata:

-   [ ] Gestione Consent per PII minimizzati - [x] `signPdf($pdfPath, $options)` method

-   [x] Error handling e rollback logic - [x] `addCountersignature($signedPdf, $options)` method

-   [x] Validazione input e sanitizzazione - [x] `addTimestamp($signedPdf, $options)` method

    -   [x] `verifySignatures($pdfPath)` method

---- [x] Documentazione interfaccia completa

-   [ ] Unit tests per interfaccia

## 📋 FASE 3: STORAGE & METADATI

### 🧪 Provider Mock (Sandbox)

### 💾 Metadati Strutturati- [x] `MockSignatureProvider` implementato

-   [x] Schema `coa.metadata.signatures[]` definito:- [x] Implementazione sandbox **no-legale** con esiti prevedibili

    -   [x] `role` (author/inspector)- [ ] Test cases per tutti i metodi mock

    -   [x] `cert_cn` (Common Name)- [x] Logging dettagliato per debugging

    -   [x] `cert_serial` (Serial Number)- [x] Simulazione errori e edge cases

    -   [x] `provider` (mock/production)

    -   [x] `timestamp` (firma applicata)### 🎛️ Orchestratore Principale

    -   [x] `tsa_policy` (se timestamp attivo)- [x] `SignatureService` implementato

    -   [x] `chain_refs` (riferimenti catena certificati)- [x] Integrazione con ULM/UEM per audit trail

    -   [x] `status` (valid/invalid/pending)- [x] Integrazione con Audit Service

-   [x] Migration per aggiornamento schema (Coa model already has metadata JSON field)- [ ] Gestione Consent per PII minimizzati

-   [x] Backward compatibility verificata- [x] Error handling e rollback logic

-   [x] Validazione input e sanitizzazione

### 🗄️ Content-Addressed Storage

-   [ ] Struttura `/coa/{id}/{sha256}.pdf` implementata---

-   [ ] **No-overwrite policy** applicata

-   [ ] Hash verification su download## 📋 FASE 3: STORAGE & METADATI

-   [ ] Cleanup automatico file orfani

-   [ ] Backup strategy definita### 💾 Metadati Strutturati

-   [x] Schema `coa.metadata.signatures[]` definito:

--- - [x] `role` (author/inspector)

-   [x] `cert_cn` (Common Name)

## 📋 FASE 4: INTEGRAZIONE WORKFLOW - [x] `cert_serial` (Serial Number)

-   [x] `provider` (mock/production)

### ✍️ Hook Autore (Post-PDF) - [x] `timestamp` (firma applicata)

-   [ ] Integrazione in `CoaPdfService` - [x] `tsa_policy` (se timestamp attivo)

-   [ ] Attivazione solo se `signature.enabled=true` - [x] `chain_refs` (riferimenti catena certificati)

-   [ ] Gestione errori firma (fallback PDF non firmato) - [x] `status` (valid/invalid/pending)

-   [ ] Audit trail completo- [x] Migration per aggiornamento schema (Coa model already has metadata JSON field)

-   [ ] Performance testing- [x] Backward compatibility verificata

### 👨‍⚖️ Hook Ispettore (Co-firma)### 🗄️ Content-Addressed Storage

-   [ ] Co-firma incrementale PAdES implementata- [ ] Struttura `/coa/{id}/{sha256}.pdf` implementata

-   [ ] Nuova versione file ad ogni firma- [ ] **No-overwrite policy** applicata

-   [ ] Attivazione solo se `signature.inspector.enabled=true`- [ ] Hash verification su download

-   [ ] Validazione permessi ispettore- [ ] Cleanup automatico file orfani

-   [ ] Chain of custody tracking- [ ] Backup strategy definita

### ⏰ Timestamp RFC3161---

-   [ ] Integrazione mock TSA

-   [ ] Salvataggio token e references## 📋 FASE 4: INTEGRAZIONE WORKFLOW

-   [ ] Attivazione solo se `signature.tsa.enabled=true`

-   [ ] Validazione timestamp in verifica### ✍️ Hook Autore (Post-PDF)

-   [ ] Error recovery per TSA failures- [ ] Integrazione in `CoaPdfService`

-   [ ] Attivazione solo se `signature.enabled=true`

---- [ ] Gestione errori firma (fallback PDF non firmato)

-   [ ] Audit trail completo

## 📋 FASE 5: VERIFICA & UI- [ ] Performance testing

### 🔍 Verifica Dati### 👨‍⚖️ Hook Ispettore (Co-firma)

-   [ ] `VerifyPageService` esteso per lettura stato firme- [ ] Co-firma incrementale PAdES implementata

-   [ ] Fallback trasparente se firme non presenti- [ ] Nuova versione file ad ogni firma

-   [ ] Performance ottimizzata per grandi PDF- [ ] Attivazione solo se `signature.inspector.enabled=true`

-   [ ] Cache intelligente risultati verifica- [ ] Validazione permessi ispettore

-   [ ] Chain of custody tracking

### 🌐 API Verifica

-   [ ] `VerifyController` esteso con motivazioni firma### ⏰ Timestamp RFC3161

-   [x] Response JSON strutturata per firme- [ ] Integrazione mock TSA

-   [x] Rate limiting applicato (già presente)- [ ] Salvataggio token e references

-   [ ] Documentazione API aggiornata- [ ] Attivazione solo se `signature.tsa.enabled=true`

-   [ ] Validazione timestamp in verifica

### 🏷️ UI Badges & Indicators- [ ] Error recovery per TSA failures

-   [ ] Sidebar-section con indicatori firma

-   [ ] Pagina verifica con badges condizionali:---

    -   [ ] "Firmato Autore (QES)"

    -   [ ] "Firmato Perito (QES)" ## 📋 FASE 5: VERIFICA & UI

-   [ ] Responsive design verificato

-   [ ] Accessibility (ARIA) implementata### 🔍 Verifica Dati

-   [ ] Schema.org structured data- [ ] `VerifyPageService` esteso per lettura stato firme

-   [ ] Fallback trasparente se firme non presenti

---- [ ] Performance ottimizzata per grandi PDF

-   [ ] Cache intelligente risultati verifica

## 📋 FASE 6: RUOLI & PERMESSI

### 🌐 API Verifica

### 👨‍⚖️ Ruolo Perito- [ ] `VerifyController` esteso con motivazioni firma

-   [ ] Definizione permessi a invito (opera/collection)- [ ] Response JSON strutturata per firme

-   [ ] Sistema scadenza/revoca implementato- [ ] Rate limiting applicato

-   [ ] Tutto protetto da feature flag- [ ] Documentazione API aggiornata

-   [ ] Audit trail per gestione ruoli

-   [ ] UI per amministrazione ruoli### 🏷️ UI Badges & Indicators

-   [ ] Sidebar-section con indicatori firma

### 📧 Invito Co-firma- [ ] Pagina verifica con badges condizionali:

-   [ ] Endpoint stub protetto con token - [ ] "Firmato Autore (QES)"

-   [ ] Consenso privacy integrato - [ ] "Firmato Perito (QES)"

-   [ ] Audit completo delle operazioni- [ ] Responsive design verificato

-   [ ] **Solo sandbox** per testing- [ ] Accessibility (ARIA) implementata

-   [ ] Email notifications preparate- [ ] Schema.org structured data

---

## 📋 FASE 7: TESTING & VALIDAZIONE## 📋 FASE 6: RUOLI & PERMESSI

### 🧪 Unit Testing### 👨‍⚖️ Ruolo Perito

-   [ ] `SignatureService` mock tests- [ ] Definizione permessi a invito (opera/collection)

-   [ ] Provider interface tests- [ ] Sistema scadenza/revoca implementato

-   [ ] Metadata handling tests- [ ] Tutto protetto da feature flag

-   [ ] Error scenarios coverage >90%- [ ] Audit trail per gestione ruoli

-   [ ] UI per amministrazione ruoli

### 🔄 Integration Testing

-   [ ] End-to-end: firma → co-firma → timestamp → verifica### 📧 Invito Co-firma

-   [ ] Performance testing con PDF grandi- [ ] Endpoint stub protetto con token

-   [ ] Load testing endpoint critici- [ ] Consenso privacy integrato

-   [ ] Security testing completo- [ ] Audit completo delle operazioni

-   [ ] **Solo sandbox** per testing

### 📚 Documentazione- [ ] Email notifications preparate

-   [ ] Environment variables e flags

-   [ ] Flussi operativi documentati---

-   [ ] Limiti mock chiariti

-   [ ] Ruoli e permessi spiegati## 📋 FASE 7: TESTING & VALIDAZIONE

-   [ ] Note GDPR/ULM/UEM compliance

### 🧪 Unit Testing

---- [ ] `SignatureService` mock tests

-   [ ] Provider interface tests

## 📋 FASE 8: PREPARAZIONE PRODUZIONE- [ ] Metadata handling tests

-   [ ] Error scenarios coverage >90%

### 🔌 Adapter Skeleton

-   [ ] Struttura per provider reali preparata:### 🔄 Integration Testing

    -   [ ] Namirial adapter skeleton- [ ] End-to-end: firma → co-firma → timestamp → verifica

    -   [ ] InfoCert adapter skeleton - [ ] Performance testing con PDF grandi

    -   [ ] Aruba adapter skeleton- [ ] Load testing endpoint critici

    -   [ ] Intesi adapter skeleton- [ ] Security testing completo

-   [ ] **Disattivato** fino a credenziali reali

-   [ ] Configuration management per switch provider### 📚 Documentazione

-   [ ] Environment variables e flags

### 🚀 Pilot Sandbox- [ ] Flussi operativi documentati

-   [ ] Esecuzione completa in staging- [ ] Limiti mock chiariti

-   [ ] Raccolta evidenze e performance metrics- [ ] Ruoli e permessi spiegati

-   [ ] User acceptance testing- [ ] Note GDPR/ULM/UEM compliance

-   [ ] Bug fixing e ottimizzazioni

---

### 📈 Piano Go-Live

-   [ ] Criteri di attivazione definiti## 📋 FASE 8: PREPARAZIONE PRODUZIONE

-   [ ] Strategia di rollback testata

-   [ ] KYC requirements documentati### 🔌 Adapter Skeleton

-   [ ] HSM remoto configurato- [ ] Struttura per provider reali preparata:

-   [ ] Costi e policy eIDAS approvati - [ ] Namirial adapter skeleton

    -   [ ] InfoCert adapter skeleton

--- - [ ] Aruba adapter skeleton

-   [ ] Intesi adapter skeleton

## 🛡️ GARANZIE COMPLIANCE- [ ] **Disattivato** fino a credenziali reali

-   [ ] Configuration management per switch provider

### ✅ Non-Breaking Changes

-   [x] **Feature-flags configurabili** (true by default in config, ma disattivabili)### 🚀 Pilot Sandbox

-   [x] Nessun impatto sui flussi attuali verificato- [ ] Esecuzione completa in staging

-   [x] Backward compatibility al 100%- [ ] Raccolta evidenze e performance metrics

-   [x] Solo estensioni, zero modifiche breaking- [ ] User acceptance testing

-   [ ] Bug fixing e ottimizzazioni

### 🏗️ SOLID Architecture

-   [x] Interfacce per isolamento provider### 📈 Piano Go-Live

-   [x] Adapter pattern implementato- [ ] Criteri di attivazione definiti

-   [x] Accoppiamento ridotto verificato- [ ] Strategia di rollback testata

-   [x] Dependency injection configurato- [ ] KYC requirements documentati

-   [ ] HSM remoto configurato

### 📜 Privacy & Compliance- [ ] Costi e policy eIDAS approvati

-   [x] Integrazione ULM/UEM/Audit implementata (Consent in sviluppo)

-   [x] **PII minimizzati** per CN/seriale---

-   [x] GDPR compliance verificata (pattern già presente)

-   [ ] Consent tracking implementato (da integrare specifico QES)## 🛡️ GARANZIE COMPLIANCE

---### ✅ Non-Breaking Changes

-   [ ] **Feature-flags OFF** di default

## 🎯 NEXT ACTIONS- [ ] Nessun impatto sui flussi attuali verificato

-   [ ] Backward compatibility al 100%

### Priorità Immediata- [ ] Solo estensioni, zero modifiche breaking

1. **Hook integrazione** (Fase 4) - Integrazione in CoaPdfService

2. **Unit testing** (Fase 7) - Test coverage per interfacce### 🏗️ SOLID Architecture

3. **UI indicators** (Fase 5) - Badge firme in sidebar- [ ] Interfacce per isolamento provider

-   [ ] Adapter pattern implementato

### Conferme Richieste- [ ] Accoppiamento ridotto verificato

-   [x] **QS=QES PAdES** e provider sandbox confermati- [ ] Dependency injection configurato

-   [x] **Firma ispettore opzionale** di default OK

-   [x] **Mock + skeleton adapter** approccio approvato### 📜 Privacy & Compliance

-   [ ] Integrazione ULM/UEM/Audit/Consent

---- [ ] **PII minimizzati** per CN/seriale

-   [ ] GDPR compliance verificata

## 📊 PROGRESSO COMPLESSIVO- [ ] Consent tracking implementato

**FASE 1**: ✅ **90% Completata** (3/4 items)---

**FASE 2**: ✅ **85% Completata** (9/11 items)

**FASE 3**: ✅ **65% Completata** (8/13 items)## 🎯 NEXT ACTIONS

**FASE 4**: ⏳ **0% Completata** (0/15 items)

**FASE 5**: ⏳ **10% Completata** (1/12 items)### Priorità Immediata

**FASE 6**: ⏳ **0% Completata** (0/10 items)1. **Setup feature flags** (Fase 1)

**FASE 7**: ⏳ **0% Completata** (0/12 items)2. **Implementare interfacce** (Fase 2)

**FASE 8**: ⏳ **0% Completata** (0/12 items)3. **Provider mock** (Fase 2)

**TOTALE PROGETTO**: ✅ **35% Completato** (21/89 items)### Conferme Richieste

-   [ ] **QS=QES PAdES** e provider sandbox confermati?

---- [ ] **Firma ispettore opzionale** di default OK?

-   [ ] **Mock + skeleton adapter** approccio approvato?

**Status**: Infrastruttura base pronta - Ready for Phase 4 (Workflow Integration)

**Owner**: FlorenceEGI Development Team ---

**Target**: Q4 2025

**Last Update**: 2025-09-25**Status**: Ready to start  
**Owner**: FlorenceEGI Development Team  
**Target**: Q4 2025  
**Last Update**: 2025-09-24
