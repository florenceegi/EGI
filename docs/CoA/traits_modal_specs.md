# Specifiche UX - Modale Selezione Traits
*Tecnica / Materiali / Supporto*

---

## Obiettivi UX

- **Selezionare** Tecnica, Materiali, Supporto da vocabolario controllato
- **Consentire "Altro"** (testo libero) quando manca la voce
- **Mostrare sempre** cosa è selezionato tramite chip rimovibili
- **Tastiera ottimizzata**: Search → Tab → Enter

---

## Struttura della Modale

### Header
- **Titolo**: "Tecnica / Materiali / Supporto"
- **Tabs**: Tecnica | Materiali | Supporto
- **Search globale** (con debounce)

### Body
- **Colonna sinistra (Categorie)**: lista verticale
  - Esempi: Pittura, Incisione, Fotografia, Scultura, Digitale, Tessile, Ceramica, Vetro, etc.
- **Colonna destra (Voci)**: grid/lista di voci filtrate per categoria + search
  - Ogni voce: label + alias in piccolo
  - Icona "+" per aggiungere
  - Tooltip con descrizione se disponibile

### Footer
- **Selected chips** (per il tab attivo)
- **Bottoni**: "Aggiungi Altro" (apre input testo), "Annulla", "Conferma"

---

## Pattern UX Importanti

### Comboselect a Chip
- **Selezione multipla**: ogni chip ha [x] per rimozione
- **Preferiti/Recenti**: se presente storico, mostra una riga "Recenti" sopra i risultati

### Gestione Conflitti
- **Nessuna limitazione**: puoi combinare liberamente più tecniche/materiali/supporti

### Congelamento CoA
- **Snapshot**: quando emetti CoA, prendi snapshot delle label+ID

---

## Ricerca e Categorie

### Sistema di Ricerca
- **Search filtra**: `label_it`, `label_en`, `aliases`
- **Nessun risultato**: mostra pulsante "Aggiungi come Altro"

### Categorie Statiche
Mappate su slug:
- `painting-*` → Pittura
- `printmaking-*` → Incisione/Stampe  
- `photography-*` → Fotografia
- `sculpture-*` → Scultura
- `digital-*/video-*/ar-vr` → Digitale
- `textile-*` → Tessile
- `ceramic-*` → Ceramica
- `glass-*` → Vetro
- `mosaic` → Mosaico

---

## Interfacce Dati (TypeScript)

```typescript
type VocabItem = {
  slug: string;            // "printmaking-etching"
  label_it: string;        // "acquaforte"
  label_en: string;
  aat_id?: string | null;  // opzionale
  category: string;        // "printmaking"
  aliases_it?: string[];
};

type TraitSelection = {
  technique: VocabItem[];  // multi
  materials: VocabItem[];
  support: VocabItem[];
  free_text: { 
    technique?: string[]; 
    materials?: string[]; 
    support?: string[] 
  };
};
```

---

## Stato & Eventi (Vanilla TS)

### Stato Locale
- `currentTab`, `query`, `selected`, `category`

### Eventi Custom
```javascript
// Emetti evento all'OK
window.dispatchEvent(new CustomEvent('egi:traits:update', { 
  detail: selection 
}));

// Aggiorna form EGI (hidden inputs JSON) o salva via fetch POST
```

---

## Accessibilità

### Focus Management
- **Focus trap** nella modale
- **Navigazione tastiera**:
  - `Esc` chiude
  - `Enter` seleziona/conferma  
  - `↑/↓` naviga lista
  - `Tab` cambia focus

### ARIA Labels
- `aria-selected`
- `role="tablist"`
- `role="listbox"`

---

## Validazione

