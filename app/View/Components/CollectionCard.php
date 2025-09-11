<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

use App\Models\Collection;

class CollectionCard extends Component {
    public $collection;
    public $id;

    /**
     * Create a new component instance.
     */
    public function __construct($id) {

        $this->id = $id;

        // Carica la collection usando l'ID
        $this->collection = Collection::findOrFail($id);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|string {
        return view('components.collection-card', [
            'collection' => $this->collection,
            'id' => $this->id
        ]);
    }
}
