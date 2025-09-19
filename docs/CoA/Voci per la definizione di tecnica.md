# Vocabolario Controllato FlorenceEGI - Seed Data v1.0.0

**Versione**: 1.0.0  
**Progetto**: FlorenceEGI Seconda Fase  
**Scopo**: Seed controllato per definizione di tecnica, materiali e supporto

---

## Note di Implementazione

Ogni entry ha:
- **slug stabile** (identificatore univoco)
- **label_it** (etichetta italiana)
- **label_en** (etichetta inglese)
- **aliases_*** (sinonimi per ricerca)
- **aat_id** (null per evitare errori di mapping manuale)
- **aat_notes** (note facoltative per contesto)

**Importante**: Congelare i valori al momento del CoA per mantenere coerenza storica.

---

## TECHNIQUE (Tecniche)

### Pittura
| Slug | Label IT | Label EN | Aliases IT | Aliases EN |
|------|----------|----------|------------|------------|
| `painting-oil` | pittura a olio | oil painting | olio | oil |
| `painting-acrylic` | pittura acrilica | acrylic painting | acrilico | acrylic |
| `painting-watercolor` | acquerello | watercolor painting | acquarello | watercolour |
| `painting-tempera` | tempera | tempera painting | - | - |
| `painting-gouache` | guazzo (gouache) | gouache painting | gouache | gouache |
| `painting-encaustic` | encausto | encaustic painting | encausto a cera | encaustic |
| `painting-fresco` | affresco | fresco | - | - |

### Disegno
| Slug | Label IT | Label EN | Aliases IT | Aliases EN |
|------|----------|----------|------------|------------|
| `drawing-graphite` | disegno a grafite | graphite drawing | matita | pencil drawing |
| `drawing-charcoal` | disegno a carboncino | charcoal drawing | carboncino | charcoal |
| `drawing-ink` | inchiostro su carta | ink drawing | inchiostro | india ink |
| `drawing-colored-pencil` | matite colorate | colored pencil drawing | - | colour pencil |
| `pastel` | pastello | pastel | - | - |

### Tecniche Contemporanee
| Slug | Label IT | Label EN | Aliases IT | Aliases EN |
|------|----------|----------|------------|------------|
| `spray-painting` | spray painting | spray painting | bomboletta | aerosol art |
| `mixed-media` | mixed media | mixed media | tecnica mista | mixed technique |
| `collage` | collage | collage | - | - |
| `assemblage` | assemblage | assemblage | - | - |

### Stampe e Incisioni (Printmaking)
| Slug | Label IT | Label EN | Aliases IT | Aliases EN |
|------|----------|----------|------------|------------|
| `printmaking-etching` | acquaforte | etching | - | - |
| `printmaking-engraving` | bulino | engraving | incisione a bulino | - |
| `printmaking-drypoint` | puntasecca | drypoint | - | - |
| `printmaking-mezzotint` | maniera nera | mezzotint | - | - |
| `printmaking-aquatint` | acquatinta | aquatint | - | - |
| `printmaking-lithography` | litografia | lithography | - | - |
| `printmaking-screenprint` | serigrafia | screen printing | serigrafia d'arte | silkscreen |
| `printmaking-woodcut` | xilografia | woodcut | - | - |
| `printmaking-linocut` | linoleografia | linocut | linoleum cut | linoleum cut |
| `printmaking-monotype` | monotipo | monotype | - | - |

### Scultura
| Slug | Label IT | Label EN | Aliases IT | Aliases EN |
|------|----------|----------|------------|------------|
| `sculpture-carving` | scultura per sottrazione (scolpire) | carving | scultura a togliere | - |
| `sculpture-modeling` | modellazione | modeling (sculpture) | - | - |
| `sculpture-casting` | fusione | casting | - | - |
| `sculpture-lost-wax` | cera persa | lost-wax casting | - | investment casting |
| `sculpture-welding` | saldatura | welding | - | - |
| `sculpture-3dprint` | stampa 3D | 3D printing | additive manufacturing | additive manufacturing |

### Fotografia
| Slug | Label IT | Label EN | Aliases IT | Aliases EN |
|------|----------|----------|------------|------------|
| `photography-gelatin-silver` | gelatina ai sali d'argento | gelatin silver print | GSP | silver gelatin |
| `photography-albumen` | stampa all'albumina | albumen print | - | - |
| `photography-cyanotype` | cianotipia | cyanotype | - | - |
| `photography-platinum` | platinotipia/palladiotipia | platinum/palladium print | - | platinum print, palladium print |
| `photography-chromogenic` | c-print (chromogenic) | chromogenic print (C-print) | - | RA-4 |
| `photography-inkjet` | stampa inkjet a pigmenti | pigment inkjet print | giclée (termine commerciale) | inkjet print |

