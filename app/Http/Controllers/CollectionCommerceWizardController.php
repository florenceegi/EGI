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
        app('Ultra\UltraLogManager\UltraLogManager')->info('COMMERCE_WIZARD_SHOW', ['collection_id' => $collection->id, 'user_id' => auth()->id()]);
        $this->authorize('update', $collection);

        $eppProjects = \App\Models\EppProject::active()->get();

        return view('merchant.collection.commerce_wizard', [
            'collection' => $collection,
            'paymentMethods' => $collection->getEffectivePaymentMethods(),
            'eppProjects' => $eppProjects,
        ]);
    }

    /**
     * Update collection commerce settings.
     */
    public function update(Collection $collection, Request $request)
    {
        app('Ultra\UltraLogManager\UltraLogManager')->info('COMMERCE_WIZARD_UPDATE_REQUEST', ['collection_id' => $collection->id, 'data_keys' => array_keys($request->all())]);
        $this->authorize('update', $collection);

        try {
            $this->commerceService->updateSettings($collection, $request->all());

            return redirect()
                ->route('collections.commerce.wizard', $collection)
                ->with('success', __('commerce.setup.messages.settings_updated'));
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    /**
     * Enable commercial status for the collection.
     */
    public function enable(Collection $collection)
    {
        app('Ultra\UltraLogManager\UltraLogManager')->info('COMMERCE_WIZARD_ENABLE_REQUEST', ['collection_id' => $collection->id]);
        $this->authorize('update', $collection);

        try {
            $this->commerceService->enableCommercial($collection);

            return redirect()
                ->route('home.collections.show', $collection)
                ->with('success', __('commerce.setup.messages.enabled_success'));
        } catch (ValidationException $e) {
             return back()->withErrors($e->errors());
        }
    }
}
