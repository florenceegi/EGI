<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Helpers\FegiAuth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Collection;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Ultra\UltraLogManager\UltraLogManager;         // Import ULM
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface; // Import Interfaccia UEM

/**
 * 📜 Oracode Controller: UserCollectionController
 * Manages user-specific collection data and preferences.
 *
 * This controller handles fetching collections accessible to the authenticated user
 * (owned and collaborated on) and setting the user's current active collection.
 * It adheres to Oracode 2.0 principles for clarity, maintainability, and explicit intent,
 * utilizing ULM for logging and an injected UEM instance for error handling.
 *
 * @package App\Http\Controllers\User
 * @version 1.2.0
 * @since 2025-05-09
 * @author Padmin D. Curtis (for Fabio Cherici)
 */
final class UserCollectionController extends Controller
{
    private UltraLogManager $logger;
    private ErrorManagerInterface $errorManager; // Proprietà per UEM

    /**
     * Constructor.
     *
     * @param UltraLogManager $logger The ULM instance for logging.
     * @param ErrorManagerInterface $errorManager The UEM instance for error handling.
     */
    public function __construct(UltraLogManager $logger, ErrorManagerInterface $errorManager)
    {
        $this->logger = $logger;
        $this->errorManager = $errorManager; // Inietta UEM
    }

