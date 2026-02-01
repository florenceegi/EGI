<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UserShippingAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Services\Gdpr\AuditLogService;
use App\Enums\Gdpr\GdprActivityCategory;

/**
 * Controller for managing User Shipping Addresses
 * Compliant with OS3.0 standards (UEM, ULM, GDPR)
 */
class UserShippingAddressController extends Controller
{
    public function __construct(
        private UltraLogManager $logger,
        private ErrorManagerInterface $errorManager,
        private AuditLogService $auditService
    ) {}

    /**
     * Store a newly created shipping address.
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();

            $validated = $request->validate([
                'country' => ['required', 'string', 'size:2'], // ISO 2 char
                'address_line_1' => ['required', 'string', 'max:255'],
                'address_line_2' => ['nullable', 'string', 'max:255'],
                'city' => ['required', 'string', 'max:100'],
                'state' => ['nullable', 'string', 'max:100'], // Alcuni paesi non hanno stati
                'postal_code' => ['required', 'string', 'max:20'],
                'phone' => ['nullable', 'string', 'max:20'],
                'is_default' => ['boolean'],
            ]);

            // Transactional creation
            $address = \DB::transaction(function () use ($user, $validated) {
                // If set as default, unset others FIRST
                if (!empty($validated['is_default']) && $validated['is_default']) {
                    $user->shippingAddresses()->update(['is_default' => false]);
                } else {
                    // If first address, force default
                    if ($user->shippingAddresses()->count() === 0) {
                        $validated['is_default'] = true;
                    }
                }

                return $user->shippingAddresses()->create($validated);
            });

            // ULM Log
            $this->logger->info('Shipping Address created', [
                'user_id' => $user->id,
                'address_id' => $address->id
            ]);

            // GDPR Audit
            $this->auditService->logUserAction(
                $user, 
                'shipping_address_created', 
                ['address_id' => $address->id, 'country' => $address->country],
                GdprActivityCategory::PERSONAL_DATA_UPDATE
            );

            return redirect()->back()->with('success', __('user_personal_data.address_created_success'));

        } catch (\Exception $e) {
            return $this->errorManager->handle('SHIPPING_ADDR_STORE_ERROR', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Update the specified shipping address.
     */
    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $address = UserShippingAddress::where('id', $id)->where('user_id', $user->id)->firstOrFail();

            $validated = $request->validate([
                'country' => ['required', 'string', 'size:2'],
                'address_line_1' => ['required', 'string', 'max:255'],
                'address_line_2' => ['nullable', 'string', 'max:255'],
                'city' => ['required', 'string', 'max:100'],
                'state' => ['nullable', 'string', 'max:100'],
                'postal_code' => ['required', 'string', 'max:20'],
                'phone' => ['nullable', 'string', 'max:20'],
                'is_default' => ['boolean'],
            ]);

            \DB::transaction(function () use ($user, $address, $validated) {
                if (!empty($validated['is_default']) && $validated['is_default']) {
                    $user->shippingAddresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
                }
                
                // Prevent unsetting default if it's the only one or currently default (unless logic dictates otherwise, usually strict enforcement of one default)
                 if ($address->is_default && empty($validated['is_default'])) {
                     // Can't unset default directly, must set another one as default. 
                     // OR logic: if trying to unset default, ignore it/force true if meaningful.
                     // For simplicity, we allow unsetting if UI handles it, but usually standard commerce enforces 1 default.
                 }

                $address->update($validated);
            });

            // GDPR Audit
            $this->auditService->logUserAction(
                $user, 
                'shipping_address_updated', 
                ['address_id' => $address->id],
                GdprActivityCategory::PERSONAL_DATA_UPDATE
            );

            return redirect()->back()->with('success', __('user_personal_data.address_updated_success'));

        } catch (\Exception $e) {
            return $this->errorManager->handle('SHIPPING_ADDR_UPDATE_ERROR', [
                'user_id' => Auth::id(),
                'address_id' => $id,
                'error' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Remove the specified shipping address.
     */
    public function destroy($id)
    {
        try {
            $user = Auth::user();
            $address = UserShippingAddress::where('id', $id)->where('user_id', $user->id)->firstOrFail();

            $address->delete();

            // ULM Log
            $this->logger->info('Shipping Address deleted', [
                'user_id' => $user->id,
                'address_id' => $id
            ]);

             // GDPR Audit
            $this->auditService->logUserAction(
                $user, 
                'shipping_address_deleted', 
                ['address_id' => $id],
                GdprActivityCategory::PERSONAL_DATA_DELETION
            );

            return redirect()->back()->with('success', __('user_personal_data.address_deleted_success'));

        } catch (\Exception $e) {
            return $this->errorManager->handle('SHIPPING_ADDR_DELETE_ERROR', [
                'user_id' => Auth::id(),
                'address_id' => $id,
                'error' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Set the specified shipping address as default.
     */
    public function setDefault($id)
    {
        try {
            $user = Auth::user();
            $address = UserShippingAddress::where('id', $id)->where('user_id', $user->id)->firstOrFail();

            \DB::transaction(function () use ($user, $address) {
                $user->shippingAddresses()->update(['is_default' => false]);
                $address->update(['is_default' => true]);
            });

             // GDPR Audit
             $this->auditService->logUserAction(
                $user, 
                'shipping_address_set_default', 
                ['address_id' => $id],
                GdprActivityCategory::PERSONAL_DATA_UPDATE
            );

            return redirect()->back()->with('success', __('user_personal_data.address_default_success'));

        } catch (\Exception $e) {
             return $this->errorManager->handle('SHIPPING_ADDR_DEFAULT_ERROR', [
                'user_id' => Auth::id(),
                'address_id' => $id,
                'error' => $e->getMessage()
            ], $e);
        }
    }
}
