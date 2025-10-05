<?php

/**
 * Generate Mock PA Acts PDF for Testing
 * 
 * Crea 5 PDF mock di atti PA con contenuto realistico per testing upload.
 * I PDF generati contengono:
 * - Header ufficiale con logo PA
 * - Numero protocollo e data
 * - Contenuto dell'atto
 * - Footer con firma digitale simulata
 * 
 * REQUIREMENTS:
 * - TCPDF library (già presente in Laravel via composer)
 * 
 * USAGE:
 * php scripts/generate_mock_pa_acts.php
 * 
 * OUTPUT:
 * - storage/testing/mock_pa_acts/*.pdf (5 files)
 * - Log con metadata JSON per ciascun file
 */

require __DIR__ . '/../vendor/autoload.php';

use TCPDF;

// Output directory
$outputDir = __DIR__ . '/../storage/testing/mock_pa_acts';
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
}

// Mock data per 5 atti PA
$mockActs = [
    [
        'doc_type' => 'delibera',
        'protocol_number' => '123/2025',
        'protocol_date' => '2025-10-01',
        'title' => 'Approvazione Bilancio di Previsione 2026',
        'description' => 'Delibera della Giunta Comunale per l\'approvazione del bilancio di previsione annuale 2026 e documento programmatico triennale 2026-2028.',
        'content' => <<<EOT
COMUNE DI FIRENZE
GIUNTA COMUNALE

DELIBERAZIONE N. 123 DEL 01/10/2025

OGGETTO: Approvazione Bilancio di Previsione 2026

LA GIUNTA COMUNALE

VISTA la proposta del Sindaco;
VISTO lo Statuto Comunale;
VISTO il Testo Unico degli Enti Locali (D.Lgs. 267/2000);
VISTA la relazione tecnica allegata;

CONSIDERATO CHE:
- Il bilancio di previsione per l'anno 2026 ammonta a Euro 125.000.000,00
- Sono stati rispettati i vincoli di pareggio di bilancio
- La programmazione è coerente con gli obiettivi strategici del Piano Triennale

DELIBERA

1. Di approvare il Bilancio di Previsione 2026 come da allegato "A"
2. Di autorizzare il Dirigente competente all'attuazione
3. Di trasmettere copia al Collegio dei Revisori
4. Di pubblicare la presente all'Albo Pretorio online per 15 giorni

Firenze, 01 ottobre 2025

Il Sindaco                    Il Segretario Generale
Dr. Mario Rossi              Dr.ssa Laura Bianchi
EOT,
        'filename' => 'delibera_123_2025_bilancio.pdf'
    ],
    [
        'doc_type' => 'determina',
        'protocol_number' => '456/2025',
        'protocol_date' => '2025-10-02',
        'title' => 'Affidamento Servizio Manutenzione Verde Pubblico',
        'description' => 'Determina dirigenziale per l\'affidamento diretto del servizio di manutenzione ordinaria del verde pubblico per il periodo ottobre 2025 - dicembre 2025.',
        'content' => <<<EOT
COMUNE DI FIRENZE
SETTORE AMBIENTE E VERDE PUBBLICO

DETERMINAZIONE DIRIGENZIALE N. 456 DEL 02/10/2025

OGGETTO: Affidamento Servizio Manutenzione Verde Pubblico

IL DIRIGENTE

VISTO il D.Lgs. 50/2016 (Codice Appalti);
VISTA la delibera di Giunta n. 89/2025;
VISTO il capitolato tecnico;

RILEVATO CHE:
- È necessario garantire la manutenzione ordinaria delle aree verdi comunali
- L'importo complessivo è pari a Euro 45.000,00 (IVA esclusa)
- È stato individuato l'operatore economico idoneo

DETERMINA

1. Di affidare alla ditta "Verde Italia S.r.l." il servizio di manutenzione
2. Di impegnare la somma di Euro 54.900,00 (IVA inclusa) sul capitolo 4523/2025
3. Di autorizzare la sottoscrizione del contratto
4. Di dare atto che il CIG è Z1234567890

Firenze, 02 ottobre 2025

Il Dirigente del Settore
Ing. Giuseppe Verdi
EOT,
        'filename' => 'determina_456_2025_verde.pdf'
    ],
    [
        'doc_type' => 'ordinanza',
        'protocol_number' => '789/2025',
        'protocol_date' => '2025-10-03',
        'title' => 'Limitazione Viabilità Centro Storico',
        'description' => 'Ordinanza sindacale contingibile e urgente per limitazione temporanea della viabilità nel centro storico durante manifestazione culturale del 15 ottobre 2025.',
        'content' => <<<EOT
COMUNE DI FIRENZE
IL SINDACO

ORDINANZA N. 789 DEL 03/10/2025

OGGETTO: Limitazione Viabilità Centro Storico - Manifestazione "Notte Bianca"

IL SINDACO

VISTO il Codice della Strada (D.Lgs. 285/1992);
VISTA la richiesta dell'Associazione Culturale "Arte e Città";
SENTITA la Polizia Municipale;

CONSIDERATO CHE:
- È prevista la manifestazione "Notte Bianca" il giorno 15 ottobre 2025
- Si rendono necessarie limitazioni temporanee al traffico veicolare
- È garantita la sicurezza dei partecipanti

ORDINA

1. La chiusura al traffico veicolare delle seguenti vie:
   - Piazza della Signoria
   - Via dei Calzaiuoli
   - Piazza del Duomo
   
2. Orario: dalle ore 18:00 del 15/10/2025 alle ore 02:00 del 16/10/2025

3. Sono esclusi dal divieto: mezzi di soccorso e forze dell'ordine

4. La Polizia Municipale è incaricata dell'esecuzione

5. Pubblicazione all'Albo Pretorio e notifica ai soggetti interessati

Firenze, 03 ottobre 2025

Il Sindaco
Dr. Mario Rossi
EOT,
        'filename' => 'ordinanza_789_2025_viabilita.pdf'
    ],
    [
        'doc_type' => 'decreto',
        'protocol_number' => '1012/2025',
        'protocol_date' => '2025-10-04',
        'title' => 'Nomina Responsabile Transizione Digitale',
        'description' => 'Decreto sindacale di nomina del Responsabile per la Transizione Digitale ai sensi del CAD (D.Lgs. 82/2005) e delle Linee Guida AgID.',
        'content' => <<<EOT
COMUNE DI FIRENZE
IL SINDACO

DECRETO N. 1012 DEL 04/10/2025

OGGETTO: Nomina Responsabile Transizione Digitale

IL SINDACO

VISTO il Codice dell'Amministrazione Digitale (D.Lgs. 82/2005);
VISTE le Linee Guida AgID sulla figura del RTD;
VISTO il curriculum del Dr. Alessandro Neri;

RILEVATO CHE:
- È necessario garantire la transizione digitale dell'ente
- Il Dr. Neri possiede i requisiti professionali richiesti
- L'incarico ha durata triennale

DECRETA

1. La nomina del Dr. Alessandro Neri quale Responsabile per la Transizione Digitale

2. Durata incarico: 01/11/2025 - 31/10/2028

3. Competenze:
   - Coordinamento digitale dell'ente
   - Implementazione SPID, PagoPA, ANPR
   - Cybersecurity e data protection

4. Il compenso è determinato in Euro 15.000,00 annui lordi

5. Trasmissione alla Corte dei Conti e pubblicazione all'Albo

Firenze, 04 ottobre 2025

Il Sindaco
Dr. Mario Rossi
EOT,
        'filename' => 'decreto_1012_2025_rtd.pdf'
    ],
    [
        'doc_type' => 'atto',
        'protocol_number' => '1234/2025',
        'protocol_date' => '2025-10-05',
        'title' => 'Concessione Patrocinio Gratuito Evento Culturale',
        'description' => 'Atto amministrativo di concessione del patrocinio gratuito del Comune per l\'evento "Festival della Musica Classica 2025" organizzato dall\'Orchestra Regionale Toscana.',
        'content' => <<<EOT
COMUNE DI FIRENZE
SETTORE CULTURA E TURISMO

ATTO AMMINISTRATIVO N. 1234 DEL 05/10/2025

OGGETTO: Concessione Patrocinio Gratuito "Festival Musica Classica 2025"

IL RESPONSABILE DEL PROCEDIMENTO

VISTA la richiesta prot. 9876 del 15/09/2025;
VISTO il Regolamento comunale per la concessione di patrocini;
VISTA la relazione artistica allegata;

ACCERTATO CHE:
- L'evento ha rilevanza culturale per la cittadinanza
- Non comporta oneri finanziari per l'ente
- È prevista adeguata visibilità al logo comunale

DISPONE

1. La concessione del patrocinio gratuito del Comune di Firenze

2. Evento: "Festival della Musica Classica 2025"
   Organizzatore: Orchestra Regionale Toscana
   Date: 20-25 novembre 2025
   Luoghi: Teatro Comunale e Sala Vanni

3. Autorizzazione uso logo comunale su materiali promozionali

4. Obblighi organizzatore:
   - Invito gratuito per 10 studenti meritevoli
   - Inserimento loghi su locandine e comunicati
   - Relazione finale sull'evento

5. Pubblicazione all'Albo Pretorio per 15 giorni consecutivi

Firenze, 05 ottobre 2025

Il Responsabile del Procedimento
Dr.ssa Elena Martini
EOT,
        'filename' => 'atto_1234_2025_patrocinio.pdf'
    ]
];

