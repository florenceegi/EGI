# FlorenceEGI PA/Enterprise Brand Guidelines

## Design System per Area Istituzionale e Business del Portale

_Versione 1.0 - 2 Ottobre 2025_  
_Derivato da: FlorenceEGI Brand Guidelines v1.0_

---

## 📍 **PREMESSA: Identità Derivata**

Questo documento definisce il **design system specifico per l'area PA/Enterprise** del portale FlorenceEGI, mantenendo la coerenza con l'identità madre ma virando su toni **istituzionali, professionali e conformi agli standard enterprise**.

**Contesto operativo:**

-   Target: Pubbliche Amministrazioni (Comuni, Musei, Archivi)
-   Target: Aziende (Made in Italy, Artigianato, Export)
-   Target: Inspector (Verificatori certificati terze parti)
-   Servizi: Certificazione patrimonio, prodotti, contratti con CoA (Certificate of Authenticity)

**Filosofia design:**

> "Eleganza rinascimentale temperata da rigore istituzionale. Trust prima di creatività."

---

# PARTE I - ADATTAMENTI IDENTITÀ VISIVA

## 🎨 **1. PALETTE COLORI PA/ENTERPRISE**

### 1.1 Palette Primaria Istituzionale

**Blu Istituzionale** `#1B365D` (Blu Algoritmo potenziato)

-   **Ruolo:** Colore DOMINANTE per area PA/Enterprise
-   **Uso:** Header, sidebar, CTA primarie, stati attivi
-   **Rationale:** Trust, affidabilità, serietà PA
-   **Varianti:**
    -   Light: `#2C4A7C` (hover, backgrounds chiari)
    -   Dark: `#0F1E36` (testi su backgrounds chiari)
    -   Extra Light: `#E8EDF4` (backgrounds sezioni)

**Grigio Strutturale** `#4A5568` (nuovo - derivato da Grigio Pietra)

-   **Ruolo:** Testi, bordi, elementi secondari
-   **Uso:** Body text, labels, dividers, cards inactive
-   **Varianti:**
    -   `#2D3748` - Headings secondari
    -   `#718096` - Testi secondari
    -   `#CBD5E0` - Bordi, separatori
    -   `#EDF2F7` - Backgrounds chiari
    -   `#F7FAFC` - Backgrounds sezioni alternate

**Oro Sobrio** `#B89968` (Oro Fiorentino desaturato -15%)

-   **Ruolo:** Accenti MISURATI, non dominanti
-   **Uso:** Badge "Certificato", highlights selezionati, status "Approvato"
-   **Rationale:** Mantiene identità FlorenceEGI ma con sobrietà professionale
-   **Regola d'uso:** MAI in grandi superfici, SOLO accenti puntuali

**Verde Certificazione** `#2D5016` (Verde Rinascita - invariato)

-   **Ruolo:** Indicatori validazione, EPP, sostenibilità
-   **Uso:** Status "Valido", badge certificazione, impatto ambientale
-   **Varianti:**
    -   Light: `#3D6B22` (hover, backgrounds)
    -   Extra Light: `#E8F4E3` (backgrounds alert success)

### 1.2 Colori Funzionali

**Rosso Urgenza** `#C13120` (invariato)

-   **Uso:** Alert critici, stati revocati, errori bloccanti

**Arancio Attenzione** `#E67E22` (invariato)

-   **Uso:** Warning, stati in revisione, notifiche non critiche

**Blu Info** `#3B82F6` (nuovo)

-   **Uso:** Info boxes, helper text, link ipertestuali

**Grigio Disabilitato** `#A0AEC0` (nuovo)

-   **Uso:** Elementi disabilitati, stati inattivi

### 1.3 Regole Applicazione Palette

**PRINCIPIO GUIDA: 60-30-10 Istituzionale**

-   60% Blu Istituzionale + Grigi Strutturali (dominanti)
-   30% Bianchi/Backgrounds chiari (respiro)
-   10% Oro Sobrio + Verde Certificazione (accenti)

**VIETATO:**

-   ❌ Oro Fiorentino originale (`#D4A574`) in grandi superfici
-   ❌ Viola Innovazione (`#8E44AD`) - troppo creativo per PA
-   ❌ Gradienti colorati - solo grigi o mono-tonali

**OBBLIGATORIO:**

