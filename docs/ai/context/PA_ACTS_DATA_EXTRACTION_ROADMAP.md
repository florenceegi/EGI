# 📊 PA ACTS DATA EXTRACTION ROADMAP - AI-READY PIPELINE

**PROBLEMA ATTUALE:**

-   Scraper salva SOLO metadati superficiali (titolo, data, tipo)
-   Allegati PDF NON processati (solo link salvati)
-   Dati finanziari (importi, CIG, fornitori) DENTRO PDF non estratti
-   AI (N.A.T.A.N.) non può analizzare numeri reali → ALLUCINA tutto

**IMPATTO PA:**

-   €145M inventati da Claude su query SROI
-   Tabelle con atti inesistenti (DET-2023/456)
-   RISCHIO LEGALE se presentati a Comune

---

## 🎯 SOLUZIONE: PIPELINE COMPLETO 3 FASI

### **FASE 1: ENHANCED SCRAPER (Quick Win - 1 settimana)**

**Obiettivo:** Estrarre dati strutturati GIÀ PRESENTI nelle API

#### **1.1 Mapping Campi Avanzato**

File: `app/Services/PaActs/PaWebScraperService.php`

```php
protected function convertSingleActToPaFormat(array $act, PaWebScraper $scraper): array
{
    // CURRENT: Solo metadati base
    $paAct = [
        'numero_atto' => $act['numeroAdozione'],
        'tipo_atto' => $act['tipoAttoDto']['nome'],
        'oggetto' => $act['oggetto'],
        // ...
    ];

    // ✅ NEW: Estrazione dati finanziari SE presenti in API
    $financialData = $this->extractFinancialData($act);
    $paAct['metadata']['financial'] = $financialData;

    return $paAct;
}

protected function extractFinancialData(array $act): array
{
    return [
        'importo' => $act['importo'] ?? $act['importoContratto'] ?? null,
        'cig' => $act['cig'] ?? null,
        'cup' => $act['cup'] ?? null,
        'aggiudicatario' => $act['aggiudicatario'] ?? null,
        'fornitore' => $act['fornitore'] ?? null,
        'budget' => $act['budget'] ?? null,
        'tipo_spesa' => $act['tipoSpesa'] ?? null,
        'capitolo_bilancio' => $act['capitoloBilancio'] ?? null,
        'fondi_europei' => $act['fondiEuropei'] ?? null,
    ];
}
```

**BENEFIT:**

-   ✅ Se API contiene dati finanziari → LI CATTURIAMO
-   ✅ Zero AI, zero OCR, zero parsing complesso
-   ✅ Implementazione: 2-3 giorni

---

### **FASE 2: PDF METADATA EXTRACTION (Medium - 2 settimane)**

**Obiettivo:** Estrarre metadati DAI PDF senza OCR completo

#### **2.1 Service PDF Text Extractor**

Nuovo file: `app/Services/PaActs/PdfMetadataExtractorService.php`

```php
namespace App\Services\PaActs;

use Smalot\PdfParser\Parser;

class PdfMetadataExtractorService
{
    /**
     * Estrae metadati finanziari da PDF usando pattern matching
     */
    public function extractFinancialMetadata(string $pdfPath): array
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($pdfPath);
        $text = $pdf->getText();

        // Pattern regex per importi
        $patterns = [
            'importo_euro' => '/(?:importo|costo|budget|valore)[:.\s]+€?\s*([0-9.,]+)/i',
            'cig' => '/CIG[:.\s]+([A-Z0-9]{10})/i',
            'cup' => '/CUP[:.\s]+([A-Z0-9]{15})/i',
            'aggiudicatario' => '/aggiudicatario[:.\s]+([^\n]{5,100})/i',
        ];

        $extracted = [];
        foreach ($patterns as $key => $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $extracted[$key] = $this->cleanExtractedValue($matches[1]);
            }
        }

        return $extracted;
    }

    protected function cleanExtractedValue(string $value): string
    {
        // Pulisce importi: "1.250.000,00" -> 1250000.00
        $value = trim($value);
        $value = str_replace(['.', ','], ['', '.'], $value);
        return $value;
    }
}
```

#### **2.2 Queue Job per Processing Allegati**

