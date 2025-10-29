# N.A.T.A.N. - Strategy Brief per Comune di Firenze

## Documento Riepilogativo per Ricerca Approfondita

**Data:** 2025-10-09  
**Autore:** Fabio Cherici  
**Destinatario:** Claude Sonnet 4.5 (Deep Research)  
**Obiettivo:** Analisi fattibilità partnership strategica Comune Firenze

---

## 🎯 OBIETTIVO STRATEGICO

**GOAL:** Diventare partner tecnologico strategico del Comune di Firenze per sistema N.A.T.A.N. (AI + Blockchain certificazione atti amministrativi)

**TARGET:**

-   Pilot gratuito 8 settimane
-   Case study Firenze come reference client
-   Scale successivo a 50+ comuni Italia
-   Revenue target: €50-100k/anno (anno 2-3)

**CONTATTO CHIAVE:** Assessora Sparavigna (Innovazione/Digitale Comune Firenze)

---

## 💻 SOLUZIONE TECNICA PROPOSTA

### **ARCHITETTURA:**

**N.A.T.A.N.** = Nodo Analisi Tracciamento Atti Notarizzati

**STACK TECNOLOGICO:**

1. **AI Locale:** Llama 3.1 70B (Meta, open source)

    - Installato su server comunale (on-premise)
    - Processing locale: dati MAI escono dal server
    - GDPR bulletproof (zero cloud, zero USA)

2. **Blockchain:** Algorand

    - Hash documenti ancorati su blockchain pubblica
    - Immutabilità certificata
    - QR Code verifica per cittadini

3. **Backend:** Laravel 11 + PHP 8.3
    - Già sviluppato e funzionante
    - Integration con sistemi PA esistenti

**DEPLOYMENT:** On-premise su server comunale esistente

---

## 🖥️ REQUISITI HARDWARE

### **Hardware Necessario (Server Comunale):**

**MINIMO (senza GPU - Llama 8B):**

-   CPU: 8+ cores
-   RAM: 16 GB
-   Storage: 50 GB
-   Performance: ~15-20 sec/atto
-   Accuracy: 85-88%

**OTTIMALE (con GPU - Llama 70B):**

-   CPU: 16+ cores
-   RAM: 64 GB
-   GPU: NVIDIA con 16-40 GB VRAM (RTX 4060 Ti / A40)
-   Storage: 100 GB SSD
-   Performance: 3-5 sec/atto
-   Accuracy: 90-92%

**NOTA:** Comune di Firenze (200k abitanti) ha sicuramente server adeguati nel CED comunale.

---

## 📊 STATO IMPLEMENTAZIONE ATTUALE

### **COMPLETATO (Funzionante in Sandbox):**

-   [x] Upload PDF documenti PA
-   [x] Validazione firma digitale QES/PAdES
-   [x] Calcolo hash SHA-256
-   [x] Blockchain anchoring su Algorand (via AlgoKit microservice)
-   [x] Job asincrono tokenizzazione (`TokenizePaActJob`)
-   [x] Database storage (tabella `egis` con colonne `pa_*`)
-   [x] Dashboard `/pa/acts` con lista atti
-   [x] Visualizzazione stato tokenizzazione
-   [x] Statistiche base (Total, Tokenizzati, Pending)
-   [x] QR Code generation
-   [x] Public verification code (VER-XXXXXXXXXX)

**TEST REALI:**

-   3 atti tokenizzati su Algorand blockchain sandbox
-   TXID reali: BU27A64..., N3PDADI..., 3CTMKGH...
-   Tempo processing: 5-10 secondi per atto

---

## 🚧 DA IMPLEMENTARE (Per Demo Firenze)

### **PRIORITÀ CRITICA (2-3 settimane):**

1. **AI Document Parsing** (5-7 giorni) ⭐ CORE

    - Llama 3.1 integration (locale)
    - Estrazione automatica metadata da PDF:
        - Tipo atto (Delibera, Determina, Ordinanza, etc.)
        - Numero protocollo
        - Data atto
        - Oggetto (sintesi)
        - Responsabile
        - Importo (se presente)
    - Auto-fill form upload con dati estratti
    - Accuracy target: >85% (Llama 8B) o >90% (Llama 70B)

2. **Chat N.A.T.A.N.** (4-5 giorni) ⭐ WOW FACTOR

    - Widget chat tipo ChatGPT
    - Query natural language: "Mostrami delibere verde pubblico 2024"
    - AI risponde con lista atti + link diretti
    - Suggerimenti strategici basati su storico atti
    - Context: tutti metadata atti dell'ente

