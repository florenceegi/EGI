<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Egi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * API Controller per ottenere immagini random per l'animazione splash
 */
class RandomImagesController extends Controller {
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
     * Ottiene immagini random da EGI pubblicati
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getRandomEgiImages(Request $request): JsonResponse {
        $limit = $request->query('limit', 30);

        try {
            // Ottieni EGI pubblicati (tutti hanno immagini via accessor)
            $egis = Egi::where('is_published', true)
                ->inRandomOrder()
                ->limit($limit)
                ->get();

            // Estrai gli URL delle immagini usando l'accessor main_image_url
            $images = $egis->map(function ($egi) {
                return $egi->main_image_url;
            })->filter()->values()->toArray();

            // Se non ci sono abbastanza immagini, ripeti quelle esistenti
            if (count($images) > 0 && count($images) < $limit) {
                $originalCount = count($images);
                while (count($images) < $limit) {
                    $images[] = $images[count($images) % $originalCount];
                }
            }

            return response()->json([
                'success' => true,
                'images' => array_values($images),
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Error fetching random EGI images: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'images' => [],
                'error' => $e->getMessage(),
            ], 200);
        }
    }
}
