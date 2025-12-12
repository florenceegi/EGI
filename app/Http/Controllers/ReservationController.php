<?php

namespace App\Http\Controllers;

use App\Events\PriceUpdated;
use App\Events\StatsUpdated;
use App\Helpers\FegiAuth;
use App\Models\Egi;
use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;
use Illuminate\Support\Facades\Session;

/**
 * ReservationController OS2.0 - UEM/ULM Orchestrated
 *
 * Handles EGI reservation requests with UEM for errors and ULM for operational logging.
 * Uses existing error codes from config and creates new ones only when needed.
 *
 * @author Padmin D. Curtis OS2.0 (for Fabio Cherici)
 * @version 2.1.0-oracode-corrected
 * @package App\Http\Controllers
 */
class ReservationController extends Controller
{
    protected ReservationService $reservationService;
    protected ErrorManagerInterface $errorManager;
    protected UltraLogManager $logger;

    public function __construct(
        ReservationService $reservationService,
        ErrorManagerInterface $errorManager,
        UltraLogManager $logger
    ) {
        $this->reservationService = $reservationService;
        $this->errorManager = $errorManager;
        $this->logger = $logger;
    }

    /**
     * Prepare structure changes data for real-time broadcasting
     *
     * @param \App\Models\Reservation $reservation
     * @param \App\Models\Egi $egi
     * @return array
     */
    private function prepareStructureChanges($reservation, $egi): array
    {
        // Get total reservation count for this EGI
        $reservationCount = $egi->reservations()->count();
        $isFirstReservation = $reservationCount === 1;

        // Get user details from reservation
        $user = $reservation->user;
        $activatorData = [];

        if ($user) {
            // ✅ USA LA FUNZIONE HELPER PER CONSISTENZA
            $formattedDisplay = formatActivatorDisplay($user);

            $activatorData = [
                'name' => $formattedDisplay['name'],
                'avatar' => $formattedDisplay['avatar'], // Ora gestito correttamente dalla helper
                'is_commissioner' => $formattedDisplay['is_commissioner'],
                // Use getAttributes to bypass the wallet accessor
                'wallet' => $user->getAttributes()['wallet'] ?? null
            ];
        } else {
            // Fallback for anonymous reservations
            $activatorData = [
                'name' => 'Utente',
                'avatar' => null,
                'is_commissioner' => false,
                'wallet' => null
            ];
        }

        return [
            'is_first_reservation' => $isFirstReservation,
            'reservation_count' => $reservationCount,
            'activator' => $activatorData,
            'button_state' => $isFirstReservation ? 'rilancia' : 'rilancia', // Sempre rilancia dopo prima prenotazione
        ];
    }

    /**
     * Prepara le statistiche globali per il broadcast real-time
     *
     * @return array
     */
    private function prepareGlobalStats(): array
    {
        try {
            // Usa il controller delle statistiche per ottenere i dati aggiornati
            $statsController = new \App\Http\Controllers\Api\PaymentDistributionStatsController();
            $response = $statsController->getGlobalStats(request());
            $responseData = $response->getData(true);

            if ($responseData['success']) {
                return $responseData;
            }

            return [];
        } catch (\Exception $e) {
            $this->logger->error('Errore nel prepareGlobalStats per broadcast', [
                'error' => $e->getMessage(),
                'log_category' => 'BROADCAST_STATS_ERROR'
            ]);
            return [];
        }
    }

    /**
     * Prepara le statistiche di una collection specifica per il broadcast real-time
     *
     * @param int $collectionId
     * @return array
     */
    private function prepareCollectionStats(int $collectionId): array
    {
        try {
            $collection = \App\Models\Collection::findOrFail($collectionId);

            // VOLUME - Solo distribuzioni di prenotazioni con sub_status = 'highest'
            $totalVolume = \App\Models\PaymentDistribution::join('reservations', 'payment_distributions.reservation_id', '=', 'reservations.id')
                ->where('payment_distributions.collection_id', $collection->id)
                ->where('reservations.sub_status', 'highest')
                ->sum('payment_distributions.amount_eur');

            // EPP - Solo distribuzioni EPP di prenotazioni con sub_status = 'highest'
            $eppTotal = \App\Models\PaymentDistribution::join('reservations', 'payment_distributions.reservation_id', '=', 'reservations.id')
                ->where('payment_distributions.collection_id', $collection->id)
                ->where('reservations.sub_status', 'highest')
                ->where('payment_distributions.user_type', 'epp')
                ->sum('payment_distributions.amount_eur');

            // EGIS - Numero EGI in questa collezione
            $totalEgis = $collection->egis()->count();

            // SELL EGIS - EGI con prenotazioni attive in questa collezione
            $sellEgis = $collection->egis()
                ->whereHas('reservations', function ($query) {
                    $query->where('is_current', true)->where('status', 'active');
                })->count();

            // Formato dati raw
            $data = [
                'volume' => $totalVolume,
                'epp' => $eppTotal,
                'collections' => 1, // Siamo su una collection specifica
                'sell_collections' => $collection->egis()->whereHas('reservations', function ($query) {
                    $query->where('is_current', true)->where('status', 'active');
                })->exists() ? 1 : 0,
                'total_egis' => $totalEgis,
                'sell_egis' => $sellEgis
            ];

            // Formato dati formattati (usa helper di formattazione)
            $formatted = [
                'volume' => '€' . number_format($totalVolume, 2),
                'epp' => '€' . number_format($eppTotal, 2),
                'collections' => number_format(1),
                'sell_collections' => number_format($data['sell_collections']),
                'total_egis' => number_format($totalEgis),
                'sell_egis' => number_format($sellEgis)
            ];

            return [
                'success' => true,
                'data' => $data,
                'formatted' => $formatted
            ];
        } catch (\Exception $e) {
            $this->logger->error('Errore nel prepareCollectionStats per broadcast', [
                'collection_id' => $collectionId,
                'error' => $e->getMessage(),
                'log_category' => 'BROADCAST_STATS_ERROR'
            ]);
            return [];
        }
    }