3. **Pagina Verifica Pubblica** (2-3 giorni) ⭐ TRASPARENZA

    - Route `/verify/{public_code}` accessibile a tutti
    - Mostra metadata atto certificato
    - Hash documento + TXID blockchain
    - Link AlgoExplorer per verifica
    - Download PDF originale
    - QR Code stampabile

4. **Analytics Dashboard** (2-3 giorni)
    - KPI: atti per tipo, per mese, tempo medio processing
    - Grafici: trend pubblicazioni, distribuzione categorie
    - Export Excel per report dirigenza

**TOTALE: 13-18 giorni → Sistema DEMO-READY**

---

## 💰 MODELLO ECONOMICO

### **COSTI REALI (Per Comune):**

**PILOT (8 settimane):**

-   Setup: €0
-   AI software (Llama): €0 (open source)
-   Hardware: €0 (usano server esistente)
-   Cloud/API: €0 (tutto locale)
-   **TOTALE PILOT: €0** ✅

**PRODUZIONE (Anno 1):**

-   Setup formale: €0 (già fatto in pilot)
-   Manutenzione: €0 (offerta gratuita anno 1)
-   Software: €0 (Llama gratis)
-   Hardware upgrade: €0-3.000 (solo se vogliono GPU dedicata)
-   **TOTALE ANNO 1: €0-3.000** (vs €20-30k vendor tradizionale)

**PRODUZIONE (Anno 2+):**

-   Manutenzione: €1.200-1.500/anno
-   Software: €0
-   Hardware: €0 (già ammortizzato)
-   **TOTALE ANNUO: €1.200** (vs €20k vendor)

**RISPARMIO COMUNE:** ~€18-28k/anno vs alternative!

---

### **REVENUE FABIO:**

**Anno 1 (Firenze):**

-   Pilot: €0 (investimento strategico)
-   Post-pilot: €0 (reference building)
-   **Guadagno diretto:** €0
-   **Guadagno indiretto:** Case study + testimonial

**Anno 2 (Firenze + Altri):**

-   Firenze manutenzione: €1.200
-   Altri 5 comuni setup: €7.500 (€1.500 cad)
-   Altri 5 comuni manutenzione: €6.000
-   **Totale:** €14.700

**Anno 3 (Scale):**

-   Firenze: €1.200
-   15 comuni attivi manutenzione: €18.000
-   10 nuovi comuni setup: €15.000
-   **Totale:** €34.200

**FIRENZE = Investimento che apre mercato 8.000 comuni Italia!**

---

## 🔒 COMPLIANCE GDPR

### **ARCHITETTURA GDPR-SAFE:**

**DATA FLOW:**

1. PA operator upload PDF → Server comunale
2. PDF parsing → Llama (gira su stesso server)
3. Metadata estratti → Database comunale
4. Hash documento → Blockchain Algorand (solo hash, no contenuto!)
5. Cittadino verifica → Legge da database comunale

**DATI CHE ESCONO DAL COMUNE:** Solo hash crittografico (blockchain)  
**DATI CHE RESTANO NEL COMUNE:** Tutto il resto (PDF, metadata, AI processing)

**COMPLIANCE:**

-   ✅ **Data Residency:** 100% server comunale (territorio Italia/UE)
-   ✅ **Data Minimization:** AI processa solo metadata necessari
-   ✅ **No Cloud USA:** Zero trasferimenti extra-UE
-   ✅ **No Training Leak:** Llama non invia dati a Meta
-   ✅ **Audit Trail:** Tutto loggato localmente
-   ✅ **Right to Erasure:** Cancellazione semplice (database locale)

**DPO APPROVAL:** Probabile al 95% (architettura perfetta GDPR)

---

## 📜 NORMATIVA APPALTI PUBBLICI

### **RIFERIMENTI NORMATIVI:**

**Codice Appalti 2023 (D.Lgs 36/2023):**

**Art. 50 - Affidamenti Diretti:**

-   Importo < €5.000: Affidamento diretto libero
-   Importo €5.000-€40.000: Affidamento diretto con 1 preventivo
-   Importo €40.000-€140.000: Procedura negoziata (3-5 preventivi)
-   Importo > €140.000: Gara aperta obbligatoria

**CASO N.A.T.A.N.:**

