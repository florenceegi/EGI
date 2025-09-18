<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

if (!function_exists('livewire_tmp_path')) {
    function livewire_tmp_path() {
        $disk = config('livewire.temporary_file_upload.disk', 'local');
        $directory = config('livewire.temporary_file_upload.directory', 'livewire-tmp');

        if (is_null($directory)) {
            $directory = 'livewire-tmp';
        }

        return Storage::disk($disk)->path($directory);
    }
}

if (!function_exists('getDynamicBucketUrl')) {
    /**
     * Determina dinamicamente l'URL del bucket tra Digital Ocean e CDN.
     *
     * @return string
     */
    function getDynamicBucketUrl(): string {
        $doUrl = config('paths.hosting.Digital_Ocean.url');
        $cdnUrl = config('paths.hosting.CDN.url');

        // Controlla la disponibilità di Digital Ocean
        if (checkUrlAvailability($doUrl)) {
            Log::info("Utilizzo di Digital Ocean: {$doUrl}");
            return $doUrl;
        }

        // Controlla la disponibilità della CDN
        if (checkUrlAvailability($cdnUrl)) {
            Log::info("Utilizzo della CDN: {$cdnUrl}");
            return $cdnUrl;
        }

        // Fallback su un valore di default
        $defaultUrl = '/storage/';
        Log::warning("Nessun servizio disponibile, uso il disco locale: {$defaultUrl}");
        return $defaultUrl;
    }

    /**
     * Verifica se un URL è disponibile.
     *
     * @param string $url
     * @return bool
     */
    function checkUrlAvailability(string $url): bool {
        try {
            $response = Http::head($url);
            return $response->successful();
        } catch (\Exception $e) {
            Log::error("Errore nella verifica dell'URL: {$url}", ['error' => $e->getMessage()]);
            return false;
        }
    }
}

if (!function_exists('hasPendingWallet')) {
    /**
     * Verifica se esiste un wallet pending per il Creator.
     *
     * @param int $proposerId
     * @return bool
     */
    function hasPendingWallet(int $proposerId): bool {

        Log::channel('florenceegi')->info('hasPendingWallet: Verifica wallet pending', [
            'proposerId' => $proposerId
        ]);

        $payload = \App\Models\NotificationPayloadWallet::where('proposer_id', $proposerId)
            ->where('status', 'LIKE', '%pending%')
            ->exists();

        Log::channel('florenceegi')->info('hasPendingWallet: Risultato verifica wallet pending', [
            'payload' => $payload
        ]);

        // Supponiamo di usare il modello NotificationPayloadWallet
        // e che la colonna 'status' contenga il valore 'pending' per i wallet in attesa.
        return $payload;
    }
}

if (!function_exists('formatActivatorDisplay')) {
    /**
     * Format activator display based on user permissions
     *
     * @param \App\Models\User $user
     * @return array ['name' => string, 'avatar' => string|null, 'is_commissioner' => bool]
     */
    function formatActivatorDisplay($user) {
             
        
        // Usa la nuova logica basata su usertype
        $isCommissioner = $user && $user->usertype === 'commissioner';

        // if ($isCommissioner) {
            // Commissioner: show real name and real avatar (if uploaded) or generated
            $name = ($user->first_name && $user->last_name)
                ? $user->first_name . ' ' . $user->last_name
                : ($user->name ?? 'Commissioner');

            // Usa sempre profile_photo_url che ora gestisce automaticamente la privacy
            $avatar = null;
            try {
                $avatar = $user->profile_photo_url; // Ora include anche DiceBear
            } catch (\Exception $e) {
                \Log::warning('Failed to get user avatar', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);
                $avatar = $user->defaultProfilePhotoUrl(); // Fallback
            }

            return [
                'name' => $name,
                'avatar' => $avatar,
                'is_commissioner' => true,
                'wallet_abbreviated' => null
            ];
        // } else {
        //     // Non-commissioner: mostra wallet troncato + avatar generato
        //     $walletAddress = $user->wallet ?? '';
        //     $abbreviated = strlen($walletAddress) >= 10
        //         ? substr($walletAddress, 0, 6) . '...' . substr($walletAddress, -4)
        //         : ($walletAddress ?: 'Utente Anonimo');

        //     // Usa sempre profile_photo_url che ora restituisce avatar generato per non-commissioner
        //     $avatar = null;
        //     try {
        //         $avatar = $user->profile_photo_url; // Ora sempre presente
        //     } catch (\Exception $e) {
        //         \Log::warning('Failed to get user avatar', [
        //             'user_id' => $user->id,
        //             'error' => $e->getMessage()
        //         ]);
        //         $avatar = $user->defaultProfilePhotoUrl(); // Fallback
        //     }

        //     return [
        //         'name' => $abbreviated,
        //         'avatar' => $avatar, // Ora include l'avatar generato
        //         'is_commissioner' => false,
        //         'wallet_abbreviated' => $abbreviated
        //     ];
        // }
    }
}

