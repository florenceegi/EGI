<?php

namespace App\Http\Controllers\PaActs;

use App\Http\Controllers\Controller;
use App\Models\Egi;
use App\Services\PaActs\PaActService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * PA Act Controller - CRUD Views
 * 
 * ============================================================================
 * CONTESTO - GESTIONE VISUALIZZAZIONI ATTI PA
 * ============================================================================
 * 
 * Controller per le views CRUD degli atti PA tokenizzati:
 * - Index: Lista atti PA dell'ente
 * - Show: Dettaglio singolo atto + info blockchain
 * - (Create/Edit gestiti da EgiController con ViewService routing)
 * 
 * TARGET USER: PA entities (enti pubblici amministrazione)
 * 
 * DIFFERENZE vs EgiController:
 * - Filtri specifici PA: protocol number, doc type, date range
 * - Display: Metadata PA (protocol, signature, blockchain)
 * - Brand: PA colors (#1B365D, #D4A574, #2D5016)
 * - Terminology: "Atto" not "Opera", "Protocollo" not "Position"
 * 
 * ============================================================================
 * ROUTES
 * ============================================================================
 * 
 * GET /pa/acts
 * - Name: pa.acts.index
 * - Middleware: auth, role:pa_entity
 * - View: pa/acts/index.blade.php
 * - Purpose: Lista atti PA dell'ente con filtri
 * 
 * GET /pa/acts/{egi}
 * - Name: pa.acts.show
 * - Middleware: auth, role:pa_entity
 * - View: pa/acts/show.blade.php
 * - Purpose: Dettaglio atto con blockchain info
 * 
 * ============================================================================
 * INDEX - LISTA ATTI PA
 * ============================================================================
 * 
 * FEATURES:
 * - Lista atti dell'ente autenticato
 * - Filtri: Protocol number, doc type, date range, anchoring status
 * - Sort: Protocol date desc (default), protocol number, upload date
 * - Pagination: 15 items per page
 * - Search: Full-text su title + description
 * 
 * QUERY:
 * ```php
 * Egi::with(['collection', 'user'])
 *     ->whereHas('collection', fn($q) => $q->where('creator_id', $user->id))
 *     ->where('metadata->doc_type', 'delibera') // Se filtro attivo
 *     ->where('metadata->protocol_number', 'LIKE', '12345%') // Se search
 *     ->whereDate('metadata->protocol_date', '>=', '2025-01-01') // Se date range
 *     ->orderBy('metadata->protocol_date', 'desc')
 *     ->paginate(15);
 * ```
 * 
 * VIEW DATA:
 * ```php
 * return view('pa.acts.index', [
 *     'acts' => $acts, // Paginated collection
 *     'filters' => $request->only(['search', 'doc_type', 'date_from', 'date_to', 'status']),
 *     'docTypes' => config('AllowedFileType.pa_documents.document_types'),
 *     'stats' => [
 *         'total' => $totalCount,
 *         'anchored' => $anchoredCount,
 *         'pending' => $pendingCount
 *     ]
 * ]);
 * ```
 * 
 * ============================================================================
 * SHOW - DETTAGLIO ATTO
 * ============================================================================
 * 
 * FEATURES:
 * - Metadata completo atto PA
 * - Info firma digitale (signer, cert, timestamp)
 * - Blockchain data (TXID, Merkle proof, anchor status)
 * - QR code per verifica pubblica
 * - Link verifica pubblica
 * - Download PDF originale (se autorizzato)
 * 
 * VIEW DATA:
 * ```php
 * return view('pa.acts.show', [
 *     'act' => $egi, // Con metadata completo
 *     'metadata' => [
 *         'protocol_number' => $egi->metadata['protocol_number'],
 *         'protocol_date' => $egi->metadata['protocol_date'],
 *         'doc_type' => $egi->metadata['doc_type'],
 *         'doc_hash' => $egi->metadata['doc_hash'],
 *         'signature_validation' => $egi->metadata['signature_validation'],
 *         'anchor_txid' => $egi->metadata['anchor_txid'] ?? null,
 *         'public_code' => $egi->metadata['public_code'],
 *         'anchored' => $egi->metadata['anchored'] ?? false
 *     ],
 *     'verification_url' => route('verify.act', $egi->metadata['public_code']),
 *     'qr_code_url' => $egi->metadata['qr_code_path'] ?? null
 * ]);
 * ```
 * 
 * ============================================================================
 * AUTHORIZATION
 * ============================================================================
 * 
 * INDEX:
 * - Solo atti dell'ente autenticato
 * - Query filtrata: whereHas('collection', creator_id = auth_user_id)
 * 
 * SHOW:
 * - Check ownership: atto appartiene all'ente
 * - Or: check collection admin (multi-user PA)
 * - 403 se non autorizzato
 * 
 * ============================================================================
 * 
 * @package App\Http\Controllers\PaActs
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - PA Acts Tokenization)
 * @date 2025-10-04
 * @purpose CRUD views controller for PA administrative acts
 * 
 * @architecture Controller Layer (Views rendering)
 * @dependencies PaActService, UltraLogManager, ErrorManager
 * @middleware auth, role:pa_entity
 * @views pa/acts/index.blade.php, pa/acts/show.blade.php
 */