    /**
     * Handle web-based reservation requests
     *
     * @param Request $request
     * @param int $egiId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reserve(Request $request, int $egiId)
    {
        $this->logger->info('[RESERVATION_WEB_ATTEMPT] Web reservation attempt started', [
            'egi_id' => $egiId,
            'user_id' => FegiAuth::id(),
            'session_id' => $request->session()->getId()
        ]);

        $user = FegiAuth::user();
        if (!$user) {
            $this->logger->warning('Unauthenticated like attempt on EGI', [
                'egi_id' => $egiId,
                'user_id' => FegiAuth::id(),
                'session_id' => $request->session()->getId(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return $this->errorManager->handle('AUTH_REQUIRED', [
                'operation' => 'web_reservation',
                'endpoint' => 'reservations.reserve',
                'user_id' => FegiAuth::id(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'session_id' => $request->session()->getId()
            ]);
        }

        try {
            // Validate inputs
            $validated = $request->validate([
                'offer_amount_fiat' => 'required|numeric|min:1',
                'fiat_currency' => 'string|size:3|in:USD,EUR,GBP',
                'terms_accepted' => 'required|accepted',
                'contact_data' => 'nullable|array'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorManager->handle(
                'VALIDATION_ERROR',
                [
                    'egi_id' => $egiId,
                    'operation' => 'web_reservation',
                    'validation_errors' => $e->errors(),
                    'user_id' => FegiAuth::id(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ],
                $e
            );
        }

        try {
            // Find EGI - wrapped in dedicated try/catch
            $egi = Egi::findOrFail($egiId);
        } catch (\Exception $e) {
            return $this->errorManager->handle('RESERVATION_EGI_NOT_FOUND', [
                'egi_id' => $egiId,
                'operation' => 'web_reservation',
                'user_id' => FegiAuth::id(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ], $e);
        }

        try {
            // Resolve authentication - dedicated try/catch for auth validation
            $user = FegiAuth::user();
            $walletAddress = Session::get('connected_wallet');

            if (!$user && !$walletAddress) {
                // Throw a custom exception to be caught by the outer catch
                throw new \Exception('Unauthorized access: no user or wallet authentication found');
            }

            // Create reservation
            $reservation = $this->reservationService->createReservation(
                [
                    'egi_id' => $egiId,
                    'offer_amount_fiat' => $validated['offer_amount_fiat'],
                    'fiat_currency' => $validated['fiat_currency'] ?? 'EUR',
                    'contact_data' => $validated['contact_data'] ?? null
                ],
                $user,
                $walletAddress
            );

            // Log success
            $this->logger->info('[RESERVATION_WEB_SUCCESS] Web reservation completed successfully', [
                'reservation_id' => $reservation->id,
                'egi_id' => $egiId,
                'user_id' => FegiAuth::id(),
                'certificate_uuid' => $reservation->certificate?->certificate_uuid,
                'offer_amount_fiat' => $reservation->offer_amount_fiat,
                'fiat_currency' => $reservation->fiat_currency
            ]);

            // 🔴 Emit real-time price update event with structure changes
            $structureChanges = $this->prepareStructureChanges($reservation, $egi);
            PriceUpdated::dispatch(
                $egiId,
                number_format((float)$reservation->offer_amount_fiat, 2),
                $reservation->fiat_currency ?? 'EUR',
                now()->toISOString(),
                $structureChanges
            );

            // 📊 Emit real-time global stats update event
            $globalStats = $this->prepareGlobalStats();
            if (!empty($globalStats)) {
                StatsUpdated::dispatch(
                    $globalStats,
                    now()->toISOString(),
                    'reservation_created'
                );
            }

            // 📊 Emit real-time collection-specific stats update event
            if ($reservation->egi && $reservation->egi->collection_id) {
                $collectionStats = $this->prepareCollectionStats($reservation->egi->collection_id);
                if (!empty($collectionStats)) {
                    StatsUpdated::dispatch(
                        $collectionStats,
                        now()->toISOString(),
                        'reservation_created',
                        $reservation->egi->collection_id
                    );
                }
            }

            return redirect()->route('egi-certificates.show', $reservation->certificate->certificate_uuid)
                ->with('success', __('reservation.success'));
        } catch (\Exception $e) {
            // Handle relaunch amount validation errors specifically
            if (str_contains($e->getMessage(), 'rilancio deve essere superiore')) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['offer_amount_fiat' => $e->getMessage()]);
            }

            // Handle unauthorized access and any other reservation creation errors
            if (str_contains($e->getMessage(), 'Unauthorized access')) {
                return $this->errorManager->handle(
                    'RESERVATION_UNAUTHORIZED',
                    [
                        'egi_id' => $egiId,
                        'has_user' => FegiAuth::user() !== null,
                        'has_wallet' => Session::has('connected_wallet'),
                        'operation' => 'web_reservation',
                        'user_id' => FegiAuth::id(),
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent()
                    ],
                    $e
                );
            }

            // Check if it's a known reservation exception
            $errorCode = method_exists($e, 'getErrorCode') ? $e->getErrorCode() : 'RESERVATION_UNKNOWN_ERROR';

            return $this->errorManager->handle(
                $errorCode,
                [
                    'egi_id' => $egiId,
                    'operation' => 'web_reservation',
                    'exception_class' => get_class($e),
                    'user_id' => FegiAuth::id(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'error' => $e->getMessage()
                ],
                $e
            );
        }
    }

    /**
     * Handle API-based reservation requests
     *
     * @param Request $request
     * @param int $egiId
     * @return JsonResponse
     */
    public function apiReserve(Request $request, int $egiId): JsonResponse
    {
        $this->logger->info('[RESERVATION_API_ATTEMPT] API reservation attempt started', [
            'egi_id' => $egiId,
            'user_id' => FegiAuth::id(),
            'api_version' => $request->header('API-Version', 'v1'),
            'connected_wallet' => Session::get('connected_wallet', null),
            'session_id' => Session::getId()
        ]);

        $user = FegiAuth::user();
        if (!$user) {
            $this->logger->warning('Unauthenticated like attempt on EGI', [
                'egi_id' => $egiId,
                'user_id' => FegiAuth::id(),
                'session_id' => $request->session()->getId(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return $this->errorManager->handle('AUTH_REQUIRED', [
                'operation' => 'web_reservation',
                'endpoint' => 'reservations.reserve',
                'user_id' => FegiAuth::id(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'session_id' => $request->session()->getId()
            ]);
        }
        try {
            // Validate inputs
            $validated = $request->validate([
                'offer_amount_fiat' => 'required|numeric|min:1',
                'terms_accepted' => 'required|accepted',
                'contact_data' => 'nullable|array',
                'wallet' => 'nullable|string|size:58'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorManager->handle(
                'VALIDATION_ERROR',
                [
                    'egi_id' => $egiId,
                    'operation' => 'api_reservation',
                    'validation_errors' => $e->errors(),
                    'user_id' => FegiAuth::id(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ],
                $e
            );
        }

        try {
            // Find EGI - dedicated try/catch
            $egi = Egi::findOrFail($egiId);
        } catch (\Exception $e) {
            return $this->errorManager->handle(
                'RESERVATION_EGI_NOT_FOUND',
                [
                    'egi_id' => $egiId,
                    'operation' => 'api_reservation',
                    'user_id' => FegiAuth::id(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ],
                $e
            );
        }

        try {
            // Resolve authentication - dedicated try/catch for auth validation
            $user = FegiAuth::user();
            $sessionWallet = Session::get('connected_wallet');
            $walletAddress = $user['wallet'] ?? $sessionWallet;

            if (!$user && !$walletAddress) {
                throw new \Exception('Unauthorized access: no user or wallet authentication found');
            }

            // Create reservation
            $reservation = $this->reservationService->createReservation(
                [
                    'egi_id' => $egiId,
                    'offer_amount_fiat' => $validated['offer_amount_fiat'],
                    'contact_data' => $validated['contact_data'] ?? null
                ],
                $user,
                $walletAddress
            );

            // Log success
            $this->logger->info('[RESERVATION_API_SUCCESS] API reservation completed successfully', [
                'reservation_id' => $reservation->id,
                'egi_id' => $egiId,
                'user_id' => FegiAuth::id(),
                'certificate_uuid' => $reservation->certificate?->certificate_uuid,
                'offer_amount_fiat' => $reservation->offer_amount_fiat
            ]);

            // 🔴 Emit real-time price update event with structure changes
            $structureChanges = $this->prepareStructureChanges($reservation, $egi);
            PriceUpdated::dispatch(
                $egiId,
                number_format((float)$reservation->offer_amount_fiat, 2),
                $reservation->fiat_currency ?? 'EUR',
                now()->toISOString(),
                $structureChanges
            );

            // 📊 Emit real-time global stats update event
            $globalStats = $this->prepareGlobalStats();
            if (!empty($globalStats)) {
                StatsUpdated::dispatch(
                    $globalStats,
                    now()->toISOString(),
                    'api_reservation_created'
                );
            }

            // 📊 Emit real-time collection-specific stats update event
            if ($reservation->egi && $reservation->egi->collection_id) {
                $collectionStats = $this->prepareCollectionStats($reservation->egi->collection_id);
                if (!empty($collectionStats)) {
                    StatsUpdated::dispatch(
                        $collectionStats,
                        now()->toISOString(),
                        'api_reservation_created',
                        $reservation->egi->collection_id
                    );
                }
            }

            return response()->json([
                'success' => true,
                'message' => __('reservation.success'),
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->usertype === 'commissioner' ? $user->name : null,
                        'last_name' => $user->usertype === 'commissioner' ? $user->last_name : null,
                        'wallet' => $sessionWallet,
                        'avatar' => $user->profile_photo_url, // Ora gestisce automaticamente la privacy
                        'is_commissioner' => $user->usertype === 'commissioner'
                    ],
                    'reservation' => [
                        'id' => $reservation->id,
                        'type' => $reservation->type,
                        'offer_amount_fiat' => $reservation->offer_amount_fiat,
                        'offer_amount_algo' => $reservation->offer_amount_algo,
                        'status' => $reservation->status,
                        'is_current' => $reservation->is_current
                    ],
                    'certificate' => $reservation->certificate ? [
                        'uuid' => $reservation->certificate->certificate_uuid,
                        'url' => $reservation->certificate->getPublicUrlAttribute(),
                        'verification_url' => $reservation->certificate->getVerificationUrl(),
                        'pdf_url' => $reservation->certificate->getPdfUrl()
                    ] : null
                ]
            ]);
        } catch (\Exception $e) {
            // Handle relaunch amount validation errors specifically
            if (str_contains($e->getMessage(), 'rilancio deve essere superiore')) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'errors' => [
                        'offer_amount_fiat' => [$e->getMessage()]
                    ]
                ], 422);
            }

            // Handle unauthorized access and any other reservation creation errors
            if (str_contains($e->getMessage(), 'Unauthorized access')) {
                return $this->errorManager->handle(
                    'RESERVATION_UNAUTHORIZED',
                    [
                        'egi_id' => $egiId,
                        'operation' => 'api_reservation',
                        'has_user' => FegiAuth::user() !== null,
                        'has_wallet' => Session::has('connected_wallet'),
                        'user_id' => FegiAuth::id(),
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent()
                    ],
                    $e
                );
            }

            $errorCode = method_exists($e, 'getErrorCode') ? $e->getErrorCode() : 'RESERVATION_UNKNOWN_ERROR';

            return $this->errorManager->handle(
                $errorCode,
                [
                    'egi_id' => $egiId,
                    'operation' => 'api_reservation',
                    'exception_class' => get_class($e),
                    'user_id' => FegiAuth::id(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'error' => $e->getMessage()
                ],
                $e
            );
        }
    }

    /**
     * Cancel a reservation
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function cancel(Request $request, int $id): JsonResponse
    {
        $this->logger->info('[RESERVATION_CANCEL_ATTEMPT] Reservation cancellation attempt', [
            'reservation_id' => $id,
            'user_id' => FegiAuth::id()
        ]);

        try {
            $reservation = Reservation::findOrFail($id);
        } catch (\Exception $e) {
            return $this->errorManager->handle(
                'RECORD_NOT_FOUND',
                [
                    'model' => 'Reservation',
                    'id' => $id,
                    'operation' => 'cancel',
                    'user_id' => FegiAuth::id(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ],
                $e
            );
        }

        try {
            // Check authorization - dedicated try/catch for auth validation
            $user = FegiAuth::user();
            $isOwner = $user && $user->id === $reservation->user_id;
            $hasWallet = $request->session()->has('connected_wallet') &&
                $reservation->certificate &&
                $reservation->certificate->wallet_address === $request->session()->get('connected_wallet');

            if (!$isOwner && !$hasWallet) {
                throw new \Exception('Unauthorized cancellation attempt');
            }

            // Cancel reservation
            $result = $this->reservationService->cancelReservation($reservation);

            if (!$result) {
                throw new \Exception('Reservation cancellation failed in service');
            }

            $this->logger->info('[RESERVATION_CANCEL_SUCCESS] Reservation cancelled successfully', [
                'reservation_id' => $id,
                'user_id' => FegiAuth::id(),
                'egi_id' => $reservation->egi_id
            ]);

            return response()->json([
                'success' => true,
                'message' => __('reservation.cancel_success')
            ]);
        } catch (\Exception $e) {
            if (str_contains($e->getMessage(), 'Unauthorized cancellation')) {
                return $this->errorManager->handle(
                    'RESERVATION_UNAUTHORIZED_CANCEL',
                    [
                        'reservation_id' => $id,
                        'user_id' => FegiAuth::id(),
                        'is_owner' => $user && $user->id === $reservation->user_id,
                        'has_wallet' => $request->session()->has('connected_wallet'),
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent()
                    ],
                    $e
                );
            }

            return $this->errorManager->handle(
                'RESERVATION_CANCEL_FAILED',
                [
                    'reservation_id' => $id,
                    'user_id' => FegiAuth::id(),
                    'exception_class' => get_class($e),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'error' => $e->getMessage()
                ],
                $e
            );
        }
    }

    /**
     * List user's active reservations
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function listUserReservations(Request $request): JsonResponse
    {
        $this->logger->info('[RESERVATION_LIST_ATTEMPT] User reservations list requested', [
            'user_id' => FegiAuth::id()
        ]);

        try {
            $user = FegiAuth::user();

            if (!$user) {
                throw new \Exception('Authentication required for listing reservations');
            }

            $reservations = $this->reservationService->getUserActiveReservations($user);

            // Transform data for API response
            $data = $reservations->map(function ($reservation) {
                return [
                    'id' => $reservation->id,
                    'egi' => [
                        'id' => $reservation->egi->id,
                        'title' => $reservation->egi->title,
                        'collection_id' => $reservation->egi->collection_id,
                        'collection_name' => $reservation->egi->collection->collection_name ?? null
                    ],
                    'type' => $reservation->type,
                    'offer_amount_fiat' => $reservation->offer_amount_fiat,
                    'offer_amount_algo' => $reservation->offer_amount_algo,
                    'created_at' => $reservation->created_at->toIso8601String(),
                    'certificate' => $reservation->certificate ? [
                        'uuid' => $reservation->certificate->certificate_uuid,
                        'url' => $reservation->certificate->getPublicUrlAttribute(),
                        'pdf_url' => $reservation->certificate->getPdfUrl()
                    ] : null
                ];
            });

            $this->logger->info('[RESERVATION_LIST_SUCCESS] User reservations retrieved successfully', [
                'user_id' => FegiAuth::id(),
                'count' => $reservations->count()
            ]);

            return response()->json([
                'success' => true,
                'count' => $reservations->count(),
                'reservations' => $data
            ]);
        } catch (\Exception $e) {
            if (str_contains($e->getMessage(), 'Authentication required')) {
                return $this->errorManager->handle(
                    'AUTH_REQUIRED',
                    [
                        'operation' => 'list_reservations',
                        'endpoint' => 'reservations.list',
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent()
                    ],
                    $e
                );
            }

            return $this->errorManager->handle(
                'RESERVATION_LIST_FAILED',
                [
                    'user_id' => FegiAuth::id(),
                    'exception_class' => get_class($e),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'error' => $e->getMessage()
                ],
                $e
            );
        }
    }

    /**
     * Get reservation status for an EGI
     *
     * @param Request $request
     * @param int $egiId
     * @return JsonResponse
     */
    public function getEgiReservationStatus(Request $request, int $egiId): JsonResponse
    {
        // $this->logger->info('[RESERVATION_STATUS_REQUEST] EGI reservation status requested', [
        //     'egi_id' => $egiId,
        //     'user_id' => FegiAuth::id()
        // ]);

        try {
            $egi = Egi::findOrFail($egiId);
        } catch (\Exception $e) {
            return $this->errorManager->handle(
                'RECORD_EGI_NOT_FOUND_IN_RESERVATION_CONTROLLER',
                [
                    'model' => 'Egi',
                    'id' => $egiId,
                    'operation' => 'reservation_status',
                    'user_id' => FegiAuth::id(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ],
                $e
            );
        }

        try {
            // Get highest priority reservation
            $highestReservation = $this->reservationService->getHighestPriorityReservation($egi);

            // Get count of all current reservations (not just active status)
            $totalReservations = Reservation::where('egi_id', $egiId)
                ->where('is_current', true)
                ->count();

            // Check if current user has a reservation
            $user = FegiAuth::user();
            $walletAddress = session('connected_wallet');
            $userReservation = null;

            if ($user) {
                $userReservation = Reservation::where('egi_id', $egiId)
                    ->where('user_id', $user->id)
                    ->where('is_current', true)
                    ->first();
            } elseif ($walletAddress) {
                $userReservation = Reservation::whereHas('certificate', function ($query) use ($walletAddress) {
                    $query->where('wallet_address', $walletAddress);
                })
                    ->where('egi_id', $egiId)
                    ->where('is_current', true)
                    ->first();
            }

            // Build response data
            $data = [
                'egi_id' => $egiId,
                'is_reserved' => $highestReservation !== null,
                'total_reservations' => $totalReservations,
                'user_has_reservation' => $userReservation !== null,
                'highest_priority_reservation' => $highestReservation ? [
                    'type' => $highestReservation->type,
                    'offer_amount_fiat' => $highestReservation->offer_amount_fiat,
                    'belongs_to_current_user' => $userReservation && $userReservation->id === $highestReservation->id
                ] : null,
                'user_reservation' => $userReservation ? [
                    'id' => $userReservation->id,
                    'type' => $userReservation->type,
                    'offer_amount_fiat' => $userReservation->offer_amount_fiat,
                    'offer_amount_algo' => $userReservation->offer_amount_algo,
                    'is_highest_priority' => $highestReservation && $userReservation->id === $highestReservation->id,
                    'created_at' => $userReservation->created_at->toIso8601String(),
                    'certificate' => $userReservation->certificate ? [
                        'uuid' => $userReservation->certificate->certificate_uuid,
                        'url' => $userReservation->certificate->getPublicUrlAttribute()
                    ] : null
                ] : null
            ];

            // $this->logger->info('[RESERVATION_STATUS_SUCCESS] EGI reservation status retrieved', [
            //     'egi_id' => $egiId,
            //     'total_reservations' => $totalReservations,
            //     'user_has_reservation' => $userReservation !== null
            // ]);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return $this->errorManager->handle(
                'RESERVATION_STATUS_FAILED',
                [
                    'egi_id' => $egiId,
                    'user_id' => FegiAuth::id(),
                    'exception_class' => get_class($e),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'error' => $e->getMessage()
                ],
                $e
            );
        }
    }

    /**
     * Get reservation history for an EGI
     *
     * @param Request $request
     * @param int $egiId
     * @return JsonResponse
     */
    public function getReservationHistory(Request $request, int $egiId): JsonResponse
    {
        try {
            // Trova l'EGI
            $egi = Egi::findOrFail($egiId);

            // Usa il ReservationService per ottenere la cronologia
            $reservations = $this->reservationService->getReservationHistory($egi);

            // Formatta i dati per la risposta
            $formattedReservations = $reservations->map(function ($reservation) {
                return [
                    'id' => $reservation->id,
                    'type' => $reservation->type,
                    'offer_amount_fiat' => $reservation->offer_amount_fiat,
                    'offer_amount_algo' => $reservation->offer_amount_algo,
                    'created_at' => $reservation->created_at->toIso8601String(),
                    'is_current' => $reservation->is_current,
                    'status' => $reservation->status,
                    'user' => $reservation->user ? [
                        'id' => $reservation->user->id,
                        'name' => $reservation->user->name ?? 'Anonymous'
                    ] : null
                ];
            });

            return response()->json([
                'success' => true,
                'egi_id' => $egiId,
                'total_count' => $reservations->count(),
                'reservations' => $formattedReservations
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorManager->handle(
                'RECORD_EGI_NOT_FOUND_IN_RESERVATION_CONTROLLER',
                [
                    'model' => 'Egi',
                    'id' => $egiId,
                    'operation' => 'reservation_history',
                    'user_id' => FegiAuth::id(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ],
                $e instanceof \Throwable ? $e : null  // Cast sicuro
            );
        } catch (\Exception $e) {
            return $this->errorManager->handle(
                'RESERVATION_HISTORY_FAILED',
                [
                    'egi_id' => $egiId,
                    'user_id' => FegiAuth::id(),
                    'exception_class' => get_class($e),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'error' => $e->getMessage()
                ],
                $e
            );
        }
    }

    /**
     * Get reservation status for an EGI
     * Used by frontend to check if EGI is reserved and update button state
     *
     * @param Request $request
     * @param int $egiId
     * @return JsonResponse
     */
    public function getReservationStatus(Request $request, int $egiId): JsonResponse
    {
        try {
            // Find the EGI
            $egi = Egi::find($egiId);
            if (!$egi) {
                return response()->json([
                    'success' => false,
                    'message' => 'EGI not found'
                ], 404);
            }

            // Check if EGI has any current reservations
            $reservationCount = Reservation::where('egi_id', $egiId)
                ->where('is_current', true)
                ->count();
            $isReserved = $reservationCount > 0;

            // Get highest priority reservation if any
            $highestPriorityReservation = null;
            if ($isReserved) {
                $highestPriorityReservation = Reservation::where('egi_id', $egiId)
                    ->where('is_current', true)
                    ->orderBy('created_at', 'asc') // Priority calculation might be complex, use date for now
                    ->first();
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'egi_id' => $egiId,
                    'is_reserved' => $isReserved,
                    'total_reservations' => $reservationCount,
                    'highest_priority' => $highestPriorityReservation ? ($highestPriorityReservation->priority ?? 1) : null,
                    'highest_priority_user' => $highestPriorityReservation ? $highestPriorityReservation->user->name : null,
                    // Debug info
                    'debug_count' => $reservationCount,
                    'debug_all_count' => Reservation::where('egi_id', $egiId)->count()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking reservation status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * EXTENSION METHODS FOR ReservationController
     *
     * ADD these methods to the existing app/Http/Controllers/ReservationController.php
     * DO NOT replace the existing file, just add these methods to it
     *
     * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
     * @version 1.0.0 (FlorenceEGI - Pre-Launch Extension)
     * @date 2025-08-15
     */

    // ============================================================================
    // ADD THESE METHODS TO YOUR EXISTING ReservationController CLASS
    // ============================================================================

    /**
     * Create or update a pre-launch reservation with public ranking
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function createPreLaunchReservation(Request $request): JsonResponse
    {
        $this->logger->info('[PRE_LAUNCH_RESERVATION] Create/update request', [
            'user_id' => FegiAuth::id(),
            'ip' => $request->ip()
        ]);

        // Validate request
        $validated = $request->validate([
            'egi_id' => 'required|integer|exists:egis,id',
            'amount_eur' => 'required|numeric|min:1|max:1000000'
        ]);

        $user = FegiAuth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required for reservations'
            ], 401);
        }

        try {
            // Check if user can reserve
            $canReserve = $this->reservationService->canUserMakePreLaunchReservation(
                $validated['egi_id'],
                $user->id
            );

            if (!$canReserve['can_reserve']) {
                return response()->json([
                    'success' => false,
                    'reason' => $canReserve['reason'],
                    'message' => $canReserve['message']
                ], 403);
            }

            // Create or update reservation
            $reservation = $this->reservationService->createOrUpdatePreLaunchReservation(
                $validated['egi_id'],
                $user->id,
                $validated['amount_eur']
            );

            $this->logger->info('[PRE_LAUNCH_RESERVATION] Reservation created/updated', [
                'reservation_id' => $reservation->id,
                'rank_position' => $reservation->rank_position,
                'is_highest' => $reservation->is_highest
            ]);

            // 🔴 Emit real-time price update event for pre-launch reservation
            $egi = Egi::find($reservation->egi_id);
            if ($egi) {
                $structureChanges = $this->prepareStructureChanges($reservation, $egi);
                PriceUpdated::dispatch(
                    $reservation->egi_id,
                    number_format((float)$reservation->amount_eur, 2),
                    'EUR',
                    now()->toISOString(),
                    $structureChanges
                );

                // 📊 Emit real-time global stats update event for pre-launch
                $globalStats = $this->prepareGlobalStats();
                if (!empty($globalStats)) {
                    StatsUpdated::dispatch(
                        $globalStats,
                        now()->toISOString(),
                        'prelaunch_reservation'
                    );
                }
            }

            return response()->json([
                'success' => true,
                'message' => $reservation->is_highest
                    ? 'Congratulations! You are now the highest bidder!'
                    : 'Reservation created. You are in position #' . $reservation->rank_position,
                'data' => [
                    'reservation_id' => $reservation->id,
                    'egi_id' => $reservation->egi_id,
                    'amount_eur' => $reservation->amount_eur,
                    'rank_position' => $reservation->rank_position,
                    'is_highest' => $reservation->is_highest,
                    'created_at' => $reservation->created_at->toIso8601String(),
                    'updated_at' => $reservation->updated_at->toIso8601String()
                ]
            ]);
        } catch (\Exception $e) {
            return $this->errorManager->handle(
                'PRE_LAUNCH_RESERVATION_CREATE_ERROR',
                [
                    'egi_id' => $validated['egi_id'],
                    'user_id' => $user->id,
                    'amount_eur' => $validated['amount_eur'],
                    'error_message' => $e->getMessage()
                ],
                $e
            );
        }
    }

    /**
     * Get pre-launch reservations with ranking for an EGI
     *
     * @param Request $request
     * @param int $egiId
     * @return JsonResponse
     */
    public function getPreLaunchRankings(Request $request, int $egiId): JsonResponse
    {
        $this->logger->info('[PRE_LAUNCH_RESERVATION] Rankings requested', [
            'egi_id' => $egiId,
            'user_id' => FegiAuth::id()
        ]);

        try {
            // Verify EGI exists
            $egi = Egi::findOrFail($egiId);

            // Get reservations with ranking
            $reservations = $this->reservationService->getEgiReservationsWithRanking($egiId);

            // Get stats
            $stats = $this->reservationService->getEgiRankingStats($egiId);

            // Format response
            $formattedReservations = $reservations->map(function ($reservation) {
                $isCurrentUser = FegiAuth::check() && $reservation->user_id === FegiAuth::id();

                return [
                    'rank_position' => $reservation->rank_position,
                    'amount_eur' => $reservation->amount_eur,
                    'is_highest' => $reservation->is_highest,
                    'is_mine' => $isCurrentUser,
                    'user' => !$isCurrentUser ? [
                        'name' => $reservation->user->name ?? 'Anonymous'
                    ] : null,
                    'created_at' => $reservation->created_at->toIso8601String()
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'egi_id' => $egiId,
                    'egi_title' => $egi->title,
                    'total_reservations' => $reservations->count(),
                    'rankings' => $formattedReservations,
                    'stats' => $stats
                ]
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'EGI not found'
            ], 404);
        } catch (\Exception $e) {
            return $this->errorManager->handle(
                'PRE_LAUNCH_RANKINGS_ERROR',
                [
                    'egi_id' => $egiId,
                    'error_message' => $e->getMessage()
                ],
                $e
            );
        }
    }

    /**
     * Withdraw a pre-launch reservation
     *
     * @param Request $request
     * @param int $reservationId
     * @return JsonResponse
     */
    public function withdrawPreLaunchReservation(Request $request, int $reservationId): JsonResponse
    {
        $this->logger->info('[PRE_LAUNCH_RESERVATION] Withdraw request', [
            'reservation_id' => $reservationId,
            'user_id' => FegiAuth::id()
        ]);

        $user = FegiAuth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
        }

        try {
            $success = $this->reservationService->withdrawPreLaunchReservation(
                $reservationId,
                $user->id
            );

            if ($success) {
                $this->logger->info('[PRE_LAUNCH_RESERVATION] Reservation withdrawn', [
                    'reservation_id' => $reservationId,
                    'user_id' => $user->id
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Reservation withdrawn successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to withdraw reservation'
            ], 500);
        } catch (\Exception $e) {
            return $this->errorManager->handle(
                'PRE_LAUNCH_WITHDRAW_ERROR',
                [
                    'reservation_id' => $reservationId,
                    'user_id' => $user->id,
                    'error_message' => $e->getMessage()
                ],
                $e
            );
        }
    }

    /**
     * Get user's pre-launch reservations
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getUserPreLaunchReservations(Request $request): JsonResponse
    {
        $user = FegiAuth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
        }

        $this->logger->info('[PRE_LAUNCH_RESERVATION] User reservations requested', [
            'user_id' => $user->id
        ]);

        try {
            $reservations = $this->reservationService->getUserReservations($user->id);

            $formattedReservations = $reservations->map(function ($reservation) {
                return [
                    'reservation_id' => $reservation->id,
                    'egi' => [
                        'id' => $reservation->egi->id,
                        'title' => $reservation->egi->title,
                        'slug' => $reservation->egi->slug,
                        'image_url' => $reservation->egi->image_url
                    ],
                    'amount_eur' => $reservation->amount_eur,
                    'rank_position' => $reservation->rank_position,
                    'is_highest' => $reservation->is_highest,
                    'status' => $reservation->status,
                    'created_at' => $reservation->created_at->toIso8601String(),
                    'updated_at' => $reservation->updated_at->toIso8601String()
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'total_count' => $reservations->count(),
                    'reservations' => $formattedReservations
                ]
            ]);
        } catch (\Exception $e) {
            return $this->errorManager->handle(
                'USER_PRE_LAUNCH_RESERVATIONS_ERROR',
                [
                    'user_id' => $user->id,
                    'error_message' => $e->getMessage()
                ],
                $e
            );
        }
    }

    /**
     * Check if user can make a pre-launch reservation for an EGI
     *
     * @param Request $request
     * @param int $egiId
     * @return JsonResponse
     */
    public function checkPreLaunchReservationEligibility(Request $request, int $egiId): JsonResponse
    {
        $user = FegiAuth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
        }

        try {
            $result = $this->reservationService->canUserMakePreLaunchReservation(
                $egiId,
                $user->id
            );

            return response()->json([
                'success' => true,
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking eligibility',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get detailed EGI information for reservation modal
     *
     * @param Request $request
     * @param int $egiId
     * @return JsonResponse
     */
    public function getEgiModalInfo(Request $request, int $egiId): JsonResponse
    {
        $this->logger->info('[EGI_MODAL_INFO] Request received', [
            'egi_id' => $egiId,
            'user_id' => FegiAuth::id(),
            'ip' => $request->ip()
        ]);

        try {
            // Find the EGI
            $egi = Egi::with(['collection', 'reservations' => function ($query) {
                $query->where('is_current', true)
                    ->where('status', 'active')
                    ->with('user');
            }])->find($egiId);

            if (!$egi) {
                $this->logger->warning('[EGI_MODAL_INFO] EGI not found', ['egi_id' => $egiId]);
                return response()->json([
                    'success' => false,
                    'message' => 'EGI not found'
                ], 404);
            }            // Get reservation information
            $reservationService = app('App\Services\ReservationService');
            $highestPriorityReservation = $reservationService->getHighestPriorityReservation($egi);

            $response = [
                'success' => true,
                'data' => [
                    'egi_id' => $egiId,
                    'title' => $egi->title,
                    'base_price' => $egi->auction_minimum_price,
                    'current_price' => $egi->auction_minimum_price,
                    'has_reservations' => false,
                    'activator' => null
                ]
            ];

            // Se c'è una prenotazione attiva
            if ($highestPriorityReservation && $highestPriorityReservation->status === 'active') {
                $activator = $highestPriorityReservation->user;
                $isCommissioner = false;
                $activatorInfo = null;

                if ($activator) {
                    // Usa la nuova logica basata su usertype
                    if ($activator->usertype === 'commissioner') {
                        // Mostra informazioni complete
                        $activatorInfo = [
                            'type' => 'commissioner',
                            'name' => ($activator->first_name && $activator->last_name)
                                ? $activator->first_name . ' ' . $activator->last_name
                                : $activator->name,
                            'avatar' => $activator->profile_photo_url, // Gestisce automaticamente la privacy
                            'id' => $activator->id
                        ];
                    } else {
                        // Mostra solo icona generata e wallet
                        $activatorInfo = [
                            'type' => 'anonymous',
                            'avatar' => $activator->profile_photo_url, // Restituirà l'avatar generato
                            'wallet' => $activator->wallet ?? 'N/A',
                            'id' => $activator->id
                        ];
                    }
                }

                $response['data']['current_price'] = $highestPriorityReservation->offer_amount_fiat;
                $response['data']['has_reservations'] = true;
                $response['data']['activator'] = $activatorInfo;
                $response['data']['reservation_type'] = $highestPriorityReservation->type;
            }

            return response()->json($response);
        } catch (\Exception $e) {
            $this->logger->error('Error getting EGI modal info', [
                'egi_id' => $egiId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error retrieving EGI information',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ============================================================================
    // END OF EXTENSION METHODS
    // ============================================================================
}
