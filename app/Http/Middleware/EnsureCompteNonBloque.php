<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCompteNonBloque
{
    /**
     * Si le compte créateur est bloqué (score d'intégrité à 10 % ou moins),
     * rediriger vers la page "Compte bloqué" sauf pour cette page et la déconnexion.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user) {
            return $next($request);
        }

        if (! $user->compte_bloque) {
            return $next($request);
        }

        // Autoriser uniquement la page compte-bloque et la déconnexion
        if ($request->routeIs('compte-bloque') || $request->routeIs('logout')) {
            return $next($request);
        }

        return redirect()->route('compte-bloque');
    }
}
