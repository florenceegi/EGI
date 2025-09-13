<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware per sostituire l'ID del creator con il nick_name nell'URL
 *
 * Questo middleware intercetta le richieste alle route creator/{id} e:
 * 1. Cerca il creator con l'ID specificato
 * 2. Se il creator ha un nick_name, reindirizza a creator/{nick_name}
 * 3. Altrimenti continua con l'ID originale
 */
class CreatorNicknameRedirect {
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response {
        // Ottieni il parametro id dalla route
        $creatorId = $request->route('id');

        // Se l'ID è numerico, potrebbe essere un ID da convertire in nick_name
        if (is_numeric($creatorId)) {
            $creator = User::find($creatorId);

            // Se il creator esiste e ha un nick_name, reindirizza all'URL con nick_name
            if ($creator && !empty($creator->nick_name)) {
                $currentUrl = $request->fullUrl();
                $newUrl = str_replace("/creator/{$creatorId}", "/creator/{$creator->nick_name}", $currentUrl);

                return redirect($newUrl, 301); // Redirect permanente
            }
        }

        return $next($request);
    }
}
