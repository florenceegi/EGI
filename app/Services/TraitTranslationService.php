<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * TraitTranslationService
 *
 * Service per gestire traduzioni automatiche dei traits in italiano.
 * Aggiorna il file resources/lang/it/trait_elements.php dinamicamente.
 *
 * @package FlorenceEGI\Services
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0
 * @date 2025-10-21
 */
class TraitTranslationService
{
    /**
     * Path al file delle traduzioni
     */
    private const TRANSLATION_FILE = 'resources/lang/it/trait_elements.php';

    public function __construct(
        private UltraLogManager $logger
    ) {}

    /**
     * Aggiunge una traduzione per una category
     *
     * @param string $key Chiave (es: "Materials")
     * @param string $translation Traduzione IT (opzionale, se NULL usa la chiave)
     * @return bool
     */
    public function addCategoryTranslation(string $key, ?string $translation = null): bool
    {
        $translation = $translation ?? $key;

        $this->logger->info("[TraitTranslation] Adding category translation", [
            'key' => $key,
            'translation' => $translation,
        ]);

        return $this->addTranslation('categories', $key, $translation);
    }

    /**
     * Aggiunge una traduzione per un trait type
     *
     * @param string $key Chiave (es: "Primary Material")
     * @param string $translation Traduzione IT
     * @return bool
     */
    public function addTypeTranslation(string $key, ?string $translation = null): bool
    {
        $translation = $translation ?? $key;

        $this->logger->info("[TraitTranslation] Adding type translation", [
            'key' => $key,
            'translation' => $translation,
        ]);

        return $this->addTranslation('types', $key, $translation);
    }

    /**
     * Aggiunge una traduzione per un trait value
     *
     * @param string $key Chiave (es: "Bronze")
     * @param string $translation Traduzione IT
     * @return bool
     */
    public function addValueTranslation(string $key, ?string $translation = null): bool
    {
        $translation = $translation ?? $key;

        $this->logger->info("[TraitTranslation] Adding value translation", [
            'key' => $key,
            'translation' => $translation,
        ]);

        return $this->addTranslation('values', $key, $translation);
    }

    /**
     * Aggiunge traduzioni per un trait completo (category + type + value)
     *
     * @param array $data ['category' => [...], 'type' => [...], 'value' => [...]]
     * @return bool
     */
    public function addCompleteTraitTranslations(array $data): bool
    {
        $this->logger->info("[TraitTranslation] Adding complete trait translations", [
            'data' => $data,
        ]);

        $success = true;

        if (!empty($data['category'])) {
            $success = $success && $this->addCategoryTranslation(
                $data['category']['key'],
                $data['category']['translation'] ?? null
            );
        }

        if (!empty($data['type'])) {
            $success = $success && $this->addTypeTranslation(
                $data['type']['key'],
                $data['type']['translation'] ?? null
            );
        }

        if (!empty($data['value'])) {
            $success = $success && $this->addValueTranslation(
                $data['value']['key'],
                $data['value']['translation'] ?? null
            );
        }

        return $success;
    }

    /**
     * Aggiunge una traduzione al file
     *
     * @param string $section 'categories', 'types', o 'values'
     * @param string $key
     * @param string $translation
     * @return bool
     */
    private function addTranslation(string $section, string $key, string $translation): bool
    {
        try {
            $filePath = base_path(self::TRANSLATION_FILE);

            if (!File::exists($filePath)) {
                $this->logger->error("[TraitTranslation] Translation file not found", [
                    'path' => $filePath,
                ]);
                return false;
            }

            // Leggi file esistente
            $translations = include $filePath;

            // Check se traduzione già esiste
            if (isset($translations[$section][$key])) {
                $this->logger->info("[TraitTranslation] Translation already exists, skipping", [
                    'section' => $section,
                    'key' => $key,
                ]);
                return true;
            }

            // Aggiungi nuova traduzione
            $translations[$section][$key] = $translation;

            // Ordina alfabeticamente per leggibilità
            ksort($translations[$section]);

            // Genera contenuto PHP
            $content = $this->generatePhpContent($translations);

            // Scrivi file
            File::put($filePath, $content);

            $this->logger->info("[TraitTranslation] Translation added successfully", [
                'section' => $section,
                'key' => $key,
                'translation' => $translation,
            ]);

            // Clear translation cache
            if (function_exists('trans_flush')) {
                trans_flush();
            }

            return true;
        } catch (\Exception $e) {
            $this->logger->error("[TraitTranslation] Failed to add translation", [
                'section' => $section,
                'key' => $key,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Genera contenuto PHP formattato per il file di traduzioni
     *
     * @param array $translations
     * @return string
     */
    private function generatePhpContent(array $translations): string
    {
        $output = "<?php\n\n";
        $output .= "/**\n";
        $output .= " * Trait Elements Translations (Italian)\n";
        $output .= " *\n";
        $output .= " * This file is auto-updated by TraitTranslationService\n";
        $output .= " * Last updated: " . now()->toDateTimeString() . "\n";
        $output .= " */\n\n";
        $output .= "return [\n";

        foreach ($translations as $section => $items) {
            $output .= "    // " . ucfirst($section) . "\n";
            $output .= "    '$section' => [\n";

            foreach ($items as $key => $value) {
                $escapedKey = addslashes($key);
                $escapedValue = addslashes($value);
                $output .= "        '$escapedKey' => '$escapedValue',\n";
            }

            $output .= "    ],\n\n";
        }

        $output .= "];\n";

        return $output;
    }

    /**
     * Verifica se una traduzione esiste
     *
     * @param string $section
     * @param string $key
     * @return bool
     */
    public function translationExists(string $section, string $key): bool
    {
        $filePath = base_path(self::TRANSLATION_FILE);

        if (!File::exists($filePath)) {
            return false;
        }

        $translations = include $filePath;

        return isset($translations[$section][$key]);
    }
}
