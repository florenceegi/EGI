<?php

namespace App\Http\Controllers;

use App\Helpers\FegiAuth;
use App\Models\Collection;
use App\Models\Epp;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Services\CollectionService;
use Illuminate\Validation\ValidationException;
use Throwable;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * Controller for managing collections display and interaction.
 *
 * Handles the listing, filtering, and viewing of EGI collections in the
 * marketplace. Implements Oracode principles for readable, maintainable code.
 *
 * --- Core Logic ---
 * 1. Retrieves collections based on filter criteria
 * 2. Sorts collections by various parameters
 * 3. Loads necessary relationships for efficient display
 * 4. Handles pagination for scalable collection browsing
 * 5. Tracks engagement metrics for collections
 * --- End Core Logic ---
 *
 * @package App\Http\Controllers
 * @author Your Name <your.email@example.com>
 * @version 1.0.0
 * @since 1.0.0
 */
class CollectionsController extends Controller {
    /**
     * Display a paginated listing of collections.
     *
     * Retrieves collections with filters applied based on query parameters.
     * Loads necessary relationships to minimize database queries.
     * Orders results based on user preference.
     *
     * @param Request $request The HTTP request with optional filter parameters
     * @return \Illuminate\View\View The view with paginated collection data
     */
    public function index(Request $request) {
        // Ottieni tutti gli EPP per il dropdown dei filtri
        $epps = Epp::select('id', 'name')->get();

        // Costruisci la query di base con le relazioni necessarie
        // Inizialmente selezioniamo solo le colonne della tabella collections
        $currentUserId = FegiAuth::id();

        $query = Collection::with([
            'creator',
            'epp',
            'egis' => function ($query) use ($currentUserId) {
                // Se l'utente non è autenticato, mostra solo EGI pubblicati
                if (!$currentUserId) {
                    $query->where('is_published', true);
                    return;
                }

                // Se l'utente è autenticato, costruisce la query condizionale
                $query->where(function ($q) use ($currentUserId) {
                    // Mostra tutti gli EGI pubblicati
                    $q->where('is_published', true)
                        // O tutti gli EGI delle collezioni di cui è creatore
                        ->orWhere(function ($subQuery) use ($currentUserId) {
                            $subQuery->whereHas('collection', function ($collectionQuery) use ($currentUserId) {
                                $collectionQuery->where('creator_id', $currentUserId);
                            });
                        });
                });
            }
        ])->select('collections.*'); // Seleziona tutte le colonne da 'collections' per evitare ambiguità iniziali

        // Nuovo filtro opzionale per creator_id
        if ($request->filled('creator')) {
            $query->where('creator_id', $request->creator);
        }

        // Filtro per stato / visibilità
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // Utente anonimo: solo pubblicate
            if (!$currentUserId) {
                $query->where('collections.is_published', true);
            } else {
                // Utente autenticato: includi anche non pubblicate se creator o con permesso create_collection
                // 1) Collezioni di cui è creator (query diretta)
                $creatorIds = Collection::where('creator_id', $currentUserId)->pluck('id');

                // 2) Collezioni collegate via pivot all'utente (candidate)
                $candidateIds = DB::table('collection_user')
                    ->where('user_id', $currentUserId)
                    ->pluck('collection_id');

                // 3) Carica le candidate e filtra in PHP usando userHasPermission (con users eager loaded)
                $eligibleIds = Collection::with(['users' => function ($r) use ($currentUserId) {
                    $r->where('users.id', $currentUserId);
                }])
                    ->whereIn('id', $candidateIds)
                    ->get()
                    ->filter(fn($c) => $c->userHasPermission($currentUserId, 'create_collection'))
                    ->pluck('id');

                // 4) Applica visibilità: pubblicate OR (creator o eligible per permesso)
                $query->where(function ($q) use ($creatorIds, $eligibleIds, $currentUserId) {
                    $q->where('collections.is_published', true)
                        ->orWhereIn('collections.id', $creatorIds)
                        ->orWhereIn('collections.id', $eligibleIds);
                });
            }
        }

        // Filtro per EPP
        if ($request->filled('epp')) {
            $query->where('epp_id', $request->epp);
        }

        // Nota: nessun filtro aggiuntivo qui; la visibilità è già gestita sopra con la condizione per utente/permessi


        // Ordinamento
        $sortBy = $request->input('sort', 'newest');

