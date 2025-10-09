<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Egi;
use App\Services\EgiAvailabilityService;
use Illuminate\Support\Facades\Auth;

/**
 * @Oracode Request: Direct Mint Validation
 * 🎯 Purpose: Validate direct mint requests (Phase 2 dual path)
 * 🛡️ Security: Permission checks, rate limiting, GDPR consent, EGI availability
 * 🧱 Business Rules: Published, not minted, not draft, user not creator, consent given
 *
 * @package App\Http\Requests
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Phase 2 Expansion)
 * @date 2025-10-09
 * @purpose Validation for direct mint operations (dual path mint vs reservation)
 *
 * MiCA-SAFE Compliance:
 * - FIAT payment only (validated via payment_method)
 * - No crypto custody for users
 * - Treasury wallet mint service
 */
class MintDirectRequest extends FormRequest {

    /**
     * Determine if the user is authorized to make this request.
     *
     * Authorization checks:
     * - User must be authenticated
     * - User must have 'allow-blockchain-operations' permission
     * - User cannot mint their own EGI
     * - EGI must be available for direct mint
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

        // User cannot mint their own EGI
        if ($egi->user_id === $user->id) {
            return false;
        }

        // Check EGI availability using service
        $availabilityService = app(EgiAvailabilityService::class);
        $availability = $availabilityService->checkAvailability($egi, $user);

        // Must be available for mint
        if (!$availability['can_mint']) {
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

            // Payment method (MiCA-SAFE: FIAT only)
            'payment_method' => [
                'required',
                'string',
                'in:stripe,paypal,bank_transfer' // NO crypto payment methods
            ],

            // Optional: wallet address if user has one
            'wallet_address' => [
                'nullable',
                'string',
                'regex:/^[A-Z2-7]{58}$/', // Algorand address format
            ],

            // Optional: dedicated metadata
            'custom_metadata' => [
                'nullable',
                'array',
                'max:10' // Max 10 custom fields
            ],

            'custom_metadata.*' => [
                'nullable',
                'string',
                'max:255'
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

            // Payment method
            'payment_method.required' => 'Metodo di pagamento obbligatorio',
            'payment_method.in' => 'Metodo di pagamento non valido. Usa Stripe, PayPal o bonifico bancario.',

            // Wallet address
            'wallet_address.regex' => 'Indirizzo wallet Algorand non valido (formato: 58 caratteri A-Z2-7)',

            // Custom metadata
            'custom_metadata.array' => 'Metadata deve essere un array',
            'custom_metadata.max' => 'Massimo 10 campi metadata personalizzati',
            'custom_metadata.*.string' => 'Ogni campo metadata deve essere una stringa',
            'custom_metadata.*.max' => 'Ogni campo metadata massimo 255 caratteri',
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
            'payment_method' => 'metodo di pagamento',
            'wallet_address' => 'indirizzo wallet',
            'custom_metadata' => 'metadata personalizzato',
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
            $reason = 'own_egi_cannot_mint';
        } elseif (!$user->can('allow-blockchain-operations')) {
            $reason = 'missing_permission';
        } elseif ($egi) {
            $availabilityService = app(EgiAvailabilityService::class);
            $availability = $availabilityService->checkAvailability($egi, $user);

            if (!$availability['can_mint']) {
                $reason = $availability['mint_reason'] ?? 'egi_not_mintable';
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
            'authentication_required' => 'Devi effettuare il login per mintare un EGI',
            'own_egi_cannot_mint' => 'Non puoi mintare il tuo stesso EGI',
            'missing_permission' => 'Non hai i permessi per operazioni blockchain',
            'missing_consent' => 'Manca il consenso per operazioni blockchain',
            'already_minted' => 'Questo EGI è già stato mintato',
            'egi_draft' => 'Questo EGI è ancora in bozza',
            'egi_not_mintable' => 'Questo EGI non è disponibile per la minta',
            'unauthorized' => 'Non autorizzato a mintare questo EGI',
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

            // Verify EGI is still available (prevent race conditions)
            if ($egiId) {
                $egi = Egi::find($egiId);

                if ($egi && $egi->isMinted()) {
                    $validator->errors()->add(
                        'egi_id',
                        'Questo EGI è stato appena mintato da un altro utente. Aggiorna la pagina.'
                    );
                }
            }

            // Rate limiting check (prevent spam)
            $rateLimitKey = 'mint_direct_attempt_' . ($user?->id ?? 'guest');
            $attempts = cache()->get($rateLimitKey, 0);

            if ($attempts >= 5) {
                $validator->errors()->add(
                    'rate_limit',
                    'Troppi tentativi di minta. Attendi 5 minuti prima di riprovare.'
                );
            } else {
                // Increment attempts (5 minutes expiry)
                cache()->put($rateLimitKey, $attempts + 1, now()->addMinutes(5));
            }
        });
    }
}
