# EGI-INFO - Project Setup Guide

## Quick Start

```bash
# 1. Create project
npm create vite@latest egi-info -- --template react-ts
cd egi-info

# 2. Install dependencies
npm install

# 3. Create folder structure
mkdir -p src/{components,sections,context,audio,styles}

# 4. Copy files from this handoff:
# - 02-WheelMenu.tsx → src/components/WheelMenu/WheelMenu.tsx
# - 03-WheelMenu.css → src/components/WheelMenu/WheelMenu.css
# - 04-InformativePageV4Wheel.tsx → src/pages/InformativePage.tsx
# - 05-base-styles.css → src/styles/base.css + page.css
# - 06-13 sections → src/sections/
# - 14-15 audio → src/audio/
# - 16 animation → src/context/

# 5. Start dev server
npm run dev
```

## Folder Structure

```
egi-info/
├── src/
│   ├── components/
│   │   └── WheelMenu/
│   │       ├── WheelMenu.tsx
│   │       ├── WheelMenu.css
│   │       └── index.ts
│   ├── sections/
│   │   ├── HeroV4.tsx
│   │   ├── HeroV4.css
│   │   ├── OriginStoryV4.tsx
│   │   ├── OriginStoryV4.css
│   │   ├── EgizzareV4.tsx
│   │   ├── EgizzareV4.css
│   │   ├── WhatIsEGIV4.tsx
│   │   ├── WhatIsEGIV4.css
│   │   ├── TransparencyV4.tsx
│   │   ├── TransparencyV4.css
│   │   ├── BlockchainSimpleV4.tsx
│   │   ├── BlockchainSimpleV4.css
│   │   ├── ProblemsV4.tsx
│   │   ├── ProblemsV4.css
│   │   ├── InvoicesV4.tsx
│   │   ├── InvoicesV4.css
│   │   ├── WhoCanUseV4.tsx
│   │   ├── WhoCanUseV4.css
│   │   ├── CTAFinalV4.tsx
│   │   ├── CTAFinalV4.css
│   │   └── index.ts
│   ├── context/
│   │   └── AnimationContext.tsx
│   ├── audio/
│   │   ├── AudioContext.tsx
│   │   ├── AudioControls.tsx
│   │   ├── AudioControls.css
│   │   └── audioConfig.ts
│   ├── pages/
│   │   ├── InformativePage.tsx
│   │   └── InformativePage.css
│   ├── styles/
│   │   └── base.css
│   ├── App.tsx
│   └── main.tsx
├── public/
│   └── audio/ (optional for local tracks)
├── package.json
├── vite.config.ts
└── tsconfig.json
```

## package.json

```json
{
  "name": "egi-info",
  "version": "1.0.0",
  "type": "module",
  "scripts": {
    "dev": "vite",
    "build": "tsc && vite build",
    "preview": "vite preview"
  },
  "dependencies": {
    "react": "^18.2.0",
    "react-dom": "^18.2.0"
  },
  "devDependencies": {
    "@types/react": "^18.2.0",
    "@types/react-dom": "^18.2.0",
    "@vitejs/plugin-react": "^4.2.0",
    "typescript": "^5.0.0",
    "vite": "^5.0.0"
  }
}
```

## vite.config.ts

```typescript
import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';

export default defineConfig({
  plugins: [react()],
  server: {
    port: 3000,
    host: true
  },
  build: {
    outDir: 'dist',
    sourcemap: true
  }
});
```

## App.tsx

```tsx
import { AudioProvider } from './audio/AudioContext';
import { AnimationProvider } from './context/AnimationContext';
import InformativePage from './pages/InformativePage';
import './styles/base.css';

export default function App() {
  return (
    <AnimationProvider>
      <AudioProvider>
        <InformativePage />
      </AudioProvider>
    </AnimationProvider>
  );
}
```

## main.tsx

```tsx
import React from 'react';
import ReactDOM from 'react-dom/client';
import App from './App';

ReactDOM.createRoot(document.getElementById('root')!).render(
  <React.StrictMode>
    <App />
  </React.StrictMode>
);
```

## Deployment (Laravel Forge)

1. Create new site: `egi-info.13.48.57.194.sslip.io`
2. Git repo: `your-org/egi-info`
3. Deploy script:

```bash
cd /home/forge/egi-info.13.48.57.194.sslip.io
git pull origin main
npm install
npm run build
```

4. Nginx config - point to `dist/` folder:

```nginx
server {
    listen 80;
    server_name egi-info.13.48.57.194.sslip.io;
    root /home/forge/egi-info.13.48.57.194.sslip.io/dist;
    
    location / {
        try_files $uri $uri/ /index.html;
    }
}
```

## Design Tokens

```css
/* Colors */
--gold: #d4af37;
--gold-light: #f4d03f;
--bg-dark: #0a0a0f;
--bg-section: #0d0d12;
--text-primary: rgba(255, 255, 255, 0.95);
--text-secondary: rgba(255, 255, 255, 0.7);

/* Responsive Breakpoint */
@media (max-width: 768px) { /* mobile */ }

/* Section Base */
min-height: 100vh;
padding: 80px 20px;
```

## Key Features

- ✅ WheelMenu circular navigation (spin animation)
- ✅ Mobile list fallback at 768px
- ✅ LocalStorage for visited sections
- ✅ Keyboard accessible (Tab, Enter, Arrow keys)
- ✅ ARIA labels for accessibility
- ✅ Audio system with royalty-free tracks
- ✅ Animation pause/disable support
- ✅ Gold (#d4af37) theme on dark background
