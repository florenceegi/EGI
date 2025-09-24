# QES Implementation Checklist - FlorenceEGI

## 📋 FASE 1: SETUP & CONFIGURAZIONE

### ✅ Conferma Iniziale
- [ ] **QS=QES PAdES** confermato come standard
- [ ] **Provider sandbox** identificato e configurato
- [ ] **Credenziali test** ottenute e verificate
- [ ] **Scope progetto** definito e approvato

### ⚙️ Feature Flags Setup
- [ ] `config/coa.php` aggiornato con sezione signature:
  - [ ] `signature.enabled=false` (default OFF)
  - [ ] `signature.inspector.enabled=false` (default OFF)  
  - [ ] `signature.tsa.enabled=false` (default OFF)
- [ ] Validazione configurazione in tutti gli ambienti
- [ ] Test toggle flags in staging

---

## 📋 FASE 2: ARCHITETTURA BASE

### 🔌 Interfacce Core
- [ ] `SignatureProviderInterface` implementata:
  - [ ] `signPdf($content, $certificate)` method
  - [ ] `addCountersignature($signedPdf, $certificate)` method
  - [ ] `addTimestamp($signedPdf)` method
  - [ ] `verifySignatures($signedPdf)` method
- [ ] Documentazione interfaccia completa
- [ ] Unit tests per interfaccia

### 🧪 Provider Mock (Sandbox)
- [ ] `MockSignatureProvider` implementato
- [ ] Implementazione sandbox **no-legale** con esiti prevedibili
- [ ] Test cases per tutti i metodi mock
- [ ] Logging dettagliato per debugging
- [ ] Simulazione errori e edge cases

### 🎛️ Orchestratore Principale  
- [ ] `SignatureService` implementato
- [ ] Integrazione con ULM/UEM per audit trail
- [ ] Integrazione con Audit Service
- [ ] Gestione Consent per PII minimizzati
- [ ] Error handling e rollback logic
- [ ] Validazione input e sanitizzazione

---

## 📋 FASE 3: STORAGE & METADATI

### 💾 Metadati Strutturati
- [ ] Schema `coa.metadata.signatures[]` definito:
  - [ ] `role` (author/inspector)
  - [ ] `cert_cn` (Common Name)
  - [ ] `cert_serial` (Serial Number)
  - [ ] `provider` (mock/production)
  - [ ] `timestamp` (firma applicata)
  - [ ] `tsa_policy` (se timestamp attivo)
  - [ ] `chain_refs` (riferimenti catena certificati)
  - [ ] `status` (valid/invalid/pending)
- [ ] Migration per aggiornamento schema
- [ ] Backward compatibility verificata

### 🗄️ Content-Addressed Storage
- [ ] Struttura `/coa/{id}/{sha256}.pdf` implementata
- [ ] **No-overwrite policy** applicata
- [ ] Hash verification su download
- [ ] Cleanup automatico file orfani
- [ ] Backup strategy definita

---

## 📋 FASE 4: INTEGRAZIONE WORKFLOW

### ✍️ Hook Autore (Post-PDF)
- [ ] Integrazione in `CoaPdfService`
- [ ] Attivazione solo se `signature.enabled=true`
- [ ] Gestione errori firma (fallback PDF non firmato)
- [ ] Audit trail completo
- [ ] Performance testing

### 👨‍⚖️ Hook Ispettore (Co-firma)
- [ ] Co-firma incrementale PAdES implementata
- [ ] Nuova versione file ad ogni firma
- [ ] Attivazione solo se `signature.inspector.enabled=true`
- [ ] Validazione permessi ispettore
- [ ] Chain of custody tracking

### ⏰ Timestamp RFC3161
- [ ] Integrazione mock TSA
- [ ] Salvataggio token e references
- [ ] Attivazione solo se `signature.tsa.enabled=true`
- [ ] Validazione timestamp in verifica
- [ ] Error recovery per TSA failures

---

## 📋 FASE 5: VERIFICA & UI

### 🔍 Verifica Dati
- [ ] `VerifyPageService` esteso per lettura stato firme
- [ ] Fallback trasparente se firme non presenti
- [ ] Performance ottimizzata per grandi PDF
- [ ] Cache intelligente risultati verifica

