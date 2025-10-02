<?php

namespace App\Http\Controllers\PA;

use App\Http\Controllers\Controller;
use App\Models\Egi;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * PA Heritage Controller - Lista e dettaglio patrimonio culturale
 *
 * @package App\Http\Controllers\PA
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - PA Enterprise MVP)
 * @date 2025-10-02
 * @purpose Controller per gestione visualizzazione patrimonio culturale PA Entity
 *
 * Features:
 * - Lista patrimonio con filtri (search, CoA status)
 * - Dettaglio singolo bene con CoA display
 * - Authorization via collection ownership
 * - ULM logging per accessi
 * - ErrorManager exception handling
 *
 * GDPR: Read-only operations, ULM logging attivo
 */
class PAHeritageController extends Controller {
    /**
     * Dependencies injection
     */
    public function __construct(
        protected UltraLogManager $logger,
        protected ErrorManagerInterface $errorManager
    ) {
        $this->middleware(['auth', 'role:pa_entity']);
    }

    /**
     * Lista patrimonio culturale con filtri
     *
     * @param Request $request
     * @return View|\Illuminate\Http\RedirectResponse
     *
     * Features:
     * - Query heritage via collection ownership (whereHas)
     * - Filters: search (title/artist), CoA status
     * - Pagination 15 items per page
     * - ULM logging con filtri applicati
     *
     * IMPORTANT: NO hidden ->take() limit (REGOLA STATISTICS)
     */
    public function index(Request $request) {
        try {
            $user = Auth::user();

            // Query heritage owned by PA entity
            $query = Egi::whereHas('collections', function ($q) use ($user) {
                $q->where('collections.owner_id', $user->id)
                    ->where('collections.type', 'artwork'); // MVP uses artwork type
            })->with(['coa', 'collections', 'media']);

            // Filter: Search by title or artist
            if ($search = $request->input('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('artist', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Filter: CoA status
            if ($coaStatus = $request->input('coa_status')) {
                if ($coaStatus === 'no_coa') {
                    $query->doesntHave('coa');
                } else {
                    $query->whereHas('coa', function ($q) use ($coaStatus) {
                        $q->where('status', $coaStatus);
                    });
                }
            }

            // Pagination (NO hidden ->take() limit!)
            $heritage = $query->orderBy('created_at', 'desc')->paginate(15);

            // ULM: Log heritage list access
            $this->logger->info('PA Heritage list accessed', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'filters' => $request->only(['search', 'coa_status']),
                'results_count' => $heritage->count(),
                'total_heritage' => $heritage->total(),
            ]);

            return view('pa.heritage.index', compact('heritage'));
        } catch (\Exception $e) {
            $this->errorManager->handle('PA_HERITAGE_LIST_ERROR', [], $e);

            return redirect()->back()->withErrors([
                'error' => 'Impossibile caricare la lista del patrimonio. Riprova tra poco.'
            ]);
        }
    }

    /**
     * Dettaglio singolo bene patrimoniale + CoA
     *
     * @param Egi $egi
     * @return View|\Illuminate\Http\RedirectResponse
     *
     * Features:
     * - Authorization: verify PA entity owns collection
     * - Eager load: CoA + files + signatures + traits + media
     * - ULM logging con EGI details
     *
     * GDPR: Read-only, no data modification
     */
    public function show(Egi $egi) {
        try {
            $user = Auth::user();

            // Authorization: PA entity must own collection containing this EGI
            $ownsCollection = $egi->collections()
                ->where('owner_id', $user->id)
                ->exists();

            if (!$ownsCollection) {
                $this->logger->warning('PA Heritage unauthorized access attempt', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'egi_id' => $egi->id,
                    'egi_title' => $egi->title,
                ]);

                abort(403, 'Non hai accesso a questo bene patrimoniale.');
            }

            // Eager load relationships for detail view
            $egi->load([
                'coa.files',
                'coa.signatures.signer',
                'coa.events',
                'coaTraits', // CoA traits (technique/materials/support)
                'collections.owner',
                'media',
            ]);

            // ULM: Log heritage detail access
            $this->logger->info('PA Heritage detail accessed', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'egi_id' => $egi->id,
                'egi_title' => $egi->title,
                'has_coa' => $egi->coa !== null,
                'coa_status' => $egi->coa?->status,
            ]);

            return view('pa.heritage.show', compact('egi'));
        } catch (\Exception $e) {
            // Skip re-throwing 403 abort exceptions
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException && $e->getStatusCode() === 403) {
                throw $e;
            }

            $this->errorManager->handle('PA_HERITAGE_DETAIL_ERROR', [], $e);

            return redirect()->route('pa.heritage.index')->withErrors([
                'error' => 'Impossibile caricare il dettaglio del bene. Riprova tra poco.'
            ]);
        }
    }
}