        switch ($sortBy) {
            case 'oldest':
                $query->oldest('created_at');
                break;
            case 'name':
                $query->orderBy('collection_name', 'asc');
                break;
            case 'popularity':
                // Per la popolarità, aggiungiamo il join e selezioniamo il conteggio dei like.
                // 🎯 Soluzione: Usiamo un subquery per calcolare i likes per ogni collection
                // Questo evita l'ambiguità del GROUP BY e permette una paginazione più pulita.
                $query->leftJoin(
                    DB::raw('(SELECT likeable_id, COUNT(id) as likes_count FROM likes WHERE likeable_type = \'App\\\\Models\\\\Collection\' GROUP BY likeable_id) as likes_aggregated'),
                    function ($join) {
                        $join->on('collections.id', '=', 'likes_aggregated.likeable_id');
                    }
                )
                    ->orderByDesc('likes_aggregated.likes_count')
                    ->select('collections.*'); // Assicurati di selezionare le colonne di collections
                break;
            case 'newest':
            default:
                $query->latest('created_at');
                break;
        }

        // Paginazione (12 elementi per pagina)
        // 🧪 Resilienza Progressiva: appends(request()->query()) mantiene i parametri del filtro nella paginazione
        $collections = $query->paginate(12)->appends($request->query());

        // Aggiungi attributi calcolati senza query extra
        foreach ($collections as $collection) {
            // Numero di EGI (usiamo EGI_number dal database, se disponibile)
            $collection->egi_count = $collection->EGI_number ?? $collection->egis->count();

            // Conteggi di likes e reservations (se le relazioni esistono)
            // Questi dovrebbero essere ricalcolati o recuperati dalla relazione eager loading,
            // non dal join della query principale che non è sempre presente.
            $collection->likes_count = Schema::hasTable('likes') ? $collection->likes()->count() : 0;
            $collection->reservations_count = Schema::hasTable('reservations') ? $collection->reservations()->count() : 0;

            // Verifica se l'utente autenticato ha messo "like"
            $collection->is_liked = auth()->check() && Schema::hasTable('likes')
                ? $collection->likes()->where('user_id', auth()->id())->exists()
                : false;
        }

