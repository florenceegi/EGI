# Gestione Partner Commerciale – Architettura PartnerHub

## 1. Scopo del documento
Questo documento descrive **come viene gestito un partner commerciale** all’interno dell’ecosistema **HUB‑EGI**, secondo l’architettura definita.

Il partner commerciale è un soggetto che **vende servizi FlorenceEGI** (ArtEGI, NATAN_LOC, EGI‑INFO, TOSCA_BANDI, o nuovi project) e ne **orchestra l’attivazione** tramite API, senza duplicare logica o dati nei project core.

---

## 2. Posizionamento architetturale

Il partner commerciale **non è un tenant** di un project esistente.
È un **project dedicato**, denominato:

> **PartnerHub**

Caratteristiche:
- Project **orizzontale**
- Funzione di **orchestrazione commerciale**
- Nessuna logica di servizio core
- Comunicazione esclusiva via API verso gli altri project

---

## 3. PartnerHub come project multi‑tenant

PartnerHub è progettato per operare **a scala globale**.

### 3.1 Tenant
- Ogni **tenant** rappresenta un **partner commerciale globale**
- Esempi: agenzie, integratori, reseller, enti culturali, soggetti PA

### 3.2 Succursali (branch)
Ogni partner può avere **più succursali**, tipicamente legate a:
- aree geografiche
- paesi
- regioni
- divisioni operative

Ogni succursale può avere:
- valuta
- lingua
- timezone
- listino dedicato
- regole commerciali e fiscali locali

---

## 4. Ruolo di PartnerHub

PartnerHub è responsabile di:
- gestione partner e succursali
- listini commerciali
- contratti e abbonamenti
- pagamenti e rinnovi
- commissioni partner
- provisioning dei servizi sui project reali
- audit e tracciabilità

PartnerHub **non implementa** servizi ArtEGI, NATAN_LOC, ecc.

---

## 5. Listino e servizi

### 5.1 Listino partner
Ogni succursale dispone di uno o più **listini**.

Ogni voce di listino:
- rappresenta un servizio vendibile
- è mappata a un **service reale** in un project esistente

### 5.2 Mappatura verso i project

Ogni servizio di listino è definito da:
- `project_key` (es. artegi, natan_loc)
- `service_key` (es. collection_premium, natan_small_pa)
- endpoint API interno per provisioning

PartnerHub usa queste informazioni per **attivare o disattivare servizi** nei project target.

---

## 6. Flusso di vendita

1. Il partner vende un servizio tramite PartnerHub
2. PartnerHub registra il contratto
3. PartnerHub gestisce pagamenti, rinnovi e stati
4. PartnerHub calcola lo stato di entitlement
5. PartnerHub chiama le API interne del project target
6. Il project target abilita o disabilita il servizio

---

## 7. Entitlement e stato del servizio

### 7.1 Modello concettuale

PartnerHub mantiene uno **stato completo** del servizio:
- contratto
- periodo pagato
- stato commerciale
- origine (partner, trial, promo, diretto)

Da questo stato deriva un solo valore finale:

> **il servizio è attivo o non attivo**

---

## 8. Interfaccia finale verso i project

I project (ArtEGI, NATAN_LOC, ecc.) **ignorano completamente**:
- partner
- listini
- contratti
- pagamenti
- branch

Ricevono solo:

```json
{
  "service_key": "collection_premium",
  "active": true
}
```

Oppure internamente:

```ts
serviceActive === true | false
```

Questa è l’unica informazione necessaria per:
- abilitare feature
- bloccare accessi
- mostrare upgrade

---

## 9. Principio architetturale chiave

> **La complessità commerciale termina in PartnerHub.**

I project core:
- non contengono logica di business commerciale
- non conoscono i partner
- non gestiscono pagamenti o contratti

Usano solo un **booleano di verità operativa**.

---

## 10. Benefici della soluzione

- Scalabilità globale
- Supporto nativo a partner multipli
- Gestione per aree geografiche
- Separazione totale delle responsabilità
- Project core semplici e stabili
- Possibilità di vendere interi project o soluzioni white‑label

---

## 11. Sintesi finale

Il partner commerciale è:
- un **project dedicato** (PartnerHub)
- **multi‑tenant**
- con **succursali per area**
- che orchestra servizi venduti tramite API

Ogni servizio, nel punto finale di consumo, si riduce a:

> **attivo / non attivo**

Questa architettura consente a FlorenceEGI di crescere in modo ordinato, globale e sostenibile.

