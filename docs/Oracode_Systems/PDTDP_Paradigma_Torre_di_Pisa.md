# PDTDP --- Paradigma della Torre di Pisa

## Principio architetturale per la gestione delle deviazioni nei sistemi complessi

**Autore:** Fabio Cherici\
**Sistema:** Oracode System\
**Stato:** Principio Architetturale\
**Versione:** 1.0\
**Data:** 2026

------------------------------------------------------------------------

# 1. Definizione

Il **Paradigma della Torre di Pisa (PDTDP)** afferma che:

> Un sistema complesso non diventa robusto eliminando ogni deviazione,
> ma distinguendo tra errori strutturali da correggere e deviazioni
> evolutive da integrare.

La robustezza nasce dalla capacità di:

-   proteggere gli invarianti del sistema
-   permettere l'adattamento delle componenti non critiche

------------------------------------------------------------------------

# 2. Origine del paradigma

Il paradigma prende ispirazione dalla costruzione della **Torre di
Pisa**.

Un errore iniziale nel terreno provocò l'inclinazione della struttura.

Gli architetti successivi non demolirono l'opera, ma:

-   analizzarono il problema
-   adattarono la costruzione ai vincoli emergenti
-   compensarono progressivamente l'inclinazione

Il risultato fu una struttura stabile la cui identità deriva proprio
dalla deviazione originaria.

Questo comportamento rappresenta una dinamica tipica dei **sistemi
complessi adattivi**.

------------------------------------------------------------------------

# 3. Il principio fondamentale

Ogni sistema complesso deve distinguere tra due categorie di deviazione.

## 3.1 Deviazioni strutturali critiche

Sono deviazioni che violano gli **invarianti fondamentali del sistema**.

Esempi:

-   violazioni di sicurezza
-   perdita di integrità dei dati
-   rottura delle transazioni atomiche
-   errori nei protocolli fondamentali
-   violazioni architetturali

### Regola operativa

deviazione critica → correzione immediata

Queste deviazioni **non possono essere integrate nel sistema**.

------------------------------------------------------------------------

## 3.2 Deviazioni evolutive

Sono deviazioni che emergono dall'uso reale del sistema ma **non
compromettono il nucleo strutturale**.

Esempi:

-   nuovi pattern di utilizzo
-   evoluzione dei flussi operativi
-   reinterpretazione funzionale di strumenti esistenti
-   adattamenti organizzativi
-   nuovi casi d'uso emergenti

### Regola operativa

deviazione evolutiva → osservazione → possibile integrazione

Queste deviazioni possono diventare **nuova struttura del sistema**.

------------------------------------------------------------------------

# 4. Architettura derivata

Il PDTDP introduce una distinzione fondamentale nella progettazione dei
sistemi:

NUCLEO RIGIDO\
+\
PERIFERIA ADATTIVA

------------------------------------------------------------------------

## 4.1 Nucleo rigido

Contiene gli **invarianti del sistema**.

Esempi:

-   sicurezza
-   integrità dei dati
-   responsabilità
-   tracciabilità
-   protocolli fondamentali
-   coerenza semantica

Il nucleo deve essere **rigorosamente protetto**.

------------------------------------------------------------------------

## 4.2 Periferia adattiva

Comprende gli elementi che possono evolvere.

Esempi:

-   interfacce
-   flussi operativi
-   modelli di utilizzo
-   integrazioni
-   pattern emergenti

Queste componenti devono essere:

-   osservabili
-   adattabili
-   migliorabili nel tempo

------------------------------------------------------------------------

# 5. Processo operativo del paradigma

Il PDTDP può essere espresso come sequenza operativa:

1.  Identificare gli invarianti del sistema
2.  Proteggere gli invarianti con rigore assoluto
3.  Rilevare le deviazioni emergenti
4.  Misurare l'impatto della deviazione
5.  Distinguere tra deviazioni critiche e deviazioni evolutive
6.  Correggere immediatamente quelle critiche
7.  Osservare e integrare quelle evolutive

------------------------------------------------------------------------

# 6. Formalizzazione

Sia:

S = sistema\
I = insieme degli invarianti\
D = deviazione osservata

Allora:

se D viola I → correzione immediata\
altrimenti → osservazione e possibile integrazione

La stabilità del sistema diventa:

STABILITÀ = protezione(invarianti)\
EVOLUZIONE = integrazione(deviazioni non critiche)

------------------------------------------------------------------------

# 7. Posizionamento nell'Oracode System

Il PDTDP agisce come **principio architetturale trasversale**.

## In OS3

  PDTDP                    OS3
  ------------------------ ---------------------
  deviazione strutturale   violazione P0
  deviazione evolutiva     miglioramento P2/P3
  nucleo rigido            invarianti OS3
  periferia adattiva       implementazioni

## In OS4

Nel sistema OS4 gli invarianti corrispondono alle **regole
epistemiche**.

Le deviazioni evolutive corrispondono ai **pattern cognitivi
emergenti**.

Il paradigma è coerente con l'**Assioma 0**:

> La verità è funzione operativa.

Un sistema resta vero se continua a funzionare mentre evolve.

## In OSZ

Nel modello bio‑architetturale OSZ:

  OSZ                        PDTDP
  -------------------------- ------------------------
  interfacce                 nucleo rigido
  implementazioni            periferia adattiva
  istanze                    deviazioni evolutive
  violazioni di protocollo   deviazioni strutturali

------------------------------------------------------------------------

# 8. Sintesi

Il Paradigma della Torre di Pisa può essere riassunto nella formula:

Proteggi il nucleo.\
Permetti l'evoluzione del resto.

Un sistema intelligente non elimina il caos.

Lo **canalizza entro una struttura stabile**.

------------------------------------------------------------------------

# 9. Formula finale

Un sistema robusto non elimina le deviazioni.\
Le classifica.

Questo principio costituisce uno dei **fondamenti architetturali
dell'Oracode System**.
