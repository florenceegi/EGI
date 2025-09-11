<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class CreatorCard extends Component {
    public function __construct(public $creator, public string $imageType = 'card', public string $displayType = 'default') {
    }

    public function render(): View|string {
        return view('components.creator-card');
    }
}
