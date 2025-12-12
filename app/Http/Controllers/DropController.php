<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use Illuminate\Http\Request;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

class DropController extends Controller
{
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
    }
    public function index()
    {
       // Collezioni in evidenza (3 items)
       $collections = Collection::where('is_published', true)
       ->take(3)
       ->get();

        // Ultime gallerie (8 items)
        // $recent = Collection::orderBy('created_at', 'desc')
        //     ->take(8)
        //     ->get();

        return view('home', compact('collections'));
    }
}
