# 🚀 PROMPT PER OTTIMIZZAZIONE ULTRA UPLOAD MANAGER - IMAGE OPTIMIZATION

## 📋 CONTESTO DEL PROGETTO

Sono Fabio, sviluppatore del progetto **FlorenceEGI** - una piattaforma NFT per Creator con centinaia di migliaia di immagini EGI. Ho bisogno di ottimizzare le performance della home page che attualmente ha un **LCP di 10.15 secondi** a causa delle immagini non ottimizzate caricate dai Creator.

## 🎯 OBIETTIVO PRIMARIO

Implementare un sistema di **ottimizzazione automatica delle immagini** nell'Ultra Upload Manager per generare varianti responsive in formato WebP, mantenendo sempre l'immagine originale in alta definizione per lo zoom nelle viste EGI.

## 🏗️ ARCHITETTURA PATH ESISTENTE (DA RISPETTARE ASSOLUTAMENTE)

### Struttura Path Attuale (`config/paths.php`)

```php
'paths' => [
    'collections' => 'users_files/collections_{collectionId}/',
    'head' => [
        'banner' => 'users_files/collections_{collectionId}/head/banner/',
        'card' => 'users_files/collections_{collectionId}/head/card/',
        'avatar' => 'users_files/collections_{collectionId}/head/avatar/',
        'EGI_asset' => 'users_files/collections_{collectionId}/head/EGI_asset/',
    ],
    'EGIs' => 'users_files/collections_{collectionId}/EGIs/',
    'user_data' => [
        'documents' => 'users_files/users-data/documents/',
    ],
]
```

### Configurazione Spatie Media Library

-   **Path Generator**: Ho configurato `CustomPathGenerator` (attualmente usa `DefaultPathGenerator`)
-   **Storage**: Sistema multi-disk con Digital Ocean come storage primario
-   **Media Collections**: `main_gallery` e `featured_image` configurate
-   **Conversions**: Temporaneamente disabilitate (da riattivare con ottimizzazioni)

### CRITICAL: Strategia di Conservazione Path

```
// ORIGINALE (SEMPRE PRESERVATO)
users_files/collections_123/EGIs/original_image.jpg

// VARIANTI OTTIMIZZATE (NUOVE - sottocartella /optimized/)
users_files/collections_123/EGIs/optimized/
├── thumbnail/original_image_150x150.webp
├── mobile/original_image_400x400.webp
├── tablet/original_image_600x600.webp
└── desktop/original_image_800x800.webp

// BANNER HERO
users_files/collections_123/head/banner/original_banner.jpg
users_files/collections_123/head/banner/optimized/
├── mobile/original_banner_800x400.webp
├── tablet/original_banner_1200x600.webp
└── desktop/original_banner_1920x960.webp
```

## 📊 SCALA MASSIVA DEL PROGETTO

-   **Immagini esistenti**: Centinaia di migliaia di EGI + hero banner
-   **Upload giornalieri**: Decine di nuove immagini dai Creator
-   **Performance target**: LCP < 2.5s (attualmente 10.15s)
-   **Backward compatibility**: Fondamentale per immagini esistenti

## 🔧 SPECIFICHE TECNICHE RICHIESTE

### 1. **ImageOptimizationProcessor** (Nuovo)

```php
// Posizione: packages/ultra/egi-module/src/Services/ImageOptimizationProcessor.php

class ImageOptimizationProcessor
{
    /**
     * Genera varianti responsive rispettando path esistenti
     * MANTIENE sempre originale ad alta definizione
     */
    public function processImage(string $originalPath, string $collectionId): array

    /**
     * Configurazione varianti per diversi contesti
     */
    public function getVariantConfigurations(): array

    /**
     * Integrazione con CustomPathGenerator esistente
     */
    public function generateOptimizedPaths(string $originalPath, string $variant): string
}
```

### 2. **Varianti Richieste**

#### Per EGI Standard:

-   **Thumbnail**: 150x150px WebP (quality 85)
-   **Mobile**: 400x400px WebP (quality 80)
-   **Tablet**: 600x600px WebP (quality 75)
-   **Desktop**: 800x800px WebP (quality 75)

#### Per Hero Banner:

-   **Mobile**: 800x400px WebP (quality 80)
-   **Tablet**: 1200x600px WebP (quality 75)
-   **Desktop**: 1920x960px WebP (quality 70)

#### Per Altri Formati:

-   **Card**: 300x300px WebP (quality 85)
-   **Avatar**: 200x200px WebP (quality 90)

### 3. **OptimizeImageJob** (Queue Job)

```php
// Posizione: packages/ultra/egi-module/src/Jobs/OptimizeImageJob.php

class OptimizeImageJob implements ShouldQueue
{
    /**
     * Processa singola immagine in background
     * Gestisce fallback per errori di ottimizzazione
     */
    public function handle(): void

    /**
     * Batch processing per immagini esistenti
     */
    public static function batchOptimizeExisting(): void
}
```

### 4. **Integrazione con Upload Esistente**

```php
// Modifica: packages/ultra/egi-module/src/Handlers/EgiUploadHandler.php

// Nel metodo dopo salvataggio originale:
protected function triggerImageOptimization(string $savedPath, int $collectionId): void
{
    // Dispatch OptimizeImageJob solo per immagini
    // Mantiene UX upload esistente (nessun rallentamento)
}
```

