<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Ultra\UltraLogManager\UltraLogManager;

class EGIImageService
{
    /**
     * Elimina tutti i file che iniziano con $prefix in $pathKey, su tutti i servizi attivi.
     */
    public static function removeOldImage(string $prefix, int $collectionId, string $pathKey): bool
    {
        $activeHostings = static::getActiveHostings();
        $folderPath = static::resolveFolderPath($pathKey, $collectionId);

        foreach ($activeHostings as $hostingName => $hostingConfig) {
            try {
                $disk = Storage::disk($hostingConfig['disk']);
                $files = $disk->files($folderPath);

                foreach ($files as $file) {
                    // Str::startsWith serve a controllare se il filename inizia col prefisso
                    if (Str::startsWith(basename($file), $prefix)) {
                        $disk->delete($file);
                    }
                }
            } catch (\Exception $e) {
                app(UltraLogManager::class)->error('Errore durante la rimozione del vecchio file', [
                    'error'        => $e->getMessage(),
                    'prefix'       => $prefix,
                    'collectionId' => $collectionId,
                    'pathKey'      => $pathKey,
                ], 'florenceegi');
                return false;
            }
        }

        return true;
    }

    /**
     * Salva il file $filename sul disco di tutti i servizi attivi.
     * Ritorna true se almeno un caricamento va a buon fine.
     */
    public static function saveEGIImage(
        int $collectionId,
        string $filename,
        $file,
        string $pathKey
    ): bool {
        $activeHostings = static::getActiveHostings();
        $folderPath = static::resolveFolderPath($pathKey, $collectionId);

        $atLeastOneSuccess = false;

        foreach ($activeHostings as $hostingName => $hostingConfig) {
            try {
                $disk = Storage::disk($hostingConfig['disk']);
                // Invece di costruire manualmente il filePath e fare file_get_contents,
                // usiamo putFileAs(), che si occupa di caricare il file correttamente.
                $disk->putFileAs($folderPath, $file, $filename);

                $atLeastOneSuccess = true;
            } catch (\Exception $e) {
                app(UltraLogManager::class)->error('Errore salvataggio immagine EGI', [
                    'error'        => $e->getMessage(),
                    'filename'     => $filename,
                    'collectionId' => $collectionId,
                    'hosting'      => $hostingName,
                ], 'florenceegi');
            }
        }

        return $atLeastOneSuccess;
    }


    /**
     * Ritorna l'URL (o percorso) dal caching; se non presente in cache, lo costruisce e lo salva.
     */
    public static function getCachedEGIImagePath(
        int $collectionId,
        string $filename,
        bool $isPublished,
        ?string $hostingService = null,
        string $pathKey = 'head.banner'
    ): ?string {
        if (!$filename) {
            return null;
        }

        // Creiamo una chiave univoca per la cache
        $cacheKey = "EGIImagePath_{$collectionId}_{$filename}_{$hostingService}";

        return Cache::remember($cacheKey, now()->addDay(), function () use (
            $collectionId, $filename, $pathKey, $hostingService, $isPublished
        ) {
            // Se non c'è hostingService, usa quello predefinito
            $hostingToUse = $hostingService ?: static::getDefaultHosting();

            // Ottieni i dettagli dell'hosting
            $hostingConfig = config("paths.hosting.$hostingToUse");
            if (!$hostingConfig) {
                return null;
            }

            $folderPath = static::resolveFolderPath($pathKey, $collectionId);

            $baseUrl = rtrim($hostingConfig['url'], '/');
            $fullUrl = "{$baseUrl}/{$folderPath}{$filename}";

            // Se l'EGI non è pubblicato, potresti gestire un placeholder o simile
            if (!$isPublished) {
                // Esempio, potresti loggare o ritornare un'immagine placeholder
            }

            return $fullUrl;
        });
    }

    /**
     * Invalida la cache per un certo file.
     */
    public static function invalidateEGIImageCache(
        int $collectionId,
        string $filename,
        ?string $hostingService = null
    ): void {
        $cacheKey = "EGIImagePath_{$collectionId}_{$filename}_{$hostingService}";
        Cache::forget($cacheKey);
    }


    /**
     * Recupera i soli hosting con 'is_active' => true
     */
    protected static function getActiveHostings(): array
    {
        $allHostings = config('paths.hosting', []);
        return array_filter($allHostings, fn($hosting) => $hosting['is_active'] === true);
    }

    /**
     * Restituisce il nome dell'hosting di default (es. "Local") o "Digital Ocean".
     */
    protected static function getDefaultHosting(): string
    {
        $default = config('paths.default_hosting', 'Local');
        return $default;
    }

    /**
     * Risolve la path prendendola da config('paths.paths') e sostituendo {collectionId}.
     */
    protected static function resolveFolderPath(string $pathKey, int $collectionId): string
    {
        $keys = explode('.', $pathKey);
        $pathsConfig = config('paths.paths', []);

        $current = $pathsConfig;
        foreach ($keys as $key) {
            if (!isset($current[$key])) {
                return '';
            }
            $current = $current[$key];
        }

        // Ora $current è una stringa che contiene "{collectionId}"
        return str_replace('{collectionId}', $collectionId, $current);
    }
}
