<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * @Oracode Controller: SuperAdmin Dashboard
 * 🎯 Purpose: SuperAdmin main dashboard with platform overview
 *
 * @package App\Http\Controllers\Superadmin
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0
 */
class SuperadminDashboardController extends Controller
{
    /**
     * Display SuperAdmin dashboard
     */
    public function index(Request $request): View
    {
        return view('superadmin.dashboard', [
            'pageTitle' => 'SuperAdmin Dashboard',
        ]);
    }
}



