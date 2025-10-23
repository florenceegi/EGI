# 📥 Albo Pretorio Scraper - Quick Start

## 🚀 USO RAPIDO

```bash
# 1. Installa dipendenze
pip3 install requests beautifulsoup4

# 2. Scarica 20 atti da Firenze
python3 scrape_albo_pretorio_firenze.py

# 3. Risultati in:
ls storage/testing/firenze_atti/pdf/
```

## ⚠️ PRIMA DI USARE

**Lo script ha una struttura HTML generica!**

1. Visita: https://albopretorionline.comune.fi.it
2. Ispeziona HTML (F12)
3. Adatta selettori nel codice (vedi guida completa)

## 📚 DOCUMENTAZIONE COMPLETA

**Leggi:** `docs/testing/GUIDA_SCRAPING_ALBO_FIRENZE.md`

Include:

-   Setup dettagliato
-   Come adattare lo script
-   Troubleshooting
-   Integrazione con N.A.T.A.N.

## 🎯 PERCHÉ È UTILE

Testing N.A.T.A.N. con **atti reali** invece di mock:

-   ✅ Validazione AI su documenti autentici
-   ✅ Demo presentazione più convincente
-   ✅ Proof of concept tangibile

## 📞 HELP

```bash
python3 scrape_albo_pretorio_firenze.py --help
```

---

**TIP:** Inizia con `--limit 1` per testare prima di scaricare tutto!
