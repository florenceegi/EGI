# Estensione Vocabolario FlorenceEGI - MVP+ 
*Gioielleria, Oreficeria e Arti Artigianali*

---

## Strategia i18n (Laravel)

### Regola Chiave
- **In DB**: solo `slug` (+ category)
- **In UI**: `__('traits.technique.jewelry-fabrication')` etc.
- **Manteniamo `aliases`** solo per la ricerca
- **File traduzioni**: `lang/it/traits.php` e `lang/en/traits.php`

### Vantaggi
- Database più leggero (solo slug)
- Traduzioni gestite via Laravel i18n
- Flessibilità per aggiungere lingue
- Aliases ottimizzati per search

---

## NUOVE CATEGORIE

### Gioielleria/Oreficeria → `jewelry-*`
### Vetro Avanzato → `glass-*` (estensione)
### Legno Artistico → `wood-*`  
### Cuoio Lavorato → `leather-*`
### Tessile Avanzato → `textile-*` (estensione)

---

## TECHNIQUE (Nuove Aggiunte)

### Gioielleria/Oreficeria (`jewelry-*`)

| Slug | Descrizione | Aliases IT | Aliases EN |
|------|-------------|------------|------------|
| `jewelry-fabrication` | Fabbricazione (segare, piegare, saldare a banco) | - | - |
| `jewelry-forging` | Forgiatura/imbutitura | imbutitura | - |
| `jewelry-repousse` | Repoussé | - | - |
| `jewelry-chasing` | Cesello | cesello | chasing |
| `jewelry-filigree` | Filigrana | - | - |
| `jewelry-granulation` | Granulazione | - | - |
| `jewelry-stone-setting-bezel` | Incassatura a castone | castone | bezel setting |
| `jewelry-stone-setting-prong` | Incassatura a griffe | griffe | prong setting |
| `jewelry-stone-setting-pave` | Pavé | - | pave setting |
| `jewelry-stone-setting-channel` | Incassatura a canale | - | channel setting |
| `jewelry-enameling-cloisonne` | Smalto cloisonné | cloisonné | - |
| `jewelry-enameling-champleve` | Smalto champlevé | champlevé | - |
| `jewelry-enameling-plique-a-jour` | Smalto plique-à-jour | plique-à-jour | - |
| `jewelry-mokume-gane` | Mokume-gane | - | - |
| `jewelry-niello` | Niello | - | - |
| `jewelry-damascening` | Damaschinatura | - | damascening |
| `jewelry-hand-engraving` | Incisione a mano | incisione manuale | hand engraving |
| `jewelry-laser-welding` | Saldatura laser | - | - |
| `jewelry-electroforming` | Elettroformatura | - | - |
| `jewelry-wax-carving` | Scultura in cera (per fusione) | modellazione cera | wax modeling |

### Vetro Avanzato (estensione `glass-*`)

| Slug | Descrizione | Aliases IT | Aliases EN |
|------|-------------|------------|------------|
| `glass-lampworking` | Lavorazione a lume/lampworking | lume | flameworking |
| `glass-stained` | Vetrata artistica (piombatura) | vetrata | stained glass |
| `glass-pate-de-verre` | Pâte de verre | - | - |
| `glass-coldworking` | Molatura/lucidatura a freddo | molatura | grinding |

### Legno Artistico (`wood-*`)

| Slug | Descrizione | Aliases IT | Aliases EN |
|------|-------------|------------|------------|
| `wood-marquetry` | Intarsio/impiallacciatura | marqueterie, intarsio | marquetry |
| `wood-turning` | Tornitura del legno | - | woodturning |
| `wood-pyrography` | Pirografia | - | woodburning |
| `wood-lacquer` | Laccatura (urushi/europea) | urushi | lacquering |

### Cuoio (`leather-*`)

| Slug | Descrizione | Aliases IT | Aliases EN |
|------|-------------|------------|------------|
| `leather-tooling` | Bulinatura/embossing del cuoio | embossing | - |
| `leather-dyeing` | Tintura/finissaggio | - | leather finishing |

### Tessile Avanzato (estensione `textile-*`)

| Slug | Descrizione | Aliases IT | Aliases EN |
|------|-------------|------------|------------|
| `textile-felting` | Infeltrimento | - | - |
| `textile-batik` | Batik/riserva | riserva | resist dyeing |
| `textile-shibori` | Shibori/legature | legature | tie-dye |
| `textile-knitting` | Maglia | - | - |
| `textile-crochet` | Uncinetto | - | - |
| `textile-needle-felting` | Infeltrimento ad ago | - | - |

