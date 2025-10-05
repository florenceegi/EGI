<?php

/**
 * Download Real Signed PA Acts from Public Sources
 * 
 * @package Scripts
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - PA Acts Testing)
 * @date 2025-10-05
 * @purpose Download real digitally signed PDF files from Italian PA public sources for testing
 */

require __DIR__ . '/../vendor/autoload.php';

// Output directory
$outputDir = __DIR__ . '/../storage/testing/real_signed_pa_acts';

// Create directory if not exists
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
    echo "✅ Created directory: {$outputDir}\n\n";
}

/**
 * Public sources of digitally signed PA acts
 * These are real administrative acts published by Italian PA entities
 */
$sources = [
    // Comune di Firenze - Albo Pretorio
    [
        'name' => 'Comune Firenze - Delibera Giunta',
        'url' => 'https://amministrazionetrasparente.comune.firenze.it',
        'type' => 'delibera',
        'description' => 'Deliberazioni di Giunta - Comune di Firenze',
    ],

    // ANAC - Autorità Nazionale Anticorruzione
    [
        'name' => 'ANAC - Delibera',
        'url' => 'https://www.anticorruzione.it',
        'type' => 'delibera',
        'description' => 'Delibere ANAC con firma digitale QES',
    ],

    // Regione Toscana - Bandi
    [
        'name' => 'Regione Toscana - Decreto',
        'url' => 'https://www.regione.toscana.it',
        'type' => 'decreto',
        'description' => 'Decreti regionali firmati digitalmente',
    ],
];

echo "🔍 RICERCA ATTI PA FIRMATI DIGITALMENTE\n";
echo "========================================\n\n";

echo "📋 Fonti pubbliche identificate:\n";
foreach ($sources as $source) {
    echo "  • {$source['name']}\n";
    echo "    Tipo: {$source['type']}\n";
    echo "    URL: {$source['url']}\n\n";
}

echo "\n⚠️  NOTA IMPORTANTE:\n";
echo "====================================\n";
echo "Gli atti PA reali sono pubblicati temporaneamente sugli Albi Pretori.\n";
echo "Per scaricarli automaticamente, sarebbe necessario:\n\n";
echo "1. Implementare un parser specifico per ogni piattaforma di Albo Pretorio\n";
echo "2. Gestire autenticazione/captcha quando presenti\n";
echo "3. Rispettare robots.txt e rate limiting\n";
echo "4. Verificare validità firma digitale dopo download\n\n";

echo "📥 ALTERNATIVA RACCOMANDATA:\n";
echo "====================================\n";
echo "Scarica manualmente alcuni PDF firmati da queste fonti:\n\n";

echo "🏛️ COMUNE DI FIRENZE:\n";
echo "1. Vai a: https://accessoconcertificato.comune.fi.it/AOL/Affissione/ComuneFi/Page\n";
echo "2. Seleziona un atto recente (Delibera/Determinazione)\n";
echo "3. Scarica il PDF allegato\n";
echo "4. Salva in: {$outputDir}/\n\n";

echo "🏛️ REGIONE TOSCANA:\n";
echo "1. Vai a: https://www.regione.toscana.it/burc (Bollettino Ufficiale)\n";
echo "2. Seleziona un decreto recente\n";
echo "3. Scarica il PDF (solitamente firmato PAdES)\n";
echo "4. Salva in: {$outputDir}/\n\n";

echo "🏛️ ANAC (Autorità Anticorruzione):\n";
echo "1. Vai a: https://www.anticorruzione.it/documents\n";
echo "2. Cerca delibere recenti\n";
echo "3. Scarica PDF con firma digitale\n";
echo "4. Salva in: {$outputDir}/\n\n";

echo "🏛️ GAZZETTA UFFICIALE:\n";
echo "1. Vai a: https://www.gazzettaufficiale.it/\n";
echo "2. Cerca atti firmati digitalmente\n";
echo "3. Scarica PDF\n";
echo "4. Salva in: {$outputDir}/\n\n";