### Digitale e Video
| Slug | Label IT | Label EN | Aliases IT | Aliases EN |
|------|----------|----------|------------|------------|
| `digital-print` | stampa digitale | digital print | - | - |
| `digital-generative` | arte generativa | generative art | - | - |
| `digital-3d-render` | render 3D | 3D render | - | - |
| `video-single-channel` | video monocanale | single-channel video | - | - |
| `installation-interactive` | installazione interattiva | interactive installation | - | - |
| `ar-vr` | AR/VR | AR/VR | realtà aumentata, realtà virtuale | augmented reality, virtual reality |

### Tessile
| Slug | Label IT | Label EN | Aliases IT | Aliases EN |
|------|----------|----------|------------|------------|
| `textile-weaving` | tessitura | weaving | - | - |
| `textile-embroidery` | ricamo | embroidery | - | - |
| `textile-tapestry` | arazzo | tapestry | - | - |

### Ceramica
| Slug | Label IT | Label EN | Aliases IT | Aliases EN |
|------|----------|----------|------------|------------|
| `ceramic-wheel-thrown` | tornitura (ceramica) | wheel-thrown ceramics | - | throwing |
| `ceramic-handbuilt` | modellazione manuale (ceramica) | handbuilt ceramics | hand-building | handbuilding |
| `ceramic-slip-cast` | colaggio in barbottina | slip casting | - | - |
| `ceramic-raku` | raku (finitura) | raku firing | - | raku glaze |

### Vetro
| Slug | Label IT | Label EN | Aliases IT | Aliases EN |
|------|----------|----------|------------|------------|
| `glass-blown` | vetro soffiato | blown glass | - | glassblowing |
| `glass-kilnformed` | vetro da forno | kiln-formed glass | fusione a caldo | kilnforming, slumping |

### Mosaico
| Slug | Label IT | Label EN | Aliases IT | Aliases EN |
|------|----------|----------|------------|------------|
| `mosaic` | mosaico | mosaic | - | - |

---

## MATERIALS (Materiali)

### Colori e Pittura
| Slug | Label IT | Label EN | Aliases IT | Aliases EN |
|------|----------|----------|------------|------------|
| `paint-oil` | colore a olio | oil paint | olio | oil |
| `paint-acrylic` | colore acrilico | acrylic paint | acrilico | acrylic |
| `paint-watercolor` | colore ad acquerello | watercolor paint | - | watercolour |
| `paint-tempera` | tempera | tempera | - | - |
| `paint-gouache` | gouache | gouache | guazzo | - |
| `wax-encaustic` | cera per encausto | encaustic wax | - | - |
| `pigment` | pigmenti | pigments | - | - |

### Strumenti da Disegno
| Slug | Label IT | Label EN | Aliases IT | Aliases EN |
|------|----------|----------|------------|------------|
| `graphite` | grafite | graphite | matita | pencil |
| `charcoal` | carboncino | charcoal | - | - |
| `pastel-soft` | pastello morbido | soft pastel | - | - |
| `pastel-oil` | pastello a olio | oil pastel | - | - |
| `ink` | inchiostro | ink | inchiostro di china | india ink |
| `spray-paint` | vernice spray | spray paint | aerosol | aerosol paint |

### Finiture e Preparazioni
| Slug | Label IT | Label EN | Aliases IT | Aliases EN |
|------|----------|----------|------------|------------|
| `gold-leaf` | foglia oro | gold leaf | - | - |
| `silver-leaf` | foglia argento | silver leaf | - | - |
| `gesso` | gesso (preparazione) | gesso (ground) | - | ground |

### Resine e Compositi
| Slug | Label IT | Label EN | Aliases IT | Aliases EN |
|------|----------|----------|------------|------------|
| `resin-epoxy` | resina epossidica | epoxy resin | - | - |
| `fiberglass` | vetroresina | fiberglass | - | glass fiber reinforced plastic |

### Metalli
| Slug | Label IT | Label EN | Aliases IT | Aliases EN |
|------|----------|----------|------------|------------|
| `metal-bronze` | bronzo | bronze | - | - |
| `metal-brass` | ottone | brass | - | - |
| `metal-aluminum` | alluminio | aluminum | alluminio | aluminium |
| `metal-steel` | acciaio | steel | acciaio inox | stainless steel |
| `metal-iron` | ferro | iron | - | - |
| `metal-copper` | rame | copper | - | - |

