<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\ErrorManager\ErrorManager;
use App\Services\UltraLogManager\UltraLogManager;
use App\Services\Auth\AuthService;
use App\Services\AuditLog\AuditLogService;
use App\Services\Coa\CoaAddendumService;
use App\Services\Coa\CoaIssueService;
use App\Services\Coa\AnnexService;
use App\Models\Coa;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/**
 * Controller per la gestione degli addendum e policy versionati del sistema CoA Pro
 *
 * Gestisce:
 * - Addendum versionati (versioning incrementale 1.0, 1.1, 2.0)
 * - Policy di rarità e valutazione
 * - Revisioni e cronologia
 * - Pubblicazione e attivazione
 *
 * @author FlorenceEGI Team
 * @version 2.0.0 Pro
 */
class CoaAddendumController extends Controller {
    protected ErrorManager $errorManager;
    protected UltraLogManager $logger;
    protected AuthService $authService;
    protected AuditLogService $auditService;
    protected CoaAddendumService $addendumService;
    protected CoaIssueService $issueService;
    protected AnnexService $annexService;

    public function __construct(
        ErrorManager $errorManager,
        UltraLogManager $logger,
        AuthService $authService,
        AuditLogService $auditService,
        CoaAddendumService $addendumService,
        CoaIssueService $issueService,
        AnnexService $annexService
    ) {
        $this->errorManager = $errorManager;
        $this->logger = $logger;
        $this->authService = $authService;
        $this->auditService = $auditService;
        $this->addendumService = $addendumService;
        $this->issueService = $issueService;
        $this->annexService = $annexService;

        // Middleware per autenticazione Pro
        $this->middleware(['auth:api', 'verified', 'role:admin|expert']);

        // Rate limiting per operazioni critiche
        $this->middleware('throttle:addendum_create,5,1')->only(['store', 'publish']);
        $this->middleware('throttle:addendum_update,10,1')->only(['update', 'revise']);
    }

