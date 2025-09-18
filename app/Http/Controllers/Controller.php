<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\View\View; // Importa la classe View

abstract class Controller extends BaseController {
    use AuthorizesRequests, ValidatesRequests;
}