### Regole Minime
- **Tecnica**: almeno 1 voce o 1 "Altro"
- **Supporto**: consigliato 1 voce (se l'opera non è puramente digitale)
- **"Altro"**: max 60 caratteri, niente HTML

---

## Layout HTML (Pseudo-Blade)

```html
<div id="traitsModal" class="fixed inset-0 hidden">
  <div class="mx-auto mt-10 w-full max-w-4xl rounded-2xl bg-white p-4 shadow-xl">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <h3 class="text-xl font-semibold">Tecnica / Materiali / Supporto</h3>
      <button class="icon-btn" data-close>&times;</button>
    </div>

    <!-- Tabs + Search -->
    <div class="mt-3">
      <div class="flex gap-3 border-b">
        <button data-tab="technique" class="tab active">Tecnica</button>
        <button data-tab="materials" class="tab">Materiali</button>
        <button data-tab="support" class="tab">Supporto</button>
        <div class="ml-auto">
          <input id="traitSearch" type="search" placeholder="Cerca..." class="input">
        </div>
      </div>

      <!-- Grid Layout -->
      <div class="mt-4 grid grid-cols-12 gap-4">
        <aside class="col-span-3 space-y-1" id="traitCategories">
          <!-- Pittura, Incisione, Fotografia, Scultura, Digitale, Tessile, Ceramica, Vetro, ... -->
        </aside>
        <section class="col-span-9 space-y-2" id="traitOptions">
          <!-- Cards opzioni filtrate -->
        </section>
      </div>

      <!-- Selected Items -->
      <div class="mt-4">
        <h4 class="text-sm font-medium">Selezionati</h4>
        <div id="traitChips" class="mt-2 flex flex-wrap gap-2">
          <!-- chips -->
        </div>
        <button id="addFreeText" class="btn-link mt-2">+ Aggiungi "Altro"</button>
      </div>
    </div>

    <!-- Footer Actions -->
    <div class="mt-5 flex justify-end gap-3">
      <button data-cancel class="btn-secondary">Annulla</button>
      <button data-confirm class="btn-primary">Conferma</button>
    </div>
  </div>
</div>
```

---

## Logica Filtro (TypeScript Pseudo)

```typescript
const state = { 
  tab: 'technique', 
  query: '', 
  category: 'all', 
  selected: { 
    technique: [], 
    materials: [], 
    support: [], 
    free_text: {} 
  } 
};

function filterItems(all: VocabItem[]) {
  const q = state.query.trim().toLowerCase();
  return all.filter(x =>
    (state.category === 'all' || x.category === state.category) &&
    (!q || 
      x.label_it.toLowerCase().includes(q) || 
      x.label_en.toLowerCase().includes(q) ||
      x.aliases_it?.some(a => a.toLowerCase().includes(q))
    )
  );
}

function toggleSelect(item: VocabItem) {
  const arr = state.selected[state.tab as keyof TraitSelection] as VocabItem[];
  const i = arr.findIndex(v => v.slug === item.slug);
  if (i >= 0) arr.splice(i, 1); 
  else arr.push(item);
  renderChips();
}
```

---

## Salvataggio (Form EGI)

### Hidden Inputs JSON
- `technique_json`
- `materials_json` 
- `support_json`

Formato: array di `{slug, label_it, aat_id?}`

### Testo Libero
- `*_free_text_json` per voci "Altro"

---

## Edge Cases

### Opere 100% Digitali
- **Supporto può essere vuoto**
- Mostra badge "Digitale" senza forzare selezione

### Multi-Supporto
- **Esempio**: carta + plexi
- **Soluzione**: consentire 2+ selezioni

### Localizzazione
- **Mostra**: `label_it`
- **Conserva**: `label_en` per future traduzioni

---

## Checklist "MVP che Vola"

- [ ] **Tabs funzionanti** + search con debounce 250ms
- [ ] **Tastiera OK** (↑/↓/Enter/Esc)
- [ ] **Chips rimovibili**
- [ ] **"Altro"** con input inline
- [ ] **Persistenza sul form EGI** (hidden JSON) + validazione server
- [ ] **Snapshot CoA** pesca solo le label (e salva anche slug/ID in archivio)

---

*Documento generato per FlorenceEGI Seconda Fase - Sistema di selezione traits avanzato*