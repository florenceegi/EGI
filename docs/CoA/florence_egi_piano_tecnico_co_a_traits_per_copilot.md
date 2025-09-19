# FlorenceEGI — Piano Tecnico CoA & Traits (per Copilot)

> Obiettivo: integrare **CoA (Certificato di Autenticità)** con la struttura **EGI + Traits**, mantenendo i due mondi separati ma collegati da uno **snapshot** immutabile. Documento scritto per essere “mangiabile” da Copilot in VSCode.

---

## 1) Cosa vogliamo creare
- Un **flusso CoA** completo:
  1. **Snapshot** dei dati minimi (titolo, anno, tecnica/supporto, dimensioni, edizione, immagini, dichiarazione, issuer, luogo/data, seriale).
  2. **PDF 1-pagina** del CoA (layout pulito, QR di verifica, seriale, hash in chiaro).
  3. **Hash SHA-256** di PDF e file chiave (immagine master).
  4. **Firma**: supporto a **QES** (quando disponibile) e **firma wallet Algorand** del digest (strato tecnico).
  5. **Pagina pubblica di verifica** `/verify/<serial>` con stato **valid/ revoked**, dati essenziali, hash, eventuale **asset_id** on-chain.
  6. **Revoca & Ri-emissione** (nuovo seriale) se cambiano i dati identificativi.

- **Traits** restano **mutabili/versionati** e non “fanno fede”. Il CoA **congela** una copia dei campi rilevanti all’emissione.

---

## 2) Cosa riutilizziamo / sfruttiamo
- **EGI esistenti** (model, services, controllers).
- **Traits** (già strutturati con vocabolario controllato + “Altro”).
- **Storage** (S3/DO Spaces) per PDF, immagini, manifest JSON.
- **Node/TS microservice Algorand** per firma/verifica del digest (SDK JS/AlgoKit).
- **Laravel 12**: Jobs, Events, Policies, Request Validation, Resource/Responder.

---

## 3) Cosa è necessario fare (backlog tecnico)
### 3.1) Dati & Migrations
Creare nuove tabelle dedicate al dominio CoA.

```sql
-- coa (stato dell’emissione)
id ULID PK,
egi_id FK, -- riferimento all’opera/EGI
serial VARCHAR(64) UNIQUE NOT NULL, -- es. COA-EGI-2025-000123
status ENUM('valid','revoked') DEFAULT 'valid',
issuer_type ENUM('author','archive','platform') DEFAULT 'author',
issuer_name VARCHAR(190) NOT NULL, -- chi emette (autore/ente)
issuer_location VARCHAR(190) NULL,
issued_at DATETIME NOT NULL,
revoked_at DATETIME NULL,
revoke_reason VARCHAR(255) NULL,
created_at, updated_at
```
```sql
-- coa_snapshot (immutabile, 1:1 con coa)
coa_id FK UNIQUE,
snapshot_json JSON NOT NULL, -- copia minima dei campi che fanno fede
created_at
```
```sql
-- coa_files (file collegati)
id PK,
coa_id FK,
kind ENUM('pdf','scan_signed','image_front','image_back','signature_detail') NOT NULL,
path VARCHAR(255) NOT NULL,
sha256 CHAR(64) NOT NULL,
bytes BIGINT NULL,
created_at
```
```sql
-- coa_signatures (tracce di firma)
id PK,
coa_id FK,
kind ENUM('qes','autograph_scan','wallet') NOT NULL,
provider VARCHAR(120) NULL, -- es. Namirial/InfoCert per QES
payload JSON NULL, -- manifest QES o metadati
pubkey VARCHAR(128) NULL, -- per wallet
signature_base64 TEXT NULL, -- firma del digest
created_at
```
```sql
-- egi_traits_version (audit dei traits “vivi”)
id PK,
egi_id FK,
version INT NOT NULL,
traits_json JSON NOT NULL,
created_by BIGINT NULL,
created_at
```

> **Copilot:** genera migrations Laravel per le 5 tabelle sopra, con FK, indici e enum gestiti via string + `check` dove serve.

---