---

## MATERIALS (Nuove Aggiunte)

### Metalli Preziosi

| Slug | Descrizione | Aliases IT | Aliases EN | Note |
|------|-------------|------------|------------|------|
| `metal-gold` | Oro | oro | gold | Karati gestiti come attributo (meta.karat:18K) |
| `metal-silver-sterling` | Argento 925 | argento sterling | sterling silver | - |
| `metal-silver-fine` | Argento 999 | argento fine | fine silver | - |
| `metal-platinum` | Platino | - | - | - |
| `metal-palladium` | Palladio | - | - | - |
| `metal-titanium` | Titanio | - | - | - |
| `metal-tantalum` | Tantalio | - | - | - |
| `metal-white-gold` | Oro bianco | - | - | - |
| `metal-rose-gold` | Oro rosa | oro rosso | rose gold, pink gold | - |

### Smalti e Trattamenti

| Slug | Descrizione | Aliases IT | Aliases EN |
|------|-------------|------------|------------|
| `enamel-vitreous` | Smalto vetroso | smalto | vitreous enamel |
| `patina-oxidation` | Ossidazioni/patine | patina | oxidation |

### Pietre e Gemme

| Slug | Descrizione | Aliases IT | Aliases EN | Note |
|------|-------------|------------|------------|------|
| `gem-diamond` | Diamante | - | - | Specie specifiche come data libero |
| `gem-corundum` | Corindone (rubino/zaffiro) | rubino, zaffiro | ruby, sapphire | - |
| `gem-emerald` | Smeraldo | - | - | - |
| `gem-quartz` | Quarzo (ametista/citrino) | ametista, citrino | amethyst, citrine | - |
| `gem-onyx` | Onice | - | - | - |
| `gem-opal` | Opale | - | - | - |
| `gem-turquoise` | Turchese | - | - | - |
| `gem-amber` | Ambra | - | - | - |
| `gem-coral` | Corallo | - | - | - |
| `gem-pearl` | Perla | perle | pearl | - |
| `gem-synthetic` | Gemme sintetiche | sintetiche | synthetic gems | - |

### Componenti Gioielleria

| Slug | Descrizione | Aliases IT | Aliases EN |
|------|-------------|------------|------------|
| `metal-wire` | Filo metallico | filo | wire |
| `metal-sheet` | Lamina metallica | lamina | sheet metal |
| `solder-jewelry` | Leghe saldanti | saldatura | jewelry solder |

---

## SUPPORT (Nuove Aggiunte)

### Supporti Gioielleria

| Slug | Descrizione | Aliases IT | Aliases EN |
|------|-------------|------------|------------|
| `support-metal-sheet` | Lamina metallica | - | - |
| `support-metal-wire` | Filo metallico | - | - |
| `support-wax-blank` | Modello in cera | - | wax model |
| `support-cast-blank` | Grezzo di fusione | - | cast blank |

---

## Mapping Categorie UI (Aggiornato)

```typescript
const categoryMapping = {
  // Esistenti + Nuove
  'jewelry-*': 'Gioielleria/Oreficeria',
  'glass-*': 'Vetro', // esteso
  'wood-*': 'Legno',
  'leather-*': 'Cuoio',
  'textile-*': 'Tessile', // esteso
  
  // Esistenti (mantenuti)
  'painting-*': 'Pittura',
  'drawing-*': 'Disegno',
  'printmaking-*': 'Incisione/Stampe',
  'sculpture-*': 'Scultura',
  'photography-*': 'Fotografia',
  'digital-*|video-*|ar-vr': 'Digitale',
  'ceramic-*': 'Ceramica',
  'mosaic': 'Mosaico',
  
  // Materials (aggiornati)
  'metal-*': 'Metalli', // include nuovi metalli preziosi
  'gem-*': 'Pietre/Gemme', // nuova categoria
  'enamel-*|patina-*': 'Finiture', // nuova categoria
  'solder-*': 'Componenti', // nuova categoria
};
```

---

## File Laravel i18n (Esempio)

### `lang/it/traits.php`