Nuovo file: `app/Jobs/ProcessPaActAttachmentsJob.php`

```php
class ProcessPaActAttachmentsJob implements ShouldQueue
{
    public function handle(PdfMetadataExtractorService $extractor)
    {
        $act = Egi::find($this->actId);
        $allegati = $act->jsonMetadata['original_data']['allegati'] ?? [];

        $financialData = [];

        foreach ($allegati as $allegato) {
            $nome = strtolower($allegato['nome'] ?? '');

            // Priorità: PDF con pattern finanziari
            if (
                str_contains($nome, 'quadro economico') ||
                str_contains($nome, 'importo') ||
                str_contains($nome, 'budget')
            ) {
                // Download PDF
                $pdfPath = $this->downloadAttachment($allegato['url']);

                // Estrai metadati
                $extracted = $extractor->extractFinancialMetadata($pdfPath);

                if (!empty($extracted)) {
                    $financialData = array_merge($financialData, $extracted);
                }
            }
        }

        // Salva in jsonMetadata
        $metadata = $act->jsonMetadata;
        $metadata['financial_extracted'] = $financialData;
        $act->jsonMetadata = $metadata;
        $act->save();
    }
}
```

**BENEFIT:**

-   ✅ Estrae importi REALI dai PDF "Quadro economico"
-   ✅ Pattern matching (non AI) → veloce e affidabile
-   ✅ Background job → non rallenta scraping
-   ✅ Libreria PHP: `smalot/pdfparser` (già testata)

**IMPLEMENTAZIONE:**

```bash
composer require smalot/pdfparser
php artisan make:service PaActs/PdfMetadataExtractorService
php artisan make:job ProcessPaActAttachmentsJob
```

---

### **FASE 3: AI-POWERED OCR (Advanced - 3-4 settimane)**

**Obiettivo:** Estrazione completa con Claude/GPT-4 Vision

#### **3.1 Service AI Document Parser**

Nuovo file: `app/Services/PaActs/AiDocumentParserService.php`

```php
class AiDocumentParserService
{
    /**
     * Usa Claude 3.5 Sonnet + Vision per estrarre dati strutturati
     */
    public function parseFinancialDocument(string $pdfPath): array
    {
        // Convert PDF to images (ogni pagina)
        $images = $this->convertPdfToImages($pdfPath);

        // Claude 3.5 Sonnet prompt
        $prompt = <<<'PROMPT'
Estrai TUTTI i dati finanziari da questo documento PA.

REGOLE FERREE:
1. Se un campo NON è presente → scrivi null (NON inventare)
2. Importi: formato numerico pulito (es: 1250000.00)
3. Cita pagina e sezione per ogni dato estratto

OUTPUT JSON:
{
    "importo_totale": number|null,
    "importo_contrattuale": number|null,
    "cig": string|null,
    "cup": string|null,
    "aggiudicatario": string|null,
    "fornitore": string|null,
    "durata_mesi": number|null,
    "fondi_europei": boolean,
    "capitolo_bilancio": string|null,
    "citations": [
        {"field": "importo_totale", "page": 3, "section": "Quadro Economico"}
    ]
}
PROMPT;

        $response = $this->anthropicService->analyzeDocument([
            'prompt' => $prompt,
            'images' => $images,
            'model' => 'claude-3-5-sonnet-20241022',
            'max_tokens' => 4096,
        ]);

        $extracted = json_decode($response['content'], true);

        // Validazione: se citation manca → field è null
        return $this->validateExtractedData($extracted);
    }
}
```

**BENEFIT:**

-   ✅ Estrae QUALSIASI dato da PDF complessi
-   ✅ Gestisce tabelle, grafici, layout complessi
-   ✅ Citation obbligatoria (pagina + sezione) → verificabile
-   ✅ Fallback: se incerto → null (NO allucinazioni)

**COSTI:**

-   Claude 3.5 Sonnet Vision: ~$0.15/documento
-   1000 atti con PDF finanziari: ~$150
-   ROI: INVALUTABILE (vs presentare dati falsi a PA)

---

## 📋 STRUTTURA DATI FINALE (jsonMetadata)

