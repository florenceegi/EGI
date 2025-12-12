<?php

namespace App\Http\Controllers;

use App\Services\NatanTutorService;
use App\Services\EgiliService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * 📜 Oracode Controller: NatanTutorController
 *
 * API endpoints for Natan Tutor operational assistant.
 *
 * @package     App\Http\Controllers
 * @author      Padmin D. Curtis (for Fabio Cherici)
 * @copyright   2025 Fabio Cherici
 * @license     MIT
 * @version     1.0.0
 *
 * @purpose     🎯 Exposes Natan Tutor actions via REST API
 * @context     🧩 Used by frontend TypeScript NatanTutor module
 *
 * @feature     🗝️ Navigation assistance endpoints
 * @feature     🗝️ Feature explanation endpoints
 * @feature     🗝️ Mint/Reserve/Purchase assistance endpoints
 * @feature     🗝️ User state and recommendations
 */
class NatanTutorController extends Controller {
    /**
     * @var NatanTutorService
     */
    protected NatanTutorService $tutorService;

    /**
     * @var EgiliService
     */
    protected EgiliService $egiliService;
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;

    /**
     * Constructor
     */
    public function __construct(
        NatanTutorService $tutorService,
        EgiliService $egiliService,
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        $this->tutorService = $tutorService;
        $this->egiliService = $egiliService;
        $this->logger = $logger;
        $this->errorManager = $errorManager;
    }

    /**
     * Get user state for Natan Tutor
     *
     * Returns balance, available actions, and recommendations.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getUserState(Request $request): JsonResponse {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'unauthenticated',
                'message' => __('auth.unauthenticated'),
            ], 401);
        }

        $state = $this->tutorService->getUserState($user);

        return response()->json([
            'success' => true,
            'data' => $state,
        ]);
    }

    /**
     * Get action cost
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getActionCost(Request $request): JsonResponse {
        $request->validate([
            'action' => 'required|string',
            'mode' => 'nullable|string|in:tutoring,expert',
        ]);

        $action = $request->input('action');
        $mode = $request->input('mode', 'tutoring');

        $cost = $this->tutorService->getActionCost($action, $mode);

        return response()->json([
            'success' => true,
            'action' => $action,
            'mode' => $mode,
            'cost' => $cost,
        ]);
    }

    /**
     * Navigate to destination
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function navigate(Request $request): JsonResponse {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'unauthenticated',
            ], 401);
        }

        $request->validate([
            'destination' => 'required|string',
            'mode' => 'nullable|string|in:tutoring,expert',
        ]);

        $destination = $request->input('destination');
        $mode = $request->input('mode', 'tutoring');

        $result = $this->tutorService->navigateTo($user, $destination, $mode);

        return response()->json($result);
    }

    /**
     * Explain a feature
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function explain(Request $request): JsonResponse {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'unauthenticated',
            ], 401);
        }

        $request->validate([
            'feature' => 'required|string',
            'mode' => 'nullable|string|in:tutoring,expert',
        ]);

        $feature = $request->input('feature');
        $mode = $request->input('mode', 'tutoring');

        $result = $this->tutorService->explainFeature($user, $feature, $mode);

        return response()->json($result);
    }

    /**
     * Assist with mint process
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function assistMint(Request $request): JsonResponse {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'unauthenticated',
            ], 401);
        }

        $request->validate([
            'mint_data' => 'nullable|array',
            'mint_data.title' => 'nullable|string|max:255',
            'mint_data.description' => 'nullable|string|max:2000',
            'mint_data.media' => 'nullable|string',
            'mode' => 'nullable|string|in:tutoring,expert',
        ]);

        $mintData = $request->input('mint_data', []);
        $mode = $request->input('mode', 'tutoring');

        $result = $this->tutorService->assistMint($user, $mintData, $mode);

        return response()->json($result);
    }

    /**
     * Assist with reservation
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function assistReservation(Request $request): JsonResponse {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'unauthenticated',
            ], 401);
        }

        $request->validate([
            'egi_id' => 'required|integer|exists:egis,id',
            'mode' => 'nullable|string|in:tutoring,expert',
        ]);

        $egiId = $request->input('egi_id');
        $mode = $request->input('mode', 'tutoring');

        $result = $this->tutorService->assistReservation($user, $egiId, $mode);

        return response()->json($result);
    }

    /**
     * Assist with Egili purchase
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function assistPurchase(Request $request): JsonResponse {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'unauthenticated',
            ], 401);
        }

        $request->validate([
            'amount' => 'required|integer|min:100',
            'mode' => 'nullable|string|in:tutoring,expert',
        ]);

        $amount = $request->input('amount');
        $mode = $request->input('mode', 'tutoring');

        $result = $this->tutorService->assistEgiliPurchase($user, $amount, $mode);

        return response()->json($result);
    }

    /**
     * Assist with collection creation
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function assistCollectionCreate(Request $request): JsonResponse {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'unauthenticated',
            ], 401);
        }

        $request->validate([
            'collection_data' => 'nullable|array',
            'collection_data.name' => 'nullable|string|max:255',
            'collection_data.description' => 'nullable|string|max:2000',
            'mode' => 'nullable|string|in:tutoring,expert',
        ]);

        $collectionData = $request->input('collection_data', []);
        $mode = $request->input('mode', 'tutoring');

        $result = $this->tutorService->assistCollectionCreate($user, $collectionData, $mode);

        return response()->json($result);
    }

    /**
     * Check if user can afford an action
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function canAfford(Request $request): JsonResponse {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'unauthenticated',
            ], 401);
        }

        $request->validate([
            'action' => 'required|string',
            'mode' => 'nullable|string|in:tutoring,expert',
        ]);

        $action = $request->input('action');
        $mode = $request->input('mode', 'tutoring');

        $canAfford = $this->tutorService->canAffordAction($user, $action, $mode);
        $cost = $this->tutorService->getActionCost($action, $mode);
        $balance = $this->egiliService->getBalance($user);

        return response()->json([
            'success' => true,
            'can_afford' => $canAfford,
            'action' => $action,
            'mode' => $mode,
            'cost' => $cost,
            'current_balance' => $balance,
        ]);
    }
}