```php
<?php

return [
    'technique' => [
        // Gioielleria
        'jewelry-fabrication' => 'fabbricazione (gioielleria)',
        'jewelry-forging' => 'forgiatura',
        'jewelry-repousse' => 'repoussé',
        'jewelry-chasing' => 'cesello',
        'jewelry-filigree' => 'filigrana',
        'jewelry-granulation' => 'granulazione',
        'jewelry-stone-setting-bezel' => 'incassatura a castone',
        'jewelry-stone-setting-prong' => 'incassatura a griffe',
        'jewelry-stone-setting-pave' => 'pavé',
        'jewelry-stone-setting-channel' => 'incassatura a canale',
        'jewelry-enameling-cloisonne' => 'smalto cloisonné',
        'jewelry-enameling-champleve' => 'smalto champlevé',
        'jewelry-enameling-plique-a-jour' => 'smalto plique-à-jour',
        'jewelry-mokume-gane' => 'mokume-gane',
        'jewelry-niello' => 'niello',
        'jewelry-damascening' => 'damaschinatura',
        'jewelry-hand-engraving' => 'incisione a mano',
        'jewelry-laser-welding' => 'saldatura laser',
        'jewelry-electroforming' => 'elettroformatura',
        'jewelry-wax-carving' => 'scultura in cera',
        
        // Vetro
        'glass-lampworking' => 'lavorazione a lume',
        'glass-stained' => 'vetrata artistica',
        'glass-pate-de-verre' => 'pâte de verre',
        'glass-coldworking' => 'molatura a freddo',
        
        // Legno
        'wood-marquetry' => 'intarsio/marqueterie',
        'wood-turning' => 'tornitura del legno',
        'wood-pyrography' => 'pirografia',
        'wood-lacquer' => 'laccatura',
        
        // Cuoio
        'leather-tooling' => 'bulinatura del cuoio',
        'leather-dyeing' => 'tintura cuoio',
        
        // Tessile
        'textile-felting' => 'infeltrimento',
        'textile-batik' => 'batik',
        'textile-shibori' => 'shibori',
        'textile-knitting' => 'maglia',
        'textile-crochet' => 'uncinetto',
        'textile-needle-felting' => 'infeltrimento ad ago',
    ],
    
    'materials' => [
        // Metalli preziosi
        'metal-gold' => 'oro',
        'metal-silver-sterling' => 'argento 925',
        'metal-silver-fine' => 'argento 999',
        'metal-platinum' => 'platino',
        'metal-palladium' => 'palladio',
        'metal-titanium' => 'titanio',
        'metal-tantalum' => 'tantalio',
        'metal-white-gold' => 'oro bianco',
        'metal-rose-gold' => 'oro rosa',
        
        // Smalti e trattamenti
        'enamel-vitreous' => 'smalto vetroso',
        'patina-oxidation' => 'ossidazione/patina',
        
        // Pietre e gemme
        'gem-diamond' => 'diamante',
        'gem-corundum' => 'corindone (rubino/zaffiro)',
        'gem-emerald' => 'smeraldo',
        'gem-quartz' => 'quarzo (ametista/citrino)',
        'gem-onyx' => 'onice',
        'gem-opal' => 'opale',
        'gem-turquoise' => 'turchese',
        'gem-amber' => 'ambra',
        'gem-coral' => 'corallo',
        'gem-pearl' => 'perla',
        'gem-synthetic' => 'gemme sintetiche',
        
        // Componenti
        'metal-wire' => 'filo metallico',
        'metal-sheet' => 'lamina metallica',
        'solder-jewelry' => 'lega saldante',
    ],
    
    'support' => [
        'support-metal-sheet' => 'lamina metallica',
        'support-metal-wire' => 'filo metallico',
        'support-wax-blank' => 'modello in cera',
        'support-cast-blank' => 'grezzo di fusione',
    ],
];
```

### `lang/en/traits.php`