    /**
     * 🎯 Get all collections accessible to the authenticated user.
     *
     * @param Request $request The incoming HTTP request.
     * @return JsonResponse
     *
     * @oracode-permissions
     *  - Implicitly requires user to be authenticated.
     * @oracode-output
     *  - On Success (200 OK): JSON with 'owned_collections' and 'collaborating_collections'.
     *  - On Unauthenticated (401 - gestito dal middleware Sanctum/Auth): Standard 401 response.
     *  - Utilizzato dal div id = "collection-list-dropdown-menu" nei layout
     */
    public function getAccessibleCollections(Request $request): JsonResponse
    {
        /** @var User|null $user */
        // $user = $request->user();

        $user = FegiAuth::user();

        if (!$user) {
            // Questa condizione dovrebbe essere gestita dal middleware auth:sanctum.
            // Se arriviamo qui, c'è un problema di configurazione del middleware
            // o la rotta non è protetta come dovrebbe.
            $this->logger->critical(
                'Unauthenticated access to getAccessibleCollections. Middleware issue?',
                ['ip_address' => $request->ip(), 'log_category' => 'AUTH_CRITICAL']
            );
            // UEM potrebbe non essere il gestore ideale qui se l'autenticazione fallisce a monte,
            // ma per coerenza, se volessimo un errore UEM specifico:
            // return $this->errorManager->handle('UEM_FRAMEWORK_AUTH_FAILURE', ['details' => 'User not resolved by request object']);
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $this->logger->info(
            'Fetching accessible collections.',
            ['user_id' => $user->id, 'log_category' => 'COLLECTION_ACCESS']
        );

        // Elenco collection dell'utente loggato
        $ownedCollections = $user->ownedCollections()
                                 ->select('id', 'collection_name')
                                 ->orderBy('collection_name', 'asc')
                                 ->get();

        // Elenco collection in cui l'utente loggato collabora come memebro della collection
        $collaboratingCollections = $user->collaborations()
                                         ->with('creator:id,email')
                                         ->select('collections.id', 'collections.collection_name', 'collections.creator_id')
                                         ->orderBy('collections.collection_name', 'asc')
                                         ->get()
                                         ->map(static function (Collection $collection): array {
                                             return [
                                                 'id' => $collection->id,
                                                 'collection_name' => $collection->collection_name,
                                                 'creator_email' => $collection->creator->email ?? 'N/A',
                                             ];
                                         });
        /**
         * @psalm-suppress LessSpecificReturnStatement
         * @psalm-suppress MoreSpecificReturnStatement
         */
        return response()->json([
            'owned_collections' => $ownedCollections,
            'collaborating_collections' => $collaboratingCollections,
        ]);
    }

    /**
     * 🎯 Get collections where the authenticated user can CREATE EGI.
     * Filtra le collection accessibili in base ai permessi del ruolo (pivot collection_users)
     * verificando il permesso Spatie "create_EGI" associato al ruolo.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getEgiCreatableCollections(Request $request): JsonResponse
    {
        /** @var User|null $user */
        $user = FegiAuth::user();

        if (!$user) {
            $this->logger->warning(
                'Unauthenticated access to getEgiCreatableCollections. Middleware issue?',
                ['ip_address' => $request->ip(), 'log_category' => 'AUTH_FAILURE']
            );
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $this->logger->info(
            'Fetching EGI-creatable collections for user.',
            ['user_id' => $user->id, 'log_category' => 'COLLECTION_ACCESS']
        );

        // Recupera sia owned che collaborations, poi filtra per permesso create_EGI
        $allCollections = collect([]);

        // Owned
        $owned = $user->ownedCollections()->select('id', 'collection_name')->get();
        $allCollections = $allCollections->merge($owned);

        // Collaborations
        $collabs = $user->collaborations()->select('collections.id', 'collections.collection_name')->get();
        $allCollections = $allCollections->merge($collabs);

        // Filtra usando la logica dei ruoli sulla pivot + Spatie\Permission
        $eligible = $allCollections->filter(function (Collection $collection) use ($user) {
            try {
                $collectionUser = $collection->users()->where('users.id', $user->id)->first();
                if (!$collectionUser) return false;
                $userRoleName = $collectionUser->pivot->role ?? null;
                if (!$userRoleName) return false;

                $role = \Spatie\Permission\Models\Role::where('name', $userRoleName)->first();
                if (!$role) return false;

                return $role->hasPermissionTo('create_EGI');
            } catch (\Throwable $e) {
                $this->logger->error('Error while checking create_EGI permission for collection', [
                    'user_id' => $user->id,
                    'collection_id' => $collection->id,
                    'error' => $e->getMessage(),
                    'log_category' => 'AUTH_ERROR'
                ]);
                return false;
            }
        })->values()->map(static function (Collection $collection): array {
            return [
                'id' => $collection->id,
                'collection_name' => $collection->collection_name,
            ];
        });

        return response()->json([
            'eligible_collections' => $eligible,
        ]);
    }
    /**
     * 🎯 Set the current active collection for the authenticated user.
     *
     * @param Request $request The incoming HTTP request.
     * @param Collection $collection The collection instance resolved by Route Model Binding.
     * @return JsonResponse
     *
     * @oracode-errors
     *  - UEM_USER_UNAUTHENTICATED (401 - da definire in uem config)
     *  - UEM_SET_CURRENT_COLLECTION_FORBIDDEN (403 - da definire in uem config)
     *  - UEM_SET_CURRENT_COLLECTION_FAILED (500 - da definire in uem config)
     */
    public function setCurrentCollection(Request $request, Collection $collection): JsonResponse
    {
        /** @var User|null $user */
        $user = $request->user();

        if (!$user) {
            $this->logger->warning(
                'Attempt to access setCurrentCollection without authenticated user.',
                ['ip_address' => $request->ip(), 'target_collection_id' => $collection->id, 'log_category' => 'AUTH_FAILURE']
            );
            // Definire 'UEM_USER_UNAUTHENTICATED' in config/error-manager.php
            // Uso dell'istanza iniettata di ErrorManagerInterface
            return $this->errorManager->handle('UEM_USER_UNAUTHENTICATED', [
                'target_collection_id' => $collection->id,
                'ip_address' => $request->ip()
            ], new \Exception('User not authenticated'));
        }

        $isOwner = $collection->creator_id === $user->id;
        $isCollaboratorQuery = $user->collaborations();
        $isCollaborator = false;
        if ($isCollaboratorQuery instanceof Builder) {
             $isCollaborator = $isCollaboratorQuery->where('collection_id', $collection->id)->exists();
        } else {
             $this->logger->error(
                'collaborations() did not return a Query Builder instance for user.',
                ['user_id' => $user->id, 'collection_id' => $collection->id, 'log_category' => 'RELATIONSHIP_ERROR']
            );
            // Considerare UEM_INTERNAL_SERVER_ERROR se questa è una condizione critica
        }

        if (!$isOwner && !$isCollaborator) {
            $this->logger->warning(
                'Unauthorized attempt to set current collection.',
                ['user_id' => $user->id, 'collection_id' => $collection->id, 'ip_address' => $request->ip(), 'log_category' => 'AUTH_FORBIDDEN']
            );
            // Definire 'UEM_SET_CURRENT_COLLECTION_FORBIDDEN' in config/error-manager.php
            return $this->errorManager->handle('UEM_SET_CURRENT_COLLECTION_FORBIDDEN', [ // Uso dell'istanza UEM
                'user_id' => $user->id,
                'collection_id' => $collection->id
            ]);
        }

        try {
            $user->current_collection_id = $collection->id;
            $user->save();

            $request->session()->put('current_collection_id', $collection->id);

           // CRUCIALE: Invalida la cache dell'app config
            $lang = app()->getLocale();
            $cacheKey = "app_config_{$lang}_{$user->id}";
            Cache::forget($cacheKey);

            // Se ci sono più lingue, invalida tutte
            foreach (config('app.available_locales', ['it', 'en']) as $locale) {
                Cache::forget("app_config_{$locale}_{$user->id}");
            }

            $this->logger->info(
                'Current collection updated successfully.',
                ['user_id' => $user->id, 'new_current_collection_id' => $collection->id, 'log_category' => 'COLLECTION_UPDATE']
            );
            /**
             * @psalm-suppress LessSpecificReturnStatement
             * @psalm-suppress MoreSpecificReturnStatement
             */
            return response()->json([
                'message' => 'Current collection updated successfully.',
                'current_collection_id' => $collection->id,
                'current_collection_name' => $collection->collection_name,
            ]);

        } catch (\Throwable $e) {
            $this->logger->error(
                'Exception while updating current collection.',
                [
                    'user_id' => $user->id,
                    'collection_id' => $collection->id,
                    'error_message' => $e->getMessage(),
                    // 'error_trace' => $e->getTraceAsString(), // UEM gestirà il logging della traccia se configurato
                    'log_category' => 'DB_ERROR'
                ]
            );
            // Definire 'UEM_SET_CURRENT_COLLECTION_FAILED' in config/error-manager.php
            return $this->errorManager->handle('UEM_SET_CURRENT_COLLECTION_FAILED', [ // Uso dell'istanza UEM
                'user_id' => $user->id,
                'collection_id' => $collection->id,
            ], $e);
        }
    }
}