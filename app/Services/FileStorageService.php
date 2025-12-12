<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Livewire\WithFileUploads;
use Exception;
use Illuminate\Support\Facades\Storage;
use App\Models\Collection;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

class FileStorageService
{
    use WithFileUploads; // Necessario per sfruttare le funzionalità di Livewire
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
    }

    /**
     * Salva un file Livewire in una posizione specifica e aggiorna il percorso nel database.
     *
     * @param \Livewire\TemporaryUploadedFile $file
     * @param mixed $file
     * @param string $filename
     * @param string $disk
     * @param int $collectionId
     * @param string $imageType
     * @return string
     * @throws Exception
     */
    public function saveFile($file, string $path, ?string $filename = null, string $disk = 'public', int $collectionId, string $imageType): string
    {
        try {
            // Usa storeAs per salvare il file
            if ($filename) {
                $savedPath = $file->storeAs($path, $filename, $disk);
            } else {
                $savedPath = $file->store($path, $disk);
            }

            $this->logger->info('File salvato:', ['path' => $savedPath], 'florenceegi');

            // Verifica se il file esiste usando il disco passato
            if (!Storage::disk($disk)->exists($savedPath)) {
                $this->logger->error('File non trovato dopo storeAs.', ['path' => $savedPath], 'florenceegi');
                throw new Exception('Errore durante il salvataggio del file.');
            }

            // Aggiorna il percorso nel database
            $this->updateCollectionImagePath($collectionId, $savedPath, $imageType);

            return $savedPath; // Restituisce il percorso relativo
        } catch (Exception $e) {
            $this->logger->error('Errore nel salvataggio del file:', ['message' => $e->getMessage()], 'florenceegi');
            throw $e;
        }
    }

    /**
     * Aggiorna il percorso dell'immagine nella tabella collections.
     *
     * @param int $collectionId
     * @param string $savedPath
     * @param string $imageType
     * @throws Exception
     */
    protected function updateCollectionImagePath(int $collectionId, string $savedPath, string $imageType): void
    {
        $collection = Collection::findOrFail($collectionId);

        switch ($imageType) {
            case 'banner':
                $collection->path_image_banner = $savedPath;
                break;
            case 'card':
                $collection->path_image_card = $savedPath;
                break;
            case 'avatar':
                $collection->path_image_avatar = $savedPath;
                break;
            case 'EGI':
                $collection->path_image_EGI = $savedPath;
                break;
            default:
                throw new Exception("Tipo di immagine non supportato: $imageType");
        }

        $collection->save();

        $this->logger->info('Percorso immagine aggiornato nel database.', [
            'collection_id' => $collectionId,
            'image_type' => $imageType,
            'path' => $savedPath,
        ], 'florenceegi');
    }
}