// Genera i PDF
$generatedFiles = [];

foreach ($mockActs as $act) {
    echo "Generazione: {$act['filename']}...\n";

    // Crea PDF con TCPDF
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // Metadata
    $pdf->SetCreator('FlorenceEGI Mock Generator');
    $pdf->SetAuthor('Comune di Firenze');
    $pdf->SetTitle($act['title']);
    $pdf->SetSubject($act['doc_type']);
    $pdf->SetKeywords('PA, atto, ' . $act['doc_type']);

    // Rimuovi header/footer default
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    // Margini
    $pdf->SetMargins(20, 20, 20);
    $pdf->SetAutoPageBreak(true, 25);

    // Font
    $pdf->SetFont('helvetica', '', 11);

    // Aggiungi pagina
    $pdf->AddPage();

    // Header personalizzato
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, 'COMUNE DI FIRENZE', 0, 1, 'C');
    $pdf->Ln(3);

    // Info protocollo
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(0, 6, 'Protocollo: ' . $act['protocol_number'], 0, 1, 'R');
    $pdf->Cell(0, 6, 'Data: ' . date('d/m/Y', strtotime($act['protocol_date'])), 0, 1, 'R');
    $pdf->Ln(5);

    // Tipo documento
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetFillColor(27, 54, 93); // Blu istituzionale
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(0, 8, strtoupper($act['doc_type']), 0, 1, 'C', true);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Ln(8);

    // Contenuto
    $pdf->SetFont('helvetica', '', 11);
    $pdf->MultiCell(0, 5, $act['content'], 0, 'L');

    // Footer con firma digitale simulata
    $pdf->Ln(10);
    $pdf->SetFont('helvetica', 'I', 9);
    $pdf->SetTextColor(100, 100, 100);
    $pdf->MultiCell(0, 4, "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n" .
        "FIRMA DIGITALE QUALIFICATA (MOCK)\n" .
        "Certificato: CN=Mario Rossi, O=Comune di Firenze\n" .
        "Emesso da: InfoCert Firma Qualificata 2\n" .
        "Valido dal: 01/01/2025 al: 31/12/2025\n" .
        "Impronta documento (SHA-256): " . hash('sha256', $act['content']) . "\n" .
        "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━", 0, 'C');

    // Salva PDF
    $filepath = $outputDir . '/' . $act['filename'];
    $pdf->Output($filepath, 'F');

    // Salva metadata JSON
    $metadataFile = $outputDir . '/' . pathinfo($act['filename'], PATHINFO_FILENAME) . '_metadata.json';
    file_put_contents($metadataFile, json_encode([
        'protocol_number' => $act['protocol_number'],
        'protocol_date' => $act['protocol_date'],
        'doc_type' => $act['doc_type'],
        'title' => $act['title'],
        'description' => $act['description'],
        'filename' => $act['filename']
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    $generatedFiles[] = [
        'pdf' => $filepath,
        'metadata' => $metadataFile
    ];

    echo "✅ Generato: {$filepath}\n";
    echo "📋 Metadata: {$metadataFile}\n\n";
}

// Summary
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✅ GENERAZIONE COMPLETATA\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
echo "📁 Directory output: {$outputDir}\n";
echo "📄 File generati: " . count($generatedFiles) . "\n\n";

echo "TESTING INSTRUCTIONS:\n";
echo "1. I file PDF sono pronti per l'upload\n";
echo "2. Usa i file *_metadata.json per compilare i form\n";
echo "3. I PDF contengono firma digitale simulata nel footer\n";
echo "4. Tutti i metadata rispettano le validation rules\n\n";

echo "CURL EXAMPLE:\n";
echo "curl -X POST http://localhost:8004/pa/acts/upload \\\n";
echo "  -H 'Authorization: Bearer YOUR_TOKEN' \\\n";
echo "  -F 'file=@{$outputDir}/delibera_123_2025_bilancio.pdf' \\\n";
echo "  -F 'protocol_number=00123/2025' \\\n";
echo "  -F 'protocol_date=2025-10-01' \\\n";
echo "  -F 'doc_type=delibera' \\\n";
echo "  -F 'title=Approvazione Bilancio di Previsione 2026' \\\n";
echo "  -F 'description=Delibera della Giunta Comunale...'\n";

echo "\n✨ Done!\n";