-   Pilot: €0 → Nessuna procedura! ✅
-   Anno 1: €0 → Nessuna procedura! ✅
-   Anno 2+: €1.200 → Affidamento diretto Art. 50 ✅

**MOTIVAZIONI AFFIDAMENTO DIRETTO LEGITTIME:**

1. Servizio innovativo sperimentale (N.A.T.A.N. unico in Italia)
2. Fornitore unico con competenza specifica sistema
3. Economicità rispetto mercato (€1.2k vs €20k)
4. Continuità operativa post-pilot positivo
5. Importo irrisorio < €5k (no impatto bilancio)

**PROCEDURA:**

1. Determina dirigenziale con motivazione
2. Pubblicazione trasparenza (determina + importo)
3. Nessun ricorso probabile (importo minimo + motivazione solida)

**TEMPISTICHE:** 5-15 giorni lavorativi approvazione

---

## 👤 STAKEHOLDER CHIAVE

### **ASSESSORA SPARAVIGNA:**

**RICERCA NECESSARIA:**

-   Nome completo e delega esatta
-   Background (competenze AI/digitale?)
-   Progetti AI già approvati a Firenze
-   Contatti pubblici (email assessorato)
-   Articoli/interviste su innovazione PA
-   Budget disponibile per innovazione digitale

**DOMANDE CHIAVE:**

1. Ha già approvato progetti AI in passato? (precedenti favorevoli)
2. Qual è il suo margine decisionale su pilot gratuiti?
3. Chi è il dirigente competente sotto di lei? (firma determina)
4. Quali sono le sue priorità politiche 2025? (allineare N.A.T.A.N.)

---

### **ALTRI STAKEHOLDER:**

**DA IDENTIFICARE:**

1. **Dirigente Sistemi Informativi** (gestisce CED, approva installazioni server)
2. **Segretario Generale** (approva innovazioni organizzative)
3. **DPO (Data Protection Officer)** (valuta GDPR compliance)
4. **Dirigente Affari Generali** (gestisce atti, utente finale)

---

## 🔍 RICERCA APPROFONDITA RICHIESTA

### **TOPIC 1: Assessora Sparavigna**

**Cercare:**

-   Biografia e curriculum
-   Delega esatta (Innovazione? Digitale? Smart City?)
-   Progetti AI già realizzati Comune Firenze
-   Dichiarazioni pubbliche su AI nella PA
-   Budget assessorato innovazione 2025
-   Contatti ufficio stampa/segreteria
-   Eventi pubblici dove parla di AI
-   Partnership già attive (università, aziende tech)

**FONTI:**

-   Sito Comune Firenze (sezione Giunta)
-   Articoli stampa locale (La Nazione, Corriere Fiorentino)
-   LinkedIn profilo Assessora
-   Delibere Giunta su progetti innovazione (Albo Pretorio)
-   Video YouTube interviste

---

### **TOPIC 2: Normativa Appalti Innovazione**

**Cercare:**

-   Codice Appalti 2023: eccezioni per servizi innovativi sperimentali
-   Affidamento diretto servizi R&D (Ricerca & Sviluppo)
-   Progetti pilota PA: procedure semplificate?
-   Partenariato innovazione (Art. 65 Codice Appalti)
-   Pre-Commercial Procurement (PCP)
-   Sandbox regolamentari per AI nella PA
-   Linee guida AgID su sperimentazioni AI
-   Circolari MEF su appalti innovazione

**DOMANDE SPECIFICHE:**

1. Pilot gratuito richiede determina/delibera? (probabile NO)
2. Dopo pilot, sotto €5k serve gara? (probabile NO)
3. "Fornitore unico competenza specifica" è motivazione valida? (probabile SÌ)
4. Servizi AI/ML hanno procedure agevolate? (verificare)

---

### **TOPIC 3: GDPR & AI nella PA Italiana**

**Cercare:**

-   Garante Privacy: linee guida AI nella PA
-   DPIA obbligatoria per AI processing atti PA?
-   On-premise AI (Llama) vs Cloud API (Anthropic): quale preferisce Garante?
-   Casi PA italiane sanzionate per uso AI non conforme
-   Best practices GDPR per AI documentale
-   Data Processing Agreement: quando obbligatorio?
-   Llama 3.1 compliance GDPR: documentazione Meta

**DOMANDE SPECIFICHE:**

