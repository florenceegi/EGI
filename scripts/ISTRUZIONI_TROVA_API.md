# 🔍 COME TROVARE L'API DELL'ALBO PRETORIO

## STEP 1: Apri la pagina nel browser
```
https://accessoconcertificato.comune.fi.it/AOL/Affissione/ComuneFi/Page
```

## STEP 2: Apri Developer Tools
- Premi **F12**
- Vai nella tab **Network** (Rete)

## STEP 3: Filtra le richieste
- Nel campo filtro scrivi: **XHR** o **Fetch**
- Ricarica la pagina (F5)

## STEP 4: Cerca chiamate API
Cerca richieste che sembrano contenere dati degli atti.
Tipicamente:
- URL contiene: `api`, `search`, `affissione`, `lista`, `results`
- Tipo: XHR o Fetch
- Response: JSON con array di atti

## STEP 5: Ispeziona la risposta
- Clicca sulla richiesta
- Tab **Response** o **Preview**
- Copia l'URL completo
- Copia i parametri della Query String

## 🎯 COSA CERCARE

### Esempio 1: API REST
```
GET /api/affissioni/search?from=2025-01-01&to=2025-10-23&limit=50
```

### Esempio 2: API POST
```
POST /api/search
Body: {"startDate": "2025-01-01", "endDate": "2025-10-23", "pageSize": 50}
```

### Esempio 3: Parametri URL
```
/results?anno=2025&mese=10&tipo=TUTTE
```

## 📋 INFORMAZIONI DA RACCOGLIERE

1. **URL completo** della chiamata API
2. **Metodo** HTTP (GET, POST, etc.)
3. **Headers** necessari (se ci sono)
4. **Parametri** (Query String o Body)
5. **Struttura JSON** della risposta (primi 2-3 atti)

---

## ⚡ QUICK TEST

Quando trovi l'URL, prova a copiarlo e testarlo con curl:

```bash
curl "URL_CHE_HAI_TROVATO" -H "User-Agent: Mozilla/5.0"
```

Se funziona, mandami:
- L'URL completo
- L'output JSON che restituisce
