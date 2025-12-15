# FlorenceEGI – EGI Master Clonable System  
### Documentazione Tecnica Ufficiale  
### Versione: 2.0  
### Stato: IMPLEMENTATO (Step 1 – Clonazione Master → Child)

---

## 1. Scopo del documento

Questo documento definisce in modo ufficiale la funzionalità **EGI Master Clonable**, che consente la creazione di EGI figli partendo da un EGI configurato come *Master/Template*.

**Aggiornamento v2.0**: Include il flusso di **Direct Mint**, dove l'acquisto di un Master EGI (tramite "Paga Ora") innesca automaticamente la clonazione e il minting del figlio, preservando il Master originale come template.

---

## 2. Descrizione generale della feature

La funzionalità permette a un Creator di:

1. Creare un EGI normale.  
2. Contrassegnarlo come **Master clonabile** tramite l’interfaccia amministrativa.  
3. Mettere in "vendita" il Master (simbolicamente).
4. Quando un utente acquista il Master:
   - Il sistema **CLONA** istantaneamente il Master in un nuovo Child EGI (assegnato all'acquirente).
   - Il processo di pagamento e minting prosegue sul **Child**.
   - Il Master rimane intatto e disponibile per future clonazioni.

Ogni figlio:
- è indipendente,  
- possiede ASA ID proprio,  
- possiede serial number unico,  
- replica tutti gli attributi, i traits, il CoA e le utility del Master.

---

## 3. Nuovi campi richiesti nella tabella `egis`

| Campo | Tipo | Descrizione |
|-------|------|-------------|
| **parent_id** | unsignedBigInteger nullable | Collegamento al Master. |
| **is_template** | boolean default false | Indica se l’EGI è un Master. |
| **is_sellable** | boolean default true | Per un Master diventa `false`. |
| **serial_number** | string nullable | Assegnato ai figli. |

---

## 4. Regole di dominio

### 4.1 EGI Master / Template
Un EGI può essere marcato come Master quando:

- non ha ASA ID,
- non è venduto,
- non è già figlio di un altro Master.

Proprietà:
```
is_template = true
is_sellable = false
parent_id = null
asa_id = null
```

### 4.2 EGI Figlio
Un EGI figlio deriva da un Master e deve essere una copia completa:

```
is_template = false
is_sellable = true
parent_id = <id master>
serial_number = <unico>
asa_id = <mint algorand>
```

---

## 5. Clonazione: requisiti funzionali

### 5.1 Elementi da clonare

La clonazione deve replicare **tutto** ciò che appartiene al Master:

- Attributi base dell’EGI
- Traits normali
- CoA completo
- CoA Traits  
- Utility  
- Immagini associate alle utility  
- Metadati JSON necessari al mint ASA  
- Relazioni eventuali non economiche

Il figlio deve essere, salvo poche eccezioni tecniche, **una copia integrale** del Master.

---

## 6. Esclusioni dalla clonazione

I seguenti campi non devono essere copiati:

- `id`
- `parent_id`
- `asa_id`
- `serial_number`
- `created_at`, `updated_at`
- eventuali stati “bozza”, “pubblicato” non coerenti

---

## 7. UX/UI – Come si imposta un Master

### Posizione:
`resources/views/egis/show.blade.php`

### Requisiti UX:

- Mostrare un toggle visibile solo ai Creator/Admin:
  ```
  [ ] Rendi questo EGI un Master clonabile
  ```

- Quando attivato:
  - `is_template = true`
  - `is_sellable = false`
  - Mostrare banner di stato: **MASTER TEMPLATE**

- Quando l’EGI è un Master:
  - mostrare badge MASTER,
  - mostrare il numero di copie generate,
  - pulsante admin/test:
    ```
    [ Genera nuova copia (child EGI) ]
    ```

---

## 8. CloneEgiFromMasterAction – Pseudocodice

```php
public function execute(Egi $master, User $owner): Egi
{
    if (!$master->is_template || $master->parent_id !== null) {
        throw new DomainException("EGI non valido per clonazione.");
    }

    // Clone dei campi base
    $child = $master->replicate([
        'id', 'parent_id', 'asa_id', 'serial_number',
        'created_at', 'updated_at'
    ]);

    $child->parent_id = $master->id;
    $child->is_template = false;
    $child->is_sellable = true;
    $child->rebind = true; // Abilitato per Rebind (Secondary Market)

    // Serial number
    $child->serial_number = SerialService::nextFor($master);

    $child->save();

    // Clone Traits
    foreach ($master->traits as $trait) {
        $child->traits()->create([
            'trait_id' => $trait->trait_id,
            'value'    => $trait->value,
        ]);
    }

    // Clone CoA
    if ($master->coa) {
        $coaClone = $master->coa->replicate();
        $coaClone->egi_id = $child->id;
        $coaClone->save();

        foreach ($master->coa->traits as $t) {
            $coaClone->traits()->create($t->replicate()->toArray());
        }
    }

    // Clone Utility + immagini
    foreach ($master->utilities as $util) {
        $utilClone = $util->replicate();
        $utilClone->egi_id = $child->id;
        $utilClone->save();

        foreach ($util->images as $img) {
            $imgClone = $img->replicate();
            $imgClone->utility_id = $utilClone->id;
            $imgClone->save();
        }
    }

    // Mint ASA
    $asa = AlgorandMintService::mint($child->toMetadata());
    $child->asa_id = $asa;
    $child->save();

    ULM::logSuccess('egi.clone', [
        'master' => $master->id,
        'child'  => $child->id,
        'asa'    => $asa,
    ]);

    return $child;
}
```

---

## 9. Logging OS3

Ogni esecuzione deve avere:

### START
```
ULM::start('egi.clone', [...]);
```

### SUCCESS
```
ULM::success('egi.clone', [...]);
```

### ERROR
via UEM:
```
UEM::report($exception, context: [...]);
```

e AuditTrail se richiesto dalla policy.

---

## 10. Futuri sviluppi (NON inclusi in questa versione)

- Vendita del Master come EGI unico  
- Royalty granulari al Master Owner  
- Economia Granulare multi-layer  
- Clonazione con override dinamico dei metadata  
- Pre-mint di lotto copiabili  
- Generazione massiva (batch clone)

---

## 11. Stato implementazione previsto

| Componente | Stato |
|------------|--------|
| Campi DB | IMPLEMENTATO |
| UI toggle Master | IMPLEMENTATO |
| CloneEgiFromMasterAction | IMPLEMENTATO |
| Mint ASA su clone | IMPLEMENTATO |
| Copia CoA + traits | IMPLEMENTATO |
| Copia Utility + immagini | IMPLEMENTATO |
| Logging OS3 | IMPLEMENTATO |
| Abilitazione Rebind (Sales) | IMPLEMENTATO |

---

## 12. Conclusione

La funzione **EGI Master Clonable** è il primo mattone del sistema di produzione scalabile basato su Master → Children, indispensabile per:

- GoldBar,
- eBook,
- opere serializzabili,
- futuri modelli economici granulari.

Questa documentazione rappresenta la versione **1.0** ufficiale del modello tecnico.

---