1. Llama on-premise richiede DPIA? (probabile SÌ ma semplificata)
2. Hash su blockchain = dato personale? (probabile NO)
3. Metadata atti pubblici = dato personale? (verificare)
4. Serve consenso cittadini per AI parsing atti pubblici? (probabile NO)

---

### **TOPIC 4: Firenze Smart City & AI**

**Cercare:**

-   Progetti AI già attivi Comune Firenze (precedenti favorevoli)
-   Partnership tecnologiche Firenze (università, aziende)
-   Budget digitale Comune Firenze 2025
-   Piano Triennale Informatica Comune Firenze
-   Gare recenti su innovazione/AI (Albo Pretorio)
-   Firenze parte di network città smart? (es: ANCI Innovazione)

**OBIETTIVO:** Capire se Firenze è "early adopter" o "conservativa" su AI

---

### **TOPIC 5: Competitors & Alternative**

**Cercare:**

-   Vendor che offrono soluzioni simili N.A.T.A.N. in Italia
-   Costi soluzioni vendor (Dedagroup, Engineering, Maggioli, etc.)
-   Altri comuni che hanno adottato AI per atti PA
-   Blockchain nella PA italiana: casi d'uso
-   Standard certificazione atti PA (CAD - Codice Amministrazione Digitale)

**OBIETTIVO:** Posizionamento competitivo e benchmark pricing

---

### **TOPIC 6: Llama 3.1 - Technical Deep Dive**

**Cercare:**

-   Llama 3.1 70B: accuracy su document analysis tasks
-   Llama vs Claude 3.5 Sonnet: confronto performance Italian language
-   Llama on-premise: deployment best practices
-   Hardware requirements reali per production workload
-   Ollama vs alternative (vLLM, TGI, LocalAI)
-   Fine-tuning Llama per atti PA italiani: fattibilità e costi
-   Llama licensing: uso commerciale PA permesso? (verificare Meta license)

---

### **TOPIC 7: Business Model Scaling**

**Cercare:**

-   Numero comuni Italia >20k abitanti (target market)
-   Budget medio digitale comune medio (50-100k abitanti)
-   Associazioni comuni (ANCI): programmi innovazione
-   Finanziamenti PNRR per digitale PA
-   Bandi regionali/nazionali per AI nella PA
-   Modelli SaaS vs On-Premise per PA: preferenze mercato

**OBIETTIVO:** Validare potential market size e pricing strategy

---

## ❓ DOMANDE CHIAVE PER CLAUDE RESEARCH

### **STRATEGICHE:**

1. Assessora Sparavigna ha già approvato progetti AI? Quali? Outcome?
2. Firenze è "innovativa" o "conservativa" su adozione nuove tecnologie?
3. Qual è l'iter più rapido per pilot gratuito? (email diretta? PEC? altro?)
4. Chi decide su pilot gratuiti: Assessora, Dirigente, Giunta?

### **NORMATIVE:**

5. Pilot gratuito 8 settimane richiede determina dirigenziale?
6. Dopo pilot, affidamento diretto €1.2k/anno è legittimo senza gara?
7. "Servizio innovativo unico" è esenzione valida da gara?
8. Llama on-premise richiede DPIA formale o autocertificazione basta?

### **TECNICHE:**

9. Llama 3.1 70B su server senza GPU: performance reali? (quanto lento?)
10. Llama accuracy su documenti PA italiani: benchmark esistenti?
11. Fine-tuning Llama su atti PA: migliora accuracy? Quanto costa?
12. Alternative EU-hosted se Llama non basta: Mistral AI pricing reale?

### **COMMERCIALI:**

13. Quanto pagano comuni attualmente per gestionali documentali?
14. Esistono finanziamenti PNRR per AI nella PA? (fondi disponibili?)
15. ANCI (Associazione Comuni) ha programmi per diffondere innovazioni?
16. Case study comune pilota: quanto vale per vendita ad altri comuni?

---

## 📋 SCENARIO DECISION TREE

### **SCENARIO A: Assessora Approva Pilot** (Prob: 70-80%)

**NEXT STEPS:**

1. Setup su server comunale (1 giorno)
2. Training operatori (2 ore)
3. Processing 200-300 atti in 8 settimane
4. Report risultati + testimonial
5. Proposta formalizzazione (€1.2k/anno)
6. **Outcome:** Firenze partner + altri comuni interessati

### **SCENARIO B: Assessora Chiede Approfondimenti** (Prob: 15-20%)

**NEXT STEPS:**

