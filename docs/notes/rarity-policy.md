# FlorenceEGI – Rarity Policy

**Data:** 2025-09-01  
**Autore:** Padmin D. Curtis (AI Partner) con Fabio Cherici  
**Contesto:** Definizione della gestione della rarità globale in FlorenceEGI, pre e post mint

---

## 🧠 Premessa

Durante la definizione del sistema trait per FlorenceEGI, è emersa la volontà di introdurre un concetto di **rarità globale**: una percentuale che indica quante volte un dato trait è presente **su tutti gli EGI della piattaforma**, non solo all’interno della singola collezione.

L’obiettivo era:
- Fornire uno strumento oggettivo di misurazione della rarità.
- Incentivare la diversificazione da parte dei creator.
- Guidare il collezionista nella selezione pre-mint.

Tuttavia, questo approccio solleva criticità post-mint, su cui abbiamo ragionato a fondo.

---

## ❓ Dubbio chiave

> Cosa succede se un trait oggi è rarissimo (es. 0.3%) ma domani viene usato da decine di altri creator, e sale al 5%?  
> Il suo valore per l’EGI già mintato **viene percepito come diminuito**, anche se quell’opera è rimasta identica.

### ⚠️ Conclusione

La **rarità globale** è *relativa e dinamica* per definizione.  
Ma una volta che un EGI è mintato, il suo stato deve essere **immutabile, coerente e non penalizzabile da dinamiche future**.

---

## ✅ Decisione finale

**1.** Ogni trait ha un campo `global_rarity_percentage` calcolato dinamicamente in base agli EGI **non ancora mintati**.  
**2.** Al momento del mint:
- il valore `global_rarity_percentage` viene **congelato** come `rarity_global_at_mint`
- questo valore è incluso nei metadata finali del token
**3.** Dopo il mint:
- il trait diventa **immutabile**
- la rarità globale **non viene più aggiornata per quell’EGI**
- la piattaforma può continuare a mostrare statistiche **attuali**, ma **non retroattive**

---

## 🔒 Implicazioni tecniche

- Nuovo campo `rarity_global_at_mint` nel pivot `egi_trait_option`
- Blocchi server-side per impedire modifiche a trait dopo il mint
- I valori congelati sono persistiti nel metadata (es. IPFS) al momento della generazione

---

## 🎯 Obiettivo raggiunto

Con questa policy:

- ✅ Gli EGI mintati **non vengono mai svalutati dal sistema**
- ✅ I creator possono giocare strategicamente **prima del mint**
- ✅ I collezionisti ricevono un’informazione **storica e oggettiva**
- ✅ La piattaforma resta coerente con i suoi valori: etica, rispetto, trasparenza

---

**Nota finale:** Questa logica è pensata per essere estendibile. In futuro si potrà introdurre un sistema di “rarità evolutiva” o “rarità narrativa”, sempre mantenendo la **sacralità del mint** come punto di non ritorno.
