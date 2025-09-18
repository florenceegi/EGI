# Addendum — CoA Pro (Core + Annessi)

> Estende il documento: **“FlorenceEGI — Piano Tecnico CoA & Traits (per Copilot)”**. 
> Scopo: portare il CoA a livello **professionale/ente** con **Core + Annessi**, mantenendo architettura a snapshot.

---

## 1) Obiettivo
- CoA **Core** (snapshot immutabile + PDF 1 pagina + seriale + hash + firma).
- **Annessi** referenziati dallo snapshot, ciascuno con **hash** e **versione**.
- **Policy**: Re-issue per modifiche sostanziali; **Addendum** per aggiornamenti non sostanziali.

---

## 2) Modello Dati — Delta
### 2.1 Nuove tabelle
```sql
-- coa_annexes: versioning degli annessi collegati a un CoA
id ULID PK,
coa_id ULID NOT NULL FK -> coa(id),
code ENUM('A_PROVENANCE','B_CONDITION','C_EXHIBITIONS','D_PHOTOS') NOT NULL,
version INT NOT NULL DEFAULT 1,
path VARCHAR(255) NOT NULL, -- file singolo (PDF) o ZIP
mime VARCHAR(127) NOT NULL,
bytes BIGINT NULL,
sha256 CHAR(64) NOT NULL,
created_by BIGINT NULL,
created_at DATETIME NOT NULL,
UNIQUE(coa_id, code, version)
```
```sql
-- coa_events: audit trail CoA (emissione, revoca, annessi, addendum)
id ULID PK,
coa_id ULID NOT NULL FK -> coa(id),
type ENUM('ISSUED','REVOKED','ANNEX_ADDED','ADDENDUM_ISSUED') NOT NULL,
payload JSON NULL, -- esito, motivi, elenco file, hash coinvolti
actor_id BIGINT NULL, -- utente piattaforma
created_at DATETIME NOT NULL,
INDEX(coa_id, type, created_at)
```

### 2.2 Estensioni tabelle esistenti
- `coa_files.kind`: aggiungere `core_pdf`, `bundle_pdf` (PDF completo Core + indice annessi), `annex_pack` (ZIP opzionale con materiale pesante), oltre ai kind già previsti.
- `coa_signatures.kind`: già gestisce `qes` | `wallet` | `autograph_scan`. Nessuna modifica obbligatoria.

> **Copilot:** genera migrations per `coa_annexes` e `coa_events`, aggiorna enum `coa_files.kind`.

---

## 3) Regole funzionali
### 3.1 Core vs Annessi
- **Core (snapshot)** contiene e “fa fede” per: titolo, autore, anno, tecnica/supporto, dimensioni, edizione, immagine fronte, dichiarazione, issuer, seriale, hash del core PDF.
- **Annessi**:
  - **A — Provenienza** (catena proprietari + prove)
  - **B — Condition Report** (stato, restauri)
  - **C — Esposizioni e Pubblicazioni** (mostre, cataloghi, ISBN)
  - **D — Dossier Immagini** (retro, firma, dettagli; può essere ZIP)

### 3.2 Politica di re-issue / addendum
- **Re-issue (nuovo seriale, revoca del precedente)** quando cambia uno dei **Core fields** o quando vengono aggiornati in modo sostanziale gli **Annessi A o B**.
- **Addendum (senza revoca)** consentito per aggiornare **C** e **D** (es. nuova mostra, nuove foto). Si crea una nuova **version** dell’annesso, si emette PDF **Addendum** con indice versioni.

> Nota: gli hash (annessi) sono mostrati nel Core e nella Verify page; l’ultima **version** è quella “effective”.

---

## 4) Flussi operativi — Delta
### 4.1 Emissione CoA (aggiornato)
1. Genera Core PDF 1-pagina + QR + `pdf_sha256`.
2. (Opz.) Genera `bundle_pdf` con **Indice Annessi** (nome, versione, sha256) — anche se gli annessi saranno caricati dopo.
3. Firma: **QES** sul `bundle_pdf` se esiste, altrimenti su `core_pdf`. Aggiungi firma **wallet** sul digest.
4. Registra evento `ISSUED` in `coa_events`.

### 4.2 Caricamento/aggiornamento Annessi
1. Upload file annesso → calcolo **SHA-256**, salvataggio in `coa_annexes` con `version = last + 1`.
2. Aggiorna indice nel `bundle_pdf` (rigenera) **oppure** emetti **Addendum PDF** che elenca solo le variazioni.
3. Registra `ANNEX_ADDED` con dettagli.

