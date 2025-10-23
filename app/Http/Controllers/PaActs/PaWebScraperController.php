<?php

namespace App\Http\Controllers\PaActs;

use App\Http\Controllers\Controller;
use App\Models\PaWebScraper;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Services\PaActs\PaWebScraperService;

/**
 * PA Web Scraper Controller
 *
 * ============================================================================
 * CONTESTO - GESTIONE WEB SCRAPER PER ATTI PA
 * ============================================================================
 *
 * Controller per configurare e gestire scraper web che recuperano atti PA
 * da fonti esterne (es: albo pretorio online, amministrazione trasparente)
 *
 * FUNZIONALITÀ:
 * - Configurazione scraper (URL, headers, payload, mapping)
 * - Test connessione API
 * - Esecuzione manuale scraping
 * - Visualizzazione log e statistiche
 * - Scheduling automatico
 *
 * ============================================================================
 * ROUTES
 * ============================================================================
 *
 * GET /pa/scrapers - Lista scraper configurati
 * GET /pa/scrapers/create - Form nuovo scraper
 * POST /pa/scrapers - Salva nuovo scraper
 * GET /pa/scrapers/{scraper} - Dettaglio scraper
 * GET /pa/scrapers/{scraper}/edit - Form modifica
 * PUT /pa/scrapers/{scraper} - Aggiorna scraper
 * DELETE /pa/scrapers/{scraper} - Elimina scraper
 * POST /pa/scrapers/{scraper}/test - Test connessione
 * POST /pa/scrapers/{scraper}/run - Esegui scraping manuale
 * POST /pa/scrapers/{scraper}/toggle - Attiva/Disattiva
 *
 * ============================================================================
 */