if (!function_exists('getGenericActivatorIcon')) {
    /**
     * Get generic activator icon SVG
     *
     * @param string $classes
     * @return string
     */
    function getGenericActivatorIcon($classes = 'w-4 h-4') {
        return '<svg class="' . $classes . ' text-gray-400" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd" />
        </svg>';
    }
}

if (!function_exists('getActivatorsCount')) {
    /**
     * Get total count of activators (collectors + commissioners)
     *
     * @return int
     */
    function getActivatorsCount(): int {
        return \App\Models\User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['collector', 'commissioner']);
        })->count();
    }
}

if (!function_exists('getCreatorsCount')) {
    /**
     * Get total count of creators
     *
     * @return int
     */
    function getCreatorsCount(): int {
        return \App\Models\User::whereHas('roles', function ($query) {
            $query->where('name', 'creator');
        })->orWhere('usertype', 'creator')->count();
    }
}

if (!function_exists('getCollectionsCount')) {
    /**
     * Get total count of published collections
     *
     * @return int
     */
    function getCollectionsCount(): int {
        return \App\Models\Collection::where('is_published', true)->count();
    }
}

if (!function_exists('getEgiActivationStatus')) {
    /**
     * Get EGI activation status with activator information
     *
     * @param \App\Models\Egi $egi
     * @return array
     */
    function getEgiActivationStatus($egi): array {
        // Trova la prenotazione attiva vincente (se esiste)
        $winningReservation = \App\Models\Reservation::where('egi_id', $egi->id)
            ->where('is_current', true)
            ->where('status', 'active')
            ->whereNull('superseded_by_id')
            ->with('user')
            ->orderByDesc('offer_amount_fiat')
            ->first();

        if ($winningReservation && $winningReservation->user) {
            $activator = $winningReservation->user;

            // Determina se è un commissioner (ha il ruolo o i permessi)
            $isCommissioner = $activator->hasRole('commissioner') ||
                $activator->can('display_public_name_on_egi');

            return [
                'status' => 'activated',
                'highest_bid' => $winningReservation->offer_amount_fiat,
                'currency' => $winningReservation->fiat_currency,
                'can_reserve' => false, // Non può più essere prenotato
                'reservations_count' => 1,
                'activator' => [
                    'name' => $isCommissioner ?
                        ($activator->first_name && $activator->last_name ?
                            $activator->first_name . ' ' . $activator->last_name :
                            $activator->name) :
                        'Co Creatore',
                    'avatar' => $isCommissioner ? $activator->profile_photo_url : null,
                    'is_commissioner' => $isCommissioner,
                    'id' => $activator->id
                ]
            ];
        }

        // Verifica se ci sono prenotazioni in competizione
        $competingReservationsCount = \App\Models\Reservation::where('egi_id', $egi->id)
            ->where('is_current', true)
            ->where('status', 'active')
            ->count();

        if ($competingReservationsCount > 1) {
            // Trova la miglior offerta in competizione
            $highestReservation = \App\Models\Reservation::where('egi_id', $egi->id)
                ->where('is_current', true)
                ->where('status', 'active')
                ->orderByDesc('offer_amount_fiat')
                ->first();

            return [
                'status' => 'in_competition',
                'highest_bid' => $highestReservation->offer_amount_fiat ?? 0,
                'currency' => $highestReservation->fiat_currency ?? 'EUR',
                'can_reserve' => true, // Può ancora fare offerte
                'reservations_count' => $competingReservationsCount,
                'activator' => null
            ];
        }

        // EGI disponibile per attivazione
        return [
            'status' => 'available',
            'highest_bid' => null,
            'can_reserve' => true, // Può essere prenotato
            'reservations_count' => 0,
            'activator' => null
        ];
    }

    /**
     * Formatta un prezzo in euro con il simbolo della valuta
     */
    function formatPrice($price): string {
        if ($price === null || $price === '') {
            return '€ 0,00';
        }

        return '€ ' . number_format((float) $price, 2, ',', '.');
    }
}