### 4.3 Addendum
- Genera **Addendum PDF**: riepilogo variazioni (es. C v2→v3), hash nuovi, firma (QES preferita, wallet sempre).
- Registra `ADDENDUM_ISSUED`.

### 4.4 Revoca
- Come da piano base, con `REVOKED` + motivo; la Verify page mostra stato **REVOCATO**.

---

## 5) Rotte — Delta
```txt
# Annex management
POST   /egi/{egi}/coa/{coa}/annex/{code}    -> AnnexController@store   (A|B|C|D)
GET    /egi/{egi}/coa/{coa}/annex           -> AnnexController@index   (lista versioni per code)

# Addendum
POST   /egi/{egi}/coa/{coa}/addendum        -> CoaAddendumController@issue
GET    /coa/{serial}/bundle.pdf             -> CoaController@bundlePdf
GET    /verify/{serial}/events              -> VerifyController@events
```

> **Copilot:** crea controller + request validation. Policy: solo owner/archivio autorizzati.

---

## 6) Services — Delta
- `AnnexService`
  - `addAnnex(Coa $coa, AnnexCode $code, UploadedFile $file): CoaAnnex`
  - Calcola sha256, versione, salva record, emette `AnnexAdded`.
- `CoaAddendumService`
  - Verifica policy (solo C/D), raccoglie delta versioni, render `addendum_pdf`, firma, registra `ADDENDUM_ISSUED`.
- `BundleService`
  - (Opz.) Rigenera `bundle_pdf` con indice annessi correnti.

> **Copilot:** creare interfacce, implementazioni e test stub.

---

## 7) Blade / PDF — Delta
- `resources/views/coa/pdf.blade.php` → aggiungere **Indice Annessi** (tabella: Code, Version, SHA-256) in appendice oppure mantenere 1 pagina e spostare l’indice nel `bundle_pdf`.
- `resources/views/coa/addendum.blade.php` → tabella “Variazioni” (da → a), elenco annessi toccati con nuove versioni/hash.
- `resources/views/verify/show.blade.php` → aggiungere sezione **Annessi** (tabella: Code, Version corrente, SHA-256, link download) e **Timeline** eventi.

---

## 8) Verify Page — Delta (DTO)
```json
{
  "serial": "COA-EGI-2025-000123",
  "status": "valid",
  "core": { "pdf_sha256": "sha256:..." },
  "annexes": [
    {"code":"A_PROVENANCE","version":1,"sha256":"sha256:...","url":"..."},
    {"code":"B_CONDITION","version":1,"sha256":"sha256:...","url":"..."},
    {"code":"C_EXHIBITIONS","version":3,"sha256":"sha256:...","url":"..."},
    {"code":"D_PHOTOS","version":2,"sha256":"sha256:...","url":"..."}
  ],
  "events": [
    {"type":"ISSUED","at":"2025-09-18"},
    {"type":"ANNEX_ADDED","code":"C_EXHIBITIONS","version":2},
    {"type":"ADDENDUM_ISSUED","note":"Aggiunta mostra Palazzo..."}
  ]
}
```

---

## 9) Test — Delta (Pest/PHPUnit)
- Upload **annesso** → crea versione + hash coerente.
- **Addendum** su C/D → genera PDF con delta, firma registrata.
- **Re-issue** tentato su A/B via Addendum → **deve fallire** (policy enforced).
- Verify page mostra **version corrente** e timeline.

---

## 10) Prompt Helper per Copilot (commenti inline)
```php
/// Copilot: crea migration `coa_annexes` e `coa_events` secondo l'Addendum.
/// Copilot: implementa `AnnexService::addAnnex()` con calcolo SHA-256 streaming e versioning per code.
/// Copilot: genera `CoaAddendumService` che compone Addendum PDF con tabella variazioni e firma; logga evento.
/// Copilot: aggiorna CoaController con `bundlePdf()` e VerifyController con `events()`.
/// Copilot: in pdf.blade aggiungi sezione "Indice Annessi" con code/version/sha e QR a /verify/{serial}.
/// Copilot: crea AnnexController (store/index) con policy owner|archive e validazione code in [A,B,C,D].
```

---

## 11) Accettazione (criteri)
- `coa_annexes` versiona correttamente (UNIQUE coerente, version++).
- Addendum PDF scaricabile, contiene delta e hash nuovi.
- Verify page mostra annessi e timeline; hash coerenti con file.
- Policy bloccante su A/B via Addendum.

---

**Fine Addendum.** Allegare questo file a VSCode accanto al piano principale e procedere con le implementazioni Delta.