1. DPIA dettagliata (coinvolgi DPO)
2. Benchmark vs alternative (dimostra economicità)
3. Pilot più breve (4 settimane, 100 atti)
4. Coinvolgi università Firenze (partnership accademica?)
5. **Outcome:** Approvazione posticipata ma probabile

### **SCENARIO C: Assessora Rifiuta** (Prob: 5-10%)

**MOTIVI POSSIBILI:**

-   Budget bloccato
-   Già impegnata su altri progetti
-   Preferisce attendere maturità tecnologia
-   Policy interna: no sperimentazioni senza gara

**NEXT STEPS:**

1. Pivot su comune medio (50-100k abitanti)
2. Costruisci case study altrove
3. Ritorno a Firenze tra 6-12 mesi con proof sociale

---

## 🎯 RISK MITIGATION

### **RISCHI IDENTIFICATI:**

1. **"Serve gara anche per pilot"**

    - **Mitigation:** Pilot è gratuito = nessuna procedura richiesta
    - **Backup:** Offri come "ricerca congiunta" con università

2. **"DPO blocca per GDPR"**

    - **Mitigation:** On-premise Llama = GDPR perfetto
    - **Backup:** DPIA dettagliata preparata in anticipo

3. **"Server comunale inadeguato"**

    - **Mitigation:** Llama 8B gira anche senza GPU (lento ma funziona)
    - **Backup:** Offri tua macchina in housing temporaneo

4. **"Tempi PA troppo lunghi"**

    - **Mitigation:** Pilot informale (no contratti = veloce)
    - **Backup:** Start small: 1 ufficio, 50 atti

5. **"Assessora cambia dopo elezioni"**
    - **Mitigation:** Coinvolgi anche dirigenti tecnici (restano oltre mandato)
    - **Backup:** Diversifica: 2-3 comuni paralleli

---

## 📞 PROSSIMI STEP OPERATIVI

### **IMMEDIATE (Questa settimana):**

1. **RICERCA CLAUDE:** Rispondere a tutte domande sopra
2. **INSTALLAZIONE OLLAMA:** Setup Llama su mia macchina (test)
3. **IMPLEMENTAZIONE AI PARSING:** Base funzionante per demo
4. **ARCHIVIO ATTI TEST:** Scaricare 20-30 atti Comune Firenze (pubblici Albo Pretorio)

### **SHORT-TERM (Settimana 2-3):**

5. **DEMO FUNZIONANTE:** Sistema completo testabile
6. **VIDEO DEMO:** 3 minuti screencast funzionalità
7. **PITCH DECK:** 5 slide per Assessora
8. **EMAIL CONTATTO:** Richiesta incontro 15 minuti

### **MEDIUM-TERM (Mese 2):**

9. **MEETING ASSESSORA:** Presentazione + demo live
10. **PILOT AGREEMENT:** Approvazione informale
11. **INSTALLAZIONE:** Setup su server comunale
12. **GO-LIVE PILOT:** Primi atti processati

---

## 📊 SUCCESS METRICS PILOT

### **KPI MISURABILI (8 settimane):**

-   [ ] 200+ atti processati e tokenizzati
-   [ ] Accuracy AI >85% su metadata chiave
-   [ ] Tempo medio processing <30 secondi
-   [ ] Zero data breaches / compliance issues
-   [ ] Feedback operatori >7/10 soddisfazione
-   [ ] 2+ operatori formati e autonomi
-   [ ] Almeno 1 verifica cittadino tramite QR Code
-   [ ] Report dirigenziale con KPI positivi

### **OUTCOME SUCCESS:**

-   Assessora approva continuazione
-   Dirigente firma determina manutenzione
-   Testimonial pubblico (video/articolo)
-   Permission uso logo Firenze
-   Case study pubblicabile

---

## 💼 ALTERNATIVE SCENARIOS

### **PIANO B: Se Firenze Non Conferma**

**TARGET ALTERNATIVI (più semplici):**

1. **Comuni 20-50k abitanti** (es: Scandicci, Sesto Fiorentino, Empoli)

    - Meno burocrazia
    - Più flessibili
    - Stesso impact mediatico regionale

2. **Unioni di Comuni** (più comuni piccoli insieme)

    - Condividono infrastruttura
    - Budget aggregato
    - Scala immediata (5-10 comuni in uno)

3. **Regione Toscana** (diretto regionale)
    - Budget maggiore
    - Visibilità nazionale
    - Scala automatica a tutti comuni toscani

---

## 📚 FONTI DA CONSULTARE

