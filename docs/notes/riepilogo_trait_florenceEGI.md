# Riepilogo Discussione Trait EGI - FlorenceEGI
**Data:** 2025-08-31  
**Partecipanti:** Fabio Cherici, Padmin D. Curtis (AI Partner)  
**Contesto:** Definizione e gestione dei trait multilingua per gli EGI su FlorenceEGI  

---

## 📌 Obiettivi iniziali

- Comprendere come i trait NFT sono gestiti a livello di lingua su marketplace esistenti.
- Valutare la possibilità di rendere i trait multilingua (es. italiano/inglese).
- Analizzare vantaggi e svantaggi di implementazioni complesse (es. con smart contract) rispetto a soluzioni più semplici.
- Strutturare un sistema interno che permetta futuri sviluppi internazionali ma senza complicare l’MVP.

---

## ❓ Dubbi emersi

- I trait sono tradotti automaticamente dalle piattaforme NFT?
- Se inserisco i trait in italiano, verranno compresi anche da utenti esterni?
- Ha senso implementare una logica di traduzione complessa già da ora (es. chiavi + dizionario multilingua)?
- Qual è il valore reale per l'utente inesperto di avere trait multilingua?
- Come evitare overload cognitivo e tecnico su un pubblico non blockchain-native?

---

## ✅ Conclusioni raggiunte

- **I trait non vengono tradotti automaticamente**: restano nella lingua in cui sono scritti dal creator. La maggior parte dei marketplace li lascia in inglese.
- **Soluzioni complesse come smart contract multilingua sono teoricamente fattibili**, ma non pragmatiche nell’MVP: rischiano di introdurre più problemi che benefici.
- **È sensato rinviare la multilingua a una futura versione** degli EGI, integrandola nativamente nei metadata solo se diventerà un reale vantaggio competitivo.
- **Per ora i trait saranno standardizzati in inglese**, scelti da liste predefinite, così da garantire chiarezza, portabilità e facilità d’uso.
- **L’expertise di Riccardo Rami sarà usata come caso pilota**, per testare le prime collezioni e validare il sistema trait/metadata.
- **I trait diventano uno strumento di storytelling e marketing**, oltre che filtri per la ricerca: aiutano a valorizzare le caratteristiche distintive degli EGI e a segmentare il pubblico.

---

## 🛠️ Azioni implementate

- Inserita una nuova categoria `Cultural` nel seeder dei trait.
- Creati trait “Cultural Origin”, “Thematic Focus”, “Artisan Technique” ed “Edition Type” con valori in inglese derivati dal contesto artistico/etico di FlorenceEGI.
- Tutti i trait sono stati strutturati per garantire compatibilità futura e chiarezza per l’utente.

---

## 📌 Prossimi step consigliati

- Terminare l’inserimento dei trait predefiniti nel frontend (selettori da lista).
- Mappare visivamente i trait nelle EGI Card (pillole, badge, tooltip).
- Prevedere una struttura JSON interna a supporto della multilingua, da attivare solo quando necessario.
- Considerare i trait come parte della strategia narrativa della piattaforma.

---

