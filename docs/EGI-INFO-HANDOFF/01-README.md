# EGI-INFO - Handoff Documentation

## Panoramica Progetto

**EGI-INFO** è un progetto React standalone per le pagine informative di FlorenceEGI.

### Target
- **Dominio**: EGI-info-.13.48.57.194.sslip.io
- **Deploy**: Laravel Forge
- **Stack**: React + TypeScript + Vite

### Struttura File

```
src/
├── App.tsx                          # Entry point
├── main.tsx                         # React mount
├── index.css                        # Global styles
├── pages/
│   └── InformativePageV4Wheel.tsx   # Main page
├── components/
│   ├── WheelMenu/
│   │   ├── WheelMenu.tsx
│   │   ├── WheelMenu.css
│   │   └── index.ts
│   └── sections/
│       ├── HeroV4.tsx/css
│       ├── OriginStoryV4.tsx/css    # ← Storia personale Fabio
│       ├── EgizzareV4.tsx/css
│       ├── WhatIsEGIV4.tsx/css
│       ├── TransparencyV4.tsx/css
│       ├── BlockchainSimpleV4.tsx/css
│       ├── ProblemsV4.tsx/css
│       ├── InvoicesV4.tsx/css
│       ├── WhoCanUseV4.tsx/css
│       └── CTAFinalV4.tsx/css
├── contexts/
│   ├── AnimationContext.tsx
│   └── AudioContext.tsx
├── audio/
│   ├── AudioProvider.tsx
│   ├── AudioControls.tsx/css
│   ├── types.ts
│   └── presets.ts
└── styles/
    ├── base.css
    └── variables.css
```

### Design System

**Colori:**
- Background: `#0a0a0f` (dark)
- Gold: `#d4af37`
- Gold Light: `#f4e4bc`
- Text Primary: `#ffffff`
- Text Secondary: `#a0a0b0`

**Font:**
- Principale: Inter, system-ui
- Motto: Playfair Display (italic)

**Responsive:**
- Breakpoint mobile: 768px
- Mobile: lista verticale
- Desktop: ruota circolare animata

### Menu Items (10 sezioni)

```typescript
const MENU_ITEMS = [
    { id: 'hero', label: 'Home', icon: '🏠' },
    { id: 'originstory', label: 'La Storia', icon: '📖', emphasized: true },
    { id: 'egizzare', label: 'Egizzare', icon: '✨' },
    { id: 'whatisegi', label: "Cos'è un EGI", icon: '💎' },
    { id: 'transparency', label: 'Trasparenza', icon: '📊' },
    { id: 'blockchain', label: 'Blockchain', icon: '🔗' },
    { id: 'problems', label: 'Problemi', icon: '🛡️' },
    { id: 'invoices', label: 'Fatture', icon: '📋' },
    { id: 'whocause', label: 'Per chi', icon: '👥' },
    { id: 'cta', label: 'Inizia', icon: '🚀' },
];
```

### Dipendenze npm

```json
{
  "dependencies": {
    "react": "^18.2.0",
    "react-dom": "^18.2.0"
  },
  "devDependencies": {
    "@vitejs/plugin-react": "^4.2.0",
    "typescript": "^5.3.0",
    "vite": "^5.0.0",
    "@types/react": "^18.2.0",
    "@types/react-dom": "^18.2.0"
  }
}
```

### Vite Config

```typescript
import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';

export default defineConfig({
  plugins: [react()],
  build: {
    outDir: 'dist',
  },
});
```

### Note Importanti

1. **WheelMenu** ha proprietà `emphasized` per evidenziare "La Storia"
2. **OriginStoryV4** contiene la storia personale di Fabio (5 anni, 5 riscritture, 5%)
3. Audio system è opzionale - può essere rimosso per semplicità
4. LocalStorage `florenceegi_wheel_visited` per skip animazione spin
5. Keyboard navigation implementata (Arrow keys + Enter)
6. ARIA labels per accessibilità

---

**Vedi i file numerati successivi per il codice completo di ogni componente.**