### 3.2) Modelli & Relazioni
- `Egi hasMany Coa`
- `Coa belongsTo Egi`
- `Coa hasOne CoaSnapshot`
- `Coa hasMany CoaFile`
- `Coa hasMany CoaSignature`
- `Egi hasMany EgiTraitsVersion`

> **Copilot:** crea i Model con cast JSON, relations e factory basiche.

---

### 3.3) Services (Laravel)
- `TraitsSnapshotService`  
  - Input: `Egi $egi`
  - Output: `array snapshot` (solo campi CoA: titolo, anno, tecnica/supporto, dimensioni, edizione, immagini selezionate, dichiarazione, issuer, luogo/data).
  - Congela valori “risolti” dai traits correnti.

- `SerialGenerator`  
  - Regola: `COA-<EGI|CREATOR>-<YYYY>-<000###>` per anno e contatore progressivo per autore/anno.

- `CoaIssueService`  
  - Crea `Coa`, `CoaSnapshot`.
  - Genera **PDF** (Blade → DomPDF/wkhtmltopdf), aggiunge **QR** con URL verifica, seriale, hash placeholder.
  - Calcola **SHA-256** del PDF e dei file collegati, salva in `coa_files`.
  - Emette evento `CoaIssued`.

- `CoaRevocationService`  
  - Cambia `status` a `revoked`, imposta `revoked_at`, `revoke_reason`, emette `CoaRevoked`.

- `HashingService`  
  - Calcola SHA-256 streaming, ritorna hex.

- `SignatureService`  
  - **QES:** per ora stub (interfaccia + adapter futuri).
  - **Wallet:** chiama microservizio Node `POST /sign` con `{digest, account}` e salva firma/pubkey.

- `VerifyPageService`  
  - Fornisce DTO per `/verify/<serial>`: stato, snapshot ridotto, hash, asset id.

> **Copilot:** crea i service con interfacce, implementazioni, test stub.

---

### 3.4) Controllers & Routes
**Routes (api + web)**
```txt
POST   /egi/{egi}/coa/issue            -> CoaController@issue  (policy: owner|archive)
POST   /egi/{egi}/coa/revoke           -> CoaController@revoke (policy + motivo)
GET    /egi/{egi}/coa                  -> CoaController@index  (lista CoA per EGI)
GET    /coa/{serial}/pdf               -> CoaController@pdf    (serve il PDF)
GET    /verify/{serial}                -> VerifyController@show (pubblico)
```

**Controller logica**
- `issue(egi)`:
  1. Valida input issuer (name, location, date) + dichiarazione.
  2. `snapshot = TraitsSnapshotService::make($egi)`
  3. `serial = SerialGenerator::next($egi)`
  4. Crea record `Coa`, `CoaSnapshot`.
  5. Genera PDF (`CoaPdfView`) con QR a `/verify/<serial>`.
  6. Calcola hash e salva su `coa_files` (kind=pdf).
  7. Se richiesto: avvia firma wallet (job async) → salva su `coa_signatures`.
  8. Ritorna DTO `CoaResource`.

- `revoke(egi)`:
  - Motivo obbligatorio, set `status=revoked`.

> **Copilot:** crea controller, request objects, policy, resources.

---

### 3.5) Blade & PDF
- `resources/views/coa/pdf.blade.php`  
  - Layout minimal, branding FlorenceEGI, dati snapshot, **seriale**, **hash PDF**, **QR** (URL verifica), firma spazio (se autografa) + nota su QES.
- `resources/views/coa/issue.blade.php`  
  - Preview snapshot, campi issuer, check “aggiungi firma wallet”.
- `resources/views/verify/show.blade.php`  
  - Stato (valid/revoked), dati chiave, hash, link scarica PDF, eventuale `asset_id`.

> **Copilot:** genera Blade con componenti Tailwind, helper per QR (es. BaconQrCode) e partial per “dichiarazione”.

---

