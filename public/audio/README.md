# 🎵 Audio Assets per FlorenceEGI

Questa cartella contiene gli asset audio per la pagina informativa.

## 📁 Struttura

```
public/audio/
├── music/                    # Tracce musicali (background)
│   ├── ambient-intro.mp3     # Fase 0: Materializzazione
│   ├── digital-scan.mp3      # Fase 1: Scansione Blockchain
│   ├── achievement.mp3       # Fase 2: Certificazione
│   ├── nature-ambient.mp3    # Fase 3: EPP Tree (crescita)
│   ├── energetic-market.mp3  # Fase 4: AMMk Marketplace
│   ├── futuristic-tech.mp3   # Fase 5: Technology
│   └── florenceegi-theme.mp3 # Tema principale (opzionale)
│
└── sfx/                      # Effetti sonori
    ├── click.mp3             # Click su bottoni
    ├── hover.mp3             # Hover su elementi
    ├── success.mp3           # Azione completata
    ├── transition.mp3        # Transizione tra fasi
    └── whoosh.mp3            # Movimento rapido
```

## 🎼 Specifiche Audio

### Tracce Musicali

-   **Formato**: MP3 (compatibilità browser)
-   **Bitrate**: 128-192 kbps (balance qualità/dimensione)
-   **Durata**: 30-120 secondi (loop seamless)
-   **Volume**: Normalizzato a -14 LUFS
-   **Loop**: Il file deve essere progettato per loop continuo

### Effetti Sonori (SFX)

-   **Formato**: MP3
-   **Bitrate**: 128 kbps
-   **Durata**: 0.1-2 secondi
-   **Volume**: Normalizzato, picchi a -6 dB

## 🎨 Mood per Fase

| Fase | Nome                 | Mood Audio Suggerito                            |
| ---- | -------------------- | ----------------------------------------------- |
| 0    | Materializzazione    | Misterioso, etereo, particelle che si aggregano |
| 1    | Scansione Blockchain | Digitale, scanner, dati in movimento            |
| 2    | Certificazione       | Triumphante, achievement, conferma              |
| 3    | EPP Tree             | Natura, crescita, organico, foglie              |
| 4    | AMMk Marketplace     | Energetico, vivace, commerciale                 |
| 5    | Technology           | Futuristico, cyber, circuit                     |

## 🆓 Risorse Audio Gratuite

### Musica Royalty-Free

-   [Pixabay Music](https://pixabay.com/music/) - Gratis, no attribuzione
-   [Mixkit](https://mixkit.co/free-stock-music/) - Gratis
-   [Uppbeat](https://uppbeat.io/) - Gratis con crediti
-   [Free Music Archive](https://freemusicarchive.org/) - Varie licenze

### Effetti Sonori

-   [Freesound](https://freesound.org/) - Community sounds
-   [Pixabay SFX](https://pixabay.com/sound-effects/) - Gratis
-   [Zapsplat](https://www.zapsplat.com/) - Gratis con account
-   [Mixkit SFX](https://mixkit.co/free-sound-effects/) - Gratis

### Generazione AI

-   [Suno AI](https://suno.ai/) - Genera musica con AI
-   [Mubert](https://mubert.com/) - Musica generativa
-   [AIVA](https://www.aiva.ai/) - Composizione AI

## 🔧 Come Aggiungere Audio

1. **Scarica/Crea** i file audio secondo le specifiche
2. **Rinomina** i file secondo la struttura sopra
3. **Copia** nella cartella `public/audio/`
4. **Testa** nel browser (Chrome richiede interazione utente prima di riprodurre audio)

## ⚠️ Note Importanti

### Autoplay Policy

I browser moderni bloccano l'autoplay audio. L'utente deve:

1. Cliccare sul bottone musica 🎵 per attivare
2. Interagire con la pagina prima che l'audio possa partire

### Performance

-   File troppo grandi rallentano il caricamento
-   Usa compressione adeguata
-   Considera lazy loading per file grandi

### Licenze

-   Verifica SEMPRE la licenza prima di usare audio
-   Alcuni richiedono attribuzione
-   Evita musica copyrighted

## 🎛️ Configurazione in AudioContext.tsx

Per modificare le tracce, edita le costanti in `AudioContext.tsx`:

```typescript
// Tracce per ogni fase
const PHASE_TRACKS: Record<number, string> = {
    0: "ambient-start",
    1: "blockchain-scan",
    // ...
};

// Libreria tracce
const MUSIC_LIBRARY: Record<string, string> = {
    "ambient-start": "/audio/music/ambient-intro.mp3",
    // ...
};

// Libreria SFX
const SFX_LIBRARY: Record<string, string> = {
    click: "/audio/sfx/click.mp3",
    // ...
};
```