-   ✅ Contrasti WCAG 2.1 AA minimi (4.5:1 per testi)
-   ✅ Backgrounds prevalentemente chiari (#F7FAFC, #FFFFFF)
-   ✅ Blu Istituzionale per tutti gli header/sidebar

---

## 🔤 **2. TIPOGRAFIA ISTITUZIONALE**

### 2.1 Font Families

**Primaria - Headings Istituzionali:**

-   Font: **IBM Plex Sans** (preferito) o **Inter** (alternativa)
-   Carattere: Sans-serif professionale, leggibilità massima
-   Uso: H1, H2, H3, titoli dashboard, menu items
-   **RAZIONALE:** Eliminiamo Playfair Display (troppo decorativo per PA)

**Secondaria - Body Text:**

-   Font: **Source Sans Pro** (confermato da guidelines originali)
-   Carattere: Leggibile, accessibile, professionale
-   Uso: Paragrafi, form labels, descrizioni

**Tecnica - Dati e Codici:**

-   Font: **JetBrains Mono** (confermato)
-   Uso: Seriali CoA, hash, ULID, codici fiscali, P.IVA

### 2.2 Scale Tipografica

```css
/* Headings - IBM Plex Sans */
H1: 32px / 2rem - font-weight: 700 - line-height: 1.2 - color: #0F1E36
H2: 24px / 1.5rem - font-weight: 600 - line-height: 1.3 - color: #2D3748
H3: 20px / 1.25rem - font-weight: 600 - line-height: 1.4 - color: #4A5568
H4: 18px / 1.125rem - font-weight: 500 - line-height: 1.5 - color: #4A5568

/* Body - Source Sans Pro */
Body Large: 16px / 1rem - font-weight: 400 - line-height: 1.6 - color: #4A5568
Body Regular: 14px / 0.875rem - font-weight: 400 - line-height: 1.6 - color: #4A5568
Body Small: 12px / 0.75rem - font-weight: 400 - line-height: 1.5 - color: #718096

/* Technical - JetBrains Mono */
Code: 14px / 0.875rem - font-weight: 400 - line-height: 1.4 - color: #2D3748
```

### 2.3 Regole Tipografiche

**OBBLIGATORIO:**

-   ✅ Tutti i testi devono avere contrasto minimo 4.5:1
-   ✅ Line-height minimo 1.5 per body text (accessibilità)
-   ✅ Nessun italic per headings (confusione visiva PA)
-   ✅ Bold solo per enfasi, non decorativo

**VIETATO:**

-   ❌ All-caps su più di 3 parole (leggibilità)
-   ❌ Underline tranne link (standard web)
-   ❌ Font size sotto 12px (WCAG compliance)

---

## 📐 **3. LAYOUT E COMPONENTI**

### 3.1 Struttura Layout Istituzionale

**Grid System:**

-   12 colonne con gutter 24px (desktop)
-   Container max-width: 1440px (enterprise standard)
-   Breakpoints:
    -   Mobile: 320px - 767px
    -   Tablet: 768px - 1023px
    -   Desktop: 1024px - 1439px
    -   Wide: 1440px+

**Spaziature Standard (multipli di 8px):**

```css
--space-xs: 8px;
--space-sm: 16px;
--space-md: 24px;
--space-lg: 32px;
--space-xl: 48px;
--space-2xl: 64px;
```

### 3.2 Sidebar PA/Enterprise

**Specifica Design:**

```css
Width: 280px (fisso desktop)
Background: #1B365D (Blu Istituzionale)
Text Color: #FFFFFF
Active Item Background: #2C4A7C (Blu Light)
Hover Item Background: rgba(255,255,255,0.1)
Border: None (clean)
Shadow: 2px 0 8px rgba(0,0,0,0.1)
```

**Struttura Menu:**

-   Logo FlorenceEGI (versione bianca) in alto
-   Ruolo utente badge sotto logo (es. "PA Entity - Comune di Firenze")
-   Menu items con icone SVG monocromatiche bianche
-   Separatori grigi chiari tra gruppi
-   Logout in fondo

### 3.3 Header Pagina

**Specifica Design:**

```css
Height: 72px
Background: #FFFFFF
Border-bottom: 1px solid #CBD5E0
Shadow: 0 1px 3px rgba(0,0,0,0.08)
```

**Contenuto:**

-   Breadcrumb navigation (sx)
-   User avatar + name (dx)
-   Notification bell (dx)
-   Settings icon (dx)

### 3.4 Cards e Containers

**Card Standard:**

```css
Background: #FFFFFF
Border: 1px solid #E2E8F0
Border-radius: 8px
Padding: 24px
Shadow: 0 1px 3px rgba(0,0,0,0.06)
Hover Shadow: 0 4px 12px rgba(0,0,0,0.1)
```

**Card Status (per CoA, EGI, etc):**

-   Status badge in alto a destra
-   Colori status:
    -   Valido: Verde Certificazione (#2D5016)
    -   In Revisione: Arancio Attenzione (#E67E22)
    -   Revocato: Rosso Urgenza (#C13120)
    -   Bozza: Grigio (#718096)

### 3.5 Forms Istituzionali

**Input Fields:**

```css
Height: 44px (accessibilità touch)
Border: 1px solid #CBD5E0
Border-radius: 6px
Padding: 12px 16px
Font-size: 14px
Color: #2D3748

Focus:
  Border: 2px solid #1B365D
  Shadow: 0 0 0 3px rgba(27,54,93,0.1)

Error:
  Border: 2px solid #C13120
  Shadow: 0 0 0 3px rgba(193,49,32,0.1)
```

**Labels:**

```css
Font-weight: 500
Font-size: 14px
Color: #2D3748
Margin-bottom: 8px
Required asterisk (*): #C13120
```

**Buttons Primary (CTA):**

```css
Background: #1B365D
Color: #FFFFFF
Height: 44px
Padding: 12px 24px
Border-radius: 6px
Font-weight: 600
Font-size: 14px

Hover:
  Background: #2C4A7C
  Transform: translateY(-1px)
  Shadow: 0 4px 12px rgba(27,54,93,0.2)

Disabled:
  Background: #A0AEC0
  Cursor: not-allowed
  Opacity: 0.6
```

**Buttons Secondary:**

```css
Background: #FFFFFF
Color: #1B365D
Border: 1px solid #1B365D
(resto uguale a Primary)
```

### 3.6 Tables & Data Grids

**Table Design:**

```css
Header:
  Background: #F7FAFC
  Font-weight: 600
  Font-size: 12px
  Color: #4A5568
  Text-transform: uppercase
  Letter-spacing: 0.5px
  Padding: 12px 16px

Row:
  Border-bottom: 1px solid #E2E8F0
  Padding: 16px
  Hover Background: #F7FAFC

Striped (opzionale):
  Even rows: #FAFAFA
```

**Pagination:**

-   Numeri pagina stile minimale
-   Current page: Background #1B365D, Color #FFFFFF
-   Altri: Color #4A5568

---

## 🎯 **4. COMPONENTI SPECIFICI PA/ENTERPRISE**

### 4.1 Badge Certificazione CoA

**Design:**

```html
<div class="coa-badge-institutional">
    <svg><!-- icona certificato --></svg>
    <span>CoA Certificato</span>
    <span class="serial">COA-EGI-2025-001234</span>
</div>
```

**Stile:**

```css
Background: linear-gradient(135deg, #2D5016 0%, #3D6B22 100%)
Color: #FFFFFF
Border: 2px solid #B89968 (Oro Sobrio)
Border-radius: 8px
Padding: 12px 16px
Font-weight: 600
Font-size: 14px
Shadow: 0 2px 8px rgba(45,80,22,0.15)
```

### 4.2 Status Indicators

**Pill Style:**

```css
Display: inline-flex
Align-items: center
Padding: 6px 12px
Border-radius: 12px (arrotondato)
Font-size: 12px
Font-weight: 600
Gap: 6px (tra icona e testo)

/* Colori per status */
.status-valid {
    background: #e8f4e3;
    color: #2d5016;
    border: 1px solid #2d5016;
}

.status-pending {
    background: #fff4e6;
    color: #e67e22;
    border: 1px solid #e67e22;
}

.status-revoked {
    background: #fee2e2;
    color: #c13120;
    border: 1px solid #c13120;
}
```

### 4.3 Timeline Audit (per CoA Events)

**Visual Design:**

-   Linea verticale sinistra: 2px solid #CBD5E0
-   Nodi eventi: Cerchi 32px diameter
    -   Background: #1B365D (eventi sistema)
    -   Background: #B89968 (eventi utente)
    -   Icona bianca centrata
-   Card evento a destra della timeline
-   Timestamp in alto (font mono, 12px, #718096)

### 4.4 QR Code Display (verifica pubblica)

**Container:**

```css
Background: #FFFFFF
Border: 2px solid #1B365D
Border-radius: 12px
Padding: 24px
Text-align: center
```

**Layout:**

-   QR code centrato (200x200px)
-   Sotto: Serial CoA (font mono, 14px, #2D3748)
-   Sotto: "Scansiona per verificare" (12px, #718096)
-   Button "Verifica Online" (secondary style)

---

## 📊 **5. DASHBOARD PA/ENTERPRISE**

### 5.1 Layout Dashboard

**Struttura 4-colonne grid (desktop):**

**Row 1 - KPI Cards:**

-   Card 1: Totale EGI Certificati
-   Card 2: CoA Attivi
-   Card 3: In Revisione
-   Card 4: Impatto EPP (se applicabile)

**Row 2 - Charts:**

-   Col 1-2: Grafico andamento certificazioni (line chart)
-   Col 3-4: Distribuzione tipologie (pie chart)

**Row 3 - Recent Activity:**

-   Full width: Tabella ultimi 10 eventi

### 5.2 KPI Card Design

**Specifica:**

```css
Background: linear-gradient(135deg, #1B365D 0%, #2C4A7C 100%)
Color: #FFFFFF
Padding: 24px
Border-radius: 12px
Min-height: 140px

Icon (top-left): 48px, opacity 0.3
Value (center): 36px, font-weight 700
Label (bottom): 14px, opacity 0.8
Trend indicator (bottom-right): Arrow + percentage
```

### 5.3 Charts Style

**Colors Palette Charts:**

-   Primary: #1B365D
-   Secondary: #2C4A7C
-   Tertiary: #B89968
-   Quaternary: #2D5016
-   Neutral: #718096

**Grid lines:** #E2E8F0 (leggere)
**Labels:** #4A5568, 12px
**Legends:** Bottom, horizontal

---

## 🔒 **6. SICUREZZA E COMPLIANCE VISIVA**

### 6.1 Indicatori GDPR

**Badge "GDPR Compliant":**

```css
Background: #E8F4E3
Color: #2D5016
Border: 1px solid #2D5016
Font-size: 11px
Font-weight: 600
Padding: 4px 8px
Border-radius: 4px
```

Posizionamento: Footer forms, privacy settings pages

### 6.2 Audit Trail Visibility

**Principio:** Ogni modifica dati sensibili DEVE mostrare:

-   Chi (user name + role badge)
-   Quando (timestamp preciso)
-   Cosa (descrizione azione)
-   Perché (se applicabile - note)

**Visual:** Timeline component obbligatorio in detail pages

### 6.3 Consent Indicators

**Checkbox Consensi:**

```css
Size: 20px x 20px (accessibilità)
Border: 2px solid #1B365D
Checked Background: #1B365D
Checkmark: #FFFFFF, bold
```

**Label consenso:** Font-size 14px, line-height 1.6, link policy evidenziati

---

## ♿ **7. ACCESSIBILITÀ OBBLIGATORIA**

### 7.1 Standard WCAG 2.1 AA

**Contrasti Minimi:**

-   Testi normali: 4.5:1 ✅
-   Testi grandi (18px+): 3:1 ✅
-   Elementi UI: 3:1 ✅

**Validazione automatica:**

-   Tool: axe DevTools, Lighthouse
-   Test su ogni componente prima del deploy

### 7.2 Navigazione Tastiera

**Tab Order:**

-   Logico e sequenziale
-   Skip link per saltare nav
-   Focus indicators visibili (outline 2px solid #1B365D)

**ARIA Labels:**

-   Obbligatori su tutti gli icon buttons
-   Form labels espliciti (no placeholder-only)
-   Role attributes per custom components

### 7.3 Screen Reader Optimization

**Regole:**

-   Heading hierarchy corretta (H1 → H2 → H3)
-   Alt text descrittivi su immagini
-   Aria-live regions per updates dinamici
-   No content in ::before/::after essenziali

---

## 📱 **8. RESPONSIVE DESIGN**

### 8.1 Mobile First Adaptations

**Sidebar → Bottom Navigation (mobile):**

```css
Position: fixed bottom
Height: 64px
Background: #1B365D
Items: 4-5 main icons
Active: Oro Sobrio underline
```

**Tables → Cards (mobile):**

-   Ogni row diventa card verticale
-   Labels inline con valori
-   Scrolling orizzontale solo se inevitabile

### 8.2 Breakpoint Behaviors

**Mobile (< 768px):**

-   1 colonna layout
-   Font-size -1px su headings
-   Padding ridotto 16px (invece 24px)
-   Buttons full-width

**Tablet (768px - 1023px):**

-   2 colonne grid dove possibile
-   Sidebar collapsabile
-   Touch targets 44px minimum

**Desktop (1024px+):**

-   Layout completo come specifiche sopra

---

## 🎨 **9. ANIMAZIONI E TRANSIZIONI**

### 9.1 Principio: Eleganza Sobria

**Durata Standard:**

-   Micro: 150ms (hover, focus)
-   Breve: 250ms (dropdown, modal)
-   Media: 350ms (page transition)
-   Lunga: 500ms (skeleton loading)

**Easing:**

-   Default: cubic-bezier(0.4, 0, 0.2, 1)
-   Entrada: cubic-bezier(0, 0, 0.2, 1)
-   Uscita: cubic-bezier(0.4, 0, 1, 1)

### 9.2 Animazioni Vietate

**NO:**

-   ❌ Bounce effects (poco professionale)
-   ❌ Rotate/spin oltre loading spinners
-   ❌ Parallax effects (distrazione PA)
-   ❌ Anything "playful" o "creative"

**YES:**

-   ✅ Fade in/out
-   ✅ Slide up/down
-   ✅ Scale (1 → 1.02 per hover)
-   ✅ Opacity transitions

---

## 📋 **10. COMPONENTI LIBRARY**

### 10.1 Componenti Prioritari da Sviluppare

**Alta priorità:**

1. **PAEntityCard** - Card collection per PA
2. **CoACertificateBadge** - Badge certificazione
3. **StatusPill** - Indicatori stato
4. **InstitutionalButton** - Button primario/secondario
5. **AuditTimeline** - Timeline eventi
6. **QRVerificationBox** - Box QR + verifica
7. **KPICardInstitutional** - Card metrica dashboard
8. **InstitutionalTable** - Tabella dati standard

**Media priorità:** 9. **EGIPreviewCard** - Preview EGI istituzionale 10. **InspectorAssignmentBox** - Assegnazione inspector 11. **FileUploadInstitutional** - Upload documenti CoA 12. **SignatureStatusIndicator** - Stato firme

### 10.2 Naming Convention Componenti

**Pattern:**

```
{Entity}{Purpose}{Variant?}.{extension}

Esempi:
- PAEntityCard.blade.php
- CoABadgeInstitutional.blade.php
- ButtonPrimaryInstitutional.blade.php
- TableDataGrid.blade.php
```

---

## 🔍 **11. CHECKLIST CONFORMITÀ**

**Prima di ogni deploy componente PA/Enterprise, verificare:**

-   [ ] Usa palette colori istituzionale (Blu dominante)
-   [ ] Tipografia IBM Plex Sans per headings
-   [ ] Contrasti WCAG 2.1 AA rispettati
-   [ ] Touch targets minimo 44px
-   [ ] ARIA labels presenti
-   [ ] Tab navigation funzionante
-   [ ] Responsive testato su 3 breakpoints
-   [ ] Animazioni sobrie (no bounce/spin)
-   [ ] Oro Sobrio SOLO per accenti puntuali
-   [ ] Form errors chiari e costruttivi
-   [ ] Loading states gestiti
-   [ ] Empty states con CTA

---

## 📚 **12. ESEMPI PRATICI**

### 12.1 Esempio: Pagina Lista CoA PA

**Layout:**

```
[Header]
  Breadcrumb: PA Dashboard > Certificati > Lista CoA

[Filters Row]
  Search Input | Status Dropdown | Date Range Picker | Export Button

[KPI Row]
  Card: Totale CoA | Card: Validi | Card: In Revisione | Card: Revocati

[Table]
  Columns: Serial | Titolo Opera | Data Emissione | Status | Azioni
  Rows: 20 per pagina con pagination
```

**Colori dominanti:**

-   Background: #F7FAFC
-   Cards: #FFFFFF
-   Header: #1B365D
-   Buttons: #1B365D (primary), #FFFFFF (secondary)

### 12.2 Esempio: Form Creazione EGI PA

**Sections:**

1. Info Base (titolo, descrizione)
2. Dati Tecnici (dimensioni, materiali)
3. Storico/Provenienza
4. Upload Immagini
5. Richiesta CoA (checkbox)

**Validazione real-time:**

-   Icona check verde (#2D5016) campi validi
-   Icona X rossa (#C13120) campi errore
-   Helper text sotto campo (12px, #718096)

---

## ✅ **13. RISORSE E ASSET**

### 13.1 File da Creare

**Palette CSS Variables:**

```css
/* File: pa-institutional-colors.css */
:root {
    --pa-blue-primary: #1b365d;
    --pa-blue-light: #2c4a7c;
    --pa-blue-dark: #0f1e36;
    --pa-blue-extra-light: #e8edf4;

    --pa-gray-900: #2d3748;
    --pa-gray-700: #4a5568;
    --pa-gray-500: #718096;
    --pa-gray-300: #cbd5e0;
    --pa-gray-100: #edf2f7;
    --pa-gray-50: #f7fafc;

    --pa-gold-accent: #b89968;
    --pa-green-cert: #2d5016;
    --pa-red-urgent: #c13120;
    --pa-orange-warning: #e67e22;
}
```

**Tailwind Config Extension:**

```js
// tailwind.config.js - PA theme
module.exports = {
    theme: {
        extend: {
            colors: {
                "pa-blue": {
                    DEFAULT: "#1B365D",
                    light: "#2C4A7C",
                    dark: "#0F1E36",
                    "extra-light": "#E8EDF4",
                },
                "pa-gold": "#B89968",
                "pa-green": "#2D5016",
                // ... altri colori
            },
            fontFamily: {
                institutional: ["IBM Plex Sans", "Inter", "sans-serif"],
                body: ["Source Sans Pro", "sans-serif"],
                mono: ["JetBrains Mono", "monospace"],
            },
        },
    },
};
```

### 13.2 Iconografia

**Style:** Outline style, 24px default, stroke-width 2px  
**Color:** Inherited (cambierà per contesto)  
**Library:** Heroicons (MIT license) + custom icons FlorenceEGI

**Custom icons specifici PA:**

-   certificate-badge
-   inspector-shield
-   pa-entity-building
-   audit-timeline
-   qr-verify

---

## 🎯 **14. IMPLEMENTAZIONE ROADMAP**

### Fase 1: Foundation (Settimana 1-2)

-   [ ] Setup CSS variables palette PA
-   [ ] Import IBM Plex Sans font
-   [ ] Creare layout base sidebar PA
-   [ ] Header + breadcrumb component

### Fase 2: Core Components (Settimana 3-4)

-   [ ] Buttons institutional
-   [ ] Form inputs + validation states
-   [ ] Cards + status pills
-   [ ] Table data grid

### Fase 3: Domain Components (Settimana 5-6)

-   [ ] CoA badge component
-   [ ] EGI preview card PA
-   [ ] Audit timeline
-   [ ] QR verification box

### Fase 4: Dashboard (Settimana 7-8)

-   [ ] KPI cards institutional
-   [ ] Charts integration
-   [ ] Recent activity table
-   [ ] Complete PA dashboard page

---

## 📞 **15. RIFERIMENTI**

**Documento Madre:**
`docs/ai/marketing/FlorenceEGI Brand Guidelines.md`

**Standard Compliance:**

-   WCAG 2.1 AA: https://www.w3.org/WAI/WCAG21/quickref/
-   PA Design System Italia (riferimento): https://designers.italia.it/

**Font Licenses:**

-   IBM Plex Sans: Open Font License
-   Source Sans Pro: Open Font License
-   JetBrains Mono: Apache License 2.0

**Testing Tools:**

-   axe DevTools (accessibility)
-   Lighthouse (performance + a11y)
-   BrowserStack (cross-browser)

---

**Questo documento è la source of truth per ogni sviluppo UI/UX nell'area PA/Enterprise del portale FlorenceEGI.**

**Ogni deviazione deve essere discussa e approvata esplicitamente.**

_FlorenceEGI PA/Enterprise - Trust attraverso l'eleganza istituzionale_

---

**#pa-design #institutional #enterprise #brand-guidelines #palette #accessibility**
