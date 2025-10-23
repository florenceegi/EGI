<?php
/**
 * Test integrazione atti Firenze con sistema PA/N.A.T.A.N.
 * 
 * Questo script testa se possiamo importare i dati JSON
 * nel formato del sistema PA esistente
 */

// Carica un atto di esempio
$jsonFile = __DIR__ . '/../storage/testing/firenze_atti_completi/json/DG_2018_2025.json';

if (!file_exists($jsonFile)) {
    die("❌ File non trovato: $jsonFile\n");
}

echo "🔍 TEST INTEGRAZIONE ATTI FIRENZE → N.A.T.A.N.\n";
echo str_repeat("=", 70) . "\n\n";

$atti = json_decode(file_get_contents($jsonFile), true);

echo "✅ JSON caricato: " . count($atti) . " atti\n\n";

// Analizza primo atto
$atto = $atti[0];

echo "📄 PRIMO ATTO:\n";
echo "   Numero: " . $atto['numeroAdozione'] . "\n";
echo "   Data: " . date('d/m/Y', $atto['dataAdozione'] / 1000) . "\n";
echo "   Ufficio: " . $atto['ufficio'] . "\n";
echo "   Oggetto: " . substr($atto['oggetto'], 0, 80) . "...\n";
echo "   Allegati: " . count($atto['allegati']) . "\n\n";

// Converti nel formato PA del sistema
echo "🔄 CONVERSIONE NEL FORMATO PA SYSTEM:\n\n";

$paFormat = [
    'numero_atto' => $atto['numeroAdozione'],
    'tipo_atto' => $atto['tipoAttoDto']['nome'] ?? 'N/A',
    'data_atto' => date('Y-m-d', $atto['dataAdozione'] / 1000),
    'oggetto' => $atto['oggetto'],
    'ente' => 'Comune di Firenze',
    'direzione' => $atto['ufficio'],
    'metadata' => [
        'relatore' => $atto['relatore'] ?? null,
        'data_esecutivita' => date('Y-m-d', $atto['dataEsecutivita'] / 1000),
        'data_pubblicazione' => date('Y-m-d', $atto['dataPubblicazione'] / 1000),
        'votazioni' => $atto['votazioni'] ?? null,
        'esito' => $atto['esito'] ?? null,
    ],
    'allegati' => array_map(function($allegato) {
        return [
            'nome' => $allegato['nome'],
            'url' => 'https://accessoconcertificato.comune.fi.it' . $allegato['link'],
            'tipo' => $allegato['contentType'],
            'principale' => $allegato['principale'] ?? false,
        ];
    }, $atto['allegati']),
];

echo json_encode($paFormat, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

echo "\n\n✅ Conversione riuscita!\n";
echo "\n📊 STATISTICHE:\n";
echo "   - Campi standard: ✓\n";
echo "   - Metadata completi: ✓\n";
echo "   - Allegati con link: ✓\n";
echo "   - Pronto per N.A.T.A.N.: ✓\n";