    /**
     * Lista addendum con filtri avanzati e paginazione
     */
    public function index(Request $request): JsonResponse {
        try {
            $validator = Validator::make($request->all(), [
                'coa_id' => 'sometimes|exists:coas,id',
                'type' => 'sometimes|in:policy,evaluation,technical,legal',
                'status' => 'sometimes|in:draft,published,archived,superseded',
                'version_type' => 'sometimes|in:major,minor,patch',
                'created_after' => 'sometimes|date',
                'created_before' => 'sometimes|date',
                'per_page' => 'sometimes|integer|min:1|max:100',
                'page' => 'sometimes|integer|min:1'
            ]);

            if ($validator->fails()) {
                return $this->errorManager->createErrorResponse(
                    'VALIDATION_ERROR',
                    'Parametri di ricerca non validi',
                    $validator->errors()->toArray(),
                    400
                );
            }

            $this->logger->info('CoaAddendum index request', [
                'user_id' => Auth::id(),
                'filters' => $request->all()
            ]);

            $addendums = $this->addendumService->getAddendumsList(
                $request->all(),
                $request->get('per_page', 20)
            );

            // Audit log per accesso lista
            $this->auditService->log([
                'user_id' => Auth::id(),
                'action' => 'addendum_list_accessed',
                'resource_type' => 'coa_addendum',
                'description' => 'Lista addendum consultata',
                'metadata' => [
                    'filters_applied' => $request->all(),
                    'results_count' => $addendums->total()
                ]
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Lista addendum recuperata',
                'data' => [
                    'addendums' => $addendums->items(),
                    'pagination' => [
                        'current_page' => $addendums->currentPage(),
                        'last_page' => $addendums->lastPage(),
                        'per_page' => $addendums->perPage(),
                        'total' => $addendums->total()
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Errore recupero lista addendum', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'request_data' => $request->all()
            ]);

            return $this->errorManager->createErrorResponse(
                'ADDENDUM_LIST_ERROR',
                'Errore durante il recupero della lista addendum',
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * Mostra dettagli di un addendum specifico
     */
    public function show(string $id): JsonResponse {
        try {
            $addendum = $this->addendumService->getAddendumDetails($id);

            if (!$addendum) {
                return $this->errorManager->createErrorResponse(
                    'ADDENDUM_NOT_FOUND',
                    'Addendum non trovato',
                    ['addendum_id' => $id],
                    404
                );
            }

            $this->logger->info('Addendum detail accessed', [
                'addendum_id' => $id,
                'user_id' => Auth::id()
            ]);

            // Audit log per visualizzazione dettagli
            $this->auditService->log([
                'user_id' => Auth::id(),
                'action' => 'addendum_viewed',
                'resource_type' => 'coa_addendum',
                'resource_id' => $id,
                'description' => 'Dettagli addendum visualizzati',
                'metadata' => [
                    'addendum_type' => $addendum['type'],
                    'addendum_version' => $addendum['version'],
                    'addendum_status' => $addendum['status']
                ]
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Dettagli addendum recuperati',
                'data' => $addendum
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Errore recupero addendum', [
                'addendum_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return $this->errorManager->createErrorResponse(
                'ADDENDUM_DETAIL_ERROR',
                'Errore durante il recupero dell\'addendum',
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * Crea un nuovo addendum
     */
    public function store(Request $request): JsonResponse {
        try {
            $validator = Validator::make($request->all(), [
                'coa_id' => 'required|exists:coas,id',
                'type' => 'required|in:policy,evaluation,technical,legal',
                'title' => 'required|string|max:255',
                'content' => 'required|array',
                'content.text' => 'required|string',
                'content.data' => 'sometimes|array',
                'version_notes' => 'sometimes|string|max:1000',
                'effective_date' => 'sometimes|date|after:today',
                'auto_publish' => 'sometimes|boolean'
            ]);

            if ($validator->fails()) {
                return $this->errorManager->createErrorResponse(
                    'VALIDATION_ERROR',
                    'Dati addendum non validi',
                    $validator->errors()->toArray(),
                    400
                );
            }

            // Verifica autorizzazioni CoA
            $coa = Coa::find($request->coa_id);
            if (!$this->authService->canManageCoaAddendum($coa)) {
                return $this->errorManager->createErrorResponse(
                    'INSUFFICIENT_PERMISSIONS',
                    'Permessi insufficienti per creare addendum su questo CoA',
                    ['coa_id' => $request->coa_id],
                    403
                );
            }

            DB::beginTransaction();

            $this->logger->info('Creazione addendum iniziata', [
                'coa_id' => $request->coa_id,
                'type' => $request->type,
                'user_id' => Auth::id()
            ]);

            $addendum = $this->addendumService->createAddendum([
                'coa_id' => $request->coa_id,
                'type' => $request->type,
                'title' => $request->title,
                'content' => $request->content,
                'version_notes' => $request->version_notes,
                'effective_date' => $request->effective_date,
                'created_by' => Auth::id()
            ]);

            // Auto-pubblicazione se richiesta
            if ($request->auto_publish) {
                $addendum = $this->addendumService->publishAddendum($addendum['id']);
            }

            // Audit log per creazione
            $this->auditService->log([
                'user_id' => Auth::id(),
                'action' => 'addendum_created',
                'resource_type' => 'coa_addendum',
                'resource_id' => $addendum['id'],
                'description' => 'Nuovo addendum creato',
                'metadata' => [
                    'coa_id' => $request->coa_id,
                    'addendum_type' => $request->type,
                    'auto_published' => $request->auto_publish ?? false,
                    'version' => $addendum['version']
                ]
            ]);

            DB::commit();

            $this->logger->info('Addendum creato con successo', [
                'addendum_id' => $addendum['id'],
                'version' => $addendum['version'],
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Addendum creato con successo',
                'data' => $addendum
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            $this->logger->error('Errore creazione addendum', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'request_data' => $request->all()
            ]);

            return $this->errorManager->createErrorResponse(
                'ADDENDUM_CREATION_ERROR',
                'Errore durante la creazione dell\'addendum',
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * Aggiorna un addendum esistente (solo se in bozza)
     */
    public function update(Request $request, string $id): JsonResponse {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'sometimes|string|max:255',
                'content' => 'sometimes|array',
                'content.text' => 'sometimes|string',
                'content.data' => 'sometimes|array',
                'version_notes' => 'sometimes|string|max:1000',
                'effective_date' => 'sometimes|date|after:today'
            ]);

            if ($validator->fails()) {
                return $this->errorManager->createErrorResponse(
                    'VALIDATION_ERROR',
                    'Dati aggiornamento non validi',
                    $validator->errors()->toArray(),
                    400
                );
            }

            $addendum = $this->addendumService->getAddendumDetails($id);
            if (!$addendum) {
                return $this->errorManager->createErrorResponse(
                    'ADDENDUM_NOT_FOUND',
                    'Addendum non trovato',
                    ['addendum_id' => $id],
                    404
                );
            }

            // Verifica che sia modificabile
            if ($addendum['status'] !== 'draft') {
                return $this->errorManager->createErrorResponse(
                    'ADDENDUM_NOT_EDITABLE',
                    'Solo gli addendum in bozza possono essere modificati',
                    ['current_status' => $addendum['status']],
                    400
                );
            }

            // Verifica autorizzazioni
            if (!$this->authService->canEditAddendum($addendum)) {
                return $this->errorManager->createErrorResponse(
                    'INSUFFICIENT_PERMISSIONS',
                    'Permessi insufficienti per modificare questo addendum',
                    ['addendum_id' => $id],
                    403
                );
            }

            DB::beginTransaction();

            $this->logger->info('Aggiornamento addendum iniziato', [
                'addendum_id' => $id,
                'user_id' => Auth::id()
            ]);

            $updatedAddendum = $this->addendumService->updateAddendum($id, array_merge(
                $request->all(),
                ['updated_by' => Auth::id()]
            ));

            // Audit log per aggiornamento
            $this->auditService->log([
                'user_id' => Auth::id(),
                'action' => 'addendum_updated',
                'resource_type' => 'coa_addendum',
                'resource_id' => $id,
                'description' => 'Addendum aggiornato',
                'metadata' => [
                    'updated_fields' => array_keys($request->all()),
                    'previous_version' => $addendum['version'],
                    'new_version' => $updatedAddendum['version']
                ]
            ]);

            DB::commit();

            $this->logger->info('Addendum aggiornato con successo', [
                'addendum_id' => $id,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Addendum aggiornato con successo',
                'data' => $updatedAddendum
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            $this->logger->error('Errore aggiornamento addendum', [
                'addendum_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return $this->errorManager->createErrorResponse(
                'ADDENDUM_UPDATE_ERROR',
                'Errore durante l\'aggiornamento dell\'addendum',
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * Crea una nuova revisione di un addendum pubblicato
     */
    public function revise(Request $request, string $id): JsonResponse {
        try {
            $validator = Validator::make($request->all(), [
                'revision_type' => 'required|in:major,minor,patch',
                'title' => 'sometimes|string|max:255',
                'content' => 'required|array',
                'content.text' => 'required|string',
                'content.data' => 'sometimes|array',
                'version_notes' => 'required|string|max:1000',
                'effective_date' => 'sometimes|date|after:today'
            ]);

            if ($validator->fails()) {
                return $this->errorManager->createErrorResponse(
                    'VALIDATION_ERROR',
                    'Dati revisione non validi',
                    $validator->errors()->toArray(),
                    400
                );
            }

            $originalAddendum = $this->addendumService->getAddendumDetails($id);
            if (!$originalAddendum) {
                return $this->errorManager->createErrorResponse(
                    'ADDENDUM_NOT_FOUND',
                    'Addendum originale non trovato',
                    ['addendum_id' => $id],
                    404
                );
            }

            // Verifica che sia pubblicato
            if ($originalAddendum['status'] !== 'published') {
                return $this->errorManager->createErrorResponse(
                    'ADDENDUM_NOT_REVISABLE',
                    'Solo gli addendum pubblicati possono essere revisionati',
                    ['current_status' => $originalAddendum['status']],
                    400
                );
            }

            // Verifica autorizzazioni
            if (!$this->authService->canReviseAddendum($originalAddendum)) {
                return $this->errorManager->createErrorResponse(
                    'INSUFFICIENT_PERMISSIONS',
                    'Permessi insufficienti per revisionare questo addendum',
                    ['addendum_id' => $id],
                    403
                );
            }

            DB::beginTransaction();

            $this->logger->info('Revisione addendum iniziata', [
                'original_addendum_id' => $id,
                'revision_type' => $request->revision_type,
                'user_id' => Auth::id()
            ]);

            $revision = $this->addendumService->createRevision($id, [
                'revision_type' => $request->revision_type,
                'title' => $request->title ?? $originalAddendum['title'],
                'content' => $request->content,
                'version_notes' => $request->version_notes,
                'effective_date' => $request->effective_date,
                'created_by' => Auth::id()
            ]);

            // Audit log per revisione
            $this->auditService->log([
                'user_id' => Auth::id(),
                'action' => 'addendum_revised',
                'resource_type' => 'coa_addendum',
                'resource_id' => $revision['id'],
                'description' => 'Nuova revisione addendum creata',
                'metadata' => [
                    'original_addendum_id' => $id,
                    'original_version' => $originalAddendum['version'],
                    'new_version' => $revision['version'],
                    'revision_type' => $request->revision_type
                ]
            ]);

            DB::commit();

            $this->logger->info('Revisione addendum creata con successo', [
                'revision_id' => $revision['id'],
                'new_version' => $revision['version'],
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Revisione addendum creata con successo',
                'data' => $revision
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            $this->logger->error('Errore creazione revisione addendum', [
                'original_addendum_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return $this->errorManager->createErrorResponse(
                'ADDENDUM_REVISION_ERROR',
                'Errore durante la creazione della revisione',
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * Pubblica un addendum in bozza
     */
    public function publish(string $id): JsonResponse {
        try {
            $addendum = $this->addendumService->getAddendumDetails($id);
            if (!$addendum) {
                return $this->errorManager->createErrorResponse(
                    'ADDENDUM_NOT_FOUND',
                    'Addendum non trovato',
                    ['addendum_id' => $id],
                    404
                );
            }

            // Verifica che sia in bozza
            if ($addendum['status'] !== 'draft') {
                return $this->errorManager->createErrorResponse(
                    'ADDENDUM_NOT_PUBLISHABLE',
                    'Solo gli addendum in bozza possono essere pubblicati',
                    ['current_status' => $addendum['status']],
                    400
                );
            }

            // Verifica autorizzazioni
            if (!$this->authService->canPublishAddendum($addendum)) {
                return $this->errorManager->createErrorResponse(
                    'INSUFFICIENT_PERMISSIONS',
                    'Permessi insufficienti per pubblicare questo addendum',
                    ['addendum_id' => $id],
                    403
                );
            }

            DB::beginTransaction();

            $this->logger->info('Pubblicazione addendum iniziata', [
                'addendum_id' => $id,
                'user_id' => Auth::id()
            ]);

            $publishedAddendum = $this->addendumService->publishAddendum($id);

            // Audit log per pubblicazione
            $this->auditService->log([
                'user_id' => Auth::id(),
                'action' => 'addendum_published',
                'resource_type' => 'coa_addendum',
                'resource_id' => $id,
                'description' => 'Addendum pubblicato',
                'metadata' => [
                    'version' => $publishedAddendum['version'],
                    'effective_date' => $publishedAddendum['effective_date'],
                    'superseded_addenda' => $publishedAddendum['superseded_count'] ?? 0
                ]
            ]);

            DB::commit();

            $this->logger->info('Addendum pubblicato con successo', [
                'addendum_id' => $id,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Addendum pubblicato con successo',
                'data' => $publishedAddendum
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            $this->logger->error('Errore pubblicazione addendum', [
                'addendum_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return $this->errorManager->createErrorResponse(
                'ADDENDUM_PUBLISH_ERROR',
                'Errore durante la pubblicazione dell\'addendum',
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * Recupera la cronologia delle versioni di un addendum
     */
    public function history(string $id): JsonResponse {
        try {
            $history = $this->addendumService->getAddendumHistory($id);

            if (empty($history)) {
                return $this->errorManager->createErrorResponse(
                    'ADDENDUM_NOT_FOUND',
                    'Addendum non trovato o senza cronologia',
                    ['addendum_id' => $id],
                    404
                );
            }

            $this->logger->info('Cronologia addendum consultata', [
                'addendum_id' => $id,
                'user_id' => Auth::id()
            ]);

            // Audit log per consultazione cronologia
            $this->auditService->log([
                'user_id' => Auth::id(),
                'action' => 'addendum_history_viewed',
                'resource_type' => 'coa_addendum',
                'resource_id' => $id,
                'description' => 'Cronologia addendum consultata',
                'metadata' => [
                    'versions_count' => count($history)
                ]
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Cronologia addendum recuperata',
                'data' => [
                    'addendum_id' => $id,
                    'history' => $history
                ]
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Errore recupero cronologia addendum', [
                'addendum_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return $this->errorManager->createErrorResponse(
                'ADDENDUM_HISTORY_ERROR',
                'Errore durante il recupero della cronologia',
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * Archivia un addendum (soft delete)
     */
    public function archive(string $id): JsonResponse {
        try {
            $addendum = $this->addendumService->getAddendumDetails($id);
            if (!$addendum) {
                return $this->errorManager->createErrorResponse(
                    'ADDENDUM_NOT_FOUND',
                    'Addendum non trovato',
                    ['addendum_id' => $id],
                    404
                );
            }

            // Verifica autorizzazioni
            if (!$this->authService->canArchiveAddendum($addendum)) {
                return $this->errorManager->createErrorResponse(
                    'INSUFFICIENT_PERMISSIONS',
                    'Permessi insufficienti per archiviare questo addendum',
                    ['addendum_id' => $id],
                    403
                );
            }

            DB::beginTransaction();

            $this->logger->info('Archiviazione addendum iniziata', [
                'addendum_id' => $id,
                'user_id' => Auth::id()
            ]);

            $archivedAddendum = $this->addendumService->archiveAddendum($id);

            // Audit log per archiviazione
            $this->auditService->log([
                'user_id' => Auth::id(),
                'action' => 'addendum_archived',
                'resource_type' => 'coa_addendum',
                'resource_id' => $id,
                'description' => 'Addendum archiviato',
                'metadata' => [
                    'version' => $addendum['version'],
                    'previous_status' => $addendum['status']
                ]
            ]);

            DB::commit();

            $this->logger->info('Addendum archiviato con successo', [
                'addendum_id' => $id,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Addendum archiviato con successo',
                'data' => $archivedAddendum
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            $this->logger->error('Errore archiviazione addendum', [
                'addendum_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return $this->errorManager->createErrorResponse(
                'ADDENDUM_ARCHIVE_ERROR',
                'Errore durante l\'archiviazione dell\'addendum',
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * Recupera le policy di rarità attive
     */
    public function rarityPolicies(): JsonResponse {
        try {
            $policies = $this->addendumService->getActiveRarityPolicies();

            $this->logger->info('Policy di rarità consultate', [
                'user_id' => Auth::id()
            ]);

            // Audit log per consultazione policy
            $this->auditService->log([
                'user_id' => Auth::id(),
                'action' => 'rarity_policies_viewed',
                'resource_type' => 'coa_addendum',
                'description' => 'Policy di rarità consultate',
                'metadata' => [
                    'active_policies_count' => count($policies)
                ]
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Policy di rarità recuperate',
                'data' => [
                    'policies' => $policies,
                    'last_updated' => $this->addendumService->getLastPolicyUpdate()
                ]
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Errore recupero policy di rarità', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return $this->errorManager->createErrorResponse(
                'RARITY_POLICIES_ERROR',
                'Errore durante il recupero delle policy di rarità',
                ['error' => $e->getMessage()],
                500
            );
        }
    }
}
