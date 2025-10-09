<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Egi;
use App\Services\EgiAvailabilityService;
use Illuminate\Support\Facades\Auth;

/**
 * @Oracode Request: Reservation Validation
 * 🎯 Purpose: Validate reservation requests (Phase 2 dual path)
 * 🛡️ Security: Permission checks, rate limiting, GDPR consent, EGI availability, duplicate prevention
 * 🧱 Business Rules: Published, not minted, not draft, user not creator, no existing reservation
 *
 * @package App\Http\Requests
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Phase 2 Expansion)
 * @date 2025-10-09
 * @purpose Validation for reservation operations (dual path mint vs reservation)
 *
 * MiCA-SAFE Compliance:
 * - Reservation = FIAT pre-authorization only
 * - No crypto custody
 * - Payment captured only on mint completion
 */
class ReservationRequest extends FormRequest {
    
    /**
     * Determine if the user is authorized to make this request.
     * 
     * Authorization checks:
     * - User must be authenticated
     * - User must have 'allow-reservation-operations' permission
     * - User cannot reserve their own EGI
     * - EGI must be available for reservation
     * - User must not have an existing reservation for this EGI
     *
     * @return bool
     */
    public function authorize(): bool {
        $user = Auth::user();

        // Must be authenticated
        if (!$user) {
            return false;
        }

        // Get EGI from route parameter
        $egiId = $this->route('id') ?? $this->input('egi_id');
        
        if (!$egiId) {
            return false;
        }

        $egi = Egi::find($egiId);
        
        if (!$egi) {
            return false;
        }

        // User cannot reserve their own EGI
        if ($egi->user_id === $user->id) {
            return false;
        }

        // Check permission (can use blockchain permission or dedicated reservation permission)
        $hasPermission = $user->can('allow-reservation-operations') 
                      || $user->can('allow-blockchain-operations');
        
        if (!$hasPermission) {
            return false;
        }

        // Check EGI availability using service
        $availabilityService = app(EgiAvailabilityService::class);
        $availability = $availabilityService->checkAvailability($egi, $user);

        // Must be available for reservation
        if (!$availability['can_reserve']) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array {
        return [
            // EGI identification
            'egi_id' => [
                'required',
                'integer',
                'exists:egis,id'
            ],

            // Reservation duration (hours)
            'reservation_duration' => [
                'nullable',
                'integer',
                'min:1',
                'max:168' // Max 7 days (168 hours)
            ],

            // Payment method for future mint (MiCA-SAFE: FIAT only)
            'preferred_payment_method' => [
                'nullable',
                'string',
                'in:stripe,paypal,bank_transfer' // NO crypto
            ],

            // GDPR consent confirmation
            'consent_reservation' => [
                'required',
                'boolean',
                'accepted' // Must be true
            ],

            // Optional: reservation notes (why reserving)
            'reservation_notes' => [
                'nullable',
                'string',
                'max:500'
            ],

            // Optional: notification preferences
            'notify_on_expiry' => [
                'nullable',
                'boolean'
            ],

            'notify_on_availability' => [
                'nullable',
                'boolean'
            ],

            // Anti-CSRF protection (implicit via FormRequest)
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array {
        return [
            // EGI validation
            'egi_id.required' => 'EGI ID obbligatorio',
            'egi_id.integer' => 'EGI ID deve essere numerico',
            'egi_id.exists' => 'EGI non trovato',

            // Reservation duration
            'reservation_duration.integer' => 'Durata deve essere numerica (ore)',
            'reservation_duration.min' => 'Durata minima 1 ora',
            'reservation_duration.max' => 'Durata massima 168 ore (7 giorni)',

            // Payment method
            'preferred_payment_method.in' => 'Metodo di pagamento non valido',

            // GDPR consent
            'consent_reservation.required' => 'Consenso prenotazione obbligatorio',
            'consent_reservation.boolean' => 'Consenso deve essere true/false',
            'consent_reservation.accepted' => 'Devi accettare i termini delle prenotazioni',

            // Notes
            'reservation_notes.string' => 'Note deve essere testo',
            'reservation_notes.max' => 'Note massimo 500 caratteri',

            // Notifications
            'notify_on_expiry.boolean' => 'Notifica scadenza deve essere true/false',
            'notify_on_availability.boolean' => 'Notifica disponibilità deve essere true/false',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array {
        return [
            'egi_id' => 'EGI',
            'reservation_duration' => 'durata prenotazione',
            'preferred_payment_method' => 'metodo pagamento preferito',
            'consent_reservation' => 'consenso prenotazione',
            'reservation_notes' => 'note prenotazione',
            'notify_on_expiry' => 'notifica scadenza',
            'notify_on_availability' => 'notifica disponibilità',
        ];
    }

    /**
     * Handle a failed authorization attempt.
     *
     * @return void
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    protected function failedAuthorization() {
        $user = Auth::user();
        $egiId = $this->route('id') ?? $this->input('egi_id');
        $egi = Egi::find($egiId);

        // Determine specific reason for rejection
        $reason = 'unauthorized';

        if (!$user) {
            $reason = 'authentication_required';
        } elseif ($egi && $egi->user_id === $user->id) {
            $reason = 'own_egi_cannot_reserve';
        } elseif (!$user->can('allow-reservation-operations') && !$user->can('allow-blockchain-operations')) {
            $reason = 'missing_permission';
        } elseif ($egi) {
            $availabilityService = app(EgiAvailabilityService::class);
            $availability = $availabilityService->checkAvailability($egi, $user);
            
            if (!$availability['can_reserve']) {
                $reason = $availability['reserve_reason'] ?? 'egi_not_reservable';
            }
        }

        throw new \Illuminate\Auth\Access\AuthorizationException(
            $this->getAuthorizationMessage($reason)
        );
    }

    /**
     * Get human-readable authorization error message.
     *
     * @param string $reason The reason code
     * @return string
     */
    private function getAuthorizationMessage(string $reason): string {
        $messages = [
            'authentication_required' => 'Devi effettuare il login per prenotare un EGI',
            'own_egi_cannot_reserve' => 'Non puoi prenotare il tuo stesso EGI',
            'missing_permission' => 'Non hai i permessi per prenotazioni',
            'missing_consent' => 'Manca il consenso per prenotazioni',
            'already_minted' => 'Questo EGI è già stato mintato',
            'egi_draft' => 'Questo EGI è ancora in bozza',
            'user_already_reserved' => 'Hai già una prenotazione attiva per questo EGI',
            'egi_not_reservable' => 'Questo EGI non è disponibile per prenotazione',
            'unauthorized' => 'Non autorizzato a prenotare questo EGI',
        ];

        return $messages[$reason] ?? $messages['unauthorized'];
    }

    /**
     * Configure the validator instance.
     *
     * @param \Illuminate\Validation\Validator $validator
     * @return void
     */
    public function withValidator(\Illuminate\Validation\Validator $validator): void {
        $validator->after(function ($validator) {
            $user = Auth::user();
            $egiId = $this->input('egi_id');

            // Additional cross-field validations
            
            // Verify GDPR consent is actually stored
            if ($user && $this->input('consent_reservation')) {
                $consentService = app(\App\Services\Gdpr\ConsentService::class);
                
                $hasConsent = $consentService->hasConsent($user, 'allow-reservation-operations')
                           || $consentService->hasConsent($user, 'allow-blockchain-operations');
                
                if (!$hasConsent) {
                    $validator->errors()->add(
                        'consent_reservation',
                        'Il consenso prenotazioni non è stato registrato nel sistema. Aggiorna le tue preferenze GDPR.'
                    );
                }
            }

            // Verify user doesn't have existing reservation (prevent duplicates)
            if ($user && $egiId) {
                $egi = Egi::find($egiId);
                
                if ($egi && $egi->isReservedByUser($user)) {
                    $validator->errors()->add(
                        'egi_id',
                        'Hai già una prenotazione attiva per questo EGI. Completala o annullala prima di crearne una nuova.'
                    );
                }
            }

            // Verify EGI is still available (prevent race conditions)
            if ($egiId) {
                $egi = Egi::find($egiId);
                
                if ($egi && $egi->isMinted()) {
                    $validator->errors()->add(
                        'egi_id',
                        'Questo EGI è stato appena mintato. Non è più disponibile per prenotazione.'
                    );
                }
            }

            // Rate limiting check (prevent spam)
            $rateLimitKey = 'reservation_attempt_' . ($user?->id ?? 'guest');
            $attempts = cache()->get($rateLimitKey, 0);
            
            if ($attempts >= 10) {
                $validator->errors()->add(
                    'rate_limit',
                    'Troppi tentativi di prenotazione. Attendi 5 minuti prima di riprovare.'
                );
            } else {
                // Increment attempts (5 minutes expiry)
                cache()->put($rateLimitKey, $attempts + 1, now()->addMinutes(5));
            }

            // Business rule: check user's active reservations limit
            if ($user) {
                $activeReservationsCount = \App\Models\Reservation::where('user_id', $user->id)
                    ->where('status', 'active')
                    ->where('sub_status', 'pending')
                    ->count();
                
                $maxReservations = config('egi.max_active_reservations', 5);
                
                if ($activeReservationsCount >= $maxReservations) {
                    $validator->errors()->add(
                        'user_limit',
                        "Hai raggiunto il limite di {$maxReservations} prenotazioni attive. Completa o annulla alcune prenotazioni prima di crearne di nuove."
                    );
                }
            }
        });
    }

    /**
     * Get validated data with defaults.
     * 
     * Adds default values for optional fields.
     *
     * @return array
     */
    public function validated($key = null, $default = null) {
        $validated = parent::validated($key, $default);

        // Add defaults
        $validated['reservation_duration'] = $validated['reservation_duration'] ?? 48; // Default 48 hours
        $validated['notify_on_expiry'] = $validated['notify_on_expiry'] ?? true;
        $validated['notify_on_availability'] = $validated['notify_on_availability'] ?? true;

        return $validated;
    }
}
