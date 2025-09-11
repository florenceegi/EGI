<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class UniversalSearchModal extends Component {
    public function render(): View|string {
        return view('components.universal-search-modal');
    }
}
