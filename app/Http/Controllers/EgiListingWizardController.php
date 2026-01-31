<?php

namespace App\Http\Controllers;

use App\Models\Egi;
use App\Services\Commerce\EgiListingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class EgiListingWizardController extends Controller
{
    protected EgiListingService $listingService;

    public function __construct(EgiListingService $listingService)
    {
        $this->middleware('auth');
        $this->listingService = $listingService;
    }

    /**
     * Display the listing wizard for an EGI.
     */
    public function show(Egi $egi)
    {
        $this->authorize('update', $egi);

        $shippingRequired = $this->listingService->shippingRequiredForEgi($egi);
        $paymentMethods = $egi->collection->getEffectivePaymentMethods();

        return view('merchant.egi.listing_wizard', [
            'egi' => $egi,
            'collection' => $egi->collection,
            'shippingRequired' => $shippingRequired,
            'paymentMethods' => $paymentMethods,
        ]);
    }

    /**
     * Update EGI listing settings.
     */
    public function update(Egi $egi, Request $request)
    {
        $this->authorize('update', $egi);

        try {
            $this->listingService->updateListing($egi, $request->all());

            return redirect()
                ->route('egi.listing.wizard', $egi)
                ->with('success', __('commerce.listing.messages.updated'));
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    /**
     * Publish the EGI listing (make it sellable).
     */
    public function publish(Egi $egi)
    {
        $this->authorize('update', $egi);

        try {
            if ($this->listingService->validateSellable($egi)) {
                $egi->update(['is_sellable' => true]);

                return redirect()
                    ->route('egis.show', $egi)
                    ->with('success', __('commerce.listing.messages.published'));
            }
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }
    }
}