```json
{
    "source": "web_scraper",
    "scraper_id": 1,
    "original_data": {
        "numero_atto": "00320",
        "tipo_atto": "Delibera di Giunta",
        "oggetto": "...",
        "allegati": [...]
    },

    // ✅ NEW: Financial data estratti
    "financial_data": {
        "extraction_method": "api|pdf_regex|ai_ocr",
        "extracted_at": "2025-10-28T12:00:00Z",
        "confidence": 0.95,

        "importo_totale": 12500000.00,
        "importo_contrattuale": 11250000.00,
        "cig": "ABC1234567",
        "cup": "J12345678912345",
        "aggiudicatario": "Acme S.p.A.",
        "fornitore": "Beta SRL",
        "durata_mesi": 36,
        "fondi_europei": true,
        "capitolo_bilancio": "CAP_2024_001",

        // Citations per audit PA
        "citations": [
            {
                "field": "importo_totale",
                "source": "Allegato: Quadro economico_signed.pdf",
                "page": 3,
                "section": "Importo Complessivo",
                "raw_text": "Importo totale: € 12.500.000,00"
            }
        ]
    },

    // Metadati altre categorie (urbanistica, sociale, etc.)
    "urban_data": {...},
    "social_data": {...}
}
```

---

## 🚀 ROADMAP IMPLEMENTAZIONE

### **SPRINT 1: Enhanced Scraper (Settimana 1)**

-   [ ] Task 1.1: Aggiungere extractFinancialData() a PaWebScraperService
-   [ ] Task 1.2: Mappare campi finanziari comuni API
-   [ ] Task 1.3: Test su scraper Comune Firenze
-   [ ] Task 1.4: Update DataSanitizerService per includere financial_data

**Deliverable:** Atti con financial_data SE presenti in API

---

### **SPRINT 2: PDF Metadata Extraction (Settimane 2-3)**

-   [ ] Task 2.1: Installare smalot/pdfparser
-   [ ] Task 2.2: Creare PdfMetadataExtractorService
-   [ ] Task 2.3: Definire pattern regex per importi, CIG, CUP
-   [ ] Task 2.4: Creare ProcessPaActAttachmentsJob
-   [ ] Task 2.5: Queue worker setup per background processing
-   [ ] Task 2.6: Dashboard monitoring estrazione PDF

**Deliverable:**

-   59 atti con PDF finanziari processati
-   Importi estratti e salvati in jsonMetadata

---

### **SPRINT 3: AI-Powered OCR (Settimane 4-6)**

-   [ ] Task 3.1: Setup Anthropic API per Vision
-   [ ] Task 3.2: Creare AiDocumentParserService
-   [ ] Task 3.3: Implementare PDF→Image conversion
-   [ ] Task 3.4: Prompt engineering anti-hallucination
-   [ ] Task 3.5: Sistema citation obbligatoria
-   [ ] Task 3.6: Validazione output (null vs value)
-   [ ] Task 3.7: Cost tracking per documento
-   [ ] Task 3.8: Retry logic per failures

**Deliverable:**

-   Estrazione dati completa anche da PDF complessi
-   Citation per ogni campo estratto (verificabile da PA)

---

### **SPRINT 4: N.A.T.A.N. Integration (Settimana 7)**

-   [ ] Task 4.1: Update buildEnterprisePrompt() per usare financial_data
-   [ ] Task 4.2: Anti-hallucination rules basate su data availability
-   [ ] Task 4.3: Few-shot examples con dati REALI vs N/A
-   [ ] Task 4.4: Template response con citation obbligatoria
-   [ ] Task 4.5: DATA VERIFICATION estesa (check citations)

**Deliverable:**

-   N.A.T.A.N. usa SOLO dati estratti (no invenzioni)
-   Se campo null → risposta dice "dato non disponibile"
-   Citation automatica in tabelle (es: "€12,5M (Quadro Economico, pag. 3)")

---

## 🎯 SUCCESS METRICS

### **Prima implementazione (OGGI):**

```
acts_with_amount: 0 / 1598 (0%)
Claude inventa: €145.750.000
Rischio PA: CATASTROFICO
```

### **Dopo SPRINT 1 (Enhanced Scraper):**

