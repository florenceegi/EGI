<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    public $pageTitle;
    public $pageDescription;
    public $robotsContent;
    public $ogType;
    public $ogImage;

    public function __construct(
        $pageTitle = null, 
        $pageDescription = null, 
        $robotsContent = null,
        $ogType = null,
        $ogImage = null
    )
    {
        $this->pageTitle = $pageTitle;
        $this->pageDescription = $pageDescription;
        $this->robotsContent = $robotsContent;
        $this->ogType = $ogType;
        $this->ogImage = $ogImage;
    }

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.app');
    }
}