```php
<?php

return [
    'technique' => [
        // Jewelry
        'jewelry-fabrication' => 'fabrication (jewelry)',
        'jewelry-forging' => 'forging',
        'jewelry-repousse' => 'repoussé',
        'jewelry-chasing' => 'chasing',
        'jewelry-filigree' => 'filigree',
        'jewelry-granulation' => 'granulation',
        'jewelry-stone-setting-bezel' => 'bezel setting',
        'jewelry-stone-setting-prong' => 'prong setting',
        'jewelry-stone-setting-pave' => 'pavé setting',
        'jewelry-stone-setting-channel' => 'channel setting',
        'jewelry-enameling-cloisonne' => 'cloisonné enamel',
        'jewelry-enameling-champleve' => 'champlevé enamel',
        'jewelry-enameling-plique-a-jour' => 'plique-à-jour enamel',
        'jewelry-mokume-gane' => 'mokume-gane',
        'jewelry-niello' => 'niello',
        'jewelry-damascening' => 'damascening',
        'jewelry-hand-engraving' => 'hand engraving',
        'jewelry-laser-welding' => 'laser welding',
        'jewelry-electroforming' => 'electroforming',
        'jewelry-wax-carving' => 'wax carving',
        
        // Glass
        'glass-lampworking' => 'lampworking',
        'glass-stained' => 'stained glass',
        'glass-pate-de-verre' => 'pâte de verre',
        'glass-coldworking' => 'coldworking',
        
        // Wood
        'wood-marquetry' => 'marquetry',
        'wood-turning' => 'wood turning',
        'wood-pyrography' => 'pyrography',
        'wood-lacquer' => 'lacquering',
        
        // Leather
        'leather-tooling' => 'leather tooling',
        'leather-dyeing' => 'leather dyeing',
        
        // Textile
        'textile-felting' => 'felting',
        'textile-batik' => 'batik',
        'textile-shibori' => 'shibori',
        'textile-knitting' => 'knitting',
        'textile-crochet' => 'crochet',
        'textile-needle-felting' => 'needle felting',
    ],
    
    'materials' => [
        // Precious metals
        'metal-gold' => 'gold',
        'metal-silver-sterling' => 'sterling silver',
        'metal-silver-fine' => 'fine silver',
        'metal-platinum' => 'platinum',
        'metal-palladium' => 'palladium',
        'metal-titanium' => 'titanium',
        'metal-tantalum' => 'tantalum',
        'metal-white-gold' => 'white gold',
        'metal-rose-gold' => 'rose gold',
        
        // Enamels and treatments
        'enamel-vitreous' => 'vitreous enamel',
        'patina-oxidation' => 'oxidation/patina',
        
        // Gems and stones
        'gem-diamond' => 'diamond',
        'gem-corundum' => 'corundum (ruby/sapphire)',
        'gem-emerald' => 'emerald',
        'gem-quartz' => 'quartz (amethyst/citrine)',
        'gem-onyx' => 'onyx',
        'gem-opal' => 'opal',
        'gem-turquoise' => 'turquoise',
        'gem-amber' => 'amber',
        'gem-coral' => 'coral',
        'gem-pearl' => 'pearl',
        'gem-synthetic' => 'synthetic gems',
        
        // Components
        'metal-wire' => 'metal wire',
        'metal-sheet' => 'metal sheet',
        'solder-jewelry' => 'jewelry solder',
    ],
    
    'support' => [
        'support-metal-sheet' => 'metal sheet',
        'support-metal-wire' => 'metal wire',
        'support-wax-blank' => 'wax blank',
        'support-cast-blank' => 'cast blank',
    ],
];
```

---

## Strategia di Rollout MVP+

### Fase 1 (Immediata)
- **Aggiungi categoria Gioielleria/Oreficeria** completa
- Tecniche core: fabrication, forging, stone-setting, enameling
- Materiali essenziali: metalli preziosi, gemme base
- Supporti base: metal-sheet, wax-blank

### Fase 2 (Successiva)
- **Estendi Vetro**: lampworking, stained glass
- **Aggiungi Legno**: marquetry, turning, pyrography  
- **Tessile avanzato**: felting, batik, shibori

### Fase 3 (Future)
- **Cuoio specializzato**
- **Tecniche rarissime** (mokume, niello) se richieste
- **Mantenimento "Altro"** per casi edge

---

## Utilizzo in UI Laravel

```javascript
// In modale traits
const displayLabel = `__('traits.technique.${item.slug}')`;

// Ricerca su aliases (mantenuti per search)
const searchMatch = item.aliases_it?.some(alias => 
  alias.toLowerCase().includes(query.toLowerCase())
) || item.aliases_en?.some(alias => 
  alias.toLowerCase().includes(query.toLowerCase())
);
```

---

*Documento generato per FlorenceEGI Seconda Fase - Estensione vocabolario MVP+ con strategia i18n Laravel*