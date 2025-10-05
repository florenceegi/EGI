# 🧪 GUIDA PRATICA - TESTING STEP 2.7

## Cosa devi fare TU per validare il sistema PA

**Autore**: Padmin D. Curtis OS3.0  
**Data**: 2025-10-05  
**Tempo stimato**: 30-45 minuti

---

## 🎯 OBIETTIVO SEMPLICE

Devi **testare manualmente** che il sistema PA funzioni correttamente. Niente di complicato: apri il browser, prova le funzioni, verifica che tutto funzioni.

---

## ✅ COSA È GIÀ VERIFICATO (da me)

Ho già controllato il **codice**:

-   ✅ Tutti i file esistono
-   ✅ Tutte le route sono definite
-   ✅ Tutti i metodi dei controller ci sono
-   ✅ I colori PA sono applicati
-   ✅ La terminologia è corretta
-   ✅ I servizi sono integrati

**NON devi ricontrollare il codice. È TUTTO OK.**

---

## 🧪 COSA DEVI TESTARE TU (pratico)

### TEST 1: LOGIN E REDIRECT ⏱️ 5 minuti

**Cosa fare**:

1. Apri http://localhost (o il tuo dominio)
2. Fai logout se sei loggato
3. Login con utente **PA Entity**
4. **VERIFICA**: Vieni reindirizzato a `/pa/dashboard` (non `/home`)
5. **VERIFICA**: Vedi la dashboard PA (non quella Creator)

**Poi**:

1. Fai logout
2. Login con utente **Creator**
3. **VERIFICA**: Vieni reindirizzato a `/home` (non `/pa/dashboard`)
4. **VERIFICA**: Vedi la dashboard Creator

**Se funziona**: ✅ Test 1 OK  
**Se NON funziona**: ❌ Dimmi cosa vedi e cosa ti aspettavi

---

### TEST 2: LISTA BENI CULTURALI PA ⏱️ 5 minuti

**Cosa fare** (loggato come PA):

