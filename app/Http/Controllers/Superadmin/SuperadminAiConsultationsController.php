<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\AiTraitGeneration;
use App\Models\Egi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * @Oracode Controller: SuperAdmin AI Consultations Management
 * 🎯 Purpose: Manage and monitor all AI consultations (trait generations, descriptions, etc.)
 *
 * @package App\Http\Controllers\Superadmin
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0
 */
class SuperadminAiConsultationsController extends Controller
{
    /**
     * Display all AI consultations with filters
     */
    public function index(Request $request): View
    {
        $query = AiTraitGeneration::with(['egi', 'user', 'proposals'])
            ->orderBy('created_at', 'desc');

        // Filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $consultations = $query->paginate(50);

        Log::channel('upload')->info('[SuperAdmin] AI Consultations index viewed', [
            'admin_id' => auth()->id(),
            'filters' => $request->only(['status', 'user_id', 'date_from', 'date_to']),
        ]);

        return view('superadmin.ai.consultations.index', [
            'consultations' => $consultations,
            'pageTitle' => 'Gestione Consulenze AI',
        ]);
    }

    /**
     * Display single consultation details
     */
    public function show(AiTraitGeneration $generation): View
    {
        $generation->load(['egi', 'user', 'proposals.matchedCategory', 'proposals.matchedType', 'proposals.createdCategory', 'proposals.createdType']);

        Log::channel('upload')->info('[SuperAdmin] AI Consultation details viewed', [
            'admin_id' => auth()->id(),
            'generation_id' => $generation->id,
            'egi_id' => $generation->egi_id,
        ]);

        return view('superadmin.ai.consultations.show', [
            'generation' => $generation,
            'pageTitle' => "Consulenza AI #{$generation->id}",
        ]);
    }

    /**
     * Display consultations for specific EGI
     */
    public function byEgi(Egi $egi): View
    {
        $consultations = $egi->aiTraitGenerations()
            ->with(['user', 'proposals'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('superadmin.ai.consultations.by-egi', [
            'egi' => $egi,
            'consultations' => $consultations,
            'pageTitle' => "Consulenze AI - {$egi->title}",
        ]);
    }

    /**
     * Display consultations for specific user
     */
    public function byUser(User $user): View
    {
        $consultations = AiTraitGeneration::where('user_id', $user->id)
            ->with(['egi', 'proposals'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('superadmin.ai.consultations.by-user', [
            'user' => $user,
            'consultations' => $consultations,
            'pageTitle' => "Consulenze AI - {$user->name}",
        ]);
    }

    /**
     * Display analytics dashboard
     */
    public function analytics(Request $request): View
    {
        // TODO: Implement analytics logic
        // - Total consultations by status
        // - Average confidence scores
        // - Most common trait categories
        // - User engagement metrics
        // - Trait creation vs matching ratios

        return view('superadmin.ai.consultations.analytics', [
            'pageTitle' => 'Analytics Consulenze AI',
        ]);
    }
}