class PaActController extends Controller {
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;
    protected PaActService $paActService;

    /**
     * Constructor - Dependency Injection
     * 
     * @param UltraLogManager $logger
     * @param ErrorManagerInterface $errorManager
     * @param PaActService $paActService
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        PaActService $paActService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->paActService = $paActService;

        // Middleware applicato in routes/web.php
        // $this->middleware(['auth', 'role:pa_entity']);
    }

    /**
     * Display list of PA acts
     * 
     * @param Request $request
     * @return View
     * 
     * FILTERS:
     * - search: Protocol number or title search
     * - doc_type: Filter by document type
     * - date_from: Filter by protocol date >= date
     * - date_to: Filter by protocol date <= date
     * - status: Filter by anchoring status (anchored, pending)
     * 
     * EXAMPLE URL:
     * /pa/acts?search=12345&doc_type=delibera&date_from=2025-01-01&status=anchored
     */
    public function index(Request $request): View {
        try {
            $user = auth()->user();

            $this->logger->info('[PaActController] Loading acts index', [
                'user_id' => $user->id,
                'filters' => $request->only(['search', 'doc_type', 'date_from', 'date_to', 'status'])
            ]);

            // Base query: Solo atti dell'ente autenticato
            $query = Egi::with(['collection', 'user'])
                ->whereHas('collection', function ($q) use ($user) {
                    $q->where('creator_id', $user->id);
                })
                ->whereNotNull('metadata->protocol_number'); // Solo atti PA

            // Filter: Search (protocol number or title)
            if ($search = $request->input('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('metadata->protocol_number', 'LIKE', "%{$search}%")
                        ->orWhere('title', 'LIKE', "%{$search}%");
                });
            }

            // Filter: Doc type
            if ($docType = $request->input('doc_type')) {
                $query->where('metadata->doc_type', $docType);
            }

            // Filter: Date range
            if ($dateFrom = $request->input('date_from')) {
                $query->whereDate('metadata->protocol_date', '>=', $dateFrom);
            }

            if ($dateTo = $request->input('date_to')) {
                $query->whereDate('metadata->protocol_date', '<=', $dateTo);
            }

            // Filter: Anchoring status
            if ($status = $request->input('status')) {
                if ($status === 'anchored') {
                    $query->where('metadata->anchored', true);
                } elseif ($status === 'pending') {
                    $query->where(function ($q) {
                        $q->whereNull('metadata->anchored')
                            ->orWhere('metadata->anchored', false);
                    });
                }
            }

            // Sort: Protocol date desc (default)
            $query->orderByRaw("JSON_EXTRACT(metadata, '$.protocol_date') DESC");

            // Paginate
            $acts = $query->paginate(15)->withQueryString();

            // Stats
            $stats = [
                'total' => Egi::whereHas('collection', fn($q) => $q->where('creator_id', $user->id))
                    ->whereNotNull('metadata->protocol_number')
                    ->count(),
                'anchored' => Egi::whereHas('collection', fn($q) => $q->where('creator_id', $user->id))
                    ->where('metadata->anchored', true)
                    ->count(),
                'pending' => Egi::whereHas('collection', fn($q) => $q->where('creator_id', $user->id))
                    ->whereNotNull('metadata->protocol_number')
                    ->where(function ($q) {
                        $q->whereNull('metadata->anchored')
                            ->orWhere('metadata->anchored', false);
                    })
                    ->count()
            ];

            return view('pa.acts.index', [
                'acts' => $acts,
                'filters' => $request->only(['search', 'doc_type', 'date_from', 'date_to', 'status']),
                'docTypes' => config('AllowedFileType.pa_documents.document_types'),
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            $this->logger->error('[PaActController] Error loading acts index', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            // Empty paginator fallback
            $emptyPaginator = new \Illuminate\Pagination\LengthAwarePaginator(
                collect(),
                0,
                15,
                1,
                ['path' => request()->url(), 'query' => request()->query()]
            );

            return view('pa.acts.index', [
                'acts' => $emptyPaginator,
                'filters' => [],
                'docTypes' => [],
                'stats' => ['total' => 0, 'anchored' => 0, 'pending' => 0],
                'error' => __('pa_acts.errors.load_failed')
            ]);
        }
    }

    /**
     * Display PA act detail
     * 
     * @param Egi $egi
     * @return View|RedirectResponse
     * 
     * AUTHORIZATION:
     * - Check atto appartiene all'ente autenticato
     * - Or check collection admin (multi-user PA)
     * 
     * DISPLAY:
     * - Metadata completo PA
     * - Firma digitale info
     * - Blockchain data (TXID, Merkle)
     * - QR code + verification URL
     */
    public function show(Egi $egi): View|RedirectResponse {
        try {
            $user = auth()->user();

            $this->logger->info('[PaActController] Loading act detail', [
                'user_id' => $user->id,
                'egi_id' => $egi->id
            ]);

            // Authorization check
            if (!$this->canViewAct($user, $egi)) {
                $this->logger->warning('[PaActController] Unauthorized access attempt', [
                    'user_id' => $user->id,
                    'egi_id' => $egi->id
                ]);

                abort(403, __('pa_acts.errors.unauthorized'));
            }

            // Load relationships
            $egi->load(['collection', 'user']);

            // Extract metadata
            $metadata = [
                'protocol_number' => $egi->metadata['protocol_number'] ?? null,
                'protocol_date' => $egi->metadata['protocol_date'] ?? null,
                'doc_type' => $egi->metadata['doc_type'] ?? null,
                'doc_hash' => $egi->metadata['doc_hash'] ?? null,
                'signature_validation' => $egi->metadata['signature_validation'] ?? [],
                'anchor_txid' => $egi->metadata['anchor_txid'] ?? null,
                'anchor_root' => $egi->metadata['anchor_root'] ?? null,
                'merkle_proof' => $egi->metadata['merkle_proof'] ?? [],
                'public_code' => $egi->metadata['public_code'] ?? null,
                'anchored' => $egi->metadata['anchored'] ?? false,
                'anchored_at' => $egi->metadata['anchored_at'] ?? null
            ];

            // Verification URL
            $verificationUrl = $metadata['public_code']
                ? route('verify.act', $metadata['public_code'])
                : null;

            // QR code URL
            $qrCodeUrl = $egi->metadata['qr_code_path'] ?? null;

            return view('pa.acts.show', [
                'act' => $egi,
                'metadata' => $metadata,
                'verification_url' => $verificationUrl,
                'qr_code_url' => $qrCodeUrl,
                'doc_type_label' => $this->getDocTypeLabel($metadata['doc_type'])
            ]);
        } catch (\Exception $e) {
            $this->logger->error('[PaActController] Error loading act detail', [
                'user_id' => auth()->id(),
                'egi_id' => $egi->id ?? null,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('pa.acts.index')
                ->withErrors(['error' => __('pa_acts.errors.load_detail_failed')]);
        }
    }

    /**
     * Check if user can view PA act
     * 
     * @param User $user
     * @param Egi $egi
     * @return bool
     * 
     * AUTHORIZATION:
     * - User owns collection (creator_id match)
     * - Or user is collection admin (pivot role = admin)
     */
    protected function canViewAct($user, Egi $egi): bool {
        // Check collection ownership
        if ($egi->collection && $egi->collection->creator_id === $user->id) {
            return true;
        }

        // Check collection admin role (multi-user PA)
        if ($egi->collection) {
            $pivotRole = $egi->collection->users()
                ->wherePivot('user_id', $user->id)
                ->first()
                ?->pivot
                ->role ?? null;

            if ($pivotRole === 'admin') {
                return true;
            }
        }

        return false;
    }

    /**
     * Get document type label (localized)
     * 
     * @param string|null $docType
     * @return string
     */
    protected function getDocTypeLabel(?string $docType): string {
        if (!$docType) {
            return __('pa_acts.doc_types.unknown');
        }

        return __("pa_acts.doc_types.{$docType}.label");
    }
}