1. Vai su `/egis` (o clicca menu "Patrimonio Culturale")
2. **VERIFICA**: Vedi SOLO i beni nelle TUE collezioni (non tutti gli EGI del sistema)
3. **VERIFICA**: I colori sono PA (blu #1B365D, oro #D4A574)
4. **VERIFICA**: Il titolo dice "Beni Culturali" (non "Opere")

**Poi** (loggato come Creator):

1. Vai su `/egis`
2. **VERIFICA**: Vedi SOLO i TUOI EGI (creati da te)
3. **VERIFICA**: NON vedi i beni PA

**Se funziona**: ✅ Test 2 OK  
**Se NON funziona**: ❌ Screenshot e dimmi cosa vedi

---

### TEST 3: CREA BENE CULTURALE PA ⏱️ 10 minuti

**Cosa fare** (loggato come PA):

1. Vai su `/egis/create`
2. **VERIFICA**: Form con titolo "Carica Bene Culturale"
3. **VERIFICA**: Colori PA (blu/oro)
4. Compila il form:
    - Titolo: "Test Bene Culturale"
    - Artista: "Artista Test"
    - Descrizione: "Descrizione test"
    - Collezione: Scegli una TUA collezione
    - Immagine: Carica un file
5. Clicca "Salva"
6. **VERIFICA**: Vieni reindirizzato alla pagina dettaglio del bene
7. **VERIFICA**: Messaggio di successo
8. **VERIFICA**: I dati sono salvati correttamente

**Se funziona**: ✅ Test 3 OK  
**Se NON funziona**: ❌ Copia l'errore che vedi

---

### TEST 4: MODIFICA BENE CULTURALE PA ⏱️ 10 minuti

**Cosa fare** (loggato come PA):

1. Vai su un bene nelle TUE collezioni
2. Clicca "Modifica"
3. **VERIFICA**: Form pre-compilato con i dati attuali
4. **VERIFICA**: Titolo "Modifica Bene Culturale"
5. Modifica qualcosa (es. titolo)
6. Clicca "Salva"
7. **VERIFICA**: Modifiche salvate
8. **VERIFICA**: Vedi le modifiche nella pagina dettaglio

**Poi prova negativo**:

1. Copia URL di un bene di ALTRA PA (se ce l'hai)
2. Prova ad accedere a `/egis/{id}/edit`
3. **VERIFICA**: Errore 403 o redirect con messaggio errore (NON devi poter modificare)

**Se funziona**: ✅ Test 4 OK  
**Se NON funziona**: ❌ Dimmi cosa succede

---

### TEST 5: ISOLAMENTO DATI ⏱️ 5 minuti

**Cosa fare**:

1. Loggato come PA, conta quanti beni vedi in `/egis`
2. Logout
3. Login come Creator
4. Vai su `/egis`, conta quanti EGI vedi
5. **VERIFICA**: I numeri sono DIVERSI
6. **VERIFICA**: Ogni utente vede SOLO i suoi dati

**Cosa NON deve succedere**:

-   ❌ PA vede EGI di Creator
-   ❌ Creator vede beni PA
-   ❌ PA vede beni di altra PA

**Se funziona**: ✅ Test 5 OK  
**Se NON funziona**: ❌ Grave problema di sicurezza, dimmi subito

---

### TEST 6: ACCESSIBILITÀ BASE ⏱️ 5 minuti

**Cosa fare**:

1. Apri una pagina PA (es. `/egis/create`)
2. Premi TAB ripetutamente
3. **VERIFICA**: Riesci a navigare tra tutti i campi
4. **VERIFICA**: Vedi il focus (bordo blu) su ogni elemento
5. Prova a inviare il form premendo INVIO
6. **VERIFICA**: Form si invia

**Se funziona**: ✅ Test 6 OK  
**Se NON funziona**: ❌ Dimmi dove si blocca il TAB

---

## 📋 CHECKLIST VELOCE

Fai questi test in ordine:

-   [ ] **TEST 1**: Login PA → `/pa/dashboard` ✅/❌
-   [ ] **TEST 1**: Login Creator → `/home` ✅/❌
-   [ ] **TEST 2**: PA vede solo sue collezioni ✅/❌
-   [ ] **TEST 2**: Creator vede solo suoi EGI ✅/❌
-   [ ] **TEST 3**: PA crea bene culturale ✅/❌
-   [ ] **TEST 4**: PA modifica bene culturale ✅/❌
-   [ ] **TEST 4**: PA NON può modificare beni altri ✅/❌
-   [ ] **TEST 5**: Dati isolati tra utenti ✅/❌
-   [ ] **TEST 6**: Navigazione con tastiera funziona ✅/❌

---

## 🚨 COME SEGNALARE PROBLEMI

**Se qualcosa non funziona, dimmi**:

1. **Cosa stavi facendo**: "Stavo cercando di creare un bene culturale..."
2. **Cosa ti aspettavi**: "Dovevo vedere il form di creazione..."
3. **Cosa è successo invece**: "Ho visto un errore 500..."
4. **Screenshot** (se possibile)

**NON serve che**:

-   ❌ Controlli il codice
-   ❌ Cerchi nei log
-   ❌ Apri il database
-   ❌ Leggi documentazione tecnica

**Basta che mi dici "non funziona" e io risolvo.**

---

## ✅ RISULTATO FINALE

**Se TUTTI i test passano**:

-   ✅ STEP 2.7 COMPLETATO
-   ✅ Sistema PA funzionante
-   ✅ Possiamo procedere a STEP 3

**Se QUALCHE test fallisce**:

-   ⚠️ Dimmi quale
-   ⚠️ Lo fisso io
-   ⚠️ Ripeti il test
-   ✅ Poi procediamo

---

## 🎯 DOMANDE FREQUENTI

**Q: Devo testare tutto in un colpo?**  
A: No, puoi fare un test alla volta. Anche uno solo oggi va bene.

**Q: Devo usare browser specifici?**  
A: No, usa Chrome/Firefox/Edge, quello che usi normalmente.

**Q: Devo testare su mobile?**  
A: No, solo desktop per ora. Mobile è TEST 6 (accessibilità) ed è opzionale.

**Q: Quanto tempo ci vuole?**  
A: 30-45 minuti se fai tutto. Anche 10 minuti per test veloce dei principali (TEST 1, 2, 3).

**Q: E se non ho utenti PA e Creator?**  
A: Dimmi, ti aiuto a crearli o usiamo quelli esistenti.

**Q: Devo documentare tutto?**  
A: No, basta che mi dici "tutto OK" o "il TEST 3 non funziona perché...".

---

## 🚀 INIZIA SUBITO

**Azione immediata**:

1. Apri browser
2. Fai TEST 1 (login redirect) - 5 minuti
3. Dimmi se funziona
4. Se OK, fai TEST 2
5. E così via...

**Puoi anche dirmi**: "Fammi vedere TEST 1 in tinker/artisan" se preferisci testare da console invece che browser.

---

**Fatto da**: Padmin D. Curtis OS3.0  
**Per**: Fabio (project owner)  
**Scopo**: Rendere chiaro WTF devi fare per validare STEP 2.7 😅
