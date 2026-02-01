<?php

namespace App\Http\Controllers\Notifications\Commerce;

use App\Enums\Gdpr\GdprActivityCategory;
use App\Http\Controllers\Controller;
use App\Models\CustomDatabaseNotification;
use App\Models\EgiBlockchain;
use App\Notifications\Commerce\EgiShippedNotification;
use App\Services\Gdpr\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;

class NotificationCommerceResponseController extends Controller
{
    public function __construct(
        private UltraLogManager $logger,
        private ErrorManagerInterface $errorManager,
        private AuditLogService $auditService
    ) {}

    public function handleShipped(Request $request)
    {
        try {
            $this->logger->info('Commerce Shipment processing started', [
                'user_id' => Auth::id(), 
                'request_data' => $request->only(['notification_id', 'carrier'])
            ]);

            $validated = $request->validate([
                'notification_id' => 'required|exists:notifications,id',
                'carrier'         => 'required|string|max:255',
                'tracking_code'   => 'required|string|max:255',
            ]);

            $notification = CustomDatabaseNotification::find($validated['notification_id']);

            // Security check: ensure the notification belongs to the actual logged-in user (merchant)
            if ($notification->notifiable_id !== Auth::id()) {
                abort(403);
            }

            // Retrieve Payload Shipping
            $payload = \App\Models\NotificationPayloadShipping::find($notification->model_id);

            if (!$payload) {
                // Fallback for legacy or direct access scenarios (if any)
                // But mainly we expect payload now.
                return back()->with('error', __('commerce.notifications.flash.order_not_found'));
            }

            // Update Payload
            $payload->update([
                'carrier'       => $validated['carrier'],
                'tracking_code' => $validated['tracking_code'],
                'shipped_at'    => now(),
                'status'        => 'shipped'
            ]);

            // Sync with Ecosystem Blockchain Record
            $purchase = $payload->egiBlockchain;
            if ($purchase) {
                $purchase->update([
                    'carrier'       => $validated['carrier'],
                    'tracking_code' => $validated['tracking_code'],
                    'shipped_at'    => now(),
                ]);
            }

            // GDPR Audit (Update personal/transactional data)
            $this->auditService->logUserAction(
                Auth::user(), 
                'order_shipped', 
                [ 
                    'purchase_id' => $purchase->id ?? 'N/A',
                    'payload_id'  => $payload->id,
                    'carrier' => $validated['carrier'],
                    'tracking_code' => $validated['tracking_code']
                ],
                GdprActivityCategory::BLOCKCHAIN_ACTIVITY // Using closest fit as per plan (or NOTIFICATION_MANAGEMENT)
            );

            // 2. Invia notifica al buyer
            if ($purchase->buyer) {
                $purchase->buyer->notify(new EgiShippedNotification($purchase));
            }

            // 3. Mark notification as 'SHIPPED' (outcome) and read (archived)
            $notification->update([
                'outcome' => 'SHIPPED',
                'read_at' => now(), // Archivia la notifica
            ]);

            $this->logger->info('Commerce Shipment processing completed', [
                'purchase_id' => $purchase->id
            ]);

            return back()->with('success', __('commerce.notifications.flash.shipped_success'));

        } catch (\Exception $e) {
            return $this->errorManager->handle('COMMERCE_SHIPMENT_FAILED', [
                'user_id' => Auth::id(),
                'notification_id' => $request->input('notification_id')
            ], $e);
        }
    }
}
