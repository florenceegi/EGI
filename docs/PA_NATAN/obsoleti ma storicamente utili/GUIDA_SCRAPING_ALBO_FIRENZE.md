# 📥 GUIDA - Scraping Albo Pretorio Firenze

**Script:** `scripts/scrape_albo_pretorio_firenze.py`  
**Scopo:** Scaricare atti reali da usare per testing N.A.T.A.N.  
**Legalità:** ✅ Albi pretori pubblici per legge (D.Lgs. 33/2013)

---

## 🎯 PERCHÉ È UTILE

### Vantaggi Atti Reali

-   ✅ **Testing accurato** con documenti veri
-   ✅ **Validazione AI** su atti autentici
-   ✅ **Demo presentazione** con dati reali
-   ✅ **Proof of concept** convincente

### Dati che Ottieni

-   PDF atti amministrativi firmati
-   Metadata: protocollo, data, tipo, titolo
-   Varietà tipologie: determine, delibere, ordinanze
-   File pronti per upload in N.A.T.A.N.

---

## ⚙️ SETUP (5 minuti)

### 1. Installa Dipendenze

```bash
cd /home/fabio/EGI

# Installa librerie Python
pip3 install requests beautifulsoup4 lxml

# Oppure con requirements.txt
cat > requirements-scraper.txt << 'EOF'
requests>=2.31.0
beautifulsoup4>=4.12.0
lxml>=4.9.0
EOF

pip3 install -r requirements-scraper.txt
```

### 2. Rendi Eseguibile

```bash
chmod +x scripts/scrape_albo_pretorio_firenze.py
```

---

## 🚀 USO BASE

### Comando Semplice (20 atti)

```bash
python3 scripts/scrape_albo_pretorio_firenze.py
```

**Output:**

```
storage/testing/firenze_atti/
├── pdf/
│   ├── Determina_123_2023_1.pdf
│   ├── Delibera_456_2023_2.pdf
│   └── ...
├── metadata/
│   └── atti_metadata_20251023_120000.json
├── SUMMARY.txt
└── scraping_log_20251023_120000.txt
```

### Opzioni Avanzate

```bash
# Scarica 50 atti
python3 scripts/scrape_albo_pretorio_firenze.py --limit 50

# Cambia directory output
python3 scripts/scrape_albo_pretorio_firenze.py --output /tmp/atti_test

# Delay maggiore (più rispettoso, più lento)
python3 scripts/scrape_albo_pretorio_firenze.py --delay 5.0

# Combinazione
python3 scripts/scrape_albo_pretorio_firenze.py --limit 30 --delay 3.0
```

---

## 🔧 ADATTAMENTO NECESSARIO

⚠️ **IMPORTANTE:** Lo script contiene una struttura HTML **generica**. Prima di usarlo devi:

### Step 1: Visita Albo Pretorio

```bash
# Apri nel browser
firefox https://albopretorionline.comune.fi.it
```

### Step 2: Ispeziona HTML

1. Apri DevTools (F12)
2. Vai alla sezione "Atti Pubblicati" o "Ricerca"
3. Ispeziona la struttura HTML:
    - Come sono organizzati gli atti? (div, table, list?)
    - Quali classi CSS usano?
    - Dove sono i link ai PDF?
    - Dove sono i metadata (protocollo, data, tipo)?

### Step 3: Adatta Codice

Apri lo script:

```bash
nano scripts/scrape_albo_pretorio_firenze.py
```

**Trova questa sezione (linea ~80):**

```python
# Trova elementi atti (struttura HTML da adattare alla realtà)
# Esempio generico - DA ADATTARE DOPO ISPEZIONE SITO REALE
atti_elements = soup.find_all('div', class_='atto-item')[:limit]
```

**Modifica con la struttura reale:**

Esempio ipotetico se gli atti sono in una tabella:

```python
# Struttura reale Firenze (ESEMPIO)
table = soup.find('table', {'id': 'tabella-atti'})
if table:
    atti_elements = table.find_all('tr', class_='riga-atto')[:limit]
```

**E poi adatta estrazione metadata (linea ~90):**

```python
# PRIMA (generico):
title_el = atto_el.find('h3') or atto_el.find('a', class_='title')

# DOPO (adattato a Firenze):
title_el = atto_el.find('td', class_='colonna-oggetto')
```

---

## 📋 ESEMPIO COMPLETO ADATTAMENTO

Supponiamo che ispezionando trovi questa struttura:

```html
<div id="risultati-ricerca">
    <div class="atto-row">
        <h4 class="atto-titolo">Determina n. 123 del 2023</h4>
        <span class="atto-data">23/10/2023</span>
        <span class="atto-tipo">Determina Dirigenziale</span>
        <a href="/download/atto123.pdf" class="btn-scarica">PDF</a>
    </div>
    <!-- altri atti... -->
</div>
```