## 4) Invarianti & Regole
- **CoA ≠ Traits**: CoA usa **snapshot** congelato; i traits possono cambiare.
- **Re-issue obbligatorio** se cambiano: titolo, anno, tecnica/supporto, dimensioni, edizione.
- **Seriale univoco** e **immutabile**.
- **Hash sempre visibile** nel PDF e nella pagina verify.
- **Firma**: supporta `qes` (quando disponibile), `wallet` (digest); `autograph_scan` come fallback transitorio.

---

## 5) Microservizio Algorand (stub)
**Endpoint previsto**
```
POST /sign
Body: { "digestHex": "<64-hex>", "account": "<addr>" }
→ 200 { "pubkey": "...", "signature_base64": "...", "algo": "ed25519" }

POST /verify
Body: { "digestHex": "...", "pubkey": "...", "signature_base64": "..." }
→ 200 { "valid": true }
```

> **Copilot:** crea client TS/axios lato Laravel (via bridge o job) e salvataggio in `coa_signatures`.

---

## 6) Test & Accettazione
**Unit / Feature (Pest/PHPUnit)**
- Generazione **seriale** (ordine, univocità per anno/autore).
- Creazione **snapshot** coerente con traits correnti.
- **Issue** CoA: crea record, genera PDF, calcola hash, stato `valid`.
- **Revoca**: stato `revoked`, verify page mostra REVOCATO.
- **Verify**: `/verify/<serial>` risponde 200 con dati attesi.
- **Firma wallet**: dato digest, salva firma e verifica positiva.

**Criteri done**
- PDF 1-pagina scaricabile, con QR funzionante.
- Hash nel PDF = Hash su verify page.
- Revoca visibile pubblicamente.

> **Copilot:** genera test scheletro con factories e storage fake.

---

## 7) Rollout
1. Migrations + seed vocabolario tecnica/materiali/supporto (già pronto).
2. Feature flag `features.coa=true`.
3. Rotte protette in admin per **Issue** e **Revoke**.
4. Monitoraggio: log eventi `CoaIssued`, `CoaRevoked`.

---

## 8) Futuri step (non-bloccanti)
- Integrazione **QES** (provider italiano), firma visibile nel PDF.
- Commit on-chain dell’hash (ARC-19/69 metadata o tx note).
- Import/merge CoA esterni (PDF firmati caricati dal creator).
- API pubblica `GET /api/coa/{serial}` con CORS read-only.

---

## 9) Prompt helper per Copilot (commenti inline)
Usa questi commenti nei file per farti completare da Copilot.

```php
/// Copilot: crea migration per tabella `coa` con le colonne descritte nel piano tecnico, FK su `egi` e indice univoco su `serial`.
/// Copilot: implementa `SerialGenerator::next(Egi $egi)` con formato COA-<CREATOR>-<YYYY>-<000###>.
/// Copilot: scrivi `TraitsSnapshotService` che risolve dai traits i campi CoA minimi e ritorna array pronto per il PDF.
/// Copilot: genera `CoaIssueService` con step: crea Coa+Snapshot → render PDF (Blade) → calcola SHA-256 → salva in coa_files → dispatch CoaIssued.
/// Copilot: aggiungi route `/verify/{serial}` e controller che restituisce view pubblica con stato e hash.
/// Copilot: implementa job `RequestWalletSignature` che invia il digest al microservizio e salva la firma in `coa_signatures`.
```

---

## 10) Mini esempio di `snapshot_json`
```json
{
  "work": {
    "title": "Untitled #3",
    "year": "2024",
    "technique": ["pigment inkjet print"],
    "support": ["rag paper"],
    "dimensions_cm": {"h": 70, "w": 50},
    "edition": "3/20"
  },
  "images": {
    "front": "ipfs://.../front.jpg",
    "signature_detail": "ipfs://.../sig.jpg"
  },
  "declaration": "Attesto che l’opera sopra descritta è autentica...",
  "issuer": {"name": "Nome Autore", "location": "Firenze", "date": "2025-09-18"},
  "serial": "COA-EGI-2025-000123"
}
```

---

**Fine.** Questo è il piano. Procedi con migrations → services → controller → Blade → test. Quando tutto gira, attiva la flag `features.coa`. 

