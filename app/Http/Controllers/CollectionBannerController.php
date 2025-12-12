<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

class CollectionBannerController extends Controller {
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;

    public function __construct(UltraLogManager $logger, ErrorManagerInterface $errorManager) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
    }

    /**
     * Upload/replace banner image for a Collection using Spatie Media.
     */
    public function store(Request $request, Collection $collection) {
        // Auth check: only the creator can update the banner
        if (!Auth::check() || Auth::id() !== (int) $collection->creator_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validator = Validator::make($request->all(), [
            'banner' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,avif', 'max:8192'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $file = $request->file('banner');

        try {
            // Save as single file in media collection 'head'
            $media = $collection
                ->addMedia($file)
                ->usingFileName('banner_' . time() . '.' . $file->getClientOriginalExtension())
                ->toMediaCollection('head');

            return response()->json([
                'success' => true,
                'original_url' => $media->getUrl(),
                'banner_url' => $media->getUrl('banner'),
                'card_url' => $media->getUrl('card'),
                'thumb_url' => $media->getUrl('thumb'),
            ]);
        } catch (\Throwable $e) {
            return $this->errorManager->handle('COLLECTION_BANNER_UPLOAD_ERROR', [
                'collection_id' => $collection->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ], $e);
        }
    }
}
