<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

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
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
    }
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