```
acts_with_financial_data: ~20 / 1598 (1.2%)
Fonte: API dirette
Claude usa: dati REALI quando disponibili
```

### **Dopo SPRINT 2 (PDF Metadata):**

```
acts_with_financial_data: ~59 / 1598 (3.7%)
Fonte: PDF pattern matching
Importi estratti: €X milioni REALI
```

### **Dopo SPRINT 3 (AI OCR):**

```
acts_with_financial_data: ~150 / 1598 (9.4%)
Fonte: AI Vision + validation
Citation coverage: 100%
Claude allucinazioni: -95%
```

### **Dopo SPRINT 4 (N.A.T.A.N. Integration):**

```
Risposte con dati inventati: <5%
Trasparenza citazioni: 100%
PA confidence: MASSIMA
Risk score: MINIMO
```

---

## 💰 COST ANALYSIS

### **SPRINT 1 (Enhanced Scraper):**

-   Effort: 16 ore dev
-   Costo: €0 infra
-   ROI: IMMEDIATO (cattura dati API)

### **SPRINT 2 (PDF Metadata):**

-   Effort: 40 ore dev
-   Costo: €0 infra (libreria open-source)
-   ROI: 59 atti con dati finanziari

### **SPRINT 3 (AI OCR):**

-   Effort: 80 ore dev
-   Costo infra: ~$150 per 1000 PDF
-   ROI: INVALUTABILE (vs scandalo PA)

### **TOTALE PROGETTO:**

-   Effort: ~136 ore (17 giorni dev)
-   Costo: ~$150 AI + €0 infra
-   **Benefit PA:** Dati verificabili, audit-ready, zero allucinazioni

---

## ⚠️ DEPENDENCIES

### **Librerie PHP:**

```bash
composer require smalot/pdfparser     # PDF text extraction
composer require intervention/image   # PDF→Image conversion
composer require spatie/pdf-to-image  # Alternative PDF converter
```

### **System Requirements:**

```bash
sudo apt-get install poppler-utils    # pdftotext, pdfimages
sudo apt-get install ghostscript      # PDF rendering
```

### **API Keys:**

```env
ANTHROPIC_API_KEY=sk-ant-...          # Claude Vision
```

---

## 🔒 GDPR COMPLIANCE

### **Data Processing:**

-   ✅ Base giuridica: Art. 23 D.Lgs 33/2013 (atti pubblici)
-   ✅ Dati processati: SOLO metadati pubblici
-   ✅ PDF inviati a Anthropic: SOLO documenti pubblici PA
-   ✅ Retention: PDF temp cancellati dopo estrazione
-   ✅ Audit trail: Log completo di ogni estrazione

### **Privacy by Design:**

-   PII detection prima invio a Claude
-   Sanitizzazione nominativi in PDF
-   Watermark "USO INTERNO PA" su output

---

## 📊 MONITORING DASHBOARD

### **Metrics da tracciare:**

```
1. Acts scraped (total)
2. Acts with financial_data (%)
3. Extraction method breakdown (api/pdf/ai)
4. AI extraction cost (€/document)
5. Extraction confidence score
6. Citation coverage (%)
7. N.A.T.A.N. hallucination rate (%)
8. PA query satisfaction score
```

### **Alert Rules:**

```
- Hallucination rate >5% → ALERT CRITICO
- Extraction failures >10% → REVIEW NEEDED
- Citation coverage <90% → QUALITY CHECK
- AI cost >$0.30/doc → OPTIMIZE PROMPT
```

---

## 🎯 IMMEDIATE NEXT STEPS (P0)

1. **OGGI:** Implementare SPRINT 1 (Enhanced Scraper)

    - Aggiungi extractFinancialData()
    - Test su 10 atti campione
    - Commit + deploy staging

2. **DOMANI:** Setup SPRINT 2 (PDF Extraction)

    - Install smalot/pdfparser
    - Crea skeleton PdfMetadataExtractorService
    - Test su "Quadro economico" PDF reale

3. **SETTIMANA PROSSIMA:** Complete SPRINT 2
    - Queue job processing
    - Dashboard monitoring
    - Production ready

**PRIORITÀ ASSOLUTA:** Far vedere al Comune dati VERI entro 10 giorni.
