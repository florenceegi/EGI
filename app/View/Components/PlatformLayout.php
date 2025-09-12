<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class PlatformLayout extends Component
{
    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('layouts.platform'); // Riferimento al file Blade del layout
    }
}