# 📥 Scraper PA con Supporto MongoDB

## 🎯 Overview

Gli scraper per atti PA sono stati aggiornati per supportare:
- **Dry-run**: Verifica quanti atti verranno processati senza scaricarli
- **Import MongoDB**: Salva direttamente in MongoDB per RAG search
- **Tracking costi**: Report dettagliato con token e costi in €

## 📋 Scraper Disponibili

1. **`scrape_firenze_deliberazioni.py`** - API scraping deliberazioni/determinazioni
2. **`scrape_albo_firenze_v2.py`** - HTML scraping albo pretorio
3. **`scrape_albo_pretorio_firenze.py`** - HTML scraping albo pretorio (alternativo)

## 🚀 Utilizzo

### Dry-Run (Preview)

Verifica quanti atti verranno processati senza scaricarli:

```bash
# Deliberazioni - Dry-run anno singolo
cd /home/fabio/EGI/scripts
python3 scrape_firenze_deliberazioni.py --anno 2024 --dry-run

# Deliberazioni - Dry-run range anni
python3 scrape_firenze_deliberazioni.py --anno-inizio 2023 --anno-fine 2024 --dry-run --tipi DG DD

# Albo Pretorio - Dry-run
python3 scrape_albo_firenze_v2.py --max-pages 2 --dry-run

# Albo Pretorio alternativo - Dry-run
python3 scrape_albo_pretorio_firenze.py --limit 10 --dry-run
```

### Scraping + MongoDB Import

Scarica atti e importa in MongoDB:

```bash
# Deliberazioni 2024 con MongoDB
python3 scrape_firenze_deliberazioni.py \
    --anno 2024 \
    --download-pdfs \
    --mongodb \
    --tenant-id 1

# Albo Pretorio con MongoDB
python3 scrape_albo_firenze_v2.py \
    --max-pages 5 \
    --download-pdfs \
    --mongodb \
    --tenant-id 1
```

### Solo Scraping (senza MongoDB)

Mantiene compatibilità con versione precedente:

```bash
# Solo scraping file system
python3 scrape_firenze_deliberazioni.py --anno 2024 --download-pdfs
```

## 📊 Report Finale

Ogni scraper genera un report completo:

```
📊 REPORT IMPORT ATTI PA → MONGODB
======================================================================
🔍 MODALITÀ: DRY-RUN (nessun dato salvato)

📈 STATISTICHE:
  ✅ Processati:     150
  ⏭️  Saltati:        5
  ❌ Errori:          2
  📝 Chunks totali:   450
  📄 Documenti:       150

💰 COSTI:
  🤖 Modello:        openai.text-embedding-3-small
  🎫 Token usati:    125,000
  💵 Costo USD:      $0.0025
  💶 Costo EUR:      €0.0023
  📊 Prezzo/M tokens: $0.02

❌ ERRORI DETTAGLIATI:
  1. Atto 123/2024: Nessun contenuto testo valido
  2. Atto 456/2024: Errore salvataggio MongoDB
======================================================================
```

## 🔧 Parametri Disponibili

### `scrape_firenze_deliberazioni.py`

```bash
--anno-inizio N          # Anno inizio (default: 2018)
--anno-fine N            # Anno fine (default: 2025)
--anno N                 # Anno singolo (sovrascrive inizio/fine)
--tipi DG DC DD          # Tipi atto specifici
--download-pdfs          # Scarica PDF
--max-pdf-per-type N     # Max PDF per tipo (per test)
--output-dir PATH        # Directory output
--dry-run                # Dry-run mode
--mongodb                # Import MongoDB
--tenant-id N            # Tenant ID MongoDB (default: 1)
```

### `scrape_albo_firenze_v2.py`

```bash
--max-pages N            # Numero massimo pagine
--download-pdfs          # Scarica PDF
--output-dir PATH        # Directory output
--dry-run                # Dry-run mode
--mongodb                # Import MongoDB
--tenant-id N            # Tenant ID MongoDB (default: 1)
```

### `scrape_albo_pretorio_firenze.py`

```bash
--limit N                # Numero massimo atti (default: 20)
--output PATH            # Directory output
--delay N                # Delay tra richieste (default: 2.0s)
--dry-run                # Dry-run mode
--mongodb                # Import MongoDB
--tenant-id N            # Tenant ID MongoDB (default: 1)
```

## 🗄️ Struttura MongoDB

I documenti vengono salvati in `documents` collection con struttura:

```json
{
  "document_id": "pa_act_firenze_dg_123_2024",
  "tenant_id": 1,
  "title": "Oggetto atto",
  "document_type": "pa_act",
  "protocol_number": "123/2024",
  "protocol_date": "2024-01-15",
  "embedding": [...],  // Document-level embedding
  "content": {
    "raw_text": "...",  // Preview 5000 char
    "full_text": "...",  // Testo completo
    "chunks": [
      {
        "chunk_index": 0,
        "chunk_text": "...",
        "embedding": [...],
        "tokens_used": 512,
        "model_used": "openai.text-embedding-3-small"
      }
    ]
  },
  "metadata": {
    "source": "pa_scraper",
    "ente": "Firenze",
    "tipo_atto": "Deliberazioni di Giunta",
    "anno": "2024",
    "scraper_type": "firenze_deliberazioni",
    "chunk_count": 5,
    "total_chars": 10000
  }
}
```

## ⚙️ Requisiti

1. **MongoDB attivo** (docker compose up -d mongodb in NATAN_LOC)
2. **Python dependencies**:
   ```bash
   pip install requests beautifulsoup4 pymongo PyPDF2 pdfplumber
   ```
3. **NATAN_LOC accessibile** (path relativo configurato automaticamente)

## 🔍 Dry-Run vs Import Reale

### Dry-Run
- ✅ Conta atti trovati
- ✅ Simula chunking (stima chunks)
- ✅ Stima costi embeddings
- ❌ NON scarica file
- ❌ NON genera embeddings reali
- ❌ NON salva in MongoDB

### Import Reale
- ✅ Scarica atti
- ✅ Estrae testo PDF
- ✅ Genera chunks reali
- ✅ Genera embeddings reali
- ✅ Salva in MongoDB
- ✅ Report costi reali

## 💡 Best Practices

1. **Sempre dry-run prima**: Verifica quanti atti ci sono
2. **Test con anno singolo**: Inizia con `--anno 2024` per testare
3. **Limita PDF iniziale**: Usa `--max-pdf-per-type 5` per test
4. **Monitora costi**: I costi sono stimati in dry-run, reali in import

## 🐛 Troubleshooting

### MongoDB non disponibile
```
⚠️  MongoDB importer non disponibile: ...
```
**Soluzione**: Avvia MongoDB: `cd /home/fabio/NATAN_LOC/docker && docker compose up -d mongodb`

### Path non trovato
**Soluzione**: Gli scraper cercano automaticamente NATAN_LOC. Se non trova, verifica la struttura directory.

### PDF non estratti
**Soluzione**: Installa PyPDF2 o pdfplumber: `pip install PyPDF2 pdfplumber`

## 📞 Support

Per problemi o domande, verifica:
1. Log dello scraper (output console)
2. MongoDB connection (`docker ps | grep mongo`)
3. Python dependencies (`pip list | grep -E "requests|beautifulsoup|pymongo|PyPDF"`)