### 🌐 API Verifica
- [ ] `VerifyController` esteso con motivazioni firma
- [ ] Response JSON strutturata per firme
- [ ] Rate limiting applicato
- [ ] Documentazione API aggiornata

### 🏷️ UI Badges & Indicators
- [ ] Sidebar-section con indicatori firma
- [ ] Pagina verifica con badges condizionali:
  - [ ] "Firmato Autore (QES)"
  - [ ] "Firmato Perito (QES)"  
- [ ] Responsive design verificato
- [ ] Accessibility (ARIA) implementata
- [ ] Schema.org structured data

---

## 📋 FASE 6: RUOLI & PERMESSI

### 👨‍⚖️ Ruolo Perito
- [ ] Definizione permessi a invito (opera/collection)
- [ ] Sistema scadenza/revoca implementato
- [ ] Tutto protetto da feature flag
- [ ] Audit trail per gestione ruoli
- [ ] UI per amministrazione ruoli

### 📧 Invito Co-firma
- [ ] Endpoint stub protetto con token
- [ ] Consenso privacy integrato
- [ ] Audit completo delle operazioni
- [ ] **Solo sandbox** per testing
- [ ] Email notifications preparate

---

## 📋 FASE 7: TESTING & VALIDAZIONE

### 🧪 Unit Testing
- [ ] `SignatureService` mock tests
- [ ] Provider interface tests
- [ ] Metadata handling tests
- [ ] Error scenarios coverage >90%

### 🔄 Integration Testing
- [ ] End-to-end: firma → co-firma → timestamp → verifica
- [ ] Performance testing con PDF grandi
- [ ] Load testing endpoint critici
- [ ] Security testing completo

### 📚 Documentazione
- [ ] Environment variables e flags
- [ ] Flussi operativi documentati
- [ ] Limiti mock chiariti
- [ ] Ruoli e permessi spiegati
- [ ] Note GDPR/ULM/UEM compliance

---

## 📋 FASE 8: PREPARAZIONE PRODUZIONE

### 🔌 Adapter Skeleton
- [ ] Struttura per provider reali preparata:
  - [ ] Namirial adapter skeleton
  - [ ] InfoCert adapter skeleton  
  - [ ] Aruba adapter skeleton
  - [ ] Intesi adapter skeleton
- [ ] **Disattivato** fino a credenziali reali
- [ ] Configuration management per switch provider

### 🚀 Pilot Sandbox
- [ ] Esecuzione completa in staging
- [ ] Raccolta evidenze e performance metrics
- [ ] User acceptance testing
- [ ] Bug fixing e ottimizzazioni

### 📈 Piano Go-Live
- [ ] Criteri di attivazione definiti
- [ ] Strategia di rollback testata
- [ ] KYC requirements documentati
- [ ] HSM remoto configurato
- [ ] Costi e policy eIDAS approvati

---

## 🛡️ GARANZIE COMPLIANCE

### ✅ Non-Breaking Changes
- [ ] **Feature-flags OFF** di default
- [ ] Nessun impatto sui flussi attuali verificato
- [ ] Backward compatibility al 100%
- [ ] Solo estensioni, zero modifiche breaking

### 🏗️ SOLID Architecture
- [ ] Interfacce per isolamento provider
- [ ] Adapter pattern implementato
- [ ] Accoppiamento ridotto verificato
- [ ] Dependency injection configurato

### 📜 Privacy & Compliance
- [ ] Integrazione ULM/UEM/Audit/Consent
- [ ] **PII minimizzati** per CN/seriale
- [ ] GDPR compliance verificata
- [ ] Consent tracking implementato

---

## 🎯 NEXT ACTIONS

### Priorità Immediata
1. **Setup feature flags** (Fase 1)
2. **Implementare interfacce** (Fase 2)  
3. **Provider mock** (Fase 2)

### Conferme Richieste
- [ ] **QS=QES PAdES** e provider sandbox confermati?
- [ ] **Firma ispettore opzionale** di default OK?
- [ ] **Mock + skeleton adapter** approccio approvato?

---

**Status**: Ready to start  
**Owner**: FlorenceEGI Development Team  
**Target**: Q4 2025  
**Last Update**: 2025-09-24