## 🔄 GESTIONE BACKWARD COMPATIBILITY

### 1. **Helper per Frontend**

```php
// Nuovo: app/Helpers/ResponsiveImageHelper.php

class ResponsiveImageHelper
{
    /**
     * Genera tag <picture> con fallback all'originale
     */
    public static function picture(string $originalUrl, array $options = []): string

    /**
     * Ottiene URL variante ottimizzata o fallback originale
     */
    public static function getOptimizedUrl(string $originalUrl, string $variant): string
}
```

### 2. **Comando Artisan per Batch**

```php
// Nuovo: packages/ultra/egi-module/src/Console/Commands/OptimizeExistingImages.php

php artisan egi:optimize-images
php artisan egi:optimize-images --collection=123
php artisan egi:optimize-images --dry-run
```

## ⚡ STRATEGIA DI IMPLEMENTAZIONE

### Fase 1: Core Development

1. **ImageOptimizationProcessor** con configurazioni varianti
2. **OptimizeImageJob** per processing asincrono
3. **Integrazione CustomPathGenerator** per path corretti
4. **Testing con immagini di prova**

### Fase 2: Integration

1. **Hook in EgiUploadHandler** post-upload
2. **ResponsiveImageHelper** per frontend
3. **Comando batch** per immagini esistenti
4. **Configurazione queue** per performance

### Fase 3: Deployment

1. **Testing su subset** immagini esistenti
2. **Batch processing** graduale (chunked)
3. **Monitoraggio performance** e storage
4. **Rollback strategy** se necessario

## 📁 STRUTTURA FILE DA CREARE

```
packages/ultra/egi-module/src/
├── Services/
│   ├── ImageOptimizationProcessor.php
│   └── ImageVariantGenerator.php
├── Jobs/
│   └── OptimizeImageJob.php
├── Console/Commands/
│   └── OptimizeExistingImages.php
└── config/
    └── image-optimization.php

app/Helpers/
└── ResponsiveImageHelper.php
```

## ⚙️ CONFIGURAZIONE VARIANTI

```php
// packages/ultra/egi-module/config/image-optimization.php

return [
    'variants' => [
        'egi' => [
            'thumbnail' => ['width' => 150, 'height' => 150, 'quality' => 85],
            'mobile' => ['width' => 400, 'height' => 400, 'quality' => 80],
            'tablet' => ['width' => 600, 'height' => 600, 'quality' => 75],
            'desktop' => ['width' => 800, 'height' => 800, 'quality' => 75],
        ],
        'banner' => [
            'mobile' => ['width' => 800, 'height' => 400, 'quality' => 80],
            'tablet' => ['width' => 1200, 'height' => 600, 'quality' => 75],
            'desktop' => ['width' => 1920, 'height' => 960, 'quality' => 70],
        ],
        'card' => [
            'default' => ['width' => 300, 'height' => 300, 'quality' => 85],
        ],
        'avatar' => [
            'default' => ['width' => 200, 'height' => 200, 'quality' => 90],
        ],
    ],
    'format' => 'webp',
    'fallback_format' => 'jpg',
    'queue' => 'images',
    'batch_size' => 50,
];
```

## 🎯 RISULTATI ATTESI

1. **Performance**: LCP ridotto da 10.15s a <2.5s
2. **Storage**: Ottimizzazione spazio con WebP
3. **UX**: Caricamento progressivo senza perdita qualità zoom
4. **Scalability**: Sistema gestisce crescita futura automaticamente
5. **Compatibility**: Zero impatto su funzionalità esistenti

## 📋 CHECKLIST IMPLEMENTAZIONE

-   [ ] **ImageOptimizationProcessor** con path integration
-   [ ] **OptimizeImageJob** con queue processing
-   [ ] **EgiUploadHandler** hook post-upload
-   [ ] **ResponsiveImageHelper** per frontend
-   [ ] **CustomPathGenerator** enhancement
-   [ ] **Comando Artisan** batch processing
-   [ ] **Configurazione varianti** completa
-   [ ] **Testing** su dataset campione
-   [ ] **Documentation** per deployment

## 🚨 VINCOLI CRITICI

1. **NEVER** modificare o spostare immagini originali
2. **ALWAYS** rispettare struttura path `users_files/collections_{collectionId}/`
3. **ALWAYS** creare sottocartella `/optimized/` per varianti
4. **MAINTAIN** backward compatibility per immagini esistenti
5. **PRESERVE** alta definizione per zoom nelle viste EGI
6. **INTEGRATE** con sistema Spatie esistente
7. **RESPECT** configurazione multi-disk storage

---

## 🎖️ NOTA FINALE

Questo sistema deve gestire una scala massiva (centinaia di migliaia di immagini) mantenendo performance elevate e zero downtime. La priorità è l'ottimizzazione LCP per la home page pubblica, mantenendo la qualità massima per le viste EGI interne.

**Target**: Sistema pronto per deployment graduale su produzione con monitoring completo.

---

**Prompt preparato da**: Fabio Cherici  
**Data**: 25 agosto 2025  
**Progetto**: FlorenceEGI Ultra Upload Manager  
**Versione**: 2.0 - Path Integration & Massive Scale
