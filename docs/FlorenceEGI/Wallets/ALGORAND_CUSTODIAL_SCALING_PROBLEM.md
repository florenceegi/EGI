# Algorand Custodial Scaling Problem

**Data:** 2025-10-14  
**Status:** 🚨 BLOCKING

---

## IL PROBLEMA

**FlorenceEGI target:** PA italiane senza wallet Algorand (98% utenti stimati).

**Problema:** Algorand richiede **minimum balance crescente** per ogni NFT custodito:

```
Minimum Balance = 0.1 ALGO + (num_NFT × 0.1 ALGO)
```

**Impatto economico:**

---

## 📊 IL PROBLEMA: MINIMUM BALANCE REQUIREMENT

### **Meccanismo Algorand**

Ogni wallet Algorand deve mantenere un **minimum balance** non spendibile:

```
Minimum Balance = 100,000 + (num_assets × 100,000) microAlgos
                = 0.1 ALGO + (num_assets × 0.1 ALGO)
```

**Esempio pratico:**

| NFT nel Wallet | Minimum Balance | USD Congelati\* |
| -------------- | --------------- | --------------- |
| 0              | 0.1 ALGO        | $0.02           |
| 10             | 1.1 ALGO        | $0.21           |
| 100            | 10.1 ALGO       | $1.92           |
| 1,000          | 100.1 ALGO      | $19             |
| 10,000         | 1,000.1 ALGO    | $190            |
| 100,000        | 10,000.1 ALGO   | $1,900          |
| 1,000,000      | 100,000.1 ALGO  | $19,000         |

_\*Prezzo ALGO: $0.19 (ottobre 2025)_

### **Stato Attuale FlorenceEGI**

```
Treasury Address: TF67P6XRLQJWBJSFIZFMTNW574VP5XWZMTH3ONP5JQLKHRWDG5IIZXLG7A
Balance: 0.99 ALGO
NFT custoditi: 8
Minimum required: 0.9 ALGO
Spendibili: 0.09 ALGO

⚠️ Non può mintare nuovo NFT (serve 0.152 ALGO)
```

---

## ❌ PERCHÉ IL MODELLO ATTUALE NON SCALA

### **Scenario 1: Single Treasury (modello attuale)**

```
Assunzioni:
- 1 treasury wallet
- Tutti gli utenti PA senza wallet (100% custodia)
- Target: 100,000 NFT in 5 anni

Costi:
- Anno 1 (20,000 NFT): 2,000 ALGO congelati = $380
- Anno 3 (60,000 NFT): 6,000 ALGO congelati = $1,140
- Anno 5 (100,000 NFT): 10,000 ALGO congelati = $1,900

⚠️ PROBLEMA: Budget crescente lineare con adozione
⚠️ RISCHIO: Insostenibile a lungo termine
```

### **Scenario 2: Zero Scaling Strategy**

Se NON affrontiamo il problema:

```
- Budget operativo cresce linearmente
- Costi imprevisti aumentano
- Profittabilità diminuisce
- Competitor con soluzioni migliori ci superano
```

---

## ✅ SOLUZIONI PROPOSTE

### **SOLUZIONE 1: Multi-Treasury Architecture**

**Concetto:** Distribuzione NFT su pool di treasury wallets.

```
Treasury Pool Manager:
├─ Treasury 1: max 1,000 NFT (100 ALGO congelati)
├─ Treasury 2: max 1,000 NFT (100 ALGO congelati)
├─ Treasury 3: max 1,000 NFT (100 ALGO congelati)
└─ Treasury N: max 1,000 NFT (100 ALGO congelati)

Logic:
- Mint su treasury con < 1,000 NFT
- Quando pieno → crea nuovo treasury
- Distributed minimum balance
```

**Budget:**

```
10 treasury × 100 ALGO = 1,000 ALGO (~$190) → 10,000 NFT
100 treasury × 100 ALGO = 10,000 ALGO (~$1,900) → 100,000 NFT
```

**Pro:**

-   ✅ Scalabile (aggiungi treasury on-demand)
-   ✅ Budget prevedibile
-   ✅ Architettura distribuita (resilience)
-   ✅ Nessun cambiamento user experience

**Contro:**

-   ⚠️ Budget comunque crescente (ridotto 10x vs single treasury)
-   ⚠️ Gestione chiavi multiple treasury
-   ⚠️ Complexity architetturale aumentata

**Implementazione:**

-   Stimata: 40 ore
-   Effort: Medium
-   Risk: Low

---

### **SOLUZIONE 2: Periodic NFT Destroy + Archival**

**Concetto:** NFT ha lifecycle finito (es. 5 anni), poi viene distrutto automaticamente.

```
Lifecycle NFT:
1. Mint → NFT attivo (0.1 ALGO congelati)
2. Year 1-5 → NFT verificabile on-chain
3. Year 5 → Auto-destroy NFT
4. Post-destroy → Certificato PDF resta valido
5. 0.1 ALGO sbloccati e riutilizzabili ✅
```

**Budget fisso:**

```
Se destroy dopo 5 anni:
Max NFT contemporanei = mint_annuali × 5

Esempio con 20,000 mint/anno:
20,000 × 5 = 100,000 NFT max contemporanei
Budget: 10,000 ALGO (~$1,900) FISSO PERMANENTE ✅
```

**Pro:**

-   ✅ Budget fisso e prevedibile
-   ✅ Sostenibile a lungo termine
-   ✅ Lifecycle management automatico
-   ✅ Certificato PDF permanente (valore legale)
-   ✅ Blockchain history verificabile (immutabile)