        return view('collections.index', compact('collections', 'epps'));
    }

    /**
     * Display the specified collection.
     *
     * Shows detailed information about a single collection, including
     * its EGIs, creator information, and related metadata.
     *
     * @param Collection $collection The collection to display
     * @return \Illuminate\View\View The view with collection details
     */
    public function show($id) {
        // Determina l'ID dell'utente autenticato (strong o weak auth)
        $currentUserId = FegiAuth::id();

        // Prima carica la collection per controllare il creator_id
        $collection = Collection::findOrFail($id);
        $isCreator = $currentUserId && $currentUserId == $collection->creator_id;

        // Ricarica la collection con gli EGI filtrati in base ai permessi
        $collection = Collection::with([
            'creator',
            'epp',
            'egis' => function ($query) use ($isCreator) {
                if (!$isCreator) {
                    // Non è il creatore: mostra solo EGI pubblicati
                    $query->where('is_published', true);
                }
                // Se è il creatore: mostra tutti gli EGI (pubblicati e non)
            },
            'egis.user',
            'egis.owner',
            'egis.blockchain.buyer', // 🤝 Co-Creator data (buyer relationship)
            'egis.reservations' => function ($query) {
                $query->where('sub_status', 'highest')
                    ->where('status', 'active')
                    ->with('user');
            },
            'likes'
        ])
            ->withCount([
                'egis' => function ($query) use ($isCreator) {
                    if (!$isCreator) {
                        // Non è il creatore: conta solo EGI pubblicati
                        $query->where('is_published', true);
                    }
                    // Se è il creatore: conta tutti gli EGI
                },
                'likes',
                'reservations'
            ])
            ->findOrFail($id);

        // Verifica like per utente strong auth
        if (auth()->check()) {
            $collection->is_liked = $collection->likes()
                ->where('user_id', auth()->id())
                ->exists();
        }
        // Verifica like per utente weak auth
        elseif (session('connected_user_id')) {
            $collection->is_liked = $collection->likes()
                ->where('user_id', session('connected_user_id'))
                ->exists();
        } else {
            $collection->is_liked = false;
        }

        // Usa il conteggio ottimizzato invece del query aggiuntivo
        // $collection->likes_count è già disponibile tramite withCount

        // Se è una collezione EPP (tipo environmental), usa la vista semplificata
        if ($collection->type === 'environmental') {
            return view('collections.show-epp', compact('collection'));
        }

        return view('collections.show', compact('collection'));
    }

    /**
     * Track a view of a collection.
     *
     * Increments view count for analytics purposes.
     * Consider implementing rate limiting to prevent abuse.
     *
     * @param Collection $collection The collection being viewed
     * @return void
     */
    protected function trackView(Collection $collection) {
        // This is a placeholder for view tracking functionality
        // You would implement your actual tracking logic here

        // Example:
        // $collection->incrementViewCount();
        // or
        // ViewLog::create([
        //     'collection_id' => $collection->id,
        //     'user_id' => auth()->id(),
        //     'ip_address' => request()->ip()
        // ]);
    }


    /**
     * @Oracode OS1: Enhanced Collection Creation Endpoint
     * 🎯 Purpose: Create new collection via AJAX with robust validation and UEM error handling
     * 🧱 Core Logic: Validates input, uses CollectionService, handles both success and error scenarios
     * 🛡️ GDPR: Minimal data processing, user consent implied by authentication
     * 📥 Input: Request with collection_name (required, string, 2-100 chars)
     * 📤 Output: JSON response with collection data or standardized error
     * 🔄 Flow: Validate -> Create via Service -> Handle Response -> Return JSON
     *
     * @param Request $request HTTP request containing collection_name
     * @return JsonResponse Standardized JSON response for AJAX consumption
     *
     * @oracode-enhanced-validation Multi-layer input validation with meaningful errors
     * @oracode-ajax-optimized Designed for seamless frontend integration
     * @oracode-ux-feedback Rich feedback for superior user experience
     *
     * @since OS1-v1.0
     * @author Padmin D. Curtis (for Fabio Cherici)
     */
    public function create(Request $request): JsonResponse {
        try {
            // 🎯 OS1 Pillar 1: Explicit Intention - Log operation start
            $operationContext = [
                'operation' => 'collection_create_request',
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()->toISOString()
            ];

            app(UltraLogManager::class)->info('Collection creation initiated', $operationContext);

            // Enhanced Authentication Check with UEM
            $user = FegiAuth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error' => 'AUTHENTICATION_REQUIRED',
                    'message' => __('authentication.required_for_collection_creation'),
                    'redirect' => route('login')
                ], 401);
            }

            $operationContext['creator_id'] = $user->id;

            // 🎯 Enhanced Permission Check for Collection Creation
            if (!$user->can('create_collection')) {
                app(UltraLogManager::class)->warning('Collection creation permission denied', [
                    ...$operationContext,
                    'user_roles' => $user->roles->pluck('name')->toArray(),
                    'user_permissions' => $user->getAllPermissions()->pluck('name')->toArray()
                ]);

                return response()->json([
                    'success' => false,
                    'error' => 'PERMISSION_DENIED',
                    'message' => __('collection.creation_failed') . ' ' . __('errors.insufficient_permissions'),
                    'required_permission' => 'create_collection'
                ], 403);
            }

            // 🎯 OS1 Enhanced Validation with Semantic Coherence
            $validated = $request->validate(
                [
                    'collection_name' => [
                        'required',
                        'string',
                        'min:2',
                        'max:100',
                        'regex:/^[a-zA-Z0-9\s\-_\'\"À-ÿ]+$/u' // Supports international chars
                    ]
                ],
                [
                    'collection_name.required' => __('validation.collection_name_required'),
                    'collection_name.min' => __('validation.collection_name_min_length'),
                    'collection_name.max' => __('validation.collection_name_max_length'),
                    'collection_name.regex' => __('validation.collection_name_invalid_characters')
                ]
            );

            $collectionName = trim($validated['collection_name']);
            $operationContext['collection_name'] = $collectionName;

            // 🎯 OS1 Pillar 4: Virtuous Circularity - Check user collection limits
            $existingCollectionsCount = Collection::where('creator_id', $user->id)->count();
            $maxCollections = config('egi.max_collections_per_user', 10);

            if ($existingCollectionsCount >= $maxCollections) {
                return response()->json([
                    'success' => false,
                    'error' => 'COLLECTION_LIMIT_EXCEEDED',
                    'message' => __('collection.limit_exceeded', ['max' => $maxCollections]),
                    'current_count' => $existingCollectionsCount,
                    'max_allowed' => $maxCollections
                ], 422);
            }

            // 🎯 OS1 Service Integration with Enhanced Error Handling
            $collectionService = app(CollectionService::class);
            $result = $collectionService->createDefaultCollection(
                $user,
                false, // Non-default collection (user created)
                $collectionName
            );

            // Handle CollectionService response types
            if ($result instanceof JsonResponse) {
                // Service returned error - forward with enhanced context
                $errorData = $result->getData(true);
                return response()->json([
                    'success' => false,
                    'error' => $errorData['error'] ?? 'COLLECTION_SERVICE_ERROR',
                    'message' => $errorData['message'] ?? __('collection.creation_failed'),
                    'service_context' => $operationContext
                ], $result->getStatusCode());
            }

            // 🎯 OS1 Success Path - Collection Created Successfully
            $collection = $result; // It's a Collection model

            // 🎯 OS1 Pillar 5: Recursive Evolution - Success logging for optimization
            $successContext = array_merge($operationContext, [
                'collection_id' => $collection->id,
                'collection_position' => $collection->position,
                'success' => true,
                'creation_duration_ms' => (microtime(true) - ($_SERVER['REQUEST_TIME_FLOAT'] ?? 0)) * 1000
            ]);

            // Log success for analytics and system improvement
            app(UltraLogManager::class)->info('[CollectionsController] Collection created successfully via create()', $successContext);

            // 🎯 OS1 Virtuous Response - Rich feedback for frontend
            return response()->json([
                'success' => true,
                'message' => __('collection.created_successfully', ['name' => $collection->collection_name]),
                'collection' => [
                    'id' => $collection->id,
                    'name' => $collection->collection_name,
                    'creator_id' => $collection->creator_id,
                    'position' => $collection->position,
                    'is_default' => $collection->is_default,
                    'created_at' => $collection->created_at->toISOString()
                ],
                'next_action' => [
                    'type' => 'redirect',
                    'url' => route('home.collections.show', ['id' => $collection->id]),
                    'message' => __('collection.redirecting_to_view')
                ],
                'user_stats' => [
                    'total_collections' => $existingCollectionsCount + 1,
                    'remaining_slots' => max(0, $maxCollections - $existingCollectionsCount - 1)
                ]
            ], 201);
        } catch (ValidationException $e) {
            // 🎯 OS1 Validation Error Handling
            return response()->json([
                'success' => false,
                'error' => 'VALIDATION_FAILED',
                'message' => __('validation.failed'),
                'errors' => $e->errors()
            ], 422);
        } catch (Throwable $e) {
            // 🎯 OS1 Comprehensive Error Boundary
            $errorContext = array_merge($operationContext ?? [], [
                'error_class' => get_class($e),
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine()
            ]);

            // Use UEM for standardized error handling
            app(ErrorManagerInterface::class)->handle('COLLECTION_CREATION_UNEXPECTED_ERROR', $errorContext, $e);

            return response()->json([
                'success' => false,
                'error' => 'UNEXPECTED_ERROR',
                'message' => __('collection.creation_unexpected_error'),
                'support_reference' => Str::uuid()->toString() // For support tracking
            ], 500);
        }
    }

    /**
     * Update the EPP Project for a collection.
     *
     * Allows the collection creator/authorized user to select an EPP Project
     * that will receive a percentage of the collection's sales.
     *
     * @param Request $request The HTTP request
     * @param int $id The collection ID
     * @return JsonResponse
     */
    public function updateEppProject(Request $request, $id): \Illuminate\Http\JsonResponse {
        try {
            // Find the collection
            $collection = Collection::findOrFail($id);

            // Check permissions
            $currentUserId = FegiAuth::id();
            if (!$collection->userHasPermission($currentUserId, 'create_collection')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. You do not have permission to modify this collection.'
                ], 403);
            }

            // Validate request
            $validated = $request->validate([
                'epp_project_id' => 'required|exists:epp_projects,id'
            ]);

            // Update the collection
            $collection->epp_project_id = $validated['epp_project_id'];
            $collection->save();

            // Load the EppProject relationship with EPP User
            $collection->load(['eppProject.eppUser.organizationData']);
            $project = $collection->eppProject;
            $eppUser = $project->eppUser;

            return response()->json([
                'success' => true,
                'message' => 'EPP Project updated successfully',
                'data' => [
                    'epp_project' => [
                        'id' => $project->id,
                        'name' => $project->name,
                        'project_type' => $project->project_type,
                        'project_type_name' => $project->project_type_name,
                        'description' => $project->description,
                        'completion_percentage' => $project->completion_percentage,
                        'epp_user' => [
                            'name' => $eppUser->name ?? 'Unknown',
                            'organization_name' => $eppUser->organizationData->organization_name ?? null,
                        ]
                    ]
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Collection or EPP Project not found'
            ], 404);
        } catch (\Throwable $e) {
            app(\Ultra\UltraLogManager\UltraLogManager::class)->error('[CollectionsController] updateEppProject failed', [
                'collection_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating EPP Project'
            ], 500);
        }
    }
}
