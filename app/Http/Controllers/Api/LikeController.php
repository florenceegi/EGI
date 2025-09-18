<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use App\Models\Egi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * @Oracode Controller for Like Management
 * 🎯 Purpose: Handles like/unlike operations for Collections and EGIs
 * 🧱 Core Logic: Toggle likes with polymorphic relationships
 * 🛡️ GDPR: Minimal data collection, user-like association only
 *
 * @package App\Http\Controllers\Api
 * @author Padmin D. Curtis for Fabio Cherici
 * @version 1.0.0
 * @date 2025-05-15
 *
 * @signature [LikeController::v1.0] florence-egi-likes
 */
class LikeController extends Controller {
    /**
     * @Oracode Logger for tracking user actions
     *
     * @var UltraLogManager
     */
    private UltraLogManager $logger;

    /** @var ErrorManagerInterface UEM interface for error handling */
    private ErrorManagerInterface $errorManager;

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
    }

    /**
     * @Oracode Toggle like for collection
     * 🎯 Purpose: Toggle like status for a collection
     * 🧱 Core Logic: Check auth (strong or weak), toggle like, return state
     * 🛡️ Security: Accepts both authenticated users and weak auth users
     *
     * @param Collection $collection
     * @param Request $request
     * @return JsonResponse
     */
    public function toggleCollectionLike($collectionId, Request $request): JsonResponse {
        $this->logger->info('Collection like toggle attempt', [
            'collection_id' => $collectionId,
            'auth_check' => auth()->check(),
            'session_wallet' => $request->session()->get('connected_wallet')
        ]);

        $userId = $this->getAuthenticatedUserId($request);

        try {

            if (!$userId) {
                $this->logger->warning('Unauthenticated like attempt on collection', [
                    'collection_id' => $collectionId,
                    'ip' => $request->ip()
                ]);

                throw new \Illuminate\Auth\AuthenticationException(
                    'Unauthenticated in LikeController::toggleCollectionLike'
                );
            }

            // Collection
            $collection = Collection::find($collectionId);
            if (!$collection) {
                $this->logger->error('Collection not found for like toggle', [
                    'collection_id' => $collectionId,
                    'creator_id' => $userId
                ]);

                throw new \Illuminate\Database\Eloquent\ModelNotFoundException(
                    'Collection not found'
                );
            }

            // Toggle the like
            $existingLike = $collection->likes()
                ->where('user_id', $userId)
                ->first();

            if ($existingLike) {
                $existingLike->delete();
                $isLiked = false;
            } else {
                $collection->likes()->create([
                    'user_id' => $userId
                ]);
                $isLiked = true;
            }

            $likesCount = $collection->likes()->count();

            $this->logger->info('Collection like toggled successfully', [
                'collection_id' => $collection->id,
                'user_id' => $userId,
                'is_liked' => $isLiked,
                'total_likes' => $likesCount
            ]);

            return response()->json([
                'success' => true,
                'is_liked' => $isLiked,
                'likes_count' => $likesCount,
                'message' => $isLiked ? 'Collection liked successfully' : 'Collection unliked successfully'
            ]);
        } catch (\Illuminate\Auth\AuthenticationException $e) {
            $this->logger->warning('Authentication required for collection like toggle', [
                'collection_id' => $collectionId,
                'error' => $e->getMessage()
            ]);

            return $this->errorManager->handle('AUTH_REQUIRED', [
                'action' => 'like_toggle',
                'resource_type' => 'collection',
                'resource_id' => $collectionId,
                'auth_status' => $this->getAuthStatus($request)
            ], $e);
        } catch (\Exception $e) {
            $this->logger->error('Failed to toggle collection like', [
                'collection_id' => $collectionId,
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);

            return $this->errorManager->handle('LIKE_TOGGLE_FAILED', [
                'resource_type' => 'collection',
                'resource_id' => $collectionId,
                'error' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * @Oracode Toggle like for EGI
     * 🎯 Purpose: Toggle like status for an EGI
     * 🧱 Core Logic: Check auth (strong or weak), toggle like, return state
     * 🛡️ Security: Accepts both authenticated users and weak auth users
     *
     * @param Egi $egi
     * @param Request $request
     * @return JsonResponse
     */
    public function toggleEgiLike(Egi $egi, Request $request): JsonResponse {

        $this->logger->info('DEBUG: Session check', [
            'session_id' => $request->session()->getId(),
            'has_session' => $request->hasSession(),
            'session_data' => $request->session()->all()
        ]);

        $this->logger->info('EGI like toggle attempt', [
            'egi_id' => $egi->id,
            'auth_check' => auth()->check(),
            'session_wallet' => $request->session()->get('connected_wallet')
        ]);

        $userId = $this->getAuthenticatedUserId($request);

        if (!$userId) {
            $this->logger->warning('Unauthenticated like attempt on EGI', [
                'egi_id' => $egi->id,
                'ip' => $request->ip()
            ]);

            return $this->errorManager->handle('AUTH_REQUIRED', [
                'action' => 'like_toggle',
                'resource_type' => 'egi',
                'resource_id' => $egi->id,
                'auth_status' => $this->getAuthStatus($request)
            ]);
        }

        try {
            // Toggle the like
            $existingLike = $egi->likes()
                ->where('user_id', $userId)
                ->first();

            if ($existingLike) {
                $existingLike->delete();
                $isLiked = false;
            } else {
                $egi->likes()->create([
                    'user_id' => $userId
                ]);
                $isLiked = true;
            }

            $likesCount = $egi->likes()->count();

            $this->logger->info('EGI like toggled successfully', [
                'egi_id' => $egi->id,
                'user_id' => $userId,
                'is_liked' => $isLiked,
                'total_likes' => $likesCount
            ]);

            return response()->json([
                'success' => true,
                'is_liked' => $isLiked,
                'likes_count' => $likesCount,
                'message' => $isLiked ? 'EGI liked successfully' : 'EGI unliked successfully'
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to toggle EGI like', [
                'egi_id' => $egi->id,
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);

            return $this->errorManager->handle('LIKE_TOGGLE_FAILED', [
                'resource_type' => 'egi',
                'resource_id' => $egi->id,
                'error' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * @Oracode Get authenticated user ID
     * 🎯 Purpose: Retrieve user ID from strong or weak auth
     * 🧱 Core Logic: Check auth() first, then fall back to session wallet
     *
     * @param Request $request
     * @return int|null
     */
    private function getAuthenticatedUserId(Request $request): ?int {
        // First check strong auth
        if (auth()->check()) {
            return auth()->id();
        }

        // Then check weak auth via session
        $walletAddress = $request->session()->get('connected_wallet');

        if (!$walletAddress) {
            $this->logger->debug('No authentication found', [
                'has_auth' => false,
                'has_wallet' => false
            ]);
            return null;
        }

        // Look up user by wallet
        $user = User::where('wallet', $walletAddress)->first();

        if ($user) {
            $this->logger->debug('Weak auth user found', [
                'user_id' => $user->id,
                'wallet_prefix' => substr($walletAddress, 0, 6) . '...'
            ]);
            return $user->id;
        }

        $this->logger->warning('Wallet in session but no user found', [
            'wallet_prefix' => substr($walletAddress, 0, 6) . '...'
        ]);

        return null;
    }

    /**
     * @Oracode Get auth status
     * 🎯 Purpose: Determine current authentication status
     * 🧱 Core Logic: Check for strong auth, weak auth, or disconnected
     *
     * @param Request $request
     * @return string
     */
    private function getAuthStatus(Request $request): string {
        if (auth()->check()) {
            return 'logged-in';
        }

        if ($request->session()->get('connected_wallet')) {
            return 'connected';
        }

        return 'disconnected';
    }
}