### Materiali Naturali
| Slug | Label IT | Label EN | Aliases IT | Aliases EN |
|------|----------|----------|------------|------------|
| `wood` | legno | wood | essenza lignea | timber |
| `stone-marble` | marmo | marble | - | - |
| `stone-limestone` | calcare | limestone | - | - |
| `stone-granite` | granito | granite | - | - |

### Ceramica
| Slug | Label IT | Label EN | Aliases IT | Aliases EN |
|------|----------|----------|------------|------------|
| `clay` | argilla | clay | - | - |
| `porcelain` | porcellana | porcelain | - | - |
| `stoneware` | gres | stoneware | - | - |
| `earthenware` | terracotta | earthenware | - | - |

### Vetro
| Slug | Label IT | Label EN | Aliases IT | Aliases EN |
|------|----------|----------|------------|------------|
| `glass` | vetro | glass | - | - |

### Carte Speciali
| Slug | Label IT | Label EN | Aliases IT | Aliases EN |
|------|----------|----------|------------|------------|
| `paper-rag` | carta cotone (rag) | rag paper (cotton) | carta 100% cotone | cotton rag paper |
| `paper-baryta` | carta baritata | baryta paper | baritata | fiber-based paper |
| `paper-rc` | carta RC (resin-coated) | resin-coated paper (RC) | - | - |
| `paper-handmade` | carta fatta a mano | handmade paper | - | - |

### Supporti Pittorici
| Slug | Label IT | Label EN | Aliases IT | Aliases EN |
|------|----------|----------|------------|------------|
| `canvas-cotton` | tela di cotone | cotton canvas | - | - |
| `canvas-linen` | tela di lino | linen canvas | - | - |
| `panel-wood` | tavola di legno | wood panel | tavola | wooden panel |
| `panel-mdf` | pannello MDF | MDF panel | - | medium-density fiberboard |
| `panel-dibond` | pannello Dibond | Dibond (aluminum composite) | alluminio composito | aluminum composite panel |
| `acrylic-sheet` | plexiglass (PMMA) | acrylic sheet (PMMA) | plexiglas, perspex | plexiglass, perspex |

### Tessuti
| Slug | Label IT | Label EN | Aliases IT | Aliases EN |
|------|----------|----------|------------|------------|
| `fabric-silk` | seta | silk | - | - |
| `fabric-wool` | lana | wool | - | - |
| `fabric-cotton` | cotone | cotton | - | - |
| `fabric-linen` | lino | linen | - | - |

### Altri Materiali
| Slug | Label IT | Label EN | Aliases IT | Aliases EN |
|------|----------|----------|------------|------------|
| `leather` | pelle | leather | - | - |
| `found-objects` | oggetti trovati | found objects | ready-made | readymade, found object |

### Elettronica
| Slug | Label IT | Label EN | Aliases IT | Aliases EN |
|------|----------|----------|------------|------------|
| `electronics-led` | LED | LED | - | - |
| `electronics-general` | elettronica | electronics | - | - |

---

## SUPPORT (Supporti)

### Tele
| Slug | Label IT | Label EN | Aliases IT | Aliases EN |
|------|----------|----------|------------|------------|
| `support-canvas-stretched-cotton` | tela di cotone intelaiata | stretched cotton canvas | - | - |
| `support-canvas-stretched-linen` | tela di lino intelaiata | stretched linen canvas | - | - |
| `support-canvas-unstretched` | tela non intelaiata | unstretched canvas | - | - |

### Pannelli Rigidi
| Slug | Label IT | Label EN | Aliases IT | Aliases EN |
|------|----------|----------|------------|------------|
| `support-wood-panel` | tavola di legno | wood panel | - | - |
| `support-plywood` | multistrato | plywood | - | - |
| `support-mdf` | pannello MDF | MDF panel | - | - |
| `support-dibond` | pannello Dibond | Dibond (aluminum composite) | - | - |
| `support-aluminum` | pannello di alluminio | aluminum panel | - | - |

