<?php

namespace App\Http\Controllers;

use App\Models\Egi;
use App\Actions\Egi\CloneEgiFromMasterAction;
use App\Helpers\FegiAuth;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * Controller for EGI Master Clonable System operations
 */
class EgiMasterController extends Controller
{
    public function __construct(
        protected UltraLogManager $logger,
        protected ErrorManagerInterface $errorManager
    ) {}

    /**
     * Toggle the Master Template status for an EGI
     */
    public function toggleMaster(Request $request, Egi $egi): JsonResponse
    {
        try {
            $user = FegiAuth::user();
            
            // Authorization: Must be owner/creator (and potentially admin in future)
            if ((int)$egi->user_id !== (int)$user->id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            // Validations
            if ($egi->parent_id) {
                return response()->json(['message' => 'Cannot make a child EGI a Master.'], 400);
            }

            if ($egi->token_EGI) {
                return response()->json(['message' => 'Cannot make a minted EGI a Master (for now).'], 400);
            }

            $isMaster = !$egi->is_template;
            
            $egi->update([
                'is_template' => $isMaster,
                'is_sellable' => !$isMaster // Masters are not sellable
            ]);

            $this->logger->info('EGI_MASTER_TOGGLE', [
                'egi_id' => $egi->id,
                'user_id' => $user->id,
                'is_template' => $isMaster
            ]);

            return response()->json([
                'success' => true,
                'is_template' => $isMaster,
                'is_sellable' => !$isMaster,
                'message' => $isMaster ? 'EGI is now a Master Template' : 'EGI is no longer a Master Template'
            ]);

        } catch (\Exception $e) {
            return $this->errorManager->handle('EGI_MASTER_TOGGLE_ERROR', [
                'egi_id' => $egi->id
            ], $e);
        }
    }

    /**
     * Toggles the "Buyer Cloning" permission for a Master Template.
     */
    public function toggleBuyerCloning(Egi $egi)
    {
        // Check if user is creator
        if (Auth::id() !== $egi->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Must be a template to enable buyer cloning
        if (!$egi->is_template) {
             return response()->json(['error' => 'EGI is not a Master Template'], 400);
        }

        $egi->allow_buyer_clone = !$egi->allow_buyer_clone;
        $egi->save();

        return response()->json([
            'message' => 'Buyer cloning status toggled',
            'allow_buyer_clone' => $egi->allow_buyer_clone
        ]);
    }

    /**
     * Generate a clone from a Master EGI
     */
    public function clone(Request $request, Egi $egi, CloneEgiFromMasterAction $action): JsonResponse
    {
        try {
            $user = FegiAuth::user();

            // Authorization
            if ((int)$egi->user_id !== (int)$user->id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            if (!$egi->is_template) {
                return response()->json(['message' => 'EGI is not a Master Template.'], 400);
            }

            // Check limit or payment (future). For now free for creator.

            $child = $action->execute($egi, $user);

            return response()->json([
                'success' => true,
                'message' => 'Clone generated successfully',
                'child_id' => $child->id,
                'serial_number' => $child->serial_number,
                'redirect_url' => route('egis.show', $child->id)
            ]);

        } catch (\Exception $e) {
            return $this->errorManager->handle('EGI_CLONE_ERROR', [
                'egi_id' => $egi->id
            ], $e);
        }
    }
}
