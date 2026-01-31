<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Services\Commerce\CollectionCommercialService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CollectionCommerceWizardController extends Controller
{
    protected CollectionCommercialService $commerceService;

    public function __construct(CollectionCommercialService $commerceService)
    {
        $this->middleware('auth');
        $this->commerceService = $commerceService;
    }

    /**
     * Display the commerce wizard for a collection.
     */
    public function show(Collection $collection)
    {
        $this->authorize('update', $collection);

        return view('merchant.collection.commerce_wizard', [
            'collection' => $collection,
            'paymentMethods' => $collection->getEffectivePaymentMethods(),
        ]);
    }

    /**
     * Update collection commerce settings.
     */
    public function update(Collection $collection, Request $request)
    {
        $this->authorize('update', $collection);

        try {
            $this->commerceService->updateSettings($collection, $request->all());

            return redirect()
                ->route('collection.commerce.wizard', $collection)
                ->with('success', 'Commercial settings updated successfully.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    /**
     * Enable commercial status for the collection.
     */
    public function enable(Collection $collection)
    {
        $this->authorize('update', $collection);

        try {
            $this->commerceService->enableCommercial($collection);

            return redirect()
                ->route('collection.show', $collection)
                ->with('success', 'Collection is now enabled for commerce!');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }
    }
}