### Carta
| Slug | Label IT | Label EN | Aliases IT | Aliases EN |
|------|----------|----------|------------|------------|
| `support-paper-rag` | carta cotone (rag) | rag paper (cotton) | - | - |
| `support-paper-watercolor` | carta per acquerello | watercolor paper | - | - |
| `support-paper-baryta` | carta baritata | baryta paper | - | - |
| `support-paper-rc` | carta RC | resin-coated paper (RC) | - | - |
| `support-paper-bristol` | carta Bristol | Bristol board | - | - |
| `support-vellum` | pergamena/velina (vellum) | vellum | - | - |

### Materiali Trasparenti
| Slug | Label IT | Label EN | Aliases IT | Aliases EN |
|------|----------|----------|------------|------------|
| `support-acrylic-sheet` | plexiglass (PMMA) | acrylic sheet (PMMA) | - | - |
| `support-glass` | vetro | glass | - | - |
| `support-mirror` | specchio | mirror | - | - |

### Supporti Leggeri
| Slug | Label IT | Label EN | Aliases IT | Aliases EN |
|------|----------|----------|------------|------------|
| `support-cardboard` | cartone | cardboard | - | - |
| `support-foamboard` | pannello piuma | foam board | forex | foamcore |

### Tessuto
| Slug | Label IT | Label EN | Aliases IT | Aliases EN |
|------|----------|----------|------------|------------|
| `support-fabric` | tessuto | fabric | - | - |
| `support-fabric-canvasboard` | cartone telato | canvas board | - | - |

### Pietra
| Slug | Label IT | Label EN | Aliases IT | Aliases EN |
|------|----------|----------|------------|------------|
| `support-stone-marble` | lastra di marmo | marble slab | - | - |
| `support-stone` | lastra di pietra | stone slab | - | - |

### Metallo
| Slug | Label IT | Label EN | Aliases IT | Aliases EN |
|------|----------|----------|------------|------------|
| `support-metal-steel` | lastra d'acciaio | steel plate | - | - |
| `support-metal-copper` | lastra di rame | copper plate | - | - |
| `support-metal-zinc` | lastra di zinco | zinc plate | - | - |

### Ceramica
| Slug | Label IT | Label EN | Aliases IT | Aliases EN |
|------|----------|----------|------------|------------|
| `support-ceramic-tile` | piastrella ceramica | ceramic tile | - | - |

### Supporti Fotografici
| Slug | Label IT | Label EN | Aliases IT | Aliases EN |
|------|----------|----------|------------|------------|
| `support-film-35mm` | pellicola 35mm | 35mm film | - | - |
| `support-film-medium-format` | pellicola medio formato | medium format film | - | - |

### Supporti Digitali
| Slug | Label IT | Label EN | Aliases IT | Aliases EN |
|------|----------|----------|------------|------------|
| `support-projection-screen` | schermo di proiezione | projection screen | - | - |
| `support-monitor` | monitor | monitor | display | display |
| `support-led-wall` | parete LED | LED wall | - | - |

---

## Mapping Categorie per UI

**Per l'implementazione della modale**, utilizzare questi mapping:

```typescript
const categoryMapping = {
  // Technique categories
  'painting-*': 'Pittura',
  'drawing-*': 'Disegno',
  'printmaking-*': 'Incisione/Stampe',
  'sculpture-*': 'Scultura',
  'photography-*': 'Fotografia',
  'digital-*|video-*|ar-vr': 'Digitale',
  'textile-*': 'Tessile',
  'ceramic-*': 'Ceramica',
  'glass-*': 'Vetro',
  'mosaic': 'Mosaico',
  
  // Materials categories  
  'paint-*|wax-*|pigment': 'Colori',
  'graphite|charcoal|pastel-*|ink|spray-paint': 'Strumenti Disegno',
  'metal-*': 'Metalli',
  'stone-*': 'Pietra',
  'fabric-*': 'Tessuti',
  'paper-*': 'Carte Speciali',
  'canvas-*|panel-*|acrylic-sheet': 'Supporti Pittorici',
  'electronics-*': 'Elettronica',
  
  // Support categories
  'support-canvas-*': 'Tele',
  'support-wood-*|support-plywood|support-mdf|support-dibond|support-aluminum': 'Pannelli',
  'support-paper-*|support-vellum': 'Carta',
  'support-acrylic-*|support-glass|support-mirror': 'Trasparenti',
  'support-metal-*': 'Metallo',
  'support-stone-*': 'Pietra',
  'support-film-*': 'Pellicola',
  'support-projection-*|support-monitor|support-led-*': 'Digitale'
};
```

---

*Documento generato per FlorenceEGI Seconda Fase - Vocabolario controllato v1.0.0*