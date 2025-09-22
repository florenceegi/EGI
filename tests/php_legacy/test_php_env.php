<?php

// test_php_env.php

echo "Versione PHP: " . PHP_VERSION . "\n";
echo "Estensione JSON caricata? ";
var_dump(extension_loaded('json'));

echo "Costante JSON_SORT_KEYS definita? ";
var_dump(defined('JSON_SORT_KEYS'));

if (defined('JSON_SORT_KEYS')) {
    echo "Valore della costante: " . JSON_SORT_KEYS . "\n";
    echo "Test di json_encode con la costante: " . json_encode(['c'=>1, 'b'=>2], JSON_SORT_KEYS) . "\n";
    echo "\nTEST SUPERATO: L'ambiente PHP base funziona correttamente.\n";
} else {
    echo "\nTEST FALLITO: La costante non è definita a livello base di PHP.\n";
}
