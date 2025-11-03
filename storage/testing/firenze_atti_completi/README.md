# 📚 Atti del Comune di Firenze - Dataset Completo

## 📊 Contenuto

### JSON Files (`json/`)

-   **DG_2018_2025.json** - 3.043 Deliberazioni di Giunta (2021-2025)
-   **DC_2018_2025.json** - 40 Deliberazioni di Consiglio (2021-2025)
-   **tutti*atti*\*.json** - File master con tutti gli atti

### PDF Files (`pdf/`)

-   10 PDF di esempio di deliberazioni recenti (2025)
-   Totale: ~11 MB

---

## 🔍 Struttura Dati

Ogni atto contiene:

```json
{
    "id": 12345,
    "numeroAdozione": "00123",
    "dataAdozione": 1641855600000,
    "dataEsecutivita": 1643324400000,
    "dataPubblicazione": 1642460400000,
    "ufficio": "Direzione XXX",
    "relatore": "Nome Cognome",
    "oggetto": "Descrizione completa dell'atto...",
    "allegati": [
        {
            "id": 55482,
            "nome": "documento.pdf",
            "link": "/trasparenza-atti-allegati/12345/documento.pdf",
            "contentType": "application/pdf",
            "principale": true
        }
    ],
    "tipoAttoDto": {
        "codice": "DG",
        "nome": "Delibera di Giunta"
    },
    "votazioni": {
        "numPresenti": 8,
        "numFavorevoli": 8,
        "numContrari": 0,
        "numAstenuti": 0,
        "nomiFavorevoli": "..."
    }
}
```

---

## 🚀 Come Usare i Dati

### 1. Leggere tutti gli atti

```python
import json

with open('json/DG_2018_2025.json', 'r', encoding='utf-8') as f:
    atti = json.load(f)

print(f"Totale atti: {len(atti)}")
print(f"Primo atto: {atti[0]['oggetto']}")
```

### 2. Filtrare per anno

```python
import json
from datetime import datetime

with open('json/DG_2018_2025.json', 'r', encoding='utf-8') as f:
    atti = json.load(f)

atti_2024 = [
    a for a in atti
    if datetime.fromtimestamp(a['dataAdozione']/1000).year == 2024
]
print(f"Atti 2024: {len(atti_2024)}")
```

### 3. Cercare per keyword

```python
import json

with open('json/DG_2018_2025.json', 'r', encoding='utf-8') as f:
    atti = json.load(f)

keyword = "mobilità"
risultati = [
    a for a in atti
    if keyword.lower() in a['oggetto'].lower()
]
print(f"Atti su '{keyword}': {len(risultati)}")
```

### 4. Scaricare PDF allegati

```python
import requests
import json

base_url = "https://accessoconcertificato.comune.fi.it"

with open('json/DG_2018_2025.json', 'r', encoding='utf-8') as f:
    atti = json.load(f)

# Prendi primo atto con PDF
for atto in atti:
    for allegato in atto.get('allegati', []):
        if allegato.get('contentType') == 'application/pdf':
            pdf_url = base_url + allegato['link']
            filename = f"{atto['numeroAdozione']}_{allegato['id']}.pdf"

            response = requests.get(pdf_url)
            with open(filename, 'wb') as f:
                f.write(response.content)

            print(f"Scaricato: {filename}")
            break
    break
```

---

## 📈 Statistiche

### Per Anno (Deliberazioni di Giunta):

-   **2021**: 668 atti
-   **2022**: 709 atti
-   **2023**: 690 atti
-   **2024**: 597 atti
-   **2025**: 379 atti (in corso)

### Totale: 3.083 atti

---

## 🔄 Aggiornare i Dati

Per scaricare atti aggiornati:

```bash
# Solo atti recenti
python3 ../../scripts/scrape_firenze_deliberazioni.py --anno-inizio 2025 --anno-fine 2025

# Tutti gli atti
python3 ../../scripts/scrape_firenze_deliberazioni.py

# Con download PDF (massimo 50)
python3 ../../scripts/scrape_firenze_deliberazioni.py --download-pdfs --max-pdf-per-type 50
```

---

## 🎯 Integrazione con N.A.T.A.N.

Questi dati sono pronti per essere usati con il sistema N.A.T.A.N. per:

1. **Analisi AI** - Estrazione automatica di metadati
2. **RAG** - Question answering su migliaia di atti
3. **Blockchain** - Ancoraggio e verifica
4. **Ricerca Intelligente** - Query in linguaggio naturale

---

## 📝 Note

-   Gli anni 2018-2020 non sono disponibili nel database del Comune
-   Le Determinazioni Dirigenziali richiedono un'API diversa
-   I link PDF sono relativi, usare base_url per scaricarli

---

## 📞 Supporto

Per aggiornamenti o modifiche allo scraper:

-   Script: `scripts/scrape_firenze_deliberazioni.py`
-   Help: `python3 scripts/scrape_firenze_deliberazioni.py --help`

---

**Data creazione**: 23 Ottobre 2025  
**Fonte**: Comune di Firenze - Amministrazione Trasparente  
**URL**: https://accessoconcertificato.comune.fi.it/trasparenza-atti/
