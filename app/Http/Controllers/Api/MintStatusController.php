<?php

/**
 * @package App\Http\Controllers\Api
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Enterprise Mint Status API)
 * @date 2025-10-13
 * @purpose Real-time mint status checking API endpoint
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Egi;
use App\Models\EgiBlockchain;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

class MintStatusController extends Controller {
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->middleware('auth');
    }

    /**
     * Get mint status for specific EGI
     *
     * @param int $egiId
     * @return JsonResponse
     */
    public function getMintStatus(int $egiId): JsonResponse {
        try {
            $egi = Egi::findOrFail($egiId);

            // Authorization: Only owner or buyer can check status
            if ($egi->user_id !== Auth::id() && $egi->owner_id !== Auth::id()) {
                // Check if user is the buyer
                $isBuyer = EgiBlockchain::where('egi_id', $egiId)
                    ->where('buyer_user_id', Auth::id())
                    ->exists();

                if (!$isBuyer) {
                    return $this->errorManager->handle('MINT_STATUS_UNAUTHORIZED', [
                        'user_id' => Auth::id(),
                        'egi_id' => $egiId,
                    ]);
                }
            }

            // Get blockchain record
            $blockchain = $egi->blockchain;

            if (!$blockchain) {
                return response()->json([
                    'status' => 'not_started',
                    'message' => 'Mint not initiated',
                ]);
            }

            // Build status response
            $response = [
                'status' => $blockchain->mint_status,
                'egi_id' => $egiId,
                'blockchain_record_id' => $blockchain->id,
            ];

            // Add blockchain data if minted
            if ($blockchain->isMinted()) {
                $response['asa_id'] = $blockchain->asa_id;
                $response['tx_id'] = $blockchain->blockchain_tx_id;
                $response['minted_at'] = $blockchain->minted_at?->toIso8601String();
                $response['explorer_url'] = "https://testnet.explorer.perawallet.app/asset/{$blockchain->asa_id}";
            }

            // Add error if failed
            if ($blockchain->mint_status === 'failed') {
                $response['error'] = $blockchain->mint_error;
            }

            // Add processing info if queued/minting
            if (in_array($blockchain->mint_status, ['minting_queued', 'minting'])) {
                $response['estimated_completion'] = now()->addMinutes(5)->toIso8601String();
                $response['message'] = 'Mint in progress on Algorand blockchain';
            }

            $this->logger->debug('Mint status checked via API', [
                'user_id' => Auth::id(),
                'egi_id' => $egiId,
                'status' => $blockchain->mint_status,
            ]);

            return response()->json($response);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorManager->handle('MINT_STATUS_EGI_NOT_FOUND', [
                'user_id' => Auth::id(),
                'egi_id' => $egiId,
            ], $e);
        } catch (\Exception $e) {
            return $this->errorManager->handle('MINT_STATUS_CHECK_ERROR', [
                'user_id' => Auth::id(),
                'egi_id' => $egiId,
                'error' => $e->getMessage(),
            ], $e);
        }
    }
}
