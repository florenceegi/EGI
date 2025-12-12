<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

class AssistantController extends Controller
{
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;

    public function __construct(UltraLogManager $logger, ErrorManagerInterface $errorManager)
    {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
    }

    public function setAutoOpen(Request $request)
    {
        $this->logger->info('Assistant auto-open POST', [
            'input' => $request->all(),
            'session_before' => session()->all(),
        ]);
        $request->validate(['auto_open' => 'boolean']);
        // Forza il valore booleano anche se arriva come stringa
        $autoOpen = filter_var($request->input('auto_open'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        session(['natan_assistant_auto_open' => $autoOpen]);
        $this->logger->info('Assistant auto-open POST after', [
            'auto_open' => $autoOpen,
            'session_after' => session()->all(),
        ]);
        return response()->json(['success' => true, 'auto_open' => $autoOpen, 'session' => session()->all()]);
    }
}