**Adatta così:**

```python
# Trova container risultati
risultati = soup.find('div', {'id': 'risultati-ricerca'})
if not risultati:
    self.log("Container risultati non trovato")
    return []

# Trova atti
atti_elements = risultati.find_all('div', class_='atto-row')[:limit]

for idx, atto_el in enumerate(atti_elements, 1):
    # Titolo
    title_el = atto_el.find('h4', class_='atto-titolo')
    title = title_el.text.strip() if title_el else f"Atto {idx}"

    # Data
    date_el = atto_el.find('span', class_='atto-data')
    pub_date = date_el.text.strip() if date_el else None

    # Tipo
    type_el = atto_el.find('span', class_='atto-tipo')
    doc_type = type_el.text.strip() if type_el else "Generico"

    # Link PDF
    pdf_el = atto_el.find('a', class_='btn-scarica')
    if not pdf_el:
        continue
    pdf_url = urljoin(self.base_url, pdf_el['href'])

    # ... resto codice
```

---

## 🧪 TEST RAPIDO

### 1. Test Connessione

```python
# Crea file test_connection.py
import requests
from bs4 import BeautifulSoup

url = "https://albopretorionline.comune.fi.it"
response = requests.get(url)
print(f"Status: {response.status_code}")
print(f"Titolo pagina: {BeautifulSoup(response.content, 'html.parser').title.text}")
```

```bash
python3 test_connection.py
```

### 2. Test Scraping (1 solo atto)

```bash
python3 scripts/scrape_albo_pretorio_firenze.py --limit 1
```

Verifica:

-   Log file creato?
-   PDF scaricato?
-   Metadata JSON corretto?

---

## 📊 OUTPUT ATTESO

### Directory Structure

```
storage/testing/firenze_atti/
├── pdf/                                    # PDF scaricati
│   ├── Determina_Dirigenziale_123_1.pdf
│   ├── Delibera_Giunta_456_2.pdf
│   ├── Ordinanza_Sindaco_789_3.pdf
│   └── ...
│
├── metadata/                               # Metadata JSON
│   └── atti_metadata_20251023_120000.json
│
├── SUMMARY.txt                             # Riepilogo leggibile
└── scraping_log_20251023_120000.txt       # Log operazioni
```

### Metadata JSON Example

```json
[
  {
    "index": 1,
    "title": "Determina Dirigenziale n. 123/2023",
    "protocol_number": "PG/2023/123456",
    "publication_date": "23/10/2023",
    "doc_type": "Determina Dirigenziale",
    "pdf_url": "https://albopretorio.../atto123.pdf",
    "local_path": "storage/testing/firenze_atti/pdf/Determina_123_1.pdf",
    "file_size": 524288,
    "file_hash": "abc123def456...",
    "source_page": "https://albopretorio...",
    "scraped_at": "2023-10-23T12:00:00"
  },
  ...
]
```

---

## 🎯 INTEGRAZIONE CON N.A.T.A.N.

### Dopo Scraping Completo

```bash
# 1. Verifica atti scaricati
ls -lh storage/testing/firenze_atti/pdf/
cat storage/testing/firenze_atti/SUMMARY.txt

# 2. Copia in directory test PA
mkdir -p storage/testing/real_signed_pa_acts
cp storage/testing/firenze_atti/pdf/*.pdf storage/testing/real_signed_pa_acts/

# 3. Usa per test upload
# Vai su dashboard PA → Upload → Seleziona PDF scaricati
```

### Testing Automatico

Puoi creare uno script per caricare automaticamente:

```bash
# test_upload_firenze_atti.sh
#!/bin/bash

PDF_DIR="storage/testing/firenze_atti/pdf"

for pdf in $PDF_DIR/*.pdf; do
    echo "Testing atto: $(basename $pdf)"

    # Qui puoi usare curl per API upload
    # O semplicemente testare manualmente

    echo "Atto testato. Next..."
    sleep 2
done
```

---

## ⚠️ TROUBLESHOOTING

### Problema: "Nessun atto trovato"

**Causa:** Struttura HTML diversa da quella attesa

**Fix:**

1. Verifica URL albo pretorio corretto
2. Ispeziona HTML pagina (salva in `debug_page.html`)
3. Adatta selettori CSS nel codice

### Problema: "PDF non scaricabile"

**Causa:** Link PDF protetto o diverso

**Fix:**

