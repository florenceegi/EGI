<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Icon;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

class IconAdminController extends Controller
{
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->middleware('can:manage_icons');
    }

    public function index()
    {
        $icons = Icon::all();
        return view('admin.icons.index', compact('icons'));
    }

    public function edit(Icon $icon)
    {
        return view('admin.icons.edit', compact('icon'));
    }

    public function update(Request $request, Icon $icon)
    {
        $request->validate([
            'html' => 'required|string',
        ]);

        $icon->update([
            'html' => $request->svg,
        ]);

        return redirect()->route('admin.icons.index')->with('status', 'Icon updated successfully!');
    }
}