class PaWebScraperController extends Controller
{
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;
    protected PaWebScraperService $scraperService;

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        PaWebScraperService $scraperService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->scraperService = $scraperService;
    }

    /**
     * Display list of web scrapers
     */
    public function index(Request $request): View
    {
        try {
            $user = Auth::user();

            $this->logger->info('[PaWebScraperController] Loading scrapers index', [
                'user_id' => $user->id
            ]);

            // Query scrapers dell'utente corrente
            $query = PaWebScraper::where('user_id', $user->id)
                ->with('createdBy')
                ->orderBy('created_at', 'desc');

            // Filter by status
            if ($status = $request->input('status')) {
                $query->where('status', $status);
            }

            // Filter by active
            if ($request->has('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }

            $scrapers = $query->paginate(10)->withQueryString();

            // Stats
            $stats = [
                'total' => PaWebScraper::where('user_id', $user->id)->count(),
                'active' => PaWebScraper::where('user_id', $user->id)->where('is_active', true)->count(),
                'total_items' => PaWebScraper::where('user_id', $user->id)->sum('total_items_scraped'),
            ];

            return view('pa.scrapers.index', compact('scrapers', 'stats'));
        } catch (\Exception $e) {
            $this->logger->error('[PaWebScraperController] Error loading scrapers', [
                'error' => $e->getMessage()
            ]);

            return view('pa.scrapers.index', [
                'scrapers' => collect(),
                'stats' => ['total' => 0, 'active' => 0, 'total_items' => 0],
                'error' => 'Errore nel caricamento degli scraper'
            ]);
        }
    }

    /**
     * Show form for creating new scraper
     */
    public function create(): View
    {
        $this->logger->info('[PaWebScraperController] Show create form');

        // Template pre-configurati (Firenze, ecc.)
        $templates = $this->getScraperTemplates();

        return view('pa.scrapers.create', compact('templates'));
    }

    /**
     * Store new scraper
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            // Check if creating from template
            if ($request->has('template')) {
                $templateKey = $request->input('template');
                $templates = $this->getScraperTemplates();

                if (!isset($templates[$templateKey])) {
                    return back()->with('error', 'Template non trovato');
                }

                $templateData = $templates[$templateKey];
                
                // Add user and status
                $templateData['user_id'] = Auth::user()->id;
                $templateData['created_by_user_id'] = Auth::id();
                $templateData['status'] = 'draft';

                // Convert arrays to JSON for storage
                if (isset($templateData['headers']) && is_array($templateData['headers'])) {
                    $templateData['headers'] = $templateData['headers'];
                }
                if (isset($templateData['payload_template']) && is_array($templateData['payload_template'])) {
                    $templateData['payload_template'] = $templateData['payload_template'];
                }
                if (isset($templateData['pii_fields_to_exclude']) && is_array($templateData['pii_fields_to_exclude'])) {
                    $templateData['pii_fields_to_exclude'] = $templateData['pii_fields_to_exclude'];
                }

                $scraper = PaWebScraper::create($templateData);

                $this->logger->info('[PaWebScraperController] Scraper created from template', [
                    'scraper_id' => $scraper->id,
                    'template' => $templateKey,
                    'name' => $scraper->name
                ]);

                return redirect()
                    ->route('pa.scrapers.show', $scraper)
                    ->with('success', 'Scraper creato con successo dal template');
            }

            // Manual creation - validate all fields
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|in:api,html,hybrid',
                'source_entity' => 'required|string|max:255',
                'description' => 'nullable|string',
                'base_url' => 'required|url',
                'api_endpoint' => 'nullable|string',
                'method' => 'required|in:GET,POST',
                'headers' => 'nullable|json',
                'payload_template' => 'nullable|json',
                'query_params' => 'nullable|json',
                'data_mapping' => 'nullable|json',
                'pagination_type' => 'nullable|in:none,offset,page,cursor',
                'pagination_config' => 'nullable|json',
                'schedule_frequency' => 'nullable|in:manual,hourly,daily,weekly,monthly',
                'legal_basis' => 'required|string',
                'data_retention_policy' => 'nullable|string',
                'is_active' => 'nullable|boolean',
            ]);

            $validated['user_id'] = Auth::user()->id;
            $validated['created_by_user_id'] = Auth::id();
            $validated['status'] = 'draft';
            $validated['is_active'] = $request->boolean('is_active', false);

            $scraper = PaWebScraper::create($validated);

            $this->logger->info('[PaWebScraperController] Scraper created manually', [
                'scraper_id' => $scraper->id,
                'name' => $scraper->name
            ]);

            return redirect()
                ->route('pa.scrapers.show', $scraper)
                ->with('success', 'Scraper creato con successo');
        } catch (\Exception $e) {
            $this->logger->error('[PaWebScraperController] Error creating scraper', [
                'error' => $e->getMessage()
            ]);

            return back()
                ->withInput()
                ->with('error', 'Errore nella creazione dello scraper: ' . $e->getMessage());
        }
    }

    /**
     * Display scraper details
     */
    public function show(PaWebScraper $scraper): View|RedirectResponse
    {
        try {
            // Authorization check
            if ($scraper->business_id !== Auth::user()->business_id) {
                abort(403, 'Non autorizzato ad accedere a questo scraper');
            }

            $this->logger->info('[PaWebScraperController] Show scraper details', [
                'scraper_id' => $scraper->id
            ]);

            return view('pa.scrapers.show', compact('scraper'));
        } catch (\Exception $e) {
            $this->logger->error('[PaWebScraperController] Error showing scraper', [
                'error' => $e->getMessage()
            ]);

            return redirect()
                ->route('pa.scrapers.index')
                ->with('error', 'Errore nel caricamento del scraper');
        }
    }

    /**
     * Show form for editing scraper
     */
    public function edit(PaWebScraper $scraper): View|RedirectResponse
    {
        try {
            // Authorization
            if ($scraper->user_id !== Auth::id()) {
                abort(403);
            }

            $templates = $this->getScraperTemplates();

            return view('pa.scrapers.edit', compact('scraper', 'templates'));
        } catch (\Exception $e) {
            return redirect()
                ->route('pa.scrapers.index')
                ->with('error', 'Errore: ' . $e->getMessage());
        }
    }

    /**
     * Update scraper
     */
    public function update(Request $request, PaWebScraper $scraper): RedirectResponse
    {
        try {
            // Authorization
            if ($scraper->user_id !== Auth::id()) {
                abort(403);
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|in:api,html,hybrid',
                'source_entity' => 'required|string|max:255',
                'description' => 'nullable|string',
                'base_url' => 'required|url',
                'api_endpoint' => 'nullable|string',
                'method' => 'required|in:GET,POST',
                'headers' => 'nullable|json',
                'payload_template' => 'nullable|json',
                'query_params' => 'nullable|json',
                'data_mapping' => 'nullable|json',
                'pagination_type' => 'nullable|in:none,offset,page,cursor',
                'pagination_config' => 'nullable|json',
                'schedule_frequency' => 'nullable|in:manual,hourly,daily,weekly,monthly',
            ]);

            $scraper->update($validated);

            return redirect()
                ->route('pa.scrapers.show', $scraper)
                ->with('success', 'Scraper aggiornato con successo');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Errore: ' . $e->getMessage());
        }
    }

    /**
     * Delete scraper
     */
    public function destroy(PaWebScraper $scraper): RedirectResponse
    {
        try {
            // Authorization
            if ($scraper->user_id !== Auth::id()) {
                abort(403);
            }

            $scraper->delete();

            return redirect()
                ->route('pa.scrapers.index')
                ->with('success', 'Scraper eliminato con successo');
        } catch (\Exception $e) {
            return back()->with('error', 'Errore: ' . $e->getMessage());
        }
    }

    /**
     * Test scraper connection
     */
    public function test(PaWebScraper $scraper): RedirectResponse
    {
        try {
            // Authorization
            if ($scraper->user_id !== Auth::id()) {
                abort(403);
            }

            $result = $scraper->testConnection();

            if ($result['success']) {
                return back()->with('success', 'Connessione testata con successo! Status: ' . $result['status_code']);
            } else {
                return back()->with('error', 'Test connessione fallito: ' . $result['message']);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Errore nel test: ' . $e->getMessage());
        }
    }

    /**
     * Toggle scraper active status
     */
    public function toggle(PaWebScraper $scraper): RedirectResponse
    {
        try {
            // Authorization
            if ($scraper->user_id !== Auth::id()) {
                abort(403);
            }

            $scraper->update([
                'is_active' => !$scraper->is_active,
                'status' => $scraper->is_active ? 'paused' : 'active'
            ]);

            $message = $scraper->is_active ? 'Scraper attivato' : 'Scraper disattivato';

            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'Errore: ' . $e->getMessage());
        }
    }

    /**
     * Execute scraper manually
     */
    public function run(Request $request, PaWebScraper $scraper): RedirectResponse
    {
        try {
            // Authorization
            if ($scraper->user_id !== Auth::id()) {
                abort(403);
            }

            $this->logger->info('[PaWebScraperController] Manual scraper execution', [
                'scraper_id' => $scraper->id,
                'user_id' => Auth::id()
            ]);

            // Get options from request (year, filters, etc.)
            $options = $request->only(['year', 'month', 'tipo', 'limit']);

            // Execute scraper with GDPR compliance
            $result = $this->scraperService->execute($scraper, Auth::user(), $options);

            if ($result['success']) {
                $message = sprintf(
                    'Scraping completato! %d atti estratti in %s secondi',
                    $result['stats']['acts_count'],
                    $result['stats']['execution_time']
                );

                // Store results in session for display/import
                session(['scraper_results' => $result]);

                return redirect()
                    ->route('pa.scrapers.show', $scraper)
                    ->with('success', $message)
                    ->with('scraper_data', $result['acts']);
            } else {
                return back()->with('error', 'Scraping fallito: ' . $result['error']);
            }
        } catch (\Exception $e) {
            $this->logger->error('[PaWebScraperController] Scraper execution error', [
                'scraper_id' => $scraper->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Errore esecuzione scraper: ' . $e->getMessage());
        }
    }

    /**
     * Get scraper templates (Firenze, ecc.)
     */
    protected function getScraperTemplates(): array
    {
        return [
            'firenze_delibere' => [
                'name' => 'Delibere Comune di Firenze',
                'type' => 'api',
                'source_entity' => 'Comune di Firenze',
                'description' => 'Scraper per deliberazioni di giunta e consiglio dal portale trasparenza',
                'base_url' => 'https://accessoconcertificato.comune.fi.it',
                'api_endpoint' => '/trasparenza-atti-cat/searchAtti',
                'method' => 'POST',
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'payload_template' => [
                    'oggetto' => '',
                    'notLoadIniziale' => 'ok',
                    'numeroAdozione' => '',
                    'competenza' => 'DG',
                    'annoAdozione' => '{{year}}',
                    'tipiAtto' => ['DG', 'DC']
                ],
                'pagination_type' => 'none',
                // GDPR
                'data_source_type' => 'public',
                'legal_basis' => 'Art. 23 D.Lgs 33/2013 - Obblighi pubblicazione atti PA + Art. 32 D.Lgs 33/2013 - Trasparenza amministrativa',
                'data_retention_policy' => 'Conservazione permanente come da CAD Art. 22 - Documenti amministrativi informatici',
                'gdpr_compliant' => true,
                'pii_fields_to_exclude' => ['email', 'telefono', 'indirizzo', 'codice_fiscale'],
            ],
            'firenze_albo' => [
                'name' => 'Albo Pretorio Comune di Firenze',
                'type' => 'html',
                'source_entity' => 'Comune di Firenze',
                'description' => 'Scraper per atti dall\'albo pretorio online',
                'base_url' => 'https://accessoconcertificato.comune.fi.it',
                'api_endpoint' => '/AOL/Affissione/ComuneFi/Page',
                'method' => 'GET',
                'pagination_type' => 'page',
                'pagination_config' => [
                    'param_name' => 'page',
                    'start' => 1,
                    'max_pages' => 50
                ]
            ],
        ];
    }
}