```python
# Prova diversi pattern per trovare PDF
pdf_link = (
    atto_el.find('a', href=lambda x: x and '.pdf' in x) or
    atto_el.find('a', class_='download') or
    atto_el.find('a', title=lambda x: x and 'scarica' in x.lower())
)
```

### Problema: "Timeout / Errore rete"

**Causa:** Rete lenta o server sovraccarico

**Fix:**

```bash
# Aumenta delay
python3 scripts/scrape_albo_pretorio_firenze.py --delay 5.0

# Riduce numero atti
python3 scripts/scrape_albo_pretorio_firenze.py --limit 10
```

---

## 📚 ALTERNATIVE

### Se Albo Pretorio Firenze Non Funziona

**Opzione A: Altri Comuni**

```python
# Cambia base_url
self.base_url = "https://albopretorio.comune.prato.it"
```

**Opzione B: Download Manuale**

1. Vai su albo pretorio
2. Scarica manualmente 10-20 PDF
3. Salva in `storage/testing/real_signed_pa_acts/`
4. Crea metadata manualmente

**Opzione C: Usa Atti Mock**

```bash
# Genera atti di test
php artisan db:seed --class=PAActsMockSeeder
```

---

## 🎓 BEST PRACTICES

### Rate Limiting Rispettoso

```python
# ✅ BUONO: 2-3 secondi tra richieste
self.delay_between_requests = 2.0

# ❌ CATTIVO: troppo veloce, sovraccarica server
self.delay_between_requests = 0.1
```

### Gestione Errori

```python
try:
    response = self.session.get(url, timeout=30)
    response.raise_for_status()
except requests.Timeout:
    self.log("Timeout - server lento, riprova")
except requests.HTTPError as e:
    self.log(f"Errore HTTP {e.response.status_code}")
```

### Logging Completo

Ogni operazione viene loggata:

-   Richieste HTTP
-   PDF scaricati
-   Errori
-   Timestamp

Così puoi debuggare facilmente.

---

## 🚀 WORKFLOW COMPLETO

### Preparazione Testing (30 min)

```bash
# 1. Setup
pip3 install requests beautifulsoup4

# 2. Ispeziona albo pretorio (browser)
firefox https://albopretorionline.comune.fi.it

# 3. Adatta script (nano/vim)
nano scripts/scrape_albo_pretorio_firenze.py

# 4. Test 1 atto
python3 scripts/scrape_albo_pretorio_firenze.py --limit 1

# 5. Se OK, scarica 20-30 atti
python3 scripts/scrape_albo_pretorio_firenze.py --limit 30

# 6. Verifica risultati
ls -lh storage/testing/firenze_atti/pdf/
cat storage/testing/firenze_atti/SUMMARY.txt

# 7. Copia per testing
cp storage/testing/firenze_atti/pdf/*.pdf storage/testing/real_signed_pa_acts/

# 8. Test upload in N.A.T.A.N.
# Dashboard PA → Upload → Test con atti reali!
```

---

## 📊 METRICHE SUCCESS

### Obiettivi

-   ✅ Almeno 20 atti scaricati
-   ✅ Varietà tipologie (determine, delibere, ordinanze)
-   ✅ PDF validi e leggibili
-   ✅ Metadata estratti correttamente

### Validazione

```bash
# Count PDF
ls storage/testing/firenze_atti/pdf/*.pdf | wc -l

# Check dimensioni (PDF validi > 10KB)
du -sh storage/testing/firenze_atti/pdf/*.pdf

# Verifica metadata
cat storage/testing/firenze_atti/metadata/*.json | jq '.[] | .title'
```

---

## 🎯 VALORE PER PRESENTAZIONE

**Con Atti Reali Puoi Dire:**

_"Questo non è un test con dati mock. Questi sono **atti reali** scaricati dall'albo pretorio del Comune di Firenze. PDF firmati, autentici, con tutta la complessità del mondo reale. E N.A.T.A.N. li processa perfettamente."_

**Impatto Demo:**

-   🎯 **Credibilità 10x**
-   🎯 **Proof of concept tangibile**
-   🎯 **Zero dubbi su fattibilità**

---

## 📝 NOTE LEGALI

### Legalità Scraping

✅ **LEGALE:**

-   Albi pretori sono pubblici per legge (trasparenza)
-   Dati già accessibili a tutti
-   Nessun login/paywall
-   Uso per testing/ricerca

✅ **RISPETTOSO:**

-   Rate limiting (2 sec tra richieste)
-   User-Agent identificabile
-   Rispetta robots.txt
-   Non sovraccarica server

---

**READY TO SCRAPE!** 🚀

Adatta lo script, testa con 1 atto, poi scarica 20-30 atti reali per testing N.A.T.A.N.!
