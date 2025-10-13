<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Fortify\Http\Responses\LogoutResponse;

class FortifyServiceProvider extends ServiceProvider {
    /**
     * Register any application services.
     */
    public function register(): void {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())) . '|' . $request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        // ✅ Dynamic redirect using AuthRedirectService (usertype-based)
        // pa_identity → pa.acts.index, creator → home, etc.
        Fortify::redirects('login', function () {
            $user = auth()->user();
            if (!$user) {
                return route('home'); // Fallback if no user (shouldn't happen)
            }

            // Use AuthRedirectService for usertype-based redirect
            $authRedirectService = app(\App\Services\Auth\AuthRedirectService::class);
            return route($authRedirectService->getRedirectRoute($user));
        });

        // Estendi il comportamento di login per gestire il wallet
        Fortify::authenticateUsing(function (Request $request) {
            // Usa il metodo di autenticazione standard di Fortify
            $user = User::where('email', $request->email)->first();
            Log::channel('upload')->info('Login attempt.', [
                'user' =>  $user,
            ]);

            // Verifica le credenziali
            if ($user && Hash::check($request->password, $user->password)) {
                // Se l'utente ha un wallet address, salvalo nei cookie
                Log::channel('upload')->info('Login attempt.', [
                    'email' => $request->email,
                    'ip' => $request->ip(),
                    'session_id' => $request->session()->getId(),
                    'user_id' => $user->id,
                    'wallet' => $user->wallet,
                ]);

                // Imposta dati sessione per lo stato "connected"
                $request->session()->put([
                    'auth_status' => 'connected', // Stato specifico per "weak auth"
                    'connected_wallet' => $user->wallet,
                    'connected_user_id' => $user->id,
                    'user_type' => $user->usertype, // Aggiungi il tipo di utente
                ]);

                if (!empty($user->wallet)) {
                    // Crea un cookie che dura 30 giorni (o il tempo che preferisci)
                    // Cookie::queue('connected_wallet', $user->wallet, 60 * 24 * 30, '/', null, false, false);

                    Cookie::queue(Cookie::make(
                        'connected_wallet', // Nome del cookie
                        $user->wallet, // Valore del cookie
                        60 * 24 * 30, // Durata: 30 giorni
                        '/', // Path: accessibile su tutto il sito
                        null, // Dominio: default
                        true, // Secure: true per HTTPS (false per test locali HTTP)
                        false, // HttpOnly: false per accesso da JavaScript
                        false, // Raw: false per codifica normale
                        'lax' // SameSite: 'lax' per sicurezza
                    ));
                }

                // Log::channel('upload')->info('Cookie wallet impostato', [
                //     'cookie' => $cookie,
                // ]);

                return $user;
            }
        });
    }
}
