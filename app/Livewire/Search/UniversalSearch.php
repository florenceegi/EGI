<?php

namespace App\Livewire\Search;

use Livewire\Component;
use App\Services\UniversalSearchService;

class UniversalSearch extends Component {
    public string $q = '';
    public array $selectedTypes = ['egi', 'collection', 'creator'];
    public array $selectedTraits = [];
    public array $selectedUserTypes = [];
    public array $selectedCollections = [];

    public array $suggestions = [];
    public array $facets = [];

    public bool $open = false;

    protected $queryString = [
        'q' => ['except' => ''],
    ];

    public function updatedQ(UniversalSearchService $service) {
        if (mb_strlen($this->q) < 2) {
            $this->suggestions = [];
            return;
        }
        $this->suggestions = $service->suggestions($this->q);
        if (!$this->facets) {
            $this->facets = $service->traitFacets();
        }
    }

    public function toggleType(string $type) {
        if (in_array($type, $this->selectedTypes)) {
            $this->selectedTypes = array_values(array_diff($this->selectedTypes, [$type]));
        } else {
            $this->selectedTypes[] = $type;
        }
    }

    public function toggleTrait(string $value) {
        if (in_array($value, $this->selectedTraits)) {
            $this->selectedTraits = array_values(array_diff($this->selectedTraits, [$value]));
        } else {
            $this->selectedTraits[] = $value;
        }
    }

    public function goToResults() {
        $params = [
            'q' => $this->q,
            'types' => implode(',', $this->selectedTypes),
        ];
        if ($this->selectedTraits) $params['traits'] = $this->selectedTraits;
        if ($this->selectedUserTypes) $params['user_types'] = $this->selectedUserTypes;
        if ($this->selectedCollections) $params['collections'] = $this->selectedCollections;
        return redirect()->route('search.results', $params);
    }

    public function render(UniversalSearchService $service) {
        // Precarichiamo facets una sola volta
        if (!$this->facets) {
            $this->facets = $service->traitFacets();
        }
        return view('livewire.search.universal-search');
    }
}
