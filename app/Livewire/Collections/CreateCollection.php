<?php

namespace App\Livewire\Collections;

use App\Helpers\FegiAuth;
use Livewire\Component;
use App\Models\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Validate;

class CreateCollection extends Component {
    public $collection = [
        'creator_id' => null,
        'type' => 'image',
        'status' => 'draft',
        'is_published' => false,
        'collection_name' => null,
        'position' => null,
        'EGI_number' => null,
        'floor_price' => null,
        'description' => null,
    ];

    // Regole di validazione
    protected $rules = [
        'collection.creator_id' => 'required|exists:users,id',
        'collection.collection_name' => 'required|string|max:255',
        'collection.type' => 'required|string|in:image,e-book,audio,video',
        'collection.position' => 'nullable|integer',
        'collection.EGI_number' => 'nullable|integer',
        'collection.floor_price' => 'nullable|numeric',
        'collection.description' => 'nullable|string',
        'collection.status' => 'nullable|string|in:draft,pending_approval,published',
        'collection.is_published' => 'nullable|boolean',
    ];

    public function create() {
        Log::channel('florenceegi')->info('Class: CreateCollection. Method: create()');

        try {
            $this->prepareCollectionData();

            $this->validate();

            $collection = Collection::create($this->collection);

            Log::channel('florenceegi')->info('Collection created successfully', [
                'collection_id' => $collection->id,
                'collection_name' => $collection->collection_name,
            ]);

            session()->flash('message', __('collection.created_successfully'));

            // Associa l'utente come creator nella tabella pivot collection_user
            $collection->users()->attach(Auth::id(), ['role' => 'creator']);

            // Reset dei campi
            $this->resetInputFields();

            return redirect()->route('home.collections.show', $collection->id);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::channel('florenceegi')->warning('Validation failed during collection creation', [
                'errors' => $e->errors(),
            ]);
            session()->flash('error', __('collection.create_validation_error'));
            throw $e;
        } catch (\Exception $e) {
            Log::channel('florenceegi')->error('Failed to create collection', [
                'error' => $e->getMessage(),
            ]);
            session()->flash('error', __('collection.creation_failed'));
        }
    }

    private function prepareCollectionData() {
        $this->collection['creator_id'] = FegiAuth::id();
        $this->collection['epp_id'] = config('app.epp_id');
        $this->collection['type'] = $this->collection['type'] ?? 'image';
        $this->collection['status'] = 'draft';
        $this->collection['is_published'] = false;
        $this->collection['position'] = $this->collection['position'] ?? 1;
        $this->collection['EGI_number'] = $this->collection['EGI_number'] ?? 1;
        $this->collection['floor_price'] = $this->collection['floor_price'] ?? 0.0;
    }

    private function resetInputFields() {
        $this->collection = [
            'creator_id' => null,
            'type' => 'image',
            'status' => 'draft',
            'is_published' => false,
            'collection_name' => null,
            'position' => null,
            'EGI_number' => null,
            'floor_price' => null,
            'description' => null,
        ];
    }

    public function render() {
        return view('livewire.collections.create-collection');
    }
}