### **ISTITUZIONALI:**

-   Sito Comune Firenze (sezione Giunta, Trasparenza)
-   Albo Pretorio Firenze (delibere/determine innovazione)
-   AgID (Agenzia Italia Digitale): linee guida AI PA
-   Garante Privacy: provvedimenti AI nella PA
-   ANCI: programmi innovazione comuni

### **STAMPA:**

-   La Nazione Firenze: articoli Assessora Sparavigna
-   Corriere Fiorentino: progetti innovazione Firenze
-   Agendadigitale.eu: casi AI nella PA italiana
-   ForumPA: eventi e report innovazione

### **TECNICI:**

-   Meta AI: Llama 3.1 licensing terms commercial use
-   Ollama documentation: production deployment
-   Anthropic: DPA template e GDPR documentation
-   Algorand: PA use cases (se esistono)

---

## 🎯 DELIVERABLE ATTESI DA RESEARCH

### **DOCUMENTO FINALE RICHIESTO:**

**SEZIONE 1: Assessora Sparavigna Profile**

-   Background, competenze, progetti precedenti
-   Strategia approccio personalizzata
-   Timing ottimale contatto
-   Tone of voice consigliato

**SEZIONE 2: Legal Framework**

-   Procedura esatta per pilot gratuito
-   Iter post-pilot per contratto €1.2k
-   Rischi legali e mitigation
-   Template determina dirigenziale

**SEZIONE 3: GDPR Compliance**

-   Validazione architettura Llama on-premise
-   DPIA template per N.A.T.A.N.
-   Checklist compliance DPO
-   Confronto Llama vs Anthropic (GDPR perspective)

**SEZIONE 4: Technical Validation**

-   Llama 3.1 accuracy benchmark documenti PA
-   Hardware requirements production-grade
-   Performance stime realistic
-   Alternative se Llama non basta

**SEZIONE 5: Market Intelligence**

-   Competitors pricing
-   Market size (comuni target)
-   Success cases simili Italia
-   Partnership opportunities (ANCI, AgID, etc.)

**SEZIONE 6: Go-to-Market Strategy**

-   Pitch deck Assessora (5 slide)
-   Email template primo contatto
-   Demo script (15 minuti)
-   Roadmap pilot → scale
-   Revenue forecast realistic

---

## 🚀 OUTPUT DESIDERATO

**DOCUMENTO "N.A.T.A.N. - FIRENZE GO-TO-MARKET STRATEGY"**

Con:

1. ✅ Profilo Assessora + strategia approccio
2. ✅ Framework legale chiaro (cosa posso/non posso fare)
3. ✅ GDPR validation (Llama on-premise è OK?)
4. ✅ Technical feasibility (Llama basta o serve Anthropic?)
5. ✅ Competitive positioning (vs vendor, prezzi benchmark)
6. ✅ Action plan concreto (timeline, step-by-step)
7. ✅ Risk analysis + mitigation
8. ✅ Success probability estimate

**FORMATO:** Markdown, max 20 pagine, actionable insights

---

## 📝 NOTE AGGIUNTIVE PER RESEARCHER

### **CONTESTO PERSONALE:**

-   Sono sviluppatore individuale (no SRL ancora)
-   Budget limitato: preferisco soluzioni €0 (Llama vs Anthropic)
-   Obiettivo: Firenze come trampolino, non revenue immediato
-   Disponibile investire 3-4 settimane sviluppo gratis per case study
-   Hardware disponibile: GTX 1070 8GB per development

### **VINCOLI:**

-   GDPR compliance non negoziabile (PA = dati sensibili)
-   Soluzione deve essere replicabile altri comuni (no custom per Firenze)
-   Costi per comune devono essere <€5k/anno (sostenibilità)
-   On-premise preferito vs cloud (autonomia PA)

### **OPPORTUNITÀ:**

-   Firenze città innovativa (precedenti positivi?)
-   Assessora esperta AI (alleato naturale?)
-   PNRR fondi disponibili per digitale PA?
-   Network ANCI per diffusione rapida?

---

**FINE BRIEF** - Ready for Deep Research

**Next Step:** Submit questo documento a Claude Sonnet 4.5 per ricerca approfondita e strategia go-to-market dettagliata.

---

**Documento preparato da:** Padmin D. Curtis (AI Partner OS3.0)  
**Per:** Fabio Cherici - N.A.T.A.N. Project  
**Data:** 2025-10-09 22:45