if (!function_exists('formatNumberAbbreviated')) {
    /**
     * Formatta un numero con notazione abbreviata per risparmiare spazio nei layout mobile
     *
     * @param int|float|string|null $number Numero da formattare
     * @param int $decimals Numero di decimali da mostrare (default: 1)
     * @param bool $showZeroDecimals Se mostrare .0 per numeri interi (default: false)
     * @return string Numero formattato con notazione abbreviata (es: 1.2K, 15.5M)
     *
     * Esempi:
     * - 999 → "999"
     * - 1234 → "1.2K"
     * - 12345 → "12.3K"
     * - 123456 → "123K"
     * - 1234567 → "1.2M"
     * - 1000000000 → "1B"
     */
    function formatNumberAbbreviated($number, int $decimals = 1, bool $showZeroDecimals = false): string {
        // Gestisci casi null o vuoti
        if ($number === null || $number === '') {
            return '0';
        }

        // Converti a numero
        $num = (float) $number;

        // Gestisci numeri negativi
        $isNegative = $num < 0;
        $num = abs($num);

        // Definisci le soglie e suffissi
        $suffixes = [
            1000000000000 => 'T', // Trilioni
            1000000000 => 'B',    // Miliardi
            1000000 => 'M',       // Milioni
            1000 => 'K'           // Migliaia
        ];

        $formatted = '';

        // Cerca la soglia appropriata
        foreach ($suffixes as $threshold => $suffix) {
            if ($num >= $threshold) {
                $value = $num / $threshold;

                // Se il valore è >= 100, non mostrare decimali per leggibilità
                if ($value >= 100) {
                    $formatted = number_format($value, 0, ',', '') . $suffix;
                }
                // Se il valore è un numero intero e non vogliamo mostrare .0
                elseif (!$showZeroDecimals && $value == floor($value)) {
                    $formatted = number_format($value, 0, ',', '') . $suffix;
                } else {
                    $formatted = number_format($value, $decimals, ',', '') . $suffix;
                }
                break;
            }
        }

        // Se non ha raggiunto nessuna soglia, mostra il numero intero
        if (empty($formatted)) {
            $formatted = number_format($num, 0, ',', '.');
        }

        // Aggiungi il segno negativo se necessario
        return $isNegative ? '-' . $formatted : $formatted;
    }
}

if (!function_exists('formatPriceAbbreviated')) {
    /**
     * Formatta un prezzo in euro con notazione abbreviata per layout mobile
     *
     * @param int|float|string|null $price Prezzo da formattare
     * @param int $decimals Numero di decimali da mostrare (default: 1)
     * @param bool $showZeroDecimals Se mostrare .0 per numeri interi (default: false)
     * @return string Prezzo formattato con simbolo euro e notazione abbreviata
     *
     * Esempi:
     * - 999 → "€ 999"
     * - 1234 → "€ 1.2K"
     * - 1234567 → "€ 1.2M"
     */
    function formatPriceAbbreviated($price, int $decimals = 1, bool $showZeroDecimals = false): string {
        if ($price === null || $price === '') {
            return '€ 0';
        }

        $formattedNumber = formatNumberAbbreviated($price, $decimals, $showZeroDecimals);
        return '€ ' . $formattedNumber;
    }
}