**Contro:**

-   ⚠️ NFT "scade" (ma certificato no)
-   ⚠️ Educazione user necessaria
-   ⚠️ Compliance da verificare (validità certificato post-destroy)

**Implementazione:**

-   Stimata: 60 ore
-   Effort: Medium-High
-   Risk: Medium (compliance legale)

---

### **SOLUZIONE 3: Ibrido Multi-Treasury + Periodic Destroy**

**Concetto:** Best of both worlds.

```
Phase 1 (MVP):
- Multi-treasury (5 wallets iniziali)
- Capacity: 5,000 NFT
- Budget: ~$95 (500 ALGO)

Phase 2 (Scale):
- Aggiungi treasury on-demand
- Monitoring automatico
- Capacity: 50,000 NFT
- Budget: ~$950 (5,000 ALGO)

Phase 3 (Mature):
- Periodic destroy (lifecycle 5 anni)
- Budget stabilizzato
- Capacity: illimitata
```

**Pro:**

-   ✅ Graduale (MVP → Scale → Mature)
-   ✅ Risk mitigation (test destroy in fase 3)
-   ✅ Budget prevedibile ogni fase
-   ✅ Flessibilità architetturale

**Contro:**

-   ⚠️ Complexity implementativa maggiore
-   ⚠️ Roadmap multi-fase

**Implementazione:**

-   Stimata: 80 ore (3 fasi)
-   Effort: High
-   Risk: Low-Medium

---

## 📊 CONFRONTO SOLUZIONI

| Criterio            | Single Treasury | Multi-Treasury | Periodic Destroy | Ibrido       |
| ------------------- | --------------- | -------------- | ---------------- | ------------ |
| **Budget iniziale** | $19             | $190           | $95              | $95          |
| **Budget 100k NFT** | $19,000 ❌      | $1,900 ⚠️      | $950 ✅          | $950 ✅      |
| **Scalabilità**     | ❌ Poor         | ⚠️ Medium      | ✅ Good          | ✅ Excellent |
| **Complexity**      | ✅ Low          | ⚠️ Medium      | ⚠️ Medium        | ❌ High      |
| **Risk**            | ❌ High         | ✅ Low         | ⚠️ Medium        | ✅ Low       |
| **Time to MVP**     | ✅ 0h (done)    | ⚠️ 40h         | ⚠️ 60h           | ❌ 80h       |

---

## 🎯 RACCOMANDAZIONE

### **Strategia consigliata: IBRIDO (3 fasi)**

**Rationale:**

1. ✅ **MVP rapido** (multi-treasury, 40h) → Launch veloce
2. ✅ **Budget prevedibile** ogni fase
3. ✅ **Risk mitigation** (test destroy in prod dopo 1 anno)
4. ✅ **Sostenibilità long-term** (budget fisso fase 3)

**Roadmap:**

```
Q4 2025 (MVP):
- Implementa multi-treasury (5 wallets)
- Capacity: 5,000 NFT
- Budget: $95
- Time: 40 ore

Q1-Q2 2026 (Scale):
- Monitoring + auto-scaling treasury
- Capacity: 50,000 NFT
- Budget: $950
- Time: 20 ore

Q3-Q4 2026 (Mature):
- Periodic destroy (5yr lifecycle)
- Budget stabilizzato: $950 FISSO
- Time: 20 ore

Total effort: 80 ore
Total budget: $950 (permanente)
```

---

## ⚠️ ALTERNATIVE NON PERSEGUITE (e perché)

### **User Wallet Mandatory**

❌ **Rejected:** Target PA non avrà wallet (98% utenti stimati)

### **Ethereum/Polygon Migration**

❌ **Rejected:** Gas fees 100x superiori, costi operativi insostenibili

### **Layer 2 Solutions (Arbitrum, etc)**

❌ **Rejected:** Complexity eccessiva, esperienza utente peggiore

### **Off-Chain NFT (database only)**

❌ **Rejected:** Zero immutabilità, zero trust, non è un NFT reale

---

## 🚨 DECISIONE RICHIESTA

**Dobbiamo decidere:**

1. **Quale soluzione implementare?**

    - [ ] Multi-Treasury (40h, $190 budget scale)
    - [ ] Periodic Destroy (60h, $950 budget fisso)
    - [ ] Ibrido 3 fasi (80h, $950 budget fisso)

2. **Quando implementare?**

    - [ ] Immediato (prima di launch MVP)
    - [ ] Post-MVP (dopo validazione mercato)
    - [ ] Graduale (fase per fase)

3. **Budget approvato?**
    - [ ] $95 (MVP multi-treasury)
    - [ ] $950 (budget fisso con destroy)
    - [ ] Altro: **\_\_\_**

---

## 📎 ALLEGATI

-   **Code reference:** `app/Services/AlgorandService.php` (checkTreasuryFunds)
-   **Current treasury:** `TF67P6XRLQJWBJSFIZFMTNW574VP5XWZMTH3ONP5JQLKHRWDG5IIZXLG7A`
-   **Microservice:** `algokit-microservice/server.js`

---

## 📞 PROSSIMI PASSI

1. **Brainstorming session** (questa chat) → Scelta soluzione
2. **Architectural design** (se necessario)
3. **Implementation** (stima effort confermata)
4. **Testing** (TestNet validation)
5. **Documentation** (admin guide)
6. **Monitoring setup** (alerting budget)

---

**Fine documento - Pronto per discussione strategica** 🚀