echo "✅ VERIFICA FIRMA DIGITALE:\n";
echo "====================================\n";
echo "Dopo il download, verifica che il PDF sia realmente firmato:\n\n";
echo "• Apri con Adobe Reader\n";
echo "• Cerca icona firma digitale in alto\n";
echo "• Clicca su firma per verificare validità\n";
echo "• Cerca certificato QES/PAdES\n\n";

echo "📝 NAMING CONVENTION:\n";
echo "====================================\n";
echo "Rinomina i file scaricati seguendo questo pattern:\n";
echo "  {tipo}_{protocollo}_{anno}_{descrizione}.pdf\n\n";
echo "Esempi:\n";
echo "  delibera_123_2025_bilancio.pdf\n";
echo "  decreto_456_2025_ordinanza_viabilita.pdf\n";
echo "  determina_789_2025_affidamento_servizio.pdf\n\n";

echo "🧪 TEST DOPO DOWNLOAD:\n";
echo "====================================\n";
echo "Una volta scaricati alcuni PDF reali firmati:\n\n";
echo "1. Verifica che siano in: {$outputDir}/\n";
echo "2. Testa upload nella tua applicazione\n";
echo "3. Verifica che la validazione firma funzioni\n";
echo "4. Controlla che i metadati vengano estratti\n\n";

echo "✅ OUTPUT DIRECTORY:\n";
echo "====================================\n";
echo "📁 {$outputDir}\n\n";

// Create a README in the directory
$readmePath = $outputDir . '/README.txt';
$readmeContent = <<<'README'
REAL SIGNED PA ACTS - TESTING DIRECTORY
========================================

Questa directory contiene PDF di atti PA realmente firmati digitalmente,
scaricati da fonti pubbliche ufficiali per testare il sistema di validazione.

FONTI PUBBLICHE CONSIGLIATE:
--------------------------------------------
1. Comune di Firenze - Albo Pretorio
   https://accessoconcertificato.comune.fi.it/AOL/Affissione/ComuneFi/Page

2. Regione Toscana - BURC
   https://www.regione.toscana.it/burc

3. ANAC - Delibere
   https://www.anticorruzione.it/documents

4. Gazzetta Ufficiale
   https://www.gazzettaufficiale.it/

REQUISITI PDF:
--------------------------------------------
✅ Firma digitale qualificata (QES/PAdES)
✅ Certificato valido
✅ Formato PDF/A quando possibile
✅ Metadati leggibili

NAMING CONVENTION:
--------------------------------------------
{tipo}_{protocollo}_{anno}_{descrizione}.pdf

Esempi:
- delibera_123_2025_bilancio.pdf
- decreto_456_2025_ordinanza.pdf
- determina_789_2025_affidamento.pdf

VERIFICA FIRMA:
--------------------------------------------
1. Apri PDF con Adobe Reader
2. Verifica presenza firma digitale
3. Controlla validità certificato
4. Verifica tipo QES/PAdES

UTILIZZO PER TEST:
--------------------------------------------
Questi PDF possono essere usati per testare:
- Upload sistema PA Acts
- Validazione firma digitale
- Estrazione metadati
- Creazione Collections (fascicoli)
- Blockchain anchoring

NOTE:
--------------------------------------------
• Gli atti scaricati sono documenti pubblici
• Rispettare copyright e licenze
• Non modificare i PDF originali
• Mantenere traccia della fonte

README;

file_put_contents($readmePath, $readmeContent);
echo "✅ Creato README: {$readmePath}\n\n";

echo "🎯 PROSSIMI PASSI:\n";
echo "====================================\n";
echo "1. Scarica manualmente 3-5 PDF firmati dalle fonti indicate\n";
echo "2. Salva in: {$outputDir}/\n";
echo "3. Verifica firme digitali con Adobe Reader\n";
echo "4. Testa upload nella tua applicazione\n";
echo "5. Verifica che il sistema di validazione funzioni correttamente\n\n";

echo "✨ Script completato!\n";
